<?php
/*
Template Name: Blog streampage
*/

global $THEMEREX_GLOBALS;
$THEMEREX_GLOBALS['blog_streampage'] = true;

get_header(); 

global $wp_query, $post;

if (empty($blog_style))	$blog_style = themerex_get_custom_option('blog_style');
$blog_columns	= max(1, (int) themerex_substr($blog_style, -1));
$show_sidebar 	= themerex_get_custom_option('show_sidebar_main');
$show_filters 	= themerex_get_custom_option('show_filters');
$ppp			= (int) themerex_get_custom_option('posts_per_page');
$hover			= themerex_get_custom_option('hover_style');
if (empty($hover)) $hover = 'square effect_shift';
$hover_dir		= themerex_get_custom_option('hover_dir');
if (empty($hover_dir)) $hover_dir = 'left_to_right';

$page_number = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);

$wp_query_need_restore = false;

$args = $wp_query->query_vars;
$args['post_status'] = current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish';

if ( is_page() || (array_key_exists('blog_filters', $THEMEREX_GLOBALS) && $THEMEREX_GLOBALS['blog_filters']!='' )) {
	unset($args['p']);
	unset($args['page_id']);
	unset($args['pagename']);
	unset($args['name']);
	$args['posts_per_page'] = $ppp;
	if ($page_number > 1) {
		$args['paged'] = $page_number;
		$args['ignore_sticky_posts'] = true;
	}
	$args = themerex_query_add_sort_order($args);
	$args = themerex_query_add_filters($args,  $THEMEREX_GLOBALS['blog_filters']);
	query_posts( $args );
	$wp_query_need_restore = true;
}

$per_page = count($wp_query->posts);
$post_number = 0;
$parent_tax_id = (int) themerex_get_custom_option('taxonomy_id');
$flt_ids = array();

$container = apply_filters('themerex_filter_blog_container', themerex_get_template_property($blog_style, 'container'), array('style'=>$blog_style, 'dir'=>'horizontal'));
$container_start = $container_end = '';
if (!empty($container)) {
	$container = explode('%s', $container);
	$container_start = !empty($container[0]) ? $container[0] : '';
	$container_end = !empty($container[1]) ? $container[1] : '';
}

$container2 = apply_filters('themerex_filter_blog_container2', themerex_get_template_property($blog_style, 'container2'), array('style'=>$blog_style, 'dir'=>'horizontal'));
$container2_start = $container2_end = '';
if (!empty($container2)) {
	$container2 = explode('%s', $container2);
	$container2_start = !empty($container2[0]) ? $container2[0] : '';
	$container2_end = !empty($container2[1]) ? $container2[1] : '';
}

themerex_show_layout($container_start . $container2_start);

if (themerex_get_template_property($blog_style, 'need_columns') && $blog_columns > 1) {
	?>
	<div class="columns_wrap <?php echo esc_attr(themerex_get_template_property($blog_style, 'container_classes')); ?>">
	<?php
}

if (themerex_get_template_property($blog_style, 'need_isotope')) {
	if (!themerex_param_is_off($show_filters)) {
		?>
		<div class="isotope_filters"></div>
		<?php
	}
	?>
	<div class="isotope_wrap <?php echo esc_attr(themerex_get_template_property($blog_style, 'container_classes')); ?>" data-columns="<?php echo esc_attr($blog_columns); ?>">
	<?php
}

while (have_posts()) { the_post();
	$post_number++;
	$post_args = array(
		'layout' => $blog_style,
		'number' => $post_number,
		'add_view_more' => false,
		'posts_on_page' => $per_page,
		'columns_count' => $blog_columns,
		// Get post data
		'strip_teaser' => false,
		'content' => themerex_get_template_property($blog_style, 'need_content'),
		'terms_list' => !themerex_param_is_off($show_filters) || themerex_get_template_property($blog_style, 'need_terms'),
		'parent_tax_id' => $parent_tax_id,
		'descr' => themerex_get_custom_option('post_excerpt_maxlength'.($blog_columns > 1 ? '_masonry' : '')),
		'sidebar' => !themerex_param_is_off($show_sidebar),
		'filters' => $show_filters != 'hide' ? $show_filters : '',
		'hover' => $hover,
		'hover_dir' => $hover_dir
	);

	$post_data = themerex_get_post_data($post_args);

	themerex_show_post_layout($post_args, $post_data);

	if ($show_filters=='tags') {					// Use tags as filter items
		if (!empty($post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms)) {
			foreach ($post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms as $tag) {
				$flt_ids[$tag->term_id] = $tag->name;
			}
		}
	}
}

if (themerex_get_template_property($blog_style, 'need_isotope')) {
	?>
	</div> <!-- /.isotope_wrap -->
	<?php 
}

if (themerex_get_template_property($blog_style, 'need_columns') && $blog_columns > 1) {
	?>
	</div> <!-- /.columns_wrap -->
	<?php
}

themerex_show_layout($container2_end . $container_end);

if (!$post_number) { 
	if ( is_search() ) {
		themerex_show_post_layout( array('layout' => 'no-search'), false );
	} else {
		themerex_show_post_layout( array('layout' => 'no-articles'), false );
	}
} else {
	// Isotope filters list
	$filters = '';
	$filter_button_classes = 'isotope_filters_button';
	if ($show_filters == 'categories') {			// Use categories as filter items
		$taxonomy = themerex_is_taxonomy();
		$cur_term = $taxonomy ? themerex_get_current_term($taxonomy) : 0;
		$cur_term_id = $cur_term ? $cur_term->term_id : 0;
		$portfolio_parent = $cur_term_id ? max(0, themerex_get_parent_taxonomy_by_property($cur_term_id, 'show_filters', 'yes', true, $taxonomy)) : 0;
		$args2 = array(
			'type'			=> !empty($args['post_type']) ? $args['post_type'] : 'post',
			'child_of'		=> $portfolio_parent,
			'orderby'		=> 'name',
			'order'			=> 'ASC',
			'hide_empty'	=> 1,
			'hierarchical'	=> 0,
			'exclude'		=> '',
			'include'		=> '',
			'number'		=> '',
			'taxonomy'		=> $taxonomy,
			'pad_counts'	=> false
		);
		$portfolio_list = get_categories($args2);
		if (is_array($portfolio_list) && count($portfolio_list) > 0) {
			$filters .= '<a href="#" data-filter="*" class="'.esc_attr($filter_button_classes . ($portfolio_parent==$cur_term_id ? ' active' : '')) . '">' . esc_html__('All', 'wineshop').'</a>';
			foreach ($portfolio_list as $cat) {
				if (isset($cat->term_id)) $filters .= '<a href="#" data-filter=".flt_'.esc_attr($cat->term_id).'" class="'.esc_attr($filter_button_classes . ($cat->term_id==$cur_term_id ? ' active' : '')).'">'.($cat->name).'</a>';
			}
		}
	} else if ($show_filters == 'tags') {																	// Use tags as filter items
		if (is_array($flt_ids) && count($flt_ids) > 0) {
			$filters .= '<a href="#" data-filter="*" class="'.esc_attr($filter_button_classes).' active">'.esc_html__('All', 'wineshop').'</a>';
			foreach ($flt_ids as $flt_id=>$flt_name) {
				$filters .= '<a href="#" data-filter=".flt_'.esc_attr($flt_id).'" class="'.esc_attr($filter_button_classes).'">'.($flt_name).'</a>';
			}
		}
	}
    if ($filters) {
        themerex_storage_set_array('js_vars', 'ppp', intval($ppp));
        //The following script is dynamically generated.
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery(".isotope_filters").append('<?php themerex_show_layout(wp_kses_post($filters)); ?>');
            });
        </script>
        <?php
    }
}

if ($post_number > 0) {
	// Pagination
	$pagination = themerex_get_custom_option('blog_pagination');
	if (in_array($pagination, array('viewmore', 'infinite'))) {
		if ($page_number < $wp_query->max_num_pages) {
			?>
			<div id="viewmore" class="pagination_wrap pagination_<?php echo esc_attr($pagination); ?>">
				<a href="#" id="viewmore_link" class="theme_button viewmore_button"><span class="icon-spin3 animate-spin viewmore_loading"></span><span class="viewmore_text_1"><?php esc_html_e('LOAD MORE', 'wineshop'); ?></span><span class="viewmore_text_2"><?php esc_html_e('Loading ...', 'wineshop'); ?></span></a>
				<span class="viewmore_loader"></span>
                <?php
                themerex_storage_set_array('js_vars', 'viewmore_page', intval($page_number));
                themerex_storage_set_array('js_vars', 'viewmore_data', serialize($args));
                themerex_storage_set_array('js_vars', 'viewmore_vars', serialize(array(
                    'blog_style' => $blog_style,
                    'columns_count' => $blog_columns,
                    'parent_tax_id' => $parent_tax_id,
                    'show_sidebar' => $show_sidebar,
                    'filters' => $show_filters!='hide' ? $show_filters : '',
                    'hover' => $hover,
                    'hover_dir' => $hover_dir,
                    'ppp' => $ppp
                )));
                ?>
			</div>
			<?php
		}
	} else {
		themerex_show_pagination(array(
			'class' => 'pagination_wrap pagination_'.esc_attr($pagination),
			'style' => $pagination,
			'button_class' => '',
			'first_text'=> '',
			'last_text' => '',
			'prev_text' => '',
			'next_text' => '',
			'pages_in_group' => $pagination=='pages' ? 10 : 20
			)
		);
	}
}

// Add template specific scripts and styles
do_action('themerex_action_blog_scripts', $blog_style);

// Restore main WP query
if ( $wp_query_need_restore ) wp_reset_query();
wp_reset_postdata();

get_footer();
?>