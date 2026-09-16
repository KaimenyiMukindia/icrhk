<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
class GoodSoul_Upcoming extends \Elementor\Widget_Base {







	public function get_name() {
		return 'goodsoul_upcoming';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Upcoming', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	public function get_script_depends() {
		return [ 'fun-fact' ];
	}
	protected function register_controls() {
		$this->start_controls_section(
			'upcoming_content',
			array(
				'label' => esc_html__( 'Upcoming Event', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Raise awareness and funds' ),
			)
		);
		$this->add_control(
			'sub_title',
			array(
				'label'   => esc_html__( 'Sub Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'UPCOMING EVENTS' ),
			)
		);

		$this->add_control(
			'evet_title',
			array(
				'label'   => esc_html__( 'Event Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Triathlon - 2019' ),
			)
		);

		$this->add_control(
			'evet_address',
			array(
				'label'   => esc_html__( 'Event Venue', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( '245 Holland Rd, Kensington, London W14 8BD, Uk <br>08 August 2019, 10.00am' ),
			)
		);

		$this->add_control(
			'evet_donate_bt',
			array(
				'label'   => esc_html__( 'Event Link Button Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Donate for event' ),
			)
		);

		$this->add_control(
			'evet_donate_url',
			array(
				'label'         => esc_html__( 'Event Donate Button URL', 'goodsoul-core' ),
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
			'evet_link_bt',
			array(
				'label'   => esc_html__( 'Event Link Button Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Book for event' ),
			)
		);

		$this->add_control(
			'evet_link_url',
			array(
				'label'         => esc_html__( 'Event Link Button URL', 'goodsoul-core' ),
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
			'event_date',
			array(
				'label'   => esc_html__( 'Event Date', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'desc'    => esc_html__( 'Enter date like (Y/M/D) e.g. (2025/12/28)', 'goodsoul-core' ),
				'default' => __( '2025/12/28' ),
			)
		);

		$this->add_control(
			'back_img',
			[
				'label'     => __( 'Background Img', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'selectors' => [
					'{{WRAPPER}} .upcoming-events-section-two' => 'background-image: url({{URL}})',
				],
			]
		);

		$this->end_controls_section();

	}
	protected function render() {
		$settings        = $this->get_settings_for_display();
		$title           = $settings['title'];
		$sub_title       = $settings['sub_title'];
		$evet_title      = $settings['evet_title'];
		$evet_address    = $settings['evet_address'];
		$event_date      = $settings['event_date'];
		$evet_donate_bt  = $settings['evet_donate_bt'];
		$evet_link_bt    = $settings['evet_link_bt'];
		$evet_link_url   = $settings['evet_link_url']['url'];
		$evet_donate_url = $settings['evet_donate_url']['url'];
		?>
<section class="upcoming-events-section-two">
	<div class="auto-container">
		<div class="sec-title text-center light">
			<h5><?php echo $title; ?></h5>
			<h1><?php echo $sub_title; ?></h1>
		</div>
		<div class="wrapper-box">
			<div class="countdown-timer-two">
				<div class="default-coundown">
					<div class="box">
						<div class="countdown time-countdown-three" data-countdown-time="<?php echo $event_date; ?>"></div>
					</div>
				</div>
			</div>
			<div class="event-block-five">
				<h1><?php echo $evet_title; ?></h1>
				<div class="text"><?php echo $evet_address; ?></div>
				<div class="link-box">
					<a href="<?php echo esc_url( $evet_link_url ); ?>" class="theme-btn btn-style-one"><span>  <?php echo $evet_donate_bt; ?></span></a>
					<a href="<?php echo esc_url( $evet_donate_url ); ?>" class="theme-btn btn-style-thirteen"><span> <?php echo $evet_link_bt; ?></span></a></div>
			</div>
		</div>

	</div>
</section>
		<?php
	}
}

\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Upcoming() );
