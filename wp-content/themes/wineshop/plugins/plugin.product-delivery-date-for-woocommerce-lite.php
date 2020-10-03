<?php
/* Product Delivery Date for WooCommerce - Lite support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('themerex_product_delivery_date_for_woocommerce_lite_theme_setup')) {
    add_action( 'themerex_action_before_init_theme', 'themerex_product_delivery_date_for_woocommerce_lite_theme_setup', 1 );
    function themerex_product_delivery_date_for_woocommerce_lite_theme_setup() {
        if (is_admin()) {
            add_filter( 'themerex_filter_required_plugins', 'themerex_product_delivery_date_for_woocommerce_lite_required_plugins' );
        }
    }
}


// Filter to add in the required plugins list
if ( !function_exists( 'themerex_product_delivery_date_for_woocommerce_lite_required_plugins' ) ) {
    function themerex_product_delivery_date_for_woocommerce_lite_required_plugins($list=array()) {
        if (in_array('product-delivery-date-for-woocommerce-lite', (array)themerex_storage_get('required_plugins')))
            $list[] = array(
                'name'         => esc_html__('Product Delivery Date for WooCommerce - Lite', 'wineshop'),
                'slug'         => 'product-delivery-date-for-woocommerce-lite',
                'required'     => false
            );
        return $list;
    }
}


