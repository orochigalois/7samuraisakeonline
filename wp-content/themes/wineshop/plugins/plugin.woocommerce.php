<?php
/* Woocommerce support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('themerex_woocommerce_theme_setup')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_woocommerce_theme_setup', 1 );
	function themerex_woocommerce_theme_setup() {

		if (themerex_exists_woocommerce()) {
			
			add_theme_support( 'woocommerce' );
			// Next setting from the WooCommerce 3.0+ enable built-in image zoom on the single product page
			add_theme_support( 'wc-product-gallery-zoom' );
			// Next setting from the WooCommerce 3.0+ enable built-in image slider on the single product page
			add_theme_support( 'wc-product-gallery-slider' );
//			// Next setting from the WooCommerce 3.0+ enable built-in image lightbox on the single product page
			add_theme_support( 'wc-product-gallery-lightbox' );
//
			add_action('themerex_action_add_styles', 				'themerex_woocommerce_frontend_scripts' );

			// Detect current page type, taxonomy and title (for custom post_types use priority < 10 to fire it handles early, than for standard post types)
			add_filter('themerex_filter_get_blog_type',				'themerex_woocommerce_get_blog_type', 9, 2);
			add_filter('themerex_filter_get_blog_title',			'themerex_woocommerce_get_blog_title', 9, 2);
			add_filter('themerex_filter_get_current_taxonomy',		'themerex_woocommerce_get_current_taxonomy', 9, 2);
			add_filter('themerex_filter_is_taxonomy',				'themerex_woocommerce_is_taxonomy', 9, 2);
			add_filter('themerex_filter_get_stream_page_title',		'themerex_woocommerce_get_stream_page_title', 9, 2);
			add_filter('themerex_filter_get_stream_page_link',		'themerex_woocommerce_get_stream_page_link', 9, 2);
			add_filter('themerex_filter_get_stream_page_id',		'themerex_woocommerce_get_stream_page_id', 9, 2);
			add_filter('themerex_filter_detect_inheritance_key',	'themerex_woocommerce_detect_inheritance_key', 9, 1);
			add_filter('themerex_filter_detect_template_page_id',	'themerex_woocommerce_detect_template_page_id', 9, 2);
			add_filter('themerex_filter_orderby_need',				'themerex_woocommerce_orderby_need', 9, 2);

			add_filter('themerex_filter_list_post_types', 			'themerex_woocommerce_list_post_types');

			add_action('themerex_action_shortcodes_list', 			'themerex_woocommerce_reg_shortcodes', 20);
			if (function_exists('themerex_exists_visual_composer') && themerex_exists_visual_composer())
				add_action('themerex_action_shortcodes_list_vc',	'themerex_woocommerce_reg_shortcodes_vc', 20);


		}

		if (is_admin()) {

			add_filter( 'themerex_filter_required_plugins',					'themerex_woocommerce_required_plugins' );
		}
	}
}

if ( !function_exists( 'themerex_woocommerce_settings_theme_setup2' ) ) {
	add_action( 'themerex_action_before_init_theme', 'themerex_woocommerce_settings_theme_setup2', 3 );
	function themerex_woocommerce_settings_theme_setup2() {
		if (themerex_exists_woocommerce()) {
			// Add WooCommerce pages in the Theme inheritance system
			themerex_add_theme_inheritance( array( 'woocommerce' => array(
				'stream_template' => '',
				'single_template' => '',
				'taxonomy' => array('product_cat'),
				'taxonomy_tags' => array('product_tag'),
				'post_type' => array('product'),
				'override' => 'page'
				) )
			);

			// Add WooCommerce specific options in the Theme Options
			global $THEMEREX_GLOBALS;

			themerex_array_insert_before($THEMEREX_GLOBALS['options'], 'partition_service', array(
				
				"partition_woocommerce" => array(
					"title" => esc_html__('WooCommerce', 'wineshop'),
					"icon" => "iconadmin-basket",
					"type" => "partition"),

				"info_wooc_1" => array(
					"title" => esc_html__('WooCommerce products list parameters', 'wineshop'),
					"desc" => esc_html__("Select WooCommerce products list's style and crop parameters", 'wineshop'),
					"type" => "info"),
		
				"shop_mode" => array(
					"title" => esc_html__('Shop list style',  'wineshop'),
					"desc" => esc_html__("WooCommerce products list's style: thumbs or list with description", 'wineshop'),
					"std" => "thumbs",
					"divider" => false,
					"options" => array(
						'thumbs' => esc_html__('Thumbs', 'wineshop'),
						'list' => esc_html__('List', 'wineshop')
					),
					"type" => "checklist"),
		
				"show_mode_buttons" => array(
					"title" => esc_html__('Show style buttons',  'wineshop'),
					"desc" => esc_html__("Show buttons to allow visitors change list style", 'wineshop'),
					"std" => "yes",
					"options" => $THEMEREX_GLOBALS['options_params']['list_yes_no'],
					"type" => "switch"),

				"shop_loop_columns" => array(
					"title" => esc_html__('Shop columns',  'wineshop'),
					"desc" => esc_html__("How many columns used to show products on shop page", 'wineshop'),
					"override" => "category,post,page",
					"std" => "3",
					"step" => 1,
					"min" => 1,
					"max" => 6,
					"type" => "spinner"),

				"show_currency" => array(
					"title" => esc_html__('Show currency selector', 'wineshop'),
					"desc" => esc_html__('Show currency selector in the user menu', 'wineshop'),
					"std" => "yes",
					"options" => $THEMEREX_GLOBALS['options_params']['list_yes_no'],
					"type" => "switch"),
		
				"show_cart" => array(
					"title" => esc_html__('Show cart button', 'wineshop'),
					"desc" => esc_html__('Show cart button in the user menu', 'wineshop'),
					"std" => "hide",
					"options" => array(
						'hide'   => esc_html__('Hide', 'wineshop'),
						'always' => esc_html__('Always', 'wineshop'),
						'shop'   => esc_html__('Only on shop pages', 'wineshop')
					),
					"type" => "checklist"),

				"crop_product_thumb" => array(
					"title" => esc_html__("Crop product's thumbnail",  'wineshop'),
					"desc" => esc_html__("Crop product's thumbnails on search results page or scale it", 'wineshop'),
					"std" => "no",
					"options" => $THEMEREX_GLOBALS['options_params']['list_yes_no'],
					"type" => "switch")
				
				)
			);

		}
	}
}

// WooCommerce hooks
if (!function_exists('themerex_woocommerce_theme_setup3')) {
	add_action( 'themerex_action_after_init_theme', 'themerex_woocommerce_theme_setup3' );
	function themerex_woocommerce_theme_setup3() {

		if (themerex_exists_woocommerce()) {
			add_action(    'woocommerce_before_subcategory_title',		'themerex_woocommerce_open_thumb_wrapper', 9 );
			add_action(    'woocommerce_before_shop_loop_item_title',	'themerex_woocommerce_open_thumb_wrapper', 9 );

			add_action(    'woocommerce_before_subcategory_title',		'themerex_woocommerce_open_item_wrapper', 20 );
			add_action(    'woocommerce_before_shop_loop_item_title',	'themerex_woocommerce_open_item_wrapper', 20 );

			add_action(    'woocommerce_after_subcategory',				'themerex_woocommerce_close_item_wrapper', 20 );
			add_action(    'woocommerce_after_shop_loop_item',			'themerex_woocommerce_close_item_wrapper', 20 );

			add_action(    'woocommerce_after_shop_loop_item_title',	'themerex_woocommerce_after_shop_loop_item_title', 7);

			add_action(    'woocommerce_after_subcategory_title',		'themerex_woocommerce_after_subcategory_title', 10 );

			add_action(    'the_title',									'themerex_woocommerce_the_title');

			// Wrap category title into link
			add_action(		'woocommerce_shop_loop_subcategory_title',  'themerex_woocommerce_shop_loop_subcategory_title', 9, 1);
            remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );
		}

		if (themerex_is_woocommerce_page()) {
			
			remove_action( 'woocommerce_sidebar', 						'woocommerce_get_sidebar', 10 );					// Remove WOOC sidebar
			
			remove_action( 'woocommerce_before_main_content',			'woocommerce_output_content_wrapper', 10);
			add_action(    'woocommerce_before_main_content',			'themerex_woocommerce_wrapper_start', 10);
			
			remove_action( 'woocommerce_after_main_content',			'woocommerce_output_content_wrapper_end', 10);		
			add_action(    'woocommerce_after_main_content',			'themerex_woocommerce_wrapper_end', 10);

			add_action(    'woocommerce_show_page_title',				'themerex_woocommerce_show_page_title', 10);

			remove_action( 'woocommerce_single_product_summary',		'woocommerce_template_single_title', 5);		
			add_action(    'woocommerce_single_product_summary',		'themerex_woocommerce_show_product_title', 5 );

			add_action(    'woocommerce_before_shop_loop', 				'themerex_woocommerce_before_shop_loop', 10 );

			add_action(    'woocommerce_product_meta_end',				'themerex_woocommerce_show_product_id', 10);

			
			if (themerex_param_is_on(themerex_get_custom_option('show_post_related'))) {
                add_filter('woocommerce_output_related_products_args', 'themerex_woocommerce_output_related_products_args');
                add_filter('woocommerce_related_products_args', 'themerex_woocommerce_related_products_args');
            } else {
                remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
            }
			
			add_filter(    'woocommerce_product_thumbnails_columns',	'themerex_woocommerce_product_thumbnails_columns' );

			add_filter(    'get_product_search_form',					'themerex_woocommerce_get_product_search_form' );

            // Set columns number for the products loop
            if ( ! get_theme_support( 'wc-product-grid-enable' ) ) {
                add_filter('post_class', 'themerex_woocommerce_loop_shop_columns_class');
                add_filter('product_cat_class', 'themerex_woocommerce_loop_shop_columns_class', 10, 3);
            }
			
			themerex_enqueue_popup();
		}
	}
}



// Check if WooCommerce installed and activated
if ( !function_exists( 'themerex_exists_woocommerce' ) ) {
	function themerex_exists_woocommerce() {
		return class_exists('Woocommerce');
	}
}

// Return true, if current page is any woocommerce page
if ( !function_exists( 'themerex_is_woocommerce_page' ) ) {
	function themerex_is_woocommerce_page() {
		$rez = false;
		if (themerex_exists_woocommerce()) {
			global $THEMEREX_GLOBALS;
			if (!empty($THEMEREX_GLOBALS['pre_query'])) {
				$id = isset($THEMEREX_GLOBALS['pre_query']->queried_object_id) ? $THEMEREX_GLOBALS['pre_query']->queried_object_id : 0;
				$rez = $THEMEREX_GLOBALS['pre_query']->get('post_type')=='product' 
						|| $id==wc_get_page_id('shop')
						|| $id==wc_get_page_id('cart')
						|| $id==wc_get_page_id('checkout')
						|| $id==wc_get_page_id('myaccount')
						|| $THEMEREX_GLOBALS['pre_query']->is_tax( 'product_cat' )
						|| $THEMEREX_GLOBALS['pre_query']->is_tax( 'product_tag' )
						|| $THEMEREX_GLOBALS['pre_query']->is_tax( get_object_taxonomies( 'product' ) );
						
			} else
				$rez = is_shop() || is_product() || is_product_category() || is_product_tag() || is_product_taxonomy() || is_cart() || is_checkout() || is_account_page();
		}
		return $rez;
	}
}

// Filter to detect current page inheritance key
if ( !function_exists( 'themerex_woocommerce_detect_inheritance_key' ) ) {
	function themerex_woocommerce_detect_inheritance_key($key) {
		if (!empty($key)) return $key;
		return themerex_is_woocommerce_page() ? 'woocommerce' : '';
	}
}

// Filter to detect current template page id
if ( !function_exists( 'themerex_woocommerce_detect_template_page_id' ) ) {
	function themerex_woocommerce_detect_template_page_id($id, $key) {
		if (!empty($id)) return $id;
		if ($key == 'woocommerce_cart')				$id = get_option('woocommerce_cart_page_id');
		else if ($key == 'woocommerce_checkout')	$id = get_option('woocommerce_checkout_page_id');
		else if ($key == 'woocommerce_account')		$id = get_option('woocommerce_account_page_id');
		else if ($key == 'woocommerce')				$id = get_option('woocommerce_shop_page_id');
		return $id;
	}
}

// Filter to detect current page type (slug)
if ( !function_exists( 'themerex_woocommerce_get_blog_type' ) ) {
	function themerex_woocommerce_get_blog_type($page, $query=null) {
		if (!empty($page)) return $page;
		
		if (is_shop()) 					$page = 'woocommerce_shop';
		else if ($query && $query->get('post_type')=='product' || is_product())		$page = 'woocommerce_product';
		else if ($query && $query->get('product_tag')!='' || is_product_tag())		$page = 'woocommerce_tag';
		else if ($query && $query->get('product_cat')!='' || is_product_category())	$page = 'woocommerce_category';
		else if (is_cart())				$page = 'woocommerce_cart';
		else if (is_checkout())			$page = 'woocommerce_checkout';
		else if (is_account_page())		$page = 'woocommerce_account';
		else if (is_woocommerce())		$page = 'woocommerce';
		return $page;
	}
}

// Filter to detect current page title
if ( !function_exists( 'themerex_woocommerce_get_blog_title' ) ) {
	function themerex_woocommerce_get_blog_title($title, $page) {
		if (!empty($title)) return $title;
		
		if ( themerex_strpos($page, 'woocommerce')!==false ) {
			if ( $page == 'woocommerce_category' ) {
				$term = get_term_by( 'slug', get_query_var( 'product_cat' ), 'product_cat', OBJECT);
				$title = $term->name;
			} else if ( $page == 'woocommerce_tag' ) {
				$term = get_term_by( 'slug', get_query_var( 'product_tag' ), 'product_tag', OBJECT);
				$title = esc_html__('Tag:', 'wineshop') . ' ' . esc_html($term->name);
			} else if ( $page == 'woocommerce_cart' ) {
				$title = esc_html__( 'Your cart', 'wineshop' );
			} else if ( $page == 'woocommerce_checkout' ) {
				$title = esc_html__( 'Checkout', 'wineshop' );
			} else if ( $page == 'woocommerce_account' ) {
				$title = esc_html__( 'Account', 'wineshop' );
			} else if ( $page == 'woocommerce_product' ) {
				$title = themerex_get_post_title();
			} else if (($page_id=get_option('woocommerce_shop_page_id')) > 0) {
				$title = themerex_get_post_title($page_id);
			} else {
				$title = esc_html__( 'Shop', 'wineshop' );
			}
		}
		
		return $title;
	}
}

// Filter to detect stream page title
if ( !function_exists( 'themerex_woocommerce_get_stream_page_title' ) ) {
	function themerex_woocommerce_get_stream_page_title($title, $page) {
		if (!empty($title)) return $title;
		if (themerex_strpos($page, 'woocommerce')!==false) {
			if (($page_id = themerex_woocommerce_get_stream_page_id(0, $page)) > 0)
				$title = themerex_get_post_title($page_id);
			else
				$title = esc_html__('Shop', 'wineshop');
		}
		return $title;
	}
}

// Filter to detect stream page ID
if ( !function_exists( 'themerex_woocommerce_get_stream_page_id' ) ) {
	function themerex_woocommerce_get_stream_page_id($id, $page) {
		if (!empty($id)) return $id;
		if (themerex_strpos($page, 'woocommerce')!==false) {
			$id = get_option('woocommerce_shop_page_id');
		}
		return $id;
	}
}

// Filter to detect stream page link
if ( !function_exists( 'themerex_woocommerce_get_stream_page_link' ) ) {
	function themerex_woocommerce_get_stream_page_link($url, $page) {
		if (!empty($url)) return $url;
		if (themerex_strpos($page, 'woocommerce')!==false) {
			$id = themerex_woocommerce_get_stream_page_id(0, $page);
			if ($id) $url = get_permalink($id);
		}
		return $url;
	}
}

// Filter to detect current taxonomy
if ( !function_exists( 'themerex_woocommerce_get_current_taxonomy' ) ) {
	function themerex_woocommerce_get_current_taxonomy($tax, $page) {
		if (!empty($tax)) return $tax;
		if ( themerex_strpos($page, 'woocommerce')!==false ) {
			$tax = 'product_cat';
		}
		return $tax;
	}
}

// Return taxonomy name (slug) if current page is this taxonomy page
if ( !function_exists( 'themerex_woocommerce_is_taxonomy' ) ) {
	function themerex_woocommerce_is_taxonomy($tax, $query=null) {
		if (!empty($tax))
			return $tax;
		else 
			return $query && $query->get('product_cat')!='' || is_product_category() ? 'product_cat' : '';
	}
}

// Return false if current plugin not need theme orderby setting
if ( !function_exists( 'themerex_woocommerce_orderby_need' ) ) {
	function themerex_woocommerce_orderby_need($need, $query=null) {
		if ($need == false)
			return $need;
		else
			return $query && !($query->get('post_type')=='product' || $query->get('product_cat')!='' || $query->get('product_tag')!='');
	}
}

// Add custom post type into list
if ( !function_exists( 'themerex_woocommerce_list_post_types' ) ) {
	function themerex_woocommerce_list_post_types($list) {
		$list['product'] = esc_html__('Products', 'wineshop');
		return $list;
	}
}


	
// Enqueue WooCommerce custom styles
if ( !function_exists( 'themerex_woocommerce_frontend_scripts' ) ) {
	function themerex_woocommerce_frontend_scripts() {

        wp_enqueue_style( 'themerex-woo-style',  themerex_get_file_url('css/woo-style.css'), array(), null );
	}
}

// Before main content
if ( !function_exists( 'themerex_woocommerce_wrapper_start' ) ) {
	function themerex_woocommerce_wrapper_start() {
		global $THEMEREX_GLOBALS;
		if (is_product() || is_cart() || is_checkout() || is_account_page()) {
			?>
			<article class="post_item post_item_single post_item_product">
			<?php
		} else {
			?>
			<div class="list_products shop_mode_<?php echo !empty($THEMEREX_GLOBALS['shop_mode']) ? $THEMEREX_GLOBALS['shop_mode'] : 'thumbs'; ?>">
			<?php
		}
	}
}

// After main content
if ( !function_exists( 'themerex_woocommerce_wrapper_end' ) ) {
	function themerex_woocommerce_wrapper_end() {
		if (is_product() || is_cart() || is_checkout() || is_account_page()) {
			?>
			</article>	<!-- .post_item -->
			<?php
		} else {
			?>
			</div>	<!-- .list_products -->
			<?php
		}
	}
}

// Check to show page title
if ( !function_exists( 'themerex_woocommerce_show_page_title' ) ) {
	function themerex_woocommerce_show_page_title($defa=true) {
		return themerex_get_custom_option('show_page_title')=='no';
	}
}

// Check to show product title
if ( !function_exists( 'themerex_woocommerce_show_product_title' ) ) {
	function themerex_woocommerce_show_product_title() {
		if (themerex_get_custom_option('show_post_title')=='yes' || themerex_get_custom_option('show_page_title')=='no') {
			wc_get_template( 'single-product/title.php' );
		}
	}
}

// Add list mode buttons
if ( !function_exists( 'themerex_woocommerce_before_shop_loop' ) ) {
	function themerex_woocommerce_before_shop_loop() {
		global $THEMEREX_GLOBALS;
		if (themerex_get_custom_option('show_mode_buttons')=='yes') {
            echo '<div class="mode_buttons"><form action="' . esc_url(themerex_get_current_url()) . '" method="post">'
				. '<input type="hidden" name="themerex_shop_mode" value="'.esc_attr($THEMEREX_GLOBALS['shop_mode']).'" />'
				. '<a href="#" class="woocommerce_thumbs icon-th" title="'.esc_attr__('Show products as thumbs', 'wineshop').'"></a>'
				. '<a href="#" class="woocommerce_list icon-th-list" title="'.esc_attr__('Show products as list', 'wineshop').'"></a>'
				. '</form></div>';
		}
	}
}


// Open thumbs wrapper for categories and products
if ( !function_exists( 'themerex_woocommerce_open_thumb_wrapper' ) ) {
	function themerex_woocommerce_open_thumb_wrapper($cat='') {
		themerex_set_global('in_product_item', true);
		?>
		<div class="post_item_wrap">
			<div class="post_featured">
				<div class="post_thumb">
					<a class="hover_icon hover_icon_link" href="<?php echo esc_url(is_object($cat) ? get_term_link($cat->slug, 'product_cat') : get_permalink()); ?>"><span class="view_button"><?php esc_html_e('view', 'wineshop') ?></span>
		<?php
	}
}

// Open item wrapper for categories and products
if ( !function_exists( 'themerex_woocommerce_open_item_wrapper' ) ) {
	function themerex_woocommerce_open_item_wrapper($cat='') {
		?>
				</a>
			</div>
		</div>
		<div class="post_content">
		<?php
	}
}

// Close item wrapper for categories and products
if ( !function_exists( 'themerex_woocommerce_close_item_wrapper' ) ) {
	function themerex_woocommerce_close_item_wrapper($cat='') {
		?>
			</div>
		</div>
		<?php
		themerex_set_global('in_product_item', false);
	}
}

// Add excerpt in output for the product in the list mode
if ( !function_exists( 'themerex_woocommerce_after_shop_loop_item_title' ) ) {
	function themerex_woocommerce_after_shop_loop_item_title() {
		global $THEMEREX_GLOBALS;
		if ($THEMEREX_GLOBALS['shop_mode'] == 'list') {
		    $excerpt = apply_filters('the_excerpt', get_the_excerpt());
			echo '<div class="description">'.trim($excerpt).'</div>';
		}
	}
}

// Add excerpt in output for the product in the list mode
if ( !function_exists( 'themerex_woocommerce_after_subcategory_title' ) ) {
	function themerex_woocommerce_after_subcategory_title($category) {
		global $THEMEREX_GLOBALS;
		if ($THEMEREX_GLOBALS['shop_mode'] == 'list')
			echo '<div class="description">' . trim($category->description) . '</div>';
	}
}

// Add Product ID for single product
if ( !function_exists( 'themerex_woocommerce_show_product_id' ) ) {
	function themerex_woocommerce_show_product_id() {
		global $post, $product;
		echo '<span class="product_id">'.esc_html__('Product ID: ', 'wineshop') . '<span>' . ($post->ID) . '</span></span>';
	}
}

// Redefine number of related products
if ( !function_exists( 'themerex_woocommerce_output_related_products_args' ) ) {
	function themerex_woocommerce_output_related_products_args($args) {
		$ppp = $ccc = 0;
		if (themerex_param_is_on(themerex_get_custom_option('show_post_related'))) {
			$ccc_add = in_array(themerex_get_custom_option('body_style'), array('fullwide', 'fullscreen')) ? 1 : 0;
			$ccc =  themerex_get_custom_option('post_related_columns');
			$ccc = $ccc > 0 ? $ccc : (themerex_param_is_off(themerex_get_custom_option('show_sidebar_main')) ? 3+$ccc_add : 2+$ccc_add);
			$ppp = themerex_get_custom_option('post_related_count');
			$ppp = $ppp > 0 ? $ppp : $ccc;
		}
		$args['posts_per_page'] = $ppp;
		$args['columns'] = $ccc;
		return $args;
	}
}

// Redefine post_type if number of related products == 0
if ( !function_exists( 'themerex_woocommerce_related_products_args' ) ) {
	function themerex_woocommerce_related_products_args($args) {
		if ($args['posts_per_page'] == 0)
			$args['post_type'] .= '_';
		return $args;
	}
}

// Number columns for product thumbnails
if ( !function_exists( 'themerex_woocommerce_product_thumbnails_columns' ) ) {
	function themerex_woocommerce_product_thumbnails_columns($cols) {
		return 5;
	}
}

// Add column class into product item in shop streampage
function themerex_woocommerce_loop_shop_columns_class($class, $class2='', $cat='') {
    if (!is_product() && !is_cart() && !is_checkout() && !is_account_page()) {
        $cols = function_exists('wc_get_default_products_per_row') ? wc_get_default_products_per_row() : 2;
        $class[] = ' column-1_' . $cols;
    }
    return $class;
}


// Search form
if ( !function_exists( 'themerex_woocommerce_get_product_search_form' ) ) {
	function themerex_woocommerce_get_product_search_form($form) {
		return '
		<form method="get" class="search_form" action="' . esc_url( home_url( '/'  ) ) . '">
			<input type="text" class="search_field" placeholder="' . esc_attr__('Search for products &hellip;', 'wineshop') . '" value="' . get_search_query() . '" name="s" title="' . esc_attr__('Search for products:', 'wineshop') . '" /><button class="search_button icon-search" type="submit"></button>
			<input type="hidden" name="post_type" value="product" />
		</form>
		';
	}
}

// Wrap product title into link
if ( !function_exists( 'themerex_woocommerce_the_title' ) ) {
	function themerex_woocommerce_the_title($title) {
		if (themerex_get_global('in_product_item') && get_post_type()=='product') {
			$title = '<a href="'.esc_url(get_permalink()).'">'.($title).'</a>';
		}
		return $title;
	}
}

// Wrap category title into link
if ( !function_exists( 'themerex_woocommerce_shop_loop_subcategory_title' ) ) {
    function themerex_woocommerce_shop_loop_subcategory_title($cat) {

        $cat->name = sprintf('<a href="%s">%s</a>', esc_url(get_term_link($cat->slug, 'product_cat')), $cat->name);
        ?>
        <h2 class="woocommerce-loop-category__title">
        <?php
        themerex_show_layout($cat->name);

        if ( $cat->count > 0 ) {
            echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">(' . esc_html( $cat->count ) . ')</mark>', $cat ); // WPCS: XSS ok.
        }
        ?>
        </h2><?php
    }
}

// Replace H2 tag to H3 tag in product headings
if ( !function_exists( 'tennisclub_woocommerce_template_loop_product_title' ) ) {
    function tennisclub_woocommerce_template_loop_product_title() {
        echo '<h3>' . wp_kses_post( get_the_title() ) . '</h3>';
    }
}

// Show pagination links
if ( !function_exists( 'themerex_woocommerce_pagination' ) ) {
	function themerex_woocommerce_pagination() {
		$style = themerex_get_custom_option('blog_pagination');
		themerex_show_pagination(array(
			'class' => 'pagination_wrap pagination_' . esc_attr($style),
			'style' => $style,
			'button_class' => '',
			'first_text'=> '',
			'last_text' => '',
			'prev_text' => '',
			'next_text' => '',
			'pages_in_group' => $style=='pages' ? 10 : 20
			)
		);
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'themerex_woocommerce_required_plugins' ) ) {
	function themerex_woocommerce_required_plugins($list=array()) {
		if (in_array('woocommerce', (array)themerex_storage_get('required_plugins')))
			$list[] = array(
					'name' 		=> 'WooCommerce',
					'slug' 		=> 'woocommerce',
					'required' 	=> false
				);

		return $list;
	}
}

// Show products navigation
if ( !function_exists( 'themerex_woocommerce_show_post_navi' ) ) {
	function themerex_woocommerce_show_post_navi($show=false) {
		return $show || (themerex_get_custom_option('show_page_title')=='yes' && is_single() && themerex_is_woocommerce_page());
	}
}


// Register shortcodes to the internal builder
//------------------------------------------------------------------------
if ( !function_exists( 'themerex_woocommerce_reg_shortcodes' ) ) {
	function themerex_woocommerce_reg_shortcodes() {
		global $THEMEREX_GLOBALS;

		// WooCommerce - Cart
		$THEMEREX_GLOBALS['shortcodes']["woocommerce_cart"] = array(
			"title" => esc_html__("Woocommerce: Cart", "wineshop"),
			"desc" => wp_kses( __("WooCommerce shortcode: show Cart page", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array()
		);
		
		// WooCommerce - Checkout
		$THEMEREX_GLOBALS['shortcodes']["woocommerce_checkout"] = array(
			"title" => esc_html__("Woocommerce: Checkout", "wineshop"),
			"desc" => wp_kses( __("WooCommerce shortcode: show Checkout page", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array()
		);
		
		// WooCommerce - My Account
		$THEMEREX_GLOBALS['shortcodes']["woocommerce_my_account"] = array(
			"title" => esc_html__("Woocommerce: My Account", "wineshop"),
			"desc" => wp_kses( __("WooCommerce shortcode: show My Account page", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array()
		);
		
		// WooCommerce - Order Tracking
		$THEMEREX_GLOBALS['shortcodes']["woocommerce_order_tracking"] = array(
			"title" => esc_html__("Woocommerce: Order Tracking", "wineshop"),
			"desc" => wp_kses( __("WooCommerce shortcode: show Order Tracking page", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array()
		);
		
		// WooCommerce - Shop Messages
		$THEMEREX_GLOBALS['shortcodes']["shop_messages"] = array(
			"title" => esc_html__("Woocommerce: Shop Messages", "wineshop"),
			"desc" => wp_kses( __("WooCommerce shortcode: show shop messages", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array()
		);
		
		// WooCommerce - Product Page
		$THEMEREX_GLOBALS['shortcodes']["product_page"] = array(
			"title" => esc_html__("Woocommerce: Product Page", "wineshop"),
			"desc" => wp_kses( __("WooCommerce shortcode: display single product page", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"sku" => array(
					"title" => esc_html__("SKU", "wineshop"),
					"desc" => wp_kses( __("SKU code of displayed product", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"id" => array(
					"title" => esc_html__("ID", "wineshop"),
					"desc" => wp_kses( __("ID of displayed product", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"posts_per_page" => array(
					"title" => esc_html__("Number", "wineshop"),
					"desc" => wp_kses( __("How many products showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "1",
					"min" => 1,
					"type" => "spinner"
				),
				"post_type" => array(
					"title" => esc_html__("Post type", "wineshop"),
					"desc" => wp_kses( __("Post type for the WP query (leave 'product')", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "product",
					"type" => "text"
				),
				"post_status" => array(
					"title" => esc_html__("Post status", "wineshop"),
					"desc" => wp_kses( __("Display posts only with this status", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "publish",
					"type" => "select",
					"options" => array(
						"publish" => esc_html__('Publish', 'wineshop'),
						"protected" => esc_html__('Protected', 'wineshop'),
						"private" => esc_html__('Private', 'wineshop'),
						"pending" => esc_html__('Pending', 'wineshop'),
						"draft" => esc_html__('Draft', 'wineshop')
					)
				)
			)
		);
		
		// WooCommerce - Product
		$THEMEREX_GLOBALS['shortcodes']["product"] = array(
			"title" => esc_html__("Woocommerce: Product", "wineshop"),
			"desc" => wp_kses( __("WooCommerce shortcode: display one product", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"sku" => array(
					"title" => esc_html__("SKU", "wineshop"),
					"desc" => wp_kses( __("SKU code of displayed product", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"id" => array(
					"title" => esc_html__("ID", "wineshop"),
					"desc" => wp_kses( __("ID of displayed product", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				)
			)
		);
		
		// WooCommerce - Best Selling Products
		$THEMEREX_GLOBALS['shortcodes']["best_selling_products"] = array(
			"title" => esc_html__("Woocommerce: Best Selling Products", "wineshop"),
			"desc" => wp_kses( __("WooCommerce shortcode: show best selling products", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"per_page" => array(
					"title" => esc_html__("Number", "wineshop"),
					"desc" => wp_kses( __("How many products showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 1,
					"type" => "spinner"
				),
				"columns" => array(
					"title" => esc_html__("Columns", "wineshop"),
					"desc" => wp_kses( __("How many columns per row use for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 2,
					"max" => 4,
					"type" => "spinner"
				)
			)
		);
		
		// WooCommerce - Recent Products
		$THEMEREX_GLOBALS['shortcodes']["recent_products"] = array(
			"title" => esc_html__("Woocommerce: Recent Products", "wineshop"),
			"desc" => wp_kses( __("WooCommerce shortcode: show recent products", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"per_page" => array(
					"title" => esc_html__("Number", "wineshop"),
					"desc" => wp_kses( __("How many products showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 1,
					"type" => "spinner"
				),
				"columns" => array(
					"title" => esc_html__("Columns", "wineshop"),
					"desc" => wp_kses( __("How many columns per row use for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 2,
					"max" => 4,
					"type" => "spinner"
				),
				"orderby" => array(
					"title" => esc_html__("Order by", "wineshop"),
					"desc" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "date",
					"type" => "select",
					"options" => array(
						"date" => esc_html__('Date', 'wineshop'),
						"title" => esc_html__('Title', 'wineshop')
					)
				),
				"order" => array(
					"title" => esc_html__("Order", "wineshop"),
					"desc" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "desc",
					"type" => "switch",
					"size" => "big",
					"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
				)
			)
		);
		
		// WooCommerce - Related Products
		$THEMEREX_GLOBALS['shortcodes']["related_products"] = array(
			"title" => esc_html__("Woocommerce: Related Products", "wineshop"),
			"desc" => wp_kses( __("WooCommerce shortcode: show related products", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"posts_per_page" => array(
					"title" => esc_html__("Number", "wineshop"),
					"desc" => wp_kses( __("How many products showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 1,
					"type" => "spinner"
				),
				"columns" => array(
					"title" => esc_html__("Columns", "wineshop"),
					"desc" => wp_kses( __("How many columns per row use for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 2,
					"max" => 4,
					"type" => "spinner"
				),
				"orderby" => array(
					"title" => esc_html__("Order by", "wineshop"),
					"desc" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "date",
					"type" => "select",
					"options" => array(
						"date" => esc_html__('Date', 'wineshop'),
						"title" => esc_html__('Title', 'wineshop')
					)
				)
			)
		);
		
		// WooCommerce - Featured Products
		$THEMEREX_GLOBALS['shortcodes']["featured_products"] = array(
			"title" => esc_html__("Woocommerce: Featured Products", "wineshop"),
			"desc" => wp_kses( __("WooCommerce shortcode: show featured products", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"per_page" => array(
					"title" => esc_html__("Number", "wineshop"),
					"desc" => wp_kses( __("How many products showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 1,
					"type" => "spinner"
				),
				"columns" => array(
					"title" => esc_html__("Columns", "wineshop"),
					"desc" => wp_kses( __("How many columns per row use for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 2,
					"max" => 4,
					"type" => "spinner"
				),
				"orderby" => array(
					"title" => esc_html__("Order by", "wineshop"),
					"desc" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "date",
					"type" => "select",
					"options" => array(
						"date" => esc_html__('Date', 'wineshop'),
						"title" => esc_html__('Title', 'wineshop')
					)
				),
				"order" => array(
					"title" => esc_html__("Order", "wineshop"),
					"desc" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "desc",
					"type" => "switch",
					"size" => "big",
					"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
				)
			)
		);
		
		// WooCommerce - Top Rated Products
		$THEMEREX_GLOBALS['shortcodes']["featured_products"] = array(
			"title" => esc_html__("Woocommerce: Top Rated Products", "wineshop"),
			"desc" => wp_kses( __("WooCommerce shortcode: show top rated products", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"per_page" => array(
					"title" => esc_html__("Number", "wineshop"),
					"desc" => wp_kses( __("How many products showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 1,
					"type" => "spinner"
				),
				"columns" => array(
					"title" => esc_html__("Columns", "wineshop"),
					"desc" => wp_kses( __("How many columns per row use for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 2,
					"max" => 4,
					"type" => "spinner"
				),
				"orderby" => array(
					"title" => esc_html__("Order by", "wineshop"),
					"desc" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "date",
					"type" => "select",
					"options" => array(
						"date" => esc_html__('Date', 'wineshop'),
						"title" => esc_html__('Title', 'wineshop')
					)
				),
				"order" => array(
					"title" => esc_html__("Order", "wineshop"),
					"desc" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "desc",
					"type" => "switch",
					"size" => "big",
					"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
				)
			)
		);
		
		// WooCommerce - Sale Products
		$THEMEREX_GLOBALS['shortcodes']["featured_products"] = array(
			"title" => esc_html__("Woocommerce: Sale Products", "wineshop"),
			"desc" => wp_kses( __("WooCommerce shortcode: list products on sale", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"per_page" => array(
					"title" => esc_html__("Number", "wineshop"),
					"desc" => wp_kses( __("How many products showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 1,
					"type" => "spinner"
				),
				"columns" => array(
					"title" => esc_html__("Columns", "wineshop"),
					"desc" => wp_kses( __("How many columns per row use for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 2,
					"max" => 4,
					"type" => "spinner"
				),
				"orderby" => array(
					"title" => esc_html__("Order by", "wineshop"),
					"desc" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "date",
					"type" => "select",
					"options" => array(
						"date" => esc_html__('Date', 'wineshop'),
						"title" => esc_html__('Title', 'wineshop')
					)
				),
				"order" => array(
					"title" => esc_html__("Order", "wineshop"),
					"desc" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "desc",
					"type" => "switch",
					"size" => "big",
					"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
				)
			)
		);
		
		// WooCommerce - Product Category
		$THEMEREX_GLOBALS['shortcodes']["product_category"] = array(
			"title" => esc_html__("Woocommerce: Products from category", "wineshop"),
			"desc" => wp_kses( __("WooCommerce shortcode: list products in specified category(-ies)", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"per_page" => array(
					"title" => esc_html__("Number", "wineshop"),
					"desc" => wp_kses( __("How many products showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 1,
					"type" => "spinner"
				),
				"columns" => array(
					"title" => esc_html__("Columns", "wineshop"),
					"desc" => wp_kses( __("How many columns per row use for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 2,
					"max" => 4,
					"type" => "spinner"
				),
				"orderby" => array(
					"title" => esc_html__("Order by", "wineshop"),
					"desc" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "date",
					"type" => "select",
					"options" => array(
						"date" => esc_html__('Date', 'wineshop'),
						"title" => esc_html__('Title', 'wineshop')
					)
				),
				"order" => array(
					"title" => esc_html__("Order", "wineshop"),
					"desc" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "desc",
					"type" => "switch",
					"size" => "big",
					"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
				),
				"category" => array(
					"title" => esc_html__("Categories", "wineshop"),
					"desc" => wp_kses( __("Comma separated category slugs", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => '',
					"type" => "text"
				),
				"operator" => array(
					"title" => esc_html__("Operator", "wineshop"),
					"desc" => wp_kses( __("Categories operator", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "IN",
					"type" => "checklist",
					"size" => "medium",
					"options" => array(
						"IN" => esc_html__('IN', 'wineshop'),
						"NOT IN" => esc_html__('NOT IN', 'wineshop'),
						"AND" => esc_html__('AND', 'wineshop')
					)
				)
			)
		);
		
		// WooCommerce - Products
		$THEMEREX_GLOBALS['shortcodes']["products"] = array(
			"title" => esc_html__("Woocommerce: Products", "wineshop"),
			"desc" => wp_kses( __("WooCommerce shortcode: list all products", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"skus" => array(
					"title" => esc_html__("SKUs", "wineshop"),
					"desc" => wp_kses( __("Comma separated SKU codes of products", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"ids" => array(
					"title" => esc_html__("IDs", "wineshop"),
					"desc" => wp_kses( __("Comma separated ID of products", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"columns" => array(
					"title" => esc_html__("Columns", "wineshop"),
					"desc" => wp_kses( __("How many columns per row use for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 2,
					"max" => 4,
					"type" => "spinner"
				),
				"orderby" => array(
					"title" => esc_html__("Order by", "wineshop"),
					"desc" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "date",
					"type" => "select",
					"options" => array(
						"date" => esc_html__('Date', 'wineshop'),
						"title" => esc_html__('Title', 'wineshop')
					)
				),
				"order" => array(
					"title" => esc_html__("Order", "wineshop"),
					"desc" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "desc",
					"type" => "switch",
					"size" => "big",
					"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
				)
			)
		);
		
		// WooCommerce - Product attribute
		$THEMEREX_GLOBALS['shortcodes']["product_attribute"] = array(
			"title" => esc_html__("Woocommerce: Products by Attribute", "wineshop"),
			"desc" => wp_kses( __("WooCommerce shortcode: show products with specified attribute", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"per_page" => array(
					"title" => esc_html__("Number", "wineshop"),
					"desc" => wp_kses( __("How many products showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 1,
					"type" => "spinner"
				),
				"columns" => array(
					"title" => esc_html__("Columns", "wineshop"),
					"desc" => wp_kses( __("How many columns per row use for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 2,
					"max" => 4,
					"type" => "spinner"
				),
				"orderby" => array(
					"title" => esc_html__("Order by", "wineshop"),
					"desc" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "date",
					"type" => "select",
					"options" => array(
						"date" => esc_html__('Date', 'wineshop'),
						"title" => esc_html__('Title', 'wineshop')
					)
				),
				"order" => array(
					"title" => esc_html__("Order", "wineshop"),
					"desc" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "desc",
					"type" => "switch",
					"size" => "big",
					"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
				),
				"attribute" => array(
					"title" => esc_html__("Attribute", "wineshop"),
					"desc" => wp_kses( __("Attribute name", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"filter" => array(
					"title" => esc_html__("Filter", "wineshop"),
					"desc" => wp_kses( __("Attribute value", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				)
			)
		);
		
		// WooCommerce - Products Categories
		$THEMEREX_GLOBALS['shortcodes']["product_categories"] = array(
			"title" => esc_html__("Woocommerce: Product Categories", "wineshop"),
			"desc" => wp_kses( __("WooCommerce shortcode: show categories with products", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"number" => array(
					"title" => esc_html__("Number", "wineshop"),
					"desc" => wp_kses( __("How many categories showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 1,
					"type" => "spinner"
				),
				"columns" => array(
					"title" => esc_html__("Columns", "wineshop"),
					"desc" => wp_kses( __("How many columns per row use for categories output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => 4,
					"min" => 2,
					"max" => 4,
					"type" => "spinner"
				),
				"orderby" => array(
					"title" => esc_html__("Order by", "wineshop"),
					"desc" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "date",
					"type" => "select",
					"options" => array(
						"date" => esc_html__('Date', 'wineshop'),
						"title" => esc_html__('Title', 'wineshop')
					)
				),
				"order" => array(
					"title" => esc_html__("Order", "wineshop"),
					"desc" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "desc",
					"type" => "switch",
					"size" => "big",
					"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
				),
				"parent" => array(
					"title" => esc_html__("Parent", "wineshop"),
					"desc" => wp_kses( __("Parent category slug", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"ids" => array(
					"title" => esc_html__("IDs", "wineshop"),
					"desc" => wp_kses( __("Comma separated ID of products", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"hide_empty" => array(
					"title" => esc_html__("Hide empty", "wineshop"),
					"desc" => wp_kses( __("Hide empty categories", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "yes",
					"type" => "switch",
					"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
				)
			)
		);
	}
}



// Add shortcodes to the VC builder
//------------------------------------------------------------------------
if ( !function_exists( 'themerex_woocommerce_reg_shortcodes_vc' ) ) {
	function themerex_woocommerce_reg_shortcodes_vc() {
		global $THEMEREX_GLOBALS;
	
		if (false && function_exists('themerex_exists_woocommerce') && themerex_exists_woocommerce()) {
		
			// WooCommerce - Cart
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "woocommerce_cart",
				"name" => esc_html__("Cart", "wineshop"),
				"description" => wp_kses( __("WooCommerce shortcode: show cart page", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
				"category" => esc_html__('WooCommerce', 'wineshop'),
				'icon' => 'icon_trx_wooc_cart',
				"class" => "trx_sc_alone trx_sc_woocommerce_cart",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => false,
				"params" => array(
					array(
						"param_name" => "dummy",
						"heading" => esc_html__("Dummy data", "wineshop"),
						"description" => wp_kses( __("Dummy data - not used in shortcodes", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					)
				)
			) );
			
			class WPBakeryShortCode_Woocommerce_Cart extends Themerex_VC_ShortCodeAlone {}
		
		
			// WooCommerce - Checkout
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "woocommerce_checkout",
				"name" => esc_html__("Checkout", "wineshop"),
				"description" => wp_kses( __("WooCommerce shortcode: show checkout page", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
				"category" => esc_html__('WooCommerce', 'wineshop'),
				'icon' => 'icon_trx_wooc_checkout',
				"class" => "trx_sc_alone trx_sc_woocommerce_checkout",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => false,
				"params" => array(
					array(
						"param_name" => "dummy",
						"heading" => esc_html__("Dummy data", "wineshop"),
						"description" => wp_kses( __("Dummy data - not used in shortcodes", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					)
				)
			) );
			
			class WPBakeryShortCode_Woocommerce_Checkout extends Themerex_VC_ShortCodeAlone {}
		
		
			// WooCommerce - My Account
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "woocommerce_my_account",
				"name" => esc_html__("My Account", "wineshop"),
				"description" => wp_kses( __("WooCommerce shortcode: show my account page", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
				"category" => esc_html__('WooCommerce', 'wineshop'),
				'icon' => 'icon_trx_wooc_my_account',
				"class" => "trx_sc_alone trx_sc_woocommerce_my_account",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => false,
				"params" => array(
					array(
						"param_name" => "dummy",
						"heading" => esc_html__("Dummy data", "wineshop"),
						"description" => wp_kses( __("Dummy data - not used in shortcodes", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					)
				)
			) );
			
			class WPBakeryShortCode_Woocommerce_My_Account extends Themerex_VC_ShortCodeAlone {}
		
		
			// WooCommerce - Order Tracking
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "woocommerce_order_tracking",
				"name" => esc_html__("Order Tracking", "wineshop"),
				"description" => wp_kses( __("WooCommerce shortcode: show order tracking page", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
				"category" => esc_html__('WooCommerce', 'wineshop'),
				'icon' => 'icon_trx_wooc_order_tracking',
				"class" => "trx_sc_alone trx_sc_woocommerce_order_tracking",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => false,
				"params" => array(
					array(
						"param_name" => "dummy",
						"heading" => esc_html__("Dummy data", "wineshop"),
						"description" => wp_kses( __("Dummy data - not used in shortcodes", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					)
				)
			) );
			
			class WPBakeryShortCode_Woocommerce_Order_Tracking extends Themerex_VC_ShortCodeAlone {}
		
		
			// WooCommerce - Shop Messages
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "shop_messages",
				"name" => esc_html__("Shop Messages", "wineshop"),
				"description" => wp_kses( __("WooCommerce shortcode: show shop messages", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
				"category" => esc_html__('WooCommerce', 'wineshop'),
				'icon' => 'icon_trx_wooc_shop_messages',
				"class" => "trx_sc_alone trx_sc_shop_messages",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => false,
				"params" => array(
					array(
						"param_name" => "dummy",
						"heading" => esc_html__("Dummy data", "wineshop"),
						"description" => wp_kses( __("Dummy data - not used in shortcodes", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					)
				)
			) );
			
			class WPBakeryShortCode_Shop_Messages extends Themerex_VC_ShortCodeAlone {}
		
		
			// WooCommerce - Product Page
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "product_page",
				"name" => esc_html__("Product Page", "wineshop"),
				"description" => wp_kses( __("WooCommerce shortcode: display single product page", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
				"category" => esc_html__('WooCommerce', 'wineshop'),
				'icon' => 'icon_trx_product_page',
				"class" => "trx_sc_single trx_sc_product_page",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "sku",
						"heading" => esc_html__("SKU", "wineshop"),
						"description" => wp_kses( __("SKU code of displayed product", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "id",
						"heading" => esc_html__("ID", "wineshop"),
						"description" => wp_kses( __("ID of displayed product", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "posts_per_page",
						"heading" => esc_html__("Number", "wineshop"),
						"description" => wp_kses( __("How many products showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "1",
						"type" => "textfield"
					),
					array(
						"param_name" => "post_type",
						"heading" => esc_html__("Post type", "wineshop"),
						"description" => wp_kses( __("Post type for the WP query (leave 'product')", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"class" => "",
						"value" => "product",
						"type" => "textfield"
					),
					array(
						"param_name" => "post_status",
						"heading" => esc_html__("Post status", "wineshop"),
						"description" => wp_kses( __("Display posts only with this status", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"class" => "",
						"value" => array(
							esc_html__('Publish', 'wineshop') => 'publish',
							esc_html__('Protected', 'wineshop') => 'protected',
							esc_html__('Private', 'wineshop') => 'private',
							esc_html__('Pending', 'wineshop') => 'pending',
							esc_html__('Draft', 'wineshop') => 'draft'
						),
						"type" => "dropdown"
					)
				)
			) );
			
			class WPBakeryShortCode_Product_Page extends Themerex_VC_ShortCodeSingle {}
		
		
		
			// WooCommerce - Product
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "product",
				"name" => esc_html__("Product", "wineshop"),
				"description" => wp_kses( __("WooCommerce shortcode: display one product", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
				"category" => esc_html__('WooCommerce', 'wineshop'),
				'icon' => 'icon_trx_product',
				"class" => "trx_sc_single trx_sc_product",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "sku",
						"heading" => esc_html__("SKU", "wineshop"),
						"description" => wp_kses( __("Product's SKU code", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "id",
						"heading" => esc_html__("ID", "wineshop"),
						"description" => wp_kses( __("Product's ID", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					)
				)
			) );
			
			class WPBakeryShortCode_Product extends Themerex_VC_ShortCodeSingle {}
		
		
			// WooCommerce - Best Selling Products
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "best_selling_products",
				"name" => esc_html__("Best Selling Products", "wineshop"),
				"description" => wp_kses( __("WooCommerce shortcode: show best selling products", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
				"category" => esc_html__('WooCommerce', 'wineshop'),
				'icon' => 'icon_trx_best_selling_products',
				"class" => "trx_sc_single trx_sc_best_selling_products",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "per_page",
						"heading" => esc_html__("Number", "wineshop"),
						"description" => wp_kses( __("How many products showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "4",
						"type" => "textfield"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", "wineshop"),
						"description" => wp_kses( __("How many columns per row use for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "1",
						"type" => "textfield"
					)
				)
			) );
			
			class WPBakeryShortCode_Best_Selling_Products extends Themerex_VC_ShortCodeSingle {}
		
		
		
			// WooCommerce - Recent Products
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "recent_products",
				"name" => esc_html__("Recent Products", "wineshop"),
				"description" => wp_kses_data( __("WooCommerce shortcode: show recent products", "wineshop") ),
				"category" => esc_html__('WooCommerce', 'wineshop'),
				'icon' => 'icon_trx_recent_products',
				"class" => "trx_sc_single trx_sc_recent_products",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "per_page",
						"heading" => esc_html__("Number", "wineshop"),
						"description" => wp_kses( __("How many products showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "4",
						"type" => "textfield"

					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", "wineshop"),
						"description" => wp_kses( __("How many columns per row use for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "1",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Order by", "wineshop"),
						"description" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							esc_html__('Date', 'wineshop') => 'date',
							esc_html__('Title', 'wineshop') => 'title'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => esc_html__("Order", "wineshop"),
						"description" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
						"type" => "dropdown"
					)
				)
			) );
			
			class WPBakeryShortCode_Recent_Products extends Themerex_VC_ShortCodeSingle {}
		
		
		
			// WooCommerce - Related Products
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "related_products",
				"name" => esc_html__("Related Products", "wineshop"),
				"description" => wp_kses( __("WooCommerce shortcode: show related products", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
				"category" => esc_html__('WooCommerce', 'wineshop'),
				'icon' => 'icon_trx_related_products',
				"class" => "trx_sc_single trx_sc_related_products",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "posts_per_page",
						"heading" => esc_html__("Number", "wineshop"),
						"description" => wp_kses( __("How many products showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "4",
						"type" => "textfield"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", "wineshop"),
						"description" => wp_kses( __("How many columns per row use for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "1",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Order by", "wineshop"),
						"description" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							esc_html__('Date', 'wineshop') => 'date',
							esc_html__('Title', 'wineshop') => 'title'
						),
						"type" => "dropdown"
					)
				)
			) );
			
			class WPBakeryShortCode_Related_Products extends Themerex_VC_ShortCodeSingle {}
		
		
		
			// WooCommerce - Featured Products
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "featured_products",
				"name" => esc_html__("Featured Products", "wineshop"),
				"description" => wp_kses( __("WooCommerce shortcode: show featured products", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
				"category" => esc_html__('WooCommerce', 'wineshop'),
				'icon' => 'icon_trx_featured_products',
				"class" => "trx_sc_single trx_sc_featured_products",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "per_page",
						"heading" => esc_html__("Number", "wineshop"),
						"description" => wp_kses( __("How many products showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "4",
						"type" => "textfield"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", "wineshop"),
						"description" => wp_kses( __("How many columns per row use for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "1",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Order by", "wineshop"),
						"description" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							esc_html__('Date', 'wineshop') => 'date',
							esc_html__('Title', 'wineshop') => 'title'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => esc_html__("Order", "wineshop"),
						"description" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
						"type" => "dropdown"
					)
				)
			) );
			
			class WPBakeryShortCode_Featured_Products extends Themerex_VC_ShortCodeSingle {}
		
		
		
			// WooCommerce - Top Rated Products
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "top_rated_products",
				"name" => esc_html__("Top Rated Products", "wineshop"),
				"description" => wp_kses_data( __("WooCommerce shortcode: show top rated products", "wineshop") ),
				"category" => esc_html__('WooCommerce', 'wineshop'),
				'icon' => 'icon_trx_top_rated_products',
				"class" => "trx_sc_single trx_sc_top_rated_products",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "per_page",
						"heading" => esc_html__("Number", "wineshop"),
						"description" => wp_kses( __("How many products showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "4",
						"type" => "textfield"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", "wineshop"),
						"description" => wp_kses( __("How many columns per row use for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "1",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Order by", "wineshop"),
						"description" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							esc_html__('Date', 'wineshop') => 'date',
							esc_html__('Title', 'wineshop') => 'title'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => esc_html__("Order", "wineshop"),
						"description" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
						"type" => "dropdown"
					)
				)
			) );
			
			class WPBakeryShortCode_Top_Rated_Products extends Themerex_VC_ShortCodeSingle {}
		
		
		
			// WooCommerce - Sale Products
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "sale_products",
				"name" => esc_html__("Sale Products", "wineshop"),
				"description" => wp_kses_data( __("WooCommerce shortcode: list products on sale", "wineshop") ),
				"category" => esc_html__('WooCommerce', 'wineshop'),
				'icon' => 'icon_trx_sale_products',
				"class" => "trx_sc_single trx_sc_sale_products",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "per_page",
						"heading" => esc_html__("Number", "wineshop"),
						"description" => wp_kses( __("How many products showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "4",
						"type" => "textfield"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", "wineshop"),
						"description" => wp_kses( __("How many columns per row use for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "1",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Order by", "wineshop"),
						"description" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							esc_html__('Date', 'wineshop') => 'date',
							esc_html__('Title', 'wineshop') => 'title'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => esc_html__("Order", "wineshop"),
						"description" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
						"type" => "dropdown"
					)
				)
			) );
			
			class WPBakeryShortCode_Sale_Products extends Themerex_VC_ShortCodeSingle {}
		
		
		
			// WooCommerce - Product Category
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "product_category",
				"name" => esc_html__("Products from category", "wineshop"),
				"description" => wp_kses_data( __("WooCommerce shortcode: list products in specified category(-ies)", "wineshop") ),
				"category" => esc_html__('WooCommerce', 'wineshop'),
				'icon' => 'icon_trx_product_category',
				"class" => "trx_sc_single trx_sc_product_category",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "per_page",
						"heading" => esc_html__("Number", "wineshop"),
						"description" => wp_kses( __("How many products showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "4",
						"type" => "textfield"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", "wineshop"),
						"description" => wp_kses( __("How many columns per row use for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "1",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Order by", "wineshop"),
						"description" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							esc_html__('Date', 'wineshop') => 'date',
							esc_html__('Title', 'wineshop') => 'title'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => esc_html__("Order", "wineshop"),
						"description" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "category",
						"heading" => esc_html__("Categories", "wineshop"),
						"description" => wp_kses( __("Comma separated category slugs", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "operator",
						"heading" => esc_html__("Operator", "wineshop"),
						"description" => wp_kses( __("Categories operator", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							esc_html__('IN', 'wineshop') => 'IN',
							esc_html__('NOT IN', 'wineshop') => 'NOT IN',
							esc_html__('AND', 'wineshop') => 'AND'
						),
						"type" => "dropdown"
					)
				)
			) );
			
			class WPBakeryShortCode_Product_Category extends Themerex_VC_ShortCodeSingle {}
		
		
		
			// WooCommerce - Products
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "products",
				"name" => esc_html__("Products", "wineshop"),
				"description" => wp_kses_data( __("WooCommerce shortcode: list all products", "wineshop") ),
				"category" => esc_html__('WooCommerce', 'wineshop'),
				'icon' => 'icon_trx_products',
				"class" => "trx_sc_single trx_sc_products",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "skus",
						"heading" => esc_html__("SKUs", "wineshop"),
						"description" => wp_kses( __("Comma separated SKU codes of products", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "ids",
						"heading" => esc_html__("IDs", "wineshop"),
						"description" => wp_kses( __("Comma separated ID of products", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", "wineshop"),
						"description" => wp_kses( __("How many columns per row use for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "1",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Order by", "wineshop"),
						"description" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							esc_html__('Date', 'wineshop') => 'date',
							esc_html__('Title', 'wineshop') => 'title'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => esc_html__("Order", "wineshop"),
						"description" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
						"type" => "dropdown"
					)
				)
			) );
			
			class WPBakeryShortCode_Products extends Themerex_VC_ShortCodeSingle {}
		
		
		
		
			// WooCommerce - Product Attribute
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "product_attribute",
				"name" => esc_html__("Products by Attribute", "wineshop"),
				"description" => wp_kses_data( __("WooCommerce shortcode: show products with specified attribute", "wineshop") ),
				"category" => esc_html__('WooCommerce', 'wineshop'),
				'icon' => 'icon_trx_product_attribute',
				"class" => "trx_sc_single trx_sc_product_attribute",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "per_page",
						"heading" => esc_html__("Number", "wineshop"),
						"description" => wp_kses_data( __("How many products showed", "wineshop") ),
						"admin_label" => true,
						"class" => "",
						"value" => "4",
						"type" => "textfield"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", "wineshop"),
						"description" => wp_kses_data( __("How many columns per row use for products output", "wineshop") ),
						"admin_label" => true,
						"class" => "",
						"value" => "1",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Order by", "wineshop"),
						"description" => wp_kses_data( __("Sorting order for products output", "wineshop") ),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							esc_html__('Date', 'wineshop') => 'date',
							esc_html__('Title', 'wineshop') => 'title'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => esc_html__("Order", "wineshop"),
						"description" => wp_kses_data( __("Sorting order for products output", "wineshop") ),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "attribute",
						"heading" => esc_html__("Attribute", "wineshop"),
						"description" => wp_kses_data( __("Attribute name", "wineshop") ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "filter",
						"heading" => esc_html__("Filter", "wineshop"),
						"description" => wp_kses_data( __("Attribute value", "wineshop") ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					)
				)
			) );
			
			class WPBakeryShortCode_Product_Attribute extends Themerex_VC_ShortCodeSingle {}
		
		
		
			// WooCommerce - Products Categories
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "product_categories",
				"name" => esc_html__("Product Categories", "wineshop"),
				"description" => wp_kses_data( __("WooCommerce shortcode: show categories with products", "wineshop") ),
				"category" => esc_html__('WooCommerce', 'wineshop'),
				'icon' => 'icon_trx_product_categories',
				"class" => "trx_sc_single trx_sc_product_categories",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "number",
						"heading" => esc_html__("Number", "wineshop"),
						"description" => wp_kses( __("How many categories showed", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "4",
						"type" => "textfield"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", "wineshop"),
						"description" => wp_kses( __("How many columns per row use for categories output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "1",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Order by", "wineshop"),
						"description" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							esc_html__('Date', 'wineshop') => 'date',
							esc_html__('Title', 'wineshop') => 'title'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => esc_html__("Order", "wineshop"),
						"description" => wp_kses( __("Sorting order for products output", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "parent",
						"heading" => esc_html__("Parent", "wineshop"),
						"description" => wp_kses( __("Parent category slug", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "date",
						"type" => "textfield"
					),
					array(
						"param_name" => "ids",
						"heading" => esc_html__("IDs", "wineshop"),
						"description" => wp_kses( __("Comma separated ID of products", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "hide_empty",
						"heading" => esc_html__("Hide empty", "wineshop"),
						"description" => wp_kses( __("Hide empty categories", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"class" => "",
						"value" => array("Hide empty" => "1" ),
						"type" => "checkbox"
					)
				)
			) );
			
			class WPBakeryShortCode_Products_Categories extends Themerex_VC_ShortCodeSingle {}
		
		}
	}
}
?>