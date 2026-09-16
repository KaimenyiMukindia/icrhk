<?php
/**
 * Envira Plugin class.
 *
 * @since 1.0.0
 *
 * @package Envira_Lightroom
 * @author  Envira Team
 */

namespace ElementorEnvira;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Envira class.
 *
 * @since 1.0.0
 *
 * @package Envira_Elementor
 * @author  Envira Team
 */
class Plugin {

    /**
     * Instance
     *
     * @since 1.0.0
     * @access private
     * @static
     *
     * @var Plugin The single instance of the class.
    */
    private static $_instance = null;

    /**
     *  Plugin class constructor
     *
     * Register plugin action hooks and filters
     *
     * @since 1.2.0
     * @access public
     */
    public function __construct() {

        // Register widget scripts.
        add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );

        // Register widgets.
        add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );

        add_action( 'elementor/elements/categories_registered', array( $this, 'add_elementor_widget_categories' ) );
        add_action( 'wp_ajax_envira_gallery_elementor_generate_gallery_html', array( $this, 'render_gallery' ), 10 );
        add_action( 'wp_ajax_nopriv_envira_gallery_elementor_generate_gallery_html', array( $this, 'render_gallery' ), 10 );

    }

    /**
     *  Render the gallery.
     *
     * Register plugin action hooks and filters
     *
     * @since 1.2.0
     * @access public
     */
    public function render_gallery() {
        
        $nonce = $_POST['nonce'];

        if ( ! wp_verify_nonce( $nonce, 'envira-elementor-nonce' ) ) {
            return;
        }

        error_log('render_gallery - start'); // @codingStandardsIgnoreLine

        // Check if variables exist.
        if ( ! isset( $_REQUEST['gallery_id'] ) || false === $_REQUEST['gallery_id'] ) { // @codingStandardsIgnoreLine
            echo 'Select a gallery from the settings dropdown.';
        }
        ob_start();
        echo do_shortcode( '[envira-gallery id="' . intval( $_REQUEST['gallery_id'] ) . '"]' ); // @codingStandardsIgnoreLine
        $output = ob_get_clean();
        
        error_log('render_gallery - stop'); // @codingStandardsIgnoreLine
        
        echo $output; 
        exit;

    }
  

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @since 1.2.0
     * @access public
     *
     * @return Plugin An instance of the class.
     */
    public static function instance() {

        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
           
        return self::$_instance;

    }
 
    /**
     * widget_scripts
     *
     * Load required plugin core files.
     *
     * @since 1.2.0
     * @access public
     */
    public function widget_scripts() {
        wp_register_script( 'elementor-envira', plugins_url( '/assets/js/envira.js', __FILE__ ), [ 'jquery' ], false, true );
        wp_enqueue_script( 'ajax-script', get_template_directory_uri() . '/js/my-ajax-script.js', array('jquery') ); // @codingStandardsIgnoreLine

        wp_localize_script( 'ajax-script', 'envira_elementor',
                array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    }
 
    /**
     * Include Widgets files
     *
     * Load widgets files
     *
     * @since 1.2.0
     * @access private
     */
    private function include_widgets_files() {

        require_once __DIR__ . '/widgets/envira.php';

    }
 
    /**
     * Register Widgets
     *
     * Register new Elementor widgets.
     *
     * @since 1.2.0
     * @access public
     */
    public function register_widgets() {

        // Its is now safe to include Widgets files.
        $this->include_widgets_files();

        // Register Widgets
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Envira() );

    }
 
	public function add_elementor_widget_categories( $elements_manager ) {

		$elements_manager->add_category(
			'envira-gallery',
			[
				'title' => __( 'Envira Gallery', 'envira-elementor' ),
				'icon'  => 'fa fa-envira',
			]
		);
	}

}

// Instantiate Plugin Class
Plugin::instance();