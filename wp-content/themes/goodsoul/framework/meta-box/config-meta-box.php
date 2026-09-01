<?php
/**
 * Register meta boxes
 *
 * @return void
 */


add_filter( 'rwmb_meta_boxes', 'goodsoul_theme_meta_box' );

function goodsoul_theme_meta_box( $meta_boxes ) {
	$prefix     = 'goodsoul_theme_metabox';
	$posts_page = get_option( 'page_for_posts' );
	if ( ! isset( $_GET['post'] ) || intval( $_GET['post'] ) != $posts_page ) {
		$meta_boxes[] = array(
			'id'       => $prefix . '_page_meta_box',
			'title'    => esc_html__( 'Page Design Settings', 'goodsoul' ),
			'pages'    => array(
				'page',
			),
			'context'  => 'normal',
			'priority' => 'core',
			'fields'   => array(
				array(
					'name'          => 'Color picker',
					'id'            => 'color_options',
					'type'          => 'color',
					// Add alpha channel?
					'alpha_channel' => true,
					// Color picker options. See here: https://automattic.github.io/Iris/.
					'js_options'    => array(
						'palettes' => array( '#125', '#459', '#78b', '#ab0', '#de3', '#f0f' ),
					),
				),
				array(
					'type' => 'single_image',
					'name' => 'Header Logo',
					'id'   => "{$prefix}_header_logo",
				),
				array(
					'type' => 'single_image',
					'name' => 'Header Sticky Logo',
					'id'   => "{$prefix}_header_sticky_logo",
				),
				array(
					'type' => 'single_image',
					'name' => 'Header Mobile Logo',
					'id'   => "{$prefix}_header_mobile_logo",
				),
				array(
					'name'            => 'Header Style',
					'id'              => "{$prefix}_header_style",
					'type'            => 'select',
					'options'         => array(
						'1' => 'one',
						'2' => 'two ',
						'3' => 'three ',
						'4' => 'four ',
						'5' => 'five ',
						'6' => 'six ',
					),
					'multiple'        => false,
					'placeholder'     => 'Select an Item',
					'select_all_none' => false,
				),
				array(
					'type' => 'single_image',
					'name' => 'Footer Logo',
					'id'   => "{$prefix}_footer_logo",
				),
				array(
					'name'            => 'footer Style',
					'id'              => "{$prefix}_show_footer",
					'type'            => 'select',
					'options'         => array(
						'style_1' => 'one',
						'style_2' => 'two ',
						'style_3' => 'three ',
						'style_4' => 'four ',
						'style_5' => 'five ',
						'style_6' => 'six ',
					),
					'multiple'        => false,
					'placeholder'     => 'Select an Item',
					'select_all_none' => false,
				),
				array(
					'name'  => 'Copyright text',
					'id'    => "{$prefix}_footer_copyright",
					'type'  => 'textarea',
					'clone' => false,
				),
				array(
					'name'   => 'Footer Links',
					'id'     => "{$prefix}_footer1_link",
					'type'   => 'textarea',
					'clone'  => false,
					'hidden' => array( "{$prefix}_show_footer", '!=', 'style_1' ),
				),
				array(
					'name'   => 'Footer Links',
					'id'     => "{$prefix}_footer3_link",
					'type'   => 'textarea',
					'clone'  => false,
					'hidden' => array( "{$prefix}_show_footer", '!=', 'style_3' ),
				),
				array(
					'name'   => 'Footer Links',
					'id'     => "{$prefix}_footer5_link",
					'type'   => 'textarea',
					'clone'  => false,
					'hidden' => array( "{$prefix}_show_footer", '!=', 'style_5' ),
				),
				array(
					'name'  => 'Newsletter Sortcode',
					'id'    => "{$prefix}_footer_shortcode",
					'type'  => 'textarea',
					'clone' => false,
				),

				array(
					'name'            => 'Footer Upper Content Template',
					'id'              => "{$prefix}_footer_content",
					'type'            => 'select',
					'options'         => goodsoul_elementor_library(),
					'multiple'        => false,
					'placeholder'     => 'Select an Item',
					'select_all_none' => false,
				),
				array(
					'name'  => 'Breadcrumb Text',
					'id'    => "{$prefix}_breadcrumb_text",
					'type'  => 'text',
					'clone' => false,
				),
				array(
					'id'      => "{$prefix}_show_breadcrumb",
					'name'    => esc_html__( 'Show Breadcrumb', 'goodsoul' ),
					'desc'    => '',
					'type'    => 'radio',
					'std'     => 'on',
					'options' => array(
						'on'  => 'Yes',
						'off' => 'No',
					),
				),
				array(
					'id'      => "{$prefix}_show_footer_ele",
					'name'    => esc_html__( 'Show Elementor footer', 'goodsoul' ),
					'desc'    => '',
					'type'    => 'radio',
					'std'     => 'on',
					'options' => array(
						'on'  => 'Yes',
						'off' => 'No',
					),
				),
				array(
					'id'      => "{$prefix}_show_backtoo_top",
					'name'    => esc_html__( 'Show Back to top', 'goodsoul' ),
					'desc'    => '',
					'type'    => 'radio',
					'std'     => 'on',
					'options' => array(
						'on'  => 'Yes',
						'off' => 'No',
					),
				),
			),
		);
	}
	return $meta_boxes;
}
