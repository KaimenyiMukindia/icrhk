<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
	require_once plugin_dir_path( __FILE__ ) . 'register-categories.php';



	define( 'GOODSOUL_FILE', __FILE__ );
	define( 'GOODSOUL_PATH', dirname( GOODSOUL_FILE ) );
	define( 'GOODSOUL_URL', plugins_url( '', GOODSOUL_FILE ) );

// if ( ! function_exists( 'goodsoul_custom_icon' ) ) {
// function Goodsoul_custom_icon( $array ) {
// $plugin_url = plugins_url();
// return array(
// 'custom-icon' => array(
// 'name'          => 'custom-icon',
// 'label'         => 'Loveus Icon',
// 'url'           => '',
// 'enqueue'       => array(
// $plugin_url . '/goodsoul-core/elementor-addons/assets/icon/style.css',
// ),
// 'prefix'        => '',
// 'displayPrefix' => '',
// 'labelIcon'     => 'fab fa-font-awesome-alt',
// 'ver'           => '5.9.0',
// 'fetchJson'     => $plugin_url . '/goodsoul-core/elementor-addons/assets/js/regular.js',
// 'native'        => 1,
// ),
// );
// }
// }
// add_filter( 'elementor/icons_manager/additional_tabs', 'goodsoul_custom_icon' );

/**
 * Main Goodsoul Core Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
final class Goodsoul_Elementor {


	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '5.6';

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @static
	 *
	 * @var Goodsoul_Elementor The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @static
	 *
	 * @return Goodsoul_Elementor An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'i18n' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ) );

	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 *
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function i18n() {

		load_plugin_textdomain( 'goodsoulcore' );

	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );
			return;
		}

		// Add Plugin actions
		add_action( 'elementor/widgets/register', array( $this, 'init_widgets' ) );
		// add_action( 'elementor/controls/controls_registered', [ $this, 'init_controls' ] );
		 add_action( 'elementor/frontend/after_register_scripts', array( $this, 'widget_scripts' ) );
		//add_action( 'wp_footer', array( $this, 'widget_scripts' ) );

		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'pricing_editor_assets' ) );

	}

	/**
	 * widget_scripts
	 *
	 * Load required plugin core files.
	 *
	 * @since  1.2.0
	 * @access public
	 */
	public function widget_scripts() {

		wp_enqueue_script( 'lazyload', plugins_url( 'assets/js/lazyload.js', __FILE__ ), array( 'jquery' ), time(), true );
		wp_enqueue_script( 'loveus-script', plugins_url( 'assets/js/addons-script.js', __FILE__ ), array( 'jquery' ), time(), true );
		wp_register_script( 'cause-slider', plugins_url( 'assets/js/charity.js', __FILE__ ), array( 'jquery' ), time(), true );
		wp_register_script( 'fun-fact', plugins_url( 'assets/js/funfact.js', __FILE__ ), array( 'jquery' ), time(), true );
		wp_register_script( 'counter', plugins_url( 'assets/js/counter.js', __FILE__ ), array( 'jquery' ), time(), true );
		wp_register_script( 'volunteer', plugins_url( 'assets/js/volunteer.js', __FILE__ ), array( 'jquery' ), time(), true );
	}

	public function pricing_editor_assets() {
		wp_enqueue_script( 'goodsoul-editor-script', plugins_url( 'assets/js/editor-script.js', __FILE__ ), array( 'jquery' ), true, true );
	}



	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'goodsoulcore' ),
			'<strong>' . esc_html__( 'Goodsoul Core', 'goodsoulcore' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'goodsoulcore' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'goodsoulcore' ),
			'<strong>' . esc_html__( 'Goodsoul Core', 'goodsoulcore' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'goodsoulcore' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */


	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'goodsoulcore' ),
			'<strong>' . esc_html__( 'Goodsoul Core', 'goodsoulcore' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'goodsoulcore' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Init Widgets
	 *
	 * Include widgets files and register them
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init_widgets() {

		include_once __DIR__ . '/addons/goodsoul_slider.php';
		include_once __DIR__ . '/addons/goodsoul_about.php';
		include_once __DIR__ . '/addons/goodsoul_funfact.php';
		include_once __DIR__ . '/addons/goodsoul_testimonial.php';

		include_once __DIR__ . '/addons/goodsoul_team_home.php';
		include_once __DIR__ . '/addons/goodsoul_team_page.php';
		include_once __DIR__ . '/addons/goodsoul_sponsors.php';

		include_once __DIR__ . '/addons/goodsoul_feature.php';
		include_once __DIR__ . '/addons/goodsoul_project.php';
		include_once __DIR__ . '/addons/goodsoul_volunteer.php';
		include_once __DIR__ . '/addons/goodsoul_caring.php';

		include_once __DIR__ . '/addons/goodsoul_payment.php';
		include_once __DIR__ . '/addons/goodsoul_whychoose.php';
		include_once __DIR__ . '/addons/goodsoul_blogs.php';
		include_once __DIR__ . '/addons/goodsoul_history.php';
		include_once __DIR__ . '/addons/goodsoul_faq.php';
		include_once __DIR__ . '/addons/goodsoul_portfolio_grid.php';
		include_once __DIR__ . '/addons/goodsoul_portfolio_masonry.php';
		include_once __DIR__ . '/addons/goodsoul_portfolio.php';
		include_once __DIR__ . '/addons/goodsoul_gallery.php';
		include_once __DIR__ . '/addons/goodsoul_call_to_action.php';
		include_once __DIR__ . '/addons/goodsoul_form.php';
		require_once __DIR__ . '/addons/goodsoul-upcomming.php';
		if ( class_exists( 'Charitable' ) ) {
			include_once __DIR__ . '/addons/goodsoul_donor.php';
			include_once __DIR__ . '/addons/goodsoul-chariti.php';
			include_once __DIR__ . '/addons/goodsoul_donation_form.php';
			include_once __DIR__ . '/addons/goodsoul_recent_donor.php';
			include_once __DIR__ . '/addons/goodsoul_intro.php';
		}

		if ( class_exists( 'Tribe__Events__Main' ) ) {

			include_once __DIR__ . '/addons/goodsoul_events.php';
			include_once __DIR__ . '/addons/goodsoul_footer_event.php';

		}

		include_once __DIR__ . '/addons/goodsoul-gallery.php';
		include_once __DIR__ . '/addons/goodsoul-contact.php';
		include_once __DIR__ . '/addons/goodsoul-subscribe.php';
		include_once __DIR__ . '/addons/goodsoul_footer_contact.php';
		include_once __DIR__ . '/addons/goodsoul_footer_newsletter.php';
		include_once __DIR__ . '/addons/goodsoul_footer_about.php';
		include_once __DIR__ . '/addons/goodsoul_footer_post.php';
		include_once __DIR__ . '/addons/goodsoul_footer_map.php';
		include_once __DIR__ . '/addons/goodsoul_footer_instagram.php';
		include_once __DIR__ . '/addons/goodsoul_footer_twitter.php';

		include_once __DIR__ . '/addons/goodsoul_blog_selector.php';
		include_once __DIR__ . '/addons/goodsoul_how_to_contribute.php';

		include_once __DIR__ . '/addons/goodsoul_event_celebs.php';
	}
}
Goodsoul_Elementor::instance();
