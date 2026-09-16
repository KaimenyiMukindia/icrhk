<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
class GoodSoul_Footer_About extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_footer_about';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Footer About ', 'goodsoul-core' );
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
				'label' => esc_html__( 'Footer About', 'goodsoul-core' ),
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
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'About' ),
				'condition' => array( 'select_layout' => array('style_1','style_3') ),
			)
        );
        $this->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'The great explorer of the truth, the master builders off human happiness no one rejects, dislikes, or avoids all pleasures itself, because it pleasures rationally encounter.' ),
			)
        );

		$this->end_controls_section();
		$this->start_controls_section(
			'about_content',
			array(
				'label' => esc_html__( 'Our Contact', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => array( 'select_layout' => 'style_3' ),
			)
		);
		$repeater = new \Elementor\Repeater();

		$this->add_control(
			'contact_main',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Contact Us' ),
				
			)
        );
		$repeater->add_control(
			'contact_title',
			array(
				'label'   => esc_html__( 'Contact Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Address:' ),
			)
		);
		$repeater->add_control(
			'contact_info',
			array(
				'label'   => esc_html__( 'Contact', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( '+211 456 789' ),
			)
		);
        $repeater->add_control(
			'contact_url',
			array(
				'label'         => esc_html__( 'Contact link', 'goodsoul-core' ),
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
			'contacts',
			array(
				'label'     => __( 'Contact List', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::REPEATER,
				'fields'    => $repeater->get_controls(),
				'default'   => array(
					array(
						'list_title'   => __( 'Contact #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Contact #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
        );
		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$title           = $settings['title'];
		$content       = $settings['content'];
		$contact_main       = $settings['contact_main'];
		$contacts       = $settings['contacts'];
		$select_layout   = $settings['select_layout'];
		$logo_Image    = goodsoul_get_options( 'footer_logo_image' );
 		$footer_logo_image    = $logo_Image['url'];
		$goodsoul_theme_metabox_footer_logo = get_post_meta(get_queried_object_id(), 'goodsoul_theme_metabox_footer_logo', array( 'size' => 'full' ));
		if(!empty($goodsoul_theme_metabox_footer_logo))
		{
			$footer_logo_image = wp_get_attachment_url( $goodsoul_theme_metabox_footer_logo);
		}
		$copyright_text    = goodsoul_get_options( 'footer_copyright' );
		?>
		<?php if ( $select_layout == 'style_1' ) { ?>
            <div class="about-widget-two footer-widget">
                <h4 class="widget-title"><?php echo $title; ?></h4>
                <div class="text"><?php echo $content; ?></div>
                <div class="logo"><a href="<?php echo get_home_url(); ?>"><img src="<?php echo esc_url($footer_logo_image);?>" alt=""></a></div>
			</div>
		<?php } elseif ( $select_layout == 'style_2' ) { ?>
			<div class="about-widget-three footer-widget">
				<div class="logo"><a href="<?php echo get_home_url(); ?>"><img src="<?php echo esc_url($footer_logo_image);?>" alt=""></a></div>
					<div class="text"><?php echo $content; ?></div>
				<div class="copyright-text"><span class="flaticon-heart-2"></span><?php echo $copyright_text; ?></div>
			</div>
		<?php } elseif ( $select_layout == 'style_3' ) { ?>
			<div class="about-widget-five footer-widget">
                        <h4 class="widget-title"><?php echo $title; ?></h4>
                        <div class="text"><?php echo $content; ?></div>
                        <h4 class="widget-title"><?php echo $contact_main; ?></h4>
                        <ul>
						<?php
						foreach ( $contacts as $contact ) {
							$contact_title    = $contact['contact_title'];
							$contact_info      = $contact['contact_info'];
							$contact_url  = $contact['contact_url']['url'];
							?>
                            <li><strong><?php echo $contact_title; ?></strong> <a href="<?php echo esc_url($contact_url); ?>"><?php echo $contact_info; ?></a></li>
                            <?php } ?>
                        </ul>
                    </div>
		<?php } ?>
		<?php
	}
}

\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Footer_About() );
