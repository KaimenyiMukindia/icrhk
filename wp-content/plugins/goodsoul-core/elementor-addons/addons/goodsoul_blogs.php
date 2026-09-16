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

class Goodsoul_Blogs extends Widget_Base {



	public function get_name() {
		return 'goodsoul_blogs';
	}

	public function get_title() {
		return esc_html__( 'goodsoul Blog', 'goodsoul-core' );
	}

	public function get_icon() {
		return 'fa fa-object-ungroup';
	}

	public function get_categories() {
		return array( 'goodsoulcore' );
	}

	private function get_blog_categories() {
		$options  = array();
		$taxonomy = 'category';
		if ( ! empty( $taxonomy ) ) {
			$terms = get_terms(
				array(
					'parent'     => 0,
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
				)
			);
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( isset( $term ) ) {
						$options[''] = 'Select';
						if ( isset( $term->slug ) && isset( $term->name ) ) {
							$options[ $term->slug ] = $term->name;
						}
					}
				}
			}
		}
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
					'style_7' => esc_html__( 'Style 7', 'goodsoul-core' ),

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
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Team behind goodsoul', 'goodsoul-core' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXTAREA,
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
			'category_id',
			array(
				'type'    => \Elementor\Controls_Manager::SELECT,
				'label'   => esc_html__( 'Category', 'goodsoul-core' ),
				'options' => $this->get_blog_categories(),
			)
		);

		$this->add_control(
			'number',
			array(
				'label'   => esc_html__( 'Number of Post', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 2,
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

		$this->add_control(
			'order_by',
			array(
				'label'   => esc_html__( 'Order By', 'goodsoul-core' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'          => esc_html__( 'Date', 'goodsoul-core' ),
					'ID'            => esc_html__( 'ID', 'goodsoul-core' ),
					'author'        => esc_html__( 'Author', 'goodsoul-core' ),
					'title'         => esc_html__( 'Title', 'goodsoul-core' ),
					'modified'      => esc_html__( 'Modified', 'goodsoul-core' ),
					'rand'          => esc_html__( 'Random', 'goodsoul-core' ),
					'comment_count' => esc_html__( 'Comment count', 'goodsoul-core' ),
					'menu_order'    => esc_html__( 'Menu order', 'goodsoul-core' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order', 'goodsoul-core' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => array(
					'desc' => esc_html__( 'DESC', 'goodsoul-core' ),
					'asc'  => esc_html__( 'ASC', 'goodsoul-core' ),
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
		$button_link   = $settings['button_link'];

		$button_link                          = $settings['button_link'];
						$button_link_target   = $settings['button_link']['is_external'] ? ' target="_blank"' : '';
						$button_link_nofollow = $settings['button_link']['nofollow'] ? ' rel="nofollow"' : '';

		$button_text    = $settings['button_text'];
		$posts_per_page = $settings['number'];
		$order_by       = $settings['order_by'];
		$order          = $settings['order'];
		$icon           = $settings['icon'];
		$pg_num         = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$args           = array(
			'post_type'      => array( 'post' ),
			'post_status'    => array( 'publish' ),
			'nopaging'       => false,
			'paged'          => $pg_num,
			'posts_per_page' => $posts_per_page,
			'category_name'  => $settings['category_id'],
			'orderby'        => $order_by,
			'order'          => $order,
		);
		$query          = new WP_Query( $args );

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


		<?php if ( $select_layout == 'style_1' || $select_layout == 'style_7' ) { ?>
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
				<?php if ( $select_layout == 'style_1' ) { ?>
				<a href="<?php echo esc_url( $button_link['url'] ); ?>" <?php echo $button_link_target . ' ' . $button_link_nofollow; ?>
					class="theme-btn btn-style-one"><span><?php echo wp_kses_post( $button_text ); ?></span></a>
			<?php } elseif ( $select_layout == 'style_7' ) { ?>
				<a href="<?php echo esc_url( $button_link['url'] ); ?>" <?php echo $button_link_target . ' ' . $button_link_nofollow; ?>
					class="theme-btn btn-style-seventeen"><span><?php echo wp_kses_post( $button_text ); ?></span></a> 
			<?php } ?>
			</div>
			<?php } ?>
		</div>
		<div class="row">
			<?php
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
					if ( isset( $image ) && $image != '' ) {
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
							<h4><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h4>
							<div class="author-info">
								<div class="image"><?php echo get_avatar( get_the_author_meta( 'ID' ), 30 ); ?></div>
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
					if ( isset( $image ) && $image != '' ) {
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
						<h4><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h4>
						<div class="author-info">
							<div class="image"><?php echo get_avatar( get_the_author_meta( 'ID' ), 30 ); ?></div>
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
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$tags  = wp_get_post_categories( get_the_ID() );
					$image = get_post_meta( get_the_ID(), 'goodsoul_metabox_content_image', true );

					?>
			<!-- News Block Two -->
			<div class="<?php echo esc_attr( $columnClass ); ?> news-block-two style-two">
				<div class="inner-box wow fadeInUp" data-wow-delay="200ms">
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
					if ( isset( $image ) && $image != '' ) {
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
						<h4><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h4>
						<div class="author-info">
							<div class="image"><?php echo get_avatar( get_the_author_meta( 'ID' ), 30 ); ?></div>
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
			<a href="<?php echo esc_url( $button_link['url'] ); ?>" <?php echo $button_link_target . ' ' . $button_link_nofollow; ?>
					class="theme-btn btn-style-one"><span><?php echo wp_kses_post( $button_text ); ?></span></a>
			</div>
			<?php } ?>
		</div>
		<div class="row">
			<?php
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$tags = wp_get_post_categories( get_the_ID() );
					?>
			<!-- News Block One -->
			<div class="<?php echo esc_attr( $columnClass ); ?> col-md-6 news-block-one">
				<div class="inner-box wow fadeInUp" data-wow-delay="200ms">
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
					if ( has_post_thumbnail() ) {
						   echo get_the_post_thumbnail( get_the_ID(), '' );
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
						<h4><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h4>
						<div class="author-info">
							<div class="image"><?php echo get_avatar( get_the_author_meta( 'ID' ), 30 ); ?></div>
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
					if ( isset( $image ) && $image != '' ) {
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
						<h4><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h4>
						<div class="author-info">
							<div class="image"><?php echo get_avatar( get_the_author_meta( 'ID' ), 30 ); ?></div>
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
					if ( isset( $image ) && $image != '' ) {
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
						<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
						<div class="author-info">
							<div class="image"><?php echo get_avatar( get_the_author_meta( 'ID' ), 30 ); ?></div>
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

			Plugin::instance()->widgets_manager->register( new Goodsoul_Blogs() );
