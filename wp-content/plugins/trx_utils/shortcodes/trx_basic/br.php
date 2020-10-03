<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('themerex_sc_br_theme_setup')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_sc_br_theme_setup' );
	function themerex_sc_br_theme_setup() {
		add_action('themerex_action_shortcodes_list', 		'themerex_sc_br_reg_shortcodes');
		if (function_exists('themerex_exists_visual_composer') && themerex_exists_visual_composer())
			add_action('themerex_action_shortcodes_list_vc','themerex_sc_br_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_br clear="left|right|both"]
*/

if (!function_exists('themerex_sc_br')) {	
	function themerex_sc_br($atts, $content = null) {
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			"clear" => ""
		), $atts)));
		$output = in_array($clear, array('left', 'right', 'both', 'all')) 
			? '<div class="clearfix" style="clear:' . str_replace('all', 'both', $clear) . '"></div>'
			: '<br />';
		return apply_filters('themerex_shortcode_output', $output, 'trx_br', $atts, $content);
	}
	add_shortcode("trx_br", "themerex_sc_br");
}



/* Add shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_br_reg_shortcodes' ) ) {
	//add_action('themerex_action_shortcodes_list', 'themerex_sc_br_reg_shortcodes');
	function themerex_sc_br_reg_shortcodes() {
		global $THEMEREX_GLOBALS;
	
		$THEMEREX_GLOBALS['shortcodes']["trx_br"] = array(
			"title" => esc_html__("Break", "trx_utils"),
			"desc" => wp_kses( __("Line break with clear floating (if need)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"clear" => 	array(
					"title" => esc_html__("Clear floating", "trx_utils"),
					"desc" => wp_kses( __("Clear floating (if need)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "checklist",
					"options" => array(
						'none' => esc_html__('None', 'trx_utils'),
						'left' => esc_html__('Left', 'trx_utils'),
						'right' => esc_html__('Right', 'trx_utils'),
						'both' => esc_html__('Both', 'trx_utils')
					)
				)
			)
		);
	}
}


/* Add shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_br_reg_shortcodes_vc' ) ) {
	//add_action('themerex_action_shortcodes_list_vc', 'themerex_sc_br_reg_shortcodes_vc');
	function themerex_sc_br_reg_shortcodes_vc() {
		global $THEMEREX_GLOBALS;
/*
		vc_map( array(
			"base" => "trx_br",
			"name" => esc_html__("Line break", "trx_utils"),
			"description" => wp_kses( __("Line break or Clear Floating", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_br',
			"class" => "trx_sc_single trx_sc_br",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "clear",
					"heading" => esc_html__("Clear floating", "trx_utils"),
					"description" => wp_kses( __("Select clear side (if need)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"value" => array(
						esc_html__('None', 'trx_utils') => 'none',
						esc_html__('Left', 'trx_utils') => 'left',
						esc_html__('Right', 'trx_utils') => 'right',
						esc_html__('Both', 'trx_utils') => 'both'
					),
					"type" => "dropdown"
				)
			)
		) );
		
		class WPBakeryShortCode_Trx_Br extends Themerex_VC_ShortCodeSingle {}
*/
	}
}
?>