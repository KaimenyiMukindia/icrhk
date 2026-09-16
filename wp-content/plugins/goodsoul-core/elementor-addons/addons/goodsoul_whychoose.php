<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
use \Elementor\Icons_Manager;

class GoodSoul_Why_Choose extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_whychoose';
	}
	public function get_title() {
		return esc_html__( 'Why Choose Us', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'whychoose_content',
			array(
				'label' => esc_html__( 'Why Choose Us', 'goodsoul-core' ),
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

				),
				'default' => esc_html__( 'style_1', 'goodsoul-core' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Why sign up as a giver on <br>the goodsoul?' ),
			)
		);

		$this->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'We have a deep passion for the work that we do to improve the efficiency and effectiveness of the non-profit sector.' ),
			)
		);

		$this->add_control(
			'image',
			array(
				'label'   => __( 'Choose Image', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_control(
			'icon_image',
			array(
				'label'   => __( 'Icon image', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);
		$this->add_control(
			'youtube_image',
			array(
				'label'   => __( 'Youtube image', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);
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
		$this->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'Button Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Let’s Start', 'goodsoul-core' ),
			)
		);
		$this->add_control(
			'button_link',
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

		$this->add_control(
			'help_content',
			array(
				'label'   => esc_html__( 'Help words', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Need assitant for join with us?' ),
			)
		);

		$this->add_control(
			'phone',
			array(
				'label'   => esc_html__( 'Phone Number', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( '+211 456 7890' ),
			)
		);

		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'point_icon',
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
			'point',
			array(
				'label'   => esc_html__( 'Point Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'It’s super simple' ),
			)
		);

		$repeater->add_control(
			'point_summary',
			array(
				'label'   => esc_html__( 'Point Summary', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Indignation and dislike men who are so beguiled and demoral <br>the charms of pleasure of the moment.' ),
			)
		);

		$this->add_control(
			'points',
			array(
				'label'   => __( 'Point List', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(
						'list_title'   => __( 'Point #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Point #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
		);
		$this->end_controls_section();
	}
	protected function render() {
		$settings      = $this->get_settings_for_display();
		$select_layout = $settings['select_layout'];
		$title         = $settings['title'];
		$content       = $settings['content'];
		$image         = ( $settings['image']['id'] != '' ) ? wp_get_attachment_url( $settings['image']['id'], 'full' ) : $settings['image']['url'];
		$youtube_image = $settings['youtube_image']['url'];
		$youtube_image = ( $settings['youtube_image']['id'] != '' ) ? wp_get_attachment_url( $settings['youtube_image']['id'], 'full' ) : $settings['youtube_image']['url'];
		$icon_image    = ( $settings['icon_image']['id'] != '' ) ? wp_get_attachment_url( $settings['icon_image']['id'], 'full' ) : $settings['icon_image']['url'];
		$youtube_link  = $settings['youtube_link']['url'];
		$button_text   = $settings['button_text'];
		$button_link   = $settings['button_link']['url'];
		$icon          = $settings['icon'];
		$help_content  = $settings['help_content'];
		$phone         = $settings['phone'];
		$points        = $settings['points'];
		?>

		<?php if ( $select_layout == 'style_1' ) { ?>
		<!-- Whychoose Us Section Two -->
		<section class="whychoose-us-section-two">
		<div class="auto-container">
			<div class="row">
				<div class="col-lg-6">
					<div class="sec-title">
						<h1><?php echo wp_kses_post( $title ); ?></h1>
						<div class="text wow fadeInUp" data-wow-delay="200ms"><?php echo wp_kses_post( $content ); ?></div>
					</div>
				<?php
				foreach ( $points as $point ) {
					$point_icon    = $point['point_icon'];
					$point_name    = $point['point'];
					$point_summary = $point['point_summary'];
					?>
					<div class="whychoose-block-two">
						<div class="icon-box"><?php Icons_Manager::render_icon( ( $point_icon ), array( 'aria-hidden' => 'true' ) ); ?></div>
						<div class="content">
							<h4><?php echo wp_kses_post( $point_name ); ?></h4>
							<div class="text"><?php echo wp_kses_post( $point_summary ); ?></div>
						</div>
					</div>
					<?php } ?>
					<div class="wrapper-box">
						<?php if ( ! empty( $button_link ) ) { ?>
						<div class="link-btn"><a href="<?php echo esc_url( $button_link ); ?>" class="theme-btn btn-style-one"><span><?php echo wp_kses_post( $button_text ); ?></span></a></div>
						<?php } ?>
						<div class="contact-info-five">
						<?php if ( ! empty( $icon ) ) { ?>
							<div class="icon-box"><?php Icons_Manager::render_icon( ( $icon ), array( 'aria-hidden' => 'true' ) ); ?></div>
							<?php } ?>
							<h5><?php echo wp_kses_post( $help_content ); ?></h5>
							<h4><a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></h4>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="image-block-two">
						<?php if ( ! empty( $icon_image ) ) { ?>
						<div class="logo-box"><div class="image wow zoomIn" data-wow-delay="500ms"><img src="<?php echo esc_url( $icon_image ); ?>" alt="<?php esc_attr_e( 'Alt', 'goodsoul-core' ); ?>"></div></div>
						<?php } ?>
						<?php if ( ! empty( $image ) ) { ?>
						<div class="image-one wow fadeInRight" data-wow-delay="200ms"><img src="<?php echo esc_url( $image ); ?>" alt="<?php esc_attr_e( 'Alt', 'goodsoul-core' ); ?>"></div>
						<?php } ?>
						<?php if ( ! empty( $youtube_link ) ) { ?>
						<div class="video-box"><img src="<?php echo esc_url( $youtube_image ); ?>" alt="<?php esc_attr_e( 'Alt', 'goodsoul-core' ); ?>"><a href="<?php echo esc_url( $youtube_link ); ?>" class="overlay-link lightbox-image video-fancybox video-btn"><span class="flaticon-multimedia-1"></span></a></div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</section>
		<?php } elseif ( $select_layout == 'style_2' ) { ?>
	<section class="whychoose-us-section">
		<div class="auto-container">
			<div class="row">
				<div class="col-lg-6">
					<div class="sec-title">
						<h1><?php echo wp_kses_post( $title ); ?></h1>
						<div class="text"><?php echo wp_kses_post( $content ); ?></div>
					</div>
					<?php
					foreach ( $points as $point ) {
						$point_icon    = $point['point_icon'];
						$point_name    = $point['point'];
						$point_summary = $point['point_summary'];
						?>
					<div class="whychoose-us-block">
						<div class="icon-box"><?php Icons_Manager::render_icon( ( $point_icon ), array( 'aria-hidden' => 'true' ) ); ?></div>
						<div class="content">
							<h4><?php echo wp_kses_post( $point_name ); ?></h4>
							<div class="text"><?php echo wp_kses_post( $point_summary ); ?></div>
						</div>
					</div>
						<?php } ?>
						<?php if ( ! empty( $button_link ) ) { ?>
					<div class="link-btn"><a href="<?php echo esc_url( $button_link ); ?>" class="theme-btn btn-style-one"><span><?php echo wp_kses_post( $button_text ); ?></span></a></div>
					<?php } ?>
				</div>
				<div class="col-lg-6">
					<div class="image-block">
						<?php if ( ! empty( $image ) ) { ?>
						<img src="<?php echo esc_url( $image ); ?>" alt="<?php esc_attr_e( 'Alt', 'goodsoul-core' ); ?>">
						<?php } ?>
						<div class="contact-info-two">
							<h5><?php echo wp_kses_post( $help_content ); ?></h5>
							<?php if ( ! empty( $icon ) ) { ?>
							<div class="icon-box"><?php Icons_Manager::render_icon( ( $icon ), array( 'aria-hidden' => 'true' ) ); ?></div>
							<?php } ?>
							<div class="phone-number"><a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></div>
						</div>
						<?php if ( ! empty( $icon_image ) ) { ?>
						<div class="logo-box"><div class="image wow zoomIn animated" data-wow-delay="500ms" style="visibility: visible; animation-delay: 500ms; animation-name: zoomIn;"><img src="<?php echo esc_url( $icon_image ); ?>" alt="<?php esc_attr_e( 'Alt', 'goodsoul-core' ); ?>"></div></div>
						<?php } ?>
						<?php if ( ! empty( $youtube_link ) ) { ?>
						<div class="video-box"><img src="<?php echo esc_url( $youtube_image ); ?>" alt="<?php esc_attr_e( 'Alt', 'goodsoul-core' ); ?>"><a href="<?php echo esc_url( $youtube_link ); ?>" class="overlay-link lightbox-image video-fancybox video-btn"><span class="flaticon-multimedia-1"></span></a></div>
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
\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Why_Choose() );
