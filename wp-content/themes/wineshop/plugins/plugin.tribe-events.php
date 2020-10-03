<?php
/* Tribe Events (TE) support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('themerex_tribe_events_theme_setup')) {
	add_action( 'themerex_action_before_init_theme', 'themerex_tribe_events_theme_setup', 1 );
	function themerex_tribe_events_theme_setup() {
		if (themerex_exists_tribe_events()) {

			// Detect current page type, taxonomy and title (for custom post_types use priority < 10 to fire it handles early, than for standard post types)
			add_filter('themerex_filter_get_blog_type',					'themerex_tribe_events_get_blog_type', 9, 2);
			add_filter('themerex_filter_get_blog_title',				'themerex_tribe_events_get_blog_title', 9, 2);
			add_filter('themerex_filter_get_current_taxonomy',			'themerex_tribe_events_get_current_taxonomy', 9, 2);
			add_filter('themerex_filter_is_taxonomy',					'themerex_tribe_events_is_taxonomy', 9, 2);
			add_filter('themerex_filter_get_stream_page_title',			'themerex_tribe_events_get_stream_page_title', 9, 2);
			add_filter('themerex_filter_get_stream_page_link',			'themerex_tribe_events_get_stream_page_link', 9, 2);
			add_filter('themerex_filter_get_stream_page_id',			'themerex_tribe_events_get_stream_page_id', 9, 2);
			add_filter('themerex_filter_get_period_links',				'themerex_tribe_events_get_period_links', 9, 3);
			add_filter('themerex_filter_detect_inheritance_key',		'themerex_tribe_events_detect_inheritance_key', 9, 1);

			add_action('themerex_action_add_styles',					'themerex_tribe_events_frontend_scripts' );

			add_filter('themerex_filter_list_post_types', 				'themerex_tribe_events_list_post_types', 10, 1);
			add_filter('themerex_filter_post_date',	 					'themerex_tribe_events_post_date', 9, 3);

			add_filter('themerex_filter_add_sort_order', 				'themerex_tribe_events_add_sort_order', 10, 3);

			// Advanced Calendar filters
			add_filter('themerex_filter_calendar_get_month_link',		'themerex_tribe_events_calendar_get_month_link', 9, 2);
			add_filter('themerex_filter_calendar_get_prev_month',		'themerex_tribe_events_calendar_get_prev_month', 9, 2);
			add_filter('themerex_filter_calendar_get_next_month',		'themerex_tribe_events_calendar_get_next_month', 9, 2);
			add_filter('themerex_filter_calendar_get_curr_month_posts',	'themerex_tribe_events_calendar_get_curr_month_posts', 9, 2);
			
			 // Add Google API key to the map's link
			add_filter('tribe_events_google_maps_api',     				'themerex_tribe_events_google_maps_api');

			// Add query params to show events in the blog
			if (themerex_get_theme_option('show_tribe_events_in_blog')=='yes') {
				add_filter( 'posts_join',								'themerex_tribe_events_posts_join', 10, 2 );
				add_filter( 'getarchives_join',							'themerex_tribe_events_getarchives_join', 10, 2 );
				add_filter( 'posts_where',								'themerex_tribe_events_posts_where', 10, 2 );
				add_filter( 'getarchives_where',						'themerex_tribe_events_getarchives_where', 10, 2 );
			}

			// Extra column for events lists
			if (themerex_get_theme_option('show_overriden_posts')=='yes') {
				add_filter('manage_edit-'.Tribe__Events__Main::POSTTYPE.'_columns',			'themerex_post_add_options_column', 9);
				add_filter('manage_'.Tribe__Events__Main::POSTTYPE.'_posts_custom_column',	'themerex_post_fill_options_column', 9, 2);
			}

			// Register shortcode [trx_events] in the list
			add_action('themerex_action_shortcodes_list',				'themerex_tribe_events_reg_shortcodes');
			if (function_exists('themerex_exists_visual_composer') && themerex_exists_visual_composer())
				add_action('themerex_action_shortcodes_list_vc',		'themerex_tribe_events_reg_shortcodes_vc');


		}
		if (is_admin()) {
			add_filter( 'themerex_filter_importer_required_plugins',	'themerex_tribe_events_importer_required_plugins', 10, 2 );
			add_filter( 'themerex_filter_required_plugins',				'themerex_tribe_events_required_plugins' );
		}
	}
}

if ( !function_exists( 'themerex_tribe_events_settings_theme_setup2' ) ) {
	add_action( 'themerex_action_before_init_theme', 'themerex_tribe_events_settings_theme_setup2', 3 );
	function themerex_tribe_events_settings_theme_setup2() {
		if (themerex_exists_tribe_events()) {
			themerex_add_theme_inheritance( array('tribe_events' => array(
				'stream_template' => 'tribe-events/default-template',
				'single_template' => '',
				'taxonomy' => array(Tribe__Events__Main::TAXONOMY),
				'taxonomy_tags' => array(),
				'post_type' => array(
					Tribe__Events__Main::POSTTYPE,
					Tribe__Events__Main::VENUE_POST_TYPE,
					Tribe__Events__Main::ORGANIZER_POST_TYPE
				),
				'override' => 'post'
				) )
			);
	
			// Add Tribe Events specific options in the Theme Options
			global $THEMEREX_GLOBALS;
	
			themerex_array_insert_before($THEMEREX_GLOBALS['options'], 'partition_reviews', array(
			
				"partition_tribe_events" => array(
						"title" => esc_html__ ('Events', 'wineshop'),
						"icon" => "iconadmin-clock",
						"type" => "partition"),
			
				"info_tribe_events_1" => array(
						"title" => esc_html__ ('Events settings', 'wineshop'),
						"desc" => esc_html__ ('Set up events posts behaviour in the blog.', 'wineshop'),
						"type" => "info"),
			
				"show_tribe_events_in_blog" => array(
						"title" => esc_html__ ('Show events in the blog',  'wineshop'),
						"desc" => esc_html__ ("Show events in stream pages (blog, archives) or only in special pages", 'wineshop'),
						"divider" => false,
						"std" => "yes",
						"options" => $THEMEREX_GLOBALS['options_params']['list_yes_no'],
						"type" => "switch")
				)
			);	
		}
	}
}

// Check if Tribe Events installed and activated
if (!function_exists('themerex_exists_tribe_events')) {
	function themerex_exists_tribe_events() {
		return class_exists( 'Tribe__Events__Main' );
	}
}


// Return true, if current page is any TE page
if ( !function_exists( 'themerex_is_tribe_events_page' ) ) {
	function themerex_is_tribe_events_page() {
		$is = false;
		if (themerex_exists_tribe_events()) {
			global $THEMEREX_GLOBALS;
			$is = in_array($THEMEREX_GLOBALS['page_template'], array('tribe-events/default-template'));
			if (!$is) {
				if (empty($THEMEREX_GLOBALS['pre_query'])) {
					if (!is_search()) $is = tribe_is_event() || tribe_is_event_query() || tribe_is_event_category() || tribe_is_event_venue() || tribe_is_event_organizer();
				} else {
					$is = !empty($THEMEREX_GLOBALS['pre_query']->tribe_is_event)
							|| !empty($THEMEREX_GLOBALS['pre_query']->tribe_is_multi_posttype)
							|| !empty($THEMEREX_GLOBALS['pre_query']->tribe_is_event_category)
							|| !empty($THEMEREX_GLOBALS['pre_query']->tribe_is_event_venue)
							|| !empty($THEMEREX_GLOBALS['pre_query']->tribe_is_event_organizer)
							|| !empty($THEMEREX_GLOBALS['pre_query']->tribe_is_event_query)
							|| !empty($THEMEREX_GLOBALS['pre_query']->tribe_is_past);
				}
			}
		}
		return $is;
	}
}

// Filter to detect current page inheritance key
if ( !function_exists( 'themerex_tribe_events_detect_inheritance_key' ) ) {
	function themerex_tribe_events_detect_inheritance_key($key) {
		if (!empty($key)) return $key;
		return themerex_is_tribe_events_page() ? 'tribe_events' : '';
	}
}

// Filter to detect current page slug
if ( !function_exists( 'themerex_tribe_events_get_blog_type' ) ) {
	function themerex_tribe_events_get_blog_type($page, $query=null) {
		if (!empty($page)) return $page;
		if (!is_search() && themerex_is_tribe_events_page()) {
			if ( isset($query->query_vars['eventDisplay']) && $query->query_vars['eventDisplay']=='day') 			$page = 'tribe_day';
			else if ( isset($query->query_vars['eventDisplay']) && $query->query_vars['eventDisplay']=='month')	$page = 'tribe_month';
			else if (is_single())																									$page = 'tribe_event';
			else if (isset($query->tribe_is_event_venue) && $query->tribe_is_event_venue)			$page = 'tribe_venue';
			else if (isset($query->tribe_is_event_organizer) && $query->tribe_is_event_organizer)	$page = 'tribe_organizer';
			else if (isset($query->tribe_is_event_category) && $query->tribe_is_event_category)		$page = 'tribe_category';
			else if (is_tag())															$page = 'tribe_tag';
			else if (isset($query->query_vars['eventDisplay']) && $query->query_vars['eventDisplay']=='upcoming')					$page = 'tribe_list';
			else																													$page = 'tribe';
		}
		return $page;
	}
}

// Filter to detect current page title
if ( !function_exists( 'themerex_tribe_events_get_blog_title' ) ) {
	function themerex_tribe_events_get_blog_title($title, $page) {
		if (!empty($title)) return $title;
		if ( themerex_strpos($page, 'tribe')!==false ) {
			if ( $page == 'tribe_category' ) {
				$cat = get_term_by( 'slug', get_query_var( 'tribe_events_cat' ), 'tribe_events_cat', ARRAY_A);
				$title = $cat['name'];
			} else if ( $page == 'tribe_tag' ) {
				$title = sprintf( esc_html__( 'Tag: %s', 'wineshop' ), single_tag_title( '', false ) );
			} else if ( $page == 'tribe_venue' ) {
				$title = sprintf( esc_html__( 'Venue: %s', 'wineshop' ), tribe_get_venue());
			} else if ( $page == 'tribe_organizer' ) {
				$title = sprintf( esc_html__( 'Organizer: %s', 'wineshop' ), tribe_get_organizer());
			} else if ( $page == 'tribe_day' ) {
				$title = sprintf( esc_html__( 'Daily Events: %s', 'wineshop' ), date_i18n(tribe_get_date_format(true), strtotime(get_query_var( 'start_date' ))) );
			} else if ( $page == 'tribe_month' ) {
				$title = sprintf( esc_html__( 'Monthly Events: %s', 'wineshop' ), date_i18n(tribe_get_option('monthAndYearFormat', 'F Y' ), strtotime(tribe_get_month_view_date())));
			} else if ( $page == 'tribe_event' ) {
				$title = themerex_get_post_title();
			} else {
				$title = esc_html__( 'Tribe Events', 'wineshop' );
			}
		}
		return $title;
	}
}

// Filter to detect stream page title
if ( !function_exists( 'themerex_tribe_events_get_stream_page_title' ) ) {
	function themerex_tribe_events_get_stream_page_title($title, $page) {
		if (!empty($title)) return $title;
		if (themerex_strpos($page, 'tribe')!==false) {
			if (($page_id = themerex_tribe_events_get_stream_page_id(0, $page)) > 0)
				$title = themerex_get_post_title($page_id);
			else
				$title = esc_html__( 'All Events', 'wineshop');
		}
		return $title;
	}
}

// Filter to detect stream page ID
if ( !function_exists( 'themerex_tribe_events_get_stream_page_id' ) ) {
	function themerex_tribe_events_get_stream_page_id($id, $page) {
		if (!empty($id)) return $id;
		if (themerex_strpos($page, 'tribe')!==false) $id = themerex_get_template_page_id('tribe-events/default-template');
		return $id;
	}
}

// Filter to detect stream page URL
if ( !function_exists( 'themerex_tribe_events_get_stream_page_link' ) ) {
	function themerex_tribe_events_get_stream_page_link($url, $page) {
		if (!empty($url)) return $url;
		if (themerex_strpos($page, 'tribe')!==false) $url = tribe_get_events_link();
		return $url;
	}
}

// Filter to return breadcrumbs links to the parent period
if ( !function_exists( 'themerex_tribe_events_get_period_links' ) ) {
	function themerex_tribe_events_get_period_links($links, $page, $delimiter='') {
		if (!empty($links)) return $links;
		global $post;
		if ($page == 'tribe_day' && is_object($post))
			$links = '<a class="breadcrumbs_item cat_parent" href="' . esc_url(tribe_get_gridview_link(false)) . '">' . date_i18n(tribe_get_option('monthAndYearFormat', 'F Y' ), strtotime(tribe_get_month_view_date())) . '</a>';
		return $links;
	}
}

// Filter to detect current taxonomy
if ( !function_exists( 'themerex_tribe_events_get_current_taxonomy' ) ) {
	function themerex_tribe_events_get_current_taxonomy($tax, $page) {
		if (!empty($tax)) return $tax;
		if ( themerex_strpos($page, 'tribe')!==false ) {
			$tax = Tribe__Events__Main::TAXONOMY;
		}
		return $tax;
	}
}

// Return taxonomy name (slug) if current page is this taxonomy page
if ( !function_exists( 'themerex_tribe_events_is_taxonomy' ) ) {
	function themerex_tribe_events_is_taxonomy($tax, $query=null) {
		if (!empty($tax))
			return $tax;
		else
			return $query && isset($query->tribe_is_event_category) && $query->tribe_is_event_category || is_tax(Tribe__Events__Main::TAXONOMY) ? Tribe__Events__Main::TAXONOMY : '';
	}
}

// Add custom post type into list
if ( !function_exists( 'themerex_tribe_events_list_post_types' ) ) {
	function themerex_tribe_events_list_post_types($list) {
		if (themerex_get_theme_option('show_tribe_events_in_blog')=='yes') {
			$list['tribe_events'] = esc_html__('Events', 'wineshop');
	    }
		return $list;
	}
}



// Return previous month and year with published posts
if ( !function_exists( 'themerex_tribe_events_calendar_get_month_link' ) ) {
	function themerex_tribe_events_calendar_get_month_link($link, $opt) {
		if (!empty($opt['posts_types']) && in_array(Tribe__Events__Main::POSTTYPE, $opt['posts_types']) && count($opt['posts_types'])==1) {
			$events = Tribe__Events__Main::instance();
			$link = $events->getLink('month', ($opt['year']).'-'.($opt['month']), null);			
		}
		return $link;
	}
}

// Return previous month and year with published posts
if ( !function_exists( 'themerex_tribe_events_calendar_get_prev_month' ) ) {
	function themerex_tribe_events_calendar_get_prev_month($prev, $opt) {
		if (!empty($opt['posts_types']) && !in_array(Tribe__Events__Main::POSTTYPE, $opt['posts_types'])) return;
		if (!empty($prev['done']) && in_array(Tribe__Events__Main::POSTTYPE, $prev['done'])) return;
		$args = array(
			'suppress_filters' => true,
			'post_type' => Tribe__Events__Main::POSTTYPE,
			'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish',
			'posts_per_page' => 1,
			'ignore_sticky_posts' => true,
			'orderby' => 'meta_value',
			'meta_key' => '_EventStartDate',
			'order' => 'desc',
			'meta_query' => array(
				array(
					'key' => '_EventStartDate',
					'value' => ($opt['year']).'-'.($opt['month']).'-01',
					'compare' => '<',
					'type' => 'DATE'
				)
			)
		);
		$q = new WP_Query($args);
		$month = $year = 0;
		if ($q->have_posts()) {
			while ($q->have_posts()) { $q->the_post();
				$dt = strtotime(get_post_meta(get_the_ID(), '_EventStartDate', true));
				$year  = date('Y', $dt);
				$month = date('m', $dt);
			}
			wp_reset_postdata();
		}
		if (empty($prev) || ($year+$month > 0 && ($prev['year']+$prev['month']==0 || ($prev['year']).($prev['month']) < ($year).($month)))) {
			$prev['year'] = $year;
			$prev['month'] = $month;
		}
		if (empty($prev['done'])) $prev['done'] = array();
		$prev['done'][] = Tribe__Events__Main::POSTTYPE;
		return $prev;
	}
}

// Return next month and year with published posts
if ( !function_exists( 'themerex_tribe_events_calendar_get_next_month' ) ) {
	function themerex_tribe_events_calendar_get_next_month($next, $opt) {
		if (!empty($opt['posts_types']) && !in_array(Tribe__Events__Main::POSTTYPE, $opt['posts_types'])) return;
		if (!empty($next['done']) && in_array(Tribe__Events__Main::POSTTYPE, $next['done'])) return;
		$args = array(
			'suppress_filters' => true,
			'post_type' => Tribe__Events__Main::POSTTYPE,
			'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish',
			'posts_per_page' => 1,
			'orderby' => 'meta_value',
			'ignore_sticky_posts' => true,
			'meta_key' => '_EventStartDate',
			'order' => 'asc',
			'meta_query' => array(
				array(
					'key' => '_EventStartDate',
					'value' => ($opt['year']).'-'.($opt['month']).'-'.($opt['last_day']).' 23:59:59',
					'compare' => '>',
					'type' => 'DATE'
				)
			)
		);
		$q = new WP_Query($args);
		$month = $year = 0;
		if ($q->have_posts()) {
			while ($q->have_posts()) { $q->the_post();
				$dt = strtotime(get_post_meta(get_the_ID(), '_EventStartDate', true));
				$year  = date('Y', $dt);
				$month = date('m', $dt);
			}
			wp_reset_postdata();
		}
		if (empty($next) || ($year+$month > 0 && ($next['year']+$next['month'] ==0 || ($next['year']).($next['month']) > ($year).($month)))) {
			$next['year'] = $year;
			$next['month'] = $month;
		}
		if (empty($next['done'])) $next['done'] = array();
		$next['done'][] = Tribe__Events__Main::POSTTYPE;
		return $next;
	}
}

// Return current month published posts
if ( !function_exists( 'themerex_tribe_events_calendar_get_curr_month_posts' ) ) {
	function themerex_tribe_events_calendar_get_curr_month_posts($posts, $opt) {
		if (!empty($opt['posts_types']) && !in_array(Tribe__Events__Main::POSTTYPE, $opt['posts_types'])) return;
		if (!empty($posts['done']) && in_array(Tribe__Events__Main::POSTTYPE, $posts['done'])) return;
		$args = array(
			'suppress_filters' => true,
			'post_type' => Tribe__Events__Main::POSTTYPE,
			'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish',
			'posts_per_page' => -1,
			'ignore_sticky_posts' => true,
			'orderby' => 'meta_value',
			'meta_key' => '_EventStartDate',
			'order' => 'asc',
			'meta_query' => array(
				array(
					'key' => '_EventStartDate',
					'value' => array(($opt['year']).'-'.($opt['month']).'-01', ($opt['year']).'-'.($opt['month']).'-'.($opt['last_day']).' 23:59:59'),
					'compare' => 'BETWEEN',
					'type' => 'DATE'
				)
			)
		);
		$q = new WP_Query($args);
		if ($q->have_posts()) {
			if (empty($posts)) $posts = array();
			$events = Tribe__Events__Main::instance();
			while ($q->have_posts()) { $q->the_post();
				$dt = strtotime(get_post_meta(get_the_ID(), '_EventStartDate', true));
				$day = (int) date('d', $dt);
				$title = get_the_title();
				if (empty($posts[$day])) 
					$posts[$day] = array();
				if (empty($posts[$day]['link']) && count($opt['posts_types'])==1)
					$posts[$day]['link'] = $events->getLink('day', ($opt['year']).'-'.($opt['month']).'-'.($day), null);
				if (empty($posts[$day]['titles']))
					$posts[$day]['titles'] = $title;
				else
					$posts[$day]['titles'] = is_int($posts[$day]['titles']) ? $posts[$day]['titles']+1 : 2;
				if (empty($posts[$day]['posts'])) $posts[$day]['posts'] = array();
				$posts[$day]['posts'][] = array(
					'post_id' => get_the_ID(),
					'post_type' => get_post_type(),
					'post_date' => date(get_option('date_format'), $dt),
					'post_title' => $title,
					'post_link' => get_permalink()
				);
			}
			wp_reset_postdata();
		}
		if (empty($posts['done'])) $posts['done'] = array();
		$posts['done'][] = Tribe__Events__Main::POSTTYPE;
		return $posts;
	}
}



// Enqueue Tribe Events custom styles
if ( !function_exists( 'themerex_tribe_events_frontend_scripts' ) ) {
	function themerex_tribe_events_frontend_scripts() {
		global $wp_styles;
		$wp_styles->done[] = 'tribe-events-custom-jquery-styles';
        wp_enqueue_style( 'tribe-style',  themerex_get_file_url('css/tribe-style.css'), array(), null );
	}
}




// Before main content
if ( !function_exists( 'themerex_tribe_events_wrapper_start' ) ) {
	function themerex_tribe_events_wrapper_start($html) {
		return '
		<section class="post tribe_events_wrapper">
			<article class="post_content">
		' . ($html);
	}
}

// After main content
if ( !function_exists( 'themerex_tribe_events_wrapper_end' ) ) {
	function themerex_tribe_events_wrapper_end($html) {
		return $html . '
			</article><!-- .post_content -->
		</section>
		';
	}
}

// Add sorting parameter in query arguments
if (!function_exists('themerex_tribe_events_add_sort_order')) {
	function themerex_tribe_events_add_sort_order($q, $orderby, $order) {
		if ($orderby == 'event_date') {
			$q['orderby'] = 'meta_value';
			$q['meta_key'] = '_EventStartDate';
		}
		return $q;
	}
}


/* Query params to show Events in blog stream
-------------------------------------------------------------------------- */

// Pre query: Join tables into main query
if ( !function_exists( 'themerex_tribe_events_posts_join' ) ) {
	function themerex_tribe_events_posts_join($join_sql, $query) {
		if (!is_admin() && $query->is_main_query()) {
			if ($query->is_day || $query->is_month || $query->is_year) {
				global $wpdb;
				$join_sql .= " LEFT JOIN " . esc_sql($wpdb->postmeta) . " AS _tribe_events_meta ON " . esc_sql($wpdb->posts) . ".ID = _tribe_events_meta.post_id AND  _tribe_events_meta.meta_key = '_EventStartDate'";
			}
		}
		return $join_sql;
	}
}

// Pre query: Join tables into archives widget query
if ( !function_exists( 'themerex_tribe_events_getarchives_join' ) ) {
	function themerex_tribe_events_getarchives_join($join_sql, $r) {
		global $wpdb;
		$join_sql .= " LEFT JOIN " . esc_sql($wpdb->postmeta) . " AS _tribe_events_meta ON " . esc_sql($wpdb->posts) . ".ID = _tribe_events_meta.post_id AND  _tribe_events_meta.meta_key = '_EventStartDate'";
		return $join_sql;
	}
}

// Pre query: Where section into main query
if ( !function_exists( 'themerex_tribe_events_posts_where' ) ) {
	function themerex_tribe_events_posts_where($where_sql, $query) {
		if (!is_admin() && $query->is_main_query()) {
			if ($query->is_day || $query->is_month || $query->is_year) {
				global $wpdb;
				$where_sql .= " OR (1=1";
				// Posts status
				if ((!isset($_REQUEST['preview']) || $_REQUEST['preview']!='true') && (!isset($_REQUEST['vc_editable']) || $_REQUEST['vc_editable']!='true')) {
					if (current_user_can('read_private_pages') && current_user_can('read_private_posts'))
						$where_sql .= " AND (" . esc_sql($wpdb->posts) . ".post_status='publish' OR " . esc_sql($wpdb->posts) . ".post_status='private')";
					else
						$where_sql .= " AND " . esc_sql($wpdb->posts) . ".post_status='publish'";
				}
				// Posts type and date
				$dt = $query->get('m');
				$y = $query->get('year');
				if (empty($y)) $y = (int) themerex_substr($dt, 0, 4);
				$where_sql .= " AND " . esc_sql($wpdb->posts) . ".post_type='".esc_sql(Tribe__Events__Main::POSTTYPE)."' AND YEAR(_tribe_events_meta.meta_value)=".esc_sql($y);
				if ($query->is_month || $query->is_day) {
					$m = $query->get('monthnum');
					if (empty($m)) $m = (int) themerex_substr($dt, 4, 2);
					$where_sql .= " AND MONTH(_tribe_events_meta.meta_value)=".esc_sql($m);
				}
				if ($query->is_day) {
					$d = $query->get('day');
					if (empty($d)) $d = (int) themerex_substr($dt, 6, 2);
					$where_sql .= " AND DAYOFMONTH(_tribe_events_meta.meta_value)=".esc_sql($d);
				}
				$where_sql .= ')';
			}
		}
		return $where_sql;
	}
}

// Pre query: Where section into archives widget query
if ( !function_exists( 'themerex_tribe_events_getarchives_where' ) ) {
	function themerex_tribe_events_getarchives_where($where_sql, $r) {
		global $wpdb;
		// Posts type and date
		$where_sql .= " OR " . esc_sql($wpdb->posts) . ".post_type='".esc_sql(Tribe__Events__Main::POSTTYPE)."'";
		return $where_sql;
	}
}

// Return tribe_events start date instead post publish date
if ( !function_exists( 'themerex_tribe_events_post_date' ) ) {
	function themerex_tribe_events_post_date($post_date, $post_id, $post_type) {
		if ($post_type == Tribe__Events__Main::POSTTYPE) {
			$post_date = get_post_meta($post_id, '_EventStartDate', true);
		}
		return $post_date;
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'themerex_tribe_events_required_plugins' ) ) {
	function themerex_tribe_events_required_plugins($list=array()) {
		if (in_array('tribe_events', themerex_storage_get('required_plugins')))
			$list[] = array(
					'name' 		=> 'Tribe Events Calendar',
					'slug' 		=> 'the-events-calendar',
					'required' 	=> false
				);

		return $list;
	}
}

	
// Add Google API key to the map's link
if ( !function_exists( 'themerex_tribe_events_google_maps_api' ) ) {
	function themerex_tribe_events_google_maps_api($url) {
		$api_key = themerex_get_theme_option('api_google');
		if ($api_key) {
			$url = themerex_add_to_url($url, array(
				'key' => $api_key
			));
		}
		return $url;
	}
}


// One-click import support
//------------------------------------------------------------------------

// Check in the required plugins
if ( !function_exists( 'themerex_tribe_events_importer_required_plugins' ) ) {
	function themerex_tribe_events_importer_required_plugins($not_installed='', $list='') {
        if (themerex_strpos($list, 'tribe_events')!==false && !themerex_exists_tribe_events() )
            $not_installed .= '<br>' . esc_html__('Tribe Events Calendar', 'wineshop');
        return $not_installed;
	}
}

// Add Google API key to the map's link
if ( !function_exists( 'themerex_tribe_events_google_maps_api' ) ) {
   function themerex_tribe_events_google_maps_api($url) {
		$api_key = themerex_get_theme_option('api_google');
		if ($api_key) {
			 $url = themerex_add_to_url($url, array(
			  'key' => $api_key
			 ));
		}
		return $url;
   }
}

// Shortcodes
//------------------------------------------------------------------------

if ( !function_exists( 'themerex_sc_events' ) ) {
	function themerex_sc_events($atts, $content=null){	
		if (themerex_in_shortcode_blogger()) return '';
		extract(themerex_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "events-1",
			"columns" => 4,
			"slider" => "no",
			"slides_space" => 0,
			"controls" => "no",
			"interval" => "",
			"autoheight" => "no",
			"align" => "",
			"ids" => "",
			"cat" => "",
			"count" => 4,
			"offset" => "",
			"orderby" => "event_date",
			"order" => "asc",
			"readmore" => esc_html__('Read more', 'wineshop'),
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"description_link" => "",
			"link_caption" => esc_html__('Learn more', 'wineshop'),
			"link" => '',
			"scheme" => '',
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
	
		if (empty($id)) $id = "sc_events_".str_replace('.', '', mt_rand());
		if (empty($width)) $width = "100%";
		if (!empty($height) && themerex_param_is_on($autoheight)) $autoheight = "no";
		if (empty($interval)) $interval = mt_rand(5000, 10000);
		
		$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);

		$ws = themerex_get_css_dimensions_from_values($width);
		$hs = themerex_get_css_dimensions_from_values('', $height);
		$css .= ($hs) . ($ws);

		$count = max(1, (int) $count);
		$columns = max(1, min(12, (int) $columns));
		if ($count < $columns) $columns = $count;

		if (themerex_param_is_on($slider)) themerex_enqueue_slider('swiper');

		$output = '<div' . ($id ? ' id="'.esc_attr($id).'_wrap"' : '') 
						. ' class="sc_events_wrap'
						. ($scheme && !themerex_param_is_off($scheme) && !themerex_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
						.'">'
					. '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
						. ' class="sc_events'
							. ' sc_events_style_'.esc_attr($style)
							. ' ' . esc_attr(themerex_get_template_property($style, 'container_classes'))
							. ' ' . esc_attr(themerex_get_slider_controls_classes($controls))
							. (themerex_param_is_on($slider)
								? ' sc_slider_swiper swiper-slider-container'
									. (themerex_param_is_on($autoheight) ? ' sc_slider_height_auto' : '')
									. ($hs ? ' sc_slider_height_fixed' : '')
								: '')
							. (!empty($class) ? ' '.esc_attr($class) : '')
							. ($align!='' && $align!='none' ? ' align'.esc_attr($align) : '')
							. '"'
						. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
						. (!empty($width) && themerex_strpos($width, '%')===false ? ' data-old-width="' . esc_attr($width) . '"' : '')
						. (!empty($height) && themerex_strpos($height, '%')===false ? ' data-old-height="' . esc_attr($height) . '"' : '')
						. ((int) $interval > 0 ? ' data-interval="'.esc_attr($interval).'"' : '')
						. ($columns > 1 ? ' data-slides-per-view="' . esc_attr($columns) . '"' : '')
						. ($slides_space > 0 ? ' data-slides-space="' . esc_attr($slides_space) . '"' : '')
						. (!themerex_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_get_animation_classes($animation)).'"' : '')
					. '>'
					. (!empty($subtitle) ? '<h6 class="sc_events_subtitle sc_item_subtitle">' . trim(themerex_strmacros($subtitle)) . '</h6>' : '')
					. (!empty($title) ? '<h2 class="sc_events_title sc_item_title">' . trim(themerex_strmacros($title)) . '</h2>' : '')
					. (!empty($description) ? '<div class="sc_events_descr sc_item_descr">'.( !empty($description_link) ? '<a href='.$description_link.'>' : ''). trim(themerex_strmacros($description)).( !empty($description_link) ? '</a>' : '') . '</div>' : '')
					. (themerex_param_is_on($slider) 
						? '<div class="slides swiper-wrapper">' 
						: ($columns > 1 
							? '<div class="sc_columns columns_wrap">' 
							: '')
						);
	
		$content = do_shortcode($content);
	
		global $post;
	
		if (!empty($ids)) {
			$posts = explode(',', $ids);
			$count = count($posts);
		}
		
		$args = array(
			'post_type' => Tribe__Events__Main::POSTTYPE,
			'post_status' => 'publish',
			'posts_per_page' => $count,
			'ignore_sticky_posts' => true,
			'order' => $order=='asc' ? 'asc' : 'desc',
			'readmore' => $readmore,
            'tribe_suppress_query_filters' => true,   // Disable all filters from Tribe Events plugin
		);
	
		if ($offset > 0 && empty($ids)) {
			$args['offset'] = $offset;
		}
	
		$args = themerex_query_add_sort_order($args, $orderby, $order);
		$args = themerex_query_add_posts_and_cats($args, $ids, Tribe__Events__Main::POSTTYPE, $cat, Tribe__Events__Main::TAXONOMY);
		$query = new WP_Query( $args );

		$post_number = 0;
			
		while ( $query->have_posts() ) { 
			$query->the_post();
			$post_number++;
			$args = array(
				'layout' => $style,
				'show' => false,
				'number' => $post_number,
				'posts_on_page' => ($count > 0 ? $count : $query->found_posts),
				"descr" => themerex_get_custom_option('post_excerpt_maxlength'.($columns > 1 ? '_masonry' : '')),
				"orderby" => $orderby,
				'content' => false,
				'terms_list' => false,
				'readmore' => $readmore,
				'columns_count' => $columns,
				'slider' => $slider,
				'tag_id' => $id ? $id . '_' . $post_number : '',
				'tag_class' => '',
				'tag_animation' => '',
				'tag_css' => '',
				'tag_css_wh' => $ws . $hs
			);
			$output .= themerex_show_post_layout($args);
		}
		wp_reset_postdata();
	
		if (themerex_param_is_on($slider)) {
			$output .= '</div>'
				. '<div class="sc_slider_controls_wrap"><a class="sc_slider_prev" href="#"></a><a class="sc_slider_next" href="#"></a></div>'
				. '<div class="sc_slider_pagination_wrap"></div>';
		} else if ($columns > 1) {
			$output .= '</div>';
		}

		$output .=  (!empty($link) ? '<div class="sc_events_button sc_item_button">'.themerex_do_shortcode('[trx_button link="'.esc_url($link).'" icon="icon-right"]'.esc_html($link_caption).'[/trx_button]').'</div>' : '')
					. '</div><!-- /.sc_events -->'
				. '</div><!-- /.sc_envents_wrap -->';
	
		// Add template specific scripts and styles
		do_action('themerex_action_blog_scripts', $style);
	
		return apply_filters('themerex_shortcode_output', $output, 'trx_events', $atts, $content);
	}
	themerex_require_shortcode('trx_events', 'themerex_sc_events');
}
// ---------------------------------- [/trx_events] ---------------------------------------



// Add [trx_events] in the shortcodes list
if (!function_exists('themerex_tribe_events_reg_shortcodes')) {
	function themerex_tribe_events_reg_shortcodes() {
		global $THEMEREX_GLOBALS;
		if (isset($THEMEREX_GLOBALS['shortcodes'])) {

			$groups		= themerex_get_list_terms(false, Tribe__Events__Main::TAXONOMY);
			$styles		= themerex_get_list_templates('events');
			$sorting	= array(
				"event_date"=> esc_html__("Start Date", 'wineshop'),
				"title" 	=> esc_html__("Alphabetically", 'wineshop'),
				"random"	=> esc_html__("Random", 'wineshop')
				);
			$controls	= themerex_get_list_slider_controls();

			themerex_array_insert_before($THEMEREX_GLOBALS['shortcodes'], 'trx_form', array(

				// Events
				"trx_events" => array(
					"title" => esc_html__("Events", "wineshop"),
					"desc" => esc_html__("Insert events list in your page (post)", "wineshop"),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"title" => array(
							"title" => esc_html__("Title", "wineshop"),
							"desc" => esc_html__("Title for the block", "wineshop"),
							"value" => "",
							"type" => "text"
						),
						"subtitle" => array(
							"title" => esc_html__("Subtitle", "wineshop"),
							"desc" => esc_html__("Subtitle for the block", "wineshop"),
							"value" => "",
							"type" => "text"
						),
						"description" => array(
							"title" => esc_html__("Description", "wineshop"),
							"desc" => esc_html__("Short description for the block", "wineshop"),
							"value" => "",
							"type" => "textarea"
						),
						"style" => array(
							"title" => esc_html__("Style", "wineshop"),
							"desc" => esc_html__("Select style to display events list", "wineshop"),
							"value" => "events-1",
							"type" => "select",
							"options" => $styles
						),
						"columns" => array(
							"title" => esc_html__("Columns", "wineshop"),
							"desc" => esc_html__("How many columns use to show events list", "wineshop"),
							"value" => 4,
							"min" => 2,
							"max" => 6,
							"step" => 1,
							"type" => "spinner"
						),
						"scheme" => array(
							"title" => esc_html__("Color scheme", "wineshop"),
							"desc" => esc_html__("Select color scheme for this block", "wineshop"),
							"value" => "",
							"type" => "checklist",
							"options" => $THEMEREX_GLOBALS['sc_params']['schemes']
						),
						"slider" => array(
							"title" => esc_html__("Slider", "wineshop"),
							"desc" => esc_html__("Use slider to show events", "wineshop"),
							"dependency" => array(
								'style' => array('events-1')
							),
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"controls" => array(
							"title" => esc_html__("Controls", "wineshop"),
							"desc" => esc_html__("Slider controls style and position", "wineshop"),
							"dependency" => array(
								'slider' => array('yes')
							),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $controls
						),
						"slides_space" => array(
							"title" => esc_html__("Space between slides", "wineshop"),
							"desc" => esc_html__("Size of space (in px) between slides", "wineshop"),
							"dependency" => array(
								'slider' => array('yes')
							),
							"value" => 0,
							"min" => 0,
							"max" => 100,
							"step" => 10,
							"type" => "spinner"
						),
						"interval" => array(
							"title" => esc_html__("Slides change interval", "wineshop"),
							"desc" => esc_html__("Slides change interval (in milliseconds: 1000ms = 1s)", "wineshop"),
							"dependency" => array(
								'slider' => array('yes')
							),
							"value" => 7000,
							"step" => 500,
							"min" => 0,
							"type" => "spinner"
						),
						"autoheight" => array(
							"title" => esc_html__("Autoheight", "wineshop"),
							"desc" => esc_html__("Change whole slider's height (make it equal current slide's height)", "wineshop"),
							"dependency" => array(
								'slider' => array('yes')
							),
							"value" => "yes",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"align" => array(
							"title" => esc_html__("Alignment", "wineshop"),
							"desc" => esc_html__("Alignment of the events block", "wineshop"),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['align']
						),
						"cat" => array(
							"title" => esc_html__("Categories", "wineshop"),
							"desc" => esc_html__("Select categories (groups) to show events list. If empty - select events from any category (group) or from IDs list", "wineshop"),
							"divider" => true,
							"value" => "",
							"type" => "select",
							"style" => "list",
							"multiple" => true,
							"options" => themerex_array_merge(array(0 => esc_html__('- Select category -', 'wineshop')), $groups)
						),
						"count" => array(
							"title" => esc_html__("Number of posts", "wineshop"),
							"desc" => esc_html__("How many posts will be displayed? If used IDs - this parameter ignored.", "wineshop"),
							"value" => 4,
							"min" => 1,
							"max" => 100,
							"type" => "spinner"
						),
						"offset" => array(
							"title" => esc_html__("Offset before select posts", "wineshop"),
							"desc" => esc_html__("Skip posts before select next part.", "wineshop"),
							"value" => 0,
							"min" => 0,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => esc_html__("Post order by", "wineshop"),
							"desc" => esc_html__("Select desired posts sorting method", "wineshop"),
							"value" => "title",
							"type" => "select",
							"options" => $sorting
						),
						"order" => array(
							"title" => esc_html__("Post order", "wineshop"),
							"desc" => esc_html__("Select desired posts order", "wineshop"),
							"value" => "asc",
							"type" => "switch",
							"size" => "big",
							"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
						),
						"ids" => array(
							"title" => esc_html__("Post IDs list", "wineshop"),
							"desc" => esc_html__("Comma separated list of posts ID. If set - parameters above are ignored!", "wineshop"),
							"value" => "",
							"type" => "text"
						),
						"readmore" => array(
							"title" => esc_html__("Read more", "wineshop"),
							"desc" => esc_html__("Caption for the Read more link (if empty - link not showed)", "wineshop"),
							"value" => "",
							"type" => "text"
						),
						"link" => array(
							"title" => esc_html__("Button URL", "wineshop"),
							"desc" => esc_html__("Link URL for the button at the bottom of the block", "wineshop"),
							"value" => "",
							"type" => "text"
						),
						"link_caption" => array(
							"title" => esc_html__("Button caption", "wineshop"),
							"desc" => esc_html__("Caption for the button at the bottom of the block", "wineshop"),
							"value" => "",
							"type" => "text"
						),
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				)

			));
		}
	}
}


// Add [trx_events] in the VC shortcodes list
if (!function_exists('themerex_tribe_events_reg_shortcodes_vc')) {
	function themerex_tribe_events_reg_shortcodes_vc() {
		global $THEMEREX_GLOBALS;

		$groups		= themerex_get_list_terms(false, Tribe__Events__Main::TAXONOMY);
		$styles		= themerex_get_list_templates('events');
		$sorting	= array(
			"event_date"=> esc_html__("Start Date", 'wineshop'),
			"title" 	=> esc_html__("Alphabetically", 'wineshop'),
			"random"	=> esc_html__("Random", 'wineshop')
			);
		$controls	= themerex_get_list_slider_controls();

		// Events
		vc_map( array(
				"base" => "trx_events",
				"name" => esc_html__("Events", "wineshop"),
				"description" => esc_html__("Insert events list", "wineshop"),
				"category" => esc_html__('Content', 'wineshop'),
				"icon" => 'icon_trx_events',
				"class" => "trx_sc_single trx_sc_events",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => esc_html__("Style", "wineshop"),
						"description" => esc_html__("Select style to display events list", "wineshop"),
						"class" => "",
						"admin_label" => true,
						"std" => "events-1",
						"value" => array_flip($styles),
						"type" => "dropdown"
					),
					array(
						"param_name" => "scheme",
						"heading" => esc_html__("Color scheme", "wineshop"),
						"description" => esc_html__("Select color scheme for this block", "wineshop"),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['schemes']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "slider",
						"heading" => esc_html__("Slider", "wineshop"),
						"description" => esc_html__("Use slider to show events", "wineshop"),
						"admin_label" => true,
						'dependency' => array(
							'element' => 'style',
							'value' => 'events-1'
						),
						"group" => esc_html__('Slider', 'wineshop'),
						"class" => "",
						"std" => "no",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['yes_no']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "controls",
						"heading" => esc_html__("Controls", "wineshop"),
						"description" => esc_html__("Slider controls style and position", "wineshop"),
						"admin_label" => true,
						"group" => esc_html__('Slider', 'wineshop'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"std" => "no",
						"value" => array_flip($controls),
						"type" => "dropdown"
					),
					array(
						"param_name" => "slides_space",
						"heading" => esc_html__("Space between slides", "wineshop"),
						"description" => esc_html__("Size of space (in px) between slides", "wineshop"),
						"admin_label" => true,
						"group" => esc_html__('Slider', 'wineshop'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "interval",
						"heading" => esc_html__("Slides change interval", "wineshop"),
						"description" => esc_html__("Slides change interval (in milliseconds: 1000ms = 1s)", "wineshop"),
						"group" => esc_html__('Slider', 'wineshop'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"value" => "7000",
						"type" => "textfield"
					),
					array(
						"param_name" => "autoheight",
						"heading" => esc_html__("Autoheight", "wineshop"),
						"description" => esc_html__("Change whole slider's height (make it equal current slide's height)", "wineshop"),
						"group" => esc_html__('Slider', 'wineshop'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"value" => array("Autoheight" => "yes" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "align",
						"heading" => esc_html__("Alignment", "wineshop"),
						"description" => esc_html__("Alignment of the events block", "wineshop"),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "title",
						"heading" => esc_html__("Title", "wineshop"),
						"description" => esc_html__("Title for the block", "wineshop"),
						"admin_label" => true,
						"group" => esc_html__('Captions', 'wineshop'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "subtitle",
						"heading" => esc_html__("Subtitle", "wineshop"),
						"description" => esc_html__("Subtitle for the block", "wineshop"),
						"group" => esc_html__('Captions', 'wineshop'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "description",
						"heading" => esc_html__("Description", "wineshop"),
						"description" => esc_html__("Description for the block", "wineshop"),
						"group" => esc_html__('Captions', 'wineshop'),
						"class" => "",
						"value" => "",
						"type" => "textarea"
					),
					array(
						"param_name" => "description_link",
						"heading" => esc_html__("Description URL", "wineshop"),
						"description" => wp_kses( __("Link URL for the description", "wineshop"), $THEMEREX_GLOBALS['allowed_tags'] ),
						"group" => esc_html__('Captions', 'wineshop'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "cat",
						"heading" => esc_html__("Categories", "wineshop"),
						"description" => esc_html__("Select category to show events. If empty - select events from any category (group) or from IDs list", "wineshop"),
						"group" => esc_html__('Query', 'wineshop'),
						"class" => "",
						"value" => array_flip(themerex_array_merge(array(0 => esc_html__('- Select category -', 'wineshop')), $groups)),
						"type" => "dropdown"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", "wineshop"),
						"description" => esc_html__("How many columns use to show events list", "wineshop"),
						"group" => esc_html__('Query', 'wineshop'),
						"admin_label" => true,
						"class" => "",
						"value" => "4",
						"type" => "textfield"
					),
					array(
						"param_name" => "count",
						"heading" => esc_html__("Number of posts", "wineshop"),
						"description" => esc_html__("How many posts will be displayed? If used IDs - this parameter ignored.", "wineshop"),
						"admin_label" => true,
						"group" => esc_html__('Query', 'wineshop'),
						"class" => "",
						"value" => "4",
						"type" => "textfield"
					),
					array(
						"param_name" => "offset",
						"heading" => esc_html__("Offset before select posts", "wineshop"),
						"description" => esc_html__("Skip posts before select next part.", "wineshop"),
						"group" => esc_html__('Query', 'wineshop'),
						"class" => "",
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Post sorting", "wineshop"),
						"description" => esc_html__("Select desired posts sorting method", "wineshop"),
						"group" => esc_html__('Query', 'wineshop'),
						"class" => "",
						"value" => array_flip($sorting),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => esc_html__("Post order", "wineshop"),
						"description" => esc_html__("Select desired posts order", "wineshop"),
						"group" => esc_html__('Query', 'wineshop'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "ids",
						"heading" => esc_html__("Event's IDs list", "wineshop"),
						"description" => esc_html__("Comma separated list of event's ID. If set - parameters above (category, count, order, etc.)  are ignored!", "wineshop"),
						"group" => esc_html__('Query', 'wineshop'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "readmore",
						"heading" => esc_html__("Read more", "wineshop"),
						"description" => esc_html__("Caption for the Read more link (if empty - link not showed)", "wineshop"),
						"admin_label" => true,
						"group" => esc_html__('Captions', 'wineshop'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "link",
						"heading" => esc_html__("Button URL", "wineshop"),
						"description" => esc_html__("Link URL for the button at the bottom of the block", "wineshop"),
						"group" => esc_html__('Captions', 'wineshop'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "link_caption",
						"heading" => esc_html__("Button caption", "wineshop"),
						"description" => esc_html__("Caption for the button at the bottom of the block", "wineshop"),
						"group" => esc_html__('Captions', 'wineshop'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					themerex_vc_width(),
					themerex_vc_height(),
					$THEMEREX_GLOBALS['vc_params']['margin_top'],
					$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
					$THEMEREX_GLOBALS['vc_params']['margin_left'],
					$THEMEREX_GLOBALS['vc_params']['margin_right'],
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['animation'],
					$THEMEREX_GLOBALS['vc_params']['css']
				)
			) );
			
		class WPBakeryShortCode_Trx_Events extends Themerex_VC_ShortCodeSingle {}

	}
}
?>