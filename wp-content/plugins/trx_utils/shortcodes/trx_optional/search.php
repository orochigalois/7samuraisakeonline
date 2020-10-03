<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('themerex_sc_search_theme_setup')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_sc_search_theme_setup' );
	function themerex_sc_search_theme_setup() {
		add_action('themerex_action_shortcodes_list', 		'themerex_sc_search_reg_shortcodes');
		if (function_exists('themerex_exists_visual_composer') && themerex_exists_visual_composer())
			add_action('themerex_action_shortcodes_list_vc','themerex_sc_search_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_search id="unique_id" open="yes|no"]
*/

if (!function_exists('themerex_sc_search')) {	
	function themerex_sc_search($atts, $content=null){	
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "regular",
			"state" => "fixed",
			"scheme" => "original",
			"ajax" => "",
			"title" => esc_html__('Search', 'trx_utils'),
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
		if (empty($ajax)) $ajax = themerex_get_theme_option('use_ajax_search');
		// Load core messages
		themerex_enqueue_messages();
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') . ' class="search_wrap search_style_'.esc_attr($style).' search_state_'.esc_attr($state)
						. (themerex_param_is_on($ajax) ? ' search_ajax' : '')
						. ($class ? ' '.esc_attr($class) : '')
						. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
					. (!themerex_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_get_animation_classes($animation)).'"' : '')
					. '>
						<div class="search_form_wrap">
							<form method="get" class="search_form" action="' . esc_url( home_url( '/' ) ) . '">
								<button type="submit" class="search_submit icon-search97" title="' . ($state=='closed' ? esc_attr__('Open search', 'trx_utils') : esc_attr__('Start search', 'trx_utils')) . '"></button>
								<input type="text" class="search_field" placeholder="' . esc_attr($title) . '" value="' . esc_attr(get_search_query()) . '" name="s" />
							</form>
						</div>
						<div class="search_results widget_area' . ($scheme && !themerex_param_is_off($scheme) && !themerex_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') . '"><a class="search_results_close icon-cancel"></a><div class="search_results_content"></div></div>
				</div>';
		return apply_filters('themerex_shortcode_output', $output, 'trx_search', $atts, $content);
	}
	add_shortcode('trx_search', 'themerex_sc_search');
}



/* Add shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_search_reg_shortcodes' ) ) {
	//add_action('themerex_action_shortcodes_list', 'themerex_sc_search_reg_shortcodes');
	function themerex_sc_search_reg_shortcodes() {
		global $THEMEREX_GLOBALS;
	
		$THEMEREX_GLOBALS['shortcodes']["trx_search"] = array(
			"title" => esc_html__("Search", "trx_utils"),
			"desc" => wp_kses( __("Show search form", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Style", "trx_utils"),
					"desc" => wp_kses( __("Select style to display search field", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "regular",
					"options" => array(
						"regular" => esc_html__('Regular', 'trx_utils'),
						"rounded" => esc_html__('Rounded', 'trx_utils')
					),
					"type" => "checklist"
				),
				"state" => array(
					"title" => esc_html__("State", "trx_utils"),
					"desc" => wp_kses( __("Select search field initial state", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "fixed",
					"options" => array(
						"fixed"  => esc_html__('Fixed',  'trx_utils'),
						"opened" => esc_html__('Opened', 'trx_utils'),
						"closed" => esc_html__('Closed', 'trx_utils')
					),
					"type" => "checklist"
				),
				"title" => array(
					"title" => esc_html__("Title", "trx_utils"),
					"desc" => wp_kses( __("Title (placeholder) for the search field", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => esc_html__("Search &hellip;", 'trx_utils'),
					"type" => "text"
				),
				"ajax" => array(
					"title" => esc_html__("AJAX", "trx_utils"),
					"desc" => wp_kses( __("Search via AJAX or reload page", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "yes",
					"options" => $THEMEREX_GLOBALS['sc_params']['yes_no'],
					"type" => "switch"
				),
				"top" => $THEMEREX_GLOBALS['sc_params']['top'],
				"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
				"left" => $THEMEREX_GLOBALS['sc_params']['left'],
				"right" => $THEMEREX_GLOBALS['sc_params']['right'],
				"id" => $THEMEREX_GLOBALS['sc_params']['id'],
				"class" => $THEMEREX_GLOBALS['sc_params']['class'],
				"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
				"css" => $THEMEREX_GLOBALS['sc_params']['css']
			)
		);
	}
}


/* Add shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_search_reg_shortcodes_vc' ) ) {
	//add_action('themerex_action_shortcodes_list_vc', 'themerex_sc_search_reg_shortcodes_vc');
	function themerex_sc_search_reg_shortcodes_vc() {
		global $THEMEREX_GLOBALS;
	
		vc_map( array(
			"base" => "trx_search",
			"name" => esc_html__("Search form", "trx_utils"),
			"description" => wp_kses( __("Insert search form", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_search',
			"class" => "trx_sc_single trx_sc_search",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Style", "trx_utils"),
					"description" => wp_kses( __("Select style to display search field", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => array(
						esc_html__('Regular', 'trx_utils') => "regular",
						esc_html__('Flat', 'trx_utils') => "flat"
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "state",
					"heading" => esc_html__("State", "trx_utils"),
					"description" => wp_kses( __("Select search field initial state", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => array(
						esc_html__('Fixed', 'trx_utils')  => "fixed",
						esc_html__('Opened', 'trx_utils') => "opened",
						esc_html__('Closed', 'trx_utils') => "closed"
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", "trx_utils"),
					"description" => wp_kses( __("Title (placeholder) for the search field", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => esc_html__("Search &hellip;", 'trx_utils'),
					"type" => "textfield"
				),
				array(
					"param_name" => "ajax",
					"heading" => esc_html__("AJAX", "trx_utils"),
					"description" => wp_kses( __("Search via AJAX or reload page", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => array(esc_html__('Use AJAX search', 'trx_utils') => 'yes'),
					"type" => "checkbox"
				),
				$THEMEREX_GLOBALS['vc_params']['id'],
				$THEMEREX_GLOBALS['vc_params']['class'],
				$THEMEREX_GLOBALS['vc_params']['animation'],
				$THEMEREX_GLOBALS['vc_params']['css'],
				$THEMEREX_GLOBALS['vc_params']['margin_top'],
				$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
				$THEMEREX_GLOBALS['vc_params']['margin_left'],
				$THEMEREX_GLOBALS['vc_params']['margin_right']
			)
		) );
		
		class WPBakeryShortCode_Trx_Search extends Themerex_VC_ShortCodeSingle {}
	}
}
?>