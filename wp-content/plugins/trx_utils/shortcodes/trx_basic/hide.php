<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('themerex_sc_hide_theme_setup')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_sc_hide_theme_setup' );
	function themerex_sc_hide_theme_setup() {
		add_action('themerex_action_shortcodes_list', 		'themerex_sc_hide_reg_shortcodes');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_hide selector="unique_id"]
*/

if (!function_exists('themerex_sc_hide')) {	
	function themerex_sc_hide($atts, $content=null){	
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			// Individual params
			"selector" => "",
			"hide" => "on",
			"delay" => 0
		), $atts)));
		$selector = trim(chop($selector));
		$output = $selector == '' ? '' : 
			'<script type="text/javascript">
				jQuery(document).ready(function() {
					'.($delay>0 ? 'setTimeout(function() {' : '').'
					jQuery("'.esc_attr($selector).'").' . ($hide=='on' ? 'hide' : 'show') . '();
					'.($delay>0 ? '},'.($delay).');' : '').'
				});
			</script>';
		return apply_filters('themerex_shortcode_output', $output, 'trx_hide', $atts, $content);
	}
	add_shortcode('trx_hide', 'themerex_sc_hide');
}



/* Add shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_hide_reg_shortcodes' ) ) {
	//add_action('themerex_action_shortcodes_list', 'themerex_sc_hide_reg_shortcodes');
	function themerex_sc_hide_reg_shortcodes() {
		global $THEMEREX_GLOBALS;
	
		$THEMEREX_GLOBALS['shortcodes']["trx_hide"] = array(
			"title" => esc_html__("Hide/Show any block", "trx_utils"),
			"desc" => wp_kses( __("Hide or Show any block with desired CSS-selector", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"selector" => array(
					"title" => esc_html__("Selector", "trx_utils"),
					"desc" => wp_kses( __("Any block's CSS-selector", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "",
					"type" => "text"
				),
				"hide" => array(
					"title" => esc_html__("Hide or Show", "trx_utils"),
					"desc" => wp_kses( __("New state for the block: hide or show", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
					"value" => "yes",
					"size" => "small",
					"options" => $THEMEREX_GLOBALS['sc_params']['yes_no'],
					"type" => "switch"
				)
			)
		);
	}
}
?>