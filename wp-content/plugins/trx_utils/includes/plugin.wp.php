<?php


//Return Post Views Count
if (!function_exists('trx_utils_get_post_views')) {
    add_filter('trx_utils_filter_get_post_views', 'trx_utils_get_post_views', 10, 2);
    function trx_utils_get_post_views($default=0, $id=0){
        global $wp_query;
        if (!$id) $id = $wp_query->current_post>=0 ? get_the_ID() : $wp_query->post->ID;
        $count_key = 'post_views_count';
        $count = get_post_meta($id, $count_key, true);
        if ($count===''){
            delete_post_meta($id, $count_key);
            add_post_meta($id, $count_key, '0');
            $count = 0;
        }
        return $count;
    }
}

//Set Post Views Count
if (!function_exists('trx_utils_set_post_views')) {
    add_action('trx_utils_filter_set_post_views', 'trx_utils_set_post_views', 10, 2);
    function trx_utils_set_post_views($id=0, $counter=-1) {
        global $wp_query;
        if (!$id) $id = $wp_query->current_post>=0 ? get_the_ID() : $wp_query->post->ID;
        $count_key = 'post_views_count';
        $count = get_post_meta($id, $count_key, true);
        if ($count===''){
            delete_post_meta($id, $count_key);
            add_post_meta($id, $count_key, 1);
        } else {
            $count = $counter >= 0 ? $counter : $count+1;
            update_post_meta($id, $count_key, $count);
        }
    }
}

//Return Post Likes Count
if (!function_exists('trx_utils_get_post_likes')) {
    add_filter('trx_utils_filter_get_post_likes', 'trx_utils_get_post_likes', 10, 2);
    function trx_utils_get_post_likes($default=0, $id=0){
        global $wp_query;
        if (!$id) $id = $wp_query->current_post>=0 ? get_the_ID() : $wp_query->post->ID;
        $count_key = 'post_likes_count';
        $count = get_post_meta($id, $count_key, true);
        if ($count===''){
            delete_post_meta($id, $count_key);
            add_post_meta($id, $count_key, '0');
            $count = 0;
        }
        return $count;
    }
}

//Set Post Likes Count
if (!function_exists('trx_utils_set_post_likes')) {
    add_action('trx_utils_filter_set_post_likes', 'trx_utils_set_post_likes', 10, 2);
    function trx_utils_set_post_likes($id=0, $count=0) {
        global $wp_query;
        if (!$id) $id = $wp_query->current_post>=0 ? get_the_ID() : $wp_query->post->ID;
        $count_key = 'post_likes_count';
        update_post_meta($id, $count_key, $count);
    }
}

// AJAX: Set post likes/views count
if ( !function_exists( 'trx_utils_callback_post_counter' ) ) {
    function trx_utils_callback_post_counter() {
        global $_REQUEST, $THEMEREX_GLOBALS;

        if ( !wp_verify_nonce( $_REQUEST['nonce'], $THEMEREX_GLOBALS['ajax_url'] ) )
            wp_die();

        $response = array('error'=>'');

        $id = (int) $_REQUEST['post_id'];
        if (isset($_REQUEST['likes'])) {
            $counter = max(0, (int) $_REQUEST['likes']);
            trx_utils_set_post_likes($id, $counter);
        } else if (isset($_REQUEST['views'])) {
            $counter = max(0, (int) $_REQUEST['views']);
            trx_utils_set_post_views($id, $counter);
        }
        echo json_encode($response);
        wp_die();
    }
}