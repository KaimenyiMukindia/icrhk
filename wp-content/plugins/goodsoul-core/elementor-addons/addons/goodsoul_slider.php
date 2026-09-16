<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Icons_Manager;

class GoodSoulBanner_Slider extends Widget_Base {
	public function get_name() {
		return 'goodsoul_banner_slider';
	}
	public function get_title() {
		return esc_html__( 'Good Soul Slider', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'banner_slider_content',
			array(
				'label' => esc_html__( 'Banner Slider Content', 'goodsoul-core' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'banner_slider_style',
			array(
				'label'   => esc_html__( 'Banner Slider Designs', 'goodsoul-core' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style_1',
				'options' => array(
					'style_1' => esc_html__( 'Main', 'goodsoul-core' ),
					'style_2' => esc_html__( 'Volunteer', 'goodsoul-core' ),
					'style_3' => esc_html__( 'Charity', 'goodsoul-core' ),
					'style_4' => esc_html__( 'Ecology', 'goodsoul-core' ),
					'style_5' => esc_html__( 'Education', 'goodsoul-core' ),
					'style_6' => esc_html__( 'Corona', 'goodsoul-core' ),
				),
			)
		);
		$repeater = new Repeater();
		$repeater->add_control(
			'banner_slider_design',
			array(
				'label'   => esc_html__( 'Inside Slider Designs', 'goodsoul-core' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style_1',
				'options' => array(
					'style_1' => esc_html__( 'One', 'goodsoul-core' ),
					'style_2' => esc_html__( 'Two', 'goodsoul-core' ),
				),
			)
		);
		$repeater->add_control(
			'slider_background_images',
			array(
				'label'   => __( 'Background image', 'goodsoul-core' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);
		$repeater->add_control(
			'slider_icon_images',
			array(
				'label'   => __( 'Background Icon image', 'goodsoul-core' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);
		$repeater->add_control(
			'banner_title',
			array(
				'label'       => esc_html__( 'Banner Title', 'goodsoul-core' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Children out of school' ),
				'placeholder' => esc_html__( 'Type your content here', 'goodsoul-core' ),
			)
		);

		$repeater->add_control(
			'tag_line_one',
			array(
				'label'        => __( 'Show Heading', 'goodsoul-core' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$repeater->add_control(
			'tag_line',
			array(
				'label'       => esc_html__( 'Banner Tag Line', 'goodsoul-core' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( '6 million out of 36 million Nigerian' ),
				'placeholder' => esc_html__( 'Type your content here', 'goodsoul-core' ),
				'condition'   => array( 'tag_line_one' => 'yes' ),
			)
		);

		$repeater->add_control(
			'tag_line_tt',
			array(
				'label'        => __( 'Show Heading two', 'goodsoul-core' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'no',
				'default'      => 'no',
			)
		);
		$repeater->add_control(
			'tag_line_t',
			array(
				'label'       => esc_html__( 'Banner Tag Line', 'goodsoul-core' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Type your content here', 'goodsoul-core' ),
				'condition'   => array( 'tag_line_tt' => 'no' ),
			)
		);

		$repeater->add_control(
			'banner_content',
			array(
				'label'       => esc_html__( 'Banner content', 'goodsoul-core' ),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => __( 'To improve the learning environment in primary schools by holistically creating world-class <br/>basic education systems to the community.', 'goodsoul-core' ),
				'placeholder' => esc_html__( 'Type your content here', 'goodsoul-core' ),
			)
		);

		$repeater->add_control(
			'text_align',
			array(
				'label'   => __( 'Alignment', 'goodsoul-core' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left'   => array(
						'title' => __( 'Left', 'goodsoul-core' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'goodsoul-core' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'goodsoul-core' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default' => 'center',
				'toggle'  => true,
			)
		);

		$repeater->start_controls_tabs(
			'action_button_tabs'
		);
			$repeater->start_controls_tab(
				'first_button',
				array(
					'label' => __( 'First Button', 'goodsoul-core' ),
				)
			);
			$repeater->add_control(
				'button_1_text',
				array(
					'label'       => esc_html__( 'Button Name', 'goodsoul-core' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => __( 'Donate Now', 'goodsoul-core' ),
					'placeholder' => esc_html__( 'Type your content here', 'goodsoul-core' ),
				)
			);
			$repeater->add_control(
				'button_1_link',
				array(
					'label'         => esc_html__( 'Button link', 'goodsoul-core' ),
					'type'          => Controls_Manager::URL,
					'show_external' => true,
					'default'       => array(
						'url'         => '',
						'is_external' => true,
						'nofollow'    => true,
					),
				)
			);

			$repeater->end_controls_tab();

			$repeater->start_controls_tab(
				'second_button',
				array(
					'label' => __( 'Second Button', 'goodsoul-core' ),
				)
			);
			$repeater->add_control(
				'second_button_on',
				array(
					'label'        => __( 'Show Second Button', 'goodsoul-core' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'no',
				)
			);
			$repeater->add_control(
				'button_2_text',
				array(
					'label'       => esc_html__( 'Second Button Name', 'goodsoul-core' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => __( 'Donate Now', 'goodsoul-core' ),
					'placeholder' => esc_html__( 'Type your content here', 'goodsoul-core' ),
					'condition'   => array( 'second_button_on' => 'yes' ),
				)
			);
			$repeater->add_control(
				'button_2_link',
				array(
					'label'         => esc_html__( 'Second Button link', 'goodsoul-core' ),
					'type'          => Controls_Manager::URL,
					'show_external' => true,
					'default'       => array(
						'url'         => '',
						'is_external' => true,
						'nofollow'    => true,
					),
					'condition'     => array( 'second_button_on' => 'yes' ),
				)
			);

			$repeater->end_controls_tab();

		$this->add_control(
			'slider_list',
			array(
				'label'   => esc_html__( 'Slider list', 'goodsoul-core' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(
						'list_title'   => __( 'Slide #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Slide #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Slide #3', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
		);

	}
	protected function render() {
		$settings            = $this->get_settings_for_display();
		$banner_slider_style = $settings['banner_slider_style'];
		$slider_list         = $settings['slider_list'];
		?>
		  <!-- Bnner Section -->
			<?php if ( $banner_slider_style == 'style_1' ) { ?>
		  <section class="banner-section">
		<div class="swiper-container banner-slider">
			<div class="swiper-wrapper">
				<!-- Slide Item -->
				<?php if ( $slider_list ) : ?>
						<?php
						foreach ( $slider_list as $item ) {
							$button_1_name = $item['button_1_text'];
							$button_1_link = $item['button_1_link']['url'];
							$button_2_text = $item['button_2_text'];
							$button_2_link = isset( $item['button_2_link']['url'] ) ? $item['button_2_link']['url'] : '';
							$tagline       = $item['tag_line'];
							$tag_line_t    = $item['tag_line_t'];
							$title         = $item['banner_title'];
							$image         = ( $item['slider_background_images']['id'] != '' ) ? wp_get_attachment_url( $item['slider_background_images']['id'], 'full' ) : $item['slider_background_images']['url'];
							$content       = $item['banner_content'];
							?>
				<div class="swiper-slide" style="background-image: url(<?php echo esc_url( $image ); ?>);">
					<div class="content-outer">
						<div class="content-box justify-content-center">
							<div class="inner text-center">
							<?php if ( ! empty( $button_2_text ) ) { ?>
								<div class="link-box-two"><a href="<?php echo esc_url( $button_2_link ); ?>" class="theme-btn default-btn"><?php echo wp_kses_post( $button_2_text ); ?></a></div>
							<?php } ?>
							<?php if ( ! empty( $tag_line_t ) ) { ?>
								<h3><?php echo wp_kses_post( $tag_line_t ); ?></h3>
							<?php } ?>
							<?php if ( ! empty( $tagline ) ) { ?>
								<h4><span class="border-shape-left"></span>
								<?php echo wp_kses_post( $tagline ); ?> <span class="border-shape-right"></span></h4>
							<?php } ?>
								<h1><?php echo wp_kses_post( $title ); ?></h1>
								<div class="text"><?php echo wp_kses_post( $content ); ?></div>
								<?php if ( ! empty( $button_1_link ) ) { ?>
									<div class="link-box">	<a href="<?php echo esc_url( $button_1_link ); ?>"
												 class="theme-btn btn-style-one donate-box-btn"><span><?php echo wp_kses_post( $button_1_name ); ?></span></a></div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
					<?php endif; ?>
				<!-- Slide Item -->
			</div>
			<div class="banner-slider-pagination style-two"></div>
			<div class="banner-slider-nav style-one">
				<div class="banner-slider-control banner-slider-button-prev"><span class="fa fa-angle-left"></span></div>
				<div class="banner-slider-control banner-slider-button-next"><span class="fa fa-angle-right"></span> </div>
			</div>
		</div>
	</section>
	<!-- End Bnner Section -->
		<!-- Bnner Section -->
				<?php
			} elseif ( $banner_slider_style == 'style_2' ) {
				?>
		<section class="banner-section style-two">
		<div class="swiper-container banner-slider">
			<div class="swiper-wrapper">
				<!-- Slide Item -->
				<?php if ( $slider_list ) : ?>
						<?php
						foreach ( $slider_list as $item ) {
							$img_link      = ( $item['slider_background_images']['id'] != '' ) ? wp_get_attachment_url( $item['slider_background_images']['id'], 'full' ) : $item['slider_background_images']['url'];
							$tagline       = $item['tag_line'];
							$button_1_name = $item['button_1_text'];
							$button_1_link = $item['button_1_link']['url'];
							$title         = $item['banner_title'];
							$text_align    = $item['text_align'];
							?>
					<div class="swiper-slide" style="background-image: url(<?php echo esc_url( $img_link ); ?>);">
						<div class="content-outer">
							<div class= "content-box
							<?php
							if ( $text_align == 'right' ) {
								echo 'justify-content-end';}
							?>
									">
							<div class="inner">
								<h4><?php echo wp_kses_post( $tagline ); ?></h4>
								<h1><?php echo wp_kses_post( $title ); ?></h1>
								<?php if ( ! empty( $button_1_link ) ) { ?>
								<div class="link-box"><a href="<?php echo esc_url( $button_1_link ); ?>" class="theme-btn btn-style-five"><span><?php echo wp_kses_post( $button_1_name ); ?></span></a></div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
					<?php } ?>
					<?php endif; ?>
			</div>
			<div class="banner-slider-pagination style-three"></div>
			</div>
	</section>
	<!-- End Bnner Section -->

		<!-- Bnner Section -->
		<?php } elseif ( $banner_slider_style == 'style_3' ) { ?>
		<section class="banner-section style-three">
		<div class="swiper-container banner-slider">
			<div class="swiper-wrapper">
				<!-- Slide Item -->
				<?php if ( $slider_list ) : ?>
					<?php
					foreach ( $slider_list as $item ) {
						$img_link      = ( $item['slider_background_images']['id'] != '' ) ? wp_get_attachment_url( $item['slider_background_images']['id'], 'full' ) : $item['slider_background_images']['url'];
						$icon_link     = ( $item['slider_icon_images']['id'] != '' ) ? wp_get_attachment_url( $item['slider_icon_images']['id'], 'full' ) : $item['slider_icon_images']['url'];
						$button_1_name = $item['button_1_text'];
						$button_1_link = $item['button_1_link']['url'];
						$button_2_name = $item['button_2_text'];
						$button_2_link = $item['button_2_link']['url'];
						$title         = $item['banner_title'];
						?>
				<div class="swiper-slide" style="background-image: url(<?php echo esc_url( $img_link ); ?>);">
					<div class="content-outer">
						<div class="content-box justify-content-center">
							<div class="inner text-center">
								<div class="logo"><img src="<?php echo esc_url( $icon_link ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></div>
								<h1><?php echo wp_kses_post( $title ); ?></h1>
								<div class="link-box">
								<?php if ( ! empty( $button_1_link ) ) { ?>
									<a href="<?php echo esc_url( $button_1_link ); ?>" class="theme-btn btn-style-ten"><span><?php echo wp_kses_post( $button_1_name ); ?></span></a>
									<?php } ?>
								<?php if ( ! empty( $button_2_link ) ) { ?>
									<a href="<?php echo esc_url( $button_2_link ); ?>" class="theme-btn btn-style-eleven"><span><?php echo wp_kses_post( $button_2_name ); ?></span></a>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
				<?php endif; ?>
			</div>
			<div class="banner-slider-pagination style-four"></div>
			<div class="banner-slider-nav style-one">
				<div class="banner-slider-control banner-slider-button-prev"><span class="fa fa-angle-left"></span></div>
				<div class="banner-slider-control banner-slider-button-next"><span class="fa fa-angle-right"></span> </div>
			</div>
		</div>
	</section>
					<!-- End Bnner Section -->

		<!-- Bnner Section -->
				<?php
		} elseif ( $banner_slider_style == 'style_4' ) {
					$content_left_4 = true;
			?>
	<section class="banner-section style-five">
		<div class="swiper-container banner-slider">
			<div class="swiper-wrapper">
				<!-- Slide Item -->
				<?php if ( $slider_list ) : ?>
					<?php
					foreach ( $slider_list as $item ) {
						$img_link      = ( $item['slider_background_images']['id'] != '' ) ? wp_get_attachment_url( $item['slider_background_images']['id'], 'full' ) : $item['slider_background_images']['url'];
						$icon_link     = ( $item['slider_icon_images']['id'] != '' ) ? wp_get_attachment_url( $item['slider_icon_images']['id'], 'full' ) : $item['slider_icon_images']['url'];
						$content       = $item['banner_content'];
						$button_1_name = $item['button_1_text'];
						$button_1_link = $item['button_1_link']['url'];
						$button_2_name = $item['button_2_text'];
						$button_2_link = isset( $item['button_2_link']['url'] ) ? $item['button_2_link']['url'] : '#';
						$title         = $item['banner_title'];
						$text_align    = $item['text_align'];

						?>
							<div class="swiper-slide" style="background-image: url(<?php echo esc_url( $img_link ); ?>);">
								<div class="content-outer">
									<div class="content-box 
									<?php
									if ( $text_align == 'right' ) {
										echo 'justify-content-end';}
									?>
									">
										<div class="inner">
											<h1><?php echo wp_kses_post( $title ); ?></h1>
											<div class="text">
											<?php
											if ( ! empty( $content ) ) {
												echo wp_kses_post( $content );}
											?>
											</div>
											<div class="link-box">
											<?php if ( ! empty( $button_1_name ) ) { ?>
											<a href="<?php echo esc_url( $button_1_link ); ?>" class="theme-btn btn-style-fourteen"><span><?php echo wp_kses_post( $button_1_name ); ?></span></a>
											<?php } ?>
											<?php if ( ! empty( $button_2_name ) ) { ?>
											<a href="<?php echo esc_url( $button_2_link ); ?>" class="theme-btn btn-style-fifteen donate-box-btn"><span><?php echo wp_kses_post( $button_2_name ); ?></span></a>
											<?php } ?>
											</div>
										</div>
									</div>
								</div>
							</div>
					<?php } //end foreach ?>
					<?php endif; ?>
			</div>
			<div class="banner-slider-pagination style-two"></div>
			<div class="banner-slider-nav style-one">
				<div class="banner-slider-control banner-slider-button-prev"><span class="flaticon-arrow-2"></span></div>
				<div class="banner-slider-control banner-slider-button-next"><span class="flaticon-arrow-2"></span> </div>
			</div>
		</div>
	</section>
	<!-- End Bnner Section -->

		<!-- Bnner Section -->
			<?php
		} elseif ( $banner_slider_style == 'style_5' ) {
			?>
	<section class="banner-section style-six">
		<div class="swiper-container banner-slider">
			<div class="swiper-wrapper">
				<!-- Slide Item -->
				<?php if ( $slider_list ) : ?>
					<?php
					foreach ( $slider_list as $item ) {
						$img_link      = ( $item['slider_background_images']['id'] != '' ) ? wp_get_attachment_url( $item['slider_background_images']['id'], 'full' ) : $item['slider_background_images']['url'];
						$tagline       = $item['tag_line'];
						$content       = $item['banner_content'];
						$button_1_name = $item['button_1_text'];
						$button_1_link = $item['button_1_link']['url'];
						$title         = $item['banner_title'];
						$text_align    = $item['text_align'];
						?>
				<div class="swiper-slide" style="background-image: url(<?php echo esc_url( $img_link ); ?>);">
					<div class="content-outer">
						<div class="content-box 
						<?php
						if ( $text_align == 'right' ) {
										echo 'justify-content-end';} elseif ( $text_align == 'center' ) {
													echo 'justify-content-center';
										}
										?>
										">	
							<div class="inner 
							<?php
							if ( $text_align == 'center' ) {
								echo esc_attr( 'text-center' ); }
							?>
							">
								<h1><?php echo wp_kses_post( $title ); ?></h1>
									<h2 
									<?php
									if ( $text_align != 'center' ) {
										?>
										class="border-shape" <?php } ?>><?php echo wp_kses_post( $tagline ); ?></h2>
								<div class="text"><?php echo wp_kses_post( $content ); ?></div>
								<?php
								if ( ! empty( $button_1_link ) ) {
									?>
								<div class="link-box">
									<a href=" <?php echo esc_url( $button_1_link ); ?> " class="theme-btn btn-style-sixteen donate-box-btn"><span><?php echo wp_kses_post( $button_1_name ); ?></span></a>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
					<?php endif; ?>
			</div>
			<div class="banner-slider-pagination style-two"></div>
			<div class="banner-slider-nav style-one">
				<div class="banner-slider-control banner-slider-button-prev"><span class="flaticon-arrow-2"></span></div>
				<div class="banner-slider-control banner-slider-button-next"><span class="flaticon-arrow-2"></span> </div>
			</div>
		</div>
	</section>
			<?php
		} elseif ( $banner_slider_style == 'style_6' ) {
			?>
	<section class="banner-section style-seven">
		<div class="swiper-container banner-slider">
			<div class="swiper-wrapper">
				<!-- Slide Item -->
				<?php if ( $slider_list ) : ?>
						<?php
						foreach ( $slider_list as $item ) {
							$img_link               = ( $item['slider_background_images']['id'] != '' ) ? wp_get_attachment_url( $item['slider_background_images']['id'], 'full' ) : $item['slider_background_images']['url'];
							$tagline                = $item['tag_line'];
							$button_1_name          = $item['button_1_text'];
							$button_2_name          = $item['button_2_text'];
							$item_first_button_link = $item['button_1_link'];
							$item_page_target       = $item['button_1_link']['is_external'] ? ' target="_blank"' : '';
							$item_page_nofollow     = $item['button_1_link']['nofollow'] ? ' rel="nofollow"' : '';
							$title                  = $item['banner_title'];
							$text_align             = $item['text_align'];

							$button_2_link          = $item['button_2_link'];
							$button_2_link_target   = $item['button_2_link']['is_external'] ? ' target="_blank"' : '';
							$button_2_link_nofollow = $item['button_2_link']['nofollow'] ? ' rel="nofollow"' : '';

							if ( $text_align == 'right' ) {
								$text_alignment = 'third_slide_new justify-content-end text-left';
							} elseif ( $text_align == 'center' ) {
								$text_alignment = 'justify-content-center text-center';
							} else {
								$text_alignment = '';
							}
							?>

				<div class="swiper-slide" style="background-image: url(<?php echo esc_url( $img_link ); ?>);">
					<div class="content-outer">
						<div class="content-box <?php echo esc_attr( $text_alignment ); ?>">
							<div class="inner">
								<h2><?php echo wp_kses_post( $tagline ); ?></h2>
								<h1><?php echo wp_kses_post( $title ); ?></h1>
							   
								<div class="link-box"><a href="<?php echo esc_url( $item_first_button_link['url'] ); ?>" <?php echo $item_page_target . ' ' . $item_page_nofollow; ?> class="theme-btn btn-style-seventeen"><span><?php echo $button_1_name; ?></span></a><a href="<?php echo esc_url( $button_2_link['url'] ); ?>" <?php echo $button_2_link_target . ' ' . $button_2_link_nofollow; ?> class="theme-btn btn-style-seventeen donate-box-btn"><span><?php echo $button_2_name; ?></span></a></div>
							</div>
						</div>
					</div>
				</div>

						<?php } endif; ?>	
			</div>
			<div class="banner-slider-pagination style-two"></div>
			<div class="banner-slider-nav style-one">
				<div class="banner-slider-control banner-slider-button-prev"><span class="flaticon-arrow-2"></span></div>
				<div class="banner-slider-control banner-slider-button-next"><span class="flaticon-arrow-2"></span> </div>
			</div>
		</div>
	</section>
	<?php } ?>
	<!-- End Bnner Section -->

	
						<?php
	}
}

Plugin::instance()->widgets_manager->register( new \GoodSoulBanner_Slider() );
