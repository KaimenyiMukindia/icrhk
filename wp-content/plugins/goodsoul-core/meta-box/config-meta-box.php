<?php

add_filter( 'rwmb_meta_boxes', 'goodsoul_meta_box' );

/**
 * Register meta boxes
 *
 * Remember to change "your_prefix" to actual prefix in your project
 *
 * @return void
 */
function goodsoul_meta_box( $meta_boxes ) {
	 $prefix      = 'goodsoul_metabox';
	$meta_boxes[] = array(
		'id'        => $prefix . '_event_images',
		'title'     => esc_html__( 'Design Settings', 'goodsoul-core' ),
		'pages'     => array(
			'tribe_events',
		),
		'context'   => 'normal',
		'priority'  => 'high',
		'tab_style' => 'left',
		'fields'    => array(
			array(
				'name'             => esc_html__( 'Event Header Image', 'goodsoul-core' ),
				'id'               => "{$prefix}_event_meta_image",
				'desc'             => '',
				'type'             => 'image_advanced',
				'max_file_uploads' => 1,
			),
			array(
				'name'             => esc_html__( 'Event Advertisement Image', 'goodsoul-core' ),
				'id'               => "{$prefix}_event_advertise_image",
				'desc'             => '',
				'type'             => 'image_advanced',
				'max_file_uploads' => 1,
			),
			array(
				'name'  => esc_html__( 'Event date counter, please input like 2019/12/30', 'goodsoul-core' ),
				'id'    => "{$prefix}_date_event_counter",
				'type'  => 'text',
				'clone' => false,
			),
			array(
				'name'  => esc_html__( 'Event Where', 'goodsoul-core' ),
				'id'    => "{$prefix}_event_where",
				'type'  => 'text',
				'clone' => false,
				'std'   => 'Where',
			),
			array(
				'name'  => esc_html__( 'Event when', 'goodsoul-core' ),
				'id'    => "{$prefix}_event_when",
				'type'  => 'text',
				'clone' => false,
				'std'   => 'When',
			),
			array(
				'name'  => esc_html__( 'Event description', 'goodsoul-core' ),
				'id'    => "{$prefix}_event_description",
				'type'  => 'textarea',
				'clone' => false,
				'std'   => 'When',
			),
			array(
				'name'  => esc_html__( 'Event Organizer', 'goodsoul-core' ),
				'id'    => "{$prefix}_event_organizer",
				'type'  => 'text',
				'clone' => false,
				'std'   => 'When',
			),
			array(
				'name'  => esc_html__( 'Event Phone', 'goodsoul-core' ),
				'id'    => "{$prefix}_event_phone",
				'type'  => 'text',
				'clone' => false,
				'std'   => 'When',
			),
			array(
				'name'  => esc_html__( 'Event Email', 'goodsoul-core' ),
				'id'    => "{$prefix}_event_email",
				'type'  => 'text',
				'clone' => false,
				'std'   => 'When',
			),
		),
	);
	$meta_boxes[] = array(
		'id'        => $prefix . '_campaign_cariable',
		'title'     => esc_html__( 'Design Settings', 'goodsoul-core' ),
		'pages'     => array(
			'campaign',
		),
		'context'   => 'normal',
		'priority'  => 'high',
		'tab_style' => 'left',
		'fields'    => array(
			array(
				'name'             => esc_html__( 'Featured Image', 'goodsoul-core' ),
				'id'               => "{$prefix}_campaign_meta_image",
				'desc'             => '',
				'type'             => 'image_advanced',
				'max_file_uploads' => 1,
			),
			array(
				'name'    => esc_html__( 'Featured content', 'goodsoul-core' ),
				'id'      => "{$prefix}_featured_content",
				'type'    => 'wysiwyg',
				'clone'   => false,
				'raw'     => false,
				'options' => array(
					'textarea_rows' => 4,
					'teeny'         => false,
				),
			),
			array(
				'name' => esc_html__( 'Event Country', 'goodsoul-core' ),
				'id'   => "{$prefix}_event_country",
				'type' => 'text',
			),

		),
	);
	$meta_boxes[] = array(
		'id'        => $prefix . '_post_advertisement',
		'title'     => esc_html__( 'External Images', 'cameron-core' ),
		'pages'     => array(
			'post',
		),
		'context'   => 'normal',
		'priority'  => 'high',
		'tab_style' => 'left',
		'fields'    => array(
			array(
				'name'             => esc_html__( 'Featured Image', 'goodsoul-core' ),
				'id'               => "{$prefix}_campaign_meta_image",
				'desc'             => '',
				'type'             => 'image',
				'max_file_uploads' => 1,
			),
			array(
				'name'    => esc_html__( 'Featured content', 'goodsoul-core' ),
				'id'      => "{$prefix}_featured_content",
				'type'    => 'wysiwyg',
				'clone'   => false,
				'raw'     => false,
				'options' => array(
					'textarea_rows' => 4,
					'teeny'         => false,
				),
			),
			array(
				'name'             => esc_html__( 'Advertisement Image', 'cameron-core' ),
				'id'               => $prefix . '_content_image',
				'type'             => 'image_advanced',
				'max_file_uploads' => 1,
			),
		),
	);
	return $meta_boxes;
}



