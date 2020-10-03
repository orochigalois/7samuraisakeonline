<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'themerex_template_header_3_theme_setup' ) ) {
	add_action( 'themerex_action_before_init_theme', 'themerex_template_header_3_theme_setup', 1 );
	function themerex_template_header_3_theme_setup() {
		themerex_add_template(array(
			'layout' => 'header_3',
			'mode'   => 'header',
			'title'  => esc_html__('Header 3', 'wineshop'),
			'icon'   => themerex_get_file_url('templates/headers/images/3.jpg')
			));
	}
}

// Template output
if ( !function_exists( 'themerex_template_header_3_output' ) ) {
	function themerex_template_header_3_output($post_options, $post_data) {
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

		<header class="top_panel_wrap top_panel_style_3 scheme_<?php echo esc_attr($post_options['scheme']); ?>">
			<div class="top_panel_wrap_inner top_panel_inner_style_3 top_panel_position_<?php echo esc_attr(themerex_get_custom_option('top_panel_position')); ?>">
			
			<?php if (themerex_get_custom_option('show_top_panel_top')=='yes') { ?>
				<div class="top_panel_top">
					<div class="content_wrap clearfix">
						<?php
						$top_panel_top_components = array('contact_info', 'search', 'login', 'socials', 'currency', 'bookmarks');
						require_once themerex_get_file_dir('templates/headers/_parts/top-panel-top.php');
						?>
					</div>
				</div>
			<?php } ?>

			<div class="top_panel_middle" <?php themerex_show_layout($header_css); ?>>
				<div class="content_wrap">
					<div class="columns_wrap columns_fluid"><div
						class="column-1_3 contact_logo">
							<?php require_once themerex_get_file_dir('templates/headers/_parts/logo.php'); ?>
						</div><div 
						class="column-2_3 menu_main_wrap">
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
			</div>

			</div>
		</header>

		<?php
	}
}
?>