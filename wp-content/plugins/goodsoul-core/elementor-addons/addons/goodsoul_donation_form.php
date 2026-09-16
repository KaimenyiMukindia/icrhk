<?php
class Goodsoul_donation_form extends \Elementor\Widget_Base {



	public function get_name() {
		return 'good_donation_form';
	}
	public function get_title() {
		return esc_html__( 'Goodsoul Donation Form', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
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
				'label' => esc_html__( 'Donation Form Header', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'form_layout',
			array(
				'label'   => esc_html__( 'Form Layout', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'style_1' => esc_html__( 'Style 1', 'goodsoul-core' ),
					'style_2' => esc_html__( 'Style 2', 'goodsoul-core' ),
					'style_3' => esc_html__( 'Style 3', 'goodsoul-core' ),
					'style_4' => esc_html__( 'Style 4', 'goodsoul-core' ),
				),
				'default' => esc_html__( 'style_1', 'goodsoul-core' ),
			)
		);
		$this->add_control(
			'background_shape',
			array(
				'label'   => esc_html__( 'Background Image ', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),

			)
		);

		$this->add_control(
			'sub_title',
			array(
				'label'      => esc_html__( 'Sub Title', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::TEXTAREA,
				'default'    => __(
					'Every $1 you donate helps to someone in the world, Goodsoul safely handover to who really needed that money.
                '
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'form_layout',
							'operator' => '==',
							'value'    => 'style_1',
						),
						array(
							'name'     => 'form_layout',
							'operator' => '==',
							'value'    => 'style_4',
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
				'default'    => __( 'Play your part' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'form_layout',
							'operator' => '==',
							'value'    => 'style_1',
						),
						array(
							'name'     => 'form_layout',
							'operator' => '==',
							'value'    => 'style_4',
						),
					),
				),
			)
		);

		$this->add_control(
			'main_button',
			array(
				'label'   => esc_html__( 'Donate Button Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Donate Now' ),
			)
		);

		$this->add_control(
			'raised',
			array(
				'label'      => esc_html__( 'Raised Text', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'default'    => __( 'Raised:' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'form_layout',
							'operator' => '==',
							'value'    => 'style_2',
						),
						array(
							'name'     => 'form_layout',
							'operator' => '==',
							'value'    => 'style_3',
						),
					),
				),

			)
		);
		$this->add_control(
			'goal',
			array(
				'label'      => esc_html__( 'Goal Text', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'default'    => __( 'Goal:' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'form_layout',
							'operator' => '==',
							'value'    => 'style_2',
						),
						array(
							'name'     => 'form_layout',
							'operator' => '==',
							'value'    => 'style_3',
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
							'name'     => 'form_layout',
							'operator' => '==',
							'value'    => 'style_2',
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
							'name'     => 'form_layout',
							'operator' => '==',
							'value'    => 'style_2',
						),
						array(
							'name'     => 'form_layout',
							'operator' => '==',
							'value'    => 'style_3',
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
							'name'     => 'form_layout',
							'operator' => '==',
							'value'    => 'style_2',
						),
						array(
							'name'     => 'form_layout',
							'operator' => '==',
							'value'    => 'style_3',
						),
					),
				),
			)
		);

		$this->add_control(
			'chariti_list_single',
			array(
				'label'      => esc_html__( 'Charity list', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::SELECT,
				'options'    => $this->get_chariti(),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'form_layout',
							'operator' => '==',
							'value'    => 'style_2',
						),
						array(
							'name'     => 'form_layout',
							'operator' => '==',
							'value'    => 'style_1',
						),
						array(
							'name'     => 'form_layout',
							'operator' => '==',
							'value'    => 'style_4',
						),
					),
				),
			)
		);

		$repeatertwo = new \Elementor\Repeater();

		$repeatertwo->add_control(
			'tab_name',
			array(
				'label'   => esc_html__( 'Category Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __(
					'Children'
				),
			)
		);

		$repeatertwo->add_control(
			'sub_title',
			array(
				'label'   => esc_html__( 'Sub Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __(
					'Beguiled and demoralized by the charms of pleasure of the moment, so by desire, that they cannot foresee.'
				),
			)
		);
		$repeatertwo->add_control(
			'main_title',
			array(
				'label'   => esc_html__( 'Main Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Donate us to achieve our goal' ),
			)
		);

		$repeatertwo->add_control(
			'is_premium',
			array(
				'label'        => __( 'Is Active??', 'goodsoul-core' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 0,

			)
		);

		$repeatertwo->add_control(
			'item_chariti_list_single',
			array(
				'label'   => esc_html__( 'chariti list', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $this->get_chariti(),
			)
		);

		$this->add_control(
			'items1',
			array(
				'label'      => esc_html__( 'Repeater List', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::REPEATER,
				'fields'     => $repeatertwo->get_controls(),
				'default'    => array(
					array(
						'list_title'   => esc_html__( 'Title #1', 'goodsoul-core' ),
						'list_content' => esc_html__( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => esc_html__( 'Title #2', 'goodsoul-core' ),
						'list_content' => esc_html__( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'form_layout',
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
		$settings    = $this->get_settings_for_display();
		$form_layout = $settings['form_layout'];
		$main_title  = $settings['main_title'];
		$sub_title   = $settings['sub_title'];
		$main_button = $settings['main_button'];

		$currency         = charitable_get_currency_helper();
		$currency_sym     = $currency->get_currency_symbol();
		$background_image = ( $settings['background_shape']['id'] != '' ) ? wp_get_attachment_image_url( $settings['background_shape']['id'], 'full' ) : $settings['background_shape']['url'];

		if ( $form_layout == 'style_1' || $form_layout == 'style_4' ) {
			$currency     = charitable_get_currency_helper();
			$currency_sym = $currency->get_currency_symbol();

			$chariti_list_single = $settings['chariti_list_single'];
			$campaign            = charitable_get_campaign( $chariti_list_single );

			$sugested_amounts = $campaign->get_suggested_donations();
			$campaign_link    = get_permalink( $chariti_list_single );
			?>
				<?php if ( $form_layout == 'style_1' ) : ?>
<section class="payment-donate-section" style="background-image: url(<?php echo esc_url( $background_image ); ?>);">
	<?php elseif ( $form_layout == 'style_4' ) : ?>
		<section class="payment-donate-section style_new_one" style="background-image: url(<?php echo esc_url( $background_image ); ?>);">
	<?php endif; ?>
	<div class="auto-container">
		<div class="sec-title text-center light">
			<h1><?php echo $main_title; ?></h1>
			<div class="text"><?php echo $sub_title; ?></div>
		</div>
		<form action="<?php echo esc_url( $campaign_link ); ?>" method="GET">
			<ul class="chicklet-list clearfix text-center">
				<?php
				$cunt = 0;
				foreach ( $sugested_amounts as $amount ) {
					?>
					
				<li class="goodsou-donform-suggested">
					<?php if ( $cunt == 0 ) : ?>
					<input type="radio" class="good_don_form-chec" id="donate-amount-<?php echo $cunt; ?>"
						value="<?php echo $amount['amount']; ?>" name="amount_out" checked="checked">
				<?php else : ?>
					<input type="radio" class="good_don_form-chec" id="donate-amount-<?php echo $cunt; ?>"
						value="<?php echo $amount['amount']; ?>" name="amount_out">
				<?php endif; ?>
					<label
						for="donate-amount-<?php echo $cunt; ?>"><?php echo $currency_sym . $amount['amount']; ?></label>
				</li>
					<?php
					$cunt++;
				}
				?>
			</ul>
				<?php if ( $form_layout == 'style_1' ) : ?>
			<div class="other-amount d-flex align-items-center justify-content-center">
				<div class="form-group">
					<span class="currency-sym-goodform-addon"><?php echo $currency_sym; ?></span>
					<input type="text" required="required" id="good_don_form_cust" name="amount_out"
						placeholder="Custom Amount"></div>
				<button class="theme-btn btn-style-five" type="submit"><span><?php echo $main_button; ?></span></button>
			</div>
			<?php elseif ( $form_layout == 'style_4' ) : ?>
			<div class="other-amount d-flex align-items-center justify-content-center">
				<div class="form-group"><input type="text" id="good_don_form_cust" name="amount_out" placeholder="Custom Amount"></div>
				<button class="theme-btn btn-style-seventeen" type="submit"><span><?php echo $main_button; ?></span></button>
			</div>
			<?php endif; ?>
		</form>
	</div>
</section>
				<?php
		} elseif ( $form_layout == 'style_2' ) {
			$chariti_list_single = $settings['chariti_list_single'];
			$campaign            = charitable_get_campaign( $chariti_list_single );

			$sugested_amounts = $campaign->get_suggested_donations();
			$campaign_link    = get_permalink( $chariti_list_single );
			$raised_txt       = $settings['raised'];
			$goal_txt         = $settings['goal'];
			$video_url        = $settings['video_url'];
			?>
<section class="payment-donate-section-three">
	<div class="sec-bg-one"></div>
	<div class="sec-bg-two"></div>
	<div class="auto-container">
		<div class="row align-items-center">
			<div class="col-lg-8">
				<div class="wrapper-box">
				<?php
				$chariti_raised         = $currency->get_monetary_amount( $campaign->get_donated_amount() );
				$chariti_goal           = $currency->get_monetary_amount( $campaign->get_goal() );
				$chariti_post_title     = $campaign->post_title;
				$chariti_post_page_link = get_post_permalink( $campaign->ID );
				$chariti_percent        = $campaign->get_percent_donated_raw();
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
					<form action="<?php echo esc_url( $campaign_link ); ?>" method="GET">
						<ul class="chicklet-list clearfix text-center">
						<?php
						$cunt = 0;
						foreach ( $sugested_amounts as $amount ) {
							?>
							<li class="goodsou-donform-suggested">
								<input type="radio" class="good_don_form-chec" id="donate-amount-<?php echo $cunt; ?>"
									value="<?php echo $amount['amount']; ?>" name="amount_out">
								<label
									for="donate-amount-<?php echo $cunt; ?>"><?php echo $currency_sym . $amount['amount']; ?></label>
							</li>
								<?php
								$cunt++;
						}
						?>
						</ul>

						<div class="other-amount d-flex align-items-center">
							<div class="form-group">
								<span class="currency-sym-goodform-addon"><?php echo $currency_sym; ?></span>
								<input type="text" required="required" id="good_don_form_cust" name="amount_out"
									placeholder="Custom Amount"></div>
							<button class="theme-btn btn-style-ten"
								type="submit"><span><?php echo $main_button; ?></span></button>
						</div>

					</form>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="default-video-box">
					<a href="<?php echo esc_url( $video_url['url'] ); ?>"
						class="overlay-link lightbox-image video-fancybox ripple"><span
							class="flaticon-multimedia-1"></span></a>
				</div>
			</div>
		</div>

	</div>
</section>
				<?php
		} elseif ( $form_layout == 'style_3' ) {
			$raised_txt = $settings['raised'];
			$goal_txt   = $settings['goal'];

			?>
<section class="payment-donate-section-two">
	<div class="sec-bg-one"></div>
	<div class="sec-bg-two"></div>
	<div class="auto-container">
		<div class="row">
			<div class="col-xl-9 offset-xl-3">
				<div class="row align-items-center">
					<div class="col-lg-4 order-lg-2">
						<ul class="nav nav-tabs tab-btn-style-one" role="tablist">
						<?php
						foreach ( $settings['items1'] as $item ) {
							$tab_name      = $item['tab_name'];
							$tab_id        = str_replace( ' ', '_', $tab_name );
							$tab_id        = strtolower( $tab_id );
							$is_premium    = $item['is_premium'];
							$area_seelcted = 'false';
							$active_class  = '';
							if ( $is_premium == 'yes' ) {
								  $active_class  = 'active';
								  $area_seelcted = 'true';
							}
							?>
							<li class="nav-item wow fadeInUp" data-wow-delay=".4s">
								<a class="nav-link <?php echo esc_attr( $active_class ); ?>"
									id="tab-<?php echo esc_attr( $tab_id ); ?>-area" data-toggle="tab"
									href="#tab-<?php echo esc_attr( $tab_id ); ?>" role="tab"
									aria-controls="tab-<?php echo esc_attr( $tab_id ); ?>"
									aria-selected="<?php echo esc_attr( $area_seelcted ); ?>">
									<h4><?php echo $tab_name; ?></h4>
								</a>
							</li>
								<?php
						}
						?>
						</ul>
					</div>
					<div class="col-lg-8">
						<!-- Tab panes -->
						<div class="tab-content">
						<?php
						foreach ( $settings['items1'] as $item ) {
							$tab_id                   = str_replace( ' ', '_', $item['tab_name'] );
							$tab_id                   = strtolower( $tab_id );
							$main_title               = $item['main_title'];
							$sub_title                = $item['sub_title'];
							$is_premium               = $item['is_premium'];
							$item_chariti_list_single = $item['item_chariti_list_single'];
							$campaign                 = charitable_get_campaign( $item_chariti_list_single );
							$sugested_amounts         = $campaign->get_suggested_donations();
							$campaign_link            = get_permalink( $item_chariti_list_single );
							$chariti_raised           = $currency->get_monetary_amount( $campaign->get_donated_amount() );
							$chariti_goal             = $currency->get_monetary_amount( $campaign->get_goal() );
							$chariti_post_title       = $campaign->post_title;
							$chariti_post_page_link   = get_post_permalink( $campaign->ID );
							$chariti_percent          = $campaign->get_percent_donated_raw();
							$active_class             = '';

							if ( $is_premium == 'yes' ) {
								$active_class = 'active';
							}
							?>
							<div class="tab-pane fadeInUp animated <?php echo esc_attr( $active_class ); ?>"
								id="tab-<?php echo esc_attr( $tab_id ); ?>" role="tabpanel"
								aria-labelledby="tab-<?php echo esc_attr( $tab_id ); ?>">
								<h1><?php echo $main_title; ?></h1>
								<div class="text">
								<?php echo $sub_title; ?>
								</div>
								<div class="info-box">
									<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $raised_txt; ?></span>
									<?php echo wp_kses_post( $chariti_raised ); ?></a>
									<a href="<?php echo wp_kses_post( $chariti_post_page_link ); ?>"><span><?php echo $goal_txt; ?></span>
									<?php echo wp_kses_post( $chariti_goal ); ?></a>
								</div>
								<!--Progress Levels-->
								<div class="progress-levels">

									<!--Skill Box-->
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
								<form action="<?php echo esc_url( $campaign_link ); ?>" method="GET">
									<ul class="chicklet-list clearfix text-center">
									<?php
									$cunt = 0;
									foreach ( $sugested_amounts as $amount ) {
										?>
										<li class="goodsou-donform-suggested">
											<input type="radio" class="good_don_form-chec"
												id="donate-amount-<?php echo $cunt; ?>"
												value="<?php echo $amount['amount']; ?>" name="amount_out">
											<label
												for="donate-amount-<?php echo $cunt; ?>"><?php echo $currency_sym . $amount['amount']; ?></label>
										</li>
											<?php
											$cunt++;
									}
									?>
									</ul>
									<div class="other-amount d-flex align-items-center">
										<div class="form-group">
											<span
												class="currency-sym-goodform-addon style-charity"><?php echo $currency_sym; ?></span>
											<input type="text" required="required" id="good_don_form_cust"
												name="amount_out" placeholder="Custom Amount">
										</div>
										<button class="theme-btn btn-style-ten"
											type="submit"><span><?php echo $main_button; ?></span></button>
									</div>
								</form>
							</div>
								<?php
						}
						?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

				<?php
		}
	}
}

\Elementor\Plugin::instance()->widgets_manager->register( new \Goodsoul_donation_form() );
