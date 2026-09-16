<?php
/*
Plugin Name: Events Addon for Elementor
Plugin URI: https://nicheaddons.com/demos/events
Description: Events Addon for Elementor covers all the must-needed elements for creating a perfect Event website using Elementor Page Builder. 30+ Unique & Basic Elementor widget covers all of the Event elements. Including getting a list of event posts from most popular Events WordPress plugins. Like, Accommodation, Venue, Conference, Event Timing Countdown, Organizer, Speakers, Schedules, Upcoming Events, and Tickets.
Author: NicheAddons
Author URI: https://nicheaddons.com/
Version: 1.8.3
Text Domain: events-addon-for-elementor
*/

// ABSPATH
if ( ! function_exists( 'naevents_block_direct_access' ) ) {
	function naevents_block_direct_access() {
		if ( ! defined( 'ABSPATH' ) ) {
			exit( 'Forbidden' );
		}
	}
}

// Plugin URL
define( 'NAEEP_PLUGIN_URL', plugins_url( '/', __FILE__ ) );

// Plugin PATH
define( 'NAEEP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'NAEEP_PLUGIN_ASTS', NAEEP_PLUGIN_URL . 'assets' );
define( 'NAEEP_PLUGIN_IMGS', NAEEP_PLUGIN_ASTS . '/images' );
define( 'NAEEP_PLUGIN_CSS', NAEEP_PLUGIN_ASTS . '/css' );
define( 'NAEEP_PLUGIN_SCRIPTS', NAEEP_PLUGIN_ASTS . '/js' );

// Events Addon for Elementor Elementor Shortcode Path
define( 'NAEEP_EM_SHORTCODE_BASE_PATH', NAEEP_PLUGIN_PATH . 'elementor/' );
define( 'NAEEP_EM_UNIQUE_SHORTCODE_PATH', NAEEP_EM_SHORTCODE_BASE_PATH . 'widgets/event-unique/' );
define( 'NAEEP_EM_SHORTCODE_PATH', NAEEP_EM_SHORTCODE_BASE_PATH . 'widgets/event/' );
define( 'NAEEP_EM_BASIC_SHORTCODE_PATH', NAEEP_EM_SHORTCODE_BASE_PATH . 'widgets/basic/' );

// Initial File
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if (is_plugin_active('events-addon-for-elementor/events-addon-for-elementor.php')) {
  if ( defined('ELEMENTOR_PATH') && file_exists( NAEEP_EM_SHORTCODE_BASE_PATH . '/em-setup.php' ) ){
    require_once( NAEEP_EM_SHORTCODE_BASE_PATH . '/em-setup.php' );
  }
}

// Plugin language
if ( ! function_exists( 'naevents_plugin_language_setup' ) ) {
  function naevents_plugin_language_setup() {
    load_plugin_textdomain( 'events-addon-for-elementor', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
  }
  add_action( 'init', 'naevents_plugin_language_setup' );
}

// Check if Elementor installed and activated
if ( ! function_exists( 'naevents_load_plugin' ) ) {
  function naevents_load_plugin() {
    if ( ! did_action( 'elementor/loaded' ) ) {
      add_action( 'admin_notices', 'admin_notice_missing_main_plugin' );
      return;
    }
  }
  add_action( 'plugins_loaded', 'naevents_load_plugin' );
}

// Warning when the site doesn't have Elementor installed or activated.
if ( ! function_exists( 'admin_notice_missing_main_plugin' ) ) {
  function admin_notice_missing_main_plugin() {
    if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
    $message = sprintf(
      /* translators: 1: Plugin name 2: Elementor */
      esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'events-addon-for-elementor' ),
      '<strong>' . esc_html__( 'Events Addon for Elementor', 'events-addon-for-elementor' ) . '</strong>',
      '<strong>' . esc_html__( 'Elementor', 'events-addon-for-elementor' ) . '</strong>'
    );
    printf( '<div class="notice notice-error is-dismissible"><p>%1$s</p></div>', $message );
  }
}

// Enqueue Files for FrontEnd
if ( ! function_exists( 'naevents_scripts_styles' ) ) {
  function naevents_scripts_styles() {
    // Styles
    wp_enqueue_style( 'font-awesome', NAEEP_PLUGIN_CSS . '/font-awesome.min.css', array(), '4.7.0', 'all' );
    wp_enqueue_style( 'animate', NAEEP_PLUGIN_CSS .'/animate.min.css', array(), '3.7.2', 'all' );
    wp_enqueue_style( 'themify-icons', NAEEP_PLUGIN_CSS .'/themify-icons.min.css', array(), '1.0.0', 'all' );
    wp_enqueue_style( 'linea', NAEEP_PLUGIN_CSS .'/linea.min.css', array(), '1.0.0', 'all' );
    wp_enqueue_style( 'magnific-popup', NAEEP_PLUGIN_CSS .'/magnific-popup.min.css', array(), '1.0', 'all' );
    wp_enqueue_style( 'owl-carousel', NAEEP_PLUGIN_CSS .'/owl.carousel.min.css', array(), '2.3.4', 'all' );
    wp_enqueue_style( 'juxtapose', NAEEP_PLUGIN_CSS .'/juxtapose.css', array(), '1.0', 'all' );
    wp_enqueue_style( 'naevents-styles', NAEEP_PLUGIN_CSS .'/styles.css', array(), '1.8.2', 'all' );
    wp_enqueue_style( 'naevents-responsive', NAEEP_PLUGIN_CSS .'/responsive.css', array(), '1.8.2', 'all' );

    // Scripts
    wp_enqueue_script( 'imagesloaded', NAEEP_PLUGIN_SCRIPTS . '/imagesloaded.pkgd.min.js', array( 'jquery' ), '4.1.4', true );
    wp_enqueue_script( 'magnific-popup', NAEEP_PLUGIN_SCRIPTS . '/jquery.magnific-popup.min.js', array( 'jquery' ), '1.1.0', true );
    wp_enqueue_script( 'juxtapose', NAEEP_PLUGIN_SCRIPTS . '/juxtapose.js', array( 'jquery' ), '1.1.2', true );
    wp_enqueue_script( 'typed', NAEEP_PLUGIN_SCRIPTS . '/typed.min.js', array( 'jquery' ), '2.0.11', true );
    wp_enqueue_script( 'owl-carousel', NAEEP_PLUGIN_SCRIPTS . '/owl.carousel.min.js', array( 'jquery' ), '2.3.4', true );
    wp_enqueue_script( 'countdown', NAEEP_PLUGIN_SCRIPTS . '/jquery.countdown.min.js', array( 'jquery' ), '1.6.2', true );
    wp_enqueue_script( 'matchheight', NAEEP_PLUGIN_SCRIPTS . '/jquery.matchHeight.min.js', array( 'jquery' ), '0.7.2', true );
    wp_enqueue_script( 'isotope', NAEEP_PLUGIN_SCRIPTS . '/isotope.min.js', array( 'jquery' ), '3.0.1', true );
    wp_enqueue_script( 'packery-mode', NAEEP_PLUGIN_SCRIPTS . '/packery-mode.pkgd.min.js', array( 'jquery' ), '2.0.1', true );
    wp_enqueue_script( 'naevents-scripts', NAEEP_PLUGIN_SCRIPTS . '/scripts.js', array( 'jquery' ), '1.8.2', true );
  }
  add_action( 'wp_enqueue_scripts', 'naevents_scripts_styles' );
}