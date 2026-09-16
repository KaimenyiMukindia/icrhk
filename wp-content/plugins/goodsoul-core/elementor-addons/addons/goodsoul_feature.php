<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
use \Elementor\Icons_Manager;

class GoodSoul_Feature extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_feature';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Feature', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'feature_content',
			array(
				'label' => esc_html__( 'Feature', 'goodsoul-core' ),
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
			'icon',
			array(
				'label'     => __( 'Choose Icon', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-star',
					'library' => 'solid',
				),
				'condition' => array( 'select_layout' => 'style_2' ),
			)
		);

		$this->add_control(
			'tagline',
			array(
				'label'     => esc_html__( 'Tagline', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => __( 'You can help with this' ),
				'condition' => array( 'select_layout' => 'style_2' ),
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

		$this->add_control(
			'content',
			array(
				'label'     => esc_html__( 'Text', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => __( 'The right way to live as a human being, Just help to those people really need your help.' ),
				'condition' => array( 'select_layout' => 'style_1' ),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'block_image',
			array(
				'label'   => __( 'Feature block image', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'block_icon',
			array(
				'label'   => __( 'Feature Icon image', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);
		$repeater->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Become a Volunteer' ),
			)
		);
		$repeater->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Indignation and dislike mens all <br>beguiled demoralized.' ),
			)
		);
		$repeater->add_control(
			'button_name',
			array(
				'label'   => esc_html__( 'Button Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Details' ),
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

		$this->add_control(
			'feature_list',
			array(
				'label'   => esc_html__( 'Feature List', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(
						'list_title'   => __( 'Feature #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Feature #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Feature #3', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Feature #4', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
		);

		$this->add_control(
			'bottom_option',
			array(
				'label'        => __( 'Show Bottom Content', 'goodsoul-core' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'select_layout' => 'style_1' ),
			)
		);

		$this->add_control(
			'sub_title',
			array(
				'label'     => esc_html__( 'Title 2', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => __( 'You have the power to bring happiness.' ),
				'condition' => array( 'select_layout' => 'style_1' ),
			)
		);

		$this->add_control(
			'sub_content',
			array(
				'label'     => esc_html__( 'Text 2', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => __( 'Expound the actual teachings of the great explorer of the truth, the masterr of human happiness.' ),
				'condition' => array( 'select_layout' => 'style_1' ),
			)
		);

		$this->add_control(
			'button_name_2',
			array(
				'label'     => esc_html__( 'Member Button Name', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => __( 'Meet Our Team' ),
				'condition' => array( 'select_layout' => 'style_1' ),
			)
		);

		$this->add_control(
			'page_link_2',
			array(
				'label'         => esc_html__( 'Member Page Link', 'goodsoul-core' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
				'condition'     => array( 'select_layout' => 'style_1' ),
			)
		);

		$this->end_controls_section();

	}
	protected function render() {
		$settings           = $this->get_settings_for_display();
		$icon               = $settings['icon'];
		$select_layout      = $settings['select_layout'];
		$bottom_option      = $settings['bottom_option'];
		$title              = $settings['title'];
		$tagline            = $settings['tagline'];
		$content            = $settings['content'];
		$sub_title          = $settings['sub_title'];
		$sub_content        = $settings['sub_content'];
		$feature_list       = $settings['feature_list'];
		$button_name_2      = $settings['button_name_2'];
		$page_link_2        = isset($settings['page_link_2']['url'])? $settings['page_link_2']['url'] :'#';
		$number_of_coloumns = count( $feature_list );
		if ( $number_of_coloumns == '1' ) {
			$coloumn_class = 'col-lg-12';
		} elseif ( $number_of_coloumns == '2' ) {
			$coloumn_class = 'col-lg-6';
		} elseif ( $number_of_coloumns == '3' ) {
			$coloumn_class = 'col-lg-4';
		} elseif ( $number_of_coloumns == '4' ) {
			$coloumn_class = 'col-lg-3';
		} else {
			$coloumn_class = 'col-lg-6';
		}
		?>

		<?php if ( $select_layout == 'style_1' ) { ?>
	<!-- Feature Section -->
	<section class="feature-section">
		<div class="auto-container">
			<div class="sec-title text-center">
				<h1><?php echo wp_kses_post( $title ); ?></h1>
				<div class="text"><?php echo wp_kses_post( $content ); ?></div>
			</div>
			<div class="row">
				<!-- Feature Block Two -->
			<?php
			foreach ( $feature_list as $feature ) {
				$feature_title       = $feature['title'];
				$feature_image     = ($feature['block_image']['id'] != '' ) ? wp_get_attachment_url( $feature['block_image']['id'], 'full' ) : $feature['block_image']['url'];
				$feature_icon     = ($feature['block_icon']['id'] != '' ) ? wp_get_attachment_url( $feature['block_icon']['id'], 'full' ) : $feature['block_icon']['url'];
				$feature_content     = $feature['content'];
				$feature_button_name = $feature['button_name'];
				$feature_page_link =  $feature['page_link']['url'];
				?>
				<div class="<?php echo $coloumn_class; ?> col-md-6 feature-block-two">
					<div class="inner-box">
						<div class="icon-box">
							<img src="<?php echo esc_url( $feature_icon ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
						</div>
						<h4><?php echo wp_kses_post( $feature_title ); ?></h4>
						<div class="overlay" style="background-image: url(<?php echo esc_url( $feature_image ); ?>);">
							<h4><?php echo wp_kses_post( $feature_title ); ?></h4>
							<div class="text"><?php echo wp_kses_post( $feature_content ); ?></div>
							<?php if ( ! empty( $feature_page_link ) ) { ?>
							<div class="link-btn"><a href="<?php echo esc_url( $feature_page_link ); ?>" class="theme-btn btn-style-five"><span><?php echo wp_kses_post( $feature_button_name ); ?></span></a></div>
							<?php } ?>
						</div>
					</div>
				</div>
			<?php } ?>
			</div>
		</div>
		<div class="call-to-action">
			<div class="auto-container">
				<div class="wrapper-box">
					<div class="left-content">
					<?php if ( $bottom_option === 'yes' ) { ?>
						<h3><?php echo wp_kses_post( $sub_title ); ?></h3>
						<div class="text"><?php echo wp_kses_post( $sub_content ); ?></div>
						<?php } ?>
					</div>
					<?php if ( ! empty( $page_link_2 ) ) { ?>
					<div class="right-content">
						<a href="<?php echo esc_url( $page_link_2 ); ?>" class="theme-btn btn-style-five"><span><?php echo wp_kses_post( $button_name_2 ); ?></span></a>
					</div>
					<?php } ?>
				</div>
			</div>                
		</div>
	</section>
			<?php } ?>

				<?php if ( $select_layout == 'style_2' ) { ?>

			<!-- Feature Section Two -->
	<section class="feature-section-two">
		<div class="auto-container">
			<div class="sec-title text-center light style-two">
					<?php if ( ! empty( $icon ) ) { ?>
				<div class="icon-box"><span class="<?php echo $icon['value']; ?>"></span></div>
				<?php } ?>
				<h5><?php echo wp_kses_post( $tagline ); ?></h5>
				<h1><?php echo wp_kses_post( $title ); ?></h1>
			</div>
			<div class="row">
					<?php
					foreach ( $feature_list as $feature ) {
						$feature_title       = $feature['title'];
						$feature_image     = ($feature['block_image']['id'] != '' ) ? wp_get_attachment_url( $feature['block_image']['id'], 'full' ) : $feature['block_image']['url'];
				        $feature_icon     = ($feature['block_icon']['id'] != '' ) ? wp_get_attachment_url( $feature['block_icon']['id'], 'full' ) : $feature['block_icon']['url'];
						$feature_content     = $feature['content'];
						$feature_button_name = $feature['button_name'];
						$feature_page_link =   $feature['page_link']['url'];
						?>
				<!-- Feature Block Two -->
				<div class="col-lg-3 col-md-6 feature-block-four">
					<div class="inner-box">
						<div class="icon-box">
							<img src="<?php echo esc_url( $feature_icon ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
						</div>
						<h4><?php echo wp_kses_post( $feature_title ); ?></h4>
						<div class="overlay" style="background-image: url(<?php echo esc_url( $feature_image ); ?>);">
							<h4><?php echo wp_kses_post( $feature_title ); ?></h4>
							<div class="text"><?php echo wp_kses_post( $feature_content ); ?></div>
							<?php if ( ! empty( $feature_page_link ) ) { ?>
							<div class="link-btn"><a href="<?php echo esc_url( $feature_page_link ); ?>" class="theme-btn btn-style-five"><span><?php echo wp_kses_post( $feature_button_name ); ?></span></a></div>
							<?php } ?>
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

						\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Feature() );
