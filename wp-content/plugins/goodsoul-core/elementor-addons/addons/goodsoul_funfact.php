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

class GoodSoul_Funfact extends Widget_Base {


	public function get_name() {
		return 'goodsoul_funfact';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Funfact', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	public function get_script_depends() {
		return array( 'fun-fact' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'aboutme_content',
			array(
				'label' => esc_html__( 'Funfact', 'goodsoul-core' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'select_layout',
			array(
				'label'   => esc_html__( 'Select Layout', 'goodsoul-core' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'style_1' => esc_html__( 'Style 1', 'goodsoul-core' ),
					'style_2' => esc_html__( 'Style 2', 'goodsoul-core' ),
					'style_3' => esc_html__( 'Style 3', 'goodsoul-core' ),
					'style_4' => esc_html__( 'Style 4', 'goodsoul-core' ),
				),
				'default' => esc_html__( 'style_1', 'goodsoul-core' ),
			)
		);

		$this->add_control(
			'select_layout_cla',
			array(
				'label'     => esc_html__( 'Select Layout class', 'goodsoul-core' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'style_cal_1' => esc_html__( 'Style 1', 'goodsoul-core' ),
					'style_cal_2' => esc_html__( 'Style 2', 'goodsoul-core' ),
				),
				'default'   => esc_html__( 'style_cal_1', 'goodsoul-core' ),
				'condition' => array( 'select_layout' => 'style_2' ),
			)
		);

		$this->add_control(
			'bg_image',
			array(
				'label'      => __( 'Background Image', 'goodsoul-core' ),
				'type'       => Controls_Manager::MEDIA,
				'default'    => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'select_layout',
							'operator' => '==',
							'value'    => 'style_2',
						),
						array(
							'name'     => 'select_layout',
							'operator' => '==',
							'value'    => 'style_4',
						),
					),
				),
			)
		);
		$this->add_control(
			'funfact_image_1',
			array(
				'label'     => __( 'Funfact Image 1', 'goodsoul-core' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'select_layout' => 'style_4' ),
			)
		);
		$this->add_control(
			'funfact_image_2',
			array(
				'label'     => __( 'Funfact Image 2', 'goodsoul-core' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'select_layout' => 'style_4' ),
			)
		);
		$this->add_control(
			'funfact_image_3',
			array(
				'label'     => __( 'Funfact Image 3', 'goodsoul-core' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'select_layout' => 'style_4' ),
			)
		);

		$this->add_control(
			'before_image',
			array(
				'label'     => esc_html__( 'Before Image', 'goodsoul-core' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'selectors' => array(
					'{{WRAPPER}} .funfacts-section-two.style-three:before' => 'background: url({{URL}})',

				),
				'condition' => array( 'select_layout' => 'style_3' ),

			)
		);

		$this->add_control(
			'section_color',
			array(
				'label'     => __( 'Background Color', 'cameron-core' ),
				'separator' => 'before',
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .funfacts-section-two.style-three:before' => 'background-color: {{VALUE}}',

				),
				'condition' => array( 'select_layout' => 'style_3' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Numbers speaking' ),
			)
		);
		$this->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Text', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Love learning about crazy facts? Then read these amazing facts that will tickle your brain.' ),
			)
		);
		$this->add_control(
			'content_2',
			array(
				'label'     => esc_html__( 'Last Summary', 'goodsoul-core' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => __( 'Indignation and dislike men who are so beguiled and demoralized by the charms of pleasure of the <br> moment, so blinded by desire, that they cannot foresee.' ),
				'condition' => array( 'select_layout' => 'style_1' ),
			)
		);
		$this->add_control(
			'button_name',
			array(
				'label'     => esc_html__( 'Button Name', 'goodsoul-core' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Donate Now' ),
				'condition' => array( 'select_layout' => 'style_1' ),
			)
		);
		$this->add_control(
			'button_link',
			array(
				'label'         => esc_html__( 'Button link', 'goodsoul-core' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
				'condition'     => array( 'select_layout' => 'style_1' ),
			)
		);
		$repeater = new Repeater();
		$repeater->add_control(
			'icon_image',
			array(
				'label'   => __( 'Icon image', 'goodsoul-core' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);
		$repeater->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Beneficiaries' ),
			)
		);
		$repeater->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Text', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Over 25600 directly beneficiaries of our foundation programme.' ),
			)
		);

		goodsoul_get_animation_control( $repeater );

		$repeater->start_controls_tabs(
			'action_button_tabs'
		);
		$repeater->start_controls_tab(
			'one',
			array(
				'label' => __( 'Prefix', 'goodsoul-core' ),
			)
		);
		$repeater->add_control(
			'prefix',
			array(
				'label'   => esc_html__( 'Prefix', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( '$', 'goodsoul-core' ),
			)
		);
		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'two',
			array(
				'label' => __( 'Data', 'goodsoul-core' ),
			)
		);
		$repeater->add_control(
			'data',
			array(
				'label'   => esc_html__( 'Data', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( '2.6', 'goodsoul-core' ),
			)
		);
		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'three',
			array(
				'label' => __( 'Affix', 'goodsoul-core' ),
			)
		);
		$repeater->add_control(
			'affix',
			array(
				'label'   => esc_html__( 'Affix', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'M', 'goodsoul-core' ),
			)
		);
		$repeater->end_controls_tab();

		$this->add_control(
			'funfact_list',
			array(
				'label'     => esc_html__( 'Funfact List', 'goodsoul-core' ),
				'type'      => Controls_Manager::REPEATER,
				'fields'    => $repeater->get_controls(),
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
				'condition' => array(
					'select_layout' => 'style_1',
				),

			)
		);

		$repeater_2 = new Repeater();
		$repeater_2->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Text', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Over 25600 directly beneficiaries of our foundation programme.' ),
			)
		);
		goodsoul_get_animation_control( $repeater_2 );
		$repeater_2->start_controls_tabs(
			'action_button_tabs'
		);
		$repeater_2->start_controls_tab(
			'one',
			array(
				'label' => __( 'Prefix', 'goodsoul-core' ),
			)
		);
		$repeater_2->add_control(
			'prefix',
			array(
				'label'   => esc_html__( 'Prefix', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( '$', 'goodsoul-core' ),
			)
		);
		$repeater_2->end_controls_tab();

		$repeater_2->start_controls_tab(
			'two',
			array(
				'label' => __( 'Data', 'goodsoul-core' ),
			)
		);
		$repeater_2->add_control(
			'data',
			array(
				'label'   => esc_html__( 'Data', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( '2.6', 'goodsoul-core' ),
			)
		);
		$repeater_2->end_controls_tab();

		$repeater_2->start_controls_tab(
			'three',
			array(
				'label' => __( 'Affix', 'goodsoul-core' ),
			)
		);
		$repeater_2->add_control(
			'affix',
			array(
				'label'   => esc_html__( 'Affix', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'M', 'goodsoul-core' ),
			)
		);
		$repeater_2->end_controls_tab();

		$this->add_control(
			'funfact_list_2',
			array(
				'label'     => esc_html__( 'Funfact List', 'goodsoul-core' ),
				'type'      => Controls_Manager::REPEATER,
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
				'condition' => array( 'select_layout' => 'style_2' ),
			)
		);

		$repeater_3 = new Repeater();
		$repeater_3->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Beneficiaries' ),
			)
		);
		$repeater_3->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Text', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Over 25600 directly beneficiaries of our foundation programme.' ),
			)
		);
		$repeater_3->start_controls_tabs(
			'action_button_tabs'
		);
		$repeater_3->start_controls_tab(
			'one',
			array(
				'label' => __( 'Prefix', 'goodsoul-core' ),
			)
		);
		$repeater_3->add_control(
			'prefix',
			array(
				'label'   => esc_html__( 'Prefix', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( '$', 'goodsoul-core' ),
			)
		);
		$repeater_3->end_controls_tab();

		$repeater_3->start_controls_tab(
			'two',
			array(
				'label' => __( 'Data', 'goodsoul-core' ),
			)
		);
		$repeater_3->add_control(
			'data',
			array(
				'label'   => esc_html__( 'Data', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( '2.6', 'goodsoul-core' ),
			)
		);
		$repeater_3->end_controls_tab();

		$repeater_3->start_controls_tab(
			'three',
			array(
				'label' => __( 'Affix', 'goodsoul-core' ),
			)
		);
		$repeater_3->add_control(
			'affix',
			array(
				'label'   => esc_html__( 'Affix', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'M', 'goodsoul-core' ),
			)
		);
		$repeater_3->end_controls_tab();

		goodsoul_get_animation_control( $repeater_3 );

		$this->add_control(
			'funfact_list_3',
			array(
				'label'      => esc_html__( 'Funfact List', 'goodsoul-core' ),
				'type'       => Controls_Manager::REPEATER,
				'fields'     => $repeater_3->get_controls(),
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
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'select_layout',
							'operator' => '==',
							'value'    => 'style_3',
						),
						array(
							'name'     => 'select_layout',
							'operator' => '==',
							'value'    => 'style_4',
						),
					),
				),
			)
		);

	}
	protected function render() {
		$settings          = $this->get_settings_for_display();
		$select_layout     = $settings['select_layout'];
		$title             = $settings['title'];
		$content           = $settings['content'];
		$bg_image          = isset( $settings['bg_image']['id'] ) ? wp_get_attachment_url( $settings['bg_image']['id'], 'full' ) : '#';
		$last_summary      = $settings['content_2'];
		$button_name       = $settings['button_name'];
		$button_link       = isset( $settings['button_link']['url'] ) ? $settings['button_link']['url'] : '#';
		$funfact_list      = $settings['funfact_list'];
		$funfact_list_2    = $settings['funfact_list_2'];
		$funfact_list_3    = $settings['funfact_list_3'];
		$select_layout_cla = $settings['select_layout_cla'];
		if ( $select_layout == 'style_4' ) :
			 $funfact_image_1 = ( $settings['funfact_image_1']['id'] != '' ) ? wp_get_attachment_image( $settings['funfact_image_1']['id'], 'full' ) : $settings['funfact_image_1']['url'];
			

			$funfact_image_2 = ( $settings['funfact_image_2']['id'] != '' ) ? wp_get_attachment_image( $settings['funfact_image_2']['id'], 'full' ) : $settings['funfact_image_2']['url'];
			

			$funfact_image_3 = ( $settings['funfact_image_3']['id'] != '' ) ? wp_get_attachment_image( $settings['funfact_image_3']['id'], 'full' ) : $settings['funfact_image_3']['url'];
			
	endif;
		?>
	 <!-- Funfact Section -->
		<?php if ( $select_layout == 'style_1' ) { ?>
	 <section class="funfacts-section">
		<div class="auto-container">
			<div class="sec-title text-center">
				<h1><?php echo wp_kses_post( $title ); ?></h1>
				<div class="text"><?php echo wp_kses_post( $content ); ?></div>
			</div>
			<div class="outer-box">
				<div class="funfact-wrapper row">
			<?php
			foreach ( $funfact_list as $funfact ) {
				$funfact_title        = $funfact['title'];
				$content              = $funfact['content'];
				$prefix               = $funfact['prefix'];
				$data                 = $funfact['data'];
				$affix                = $funfact['affix'];
				$animation_class      = $funfact['animation_class'];
				$animation_delay_time = $funfact['addon_animation_delay_time'];
				$icon_image           = ( $funfact['icon_image']['id'] != '' ) ? wp_get_attachment_url( $funfact['icon_image']['id'], 'full' ) : $funfact['icon_image']['url'];
				?>
					<!--Column-->
					<div class="col-lg-4 counter-block wow <?php echo esc_attr( $animation_class ); ?>" data-wow-delay="<?php echo esc_attr( $animation_delay_time ); ?>">
						<div class="inner-box">
							<div class="icon-box"><img src="<?php echo esc_url( $icon_image ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></div>
							<h4><?php echo wp_kses_post( $funfact_title ); ?></h4>
							<div class="count-box">
								<span class="prefix"><?php echo wp_kses_post( $prefix ); ?></span><span class="count-text" data-speed="3000" data-stop="<?php echo wp_kses_post( $data ); ?>"></span><span class="affix"><?php echo wp_kses_post( $affix ); ?></span>
							</div>
							<div class="text"><?php echo wp_kses_post( $content ); ?></div>
						</div>
					</div>
			<?php } ?>
				</div>
			</div>                
			<div class="bottom-content text-center">
				<div class="text"><?php echo wp_kses_post( $last_summary ); ?></div>
			<?php if ( ! empty( $button_link ) ) { ?>
				<div class="link-btn"><a href="" class="theme-btn btn-style-one donate-box-btn"><span><?php echo wp_kses_post( $button_name ); ?></span></a></div>
			<?php } ?>
			</div>
		</div>
	</section>
			<?php
		} elseif ( $select_layout == 'style_2' ) {
			$class_select_layout = 'funfacts-section-two';
			if ( $select_layout_cla == 'style_cal_1' ) {
				$class_select_layout = 'funfacts-section-two style-two';
			}
			?>
			<!-- Funfact Section -->
			<section class="<?php echo esc_attr( $class_select_layout ); ?>" style="background-image: url(<?php echo esc_url( $bg_image ); ?>);">
		<div class="auto-container">
			<div class="sec-title text-center light">
				<h5><?php echo wp_kses_post( $title ); ?></h5>
				<h1><?php echo wp_kses_post( $content ); ?></h1>
			</div>
			<div class="outer-box">
				<div class="funfact-wrapper row">
			<?php
			foreach ( $funfact_list_2 as $funfact ) {
				$content              = $funfact['content'];
				$prefix               = $funfact['prefix'];
				$data                 = $funfact['data'];
				$affix                = $funfact['affix'];
				$animation_class      = $funfact['animation_class'];
				$animation_delay_time = $funfact['addon_animation_delay_time'];
				?>
					<!--Column-->
					<div class="col-lg-4 counter-block-two wow <?php echo esc_attr( $animation_class ); ?>" data-wow-delay="<?php echo esc_attr( $animation_delay_time ); ?>">
						<div class="inner-box">
							<div class="count-box">
								<span class="prefix"><?php echo wp_kses_post( $prefix ); ?></span><span class="count-text" data-speed="3000" data-stop="<?php echo wp_kses_post( $data ); ?>"></span><span class="affix"><?php echo wp_kses_post( $affix ); ?></span>
							</div>
							<div class="text"><?php echo wp_kses_post( $content ); ?></div>
						</div>
					</div>
			<?php } ?>
				</div>
			</div>
		</div>
	</section>
				<?php } elseif ( $select_layout == 'style_3' ) { ?>
				<!-- Funfact Section -->
				<section class="funfacts-section-two style-three">
		<div class="auto-container">
			<div class="sec-title text-center light">
				<h5><?php echo wp_kses_post( $title ); ?></h5>
				<h1><?php echo wp_kses_post( $content ); ?></h1>
			</div>
			<div class="outer-box">
				<div class="funfact-wrapper row">
			<?php
			foreach ( $funfact_list_3 as $funfact ) {
				$funfact_title        = $funfact['title'];
				$content              = $funfact['content'];
				$prefix               = $funfact['prefix'];
				$data                 = $funfact['data'];
				$affix                = $funfact['affix'];
				$animation_class      = $funfact['animation_class'];
				$animation_delay_time = $funfact['addon_animation_delay_time'];
				?>
					<!--Column-->
					<div class="col-lg-4 counter-block-two wow <?php echo esc_attr( $animation_class ); ?>" data-wow-delay="<?php echo esc_attr( $animation_delay_time ); ?>">
						<div class="inner-box">
							<h4><?php echo wp_kses_post( $funfact_title ); ?></h4>
							<div class="count-box counted">
								<span class="prefix"><?php echo wp_kses_post( $prefix ); ?></span><span class="count-text" data-speed="3000" data-stop="<?php echo wp_kses_post( $data ); ?>"><?php echo wp_kses_post( $data ); ?></span><span class="affix"><?php echo wp_kses_post( $affix ); ?></span>
							</div>
							<div class="text"><?php echo wp_kses_post( $content ); ?></div>
						</div>
					</div>
			<?php } ?>
				</div>
			</div>
		</div>
	</section>
<?php } elseif ( $select_layout == 'style_4' ) { ?>
	<section class="funfacts-section-two style_new_one" style="background-image: url(<?php echo esc_url( $bg_image ); ?>);">
		<div class="image_1 fun_image">
					<?php echo $funfact_image_1; ?>	
		</div><div class="image_2 fun_image">
					<?php echo $funfact_image_2; ?>
		</div>
		<div class="auto-container">
			<div class="image_box text-center m-auto">
					<?php echo $funfact_image_3; ?>
			</div>
			<div class="sec-title text-center light">
				<h5><?php echo wp_kses_post( $title ); ?></h5>
				<h1><?php echo wp_kses_post( $content ); ?></h1>
			</div>
			<div class="outer-box">
				<div class="funfact-wrapper row">
					<!--Column-->
					<?php
					foreach ( $funfact_list_3 as $funfact ) {
						$funfact_title        = $funfact['title'];
						$item_content         = $funfact['content'];
						$prefix               = $funfact['prefix'];
						$data                 = $funfact['data'];
						$affix                = $funfact['affix'];
						$animation_class      = $funfact['animation_class'];
						$animation_delay_time = $funfact['addon_animation_delay_time'];
						?>
					<div class="col-lg-4 d-flex  counter-block-two wow <?php echo $animation_class; ?>" data-wow-delay="<?php echo $animation_delay_time; ?>">
						<div class="inner-box">
							<div class="count-box">
								<span class="prefix"></span><span class="affix"><?php echo wp_kses_post( $prefix ); ?></span><span class="count-text" data-speed="3000" data-stop="<?php echo wp_kses_post( $data ); ?>"></span><span class="affix"><?php echo wp_kses_post( $affix ); ?></span>
							</div>
							<div class="text"><?php echo wp_kses_post( $item_content ); ?></div>
						</div>
					</div>
					 <!--Column-->
					 <?php } ?>
				   
				</div>
			</div>
		</div>
	</section>
			<?php } ?>
	
		<?php
	}
}
Plugin::instance()->widgets_manager->register( new \GoodSoul_Funfact() );
