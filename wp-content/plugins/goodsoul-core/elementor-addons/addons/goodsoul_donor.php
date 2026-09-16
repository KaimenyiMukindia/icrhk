<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
use \Elementor\Icons_Manager;

class GoodSoul_Donor extends \Elementor\Widget_Base {



	public function get_name() {
		return 'goodsoul_donor';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Donor', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'donor_content',
			array(
				'label' => esc_html__( 'Donors', 'goodsoul-core' ),
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
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'The trusted choice of donors' ),
			)
		);
		$this->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'We connects nonprofits, donors, and companies in nearly every country around the world.', 'goodsoul-core' ),
			)
		);
		$this->add_control(
			'background_before_image',
			array(
				'label'     => esc_html__( 'Background Before Image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'select_layout' => 'style_3' ),
				'selectors' => array(
					'{{WRAPPER}} .donor-section.style_new_one .right-column::before' => 'background: url({{URL}}) !important',
				),
			)
		);
		$this->add_control(
			'background_image',
			array(
				'label'     => __( 'Background image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'select_layout' => 'style_1' ),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'image',
			array(
				'label'   => __( 'Client image', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'name',
			array(
				'label'   => esc_html__( 'Client Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Austin Leon' ),
			)
		);
		$repeater->add_control(
			'location',
			array(
				'label'   => esc_html__( 'Client Location', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'San Jose' ),
			)
		);

		$repeater->add_control(
			'twitter_link',
			array(
				'label'         => esc_html__( 'Client twitter link', 'goodsoul-core' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);

		$repeater->add_control(
			'donation',
			array(
				'label'   => esc_html__( 'Purpose ', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Donation' ),
			)
		);

		$repeater->add_control(
			'amount',
			array(
				'label'   => esc_html__( 'Amount ', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( '$250' ),
			)
		);

		goodsoul_get_animation_control( $repeater );

		$this->add_control(
			'client_list',
			array(
				'label'   => esc_html__( 'Client List', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(
						'list_title'   => __( 'Client #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Client #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Client #3', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
		);

		$this->add_control(
			'before_image',
			array(
				'label'     => esc_html__( 'Background Image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'selectors' => array(
					'{{WRAPPER}} .donor-section.style-two:before' => 'background: url({{URL}})',

				),
				'condition' => array( 'select_layout' => 'style_2' ),

			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'donor_content2',
			array(
				'label' => esc_html__( 'Banifits', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater_2 = new Repeater();
		$repeater_2->add_control(
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

		$repeater_2->add_control(
			'banifit_title',
			array(
				'label'   => esc_html__( 'Banifit Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Maximum tax <br> benefit' ),
			)
		);

		goodsoul_get_animation_control( $repeater_2 );

		$this->add_control(
			'banifit_list',
			array(
				'label'      => esc_html__( 'Banifit List', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::REPEATER,
				'fields'     => $repeater_2->get_controls(),
				'default'    => array(
					array(
						'list_title'   => __( 'Banifit #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Banifit #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Banifit #3', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'select_layout',
							'operator' => '==',
							'value'    => 'style_1',
						),
						array(
							'name'     => 'select_layout',
							'operator' => '==',
							'value'    => 'style_3',
						),
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
		if ( $select_layout == 'style_1' ) :
			$background_image = ( $settings['background_image']['id'] != '' ) ? wp_get_attachment_url( $settings['background_image']['id'], 'full' ) : $settings['background_image']['url'];
			$title_class      = 'light';
		endif;
		$client_list = $settings['client_list'];

		?>

		<?php
		if ( $select_layout == 'style_1' || $select_layout == 'style_3' ) {
			$banifit_list = $settings['banifit_list'];
			?>
	<!-- Donor Section -->
			<?php if ( $select_layout == 'style_1' ) : ?>
	<section class="donor-section" style="background-image: url(<?php echo esc_url( $background_image ); ?>);">
	<?php elseif ( $select_layout == 'style_3' ) : ?>
		<section class="donor-section style_new_one">
	<?php endif; ?>
		<div class="auto-container">
			<div class="row">
				<div class="col-lg-8 left-column">
					<div class="sec-title <?php echo $title_class; ?>">
						<h1><?php echo wp_kses_post( $title ); ?></h1>
						<div class="text"><?php echo wp_kses_post( $content ); ?></div>
					</div>
					<div class="row">
			<?php
			foreach ( $client_list as $client ) {
				$image                = ( $client['image']['id'] != '' ) ? wp_get_attachment_url( $client['image']['id'], 'full' ) : $client['image']['url'];
				$name                 = $client['name'];
				$location             = $client['location'];
				$twitter_link         = $client['twitter_link']['url'];
				$donation_type        = $client['donation'];
				$amount               = $client['amount'];
				$animation_class      = $client['animation_class'];
				$animation_delay_time = $client['addon_animation_delay_time'];
				?>
						<div class="col-md-4 donor-block">
							<div class="inner-box wow <?php echo esc_attr( $animation_class ); ?>" data-wow-delay="<?php echo esc_attr( $animation_delay_time ); ?>">
								<div class="top-content">
									<div class="image">
										<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
										<div class="overlay">
											<div class="icon"><a href="<?php echo esc_url( $twitter_link ); ?>"><span class="fa fa-twitter"></span></a></div>
										</div>
									</div>
									<h4><?php echo wp_kses_post( $name ); ?></h4>
									<div class="location"><?php echo wp_kses_post( $location ); ?></div>
								</div>
								<div class="bottom-content">
									<div class="text"><?php echo wp_kses_post( $donation_type ); ?></div>
									<div class="price"><?php echo wp_kses_post( $amount ); ?></div>
								</div>
							</div>
						</div>
			<?php } ?>
					</div>
				</div>
				<div class="col-lg-4 right-column">
			<?php
			foreach ( $banifit_list as $banifit ) {
				$banifit_title        = $banifit['banifit_title'];
				$icon                 = $banifit['icon'];
				$animation_class      = $banifit['animation_class'];
				$animation_delay_time = $banifit['addon_animation_delay_time'];
				?>
					<div class="feature-block-one wow <?php echo esc_attr( $animation_class ); ?>" data-wow-delay="<?php echo esc_attr( $animation_delay_time ); ?>">
						<div class="icon-box"><span class="<?php echo $icon['value']; ?>"></span></div>
						<h4><?php echo wp_kses_post( $banifit_title ); ?></h4>
					</div>
			<?php } ?>
				</div>
			</div>
		</div>
	</section>
			<?php } elseif ( $select_layout == 'style_2' ) { ?>

			<!-- Donor Section -->
			<section class="donor-section style-two">
		<div class="auto-container">
			<div class="sec-title text-center">
				<h1><?php echo wp_kses_post( $title ); ?></h1>
				<div class="text"><?php echo wp_kses_post( $content ); ?></div>
			</div>
			<div class="row">
			<?php
			foreach ( $client_list as $client ) {
				$image         = $client['image']['url'];
				$image         = ( $client['image']['id'] != '' ) ? wp_get_attachment_url( $client['image']['id'], 'full' ) : $client['image']['url'];
				$name          = $client['name'];
				$location      = $client['location'];
				$twitter_link  = $client['twitter_link']['url'];
				$donation_type = $client['donation'];
				$amount        = $client['amount'];
				?>
				<div class="col-md-4 donor-block">
					<div class="inner-box wow fadeInUp" data-wow-delay="200ms">
						<div class="top-content">
							<div class="image">
								<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
								<div class="overlay">
									<div class="icon"><a href="<?php echo esc_url( $twitter_link ); ?>"><span class="fa fa-twitter"></span></a></div>
								</div>
							</div>
							<h4><?php echo wp_kses_post( $name ); ?></h4>
							<div class="location"><?php echo wp_kses_post( $location ); ?></div>
						</div>
						<div class="bottom-content">
							<div class="text"><?php echo wp_kses_post( $donation_type ); ?></div>
							<div class="price"><?php echo wp_kses_post( $amount ); ?></div>
						</div>
					</div>
				</div>
			<?php } ?>
			</div>
		</div>
	</section>
		<?php } ?>
		<?php
	}
}

						\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Donor() );
