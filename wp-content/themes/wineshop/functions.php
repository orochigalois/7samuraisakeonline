<?php
/**
 * Theme sprecific functions and definitions
 */

/**
 * Fire the wp_body_open action.
 *
 * Added for backwards compatibility to support pre 5.2.0 WordPress versions.
 */
if ( ! function_exists( 'wp_body_open' ) ) {
    function wp_body_open() {
        /**
         * Triggered after the opening <body> tag.
         */
        do_action('wp_body_open');
    }
}


/* Theme setup section
------------------------------------------------------------------- */

// Set the content width based on the theme's design and stylesheet.
if ( ! isset( $content_width ) ) $content_width = 1170; /* pixels */

// Add theme specific actions and filters
// Attention! Function were add theme specific actions and filters handlers must have priority 1
if ( !function_exists( 'themerex_theme_setup' ) ) {
	add_action( 'themerex_action_before_init_theme', 'themerex_theme_setup', 1 );
	function themerex_theme_setup() {

		// Register theme menus
		add_filter( 'themerex_filter_add_theme_menus',		'themerex_add_theme_menus' );

		// Register theme sidebars
		add_filter( 'themerex_filter_add_theme_sidebars',	'themerex_add_theme_sidebars' );


        // Add default posts and comments RSS feed links to head
        add_theme_support( 'automatic-feed-links' );

        // Enable support for Post Thumbnails
        add_theme_support( 'post-thumbnails' );

        // Custom header setup
        add_theme_support( 'custom-header', array('header-text'=>false));

        // Custom backgrounds setup
        add_theme_support( 'custom-background');

        // Supported posts formats
        add_theme_support( 'post-formats', array('gallery', 'video', 'audio', 'link', 'quote', 'image', 'status', 'aside', 'chat') );

        // Autogenerate title tag
        add_theme_support('title-tag');

        // Add user menu
        add_theme_support('nav-menus');

        // WooCommerce Support
        add_theme_support( 'woocommerce' );

        // Gutenberg support
        add_theme_support( 'align-wide' );


        add_action('wp_head', 'themerex_head_add_page_meta', 1);

        global $THEMEREX_GLOBALS;

        $THEMEREX_GLOBALS['required_plugins'] = array(
                'visual_composer',
                'revslider',
                'woocommerce',
                'buddypress',
                'bbpress',
                'calcfields',
                'essgrids',
                'learndash',
                'tribe_events',
                'trx_donations',
            	'trx_utils',
                'responsive_poll',
                'wp-gdpr-compliance',
                'contact-form7',
                'trx_updater',
                'elegro-payment',
                'product-delivery-date-for-woocommerce-lite'
        );

        if ( is_dir(THEMEREX_THEME_PATH . 'demo/') ) {
            $THEMEREX_GLOBALS['demo_data_url'] = THEMEREX_THEME_PATH . 'demo/';
        } else {
            $THEMEREX_GLOBALS['demo_data_url'] =  esc_url(themerex_get_protocol().'://wineshop.upd.themerex.net/demo'); // Demo-site domain
        }
	}
}

add_filter( 'body_class', 'themerex_add_body_classes' );

if ( ! function_exists( 'themerex_add_body_classes' ) ) {
    //Handler of the add_filter( 'body_class', 'themerex_add_body_classes' );
    function themerex_add_body_classes( $classes ) {
        $body_style  = themerex_get_custom_option('body_style');
        $theme_skin = themerex_esc(themerex_get_custom_option('theme_skin'));
        $article_style = themerex_get_custom_option('article_style');
        $blog_style = themerex_get_custom_option(is_singular() && !themerex_get_global('blog_streampage') ? 'single_style' : 'blog_style');
        $top_panel_position = themerex_get_custom_option('top_panel_position');
        $video_bg_show  = themerex_get_custom_option('show_video_bg')=='yes' && (themerex_get_custom_option('video_bg_youtube_code')!='' || themerex_get_custom_option('video_bg_url')!='');


        $classes[] = 'themerex_body body_style_' . esc_attr($body_style);
        $classes[] = ' body_' . (themerex_get_custom_option('body_filled')=='yes' ? 'filled' : 'transparent');
        $classes[] =  ' theme_skin_' . esc_attr($theme_skin);
        $classes[] =  ' article_style_' . esc_attr($article_style);
        $classes[] =  ' layout_' . esc_attr($blog_style);
        $classes[] =  ' template_' . esc_attr(themerex_get_template_name($blog_style));
        $classes[] =  (!themerex_param_is_off($top_panel_position) ? ' top_panel_show top_panel_' . esc_attr($top_panel_position) : 'top_panel_hide');
        $classes[] =  ' ' . esc_attr(themerex_get_sidebar_class());
        $classes[] =  ($video_bg_show ? ' video_bg_show' : '');
        return $classes;
    }
}

// Add page meta to the head
if (!function_exists('themerex_head_add_page_meta')) {
    function themerex_head_add_page_meta() {
        ?>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="//gmpg.org/xfn/11">
        <?php
    }
}

// Add/Remove theme nav menus
if ( !function_exists( 'themerex_add_theme_menus' ) ) {
	function themerex_add_theme_menus($menus) {
		return $menus;
	}
}


// Add theme specific widgetized areas
if ( !function_exists( 'themerex_add_theme_sidebars' ) ) {
	function themerex_add_theme_sidebars($sidebars=array()) {
		if (is_array($sidebars)) {
			$theme_sidebars = array(
				'sidebar_main'		=> esc_html__( 'Main Sidebar', 'wineshop' ),
				'sidebar_outer'		=> esc_html__( 'Outer Sidebar', 'wineshop' ),
				'sidebar_footer'	=> esc_html__( 'Footer Sidebar', 'wineshop' ),
				'language_switcher'	=> esc_html__( 'Language Switcher Sidebar', 'wineshop' )
			);
			if (function_exists('themerex_exists_woocommerce') && themerex_exists_woocommerce()) {
				$theme_sidebars['sidebar_cart']  = esc_html__( 'WooCommerce Cart Sidebar', 'wineshop' );
			}
			$sidebars = array_merge($theme_sidebars, $sidebars);
		}
		return $sidebars;
	}
}


// Add theme required plugins
if ( !function_exists( 'themerex_add_required_plugins' ) ) {
    add_filter( 'themerex_filter_required_plugins',		'themerex_add_required_plugins' );
    function themerex_add_required_plugins($plugins) {
        $plugins[] = array(
            'name' 		=> esc_html__('WineShop Utilities', 'wineshop'),
            'version'	=> '3.2.1',					// Minimal required version
            'slug' 		=> 'trx_utils',
            'source'	=> themerex_get_file_dir('plugins/install/trx_utils.zip'),
            'required' 	=> true
        );
        $plugins[] = array(
            'name' 		=> esc_html__('Contact Form 7', 'wineshop'),
            'slug' 		=> 'contact-form-7',
            'required' 	=> false
        );
        return $plugins;
    }
}

//------------------------------------------------------------------------
// One-click import support
//------------------------------------------------------------------------

// Set theme specific importer options
if ( ! function_exists( 'themerex_importer_set_options' ) ) {
    add_filter( 'trx_utils_filter_importer_options', 'themerex_importer_set_options', 9 );
    function themerex_importer_set_options( $options=array() ) {
        if ( is_array( $options ) ) {
            // Save or not installer's messages to the log-file
            $options['debug'] = false;

            $wpml_slug = themerex_exists_wpml() ? '-wpml' : '';


            // Prepare demo data
            if ( is_dir( THEMEREX_THEME_PATH . 'demo/' ) ) {
                $options['demo_url'] = THEMEREX_THEME_PATH . 'demo/';
            }

            else {
                $options['demo_url'] = esc_url( themerex_get_protocol().'://demofiles.themerex.net/wineshop' . $wpml_slug ); // Demo-site domain
            }

            // Required plugins
            $options['required_plugins'] =  array(
                'js_composer',
                'revslider',
                'woocommerce',
                'the-events-calendar',
                'wp-gdpr-compliance',
                'contact-form-7',
                'sitepress-multilingual-cms'
            );

            $options['theme_slug'] = 'themerex';

            // Set number of thumbnails to regenerate when its imported (if demo data was zipped without cropped images)
            // Set 0 to prevent regenerate thumbnails (if demo data archive is already contain cropped images)
            $options['regenerate_thumbnails'] = 3;
            // Default demo
            $options['files']['default']['title'] = esc_html__( 'WineShop Demo', 'wineshop' );
            $options['files']['default']['domain_dev'] = esc_url(themerex_get_protocol().'://wineshop.upd.themerex.net'); // Developers domain
            $options['files']['default']['domain_demo']= esc_url(themerex_get_protocol().'://wineshop.themerex.net'); // Demo-site domain

        }
        return $options;
    }
}


/* Include framework core files
------------------------------------------------------------------- */
// If now is WP Heartbeat call - skip loading theme core files
if (!isset($_POST['action']) || $_POST['action']!="heartbeat") {
	require_once get_template_directory().'/fw/loader.php';
}


// Add theme required plugins
if ( !function_exists( 'themerex_add_trx_utils' ) ) {
    add_filter( 'trx_utils_active', 'themerex_add_trx_utils' );
    function themerex_add_trx_utils($enable=true) {
        return true;
    }
}

// Return text for the Privacy Policy checkbox
if ( ! function_exists('themerex_get_privacy_text' ) ) {
    function themerex_get_privacy_text() {
        $page = get_option( 'wp_page_for_privacy_policy' );
        $privacy_text = themerex_get_theme_option( 'privacy_text' );
        return apply_filters( 'themerex_filter_privacy_text', wp_kses_post(
                $privacy_text
                . ( ! empty( $page ) && ! empty( $privacy_text )
                    // Translators: Add url to the Privacy Policy page
                    ? ' ' . sprintf( __( 'For further details on handling user data, see our %s', 'wineshop' ),
                        '<a href="' . esc_url( get_permalink( $page ) ) . '" target="_blank">'
                        . __( 'Privacy Policy', 'wineshop' )
                        . '</a>' )
                    : ''
                )
            )
        );
    }
}
// Return text for the "I agree ..." checkbox
if ( ! function_exists( 'themerex_trx_utils_privacy_text' ) ) {
    add_filter( 'trx_utils_filter_privacy_text', 'themerex_trx_utils_privacy_text' );
    function themerex_trx_utils_privacy_text( $text='' ) {
        return themerex_get_privacy_text();
    }
}

// Add class trx_utils_activated
if(!function_exists('themerex_add_body_class')) {
    if(!function_exists ( 'themerex_require_data')){
        add_filter( 'body_class', 'themerex_add_body_class' );
        function themerex_add_body_class($classes){
            $classes[] = 'default_header';
            return $classes;
        }
    }
}
?>