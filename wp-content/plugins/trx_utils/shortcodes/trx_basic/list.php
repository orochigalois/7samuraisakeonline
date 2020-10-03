<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('themerex_sc_list_theme_setup')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_sc_list_theme_setup' );
	function themerex_sc_list_theme_setup() {
		add_action('themerex_action_shortcodes_list', 		'themerex_sc_list_reg_shortcodes');
		if (function_exists('themerex_exists_visual_composer') && themerex_exists_visual_composer())
			add_action('themerex_action_shortcodes_list_vc','themerex_sc_list_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_list id="unique_id" style="arrows|iconed|ol|ul"]
	[trx_list_item id="unique_id" title="title_of_element"]Et adipiscing integer.[/trx_list_item]
	[trx_list_item]A pulvinar ut, parturient enim porta ut sed, mus amet nunc, in.[/trx_list_item]
	[trx_list_item]Duis sociis, elit odio dapibus nec, dignissim purus est magna integer.[/trx_list_item]
	[trx_list_item]Nec purus, cras tincidunt rhoncus proin lacus porttitor rhoncus.[/trx_list_item]
[/trx_list]
*/

if (!function_exists('themerex_sc_list')) {	
	function themerex_sc_list($atts, $content=null){	
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "ul",
			"icon" => "icon-right",
			"icon_color" => "",
			"color" => "",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
		$css .= $color !== '' ? 'color:' . esc_attr($color) .';' : '';
		if (trim($style) == '' || (trim($icon) == '' && $style=='iconed')) $style = 'ul';
		global $THEMEREX_GLOBALS;
		$THEMEREX_GLOBALS['sc_list_counter'] = 0;
		$THEMEREX_GLOBALS['sc_list_icon'] = empty($icon) || themerex_param_is_inherit($icon) ? "icon-right" : $icon;
		$THEMEREX_GLOBALS['sc_list_icon_color'] = $icon_color;
		$THEMEREX_GLOBALS['sc_list_style'] = $style;
		$output = '<' . ($style=='ol' ? 'ol' : 'ul')
				. ($id ? ' id="'.esc_attr($id).'"' : '')
				. ' class="sc_list sc_list_style_' . esc_attr($style) . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. (!themerex_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_get_animation_classes($animation)).'"' : '')
				. '>'
				. do_shortcode($content)
				. '</' .($style=='ol' ? 'ol' : 'ul') . '>';
		return apply_filters('themerex_shortcode_output', $output, 'trx_list', $atts, $content);
	}
	add_shortcode('trx_list', 'themerex_sc_list');
}


if (!function_exists('themerex_sc_list_item')) {	
	function themerex_sc_list_item($atts, $content=null) {
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts( array(
			// Individual params
			"color" => "",
			"icon" => "",
			"icon_color" => "",
			"title" => "",
			"link" => "",
			"target" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts)));
		global $THEMEREX_GLOBALS;
		$THEMEREX_GLOBALS['sc_list_counter']++;
		$css .= $color !== '' ? 'color:' . esc_attr($color) .';' : '';
		if (trim($icon) == '' || themerex_param_is_inherit($icon)) $icon = $THEMEREX_GLOBALS['sc_list_icon'];
		if (trim($color) == '' || themerex_param_is_inherit($icon_color)) $icon_color = $THEMEREX_GLOBALS['sc_list_icon_color'];
		$content = do_shortcode($content);
		if (empty($content)) $content = $title;
		$output = '<li' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_list_item' 
			. (!empty($class) ? ' '.esc_attr($class) : '')
			. ($THEMEREX_GLOBALS['sc_list_counter'] % 2 == 1 ? ' odd' : ' even') 
			. ($THEMEREX_GLOBALS['sc_list_counter'] == 1 ? ' first' : '')  
			. '"' 
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. ($title ? ' title="'.esc_attr($title).'"' : '') 
			. '>' 
			. (!empty($link) ? '<a href="'.esc_url($link).'"' . (!empty($target) ? ' target="'.esc_attr($target).'"' : '') . '>' : '')
			. ($THEMEREX_GLOBALS['sc_list_style']=='iconed' && $icon!='' ? '<span class="sc_list_icon '.esc_attr($icon).'"'.($icon_color !== '' ? ' style="color:'.esc_attr($icon_color).';"' : '').'></span>' : '')
			. trim($content)
			. (!empty($link) ? '</a>': '')
			. '</li>';
		return apply_filters('themerex_shortcode_output', $output, 'trx_list_item', $atts, $content);
	}
	add_shortcode('trx_list_item', 'themerex_sc_list_item');
}



/* Add shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_list_reg_shortcodes' ) ) {
	//add_action('themerex_action_shortcodes_list', 'themerex_sc_list_reg_shortcodes');
	function themerex_sc_list_reg_shortcodes() {
		global $THEMEREX_GLOBALS;
	
		$THEMEREX_GLOBALS['shortcodes']["trx_list"] = array(
			"title" => esc_html__("List", "trx_utils"),
			"desc" => wp_kses( __("List items with specific bullets", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Bullet's style", "trx_utils"),
					"desc" => wp_kses( __("Bullet's style for each list item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "ul",
					"type" => "checklist",
					"options" => $THEMEREX_GLOBALS['sc_params']['list_styles']
				), 
				"color" => array(
					"title" => esc_html__("Color", "trx_utils"),
					"desc" => wp_kses( __("List items color", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "color"
				),
				"icon" => array(
					"title" => esc_html__('List icon',  'trx_utils'),
					"desc" => wp_kses( __("Select list icon from Fontello icons set (only for style=Iconed)",  'trx_utils'), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "",
					"type" => "icons",
					"options" => $THEMEREX_GLOBALS['sc_params']['icons']
				),
				"icon_color" => array(
					"title" => esc_html__("Icon color", "trx_utils"),
					"desc" => wp_kses( __("List icons color", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"dependency" => array(
						'style' => array('iconed')
					),
					"type" => "color"
				),
				"top" => $THEMEREX_GLOBALS['sc_params']['top'],
				"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
				"left" => $THEMEREX_GLOBALS['sc_params']['left'],
				"right" => $THEMEREX_GLOBALS['sc_params']['right'],
				"id" => $THEMEREX_GLOBALS['sc_params']['id'],
				"class" => $THEMEREX_GLOBALS['sc_params']['class'],
				"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
				"css" => $THEMEREX_GLOBALS['sc_params']['css']
			),
			"children" => array(
				"name" => "trx_list_item",
				"title" => esc_html__("Item", "trx_utils"),
				"desc" => wp_kses( __("List item with specific bullet", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
				"decorate" => false,
				"container" => true,
				"params" => array(
					"_content_" => array(
						"title" => esc_html__("List item content", "trx_utils"),
						"desc" => wp_kses( __("Current list item content", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"rows" => 4,
						"value" => "",
						"type" => "textarea"
					),
					"title" => array(
						"title" => esc_html__("List item title", "trx_utils"),
						"desc" => wp_kses( __("Current list item title (show it as tooltip)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"value" => "",
						"type" => "text"
					),
					"color" => array(
						"title" => esc_html__("Color", "trx_utils"),
						"desc" => wp_kses( __("Text color for this item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"value" => "",
						"type" => "color"
					),
					"icon" => array(
						"title" => esc_html__('List icon',  'trx_utils'),
						"desc" => wp_kses( __("Select list item icon from Fontello icons set (only for style=Iconed)",  'trx_utils'), $THEMEREX_GLOBALS['allowed_tags'] ),
						"value" => "",
						"type" => "icons",
						"options" => $THEMEREX_GLOBALS['sc_params']['icons']
					),
					"icon_color" => array(
						"title" => esc_html__("Icon color", "trx_utils"),
						"desc" => wp_kses( __("Icon color for this item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"value" => "",
						"type" => "color"
					),
					"link" => array(
						"title" => esc_html__("Link URL", "trx_utils"),
						"desc" => wp_kses( __("Link URL for the current list item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"divider" => true,
						"value" => "",
						"type" => "text"
					),
					"target" => array(
						"title" => esc_html__("Link target", "trx_utils"),
						"desc" => wp_kses( __("Link target for the current list item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"value" => "",
						"type" => "text"
					),
					"id" => $THEMEREX_GLOBALS['sc_params']['id'],
					"class" => $THEMEREX_GLOBALS['sc_params']['class'],
					"css" => $THEMEREX_GLOBALS['sc_params']['css']
				)
			)
		);
	}
}


/* Add shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_list_reg_shortcodes_vc' ) ) {
	//add_action('themerex_action_shortcodes_list_vc', 'themerex_sc_list_reg_shortcodes_vc');
	function themerex_sc_list_reg_shortcodes_vc() {
		global $THEMEREX_GLOBALS;
	
		vc_map( array(
			"base" => "trx_list",
			"name" => esc_html__("List", "trx_utils"),
			"description" => wp_kses( __("List items with specific bullets", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"category" => esc_html__('Content', 'trx_utils'),
			"class" => "trx_sc_collection trx_sc_list",
			'icon' => 'icon_trx_list',
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => false,
			"as_parent" => array('only' => 'trx_list_item'),
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Bullet's style", "trx_utils"),
					"description" => wp_kses( __("Bullet's style for each list item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"admin_label" => true,
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['list_styles']),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Color", "trx_utils"),
					"description" => wp_kses( __("List items color", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("List icon", "trx_utils"),
					"description" => wp_kses( __("Select list icon from Fontello icons set (only for style=Iconed)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					'dependency' => array(
						'element' => 'style',
						'value' => array('iconed')
					),
					"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon_color",
					"heading" => esc_html__("Icon color", "trx_utils"),
					"description" => wp_kses( __("List icons color", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					'dependency' => array(
						'element' => 'style',
						'value' => array('iconed')
					),
					"value" => "",
					"type" => "colorpicker"
				),
				$THEMEREX_GLOBALS['vc_params']['id'],
				$THEMEREX_GLOBALS['vc_params']['class'],
				$THEMEREX_GLOBALS['vc_params']['animation'],
				$THEMEREX_GLOBALS['vc_params']['css'],
				$THEMEREX_GLOBALS['vc_params']['margin_top'],
				$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
				$THEMEREX_GLOBALS['vc_params']['margin_left'],
				$THEMEREX_GLOBALS['vc_params']['margin_right']
			),
			'default_content' => '
				[trx_list_item][/trx_list_item]
				[trx_list_item][/trx_list_item]
			'
		) );
		
		
		vc_map( array(
			"base" => "trx_list_item",
			"name" => esc_html__("List item", "trx_utils"),
			"description" => wp_kses( __("List item with specific bullet", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"class" => "trx_sc_container trx_sc_list_item",
			"show_settings_on_create" => true,
			"content_element" => true,
			"is_container" => true,
			'icon' => 'icon_trx_list_item',
			"as_child" => array('only' => 'trx_list'), // Use only|except attributes to limit parent (separate multiple values with comma)
			"as_parent" => array('except' => 'trx_list'),
			"params" => array(
				array(
					"param_name" => "title",
					"heading" => esc_html__("List item title", "trx_utils"),
					"description" => wp_kses( __("Title for the current list item (show it as tooltip)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link URL", "trx_utils"),
					"description" => wp_kses( __("Link URL for the current list item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"group" => esc_html__('Link', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "target",
					"heading" => esc_html__("Link target", "trx_utils"),
					"description" => wp_kses( __("Link target for the current list item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"group" => esc_html__('Link', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Color", "trx_utils"),
					"description" => wp_kses( __("Text color for this item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("List item icon", "trx_utils"),
					"description" => wp_kses( __("Select list item icon from Fontello icons set (only for style=Iconed)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon_color",
					"heading" => esc_html__("Icon color", "trx_utils"),
					"description" => wp_kses( __("Icon color for this item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
/*
				array(
					"param_name" => "content",
					"heading" => esc_html__("List item text", "trx_utils"),
					"description" => wp_kses( __("Current list item content", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
*/
				$THEMEREX_GLOBALS['vc_params']['id'],
				$THEMEREX_GLOBALS['vc_params']['class'],
				$THEMEREX_GLOBALS['vc_params']['css']
			)
		
		) );
		
		class WPBakeryShortCode_Trx_List extends Themerex_VC_ShortCodeCollection {}
		class WPBakeryShortCode_Trx_List_Item extends THEMEREX_VC_ShortCodeContainer {}
	}
}
?>