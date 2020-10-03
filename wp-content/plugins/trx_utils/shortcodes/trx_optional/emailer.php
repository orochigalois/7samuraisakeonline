<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('themerex_sc_emailer_theme_setup')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_sc_emailer_theme_setup' );
	function themerex_sc_emailer_theme_setup() {
		add_action('themerex_action_shortcodes_list', 		'themerex_sc_emailer_reg_shortcodes');
		if (function_exists('themerex_exists_visual_composer') && themerex_exists_visual_composer())
			add_action('themerex_action_shortcodes_list_vc','themerex_sc_emailer_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

//[trx_emailer group=""]

if (!function_exists('themerex_sc_emailer')) {	
	function themerex_sc_emailer($atts, $content = null) {
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			// Individual params
			"group" => "",
			"open" => "yes",
			"align" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => "",
			"width" => "",
			"height" => ""
		), $atts)));
		$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
		$css .= themerex_get_css_dimensions_from_values($width, $height);
		// Load core messages
		themerex_enqueue_messages();
        static $cnt = 1;
        $cnt++;
        $privacy = themerex_get_privacy_text();
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
					. ' class="sc_emailer' . ($align && $align!='none' ? ' align' . esc_attr($align) : '') . (themerex_param_is_on($open) ? ' sc_emailer_opened' : '') . (!empty($class) ? ' '.esc_attr($class) : '') . '"' 
					. ($css ? ' style="'.esc_attr($css).'"' : '') 
					. (!themerex_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_get_animation_classes($animation)).'"' : '')
					. '>'
				. '<form class="sc_emailer_form">'
				. '<input type="text" class="sc_emailer_input" name="email" value="" placeholder="'.esc_attr__('Please, enter you email address.', 'trx_utils').'">'
				. '<a href="#" class="sc_emailer_button icon-mail" title="'.esc_attr__('Submit', 'trx_utils').'" data-group="'.esc_attr($group ? $group : esc_html__('E-mailer subscription', 'trx_utils')).'"></a></div>';



    if (!empty($privacy)) {
        $output .= '<div class="sc_form_field sc_form_field_checkbox">'
            .'<input type="checkbox" id="i_agree_privacy_policy_sc_form_' . esc_attr($cnt) . '" name="i_agree_privacy_policy" class="sc_form_privacy_checkbox e-mailer" value="1">'
            .'<label for="i_agree_privacy_policy_sc_form_' . esc_attr($cnt) . '">' . ($privacy) . '</label>'
            .'</div>';
        }


    $output .= '</form></div>';

		return apply_filters('themerex_shortcode_output', $output, 'trx_emailer', $atts, $content);
	}
	add_shortcode("trx_emailer", "themerex_sc_emailer");
}



/* Add shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_emailer_reg_shortcodes' ) ) {
	//add_action('themerex_action_shortcodes_list', 'themerex_sc_emailer_reg_shortcodes');
	function themerex_sc_emailer_reg_shortcodes() {
		global $THEMEREX_GLOBALS;
	
		$THEMEREX_GLOBALS['shortcodes']["trx_emailer"] = array(
			"title" => esc_html__("E-mail collector", "trx_utils"),
			"desc" => wp_kses( __("Collect the e-mail address into specified group", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"group" => array(
					"title" => esc_html__("Group", "trx_utils"),
					"desc" => wp_kses( __("The name of group to collect e-mail address", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"open" => array(
					"title" => esc_html__("Open", "trx_utils"),
					"desc" => wp_kses( __("Initially open the input field on show object", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"divider" => true,
					"value" => "yes",
					"type" => "switch",
					"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
				),
				"align" => array(
					"title" => esc_html__("Alignment", "trx_utils"),
					"desc" => wp_kses( __("Align object to left, center or right", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"divider" => true,
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => $THEMEREX_GLOBALS['sc_params']['align']
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
			)
		);
	}
}


/* Add shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_emailer_reg_shortcodes_vc' ) ) {
	//add_action('themerex_action_shortcodes_list_vc', 'themerex_sc_emailer_reg_shortcodes_vc');
	function themerex_sc_emailer_reg_shortcodes_vc() {
		global $THEMEREX_GLOBALS;
	
		vc_map( array(
			"base" => "trx_emailer",
			"name" => esc_html__("E-mail collector", "trx_utils"),
			"description" => wp_kses( __("Collect e-mails into specified group", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_emailer',
			"class" => "trx_sc_single trx_sc_emailer",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "group",
					"heading" => esc_html__("Group", "trx_utils"),
					"description" => wp_kses( __("The name of group to collect e-mail address", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "open",
					"heading" => esc_html__("Opened", "trx_utils"),
					"description" => wp_kses( __("Initially open the input field on show object", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => array(esc_html__('Initially opened', 'trx_utils') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "trx_utils"),
					"description" => wp_kses( __("Align field to left, center or right", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
					"type" => "dropdown"
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
			)
		) );
		
		class WPBakeryShortCode_Trx_Emailer extends Themerex_VC_ShortCodeSingle {}
	}
}
?>