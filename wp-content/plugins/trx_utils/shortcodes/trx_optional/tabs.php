<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('themerex_sc_tabs_theme_setup')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_sc_tabs_theme_setup' );
	function themerex_sc_tabs_theme_setup() {
		add_action('themerex_action_shortcodes_list', 		'themerex_sc_tabs_reg_shortcodes');
		if (function_exists('themerex_exists_visual_composer') && themerex_exists_visual_composer())
			add_action('themerex_action_shortcodes_list_vc','themerex_sc_tabs_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_tabs id="unique_id" tab_names="Planning|Development|Support" style="1|2" initial="1 - num_tabs"]
	[trx_tab]Randomised words which don't look even slightly believable. If you are going to use a passage. You need to be sure there isn't anything embarrassing hidden in the middle of text established fact that a reader will be istracted by the readable content of a page when looking at its layout.[/trx_tab]
	[trx_tab]Fact reader will be distracted by the <a href="#" class="main_link">readable content</a> of a page when. Looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using content here, content here, making it look like readable English will uncover many web sites still in their infancy. Various versions have evolved over. There are many variations of passages of Lorem Ipsum available, but the majority.[/trx_tab]
	[trx_tab]Distracted by the  readable content  of a page when. Looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using content here, content here, making it look like readable English will uncover many web sites still in their infancy. Various versions have  evolved over.  There are many variations of passages of Lorem Ipsum available.[/trx_tab]
[/trx_tabs]
*/
global $THEMEREX_GLOBALS;
$THEMEREX_GLOBALS['sc_tab_counter'] = 0;
$THEMEREX_GLOBALS['sc_tab_scroll'] =  "no";
$THEMEREX_GLOBALS['sc_tab_height'] = '';
$THEMEREX_GLOBALS['sc_tab_id']     = '';
$THEMEREX_GLOBALS['sc_tab_titles'] = array();
		
if (!function_exists('themerex_sc_tabs')) {	
	function themerex_sc_tabs($atts, $content = null) {
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			// Individual params
			"initial" => "1",
			"scroll" => "no",
			"style" => "1",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
	
		$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
		$css .= themerex_get_css_dimensions_from_values($width);
	
		if (!themerex_param_is_off($scroll)) themerex_enqueue_slider();
		if (empty($id)) $id = 'sc_tabs_'.str_replace('.', '', mt_rand());
	
		global $THEMEREX_GLOBALS;
		$THEMEREX_GLOBALS['sc_tab_counter'] = 0;
		$THEMEREX_GLOBALS['sc_tab_scroll'] = $scroll;
		$THEMEREX_GLOBALS['sc_tab_height'] = themerex_prepare_css_value($height);
		$THEMEREX_GLOBALS['sc_tab_id']     = $id;
		$THEMEREX_GLOBALS['sc_tab_titles'] = array();
	
		$content = do_shortcode($content);
	
		$sc_tab_titles = $THEMEREX_GLOBALS['sc_tab_titles'];
	
		$initial = max(1, min(count($sc_tab_titles), (int) $initial));
	
		$tabs_output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
							. ' class="sc_tabs sc_tabs_style_'.esc_attr($style) . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
							. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
							. (!themerex_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_get_animation_classes($animation)).'"' : '')
							. ' data-active="' . ($initial-1) . '"'
							. '>'
						.'<ul class="sc_tabs_titles">';
		$titles_output = '';
		for ($i = 0; $i < count($sc_tab_titles); $i++) {
			$classes = array('sc_tabs_title');
			if ($i == 0) $classes[] = 'first';
			else if ($i == count($sc_tab_titles) - 1) $classes[] = 'last';
			$titles_output .= '<li class="'.join(' ', $classes).'">'
								. '<a href="#'.esc_attr($sc_tab_titles[$i]['id']).'" class="theme_button" id="'.esc_attr($sc_tab_titles[$i]['id']).'_tab">' . ($sc_tab_titles[$i]['title']) . '</a>'
								. '</li>';
		}

        wp_enqueue_script('jquery-ui-tabs', false, array('jquery','jquery-ui-core'), null, true);
        wp_enqueue_script('jquery-effects-fade', false, array('jquery','jquery-effects-core'), null, true);
	
		$tabs_output .= $titles_output
			. '</ul>' 
			. ($content)
			.'</div>';
		return apply_filters('themerex_shortcode_output', $tabs_output, 'trx_tabs', $atts, $content);
	}
	add_shortcode("trx_tabs", "themerex_sc_tabs");
}


if (!function_exists('themerex_sc_tab')) {	
	function themerex_sc_tab($atts, $content = null) {
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			// Individual params
			"tab_id" => "",		// get it from VC
			"title" => "",		// get it from VC
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts)));
		global $THEMEREX_GLOBALS;
		$THEMEREX_GLOBALS['sc_tab_counter']++;
		if (empty($id))
			$id = !empty($tab_id) ? $tab_id : ($THEMEREX_GLOBALS['sc_tab_id']).'_'.($THEMEREX_GLOBALS['sc_tab_counter']);
		$sc_tab_titles = $THEMEREX_GLOBALS['sc_tab_titles'];
		if (isset($sc_tab_titles[$THEMEREX_GLOBALS['sc_tab_counter']-1])) {
			$sc_tab_titles[$THEMEREX_GLOBALS['sc_tab_counter']-1]['id'] = $id;
			if (!empty($title))
				$sc_tab_titles[$THEMEREX_GLOBALS['sc_tab_counter']-1]['title'] = $title;
		} else {
			$sc_tab_titles[] = array(
				'id' => $id,
				'title' => $title
			);
		}
		$THEMEREX_GLOBALS['sc_tab_titles'] = $sc_tab_titles;
		$output = '<div id="'.esc_attr($id).'"'
					.' class="sc_tabs_content' 
						. ($THEMEREX_GLOBALS['sc_tab_counter'] % 2 == 1 ? ' odd' : ' even') 
						. ($THEMEREX_GLOBALS['sc_tab_counter'] == 1 ? ' first' : '') 
						. (!empty($class) ? ' '.esc_attr($class) : '') 
						. '"'
						. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
						. '>' 
				. (themerex_param_is_on($THEMEREX_GLOBALS['sc_tab_scroll']) 
					? '<div id="'.esc_attr($id).'_scroll" class="sc_scroll sc_scroll_vertical" style="height:'.($THEMEREX_GLOBALS['sc_tab_height'] != '' ? $THEMEREX_GLOBALS['sc_tab_height'] : '200px').';"><div class="sc_scroll_wrapper swiper-wrapper"><div class="sc_scroll_slide swiper-slide">' 
					: '')
				. do_shortcode($content) 
				. (themerex_param_is_on($THEMEREX_GLOBALS['sc_tab_scroll']) 
					? '</div></div><div id="'.esc_attr($id).'_scroll_bar" class="sc_scroll_bar sc_scroll_bar_vertical '.esc_attr($id).'_scroll_bar"></div></div>' 
					: '')
			. '</div>';
		return apply_filters('themerex_shortcode_output', $output, 'trx_tab', $atts, $content);
	}
	add_shortcode("trx_tab", "themerex_sc_tab");
}



/* Add shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_tabs_reg_shortcodes' ) ) {
	//add_action('themerex_action_shortcodes_list', 'themerex_sc_tabs_reg_shortcodes');
	function themerex_sc_tabs_reg_shortcodes() {
		global $THEMEREX_GLOBALS;
	
		$THEMEREX_GLOBALS['shortcodes']["trx_tabs"] = array(
			"title" => esc_html__("Tabs", "trx_utils"),
			"desc" => wp_kses( __("Insert tabs in your page (post)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Tabs style", "trx_utils"),
					"desc" => wp_kses( __("Select style for tabs items", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 1,
					"options" => themerex_get_list_styles(1, 2),
					"type" => "radio"
				),
				"initial" => array(
					"title" => esc_html__("Initially opened tab", "trx_utils"),
					"desc" => wp_kses( __("Number of initially opened tab", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"divider" => true,
					"value" => 1,
					"min" => 0,
					"type" => "spinner"
				),
				"scroll" => array(
					"title" => esc_html__("Use scroller", "trx_utils"),
					"desc" => wp_kses( __("Use scroller to show tab content (height parameter required)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"divider" => true,
					"value" => "no",
					"type" => "switch",
					"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
				),
				"width" => themerex_shortcodes_width(),
				"height" => themerex_shortcodes_height(),
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
				"name" => "trx_tab",
				"title" => esc_html__("Tab", "trx_utils"),
				"desc" => wp_kses( __("Tab item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
				"container" => true,
				"params" => array(
					"title" => array(
						"title" => esc_html__("Tab title", "trx_utils"),
						"desc" => wp_kses( __("Current tab title", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"value" => "",
						"type" => "text"
					),
					"_content_" => array(
						"title" => esc_html__("Tab content", "trx_utils"),
						"desc" => wp_kses( __("Current tab content", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"divider" => true,
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
if ( !function_exists( 'themerex_sc_tabs_reg_shortcodes_vc' ) ) {
	//add_action('themerex_action_shortcodes_list_vc', 'themerex_sc_tabs_reg_shortcodes_vc');
	function themerex_sc_tabs_reg_shortcodes_vc() {
		global $THEMEREX_GLOBALS;
	
		$tab_id_1 = 'sc_tab_'.time() . '_1_' . rand( 0, 100 );
		$tab_id_2 = 'sc_tab_'.time() . '_2_' . rand( 0, 100 );
		vc_map( array(
			"base" => "trx_tabs",
			"name" => esc_html__("Tabs", "trx_utils"),
			"desc" => wp_kses( __("Tabs", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_tabs',
			"class" => "trx_sc_collection trx_sc_tabs",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => false,
			"as_parent" => array('only' => 'trx_tab'),
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Tabs style", "trx_utils"),
					"desc" => wp_kses( __("Select style of tabs items", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(themerex_get_list_styles(1, 2)),
					"type" => "dropdown"
				),
				array(
					"param_name" => "initial",
					"heading" => esc_html__("Initially opened tab", "trx_utils"),
					"desc" => wp_kses( __("Number of initially opened tab", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => 1,
					"type" => "textfield"
				),
				array(
					"param_name" => "scroll",
					"heading" => esc_html__("Scroller", "trx_utils"),
					"desc" => wp_kses( __("Use scroller to show tab content (height parameter required)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => array("Use scroller" => "yes" ),
					"type" => "checkbox"
				),
				$THEMEREX_GLOBALS['vc_params']['id'],
				$THEMEREX_GLOBALS['vc_params']['class'],
				$THEMEREX_GLOBALS['vc_params']['animation'],
				$THEMEREX_GLOBALS['vc_params']['css'],
				themerex_vc_width(),
				themerex_vc_height(),
				$THEMEREX_GLOBALS['vc_params']['margin_top'],
				$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
				$THEMEREX_GLOBALS['vc_params']['margin_left'],
				$THEMEREX_GLOBALS['vc_params']['margin_right']
			),
			'default_content' => '
				[trx_tab title="' . esc_html__( 'Tab 1', 'trx_utils' ) . '" tab_id="'.esc_attr($tab_id_1).'"][/trx_tab]
				[trx_tab title="' . esc_html__( 'Tab 2', 'trx_utils' ) . '" tab_id="'.esc_attr($tab_id_2).'"][/trx_tab]
			',
			"custom_markup" => '
				<div class="wpb_tabs_holder wpb_holder vc_container_for_children">
					<ul class="tabs_controls">
					</ul>
					%content%
				</div>
			',
			'js_view' => 'VcTrxTabsView'
		) );
		
		
		vc_map( array(
			"base" => "trx_tab",
			"name" => esc_html__("Tab item", "trx_utils"),
			"desc" => wp_kses( __("Single tab item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"show_settings_on_create" => true,
			"class" => "trx_sc_collection trx_sc_tab",
			"content_element" => true,
			"is_container" => true,
			'icon' => 'icon_trx_tab',
			"as_child" => array('only' => 'trx_tabs'),
			"as_parent" => array('except' => 'trx_tabs'),
			"params" => array(
				array(
					"param_name" => "title",
					"heading" => esc_html__("Tab title", "trx_utils"),
					"desc" => wp_kses( __("Title for current tab", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "tab_id",
					"heading" => esc_html__("Tab ID", "trx_utils"),
					"desc" => wp_kses( __("ID for current tab (required). Please, start it from letter.", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				$THEMEREX_GLOBALS['vc_params']['id'],
				$THEMEREX_GLOBALS['vc_params']['class'],
				$THEMEREX_GLOBALS['vc_params']['css']
			),
		  'js_view' => 'VcTrxTabView'
		) );
		class WPBakeryShortCode_Trx_Tabs extends THEMEREX_VC_ShortCodeTabs {}
		class WPBakeryShortCode_Trx_Tab extends THEMEREX_VC_ShortCodeTab {}
	}
}
?>