<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'themerex_template_no_search_theme_setup' ) ) {
	add_action( 'themerex_action_before_init_theme', 'themerex_template_no_search_theme_setup', 1 );
	function themerex_template_no_search_theme_setup() {
		themerex_add_template(array(
			'layout' => 'no-search',
			'mode'   => 'internal',
			'title'  => esc_html__('No search results found', 'wineshop'),
			'w'		 => null,
			'h'		 => null
		));
	}
}

// Template output
if ( !function_exists( 'themerex_template_no_search_output' ) ) {
	function themerex_template_no_search_output($post_options, $post_data) {
		global $THEMEREX_GLOBALS;
		?>
		<article class="post_item">
			<div class="post_content">
				<h2 class="post_title"><?php echo sprintf(esc_html__('Search: %s', 'wineshop'), get_search_query()); ?></h2>
				<p><?php esc_html_e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'wineshop' ); ?></p>
				<p><?php echo wp_kses( sprintf(__('Go back, or return to <a href="%s">%s</a> home page to choose a new page.', 'wineshop'), esc_url(home_url('/')), get_bloginfo()), $THEMEREX_GLOBALS['allowed_tags'] ); ?>
				<br><?php esc_html_e('Please report any broken links to our team.', 'wineshop'); ?></p>
				<?php if (function_exists('themerex_sc_search')) themerex_show_layout(themerex_sc_search(array('state'=>"fixed"))); ?>
			</div>	<!-- /.post_content -->
		</article>	<!-- /.post_item -->
		<?php
	}
}
?>