<?php
/**
 * File for registering custom post types.
 *
 * @package    WP_Dentist
 * @subpackage Includes
 * @since      1.0.0
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2013 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/plugins/wpdentist
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register custom post types on the 'init' hook. */
add_action( 'init', 'wpdentist_register_post_types' );

/* Filter post updated messages for custom post types. */
add_filter( 'post_updated_messages', 'rp_post_updated_messages' );

/* Filter the "enter title here" text. */
add_filter( 'enter_title_here', 'rp_enter_title_here', 10, 2 );

/**
 * Registers post types needed by the plugin.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function wpdentist_register_post_types() {

	/* Get plugin settings. */
	$settings = get_option( 'wpdentist_settings', rp_get_default_settings() );

	/* Set up the arguments for the post type. */
	$args = array(
		'description'         => $settings['wpdentist_item_description'],
		'public'              => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'show_in_nav_menus'   => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => null,
		'menu_icon'           => null,
		'can_export'          => true,
		'delete_with_user'    => false,
		'hierarchical'        => false,
		'has_archive'         => rp_wpdentist_menu_base(),
		'query_var'           => 'wpdentist_item',
		'capability_type'     => 'wpdentist_item',
		'map_meta_cap'        => true,

		'capabilities' => array(

			// meta caps (don't assign these to roles)
			'edit_post'              => 'edit_wpdentist_item',
			'read_post'              => 'read_wpdentist_item',
			'delete_post'            => 'delete_wpdentist_item',

			// primitive/meta caps
			'create_posts'           => 'create_wpdentist_items',

			// primitive caps used outside of map_meta_cap()
			'edit_posts'             => 'edit_wpdentist_items',
			'edit_others_posts'      => 'manage_wpdentist',
			'publish_posts'          => 'manage_wpdentist',
			'read_private_posts'     => 'read',

			// primitive caps used inside of map_meta_cap()
			'read'                   => 'read',
			'delete_posts'           => 'manage_wpdentist',
			'delete_private_posts'   => 'manage_wpdentist',
			'delete_published_posts' => 'manage_wpdentist',
			'delete_others_posts'    => 'manage_wpdentist',
			'edit_private_posts'     => 'edit_wpdentist_items',
			'edit_published_posts'   => 'edit_wpdentist_items'
		),

		'rewrite' => array(
			'slug'       => rp_wpdentist_menu_base() . '/items',
			'with_front' => false,
			'pages'      => true,
			'feeds'      => true,
			'ep_mask'    => EP_PERMALINK,
		),

		'supports' => array(
			'title',
			'editor',
			'excerpt',
			'thumbnail',
			'comments',
			'revisions',
		),

		'labels' => array(
			'name'               => __( 'Menu Items',                   'wpdentist' ),
			'singular_name'      => __( 'Menu Item',                    'wpdentist' ),
			'menu_name'          => __( 'WPDentist',                    'wpdentist' ),
			'name_admin_bar'     => __( 'WPDentist Menu Item',          'wpdentist' ),
			'all_items'          => __( 'Menu Items',                   'wpdentist' ),
			'add_new'            => __( 'Add Menu Item',                'wpdentist' ),
			'add_new_item'       => __( 'Add New Menu Item',            'wpdentist' ),
			'edit_item'          => __( 'Edit Menu Item',               'wpdentist' ),
			'new_item'           => __( 'New Menu Item',                'wpdentist' ),
			'view_item'          => __( 'View Menu Item',               'wpdentist' ),
			'search_items'       => __( 'Search Menu Items',            'wpdentist' ),
			'not_found'          => __( 'No menu items found',          'wpdentist' ),
			'not_found_in_trash' => __( 'No menu items found in trash', 'wpdentist' ),

			/* Custom archive label.  Must filter 'post_type_archive_title' to use. */
			'archive_title'      => $settings['wpdentist_item_archive_title'],
		)
	);

	/* Register the post type. */
	register_post_type( 'wpdentist_item', $args );
}

/**
 * Custom "enter title here" text.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $title
 * @param  object  $post
 * @return string
 */
function rp_enter_title_here( $title, $post ) {

	if ( 'wpdentist_item' === $post->post_type )
		$title = __( 'Enter name', 'wpdentist' );

	return $title;
}

/**
 * @since  1.0.0
 * @access public
 * @return void
 */
function rp_post_updated_messages( $messages ) {
	global $post, $post_ID;

	$messages['wpdentist_item'] = array(
		 0 => '', // Unused. Messages start at index 1.
		 1 => sprintf( __( 'Menu item updated. <a href="%s">View menu item</a>', 'wpdentist' ), esc_url( get_permalink( $post_ID ) ) ),
		 2 => '',
		 3 => '',
		 4 => __( 'Menu item updated.', 'wpdentist' ),
		 5 => isset( $_GET['revision'] ) ? sprintf( __( 'Menu item restored to revision from %s', 'wpdentist' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		 6 => sprintf( __( 'Menu item published. <a href="%s">View menu item</a>', 'wpdentist' ), esc_url( get_permalink( $post_ID ) ) ),
		 7 => __( 'Menu item saved.', 'wpdentist' ),
		 8 => sprintf( __( 'Menu item submitted. <a target="_blank" href="%s">Preview menu item</a>', 'wpdentist' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		 9 => sprintf( __( 'Menu item scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview menu item</a>', 'wpdentist' ), date_i18n( __( 'M j, Y @ G:i', 'wpdentist' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
		10 => sprintf( __( 'Menu item draft updated. <a target="_blank" href="%s">Preview menu item</a>', 'wpdentist' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
	);

	return $messages;
}
