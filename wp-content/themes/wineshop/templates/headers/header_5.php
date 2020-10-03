<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'themerex_template_header_5_theme_setup' ) ) {
	add_action( 'themerex_action_before_init_theme', 'themerex_template_header_5_theme_setup', 1 );
	function themerex_template_header_5_theme_setup() {
		themerex_add_template(array(
			'layout' => 'header_5',
			'mode'   => 'header',
			'title'  => esc_html__('Header 5', 'wineshop'),
			'icon'   => themerex_get_file_url('templates/headers/images/5.jpg')
			));
	}
}

// Template output
if ( !function_exists( 'themerex_template_header_5_output' ) ) {
	function themerex_template_header_5_output($post_options, $post_data) {
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

		<header class="top_panel_wrap top_panel_style_5 scheme_<?php echo esc_attr($post_options['scheme']); ?>">
			<div class="top_panel_wrap_inner top_panel_inner_style_5 top_panel_position_<?php echo esc_attr(themerex_get_custom_option('top_panel_position')); ?>">
			
			<?php if (themerex_get_custom_option('show_top_panel_top')=='yes') { ?>
				<div class="top_panel_top">
					<div class="content_wrap clearfix">
						<?php
						$top_panel_top_components = array('contact_info', 'login', 'currency', 'bookmarks', 'socials');
						require_once themerex_get_file_dir('templates/headers/_parts/top-panel-top.php');
						?>
					</div>
				</div>
			<?php } ?>

			<div class="top_panel_middle" <?php themerex_show_layout($header_css); ?>>
					<div class="contact_logo columns_wrap columns_fluid content_wrap no_margins">
						<div class="column-1_3">
							<?php

							if ((themerex_get_custom_option('contact_email') != '' || themerex_get_custom_option('contact_phone') != '') && themerex_exists_trx_utils()  ) {
								?>
								<div class="menu_main_info">
									<span class="icon icon-telephone60"></span>
                                    <?php echo (themerex_get_custom_option('contact_phone') != '' ? '<span class="phone">'. esc_html__ ('Call us: ', 'wineshop') .'<a href="tel:'.themerex_get_custom_option('contact_phone').'">'.themerex_get_custom_option('contact_phone').'</a>'.'</span>' : ''); ?>
                                    <?php echo (themerex_get_custom_option('contact_email') != '' ? '<span class="email">'.'<a href="mailto:'.themerex_get_custom_option('contact_email').'">'.themerex_get_custom_option('contact_email').'</a>'.'</span>' : ''); ?>
								</div>
								<?php
							}
							?>
						</div>
						<div class="column-1_3"><?php require_once themerex_get_file_dir('templates/headers/_parts/logo.php'); ?></div>
						<div class="column-1_3">
							<?php
							if (function_exists('themerex_exists_woocommerce') && themerex_exists_woocommerce() && (themerex_is_woocommerce_page() && themerex_get_custom_option('show_cart')=='shop' || themerex_get_custom_option('show_cart')=='always') && !(is_checkout() || is_cart() || defined('WOOCOMMERCE_CHECKOUT') || defined('WOOCOMMERCE_CART'))) {
								?>
								<div class="menu_main_cart top_panel_icon">
									<?php require_once themerex_get_file_dir('templates/headers/_parts/contact-info-cart.php'); ?>
									<span class="text"><?php echo esc_html_e('Your cart:', 'wineshop'); ?></span>
									<span class="cart_total"><span><?php echo esc_html($cart_items) . ' ' . ($cart_items == 1 ? esc_html__('item - ', 'wineshop') : esc_html__('items - ', 'wineshop')); ?></span><?php echo WC()->cart->get_cart_subtotal(); ?></span>
								</div>
								<?php
							}
							?>
						</div>
					</div>
                <div class="language_switcher clearfix">
                    <?php
                    //Language sidebar
                    $language_show  = themerex_get_custom_option('show_sidebar_language');
                    $sidebar_name = themerex_get_custom_option('language_switcher');
                    if (!themerex_param_is_off($language_show) && is_active_sidebar($sidebar_name)) {
                        $THEMEREX_GLOBALS['current_sidebar'] = 'language';
                        ?>
                        <?php
                        ob_start();
                        do_action( 'before_sidebar' );
                        if ( is_active_sidebar( $sidebar_name ) ) {
                            dynamic_sidebar( $sidebar_name );
                        }
                        do_action( 'after_sidebar' );
                        $out = ob_get_contents();
                        ob_end_clean();
                        themerex_show_layout(chop(preg_replace("/<\/aside>[\r\n\s]*<aside/", "</aside><aside", $out)));
                        ?>
                        <?php
                    }
                    ?>
                </div>
					<div class="menu_main_wrap clearfix">

						<a href="#" class="menu_main_responsive_button"></a>
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