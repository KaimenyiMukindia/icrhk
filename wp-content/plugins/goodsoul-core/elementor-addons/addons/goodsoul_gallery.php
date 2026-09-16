<?php
/**
 * Elementor gallery
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
use \Elementor\Icons_Manager;
class GoodSoul_Gallery extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_gellery';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Gallery', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'gallery_content',
			array(
				'label' => esc_html__( 'Gallery', 'goodsoul-core' ),
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
			'select_show',
			array(
				'label'   => esc_html__( 'Tagline', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::HIDDEN,
				'default' => __( 'select_show' ),
			)
		);

		$this->add_control(
			'tagline',
			array(
				'label'   => esc_html__( 'Tagline', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'OUR GALLERY' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'goodsoul-core' ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Gallery of our works' ),
			)
		);

		$this->add_control(
			'btn_name',
			array(
				'label'   => esc_html__( 'Button name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'More from Gallery' ),
			)
		);
		$this->add_control(
			'page_link',
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
			'zoom_btn_name',
			array(
				'label'   => esc_html__( 'Zoom Button name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Zoom' ),
				'condition' => array( 'select_layout' => 'style_1' ),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'image',
			array(
				'label'   => __( 'Choose image', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'tagline',
			array(
				'label'   => esc_html__( 'Tagline', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Environment' ),
			)
		);

		$repeater->add_control(
			'tagline_show',
			array(
				'label'   => esc_html__( 'Tagline', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::HIDDEN,
				'default' => __( 'tagline_show' ),
			)
		);

		$repeater->add_control(
			'caption',
			array(
				'label'   => esc_html__( 'Caption', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Clean Kamilo Beach' ),
			)
		);

		$repeater->add_control(
			'caption_link',
			array(
				'label'         => esc_html__( 'Caption Link', 'goodsoul-core' ),
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
			'image_list',
			array(
				'label'   => esc_html__( 'Image List', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(
						'list_title'   => __( 'Image #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Image #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Image #3', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
		);

	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$select_layout = $settings['select_layout'];
		$tagline       = $settings['tagline'];
		$title         = $settings['title'];
		$btn_name      = $settings['btn_name'];
		$zoom_btn_name      = $settings['zoom_btn_name'];
		$page_link     = $settings['page_link']['url'];
		$image_list    = $settings['image_list'];
		?>
		<?php if ( $select_layout == 'style_1' ) { ?>
	<!-- Gallery Section -->
	<section class="gallery-section">
		<div class="fluid-container">
			<div class="wrapper-box">
				<div class="content-column">
					<div class="sec-title light">
						<h5><?php echo wp_kses_post( $tagline ); ?></h5>
						<h1><?php echo wp_kses_post( $title ); ?></h1>
					</div>
					<?php if ( ! empty( $page_link ) ) { ?>
					<div class="link-btn"><a href="<?php echo esc_url( $page_link ); ?>"><span class="flaticon-next"></span><?php echo wp_kses_post( $btn_name ); ?> </a></div>
					<?php } ?>
				</div>
				<div class="portfolio-column">
					<div class="four-item-carousel owl-theme owl-carousel owl-nav-style-three owl-dots-none">
					<?php
					foreach ( $image_list as $img ) {
						$image        = ( $img['image']['id'] != '' ) ? wp_get_attachment_url( $img['image']['id'], 'full' ) : $img['image']['url'];
						$caption      = $img['caption'];
						$caption_link = $img['caption_link']['url'];
						?>
						<!-- Gallery Block One -->
						<div class="gallery-block-one">
							<div class="inner-box">
								<div class="image"><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></div>
								<div class="overlay">
									<a data-fancybox="example gallery" href="<?php echo esc_url( $image ); ?>" class="zoom-btn"><span><?php echo $zoom_btn_name ; ?></span></a>
									<h4><a href="<?php echo esc_url( $caption_link ); ?>"><?php echo wp_kses_post( $caption ); ?></a></h4>
								</div>
							</div>                            
						</div>
					<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php } ?>

		<?php if ( $select_layout == 'style_2' ) { ?>
		<!-- Gallery Section Two -->
		<section class="gallery-section-two">
		<div class="fluid-container">
			<div class="wrapper-box">
				<div class="content-column">
					<div class="sec-title light">
						<h5><?php echo wp_kses_post( $tagline ); ?></h5>
						<h1><?php echo wp_kses_post( $title ); ?></h1>
					</div>
					<?php if ( ! empty( $page_link ) ) { ?>
					<div class="link-btn"><a href="<?php echo esc_url( $page_link ); ?>"><span class="flaticon-next"></span><?php echo wp_kses_post( $btn_name ); ?></a></div>
					<?php } ?>
				</div>
				<?php
				foreach ( $image_list as $img ) {
					$image         = ( $img['image']['id'] != '' ) ? wp_get_attachment_url( $img['image']['id'], 'full' ) : $img['image']['url'];
					$caption      = $img['caption'];
					$tagline      = $img['tagline'];
					$caption_link = $img['caption_link']['url'];
					?>
				<!-- Gallery Block Two -->
				<div class="gallery-block-two">
					<div class="inner-box">
						<figure class="image">
							<img src="<?php echo esc_url( $image ); ?>" alt="image">
							<div class="overlay">
								<a class="lightbox-image option-btn" title="<?php echo wp_kses_post( $caption ); ?>" data-fancybox="example-gallery" href="<?php echo esc_url( $image ); ?>">
									<i class="flaticon-more-1"></i>
								</a>
								<a class="link-btn" href="<?php echo esc_url( $caption_link ); ?>">
									<i class="flaticon-link"></i>
								</a>                                
							</div>
						</figure>
						<div class="caption-title">
							<span><?php echo wp_kses_post( $tagline ); ?></span>
							<h4><a href="<?php echo esc_url( $caption_link ); ?>"><?php echo wp_kses_post( $caption ); ?></a></h4>
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

						\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Gallery() );
