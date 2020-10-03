<?php
/**
Template Name: Single post
 */


get_header();




global $THEMEREX_GLOBALS;
$single_style = !empty($THEMEREX_GLOBALS['single_style']) ? $THEMEREX_GLOBALS['single_style'] : themerex_get_custom_option('single_style');

while (have_posts()) { the_post();
	themerex_show_post_layout(
		array(
			'layout' => $single_style,
			'sidebar' => !themerex_param_is_off(themerex_get_custom_option('show_sidebar_main')),
			'content' => themerex_get_template_property($single_style, 'need_content'),
			'terms_list' => themerex_get_template_property($single_style, 'need_terms')
		)
	);
}

get_footer();
?>