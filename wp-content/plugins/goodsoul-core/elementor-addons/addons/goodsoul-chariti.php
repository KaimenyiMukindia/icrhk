<?php
/**
 * Elementor chariti_area__o
 *
 * @since 1.0.0
 */
class Chariti_area__o extends \Elementor\Widget_Base {

	public function get_name() {
		return 'chariti_area__o';
	}
	public function get_title() {
		return esc_html__( 'Goodsoul Charity', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	public function get_script_depends() {
		return array( 'cause-slider' );
	}
	private function get_chariti() {
		$options = array();
		$args    = array(
			'post_type'      => array( 'campaign' ),
			'posts_per_page' => -1,
		);
		$causes  = new WP_Query( $args );
		if ( $causes->have_posts() ) {
			while ( $causes->have_posts() ) {
				$causes->the_post();
				$options[ get_the_ID() ] = get_the_title();
			}
		}
		wp_reset_postdata();
		return $options;
	}
	protected function register_controls() {
		$this->start_controls_section(
			'chariti_header',
			array(
				'label' => esc_html__( 'chariti Header area', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'chariti_layout',
			array(
				'label'   => esc_html__( 'Select Layout', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'style_1'  => esc_html__( 'Style 1', 'goodsoul-core' ),
					'style_2'  => esc_html__( 'Style 2', 'goodsoul-core' ),
					'style_3'  => esc_html__( 'Style 3', 'goodsoul-core' ),
					'style_4'  => esc_html__( 'Style 4', 'goodsoul-core' ),
					'style_5'  => esc_html__( 'Style 5', 'goodsoul-core' ),
					'style_6'  => esc_html__( 'Style 6', 'goodsoul-core' ),
					'style_7'  => esc_html__( 'Style 7', 'goodsoul-core' ),
					'style_8'  => esc_html__( 'Style 8', 'goodsoul-core' ),
					'style_9'  => esc_html__( 'Style 9', 'goodsoul-core' ),
					'style_10' => esc_html__( 'Style 10', 'goodsoul-core' ),
					'style_11' => esc_html__( 'Style 11', 'goodsoul-core' ),
					'style_12' => esc_html__( 'Style 12', 'goodsoul-core' ),
					'style_13' => esc_html__( 'Style 13', 'goodsoul-core' ),
					'style_14' => esc_html__( 'Style 14', 'goodsoul-core' ),
				),
				'default' => esc_html__( 'style_1', 'goodsoul-core' ),
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'     => __( 'Choose Icon', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-star',
					'library' => 'solid',
				),
				'condition' => array( 'chariti_layout' => 'style_6' ),
			)
		);
		$this->add_control(
			'sub_title',
			array(
				'label'      => esc_html__( 'Sub Title', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::TEXTAREA,
				'default'    => __( 'Complete our online registration form:' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_6',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_7',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_8',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_9',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_12',
						),
					),
				),
			)
		);
		$this->add_control(
			'main_title',
			array(
				'label'      => esc_html__( 'main Title', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::TEXTAREA,
				'default'    => __( 'Complete our online registration form:' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_3',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_13',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_6',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_7',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_8',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_9',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_12',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_14',
						),
					),
				),
			)
		);
		$this->add_control(
			'content',
			array(
				'label'      => esc_html__( 'Text', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::TEXTAREA,
				'default'    => __( 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_3',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_13',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_9',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_12',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_14',
						),
					),
				),
			)
		);
		$this->add_control(
			'left_img',
			array(
				'label'      => __( 'Left Img', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::MEDIA,
				'default'    => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'selectors'  => array(
					'{{WRAPPER}} .payment-donate-section-two .sec-bg-one' => 'background-image: url({{URL}})',
					'{{WRAPPER}} .payment-donate-section-three .sec-bg-one' => 'background-image: url({{URL}})',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_10',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_11',
						),
					),
				),
			)
		);

		$this->add_control(
			'seven_back_image',
			array(
				'label'      => esc_html__( 'Background Image', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::MEDIA,
				'default'    => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'selectors'  => array(
					'{{WRAPPER}} .causes-section-six:before' => 'background: url({{URL}})',

				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_7',
						),
					),
				),

			)
		);

		$this->add_control(
			'seven_section_color',
			array(
				'label'      => __( 'Background Color', 'cameron-core' ),
				'separator'  => 'before',
				'type'       => \Elementor\Controls_Manager::COLOR,
				'selectors'  => array(
					'{{WRAPPER}} .causes-section-six:before' => 'background-color: {{VALUE}}',

				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_7',
						),
					),
				),
			)
		);

		$this->add_control(
			'right_img',
			array(
				'label'      => __( 'Left Img', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::MEDIA,
				'default'    => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'selectors'  => array(
					'{{WRAPPER}} .payment-donate-section-two .sec-bg-two' => 'background-image: url({{URL}})',
					'{{WRAPPER}} .payment-donate-section-three .sec-bg-two' => 'background-image: url({{URL}})',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_10',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_11',
						),
					),
				),
			)
		);

		$this->add_control(
			'video_url',
			array(
				'label'         => esc_html__( 'video url', 'goodsoul-core' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
				'conditions'    => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_11',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_12',
						),
					),
				),
			)
		);
		$this->add_control(
			'raised',
			array(
				'label'   => esc_html__( 'Raised Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Raised:' ),

			)
		);
		$this->add_control(
			'goal',
			array(
				'label'   => esc_html__( 'Goal Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Goal:' ),
			)
		);
		$this->add_control(
			'main_button',
			array(
				'label'      => esc_html__( 'Donate Button Name', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'default'    => __( 'Donate Now' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_2',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_3',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_13',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_4',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_5',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_7',
						),
						array(
							'name'     => 'chariti_layout',
							'operator' => '==',
							'value'    => 'style_14',
						),
					),
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'chariti_list_area',
			array(
				'label' => esc_html__( 'chariti list area', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'chariti_list_single',
			array(
				'label'   => esc_html__( 'chariti list', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $this->get_chariti(),
			)
		);
		$repeater->add_control(
			'chariti_list_single_layout',
			array(
				'label'   => esc_html__( 'Select Layout', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'design_1' => esc_html__( 'Normal', 'goodsoul-core' ),
					'design_2' => esc_html__( 'Tab', 'goodsoul-core' ),
				),
				'default' => esc_html__( 'design_1', 'goodsoul-core' ),
			)
		);
		$repeater->add_control(
			'chariti_list_single_tab',
			array(
				'label'      => esc_html__( 'Tab active', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::SELECT,
				'options'    => array(
					'no'     => esc_html__( 'no', 'goodsoul-core' ),
					'active' => esc_html__( 'yes', 'goodsoul-core' ),
				),
				'default'    => 'active',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'chariti_list_single_layout',
							'operator' => '==',
							'value'    => 'design_2',
						),
					),
				),
			)
		);
		$repeater->add_control(
			'chariti_list_single_img',
			array(
				'label'      => __( 'External Images', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::MEDIA,
				'default'    => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'chariti_list_single_layout',
							'operator' => '!==',
							'value'    => 'design_2',
						)
					)
				)
			)
		);

		$repeater->add_control(
			'icon_cat',
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
			'charii_cat_text',
			array(
				'label'   => esc_html__( 'Category text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Health & Diseases' ),

			)
		);

		$repeater->add_control(
			'raised_and_don_info',
			array(
				'label'   => esc_html__( 'Raised And Donor Info Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Raised by 84 people in 12 days' ),

			)
		);

		$this->add_control(
			'chariti_list',
			array(
				'label'  => esc_html__( 'chariti list', 'goodsoul-core' ),
				'type'   => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			)
		);
		$this->end_controls_section();
	}
	protected function render() {
		$settings       = $this->get_settings_for_display();
		$icon           = $settings['icon'];
		$main_title     = $settings['main_title'];
		$sub_title      = $settings['sub_title'];
		$content        = $settings['content'];
		$main_button    = $settings['main_button'];
		$chariti_layout = $settings['chariti_layout'];
		$chariti_list   = $settings['chariti_list'];
		$video_url      = ! empty( $settings['video_url']['url'] ) ? $settings['video_url']['url'] : '';
		$raised_txt     = $settings['raised'];
		$goal_txt       = $settings['goal'];
		?>
		<?php if ( $chariti_layout == 'style_1' ) : ?>
<section class="causes-section-four">
	<div class="auto-container">
		<div class="cause-wrapper">
			<div class="row">
			<?php if ( $chariti_list ) : ?>
				<?php
				foreach ( $chariti_list as $item ) {
					$chariti_list_single     = $item['chariti_list_single'];
					$chariti_list_single_img = ( $item['chariti_list_single_img']['id'] != '' ) ? wp_get_attachment_url( $item['chariti_list_single_img']['id'], 'full' ) : $item['chariti_list_single_img']['url'];
					?>
					<?php
					 $chariti_campaign        = charitable_get_campaign( $chariti_list_single );
					 $chariti_image_url       = get_the_post_thumbnail_url( $chariti_campaign->ID );
					 $chariti_currency_helper = charitable_get_currency_helper();
					 $chariti_raised          = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_donated_amount() );
					 $chariti_goal            = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_goal() );
					 $chariti_post_title      = $chariti_campaign->post_title;
					 $chariti_post_content    = get_post_meta( $chariti_campaign->ID, 'goodsoul_metabox_featured_content', true );
					 $chariti_percent         = $chariti_campaign->get_percent_donated_raw();
					 $chariti_categories      = $chariti_campaign->get( 'categories', true );
					 $chariti_post_page_link  = get_post_permalink( $chariti_campaign->ID );
					?>
				<div class="cause-block-four col-lg-4">
					<div class="inner-box">
						<div class="image">
							<img src="
					<?php
					if ( ! empty( $chariti_list_single_img ) ) {
						echo esc_url( $chariti_list_single_img );
					} else {
						echo esc_url( $chariti_image_url );
					}
					?>
							"
								alt="">
							<div class="overlay"><a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"
									class="donate-box-btn"><?php echo $main_button; ?></a></div>
						</div>
						<div class="lower-content">
							<div class="wrapper-box">
								<h4><a
										href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><?php echo wp_kses_post( $chariti_post_title ); ?></a>
								</h4>
								<div class="text"><?php echo wp_trim_words( $chariti_post_content, 11, '...' ); ?></div>
								<div class="info-box">
									<div class="raised">
										<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $raised_txt; ?></span>
					<?php echo wp_kses_post( $chariti_raised ); ?></a>
									</div>
									<div class="count-box">
										<span class="count-text" data-speed="3000"
											data-stop="<?php echo wp_kses_post( round( $chariti_percent ) ); ?>">0</span><span
											class="affix">%</span>
									</div>
									<div class="goal">
										<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $goal_txt; ?></span>
					<?php echo wp_kses_post( $chariti_goal ); ?></a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			<?php endif; ?>
			</div>
		</div>
	</div>
</section>
		<?php elseif ( $chariti_layout == 'style_2' ) : ?>
<section class="causes-section-three style-two">
	<div class="auto-container">
		<div class="wrapper-box">
			<?php if ( $chariti_list ) : ?>
				<?php
				foreach ( $chariti_list as $item ) {
					$chariti_list_single     = $item['chariti_list_single'];
					$chariti_list_single_img = ( $item['chariti_list_single_img']['id'] != '' ) ? wp_get_attachment_url( $item['chariti_list_single_img']['id'], 'full' ) : $item['chariti_list_single_img']['url'];
					?>
					<?php
					$chariti_campaign        = charitable_get_campaign( $chariti_list_single );
					$chariti_image_url       = get_the_post_thumbnail_url( $chariti_campaign->ID );
					$chariti_currency_helper = charitable_get_currency_helper();
					$chariti_raised          = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_donated_amount() );
					$chariti_goal            = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_goal() );
					$chariti_post_title      = $chariti_campaign->post_title;
					$chariti_post_content    = get_post_meta( $chariti_campaign->ID, 'goodsoul_metabox_featured_content', true );
					$chariti_percent         = $chariti_campaign->get_percent_donated_raw();
					$chariti_categories      = $chariti_campaign->get( 'categories', true );
					$chariti_post_page_link  = get_post_permalink( $chariti_campaign->ID );
					?>
			<div class="cause-block-three style-two">
				<div class="inner-box">
					<div class="image">
						<img src="
					<?php
					if ( ! empty( $chariti_list_single_img ) ) {
						echo esc_url( $chariti_list_single_img );
					} else {
						echo esc_url( $chariti_image_url );
					}
					?>
						"
							alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
					</div>
					<div class="content-wrapper">
						<!--Progress Levels-->
						<div class="progress-levels style-two">
							<div class="progress-box wow fadeInRight" data-wow-delay="100ms" data-wow-duration="1500ms">
								<div class="inner">
									<div class="bar">
										<div class="bar-innner">
											<div class="bar-fill"
												data-percent="<?php echo wp_kses_post( round( $chariti_percent ) ); ?>">
												<div class="percent"></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="content">
							<h4><a
									href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><?php echo wp_kses_post( $chariti_post_title ); ?></a>
							</h4>
							<div class="text"><?php echo wp_trim_words( $chariti_post_content, 11, '...' ); ?></div>
							<ul class="info-box">
								<li><a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $raised_txt; ?></span>
										<br><?php echo wp_kses_post( $chariti_raised ); ?></a></li>
								<li><a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $goal_txt; ?></span>
										<br><?php echo wp_kses_post( $chariti_goal ); ?></a></li>
							</ul>
							<div class="donate-btn"><a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"
									class="theme-btn btn-style-eight donate-box-btn"><span><?php echo $main_button; ?></span></a>
							</div>
						</div>
					</div>
				</div>
			</div>
				<?php } ?>
			<?php endif; ?>
		</div>
	</div>
</section>
			<?php
		elseif ( $chariti_layout == 'style_3' || $chariti_layout == 'style_13' ) :
			$style_class = '';
			if ( $chariti_layout == 'style_3' ) {
				$style_class = 'style-two';
			} else {
				$style_class = 'style-main-chariti';
			}
			?>
<section class="causes-section <?php echo $style_class; ?>">
	<div class="auto-container">
		<div class="sec-title text-center">
			<h1><?php echo wp_kses_post( $main_title ); ?></h1>
			<div class="text"><?php echo wp_kses_post( $content ); ?></div>
		</div>
		<div class="cause-carousel-wrapper">
			<div class="cause-carousel owl-theme owl-carousel owl-dot-style-one owl-nav-none">
			<?php if ( $chariti_list ) : ?>
				<?php
				foreach ( $chariti_list as $item ) {
					$chariti_list_single     = $item['chariti_list_single'];
					$icon_cat                = $item['icon_cat'];
					$charii_cat_text         = $item['charii_cat_text'];
					$raised_and_don_info     = $item['raised_and_don_info'];
					$chariti_list_single_img = $item['chariti_list_single_img']['url'];
					?>
					<?php
					$chariti_campaign        = charitable_get_campaign( $chariti_list_single );
					$chariti_image_url       = get_the_post_thumbnail_url( $chariti_campaign->ID );
					$chariti_currency_helper = charitable_get_currency_helper();
					$chariti_raised          = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_donated_amount() );
					$chariti_goal            = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_goal() );
					$chariti_post_title      = $chariti_campaign->post_title;
					$chariti_post_content    = get_post_meta( $chariti_campaign->ID, 'goodsoul_metabox_featured_content', true );
					$chariti_percent         = $chariti_campaign->get_percent_donated_raw();
					$chariti_categories      = $chariti_campaign->get( 'categories', true );
					$chariti_post_page_link  = get_post_permalink( $chariti_campaign->ID );
					?>
				<div class="cause-block-one">
					<div class="inner-box">
						<div class="image"><a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><img
									src="
					<?php
					if ( ! empty( $chariti_list_single_img ) ) {
						echo esc_url( $chariti_list_single_img );
					} else {
						echo esc_url( $chariti_image_url );
					}
					?>
									"
									alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></a></div>
						<div class="lower-content">
							<h4><a
									href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><?php echo wp_kses_post( $chariti_post_title ); ?></a>
							</h4>
							<div class="category"
							><a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>">
					<?php \Elementor\Icons_Manager::render_icon( ( $icon_cat ), array( 'aria-hidden' => 'true' ) ); ?>
					<?php echo wp_kses_post( $charii_cat_text ); ?></a>
							</div>
							<div class="text"><?php echo $chariti_post_content; ?></div>
							<div class="info-box">
								<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $raised_txt; ?></span>
					<?php echo wp_kses_post( $chariti_raised ); ?></a>
								<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $goal_txt; ?></span>
					<?php echo wp_kses_post( $chariti_goal ); ?></a>
							</div>
							<!--Progress Levels-->
							<div class="progress-levels">

								<!--Skill Box-->
								<div class="progress-box wow fadeInRight" data-wow-delay="100ms"
									data-wow-duration="1500ms">
									<div class="inner">
										<div class="bar">
											<div class="bar-innner">
												<div class="bar-fill"
													data-percent="<?php echo wp_kses_post( round( $chariti_percent ) ); ?>">
													<div class="percent"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
					<?php
					if ( $raised_and_don_info != '' ) {
						?>
								<div class="text"><?php echo $raised_and_don_info; ?></div>
						<?php
					}
					?>
							<div class="bottom-content">
								<div class="link-btn"><a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"
										class="theme-btn btn-style-one donate-box-btn"><span><?php echo $main_button; ?></span></a>
								</div>
								<div class="share-icon post-share-icon">
									<div class="share-btn"><i class="flaticon-share"></i></div>
									<ul>
										<li><a onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"
												href="https://www.facebook.com/sharer/sharer.php?u=<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span
													class="fa fa-facebook"></span></a></li>
										<li><a onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"
												href="https://twitter.com/home?status=<?php echo urlencode( get_the_title() ); ?>-<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span
													class="fa fa-twitter"></span></a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			<?php endif; ?>
			</div>
		</div>
	</div>
</section>
		<?php elseif ( $chariti_layout == 'style_4' ) : ?>
			<?php
			function ed_remove_phone_field_from_donation_form12( $fields ) {
				unset( $fields['phone'] );
				unset( $fields['address'] );
				unset( $fields['address_2'] );
				unset( $fields['city'] );
				unset( $fields['state'] );
				unset( $fields['country'] );
				unset( $fields['postcode'] );
				return $fields;
			}
			add_filter( 'charitable_donation_form_user_fields', 'ed_remove_phone_field_from_donation_form12' );
			?>
<section class="causes-section-three">
	<div class="auto-container">
		<div class="cause-carousel-wrapper">
			<div class="single-item-carousel owl-theme owl-carousel owl-dot-style-one owl-nav-none">
			<?php if ( $chariti_list ) : ?>
				<?php
				foreach ( $chariti_list as  $key => $item ) {
					$chariti_list_single     = $item['chariti_list_single'];
					$chariti_list_single_img = ( $item['chariti_list_single_img']['id'] != '' ) ? wp_get_attachment_url( $item['chariti_list_single_img']['id'], 'full' ) : $item['chariti_list_single_img']['url'];
					?>
					<?php
					 $chariti_campaign        = charitable_get_campaign( $chariti_list_single );
					 $chariti_image_url       = get_the_post_thumbnail_url( $chariti_campaign->ID );
					 $chariti_currency_helper = charitable_get_currency_helper();
					 $chariti_raised          = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_donated_amount() );
					 $chariti_goal            = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_goal() );
					 $chariti_post_title      = $chariti_campaign->post_title;
					 $chariti_post_content    = get_post_meta( $chariti_campaign->ID, 'goodsoul_metabox_featured_content', true );
					 $chariti_percent         = $chariti_campaign->get_percent_donated_raw();
					 $chariti_categories      = $chariti_campaign->get( 'categories', true );
					 $chariti_post_page_link  = get_post_permalink( $chariti_campaign->ID );
					?>
				<div class="cause-block-three">
					<div class="inner-box">
						<div class="image">
							<img src="<?php echo esc_url( $chariti_list_single_img ); ?>"
								alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
						</div>
						<div class="content-wrapper">
							<!--Progress Levels-->
							<div class="progress-levels style-two">

								<!--Skill Box-->
								<div class="progress-box wow fadeInRight" data-wow-delay="100ms"
									data-wow-duration="1500ms">
									<div class="inner">
										<div class="bar">
											<div class="bar-innner">
												<div class="bar-fill"
													data-percent="<?php echo wp_kses_post( round( $chariti_percent ) ); ?>">
													<div class="percent"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="content">
								<h4><a
										href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><?php echo wp_kses_post( $chariti_post_title ); ?></a>
								</h4>
								<div class="text"><?php echo wp_trim_words( $chariti_post_content, 11, '...' ); ?></div>
								<ul class="info-box">
									<li><a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $raised_txt; ?></span>
											<br><?php echo wp_kses_post( $chariti_raised ); ?></a></li>
									<li><a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $goal_txt; ?></span>
											<br><?php echo wp_kses_post( $chariti_goal ); ?></a></li>
								</ul>
								<div class="donate-btn"><a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"
										data-target="<?php echo $key; ?>"
										class="theme-btn btn-style-eight donate-box-btn"><span><?php echo $main_button; ?></span></a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			<?php endif; ?>
			</div>
		</div>
	</div>
</section>
			<?php
			foreach ( $chariti_list as $key => $item ) {
				$chariti_list_single  = $item['chariti_list_single'];
				$chariti_campaign     = charitable_get_campaign( $chariti_list_single );
				$chariti_post_title   = $chariti_campaign->post_title;
				$chariti_post_content = get_post_meta( $chariti_campaign->ID, 'goodsoul_metabox_featured_content', true );
				?>
<div id="donate-popup" class="donate-popup new-poppu-css style-two donate-popup-<?php echo $key; ?>">
	<div class="popup-overlay"></div>
	<div class="donate-form-area">
		<div class="donate-form-wrapper">
			<div class="close-donate theme-btn"><span class="flaticon-close"></span></div>
			<div class="sec-title text-center">
				<h1><?php echo wp_kses_post( $chariti_post_title ); ?></h1>
				<div class="text"><?php echo wp_trim_words( $chariti_post_content, 11, '...' ); ?></div>
			</div>
			<div class="donate-form default-form">
				<?php echo do_shortcode( '[charitable_donation_form campaign_id=" ' . $chariti_campaign->ID . ' "]' ); ?>
			</div>
		</div>
	</div>
</div>
			<?php } ?>
		<?php elseif ( $chariti_layout == 'style_5' ) : ?>
<section class="causes-section-two">
	<div class="auto-container">
		<div class="cause-wrapper">
			<div class="row">
			<?php if ( $chariti_list ) : ?>
				<?php
				foreach ( $chariti_list as $item ) {
					$chariti_list_single     = $item['chariti_list_single'];
					$chariti_list_single_img = $item['chariti_list_single_img']['url'];
					$chariti_list_single_img = ( $item['chariti_list_single_img']['id'] != '' ) ? wp_get_attachment_url( $item['chariti_list_single_img']['id'], 'full' ) : $item['chariti_list_single_img']['url'];
					?>
					<?php
					 $chariti_campaign        = charitable_get_campaign( $chariti_list_single );
					 $chariti_image_url       = get_the_post_thumbnail_url( $chariti_campaign->ID );
					 $chariti_currency_helper = charitable_get_currency_helper();
					 $chariti_raised          = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_donated_amount() );
					 $chariti_goal            = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_goal() );
					 $chariti_post_title      = $chariti_campaign->post_title;
					 $chariti_post_content    = get_post_meta( $chariti_campaign->ID, 'goodsoul_metabox_featured_content', true );
					 $chariti_percent         = $chariti_campaign->get_percent_donated_raw();
					 $chariti_categories      = $chariti_campaign->get( 'categories', true );
					 $chariti_post_page_link  = get_post_permalink( $chariti_campaign->ID );
					?>



				<div class="cause-block-two col-lg-4">
					<div class="inner-box">
						<div class="image">
							<img src="<?php echo esc_url( $chariti_list_single_img ); ?>"
								alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
							<div class="overlay">
								<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"
									class="theme-btn btn-style-seven"><span><?php echo $main_button; ?></span></a>
							</div>
						</div>
						<div class="lower-content">
							<!--Progress Levels-->
							<div class="progress-levels style-two">

								<!--Skill Box-->
								<div class="progress-box wow fadeInRight" data-wow-delay="100ms"
									data-wow-duration="1500ms">
									<div class="inner">
										<div class="bar">
											<div class="bar-innner">
												<div class="bar-fill"
													data-percent="<?php echo wp_kses_post( round( $chariti_percent ) ); ?>">
													<div class="percent"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="wrapper-box">
								<h4><a
										href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><?php echo wp_kses_post( $chariti_post_title ); ?></a>
								</h4>
								<div class="info-box">
									<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $raised_txt; ?></span>
					<?php echo wp_kses_post( $chariti_raised ); ?></a>
									<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $goal_txt; ?></span>
					<?php echo wp_kses_post( $chariti_goal ); ?></a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			<?php endif; ?>
			</div>
		</div>
	</div>
</section>
		<?php elseif ( $chariti_layout == 'style_6' ) : ?>
<section class="causes-section-five">
	<div class="auto-container">
		<div class="sec-title style-two">
			<div class="icon-box"><span class="<?php echo $icon['value']; ?>"></span></div>
			<h5><?php echo wp_kses_post( $sub_title ); ?></h5>
			<h1><?php echo wp_kses_post( $main_title ); ?></h1>
		</div>
		<div class="cause-carousel-wrapper">
			<div class="four-item-carousel owl-theme owl-carousel owl-dots-none owl-nav-none">
			<?php if ( $chariti_list ) : ?>
				<?php
				foreach ( $chariti_list as $item ) {
					$chariti_list_single     = $item['chariti_list_single'];
					$chariti_list_single_img = ( $item['chariti_list_single_img']['id'] != '' ) ? wp_get_attachment_url( $item['chariti_list_single_img']['id'], 'full' ) : $item['chariti_list_single_img']['url'];
					?>

					<?php
					$chariti_campaign        = charitable_get_campaign( $chariti_list_single );
					$chariti_image_url       = get_the_post_thumbnail_url( $chariti_campaign->ID );
					$chariti_currency_helper = charitable_get_currency_helper();
					$chariti_raised          = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_donated_amount() );
					$chariti_goal            = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_goal() );
					$chariti_post_title      = $chariti_campaign->post_title;
					$chariti_post_content    = get_post_meta( $chariti_campaign->ID, 'goodsoul_metabox_featured_content', true );
					$chariti_percent         = $chariti_campaign->get_percent_donated_raw();
					$chariti_categories      = $chariti_campaign->get( 'categories', true );
					$chariti_post_page_link  = get_post_permalink( $chariti_campaign->ID );
					?>

				<div class="cause-block-five">
					<div class="inner-box">
						<div class="image">
							<div class="link-btn"><a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"
									class="theme-btn donate-box-btn"><i
										class="fa fa-arrow-circle-o-right"></i><?php echo $main_button; ?></a></div>
							<img src="
					<?php
					if ( ! empty( $chariti_list_single_img ) ) {
						echo esc_url( $chariti_list_single_img );
					} else {
						echo esc_url( $chariti_image_url );
					}
					?>
							"
								alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
						</div>
						<div class="lower-content">
							<div class="progress-block">
								<div class="inner-box">
									<div class="graph-outer">
										<input type="text" class="dial" data-fgColor="#02be6c" data-bgColor="#f0edea"
											data-width="70" data-height="70" data-linecap="normal"
											value="<?php echo wp_kses_post( round( $chariti_percent ) ); ?>">
										<div class="inner-text count-box"><span class="count-text"
												data-stop="<?php echo wp_kses_post( round( $chariti_percent ) ); ?>"
												data-speed="2000"></span>%</div>
									</div>
								</div>
							</div>
							<h4><a
									href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><?php echo wp_kses_post( $chariti_post_title ); ?></a>
							</h4>
							<div class="text"><?php echo wp_trim_words( $chariti_post_content, 11, '...' ); ?></div>
							<div class="info-box">
								<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $raised_txt; ?></span><br>
					<?php echo wp_kses_post( $chariti_raised ); ?></a>
								<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $goal_txt; ?></span><br>
					<?php echo wp_kses_post( $chariti_goal ); ?></a>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			<?php endif; ?>
			</div>
		</div>
	</div>
</section>
		<?php elseif ( $chariti_layout == 'style_7' ) : ?>
<section class="causes-section-six">
	<div class="auto-container">
		<div class="sec-title light text-center style-three">
			<h5><?php echo wp_kses_post( $sub_title ); ?></h5>
			<h1><?php echo wp_kses_post( $main_title ); ?></h1>
		</div>
			<?php if ( $chariti_list ) : ?>
		<div class="wrapper-box">
				<?php
				foreach ( $chariti_list as $item ) {
					$chariti_list_single     = $item['chariti_list_single'];
					$chariti_list_single_img = ( $item['chariti_list_single_img']['id'] != '' ) ? wp_get_attachment_url( $item['chariti_list_single_img']['id'], 'full' ) : $item['chariti_list_single_img']['url'];
					?>
					<?php
					$chariti_campaign        = charitable_get_campaign( $chariti_list_single );
					$chariti_image_url       = get_the_post_thumbnail_url( $chariti_campaign->ID );
					$chariti_currency_helper = charitable_get_currency_helper();
					$chariti_raised          = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_donated_amount() );
					$chariti_goal            = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_goal() );
					$chariti_post_title      = $chariti_campaign->post_title;
					$chariti_post_content    = get_post_meta( $chariti_campaign->ID, 'goodsoul_metabox_featured_content', true );
					$chariti_percent         = $chariti_campaign->get_percent_donated_raw();
					$chariti_categories      = $chariti_campaign->get( 'categories', true );
					$chariti_post_page_link  = get_post_permalink( $chariti_campaign->ID );
					?>
			<div class="row align-items-center">
				<div class="column col-lg-4">
					<div class="image"><img
							src="
					<?php
					if ( ! empty( $chariti_list_single_img ) ) {
						echo esc_url( $chariti_list_single_img );
					} else {
						echo esc_url( $chariti_image_url );
					}
					?>
							"
							alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></div>
				</div>
				<div class="column col-lg-4">
					<div class="py-4">
						<h2><?php echo wp_kses_post( $chariti_post_title ); ?></h2>
						<div class="text"><?php echo wp_trim_words( $chariti_post_content, 11, '...' ); ?></div>
						<div class="link-btn"><a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"
								class="theme-btn btn-style-sixteen donate-box-btn"><span><?php echo $main_button; ?></span></a>
						</div>
					</div>
				</div>
				<div class="column col-lg-4">
					<div class="donation-wrapper">
						<div class="info-box">
							<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $raised_txt; ?></span><br>
					<?php echo wp_kses_post( $chariti_raised ); ?></a> <br>
							<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $goal_txt; ?></span><br>
					<?php echo wp_kses_post( $chariti_goal ); ?></a>
						</div>
						<div class="progress-block">
							<div class="inner-box">
								<div class="graph-outer">
									<input type="text" class="dial" data-fgColor="#18bec2" data-bgColor="#f0edea"
										data-width="80" data-height="80" data-linecap="normal"
										value="<?php echo wp_kses_post( round( $chariti_percent ) ); ?>">
									<div class="inner-text count-box"><span class="count-text"
											data-stop="<?php echo wp_kses_post( round( $chariti_percent ) ); ?>"
											data-speed="2000"></span>%</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
				<?php } ?>
		</div>
			<?php endif; ?>
	</div>
</section>
		<?php elseif ( $chariti_layout == 'style_8' ) : ?>
<section class="causes-section-seven">
	<div class="auto-container">
		<div class="sec-title text-center style-three">
			<h5><?php echo wp_kses_post( $sub_title ); ?></h5>
			<h1><?php echo wp_kses_post( $main_title ); ?></h1>
		</div>
		<div class="cause-carousel-wrapper">
			<div class="three-item-carousel owl-theme owl-carousel owl-nav-none owl-dot-style-one">
			<?php if ( $chariti_list ) : ?>
				<?php
				foreach ( $chariti_list as $item ) {
					$chariti_list_single     = $item['chariti_list_single'];
					$chariti_list_single_img = $item['chariti_list_single_img']['url'];
					$chariti_list_single_img = ( $item['chariti_list_single_img']['id'] != '' ) ? wp_get_attachment_url( $item['chariti_list_single_img']['id'], 'full' ) : $item['chariti_list_single_img']['url'];
					?>
					<?php
					$chariti_campaign        = charitable_get_campaign( $chariti_list_single );
					$chariti_image_url       = get_the_post_thumbnail_url( $chariti_campaign->ID );
					$chariti_currency_helper = charitable_get_currency_helper();
					$chariti_raised          = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_donated_amount() );
					$chariti_goal            = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_goal() );
					$chariti_post_title      = $chariti_campaign->post_title;
					$chariti_post_content    = get_post_meta( $chariti_campaign->ID, 'goodsoul_metabox_featured_content', true );
					$chariti_percent         = $chariti_campaign->get_percent_donated_raw();
					$chariti_categories      = $chariti_campaign->get( 'categories', true );
					$chariti_post_page_link  = get_post_permalink( $chariti_campaign->ID );
					?>
				<div class="cause-block-seven">
					<div class="inner-box">
						<div class="image">
							<img src="
					<?php
					if ( ! empty( $chariti_list_single_img ) ) {
						echo esc_url( $chariti_list_single_img );
					} else {
						echo esc_url( $chariti_image_url );
					}
					?>
							"
								alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
							<div class="link-btn"><a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"
									class="theme-btn btn-style-one donate-box-btn"><span><?php echo $main_button; ?></span></a>
							</div>
						</div>
						<div class="lower-content">
							<h4><a
									href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><?php echo wp_kses_post( $chariti_post_title ); ?></a>
							</h4>
							<div class="category"><a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span
										class="flaticon-user-1"></span><?php echo wp_kses_post( $chariti_categories ); ?></a>
							</div>
							<div class="text"><?php echo wp_trim_words( $chariti_post_content, 11, '...' ); ?></div>
							<div class="info-box">
								<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $raised_txt; ?></span>
					<?php echo wp_kses_post( $chariti_raised ); ?></a>
								<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $goal_txt; ?></span>
					<?php echo wp_kses_post( $chariti_goal ); ?></a>
							</div>
							<!--Progress Levels-->
							<div class="progress-levels">

								<!--Skill Box-->
								<div class="progress-box wow fadeInRight" data-wow-delay="100ms"
									data-wow-duration="1500ms">
									<div class="inner">
										<div class="bar">
											<div class="bar-innner">
												<div class="bar-fill"
													data-percent="<?php echo wp_kses_post( round( $chariti_percent ) ); ?>">
													<div class="percent"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			<?php endif; ?>
			</div>
		</div>
	</div>
</section>
		<?php elseif ( $chariti_layout == 'style_9' ) : ?>
<section class="payment-donate-section charitable-addons-nine">
	<div class="auto-container">
		<div class="sec-title text-center light">
			<h1><?php echo $main_title; ?></h1>
			<div class="text"><?php echo $content; ?></div>
		</div>
			<?php
			function ed_remove_phone_field_from_donation_form( $fields ) {
				unset( $fields['phone'] );
				unset( $fields['address'] );
				unset( $fields['address_2'] );
				unset( $fields['city'] );
				unset( $fields['state'] );
				unset( $fields['country'] );
				unset( $fields['postcode'] );
				return $fields;
			}
			add_filter( 'charitable_donation_form_user_fields', 'ed_remove_phone_field_from_donation_form' );
			?>
			<?php
			foreach ( $chariti_list as $item ) {
				$chariti_list_single = $item['chariti_list_single'];
				$chariti_campaign    = charitable_get_campaign( $chariti_list_single );
			}
			?>
			<?php echo do_shortcode( '[charitable_donation_form campaign_id=" ' . $chariti_campaign->ID . ' "]' ); ?>
	</div>
</section>
		<?php elseif ( $chariti_layout == 'style_10' ) : ?>
<section class="payment-donate-section-two">
	<div class="sec-bg-one"></div>
	<div class="sec-bg-two"></div>
	<div class="auto-container">
		<div class="row">
			<div class="col-xl-9 offset-xl-3">
				<div class="row align-items-center">
					<div class="col-lg-4 order-lg-2">
						<ul class="nav nav-tabs tab-btn-style-one" role="tablist">

			<?php if ( $chariti_list ) : ?>
				<?php
				foreach ( $chariti_list as $key => $item ) {
					$chariti_list_single_tab = $item['chariti_list_single_tab'];
					$chariti_list_single     = $item['chariti_list_single'];
					$chariti_campaign        = charitable_get_campaign( $chariti_list_single );
					$chariti_categories      = $chariti_campaign->get( 'categories', true );
					?>
							<li class="nav-item wow fadeInUp" data-wow-delay=".6s">
								<a class="nav-link <?php echo wp_kses_post( $chariti_list_single_tab ); ?>" id="tab-two"
									data-toggle="tab" href="#tab-two-<?php echo $key; ?>" role="tab"
									aria-controls="tab-two" aria-selected="false">
									<h4><?php echo wp_kses_post( $chariti_categories ); ?></h4>
								</a>
							</li>
				<?php } ?>
			<?php endif; ?>
						</ul>
					</div>
					<div class="col-lg-8">
						<!-- Tab panes -->
						<div class="tab-content">
			<?php
			function ed_remove_phone_field_from_donation_form( $fields ) {
				unset( $fields['phone'] );
				unset( $fields['address'] );
				unset( $fields['address_2'] );
				unset( $fields['city'] );
				unset( $fields['state'] );
				unset( $fields['country'] );
				unset( $fields['postcode'] );
				return $fields;
			}
			add_filter( 'charitable_donation_form_user_fields', 'ed_remove_phone_field_from_donation_form' );
			?>
			<?php
			foreach ( $chariti_list as $key => $item ) {
				$chariti_list_single_tab = $item['chariti_list_single_tab'];
				$chariti_list_single     = $item['chariti_list_single'];
				$chariti_list_single_img = '';
				if ( isset( $events_single_bg_metabox_url ) && ! empty( $events_single_bg_metabox_url ) ) :
					$chariti_list_single_img = ( $item['chariti_list_single_img']['id'] != '' ) ? wp_get_attachment_url( $item['chariti_list_single_img']['id'], 'full' ) : $item['chariti_list_single_img']['url'];
				endif;
				?>
				<?php
				$chariti_campaign        = charitable_get_campaign( $chariti_list_single );
				$chariti_image_url       = get_the_post_thumbnail_url( $chariti_campaign->ID );
				$chariti_currency_helper = charitable_get_currency_helper();
				$chariti_raised          = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_donated_amount() );
				$chariti_goal            = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_goal() );
				$chariti_post_title      = $chariti_campaign->post_title;
				$chariti_post_content    = get_post_meta( $chariti_campaign->ID, 'goodsoul_metabox_featured_content', true );
				$chariti_percent         = $chariti_campaign->get_percent_donated_raw();
				$chariti_categories      = $chariti_campaign->get( 'categories', true );
				$chariti_post_page_link  = get_post_permalink( $chariti_campaign->ID );
				?>
							<div class="tab-pane fadeInUp animated <?php echo wp_kses_post( $chariti_list_single_tab ); ?>"
								id="tab-two-<?php echo $key; ?>" role="tabpanel" aria-labelledby="tab-two">
								<h1><?php echo wp_kses_post( $chariti_post_title ); ?></h1>
								<div class="text">
				<?php echo wp_trim_words( $chariti_post_content, 11, '...' ); ?>
								</div>
								<div class="info-box">
									<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $raised_txt; ?></span>
				<?php echo wp_kses_post( $chariti_raised ); ?></a>
									<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $goal_txt; ?></span>
				<?php echo wp_kses_post( $chariti_goal ); ?></a>
								</div>
								<div class="progress-levels">
									<div class="progress-box animated fadeInRight" data-wow-delay="100ms"
										data-wow-duration="1500ms">
										<div class="inner">
											<div class="bar">
												<div class="bar-innner">
													<div class="bar-fill"
														data-percent="<?php echo wp_kses_post( round( $chariti_percent ) ); ?>">
														<div class="percent"></div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
				<?php echo do_shortcode( '[charitable_donation_form campaign_id=" ' . $chariti_campaign->ID . ' "]' ); ?>
							</div>
			<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
		
		<?php elseif ( $chariti_layout == 'style_11' ) : ?>
			<?php
			function ed_remove_phone_field_from_donation_form( $fields ) {
				unset( $fields['phone'] );
				unset( $fields['address'] );
				unset( $fields['address_2'] );
				unset( $fields['city'] );
				unset( $fields['state'] );
				unset( $fields['country'] );
				unset( $fields['postcode'] );
				return $fields;
			}
			add_filter( 'charitable_donation_form_user_fields', 'ed_remove_phone_field_from_donation_form' );
			?>
<section class="payment-donate-section-three">
	<div class="sec-bg-one"></div>
	<div class="sec-bg-two"></div>
	<div class="auto-container">
		<div class="row align-items-center">
			<div class="col-lg-8">
				<div class="wrapper-box">
			<?php
			foreach ( $chariti_list as $item ) {
				 $chariti_list_single = $item['chariti_list_single'];
				?>
				<?php
				  $chariti_campaign        = charitable_get_campaign( $chariti_list_single );
				  $chariti_image_url       = get_the_post_thumbnail_url( $chariti_campaign->ID );
				  $chariti_currency_helper = charitable_get_currency_helper();
				  $chariti_raised          = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_donated_amount() );
				  $chariti_goal            = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_goal() );
				  $chariti_post_title      = $chariti_campaign->post_title;
				  $chariti_post_content    = get_post_meta( $chariti_campaign->ID, 'goodsoul_metabox_featured_content', true );
				  $chariti_percent         = $chariti_campaign->get_percent_donated_raw();
				  $chariti_categories      = $chariti_campaign->get( 'categories', true );
				  $chariti_post_page_link  = get_post_permalink( $chariti_campaign->ID );
				?>
					<h1><?php echo wp_kses_post( $chariti_post_title ); ?></h1>
					<div class="info-box">
						<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $raised_txt; ?></span>
				<?php echo wp_kses_post( $chariti_raised ); ?></a>
						<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $goal_txt; ?></span>
				<?php echo wp_kses_post( $chariti_goal ); ?></a>
					</div>
					<div class="progress-levels">
						<div class="progress-box animated fadeInRight" data-wow-delay="100ms"
							data-wow-duration="1500ms">
							<div class="inner">
								<div class="bar">
									<div class="bar-innner">
										<div class="bar-fill"
											data-percent="<?php echo wp_kses_post( round( $chariti_percent ) ); ?>">
											<div class="percent"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php echo do_shortcode( '[charitable_donation_form campaign_id=" ' . $chariti_campaign->ID . ' "]' ); ?>
			<?php } ?>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="default-video-box">
					<a href="<?php echo esc_url( $video_url ); ?>"
						class="overlay-link lightbox-image video-fancybox ripple"><span
							class="flaticon-multimedia-1"></span></a>
				</div>
			</div>
		</div>

	</div>
</section>
		
		<?php elseif ( $chariti_layout == 'style_12' ) : ?>
			<?php
			function ed_remove_phone_field_from_donation_form( $fields ) {
				unset( $fields['phone'] );
				unset( $fields['address'] );
				unset( $fields['address_2'] );
				unset( $fields['city'] );
				unset( $fields['state'] );
				unset( $fields['country'] );
				unset( $fields['postcode'] );
				return $fields;
			}
			add_filter( 'charitable_donation_form_user_fields', 'ed_remove_phone_field_from_donation_form' );
			?>
<section class="payment-donate-section-four" style="background-image: url(images/background/bg-8.jpg);">
	<div class="auto-container">
		<div class="sec-title text-center light style-three">
			<h5><?php echo wp_kses_post( $main_title ); ?></h5>
			<h1><?php echo wp_kses_post( $sub_title ); ?></h1>
			<div class="text"><?php echo wp_kses_post( $content ); ?></div>
		</div>
			<?php
			foreach ( $chariti_list as $item ) {
				$chariti_list_single = $item['chariti_list_single'];
				?>
				<?php
				$chariti_campaign        = charitable_get_campaign( $chariti_list_single );
				$chariti_image_url       = get_the_post_thumbnail_url( $chariti_campaign->ID );
				$chariti_currency_helper = charitable_get_currency_helper();
				$chariti_raised          = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_donated_amount() );
				$chariti_goal            = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_goal() );
				$chariti_post_title      = $chariti_campaign->post_title;
				$chariti_percent         = $chariti_campaign->get_percent_donated_raw();
				$chariti_categories      = $chariti_campaign->get( 'categories', true );
				$chariti_post_page_link  = get_post_permalink( $chariti_campaign->ID );
				?>

		<div class="wrapper-box asajhs">
			<div class="donate-info-wrapper">
				<div class="info-box">
					<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $raised_txt; ?></span>
				<?php echo wp_kses_post( $chariti_raised ); ?></a>
					<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $goal_txt; ?></span>
				<?php echo wp_kses_post( $chariti_goal ); ?></a>
				</div>
				<!--Progress Levels-->
				<div class="progress-levels">

					<!--Skill Box-->
					<div class="progress-box animated fadeInRight" data-wow-delay="100ms" data-wow-duration="1500ms">
						<div class="inner">
							<div class="bar">
								<div class="bar-innner">
									<div class="bar-fill"
										data-percent="<?php echo wp_kses_post( round( $chariti_percent ) ); ?>">
										<div class="percent"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
				<?php echo do_shortcode( '[charitable_donation_form campaign_id=" ' . $chariti_campaign->ID . ' "]' ); ?>
		</div>
			<?php } ?>
	</div>
</section>
		<?php elseif ( $chariti_layout == 'style_14' ) : ?>
<section class="causes-section-two style_new_one">
		<div class="auto-container">
			<div class="sec-title text-center">
				<h1><?php echo wp_kses_post( $main_title ); ?></h1>
				<div class="text"><?php echo wp_kses_post( $content ); ?></div>
			</div>
			<div class="cause-wrapper">
				<div class="row">
				   
					<!-- Cause Block two -->
					<?php if ( $chariti_list ) : ?>
								<?php
								foreach ( $chariti_list as $item ) {
									$chariti_list_single     = $item['chariti_list_single'];
									$chariti_list_single_img = $item['chariti_list_single_img']['url'];
									$chariti_list_single_img = ( $item['chariti_list_single_img']['id'] != '' ) ? wp_get_attachment_url( $item['chariti_list_single_img']['id'], 'full' ) : $item['chariti_list_single_img']['url'];
									?>
									<?php
									$chariti_campaign        = charitable_get_campaign( $chariti_list_single );
									$chariti_image_url       = get_the_post_thumbnail_url( $chariti_campaign->ID );
									$chariti_currency_helper = charitable_get_currency_helper();
									$chariti_raised          = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_donated_amount() );
									$chariti_goal            = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_goal() );
									$chariti_post_title      = $chariti_campaign->post_title;
									$chariti_post_content    = get_post_meta( $chariti_campaign->ID, 'goodsoul_metabox_featured_content', true );
									$chariti_percent         = $chariti_campaign->get_percent_donated_raw();
									$chariti_categories      = $chariti_campaign->get( 'categories', true );
									$chariti_post_page_link  = get_post_permalink( $chariti_campaign->ID );
									$chariti_post_country    = get_post_meta( $chariti_campaign->ID, 'goodsoul_metabox_event_country', true );

									?>
					<div class="cause-block-two col-lg-4">
						<div class="inner-box">
							<div class="image">
							<img src="
									<?php
									if ( ! empty( $chariti_list_single_img ) ) {
										echo esc_url( $chariti_list_single_img );
									} else {
										echo esc_url( $chariti_image_url ); }
									?>
								" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
								<div class="overlay">
									<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>" class="theme-btn btn-style-seventeen"><span><?php echo $main_button; ?></span></a>
								</div>
							</div>
							<div class="lower-content">
								<!--Progress Levels-->
								<div class="progress-levels style-two">
											
									<!--Skill Box-->
									<div class="progress-box wow fadeInRight" data-wow-delay="100ms" data-wow-duration="1500ms">
										<div class="inner">
											<div class="bar">
												<div class="bar-innner"><div class="bar-fill" data-percent="<?php echo wp_kses_post( round( $chariti_percent ) ); ?>"><div class="percent"></div></div></div>
											</div>
										</div>
									</div>
								</div>
								<div class="wrapper-box">
									<h6><span><?php esc_html_e( 'Country' ); ?></span>: <?php echo $chariti_post_country; ?></h6>
									<h4><a href="cause-details.html"><?php echo wp_kses_post( $chariti_post_title ); ?></a></h4>
									<div class="info-box">
										<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $raised_txt; ?>:</span> <?php echo wp_kses_post( $chariti_raised ); ?></a>
										<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $goal_txt; ?>:</span> <?php echo wp_kses_post( $chariti_goal ); ?></a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php } endif; ?>
					
				</div>                
			</div>
		</div>
	</section>
			<?php
	endif;
	}
}
\Elementor\Plugin::instance()->widgets_manager->register( new \Chariti_area__o() );
