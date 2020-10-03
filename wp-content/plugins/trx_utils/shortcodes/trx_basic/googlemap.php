<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('themerex_sc_googlemap_theme_setup')) {
    add_action( 'themerex_action_before_init_theme', 'themerex_sc_googlemap_theme_setup' );
    function themerex_sc_googlemap_theme_setup() {
        add_action('themerex_action_shortcodes_list', 		'themerex_sc_googlemap_reg_shortcodes');
        if (function_exists('themerex_exists_visual_composer') && themerex_exists_visual_composer())
            add_action('themerex_action_shortcodes_list_vc','themerex_sc_googlemap_reg_shortcodes_vc');
    }
}



/* Shortcode implementation
-------------------------------------------------------------------- */

//[trx_googlemap id="unique_id" width="width_in_pixels_or_percent" height="height_in_pixels"]
//	[trx_googlemap_marker address="your_address"]
//[/trx_googlemap]

if (!function_exists('themerex_sc_googlemap')) {
    function themerex_sc_googlemap($atts, $content = null) {
        if (themerex_in_shortcode_blogger()) return '';
        extract(themerex_html_decode(shortcode_atts(array(
            // Individual params
            "zoom" => 16,
            "style" => 'default',
            // Common params
            "id" => "",
            "class" => "",
            "css" => "",
            "animation" => "",
            "width" => "100%",
            "height" => "400",
            "top" => "",
            "bottom" => "",
            "left" => "",
            "right" => ""
        ), $atts)));
        $class .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
        $css .= themerex_get_css_dimensions_from_values($width, $height);
        if (empty($id)) $id = 'sc_googlemap_'.str_replace('.', '', mt_rand());
        if (empty($style)) $style = themerex_get_custom_option('googlemap_style');
        if (themerex_get_theme_option('api_google') != '') {
            //if (themerex_param_is_on(themerex_get_theme_option('debug_mode'))) {
            $api_key = themerex_get_theme_option('api_google');
            wp_enqueue_script('googlemap', themerex_get_protocol() . '://maps.google.com/maps/api/js' . ($api_key ? '?key=' . $api_key : ''), array(), null, true);
            wp_enqueue_script('themerex-googlemap-script', trx_utils_get_file_url('js/core.googlemap.js'), array(), null, true);
            //}
        }

        global $THEMEREX_GLOBALS;
        $THEMEREX_GLOBALS['sc_googlemap_markers'] = array();
        $content = do_shortcode($content);
        $output = '';
        if (count($THEMEREX_GLOBALS['sc_googlemap_markers']) == 0) {
            $THEMEREX_GLOBALS['sc_googlemap_markers'][] = array(
                'title' => themerex_get_custom_option('googlemap_title'),
                'description' => themerex_strmacros(themerex_get_custom_option('googlemap_description')),
                'latlng' => themerex_get_custom_option('googlemap_latlng'),
                'address' => themerex_get_custom_option('googlemap_address'),
                'point' => themerex_get_custom_option('googlemap_marker')
            );
        }
        $output .= '<div id="'.esc_attr($id).'"'
            . ' class="sc_googlemap'. (!empty($class) ? ' '.esc_attr($class) : '').'"'
            . ($css!='' ? ' style="'.esc_attr($css).'"' : '')
            . (!themerex_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_get_animation_classes($animation)).'"' : '')
            . ' data-zoom="'.esc_attr($zoom).'"'
            . ' data-style="'.esc_attr($style).'"'
            . '>';
        $cnt = 0;
        foreach ($THEMEREX_GLOBALS['sc_googlemap_markers'] as $marker) {
            $cnt++;
            if (empty($marker['id'])) $marker['id'] = $id.'_'.intval($cnt);
            if (themerex_get_theme_option('api_google') != '') {
                $output .= '<div id="'.esc_attr($marker['id']).'" class="sc_googlemap_marker"'
                    . ' data-title="'.esc_attr($marker['title']).'"'
                    . ' data-description="'.esc_attr(themerex_strmacros($marker['description'])).'"'
                    . ' data-address="'.esc_attr($marker['address']).'"'
                    . ' data-latlng="'.esc_attr($marker['latlng']).'"'
                    . ' data-point="'.esc_attr($marker['point']).'"'
                    . '></div>';
            } else {
                $output .= '<iframe src="https://maps.google.com/maps?t=m&output=embed&iwloc=near&z='.esc_attr($zoom > 0 ? $zoom : 14).'&q='
                    . esc_attr(!empty($marker['address']) ? urlencode($marker['address']) : '')
                    . ( !empty($marker['latlng'])
                        ? ( !empty($marker['address']) ? '@' : '' ) . str_replace(' ', '', $marker['latlng'])
                        : ''
                    ) . '" scrolling="no" marginheight="0" marginwidth="0" frameborder="0"'.
                    ' "aria-label="' . esc_attr(!empty($marker['title']) ? $marker['title'] : '') . '"></iframe>';
                break; // Remove this line if you want display separate iframe for each marker (otherwise only first marker shown)
            }
        }
        $output .= '</div>';
        return apply_filters('themerex_shortcode_output', $output, 'trx_googlemap', $atts, $content);
    }
    add_shortcode("trx_googlemap", "themerex_sc_googlemap");
}


if (!function_exists('themerex_sc_googlemap_marker')) {
    function themerex_sc_googlemap_marker($atts, $content = null) {
        if (themerex_in_shortcode_blogger()) return '';
        extract(themerex_html_decode(shortcode_atts(array(
            // Individual params
            "title" => "",
            "address" => "",
            "latlng" => "",
            "point" => "",
            // Common params
            "id" => ""
        ), $atts)));
        if (!empty($point)) {
            if ($point > 0) {
                $attach = wp_get_attachment_image_src( $point, 'full' );
                if (isset($attach[0]) && $attach[0]!='')
                    $point = $attach[0];
            }
        }
        global $THEMEREX_GLOBALS;
        $THEMEREX_GLOBALS['sc_googlemap_markers'][] = array(
            'id' => $id,
            'title' => $title,
            'description' => do_shortcode($content),
            'latlng' => $latlng,
            'address' => $address,
            'point' => $point ? $point : themerex_get_custom_option('googlemap_marker')
        );
        return '';
    }
    add_shortcode("trx_googlemap_marker", "themerex_sc_googlemap_marker");
}



/* Add shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_googlemap_reg_shortcodes' ) ) {
    //add_action('themerex_action_shortcodes_list', 'themerex_sc_googlemap_reg_shortcodes');
    function themerex_sc_googlemap_reg_shortcodes() {
        global $THEMEREX_GLOBALS;

        $THEMEREX_GLOBALS['shortcodes']["trx_googlemap"] = array(
            "title" => esc_html__("Google map", "trx_utils"),
            "desc" => wp_kses( __("Insert Google map with specified markers", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
            "decorate" => false,
            "container" => true,
            "params" => array(
                "zoom" => array(
                    "title" => esc_html__("Zoom", "trx_utils"),
                    "desc" => wp_kses( __("Map zoom factor", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
                    "divider" => true,
                    "value" => 16,
                    "min" => 1,
                    "max" => 20,
                    "type" => "spinner"
                ),
                "style" => array(
                    "title" => esc_html__("Map style", "trx_utils"),
                    "desc" => wp_kses( __("Select map style", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
                    "value" => "default",
                    "type" => "checklist",
                    "options" => $THEMEREX_GLOBALS['sc_params']['googlemap_styles']
                ),
                "width" => themerex_shortcodes_width('100%'),
                "height" => themerex_shortcodes_height(240),
                "top" => $THEMEREX_GLOBALS['sc_params']['top'],
                "bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
                "left" => $THEMEREX_GLOBALS['sc_params']['left'],
                "right" => $THEMEREX_GLOBALS['sc_params']['right'],
                "id" => $THEMEREX_GLOBALS['sc_params']['id'],
                "class" => $THEMEREX_GLOBALS['sc_params']['class'],
                "animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
                "css" => $THEMEREX_GLOBALS['sc_params']['css']
            ),
            "children" => array(
                "name" => "trx_googlemap_marker",
                "title" => esc_html__("Google map marker", "trx_utils"),
                "desc" => wp_kses( __("Google map marker", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
                "decorate" => false,
                "container" => true,
                "params" => array(
                    "address" => array(
                        "title" => esc_html__("Address", "trx_utils"),
                        "desc" => wp_kses( __("Address of this marker", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
                        "value" => "",
                        "type" => "text"
                    ),
                    "latlng" => array(
                        "title" => esc_html__("Latitude and Longitude", "trx_utils"),
                        "desc" => wp_kses( __("Comma separated marker's coorditanes (instead Address)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
                        "value" => "",
                        "type" => "text"
                    ),
                    "point" => array(
                        "title" => esc_html__("URL for marker image file", "trx_utils"),
                        "desc" => wp_kses( __("Select or upload image or write URL from other site for this marker. If empty - use default marker", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
                        "readonly" => false,
                        "value" => "",
                        "type" => "media"
                    ),
                    "title" => array(
                        "title" => esc_html__("Title", "trx_utils"),
                        "desc" => wp_kses( __("Title for this marker", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
                        "value" => "",
                        "type" => "text"
                    ),
                    "_content_" => array(
                        "title" => esc_html__("Description", "trx_utils"),
                        "desc" => wp_kses( __("Description for this marker", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
                        "rows" => 4,
                        "value" => "",
                        "type" => "textarea"
                    ),
                    "id" => $THEMEREX_GLOBALS['sc_params']['id']
                )
            )
        );
    }
}


/* Add shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_sc_googlemap_reg_shortcodes_vc' ) ) {
    //add_action('themerex_action_shortcodes_list_vc', 'themerex_sc_googlemap_reg_shortcodes_vc');
    function themerex_sc_googlemap_reg_shortcodes_vc() {
        global $THEMEREX_GLOBALS;

        vc_map( array(
            "base" => "trx_googlemap",
            "name" => esc_html__("Google map", "trx_utils"),
            "description" => wp_kses( __("Insert Google map with desired address or coordinates", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
            "category" => esc_html__('Content', 'trx_utils'),
            'icon' => 'icon_trx_googlemap',
            "class" => "trx_sc_collection trx_sc_googlemap",
            "content_element" => true,
            "is_container" => true,
            "as_parent" => array('only' => 'trx_googlemap_marker'),
            "show_settings_on_create" => true,
            "params" => array(
                array(
                    "param_name" => "zoom",
                    "heading" => esc_html__("Zoom", "trx_utils"),
                    "description" => wp_kses( __("Map zoom factor", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
                    "admin_label" => true,
                    "class" => "",
                    "value" => "16",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "style",
                    "heading" => esc_html__("Style", "trx_utils"),
                    "description" => wp_kses( __("Map custom style", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
                    "admin_label" => true,
                    "class" => "",
                    "value" => array_flip($THEMEREX_GLOBALS['sc_params']['googlemap_styles']),
                    "type" => "dropdown"
                ),
                $THEMEREX_GLOBALS['vc_params']['id'],
                $THEMEREX_GLOBALS['vc_params']['class'],
                $THEMEREX_GLOBALS['vc_params']['animation'],
                $THEMEREX_GLOBALS['vc_params']['css'],
                themerex_vc_width('100%'),
                themerex_vc_height(240),
                $THEMEREX_GLOBALS['vc_params']['margin_top'],
                $THEMEREX_GLOBALS['vc_params']['margin_bottom'],
                $THEMEREX_GLOBALS['vc_params']['margin_left'],
                $THEMEREX_GLOBALS['vc_params']['margin_right']
            )
        ) );

        vc_map( array(
            "base" => "trx_googlemap_marker",
            "name" => esc_html__("Googlemap marker", "trx_utils"),
            "description" => wp_kses( __("Insert new marker into Google map", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
            "class" => "trx_sc_collection trx_sc_googlemap_marker",
            'icon' => 'icon_trx_googlemap_marker',
            //"allowed_container_element" => 'vc_row',
            "show_settings_on_create" => true,
            "content_element" => true,
            "is_container" => true,
            "as_child" => array('only' => 'trx_googlemap'), // Use only|except attributes to limit parent (separate multiple values with comma)
            "params" => array(
                array(
                    "param_name" => "address",
                    "heading" => esc_html__("Address", "trx_utils"),
                    "description" => wp_kses( __("Address of this marker", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
                    "admin_label" => true,
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "latlng",
                    "heading" => esc_html__("Latitude and Longitude", "trx_utils"),
                    "description" => wp_kses( __("Comma separated marker's coorditanes (instead Address)", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
                    "admin_label" => true,
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "title",
                    "heading" => esc_html__("Title", "trx_utils"),
                    "description" => wp_kses( __("Title for this marker", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
                    "admin_label" => true,
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "point",
                    "heading" => esc_html__("URL for marker image file", "trx_utils"),
                    "description" => wp_kses( __("Select or upload image or write URL from other site for this marker. If empty - use default marker", "trx_utils"), $THEMEREX_GLOBALS['allowed_tags'] ),
                    "class" => "",
                    "value" => "",
                    "type" => "attach_image"
                ),
                $THEMEREX_GLOBALS['vc_params']['id']
            )
        ) );

        class WPBakeryShortCode_Trx_Googlemap extends Themerex_VC_ShortCodeCollection {}
        class WPBakeryShortCode_Trx_Googlemap_Marker extends Themerex_VC_ShortCodeCollection {}
    }
}
?>