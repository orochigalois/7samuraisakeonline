<?php
/*
 * Support for the Courses and Lessons
 */



// Register custom post type
if (!function_exists('trx_utils_support_courses_post_type')) {
	add_action( 'trx_utils_custom_post_type', 'trx_utils_support_courses_post_type', 10, 2 );
	function trx_utils_support_courses_post_type($name, $args=false) {
		
		if ($name=='courses') {

			if ($args===false) {
				$args = array(
					'label'               => esc_html__( 'Course item', 'trx_utils' ),
					'description'         => esc_html__( 'Course Description', 'trx_utils' ),
					'labels'              => array(
						'name'                => esc_html_x( 'Courses', 'Post Type General Name', 'trx_utils' ),
						'singular_name'       => esc_html_x( 'Course item', 'Post Type Singular Name', 'trx_utils' ),
						'menu_name'           => esc_html__( 'Courses', 'trx_utils' ),
						'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_utils' ),
						'all_items'           => esc_html__( 'All Courses', 'trx_utils' ),
						'view_item'           => esc_html__( 'View Item', 'trx_utils' ),
						'add_new_item'        => esc_html__( 'Add New Course item', 'trx_utils' ),
						'add_new'             => esc_html__( 'Add New', 'trx_utils' ),
						'edit_item'           => esc_html__( 'Edit Item', 'trx_utils' ),
						'update_item'         => esc_html__( 'Update Item', 'trx_utils' ),
						'search_items'        => esc_html__( 'Search Item', 'trx_utils' ),
						'not_found'           => esc_html__( 'Not found', 'trx_utils' ),
						'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_utils' ),
					),
					'supports'            => array( 'title', 'excerpt', 'editor', 'author', 'thumbnail', 'comments', 'custom-fields'),
					'hierarchical'        => false,
					'public'              => true,
					'show_ui'             => true,
					'menu_icon'			  => 'dashicons-format-chat',
					'show_in_menu'        => true,
					'show_in_nav_menus'   => true,
					'show_in_admin_bar'   => true,
					'menu_position'       => '52.5',
					'can_export'          => true,
					'has_archive'         => false,
					'exclude_from_search' => false,
					'publicly_queryable'  => true,
					'query_var'           => true,
					'capability_type'     => 'page',
					'rewrite'             => true
					);
			}
			register_post_type( $name, $args );
			trx_utils_add_rewrite_rules($name);

		} else if ($name=='lesson') {

			if ($args===false) {
				$args = array(
					'label'               => esc_html__( 'Lesson', 'trx_utils' ),
					'description'         => esc_html__( 'Lesson Description', 'trx_utils' ),
					'labels'              => array(
						'name'                => _x( 'Lessons', 'Post Type General Name', 'trx_utils' ),
						'singular_name'       => _x( 'Lesson', 'Post Type Singular Name', 'trx_utils' ),
						'menu_name'           => esc_html__( 'Lessons', 'trx_utils' ),
						'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_utils' ),
						'all_items'           => esc_html__( 'All lessons', 'trx_utils' ),
						'view_item'           => esc_html__( 'View Item', 'trx_utils' ),
						'add_new_item'        => esc_html__( 'Add New lesson', 'trx_utils' ),
						'add_new'             => esc_html__( 'Add New', 'trx_utils' ),
						'edit_item'           => esc_html__( 'Edit Item', 'trx_utils' ),
						'update_item'         => esc_html__( 'Update Item', 'trx_utils' ),
						'search_items'        => esc_html__( 'Search Item', 'trx_utils' ),
						'not_found'           => esc_html__( 'Not found', 'trx_utils' ),
						'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_utils' ),
					),
					'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt'),
					'hierarchical'        => false,
					'public'              => true,
					'show_ui'             => true,
					'menu_icon'			  => 'dashicons-format-chat',
					'show_in_menu'        => true,
					'show_in_nav_menus'   => true,
					'show_in_admin_bar'   => true,
					'menu_position'       => '52.6',
					'can_export'          => true,
					'has_archive'         => false,
					'exclude_from_search' => true,
					'publicly_queryable'  => true,
					'capability_type'     => 'page'
					);
			}
			register_post_type( $name, $args );
		}
	}
}
		

// Register custom taxonomy
if (!function_exists('trx_utils_support_courses_taxonomy')) {
	add_action( 'trx_utils_custom_taxonomy', 'trx_utils_support_courses_taxonomy', 10, 2 );
	function trx_utils_support_courses_taxonomy($name, $args=false) {
		
		if ($name=='courses_group') {

			if ($args===false) {
				$args = array(
					'post_type' 		=> 'courses',
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => _x( 'Courses Groups', 'taxonomy general name', 'trx_utils' ),
						'singular_name'     => _x( 'Courses Group', 'taxonomy singular name', 'trx_utils' ),
						'search_items'      => esc_html__( 'Search Groups', 'trx_utils' ),
						'all_items'         => esc_html__( 'All Groups', 'trx_utils' ),
						'parent_item'       => esc_html__( 'Parent Group', 'trx_utils' ),
						'parent_item_colon' => esc_html__( 'Parent Group:', 'trx_utils' ),
						'edit_item'         => esc_html__( 'Edit Group', 'trx_utils' ),
						'update_item'       => esc_html__( 'Update Group', 'trx_utils' ),
						'add_new_item'      => esc_html__( 'Add New Group', 'trx_utils' ),
						'new_item_name'     => esc_html__( 'New Group Name', 'trx_utils' ),
						'menu_name'         => esc_html__( 'Courses Groups', 'trx_utils' ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => 'courses_group' )
					);
			}
			register_taxonomy( $name, $args['post_type'], $args);

		} else if ($name=='courses_tag') {

			if ($args===false) {
				$args = array(
					'post_type' 		=> 'courses',
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => _x( 'Courses Tags', 'taxonomy general name', 'trx_utils' ),
						'singular_name'     => _x( 'Courses Tag', 'taxonomy singular name', 'trx_utils' ),
						'search_items'      => esc_html__( 'Search Tags', 'trx_utils' ),
						'all_items'         => esc_html__( 'All Tags', 'trx_utils' ),
						'parent_item'       => esc_html__( 'Parent Tag', 'trx_utils' ),
						'parent_item_colon' => esc_html__( 'Parent Tag:', 'trx_utils' ),
						'edit_item'         => esc_html__( 'Edit Tag', 'trx_utils' ),
						'update_item'       => esc_html__( 'Update Tag', 'trx_utils' ),
						'add_new_item'      => esc_html__( 'Add New Tag', 'trx_utils' ),
						'new_item_name'     => esc_html__( 'New Tag Name', 'trx_utils' ),
						'menu_name'         => esc_html__( 'Courses Tags', 'trx_utils' ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => 'courses_tag' )
				);
			}
			register_taxonomy( $name, $args['post_type'], $args);
		}
	}
}
?>