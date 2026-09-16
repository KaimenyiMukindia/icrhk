<?php
/** 
 * Elementor  Footer_gallery  Footer_gallery
 * @since 1.0.0
*/
class  Footer_gallery extends \Elementor\Widget_Base {
    public function get_name() {
        return 'footer_gallery';
    }
    
    public function get_title(){
        return esc_html__( 'Footer gallery', 'goodsoul-core' );
    }
    public function get_icon(){
        return 'fa fa-object-ungroup';
    }
    public function get_categories(){
        return [ 'goodsoulcore' ];
    }
    protected function register_controls() {
        $this->start_controls_section(
            'footer_gallery-area',
            [
                'label' => esc_html__( 'Gallery', 'goodsoul-core' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'widget_title',
            [
                'label' => esc_html__( 'Title', 'goodsoul-core' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'title', 'goodsoul-core' ),
            ]
        );       
        $this->end_controls_section();
        $this->start_controls_section(
            'gallery_list_area',
            [
                'label' => esc_html__( 'Gallery area', 'goodsoul-core' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $repeater = new \Elementor\Repeater();
        
        $repeater->add_control(
            'gallery_list_img', [
                'label' => __( 'Image area', 'goodsoul-core' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $this->add_control(
            'gallery_list', [
                'label' => esc_html__( 'gallery list', 'goodsoul-core' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );
        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_title = $settings['widget_title'];
        $gallery_list = $settings['gallery_list'];
    ?>
            <div class=" instagram-widget footer-widget">
                <h4 class="widget-title-two"><?php echo esc_html( $widget_title ); ?></h4>
                <div class="wrapper-box">
                    <?php foreach ( $gallery_list as $item ) {
                        $gallery_list_bg = ( $item['gallery_list_img']['id'] != '' ) ? wp_get_attachment_url( $item['gallery_list_img']['id'], 'full' ) : $item['gallery_list_img']['url'];
                    ?>
                        <div class="image">
                            <a href="#"><img src="<?php echo esc_attr($gallery_list_bg); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></a>
                        </div>
                    <?php } ?>
                </div>                
            </div>
        <?php
    }
}
\Elementor\Plugin::instance()->widgets_manager->register( new \Footer_gallery() );