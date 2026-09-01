<?php
class goodsoul_Style {
	public function __construct() {
		 add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style' ), 20 );
	}
	public function enqueue_style() {
		wp_enqueue_style( 'bootstrap', GOODSOUL_CSS_URL . 'bootstrap.css', false, '1' );
		wp_enqueue_style( 'bootstrap-select', GOODSOUL_CSS_URL . 'bootstrap-select.min.css', false, '1' );
		wp_enqueue_style( 'animate', GOODSOUL_CSS_URL . 'animate.css', false, '1' );
		wp_enqueue_style( 'custom-animate', GOODSOUL_CSS_URL . 'custom-animate.css', false, '1' );
		wp_enqueue_style( 'custom-animate', GOODSOUL_CSS_URL . 'flaticon.css', false, '1' );
		wp_enqueue_style( 'font-awesome', GOODSOUL_CSS_URL . 'font-awesome.css', false, '1' );
		wp_enqueue_style( 'flaticon', GOODSOUL_CSS_URL . 'flaticon.css', false, '1' );
		wp_enqueue_style( 'fancybox', GOODSOUL_CSS_URL . 'jquery.fancybox.min.css', false, '1' );
		wp_enqueue_style( 'jquery-ui', GOODSOUL_CSS_URL . 'jquery-ui.css', false, '1' );
		wp_enqueue_style( 'owl', GOODSOUL_CSS_URL . 'owl.css', false, '1' );
		wp_enqueue_style( 'swiper', GOODSOUL_CSS_URL . 'swiper.min.css', false, '1' );
		wp_enqueue_style( 'goodsoul-style', get_stylesheet_uri(), null, date( 'Y-m-d H:i:s' ) );
		wp_enqueue_style( 'goodsoul-base-style', GOODSOUL_CSS_URL . 'goodsoul-base-style.css', false, date( 'Y-m-d H:i:s' ) );
		wp_enqueue_style( 'responsive', GOODSOUL_CSS_URL . 'responsive.css', false, date( 'Y-m-d H:i:s' ) );
	}
}
$goodsoul_style = new goodsoul_Style();
