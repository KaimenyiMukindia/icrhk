<?php

/** 
 * Elementor donors list Loveus_sponsors__o
 * @since 1.0.0
*/
use \Elementor\Repeater;
use \Elementor\Icons_Manager;

class GoodSoul_Payment extends \Elementor\Widget_Base {
    public function get_name() {
        return 'goodsoul_payment';
    }
    public function get_title(){
        return esc_html__( 'Goodsoul Payment', 'goodsoul-core' );
    }
    public function get_icon(){
        return 'fa fa-object-ungroup';
    }
    public function get_categories(){
        return [ 'goodsoulcore' ];
    }
    protected function register_controls() {
        $this->start_controls_section(
            'mission_list',
            [
                'label' => esc_html__( 'Payment', 'goodsoul-core' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
			'bg_image',
			array(
				'label'     => __( 'Background Image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
        );
        
        $this->add_control(
			'tagline',
			array(
				'label'   => esc_html__( 'Tagline', 'goodsoul-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __( 'HOW CAN YOU DONATE' ),
			)
        );

        $this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __( 'OUR DONARS CAN DONATE ONLINE<br> IN DIFFERENT WAYS' ),
			)
        );
        
        
        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'methode_logo', [
                'label' => esc_html__( 'Methode Logo', 'goodsoul-core' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $repeater->add_control(
			'methode_link',
			array(
				'label'         => esc_html__( 'Page link', 'goodsoul-core' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);
        $this->add_control(
            'methode_list', [
                'label' => esc_html__( ' Mathode List', 'goodsoul-core' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );
        $this->end_controls_section();
  

    }
    protected function render() {
        $settings = $this->get_settings_for_display();
        $bg_image = ( $settings['bg_image']['id'] != '' ) ? wp_get_attachment_url( $settings['bg_image']['id'], 'full' ) : $settings['bg_image']['url'];
    
        $tagline = $settings['tagline'];
        $title = $settings['title'];
        $methode_list = $settings['methode_list'];
        ?>

        <!-- Via Payment Section -->
        <section class="via-payment-section" style="background-image: url(<?php echo esc_url($bg_image); ?>);">
        <div class="auto-container">
            <h5><?php echo wp_kses_post( $tagline ); ?></h5>
            <h1><?php echo wp_kses_post( $title ); ?> </h1>
            <div class="five-item-carousel owl-theme owl-carousel owl-nav-none owl-dots-none">
            <?php if( $methode_list ) : ?>
                <?php foreach ( $methode_list as $item ) { 
                    $image = $item['methode_logo']['url'] ;
                    $link = $item['methode_link']['url'];?>
                <div class="image"><a href="<?php echo esc_url($link); ?>"><img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></a></div>
                <?php } ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    

    
        <?php
    }
}
\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Payment() );