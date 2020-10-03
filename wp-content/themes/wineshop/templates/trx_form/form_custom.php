<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'themerex_template_form_custom_theme_setup' ) ) {
	add_action( 'themerex_action_before_init_theme', 'themerex_template_form_custom_theme_setup', 1 );
	function themerex_template_form_custom_theme_setup() {
		themerex_add_template(array(
			'layout' => 'form_custom',
			'mode'   => 'forms',
			'title'  => esc_html__('Custom Form', 'wineshop')
			));
	}
}

// Template output
if ( !function_exists( 'themerex_template_form_custom_output' ) ) {
	function themerex_template_form_custom_output($post_options, $post_data) {
		global $THEMEREX_GLOBALS;
		?>
		<form <?php themerex_show_layout($post_options['id'] ? ' id="'.esc_attr($post_options['id']).'"' : ''); ?> data-formtype="<?php echo esc_attr($post_options['layout']); ?>" method="post" action="<?php echo esc_url($post_options['action'] ? $post_options['action'] : $THEMEREX_GLOBALS['ajax_url']); ?>">
			<?php themerex_show_layout($post_options['content']); ?>
			<div class="result sc_infobox"></div>
		</form>
		<?php
	}
}
?>