<?php
/**
 * ThemeREX Framework: Testimonial post type settings
 *
 * @package	themerex
 * @since	themerex 1.0
 */

// Theme init
if (!function_exists('themerex_testimonial_theme_setup')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_testimonial_theme_setup' );
	function themerex_testimonial_theme_setup() {
	
		// Add item in the admin menu
        add_filter('trx_utils_filter_override_options',			'themerex_testimonial_add_override_options');

		// Save data from override options
		add_action('save_post',				'themerex_testimonial_save_data');

		// Add shortcodes [trx_testimonials] and [trx_testimonials_item]
		add_action('themerex_action_shortcodes_list',		'themerex_testimonials_reg_shortcodes');
		if (function_exists('themerex_exists_visual_composer') && themerex_exists_visual_composer())
			add_action('themerex_action_shortcodes_list_vc','themerex_testimonials_reg_shortcodes_vc');

		// override options fields
		global $THEMEREX_GLOBALS;
		$THEMEREX_GLOBALS['testimonial_override_options'] = array(
			'id' => 'testimonial-override-options',
			'title' => esc_html__('Testimonial Details', 'wineshop'),
			'page' => 'testimonial',
			'context' => 'normal',
			'priority' => 'high',
			'fields' => array(
				"testimonial_author" => array(
					"title" => esc_html__('Testimonial author',  'wineshop'),
					"desc" => wp_kses( __("Name of the testimonial's author", 'wineshop'), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "testimonial_author",
					"std" => "",
					"type" => "text"),
				"testimonial_position" => array(
					"title" => esc_html__("Author's position",  'wineshop'),
					"desc" => wp_kses( __("Position of the testimonial's author", 'wineshop'), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "testimonial_author",
					"std" => "",
					"type" => "text"),
				"testimonial_email" => array(
					"title" => esc_html__("Author's e-mail",  'wineshop'),
					"desc" => wp_kses( __("E-mail of the testimonial's author - need to take Gravatar (if registered)", 'wineshop'), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "testimonial_email",
					"std" => "",
					"type" => "text"),
				"testimonial_link" => array(
					"title" => esc_html__('Testimonial link',  'wineshop'),
					"desc" => wp_kses( __("URL of the testimonial source or author profile page", 'wineshop'), $THEMEREX_GLOBALS['allowed_tags'] ),
					"class" => "testimonial_link",
					"std" => "",
					"type" => "text")
			)
		);

        // Add supported data types
        themerex_theme_support_pt('testimonial');
        themerex_theme_support_tx('testimonial_group');
	}
}


// Add override options
if (!function_exists('themerex_testimonial_add_override_options')) {
    function themerex_testimonial_add_override_options($boxes = array()) {
        $boxes[] = array_merge(themerex_storage_get('testimonial_override_options'), array('callback' => 'themerex_testimonial_show_override_options'));
        return $boxes;
    }
}

// Callback function to show fields in override options
if (!function_exists('themerex_testimonial_show_override_options')) {
	function themerex_testimonial_show_override_options() {
		global $post, $THEMEREX_GLOBALS;

		// Use nonce for verification
		echo '<input type="hidden" name="override_options_testimonial_nonce" value="', esc_attr($THEMEREX_GLOBALS['admin_nonce']), '" />';
		
		$data = get_post_meta($post->ID, 'testimonial_data', true);
	
		$fields = $THEMEREX_GLOBALS['testimonial_override_options']['fields'];
		?>
		<table class="testimonial_area">
		<?php
		if (is_array($fields) && count($fields) > 0) {
			foreach ($fields as $id=>$field) { 
				$meta = isset($data[$id]) ? $data[$id] : '';
				?>
				<tr class="testimonial_field <?php echo esc_attr($field['class']); ?>" valign="top">
					<td><label for="<?php echo esc_attr($id); ?>"><?php echo esc_attr($field['title']); ?></label></td>
					<td><input type="text" name="<?php echo esc_attr($id); ?>" id="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($meta); ?>" size="30" />
						<br><small><?php echo esc_attr($field['desc']); ?></small></td>
				</tr>
				<?php
			}
		}
		?>
		</table>
		<?php
	}
}


// Save data from override options
if (!function_exists('themerex_testimonial_save_data')) {
	function themerex_testimonial_save_data($post_id) {
		global $THEMEREX_GLOBALS;
		// verify nonce
		if (!isset($_POST['override_options_testimonial_nonce']) || !wp_verify_nonce($_POST['override_options_testimonial_nonce'], $THEMEREX_GLOBALS['admin_url'])) {
			return $post_id;
		}

		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		// check permissions
		if ($_POST['post_type']!='testimonial' || !current_user_can('edit_post', $post_id)) {
			return $post_id;
		}

		$data = array();

		$fields = $THEMEREX_GLOBALS['testimonial_override_options']['fields'];

		// Post type specific data handling
		if (is_array($fields) && count($fields) > 0) {
			foreach ($fields as $id=>$field) { 
				if (isset($_POST[$id])) 
					$data[$id] = stripslashes($_POST[$id]);
			}
		}

		update_post_meta($post_id, 'testimonial_data', $data);
	}
}






// ---------------------------------- [trx_testimonials] ---------------------------------------

if (!function_exists('themerex_sc_testimonials')) {
	function themerex_sc_testimonials($atts, $content=null){	
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "testimonials-1",
			"columns" => 1,
			"slider" => "yes",
			"slides_space" => 0,
			"controls" => "no",
			"interval" => "",
			"autoheight" => "no",
			"align" => "",
			"custom" => "no",
			"ids" => "",
			"cat" => "",
			"count" => "3",
			"offset" => "",
			"orderby" => "date",
			"order" => "desc",
			"scheme" => "",
			"bg_color" => "",
			"bg_image" => "",
			"bg_overlay" => "",
			"bg_texture" => "",
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"description_link" => "",
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
	
		if (empty($id)) $id = "sc_testimonials_".str_replace('.', '', mt_rand());
		if (empty($width)) $width = "100%";
		if (!empty($height) && themerex_param_is_on($autoheight)) $autoheight = "no";
		if (empty($interval)) $interval = mt_rand(5000, 10000);
	
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

		$ws = themerex_get_css_dimensions_from_values($width);
		$hs = themerex_get_css_dimensions_from_values('', $height);
		$css .= ($hs) . ($ws);

		$count = max(1, (int) $count);
		$columns = max(1, min(12, (int) $columns));
		if (themerex_param_is_off($custom) && $count < $columns) $columns = $count;
		
		global $THEMEREX_GLOBALS;
		$THEMEREX_GLOBALS['sc_testimonials_id'] = $id;
		$THEMEREX_GLOBALS['sc_testimonials_style'] = $style;
		$THEMEREX_GLOBALS['sc_testimonials_columns'] = $columns;
		$THEMEREX_GLOBALS['sc_testimonials_counter'] = 0;
		$THEMEREX_GLOBALS['sc_testimonials_slider'] = $slider;
		$THEMEREX_GLOBALS['sc_testimonials_css_wh'] = $ws . $hs;

		if (themerex_param_is_on($slider)) themerex_enqueue_slider('swiper');
	
		$output = ($bg_color!='' || $bg_image!='' || $bg_overlay>0 || $bg_texture>0 || themerex_strlen($bg_texture)>2 || ($scheme && !themerex_param_is_off($scheme) && !themerex_param_is_inherit($scheme))
					? '<div class="sc_testimonials_wrap sc_section'
							. ($scheme && !themerex_param_is_off($scheme) && !themerex_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
							. '"'
						.' style="'
							. ($bg_color !== '' && $bg_overlay==0 ? 'background-color:' . esc_attr($bg_color) . ';' : '')
							. ($bg_image !== '' ? 'background-image:url(' . esc_url($bg_image) . ');' : '')
							. '"'
						. (!themerex_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_get_animation_classes($animation)).'"' : '')
						. '>'
						. '<div class="sc_section_overlay'.($bg_texture>0 ? ' texture_bg_'.esc_attr($bg_texture) : '') . '"'
								. ' style="' . ($bg_overlay>0 ? 'background-color:rgba('.(int)$rgb['r'].','.(int)$rgb['g'].','.(int)$rgb['b'].','.min(1, max(0, $bg_overlay)).');' : '')
									. (themerex_strlen($bg_texture)>2 ? 'background-image:url('.esc_url($bg_texture).');' : '')
									. '"'
									. ($bg_overlay > 0 ? ' data-overlay="'.esc_attr($bg_overlay).'" data-bg_color="'.esc_attr($bg_color).'"' : '')
									. '>' 
					: '')
				. '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_testimonials sc_testimonials_style_'.esc_attr($style)
 					. ' ' . esc_attr(themerex_get_template_property($style, 'container_classes'))
 					. (themerex_param_is_on($slider)
						? ' sc_slider_swiper swiper-slider-container'
							. ' ' . esc_attr(themerex_get_slider_controls_classes($controls))
							. (themerex_param_is_on($autoheight) ? ' sc_slider_height_auto' : '')
							. ($hs ? ' sc_slider_height_fixed' : '')
						: '')
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. ($align!='' && $align!='none' ? ' align'.esc_attr($align) : '')
					. '"'
				. ($bg_color=='' && $bg_image=='' && $bg_overlay==0 && ($bg_texture=='' || $bg_texture=='0') && !themerex_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_get_animation_classes($animation)).'"' : '')
				. (!empty($width) && themerex_strpos($width, '%')===false ? ' data-old-width="' . esc_attr($width) . '"' : '')
				. (!empty($height) && themerex_strpos($height, '%')===false ? ' data-old-height="' . esc_attr($height) . '"' : '')
				. ((int) $interval > 0 ? ' data-interval="'.esc_attr($interval).'"' : '')
				. ($columns > 1 ? ' data-slides-per-view="' . esc_attr($columns) . '"' : '')
				. ($slides_space > 0 ? ' data-slides-space="' . esc_attr($slides_space) . '"' : '')
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. '>'
			. (!empty($subtitle) ? '<h6 class="sc_testimonials_subtitle sc_item_subtitle">' . trim(themerex_strmacros($subtitle)) . '</h6>' : '')
			. (!empty($title) ? '<h2 class="sc_testimonials_title sc_item_title">' . trim(themerex_strmacros($title)) . '</h2>' : '')
			. (!empty($description) ? '<div class="sc_testimonials_descr sc_item_descr">'.( !empty($description_link) ? '<a href='.$description_link.'>' : ''). trim(themerex_strmacros($description)).( !empty($description_link) ? '</a>' : '') . '</div>' : '')
			. (themerex_param_is_on($slider) 
				? '<div class="slides swiper-wrapper">' 
				: ($columns > 1 
					? '<div class="sc_columns columns_wrap">' 
					: '')
				);
	
		$content = do_shortcode($content);
			
		if (themerex_param_is_on($custom) && $content) {
			$output .= $content;
		} else {
			global $post;
		
			if (!empty($ids)) {
				$posts = explode(',', $ids);
				$count = count($posts);
			}
			
			$args = array(
				'post_type' => 'testimonial',
				'post_status' => 'publish',
				'posts_per_page' => $count,
				'ignore_sticky_posts' => true,
				'order' => $order=='asc' ? 'asc' : 'desc',
			);
		
			if ($offset > 0 && empty($ids)) {
				$args['offset'] = $offset;
			}
		
			$args = themerex_query_add_sort_order($args, $orderby, $order);
			$args = themerex_query_add_posts_and_cats($args, $ids, 'testimonial', $cat, 'testimonial_group');
	
			$query = new WP_Query( $args );
	
			$post_number = 0;
				
			while ( $query->have_posts() ) { 
				$query->the_post();
				$post_number++;
				$args = array(
					'layout' => $style,
					'show' => false,
					'number' => $post_number,
					'posts_on_page' => ($count > 0 ? $count : $query->found_posts),
					"descr" => themerex_get_custom_option('post_excerpt_maxlength'.($columns > 1 ? '_masonry' : '')),
					"orderby" => $orderby,
					'content' => false,
					'terms_list' => false,
					'columns_count' => $columns,
					'slider' => $slider,
					'tag_id' => $id ? $id . '_' . $post_number : '',
					'tag_class' => '',
					'tag_animation' => '',
					'tag_css' => '',
					'tag_css_wh' => $ws . $hs
				);
				$post_data = themerex_get_post_data($args);
				$post_data['post_content'] = wpautop($post_data['post_content']);	// Add <p> around text and paragraphs. Need separate call because 'content'=>false (see above)
				$post_meta = get_post_meta($post_data['post_id'], 'testimonial_data', true);
				$thumb_sizes = themerex_get_thumb_sizes(array('layout' => $style));
				$args['author'] = $post_meta['testimonial_author'];
				$args['position'] = $post_meta['testimonial_position'];
				$args['link'] = !empty($post_meta['testimonial_link']) ? $post_meta['testimonial_link'] : '';
				$args['email'] = $post_meta['testimonial_email'];
				$args['photo'] = $post_data['post_thumb'];
				if (empty($args['photo']) && !empty($args['email'])) $args['photo'] = get_avatar($args['email'], $thumb_sizes['w']*min(2, max(1, themerex_get_theme_option("retina_ready"))));
				$output .= themerex_show_post_layout($args, $post_data);
			}
			wp_reset_postdata();
		}
	
		if (themerex_param_is_on($slider)) {
			$output .= '</div>'
				. '<div class="sc_slider_controls_wrap"><a class="sc_slider_prev" href="#"></a><a class="sc_slider_next" href="#"></a></div>'
				. '<div class="sc_slider_pagination_wrap"></div>';
		} else if ($columns > 1) {
			$output .= '</div>';
		}

		$output .= '</div>'
					. ($bg_color!='' || $bg_image!='' || $bg_overlay>0 || $bg_texture>0 || themerex_strlen($bg_texture)>2
						?  '</div></div>'
						: '');
	
		// Add template specific scripts and styles
		do_action('themerex_action_blog_scripts', $style);

		return apply_filters('themerex_shortcode_output', $output, 'trx_testimonials', $atts, $content);
	}
	themerex_require_shortcode('trx_testimonials', 'themerex_sc_testimonials');
}
	
	
if (!function_exists('themerex_sc_testimonials_item')) {
	function themerex_sc_testimonials_item($atts, $content=null){	
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			// Individual params
			"author" => "",
			"position" => "",
			"link" => "",
			"photo" => "",
			"email" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
		), $atts)));

		global $THEMEREX_GLOBALS;
		$THEMEREX_GLOBALS['sc_testimonials_counter']++;
	
		$id = $id ? $id : ($THEMEREX_GLOBALS['sc_testimonials_id'] ? $THEMEREX_GLOBALS['sc_testimonials_id'] . '_' . $THEMEREX_GLOBALS['sc_testimonials_counter'] : '');
	
		$thumb_sizes = themerex_get_thumb_sizes(array('layout' => $THEMEREX_GLOBALS['sc_testimonials_style']));

		if (empty($photo)) {
			if (!empty($email))
				$photo = get_avatar($email, $thumb_sizes['w']*min(2, max(1, themerex_get_theme_option("retina_ready"))));
		} else {
			if ($photo > 0) {
				$attach = wp_get_attachment_image_src( $photo, 'full' );
				if (isset($attach[0]) && $attach[0]!='')
					$photo = $attach[0];
			}
			$photo = themerex_get_resized_image_tag($photo, $thumb_sizes['w'], $thumb_sizes['h']);
		}

		$post_data = array(
			'post_content' => do_shortcode($content)
		);
		$args = array(
			'layout' => $THEMEREX_GLOBALS['sc_testimonials_style'],
			'number' => $THEMEREX_GLOBALS['sc_testimonials_counter'],
			'columns_count' => $THEMEREX_GLOBALS['sc_testimonials_columns'],
			'slider' => $THEMEREX_GLOBALS['sc_testimonials_slider'],
			'show' => false,
			'descr'  => 0,
			'tag_id' => $id,
			'tag_class' => $class,
			'tag_animation' => '',
			'tag_css' => $css,
			'tag_css_wh' => $THEMEREX_GLOBALS['sc_testimonials_css_wh'],
			'author' => $author,
			'position' => $position,
			'link' => $link,
			'email' => $email,
			'photo' => $photo
		);
		$output = themerex_show_post_layout($args, $post_data);

		return apply_filters('themerex_shortcode_output', $output, 'trx_testimonials_item', $atts, $content);
	}
	themerex_require_shortcode('trx_testimonials_item', 'themerex_sc_testimonials_item');
}
// ---------------------------------- [/trx_testimonials] ---------------------------------------



// Add [trx_testimonials] and [trx_testimonials_item] in the shortcodes list
if (!function_exists('themerex_testimonials_reg_shortcodes')) {
	function themerex_testimonials_reg_shortcodes() {
		global $THEMEREX_GLOBALS;
		if (isset($THEMEREX_GLOBALS['shortcodes'])) {

			$testimonials_groups = themerex_get_list_terms(false, 'testimonial_group');
			$testimonials_styles = themerex_get_list_templates('testimonials');
			$controls = themerex_get_list_slider_controls();

			themerex_array_insert_before($THEMEREX_GLOBALS['shortcodes'], 'trx_title', array(
			
				// Testimonials
				"trx_testimonials" => array(
					"title" => esc_html__("Testimonials", "wineshop"),
					"desc" => wp_kses( __("Insert testimonials into post (page)", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"title" => array(
							"title" => esc_html__("Title", "wineshop"),
							"desc" => wp_kses( __("Title for the block", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"value" => "",
							"type" => "text"
						),
						"subtitle" => array(
							"title" => esc_html__("Subtitle", "wineshop"),
							"desc" => wp_kses( __("Subtitle for the block", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"value" => "",
							"type" => "text"
						),
						"description" => array(
							"title" => esc_html__("Description", "wineshop"),
							"desc" => wp_kses( __("Short description for the block", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"value" => "",
							"type" => "textarea"
						),
						"style" => array(
							"title" => esc_html__("Testimonials style", "wineshop"),
							"desc" => wp_kses( __("Select style to display testimonials", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"value" => "testimonials-1",
							"type" => "select",
							"options" => $testimonials_styles
						),
						"columns" => array(
							"title" => esc_html__("Columns", "wineshop"),
							"desc" => wp_kses( __("How many columns use to show testimonials", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"value" => 1,
							"min" => 1,
							"max" => 6,
							"step" => 1,
							"type" => "spinner"
						),
						"slider" => array(
							"title" => esc_html__("Slider", "wineshop"),
							"desc" => wp_kses( __("Use slider to show testimonials", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"value" => "yes",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"controls" => array(
							"title" => esc_html__("Controls", "wineshop"),
							"desc" => wp_kses( __("Slider controls style and position", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"dependency" => array(
								'slider' => array('yes')
							),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $controls
						),
						"slides_space" => array(
							"title" => esc_html__("Space between slides", "wineshop"),
							"desc" => wp_kses( __("Size of space (in px) between slides", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"dependency" => array(
								'slider' => array('yes')
							),
							"value" => 0,
							"min" => 0,
							"max" => 100,
							"step" => 10,
							"type" => "spinner"
						),
						"interval" => array(
							"title" => esc_html__("Slides change interval", "wineshop"),
							"desc" => wp_kses( __("Slides change interval (in milliseconds: 1000ms = 1s)", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"dependency" => array(
								'slider' => array('yes')
							),
							"value" => 7000,
							"step" => 500,
							"min" => 0,
							"type" => "spinner"
						),
						"autoheight" => array(
							"title" => esc_html__("Autoheight", "wineshop"),
							"desc" => wp_kses( __("Change whole slider's height (make it equal current slide's height)", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"dependency" => array(
								'slider' => array('yes')
							),
							"value" => "yes",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"align" => array(
							"title" => esc_html__("Alignment", "wineshop"),
							"desc" => wp_kses( __("Alignment of the testimonials block", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['align']
						),
						"custom" => array(
							"title" => esc_html__("Custom", "wineshop"),
							"desc" => wp_kses( __("Allow get testimonials from inner shortcodes (custom) or get it from specified group (cat)", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"divider" => true,
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"cat" => array(
							"title" => esc_html__("Categories", "wineshop"),
							"desc" => wp_kses( __("Select categories (groups) to show testimonials. If empty - select testimonials from any category (group) or from IDs list", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"dependency" => array(
								'custom' => array('no')
							),
							"divider" => true,
							"value" => "",
							"type" => "select",
							"style" => "list",
							"multiple" => true,
							"options" => themerex_array_merge(array(0 => esc_html__('- Select category -', 'wineshop')), $testimonials_groups)
						),
						"count" => array(
							"title" => esc_html__("Number of posts", "wineshop"),
							"desc" => wp_kses( __("How many posts will be displayed? If used IDs - this parameter ignored.", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => 3,
							"min" => 1,
							"max" => 100,
							"type" => "spinner"
						),
						"offset" => array(
							"title" => esc_html__("Offset before select posts", "wineshop"),
							"desc" => wp_kses( __("Skip posts before select next part.", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => 0,
							"min" => 0,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => esc_html__("Post order by", "wineshop"),
							"desc" => wp_kses( __("Select desired posts sorting method", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "date",
							"type" => "select",
							"options" => $THEMEREX_GLOBALS['sc_params']['sorting']
						),
						"order" => array(
							"title" => esc_html__("Post order", "wineshop"),
							"desc" => wp_kses( __("Select desired posts order", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "desc",
							"type" => "switch",
							"size" => "big",
							"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
						),
						"ids" => array(
							"title" => esc_html__("Post IDs list", "wineshop"),
							"desc" => wp_kses( __("Comma separated list of posts ID. If set - parameters above are ignored!", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "",
							"type" => "text"
						),
						"scheme" => array(
							"title" => esc_html__("Color scheme", "wineshop"),
							"desc" => wp_kses( __("Select color scheme for this block", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"value" => "",
							"type" => "checklist",
							"options" => $THEMEREX_GLOBALS['sc_params']['schemes']
						),
						"bg_color" => array(
							"title" => esc_html__("Background color", "wineshop"),
							"desc" => wp_kses( __("Any background color for this section", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"value" => "",
							"type" => "color"
						),
						"bg_image" => array(
							"title" => esc_html__("Background image URL", "wineshop"),
							"desc" => wp_kses( __("Select or upload image or write URL from other site for the background", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"readonly" => false,
							"value" => "",
							"type" => "media"
						),
						"bg_overlay" => array(
							"title" => esc_html__("Overlay", "wineshop"),
							"desc" => wp_kses( __("Overlay color opacity (from 0.0 to 1.0)", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"min" => "0",
							"max" => "1",
							"step" => "0.1",
							"value" => "0",
							"type" => "spinner"
						),
						"bg_texture" => array(
							"title" => esc_html__("Texture", "wineshop"),
							"desc" => wp_kses( __("Predefined texture style from 1 to 11. 0 - without texture.", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
							"min" => "0",
							"max" => "11",
							"step" => "1",
							"value" => "0",
							"type" => "spinner"
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
						"name" => "trx_testimonials_item",
						"title" => esc_html__("Item", "wineshop"),
						"desc" => wp_kses( __("Testimonials item (custom parameters)", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"container" => true,
						"params" => array(
							"author" => array(
								"title" => esc_html__("Author", "wineshop"),
								"desc" => wp_kses( __("Name of the testimonmials author", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
								"value" => "",
								"type" => "text"
							),
							"link" => array(
								"title" => esc_html__("Link", "wineshop"),
								"desc" => wp_kses( __("Link URL to the testimonmials author page", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
								"value" => "",
								"type" => "text"
							),
							"email" => array(
								"title" => esc_html__("E-mail", "wineshop"),
								"desc" => wp_kses( __("E-mail of the testimonmials author (to get gravatar)", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
								"value" => "",
								"type" => "text"
							),
							"photo" => array(
								"title" => esc_html__("Photo", "wineshop"),
								"desc" => wp_kses( __("Select or upload photo of testimonmials author or write URL of photo from other site", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
								"value" => "",
								"type" => "media"
							),
							"_content_" => array(
								"title" => esc_html__("Testimonials text", "wineshop"),
								"desc" => wp_kses( __("Current testimonials text", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
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
				)

			));
		}
	}
}


// Add [trx_testimonials] and [trx_testimonials_item] in the VC shortcodes list
if (!function_exists('themerex_testimonials_reg_shortcodes_vc')) {
	function themerex_testimonials_reg_shortcodes_vc() {
		global $THEMEREX_GLOBALS;

		$testimonials_groups = themerex_get_list_terms(false, 'testimonial_group');
		$testimonials_styles = themerex_get_list_templates('testimonials');
		$controls			 = themerex_get_list_slider_controls();
			
		// Testimonials			
		vc_map( array(
				"base" => "trx_testimonials",
				"name" => esc_html__("Testimonials", "wineshop"),
				"description" => wp_kses( __("Insert testimonials slider", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
				"category" => esc_html__('Content', 'wineshop'),
				'icon' => 'icon_trx_testimonials',
				"class" => "trx_sc_columns trx_sc_testimonials",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"as_parent" => array('only' => 'trx_testimonials_item'),
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => esc_html__("Testimonials style", "wineshop"),
						"description" => wp_kses( __("Select style to display testimonials", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"class" => "",
						"admin_label" => true,
						"value" => array_flip($testimonials_styles),
						"type" => "dropdown"
					),
					array(
						"param_name" => "slider",
						"heading" => esc_html__("Slider", "wineshop"),
						"description" => wp_kses( __("Use slider to show testimonials", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"group" => esc_html__('Slider', 'wineshop'),
						"class" => "",
						"std" => "yes",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['yes_no']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "controls",
						"heading" => esc_html__("Controls", "wineshop"),
						"description" => wp_kses( __("Slider controls style and position", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"group" => esc_html__('Slider', 'wineshop'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"std" => "no",
						"value" => array_flip($controls),
						"type" => "dropdown"
					),
					array(
						"param_name" => "slides_space",
						"heading" => esc_html__("Space between slides", "wineshop"),
						"description" => wp_kses( __("Size of space (in px) between slides", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"group" => esc_html__('Slider', 'wineshop'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "interval",
						"heading" => esc_html__("Slides change interval", "wineshop"),
						"description" => wp_kses( __("Slides change interval (in milliseconds: 1000ms = 1s)", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"group" => esc_html__('Slider', 'wineshop'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"value" => "7000",
						"type" => "textfield"
					),
					array(
						"param_name" => "autoheight",
						"heading" => esc_html__("Autoheight", "wineshop"),
						"description" => wp_kses( __("Change whole slider's height (make it equal current slide's height)", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"group" => esc_html__('Slider', 'wineshop'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"value" => array("Autoheight" => "yes" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "align",
						"heading" => esc_html__("Alignment", "wineshop"),
						"description" => wp_kses( __("Alignment of the testimonials block", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "custom",
						"heading" => esc_html__("Custom", "wineshop"),
						"description" => wp_kses( __("Allow get testimonials from inner shortcodes (custom) or get it from specified group (cat)", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"class" => "",
						"value" => array("Custom slides" => "yes" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "title",
						"heading" => esc_html__("Title", "wineshop"),
						"description" => wp_kses( __("Title for the block", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"group" => esc_html__('Captions', 'wineshop'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "subtitle",
						"heading" => esc_html__("Subtitle", "wineshop"),
						"description" => wp_kses( __("Subtitle for the block", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"group" => esc_html__('Captions', 'wineshop'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "description",
						"heading" => esc_html__("Description", "wineshop"),
						"description" => wp_kses( __("Description for the block", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"group" => esc_html__('Captions', 'wineshop'),
						"class" => "",
						"value" => "",
						"type" => "textarea"
					),
					array(
						"param_name" => "description_link",
						"heading" => esc_html__("Description URL", "wineshop"),
						"description" => wp_kses( __("Link URL for the description", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"group" => esc_html__('Captions', 'wineshop'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "cat",
						"heading" => esc_html__("Categories", "wineshop"),
						"description" => wp_kses( __("Select categories (groups) to show testimonials. If empty - select testimonials from any category (group) or from IDs list", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"group" => esc_html__('Query', 'wineshop'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => array_flip(themerex_array_merge(array(0 => esc_html__('- Select category -', 'wineshop')), $testimonials_groups)),
						"type" => "dropdown"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", "wineshop"),
						"description" => wp_kses( __("How many columns use to show testimonials", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"group" => esc_html__('Query', 'wineshop'),
						"admin_label" => true,
						"class" => "",
						"value" => "1",
						"type" => "textfield"
					),
					array(
						"param_name" => "count",
						"heading" => esc_html__("Number of posts", "wineshop"),
						"description" => wp_kses( __("How many posts will be displayed? If used IDs - this parameter ignored.", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"group" => esc_html__('Query', 'wineshop'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "3",
						"type" => "textfield"
					),
					array(
						"param_name" => "offset",
						"heading" => esc_html__("Offset before select posts", "wineshop"),
						"description" => wp_kses( __("Skip posts before select next part.", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"group" => esc_html__('Query', 'wineshop'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Post sorting", "wineshop"),
						"description" => wp_kses( __("Select desired posts sorting method", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"group" => esc_html__('Query', 'wineshop'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['sorting']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => esc_html__("Post order", "wineshop"),
						"description" => wp_kses( __("Select desired posts order", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"group" => esc_html__('Query', 'wineshop'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "ids",
						"heading" => esc_html__("Post IDs list", "wineshop"),
						"description" => wp_kses( __("Comma separated list of posts ID. If set - parameters above are ignored!", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"group" => esc_html__('Query', 'wineshop'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "scheme",
						"heading" => esc_html__("Color scheme", "wineshop"),
						"description" => wp_kses( __("Select color scheme for this block", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"group" => esc_html__('Colors and Images', 'wineshop'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['schemes']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "bg_color",
						"heading" => esc_html__("Background color", "wineshop"),
						"description" => wp_kses( __("Any background color for this section", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"group" => esc_html__('Colors and Images', 'wineshop'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_image",
						"heading" => esc_html__("Background image URL", "wineshop"),
						"description" => wp_kses( __("Select background image from library for this section", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"group" => esc_html__('Colors and Images', 'wineshop'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "bg_overlay",
						"heading" => esc_html__("Overlay", "wineshop"),
						"description" => wp_kses( __("Overlay color opacity (from 0.0 to 1.0)", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"group" => esc_html__('Colors and Images', 'wineshop'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_texture",
						"heading" => esc_html__("Texture", "wineshop"),
						"description" => wp_kses( __("Texture style from 1 to 11. Empty or 0 - without texture.", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"group" => esc_html__('Colors and Images', 'wineshop'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					themerex_vc_width(),
					themerex_vc_height(),
					$THEMEREX_GLOBALS['vc_params']['margin_top'],
					$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
					$THEMEREX_GLOBALS['vc_params']['margin_left'],
					$THEMEREX_GLOBALS['vc_params']['margin_right'],
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['animation'],
					$THEMEREX_GLOBALS['vc_params']['css']
				),
				'js_view' => 'VcTrxColumnsView'
		) );
			
			
		vc_map( array(
				"base" => "trx_testimonials_item",
				"name" => esc_html__("Testimonial", "wineshop"),
				"description" => wp_kses( __("Single testimonials item", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
				"show_settings_on_create" => true,
				"class" => "trx_sc_collection trx_sc_column_item trx_sc_testimonials_item",
				"content_element" => true,
				"is_container" => true,
				'icon' => 'icon_trx_testimonials_item',
				"as_child" => array('only' => 'trx_testimonials'),
				"as_parent" => array('except' => 'trx_testimonials'),
				"params" => array(
					array(
						"param_name" => "author",
						"heading" => esc_html__("Author", "wineshop"),
						"description" => wp_kses( __("Name of the testimonmials author", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "link",
						"heading" => esc_html__("Link", "wineshop"),
						"description" => wp_kses( __("Link URL to the testimonmials author page", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "email",
						"heading" => esc_html__("E-mail", "wineshop"),
						"description" => wp_kses( __("E-mail of the testimonmials author", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "photo",
						"heading" => esc_html__("Photo", "wineshop"),
						"description" => wp_kses( __("Select or upload photo of testimonmials author or write URL of photo from other site", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['css']
				),
				'js_view' => 'VcTrxColumnItemView'
		) );
			
		class WPBakeryShortCode_Trx_Testimonials extends Themerex_VC_ShortCodeColumns {}
		class WPBakeryShortCode_Trx_Testimonials_Item extends Themerex_VC_ShortCodeCollection {}
		
	}
}
?>