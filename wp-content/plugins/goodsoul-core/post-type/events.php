<?php
// Register Custom Post Type
function loveus_event_post_type() {

	$labels = array(
		'name'                  => _x( 'Events', 'Post Type General Name', 'goodsoul-core' ),
		'singular_name'         => _x( 'Event', 'Post Type Singular Name', 'goodsoul-core' ),
		'menu_name'             => __( 'Event', 'goodsoul-core' ),
		'name_admin_bar'        => __( 'Event', 'goodsoul-core' ),
		'archives'              => __( 'Item Event', 'goodsoul-core' ),
		'parent_item_colon'     => __( 'Parent Item:', 'goodsoul-core' ),
		'all_items'             => __( 'All Event', 'goodsoul-core' ),
		'add_new_item'          => __( 'Add New Event', 'goodsoul-core' ),
		'add_new'               => __( 'Add New Event', 'goodsoul-core' ),
		'new_item'              => __( 'New Event Item', 'goodsoul-core' ),
		'edit_item'             => __( 'Edit Event Item', 'goodsoul-core' ),
		'update_item'           => __( 'Update Event Item', 'goodsoul-core' ),
		'view_item'             => __( 'View Event Item', 'goodsoul-core' ),
		'search_items'          => __( 'Search Item', 'goodsoul-core' ),
		'not_found'             => __( 'Not found', 'goodsoul-core' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'goodsoul-core' ),
		'featured_image'        => __( 'Featured Image', 'goodsoul-core' ),
		'set_featured_image'    => __( 'Set featured image', 'goodsoul-core' ),
		'remove_featured_image' => __( 'Remove featured image', 'goodsoul-core' ),
		'use_featured_image'    => __( 'Use as featured image', 'goodsoul-core' ),
		'insert_into_item'      => __( 'Insert into item', 'goodsoul-core' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'goodsoul-core' ),
		'items_list'            => __( 'Items list', 'goodsoul-core' ),
		'items_list_navigation' => __( 'Items list navigation', 'goodsoul-core' ),
		'filter_items_list'     => __( 'Filter items list', 'goodsoul-core' ),
	);

	$args = array(
		'labels'             => $labels,
		'description'        => __( 'Description.', 'goodsoul-core' ),
		'public'             => true,
		'show_in_rest'       => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'event' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'thumbnail' ),
	);
	register_post_type( 'loveus_events', $args );
}

add_action( 'init', 'loveus_event_post_type', 0 );

