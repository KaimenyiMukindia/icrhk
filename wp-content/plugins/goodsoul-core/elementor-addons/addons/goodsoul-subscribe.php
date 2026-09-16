<?php
/** 
 * Elementor eventsub_area__o
 * @since 1.0.0
*/
class eventsub_area__o extends \Elementor\Widget_Base {
    public function get_name() {
        return 'eventsub_area__o';
    }
    public function get_title(){
        return esc_html__( 'eventsub event area', 'goodsoul-core' );
    }
    public function get_icon(){
        return 'fa fa-object-ungroup';
    }
    public function get_categories(){
        return [ 'goodsoulcore' ];
    }
    protected function register_controls() {
        $this->start_controls_section(
            'h_header',
            [
                'label' => esc_html__( 'eventsub Header area', 'goodsoul-core' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
			'left_img',
			[
				'label' => __( 'Left Img', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
                ]
			]
        );
        $this->add_control(
			'h_title',
			array(
				'label'   => esc_html__( 'Heading title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'UPCOMING EVENTS' ),
			)
        );
        $this->add_control(
			'main_title',
			array(
				'label'   => esc_html__( 'social ', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Complete our online registration form:' ),
			)
        );
        $this->end_controls_section();

        

    }
    protected function render() {
        $settings = $this->get_settings_for_display();
            $left_img = ( $settings['left_img']['id'] != '' ) ? wp_get_attachment_url( $settings['left_img']['id'], 'full' ) : $settings['left_img']['url'];
            $h_title = $settings['h_title'];
            $main_title = $settings['main_title'];
        ?>
       
            <div class="newsletter-two">
                <div class="auto-container">
                    <div class="wrapper-box">
                        <div class="row m-0 justify-content-between align-items-center">
                            <div class="column">
                                <div class="logo"><a href=""><img src="<?php echo esc_url($left_img); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></a></div>
                            </div>
                            <div class="column">
                                
                                <div class="someform">
                                    <?php echo do_shortcode($h_title); ?>
                                </div>
                            </div>
                            <div class="column">
                                <ul class="social-icon-three">
                                    <?php echo wp_kses_post($main_title); ?>
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }
}

\Elementor\Plugin::instance()->widgets_manager->register( new \eventsub_area__o() );
