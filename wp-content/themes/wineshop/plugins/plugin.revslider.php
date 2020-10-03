<?php
/* Revolution Slider support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('themerex_revslider_theme_setup')) {
    add_action( 'themerex_action_before_init_theme', 'themerex_revslider_theme_setup', 1 );
    function themerex_revslider_theme_setup() {
        if (themerex_exists_revslider()) {
            add_filter( 'themerex_filter_list_sliders',					'themerex_revslider_list_sliders' );
            add_filter( 'themerex_filter_shortcodes_params',			'themerex_revslider_shortcodes_params' );
            add_filter( 'themerex_filter_theme_options_params',			'themerex_revslider_theme_options_params' );
        }
        if (is_admin()) {
            add_filter( 'themerex_filter_required_plugins',				'themerex_revslider_required_plugins' );
        }
    }
}

if ( !function_exists( 'themerex_revslider_settings_theme_setup2' ) ) {
    add_action( 'themerex_action_before_init_theme', 'themerex_revslider_settings_theme_setup2', 3 );
    function themerex_revslider_settings_theme_setup2() {
        if (themerex_exists_revslider()) {

            // Add Revslider specific options in the Theme Options
            global $THEMEREX_GLOBALS;

            themerex_array_insert_after($THEMEREX_GLOBALS['options'], 'slider_engine', array(

                    "slider_alias" => array(
                        "title" => esc_html__('Revolution Slider: Select slider',  'wineshop'),
                        "desc" => wp_kses( __("Select slider to show (if engine=revo in the field above)", 'wineshop'), $THEMEREX_GLOBALS['allowed_tags'] ),
                        "override" => "category,services_group,page",
                        "dependency" => array(
                            'show_slider' => array('yes'),
                            'slider_engine' => array('revo')
                        ),
                        "std" => "",
                        "options" => $THEMEREX_GLOBALS['options_params']['list_revo_sliders'],
                        "type" => "select")

                )
            );

        }
    }
}

// Check if RevSlider installed and activated
if ( !function_exists( 'themerex_exists_revslider' ) ) {
    function themerex_exists_revslider() {
        return function_exists('rev_slider_shortcode');
    }
}

// Filter to add in the required plugins list
if ( !function_exists( 'themerex_revslider_required_plugins' ) ) {
    function themerex_revslider_required_plugins($list=array()) {
        if (in_array('revslider', themerex_storage_get('required_plugins'))) {
            $path = themerex_get_file_dir('plugins/install/revslider.zip');
            if (file_exists($path)) {
                $list[] = array(
                    'name' 		=> esc_html__('Revolution Slider', 'wineshop'),
                    'slug' 		=> 'revslider',
                    'version'   => '6.2.2',
                    'source'	=> $path,
                    'required' 	=> false
                );
            }
        }
        return $list;
    }
}



// Lists
//------------------------------------------------------------------------

// Add RevSlider in the sliders list, prepended inherit (if need)
if ( !function_exists( 'themerex_revslider_list_sliders' ) ) {
    function themerex_revslider_list_sliders($list=array()) {
        $list["revo"] = esc_html__("Layer slider (Revolution)", 'wineshop');
        return $list;
    }
}

// Return Revo Sliders list, prepended inherit (if need)
if ( !function_exists( 'themerex_get_list_revo_sliders' ) ) {
    function themerex_get_list_revo_sliders($prepend_inherit=false) {
        global $THEMEREX_GLOBALS;
        if (isset($THEMEREX_GLOBALS['list_revo_sliders']))
            $list = $THEMEREX_GLOBALS['list_revo_sliders'];
        else {
            $list = array();
            if (themerex_exists_revslider()) {
                global $wpdb;
                $rows = $wpdb->get_results( "SELECT alias, title FROM " . esc_sql($wpdb->prefix) . "revslider_sliders" );
                if (is_array($rows) && count($rows) > 0) {
                    foreach ($rows as $row) {
                        $list[$row->alias] = $row->title;
                    }
                }
            }
            $THEMEREX_GLOBALS['list_revo_sliders'] = $list = apply_filters('themerex_filter_list_revo_sliders', $list);
        }
        return $prepend_inherit ? themerex_array_merge(array('inherit' => esc_html__("Inherit", 'wineshop')), $list) : $list;
    }
}

// Add RevSlider in the shortcodes params
if ( !function_exists( 'themerex_revslider_shortcodes_params' ) ) {
    function themerex_revslider_shortcodes_params($list=array()) {
        $list["revo_sliders"] = themerex_get_list_revo_sliders();
        return $list;
    }
}

// Add RevSlider in the Theme Options params
if ( !function_exists( 'themerex_revslider_theme_options_params' ) ) {
    function themerex_revslider_theme_options_params($list=array()) {
        global $THEMEREX_GLOBALS;
        $list["list_revo_sliders"] = array('$'.$THEMEREX_GLOBALS['theme_slug'].'_get_list_revo_sliders' => '');
        return $list;
    }
}
?>