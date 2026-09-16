<?php
/**
 * Elementor Protfolio area
 *
 * @since 1.0.0
 */
class Goodsoul_Portfolio extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_portfolio';
	}
	public function get_title() {
		return __( 'Goodsoul Portfolio', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'content_section_list',
			array(
				'label' => __( 'Protfolio list area', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'all_btn_name',
			array(
				'label'   => esc_html__( 'View All Button', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'All' ),
			)
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'protfolio_filter',
			array(
				'label'   => __( 'Single Service name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Category 1' ),
			)
		);
		$repeater->add_control(
			'protfolio_img',
			array(
				'label'   => __( 'Background image', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);
		$this->add_control(
			'protfolio_list',
			array(
				'label'  => __( 'About count list', 'goodsoul-core' ),
				'type'   => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),

			)
		);
		$this->end_controls_section();
	}
	protected function render() {
		$settings       = $this->get_settings_for_display();
		$all_btn_name    = $settings['all_btn_name'];
		$protfolio_list = $settings['protfolio_list'];
		?>
	<!-- Gallery Page Section -->
	<section class="gallery-page-section">
		<div class="auto-container">
			<!--MixitUp Galery-->
			<div class="mixitup-gallery">
				<!--Filter-->
				<div class="filters clearfix">
					<ul class="filter-tabs filter-btns clearfix">
						<li class="active filter" data-role="button" data-filter="all"><?php echo  $all_btn_name ; ?></li>
						<?php
							$category_arr       = array();
							$category_arr_class = array();
						foreach ( $protfolio_list as $key => $item ) {
							$cat                        = $item['protfolio_filter'];
							$child_categories_ex        = explode( ',', $cat );
							$child_categories           = str_replace( ',', ' ', $cat );
							$category_arr_class[ $key ] = strtolower( $child_categories );
							foreach ( $child_categories_ex as $child_category ) {
								$category_arr[] = strtolower( $child_category );
							}
						}
							$category_arr = array_unique( $category_arr );
						foreach ( $category_arr as $category ) {
							echo '<li class="filter" data-role="button" data-filter=".' . $category . '">' . $category . '</li>';
						}
						?>
					</ul>
				</div>

				<div class="filter-list row">
				<?php if ( $protfolio_list ) { ?>
					<?php foreach ( $protfolio_list as $key => $protfolio ) {
                     $image  = ( $protfolio['protfolio_img']['id'] != '' ) ? wp_get_attachment_url( $protfolio['protfolio_img']['id'], 'full' ) : $protfolio['protfolio_img']['url'];
						?>
					<div class="gallery-item-two mix all <?php echo esc_attr( $category_arr_class[ $key ] ); ?> col-lg-4 col-md-6 col-sm-12">
						<div class="image-box">
							<figure class="image"><img  src="<?php echo $image; ?>"  alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></figure>
							<div class="overlay-box"><a href="<?php echo $image; ?>" class="lightbox-image" data-fancybox="gallery"><span class="icon flaticon-cross-1"></span></a></div>
						</div>
					</div>
					<?php } ?>
				<?php } ?>
					
				</div>
			</div>
		</div>
	</section>
	<!-- End Gallery Page Section -->


		<?php
	}
}\Elementor\Plugin::instance()->widgets_manager->register( new \Goodsoul_Portfolio() );
