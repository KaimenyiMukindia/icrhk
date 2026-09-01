<?php
class goodsoul_Scripts {
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}
	public function enqueue_scripts() {

		wp_enqueue_script( 'appear', GOODSOUL_JS_URL . 'appear.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'bootstrap', GOODSOUL_JS_URL . 'bootstrap.min.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'isotope', GOODSOUL_JS_URL . 'isotope.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'countdown', GOODSOUL_JS_URL . 'jquery.countdown.min.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'fancybox', GOODSOUL_JS_URL . 'jquery.fancybox.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'nicescroll', GOODSOUL_JS_URL . 'jquery.nicescroll.min.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'knob', GOODSOUL_JS_URL . 'knob.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'map-script', GOODSOUL_JS_URL . 'map-script.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'menu-nav-btn', GOODSOUL_JS_URL . 'menu-nav-btn.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'owl', GOODSOUL_JS_URL . 'owl.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'popper', GOODSOUL_JS_URL . 'popper.min.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'goodsoul-swiper', GOODSOUL_JS_URL . 'swiper.min.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'tweenMax', GOODSOUL_JS_URL . 'TweenMax.min.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'wow', GOODSOUL_JS_URL . 'wow.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'bootstrap-touchspin', GOODSOUL_JS_URL . 'jquery.bootstrap-touchspin.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'bootstrap-select', GOODSOUL_JS_URL . 'bootstrap-select.min.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'popper', GOODSOUL_JS_URL . 'popper.min.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'goodsoul-scripts', GOODSOUL_JS_URL . 'script.js', array( 'jquery' ), time(), true );
	}
}
$goodsoul_scripts = new goodsoul_Scripts();
