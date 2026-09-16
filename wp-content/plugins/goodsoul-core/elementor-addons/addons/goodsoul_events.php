<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Widget_Base;

class GoodSoul_Events extends Widget_Base {

	public function get_name() {
		return 'goodsoul_events';
	}

	public function get_title() {
		return esc_html__( 'Events', 'goodsoul-core' );
	}

	public function get_icon() {
		return 'fa fa-object-ungroup';
	}

	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	public function get_script_depends() {
		return array( 'counter' );
	}

	private function get_category_list() {
		$options  = array();
		$taxonomy = 'tribe_events_cat';
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
			'event_design_area',
			array(
				'label' => esc_html__( 'event design area', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'event_style',
			array(
				'label'   => esc_html__( 'event SHow Style', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'style_1',
				'options' => array(
					'style_1' => esc_html__( 'One', 'goodsoul-core' ),
					'style_2' => esc_html__( 'Two', 'goodsoul-core' ),
					'style_3' => esc_html__( 'Three', 'goodsoul-core' ),
					'style_4' => esc_html__( 'Four', 'goodsoul-core' ),
					'style_5' => esc_html__( 'Five', 'goodsoul-core' ),
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_program',
			array(
				'label' => esc_html__( 'Content', 'goodsoul-core' ),
			)
		);
		$this->add_control(
			'tagtitle',
			array(
				'label'     => esc_html__( 'Tagtitle', 'goodsoul-core' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Our Events',
				'condition' => array( 'event_style' => 'style_3' ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Our exciting events',
			)
		);
		$this->add_control(
			'content',
			array(
				'label'     => esc_html__( 'Text', 'goodsoul-core' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => 'Here are our exciting events...would be great to see you at the next one!',
				'condition' => array(
					'event_style' => array( 'style_1', 'style_2', 'style_5' ),
				),
			)
		);
		$this->add_control(
			'join_text',
			array(
				'label'   => esc_html__( 'Join Button Name', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Join Now',
			)
		);
		$this->add_control(
			'ongoing_text',
			array(
				'label'     => esc_html__( 'Ongoing Text', 'goodsoul-core' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Ongoing',
				'condition' => array( 'event_style' => 'style_1' ),
			)
		);
		$this->add_control(
			'upcoming_text',
			array(
				'label'     => esc_html__( 'Upcoming Text', 'goodsoul-core' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Upcoming',
				'condition' => array( 'event_style' => 'style_1' ),
			)
		);
		$this->add_control(
			'button_text',
			array(
				'label'     => esc_html__( 'Viwe All Button Name', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => __( 'View All Events', 'goodsoul-core' ),
				'condition' => array( 'event_style' => 'style_3' ),
			)
		);
		$this->add_control(
			'donate_text',
			array(
				'label'     => esc_html__( 'Donate Button Text', 'goodsoul-core' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Donate for event',
				'condition' => array( 'event_style' => 'style_5' ),
			)
		);
		$this->add_control(
			'button_link',
			array(
				'label'         => esc_html__( 'Event Page link', 'goodsoul-core' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
				'condition'     => array( 'event_style' => 'style_3' ),
			)
		);
		$this->add_control(
			'category_slug',
			array(
				'type'    => \Elementor\Controls_Manager::SELECT,
				'label'   => esc_html__( 'Category', 'goodsoul-core' ),
				'options' => $this->get_category_list(),
			)
		);

		$this->add_control(
			'number',
			array(
				'label'   => esc_html__( 'Number of Post', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 3,
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
		$settings       = $this->get_settings();
		$title          = $settings['title'];
		$join_text      = $settings['join_text'];
		$tagtitle       = $settings['tagtitle'];
		$button_text    = $settings['button_text'];
		$donate_text    = $settings['donate_text'];
		$category       = $settings['category_slug'];
		$ongoing_text   = $settings['ongoing_text'];
		$upcoming_text  = $settings['upcoming_text'];
		$button_link    = $settings['button_link']['url'];
		$content        = $settings['content'];
		$event_style    = $settings['event_style'];
		$number_of_post = (int) $settings['number'];
		global $post;
		/*
		$get_posts = tribe_get_events(
			array(
				'posts_per_page' => $number_of_post,
				'tax_query'      => array(
					array(
						'taxonomy' => 'tribe_events_cat',
						'field'    => 'slug',
						'terms'    => $category,
					),
				),
			)
		);
		*/
		$args = array(
			'post_status'    => 'publish',
			'post_type'      => array( Tribe__Events__Main::POSTTYPE ),
			'posts_per_page' => $number_of_post,
			// order by startdate from newest to oldest
			'meta_key'       => '_EventStartDate',
			// required in 3.x
			'eventDisplay'   => 'custom',
			'tax_query'      => array(
				array(
					'taxonomy' => 'tribe_events_cat',
					'field'    => 'slug',
					'terms'    => $category,
				),
			),
		);

		$get_posts = new WP_Query( $args );

		$time_format = get_option( 'time_format', Tribe__Date_Utils::TIMEFORMAT );
		$start_time  = tribe_get_start_date( $post->ID, false, $time_format );
		$end_time    = tribe_get_end_date( $post->ID, false, $time_format );

		?>


		<?php if ( $event_style == 'style_1' ) : ?>
		 <!--Events Section-->
		 <section class="events-section">
				<!--Event Tabs-->
				<div class="event-tabs">
					<div class="auto-container">
						<div class="row m-0 justify-content-md-between align-items-end">
							<div class="sec-title">
								<h1><?php echo wp_kses_post( $title ); ?></h1>
								<div class="text"><?php echo wp_kses_post( $content ); ?></div>
							</div>
							<!--Tabs Header-->
							<div class="tabs-header clearfix">
								<ul class="event-tab-btns clearfix">
									<li class="event-tab-btn active-btn" data-tab="#event-tab-1"><?php echo $ongoing_text; ?></li>
									<li class="event-tab-btn" data-tab="#event-tab-2"><?php echo $upcoming_text; ?></li>
								</ul>
							</div>
						</div> 
						<div class="event-tab-wrapper">
							<!--Tabs Content-->  
							<div class="event-tabs-content">
								<!--Event Tab / Active Tab-->
								<div class="event-tab active-tab" id="event-tab-1">
									<div class="event-carousel owl-theme owl-carousel owl-dot-style-one owl-nav-none">
									<?php
									while ( $get_posts->have_posts() ) {
										$get_posts->the_post();
										$event_advertise_image = get_post_meta( get_the_ID(), 'goodsoul_metabox_event_advertise_image', true );

										?>
										<!-- Event Blokc One -->
										<div class="event-block-one">
											<div class="inner-box">
											<?php
											if ( has_post_thumbnail() ) {
												?>
												<div class="image"> 
												<?php
												if ( isset( $event_advertise_image ) && $event_advertise_image != '' ) {
													$ad_im = wp_get_attachment_image_url( $event_advertise_image, 'full' );
													?>
														<img src="<?php echo esc_url( $ad_im ); ?>" alt="Awesome Image">
														<?php
												} else {
													the_post_thumbnail();
												}
												?>
												</div>
												<?php } ?>
												<div class="lower-content">
													<div class="date">
														<h1><?php echo tribe_get_start_date( $post->ID, false, 'j' ); ?></h1>
														<div class="text"><span><?php echo tribe_get_start_date( $post->ID, false, 'F' ); ?></span> <br><?php echo tribe_get_start_date( $post->ID, false, 'G:i' ); ?>  - <?php echo tribe_get_end_date( $post->ID, false, 'G:i ' ); ?></div>
													</div>
													<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
													<div class="location"><span class="flaticon-point"></span><?php echo tribe_get_address(); ?></div>
												</div>
												<div class="link-btn"><a href="<?php the_permalink(); ?>"><span class="flaticon-next"></span><?php echo $join_text; ?></a></div>
											</div>
										</div>
											<?php

									}
									?>
									</div>
								</div>

								<!--Event Tab-->
								<div class="event-tab" id="event-tab-2">
									<div class="event-carousel owl-theme owl-carousel owl-dot-style-one owl-nav-none">
									
										<!-- Event Blokc One -->
										<?php
										while ( $get_posts->have_posts() ) {
											$get_posts->the_post();
											$event_advertise_image = get_post_meta( get_the_ID(), 'goodsoul_metabox_event_advertise_image', true );

											?>
										<div class="event-block-one">
											<div class="inner-box">
												<?php
												if ( has_post_thumbnail() ) {
													?>
												<div class="image"> 
													<?php
													if ( isset( $event_advertise_image ) && $event_advertise_image != '' ) {
														$ad_im = wp_get_attachment_image_url( $event_advertise_image, 'full' );
														?>
																			<img src="<?php echo esc_url( $ad_im ); ?>" alt="Awesome Image">
														<?php
													} else {
														the_post_thumbnail();
													}
													?>
											</div>
													<?php } ?>
												<div class="lower-content">
													<div class="date">
														<h1><?php echo tribe_get_start_date( $post->ID, false, 'j' ); ?></h1>
														<div class="text"><span><?php echo tribe_get_start_date( $post->ID, false, 'F' ); ?></span> <br><?php echo tribe_get_start_date( $post->ID, false, 'G:i' ); ?>  - <?php echo tribe_get_end_date( $post->ID, false, 'G:i' ); ?></div>
													</div>
													<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
													<div class="location"><span class="flaticon-point"></span><?php echo tribe_get_address(); ?></div>
												</div>
												<div class="link-btn"><a href="<?php the_permalink(); ?>"><span class="flaticon-next"></span><?php echo $join_text; ?></a></div>
											</div>
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
		<?php endif; ?>

		<?php
		if ( $event_style == 'style_2' ) :
			$first_block = true;
			?>
	<!-- Events Section Two -->
	<section class="events-section-two">
		<div class="auto-container">
			<div class="sec-title text-center">
				<h1><?php echo wp_kses_post( $title ); ?></h1>
				<div class="text"><?php echo wp_kses_post( $content ); ?></div>
			</div>
			<div class="row">
			<?php
			while ( $get_posts->have_posts() ) {
				$get_posts->the_post();
				$event_advertise_image = get_post_meta( get_the_ID(), 'goodsoul_metabox_event_advertise_image', true );

				?>
				<?php if ( $first_block == true ) { ?>
				<div class="col-lg-6">
					<div class="event-block-two">
						<div class="inner-box">
						<?php
						if ( has_post_thumbnail() ) {
							?>
							<div class="image"><a href="<?php the_permalink(); ?>">
							<?php
							if ( isset( $event_advertise_image ) && $event_advertise_image != '' ) {
								$ad_im = wp_get_attachment_image_url( $event_advertise_image, 'full' );
								?>
												<img src="<?php echo esc_url( $ad_im ); ?>" alt="Awesome Image">
								<?php
							} else {
								the_post_thumbnail();
							}
							?>
						</a>
					</div>
							<?php } ?>
							<div class="lower-content">
								<div class="countdown-timer">
									<div class="default-coundown">
										<div class="box">
											<div class="countdown time-countdown-two" data-countdown-time="<?php echo tribe_get_start_date( $post, false, 'Y/m/d' ); ?>"></div>
										</div>
									</div>
								</div>
								<div class="event-info">
									<div class="time"><span class="flaticon-clock"></span><?php echo tribe_get_start_date( $post, false, 'd M Y , G:i' ); ?></div>
									<div class="location"><span class="flaticon-point"></span><?php echo tribe_get_address(); ?></div>
								</div>
								<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<?php
					 $first_block = false;} else {
					?>
					<div class="event-block-three">
						<div class="inner-box">
						<?php
						if ( has_post_thumbnail() ) {
							?>
							<div class="image">
								<a href="<?php the_permalink(); ?>">
								<?php
								if ( isset( $event_advertise_image ) && $event_advertise_image != '' ) {
									$ad_im = wp_get_attachment_image_url( $event_advertise_image, 'full' );
									?>
												<img src="<?php echo esc_url( $ad_im ); ?>" alt="Awesome Image">
									<?php
								} else {
									the_post_thumbnail( 'event-sidebar-img' );
								}
								?>
								</a>
							</div>
							<?php } ?>
							<div class="content">
								<div class="toggle-btn"><span class="fa fa-plus"></span></div>
								<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
								<div class="time"><span class="flaticon-clock"></span>
									<?php echo tribe_get_start_date( $post->ID, false, 'd M Y , g:ia' ); ?>
									</div>
								<div class="text"><?php the_excerpt(); ?></div>
							</div>
							<a href="<?php the_permalink(); ?>" class="join-btn"><?php echo $join_text; ?></a>
						</div>
					</div>
					<?php
					 }
			}
			?>
				</div>
			</div>
		</div>
	</section>
			<?php endif; ?>
			<?php
			if ( $event_style == 'style_3' ) :
				?>
	
	<!-- Events Section Five -->
	<section class="events-section style-three">
		<div class="auto-container">
			<div class="row m-0 justify-content-md-between align-items-end">
				<div class="sec-title style-three">
					<h5><?php echo wp_kses_post( $tagtitle ); ?></h5>
					<h1><?php echo wp_kses_post( $title ); ?></h1>
				</div>
				<!--Link Btn-->
				<?php if ( ! empty( $button_link ) ) { ?>
				<div class="link-btn mb-50">
					<a href="<?php echo esc_url( $button_link ); ?>" class="theme-btn btn-style-one"><span><?php echo wp_kses_post( $button_text ); ?></span></a>
				</div>
					<?php } ?>
			</div>
			<div class="row">
				<?php
				while ( $get_posts->have_posts() ) {
					$get_posts->the_post();
					$event_advertise_image = get_post_meta( get_the_ID(), 'goodsoul_metabox_event_advertise_image', true );
					?>
				<!-- Event Blokc One -->
				<div class="event-block-one col-lg-4">
					<div class="inner-box">
					<?php
					if ( has_post_thumbnail() ) {
						?>
						<div class="image">
						<?php
						if ( isset( $event_advertise_image ) && $event_advertise_image != '' ) {
							$ad_im = wp_get_attachment_image_url( $event_advertise_image, 'full' );
							?>
								<img src="<?php echo esc_url( $ad_im ); ?>" alt="Awesome Image">
								<?php
						} else {
							the_post_thumbnail();
						}
						?>
						</div>
						<?php } ?>
						<div class="lower-content">
							<div class="date">
								<h1><?php echo tribe_get_start_date( $post->ID, false, 'j' ); ?></h1>
								<div class="text"><span><?php echo tribe_get_start_date( $post->ID, false, 'F' ); ?></span> <br><?php echo tribe_get_start_date( $post->ID, false, 'G:i' ); ?>  - <?php echo tribe_get_end_date( $post->ID, false, 'G:i' ); ?></div>
							</div>
							<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
							<div class="location"><span class="flaticon-point"></span><?php echo tribe_get_address(); ?></div>
						</div>
						<div class="link-btn"><a href="<?php the_permalink(); ?>"><span class="flaticon-next"></span><?php echo $join_text; ?></a></div>
					</div>
				</div>
					<?php } ?>
			</div>
		</div>
	</section>
			<?php endif; ?>
			<?php if ( $event_style == 'style_4' ) : ?>
				 <!-- Events Section -->
				 <section class="events-section style-two">
					<div class="auto-container">
						<div class="row">
							<?php
							while ( $get_posts->have_posts() ) {
								$get_posts->the_post();
								$event_advertise_image = get_post_meta( get_the_ID(), 'goodsoul_metabox_event_advertise_image', true );
								?>
								<div class="event-block-one col-lg-4 col-md-6">
									<div class="inner-box">
										<div class="image">
										<?php
										if ( isset( $event_advertise_image ) && $event_advertise_image != '' ) {
											$ad_im = wp_get_attachment_image_url( $event_advertise_image, 'full' );
											?>
												<img src="<?php echo esc_url( $ad_im ); ?>" alt="Awesome Image">
												<?php
										} else {
											the_post_thumbnail();
										}
										?>
										</div>
										<div class="lower-content">
											<div class="date">
												<h1>
												<?php echo tribe_get_start_date( $post->ID, false, 'j' ); ?>
												</h1>
												<div class="text"><span>
												<?php echo tribe_get_start_date( $post->ID, false, 'F' ); ?>
												<?php // echo get_the_date( 'F' ); ?></span> <br><?php echo tribe_get_start_date( $post->ID, false, 'G:i ' ); ?>  - <?php echo tribe_get_end_date( $post->ID, false, 'G:i ' ); ?></div>
											</div>
											<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
											<div class="location"><span class="flaticon-point"></span><?php echo tribe_get_address(); ?></div>
										</div>
										<div class="link-btn"><a href="<?php the_permalink(); ?>"><span class="flaticon-next-1"></span><?php echo $join_text; ?></a></div>
									</div>
								</div>
								<?php } ?>
						</div>


					</div>
				</section>
			<?php endif; ?>
			<?php if ( $event_style == 'style_5' ) : ?>
				<section class="upcoming-events-section">
					<div class="auto-container">
						<div class="sec-title text-center light">
							<h5><?php echo wp_kses_post( $tagtitle ); ?></h5>
							<h1><?php echo wp_kses_post( $title ); ?></h1>
						</div>
						<div class="single-item-carousel owl-carousel owl-theme owl-nav-style-three">
						<?php
						while ( $get_posts->have_posts() ) {
							$get_posts->the_post();
							?>
							<div class="event-block-four">
								<h1><?php the_title(); ?></h1>
								<div class="text"><?php echo tribe_get_address(); ?>
									<br><?php echo tribe_get_start_date( $post->ID, false, 'd M Y , g.ia' ); ?></div>
								<div class="link-box"><a href="#" class="theme-btn btn-style-twelve"><span><?php echo $donate_text; ?></span></a><a href="<?php the_permalink(); ?>" class="theme-btn btn-style-eleven"><span><?php echo $join_text; ?></span></a></div>
							</div>
							<?php } ?>
						</div>
					</div>
				</section>
			<?php endif; ?>
		<?php
	}

	protected function content_template() {

	}

}

Plugin::instance()->widgets_manager->register( new GoodSoul_Events() );
