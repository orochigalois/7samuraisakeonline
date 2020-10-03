<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('themerex_sc_video_theme_setup')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_sc_video_theme_setup' );
	function themerex_sc_video_theme_setup() {
		add_action('themerex_action_shortcodes_list', 		'themerex_sc_video_reg_shortcodes');
		if (function_exists('themerex_exists_visual_composer') && themerex_exists_visual_composer())
			add_action('themerex_action_shortcodes_list_vc','themerex_sc_video_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

//[trx_video id="unique_id" url="http://player.vimeo.com/video/20245032?title=0&amp;byline=0&amp;portrait=0" width="" height=""]

if (!function_exists('themerex_sc_video')) {	
	function themerex_sc_video($atts, $content = null) {
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			// Individual params
			"url" => '',
			"src" => '',
			"image" => '',
			"ratio" => '16:9',
			"autoplay" => 'off',
			"align" => '',
			"bg_image" => '',
			"bg_top" => '',
			"bg_bottom" => '',
			"bg_left" => '',
			"bg_right" => '',
			"frame" => "on",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"width" => '',
			"height" => '',
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
	
		if (empty($autoplay)) $autoplay = 'off';
		
		$ratio = empty($ratio) ? "16:9" : str_replace(array('/','\\','-'), ':', $ratio);
		$ratio_parts = explode(':', $ratio);
		if (empty($height) && empty($width)) {
			$width='100%';
			if (themerex_param_is_off(themerex_get_custom_option('substitute_video'))) $height="400";
		}
		$ed = themerex_substr($width, -1);
		if (empty($height) && !empty($width) && $ed!='%') {
			$height = round($width / $ratio_parts[0] * $ratio_parts[1]);
		}
		if (!empty($height) && empty($width)) {
			$width = round($height * $ratio_parts[0] / $ratio_parts[1]);
		}
		$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
		$css_dim = themerex_get_css_dimensions_from_values($width, $height);
		$css_bg = themerex_get_css_paddings_from_values($bg_top, $bg_right, $bg_bottom, $bg_left);
	
		if ($src=='' && $url=='' && isset($atts[0])) {
			$src = $atts[0];
		}
		$url = $src!='' ? $src : $url;
		if ($image!='' && themerex_param_is_off($image))
			$image = '';
		else {
			if (themerex_param_is_on($autoplay) && is_singular() && !themerex_get_global('blog_streampage'))
				$image = '';
			else {
				if ($image > 0) {
					$attach = wp_get_attachment_image_src( $image, 'full' );
					if (isset($attach[0]) && $attach[0]!='')
						$image = $attach[0];
				}
				if ($bg_image) {
					$thumb_sizes = themerex_get_thumb_sizes(array(
						'layout' => 'grid_3'
					));
					if (!is_single() || !empty($image)) $image = themerex_get_resized_image_url(empty($image) ? get_the_ID() : $image, $thumb_sizes['w'], $thumb_sizes['h'], null, false, false, false);
				} else
					if (!is_single() || !empty($image)) $image = themerex_get_resized_image_url(empty($image) ? get_the_ID() : $image, $ed!='%' ? $width : null, $height);
				if (empty($image) && (!is_singular() || themerex_get_global('blog_streampage')))	// || themerex_param_is_off($autoplay)))
					$image = themerex_get_video_cover_image($url);
			}
		}
		if ($bg_image > 0) {
			$attach = wp_get_attachment_image_src( $bg_image, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$bg_image = $attach[0];
		}
		if ($bg_image) {
			$css_bg .= $css . 'background-image: url('.esc_url($bg_image).');';
			$css = $css_dim;
		} else {
			$css .= $css_dim;
		}
	
		$url = themerex_get_video_player_url($src!='' ? $src : $url);
		
		$video = '<video' . ($id ? ' id="' . esc_attr($id) . '"' : '') 
			. ' class="sc_video'. (!empty($class) ? ' '.esc_attr($class) : '').'"'
			. ' src="' . esc_url($url) . '"'
			. ' width="' . esc_attr($width) . '" height="' . esc_attr($height) . '"' 
			. ' data-width="' . esc_attr($width) . '" data-height="' . esc_attr($height) . '"' 
			. ' data-ratio="'.esc_attr($ratio).'"'
			. ($image ? ' poster="'.esc_attr($image).'" data-image="'.esc_attr($image).'"' : '') 
			. (!themerex_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_get_animation_classes($animation)).'"' : '')
			. ($align && $align!='none' ? ' data-align="'.esc_attr($align).'"' : '')
			. ($bg_image ? ' data-bg-image="'.esc_attr($bg_image).'"' : '') 
			. ($css_bg!='' ? ' data-style="'.esc_attr($css_bg).'"' : '') 
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
			. (($image && themerex_param_is_on(themerex_get_custom_option('substitute_video'))) || (themerex_param_is_on($autoplay) && is_singular() && !themerex_get_global('blog_streampage')) ? ' autoplay="autoplay"' : '') 
			. ' controls="controls" loop="loop"'
			. '>'
			. '</video>';
		if (themerex_param_is_off(themerex_get_custom_option('substitute_video'))) {
			if (themerex_param_is_on($frame)) $video = themerex_get_video_frame($video, $image, $css, $css_bg);
		} else {
			if ((isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') && (isset($_POST['action']) && $_POST['action']=='vc_load_shortcode')) {
				$video = themerex_substitute_video($video, $width, $height, false);
			}
		}
		if (themerex_get_theme_option('use_mediaelement')=='yes')
            wp_enqueue_script('wp-mediaelement');
		return apply_filters('themerex_shortcode_output', $video, 'trx_video', $atts, $content);
	}
	add_shortcode("trx_video", "themerex_sc_video");
}



/* Add shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_video_reg_shortcodes' ) ) {
	//add_action('themerex_action_shortcodes_list', 'themerex_sc_video_reg_shortcodes');
	function themerex_sc_video_reg_shortcodes() {
		global $THEMEREX_GLOBALS;
	
		$THEMEREX_GLOBALS['shortcodes']["trx_video"] = array(
			"title" => esc_html__("Video", "trx_utils"),
			"desc" => wp_kses( __("Insert video player", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"url" => array(
					"title" => esc_html__("URL for video file", "trx_utils"),
					"desc" => wp_kses( __("Select video from media library or paste URL for video file from other site", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"readonly" => false,
					"value" => "",
					"type" => "media",
					"before" => array(
						'title' => esc_html__('Choose video', 'trx_utils'),
						'action' => 'media_upload',
						'type' => 'video',
						'multiple' => false,
						'linked_field' => '',
						'captions' => array( 	
							'choose' => esc_html__('Choose video file', 'trx_utils'),
							'update' => esc_html__('Select video file', 'trx_utils')
						)
					),
					"after" => array(
						'icon' => 'icon-cancel',
						'action' => 'media_reset'
					)
				),
				"ratio" => array(
					"title" => esc_html__("Ratio", "trx_utils"),
					"desc" => wp_kses( __("Ratio of the video", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "16:9",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => array(
						"16:9" => esc_html__("16:9", 'trx_utils'),
						"4:3" => esc_html__("4:3", 'trx_utils')
					)
				),
				"autoplay" => array(
					"title" => esc_html__("Autoplay video", "trx_utils"),
					"desc" => wp_kses( __("Autoplay video on page load", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "off",
					"type" => "switch",
					"options" => $THEMEREX_GLOBALS['sc_params']['on_off']
				),
				"align" => array(
					"title" => esc_html__("Align", "trx_utils"),
					"desc" => wp_kses( __("Select block alignment", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => $THEMEREX_GLOBALS['sc_params']['align']
				),
				"image" => array(
					"title" => esc_html__("Cover image", "trx_utils"),
					"desc" => wp_kses( __("Select or upload image or write URL from other site for video preview", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"bg_image" => array(
					"title" => esc_html__("Background image", "trx_utils"),
					"desc" => wp_kses( __("Select or upload image or write URL from other site for video background. Attention! If you use background image - specify paddings below from background margins to video block in percents!", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"divider" => true,
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"bg_top" => array(
					"title" => esc_html__("Top offset", "trx_utils"),
					"desc" => wp_kses( __("Top offset (padding) inside background image to video block (in percent). For example: 3%", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'bg_image' => array('not_empty')
					),
					"value" => "",
					"type" => "text"
				),
				"bg_bottom" => array(
					"title" => esc_html__("Bottom offset", "trx_utils"),
					"desc" => wp_kses( __("Bottom offset (padding) inside background image to video block (in percent). For example: 3%", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'bg_image' => array('not_empty')
					),
					"value" => "",
					"type" => "text"
				),
				"bg_left" => array(
					"title" => esc_html__("Left offset", "trx_utils"),
					"desc" => wp_kses( __("Left offset (padding) inside background image to video block (in percent). For example: 20%", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'bg_image' => array('not_empty')
					),
					"value" => "",
					"type" => "text"
				),
				"bg_right" => array(
					"title" => esc_html__("Right offset", "trx_utils"),
					"desc" => wp_kses( __("Right offset (padding) inside background image to video block (in percent). For example: 12%", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'bg_image' => array('not_empty')
					),
					"value" => "",
					"type" => "text"
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
if ( !function_exists( 'themerex_sc_video_reg_shortcodes_vc' ) ) {
	//add_action('themerex_action_shortcodes_list_vc', 'themerex_sc_video_reg_shortcodes_vc');
	function themerex_sc_video_reg_shortcodes_vc() {
		global $THEMEREX_GLOBALS;
	
		vc_map( array(
			"base" => "trx_video",
			"name" => esc_html__("Video", "trx_utils"),
			"description" => wp_kses( __("Insert video player", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_video',
			"class" => "trx_sc_single trx_sc_video",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "url",
					"heading" => esc_html__("URL for video file", "trx_utils"),
					"description" => wp_kses( __("Paste URL for video file", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "ratio",
					"heading" => esc_html__("Ratio", "trx_utils"),
					"description" => wp_kses( __("Select ratio for display video", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => array(
						esc_html__('16:9', 'trx_utils') => "16:9",
						esc_html__('4:3', 'trx_utils') => "4:3"
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "autoplay",
					"heading" => esc_html__("Autoplay video", "trx_utils"),
					"description" => wp_kses( __("Autoplay video on page load", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => array("Autoplay" => "on" ),
					"type" => "checkbox"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "trx_utils"),
					"description" => wp_kses( __("Select block alignment", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
					"type" => "dropdown"
				),
				array(
					"param_name" => "image",
					"heading" => esc_html__("Cover image", "trx_utils"),
					"description" => wp_kses( __("Select or upload image or write URL from other site for video preview", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "bg_image",
					"heading" => esc_html__("Background image", "trx_utils"),
					"description" => wp_kses( __("Select or upload image or write URL from other site for video background. Attention! If you use background image - specify paddings below from background margins to video block in percents!", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Background', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "bg_top",
					"heading" => esc_html__("Top offset", "trx_utils"),
					"description" => wp_kses( __("Top offset (padding) from background image to video block (in percent). For example: 3%", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Background', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "bg_bottom",
					"heading" => esc_html__("Bottom offset", "trx_utils"),
					"description" => wp_kses( __("Bottom offset (padding) from background image to video block (in percent). For example: 3%", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Background', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "bg_left",
					"heading" => esc_html__("Left offset", "trx_utils"),
					"description" => wp_kses( __("Left offset (padding) from background image to video block (in percent). For example: 20%", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Background', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "bg_right",
					"heading" => esc_html__("Right offset", "trx_utils"),
					"description" => wp_kses( __("Right offset (padding) from background image to video block (in percent). For example: 12%", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Background', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
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
		
		class WPBakeryShortCode_Trx_Video extends Themerex_VC_ShortCodeSingle {}
	}
}
?>