<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
use \Elementor\Icons_Manager;

class GoodSoul_Portfolio_Masonry extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_portfolio_masonry';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Portfolio Masonry', 'plugin-name' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'masonry_content',
			array(
				'label' => esc_html__( 'Masonry view', 'plugin-name' ),
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

		$repeater = new Repeater();

		$repeater->add_control(
			'tagline_option',
			array(
				'label'        => __( 'Show Tagline', 'goodsoul-core' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$repeater->add_control(
			'tagline',
			array(
				'label'   => esc_html__( 'Tagline', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Awareness' ),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Cancer Network' ),
			)
		);
		$repeater->add_control(
			'page_link',
			array(
				'label'         => esc_html__( 'Page Link', 'goodsoul-core' ),
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
			'col_size',
			array(
				'label'   => esc_html__( 'Column Size', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'2'  => esc_html__( '2', 'goodsoul-core' ),
					'3'  => esc_html__( '3', 'goodsoul-core' ),
					'4'  => esc_html__( '4', 'goodsoul-core' ),
					'6'  => esc_html__( '6', 'goodsoul-core' ),
					'8'  => esc_html__( '8', 'goodsoul-core' ),
					'10' => esc_html__( '10', 'goodsoul-core' ),
					'12' => esc_html__( '12', 'goodsoul-core' ),

				),
				'default' => esc_html__( '4', 'goodsoul-core' ),
			)
		);
		$repeater->add_control(
			'image',
			array(
				'label'   => __( 'Image', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_control(
			'portfolio_list',
			array(
				'label'   => esc_html__( 'Portfolio List', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(
						'list_title'   => __( 'Item #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Item #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Item #3', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Feature #4', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
		);

		$this->end_controls_section();

	}
	protected function render() {
		$settings = $this->get_settings_for_display();

		$select_layout  = $settings['select_layout'];
		$portfolio_list = $settings['portfolio_list'];

		?>

		<?php if ( $select_layout == 'style_1' ) { ?>

	<!-- Gallery Section six -->
	<section class="gallery-section-six">
		<div class="sortable-masonry">

			<div class="auto-container">
				<div class="items-container row">
					<?php
					foreach ( $portfolio_list as $portfolio ) {
						$image         = ( $portfolio['image']['id'] != '' ) ? wp_get_attachment_url( $portfolio['image']['id'], 'full' ) : $portfolio['image']['url'];
						$tagline_option = $portfolio['tagline_option'];
						$tagline        = $portfolio['tagline'];
						$title          = $portfolio['title'];
						$col_size       = $portfolio['col_size'];
						$page_link      = $portfolio['page_link']['url'];
						?>
					<!-- Portfolio Block Two -->
					<div class=" col-lg-<?php echo $col_size; ?> col-md-6 gallery-block-three">
						<div class="inner-box">
							<div class="image">
								<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
								<div class="overlay">
									<a data-fancybox="example gallery" href="<?php echo esc_url( $image ); ?>" class="zoom-btn"><span class="flaticon-more-1"></span></a>
								</div>
							</div>
							<div class="caption-title">
							<?php
							if ( $tagline_option === 'yes' ) {
								?>
								<h5><?php echo wp_kses_post( $tagline ); ?></h5>
								<?php } ?>
								<h4><a href="<?php echo esc_url( $page_link ); ?>"><?php echo wp_kses_post( $title ); ?></a></h4>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</section>
			<?php } ?>

						<?php if ( $select_layout == 'style_2' ) { ?>


	<!-- Gallery Section six -->
	<section class="gallery-section-six">
		<div class="sortable-masonry">

			<div class="auto-container">
				<div class="items-container row">
							<?php
							foreach ( $portfolio_list as $portfolio ) {
								$image         = ( $portfolio['image']['id'] != '' ) ? wp_get_attachment_url( $portfolio['image']['id'], 'full' ) : $portfolio['image']['url'];
								$tagline_option = $portfolio['tagline_option'];
								$tagline        = $portfolio['tagline'];
								$title          = $portfolio['title'];
								$col_size       = $portfolio['col_size'];
								$page_link      = $portfolio['page_link']['url'];
								?>
					<!-- Portfolio Block Two -->
					<div class="col-lg-<?php echo $col_size; ?> col-md-6 gallery-block-five">
						<div class="inner-box">
							<div class="image">
								<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
							</div>
							<div class="caption-title">
								<?php
								if ( $tagline_option === 'yes' ) {
									?>
									<h5><?php echo wp_kses_post( $tagline ); ?></h5>
								<?php } ?>
								<h4><a href="<?php echo esc_url( $page_link ); ?>"><?php echo wp_kses_post( $title ); ?><span class="flaticon-next"></span></a></h4>
							</div>
						</div>
					</div>
							<?php } ?>
				</div>
			</div>
		</div>
	</section>
					<?php } ?>
	
								<?php
	}
}

\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Portfolio_Masonry() );
