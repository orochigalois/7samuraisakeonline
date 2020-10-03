<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('themerex_sc_columns_theme_setup')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_sc_columns_theme_setup' );
	function themerex_sc_columns_theme_setup() {
		add_action('themerex_action_shortcodes_list', 		'themerex_sc_columns_reg_shortcodes');
		if (function_exists('themerex_exists_visual_composer') && themerex_exists_visual_composer())
			add_action('themerex_action_shortcodes_list_vc','themerex_sc_columns_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_columns id="unique_id" count="number"]
	[trx_column_item id="unique_id" span="2 - number_columns"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta, odio arcu vut natoque dolor ut, enim etiam vut augue. Ac augue amet quis integer ut dictumst? Elit, augue vut egestas! Tristique phasellus cursus egestas a nec a! Sociis et? Augue velit natoque, amet, augue. Vel eu diam, facilisis arcu.[/trx_column_item]
	[trx_column_item]A pulvinar ut, parturient enim porta ut sed, mus amet nunc, in. Magna eros hac montes, et velit. Odio aliquam phasellus enim platea amet. Turpis dictumst ultrices, rhoncus aenean pulvinar? Mus sed rhoncus et cras egestas, non etiam a? Montes? Ac aliquam in nec nisi amet eros! Facilisis! Scelerisque in.[/trx_column_item]
	[trx_column_item]Duis sociis, elit odio dapibus nec, dignissim purus est magna integer eu porta sagittis ut, pid rhoncus facilisis porttitor porta, et, urna parturient mid augue a, in sit arcu augue, sit lectus, natoque montes odio, enim. Nec purus, cras tincidunt rhoncus proin lacus porttitor rhoncus, vut enim habitasse cum magna.[/trx_column_item]
	[trx_column_item]Nec purus, cras tincidunt rhoncus proin lacus porttitor rhoncus, vut enim habitasse cum magna. Duis sociis, elit odio dapibus nec, dignissim purus est magna integer eu porta sagittis ut, pid rhoncus facilisis porttitor porta, et, urna parturient mid augue a, in sit arcu augue, sit lectus, natoque montes odio, enim.[/trx_column_item]
[/trx_columns]
*/

if (!function_exists('themerex_sc_columns')) {	
	function themerex_sc_columns($atts, $content=null){	
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			// Individual params
			"count" => "2",
			"fluid" => "no",
			"margins" => "yes",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
		$css .= themerex_get_css_dimensions_from_values($width, $height);
		$count = max(1, min(12, (int) $count));
		global $THEMEREX_GLOBALS;
		$THEMEREX_GLOBALS['sc_columns_counter'] = 1;
		$THEMEREX_GLOBALS['sc_columns_after_span2'] = false;
		$THEMEREX_GLOBALS['sc_columns_after_span3'] = false;
		$THEMEREX_GLOBALS['sc_columns_after_span4'] = false;
		$THEMEREX_GLOBALS['sc_columns_count'] = $count;
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="columns_wrap sc_columns'
					. ' columns_' . (themerex_param_is_on($fluid) ? 'fluid' : 'nofluid') 
					. (!empty($margins) && themerex_param_is_off($margins) ? ' no_margins' : '') 
					. ' sc_columns_count_' . esc_attr($count)
					. (!empty($class) ? ' '.esc_attr($class) : '') 
				. '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. (!themerex_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_get_animation_classes($animation)).'"' : '')
				. '>'
					. do_shortcode($content)
				. '</div>';
		return apply_filters('themerex_shortcode_output', $output, 'trx_columns', $atts, $content);
	}
	add_shortcode('trx_columns', 'themerex_sc_columns');
}


if (!function_exists('themerex_sc_column_item')) {	
	function themerex_sc_column_item($atts, $content=null) {
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts( array(
			// Individual params
			"span" => "1",
			"align" => "",
			"color" => "",
			"bg_color" => "",
			"bg_image" => "",
			"bg_tile" => "no",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => ""
		), $atts)));
		$css .= ($align !== '' ? 'text-align:' . esc_attr($align) . ';' : '') 
			. ($color !== '' ? 'color:' . esc_attr($color) . ';' : '');
		$span = max(1, min(11, (int) $span));
		if (!empty($bg_image)) {
			if ($bg_image > 0) {
				$attach = wp_get_attachment_image_src( $bg_image, 'full' );
				if (isset($attach[0]) && $attach[0]!='')
					$bg_image = $attach[0];
			}
		}
		global $THEMEREX_GLOBALS;
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') . ' class="column-'.($span > 1 ? esc_attr($span) : 1).'_'.esc_attr($THEMEREX_GLOBALS['sc_columns_count']).' sc_column_item sc_column_item_'.esc_attr($THEMEREX_GLOBALS['sc_columns_counter']) 
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. ($THEMEREX_GLOBALS['sc_columns_counter'] % 2 == 1 ? ' odd' : ' even') 
					. ($THEMEREX_GLOBALS['sc_columns_counter'] == 1 ? ' first' : '') 
					. ($span > 1 ? ' span_'.esc_attr($span) : '') 
					. ($THEMEREX_GLOBALS['sc_columns_after_span2'] ? ' after_span_2' : '') 
					. ($THEMEREX_GLOBALS['sc_columns_after_span3'] ? ' after_span_3' : '') 
					. ($THEMEREX_GLOBALS['sc_columns_after_span4'] ? ' after_span_4' : '') 
					. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
					. (!themerex_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_get_animation_classes($animation)).'"' : '')
					. '>'
					. ($bg_color!=='' || $bg_image !== '' ? '<div class="sc_column_item_inner" style="'
							. ($bg_color !== '' ? 'background-color:' . esc_attr($bg_color) . ';' : '')
							. ($bg_image !== '' ? 'background-image:url(' . esc_url($bg_image) . ');'.(themerex_param_is_on($bg_tile) ? 'background-repeat:repeat;' : 'background-repeat:no-repeat;background-size:cover;') : '')
							. '">' : '')
						. do_shortcode($content)
					. ($bg_color!=='' || $bg_image !== '' ? '</div>' : '')
					. '</div>';
		$THEMEREX_GLOBALS['sc_columns_counter'] += $span;
		$THEMEREX_GLOBALS['sc_columns_after_span2'] = $span==2;
		$THEMEREX_GLOBALS['sc_columns_after_span3'] = $span==3;
		$THEMEREX_GLOBALS['sc_columns_after_span4'] = $span==4;
		return apply_filters('themerex_shortcode_output', $output, 'trx_column_item', $atts, $content);
	}
	add_shortcode('trx_column_item', 'themerex_sc_column_item');
}



/* Add shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_columns_reg_shortcodes' ) ) {
	//add_action('themerex_action_shortcodes_list', 'themerex_sc_columns_reg_shortcodes');
	function themerex_sc_columns_reg_shortcodes() {
		global $THEMEREX_GLOBALS;
	
		$THEMEREX_GLOBALS['shortcodes']["trx_columns"] = array(
			"title" => esc_html__("Columns", "trx_utils"),
			"desc" => wp_kses( __("Insert up to 5 columns in your page (post)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"fluid" => array(
					"title" => esc_html__("Fluid columns", "trx_utils"),
					"desc" => wp_kses( __("To squeeze the columns when reducing the size of the window (fluid=yes) or to rebuild them (fluid=no)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "no",
					"type" => "switch",
					"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
				), 
				"margins" => array(
					"title" => esc_html__("Margins between columns", "trx_utils"),
					"desc" => wp_kses( __("Add margins between columns", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "yes",
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
				"name" => "trx_column_item",
				"title" => esc_html__("Column", "trx_utils"),
				"desc" => wp_kses( __("Column item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
				"container" => true,
				"params" => array(
					"span" => array(
						"title" => esc_html__("Merge columns", "trx_utils"),
						"desc" => wp_kses( __("Count merged columns from current", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"value" => "",
						"type" => "text"
					),
					"align" => array(
						"title" => esc_html__("Alignment", "trx_utils"),
						"desc" => wp_kses( __("Alignment text in the column", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"value" => "",
						"type" => "checklist",
						"dir" => "horizontal",
						"options" => $THEMEREX_GLOBALS['sc_params']['align']
					),
					"color" => array(
						"title" => esc_html__("Fore color", "trx_utils"),
						"desc" => wp_kses( __("Any color for objects in this column", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"value" => "",
						"type" => "color"
					),
					"bg_color" => array(
						"title" => esc_html__("Background color", "trx_utils"),
						"desc" => wp_kses( __("Any background color for this column", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"value" => "",
						"type" => "color"
					),
					"bg_image" => array(
						"title" => esc_html__("URL for background image file", "trx_utils"),
						"desc" => wp_kses( __("Select or upload image or write URL from other site for the background", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"readonly" => false,
						"value" => "",
						"type" => "media"
					),
					"bg_tile" => array(
						"title" => esc_html__("Tile background image", "trx_utils"),
						"desc" => wp_kses( __("Do you want tile background image or image cover whole column?", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"value" => "no",
						"dependency" => array(
							'bg_image' => array('not_empty')
						),
						"type" => "switch",
						"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
					),
					"_content_" => array(
						"title" => esc_html__("Column item content", "trx_utils"),
						"desc" => wp_kses( __("Current column item content", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"divider" => true,
						"rows" => 4,
						"value" => "",
						"type" => "textarea"
					),
					"id" => $THEMEREX_GLOBALS['sc_params']['id'],
					"class" => $THEMEREX_GLOBALS['sc_params']['class'],
					"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
					"css" => $THEMEREX_GLOBALS['sc_params']['css']
				)
			)
		);
	}
}


/* Add shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_columns_reg_shortcodes_vc' ) ) {
	//add_action('themerex_action_shortcodes_list_vc', 'themerex_sc_columns_reg_shortcodes_vc');
	function themerex_sc_columns_reg_shortcodes_vc() {
		global $THEMEREX_GLOBALS;
	
		vc_map( array(
			"base" => "trx_columns",
			"name" => esc_html__("Columns", "trx_utils"),
			"description" => wp_kses( __("Insert columns with margins", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_columns',
			"class" => "trx_sc_columns",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => false,
			"as_parent" => array('only' => 'trx_column_item'),
			"params" => array(
				array(
					"param_name" => "count",
					"heading" => esc_html__("Columns count", "trx_utils"),
					"description" => wp_kses( __("Number of the columns in the container.", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => "2",
					"type" => "textfield"
				),
				array(
					"param_name" => "fluid",
					"heading" => esc_html__("Fluid columns", "trx_utils"),
					"description" => wp_kses( __("To squeeze the columns when reducing the size of the window (fluid=yes) or to rebuild them (fluid=no)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => array(esc_html__('Fluid columns', 'trx_utils') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "margins",
					"heading" => esc_html__("Margins between columns", "trx_utils"),
					"description" => wp_kses( __("Add margins between columns", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"std" => "yes",
					"value" => array(esc_html__('Disable margins between columns', 'trx_utils') => 'no'),
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
				[trx_column_item][/trx_column_item]
				[trx_column_item][/trx_column_item]
			',
			'js_view' => 'VcTrxColumnsView'
		) );
		
		
		vc_map( array(
			"base" => "trx_column_item",
			"name" => esc_html__("Column", "trx_utils"),
			"description" => wp_kses( __("Column item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"show_settings_on_create" => true,
			"class" => "trx_sc_collection trx_sc_column_item",
			"content_element" => true,
			"is_container" => true,
			'icon' => 'icon_trx_column_item',
			"as_child" => array('only' => 'trx_columns'),
			"as_parent" => array('except' => 'trx_columns'),
			"params" => array(
				array(
					"param_name" => "span",
					"heading" => esc_html__("Merge columns", "trx_utils"),
					"description" => wp_kses( __("Count merged columns from current", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "trx_utils"),
					"description" => wp_kses( __("Alignment text in the column", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Fore color", "trx_utils"),
					"description" => wp_kses( __("Any color for objects in this column", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", "trx_utils"),
					"description" => wp_kses( __("Any background color for this column", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_image",
					"heading" => esc_html__("URL for background image file", "trx_utils"),
					"description" => wp_kses( __("Select or upload image or write URL from other site for the background", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "bg_tile",
					"heading" => esc_html__("Tile background image", "trx_utils"),
					"description" => wp_kses( __("Do you want tile background image or image cover whole column?", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					'dependency' => array(
						'element' => 'bg_image',
						'not_empty' => true
					),
					"std" => "no",
					"value" => array(esc_html__('Tile background image', 'trx_utils') => 'yes'),
					"type" => "checkbox"
				),
				$THEMEREX_GLOBALS['vc_params']['id'],
				$THEMEREX_GLOBALS['vc_params']['class'],
				$THEMEREX_GLOBALS['vc_params']['animation'],
				$THEMEREX_GLOBALS['vc_params']['css']
			),
			'js_view' => 'VcTrxColumnItemView'
		) );
		
		class WPBakeryShortCode_Trx_Columns extends Themerex_VC_ShortCodeColumns {}
		class WPBakeryShortCode_Trx_Column_Item extends Themerex_VC_ShortCodeCollection {}
	}
}
?>