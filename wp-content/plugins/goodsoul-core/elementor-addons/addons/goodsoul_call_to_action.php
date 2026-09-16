<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
use \Elementor\Icons_Manager;
class GoodSoul_Call_To_Action extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_call_to_action';
	}
	
	public function get_title() {
		return esc_html__( 'GoodSoul Call To Action', 'goodsoul-core' );
	}

	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'gallery_content',
			array(
				'label' => esc_html__( 'Gallery', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'bg_image',
			array(
				'label'   => __( 'Background image', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_control(
			'tagline',
			array(
				'label'       => esc_html__( 'Tagline', 'goodsoul-core' ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'CHOOSE THE PLACE TO' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'goodsoul-core' ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => __( 'BECOME A GOODSOUL VOLUNTEER' ),
			)
		);

		$this->add_control(
			'content',
			array(
				'label'       => esc_html__( 'Text', 'goodsoul-core' ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => __( 'Must explain to you how all this mistaken idea of denouncing pleasure and <br> we will give you a complete the system teaching.' ),
			)
		);

		$this->add_control(
			'btn_name',
			array(
				'label'   => esc_html__( 'Button name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Become a Volunteer' ),
			)
		);

		$this->add_control(
			'page_link',
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

	}
	protected function render() {
		$settings = $this->get_settings_for_display();

		$tagline   = $settings['tagline'];
		$title     = $settings['title'];
		$content   = $settings['content'];
		$btn_name  = $settings['btn_name'];
		$page_link = $settings['page_link']['url'];
		$bg_image = ( $settings['bg_image']['id'] != '' ) ? wp_get_attachment_url( $settings['bg_image']['id'], 'full' ) : $settings['bg_image']['url'];
		
	?>
	
	<!-- Call To Action Two -->
	<div class="call-to-action-two" style="background-image: url(<?php echo esc_url( $bg_image ); ?>);">
		<div class="auto-container">
			<div class="text-center">
				<h5><?php echo wp_kses_post( $tagline ); ?></h5>
				<h1><?php echo wp_kses_post( $title ); ?></h1>
				<div class="text"><?php echo wp_kses_post( $content ); ?></div>
				<?php if ( ! empty( $page_link ) ) { ?>
				<div class="link-btn"><a href="<?php echo esc_url( $page_link ); ?>" class="theme-btn btn-style-fifteen"><span><?php echo wp_kses_post( $btn_name ); ?></span></a></div>
				<?php } ?>
			</div>
		</div>
	</div>
	
						<?php
	}
}

\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Call_To_Action() );
