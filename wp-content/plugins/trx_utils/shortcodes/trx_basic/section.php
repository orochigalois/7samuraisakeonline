<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('themerex_sc_section_theme_setup')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_sc_section_theme_setup' );
	function themerex_sc_section_theme_setup() {
		add_action('themerex_action_shortcodes_list', 		'themerex_sc_section_reg_shortcodes');
		if (function_exists('themerex_exists_visual_composer') && themerex_exists_visual_composer())
			add_action('themerex_action_shortcodes_list_vc','themerex_sc_section_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_section id="unique_id" class="class_name" style="css-styles" dedicated="yes|no"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_section]
*/

global $THEMEREX_GLOBALS;
$THEMEREX_GLOBALS['sc_section_dedicated'] = '';

if (!function_exists('themerex_sc_section')) {	
	function themerex_sc_section($atts, $content=null){	
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			// Individual params
			"dedicated" => "no",
			"align" => "none",
			"columns" => "none",
			"pan" => "no",
			"scroll" => "no",
			"scroll_dir" => "horizontal",
			"scroll_controls" => "no",
			"color" => "",
			"scheme" => "",
			"bg_color" => "",
			"bg_image" => "",
			"bg_overlay" => "",
			"bg_texture" => "",
			"bg_tile" => "no",
			"bg_padding" => "yes",
			"font_size" => "",
			"font_weight" => "",
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"description_link" => "",
			"link_caption" => esc_html__('Learn more', 'trx_utils'),
			"link" => '',
            "target" => "",
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
	
		if ($bg_image > 0) {
			$attach = wp_get_attachment_image_src( $bg_image, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$bg_image = $attach[0];
		}
	
		if ($bg_overlay > 0) {
			if ($bg_color=='') $bg_color = themerex_get_scheme_color('bg');
			$rgb = themerex_hex2rgb($bg_color);
		}
	
		$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
		$css .= ($color !== '' ? 'color:' . esc_attr($color) . ';' : '')
			.($bg_color !== '' && $bg_overlay==0 ? 'background-color:' . esc_attr($bg_color) . ';' : '')
			.($bg_image !== '' ? 'background-image:url(' . esc_url($bg_image) . ');'.(themerex_param_is_on($bg_tile) ? 'background-repeat:repeat;' : 'background-repeat:no-repeat;background-size:cover;') : '')
			.(!themerex_param_is_off($pan) ? 'position:relative;' : '')
			.($font_size != '' ? 'font-size:' . esc_attr(themerex_prepare_css_value($font_size)) . '; line-height: 1.3em;' : '')
			.($font_weight != '' && !themerex_param_is_inherit($font_weight) ? 'font-weight:' . esc_attr($font_weight) . ';' : '');
		$css_dim = themerex_get_css_dimensions_from_values($width, $height);
		if ($bg_image == '' && $bg_color == '' && $bg_overlay==0 && $bg_texture==0 && themerex_strlen($bg_texture)<2) $css .= $css_dim;
		
		$width  = themerex_prepare_css_value($width);
		$height = themerex_prepare_css_value($height);
	
		if ((!themerex_param_is_off($scroll) || !themerex_param_is_off($pan)) && empty($id)) $id = 'sc_section_'.str_replace('.', '', mt_rand());
	
		if (!themerex_param_is_off($scroll)) themerex_enqueue_slider();
	
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_section' 
					. ($class ? ' ' . esc_attr($class) : '') 
					. ($scheme && !themerex_param_is_off($scheme) && !themerex_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
					. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
					. (!empty($columns) && $columns!='none' ? ' column-'.esc_attr($columns) : '') 
					. (themerex_param_is_on($scroll) && !themerex_param_is_off($scroll_controls) ? ' sc_scroll_controls sc_scroll_controls_'.esc_attr($scroll_dir).' sc_scroll_controls_type_'.esc_attr($scroll_controls) : '')
					. '"'
				. (!themerex_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_get_animation_classes($animation)).'"' : '')
				. ($css!='' || $css_dim!='' ? ' style="'.esc_attr($css.$css_dim).'"' : '')
				.'>' 
				. '<div class="sc_section_inner">'
					. ($bg_image !== '' || $bg_color !== '' || $bg_overlay>0 || $bg_texture>0 || themerex_strlen($bg_texture)>2
						? '<div class="sc_section_overlay'.($bg_texture>0 ? ' texture_bg_'.esc_attr($bg_texture) : '') . '"'
							. ' style="' . ($bg_overlay>0 ? 'background-color:rgba('.(int)$rgb['r'].','.(int)$rgb['g'].','.(int)$rgb['b'].','.min(1, max(0, $bg_overlay)).');' : '')
								. (themerex_strlen($bg_texture)>2 ? 'background-image:url('.esc_url($bg_texture).');' : '')
								. '"'
								. ($bg_overlay > 0 ? ' data-overlay="'.esc_attr($bg_overlay).'" data-bg_color="'.esc_attr($bg_color).'"' : '')
								. '>'
								. '<div class="sc_section_content' . (themerex_param_is_on($bg_padding) ? ' padding_on' : ' padding_off') . '"'
									. ' style="'.esc_attr($css_dim).'"'
									. '>'
						: '')
					. (themerex_param_is_on($scroll) 
						? '<div id="'.esc_attr($id).'_scroll" class="sc_scroll sc_scroll_'.esc_attr($scroll_dir).' swiper-slider-container scroll-container"'
							. ' style="'.($height != '' ? 'height:'.esc_attr($height).';' : '') . ($width != '' ? 'width:'.esc_attr($width).';' : '').'"'
							. '>'
							. '<div class="sc_scroll_wrapper swiper-wrapper">' 
							. '<div class="sc_scroll_slide swiper-slide">' 
						: '')
					. (themerex_param_is_on($pan) 
						? '<div id="'.esc_attr($id).'_pan" class="sc_pan sc_pan_'.esc_attr($scroll_dir).'">' 
						: '')
							. (!empty($title) ? '<h2 class="sc_section_title sc_item_title">' . trim(themerex_strmacros($title)) . '</h2>' : '')
							. (!empty($subtitle) ? '<h6 class="sc_section_subtitle sc_item_subtitle">' . trim(themerex_strmacros($subtitle)) . '</h6>' : '')
							. (!empty($description) ? '<div class="sc_section_descr sc_item_descr">'.( !empty($description_link) ? '<a href='.$description_link.'>' : ''). trim(themerex_strmacros($description)).( !empty($description_link) ? '</a>' : '') . '</div>' : '')
							. do_shortcode($content)
							. (!empty($link) ? '<div class="sc_section_button sc_item_button">'.themerex_do_shortcode('[trx_button link="'.esc_url($link).'"  icon="icon-right"'.(!empty($target) ? ' target="'.esc_attr($target).'"' : '').' ]'.esc_html($link_caption).'[/trx_button]').'</div>' : '')
					. (themerex_param_is_on($pan) ? '</div>' : '')
					. (themerex_param_is_on($scroll) 
						? '</div></div><div id="'.esc_attr($id).'_scroll_bar" class="sc_scroll_bar sc_scroll_bar_'.esc_attr($scroll_dir).' '.esc_attr($id).'_scroll_bar"></div></div>'
							. (!themerex_param_is_off($scroll_controls) ? '<div class="sc_scroll_controls_wrap"><a class="sc_scroll_prev" href="#"></a><a class="sc_scroll_next" href="#"></a></div>' : '')
						: '')
					. ($bg_image !== '' || $bg_color !== '' || $bg_overlay > 0 || $bg_texture>0 || themerex_strlen($bg_texture)>2 ? '</div></div>' : '')
					. '</div>'
				. '</div>';
		if (themerex_param_is_on($dedicated)) {
			global $THEMEREX_GLOBALS;
			if ($THEMEREX_GLOBALS['sc_section_dedicated']=='') {
				$THEMEREX_GLOBALS['sc_section_dedicated'] = $output;
			}
			$output = '';
		}
		return apply_filters('themerex_shortcode_output', $output, 'trx_section', $atts, $content);
	}
	add_shortcode('trx_section', 'themerex_sc_section');
	add_shortcode('trx_block', 'themerex_sc_section');
}



/* Add shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_section_reg_shortcodes' ) ) {
	//add_action('themerex_action_shortcodes_list', 'themerex_sc_section_reg_shortcodes');
	function themerex_sc_section_reg_shortcodes() {
		global $THEMEREX_GLOBALS;
	
		$THEMEREX_GLOBALS['shortcodes']["trx_block"] = 
		$THEMEREX_GLOBALS['shortcodes']["trx_section"] = array(
			"title" => esc_html__("Block container", "trx_utils"),
			"desc" => wp_kses( __("Container for any block ([section] analog - to enable nesting)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => true,
			"container" => true,
			"params" => array(
				"title" => array(
					"title" => esc_html__("Title", "trx_utils"),
					"desc" => wp_kses( __("Title for the block", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"subtitle" => array(
					"title" => esc_html__("Subtitle", "trx_utils"),
					"desc" => wp_kses( __("Subtitle for the block", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"description" => array(
					"title" => esc_html__("Description", "trx_utils"),
					"desc" => wp_kses( __("Short description for the block", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "textarea"
				),
				"link" => array(
					"title" => esc_html__("Button URL", "trx_utils"),
					"desc" => wp_kses( __("Link URL for the button at the bottom of the block", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
                "target" => array(
                    "title" => esc_html__("Link target", "trx_utils"),
                    "desc" => wp_kses( __("Target for link on button click", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
                    "dependency" => array(
                        'link' => array('not_empty')
                    ),
                    "value" => "",
                    "type" => "text"
                ),
				"link_caption" => array(
					"title" => esc_html__("Button caption", "trx_utils"),
					"desc" => wp_kses( __("Caption for the button at the bottom of the block", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"dedicated" => array(
					"title" => esc_html__("Dedicated", "trx_utils"),
					"desc" => wp_kses( __("Use this block as dedicated content - show it before post title on single page", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "no",
					"type" => "switch",
					"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
				),
				"align" => array(
					"title" => esc_html__("Align", "trx_utils"),
					"desc" => wp_kses( __("Select block alignment", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => $THEMEREX_GLOBALS['sc_params']['align']
				),
				"columns" => array(
					"title" => esc_html__("Columns emulation", "trx_utils"),
					"desc" => wp_kses( __("Select width for columns emulation", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "none",
					"type" => "checklist",
					"options" => $THEMEREX_GLOBALS['sc_params']['columns']
				), 
				"pan" => array(
					"title" => esc_html__("Use pan effect", "trx_utils"),
					"desc" => wp_kses( __("Use pan effect to show section content", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"divider" => true,
					"value" => "no",
					"type" => "switch",
					"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
				),
				"scroll" => array(
					"title" => esc_html__("Use scroller", "trx_utils"),
					"desc" => wp_kses( __("Use scroller to show section content", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"divider" => true,
					"value" => "no",
					"type" => "switch",
					"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
				),
				"scroll_dir" => array(
					"title" => esc_html__("Scroll and Pan direction", "trx_utils"),
					"desc" => wp_kses( __("Scroll and Pan direction (if Use scroller = yes or Pan = yes)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'pan' => array('yes'),
						'scroll' => array('yes')
					),
					"value" => "horizontal",
					"type" => "switch",
					"size" => "big",
					"options" => $THEMEREX_GLOBALS['sc_params']['dir']
				),
				"scroll_controls" => array(
					"title" => esc_html__("Scroll controls", "trx_utils"),
					"desc" => wp_kses( __("Show scroll controls (if Use scroller = yes)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'scroll' => array('yes')
					),
					"value" => "no",
					"type" => "switch",
					"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
				),
				"scheme" => array(
					"title" => esc_html__("Color scheme", "trx_utils"),
					"desc" => wp_kses( __("Select color scheme for this block", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "checklist",
					"options" => $THEMEREX_GLOBALS['sc_params']['schemes']
				),
				"color" => array(
					"title" => esc_html__("Fore color", "trx_utils"),
					"desc" => wp_kses( __("Any color for objects in this section", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"divider" => true,
					"value" => "",
					"type" => "color"
				),
				"bg_color" => array(
					"title" => esc_html__("Background color", "trx_utils"),
					"desc" => wp_kses( __("Any background color for this section", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "color"
				),
				"bg_image" => array(
					"title" => esc_html__("Background image URL", "trx_utils"),
					"desc" => wp_kses( __("Select or upload image or write URL from other site for the background", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"bg_tile" => array(
					"title" => esc_html__("Tile background image", "trx_utils"),
					"desc" => wp_kses( __("Do you want tile background image or image cover whole block?", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "no",
					"dependency" => array(
						'bg_image' => array('not_empty')
					),
					"type" => "switch",
					"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
				),
				"bg_overlay" => array(
					"title" => esc_html__("Overlay", "trx_utils"),
					"desc" => wp_kses( __("Overlay color opacity (from 0.0 to 1.0)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"min" => "0",
					"max" => "1",
					"step" => "0.1",
					"value" => "0",
					"type" => "spinner"
				),
				"bg_texture" => array(
					"title" => esc_html__("Texture", "trx_utils"),
					"desc" => wp_kses( __("Predefined texture style from 1 to 11. 0 - without texture.", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"min" => "0",
					"max" => "11",
					"step" => "1",
					"value" => "0",
					"type" => "spinner"
				),
				"bg_padding" => array(
					"title" => esc_html__("Paddings around content", "trx_utils"),
					"desc" => wp_kses( __("Add paddings around content in this section (only if bg_color or bg_image enabled).", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "yes",
					"dependency" => array(
						'compare' => 'or',
						'bg_color' => array('not_empty'),
						'bg_texture' => array('not_empty'),
						'bg_image' => array('not_empty')
					),
					"type" => "switch",
					"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
				),
				"font_size" => array(
					"title" => esc_html__("Font size", "trx_utils"),
					"desc" => wp_kses( __("Font size of the text (default - in pixels, allows any CSS units of measure)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"font_weight" => array(
					"title" => esc_html__("Font weight", "trx_utils"),
					"desc" => wp_kses( __("Font weight of the text", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
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
				"_content_" => array(
					"title" => esc_html__("Container content", "trx_utils"),
					"desc" => wp_kses( __("Content for section container", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
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
		$THEMEREX_GLOBALS['shortcodes']["trx_section"]["title"] = esc_html__("Section container", "trx_utils");
		$THEMEREX_GLOBALS['shortcodes']["trx_section"]["desc"] = esc_html__("Container for any section ([block] analog - to enable nesting)", "trx_utils");
	}
}


/* Add shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_section_reg_shortcodes_vc' ) ) {
	//add_action('themerex_action_shortcodes_list_vc', 'themerex_sc_section_reg_shortcodes_vc');
	function themerex_sc_section_reg_shortcodes_vc() {
		global $THEMEREX_GLOBALS;
	
		$sc = array(
			"base" => "trx_block",
			"name" => esc_html__("Block container", "trx_utils"),
			"description" => wp_kses( __("Container for any block ([section] analog - to enable nesting)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_block',
			"class" => "trx_sc_collection trx_sc_block",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "dedicated",
					"heading" => esc_html__("Dedicated", "trx_utils"),
					"description" => wp_kses( __("Use this block as dedicated content - show it before post title on single page", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => array(esc_html__('Use as dedicated content', 'trx_utils') => 'yes'),
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
					"param_name" => "columns",
					"heading" => esc_html__("Columns emulation", "trx_utils"),
					"description" => wp_kses( __("Select width for columns emulation", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['columns']),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", "trx_utils"),
					"description" => wp_kses( __("Title for the block", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"group" => esc_html__('Captions', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "subtitle",
					"heading" => esc_html__("Subtitle", "trx_utils"),
					"description" => wp_kses( __("Subtitle for the block", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Captions', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "description",
					"heading" => esc_html__("Description", "trx_utils"),
					"description" => wp_kses( __("Description for the block", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Captions', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "textarea"
				),
				array(
					"param_name" => "description_link",
					"heading" => esc_html__("Description URL", "trx_utils"),
					"description" => wp_kses( __("Link URL for the description", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Captions', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Button URL", "trx_utils"),
					"description" => wp_kses( __("Link URL for the button at the bottom of the block", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Captions', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
                array(
                    "param_name" => "target",
                    "heading" => esc_html__("Link target", "trx_utils"),
                    "description" => wp_kses( __("Target for the link on button click", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
                    "class" => "",
                    "group" => esc_html__('Captions', 'trx_utils'),
                    "value" => "",
                    "type" => "textfield"
                ),
				array(
					"param_name" => "link_caption",
					"heading" => esc_html__("Button caption", "trx_utils"),
					"description" => wp_kses( __("Caption for the button at the bottom of the block", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Captions', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "pan",
					"heading" => esc_html__("Use pan effect", "trx_utils"),
					"description" => wp_kses( __("Use pan effect to show section content", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Scroll', 'trx_utils'),
					"class" => "",
					"value" => array(esc_html__('Content scroller', 'trx_utils') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "scroll",
					"heading" => esc_html__("Use scroller", "trx_utils"),
					"description" => wp_kses( __("Use scroller to show section content", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Scroll', 'trx_utils'),
					"admin_label" => true,
					"class" => "",
					"value" => array(esc_html__('Content scroller', 'trx_utils') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "scroll_dir",
					"heading" => esc_html__("Scroll direction", "trx_utils"),
					"description" => wp_kses( __("Scroll direction (if Use scroller = yes)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"group" => esc_html__('Scroll', 'trx_utils'),
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['dir']),
					'dependency' => array(
						'element' => 'scroll',
						'not_empty' => true
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "scroll_controls",
					"heading" => esc_html__("Scroll controls", "trx_utils"),
					"description" => wp_kses( __("Show scroll controls (if Use scroller = yes)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"group" => esc_html__('Scroll', 'trx_utils'),
					'dependency' => array(
						'element' => 'scroll',
						'not_empty' => true
					),
					"value" => array(esc_html__('Show scroll controls', 'trx_utils') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "scheme",
					"heading" => esc_html__("Color scheme", "trx_utils"),
					"description" => wp_kses( __("Select color scheme for this block", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Colors and Images', 'trx_utils'),
					"class" => "",
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['schemes']),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Fore color", "trx_utils"),
					"description" => wp_kses( __("Any color for objects in this section", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Colors and Images', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", "trx_utils"),
					"description" => wp_kses( __("Any background color for this section", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Colors and Images', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_image",
					"heading" => esc_html__("Background image URL", "trx_utils"),
					"description" => wp_kses( __("Select background image from library for this section", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Colors and Images', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "bg_tile",
					"heading" => esc_html__("Tile background image", "trx_utils"),
					"description" => wp_kses( __("Do you want tile background image or image cover whole block?", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Colors and Images', 'trx_utils'),
					"class" => "",
					'dependency' => array(
						'element' => 'bg_image',
						'not_empty' => true
					),
					"std" => "no",
					"value" => array(esc_html__('Tile background image', 'trx_utils') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "bg_overlay",
					"heading" => esc_html__("Overlay", "trx_utils"),
					"description" => wp_kses( __("Overlay color opacity (from 0.0 to 1.0)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Colors and Images', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "bg_texture",
					"heading" => esc_html__("Texture", "trx_utils"),
					"description" => wp_kses( __("Texture style from 1 to 11. Empty or 0 - without texture.", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Colors and Images', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "bg_padding",
					"heading" => esc_html__("Paddings around content", "trx_utils"),
					"description" => wp_kses( __("Add paddings around content in this section (only if bg_color or bg_image enabled).", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Colors and Images', 'trx_utils'),
					"class" => "",
					'dependency' => array(
						'element' => array('bg_color','bg_texture','bg_image'),
						'not_empty' => true
					),
					"std" => "yes",
					"value" => array(esc_html__('Disable padding around content in this block', 'trx_utils') => 'no'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "font_size",
					"heading" => esc_html__("Font size", "trx_utils"),
					"description" => wp_kses( __("Font size of the text (default - in pixels, allows any CSS units of measure)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "font_weight",
					"heading" => esc_html__("Font weight", "trx_utils"),
					"description" => wp_kses( __("Font weight of the text", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
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
				/*
				array(
					"param_name" => "content",
					"heading" => esc_html__("Container content", "trx_utils"),
					"description" => wp_kses( __("Content for section container", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
				*/
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
		);
		
		// Block
		vc_map($sc);
		
		// Section
		$sc["base"] = 'trx_section';
		$sc["name"] = esc_html__("Section container", "trx_utils");
		$sc["description"] = wp_kses( __("Container for any section ([block] analog - to enable nesting)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] );
		$sc["class"] = "trx_sc_collection trx_sc_section";
		$sc["icon"] = 'icon_trx_section';
		vc_map($sc);
		
		class WPBakeryShortCode_Trx_Block extends Themerex_VC_ShortCodeCollection {}
		class WPBakeryShortCode_Trx_Section extends Themerex_VC_ShortCodeCollection {}
	}
}
?>