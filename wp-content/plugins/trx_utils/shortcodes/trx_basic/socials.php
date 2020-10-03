<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('themerex_sc_socials_theme_setup')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_sc_socials_theme_setup' );
	function themerex_sc_socials_theme_setup() {
		add_action('themerex_action_shortcodes_list', 		'themerex_sc_socials_reg_shortcodes');
		if (function_exists('themerex_exists_visual_composer') && themerex_exists_visual_composer())
			add_action('themerex_action_shortcodes_list_vc','themerex_sc_socials_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_socials id="unique_id" size="small"]
	[trx_social_item name="facebook" url="profile url" icon="path for the icon"]
	[trx_social_item name="twitter" url="profile url"]
[/trx_socials]
*/

if (!function_exists('themerex_sc_socials')) {
	function themerex_sc_socials($atts, $content=null){
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			// Individual params
			"size" => "small",		// tiny | small | medium | large
			"shape" => "square",	// round | square
			"type" => themerex_get_theme_setting('socials_type'),	// icons | images
			"socials" => "",
			"custom" => "no",
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
		global $THEMEREX_GLOBALS;
		$THEMEREX_GLOBALS['sc_social_icons'] = false;
		$THEMEREX_GLOBALS['sc_social_type'] = $type;
		if (!empty($socials)) {
			$allowed = explode('|', $socials);
			$list = array();
			for ($i=0; $i<count($allowed); $i++) {
				$s = explode('=', $allowed[$i]);
				if (!empty($s[1])) {
					$list[] = array(
						'icon'	=> $type=='images' ? themerex_get_socials_url($s[0]) : 'icon-'.$s[0],
						'url'	=> $s[1]
						);
				}
			}
			if (count($list) > 0) $THEMEREX_GLOBALS['sc_social_icons'] = $list;
		} else if (themerex_param_is_off($custom))
			$content = do_shortcode($content);
		if ($THEMEREX_GLOBALS['sc_social_icons']===false) $THEMEREX_GLOBALS['sc_social_icons'] = themerex_get_custom_option('social_icons');
		$output = themerex_prepare_socials($THEMEREX_GLOBALS['sc_social_icons']);
		$output = $output
			? '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
				. ' class="sc_socials sc_socials_type_' . esc_attr($type) . ' sc_socials_shape_' . esc_attr($shape) . ' sc_socials_size_' . esc_attr($size) . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. (!themerex_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_get_animation_classes($animation)).'"' : '')
				. '>'
				. ($output)
				. '</div>'
			: '';
		return apply_filters('themerex_shortcode_output', $output, 'trx_socials', $atts, $content);
	}
	add_shortcode('trx_socials', 'themerex_sc_socials');
}


if (!function_exists('themerex_sc_social_item')) {
	function themerex_sc_social_item($atts, $content=null){
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			// Individual params
			"name" => "",
			"url" => "",
			"icon" => ""
		), $atts)));
		global $THEMEREX_GLOBALS;
		if (!empty($name) && empty($icon)) {
			$type = $THEMEREX_GLOBALS['sc_social_type'];
			if ($type=='images') {
				if (file_exists(themerex_get_socials_dir($name.'.png')))
					$icon = themerex_get_socials_url($name.'.png');
			} else
				$icon = 'icon-'.esc_attr($name);
		}
		if (!empty($icon) && !empty($url)) {
			if ($THEMEREX_GLOBALS['sc_social_icons']===false) $THEMEREX_GLOBALS['sc_social_icons'] = array();
			$THEMEREX_GLOBALS['sc_social_icons'][] = array(
				'icon' => $icon,
				'url' => $url
			);
		}
		return '';
	}
	add_shortcode('trx_social_item', 'themerex_sc_social_item');
}



/* Add shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_socials_reg_shortcodes' ) ) {
	//add_action('themerex_action_shortcodes_list', 'themerex_sc_socials_reg_shortcodes');
	function themerex_sc_socials_reg_shortcodes() {
		global $THEMEREX_GLOBALS;
	
		$THEMEREX_GLOBALS['shortcodes']["trx_socials"] = array(
			"title" => esc_html__("Social icons", "trx_utils"),
			"desc" => wp_kses( __("List of social icons (with hovers)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"type" => array(
					"title" => esc_html__("Icon's type", "trx_utils"),
					"desc" => wp_kses( __("Type of the icons - images or font icons", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => themerex_get_theme_setting('socials_type'),
					"options" => array(
						'icons' => esc_html__('Icons', 'trx_utils'),
						'images' => esc_html__('Images', 'trx_utils')
					),
					"type" => "checklist"
				), 
				"size" => array(
					"title" => esc_html__("Icon's size", "trx_utils"),
					"desc" => wp_kses( __("Size of the icons", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "small",
					"options" => $THEMEREX_GLOBALS['sc_params']['sizes'],
					"type" => "checklist"
				), 
				"shape" => array(
					"title" => esc_html__("Icon's shape", "trx_utils"),
					"desc" => wp_kses( __("Shape of the icons", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "square",
					"options" => $THEMEREX_GLOBALS['sc_params']['shapes'],
					"type" => "checklist"
				), 
				"socials" => array(
					"title" => esc_html__("Manual socials list", "trx_utils"),
					"desc" => wp_kses( __("Custom list of social networks. For example: twitter=http://twitter.com/my_profile|facebook=http://facebook.com/my_profile. If empty - use socials from Theme options.", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"custom" => array(
					"title" => esc_html__("Custom socials", "trx_utils"),
					"desc" => wp_kses( __("Make custom icons from inner shortcodes (prepare it on tabs)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"divider" => true,
					"value" => "no",
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
			),
			"children" => array(
				"name" => "trx_social_item",
				"title" => esc_html__("Custom social item", "trx_utils"),
				"desc" => wp_kses( __("Custom social item: name, profile url and icon url", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
				"decorate" => false,
				"container" => false,
				"params" => array(
					"name" => array(
						"title" => esc_html__("Social name", "trx_utils"),
						"desc" => wp_kses( __("Name (slug) of the social network (twitter, facebook, linkedin, etc.)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"value" => "",
						"type" => "text"
					),
					"url" => array(
						"title" => esc_html__("Your profile URL", "trx_utils"),
						"desc" => wp_kses( __("URL of your profile in specified social network", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"value" => "",
						"type" => "text"
					),
					"icon" => array(
						"title" => esc_html__("URL (source) for icon file", "trx_utils"),
						"desc" => wp_kses( __("Select or upload image or write URL from other site for the current social icon", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"readonly" => false,
						"value" => "",
						"type" => "media"
					)
				)
			)
		);
	}
}


/* Add shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_socials_reg_shortcodes_vc' ) ) {
	//add_action('themerex_action_shortcodes_list_vc', 'themerex_sc_socials_reg_shortcodes_vc');
	function themerex_sc_socials_reg_shortcodes_vc() {
		global $THEMEREX_GLOBALS;
	
		vc_map( array(
			"base" => "trx_socials",
			"name" => esc_html__("Social icons", "trx_utils"),
			"description" => wp_kses( __("Custom social icons", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_socials',
			"class" => "trx_sc_collection trx_sc_socials",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"as_parent" => array('only' => 'trx_social_item'),
			"params" => array_merge(array(
				array(
					"param_name" => "type",
					"heading" => esc_html__("Icon's type", "trx_utils"),
					"description" => wp_kses( __("Type of the icons - images or font icons", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"std" => themerex_get_theme_setting('socials_type'),
					"value" => array(
						esc_html__('Icons', 'trx_utils') => 'icons',
						esc_html__('Images', 'trx_utils') => 'images'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "size",
					"heading" => esc_html__("Icon's size", "trx_utils"),
					"description" => wp_kses( __("Size of the icons", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"std" => "small",
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['sizes']),
					"type" => "dropdown"
				),
				array(
					"param_name" => "shape",
					"heading" => esc_html__("Icon's shape", "trx_utils"),
					"description" => wp_kses( __("Shape of the icons", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"std" => "square",
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['shapes']),
					"type" => "dropdown"
				),
				array(
					"param_name" => "socials",
					"heading" => esc_html__("Manual socials list", "trx_utils"),
					"description" => wp_kses( __("Custom list of social networks. For example: twitter=http://twitter.com/my_profile|facebook=http://facebook.com/my_profile. If empty - use socials from Theme options.", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "custom",
					"heading" => esc_html__("Custom socials", "trx_utils"),
					"description" => wp_kses( __("Make custom icons from inner shortcodes (prepare it on tabs)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => array(esc_html__('Custom socials', 'trx_utils') => 'yes'),
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
			))
		) );
		
		
		vc_map( array(
			"base" => "trx_social_item",
			"name" => esc_html__("Custom social item", "trx_utils"),
			"description" => wp_kses( __("Custom social item: name, profile url and icon url", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"show_settings_on_create" => true,
			"content_element" => true,
			"is_container" => false,
			'icon' => 'icon_trx_social_item',
			"class" => "trx_sc_single trx_sc_social_item",
			"as_child" => array('only' => 'trx_socials'),
			"as_parent" => array('except' => 'trx_socials'),
			"params" => array(
				array(
					"param_name" => "name",
					"heading" => esc_html__("Social name", "trx_utils"),
					"description" => wp_kses( __("Name (slug) of the social network (twitter, facebook, linkedin, etc.)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "url",
					"heading" => esc_html__("Your profile URL", "trx_utils"),
					"description" => wp_kses( __("URL of your profile in specified social network", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("URL (source) for icon file", "trx_utils"),
					"description" => wp_kses( __("Select or upload image or write URL from other site for the current social icon", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				)
			)
		) );
		
		class WPBakeryShortCode_Trx_Socials extends Themerex_VC_ShortCodeCollection {}
		class WPBakeryShortCode_Trx_Social_Item extends Themerex_VC_ShortCodeSingle {}
	}
}
?>