<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
use \Elementor\Icons_Manager;

class GoodSoul_Testimonial extends \Elementor\Widget_Base {

	public function get_name() {
		return 'goodsoul_testimonial';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Testimonial', 'goodsoul-core' );
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
				'label' => esc_html__( 'Testimonial', 'goodsoul-core' ),
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
					'style_4' => esc_html__( 'Style 4', 'goodsoul-core' ),
					'style_5' => esc_html__( 'Style 5', 'goodsoul-core' ),

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
				'condition' => array( 'select_layout' => 'style_4' ),
			)
		);

		$this->add_control(
			'image_1',
			array(
				'label'     => __( 'Background First Image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
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
				'condition' => array( 'select_layout' => 'style_2' ),
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
				'condition'     => array( 'select_layout' => 'style_2' ),
			)
		);
		$this->add_control(
			'tagline',
			array(
				'label'      => esc_html__( 'Tagline', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'default'    => __( 'FEEDBACKS' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'select_layout',
							'operator' => '==',
							'value'    => 'style_3',
						),
						array(
							'name'     => 'select_layout',
							'operator' => '==',
							'value'    => 'style_4',
						),
						array(
							'name'     => 'select_layout',
							'operator' => '==',
							'value'    => 'style_5',
						),
					),
				),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Numbers speaking' ),
			)
		);
		$this->add_control(
			'content',
			array(
				'label'     => esc_html__( 'Text', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => __( 'Love learning about crazy facts? Then read these amazing facts that will tickle your brain.' ),
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
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'It’s helped me so much. Now I can <br>really get around office.' ),
			)
		);
		$repeater->add_control(
			'content',
			array(
				'label'   => esc_html__( 'What said', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Goodsoul! charities provided the jump <br>start we needed to expand our all efforts <br>and train more volunteers.' ),
			)
		);

		$repeater->add_control(
			'name',
			array(
				'label'   => esc_html__( 'Author Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Ollie Reuben' ),
			)
		);
		$repeater->add_control(
			'designation',
			array(
				'label'   => esc_html__( 'Author Designation', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'CEO & Founder ' ),
			)
		);

		$repeater->add_control(
			'company',
			array(
				'label'   => esc_html__( 'Company Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Sun Life' ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'         => esc_html__( 'Company link', 'goodsoul-core' ),
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
			'stars',
			array(
				'label'   => esc_html__( 'Given Stars', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'1' => esc_html__( '1', 'goodsoul-core' ),
					'2' => esc_html__( '2', 'goodsoul-core' ),
					'3' => esc_html__( '3', 'goodsoul-core' ),
					'4' => esc_html__( '4', 'goodsoul-core' ),
					'5' => esc_html__( '5', 'goodsoul-core' ),

				),
				'default' => esc_html__( '5', 'goodsoul-core' ),
			)
		);

		$this->add_control(
			'author_list',
			array(
				'label'      => esc_html__( 'Client List', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::REPEATER,
				'fields'     => $repeater->get_controls(),
				'default'    => array(
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
							'value'    => 'style_2',
						),
						array(
							'name'     => 'select_layout',
							'operator' => '==',
							'value'    => 'style_3',
						),
						array(
							'name'     => 'select_layout',
							'operator' => '==',
							'value'    => 'style_5',
						),
					),
				),
			)
		);

		$repeater_2 = new Repeater();

		$repeater_2->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'It’s helped me so much. Now I can <br>really get around office.' ),
			)
		);
		$repeater_2->add_control(
			'content',
			array(
				'label'   => esc_html__( 'What said', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Goodsoul! charities provided the jump <br>start we needed to expand our all efforts <br>and train more volunteers.' ),
			)
		);

		$repeater_2->add_control(
			'name',
			array(
				'label'   => esc_html__( 'Author Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Ollie Reuben' ),
			)
		);
		$repeater_2->add_control(
			'designation',
			array(
				'label'   => esc_html__( 'Author Designation', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'CEO & Founder ' ),
			)
		);

		$repeater_2->add_control(
			'company',
			array(
				'label'   => esc_html__( 'Company Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Sun Life' ),
			)
		);

		$repeater_2->add_control(
			'link',
			array(
				'label'         => esc_html__( 'Company link', 'goodsoul-core' ),
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
			'author_list_2',
			array(
				'label'     => esc_html__( 'Client List', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::REPEATER,
				'fields'    => $repeater->get_controls(),
				'default'   => array(
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
				'condition' => array( 'select_layout' => 'style_4' ),
			)
		);
		$this->end_controls_section();
	}
	protected function render() {
		$settings      = $this->get_settings_for_display();
		$title         = $settings['title'];
		$tagline       = $settings['tagline'];
		$content       = $settings['content'];
		$icon          = $settings['icon'];
		$author_list   = $settings['author_list'];
		$author_list_2 = $settings['author_list_2'];
		$select_layout = $settings['select_layout'];
		$image_1       = isset( $settings['image_1']['id'] ) ? wp_get_attachment_url( $settings['image_1']['id'], 'full' ) : '#';
		$image_2       = isset( $settings['image_2']['id'] ) ? wp_get_attachment_url( $settings['image_2']['id'], 'full' ) : '#';
		$youtube_link  = isset( $settings['youtube_link']['url'] ) ? $settings['youtube_link']['url'] : '#';
		?>

<!-- Testimonial Section Four -->
		<?php if ( $select_layout == 'style_1' ) { ?>
<section class="testimonial-section-four">
	<div class="auto-container">
		<div class="sec-title text-center">
			<h1><?php echo wp_kses_post( $title ); ?></h1>
			<div class="text"><?php echo wp_kses_post( $content ); ?></div>
		</div>
		<div class="row">
			<div class="three-item-carousel owl-theme owl-carousel owl-nav-none owl-dot-style-one">
				<?php
				foreach ( $author_list as $author ) {
					$author_photo        = ( $author['image']['id'] != '' ) ? wp_get_attachment_url( $author['image']['id'], 'full' ) : $author['image']['url'];
					$title               = $author['title'];
					$content             = $author['content'];
					$author_name         = $author['name'];
					$author_designation  = $author['designation'];
					$author_company      = $author['company'];
					$author_company_link = $author['link']['url'];
					?>

				<div class="testimonial-block-four">
					<div class="inner-box">
						<?php if ( $author_photo ) : ?>
						<div class="image"><img src="<?php echo esc_url( $author_photo ); ?>"
								alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></div>
						<?php endif; ?>
						<h4><?php echo wp_kses_post( $title ); ?></h4>
						<div class="text"><?php echo wp_kses_post( $content ); ?> </div>
						<div class="author-title"><?php echo wp_kses_post( $author_name ); ?></div>
						<div class="designation"><?php echo wp_kses_post( $author_designation ); ?><a
								href="<?php echo esc_url( $author_company_link ); ?>"><?php echo wp_kses_post( $author_company ); ?></a>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</section>
		<!-- Testimonial Section -->
			<?php
		} elseif ( $select_layout == 'style_2' ) {
			?>
		<section class="testimonial-section">
		<div class="sec-bg-one" style="background-image: url(<?php echo esc_url( $image_1 ); ?>);"></div>
		<div class="sec-bg-two" style="background-image: url(<?php echo esc_url( $image_2 ); ?>);"></div>
		<div class="auto-container">
			<div class="row align-items-lg-center justify-content-lg-between">
				<div class="col-lg-8">
					<div class="testimonial-carousel-wrapper">
						<div class="single-item-carousel owl-carousel owl-theme">
					<?php
					foreach ( $author_list as $author ) {
						$author_photo        = ( $author['image']['id'] != '' ) ? wp_get_attachment_url( $author['image']['id'], 'full' ) : $author['image']['url'];
						$title               = $author['title'];
						$content             = $author['content'];
						$author_name         = $author['name'];
						$author_designation  = $author['designation'];
						$author_company      = $author['company'];
						$author_company_link = $author['link']['url'];
						$stars               = $author['stars'];
						?>
							<div class="testimonial-block-one">
								<div class="inner-box">
									<div class="author-box">
										<div class="author-info">
											<div class="image"><img src="<?php echo esc_url( $author_photo ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></div>
											<div class="author-title"><?php echo wp_kses_post( $author_name ); ?></div>
											<div class="designation"><?php echo wp_kses_post( $author_designation ); ?><a href="<?php echo esc_url( $author_company_link ); ?>"><?php echo wp_kses_post( $author_company ); ?></a></div>
											<div class="logo"></div>
										</div>                                            
									</div>
									<div class="content-box">
										<h4><?php echo wp_kses_post( $title ); ?></h4>
										<div class="text"><?php echo wp_kses_post( $content ); ?></div>
										<div class="rating">
										<?php for ( $i = 0;$i < $stars;$i++ ) { ?>
											<a href="#"><span class="fa fa-star"></span></a>
											<?php } ?>
										</div>
										<div class="logo"></div>
									</div>
								</div>
							</div>
							<?php } ?>
						</div>
						
					</div>
				</div>
				<div class="col-lg-4">
					<div class="default-video-box">
						<a href="<?php echo esc_url( $youtube_link ); ?>"
							class="overlay-link lightbox-image video-fancybox ripple"><span
								class="flaticon-multimedia-1"></span></a>
					</div>
				</div>
			</div>
		</div>
	</section>
										<?php } elseif ( $select_layout == 'style_3' ) { ?>
			   <!-- Testimonial Section Two -->
	<section class="testimonial-section-two">
		<div class="auto-container">
			<div class="sec-title text-center">
				<h5><?php echo wp_kses_post( $tagline ); ?></h5>
				<h1><?php echo wp_kses_post( $title ); ?></h1>
			</div>
			<div class="wrapper-box">
				<div class="single-item-carousel owl-theme owl-carousel owl-dot-style-one owl-nav-none">
				<?php
				foreach ( $author_list as $author ) {
					$author_photo        = ( $author['image']['id'] != '' ) ? wp_get_attachment_url( $author['image']['id'], 'full' ) : $author['image']['url'];
					$title               = $author['title'];
					$content             = $author['content'];
					$author_name         = $author['name'];
					$author_designation  = $author['designation'];
					$author_company      = $author['company'];
					$author_company_link = $author['link']['url'];
					$stars               = $author['stars'];
					?>
				<!-- Testimonial Block Two -->
				<div class="testimonial-block-two">
					<div class="inner-box">
						<div class="author-box">
							<div class="image">
								<?php if ( $author_photo ) : ?>
								<img src="<?php echo esc_url( $author_photo ); ?>" alt="asas">
								<?php endif; ?>
								<div class="share-icon"><span class="flaticon-share"></span></div>
							</div>
						</div>
						<div class="content-box">
							<div class="icon-box"><img
									src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/icon-10.png"
									alt="">
							</div>
							<h3><?php echo wp_kses_post( $title ); ?></h3>
							<div class="text"><?php echo wp_kses_post( $content ); ?></div>
							<div class="author-info">
								<div class="author-title"><?php echo wp_kses_post( $author_name ); ?>,</div>
								<div class="designation"><?php echo wp_kses_post( $author_designation ); ?> <a
										href="<?php echo esc_url( $author_company_link ); ?>"><?php echo wp_kses_post( $author_company ); ?></a>
								</div>
							</div>
							<div class="rating">
								<?php
								for ( $i = 1;$i < 6;$i++ ) {
									if ( $i <= $stars ) {
										?>
								<a href="#"><span class="fa fa-star"></span></a>
										<?php
									} else {
										?>
								<a href="#"><span class="fa fa-star-o"></span></a>
										<?php
									}
								}
								?>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</section>


			<?php
										} elseif ( $select_layout == 'style_4' ) {
											?>
<!-- Testimonial Section Three  -->
<section class="testimonial-section-three">
	<div class="auto-container">
		<div class="sec-title style-two">
											<?php if ( ! empty( $icon ) ) { ?>
			<div class="icon-box"><span class="<?php echo $icon['value']; ?>"></span></div>
			<?php } ?>
			<h5><?php echo wp_kses_post( $tagline ); ?></h5>
			<h1><?php echo wp_kses_post( $title ); ?></h1>
		</div>
		<div class="wrapper-box">
			<div class="three-item-carousel owl-carousel owl-theme owl-dots-none owl-nav-style-two">
											<?php
											foreach ( $author_list_2 as $author ) {
												$title               = $author['title'];
												$content             = $author['content'];
												$author_name         = $author['name'];
												$author_designation  = $author['designation'];
												$author_company      = $author['company'];
												$author_company_link = $author['link']['url'];
												?>
				<div class="testimonial-block-three">
					<div class="inner-box">
						<div class="top-content">
							<div class="quote"><span class="flaticon-quote"></span></div>
							<h4><?php echo wp_kses_post( $title ); ?></h4>
							<div class="text"><?php echo wp_kses_post( $content ); ?></div>
						</div>
						<div class="author-info">
							<div class="author-title"><?php echo wp_kses_post( $author_name ); ?></div>
							<div class="designation"><?php echo wp_kses_post( $author_designation ); ?> <a
									href="<?php echo esc_url( $author_company_link ); ?>"><?php echo wp_kses_post( $author_company ); ?></a>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</section>

											<?php
										} elseif ( $select_layout == 'style_5' ) {
											?>

<!-- Testimonial Section Three  -->
<section class="testimonial-section-five">
	<div class="auto-container">
		<div class="sec-title style-three text-center">
			<h5><?php echo wp_kses_post( $tagline ); ?></h5>
			<h1><?php echo wp_kses_post( $title ); ?></h1>
		</div>
		<div class="wrapper-box">
			<div class="three-item-carousel owl-carousel owl-theme owl-dot-style-one owl-nav-none">
											<?php
											foreach ( $author_list as $author ) {
												$author_photo        = ( $author['image']['id'] != '' ) ? wp_get_attachment_url( $author['image']['id'], 'full' ) : $author['image']['url'];
												$title               = $author['title'];
												$content             = $author['content'];
												$author_name         = $author['name'];
												$author_designation  = $author['designation'];
												$author_company      = $author['company'];
												$author_company_link = $author['link']['url'];
												?>
				<div class="testimonial-block-five">
					<div class="inner-box">
						<div class="top-content">
												<?php if ( $author_photo ) : ?>
							<div class="image"><img src="<?php echo esc_url( $author_photo ); ?>"
									alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></div>
							<?php endif; ?>
							<h4><?php echo wp_kses_post( $title ); ?></h4>
							<div class="text"><?php echo wp_kses_post( $content ); ?></div>
						</div>
						<div class="author-info">
							<div class="author-title"><?php echo wp_kses_post( $author_name ); ?></div>
							<div class="designation"><?php echo wp_kses_post( $author_designation ); ?><a
									href="<?php echo esc_url( $author_company_link ); ?>"><?php echo wp_kses_post( $author_company ); ?></a>
							</div>
						</div>
					</div>
												<?php } ?>
				</div>
												<?php } ?>
			</div>
		</div>
	</div>
</section>
	
						<?php
	}
}

						\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Testimonial() );
