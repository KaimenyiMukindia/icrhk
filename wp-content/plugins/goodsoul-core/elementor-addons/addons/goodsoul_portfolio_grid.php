<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
use \Elementor\Icons_Manager;

class GoodSoul_Portfolio_Grid extends \Elementor\Widget_Base {










	public $category_arr = array();

	public function get_name() {
		return 'goodsoul_portfolio_grid';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Portfolio Grid', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'grid_content',
			array(
				'label' => esc_html__( 'Grid View', 'goodsoul-core' ),
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
			'mail_tagline',
			array(
				'label'     => esc_html__( 'Tagline', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => __( 'OUR WORKS' ),
				'condition' => array( 'select_layout' => 'style_3' ),
			)
		);
		$this->add_control(
			'mail_title',
			array(
				'label'     => esc_html__( 'Title', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => __( 'Gallery of our works' ),
				'condition' => array( 'select_layout' => 'style_3' ),
			)
		);

		$this->add_control(
			'all_btn_name',
			array(
				'label'      => esc_html__( 'View All Button', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'default'    => __( 'View All' ),
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'select_layout',
							'operator' => '==',
							'value'    => 'style_2',
						],
						[
							'name'     => 'select_layout',
							'operator' => '==',
							'value'    => 'style_3',
						],
					],
				],
			)
		);
		$repeater = new Repeater();
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
			'child_category',
			array(
				'label'       => esc_html__( 'Category', 'goodsoul-core' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => ( '' ),
				'description' => esc_html__( 'Enter categories with comma separated for multi entries', 'goodsoul-core' ),
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
				'label'      => esc_html__( 'Portfolio List', 'goodsoul-core' ),
				'type'       => \Elementor\Controls_Manager::REPEATER,
				'fields'     => $repeater->get_controls(),
				'default'    => array(
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
				),
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'select_layout',
							'operator' => '==',
							'value'    => 'style_2',
						],
						[
							'name'     => 'select_layout',
							'operator' => '==',
							'value'    => 'style_3',
						],
					],
				],
			)
		);

		$repeater_2 = new Repeater();
		$repeater_2->add_control(
			'tagline',
			array(
				'label'   => esc_html__( 'Tagline', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Awareness' ),
			)
		);
		$repeater_2->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Cancer Network' ),
			)
		);
		$repeater_2->add_control(
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
		$repeater_2->add_control(
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
			'portfolio_list_2',
			array(
				'label'     => esc_html__( 'Portfolio List', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::REPEATER,
				'fields'    => $repeater_2->get_controls(),
				'default'   => array(
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

				),
				'condition' => array( 'select_layout' => 'style_1' ),
			)
		);

		$this->end_controls_section();

	}
	protected function render() {
		$settings         = $this->get_settings_for_display();
		$select_layout    = $settings['select_layout'];
		$all_btn_name     = $settings['all_btn_name'];
		$portfolio_list   = $settings['portfolio_list'];
		$portfolio_list_2 = $settings['portfolio_list_2'];

		if ( ! empty( $portfolio_list ) ) {
			foreach ( $portfolio_list as $portfolio ) {

				if ( ! empty( $portfolio['child_category'] ) ) {
					$child_categories = explode( ',', $portfolio['child_category'] );

					foreach ( $child_categories as $child_category ) {

						if ( empty( $this->category_arr ) ) {

							$this->category_arr[] = strtolower( $child_category );

						} else {
							if ( ( ! in_array( strtolower( $child_category ), $this->category_arr ) && ( ! empty( strtolower( $child_category ) ) ) ) ) {

								$this->category_arr[] = $child_category;
							}
						}
					}
				}
			}
		}

		$this->category_arr = array_unique( $this->category_arr );
		?>

		<?php if ( $select_layout == 'style_1' ) { ?>

<!-- Gallery Section Four -->
<section class="gallery-section-four">
	<div class="auto-container">
		<div class="row">
			<?php
			foreach ( $portfolio_list_2 as $portfolio ) {
				$image     = ( $portfolio['image']['id'] != '' ) ? wp_get_attachment_url( $portfolio['image']['id'], 'full' ) : $portfolio['image']['url'];
				$tagline   = $portfolio['tagline'];
				$title     = $portfolio['title'];
				$page_link = $portfolio['page_link']['url'];

				?>
			<div class="col-lg-4 col-md-6 gallery-block-three">
				<div class="inner-box">
					<div class="image">
						<img src="<?php echo esc_url( $image ); ?>"
							alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
						<div class="overlay">
							<a data-fancybox="example gallery" href="<?php echo esc_url( $image ); ?>"
								class="zoom-btn"><span class="flaticon-more-1"></span></a>
						</div>
					</div>
					<div class="caption-title">
						<h5><?php echo wp_kses_post( $tagline ); ?></h5>
						<h4><a href="<?php echo esc_url( $page_link ); ?>"><?php echo wp_kses_post( $title ); ?></a>
						</h4>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</section>
			<?php
		} elseif ( $select_layout == 'style_2' ) {
			?>


<!-- Gallery Section five -->
<section class="gallery-section-five">
	<div class="sortable-masonry">

		<div class="auto-container">
			<!--Filter-->
			<div class="filters text-center mb-50 clearfix">
				<ul class="filter-tabs filter-btns clearfix">
					<li class="active filter" data-role="button" data-filter=".all">
						<span><?php echo $all_btn_name; ?></span></li>
			<?php foreach ( $this->category_arr as $cat ) { ?>
					<li class="filter" data-role="button" data-filter=".<?php echo strtolower( $cat ); ?>">
						<span><?php echo $cat; ?></span></li>
			<?php } ?>
				</ul>
			</div>
		</div>
		<div class="auto-container">
			<div class="items-container row">
			<?php
			foreach ( $portfolio_list as $portfolio ) {
				$image     = $portfolio['image']['url'];
				$image     = ( $portfolio['image']['id'] != '' ) ? wp_get_attachment_url( $portfolio['image']['id'], 'full' ) : $portfolio['image']['url'];
				$tagline   = $portfolio['tagline'];
				$title     = $portfolio['title'];
				$page_link = $portfolio['page_link']['url'];

				// $new_categories = array();
				$category_str     = '';
				$child_categories = explode( ',', $portfolio['child_category'] );
				foreach ( $child_categories as $child_category ) {

					$category_str .= $child_category . ' ';

				}
				?>


				<!-- Portfolio Block Two -->
				<div class="col-lg-4 col-md-6 gallery-block-four all <?php echo strtolower( $category_str ); ?>">
					<div class="inner-box">
						<div class="image">
							<img src="<?php echo esc_url( $image ); ?>"
								alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
							<div class="overlay">
								<a data-fancybox="example gallery" href="<?php echo esc_url( $image ); ?>"
									class="zoom-btn"><span class="flaticon-more-1"></span></a>
							</div>
						</div>
						<div class="caption-title">
							<h5><?php echo wp_kses_post( $tagline ); ?></h5>
							<h4><a href="<?php echo esc_url( $page_link ); ?>"><?php echo wp_kses_post( $title ); ?></a>
							</h4>
						</div>
					</div>
				</div>
			<?php } ?>
				<!-- End block -->
			</div>
		</div>
	</div>
</section>
			<?php
		} elseif ( $select_layout == 'style_3' ) {
			$main_tagline = $settings['mail_tagline'];
			$main_title   = $settings['mail_title'];
			?>
<section class="gallery-section-three">
	<div class="auto-container">
		<div class="sec-title text-center style-three">
			<h5><?php echo wp_kses_post( $main_tagline ); ?></h5>
			<h1><?php echo wp_kses_post( $main_title ); ?></h1>
		</div>
	</div>
	<div class="sortable-masonry">

		<div class="auto-container">
			<!--Filter-->
			<div class="filters text-center mb-50 clearfix">
				<ul class="filter-tabs filter-btns clearfix">
					<li class="active filter" data-role="button" data-filter=".all">
						<span><?php echo $all_btn_name; ?></span></li>
			<?php foreach ( $this->category_arr as $cat ) { ?>
					<li class="filter" data-role="button" data-filter=".<?php echo strtolower( $cat ); ?>">
						<span><?php echo $cat; ?></span></li>
			<?php } ?>
				</ul>
			</div>
		</div>
		<div class="auto-container">
			<div class="items-container row">
				<!-- Portfolio Block Two -->
			<?php
			foreach ( $portfolio_list as $portfolio ) {
				$image     = $portfolio['image']['url'];
				$image     = ( $portfolio['image']['id'] != '' ) ? wp_get_attachment_url( $portfolio['image']['id'], 'full' ) : $portfolio['image']['url'];
				$tagline   = $portfolio['tagline'];
				$title     = $portfolio['title'];
				$page_link = $portfolio['page_link']['url'];

				// $new_categories = array();
				$category_str     = '';
				$child_categories = explode( ',', $portfolio['child_category'] );
				foreach ( $child_categories as $child_category ) {

					$category_str .= $child_category . ' ';

				}
				?>
				<!-- Portfolio Block Two -->
				<div class="col-lg-4 col-md-6 gallery-block-three all <?php echo strtolower( $category_str ); ?>">
					<div class="inner-box">
						<div class="image">
							<img src="<?php echo esc_url( $image ); ?>"
								alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
							<div class="overlay">
								<a data-fancybox="example gallery" href="<?php echo esc_url( $image ); ?>"
									class="zoom-btn"><span class="flaticon-more-1"></span></a>
							</div>
						</div>
						<div class="caption-title">
							<h5><?php echo wp_kses_post( $tagline ); ?></h5>
							<h4><a href="<?php echo esc_url( $page_link ); ?>"><?php echo wp_kses_post( $title ); ?></a>
							</h4>
						</div>
					</div>
				</div>
			<?php } ?>
			</div>
		</div>
	</div>
</section>
			<?php
		}
	}
}

\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Portfolio_Grid() );
