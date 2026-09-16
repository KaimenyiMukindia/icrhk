<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	use Elementor\Controls_Manager;
	use Elementor\Plugin;
	use Elementor\Utils;
	use Elementor\Widget_Base;
	use \Elementor\Repeater;
	use \Elementor\Icons_Manager;

class Goodsoul_Blogs_Selector extends Widget_Base {



	public function get_name() {
		return 'goodsoul_blog_selector';
	}

	public function get_title() {
		return esc_html__( 'Goodsoul Blog Seleector', 'goodsoul-core' );
	}

	public function get_icon() {
		return 'fa fa-object-ungroup';
	}

	public function get_categories() {
		return array( 'goodsoulcore' );
	}

	private function get_posts_custom() {
		$options = array();

		$args = array(
			'post_type'   => array( 'post' ),
			'post_status' => 'publish',
			'showposts'   => -1,
		);

		$causes = new WP_Query( $args );

		// The Loop
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
			'section_blogs',
			array(
				'label' => esc_html__( 'Blogs', 'goodsoul-core' ),
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
					'style_6' => esc_html__( 'Style 6', 'goodsoul-core' ),

				),
				'default' => esc_html__( 'style_1', 'goodsoul-core' ),
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'     => __( 'Choose Icon', 'text-domain' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-star',
					'library' => 'solid',
				),
				'condition' => array( 'select_layout' => 'style_5' ),
			)
		);

		$this->add_control(
			'tagline',
			array(
				'label'   => esc_html__( 'Tagline', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Team behind goodsoul', 'goodsoul-core' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => 'Our work would not be possible without the work of our dedicated volunteers.',
			)
		);
		$this->add_control(
			'button_text',
			array(
				'label'     => esc_html__( 'Button Name', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => __( 'Meet All Members', 'goodsoul-core' ),
				'condition' => array(
					'select_layout' => array( 'style_1', 'style_4' ),
				),
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
				'condition'     => array(
					'select_layout' => array( 'style_1', 'style_4' ),
				),
			)
		);

		$this->add_control(
			'column',
			array(
				'label'   => esc_html__( 'Number of Column', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '2',
				'options' => array(
					'2' => esc_html__( '2', 'goodsoul-core' ),
					'3' => esc_html__( '3', 'goodsoul-core' ),
					'4' => esc_html__( '4', 'goodsoul-core' ),
				),
			)
		);

		$repeatertwo = new \Elementor\Repeater();

		$repeatertwo->add_control(
			'post_name',
			array(
				'label'   => esc_html__( 'Select Posts', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $this->get_posts_custom(),
			)
		);

		$repeatertwo->add_control(
			'show_external_name',
			array(
				'label'   => esc_html__( 'Show external name for this post', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'false',
			)
		);

		$repeatertwo->add_control(
			'external_name',
			array(
				'label'       => esc_html__( 'Name of the post', 'goodsoul-core' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'External name of the post', 'goodsoul-core' ),
				'condition'   => array(
					'show_external_name' => 'yes',
				),
			)
		);

		$repeatertwo->add_control(
			'external_image',
			array(
				'label'   => esc_html__( 'Show external image for this post', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'false',
			)
		);

		$repeatertwo->add_control(
			'post_image',
			array(
				'label'     => esc_html__( 'Post image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'external_image' => 'yes',
				),
			)
		);

		goodsoul_get_animation_control( $repeatertwo );

		$this->add_control(
			'items1',
			array(
				'label'   => esc_html__( 'Repeater List', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeatertwo->get_controls(),
				'default' => array(
					array(
						'list_title'   => esc_html__( 'Title #1', 'goodsoul-core' ),
						'list_content' => esc_html__( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => esc_html__( 'Title #2', 'goodsoul-core' ),
						'list_content' => esc_html__( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

		$select_layout = $settings['select_layout'];
		$tagline       = $settings['tagline'];
		$title         = $settings['title'];
		$button_link   = $settings['button_link']['url'];
		$button_text   = $settings['button_text'];
		$icon          = $settings['icon'];

		$columnClass = '';
		$column      = $settings['column'];
		switch ( $column ) {
			case '3':
				$columnClass = 'col-lg-4';
				break;
			case '4':
				$columnClass = 'col-lg-3';
				break;
			default:
				$columnClass = 'col-lg-6';
		}

		?>


		<?php if ( $select_layout == 'style_1' ) { ?>
<!-- Blog Section -->
<section class="blog-section">
	<div class="auto-container">
		<div class="row m-0 justify-content-md-between align-items-end">
			<div class="sec-title">
				<h1><?php echo wp_kses_post( $tagline ); ?></h1>
				<div class="text"><?php echo wp_kses_post( $title ); ?></div>
			</div>
			<!--Link Btn-->
			<?php if ( ! empty( $button_link ) ) { ?>
			<div class="link-btn mb-50">
				<a href="<?php echo esc_url( $button_link ); ?>"
					class="theme-btn btn-style-one"><span><?php echo wp_kses_post( $button_text ); ?></span></a>
			</div>
			<?php } ?>
		</div>
		<div class="row">
			<?php
			foreach ( $settings['items1'] as $item ) {

				$external_image     = $item['external_image'];
				$show_external_name = $item['show_external_name'];

				$args       = array(
					'p'         => $item['post_name'],
					'post_type' => 'post',
				);
				$query      = new WP_Query( $args );
				$video_post = false;
				$count      = 1;
				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						$tags  = wp_get_post_categories( get_the_ID() );
						$image = get_post_meta( get_the_ID(), 'goodsoul_metabox_content_image', true );

						?>
			<!-- News Block One -->
			<div class="<?php echo esc_attr( $columnClass ); ?> col-md-6 news-block-one">
						<?php
						if ( $count % 2 == 0 ) {
							?>
				<div class="inner-box wow fadeInDown" data-wow-delay="400ms">
							<?php
						} else {
							?>
					<div class="inner-box wow fadeInUp" data-wow-delay="200ms">
							<?php
						}
						$count++;
						?>


						<div class="category">
							<?php
							foreach ( $tags as $tag ) {
								   $cats     = get_the_category_by_ID( $tag );
								   $cat_link = get_category_link( $tag );
								if ( strtolower( $cats ) == 'video' ) {
									$video_post = true;
								}
								?>
							<a href="<?php echo esc_url( $cat_link ); ?>"><?php echo $cats; ?></a>
								<?php
							}
							?>
						</div>
						<div class="image">
							<a href="<?php echo esc_url( get_permalink() ); ?>">
								<?php
								if ( $external_image == 'yes' ) {
									$image_url = ( $item['post_image']['id'] != '' ) ? wp_get_attachment_image_url( $item['post_image']['id'], 'full' ) : $item['post_image']['url'];
									?>
								<img src="<?php echo esc_url( $image_url ); ?>" alt="Awesome Image">
									<?php
								} elseif ( isset( $image ) && $image != '' ) {
									 $ad_im = wp_get_attachment_image_url( $image, 'full' );
									?>
								<img src="<?php echo esc_url( $ad_im ); ?>" alt="Awesome Image">
									<?php
								} else {
									  the_post_thumbnail();
								}
								?>
							</a>
							<div class="post-meta-info">
								<a href="#"><span
										class="flaticon-eye"></span><?php echo get_post_meta( get_the_ID(), 'post_views_count', true ); ?></a>
								<a href="#"><span
										class="flaticon-comment"></span><?php echo get_comments_number( get_the_ID() ); ?></a>
							</div>
							<?php if ( $video_post == true ) { ?>
							<div class="youtube-video-box"><a href="<?php echo esc_url( get_permalink() ); ?>"><span
										class="flaticon-logo"></span></a></div>
								<?php
								$video_post = false;
							}
							?>
						</div>
						<div class="lower-content">
							<div class="date"><?php echo get_the_date( 'M d, Y' ); ?></div>
							<h4><a href="<?php echo esc_url( get_permalink() ); ?>">
									<?php
									if ( $show_external_name == 'yes' ) {
										echo $item['external_name'];
									} else {
											the_title();
									}
									?>
								</a>
							</h4>
							<div class="author-info">
								<div class="image"><?php echo wp_kses_post( get_avatar( get_the_ID(), 30 ) ); ?></div>
								<div class="author-title"><a
										href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo get_the_author(); ?></a>
								</div>
								<?php
								do_action( 'goodsoul_share_button_page' );
								?>
							</div>
						</div>
					</div>
				</div>
						<?php
					}
							wp_reset_postdata();
				}
			}
			?>
			</div>
		</div>
</section>
		<?php } ?>
		<?php if ( $select_layout == 'style_2' ) { ?>

<!-- Blog Section -->
<section class="blog-section">
	<div class="auto-container">
		<div class="sec-title text-center">
			<h1><?php echo wp_kses_post( $tagline ); ?></h1>
			<div class="text"><?php echo wp_kses_post( $title ); ?></div>
		</div>
		<div class="row">
			<?php
			foreach ( $settings['items1'] as $item ) {

				$external_image     = $item['external_image'];
				$show_external_name = $item['show_external_name'];

				$args       = array(
					'p'         => $item['post_name'],
					'post_type' => 'post',
				);
				$query      = new WP_Query( $args );
				$video_post = false;
				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						$tags = wp_get_post_categories( get_the_ID() );
						?>
			<!-- News Block Two -->
			<div class="<?php echo esc_attr( $columnClass ); ?> news-block-two">
				<div class="inner-box wow fadeInUp" data-wow-delay="200ms">
					<div class="image">
						<div class="category">
							<?php
							foreach ( $tags as $tag ) {
								  $cats     = get_the_category_by_ID( $tag );
								  $cat_link = get_category_link( $tag );
								if ( strtolower( $cats ) == 'video' ) {
									$video_post = true;
								}
								?>
							<a href="<?php echo esc_url( $cat_link ); ?>"><?php echo $cats; ?></a>
								<?php
							}
							?>
						</div>
						<a href="<?php echo esc_url( get_permalink() ); ?>"><a
								href="<?php echo esc_url( get_permalink() ); ?>">
								<?php
								if ( $external_image == 'yes' ) {
									$image_url = ( $item['post_image']['id'] != '' ) ? wp_get_attachment_image_url( $item['post_image']['id'], 'full' ) : $item['post_image']['url'];
									?>
								<img src="<?php echo esc_url( $image_url ); ?>" alt="Awesome Image">
									<?php
								} elseif ( isset( $image ) && $image != '' ) {
									 $ad_im = wp_get_attachment_image_url( $image, 'full' );
									?>
								<img src="<?php echo esc_url( $ad_im ); ?>" alt="Awesome Image">
									<?php
								} else {
									  the_post_thumbnail();
								}
								?>
							</a>
							<div class="post-meta-info">
								<a href="#"><span
										class="flaticon-eye"></span><?php echo get_post_meta( get_the_ID(), 'post_views_count', true ); ?></a>
								<a href="#"><span
										class="flaticon-comment"></span><?php echo get_comments_number( get_the_ID() ); ?></a>
							</div>
							<?php if ( $video_post == true ) { ?>
							<div class="youtube-video-box"><a href="<?php echo esc_url( get_permalink() ); ?>"><span
										class="flaticon-logo"></span></a></div>
								<?php
									$video_post = false;
							}
							?>
					</div>
					<div class="lower-content">
						<div class="date"><span class="flaticon-clock"></span><?php echo get_the_date( 'M d, Y' ); ?>
						</div>
						<h4><a href="<?php echo esc_url( get_permalink() ); ?>">
								<?php
								if ( $show_external_name == 'yes' ) {
									echo $item['external_name'];
								} else {
										the_title();
								}
								?>
							</a></h4>
						<div class="author-info">
							<div class="image"><?php echo wp_kses_post( get_avatar( get_the_ID(), 30 ) ); ?></div>
							<div class="author-title"><a
									href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo get_the_author(); ?></a>
							</div>
							<?php
							do_action( 'goodsoul_share_button_page' );
							?>
						</div>
					</div>
				</div>
			</div>
						<?php
					}
					wp_reset_postdata();
				}
			}
			?>
		</div>
	</div>
</section>
		<?php } ?>
		<?php if ( $select_layout == 'style_3' ) { ?>

<!-- Blog Section -->
<section class="blog-section">
	<div class="auto-container">
		<div class="sec-title text-center">
			<h5><?php echo wp_kses_post( $tagline ); ?></h5>
			<h1><?php echo wp_kses_post( $title ); ?></h1>
		</div>
		<div class="row">
			<?php
			foreach ( $settings['items1'] as $item ) {

				$external_image       = $item['external_image'];
				$show_external_name   = $item['show_external_name'];
				$animation_class      = $item['animation_class'];
				$animation_delay_time = $item['addon_animation_delay_time'];

				$args  = array(
					'p'         => $item['post_name'],
					'post_type' => 'post',
				);
				$query = new WP_Query( $args );
				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						$tags  = wp_get_post_categories( get_the_ID() );
						$image = get_post_meta( get_the_ID(), 'goodsoul_metabox_content_image', true );

						?>
			<!-- News Block Two -->
			<div class="<?php echo esc_attr( $columnClass ); ?> news-block-two style-two">
				<div class="inner-box wow <?php echo esc_attr( $animation_class ); ?>" data-wow-delay="<?php echo esc_attr( $animation_delay_time ); ?>">
					<div class="image">
						<div class="category">
							<?php
							foreach ( $tags as $tag ) {
									  $cats     = get_the_category_by_ID( $tag );
									  $cat_link = get_category_link( $tag );
								?>
							<a href="<?php echo esc_url( $cat_link ); ?>"><?php echo $cats; ?></a>
								<?php
							}
							?>
						</div>
						<a href="<?php echo esc_url( get_permalink() ); ?>">
							<?php
							if ( $external_image == 'yes' ) {
								$image_url = ( $item['post_image']['id'] != '' ) ? wp_get_attachment_image_url( $item['post_image']['id'], 'full' ) : $item['post_image']['url'];
								?>
							<img src="<?php echo esc_url( $image_url ); ?>" alt="Awesome Image">
								<?php
							} elseif ( isset( $image ) && $image != '' ) {
								 $ad_im = wp_get_attachment_image_url( $image, 'full' );
								?>
							<img src="<?php echo esc_url( $ad_im ); ?>" alt="Awesome Image">
								<?php
							} else {
								  the_post_thumbnail();
							}
							?>
						</a>
						<div class="post-meta-info">
							<a href="#"><span
									class="flaticon-eye"></span><?php echo get_post_meta( get_the_ID(), 'post_views_count', true ); ?></a>
							<a href="#"><span
									class="flaticon-comment"></span><?php echo get_comments_number( get_the_ID() ); ?></a>
						</div>
					</div>
					<div class="lower-content">
						<div class="date"><span class="flaticon-clock"></span><?php echo get_the_date( 'M d, Y' ); ?>
						</div>
						<h4><a href="<?php echo esc_url( get_permalink() ); ?>">
								<?php
								if ( $show_external_name == 'yes' ) {
									echo $item['external_name'];
								} else {
									the_title();
								}
								?>
							</a></h4>
						<div class="author-info">
							<div class="image"><?php echo wp_kses_post( get_avatar( get_the_ID(), 30 ) ); ?></div>
							<div class="author-title"><a
									href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo get_the_author(); ?></a>
							</div>
							<?php
								do_action( 'goodsoul_share_button_page' );
							?>
						</div>
					</div>
				</div>
			</div>
						<?php
					}
					wp_reset_postdata();
				}
			}
			?>
		</div>
	</div>
</section>
		<?php } ?>
		<?php if ( $select_layout == 'style_4' ) { ?>

<!-- Blog Section -->
<section class="blog-section style-three">
	<div class="auto-container">
		<div class="row m-0 justify-content-md-between align-items-end">
			<div class="sec-title style-three">
				<h5><?php echo wp_kses_post( $tagline ); ?></h5>
				<h1><?php echo wp_kses_post( $title ); ?></h1>
			</div>
			<!--Link Btn-->
			<?php if ( ! empty( $button_link ) ) { ?>
			<div class="link-btn mb-50">
				<a href="<?php echo esc_url( $button_link ); ?>"
					class="theme-btn btn-style-one"><span><?php echo wp_kses_post( $button_text ); ?></span></a>
			</div>
			<?php } ?>
		</div>
		<div class="row">
			<?php
			foreach ( $settings['items1'] as $item ) {

				$external_image       = $item['external_image'];
				$show_external_name   = $item['show_external_name'];
				$animation_class      = $item['animation_class'];
				$animation_delay_time = $item['addon_animation_delay_time'];

				$args  = array(
					'p'         => $item['post_name'],
					'post_type' => 'post',
				);
				$query = new WP_Query( $args );
				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						$tags = wp_get_post_categories( get_the_ID() );
						?>
			<!-- News Block One -->
			<div class="<?php echo esc_attr( $columnClass ); ?> col-md-6 news-block-one">
				<div class="inner-box wow <?php echo $animation_class; ?>" data-wow-delay="<?php echo $animation_delay_time; ?>">
					<div class="category">
						<?php
						foreach ( $tags as $tag ) {
							   $cats     = get_the_category_by_ID( $tag );
							   $cat_link = get_category_link( $tag );
							?>
						<a href="<?php echo esc_url( $cat_link ); ?>"><?php echo $cats; ?></a>
							<?php
						}
						?>
					</div>

					<div class="image">
						<?php
						if ( $external_image == 'yes' ) {
							$image_url = ( $item['post_image']['id'] != '' ) ? wp_get_attachment_image_url( $item['post_image']['id'], 'full' ) : $item['post_image']['url'];
							?>
						<img src="<?php echo esc_url( $image_url ); ?>" alt="Awesome Image">
							<?php
						} elseif ( isset( $image ) && $image != '' ) {
							 $ad_im = wp_get_attachment_image_url( $image, 'full' );
							?>
						<img src="<?php echo esc_url( $ad_im ); ?>" alt="Awesome Image">
							<?php
						} else {
							  the_post_thumbnail();
						}
						?>
						<a class="read-more-link" href="<?php echo esc_url( get_permalink() ); ?>"><span
								class="flaticon-more"></span></a>
						<div class="post-meta-info">
							<a href="#"><span
									class="flaticon-eye"></span><?php echo get_post_meta( get_the_ID(), 'post_views_count', true ); ?></a>
							<a href="#"><span class="flaticon-comment"></span><?php goodsoul_comments_count(); ?></a>
						</div>
					</div>
					<div class="lower-content">
						<div class="date"><span class="flaticon-clock"></span><?php echo get_the_date( 'M d, Y' ); ?>
						</div>
						<h4><a href="<?php echo esc_url( get_permalink() ); ?>">
								<?php
								if ( $show_external_name == 'yes' ) {
									echo $item['external_name'];
								} else {
									the_title();
								}
								?>
							</a></h4>
						<div class="author-info">
							<div class="image"><?php echo wp_kses_post( get_avatar( get_the_ID(), 30 ) ); ?></div>
							<div class="author-title"><a
									href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo get_the_author(); ?></a>
							</div>
							<?php
							do_action( 'goodsoul_share_button_page' );
							?>
						</div>
					</div>
				</div>
			</div>
						<?php
					}
						wp_reset_postdata();
				}
			}
			?>
		</div>
	</div>
</section>
		<?php } ?>
		<?php if ( $select_layout == 'style_5' ) { ?>
<!-- Blog Section -->
<section class="blog-section style-two">
	<div class="auto-container">
		<div class="sec-title text-center style-two">
			<div class="icon-box"><span class="<?php echo $icon['value']; ?>"></span></div>
			<h5><?php echo wp_kses_post( $tagline ); ?></h5>
			<h1><?php echo wp_kses_post( $title ); ?></h1>
		</div>
		<div class="row">
			<?php
			foreach ( $settings['items1'] as $item ) {

				$external_image     = $item['external_image'];
				$show_external_name = $item['show_external_name'];

				$args       = array(
					'p'         => $item['post_name'],
					'post_type' => 'post',
				);
				$query      = new WP_Query( $args );
				$video_post = false;
				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						$tags  = wp_get_post_categories( get_the_ID() );
						$image = get_post_meta( get_the_ID(), 'goodsoul_metabox_content_image', true );

						?>
			<!-- News Block One -->
			<div class="<?php echo esc_attr( $columnClass ); ?> col-md-6 news-block-one">
				<div class="inner-box wow fadeInUp" data-wow-delay="200ms">
					<div class="category">
						<?php
						foreach ( $tags as $tag ) {
							   $cats     = get_the_category_by_ID( $tag );
							   $cat_link = get_category_link( $tag );
							if ( strtolower( $cats ) == 'video' ) {
								$video_post = true;
							}
							?>
						<a href="<?php echo esc_url( $cat_link ); ?>"><?php echo $cats; ?></a>
							<?php
						}
						?>
					</div>
					<div class="image">
						<a href="<?php echo esc_url( get_permalink() ); ?>">
							<?php
							if ( $external_image == 'yes' ) {
								$image_url = ( $item['post_image']['id'] != '' ) ? wp_get_attachment_image_url( $item['post_image']['id'], 'full' ) : $item['post_image']['url'];
								?>
							<img src="<?php echo esc_url( $image_url ); ?>" alt="Awesome Image">
								<?php
							} elseif ( isset( $image ) && $image != '' ) {
								 $ad_im = wp_get_attachment_image_url( $image, 'full' );
								?>
							<img src="<?php echo esc_url( $ad_im ); ?>" alt="Awesome Image">
								<?php
							} else {
								  the_post_thumbnail();
							}
							?>
						</a>
						<div class="post-meta-info">
							<a href="#"><span
									class="flaticon-eye"></span><?php echo get_post_meta( get_the_ID(), 'post_views_count', true ); ?></a>
							<a href="#"><span
									class="flaticon-comment"></span><?php echo get_comments_number( get_the_ID() ); ?></a>
						</div>
						<?php if ( $video_post == true ) { ?>
						<div class="youtube-video-box"><a href="<?php echo esc_url( get_permalink() ); ?>"><span
									class="flaticon-logo"></span></a></div>
							<?php
								$video_post = false;
						}
						?>
					</div>
					<div class="lower-content">
						<div class="date"><?php echo get_the_date( 'M d, Y' ); ?></div>
						<h4><a href="<?php echo esc_url( get_permalink() ); ?>">
								<?php
								if ( $show_external_name == 'yes' ) {
									echo $item['external_name'];
								} else {
									the_title();
								}
								?>
							</a></h4>
						<div class="author-info">
							<div class="image"><?php echo wp_kses_post( get_avatar( get_the_ID(), 30 ) ); ?></div>
							<div class="author-title"><a
									href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo get_the_author(); ?></a>
							</div>
							<?php
							do_action( 'goodsoul_share_button_page' );
							?>
						</div>
					</div>
				</div>
			</div>
						<?php
					}
						wp_reset_postdata();
				}
			}
			?>
		</div>
	</div>
</section>
		<?php } ?>
		<?php if ( $select_layout == 'style_6' ) { ?>
<section class="blog-section">
	<div class="auto-container">
		<div class="sec-title text-center">
			<h1><?php echo wp_kses_post( $tagline ); ?></h1>
			<div class="text"><?php echo wp_kses_post( $title ); ?></div>
		</div>
		<div class="row">
			<?php
			foreach ( $settings['items1'] as $item ) {

				$external_image     = $item['external_image'];
				$show_external_name = $item['show_external_name'];

				$args  = array(
					'p'         => $item['post_name'],
					'post_type' => 'post',
				);
				$query = new WP_Query( $args );
				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						$tags  = wp_get_post_categories( get_the_ID() );
						$image = get_post_meta( get_the_ID(), 'goodsoul_metabox_content_image', true );
						?>
			<div class="<?php echo esc_attr( $columnClass ); ?> news-block-two">
				<div class="inner-box wow fadeInUp" data-wow-delay="200ms">
					<div class="image">
						<div class="category">
							<?php
							foreach ( $tags as $tag ) {
									  $cats     = get_the_category_by_ID( $tag );
									  $cat_link = get_category_link( $tag );
								if ( strtolower( $cats ) == 'video' ) {
									$video_post = true;
								}
								?>
							<a href="<?php echo esc_url( $cat_link ); ?>"><?php echo $cats; ?></a>
								<?php
							}
							?>
						</div>
						<a href="<?php the_permalink(); ?>">
							<?php
							if ( $external_image == 'yes' ) {
														$image_url = ( $item['post_image']['id'] != '' ) ? wp_get_attachment_image_url( $item['post_image']['id'], 'full' ) : $item['post_image']['url'];
								?>
							<img src="<?php echo esc_url( $image_url ); ?>" alt="Awesome Image">
								<?php
							} elseif ( isset( $image ) && $image != '' ) {
											 $ad_im = wp_get_attachment_image_url( $image, 'full' );
								?>
							<img src="<?php echo esc_url( $ad_im ); ?>" alt="Awesome Image">
								<?php
							} else {
										  the_post_thumbnail();
							}
							?>
						</a>
						<div class="post-meta-info">
							<a href="<?php the_permalink(); ?>"><span
									class="flaticon-eye"></span><?php echo get_post_meta( get_the_ID(), 'post_views_count', true ); ?></a>
							<a href="<?php the_permalink(); ?>"><span
									class="flaticon-comment"></span><?php goodsoul_comments_count(); ?></a>
						</div>
					</div>
					<div class="lower-content">
						<div class="date"><span class="flaticon-clock"></span><?php echo get_the_date( 'M d, Y' ); ?>
						</div>
						<h4><a href="<?php the_permalink(); ?>">
								<?php
								if ( $show_external_name == 'yes' ) {
														echo $item['external_name'];
								} else {
													the_title();
								}
								?>
							</a></h4>
						<div class="author-info">
							<div class="image"><?php goodsoul_author_image(); ?></div>
							<div class="author-title"><a
									href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo get_the_author(); ?></a>
							</div>
							<div class="share-are-me">
								<?php
								do_action( 'goodsoul_share_button' );
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
						<?php
					} wp_reset_postdata();
				}
			}
			?>
		</div>
	</div>
</section>

		<?php } ?>
		<?php
	}

	protected function content_template() {

	}
}

			Plugin::instance()->widgets_manager->register( new Goodsoul_Blogs_Selector() );
