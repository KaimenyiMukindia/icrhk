<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
use \Elementor\Icons_Manager;
class GoodSoul_Caring extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_caring';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Caring', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'caring_content',
			array(
				'label' => esc_html__( 'Caring', 'goodsoul-core' ),
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
		$repeater = new Repeater();
		$repeater->add_control(
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
		$repeater->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Are you healthy & happy? <br>make these too' ),
			)
		);
		$repeater->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'How all this mistaken idea denouncing pleasure and praising pain was born and we will give you a complete.' ),
			)
		);
		$repeater->add_control(
			'btn_name',
			array(
				'label'   => esc_html__( 'Button Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Join With Us' ),
			)
		);
		$repeater->add_control(
			'btn_link',
			array(
				'label'         => esc_html__( 'Button Link', 'goodsoul-core' ),
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
			'caring_list',
			array(
				'label'   => esc_html__( 'Caring List', 'goodsoul-core' ),
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
				),
			)
		);

	}
	protected function render() {
		$settings           = $this->get_settings_for_display();
		$caring_list        = $settings['caring_list'];
		$select_layout      = $settings['select_layout'];
		$coloumn_class      = '';
		$number_of_coloumns = $settings['number_of_coloumns'];
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
		$section_class = ( $select_layout == 'style_3' ) ? 'style_new_one' : '';
		?>
		<?php if ( $select_layout == 'style_1' || $select_layout == 'style_3' ) { ?>
	<!-- Caring Section -->
	<section class="caring-section <?php echo $section_class; ?>">
		<div class="auto-container">
			<div class="wrapper-box">
				<div class="row">
				<?php
				foreach ( $caring_list as $caring ) {
					$icon  = $caring['icon'];
					$title = $caring['title'];
					?>
					<div class="<?php echo $coloumn_class; ?> caring-block">
						<div class="inner-box">
							<div class="icon-box">
								<span class="<?php echo $icon['value']; ?>"></span>
							</div>
							<h4><?php echo wp_kses_post( $title ); ?></h4>
						</div>
					</div>
				<?php } ?>
				</div>
			</div>
		</div>
	</section>
	<?php } elseif ( $select_layout == 'style_2' ) { ?>
		<section class="caring-section">
			<div class="auto-container">
				<div class="caring-outer">
					<div class="row">
					<?php
					foreach ( $caring_list as $caring ) {
						$icon     = $caring['icon'];
						$title    = $caring['title'];
						$content  = $caring['content'];
						$btn_name = $caring['btn_name'];
						$btn_link = $caring['btn_link']['url'];
						?>
						<div class="<?php echo $coloumn_class; ?> caring-block-two">
							<div class="inner-box">
								<div class="icon-box">
									<?php Icons_Manager::render_icon( ( $icon ), array( 'aria-hidden' => 'true' ) ); ?>
								</div>
								<h4><?php echo $title; ?></h4>
								<div class="text"><?php echo $content; ?></div>
								<div class="link-btn"><a href="<?php echo esc_url( $btn_link ); ?>" class="theme-btn"><?php echo $btn_name; ?></a></div>
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

\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Caring() );
