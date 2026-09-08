<?php
class goodsoul_Style {
	public function __construct() {
		 add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style' ), 20 );
	}
	public function enqueue_style() {
		$version = function_exists( 'filemtime' ) ? filemtime( get_template_directory() . '/style.css' ) : null;
		wp_enqueue_style( 'bootstrap', GOODSOUL_CSS_URL . 'bootstrap.css', false, file_exists( get_template_directory() . '/assets/css/bootstrap.css' ) ? filemtime( get_template_directory() . '/assets/css/bootstrap.css' ) : '1' );
		wp_enqueue_style( 'bootstrap-select', GOODSOUL_CSS_URL . 'bootstrap-select.min.css', false, file_exists( get_template_directory() . '/assets/css/bootstrap-select.min.css' ) ? filemtime( get_template_directory() . '/assets/css/bootstrap-select.min.css' ) : '1' );
		wp_enqueue_style( 'animate', GOODSOUL_CSS_URL . 'animate.css', false, file_exists( get_template_directory() . '/assets/css/animate.css' ) ? filemtime( get_template_directory() . '/assets/css/animate.css' ) : '1' );
		wp_enqueue_style( 'custom-animate', GOODSOUL_CSS_URL . 'custom-animate.css', false, file_exists( get_template_directory() . '/assets/css/custom-animate.css' ) ? filemtime( get_template_directory() . '/assets/css/custom-animate.css' ) : '1' );
		wp_enqueue_style( 'font-awesome', GOODSOUL_CSS_URL . 'font-awesome.css', false, file_exists( get_template_directory() . '/assets/css/font-awesome.css' ) ? filemtime( get_template_directory() . '/assets/css/font-awesome.css' ) : '1' );
		wp_enqueue_style( 'flaticon', GOODSOUL_CSS_URL . 'flaticon.css', false, file_exists( get_template_directory() . '/assets/css/flaticon.css' ) ? filemtime( get_template_directory() . '/assets/css/flaticon.css' ) : '1' );
		wp_enqueue_style( 'fancybox', GOODSOUL_CSS_URL . 'jquery.fancybox.min.css', false, file_exists( get_template_directory() . '/assets/css/jquery.fancybox.min.css' ) ? filemtime( get_template_directory() . '/assets/css/jquery.fancybox.min.css' ) : '1' );
		wp_enqueue_style( 'jquery-ui', GOODSOUL_CSS_URL . 'jquery-ui.css', false, file_exists( get_template_directory() . '/assets/css/jquery-ui.css' ) ? filemtime( get_template_directory() . '/assets/css/jquery-ui.css' ) : '1' );
		wp_enqueue_style( 'owl', GOODSOUL_CSS_URL . 'owl.css', false, file_exists( get_template_directory() . '/assets/css/owl.css' ) ? filemtime( get_template_directory() . '/assets/css/owl.css' ) : '1' );
		wp_enqueue_style( 'swiper', GOODSOUL_CSS_URL . 'swiper.min.css', false, file_exists( get_template_directory() . '/assets/css/swiper.min.css' ) ? filemtime( get_template_directory() . '/assets/css/swiper.min.css' ) : '1' );
		wp_enqueue_style( 'goodsoul-style', get_stylesheet_uri(), null, $version ?: null );
		wp_enqueue_style( 'goodsoul-base-style', GOODSOUL_CSS_URL . 'goodsoul-base-style.css', false, file_exists( get_template_directory() . '/assets/css/goodsoul-base-style.css' ) ? filemtime( get_template_directory() . '/assets/css/goodsoul-base-style.css' ) : null );
		wp_enqueue_style( 'responsive', GOODSOUL_CSS_URL . 'responsive.css', false, file_exists( get_template_directory() . '/assets/css/responsive.css' ) ? filemtime( get_template_directory() . '/assets/css/responsive.css' ) : null );
	}
}
$goodsoul_style = new goodsoul_Style();
