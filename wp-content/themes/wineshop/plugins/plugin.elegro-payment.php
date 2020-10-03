<?php
/* Elegro Crypto Payment support functions
------------------------------------------------------------------------------- */

// Check if plugin installed and activated
if ( !function_exists( 'themerex_exists_elegro_payment' ) ) {
    function themerex_exists_elegro_payment() {
        return class_exists( 'WC_Elegro_Payment' );
    }
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('themerex_elegro_payment_theme_setup')) {
    add_action( 'themerex_action_before_init_theme', 'themerex_elegro_payment_theme_setup', 1 );
    function themerex_elegro_payment_theme_setup() {
        if (themerex_exists_elegro_payment()) {
            add_action('themerex_action_add_styles',	'themerex_elegro_payment_frontend_scripts' );
        }
        if (is_admin()) {
            add_filter( 'themerex_filter_required_plugins',		'themerex_elegro_payment_required_plugins' );
        }
    }
}

// Filter to add in the required plugins list
if ( !function_exists( 'themerex_elegro_payment_required_plugins' ) ) {
    function themerex_elegro_payment_required_plugins($list=array()) {
        if (in_array('elegro-payment', (array)themerex_storage_get('required_plugins'))) {
            $list[] = array(
                'name' 		=> esc_html__('Elegro Crypto Payment', 'wineshop'),
                'slug' 		=> 'elegro-payment',
                'required' 	=> false
            );
        }
        return $list;
    }
}


// Enqueue Elegro Payment custom styles
if ( !function_exists( 'themerex_elegro_payment_frontend_scripts' ) ) {
    function themerex_elegro_payment_frontend_scripts() {
        if (file_exists(themerex_get_file_dir('css/plugin.elegro-payment.css')))
            wp_enqueue_style( 'themerex-plugin-elegro-payment-style',  themerex_get_file_url('css/plugin.elegro-payment.css'), array(), null );
    }
}
?>