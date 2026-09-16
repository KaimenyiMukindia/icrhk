<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
class GoodSoul_Footer_Newsletter extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_footer_newsletter';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Footer Newsletter ', 'goodsoul-core' );
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
				'label' => esc_html__( 'Footer Newsletter', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Newsletter' ),
			)
        );
        $this->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Subscribe us and get latest news &amp; <br>upcoming events.' ),
			)
        );
        $this->add_control(
			'shortcode',
			array(
				'label'   => esc_html__( 'ShortCode', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
			)
		);

		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$title           = $settings['title'];
        $content       = $settings['content'];
        $shortcode       = $settings['shortcode'];
		?>
            <div class="newsletter-widget footer-widget">
                <h4 class="widget-title"><?php echo $title; ?></h4>
                <div class="text"><?php echo $content; ?></div>
                <?php echo do_shortcode($shortcode); ?>
            </div>
		<?php
	}
}

\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Footer_Newsletter() );
