<?php
/**
 * Plugin Name: Envira Gallery - Elementor Addon
 * Plugin URI:  http://enviragallery.com
 * Description: Adds a custom Envira module to the Elementor Page Builder.
 * Author:      Envira Gallery Team
 * Author URI:  http://enviragallery.com
 * Version:     1.0.0
 * Text Domain: envira-elementor
 * Domain Path: languages
 *
 * @package Envira Gallery
 * @subpackage Envira Elementor
 *
 * Envira Gallery is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Envira Gallery is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Envira Gallery. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Elementor Envira Class
 *
 * The init class that runs the Elementor Envira plugin.
 * Intended To make sure that the plugin's minimum requirements are met.
 *
 * You should only modify the constants to match your plugin's needs.
 *
 * Any custom code should go inside Plugin Class in the plugin.php file.
 *
 * @since 1.0.0
 */
class Elementor_Envira {

	/**
	 * Holds the class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * The name of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_name = 'Envira Elementor';

	/**
	 * Unique plugin slug identifier.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_slug = 'envira-elementor';

	/**
	 * Plugin file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $file = __FILE__;

	/**
	 * Allowed HTML
	 *
	 * @var mixed
	 * @access public
	 */
	public $wp_kses_allowed_html = array(
		'a'      => array(
			'href'                => array(),
			'target'              => array(),
			'class'               => array(),
			'title'               => array(),
			'data-status'         => array(),
			'data-envira-tooltip' => array(),
			'data-id'             => array(),
		),
		'br'     => array(),
		'img'    => array(
			'src'   => array(),
			'class' => array(),
			'alt'   => array(),
		),
		'h1'     => array(
			'class' => array(),
		),
		'h2'     => array(
			'class' => array(),
		),
		'h3'     => array(
			'class' => array(),
		),
		'h4'     => array(
			'class' => array(),
		),
		'h5'     => array(
			'class' => array(),
		),
		'h6'     => array(
			'class' => array(),
		),
		'div'    => array(
			'class' => array(),
		),
		'li'     => array(
			'id'                              => array(),
			'class'                           => array(),
			'data-envira-gallery-image'       => array(),
			'data-envira-gallery-image-model' => array(),
		),
		'em'     => array(),
		'span'   => array(
			'class' => array(),
		),
		'strong' => array(),
	);

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	public $minimum_elementor_version = '2.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 * @var string Minimum PHP version required to run the plugin.
	 */
	public $minimum_php_version = '7.0';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		// Load translation.
		add_action( 'init', array( $this, 'i18n' ) );

		// Init Plugin.
		add_action( 'plugins_loaded', array( $this, 'init' ) );

	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 * Fired by `init` action hook.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function i18n() {
		load_plugin_textdomain( 'elementor-envira' );
	}

	/**
	 * Initialize the plugin
	 *
	 * Validates that Elementor is already loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed include the plugin class.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function init() {

		// Check if Elementor installed and activated.
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
			return;
		}

		// Check for required Elementor version.
		if ( ! version_compare( ELEMENTOR_VERSION, $this->minimum_elementor_version, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return;
		}

		// Check for required PHP version.
		if ( version_compare( PHP_VERSION, $ths->minimum_php_version, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );
			return;
		}

		// Once we get here, We have passed all validation checks so we can safely include our plugin.
		require_once 'plugin.php';
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) { // @codingStandardsIgnoreLine
			unset( $_GET['activate'] ); // @codingStandardsIgnoreLine
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-envira' ),
			'<strong>' . esc_html__( 'Elementor Envira', 'elementor-envira' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor-envira' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses( $message, $this->wp_kses_allowed_html ) );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) { // @codingStandardsIgnoreLine
			unset( $_GET['activate'] ); // @codingStandardsIgnoreLine
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-envira' ),
			'<strong>' . esc_html__( 'Elementor Envira', 'elementor-envira' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor-envira' ) . '</strong>',
			$this->minimum_php_version
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses( $message, $this->wp_kses_allowed_html ) );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) { // @codingStandardsIgnoreLine
			unset( $_GET['activate'] ); // @codingStandardsIgnoreLine
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-envira' ),
			'<strong>' . esc_html__( 'Elementor Envira', 'elementor-envira' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'elementor-envira' ) . '</strong>',
			self::minimum_php_version
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses( $message, $this->wp_kses_allowed_html ) );
	}

}

// Instantiate Elementor_Envira.
new Elementor_Envira();
