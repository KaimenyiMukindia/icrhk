<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
use \Elementor\Icons_Manager;
class GoodSoul_Footer_Contact extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_footer_contact';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Footer Contact ', 'goodsoul-core' );
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
				'label' => esc_html__( 'Footer Contact', 'goodsoul-core' ),
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
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Contact' ),
			)
		);

		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'has_title',
			array(
				'label'   => esc_html__( 'Need A Title?', 'goodsoul-core' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);
		$repeater->add_control(
			'address_title',
			array(
				'label'   => esc_html__( 'Address Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Address:' ),
				'condition'    => array( 'has_title' => 'yes' ),
			)
		);
		$repeater->add_control(
			'address',
			array(
				'label'   => esc_html__( 'Address Details', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( '5404 Berrick Street,' ),
			)
		);
		$this->add_control(
			'adresses',
			array(
				'label'     => __( 'Address List', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::REPEATER,
				'fields'    => $repeater->get_controls(),
				'default'   => array(
					array(
						'list_title'   => __( 'Address #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Address #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
        );
        $repeater2 = new \Elementor\Repeater();
		$repeater2->add_control(
			'contact_style',
			array(
				'label'   => esc_html__( 'Link show Style', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'style_1' => esc_html__( 'Text', 'goodsoul-core' ),
					'style_2' => esc_html__( 'Icon', 'goodsoul-core' ),

				),
				'default' => esc_html__( 'style_1', 'goodsoul-core' ),
			)
		);
		$repeater2->add_control(
			'contact_info',
			array(
				'label'   => esc_html__( 'Contact', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( '+211 456 789' ),
				'condition'    => array( 'contact_style' => 'style_1' ),
			)
		);
		$repeater2->add_control(
			'icon',
			array(
				'label'     => __( 'Choose Icon', 'text-domain' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-star',
					'library' => 'solid',
				),
				'condition'    => array( 'contact_style' => 'style_2' ),
			)
		);
        $repeater2->add_control(
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
				'fields'    => $repeater2->get_controls(),
				'default'   => array(
					array(
						'list_title'   => __( 'Contact #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
		);
		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$select_layout   = $settings['select_layout'];
		$title           = $settings['title'];
        $adresses       = $settings['adresses'];
        $contacts       = $settings['contacts'];
		?>
		<?php if ( $select_layout == 'style_1' ) { ?>
            <div class="contact-widget footer-widget">
                <h4 class="widget-title"><?php echo $title; ?></h4>
                <ul>
                    <?php
						foreach ( $adresses as $adresse ) {
							$myaddress    = $adresse['address'];
							$has_title      = $adresse['has_title'];
							$address_title  = $adresse['address_title'];
							?>
                    <li><?php if($has_title) { ?><strong><?php echo $address_title; ?></strong><?php } ?><?php echo $myaddress; ?></li>
                        <?php } ?>
                </ul>
                <?php
                foreach ( $contacts as $contact ) {
                    $contact_info    = $contact['contact_info'];
					$contact_url    = $contact['contact_url']['url'];
					$contact_style   	 = 		$contact['contact_style'];
					$icon   	 = 		$contact['icon'];
                    ?>
				<?php if($contact_style == 'style_1') { ?>
                <h3><a href="<?php echo esc_url($contact_url); ?>"><?php echo $contact_info; ?></a></h3>
                <?php }elseif($contact_style == 'style_2') { ?>
				<li><a href="<?php echo esc_url($contact_url); ?>"><?php Icons_Manager::render_icon( ( $icon ), array( 'aria-hidden' => 'true' ) ); ?></a></li>
				<?php }} ?>
            </div>
		<?php } elseif ( $select_layout == 'style_2' ) { ?>
			<div class="contact-widget footer-widget">
				<h4 class="widget-title"><?php echo $title; ?></h4>
				<ul>
				<?php
						foreach ( $adresses as $adresse ) {
							$myaddress    = $adresse['address'];
							$has_title      = $adresse['has_title'];
							$address_title  = $adresse['address_title'];
							?>
					<li><?php if($has_title) { ?><strong><?php echo $address_title; ?></strong><?php } ?><?php echo $myaddress; ?></li>
					<?php } ?>
				</ul>
				<ul class="social-icon-three">
				<?php
                foreach ( $contacts as $contact ) {
					$contact_info    = $contact['contact_info'];
					$contact_style   	 = 		$contact['contact_style'];
					$icon   	 = 		$contact['icon'];
                    $contact_url    = $contact['contact_url']['url'];
                    ?>
					<?php if($contact_style == 'style_1') { ?>
					<h3><a href="<?php echo esc_url($contact_url); ?>"><?php echo $contact_info; ?></a></h3>
					<?php }elseif($contact_style == 'style_2') { ?>
					<li><a href="<?php echo esc_url($contact_url); ?>"><?php Icons_Manager::render_icon( ( $icon ), array( 'aria-hidden' => 'true' ) ); ?></a></li>
					<?php }} ?>
				</ul>
			</div>
		<?php } ?>
		<?php
	}
}

\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Footer_Contact() );
