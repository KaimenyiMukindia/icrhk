<?php

// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => _x( 'Service Box', 'Service Box', wpshopmart_service_box_pro_text_domain ),
		'singular_name'       => _x( 'Service Box', 'Service Box', wpshopmart_service_box_pro_text_domain ),
		'menu_name'           => __( 'Service Box Pro',  wpshopmart_service_box_pro_text_domain ),
		'parent_item_colon'   => __( 'Parent Service Box',  wpshopmart_service_box_pro_text_domain ),
		'all_items'           => __( 'All Service Box',  wpshopmart_service_box_pro_text_domain ),
		'view_item'           => __( 'View Service Box',  wpshopmart_service_box_pro_text_domain ),
		'add_new_item'        => __( 'Add New Service Box',  wpshopmart_service_box_pro_text_domain ),
		'add_new'             => __( 'Add New',  wpshopmart_service_box_pro_text_domain ),
		'edit_item'           => __( 'Edit Service Box',  wpshopmart_service_box_pro_text_domain ),
		'update_item'         => __( 'Update Service Box',  wpshopmart_service_box_pro_text_domain ),
		'search_items'        => __( 'Search Service Box',  wpshopmart_service_box_pro_text_domain ),
		'not_found'           => __( 'No service box Found',  wpshopmart_service_box_pro_text_domain ),
		'not_found_in_trash'  => __( 'Not found in Trash',  wpshopmart_service_box_pro_text_domain ),
	);
	
// Set other options for Custom Post Type
	
	$args = array(
		'label'               =>  __( 'Service Box', wpshopmart_service_box_pro_text_domain ),
		'description'         =>  __( 'Service Box', wpshopmart_service_box_pro_text_domain ),
		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports'            => array( 'title', '', '', '', '', '', '', '', '', '', '', ),
		// You can associate this CPT with a taxonomy or custom taxonomy. 
		//'taxonomies'          => array('category','post_tag'),
		/* A hierarchical CPT is like Pages and can have
		* Parent and child items. A non-hierarchical CPT
		* is like Posts.
		*/	
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'menu_icon'           => service_box_directory_url.'assets/images/wpsm_service_box_pro.png',
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => false,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => false,
		'capability_type'     => 'page',
		
	);
	
	// Registering your Custom Post Type
	register_post_type( 'servicebox', $args );

?>