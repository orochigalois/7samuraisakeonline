<?php
/*
Plugin Name: ThemeREX Utilities
Plugin URI: http://themerex.net
Description: Utils for files, directories, post type and taxonomies manipulations
Version: 3.2.1
Author: ThemeREX
Author URI: http://themerex.net
*/

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Current version
if ( ! defined( 'TRX_UTILS_VERSION' ) ) {
	define( 'TRX_UTILS_VERSION', '3.2.1' );
}


if (!defined('TRX_UTILS_PLUGIN_DIR'))	define('TRX_UTILS_PLUGIN_DIR', plugin_dir_path(__FILE__));
if (!defined('TRX_UTILS_PLUGIN_URL'))	define('TRX_UTILS_PLUGIN_URL', plugin_dir_url(__FILE__));
if (!defined('TRX_UTILS_PLUGIN_BASE'))	define('TRX_UTILS_PLUGIN_BASE',dirname(plugin_basename(__FILE__)));



global $TRX_UTILS_STORAGE;
$TRX_UTILS_STORAGE = array(
	// Plugin's location and name
	'plugin_dir' => plugin_dir_path(__FILE__),
	'plugin_url' => plugin_dir_url(__FILE__),
	'plugin_base'=> explode('/', plugin_basename(__FILE__)),
    'plugin_active' => false,
	// Custom post types and taxonomies
	'register_taxonomies' => array(),
	'register_post_types' => array()
);


// Plugin activate hook
if (!function_exists('trx_utils_activate')) {
	register_activation_hook(__FILE__, 'trx_utils_activate');
	function trx_utils_activate() {
		update_option('trx_utils_just_activated', 'yes');
	}
}



if ( !function_exists( 'trx_utils_load_plugin_textdomain' ) ) {
    add_action( 'plugins_loaded', 'trx_utils_load_plugin_textdomain');
    function trx_utils_load_plugin_textdomain() {
        static $loaded = false;
        if ( $loaded ) return true;
        $domain = 'trx_utils';
        if ( is_textdomain_loaded( $domain ) && !is_a( $GLOBALS['l10n'][ $domain ], 'NOOP_Translations' ) ) return true;
        $loaded = true;
        load_plugin_textdomain( $domain, false, TRX_UTILS_PLUGIN_BASE . '/languages' );
    }
}


// Plugin init
if (!function_exists('trx_utils_setup')) {
	add_action( 'init', 'trx_utils_setup' );
	function trx_utils_setup() {
		global $TRX_UTILS_STORAGE;

		// Load translation files

		
		if (count($TRX_UTILS_STORAGE['register_taxonomies']) > 0) {
			foreach ($TRX_UTILS_STORAGE['register_taxonomies'] as $name=>$args) {
				do_action('trx_utils_custom_taxonomy', $name, $args);
			}
		}
		
		if (count($TRX_UTILS_STORAGE['register_post_types']) > 0) {
			foreach ($TRX_UTILS_STORAGE['register_post_types'] as $name=>$args) {
				do_action('trx_utils_custom_post_type', $name, $args);
			}
		}

		// Check if this is first run
		if (get_option('trx_utils_just_activated')=='yes') {
			update_option('trx_utils_just_activated', 'no');
			flush_rewrite_rules();			
		}

	}
}



/* Support for the custom post types and taxonomies
------------------------------------------------------ */

// Register theme required types and taxes
if (!function_exists('trx_utils_theme_support')) {	
	function trx_utils_theme_support($type, $name, $args=false) {
		global $TRX_UTILS_STORAGE;
		if ($type == 'taxonomy')
			$TRX_UTILS_STORAGE['register_taxonomies'][$name] = $args;
		else
			$TRX_UTILS_STORAGE['register_post_types'][$name] = $args;
	}
}
if (!function_exists('trx_utils_theme_support_pt')) {	
	function trx_utils_theme_support_pt($name, $args=false) {
		global $TRX_UTILS_STORAGE;
		$TRX_UTILS_STORAGE['register_post_types'][$name] = $args;
	}
}
if (!function_exists('trx_utils_theme_support_tx')) {	
	function trx_utils_theme_support_tx($name, $args=false) {
		global $TRX_UTILS_STORAGE;
		$TRX_UTILS_STORAGE['register_taxonomies'][$name] = $args;
	}
}

// Add rewrite rules for custom post type
if (!function_exists('trx_utils_add_rewrite_rules')) {	
	function trx_utils_add_rewrite_rules($name) {
	    add_rewrite_rule(trim($name).'/?$', 'index.php?post_type='.trim($name), 'top');
//	    add_rewrite_rule(trim($name).'/([^/]+)/?$', 'index.php?'.trim($name).'=$matches[1]', 'top');
//		add_rewrite_tag('%'.trim($name).'%', '([^&]+)');
	}
}

// Include supported post types and taxonomies
require_once 'api/api.php';
require_once 'includes/plugin.files.php';
require_once 'includes/plugin.debug.php';
require_once 'includes/plugin.html.php';
require_once 'includes/plugin.users.php';
require_once 'includes/support.attachment.php';
require_once 'includes/support.clients.php';
require_once 'includes/support.courses.php';
require_once 'includes/support.matches.php';
require_once 'includes/support.menuitems.php';
require_once 'includes/support.services.php';
require_once 'includes/support.team.php';
require_once 'includes/support.testimonials.php';
require_once 'includes/core.socials.php';
require_once 'includes/core.courses.php';
require_once 'includes/plugin.wp.php';
require_once 'includes/core.reviews.php';


/* Load plugin's translation files
------------------------------------------------------------------- */
if ( !function_exists( 'trx_utils_load_plugin_textdomain' ) ) {
	function trx_utils_load_plugin_textdomain($domain='trx_utils') {
		if ( is_textdomain_loaded( $domain ) && !is_a( $GLOBALS['l10n'][ $domain ], 'NOOP_Translations' ) ) return true;
		return load_plugin_textdomain( $domain, false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}
}


/* Load required styles and scripts for the frontend
------------------------------------------------------ */
if ( !function_exists( 'trx_utils_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_utils_load_scripts_front');
	function trx_utils_load_scripts_front() {
		
		trx_utils_enqueue_script( 'trx_utils', trx_utils_get_file_url('js/trx_utils.js'), array('jquery'), null, true );

		// Add variables into JS
		wp_localize_script( 'trx_utils', 'TRX_UTILS_STORAGE', apply_filters('trx_utils_localize_script', array(
			// AJAX parameters
			'ajax_url'	=> esc_url(admin_url('admin-ajax.php')),
			'ajax_nonce'=> esc_attr(wp_create_nonce(admin_url('admin-ajax.php'))),
			// Site base url
			'site_url'	=> esc_url(get_site_url()),
			// User logged in
			'user_logged_in'=> is_user_logged_in() ? 1 : 0,
			// E-mail mask to validate forms
			'email_mask' => '^([a-zA-Z0-9_\\-]+\\.)*[a-zA-Z0-9_\\-]+@[a-z0-9_\\-]+(\\.[a-z0-9_\\-]+)*\\.[a-z]{2,6}$',
			// JS Messages
			'msg_ajax_error'			=> addslashes(esc_html__('Invalid server answer!', 'trx_utils')),
			'msg_error_global'			=> addslashes(esc_html__('Invalid field\'s value!', 'trx_utils')),
			'msg_name_empty'			=> addslashes(esc_html__("The name can't be empty", 'trx_utils')),
			'msg_email_empty'			=> addslashes(esc_html__('Too short (or empty) email address', 'trx_utils')),
			'msg_email_not_valid'		=> addslashes(esc_html__('Invalid email address', 'trx_utils')),
			'msg_text_empty'			=> addslashes(esc_html__("The message text can't be empty", 'trx_utils')),
			'msg_send_complete'			=> addslashes(esc_html__("Send message complete!", 'trx_utils')),
			'msg_send_error'			=> addslashes(esc_html__('Transmit failed!', 'trx_utils')),
			) )
		);
	}
}

if (is_admin()) {
    require_once trx_utils_get_file_dir('tools/emailer/emailer.php');
    require_once trx_utils_get_file_dir('tools/po_composer/po_composer.php');
}

/* Shortcodes
------------------------------------------------------ */

// Register theme required shortcodes
if (!function_exists('trx_utils_require_shortcode')) {	
	function trx_utils_require_shortcode($name, $callback) {
		add_shortcode($name, $callback);
	}
}



/* PHP settings
------------------------------------------------------ */

// Change memory limit
if (!function_exists('trx_utils_set_memory')) {	
	function trx_utils_set_memory($value) {
		@ini_set('memory_limit', $value);
	}
}



/* Twitter API
------------------------------------------------------ */
if (!function_exists('trx_utils_twitter_acquire_data')) {
	function trx_utils_twitter_acquire_data($cfg) {
		if (empty($cfg['mode'])) $cfg['mode'] = 'user_timeline';
		$data = get_transient("twitter_data_".($cfg['mode']));
		if (!$data) {
			require_once( plugin_dir_path( __FILE__ ) . 'lib/tmhOAuth/tmhOAuth.php' );
			$tmhOAuth = new tmhOAuth(array(
				'consumer_key'    => $cfg['consumer_key'],
				'consumer_secret' => $cfg['consumer_secret'],
				'token'           => $cfg['token'],
				'secret'          => $cfg['secret']
			));
			$code = $tmhOAuth->user_request(array(
				'url' => $tmhOAuth->url(trx_utils_twitter_mode_url($cfg['mode']))
			));
			if ($code == 200) {
				$data = json_decode($tmhOAuth->response['response'], true);
				if (isset($data['status'])) {
					$code = $tmhOAuth->user_request(array(
						'url' => $tmhOAuth->url(trx_utils_twitter_mode_url($cfg['oembed'])),
						'params' => array(
							'id' => $data['status']['id_str']
						)
					));
					if ($code == 200)
						$data = json_decode($tmhOAuth->response['response'], true);
				}
				set_transient("twitter_data_".($cfg['mode']), $data, 60*60);
			}
		} else if (!is_array($data) && substr($data, 0, 2)=='a:') {
			$data = unserialize($data);
		}
		return $data;
	}
}

if (!function_exists('trx_utils_twitter_mode_url')) {
	function trx_utils_twitter_mode_url($mode) {
		$url = '/1.1/statuses/';
		if ($mode == 'user_timeline')
			$url .= $mode;
		else if ($mode == 'home_timeline')
			$url .= $mode;
		return $url;
	}
}



/* LESS compilers
------------------------------------------------------ */

// Compile less-files
if (!function_exists('trx_utils_less_compiler')) {	
	function trx_utils_less_compiler($list, $opt) {

		$success = true;

		// Load and create LESS Parser
		if ($opt['compiler'] == 'lessc') {
			// 1: Compiler Lessc
			require_once( plugin_dir_path( __FILE__ ) . 'lib/lessc/lessc.inc.php' );
		} else {
			// 2: Compiler Less
			require_once( plugin_dir_path( __FILE__ ) . 'lib/less/Less.php' );
		}

		foreach($list as $file) {
			if (empty($file) || !file_exists($file)) continue;
			$file_css = substr_replace($file , 'css', strrpos($file , '.') + 1);
				
			// Check if time of .css file after .less - skip current .less
			if (!empty($opt['check_time']) && file_exists($file_css)) {
				$css_time = filemtime($file_css);
				if ($css_time >= filemtime($file) && ($opt['utils_time']==0 || $css_time > $opt['utils_time'])) continue;
			}
				
			// Compile current .less file
			try {
				// Create Parser
				if ($opt['compiler'] == 'lessc') {
					$parser = new lessc;
					if (!empty($opt['import'])) $parser->setImportDir($opt['import']);
					//$parser->registerFunction("replace", "trx_utils_less_func_replace");
					if ($opt['compressed']) $parser->setFormatter("compressed");
				} else {
					if ($opt['compressed'])
						$args = array('compress' => true);
					else {
						$args = array('compress' => false);
						if ($opt['map'] != 'no') {
							$args['sourceMap'] = true;
							if ($opt['map'] == 'external') {
								$args['sourceMapWriteTo'] = $file.'.map';
								$args['sourceMapURL'] = str_replace(
									array(get_template_directory(), get_stylesheet_directory()),
									array(get_template_directory_uri(), get_stylesheet_directory_uri()),
									$file) . '.map';
							}
						}
					}
					$parser = new Less_Parser($args);
				}

				// Parse main file
				$css = '';

				if ($opt['map'] != 'no' || !empty($opt['parse_files'])) {
					
					if ($opt['map'] != 'no' || $opt['compiler'] == 'less') {
						// Parse main file
						$parser->parseFile( $file, '');
						// Parse less utils
						if (is_array($opt['utils']) && count($opt['utils']) > 0) {
							foreach($opt['utils'] as $utility) {
								$parser->parseFile( $utility, '');
							}
						}
						// Parse less vars (from Theme Options)
						if (!empty($opt['vars'])) {
							$parser->parse($opt['vars']);
						}
						// Get compiled CSS code
						$css = $parser->getCss();
						// Reset LESS engine
						$parser->Reset();
					} else {
						$css = $parser->compileFile($file);
					}

				} else if (($text = file_get_contents($file))!='') {
					$parts = $opt['separator'] != '' ? explode($opt['separator'], $text) : array($text);
					for ($i=0; $i<count($parts); $i++) {
						$text = $parts[$i]
							. (!empty($opt['utils']) ? $opt['utils'] : '')			// Add less utils
							. (!empty($opt['vars']) ? $opt['vars'] : '');			// Add less vars (from Theme Options)
						// Get compiled CSS code
						if ($opt['compiler']=='lessc')
							$css .= $parser->compile($text);
						else {
							$parser->parse($text);
							$css .= $parser->getCss();
							$parser->Reset();
						}
					}
					if ($css && $opt['compiler']=='lessc' && $opt['compressed']) {
						$css = trx_utils_minify_css($css);
					}
				}
				if ($css) {
					if ($opt['map']=='no') {
						// If it main theme style - append CSS after header comments
						if ($file == get_template_directory(). '/style.less') {
							// Append to the main Theme Style CSS
							$theme_css = file_get_contents( get_template_directory() . '/style.css' );
							$css = substr($theme_css, 0, strpos($theme_css, '*/')+2) . "\n\n" . $css;
						} else {
							$css =	"/*"
									. "\n"
									. __('Attention! Do not modify this .css-file!', 'trx_utils') 
									. "\n"
									. __('Please, make all necessary changes in the corresponding .less-file!', 'trx_utils')
									. "\n"
									. "*/"
									. "\n"
									. '@charset "utf-8";'
									. "\n\n"
									. $css;
						}
					}
					// Save compiled CSS
					file_put_contents( $file_css, $css);
				}
			} catch (Exception $e) {
				if (function_exists('dfl')) dfl($e->getMessage());
				$success = false;
			}
		}
		return $success;
	}
}


// Shortcodes init
if (!function_exists('trx_utils_sc_init')) {
    add_action( 'after_setup_theme', 'trx_utils_sc_init' );
    function trx_utils_sc_init() {
        global $TRX_UTILS_STORAGE;
        if ( !($TRX_UTILS_STORAGE['plugin_active'] = apply_filters('trx_utils_active', $TRX_UTILS_STORAGE['plugin_active'])) ) return;

        // Include shortcodes
        require_once trx_utils_get_file_dir('shortcodes/core.shortcodes.php');

        require_once trx_utils_get_file_dir('shortcodes/trx_basic/anchor.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/audio.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/blogger.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/br.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/call_to_action.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/chat.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/columns.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/content.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/form.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/googlemap.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/hide.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/image.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/infobox.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/line.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/list.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/price_block.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/quote.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/reviews.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/section.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/skills.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/slider.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/socials.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/table.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/title.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/twitter.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_basic/video.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_optional/accordion.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_optional/button.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_optional/countdown.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_optional/dropcaps.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_optional/emailer.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_optional/gap.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_optional/highlight.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_optional/icon.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_optional/number.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_optional/parallax.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_optional/popup.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_optional/price.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_optional/search.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_optional/tabs.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_optional/toggles.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_optional/tooltip.php');
        require_once trx_utils_get_file_dir('shortcodes/trx_optional/zoom.php');
    }
}



// Widgets init
if (!function_exists('trx_utils_setup_widgets')) {
    add_action( 'widgets_init', 'trx_utils_setup_widgets', 9 );

    function trx_utils_setup_widgets() {
        global $TRX_UTILS_STORAGE;
        if ( !($TRX_UTILS_STORAGE['plugin_active'] = apply_filters('trx_utils_active', $TRX_UTILS_STORAGE['plugin_active'])) ) return;

        // Include widgets
        require_once trx_utils_get_file_dir('widgets/advert.php');
        require_once trx_utils_get_file_dir('widgets/calendar.php');
        require_once trx_utils_get_file_dir('widgets/categories.php');
        require_once trx_utils_get_file_dir('widgets/flickr.php');
        require_once trx_utils_get_file_dir('widgets/popular_posts.php');
        require_once trx_utils_get_file_dir('widgets/recent_posts.php');
        require_once trx_utils_get_file_dir('widgets/recent_reviews.php');
        require_once trx_utils_get_file_dir('widgets/socials.php');
        require_once trx_utils_get_file_dir('widgets/top10.php');
        require_once trx_utils_get_file_dir('widgets/twitter.php');
        require_once trx_utils_get_file_dir('widgets/qrcode/qrcode.php');
    }
}


// Demo data import/export
if ( file_exists( TRX_UTILS_PLUGIN_DIR . 'importer/importer.php' ) ) {
    require_once TRX_UTILS_PLUGIN_DIR . 'importer/importer.php';
}


/* Support for meta boxes
--------------------------------------------------- */
if (!function_exists('trx_utils_meta_box_add')) {
    add_action('add_meta_boxes', 'trx_utils_meta_box_add');
    function trx_utils_meta_box_add() {
        // Custom theme-specific meta-boxes
        $boxes = apply_filters('trx_utils_filter_override_options', array());
        if (is_array($boxes)) {
            foreach ($boxes as $box) {
                $box = array_merge(array('id' => '',
                    'title' => '',
                    'callback' => '',
                    'page' => null,        // screen
                    'context' => 'advanced',
                    'priority' => 'default',
                    'callbacks' => null
                ),
                    $box);
                add_meta_box($box['id'], $box['title'], $box['callback'], $box['page'], $box['context'], $box['priority'], $box['callbacks']);
            }
        }
    }
}

// Return text for the Privacy Policy checkbox
if (!function_exists('trx_utils_get_privacy_text')) {
    function trx_utils_get_privacy_text() {
        $page = get_option('wp_page_for_privacy_policy');
        return apply_filters( 'trx_utils_filter_privacy_text', wp_kses_post(
                __( 'I agree that my submitted data is being collected and stored.', 'trx_utils' )
                . ( '' != $page
                    // Translators: Add url to the Privacy Policy page
                    ? ' ' . sprintf(__('For further details on handling user data, see our %s', 'trx_utils'),
                        '<a href="' . esc_url(get_permalink($page)) . '" target="_blank">'
                        . __('Privacy Policy', 'trx_utils')
                        . '</a>')
                    : ''
                )
            )
        );
    }
}

if (!function_exists('themerex_strtoproper')) {
	function themerex_strtoproper($text) {
		$rez = ''; $last = ' ';
		for ($i=0; $i<themerex_strlen($text); $i++) {
			$ch = themerex_substr($text, $i, 1);
			$rez .= themerex_strpos(' .,:;?!()[]{}+=', $last)!==false ? themerex_strtoupper($ch) : themerex_strtolower($ch);
			$last = $ch;
		}
		return $rez;
	}
}

// Add image dimensions with layout name
if (!function_exists('themerex_add_thumb_sizes')) {
	function themerex_add_thumb_sizes($sizes) {
		global $THEMEREX_GLOBALS;
		if (!isset($sizes['h_crop']))		$sizes['h_crop'] =  isset($sizes['h']) ? $sizes['h'] : null;

		if (empty($sizes['thumb_title']))	$sizes['thumb_title'] = themerex_strtoproper($sizes['layout']);
		$thumb_slug = themerex_get_slug($sizes['thumb_title']);
		if (empty($THEMEREX_GLOBALS['thumb_sizes'][$thumb_slug])) {
			if (empty($THEMEREX_GLOBALS['thumb_sizes'])) $THEMEREX_GLOBALS['thumb_sizes'] = array();
			$THEMEREX_GLOBALS['thumb_sizes'][$thumb_slug] = $sizes;
			add_image_size( 'themerex-'.$thumb_slug, $sizes['w'], $sizes['h'], $sizes['h']!=null );
			if ($sizes['h']!=$sizes['h_crop']) {
				add_image_size( 'themerex-'.$thumb_slug.'_crop', $sizes['w'], $sizes['h_crop'], true );
			}
		}
	}
}




if(!function_exists('trx_utils_filter_options')){
    add_filter('themerex_filter_options', 'trx_utils_filter_options');
    function trx_utils_filter_options($options){
        global $THEMEREX_GLOBALS;
        $custom_options = array(

            'info_other_3' => array(
                "title" => esc_html__('Additional CSS and HTML/JS code', 'wineshop'),
                "desc" => wp_kses( __('Put here your custom CSS and JS code', 'wineshop'), $THEMEREX_GLOBALS['allowed_tags'] ),
                "override" => "category,services_group,page,post",
                "type" => "info"
            ),

            'custom_css_html' => array(
                "title" => esc_html__('Use custom CSS/HTML/JS', 'wineshop'),
                "desc" => wp_kses( __('Do you want use custom HTML/CSS/JS code in your site? For example: custom styles, Google Analitics code, etc.', 'wineshop'), $THEMEREX_GLOBALS['allowed_tags'] ),
                "std" => "no",
                "options" => $THEMEREX_GLOBALS['options_params']['list_yes_no'],
                "type" => "switch"
            ),



            "gtm_code" => array(
                "title" => esc_html__('Google tags manager or Google analitics code',  'wineshop'),
                "desc" => wp_kses( __('Put here Google Tags Manager (GTM) code from your account: Google analitics, remarketing, etc. This code will be placed after open body tag.',  'wineshop'), $THEMEREX_GLOBALS['allowed_tags'] ),
                "dependency" => array(
                    'custom_css_html' => array('yes')
                ),
                "cols" => 80,
                "rows" => 20,
                "std" => "",
                "type" => "textarea"),

            "gtm_code2" => array(
                "title" => esc_html__('Google remarketing code',  'wineshop'),
                "desc" => wp_kses( __('Put here Google Remarketing code from your account. This code will be placed before close body tag.',  'wineshop'), $THEMEREX_GLOBALS['allowed_tags'] ),
                "dependency" => array(
                    'custom_css_html' => array('yes')
                ),
                "divider" => false,
                "cols" => 80,
                "rows" => 20,
                "std" => "",
                "type" => "textarea"),

            'custom_code' => array(
                "title" => esc_html__('Your custom HTML/JS code',  'wineshop'),
                "desc" => wp_kses( __('Put here your invisible html/js code: Google analitics, counters, etc',  'wineshop'), $THEMEREX_GLOBALS['allowed_tags'] ),
                "override" => "category,services_group,post,page",
                "dependency" => array(
                    'custom_css_html' => array('yes')
                ),
                "cols" => 80,
                "rows" => 20,
                "std" => "",
                "type" => "textarea"
            ),

            'custom_css' => array(
                "title" => esc_html__('Your custom CSS code',  'wineshop'),
                "desc" => wp_kses( __('Put here your css code to correct main theme styles',  'wineshop'), $THEMEREX_GLOBALS['allowed_tags'] ),
                "override" => "category,services_group,post,page",
                "dependency" => array(
                    'custom_css_html' => array('yes')
                ),
                "divider" => false,
                "cols" => 80,
                "rows" => 20,
                "std" => "",
                "type" => "textarea"
            ),

        );

        trx_utils_array_insert_after($options, 'fonts_subset', $custom_options);

        return $options;
    }
}


// Show additional fields in the user profile
if (!function_exists('themerex_add_fields_in_user_info')) {
    function themerex_add_fields_in_user_info( $user ) {
        ?>
        <h3><?php esc_html_e('User Position', 'wineshop'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="user_position"><?php esc_html_e('User position', 'wineshop'); ?>:</label></th>
                <td><input type="text" name="user_position" id="user_position" size="55" value="<?php echo esc_attr(get_the_author_meta('user_position', $user->ID)); ?>" />
                    <span class="description"><?php esc_html_e('Please, enter your position in the company', 'wineshop'); ?></span>
                </td>
            </tr>
        </table>

        <h3><?php esc_html_e('Social links', 'wineshop'); ?></h3>
        <table class="form-table">
            <?php
            $socials_type = themerex_get_theme_setting('socials_type');
            $social_list = themerex_get_theme_option('social_icons');
            if (is_array($social_list) && count($social_list) > 0) {
                foreach ($social_list as $soc) {
                    if ($socials_type == 'icons') {
                        $parts = explode('-', $soc['icon'], 2);
                        $sn = isset($parts[1]) ? $parts[1] : $sn;
                    } else {
                        $sn = basename($soc['icon']);
                        $sn = themerex_substr($sn, 0, themerex_strrpos($sn, '.'));
                        if (($pos=themerex_strrpos($sn, '_'))!==false)
                            $sn = themerex_substr($sn, 0, $pos);
                    }
                    if (!empty($sn)) {
                        ?>
                        <tr>
                            <th><label for="user_<?php echo esc_attr($sn); ?>"><?php themerex_show_layout(themerex_strtoproper($sn)); ?>:</label></th>
                            <td><input type="text" name="user_<?php echo esc_attr($sn); ?>" id="user_<?php echo esc_attr($sn); ?>" size="55" value="<?php echo esc_attr(get_the_author_meta('user_'.($sn), $user->ID)); ?>" />
                                <span class="description"><?php echo sprintf(esc_html__('Please, enter your %s link', 'wineshop'), themerex_strtoproper($sn)); ?></span>
                            </td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
        </table>
        <?php
    }
}

// Save / update additional fields
if (!function_exists('themerex_save_fields_in_user_info')) {
    function themerex_save_fields_in_user_info( $user_id ) {
        if ( !current_user_can( 'edit_user', $user_id ) )
            return false;
        update_user_meta( $user_id, 'user_position', $_POST['user_position'] );
        $socials_type = themerex_get_theme_setting('socials_type');
        $social_list = themerex_get_theme_option('social_icons');
        if (is_array($social_list) && count($social_list) > 0) {
            foreach ($social_list as $soc) {
                if ($socials_type == 'icons') {
                    $parts = explode('-', $soc['icon'], 2);
                    $sn = isset($parts[1]) ? $parts[1] : $sn;
                } else {
                    $sn = basename($soc['icon']);
                    $sn = themerex_substr($sn, 0, themerex_strrpos($sn, '.'));
                    if (($pos=themerex_strrpos($sn, '_'))!==false)
                        $sn = themerex_substr($sn, 0, $pos);
                }
                update_user_meta( $user_id, 'user_'.($sn), $_POST['user_'.($sn)] );
            }
        }
    }
}

// Google map
if(function_exists('themerex_get_custom_option')){
    if ( themerex_get_custom_option('show_googlemap')=='yes' ) {
        $api_key = themerex_get_theme_option('api_google');
        wp_enqueue_script( 'googlemap', themerex_get_protocol().'://maps.google.com/maps/api/js'.($api_key ? '?key='.$api_key : ''), array(), null, true );
        wp_enqueue_script( 'googlemap-script', trx_utils_get_file_url('js/core.googlemap.js'), array(), null, true );
    }
}
?>