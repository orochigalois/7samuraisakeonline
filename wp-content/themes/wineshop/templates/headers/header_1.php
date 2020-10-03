<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'themerex_template_header_1_theme_setup' ) ) {
	add_action( 'themerex_action_before_init_theme', 'themerex_template_header_1_theme_setup', 1 );
	function themerex_template_header_1_theme_setup() {
		themerex_add_template(array(
			'layout' => 'header_1',
			'mode'   => 'header',
			'title'  => esc_html__('Header 1', 'wineshop'),
			'icon'   => themerex_get_file_url('templates/headers/images/1.jpg')
			));
	}
}

// Template output
if ( !function_exists( 'themerex_template_header_1_output' ) ) {
	function themerex_template_header_1_output($post_options, $post_data) {
		global $THEMEREX_GLOBALS;

		// WP custom header
		$header_css = '';
		if ($post_options['position'] != 'over') {
			$header_image = get_header_image();
			$header_css = $header_image!='' 
				? ' style="background: url('.esc_url($header_image).') repeat center top"' 
				: '';
		}
		?>
		
		<div class="top_panel_fixed_wrap"></div>

		<header class="top_panel_wrap top_panel_style_1 scheme_<?php echo esc_attr($post_options['scheme']); ?>">
			<div class="top_panel_wrap_inner top_panel_inner_style_1 top_panel_position_<?php echo esc_attr(themerex_get_custom_option('top_panel_position')); ?>">
			
			<?php if (themerex_get_custom_option('show_top_panel_top')=='yes') { ?>
				<div class="top_panel_top">
					<div class="content_wrap clearfix">
						<?php
						$top_panel_top_components = array('contact_info', 'open_hours', 'login', 'socials', 'currency', 'bookmarks');
						require_once themerex_get_file_dir('templates/headers/_parts/top-panel-top.php');
						?>
					</div>
				</div>
			<?php } ?>

			<div class="top_panel_middle" <?php themerex_show_layout($header_css); ?>>
				<div class="content_wrap">
					<div class="columns_wrap columns_fluid">
						<div class="column-2_5 contact_logo">
							<?php require_once themerex_get_file_dir('templates/headers/_parts/logo.php'); ?>
						</div><?php
						// Address
						$contact_address_1=trim(themerex_get_custom_option('contact_address_1'));
						$contact_address_2=trim(themerex_get_custom_option('contact_address_2'));
						if (!empty($contact_address_1) || !empty($contact_address_2)) {
							?><div class="column-1_5 contact_field contact_address">
								<span class="contact_icon icon-home"></span>
								<span class="contact_label contact_address_1"><?php themerex_show_layout($contact_address_1); ?></span>
								<span class="contact_address_2"><?php themerex_show_layout($contact_address_2); ?></span>
							</div><?php
						}
						
						// Phone and email
						$contact_phone=trim(themerex_get_custom_option('contact_phone'));
						$contact_email=trim(themerex_get_custom_option('contact_email'));
						if (!empty($contact_phone) || !empty($contact_email)) {
							?><div class="column-1_5 contact_field contact_phone">
								<span class="contact_icon icon-phone"></span>
								<span class="contact_label contact_phone"><?php themerex_show_layout($contact_phone); ?></span>
								<span class="contact_email"><?php themerex_show_layout($contact_email); ?></span>
							</div><?php
						}
						
						// Woocommerce Cart
						if (function_exists('themerex_exists_woocommerce') && themerex_exists_woocommerce() && (themerex_is_woocommerce_page() && themerex_get_custom_option('show_cart')=='shop' || themerex_get_custom_option('show_cart')=='always') && !(is_checkout() || is_cart() || defined('WOOCOMMERCE_CHECKOUT') || defined('WOOCOMMERCE_CART'))) {
							?><div class="column-1_5 contact_field contact_cart"><?php require_once themerex_get_file_dir('templates/headers/_parts/contact-info-cart.php'); ?></div><?php
						}
						?></div>
				</div>
			</div>

			<div class="top_panel_bottom">
				<div class="content_wrap clearfix">
					<a href="#" class="menu_main_responsive_button"><?php esc_html_e('Menu', 'wineshop'); ?></a>
					<nav class="menu_main_nav_area">
						<?php
						if (empty($THEMEREX_GLOBALS['menu_main'])) $THEMEREX_GLOBALS['menu_main'] = themerex_get_nav_menu('menu_main');
						if (empty($THEMEREX_GLOBALS['menu_main'])) $THEMEREX_GLOBALS['menu_main'] = themerex_get_nav_menu();
                        themerex_show_layout($THEMEREX_GLOBALS['menu_main']);
						?>
					</nav>
				</div>
			</div>

			</div>
		</header>

		<?php
	}
}
?>