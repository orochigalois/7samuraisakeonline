<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('themerex_sc_highlight_theme_setup')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_sc_highlight_theme_setup' );
	function themerex_sc_highlight_theme_setup() {
		add_action('themerex_action_shortcodes_list', 		'themerex_sc_highlight_reg_shortcodes');
		if (function_exists('themerex_exists_visual_composer') && themerex_exists_visual_composer())
			add_action('themerex_action_shortcodes_list_vc','themerex_sc_highlight_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_highlight id="unique_id" color="fore_color's_name_or_#rrggbb" backcolor="back_color's_name_or_#rrggbb" style="custom_style"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_highlight]
*/

if (!function_exists('themerex_sc_highlight')) {	
	function themerex_sc_highlight($atts, $content=null){	
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			// Individual params
			"color" => "",
			"bg_color" => "",
			"font_size" => "",
			"type" => "1",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts)));
		$css .= ($color != '' ? 'color:' . esc_attr($color) . ';' : '')
			.($bg_color != '' ? 'background-color:' . esc_attr($bg_color) . ';' : '')
			.($font_size != '' ? 'font-size:' . esc_attr(themerex_prepare_css_value($font_size)) . '; line-height: 1em;' : '');
		$output = '<span' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_highlight'.($type>0 ? ' sc_highlight_style_'.esc_attr($type) : ''). (!empty($class) ? ' '.esc_attr($class) : '').'"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. '>' 
				. do_shortcode($content) 
				. '</span>';
		return apply_filters('themerex_shortcode_output', $output, 'trx_highlight', $atts, $content);
	}
	add_shortcode('trx_highlight', 'themerex_sc_highlight');
}



/* Add shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_highlight_reg_shortcodes' ) ) {
	//add_action('themerex_action_shortcodes_list', 'themerex_sc_highlight_reg_shortcodes');
	function themerex_sc_highlight_reg_shortcodes() {
		global $THEMEREX_GLOBALS;
	
		$THEMEREX_GLOBALS['shortcodes']["trx_highlight"] = array(
			"title" => esc_html__("Highlight text", "trx_utils"),
			"desc" => wp_kses( __("Highlight text with selected color, background color and other styles", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"type" => array(
					"title" => esc_html__("Type", "trx_utils"),
					"desc" => wp_kses( __("Highlight type", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "1",
					"type" => "checklist",
					"options" => array(
						0 => esc_html__('Custom', 'trx_utils'),
						1 => esc_html__('Type 1', 'trx_utils'),
						2 => esc_html__('Type 2', 'trx_utils'),
						3 => esc_html__('Type 3', 'trx_utils')
					)
				),
				"color" => array(
					"title" => esc_html__("Color", "trx_utils"),
					"desc" => wp_kses( __("Color for the highlighted text", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"divider" => true,
					"value" => "",
					"type" => "color"
				),
				"bg_color" => array(
					"title" => esc_html__("Background color", "trx_utils"),
					"desc" => wp_kses( __("Background color for the highlighted text", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "color"
				),
				"font_size" => array(
					"title" => esc_html__("Font size", "trx_utils"),
					"desc" => wp_kses( __("Font size of the highlighted text (default - in pixels, allows any CSS units of measure)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"_content_" => array(
					"title" => esc_html__("Highlighting content", "trx_utils"),
					"desc" => wp_kses( __("Content for highlight", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
				"id" => $THEMEREX_GLOBALS['sc_params']['id'],
				"class" => $THEMEREX_GLOBALS['sc_params']['class'],
				"css" => $THEMEREX_GLOBALS['sc_params']['css']
			)
		);
	}
}


/* Add shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_highlight_reg_shortcodes_vc' ) ) {
	//add_action('themerex_action_shortcodes_list_vc', 'themerex_sc_highlight_reg_shortcodes_vc');
	function themerex_sc_highlight_reg_shortcodes_vc() {
		global $THEMEREX_GLOBALS;
	
		vc_map( array(
			"base" => "trx_highlight",
			"name" => esc_html__("Highlight text", "trx_utils"),
			"description" => wp_kses( __("Highlight text with selected color, background color and other styles", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_highlight',
			"class" => "trx_sc_single trx_sc_highlight",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "type",
					"heading" => esc_html__("Type", "trx_utils"),
					"description" => wp_kses( __("Highlight type", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
							esc_html__('Custom', 'trx_utils') => 0,
							esc_html__('Type 1', 'trx_utils') => 1,
							esc_html__('Type 2', 'trx_utils') => 2,
							esc_html__('Type 3', 'trx_utils') => 3
						),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Text color", "trx_utils"),
					"description" => wp_kses( __("Color for the highlighted text", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", "trx_utils"),
					"description" => wp_kses( __("Background color for the highlighted text", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "font_size",
					"heading" => esc_html__("Font size", "trx_utils"),
					"description" => wp_kses( __("Font size for the highlighted text (default - in pixels, allows any CSS units of measure)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "content",
					"heading" => esc_html__("Highlight text", "trx_utils"),
					"description" => wp_kses( __("Content for highlight", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
				$THEMEREX_GLOBALS['vc_params']['id'],
				$THEMEREX_GLOBALS['vc_params']['class'],
				$THEMEREX_GLOBALS['vc_params']['css']
			),
			'js_view' => 'VcTrxTextView'
		) );
		
		class WPBakeryShortCode_Trx_Highlight extends Themerex_VC_ShortCodeSingle {}
	}
}
?>