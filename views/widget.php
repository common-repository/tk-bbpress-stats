<!-- This file is used to markup the public-facing widget. -->

<dl>
<?php

global $wp_query, $wpdb;
  $user_count = $wpdb->get_var("SELECT COUNT(ID)  FROM ".$wpdb->prefix."users");
	$topic_count = $wpdb->get_var("SELECT COUNT(ID) FROM ".$wpdb->prefix."posts WHERE post_status = 'publish' AND post_type = 'topic'");
	$reply_count = $wpdb->get_var("SELECT COUNT(ID) FROM ".$wpdb->prefix."posts WHERE post_status = 'publish' AND post_type = 'reply'");
	$forum_count = $wpdb->get_var("SELECT COUNT(ID) FROM ".$wpdb->prefix."posts WHERE post_status = 'publish' AND post_type = 'forum'");
	$total_count = $topic_count + $reply_count;


if($show_user_count == 'true'){
    echo '<dt class="tk_bbpress_stats">' . 'Users: ' . '</dt>' . '<dd class="tk_bbpress_stats">' . number_format($user_count) . '</dd>';
}
if($show_forum_count == 'true'){
    echo '<dt class="tk_bbpress_stats">' . 'Forums: ' . '</dt>' . '<dd class="tk_bbpress_stats">' . number_format($forum_count) . '</dd>';
}
if($show_topic_count == 'true'){
    echo '<dt class="tk_bbpress_stats">' . 'Topics: ' . '</dt>' . '<dd class="tk_bbpress_stats">' . number_format($topic_count) . '</dd>';
}
if($show_reply_count == 'true'){
    echo '<dt class="tk_bbpress_stats">' . 'Replies: ' . '</dt>' . '<dd class="tk_bbpress_stats">' . number_format($reply_count) . '</dd>';
}
if($show_total_count == 'true'){
    echo '<dt class="tk_bbpress_stats">' . 'Total Posts: ' . '</dt>' . '<dd class="tk_bbpress_stats">' . number_format($total_count) . '</dd>';
}

?>
</dl>
