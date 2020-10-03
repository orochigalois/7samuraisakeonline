<?php
/**
 * ThemeREX Framework
 *
 * @package themerex
 * @since themerex 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Framework directory path from theme root
if ( ! defined( 'THEMEREX_FW_DIR' ) )		define( 'THEMEREX_FW_DIR', 'fw/' );

if ( ! defined( 'THEMEREX_THEME_PATH' ) )	define( 'THEMEREX_THEME_PATH',	trailingslashit( get_template_directory() ) );
if ( ! defined( 'THEMEREX_FW_PATH' ) )		define( 'THEMEREX_FW_PATH',		THEMEREX_THEME_PATH . THEMEREX_FW_DIR );

// Theme timing
if ( ! defined( 'THEMEREX_START_TIME' ) )	define( 'THEMEREX_START_TIME', microtime());			// Framework start time
if ( ! defined( 'THEMEREX_START_MEMORY' ) )	define( 'THEMEREX_START_MEMORY', memory_get_usage());	// Memory usage before core loading

// Global variables storage
global $THEMEREX_GLOBALS;
$THEMEREX_GLOBALS = array(
	'theme_slug'	=> 'themerex',	// Theme slug (used as prefix for theme's functions, text domain, global variables, etc.)
	'options_prefix'=> 'themerex',	// Prefix for the theme options in the postmeta and wp options
	'page_template'	=> '',			// Storage for current page template name (used in the inheritance system)
    'allowed_tags'	=> array(		// Allowed tags list (with attributes) in translations
    	'b' => array(),
    	'strong' => array(),
    	'i' => array(),
    	'em' => array(),
    	'u' => array(),
    	'a' => array(
			'href' => array(),
			'title' => array(),
			'target' => array(),
			'id' => array(),
			'class' => array()
		),
    	'span' => array(
			'id' => array(),
			'class' => array()
		)
    )	
);

/* Theme setup section
-------------------------------------------------------------------- */
if ( !function_exists( 'themerex_loader_theme_setup' ) ) {
	add_action( 'after_setup_theme', 'themerex_loader_theme_setup', 20 );
	function themerex_loader_theme_setup() {
		
		// Init admin url and nonce
		global $THEMEREX_GLOBALS;
		$THEMEREX_GLOBALS['admin_url']	= get_admin_url();
		$THEMEREX_GLOBALS['admin_nonce']= wp_create_nonce(get_admin_url());
		$THEMEREX_GLOBALS['ajax_url']	= admin_url('admin-ajax.php');
		$THEMEREX_GLOBALS['ajax_nonce']	= wp_create_nonce(admin_url('admin-ajax.php'));

		// Before init theme
		do_action('themerex_action_before_init_theme');

		// Load current values for main theme options
		themerex_load_main_options();

		// Theme core init - only for admin side. In frontend it called from header.php
		if ( is_admin() ) {
			themerex_core_init_theme();
		}
	}
}


/* Include core parts
------------------------------------------------------------------------ */

// Manual load important libraries before load all rest files
// core.strings must be first - we use themerex_str...() in the themerex_get_file_dir()
require_once (file_exists(get_stylesheet_directory().('/'.THEMEREX_FW_DIR).'core/core.strings.php') ? get_stylesheet_directory() : get_template_directory()).('/'.THEMEREX_FW_DIR).'core/core.strings.php';
// core.files must be first - we use themerex_get_file_dir() to include all rest parts
require_once (file_exists(get_stylesheet_directory().('/'.THEMEREX_FW_DIR).'core/core.files.php') ? get_stylesheet_directory() : get_template_directory()).('/'.THEMEREX_FW_DIR).'core/core.files.php';



// Include custom theme files
themerex_autoload_folder( 'includes' );

// Include core files
themerex_autoload_folder( 'core' );

// Include theme-specific plugins and post types
themerex_autoload_folder( 'plugins' );

// Include theme templates
themerex_autoload_folder( 'templates' );


?>