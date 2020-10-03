<?php
/* WPBakery PageBuilder support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('themerex_vc_theme_setup')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_vc_theme_setup', 1 );
	function themerex_vc_theme_setup() {

		if (is_admin()) {
			add_filter( 'themerex_filter_importer_required_plugins',		'themerex_vc_importer_required_plugins', 10, 2 );
			add_filter( 'themerex_filter_required_plugins',					'themerex_vc_required_plugins' );
		}
	}
}

// Check if WPBakery PageBuilder installed and activated
if ( !function_exists( 'themerex_exists_visual_composer' ) ) {
	function themerex_exists_visual_composer() {
		return class_exists('Vc_Manager');
	}
}

// Check if WPBakery PageBuilder in frontend editor mode
if ( !function_exists( 'themerex_vc_is_frontend' ) ) {
	function themerex_vc_is_frontend() {
		return (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true')
			|| (isset($_GET['vc_action']) && $_GET['vc_action']=='vc_inline');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'themerex_vc_required_plugins' ) ) {
	function themerex_vc_required_plugins($list=array()) {
		if (in_array('visual_composer', (array)themerex_storage_get('required_plugins'))) {
			$path = themerex_get_file_dir('plugins/install/js_composer.zip');
			if (file_exists($path)) {
		$list[] = array(
					'name' 		=> esc_html__('WPBakery PageBuilder', 'wineshop'),
					'slug' 		=> 'js_composer',
					'version'   => '6.1',
					'source'	=> $path,
					'required' 	=> false
				);
			}
		}
		return $list;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check VC in the required plugins
if ( !function_exists( 'themerex_vc_importer_required_plugins' ) ) {
	function themerex_vc_importer_required_plugins($not_installed='', $list='') {
		if (!themerex_exists_visual_composer() )
            $not_installed .= '<br>' . esc_html__('WPBakery PageBuilder', 'wineshop');
		return $not_installed;
	}
}
?>