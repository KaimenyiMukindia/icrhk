<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
class GoodSoul_Footer_Twitter extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_footer_twitter';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Footer Twitter ', 'goodsoul-core' );
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
				'label' => esc_html__( 'Footer Twitter', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'WorldWide Trends' ),
			)
        );
        $this->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Did You Know! Once you initiate a payment towards the <a href="#">#charity,</a> you will receive a confirmation email against that <a href="#">#payment</a>.' ),
			)
        );
        $this->add_control(
			'link_name',
			array(
				'label'   => esc_html__( 'Charity link Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'https://www.goodsoul/charity.com' ),
			)
        );
        $this->add_control(
			'my_link',
			array(
				'label'         => esc_html__( 'Charity link', 'goodsoul-core' ),
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
			'user_name',
			array(
				'label'   => esc_html__( 'User Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Mark Richarson' ),
			)
        );
        $this->add_control(
			'account',
			array(
				'label'   => esc_html__( 'Account Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( '@caregiver' ),
			)
        );
        $this->add_control(
			'account_link',
			array(
				'label'         => esc_html__( 'Account link', 'goodsoul-core' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
        );

		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$title           = $settings['title'];
        $content       = $settings['content'];
        $link_name       = $settings['link_name'];
        $account       = $settings['account'];
        $user_name       = $settings['user_name'];
        $my_link       = $settings['my_link']['url'];
        $account_link       = $settings['account_link']['url'];
		?>
            <div class="twitter-widget footer-widget">
                <h4 class="widget-title-two"><?php echo $title; ?></h4>
                <div class="text"><?php echo $content; ?></div>
                <div class="link"><a href="<?php echo esc_url($my_link);?>"><?php echo $link_name; ?></a></div>
                <div class="twitter-portfolio">
                <div class="icon"><span class="fa fa-twitter"></span></div>
                    <h4><?php echo $user_name; ?></h4>
                <a href="<?php echo esc_url($account_link);?>" class="user"><?php echo $account; ?></a>
                </div>
            </div>
		<?php
	}
}

\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Footer_Twitter() );
