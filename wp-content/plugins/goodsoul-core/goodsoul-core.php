<?php
/*
Plugin Name: Goodsoul Core
Plugin URI: http://smartdatasoft.com/
Description: Helping for the SmartDataSoft theme.
Version: 2.4
Author: SmartDataSoft
Author URI: http://smartdatasoft.com/
License: GPLv2 or later
Text Domain: goodsoul-core
Domain Path: /languages/
 */

/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function goodsoul_core_load_textdomain() {
	load_plugin_textdomain( 'goodsoul-core', false, dirname( __FILE__ ) . '/languages' );
}
add_action( 'plugins_loaded', 'goodsoul_core_load_textdomain' );

define( 'PLUGIN_DIR', dirname( __FILE__ ) . '/' );

if ( ! defined( 'GOODSOUL_CORE_PLUGIN_URI' ) ) {
	define( 'GOODSOUL_CORE_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
}
require_once PLUGIN_DIR . '/widgets/goodsoul-sidebar-latest-post-widget.php';
require_once PLUGIN_DIR . '/share/share-function.php';
require_once PLUGIN_DIR . '/meta-box/meta-box-conditional-logic/meta-box-conditional-logic.php';
require_once PLUGIN_DIR . '/meta-box/config-meta-box.php';

function goodsoul_events_excerpt_support_for_cpt() {
	add_post_type_support( 'goodsoul_events', 'excerpt' );
}
add_action( 'init', 'goodsoul_events_excerpt_support_for_cpt' );

/**
 * addons Initializer.
 */
require_once plugin_dir_path( __FILE__ ) . 'elementor-addons/init.php';

if ( class_exists( 'Tribe__Events__Main' ) ) {
	remove_filter( 'admin_post_thumbnail_html', array( tribe( Tribe\Events\Views\V2\Hooks::class ), 'filter_admin_post_thumbnail_html' ) );
}

// add support link to the WP Toolbar
function goodsoul_core_toolbar_link($wp_admin_bar) {
    $args = array(
        'id' => 'support_link',
        'title' => esc_html__('Theme Support & Documentation links','goodsoul-core'), 
        'href' => home_url() . '/wp-admin/admin.php?page=envato-theme-license-support', 
        'meta' => array(
            'class' => 'sp_link', 
            'title' => esc_html__('Support','goodsoul-core')
            )
    );
    $wp_admin_bar->add_node($args);
}
add_action('admin_bar_menu', 'goodsoul_core_toolbar_link', 999);