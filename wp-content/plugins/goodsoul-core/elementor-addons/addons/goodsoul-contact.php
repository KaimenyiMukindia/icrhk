<?php
/**
 * Elementor contact_area__o
 *
 * @since 1.0.0
 */
class contact_area__o extends \Elementor\Widget_Base {
	public function get_name() {
		return 'contact_area__o';
	}
	public function get_title() {
		return esc_html__( 'Volunteer Form', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'contact_header',
			array(
				'label' => esc_html__( 'contact Header area', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Become a volunteer' ),
			)
		);
		$this->add_control(
			'extra_class',
			array(
				'label'   => esc_html__( 'Extra Class', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Become a volunteer' ),
			)
		);
		$this->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Please read below to see what a few of our charity partners have to say about us.' ),
			)
		);
		$this->add_control(
			'shrotcode',
			array(
				'label' => esc_html__( 'Contact Form Shortcode', 'goodsoul-core' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$this->add_control(
			'left_image',
			array(
				'label'        => __( 'left Side Images', 'goodsoul-core' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);
		$this->add_control(
			'left_first',
			array(
				'label'     => __( 'Left First image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'left_image' => 'yes' ),
			)
		);
		$this->add_control(
			'left_second',
			array(
				'label'     => __( 'Left Second image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'left_image' => 'yes' ),
			)
		);
		$this->add_control(
			'left_third',
			array(
				'label'     => __( 'Left Third image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'left_image' => 'yes' ),
			)
		);
		$this->add_control(
			'left_forth',
			array(
				'label'     => __( 'Left Fourth image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'left_image' => 'yes' ),
			)
		);

		$this->add_control(
			'right_image',
			array(
				'label'        => __( 'Right Side Images', 'goodsoul-core' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);
		$this->add_control(
			'right_first',
			array(
				'label'     => __( 'Right First image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'right_image' => 'yes' ),
			)
		);
		$this->add_control(
			'right_second',
			array(
				'label'     => __( 'Right Second image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'right_image' => 'yes' ),
			)
		);
		$this->add_control(
			'right_third',
			array(
				'label'     => __( 'Right Third image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'right_image' => 'yes' ),
			)
		);
		$this->add_control(
			'right_forth',
			array(
				'label'     => __( 'Right Fourth image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'right_image' => 'yes' ),
			)
		);
		$this->end_controls_section();
	}
	protected function render() {
		$settings         = $this->get_settings_for_display();
			$title        = $settings['title'];
			$content      = $settings['content'];
			$extra_class  = $settings['extra_class'];
			$shrotcode    = $settings['shrotcode'];
			$left_image   = $settings['left_image'];
			$left_first   = ( $settings['left_first']['id'] != '' ) ? wp_get_attachment_url( $settings['left_first']['id'], 'full' ) : $settings['left_first']['url'];
			$left_second  = ( $settings['left_second']['id'] != '' ) ? wp_get_attachment_url( $settings['left_second']['id'], 'full' ) : $settings['left_second']['url'];
			$left_third   = ( $settings['left_third']['id'] != '' ) ? wp_get_attachment_url( $settings['left_third']['id'], 'full' ) : $settings['left_third']['url'];
			$left_forth   = ( $settings['left_forth']['id'] != '' ) ? wp_get_attachment_url( $settings['left_forth']['id'], 'full' ) : $settings['left_forth']['url'];
			$right_image  = $settings['right_image'];
			$right_first  = ( $settings['right_first']['id'] != '' ) ? wp_get_attachment_url( $settings['right_first']['id'], 'full' ) : $settings['right_first']['url'];
			$right_second = ( $settings['right_second']['id'] != '' ) ? wp_get_attachment_url( $settings['right_second']['id'], 'full' ) : $settings['right_second']['url'];
			$right_third  = ( $settings['right_third']['id'] != '' ) ? wp_get_attachment_url( $settings['right_third']['id'], 'full' ) : $settings['right_third']['url'];
			$right_forth  = ( $settings['right_forth']['id'] != '' ) ? wp_get_attachment_url( $settings['right_forth']['id'], 'full' ) : $settings['right_forth']['url'];
		?>
			<section class="volunteer-section <?php echo $extra_class ?? ''; ?>">
				<div class="auto-container">
					<div class="sec-title text-center">
						<h1><?php echo wp_kses_post( $title ); ?></h1>
						<div class="text"><?php echo wp_kses_post( $content ); ?></div>
					</div>
					<div class="row">
					<?php if ( $left_image == 'yes' ) { ?>
						<div class="col-lg-3">
							<div class="image-wrapper-one wow fadeInLeft animated" data-wow-delay="400ms" style="visibility: visible; animation-delay: 400ms; animation-name: fadeInLeft;">
								<div class="row no-gutters justify-content-center align-items-center">
									<div class="image"><img src="<?php echo esc_url( $left_first ); ?>" alt=""></div>
								</div>
								<div class="row no-gutters justify-content-center align-items-center">
									<div class="image"><img src="<?php echo esc_url( $left_second ); ?>" alt=""></div>
									<div class="image"><img src="<?php echo esc_url( $left_third ); ?>" alt=""></div>
								</div>
								<div class="row no-gutters justify-content-center align-items-center">
									<div class="image"><img src="<?php echo esc_url( $left_forth ); ?>" alt=""></div>
								</div>
							</div>
						</div>
					<?php } ?>
					<?php if ( $right_image == 'yes' ) { ?>
						<div class="col-lg-3 order-lg-2">
							<div class="image-wrapper-two wow fadeInRight animated" data-wow-delay="600ms" style="visibility: visible; animation-delay: 600ms; animation-name: fadeInRight;">
								<div class="row no-gutters justify-content-center align-items-center">
									<div class="image"><img src="<?php echo esc_url( $right_first ); ?>" alt=""></div>
								</div>
								<div class="row no-gutters justify-content-center align-items-center">
									<div class="image"><img src="<?php echo esc_url( $right_second ); ?>" alt=""></div>
									<div class="image"><img src="<?php echo esc_url( $right_third ); ?>" alt=""></div>
								</div>
								<div class="row no-gutters justify-content-center align-items-center">
									<div class="image"><img src="<?php echo esc_url( $right_forth ); ?>" alt=""></div>
								</div>
							</div>
						</div>
						<?php } ?>
						<div class="col-lg-6">
							<div class="default-form-area wow fadeInUp animated" data-wow-delay="200ms" style="visibility: visible; animation-delay: 200ms; animation-name: fadeInUp;">
								
								<div id="contact-form" name="contact_form" class="contact-form" >
							
									<?php echo do_shortcode( $shrotcode ); ?>

								</div>

							</div>
						</div>
					</div>
				</div>
			</section>
		<?php
	}
}

\Elementor\Plugin::instance()->widgets_manager->register( new \contact_area__o() );
