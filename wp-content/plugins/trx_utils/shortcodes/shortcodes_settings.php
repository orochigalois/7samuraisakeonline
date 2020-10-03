<?php

// Check if shortcodes settings are now used
if ( !function_exists( 'themerex_shortcodes_is_used' ) ) {
	function themerex_shortcodes_is_used() {
		return themerex_options_is_used() 															// All modes when Theme Options are used
			|| (is_admin() && isset($_POST['action']) 
					&& in_array($_POST['action'], array('vc_edit_form', 'wpb_show_edit_form')))		// AJAX query when save post/page
			|| (function_exists('themerex_vc_is_frontend') && themerex_vc_is_frontend());		// VC Frontend editor mode
	}
}

// Width and height params
if ( !function_exists( 'themerex_shortcodes_width' ) ) {
	function themerex_shortcodes_width($w="") {
		return array(
			"title" => esc_html__("Width", "trx_utils"),
			"divider" => true,
			"value" => $w,
			"type" => "text"
		);
	}
}
if ( !function_exists( 'themerex_shortcodes_height' ) ) {
	function themerex_shortcodes_height($h='') {
		global $THEMEREX_GLOBALS;
		return array(
			"title" => esc_html__("Height", "trx_utils"),
			"desc" => wp_kses( __("Width (in pixels or percent) and height (only in pixels) of element", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"value" => $h,
			"type" => "text"
		);
	}
}

/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'themerex_shortcodes_settings_theme_setup' ) ) {
//	if ( themerex_vc_is_frontend() )
	if ( (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') || (isset($_GET['vc_action']) && $_GET['vc_action']=='vc_inline') )
		add_action( 'themerex_action_before_init_theme', 'themerex_shortcodes_settings_theme_setup', 20 );
	else
		add_action( 'themerex_action_after_init_theme', 'themerex_shortcodes_settings_theme_setup' );
	function themerex_shortcodes_settings_theme_setup() {
		if (themerex_shortcodes_is_used()) {
			global $THEMEREX_GLOBALS;

			// Sort templates alphabetically
			ksort($THEMEREX_GLOBALS['registered_templates']);

			// Prepare arrays 
			$THEMEREX_GLOBALS['sc_params'] = array(
			
				// Current element id
				'id' => array(
					"title" => esc_html__("Element ID", "trx_utils"),
					"desc" => wp_kses( __("ID for current element", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
			
				// Current element class
				'class' => array(
					"title" => esc_html__("Element CSS class", "trx_utils"),
					"desc" => wp_kses( __("CSS class for current element (optional)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
			
				// Current element style
				'css' => array(
					"title" => esc_html__("CSS styles", "trx_utils"),
					"desc" => wp_kses( __("Any additional CSS rules (if need)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
			
			
				// Switcher choises
				'list_styles' => array(
					'ul'	=> esc_html__('Unordered', 'trx_utils'),
					'ol'	=> esc_html__('Ordered', 'trx_utils'),
					'iconed'=> esc_html__('Iconed', 'trx_utils')
				),
				'yes_no'	=> themerex_get_list_yesno(),
				'on_off'	=> themerex_get_list_onoff(),
				'dir' 		=> themerex_get_list_directions(),
				'align'		=> themerex_get_list_alignments(),
				'float'		=> themerex_get_list_floats(),
				'show_hide'	=> themerex_get_list_showhide(),
				'sorting' 	=> themerex_get_list_sortings(),
				'ordering' 	=> themerex_get_list_orderings(),
				'shapes'	=> themerex_get_list_shapes(),
				'sizes'		=> themerex_get_list_sizes(),
				'sliders'	=> themerex_get_list_sliders(),
				'categories'=> themerex_get_list_categories(),
				'columns'	=> themerex_get_list_columns(),
				'images'	=> array_merge(array('none'=>"none"), themerex_get_list_files("images/icons", "png")),
				'icons'		=> array_merge(array("inherit", "none"), themerex_get_list_icons()),
				'locations'	=> themerex_get_list_dedicated_locations(),
				'filters'	=> themerex_get_list_portfolio_filters(),
				'formats'	=> themerex_get_list_post_formats_filters(),
				'hovers'	=> themerex_get_list_hovers(true),
				'hovers_dir'=> themerex_get_list_hovers_directions(true),
				'schemes'	=> themerex_get_list_color_schemes(true),
				'animations'		=> themerex_get_list_animations_in(),
				'margins' 			=> themerex_get_list_margins(true),
				'blogger_styles'	=> themerex_get_list_templates_blogger(),
				'forms'				=> themerex_get_list_templates_forms(),
				'posts_types'		=> themerex_get_list_posts_types(),
				'googlemap_styles'	=> themerex_get_list_googlemap_styles(),
				'field_types'		=> themerex_get_list_field_types(),
				'label_positions'	=> themerex_get_list_label_positions()
			);

			$THEMEREX_GLOBALS['sc_params']['animation'] = array(
				"title" => esc_html__("Animation",  'trx_utils'),
				"desc" => wp_kses( __('Select animation while object enter in the visible area of page',  'trx_utils'), $THEMEREX_GLOBALS['allowed_tags'] ),
				"value" => "none",
				"type" => "select",
				"options" => $THEMEREX_GLOBALS['sc_params']['animations']
			);
			$THEMEREX_GLOBALS['sc_params']['top'] = array(
				"title" => esc_html__("Top margin",  'trx_utils'),
				"divider" => true,
				"value" => "",
				"type" => "text"
			);
			$THEMEREX_GLOBALS['sc_params']['bottom'] = array(
				"title" => esc_html__("Bottom margin",  'trx_utils'),
				"value" => "",
				"type" => "text"
			);
			$THEMEREX_GLOBALS['sc_params']['left'] = array(
				"title" => esc_html__("Left margin",  'trx_utils'),
				"value" => "",
				"type" => "text"
			);
			$THEMEREX_GLOBALS['sc_params']['right'] = array(
				"title" => esc_html__("Right margin",  'trx_utils'),
				"desc" => wp_kses( __("Margins around this shortcode", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
				"value" => "",
				"type" => "text"
			);

			$THEMEREX_GLOBALS['sc_params'] = apply_filters('themerex_filter_shortcodes_params', $THEMEREX_GLOBALS['sc_params']);

	
			// Shortcodes list
			//------------------------------------------------------------------
			$THEMEREX_GLOBALS['shortcodes'] = array();
			
			// Add shortcodes
			do_action('themerex_action_shortcodes_list');

			// Sort shortcodes list
			ksort($THEMEREX_GLOBALS['shortcodes']);
		}
	}
}
?>