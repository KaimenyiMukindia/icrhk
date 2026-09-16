<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Plugin;
class GoodSoul_Footer_Map extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_footer_map';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Footer Map ', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'aboutme_content',
			array(
				'label' => esc_html__( 'Footer Map', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Our Offices' ),
			)
        );
        $this->add_control(
			'btn_name',
			array(
				'label'   => esc_html__( 'Button Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'CONTACT US' ),
			)
        );
        $this->add_control(
			'btn_link',
			array(
				'label'         => esc_html__( 'Button link', 'goodsoul-core' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'label' => __( 'Background', 'goodsoul-core' ),
                'types' => [ 'classic'],
                'selector' => '{{WRAPPER}} .wrapper-box:before',
            ]
        );
        $repeater = new Repeater();
					
            
                    $repeater->add_control(
                        'item_title',
                        [
                        'label' => esc_html__( 'Title', 'goodsoul-core' ),
                        'type' => Controls_Manager::TEXT,
                        'default' => __( '54 Berrick Street, <br>Boston 02115.', 'goodsoul-core' ),
                        ]
                    );
                    $repeater->start_controls_tabs(
                        'action_button_tabs',
                        array(
                            'label'     => __( 'Position', 'goodsoul-core' ),
                            'separator' => 'before',
                        )
                    );
                    $repeater->start_controls_tab(
                        'one',
                        array(
                            'label' => __( 'Top', 'goodsoul-core' ),
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
                    $repeater->end_controls_tab();
            
                    $repeater->start_controls_tab(
                        'three',
                        array(
                            'label' => __( 'Left', 'goodsoul-core' ),
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
                    $repeater->end_controls_tab();
            
                    $repeater->start_controls_tab(
                        'two',
                        array(
                            'label' => __( 'Right', 'goodsoul-core' ),
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
            
                    $repeater->end_controls_tab();
            
                    $repeater->start_controls_tab(
                        'four',
                        array(
                            'label' => __( 'bottom', 'goodsoul-core' ),
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
                    $repeater->end_controls_tab();
            
                    $repeater->end_controls_tabs();

                    $this->add_control(
                    'items',
                    [
                        'label' => esc_html__( 'Repeater List', 'goodsoul-core' ),
                        'type' => Controls_Manager::REPEATER,
                        'fields' => $repeater->get_controls(),
                        'default' => [
                        [
                            'list_title' => esc_html__( 'Title #1', 'goodsoul-core' ),
                            'list_content' => esc_html__( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
                        ],
                        [
                            'list_title' => esc_html__( 'Title #2', 'goodsoul-core' ),
                            'list_content' => esc_html__( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
                        ],
                        ],
                    ]
                    );
	$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$title           = $settings['title'];
        $btn_name       = $settings['btn_name'];
        $btn_link       = $settings['btn_link']['url'];
        $footer_logo_image    = goodsoul_get_options( 'footer_logo_image' );
		?>
            <div class="office-location-widget footer-widget">
                <h4 class="widget-title"><?php echo $title; ?></h4>
                <div class="wrapper-box">
                <?php foreach($settings["items"] as $icon_index => $item){
                    $item_title = $item["item_title"];
                    $myclass    = str_replace( ' ', '_', strtolower( $item_title ) );
                    $icon_top                = $item['icon_top'];
                    $icon_right              = $item['icon_right'];
                    $icon_left               = $item['icon_left'];
                    $icon_bottom             = $item['icon_bottom'];
                    if ( ! empty( $icon_top ) ) {
                        $this->add_render_attribute( 'icon-' . $icon_index, 'style', 'top:' . $icon_top . ';' );
                    }

                    if ( ! empty( $icon_right ) ) {
                        $this->add_render_attribute( 'icon-' . $icon_index, 'style', 'right:' . $icon_right . ';' );
                    }

                    if ( ! empty( $icon_bottom ) ) {
                        $this->add_render_attribute( 'icon-' . $icon_index, 'style', 'bottom:' . $icon_bottom . ';' );
                    }

                    if ( ! empty( $icon_left ) ) {
                        $this->add_render_attribute( 'icon-' . $icon_index, 'style', 'left:' . $icon_left . ';' );
                    }
                        ?> 
                    <div class="location-point <?php echo $myclass; ?>">
                        <div class="content">
                            <div class="text"><?php echo $item_title; ?></div>
                        </div>
                        <span class="point"></span>
                    </div>
                    <?php } ?> 
                </div>
                <div class="link-btn"><a href="<?php echo esc_url($btn_link); ?>"><span class="flaticon-next"></span><?php echo $btn_name; ?></a></div>
            </div>
		<?php
	}
}

\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Footer_Map() );
