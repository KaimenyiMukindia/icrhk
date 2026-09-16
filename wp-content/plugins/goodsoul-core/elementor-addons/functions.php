<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


add_action(
	'elementor/init',
	function () {
		\Elementor\Plugin::$instance->elements_manager->add_category(
			'cameron',
			array(
				'title' => __( 'cameron', 'goodsoul-core' ),
				'icon'  => 'fa fa-plug',
			),
			1
		);
	}
);

function get_all_post() {
	$options           = array();
	$post_types        = get_post_types();
	$post_type_not__in = array( 'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'elementor_library' );

	foreach ( $post_type_not__in as $post_type_not ) {
		unset( $post_types[ $post_type_not ] );
	}
	$post_type = array_values( $post_types );
	$all_posts = get_posts(
		array(
			'post_type' => $post_type,
		)
	);

	if ( ! empty( $all_posts ) && ! is_wp_error( $all_posts ) ) {
		foreach ( $all_posts as $post ) {
			$this->options[ $post->ID ] = strlen( $post->post_title ) > 20 ? substr( $post->post_title, 0, 20 ) . '...' : $post->post_title;
		}
	}
	return $this->options;
}
function getContactFormId() {
	$contact_forms = array();
	$cf7           = get_posts( 'post_type="wpcf7_contact_form"&numberposts=-1' );
	if ( $cf7 ) {
		foreach ( $cf7 as $cform ) {
			$contact_forms[ $cform->ID ] = $cform->post_title;
		}
	} else {
		$contact_forms[ __( 'No contact forms found', 'goodsoul-core' ) ] = 0;
	}
	return $contact_forms;
}

if ( ! function_exists( 'get_contact_form_7_posts' ) ) :

	function get_contact_form_7_posts() {
		$args    = array(
			'post_type'      => 'wpcf7_contact_form',
			'posts_per_page' => -1,
		);
		$catlist = array();
		if ( $categories = get_posts( $args ) ) {
			foreach ( $categories as $category ) {
				(int) $catlist[ $category->ID ] = $category->post_title;
			}
		} else {
			(int) $catlist['0'] = esc_html__( 'No content From 7 form found', 'goodsoul-core' );
		}
		return $catlist;
	}

endif;


if ( ! function_exists( 'get_all_pages' ) ) :

	function get_all_pages() {
		$args    = array(
			'post_type'      => 'page',
			'posts_per_page' => -1,
		);
		$catlist = array();
		if ( $categories = get_posts( $args ) ) {
			foreach ( $categories as $category ) {
				(int) $catlist[ $category->ID ] = $category->post_title;
			}
		} else {
			(int) $catlist['0'] = esc_html__( 'No Pages Found!', 'goodsoul-core' );
		}
		return $catlist;
	}

endif;

function goodsoul_custom_icon( $array ) {
	$plugin_url = plugins_url();
	return array(
		'custom-icon' => array(
			'name'          => 'custom-icon',
			'label'         => 'Goodsoul Icon',
			'url'           => '',
			'enqueue'       => array(
				$plugin_url . '/goodsoul-core/elementor-addons/assets/icon/style.css',
			),
			'prefix'        => '',
			'displayPrefix' => '',
			'labelIcon'     => '',
			'ver'           => '',
			'fetchJson'     => $plugin_url . '/goodsoul-core/elementor-addons/assets/js/regular.js',
			'native'        => 1,
		),
	);
}
add_filter( 'elementor/icons_manager/additional_tabs', 'goodsoul_custom_icon' );

function cameron_modify_controls( $controls_registry ) {
	// Get existing icons
	$icons = $controls_registry->get_control( 'icon' )->get_settings( 'options' );
	// Append new icons
	$new_icons = array_merge(
		array(

			'cameron-icon-lock-1'                   => 'cameron-icon-lock-1',
			'cameron-icon-lock-2'                   => 'cameron-icon-lock-2',
			'cameron-icon-lock-3'                   => 'cameron-icon-lock-3',
			'cameron-icon-camera-1'                 => 'cameron-icon-camera-1',
			'cameron-icon-lock-4'                   => 'cameron-icon-lock-4',
			'cameron-icon-user-2'                   => 'cameron-icon-user-2',
			'cameron-icon-star-3'                   => 'cameron-icon-star-3',
			'cameron-icon-head-phone'               => 'cameron-icon-head-phone',
			'cameron-icon-lock-5'                   => 'cameron-icon-lock-5',
			'cameron-icon-smile-1'                  => 'cameron-icon-smile-1',
			'cameron-icon-coffe'                    => 'cameron-icon-coffe',
			'cameron-icon-untitled-14'              => 'cameron-icon-untitled-14',
			'cameron-icon-smartphone'               => 'cameron-icon-smartphone',
			'cameron-icon-opened-email-outlined-interface-symbol' => 'cameron-icon-opened-email-outlined-interface-symbol',
			'cameron-icon-placeholder'              => 'cameron-icon-placeholder',
			'cameron-icon-bank'                     => 'cameron-icon-bank',
			'cameron-icon-briefcase'                => 'cameron-icon-briefcase',
			'cameron-icon-coffee-cup'               => 'cameron-icon-coffee-cup',
			'cameron-icon-diploma'                  => 'cameron-icon-diploma',
			'cameron-icon-email'                    => 'cameron-icon-email',
			'cameron-icon-headphones'               => 'cameron-icon-headphones',
			'cameron-icon-key'                      => 'cameron-icon-key',
			'cameron-icon-left-arrow'               => 'cameron-icon-left-arrow',
			'cameron-icon-locked'                   => 'cameron-icon-locked',
			'cameron-icon-right-arrow'              => 'cameron-icon-right-arrow',
			'cameron-icon-safe-box'                 => 'cameron-icon-safe-box',
			'cameron-icon-shopping-bag'             => 'cameron-icon-shopping-bag',
			'cameron-icon-smile'                    => 'cameron-icon-smile',
			'cameron-icon-star'                     => 'cameron-icon-star',
			'cameron-icon-support'                  => 'cameron-icon-support',
			'cameron-icon-temperature'              => 'cameron-icon-temperature',
			'cameron-icon-traveler-with-a-suitcase' => 'cameron-icon-traveler-with-a-suitcase',
			'cameron-icon-user'                     => 'cameron-icon-user',
		),
		$icons
	);
	// Then we set a new list of icons as the options of the icon control
	$controls_registry->get_control( 'icon' )->set_settings( 'options', $new_icons );
}

add_action( 'elementor/controls/controls_registered', 'cameron_modify_controls', 10, 1 );

function goodsoul_get_animation_control( $obj, $animationClass = 'fadeInLeft', $animationDelay = '0', $animationDuration = '1500' ) {
	$obj->add_control(
		'animation_class',
		array(
			'label'     => __( 'Animation Class', 'goodsoul-core' ),
			'separator' => 'before',
			'type'      => \Elementor\Controls_Manager::ANIMATION,
			'default'   => $animationClass,
		)
	);
	$obj->add_control(
		'addon_animation_delay_time',
		array(
			'label'     => __( 'Delay Time (ms)', 'goodsoul-core' ),
			'separator' => 'before',
			'type'      => \Elementor\Controls_Manager::TEXT,
			'default'   => $animationDelay,
		)
	);
}
