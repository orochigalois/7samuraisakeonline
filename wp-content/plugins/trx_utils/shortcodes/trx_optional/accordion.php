<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('themerex_sc_accordion_theme_setup')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_sc_accordion_theme_setup' );
	function themerex_sc_accordion_theme_setup() {
		add_action('themerex_action_shortcodes_list', 		'themerex_sc_accordion_reg_shortcodes');
		if (function_exists('themerex_exists_visual_composer') && themerex_exists_visual_composer())
			add_action('themerex_action_shortcodes_list_vc','themerex_sc_accordion_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_accordion counter="off" initial="1"]
	[trx_accordion_item title="Accordion Title 1"]Lorem ipsum dolor sit amet, consectetur adipisicing elit[/trx_accordion_item]
	[trx_accordion_item title="Accordion Title 2"]Proin dignissim commodo magna at luctus. Nam molestie justo augue, nec eleifend urna laoreet non.[/trx_accordion_item]
	[trx_accordion_item title="Accordion Title 3 with custom icons" icon_closed="icon-check" icon_opened="icon-delete"]Curabitur tristique tempus arcu a placerat.[/trx_accordion_item]
[/trx_accordion]
*/
if (!function_exists('themerex_sc_accordion')) {	
	function themerex_sc_accordion($atts, $content=null){	
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			// Individual params
			"initial" => "1",
			"counter" => "off",
			"icon_closed" => "icon-plus",
			"icon_opened" => "icon-minus",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
		$initial = max(0, (int) $initial);
		global $THEMEREX_GLOBALS;
		$THEMEREX_GLOBALS['sc_accordion_counter'] = 0;
		$THEMEREX_GLOBALS['sc_accordion_show_counter'] = themerex_param_is_on($counter);
		$THEMEREX_GLOBALS['sc_accordion_icon_closed'] = empty($icon_closed) || themerex_param_is_inherit($icon_closed) ? "icon-plus" : $icon_closed;
		$THEMEREX_GLOBALS['sc_accordion_icon_opened'] = empty($icon_opened) || themerex_param_is_inherit($icon_opened) ? "icon-minus" : $icon_opened;
        wp_enqueue_script('jquery-ui-accordion', false, array('jquery','jquery-ui-core'), null, true);
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_accordion'
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. (themerex_param_is_on($counter) ? ' sc_show_counter' : '') 
				. '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				. ' data-active="' . ($initial-1) . '"'
				. (!themerex_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_get_animation_classes($animation)).'"' : '')
				. '>'
				. do_shortcode($content)
				. '</div>';
		return apply_filters('themerex_shortcode_output', $output, 'trx_accordion', $atts, $content);
	}
	add_shortcode('trx_accordion', 'themerex_sc_accordion');
}


if (!function_exists('themerex_sc_accordion_item')) {	
	function themerex_sc_accordion_item($atts, $content=null) {
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts( array(
			// Individual params
			"icon_closed" => "",
			"icon_opened" => "",
			"title" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts)));
		global $THEMEREX_GLOBALS;
		$THEMEREX_GLOBALS['sc_accordion_counter']++;
		if (empty($icon_closed) || themerex_param_is_inherit($icon_closed)) $icon_closed = $THEMEREX_GLOBALS['sc_accordion_icon_closed'] ? $THEMEREX_GLOBALS['sc_accordion_icon_closed'] : "icon-plus";
		if (empty($icon_opened) || themerex_param_is_inherit($icon_opened)) $icon_opened = $THEMEREX_GLOBALS['sc_accordion_icon_opened'] ? $THEMEREX_GLOBALS['sc_accordion_icon_opened'] : "icon-minus";
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_accordion_item' 
				. (!empty($class) ? ' '.esc_attr($class) : '')
				. ($THEMEREX_GLOBALS['sc_accordion_counter'] % 2 == 1 ? ' odd' : ' even') 
				. ($THEMEREX_GLOBALS['sc_accordion_counter'] == 1 ? ' first' : '') 
				. '">'
				. '<h5 class="sc_accordion_title">'
				. ($title)
				. (!themerex_param_is_off($icon_closed) ? '<span class="sc_accordion_icon sc_accordion_icon_closed '.esc_attr($icon_closed).'"></span>' : '')
				. (!themerex_param_is_off($icon_opened) ? '<span class="sc_accordion_icon sc_accordion_icon_opened '.esc_attr($icon_opened).'"></span>' : '')
				. ($THEMEREX_GLOBALS['sc_accordion_show_counter'] ? '<span class="sc_items_counter">'.($THEMEREX_GLOBALS['sc_accordion_counter']).'</span>' : '')
				. '</h5>'
				. '<div class="sc_accordion_content"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
					. '>'
					. do_shortcode($content) 
				. '</div>'
				. '</div>';
		return apply_filters('themerex_shortcode_output', $output, 'trx_accordion_item', $atts, $content);
	}
	add_shortcode('trx_accordion_item', 'themerex_sc_accordion_item');
}



/* Add shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_accordion_reg_shortcodes' ) ) {
	//add_action('themerex_action_shortcodes_list', 'themerex_sc_accordion_reg_shortcodes');
	function themerex_sc_accordion_reg_shortcodes() {
		global $THEMEREX_GLOBALS;
	
		$THEMEREX_GLOBALS['shortcodes']["trx_accordion"] = array(
			"title" => esc_html__("Accordion", "trx_utils"),
			"desc" => wp_kses( __("Accordion items", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"counter" => array(
					"title" => esc_html__("Counter", "trx_utils"),
					"desc" => wp_kses( __("Display counter before each accordion title", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "off",
					"type" => "switch",
					"options" => $THEMEREX_GLOBALS['sc_params']['on_off']
				),
				"initial" => array(
					"title" => esc_html__("Initially opened item", "trx_utils"),
					"desc" => wp_kses( __("Number of initially opened item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 1,
					"min" => 0,
					"type" => "spinner"
				),
				"icon_closed" => array(
					"title" => esc_html__("Icon while closed",  'trx_utils'),
					"desc" => wp_kses( __('Select icon for the closed accordion item from Fontello icons set',  'trx_utils'), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "icons",
					"options" => $THEMEREX_GLOBALS['sc_params']['icons']
				),
				"icon_opened" => array(
					"title" => esc_html__("Icon while opened",  'trx_utils'),
					"desc" => wp_kses( __('Select icon for the opened accordion item from Fontello icons set',  'trx_utils'), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "icons",
					"options" => $THEMEREX_GLOBALS['sc_params']['icons']
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
				"name" => "trx_accordion_item",
				"title" => esc_html__("Item", "trx_utils"),
				"desc" => wp_kses( __("Accordion item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
				"container" => true,
				"params" => array(
					"title" => array(
						"title" => esc_html__("Accordion item title", "trx_utils"),
						"desc" => wp_kses( __("Title for current accordion item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"value" => "",
						"type" => "text"
					),
					"icon_closed" => array(
						"title" => esc_html__("Icon while closed",  'trx_utils'),
						"desc" => wp_kses( __('Select icon for the closed accordion item from Fontello icons set',  'trx_utils'), $THEMEREX_GLOBALS['allowed_tags'] ),
						"value" => "",
						"type" => "icons",
						"options" => $THEMEREX_GLOBALS['sc_params']['icons']
					),
					"icon_opened" => array(
						"title" => esc_html__("Icon while opened",  'trx_utils'),
						"desc" => wp_kses( __('Select icon for the opened accordion item from Fontello icons set',  'trx_utils'), $THEMEREX_GLOBALS['allowed_tags'] ),
						"value" => "",
						"type" => "icons",
						"options" => $THEMEREX_GLOBALS['sc_params']['icons']
					),
					"_content_" => array(
						"title" => esc_html__("Accordion item content", "trx_utils"),
						"desc" => wp_kses( __("Current accordion item content", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"rows" => 4,
						"value" => "",
						"type" => "textarea"
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
if ( !function_exists( 'themerex_sc_accordion_reg_shortcodes_vc' ) ) {
	//add_action('themerex_action_shortcodes_list_vc', 'themerex_sc_accordion_reg_shortcodes_vc');
	function themerex_sc_accordion_reg_shortcodes_vc() {
		global $THEMEREX_GLOBALS;
	
		vc_map( array(
			"base" => "trx_accordion",
			"name" => esc_html__("Accordion", "trx_utils"),
			"description" => wp_kses( __("Accordion items", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_accordion',
			"class" => "trx_sc_collection trx_sc_accordion",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => false,
			"as_parent" => array('only' => 'trx_accordion_item'),	// Use only|except attributes to limit child shortcodes (separate multiple values with comma)
			"params" => array(
				array(
					"param_name" => "counter",
					"heading" => esc_html__("Counter", "trx_utils"),
					"description" => wp_kses( __("Display counter before each accordion title", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => array("Add item numbers before each element" => "on" ),
					"type" => "checkbox"
				),
				array(
					"param_name" => "initial",
					"heading" => esc_html__("Initially opened item", "trx_utils"),
					"description" => wp_kses( __("Number of initially opened item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => 1,
					"type" => "textfield"
				),
				array(
					"param_name" => "icon_closed",
					"heading" => esc_html__("Icon while closed", "trx_utils"),
					"description" => wp_kses( __("Select icon for the closed accordion item from Fontello icons set", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon_opened",
					"heading" => esc_html__("Icon while opened", "trx_utils"),
					"description" => wp_kses( __("Select icon for the opened accordion item from Fontello icons set", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
					"type" => "dropdown"
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
				[trx_accordion_item title="' . esc_html__( 'Item 1 title', 'trx_utils' ) . '"][/trx_accordion_item]
				[trx_accordion_item title="' . esc_html__( 'Item 2 title', 'trx_utils' ) . '"][/trx_accordion_item]
			',
			"custom_markup" => '
				<div class="wpb_accordion_holder wpb_holder clearfix vc_container_for_children">
					%content%
				</div>
				<div class="tab_controls">
					<button class="add_tab" title="'.esc_attr__("Add item", "trx_utils").'">'.esc_html__("Add item", "trx_utils").'</button>
				</div>
			',
			'js_view' => 'VcTrxAccordionView'
		) );
		
		
		vc_map( array(
			"base" => "trx_accordion_item",
			"name" => esc_html__("Accordion item", "trx_utils"),
			"description" => wp_kses( __("Inner accordion item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"show_settings_on_create" => true,
			"content_element" => true,
			"is_container" => true,
			'icon' => 'icon_trx_accordion_item',
			"as_child" => array('only' => 'trx_accordion'), 	// Use only|except attributes to limit parent (separate multiple values with comma)
			"as_parent" => array('except' => 'trx_accordion'),
			"params" => array(
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", "trx_utils"),
					"description" => wp_kses( __("Title for current accordion item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "icon_closed",
					"heading" => esc_html__("Icon while closed", "trx_utils"),
					"description" => wp_kses( __("Select icon for the closed accordion item from Fontello icons set", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon_opened",
					"heading" => esc_html__("Icon while opened", "trx_utils"),
					"description" => wp_kses( __("Select icon for the opened accordion item from Fontello icons set", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
					"type" => "dropdown"
				),
				$THEMEREX_GLOBALS['vc_params']['id'],
				$THEMEREX_GLOBALS['vc_params']['class'],
				$THEMEREX_GLOBALS['vc_params']['css']
			),
		  'js_view' => 'VcTrxAccordionTabView'
		) );

		class WPBakeryShortCode_Trx_Accordion extends THEMEREX_VC_ShortCodeAccordion {}
		class WPBakeryShortCode_Trx_Accordion_Item extends THEMEREX_VC_ShortCodeAccordionItem {}
	}
}
?>