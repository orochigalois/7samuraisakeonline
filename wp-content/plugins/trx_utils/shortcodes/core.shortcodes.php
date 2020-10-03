<?php
/**
 * ThemeREX Framework: shortcodes manipulations
 *
 * @package	themerex
 * @since	themerex 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Theme init
if (!function_exists('themerex_sc_theme_setup')) {
	add_action( 'themerex_action_init_theme', 'themerex_sc_theme_setup', 1 );
	function themerex_sc_theme_setup() {
		// Add sc stylesheets
		add_action('themerex_action_add_styles', 'themerex_sc_add_styles', 1);
	}
}

if (!function_exists('themerex_sc_theme_setup2')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_sc_theme_setup2' );
	function themerex_sc_theme_setup2() {

		if ( !is_admin() || isset($_POST['action']) ) {
			// Enable/disable shortcodes in excerpt
			add_filter('the_excerpt', 					'themerex_sc_excerpt_shortcodes');
	
			// Prepare shortcodes in the content
			if (function_exists('themerex_sc_prepare_content')) themerex_sc_prepare_content();
		}

		// Add init script into shortcodes output in VC frontend editor
		add_filter('themerex_shortcode_output', 'themerex_sc_add_scripts', 10, 4);

		// AJAX: Send contact form data
		add_action('wp_ajax_send_form',			'themerex_sc_form_send');
		add_action('wp_ajax_nopriv_send_form',	'themerex_sc_form_send');

		// Show shortcodes list in admin editor
		add_action('media_buttons',				'themerex_sc_selector_add_in_toolbar', 11);

	}
}


// Add shortcodes styles
if ( !function_exists( 'themerex_sc_add_styles' ) ) {
	//add_action('themerex_action_add_styles', 'themerex_sc_add_styles', 1);
	function themerex_sc_add_styles() {
		// Shortcodes
        wp_enqueue_style( 'themerex-shortcodes-style',	trx_utils_get_file_url('shortcodes/theme.shortcodes.css'), array(), null );
	}
}


// Add shortcodes init scripts
if ( !function_exists( 'themerex_sc_add_scripts' ) ) {
	//add_filter('themerex_shortcode_output', 'themerex_sc_add_scripts', 10, 4);
	function themerex_sc_add_scripts($output, $tag='', $atts=array(), $content='') {

		global $THEMEREX_GLOBALS;
		
		if (empty($THEMEREX_GLOBALS['shortcodes_scripts_added'])) {
			$THEMEREX_GLOBALS['shortcodes_scripts_added'] = true;
            wp_enqueue_script( 'themerex-shortcodes-script', trx_utils_get_file_url('shortcodes/theme.shortcodes.js'), array('jquery'), null, true );
		}
		
		return $output;
	}
}


/* Prepare text for shortcodes
-------------------------------------------------------------------------------- */

// Prepare shortcodes in content
if (!function_exists('themerex_sc_prepare_content')) {
	function themerex_sc_prepare_content() {
		if (function_exists('themerex_sc_clear_around')) {
			$filters = array(
				array('trx_utils', 'sc', 'clear', 'around'),
				array('widget', 'text'),
				array('the', 'excerpt'),
				array('the', 'content')
			);
			if (function_exists('themerex_exists_woocommerce') && themerex_exists_woocommerce()) {
				$filters[] = array('woocommerce', 'template', 'single', 'excerpt');
				$filters[] = array('woocommerce', 'short', 'description');
			}
			if (is_array($filters) && count($filters) > 0) {
				foreach ($filters as $flt)
					add_filter(join('_', $flt), 'themerex_sc_clear_around', 1);	// Priority 1 to clear spaces before do_shortcodes()
			}
		}
	}
}

// Enable/Disable shortcodes in the excerpt
if (!function_exists('themerex_sc_excerpt_shortcodes')) {
	function themerex_sc_excerpt_shortcodes($content) {
		if (!empty($content)) {
			$content = do_shortcode($content);
			//$content = strip_shortcodes($content);
		}
		return $content;
	}
}



/*
// Remove spaces and line breaks between close and open shortcode brackets ][:
[trx_columns]
	[trx_column_item]Column text ...[/trx_column_item]
	[trx_column_item]Column text ...[/trx_column_item]
	[trx_column_item]Column text ...[/trx_column_item]
[/trx_columns]

convert to

[trx_columns][trx_column_item]Column text ...[/trx_column_item][trx_column_item]Column text ...[/trx_column_item][trx_column_item]Column text ...[/trx_column_item][/trx_columns]
*/
if (!function_exists('themerex_sc_clear_around')) {
	function themerex_sc_clear_around($content) {
		if (!empty($content)) $content = preg_replace("/\](\s|\n|\r)*\[/", "][", $content);
		return $content;
	}
}






/* Shortcodes support utils
---------------------------------------------------------------------- */

// ThemeREX shortcodes load scripts
if (!function_exists('themerex_sc_load_scripts')) {
	function themerex_sc_load_scripts() {
        wp_enqueue_script( 'themerex-shortcodes-script', trx_utils_get_file_url('shortcodes/shortcodes_admin.js'), array('jquery'), null, true );
        wp_enqueue_script( 'themerex-selection-script',  themerex_get_file_url('js/jquery.selection.js'), array('jquery'), null, true );
	}
}

// ThemeREX shortcodes prepare scripts
if (!function_exists('themerex_sc_prepare_scripts')) {
	function themerex_sc_prepare_scripts() {
		global $THEMEREX_GLOBALS;
		if (!isset($THEMEREX_GLOBALS['shortcodes_prepared'])) {
			$THEMEREX_GLOBALS['shortcodes_prepared'] = true;
			$json_parse_func = 'eval';	// 'JSON.parse'
			?>
			<script type="text/javascript">
				jQuery(document).ready(function(){
					try {
						THEMEREX_GLOBALS['shortcodes'] = <?php themerex_show_layout($json_parse_func); ?>(<?php echo json_encode( themerex_array_prepare_to_json($THEMEREX_GLOBALS['shortcodes']) ); ?>);
					} catch (e) {}
					THEMEREX_GLOBALS['shortcodes_cp'] = '<?php echo is_admin() ? (!empty($THEMEREX_GLOBALS['to_colorpicker']) ? $THEMEREX_GLOBALS['to_colorpicker'] : 'wp') : 'custom'; ?>';	// wp | tiny | custom
				});
			</script>
			<?php
		}
	}
}

// Show shortcodes list in admin editor
if (!function_exists('themerex_sc_selector_add_in_toolbar')) {
	//add_action('media_buttons','themerex_sc_selector_add_in_toolbar', 11);
	function themerex_sc_selector_add_in_toolbar(){

		if ( !themerex_options_is_used() ) return;

		themerex_sc_load_scripts();
		themerex_sc_prepare_scripts();

		global $THEMEREX_GLOBALS;

		$shortcodes = $THEMEREX_GLOBALS['shortcodes'];
		$shortcodes_list = '<select class="sc_selector"><option value="">&nbsp;'.esc_html__('- Select Shortcode -', 'trx_utils').'&nbsp;</option>';

		if (is_array($shortcodes) && count($shortcodes) > 0) {
			foreach ($shortcodes as $idx => $sc) {
				$shortcodes_list .= '<option value="'.esc_attr($idx).'" title="'.esc_attr($sc['desc']).'">'.esc_html($sc['title']).'</option>';
			}
		}

		$shortcodes_list .= '</select>';

		echo balanceTags($shortcodes_list);
	}
}

// ThemeREX shortcodes builder settings
require_once trx_utils_get_file_dir('shortcodes/shortcodes_settings.php');

// VC shortcodes settings
if ( class_exists('WPBakeryShortCode') ) {
	require_once trx_utils_get_file_dir('shortcodes/shortcodes_vc.php');
}

?>