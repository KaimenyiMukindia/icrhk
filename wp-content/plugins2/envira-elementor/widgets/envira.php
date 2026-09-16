<?php
/**
 * Envira Widget class.
 *
 * @since 1.0.0
 *
 * @package Envira_Lightroom
 * @author  Envira Team
 */

namespace ElementorEnvira\Widgets;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Envira\Frontend\Shortcode;

/**
 * Envira class.
 *
 * @since 1.0.0
 *
 * @package Envira_Elementor
 * @author  Envira Team
 */
class Envira extends Widget_Base {

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     *
     * @param   array $data Data.
     * @param   array $args Settings.
     * @return  string              Access Token
    */
    public function __construct( $data = array(), $args = null ) {

        parent::__construct($data, $args);

            $version = time();

            wp_localize_script(
                ENVIRA_SLUG . '-script',
                'envira_gallery',
                array(
                    'debug'      => false,
                    'll_delay'   => 500,
                    'll_initial' => 'false',
                    'll'         => 'false',
                    'mobile'     => false,
                    'nonce'      => wp_create_nonce( 'envira-elementor-nonce' ),
                )
            );
        
        wp_enqueue_style( ENVIRA_SLUG . '-style' );

        wp_enqueue_style( ENVIRA_SLUG . '-jgallery' );
        
        add_filter( 'elementor/widget/print_template', array( $this, 'skin_print_template' ), 10, 2 );


    }

    /**
     * Retrieve the widget name.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'envira';
    }
  
    /**
     * Retrieve the widget title.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {

        return __( 'Envira', 'elementor-envira' );

    }

    public function get_script_depends() {

        return array( 'envira-gallery-script', 'envira-gallery-style', 'envira-gallery-jgallery');

    }
  
    /**
     * Retrieve the widget icon.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {

        return 'fa fa-envira';

    }
 
    /**
     * Retrieve the list of categories the widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * Note that currently Elementor supports only one category.
     * When multiple categories passed, Elementor uses the first one.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {

        return [ 'envira-gallery' ];

    }
  
    /**
     * Register the widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.1.0
     *
     * @access protected
     */
    protected function _register_controls() { // @codingStandardsIgnoreLine

        if ( function_exists('envira_get_galleries') ) {
            $galleries        = envira_get_galleries( false, true );
            $gallery_dropdown = array();
            foreach ( $galleries as $gallery ) {
                $gallery_dropdown[ $gallery['id'] ] = $gallery['config']['title'];
            }
            // Sort Galleries
            natsort( $gallery_dropdown );
        }

        $this->start_controls_section(
                'section_content',
            array(
                'label' => __( 'Gallery', 'elementor-envira' ),
            )
        );

        $this->add_control(
            'envira_gallery_id',
            [
            'label'   => __( 'Gallery', 'plugin-name' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => $gallery_dropdown,
            'default' => false,
            ]
        );
      

        $this->end_controls_section();
    }
 
    /**
     * Render the widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.1.0
     *
     * @access protected
     */
    protected function render() {
        $setting = $this->get_settings_for_display();

        if ( empty( $setting ) && '' === $setting['envira_gallery_id'] || false === $setting['envira_gallery_id'] || 'default' === $setting['envira_gallery_id'] ) { 
            return;
        }

        ?>

        <?php echo do_shortcode( '[envira-gallery id="'.intval($setting['envira_gallery_id']).'"]' ); ?>

        <?php if ( Plugin::$instance->editor->is_edit_mode() ) : ?>

        <?php endif;

    }



    public function skin_print_template( $content, $button ) {
        return $content;
    }

    /**
     * Render the widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since 1.1.0
     *
     * @access protected
     */
    protected function _content_template() { // @codingStandardsIgnoreLine
    ?>

    <div class="envira-elementor-container gallery-{{{ settings.envira_gallery_id }}}">
        <div class="envira-elementor-start">
        </div>
    </div> 

    <script type="text/javascript">

    jQuery.ajax(envira_elementor.ajax_url, {
			type: "POST",
			data: {
				nonce: envira_gallery.nonce,
        action: 'envira_gallery_elementor_generate_gallery_html',
				gallery_id: "{{{ settings.envira_gallery_id }}}",
			},
			success: function(response) {
        // Tell the view we've finished successfully
        console.log(response);
        var findiv = jQuery('body').find('.envira-elementor-container.gallery-{{{ settings.envira_gallery_id }}}').find('div.envira-elementor-start');
        findiv.html(response);
			},
			error: function(error_message) {
				// Tell wp.media we've finished, but there was an error
        alert( "error" );
				alert( "{{{ settings.envira_gallery_id }}}" );        
			},
    });

    </script>

    <?php
    }
}