<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
class GoodSoul_Footer_Instagram extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_footer_instagram';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Footer Instagram', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'aboutme_content',
			array(
				'label' => esc_html__( 'Footer Intagram', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'WorldWide Tour' ),
			)
        );
        $repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'image',
			array(
				'label'   => __( 'Instagram image', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);
        $repeater->add_control(
			'id_url',
			array(
				'label'         => esc_html__( 'Id link', 'goodsoul-core' ),
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
			'image_list',
			array(
				'label'     => __( 'Image List', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::REPEATER,
				'fields'    => $repeater->get_controls(),
				'default'   => array(
					array(
						'list_title'   => __( 'Image #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Image #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
        );
		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
        $title           = $settings['title'];
        $image_list      = $settings['image_list'];
		?>
            <div class="instagram-widget footer-widget">
                <h4 class="widget-title-two"><?php echo $title; ?></h4>
                <div class="wrapper-box">
                    <?php foreach($image_list as $image) { 
                        $img_url = $image['image']['url'];
                        $id_url = $image['id_url']['url'];
                        ?>
                    <div class="image">
                    <a href="<?php echo esc_url($id_url);?>"><img src="<?php echo esc_url($img_url);?>" alt=""></a>
                    </div>
                    <?php } ?> 
                </div>
            </div>
		<?php
	}
}

\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Footer_Instagram() );
