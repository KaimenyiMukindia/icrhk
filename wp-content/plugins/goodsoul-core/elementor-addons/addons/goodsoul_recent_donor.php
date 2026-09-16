<?php
class Goodsoul_Recent_donrs extends \Elementor\Widget_Base {

	public function get_name() {
		return 'good_recent_dons';
	}
	public function get_title() {
		return esc_html__( 'Goodsoul Recent Donors', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return [ 'goodsoulcore' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'donors',
			[
				'label' => esc_html__( 'Donors', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'text',
			array(
				'label'   => esc_html__( 'Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Indignation & dislike men who sed <br>are like beguiled.' ),
			)
		);

		$repeater->add_control(
			'image_don',
			array(
				'label'   => __( 'Donor Image', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);
		$repeater->add_control(
			'icon_top',
			array(
				'label'   => esc_html__( 'Top', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( '25%', 'goodsoul-core' ),
			)
		);
		$repeater->add_control(
			'icon_left',
			array(
				'label'   => esc_html__( 'Left', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( '30%', 'goodsoul-core' ),

			)
		);
		$repeater->add_control(
			'icon_right',
			array(
				'label'   => esc_html__( 'Right', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( '30%', 'goodsoul-core' ),

			)
		);

		$repeater->add_control(
			'icon_bottom',
			array(
				'label'   => esc_html__( 'Bottom', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( '30%', 'goodsoul-core' ),

			)
		);

		$this->add_control(
			'items',
			array(
				'label'   => __( 'Items', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(
						'list_title'   => __( 'Question #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Question #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
		);

		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();

		?>
<div class="recent-donars">
	<div class="wrapper-box">
		<?php
		foreach ( $settings['items'] as $icon_index => $item ) {
			$image_don   = ( $item['image_don']['id'] != '' ) ? wp_get_attachment_image_url( $item['image_don']['id'], 'full' ) : $item['image_don']['url'];
			$text        = $item['text'];
			$icon_top    = $item['icon_top'];
			$icon_right  = $item['icon_right'];
			$icon_left   = $item['icon_left'];
			$icon_bottom = $item['icon_bottom'];
			if ( ! empty( $icon_top ) ) {
				$this->add_render_attribute( 'icon-' . $icon_index, 'style', 'top:' . $icon_top . ';' );
			}

			if ( ! empty( $icon_right ) ) {
				$this->add_render_attribute( 'donor-' . $icon_index, 'style', 'right:' . $icon_right . ';' );
			}

			if ( ! empty( $icon_bottom ) ) {
				$this->add_render_attribute( 'donor-' . $icon_index, 'style', 'bottom:' . $icon_bottom . ';' );
			}

			if ( ! empty( $icon_left ) ) {
				$this->add_render_attribute( 'donor-' . $icon_index, 'style', 'left:' . $icon_left . ';' );
			}
			?>
		<div class="donor-thumb <?php echo $this->get_render_attribute_string( 'donor-' . $icon_index ); ?>">
			<div class="image"><img src="<?php echo esc_url( $image_don ); ?>" alt=""></div>
			<div class="content">
				<div class="text"><?php echo $text; ?></div>
			</div>
			<span class="point"></span>
		</div>
			<?php
		}
		?>
	</div>
</div>
		<?php
	}
}
\Elementor\Plugin::instance()->widgets_manager->register( new \Goodsoul_Recent_donrs() );
