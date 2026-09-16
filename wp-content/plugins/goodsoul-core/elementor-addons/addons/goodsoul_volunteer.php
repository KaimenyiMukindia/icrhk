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

class GoodSoul_volunteer extends Widget_Base {
	public function get_name() {
		return 'goodsoul_volunteer';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Volunteer', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	public function get_script_depends() {
		return array( 'volunteer' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'volunteer_content',
			array(
				'label' => esc_html__( 'Volunteer', 'goodsoul-core' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'bg_image',
			array(
				'label'   => __( 'Background image', 'goodsoul-core' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'image',
			array(
				'label'   => __( 'Volunteer image', 'goodsoul-core' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label'   => __( 'Choose Icon', 'goodsoul-core' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-star',
					'library' => 'solid',
				),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Caring is the gift of making <br>the ordinary special.' ),
			)
		);

		$repeater->add_control(
			'sub_title',
			array(
				'label'   => esc_html__( 'Sub Title', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Complete our online registration form:' ),
			)
		);
		$repeater->add_control(
			'form_list',
			array(
				'label'   => esc_html__( 'List content', 'goodsoul-core' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => __(
					'<li>With righteous indignation and dislike</li>
				<li>Perfectly simple and easy</li>
				<li>Have to be repudiated & annoyances</li>'
				),

			)
		);

		$this->add_control(
			'volunteer_list',
			array(
				'label'   => esc_html__( 'Volunteer List', 'goodsoul-core' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(
						'list_title'   => __( 'Volunteer #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Volunteer #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Volunteer #3', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
		);

		$this->end_controls_section();

	}
	protected function render() {

		$settings       = $this->get_settings_for_display();
		$bg_image       = ( $settings['bg_image']['id'] != '' ) ? wp_get_attachment_url( $settings['bg_image']['id'], 'full' ) : $settings['bg_image']['url'];
		$volunteer_list = $settings['volunteer_list'];
		?>

	<!-- Volunteer Section Two -->
	<section class="volunteer-section-two" style="background-image: url(<?php echo esc_url( $bg_image ); ?>);">
		<div class="auto-container">
			<div class="volunteer-carousel">
				<div class="row">
					<div class="col-lg-6">
						<div class="swiper-container volunteer-image">
							<div class="swiper-wrapper">
						<?php
						foreach ( $volunteer_list as $volunteer ) {
							$image = $volunteer['image']['url'];
							?>
								<div class="swiper-slide">
									<div class="author-thumb"><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></div>
								</div>
							<?php } ?>
							</div>
							<div class="swiper-nav-button">
								<!-- Add Arrows -->
								<div class="swiper-button-next"><i class="fa fa-angle-right"></i></div>
								<div class="swiper-button-prev"><i class="fa fa-angle-left"></i></div>
							</div>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="swiper-container volunteer-content">
							<div class="swiper-wrapper">
							<?php
							foreach ( $volunteer_list as $volunteer ) {
								$title     = $volunteer['title'];
								$sub_title = $volunteer['sub_title'];
								$form_list = $volunteer['form_list'];
								$icon      = $volunteer['icon'];
								?>
								<div class="swiper-slide">
									<!-- Volunteer Block -->
									<div class="volunteer-block">
										<div class="inner-box">
											<div class="icon-box"><span class="<?php echo esc_attr( $icon['value'] ); ?>"></span></div>
											<h1><?php echo wp_kses_post( $title ); ?></h1>
											<?php if ( ! empty( $sub_title ) ) { ?>
											<h4><span class="flaticon-next"></span><?php echo wp_kses_post( $sub_title ); ?></h4>
											<?php } ?>
											<ul class="list">
											<?php echo wp_kses_post( $form_list ); ?>
											</ul>
										</div>
									</div>
								</div>
							<?php } ?>
							</div>
							<div class="swiper-counter-two">
								<div id="current">01</div>
								<div id="total"></div>
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

Plugin::instance()->widgets_manager->register( new GoodSoul_volunteer() );
