<?php
class goodsoul_Scripts {
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}
	public function enqueue_scripts() {
		$asset_file = function_exists( 'filemtime' ) ? filemtime( get_template_directory() . '/assets/js/script.js' ) : null;

		wp_enqueue_script( 'appear', GOODSOUL_JS_URL . 'appear.js', array( 'jquery' ), file_exists( get_template_directory() . '/assets/js/appear.js' ) ? filemtime( get_template_directory() . '/assets/js/appear.js' ) : null, true );
		wp_enqueue_script( 'bootstrap', GOODSOUL_JS_URL . 'bootstrap.min.js', array( 'jquery' ), file_exists( get_template_directory() . '/assets/js/bootstrap.min.js' ) ? filemtime( get_template_directory() . '/assets/js/bootstrap.min.js' ) : null, true );
		wp_enqueue_script( 'isotope', GOODSOUL_JS_URL . 'isotope.js', array( 'jquery' ), file_exists( get_template_directory() . '/assets/js/isotope.js' ) ? filemtime( get_template_directory() . '/assets/js/isotope.js' ) : null, true );
		wp_enqueue_script( 'countdown', GOODSOUL_JS_URL . 'jquery.countdown.min.js', array( 'jquery' ), file_exists( get_template_directory() . '/assets/js/jquery.countdown.min.js' ) ? filemtime( get_template_directory() . '/assets/js/jquery.countdown.min.js' ) : null, true );
		wp_enqueue_script( 'fancybox', GOODSOUL_JS_URL . 'jquery.fancybox.js', array( 'jquery' ), file_exists( get_template_directory() . '/assets/js/jquery.fancybox.js' ) ? filemtime( get_template_directory() . '/assets/js/jquery.fancybox.js' ) : null, true );
		wp_enqueue_script( 'nicescroll', GOODSOUL_JS_URL . 'jquery.nicescroll.min.js', array( 'jquery' ), file_exists( get_template_directory() . '/assets/js/jquery.nicescroll.min.js' ) ? filemtime( get_template_directory() . '/assets/js/jquery.nicescroll.min.js' ) : null, true );
		wp_enqueue_script( 'knob', GOODSOUL_JS_URL . 'knob.js', array( 'jquery' ), file_exists( get_template_directory() . '/assets/js/knob.js' ) ? filemtime( get_template_directory() . '/assets/js/knob.js' ) : null, true );
		wp_enqueue_script( 'map-script', GOODSOUL_JS_URL . 'map-script.js', array( 'jquery' ), file_exists( get_template_directory() . '/assets/js/map-script.js' ) ? filemtime( get_template_directory() . '/assets/js/map-script.js' ) : null, true );
		wp_enqueue_script( 'menu-nav-btn', GOODSOUL_JS_URL . 'menu-nav-btn.js', array( 'jquery' ), file_exists( get_template_directory() . '/assets/js/menu-nav-btn.js' ) ? filemtime( get_template_directory() . '/assets/js/menu-nav-btn.js' ) : null, true );
		wp_enqueue_script( 'owl', GOODSOUL_JS_URL . 'owl.js', array( 'jquery' ), file_exists( get_template_directory() . '/assets/js/owl.js' ) ? filemtime( get_template_directory() . '/assets/js/owl.js' ) : null, true );
		wp_enqueue_script( 'popper', GOODSOUL_JS_URL . 'popper.min.js', array( 'jquery' ), file_exists( get_template_directory() . '/assets/js/popper.min.js' ) ? filemtime( get_template_directory() . '/assets/js/popper.min.js' ) : null, true );
		wp_enqueue_script( 'goodsoul-swiper', GOODSOUL_JS_URL . 'swiper.min.js', array( 'jquery' ), file_exists( get_template_directory() . '/assets/js/swiper.min.js' ) ? filemtime( get_template_directory() . '/assets/js/swiper.min.js' ) : null, true );
		wp_enqueue_script( 'tweenMax', GOODSOUL_JS_URL . 'TweenMax.min.js', array( 'jquery' ), file_exists( get_template_directory() . '/assets/js/TweenMax.min.js' ) ? filemtime( get_template_directory() . '/assets/js/TweenMax.min.js' ) : null, true );
		wp_enqueue_script( 'wow', GOODSOUL_JS_URL . 'wow.js', array( 'jquery' ), file_exists( get_template_directory() . '/assets/js/wow.js' ) ? filemtime( get_template_directory() . '/assets/js/wow.js' ) : null, true );
		wp_enqueue_script( 'bootstrap-touchspin', GOODSOUL_JS_URL . 'jquery.bootstrap-touchspin.js', array( 'jquery' ), file_exists( get_template_directory() . '/assets/js/jquery.bootstrap-touchspin.js' ) ? filemtime( get_template_directory() . '/assets/js/jquery.bootstrap-touchspin.js' ) : null, true );
		wp_enqueue_script( 'bootstrap-select', GOODSOUL_JS_URL . 'bootstrap-select.min.js', array( 'jquery' ), file_exists( get_template_directory() . '/assets/js/bootstrap-select.min.js' ) ? filemtime( get_template_directory() . '/assets/js/bootstrap-select.min.js' ) : null, true );
		wp_enqueue_script( 'goodsoul-scripts', GOODSOUL_JS_URL . 'script.js', array( 'jquery' ), $asset_file ?: null, true );
	}
}
$goodsoul_scripts = new goodsoul_Scripts();
