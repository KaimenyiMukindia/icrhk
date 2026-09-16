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

class GoodSoul_History extends Widget_Base {
	public function get_name() {
		return 'goodsoul_history';
	}
	public function get_title() {
		return esc_html__( 'Goodsoul History', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'whychoose_content',
			array(
				'label' => esc_html__( 'History', 'goodsoul-core' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
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

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'A small charity has a big impact' ),
			)
		);

		$this->add_control(
			'left_content',
			array(
				'label'   => esc_html__( 'Left Content', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( '<h4>With more than <span>3,900 staff of 50 nationalities,</span> Concern operates in 25 of the world’s poor countries, helping people to achieve major in their lives.</h4>' ),
			)
		);

		$this->add_control(
			'experience_title',
			array(
				'label'   => esc_html__( 'Experience Title', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'More than 32 years experience' ),
			)
		);
		$this->add_control(
			'experience_summary',
			array(
				'label'   => esc_html__( 'Experience Summary', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Beguiled and demoralized by the charms of pleasure of the moment, so by desire, that they cannot foresee the pain and trouble that are bound to ensue   to those who fail in their shrinking.' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			array(
				'label'   => __( 'Image', 'goodsoul-core' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'year',
			array(
				'label'   => esc_html__( 'Year', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( '1978' ),
			)
		);

		$repeater->add_control(
			'summary',
			array(
				'label'   => esc_html__( 'Some Text', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'In the summer of 1980, Jonathan, Richard, Mark and Craig <br>met to discuss how they could use their skills to raise money to the  daily <br>life of the poor people.' ),
			)
		);

		$this->add_control(
			'yearlist',
			array(
				'label'   => __( 'Experience Years', 'goodsoul-core' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(
						'list_title'   => __( 'Year #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Year #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
		);
		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();

		$icon               = $settings['icon'];
		$title              = $settings['title'];
		$left_content       = $settings['left_content'];
		$experience_title   = $settings['experience_title'];
		$experience_summary = $settings['experience_summary'];
		$yearlist           = $settings['yearlist'];
		?>

	<!-- History Section -->
	<section class="history-section">
		<div class="auto-container">
			<div class="description">
				<div class="top-content text-center">
				<?php if ( ! empty( $icon ) ) { ?>
					<div class="icon-box"><span class="<?php echo $icon['value']; ?>"></span></div>
				<?php } ?>
					<h1><?php echo wp_kses_post( $title ); ?></h1>
				</div>
				<div class="row">
					<div class="col-lg-6">
				<?php echo wp_kses_post( $left_content ); ?>
					</div>
					<div class="col-lg-6">
						<h3><?php echo wp_kses_post( $experience_title ); ?></h3>
						<div class="text"><?php echo wp_kses_post( $experience_summary ); ?></div>
					</div>
				</div>
			</div> 
			<div class="history-carousel">
				<div class="swiper-container history-image">
					<div class="swiper-wrapper">
				<?php
				foreach ( $yearlist as $year ) {
					$image     = ( $year['image']['id'] != '' ) ? wp_get_attachment_url( $year['image']['id'], 'full' ) : $year['image']['url'];
					$year_name = $year['year'];
					?>
						<div class="swiper-slide">
							<div class="image">
								<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
								<div class="year"><?php echo wp_kses_post( $year_name ); ?></div>
							</div>
						</div>
					<?php } ?>
					</div>
				</div>
				<div class="swiper-container history-content">
					<div class="swiper-wrapper">
					<?php
					foreach ( $yearlist as $year ) {
						$summary = $year['summary'];
						?>
						<div class="swiper-slide">
							<div class="text"><?php echo wp_kses_post( $summary ); ?></div>
						</div>
					<?php } ?>
					</div>
					<div class="swiper-nav-button">
						<!-- Add Arrows -->
						<div class="swiper-button-next"><i class="flaticon-next"></i></div>
						<div class="swiper-button-prev"><i class="flaticon-next"></i></div>
					</div>
				</div>
			</div>               
		</div>
	</section>
							<?php
	}
}

Plugin::instance()->widgets_manager->register( new \GoodSoul_History() );
