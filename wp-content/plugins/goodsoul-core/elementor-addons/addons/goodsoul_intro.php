<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
use \Elementor\Icons_Manager;

class GoodSoul_Intro extends \Elementor\Widget_Base {





	public function get_name() {
		return 'goodsoul_intro';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Intro', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}

	private function get_causes() {
		$options = array();
		$args    = array(
			'post_type'      => array( 'campaign' ),
			'posts_per_page' => -1,
		);
		$causes  = new WP_Query( $args );
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
			'feature_content',
			array(
				'label' => esc_html__( 'Intro', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'before_image',
			array(
				'label'     => esc_html__( 'Before Image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'selectors' => array(
					'{{WRAPPER}} .intro-section:before' => 'background: url({{URL}})',

				),

			)
		);

		$this->add_control(
			'section_color',
			array(
				'label'     => __( 'Background Color', 'cameron-core' ),
				'separator' => 'before',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .intro-section:before' => 'background-color: {{VALUE}}',

				),
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'   => __( 'Choose Icon', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-star',
					'library' => 'solid',
				),
			)
		);

		$this->add_control(
			'tagline',
			array(
				'label'   => esc_html__( 'Tagline', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Welcome to Goodsoul' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Our mission is save environment from <br>plastic and pollution' ),
			)
		);
		$this->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a pleasure.' ),
			)
		);

		$this->add_control(
			'experiance_year',
			array(
				'label'   => esc_html__( 'Experienced', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( '13 <br> <span>Years Service</span>' ),
			)
		);

		$this->add_control(
			'experiance_summary',
			array(
				'label'   => esc_html__( 'Experience Summary', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __(
					'<h4>Goodsoul Protect</h4>
				<h1>26,845 m2</h1>
				<h4>Gas Emission Around The World.</h4>'
				),
			)
		);

		$this->add_control(
			'button_name',
			array(
				'label'   => esc_html__( 'Button Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Continue Reading' ),
			)
		);

		$this->add_control(
			'link',
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
			'campage_name',
			array(
				'label'   => esc_html__( 'Campage Button Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Learn More' ),
			)
		);
		$this->add_control(
			'number_of_coloumns',
			array(
				'label'   => __( 'Number Of Posts', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'1' => __( '1', 'goodsoul-core' ),
					'2' => __( '2', 'goodsoul-core' ),
					'3' => __( '3', 'goodsoul-core' ),
				),
				'default' => '3',
			)
		);

		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'causes',
			array(
				'label'   => __( 'Select The Proper Cost', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $this->get_causes(),

			)
		);

		$repeater->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Summary', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Our power of choice is untrammelled and <br/>nothing prevents like best.' ),

			)
		);
		$repeater->add_control(
			'campaigns',
			array(
				'label' => esc_html__( 'Campaigns', 'goodsoul-core' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,

			)
		);

		$this->add_control(
			'items',
			array(
				'label'  => __( 'Repeater List', 'goodsoul-core' ),
				'type'   => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			)
		);

		$this->end_controls_section();

	}
	protected function render() {
		$settings           = $this->get_settings_for_display();
		$icon               = $settings['icon'];
		$tagline            = $settings['tagline'];
		$title              = $settings['title'];
		$content            = $settings['content'];
		$experiance_year    = $settings['experiance_year'];
		$experiance_summary = $settings['experiance_summary'];
		$button_name        = $settings['button_name'];
		$campage_name       = $settings['campage_name'];
		$page_link          = $settings['link']['url'];
		$number_of_coloumns = $settings['number_of_coloumns'];

		$coloumn_class = '';

		if ( $number_of_coloumns == '1' ) {
			$coloumn_class = 'col-lg-12';
		} elseif ( $number_of_coloumns == '2' ) {
			$coloumn_class = 'col-lg-6';
		} elseif ( $number_of_coloumns == '3' ) {
			$coloumn_class = 'col-lg-4';
		}
		?>


	<!-- Intro Section -->
	<section class="intro-section">
		<div class="auto-container">
			<div class="sec-title text-center style-two">
		<?php if ( ! empty( $icon ) ) { ?>
				<div class="icon-box"><span class="<?php echo $icon['value']; ?>"></span></div>
		<?php } ?>
				<h5><?php echo wp_kses_post( $tagline ); ?></h5>
				<h1><?php echo wp_kses_post( $title ); ?></h1>
			</div>
			<div class="top-content">
				<div class="row">
					<div class="col-lg-6">
						<div class="experience-block">
							<div class="experience-years"><?php echo wp_kses_post( $experiance_year ); ?></div>
		<?php echo wp_kses_post( $experiance_summary ); ?>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="text"><?php echo wp_kses_post( $content ); ?></div>
		<?php if ( ! empty( $page_link ) ) { ?>
						<a href="<?php echo esc_url( $page_link ); ?>"><span class="flaticon-next"></span><?php echo wp_kses_post( $button_name ); ?></a>
		<?php } ?>
					</div>
				</div>
			</div>
			<div class="bottom-content">
				<div class="row">
		<?php
		if ( $settings['items'] ) :
			foreach ( $settings['items'] as $item ) {

				  $campaign       = charitable_get_campaign( $item['causes'] );
				  $image_url      = get_the_post_thumbnail_url( $campaign->ID );
				  $title          = $campaign->post_title;
				  $post_page_link = $campaign->guid;
				  $content        = $item['content'];
				  $campaigns      = $item['campaigns'];
				?>
					<!-- Intro Block -->
					<div class="intro-block-one <?php echo $coloumn_class; ?>">
						<div class="inner-box">
							<div class="wrrapper-box">
								<div class="image"><img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></div>
								<div class="content">
									<div class="campaigns"><a href="<?php echo esc_url( $post_page_link ); ?>"><span class="flaticon-next"></span><?php echo $campaigns; ?></a></div>
									<h4><?php echo wp_kses_post( $title ); ?></h4>
									<div class="text"><?php echo wp_kses_post( $content ); ?></div>
								</div>
							</div>
							<div class="link-btn"><a href="<?php echo esc_url( $post_page_link ); ?>"><?php echo $campage_name; ?></a></div>
						</div>
					</div>
				<?php
			}
		endif;
		?>
				</div>
			</div>
		</div>
	</section>
		<?php
	}
}

						\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Intro() );
