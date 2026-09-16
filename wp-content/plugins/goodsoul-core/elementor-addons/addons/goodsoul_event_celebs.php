<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
use \Elementor\Icons_Manager;

class GoodSoul_Event_Celebs extends \Elementor\Widget_Base {












	public function get_name() {
		return 'goodsoul_celebs';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Event Celebs', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'donor_content',
			array(
				'label' => esc_html__( 'Celebs', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'The trusted choice of donors' ),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'image',
			array(
				'label'   => __( 'Celeb image', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'name',
			array(
				'label'   => esc_html__( 'Celeb Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Benjie Alphonso' ),
			)
		);

		$repeater->add_control(
			'desig',
			array(
				'label'   => esc_html__( 'Designation ', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Founder' ),
			)
		);

		$repeater->add_control(
			'social',
			array(
				'label'   => esc_html__( 'Amount ', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __(
					'<ul class="social-icon-two">
                <li><a href="#"><span class="fa fa-facebook"></span></a></li>
                <li><a href="#"><span class="fa fa-twitter"></span></a></li>
                <li><a href="#"><span class="fa fa-linkedin"></span></a></li>
            </ul>'
				),
			)
		);

		$repeater->add_control(
			'link_celeb',
			array(
				'label'         => esc_html__( 'Celeb Link', 'goodsoul-core' ),
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
			'client_list',
			array(
				'label'   => esc_html__( 'Celebs List', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
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
			)
		);

	}
	protected function render() {
		$settings    = $this->get_settings_for_display();
		$title       = $settings['title'];
		$client_list = $settings['client_list'];
		?>
<section class="team-section-four">
	<div class="auto-container">
		<div class="sec-title">
			<h1><?php echo $title; ?></h1>
			<span class="border-bottom"></span>
		</div>
		<div class="wrapper-box">
			<div class="four-item-carousel owl-theme owl-carousel owl-nav-style-two owl-dots-none">
				<!-- Team Blokc Four -->
				<?php
				foreach ( $client_list as $client ) {
					$image      = ( $client['image']['id'] != '' ) ? wp_get_attachment_url( $client['image']['id'], 'full' ) : $client['image']['url'];
					$name       = $client['name'];
					$desig      = $client['desig'];
					$social     = $client['social'];
					$link_celeb = $client['link_celeb']['url'];
					?>
				<div class="team-block-four">
					<div class="inner-box">
						<div class="image">
							<a href="<?php echo esc_url( $link_celeb ); ?>">
								<img src="<?php echo esc_url( $image ); ?>"
									alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
							</a>
						</div>
						<div class="lower-content">
							<div class="author-info">
								<h4> <a href="<?php echo esc_url( $link_celeb ); ?>"><?php echo $name; ?></a></h4>
								<div class="designation"><?php echo $desig; ?></div>
							</div>
							<?php echo $social; ?>
						</div>
					</div>
				</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</section>
		<?php
	}
}

\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Event_Celebs() );
