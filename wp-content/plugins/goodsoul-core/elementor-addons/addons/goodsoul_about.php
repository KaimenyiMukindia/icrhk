<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
class GoodSoul_About extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_about';
	}
	public function get_title() {
		return esc_html__( 'Good Soul About', 'goodsoul-core' );
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
				'label' => esc_html__( 'About', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'select_layout',
			array(
				'label'   => esc_html__( 'Select Layout', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'style_1' => esc_html__( 'Style 1', 'goodsoul-core' ),
					'style_2' => esc_html__( 'Style 2', 'goodsoul-core' ),
					'style_3' => esc_html__( 'Style 3', 'goodsoul-core' ),

				),
				'default' => esc_html__( 'style_1', 'goodsoul-core' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Be part of a change <br>you want to see in the world' ),
			)
		);

		$this->add_control(
			'words',
			array(
				'label'   => esc_html__( 'Some Words', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( '“Generosity consists not of the sum given, but the manner in <br>which it is bestowed.”' ),
			)
		);
		$this->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'How all this mistaken idea of denouncing pleasure and praising pain was born <br>and I will give you a complete account of the system expound the actually <br>teachings of the great explorer of the truth pursues.' ),
			)
		);

		$this->add_control(
			'help_content',
			array(
				'label'     => esc_html__( 'Help words', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => __( 'NEED ASSISTANT FOR <br/> JOIN WITH US?' ),
				'condition' => array( 'select_layout' => 'style_3' ),
			)
		);

		$this->add_control(
			'phone',
			array(
				'label'     => esc_html__( 'Phone Number', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => __( '+211 456 7890' ),
				'condition' => array( 'select_layout' => 'style_3' ),
			)
		);

		$this->add_control(
			'mission_content',
			array(
				'label'     => esc_html__( 'Mission Summary', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => __( 'Beguiled and demoralized by the charms off pleasure the moments, so by desire trouble.' ),
				'condition' => array( 'select_layout' => 'style_1' ),
			)
		);
		$this->add_control(
			'vision_content',
			array(
				'label'     => esc_html__( 'Vision Summary', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => __( 'The great explorer of the truth, theats masters builders off human happiness no one rejects.' ),
				'condition' => array( 'select_layout' => 'style_1' ),
			)
		);
		$this->add_control(
			'icon_image',
			array(
				'label'     => __( 'Background Icon image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'select_layout' => 'style_1' ),
			)
		);

		$this->add_control(
			'image_1',
			array(
				'label'   => __( 'Background Image', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);
		$this->add_control(
			'experiance',
			array(
				'label'     => esc_html__( 'Experience ', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => __( '32+ <br>Years experience' ),
				'condition' => array( 'select_layout' => 'style_2' ),
			)
		);
		$this->add_control(
			'image_2',
			array(
				'label'     => __( 'Background Second Image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'select_layout' => array( 'style_1', 'style_2' ),
				),
			)
		);
		$this->add_control(
			'image_3',
			array(
				'label'     => __( 'Background Third Image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'select_layout' => 'style_1' ),
			)
		);
		$this->start_controls_tabs(
			'action_button_tabs'
		);
		$this->start_controls_tab(
			'first_button',
			array(
				'label' => __( 'Button 1', 'goodsoul-core' ),
			)
		);
		$this->add_control(
			'button_1_text',
			array(
				'label'   => esc_html__( 'Button Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Our Mission', 'goodsoul-core' ),
			)
		);
		$this->add_control(
			'button_1_link',
			array(
				'label'         => esc_html__( 'Button link', 'goodsoul-core' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'second_button',
			array(
				'label'     => __( 'Button 2', 'goodsoul-core' ),
				'condition' => array( 'select_layout' => 'style_1' ),
			)
		);
		$this->add_control(
			'button_2_text',
			array(
				'label'   => esc_html__( 'Second Button Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Our Vision', 'goodsoul-core' ),
			)
		);
		$this->add_control(
			'button_2_link',
			array(
				'label'         => esc_html__( 'Second Button link', 'goodsoul-core' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'third_button',
			array(
				'label'     => __( 'Button 3', 'goodsoul-core' ),
				'condition' => array( 'select_layout' => 'style_1' ),
			)
		);
		$this->add_control(
			'button_3_text',
			array(
				'label'   => esc_html__( 'Third Button Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'More About Us', 'goodsoul-core' ),
			)
		);
		$this->add_control(
			'button_3_link',
			array(
				'label'         => esc_html__( 'Second Button link', 'goodsoul-core' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);
		$this->end_controls_tab();
		$this->add_control(
			'youtube_link',
			array(
				'label'         => esc_html__( 'Youtube link', 'goodsoul-core' ),
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
			'point',
			array(
				'label'   => esc_html__( 'Point Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Give Donation' ),
			)
		);

		$repeater->add_control(
			'point_summary',
			array(
				'label'   => esc_html__( 'Point Summary', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Indignation and dislikemen who are so beguiled and demoral.' ),
			)
		);

		$this->add_control(
			'points',
			array(
				'label'     => __( 'Point List', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::REPEATER,
				'fields'    => $repeater->get_controls(),
				'default'   => array(
					array(
						'list_title'   => __( 'Point #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Point #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
				'condition' => array( 'select_layout' => 'style_3' ),
			)
		);
		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();

		$select_layout   = $settings['select_layout'];
		$title           = $settings['title'];
		$some_word       = $settings['words'];
		$content         = $settings['content'];
		$experiance      = $settings['experiance'];
		$button_1_name   = $settings['button_1_text'];
		$button_1_link   = $settings['button_1_link']['url'];
		$button_2_name   = $settings['button_2_text'];
		$button_2_link   = $settings['button_2_link']['url'];
		$button_3_name   = $settings['button_3_text'];
		$button_3_link   = $settings['button_3_link']['url'];
		$mission_content = $settings['mission_content'];
		$vision_content  = $settings['vision_content'];
		$youtube_link    = $settings['youtube_link']['url'];

		$icon_image = isset($settings['icon_image']['id']) ? wp_get_attachment_url( $settings['icon_image']['id'], 'full' ) : '#';
		$image_1 = isset( $settings['image_1']['id']) ? wp_get_attachment_url( $settings['image_1']['id'], 'full' ) : '#';
		$image_2 = isset( $settings['image_2']['id'] ) ? wp_get_attachment_url( $settings['image_2']['id'], 'full' ) : '#';
		$image_3 = isset( $settings['image_3']['id'] ) ? wp_get_attachment_url( $settings['image_3']['id'], 'full' ) : '#';
	
		$help_content    = $settings['help_content'];
		$phone           = $settings['phone'];
		$points          = $settings['points'];
		?>
	<!-- About Section -->
		<?php if ( $select_layout == 'style_1' ) { ?>
	<section class="about-section">
		<div class="auto-container">
			<div class="row">
				<div class="col-lg-6">
					<div class="about-content-block">
						<h1><?php echo wp_kses_post( $title ); ?></h1>
						<h4><?php echo wp_kses_post( $some_word ); ?></h4>
						<div class="text wow fadeInUp" data-wow-delay="200ms"><?php echo wp_kses_post( $content ); ?></div>
						<div class="row">
							<div class="col-md-6">
							<?php if ( ! empty( $button_1_link ) ) { ?>
								<div class="link-btn wow fadeInLeft" data-wow-delay="500ms"><a href="<?php echo esc_url( $button_1_link ); ?>" class="theme-btn btn-style-two"><i class="flaticon-next"></i><span><?php echo wp_kses_post( $button_1_name ); ?></span></a></div>
								<?php } ?>
								<div class="text"><?php echo wp_kses_post( $mission_content ); ?></div>
							</div>
							<div class="col-md-6">
								<?php if ( ! empty( $button_2_link ) ) { ?>
								<div class="link-btn wow fadeInRight" data-wow-delay="900ms"><a href="<?php echo esc_url( $button_2_link ); ?>" class="theme-btn btn-style-three"><i class="flaticon-next"></i><span><?php echo wp_kses_post( $button_2_name ); ?></span></a></div>
								<?php } ?>
								<div class="text"><?php echo wp_kses_post( $vision_content ); ?></div>
							</div>
						</div>
						<div class="link-btn-two">
							<?php if ( ! empty( $button_3_link ) ) { ?>
							<a href="<?php echo esc_url( $button_3_link ); ?>" class="theme-btn btn-style-one"><span><?php echo wp_kses_post( $button_3_name ); ?></span></a>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="about-image-block">
						<?php if ( ! empty( $icon_image ) ) { ?>
						<div class="logo-box"><div class="image wow zoomIn" data-wow-delay="500ms"><img src="<?php echo esc_url( $icon_image ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></div></div>
						<?php } ?>
						<?php if ( ! empty( $image_1 ) ) { ?>
						<div class="image-one wow fadeInUp" data-wow-delay="200ms"><img src="<?php echo esc_url( $image_1 ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></div>
						<?php } ?>
						<?php if ( ! empty( $image_2 ) ) { ?>
						<div class="image-two"><img src="<?php echo esc_url( $image_2 ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
							<?php if ( ! empty( $youtube_link ) ) { ?>
							<a href="<?php echo esc_url( $youtube_link ); ?>" class="overlay-link lightbox-image video-fancybox"><span class="flaticon-multimedia"></span></a>
							<?php } ?>
						</div>
						<?php } ?>
						<?php if ( ! empty( $image_3 ) ) { ?>
						<div class="image-three wow fadeInRight" data-wow-delay="200ms"><img src="<?php echo esc_url( $image_3 ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php } ?>
	<!-- About Section Two -->
		<?php if ( $select_layout == 'style_2' ) { ?>
	<section class="about-section-two">
		<div class="auto-container">
			<div class="sec-title text-center">
				<h5><?php echo wp_kses_post( $title ); ?></h5>
				<h1><?php echo wp_kses_post( $some_word ); ?></h1>
			</div>
			<div class="row">
				<div class="col-lg-6">
					<div class="row">
						<div class="col-md-6">
							<div class="image">
								<img src="<?php echo esc_url( $image_1 ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
								<h4><?php echo wp_kses_post( $experiance ); ?></h4>
							</div>
						</div>
						<div class="col-md-6">
							<div class="image">
								<img src="<?php echo esc_url( $image_2 ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
								<?php if ( ! empty( $youtube_link ) ) { ?>
								<a href="<?php echo esc_url( $youtube_link ); ?>" class="lightbox-image video-link video-btn"><span class="flaticon-multimedia-1"></span></a>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="text"><?php echo wp_kses_post( $content ); ?></div>
					<?php if ( ! empty( $button_1_link ) ) { ?>
					<div class="link-btn"><a href="<?php echo esc_url( $button_1_link ); ?>"><span class="flaticon-next"></span><?php echo wp_kses_post( $button_1_name ); ?></a></div>
					<?php } ?>
				</div>
			</div>
		</div>
	</section>
	<?php } ?>

		<?php if ( $select_layout == 'style_3' ) { ?>
	<!-- About Section Three -->
	<section class="about-section-three">
		<div class="auto-container">
			<div class="row">
				<div class="col-lg-6">
					<div class="image-box">
						<div class="image"><img src="<?php echo esc_url( $image_1 ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></div>
						<div class="contact-box">
							<div class="icon-box"><span class="flaticon-phone-1"></span></div>
							<h5><?php echo wp_kses_post( $help_content ); ?></h5>
							<h4><a href="tel:<?php echo esc_html( $phone ); ?>"><?php echo wp_kses_post( $phone ); ?></a></h4>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="content">
						<h1><?php echo wp_kses_post( $title ); ?></h1>
						<h4><?php echo wp_kses_post( $some_word ); ?></h4>
						<div class="text"><?php echo wp_kses_post( $content ); ?></div>
						<?php
						foreach ( $points as $point ) {
							$point_name    = $point['point'];
							$point_summary = $point['point_summary'];
							?>
						<div class="point-block">
							<span class="flaticon-tick"></span>
							<h4><?php echo wp_kses_post( $point_name ); ?></h4>
							<div class="text"><?php echo wp_kses_post( $point_summary ); ?></div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</section>
		<?php 
		}
	}
}
\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_About() );
