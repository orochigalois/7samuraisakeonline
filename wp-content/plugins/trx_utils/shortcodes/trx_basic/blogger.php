<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('themerex_sc_blogger_theme_setup')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_sc_blogger_theme_setup' );
	function themerex_sc_blogger_theme_setup() {
		add_action('themerex_action_shortcodes_list', 		'themerex_sc_blogger_reg_shortcodes');
		if (function_exists('themerex_exists_visual_composer') && themerex_exists_visual_composer())
			add_action('themerex_action_shortcodes_list_vc','themerex_sc_blogger_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_blogger id="unique_id" ids="comma_separated_list" cat="id|slug" orderby="date|views|comments" order="asc|desc" count="5" descr="0" dir="horizontal|vertical" style="regular|date|image_large|image_medium|image_small|accordion|list" border="0"]
*/
global $THEMEREX_GLOBALS;
$THEMEREX_GLOBALS['sc_blogger_busy'] = false;

if (!function_exists('themerex_sc_blogger')) {	
	function themerex_sc_blogger($atts, $content=null){	
		if (themerex_in_shortcode_blogger(true)) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "accordion",
			"filters" => "no",
			"post_type" => "post",
			"ids" => "",
			"cat" => "",
			"count" => "3",
			"columns" => "",
			"offset" => "",
			"orderby" => "date",
			"order" => "desc",
			"only" => "no",
			"descr" => "",
			"readmore" => "",
			"loadmore" => "no",
			"location" => "default",
			"dir" => "horizontal",
			"hover" => themerex_get_theme_option('hover_style'),
			"hover_dir" => themerex_get_theme_option('hover_dir'),
			"scroll" => "no",
			"controls" => "no",
			"rating" => "no",
			"info" => "yes",
			"links" => "yes",
			"date_format" => "",
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"description_link" => "",
			"link" => '',
			"link_caption" => esc_html__('Learn more', 'trx_utils'),
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
		$width  = themerex_prepare_css_value($width);
		$height = themerex_prepare_css_value($height);
	
		global $post, $THEMEREX_GLOBALS;
	
		$THEMEREX_GLOBALS['sc_blogger_busy'] = true;
		$THEMEREX_GLOBALS['sc_blogger_counter'] = 0;
	
		if (empty($id)) $id = "sc_blogger_".str_replace('.', '', mt_rand());
		
		if ($style=='date' && empty($date_format)) $date_format = 'd.m+Y';
	
		if (!empty($ids)) {
			$posts = explode(',', str_replace(' ', '', $ids));
			$count = count($posts);
		}
		
		if ($descr == '') $descr = themerex_get_custom_option('post_excerpt_maxlength'.($columns > 1 ? '_masonry' : ''));
	
		if (!themerex_param_is_off($scroll)) {
			themerex_enqueue_slider();
			if (empty($id)) $id = 'sc_blogger_'.str_replace('.', '', mt_rand());
		}
		
		$class = apply_filters('themerex_filter_blog_class',
					'sc_blogger'
					. ' layout_'.esc_attr($style)
					. ' template_'.esc_attr(themerex_get_template_name($style))
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. ' ' . esc_attr(themerex_get_template_property($style, 'container_classes'))
					. ' sc_blogger_' . ($dir=='vertical' ? 'vertical' : 'horizontal')
					. (themerex_param_is_on($scroll) && themerex_param_is_on($controls) ? ' sc_scroll_controls sc_scroll_controls_type_top sc_scroll_controls_'.esc_attr($dir) : '')
					. ($descr == 0 ? ' no_description' : ''),
					array('style'=>$style, 'dir'=>$dir, 'descr'=>$descr)
		);
	
		$container = apply_filters('themerex_filter_blog_container', themerex_get_template_property($style, 'container'), array('style'=>$style, 'dir'=>$dir));
		$container_start = $container_end = '';
		if (!empty($container)) {
			$container = explode('%s', $container);
			$container_start = !empty($container[0]) ? $container[0] : '';
			$container_end = !empty($container[1]) ? $container[1] : '';
		}
		$container2 = apply_filters('themerex_filter_blog_container2', themerex_get_template_property($style, 'container2'), array('style'=>$style, 'dir'=>$dir));
		$container2_start = $container2_end = '';
		if (!empty($container2)) {
			$container2 = explode('%s', $container2);
			$container2_start = !empty($container2[0]) ? $container2[0] : '';
			//$container2_start = str_replace(array('%css'), array('style="height:'.esc_attr($height).'"'), $container2_start);
			$container2_end = !empty($container2[1]) ? $container2[1] : '';
		}
	
		$output = '<div'
				. ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="'.($style=='list' ? 'sc_list sc_list_style_iconed ' : '') . esc_attr($class).'"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. (!themerex_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_get_animation_classes($animation)).'"' : '')
			. '>'
			. ($container_start)
			. (!empty($title) ? '<h2 class="sc_blogger_title sc_item_title">' . trim(themerex_strmacros($title)) . '</h2>' : '')
			. (!empty($subtitle) ? '<h6 class="sc_blogger_subtitle sc_item_subtitle">' . trim(themerex_strmacros($subtitle)) . '</h6>' : '')
			. (!empty($description) ? '<div class="sc_blogger_descr sc_item_descr">'.( !empty($description_link) ? '<a href='.$description_link.'>' : ''). trim(themerex_strmacros($description)).( !empty($description_link) ? '</a>' : '') . '</div>' : '')
			. ($container2_start)
			. ($style=='list' ? '<ul class="sc_list sc_list_style_iconed">' : '')
			. ($dir=='horizontal' && $columns > 1 && themerex_get_template_property($style, 'need_columns') ? '<div class="columns_wrap">' : '')
			. (themerex_param_is_on($scroll) 
				? '<div id="'.esc_attr($id).'_scroll" class="sc_scroll sc_scroll_'.esc_attr($dir).' sc_slider_noresize swiper-slider-container scroll-container"'
					. ' style="'.($dir=='vertical' ? 'height:'.($height != '' ? $height : "230px").';' : 'width:'.($width != '' ? $width.';' : "100%;")).'"'
					. '>'
					. '<div class="sc_scroll_wrapper swiper-wrapper">' 
						. '<div class="sc_scroll_slide swiper-slide">' 
				: '')
			;
	
		if (themerex_get_template_property($style, 'need_isotope')) {
			if (!themerex_param_is_off($filters))
				$output .= '<div class="isotope_filters"></div>';
			if ($columns<1) $columns = themerex_substr($style, -1);
			$output .= '<div class="isotope_wrap" data-columns="'.max(1, min(12, $columns)).'">';
		}
	
		$args = array(
			'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish',
			'posts_per_page' => $count,
			'ignore_sticky_posts' => true,
			'order' => $order=='asc' ? 'asc' : 'desc',
			'orderby' => 'date',
		);
	
		if ($offset > 0 && empty($ids)) {
			$args['offset'] = $offset;
		}
	
		$args = themerex_query_add_sort_order($args, $orderby, $order);
		if (!themerex_param_is_off($only)) $args = themerex_query_add_filters($args, $only);
		$args = themerex_query_add_posts_and_cats($args, $ids, $post_type, $cat);
	
		$query = new WP_Query( $args );
	
		$flt_ids = array();
	
		while ( $query->have_posts() ) { $query->the_post();
	
			$THEMEREX_GLOBALS['sc_blogger_counter']++;
	
			$args = array(
				'layout' => $style,
				'show' => false,
				'number' => $THEMEREX_GLOBALS['sc_blogger_counter'],
				'add_view_more' => false,
				'posts_on_page' => ($count > 0 ? $count : $query->found_posts),
				// Additional options to layout generator
				"location" => $location,
				"descr" => $descr,
				"readmore" => $readmore,
				"loadmore" => $loadmore,
				"reviews" => themerex_param_is_on($rating),
				"dir" => $dir,
				"scroll" => themerex_param_is_on($scroll),
				"info" => themerex_param_is_on($info),
				"links" => themerex_param_is_on($links),
				"orderby" => $orderby,
				"columns_count" => $columns,
				"date_format" => $date_format,
				// Get post data
				'strip_teaser' => false,
				'content' => themerex_get_template_property($style, 'need_content'),
				'terms_list' => !themerex_param_is_off($filters) || themerex_get_template_property($style, 'need_terms'),
				'filters' => themerex_param_is_off($filters) ? '' : $filters,
				'hover' => $hover,
				'hover_dir' => $hover_dir
			);
			$post_data = themerex_get_post_data($args);
			$output .= themerex_show_post_layout($args, $post_data);
		
			if (!themerex_param_is_off($filters)) {
				if ($filters == 'tags') {			// Use tags as filter items
					if (!empty($post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms) && is_array($post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms)) {
						foreach ($post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms as $tag) {
							$flt_ids[$tag->term_id] = $tag->name;
						}
					}
				}
			}
	
		}
	
		wp_reset_postdata();
	
		// Close isotope wrapper
		if (themerex_get_template_property($style, 'need_isotope'))
			$output .= '</div>';
	
		// Isotope filters list
		if (!themerex_param_is_off($filters)) {
			$filters_list = '';
			if ($filters == 'categories') {			// Use categories as filter items
				$taxonomy = themerex_get_taxonomy_categories_by_post_type($post_type);
				$portfolio_parent = $cat ? max(0, themerex_get_parent_taxonomy_by_property($cat, 'show_filters', 'yes', true, $taxonomy)) : 0;
				$args2 = array(
					'type'			=> $post_type,
					'child_of'		=> $portfolio_parent,
					'orderby'		=> 'name',
					'order'			=> 'ASC',
					'hide_empty'	=> 1,
					'hierarchical'	=> 0,
					'exclude'		=> '',
					'include'		=> '',
					'number'		=> '',
					'taxonomy'		=> $taxonomy,
					'pad_counts'	=> false
				);
				$portfolio_list = get_categories($args2);
				if (is_array($portfolio_list) && count($portfolio_list) > 0) {
					$filters_list .= '<a href="#" data-filter="*" class="theme_button active">'.esc_html__('All', 'trx_utils').'</a>';
					foreach ($portfolio_list as $cat) {
						$filters_list .= '<a href="#" data-filter=".flt_'.esc_attr($cat->term_id).'" class="theme_button">'.($cat->name).'</a>';
					}
				}
			} else {								// Use tags as filter items
				if (is_array($flt_ids) && count($flt_ids) > 0) {
					$filters_list .= '<a href="#" data-filter="*" class="theme_button active">'.esc_html__('All', 'trx_utils').'</a>';
					foreach ($flt_ids as $flt_id=>$flt_name) {
						$filters_list .= '<a href="#" data-filter=".flt_'.esc_attr($flt_id).'" class="theme_button">'.($flt_name).'</a>';
					}
				}
			}
			if ($filters_list) {
				$output .= '<script type="text/javascript">'
					. 'jQuery(document).ready(function () {'
						. 'jQuery("#'.esc_attr($id).' .isotope_filters").append("'.addslashes($filters_list).'");'
					. '});'
					. '</script>';
			}
		}
		$output	.= (themerex_param_is_on($scroll) 
				? '</div></div><div id="'.esc_attr($id).'_scroll_bar" class="sc_scroll_bar sc_scroll_bar_'.esc_attr($dir).' '.esc_attr($id).'_scroll_bar"></div></div>'
					. (!themerex_param_is_off($controls) ? '<div class="sc_scroll_controls_wrap"><a class="sc_scroll_prev" href="#"></a><a class="sc_scroll_next" href="#"></a></div>' : '')
				: '')
			. ($dir=='horizontal' && $columns > 1 && themerex_get_template_property($style, 'need_columns') ? '</div>' :  '')
			. ($style == 'list' ? '</ul>' : '')
			. ($container2_end)
			. (!empty($link) 
				? '<div class="sc_blogger_button sc_item_button">'.do_shortcode('[trx_button link="'.esc_url($link).'" icon="icon-right"]'.esc_html($link_caption).'[/trx_button]').'</div>' 				: '')
			. ($container_end)
			. '</div>';
	
		// Add template specific scripts and styles
		do_action('themerex_action_blog_scripts', $style);
		
		$THEMEREX_GLOBALS['sc_blogger_busy'] = false;
	
		return apply_filters('themerex_shortcode_output', $output, 'trx_blogger', $atts, $content);
	}
	add_shortcode('trx_blogger', 'themerex_sc_blogger');
}



/* Add shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_blogger_reg_shortcodes' ) ) {
	//add_action('themerex_action_shortcodes_list', 'themerex_sc_blogger_reg_shortcodes');
	function themerex_sc_blogger_reg_shortcodes() {
		global $THEMEREX_GLOBALS;
	
		$THEMEREX_GLOBALS['shortcodes']["trx_blogger"] = array(
			"title" => esc_html__("Blogger", "trx_utils"),
			"desc" => wp_kses( __("Insert posts (pages) in many styles from desired categories or directly from ids", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
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
				"style" => array(
					"title" => esc_html__("Posts output style", "trx_utils"),
					"desc" => wp_kses( __("Select desired style for posts output", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "accordion",
					"type" => "select",
					"options" => $THEMEREX_GLOBALS['sc_params']['blogger_styles']
				),
				"filters" => array(
					"title" => esc_html__("Show filters", "trx_utils"),
					"desc" => wp_kses( __("Use post's tags or categories as filter buttons", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "no",
					"dir" => "horizontal",
					"type" => "checklist",
					"options" => $THEMEREX_GLOBALS['sc_params']['filters']
				),
				"hover" => array(
					"title" => esc_html__("Hover effect", "trx_utils"),
					"desc" => wp_kses( __("Select hover effect (only if style=Portfolio)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'style' => array('portfolio','grid','square','short','colored','courses')
					),
					"value" => "",
					"type" => "select",
					"options" => $THEMEREX_GLOBALS['sc_params']['hovers']
				),
				"hover_dir" => array(
					"title" => esc_html__("Hover direction", "trx_utils"),
					"desc" => wp_kses( __("Select hover direction (only if style=Portfolio and hover=Circle|Square)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'style' => array('portfolio','grid','square','short','colored','courses'),
						'hover' => array('square','circle')
					),
					"value" => "left_to_right",
					"type" => "select",
					"options" => $THEMEREX_GLOBALS['sc_params']['hovers_dir']
				),
				"dir" => array(
					"title" => esc_html__("Posts direction", "trx_utils"),
					"desc" => wp_kses( __("Display posts in horizontal or vertical direction", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "horizontal",
					"type" => "switch",
					"size" => "big",
					"options" => $THEMEREX_GLOBALS['sc_params']['dir']
				),
				"post_type" => array(
					"title" => esc_html__("Post type", "trx_utils"),
					"desc" => wp_kses( __("Select post type to show", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "post",
					"type" => "select",
					"options" => $THEMEREX_GLOBALS['sc_params']['posts_types']
				),
				"ids" => array(
					"title" => esc_html__("Post IDs list", "trx_utils"),
					"desc" => wp_kses( __("Comma separated list of posts ID. If set - parameters above are ignored!", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"cat" => array(
					"title" => esc_html__("Categories list", "trx_utils"),
					"desc" => wp_kses( __("Select the desired categories. If not selected - show posts from any category or from IDs list", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'ids' => array('is_empty'),
						'post_type' => array('refresh')
					),
					"divider" => true,
					"value" => "",
					"type" => "select",
					"style" => "list",
					"multiple" => true,
					"options" => themerex_array_merge(array(0 => esc_html__('- Select category -', 'trx_utils')), $THEMEREX_GLOBALS['sc_params']['categories'])
				),
				"count" => array(
					"title" => esc_html__("Total posts to show", "trx_utils"),
					"desc" => wp_kses( __("How many posts will be displayed? If used IDs - this parameter ignored.", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'ids' => array('is_empty')
					),
					"value" => 3,
					"min" => 1,
					"max" => 100,
					"type" => "spinner"
				),
				"columns" => array(
					"title" => esc_html__("Columns number", "trx_utils"),
					"desc" => wp_kses( __("How many columns used to show posts? If empty or 0 - equal to posts number", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'dir' => array('horizontal')
					),
					"value" => 3,
					"min" => 1,
					"max" => 100,
					"type" => "spinner"
				),
				"offset" => array(
					"title" => esc_html__("Offset before select posts", "trx_utils"),
					"desc" => wp_kses( __("Skip posts before select next part.", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'ids' => array('is_empty')
					),
					"value" => 0,
					"min" => 0,
					"max" => 100,
					"type" => "spinner"
				),
				"orderby" => array(
					"title" => esc_html__("Post order by", "trx_utils"),
					"desc" => wp_kses( __("Select desired posts sorting method", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "date",
					"type" => "select",
					"options" => $THEMEREX_GLOBALS['sc_params']['sorting']
				),
				"order" => array(
					"title" => esc_html__("Post order", "trx_utils"),
					"desc" => wp_kses( __("Select desired posts order", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "desc",
					"type" => "switch",
					"size" => "big",
					"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
				),
				"only" => array(
					"title" => esc_html__("Select posts only", "trx_utils"),
					"desc" => wp_kses( __("Select posts only with reviews, videos, audios, thumbs or galleries", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "no",
					"type" => "select",
					"options" => $THEMEREX_GLOBALS['sc_params']['formats']
				),
				"scroll" => array(
					"title" => esc_html__("Use scroller", "trx_utils"),
					"desc" => wp_kses( __("Use scroller to show all posts", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"divider" => true,
					"value" => "no",
					"type" => "switch",
					"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
				),
				"controls" => array(
					"title" => esc_html__("Show slider controls", "trx_utils"),
					"desc" => wp_kses( __("Show arrows to control scroll slider", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"dependency" => array(
						'scroll' => array('yes')
					),
					"value" => "no",
					"type" => "switch",
					"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
				),
				"location" => array(
					"title" => esc_html__("Dedicated content location", "trx_utils"),
					"desc" => wp_kses( __("Select position for dedicated content (only for style=excerpt)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"divider" => true,
					"dependency" => array(
						'style' => array('excerpt')
					),
					"value" => "default",
					"type" => "select",
					"options" => $THEMEREX_GLOBALS['sc_params']['locations']
				),
				"rating" => array(
					"title" => esc_html__("Show rating stars", "trx_utils"),
					"desc" => wp_kses( __("Show rating stars under post's header", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "no",
					"type" => "switch",
					"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
				),
				"info" => array(
					"title" => esc_html__("Show post info block", "trx_utils"),
					"desc" => wp_kses( __("Show post info block (author, date, tags, etc.)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "no",
					"type" => "switch",
					"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
				),
				"links" => array(
					"title" => esc_html__("Allow links on the post", "trx_utils"),
					"desc" => wp_kses( __("Allow links on the post from each blogger item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "yes",
					"type" => "switch",
					"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
				),
				"descr" => array(
					"title" => esc_html__("Description length", "trx_utils"),
					"desc" => wp_kses( __("How many characters are displayed from post excerpt? If 0 - don't show description", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 0,
					"min" => 0,
					"step" => 10,
					"type" => "spinner"
				),
				"readmore" => array(
					"title" => esc_html__("More link text", "trx_utils"),
					"desc" => wp_kses( __("Read more link text. If empty - show 'More', else - used as link text", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"link" => array(
					"title" => esc_html__("Button URL", "trx_utils"),
					"desc" => wp_kses( __("Link URL for the button at the bottom of the block", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"link_caption" => array(
					"title" => esc_html__("Button caption", "trx_utils"),
					"desc" => wp_kses( __("Caption for the button at the bottom of the block", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
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
if ( !function_exists( 'themerex_sc_blogger_reg_shortcodes_vc' ) ) {
	//add_action('themerex_action_shortcodes_list_vc', 'themerex_sc_blogger_reg_shortcodes_vc');
	function themerex_sc_blogger_reg_shortcodes_vc() {
		global $THEMEREX_GLOBALS;
	
		vc_map( array(
			"base" => "trx_blogger",
			"name" => esc_html__("Blogger", "trx_utils"),
			"description" => wp_kses( __("Insert posts (pages) in many styles from desired categories or directly from ids", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_blogger',
			"class" => "trx_sc_single trx_sc_blogger",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Output style", "trx_utils"),
					"description" => wp_kses( __("Select desired style for posts output", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"std" => "accordion",
					"class" => "",
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['blogger_styles']),
					"type" => "dropdown"
				),
				array(
					"param_name" => "filters",
					"heading" => esc_html__("Show filters", "trx_utils"),
					"description" => wp_kses( __("Use post's tags or categories as filter buttons", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['filters']),
					"type" => "dropdown"
				),
				array(
					"param_name" => "hover",
					"heading" => esc_html__("Hover effect", "trx_utils"),
					"description" => wp_kses( __("Select hover effect (only if style=Portfolio)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['hovers']),
					'dependency' => array(
						'element' => 'style',
						'value' => array('portfolio_2','portfolio_3','portfolio_4','grid_2','grid_3','grid_4','square_2','square_3','square_4','short_2','short_3','short_4','colored_2','colored_3','colored_4','courses_2','courses_3','courses_4')
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "hover_dir",
					"heading" => esc_html__("Hover direction", "trx_utils"),
					"description" => wp_kses( __("Select hover direction (only if style=Portfolio and hover=Circle|Square)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['hovers_dir']),
					'dependency' => array(
						'element' => 'style',
						'value' => array('portfolio_2','portfolio_3','portfolio_4','grid_2','grid_3','grid_4','square_2','square_3','square_4','short_2','short_3','short_4','colored_2','colored_3','colored_4','courses_2','courses_3','courses_4')
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "location",
					"heading" => esc_html__("Dedicated content location", "trx_utils"),
					"description" => wp_kses( __("Select position for dedicated content (only for style=excerpt)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					'dependency' => array(
						'element' => 'style',
						'value' => array('excerpt')
					),
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['locations']),
					"type" => "dropdown"
				),
				array(
					"param_name" => "dir",
					"heading" => esc_html__("Posts direction", "trx_utils"),
					"description" => wp_kses( __("Display posts in horizontal or vertical direction", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"admin_label" => true,
					"class" => "",
					"std" => "horizontal",
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['dir']),
					"type" => "dropdown"
				),
				array(
					"param_name" => "columns",
					"heading" => esc_html__("Columns number", "trx_utils"),
					"description" => wp_kses( __("How many columns used to display posts?", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					'dependency' => array(
						'element' => 'dir',
						'value' => 'horizontal'
					),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "rating",
					"heading" => esc_html__("Show rating stars", "trx_utils"),
					"description" => wp_kses( __("Show rating stars under post's header", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Details', 'trx_utils'),
					"class" => "",
					"value" => array(esc_html__('Show rating', 'trx_utils') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "info",
					"heading" => esc_html__("Show post info block", "trx_utils"),
					"description" => wp_kses( __("Show post info block (author, date, tags, etc.)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"std" => 'yes',
					"value" => array(esc_html__('Show info', 'trx_utils') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "descr",
					"heading" => esc_html__("Description length", "trx_utils"),
					"description" => wp_kses( __("How many characters are displayed from post excerpt? If 0 - don't show description", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Details', 'trx_utils'),
					"class" => "",
					"value" => 0,
					"type" => "textfield"
				),
				array(
					"param_name" => "links",
					"heading" => esc_html__("Allow links to the post", "trx_utils"),
					"description" => wp_kses( __("Allow links to the post from each blogger item", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Details', 'trx_utils'),
					"class" => "",
					"std" => 'yes',
					"value" => array(esc_html__('Allow links', 'trx_utils') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "readmore",
					"heading" => esc_html__("More link text", "trx_utils"),
					"description" => wp_kses( __("Read more link text. If empty - show 'More', else - used as link text", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Details', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
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
					"param_name" => "link_caption",
					"heading" => esc_html__("Button caption", "trx_utils"),
					"description" => wp_kses( __("Caption for the button at the bottom of the block", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Captions', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "post_type",
					"heading" => esc_html__("Post type", "trx_utils"),
					"description" => wp_kses( __("Select post type to show", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Query', 'trx_utils'),
					"class" => "",
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['posts_types']),
					"type" => "dropdown"
				),
				array(
					"param_name" => "ids",
					"heading" => esc_html__("Post IDs list", "trx_utils"),
					"description" => wp_kses( __("Comma separated list of posts ID. If set - parameters above are ignored!", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Query', 'trx_utils'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "cat",
					"heading" => esc_html__("Categories list", "trx_utils"),
					"description" => wp_kses( __("Select category. If empty - show posts from any category or from IDs list", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					'dependency' => array(
						'element' => 'ids',
						'is_empty' => true
					),
					"group" => esc_html__('Query', 'trx_utils'),
					"class" => "",
					"value" => array_flip(themerex_array_merge(array(0 => esc_html__('- Select category -', 'trx_utils')), $THEMEREX_GLOBALS['sc_params']['categories'])),
					"type" => "dropdown"
				),
				array(
					"param_name" => "count",
					"heading" => esc_html__("Total posts to show", "trx_utils"),
					"description" => wp_kses( __("How many posts will be displayed? If used IDs - this parameter ignored.", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					'dependency' => array(
						'element' => 'ids',
						'is_empty' => true
					),
					"admin_label" => true,
					"group" => esc_html__('Query', 'trx_utils'),
					"class" => "",
					"value" => 3,
					"type" => "textfield"
				),
				array(
					"param_name" => "offset",
					"heading" => esc_html__("Offset before select posts", "trx_utils"),
					"description" => wp_kses( __("Skip posts before select next part.", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					'dependency' => array(
						'element' => 'ids',
						'is_empty' => true
					),
					"group" => esc_html__('Query', 'trx_utils'),
					"class" => "",
					"value" => 0,
					"type" => "textfield"
				),
				array(
					"param_name" => "orderby",
					"heading" => esc_html__("Post order by", "trx_utils"),
					"description" => wp_kses( __("Select desired posts sorting method", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"group" => esc_html__('Query', 'trx_utils'),
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['sorting']),
					"type" => "dropdown"
				),
				array(
					"param_name" => "order",
					"heading" => esc_html__("Post order", "trx_utils"),
					"description" => wp_kses( __("Select desired posts order", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"group" => esc_html__('Query', 'trx_utils'),
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
					"type" => "dropdown"
				),
				array(
					"param_name" => "only",
					"heading" => esc_html__("Select posts only", "trx_utils"),
					"description" => wp_kses( __("Select posts only with reviews, videos, audios, thumbs or galleries", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "",
					"group" => esc_html__('Query', 'trx_utils'),
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['formats']),
					"type" => "dropdown"
				),
				array(
					"param_name" => "scroll",
					"heading" => esc_html__("Use scroller", "trx_utils"),
					"description" => wp_kses( __("Use scroller to show all posts", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Scroll', 'trx_utils'),
					"class" => "",
					"value" => array(esc_html__('Use scroller', 'trx_utils') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "controls",
					"heading" => esc_html__("Show slider controls", "trx_utils"),
					"description" => wp_kses( __("Show arrows to control scroll slider", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"group" => esc_html__('Scroll', 'trx_utils'),
					"class" => "",
					"value" => array(esc_html__('Show controls', 'trx_utils') => 'yes'),
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
		) );
		
		class WPBakeryShortCode_Trx_Blogger extends Themerex_VC_ShortCodeSingle {}
	}
}
?>