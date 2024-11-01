<?php
/**
 *
 *
 * @package   TK_bbPress_Stats
 * @author    Tony Korologos <admin@tkserver.com>
 * @license   GPL-2.0+
 * @link      http://www.tkserver.com
 * @copyright 2015 TK Server
 *
 * @wordpress-plugin
 * Plugin Name:       TK bbPress Stats
 * Plugin URI:        http://www.tkserver.com
 * Description:       Meaningful bbPress Statistics
 * Version:           1.0.3
 * Author:            Tony Korologos
 * Author URI:        http://www.tkserver.com
 * Text Domain:       tk-bbpress-stats
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /lang
 * GitHub Plugin URI: https://github.com/<owner>/<repo>
 */

 // Prevent direct file access
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

// this allows us to post the widget into a page or post via shortcode like so: [widget widget_name="widget_name"]
function widget($atts) {
    global $wp_widget_factory;
    extract(shortcode_atts(array(
        'widget_name' => FALSE
    ), $atts));
    $widget_name = esc_html($widget_name);
    if (!is_a($wp_widget_factory->widgets[$widget_name], 'WP_Widget')):
        $wp_class = 'WP_Widget_'.ucwords(strtolower($class));
        if (!is_a($wp_widget_factory->widgets[$wp_class], 'WP_Widget')):
            return '<p>'.sprintf(__("%s: Widget class not found. Make sure this widget exists and the class name is correct"),'<strong>'.$class.'</strong>').'</p>';
        else:
            $class = $wp_class;
        endif;
    endif;

		$instance = array();

		$tk_bbpress_stats_options = get_option( 'tk_bbpress_stats_option_name' ); // Array of All Options

		if(isset($tk_bbpress_stats_options['show_topic_count_0'])){$show_topic_count_0 = $tk_bbpress_stats_options['show_topic_count_0'];
		 	} else {$show_topic_count_0 = NULL; } // Show Topic Count
		if(isset($tk_bbpress_stats_options['show_reply_count_1'])){$show_reply_count_1 = $tk_bbpress_stats_options['show_reply_count_1'];
			} else {$show_reply_count_1 = NULL; } // Show Reply Count
		if(isset($tk_bbpress_stats_options['show_total_count_2'])){$show_total_count_2 = $tk_bbpress_stats_options['show_total_count_2'];
			} else {$show_total_count_2 = NULL; } // Show Total Count
		if(isset($tk_bbpress_stats_options['show_user_count_3'])){$show_user_count_3 = $tk_bbpress_stats_options['show_user_count_3'];
			} else {$show_user_count_3 = NULL; } // Show User Count
		if(isset($tk_bbpress_stats_options['show_forum_count_4'])){$show_forum_count_4 = $tk_bbpress_stats_options['show_forum_count_4'];
			} else {$show_forum_count_4 = NULL; } // Show Total Count

				$instance['title'] = ''; // title not needed for pages or posts, right?
				$instance[ 'show_topic_count' ] = $show_topic_count_0 ;
				$instance[ 'show_reply_count' ] = $show_reply_count_1 ;
				$instance[ 'show_total_count' ] = $show_total_count_2 ;
				$instance[ 'show_user_count' 	] = $show_user_count_3 ;
				$instance[ 'show_forum_count' ] = $show_forum_count_4 ;

    ob_start();
    the_widget($widget_name, $instance, array('widget_id'=>'arbitrary-instance-',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ));
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}
add_shortcode('widget','widget');


class TKBbPressStats {
	private $tk_bbpress_stats_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'tk_bbpress_stats_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'tk_bbpress_stats_page_init' ) );
	}

	public function tk_bbpress_stats_add_plugin_page() {
		add_options_page(
			'TK bbPress Stats', // page_title
			'TK bbPress Stats', // menu_title
			'manage_options', // capability
			'tk-bbpress-stats', // menu_slug
			array( $this, 'tk_bbpress_stats_create_admin_page' ) // function
		);
	}

	public function tk_bbpress_stats_create_admin_page() {
		$this->tk_bbpress_stats_options = get_option( 'tk_bbpress_stats_option_name' ); ?>

		<div class="wrap">
			<h2>TK bbPress Stats</h2>
			<p>Settings for page/post display. Insert the widget into a post or page with the following short code: [widget widget_name="TK_bbPress_Stats"]</p>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'tk_bbpress_stats_option_group' );
					do_settings_sections( 'tk-bbpress-stats-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function tk_bbpress_stats_page_init() {
		register_setting(
			'tk_bbpress_stats_option_group', // option_group
			'tk_bbpress_stats_option_name', // option_name
			array( $this, 'tk_bbpress_stats_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'tk_bbpress_stats_setting_section', // id
			'Settings', // title
			array( $this, 'tk_bbpress_stats_section_info' ), // callback
			'tk-bbpress-stats-admin' // page
		);

		add_settings_field(
			'show_topic_count_0', // id
			'Show Topic Count', // title
			array( $this, 'show_topic_count_0_callback' ), // callback
			'tk-bbpress-stats-admin', // page
			'tk_bbpress_stats_setting_section' // section
		);

		add_settings_field(
			'show_reply_count_1', // id
			'Show Reply Count', // title
			array( $this, 'show_reply_count_1_callback' ), // callback
			'tk-bbpress-stats-admin', // page
			'tk_bbpress_stats_setting_section' // section
		);

		add_settings_field(
			'show_total_count_2', // id
			'Show Total Count', // title
			array( $this, 'show_total_count_2_callback' ), // callback
			'tk-bbpress-stats-admin', // page
			'tk_bbpress_stats_setting_section' // section
		);

		add_settings_field(
			'show_user_count_3', // id
			'Show User Count', // title
			array( $this, 'show_user_count_3_callback' ), // callback
			'tk-bbpress-stats-admin', // page
			'tk_bbpress_stats_setting_section' // section
		);

		add_settings_field(
			'show_forum_count_4', // id
			'Show Forum Count', // title
			array( $this, 'show_forum_count_4_callback' ), // callback
			'tk-bbpress-stats-admin', // page
			'tk_bbpress_stats_setting_section' // section
		);
	}

	public function tk_bbpress_stats_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['show_topic_count_0'] ) ) {
			$sanitary_values['show_topic_count_0'] = $input['show_topic_count_0'];
		}

		if ( isset( $input['show_reply_count_1'] ) ) {
			$sanitary_values['show_reply_count_1'] = $input['show_reply_count_1'];
		}

		if ( isset( $input['show_total_count_2'] ) ) {
			$sanitary_values['show_total_count_2'] = $input['show_total_count_2'];
		}

		if ( isset( $input['show_user_count_3'] ) ) {
			$sanitary_values['show_user_count_3'] = $input['show_user_count_3'];
		}

		if ( isset( $input['show_forum_count_4'] ) ) {
			$sanitary_values['show_forum_count_4'] = $input['show_forum_count_4'];
		}

		return $sanitary_values;
	}

	public function tk_bbpress_stats_section_info() {

	}

	public function show_topic_count_0_callback() {
		printf(
			'<input type="checkbox" name="tk_bbpress_stats_option_name[show_topic_count_0]" id="show_topic_count_0" value="show_topic_count_0" %s>',
			( isset( $this->tk_bbpress_stats_options['show_topic_count_0'] ) && $this->tk_bbpress_stats_options['show_topic_count_0'] === 'show_topic_count_0' ) ? 'checked' : ''
		);
	}

	public function show_reply_count_1_callback() {
		printf(
			'<input type="checkbox" name="tk_bbpress_stats_option_name[show_reply_count_1]" id="show_reply_count_1" value="show_reply_count_1" %s>',
			( isset( $this->tk_bbpress_stats_options['show_reply_count_1'] ) && $this->tk_bbpress_stats_options['show_reply_count_1'] === 'show_reply_count_1' ) ? 'checked' : ''
		);
	}

	public function show_total_count_2_callback() {
		printf(
			'<input type="checkbox" name="tk_bbpress_stats_option_name[show_total_count_2]" id="show_total_count_2" value="show_total_count_2" %s> <label for="show_total_count_2"> Topics + Replies</label>',
			( isset( $this->tk_bbpress_stats_options['show_total_count_2'] ) && $this->tk_bbpress_stats_options['show_total_count_2'] === 'show_total_count_2' ) ? 'checked' : ''
		);
	}

	public function show_user_count_3_callback() {
		printf(
			'<input type="checkbox" name="tk_bbpress_stats_option_name[show_user_count_3]" id="show_user_count_3" value="show_user_count_3" %s>',
			( isset( $this->tk_bbpress_stats_options['show_user_count_3'] ) && $this->tk_bbpress_stats_options['show_user_count_3'] === 'show_user_count_3' ) ? 'checked' : ''
		);
	}

	public function show_forum_count_4_callback() {
		printf(
			'<input type="checkbox" name="tk_bbpress_stats_option_name[show_forum_count_4]" id="show_forum_count_4" value="show_forum_count_4" %s>',
			( isset( $this->tk_bbpress_stats_options['show_forum_count_4'] ) && $this->tk_bbpress_stats_options['show_forum_count_4'] === 'show_forum_count_4' ) ? 'checked' : ''
		);
	}

}
if ( is_admin() )
	$tk_bbpress_stats = new TKBbPressStats();




// TODO: change 'Widget_Name' to the name of your plugin
class TK_bbPress_Stats extends WP_Widget {

    /**
     * @TODO - Rename "widget-name" to the name your your widget
     *
     * Unique identifier for your widget.
     *
     *
     * The variable name is used as the text domain when internationalizing strings
     * of text. Its value should match the Text Domain file header in the main
     * widget file.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $widget_slug = 'tk-bbpress-stats';

	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		// TODO: update description
		parent::__construct(
			$this->get_widget_slug(),
			__( 'TK bbPress Stats', $this->get_widget_slug() ),
			array(
				'classname'  => $this->get_widget_slug().'-class',
				'description' => __( 'Stats for bbPress.', $this->get_widget_slug() )
			)
		);

		// Register admin styles and scripts
		add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		// Register site styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_scripts' ) );

		// Refreshing the widget's cached output with each new post
		add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

	} // end constructor


    /**
     * Return the widget slug.
     *
     * @since    1.0.0
     *
     * @return    Plugin slug variable.
     */
    public function get_widget_slug() {
        return $this->widget_slug;
    }

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array args  The array of form elements
	 * @param array instance The current instance of the widget
	 */



	public function widget( $args, $instance ) {

      //store the options in variables

  $show_topic_count = $instance['show_topic_count'] ? 'true' : 'false';
  $show_reply_count = $instance['show_reply_count'] ? 'true' : 'false';
  $show_total_count = $instance['show_total_count'] ? 'true' : 'false';
  $show_user_count = $instance['show_user_count']   ? 'true' : 'false';
  $show_forum_count = $instance['show_forum_count'] ? 'true' : 'false';

 	echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		//echo __( 'Hello, World!', 'text_domain' );
		echo $args['after_widget'];



		// Check if there is a cached output
		$cache = wp_cache_get( $this->get_widget_slug(), 'widget' );

		if ( !is_array( $cache ) )
			$cache = array();

		if ( ! isset ( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset ( $cache[ $args['widget_id'] ] ) )
			return print $cache[ $args['widget_id'] ];

		// go on with your widget logic, put everything into a string and …

		extract( $args, EXTR_SKIP );

		$widget_string = $before_widget;

		// TODO: Here is where you manipulate your widget's values based on their input fields
		ob_start();
		include( plugin_dir_path( __FILE__ ) . 'views/widget.php' );
		$widget_string .= ob_get_clean();
		$widget_string .= $after_widget;

		$cache[ $args['widget_id'] ] = $widget_string;

		wp_cache_set( $this->get_widget_slug(), $cache, 'widget' );

		print $widget_string;

	} // end widget


	public function flush_widget_cache()
	{
    	wp_cache_delete( $this->get_widget_slug(), 'widget' );
	}
	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param array new_instance The new instance of values to be generated via the update.
	 * @param array old_instance The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

	  $instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        $instance[ 'show_topic_count' ] = $new_instance[ 'show_topic_count' ];
        $instance[ 'show_reply_count' ] = $new_instance[ 'show_reply_count' ];
        $instance[ 'show_total_count' ] = $new_instance[ 'show_total_count' ];
        $instance[ 'show_user_count' ]  = $new_instance[ 'show_user_count' ];
        $instance[ 'show_forum_count' ] = $new_instance[ 'show_forum_count' ];

		return $instance;

	} // end widget

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param array instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {

    ?>
    <p>
    <input class="checkbox" type="checkbox" <?php checked( $instance[ 'show_topic_count' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'show_topic_count' ); ?>" name="<?php echo $this->get_field_name( 'show_topic_count' ); ?>" />
    <label for="<?php echo $this->get_field_id( 'show_topic_count' ); ?>">Show Topic Count</label>
    </p>
    <p>
    <input class="checkbox" type="checkbox" <?php checked( $instance[ 'show_forum_count' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'show_forum_count' ); ?>" name="<?php echo $this->get_field_name( 'show_forum_count' ); ?>" />
    <label for="<?php echo $this->get_field_id( 'show_forum_count' ); ?>">Show Forum Count</label>
    </p>
    <p>
    <input class="checkbox" type="checkbox" <?php checked( $instance[ 'show_reply_count' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'show_reply_count' ); ?>" name="<?php echo $this->get_field_name( 'show_reply_count' ); ?>" />
    <label for="<?php echo $this->get_field_id( 'show_reply_count' ); ?>">Show Reply Count</label>
  </p>
      <p>
    <input class="checkbox" type="checkbox" <?php checked( $instance[ 'show_total_count' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'show_total_count' ); ?>" name="<?php echo $this->get_field_name( 'show_total_count' ); ?>" />
    <label for="<?php echo $this->get_field_id( 'show_total_count' ); ?>">Show Total Count</label>
  </p>
      <p>
    <input class="checkbox" type="checkbox" <?php checked( $instance[ 'show_user_count' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'show_user_count' ); ?>" name="<?php echo $this->get_field_name( 'show_user_count' ); ?>" />
    <label for="<?php echo $this->get_field_id( 'show_user_count' ); ?>">Show User Count</label>
  </p>
  <?php


		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php

		// Display the admin form
		include( plugin_dir_path(__FILE__) . 'views/admin.php' );

	} // end form



	/*--------------------------------------------------*/
	/* Public Functions
	/*--------------------------------------------------*/

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {

		// TODO be sure to change 'widget-name' to the name of *your* plugin
		load_plugin_textdomain( $this->get_widget_slug(), false, plugin_dir_path( __FILE__ ) . 'lang/' );

	} // end widget_textdomain

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param  boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public function activate( $network_wide ) {
		// TODO define activation functionality here
	} // end activate

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public function deactivate( $network_wide ) {
		// TODO define deactivation functionality here
	} // end deactivate

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles() {

		wp_enqueue_style( $this->get_widget_slug().'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ) );

	} // end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */
	public function register_admin_scripts() {

		wp_enqueue_script( $this->get_widget_slug().'-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array('jquery') );

	} // end register_admin_scripts

	/**
	 * Registers and enqueues widget-specific styles.
	 */
	public function register_widget_styles() {

		wp_enqueue_style( $this->get_widget_slug().'-widget-styles', plugins_url( 'css/widget.css', __FILE__ ) );

	} // end register_widget_styles

	/**
	 * Registers and enqueues widget-specific scripts.
	 */
	public function register_widget_scripts() {

		wp_enqueue_script( $this->get_widget_slug().'-script', plugins_url( 'js/widget.js', __FILE__ ), array('jquery') );

	} // end register_widget_scripts

} // end class

// TODO: Remember to change 'Widget_Name' to match the class name definition
add_action( 'widgets_init', create_function( '', 'register_widget("TK_bbPress_Stats");' ) );
