<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('themerex_sc_icon_theme_setup')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_sc_icon_theme_setup' );
	function themerex_sc_icon_theme_setup() {
		add_action('themerex_action_shortcodes_list', 		'themerex_sc_icon_reg_shortcodes');
		if (function_exists('themerex_exists_visual_composer') && themerex_exists_visual_composer())
			add_action('themerex_action_shortcodes_list_vc','themerex_sc_icon_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_icon id="unique_id" style='round|square' icon='' color="" bg_color="" size="" weight=""]
*/

if (!function_exists('themerex_sc_icon')) {	
	function themerex_sc_icon($atts, $content=null){	
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			// Individual params
			"icon" => "",
			"color" => "",
			"bg_color" => "",
			"bg_shape" => "",
			"font_size" => "",
			"font_weight" => "",
			"align" => "",
			"link" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
		$css2 = ($font_weight != '' && !themerex_is_inherit_option($font_weight) ? 'font-weight:'. esc_attr($font_weight).';' : '')
			. ($font_size != '' ? 'font-size:' . esc_attr(themerex_prepare_css_value($font_size)) . '; line-height: ' . (!$bg_shape || themerex_param_is_inherit($bg_shape) ? '1' : '1.2') . 'em;' : '')
			. ($color != '' ? 'color:'.esc_attr($color).';' : '')
			. ($bg_color != '' ? 'background-color:'.esc_attr($bg_color).';border-color:'.esc_attr($bg_color).';' : '')
		;
		$output = $icon!='' 
			? ($link ? '<a href="'.esc_url($link).'"' : '<span') . ($id ? ' id="'.esc_attr($id).'"' : '')
				. ' class="sc_icon '.esc_attr($icon)
					. ($bg_shape && !themerex_param_is_inherit($bg_shape) ? ' sc_icon_shape_'.esc_attr($bg_shape) : '')
					. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '')
				.'"'
				.($css || $css2 ? ' style="'.($class ? 'display:block;' : '') . ($css) . ($css2) . '"' : '')
				.'>'
				.($link ? '</a>' : '</span>')
			: '';
		return apply_filters('themerex_shortcode_output', $output, 'trx_icon', $atts, $content);
	}
	add_shortcode('trx_icon', 'themerex_sc_icon');
}



/* Add shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_icon_reg_shortcodes' ) ) {
	//add_action('themerex_action_shortcodes_list', 'themerex_sc_icon_reg_shortcodes');
	function themerex_sc_icon_reg_shortcodes() {
		global $THEMEREX_GLOBALS;
	
		$THEMEREX_GLOBALS['shortcodes']["trx_icon"] = array(
			"title" => esc_html__("Icon", "trx_utils"),
			"desc" => wp_kses( __("Insert icon", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"icon" => array(
					"title" => esc_html__('Icon',  'trx_utils'),
					"desc" => wp_kses( __('Select font icon from the Fontello icons set',  'trx_utils'), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "icons",
					"options" => $THEMEREX_GLOBALS['sc_params']['icons']
				),
				"color" => array(
					"title" => esc_html__("Icon's color", "trx_utils"),
					"desc" => wp_kses( __("Icon's color", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "",
					"type" => "color"
				),
				"bg_shape" => array(
					"title" => esc_html__("Background shape", "trx_utils"),
					"desc" => wp_kses( __("Shape of the icon background", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "none",
					"type" => "radio",
					"options" => array(
						'none' => esc_html__('None', 'trx_utils'),
						'round' => esc_html__('Round', 'trx_utils'),
						'square' => esc_html__('Square', 'trx_utils')
					)
				),
				"bg_color" => array(
					"title" => esc_html__("Icon's background color", "trx_utils"),
					"desc" => wp_kses( __("Icon's background color", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'icon' => array('not_empty'),
						'background' => array('round','square')
					),
					"value" => "",
					"type" => "color"
				),
				"font_size" => array(
					"title" => esc_html__("Font size", "trx_utils"),
					"desc" => wp_kses( __("Icon's font size", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "",
					"type" => "spinner",
					"min" => 8,
					"max" => 240
				),
				"font_weight" => array(
					"title" => esc_html__("Font weight", "trx_utils"),
					"desc" => wp_kses( __("Icon font weight", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "",
					"type" => "select",
					"size" => "medium",
					"options" => array(
						'100' => esc_html__('Thin (100)', 'trx_utils'),
						'300' => esc_html__('Light (300)', 'trx_utils'),
						'400' => esc_html__('Normal (400)', 'trx_utils'),
						'700' => esc_html__('Bold (700)', 'trx_utils')
					)
				),
				"align" => array(
					"title" => esc_html__("Alignment", "trx_utils"),
					"desc" => wp_kses( __("Icon text alignment", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => $THEMEREX_GLOBALS['sc_params']['align']
				), 
				"link" => array(
					"title" => esc_html__("Link URL", "trx_utils"),
					"desc" => wp_kses( __("Link URL from this icon (if not empty)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"top" => $THEMEREX_GLOBALS['sc_params']['top'],
				"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
				"left" => $THEMEREX_GLOBALS['sc_params']['left'],
				"right" => $THEMEREX_GLOBALS['sc_params']['right'],
				"id" => $THEMEREX_GLOBALS['sc_params']['id'],
				"class" => $THEMEREX_GLOBALS['sc_params']['class'],
				"css" => $THEMEREX_GLOBALS['sc_params']['css']
			)
		);
	}
}


/* Add shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_icon_reg_shortcodes_vc' ) ) {
	//add_action('themerex_action_shortcodes_list_vc', 'themerex_sc_icon_reg_shortcodes_vc');
	function themerex_sc_icon_reg_shortcodes_vc() {
		global $THEMEREX_GLOBALS;
	
		vc_map( array(
			"base" => "trx_icon",
			"name" => esc_html__("Icon", "trx_utils"),
			"description" => wp_kses( __("Insert the icon", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_icon',
			"class" => "trx_sc_single trx_sc_icon",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Icon", "trx_utils"),
					"description" => wp_kses( __("Select icon class from Fontello icons set", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Text color", "trx_utils"),
					"description" => wp_kses( __("Icon's color", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", "trx_utils"),
					"description" => wp_kses( __("Background color for the icon", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_shape",
					"heading" => esc_html__("Background shape", "trx_utils"),
					"description" => wp_kses( __("Shape of the icon background", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('None', 'trx_utils') => 'none',
						esc_html__('Round', 'trx_utils') => 'round',
						esc_html__('Square', 'trx_utils') => 'square'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "font_size",
					"heading" => esc_html__("Font size", "trx_utils"),
					"description" => wp_kses( __("Icon's font size", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "font_weight",
					"heading" => esc_html__("Font weight", "trx_utils"),
					"description" => wp_kses( __("Icon's font weight", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => array(
						esc_html__('Default', 'trx_utils') => 'inherit',
						esc_html__('Thin (100)', 'trx_utils') => '100',
						esc_html__('Light (300)', 'trx_utils') => '300',
						esc_html__('Normal (400)', 'trx_utils') => '400',
						esc_html__('Bold (700)', 'trx_utils') => '700'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Icon's alignment", "trx_utils"),
					"description" => wp_kses( __("Align icon to left, center or right", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
					"type" => "dropdown"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link URL", "trx_utils"),
					"description" => wp_kses( __("Link URL from this icon (if not empty)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				$THEMEREX_GLOBALS['vc_params']['id'],
				$THEMEREX_GLOBALS['vc_params']['class'],
				$THEMEREX_GLOBALS['vc_params']['css'],
				$THEMEREX_GLOBALS['vc_params']['margin_top'],
				$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
				$THEMEREX_GLOBALS['vc_params']['margin_left'],
				$THEMEREX_GLOBALS['vc_params']['margin_right']
			),
		) );
		
		class WPBakeryShortCode_Trx_Icon extends Themerex_VC_ShortCodeSingle {}
	}
}
?>