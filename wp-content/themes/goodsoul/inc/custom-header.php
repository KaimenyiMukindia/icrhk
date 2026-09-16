<?php
/**
 * Sample implementation of the Custom Header feature
 *
 * You can add an optional custom header image to header.php like so ...
 *
 * @link https://developer.wordpress.org/themes/functionality/custom-headers/
 *
 * @package goodsoul
 */

/**
 * Set up the WordPress core custom header feature.
 *
 * @uses goodsoul_header_style()
 */
function goodsoul_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'goodsoul_custom_header_args', array(
		'default-image'          => '',
		'default-text-color'     => '000000',
		'width'                  => 1000,
		'height'                 => 250,
		'flex-height'            => true,
		'wp-head-callback'       => 'goodsoul_header_style',
	) ) );
}
add_action( 'after_setup_theme', 'goodsoul_custom_header_setup' );

if ( ! function_exists( 'goodsoul_header_style' ) ) :
	/**
	 * Styles the header image and text displayed on the blog.
	 *
	 * @see goodsoul_custom_header_setup().
	 */
	function goodsoul_header_style() {
		$header_text_color = get_header_textcolor();

		/*
		 * If no custom options for text are set, let's bail.
		 * get_header_textcolor() options: Any hex value, 'blank' to hide text. Default: add_theme_support( 'custom-header' ).
		 */
		if ( get_theme_support( 'custom-header', 'default-text-color' ) === $header_text_color ) {
			return;
		}

		if (!display_header_text()) {
            $goodsoul_cus_css = ".site-title,
                .site-description {
                position: absolute;
                clip: rect(1px, 1px, 1px, 1px);
                }";
            wp_add_inline_style('goodsoul-style', $goodsoul_cus_css);
        } else {
            $goodsoul_cus_css = ".site-title a,
                .site-description {
                color: #" . esc_attr($header_text_color) . "
                }
               ";
            wp_add_inline_style('goodsoul-style', $goodsoul_cus_css);
        }
	}
endif;
