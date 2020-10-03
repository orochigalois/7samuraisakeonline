<?php
$show_all_counters = !isset($post_options['counters']);
$counters_tag = is_single() ? 'span' : 'a';
 
if ($show_all_counters || themerex_strpos($post_options['counters'], 'views')!==false) {
	?>
	<<?php themerex_show_layout($counters_tag); ?> class="post_counters_item post_counters_views icon-eye" title="<?php echo esc_attr( sprintf(esc_attr__('Views - %s', 'wineshop'), $post_data['post_views']) ); ?>" href="<?php echo esc_url($post_data['post_link']); ?>"><?php themerex_show_layout($post_data['post_views']); ?></<?php themerex_show_layout($counters_tag); ?>>
	<?php
}

if ($show_all_counters || themerex_strpos($post_options['counters'], 'comments')!==false) {
	?>
	<a class="post_counters_item post_counters_comments icon-comment" title="<?php echo esc_attr( sprintf(esc_attr__('Comments - %s', 'wineshop'), $post_data['post_comments']) ); ?>" href="<?php echo esc_url($post_data['post_comments_link']); ?>"><span class="post_counters_number"><?php themerex_show_layout($post_data['post_comments']); ?></span></a>
	<?php 
}
 
$rating = $post_data['post_reviews_'.(themerex_get_theme_option('reviews_first')=='author' ? 'author' : 'users')];
if ($rating > 0 && ($show_all_counters || themerex_strpos($post_options['counters'], 'rating')!==false)) { 
	?>
	<<?php themerex_show_layout($counters_tag); ?> class="post_counters_item post_counters_rating icon-star" title="<?php echo esc_attr( sprintf(esc_attr__('Rating - %s', 'wineshop'), $rating) ); ?>" href="<?php echo esc_url($post_data['post_link']); ?>"><span class="post_counters_number"><?php themerex_show_layout($rating); ?></span></<?php themerex_show_layout($counters_tag); ?>>
	<?php
}

if ($show_all_counters || themerex_strpos($post_options['counters'], 'likes')!==false) {
	// Load core messages
	themerex_enqueue_messages();
	$likes = isset($_COOKIE['themerex_likes']) ? $_COOKIE['themerex_likes'] : '';
	$allow = themerex_strpos($likes, ','.($post_data['post_id']).',')===false;
	?>
	<a class="post_counters_item post_counters_likes icon-heart <?php themerex_show_layout($allow ? 'enabled' : 'disabled'); ?>" title="<?php themerex_show_layout($allow ? esc_attr__('Like', 'wineshop') : esc_attr__('Dislike', 'wineshop')); ?>" href="#"
		data-postid="<?php echo esc_attr($post_data['post_id']); ?>"
		data-likes="<?php echo esc_attr($post_data['post_likes']); ?>"
		data-title-like="<?php esc_html_e('Like', 'wineshop'); ?>"
		data-title-dislike="<?php esc_html_e('Dislike', 'wineshop'); ?>"><span class="post_counters_number"><?php themerex_show_layout($post_data['post_likes']); ?></span></a>
	<?php
}

if (is_single() && themerex_strpos($post_options['counters'], 'markup')!==false) {
	?>
	<meta itemprop="interactionCount" content="User<?php echo esc_attr(themerex_strpos($post_options['counters'],'comments')!==false ? 'Comments' : 'PageVisits'); ?>:<?php echo esc_attr(themerex_strpos($post_options['counters'], 'comments')!==false ? $post_data['post_comments'] : $post_data['post_views']); ?>" />
	<?php
}
?>