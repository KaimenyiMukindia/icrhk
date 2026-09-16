<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
function add_elementor_widget_categories( $elements_manager ) {
	$elements_manager->add_category(
		'goodsoulcore',
		array(
			'title' => __( 'Good Soule', 'goodsoulcore' ),
			'icon'  => 'fa fa-plug',
		)
	);
}
add_action( 'elementor/elements/categories_registered', 'add_elementor_widget_categories' );



function getAnimationControl( $obj, $animationClass = 'fadeInLeft', $animationDelay = '0', $animationDuration = '1500' ) {

	$obj->add_control(
		'heading',
		array(
			'label'     => __( 'Animation Options', 'goodsoulcore' ),
			'type'      => \Elementor\Controls_Manager::HEADING,
			'separator' => 'default',
		)
	);

	$obj->add_control(
		'animation_class',
		array(
			'label'   => __( 'Animation Class', 'goodsoulcore' ),
			'type'    => \Elementor\Controls_Manager::ANIMATION,
			'default' => $animationClass,
		)
	);

	$obj->add_control(
		'animation_delay_time',
		array(
			'label'   => __( 'Delay Time(ms)', 'goodsoulcore' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => $animationDelay,
		)
	);

	$obj->add_control(
		'animation_duration',
		array(
			'label'   => __( 'Duration Time(ms)', 'goodsoulcore' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => $animationDuration,
		)
	);
}
