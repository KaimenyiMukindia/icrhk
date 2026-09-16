<?php
class Goodsoul_How_contribute extends \Elementor\Widget_Base {

	public function get_name() {
		return 'good_how_contribute';
	}
	public function get_title() {
		return esc_html__( 'Goodsoul How Contribute', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return [ 'goodsoulcore' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'contribute',
			[
				'label' => esc_html__( 'Contribute', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'image_big',
			array(
				'label'   => __( 'Image', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_control(
			'video_url',
			array(
				'label'         => esc_html__( 'Video url', 'goodsoul-core' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'name',
			array(
				'label'   => esc_html__( 'Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Volunteer' ),
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label'   => __( 'Choose Icon', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-star',
					'library' => 'solid',
				),
			)
		);

		$repeater->add_control(
			'text',
			array(
				'label'   => esc_html__( 'Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Indignation & dislike men who sed <br>are like beguiled.' ),
			)
		);

		$repeater->add_control(
			'link_text',
			array(
				'label'   => esc_html__( 'Link Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Become a Volunteer' ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'         => esc_html__( 'Url', 'goodsoul-core' ),
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
			'items',
			array(
				'label'   => __( 'Items', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(
						'list_title'   => __( 'Question #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Question #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
		);

		$this->end_controls_section();
	}
	protected function render() {
		$settings  = $this->get_settings_for_display();
		$video_url = $settings['video_url'];
		$image_url = ( $settings['image_big']['id'] != '' ) ? wp_get_attachment_image_url( $settings['image_big']['id'], 'full' ) : $settings['image_big']['url'];

		?>
<div class="row">
	<div class="col-lg-6">
		<?php
		foreach ( $settings['items'] as $item ) {
			$name      = $item['name'];
			$text      = $item['text'];
			$icon      = $item['icon'];
			$link_text = $item['link_text'];
			$link      = $item['link']['url'];

			?>
		<div class="feature-block-three">
			<div class="inner-box">
				<div class="icon-box"><?php \Elementor\Icons_Manager::render_icon( ( $icon ), array( 'aria-hidden' => 'true' ) ); ?></div>
				<h4><?php echo $name; ?></h4>
				<div class="text"><?php echo $text; ?></div>
				<div class="link"><a href="<?php echo esc_url( $link ); ?>"><i class="flaticon-next"></i><?php echo $link_text; ?></a></div>
			</div>
		</div>
			<?php
		}
		?>
		  
	</div>
	<div class="col-lg-6">
		<div class="video-box">
			<img src="<?php echo esc_url( $image_url ); ?>" alt="">
			<a
				href="<?php echo esc_url( $video_url['url'] ); ?>"
				class="overlay-link lightbox-image video-fancybox"><span class="flaticon-multimedia-1"></span></a></div>
	</div>
</div>
		<?php
	}
}
\Elementor\Plugin::instance()->widgets_manager->register( new \Goodsoul_How_contribute() );
