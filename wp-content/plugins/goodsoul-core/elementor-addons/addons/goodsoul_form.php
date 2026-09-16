<?php
/**
 * Elementor donors list Loveus_sponsors__o
 *
 * @since 1.0.0
 */

use \Elementor\Repeater;
use \Elementor\Icons_Manager;

class GoodSoul_Form extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_form';
	}
	public function get_title() {
		return esc_html__( 'Goodsoul Contact Form', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'forms',
			array(
				'label' => esc_html__( 'Forms', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'title_contact_file',
			array(
				'label'   => esc_html__( 'Purpose ', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Donation' ),
			)
		);

		$this->add_control(
			'cf7',
			array(
				'label'       => esc_html__( 'Select Contact Form', 'goodsoul-core' ),
				'description' => esc_html__( 'Contact form 7 - plugin must be installed and there must be some contact forms made with the contact form 7', 'goodsoul-core' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'multiple'    => false,
				'label_block' => 1,
				'options'     => get_contact_form_7_posts(),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'contact_info',
			array(
				'label' => esc_html__( 'Contact Info', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title_2',
			array(
				'label'   => esc_html__( 'Button Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Global HQ' ),
			)
		);
		$repeater->add_control(
			'address',
			array(
				'label'   => esc_html__( 'Address', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( '5404 Berrick Street, 2nd cross str, Boston, MA 02115.' ),
			)
		);

		$repeater->add_control(
			'link_name',
			array(
				'label'   => esc_html__( 'Link Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Your Nearest Location' ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'         => esc_html__( 'Link', 'goodsoul-core' ),
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
			'contact_list',
			array(
				'label'   => esc_html__( 'Contact List', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
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

		$settings = $this->get_settings();
		// to show on the fontend
		static $v_veriable = 0;
		$title_contact_file             = $settings['title_contact_file'];
		$contact_list             = $settings['contact_list'];
		?>
		    <section class="contact-form-section">
        <div class="auto-container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="default-form-area">
                        <div class="sec-title">
                            <h1><?php echo wp_kses_post( $title_contact_file ); ?></h1>
                        </div>
						<?php
						if ( ! empty( $settings['cf7'] ) ) {
							echo '<div class="elementor-shortcode dexencore-cf7-' . $v_veriable . '">';
								echo do_shortcode( '[contact-form-7 id="' . $settings['cf7'] . '"]' );
							echo '</div>';
						}

						if ( ! empty( $settings['cf7_redirect_page'] ) || ! empty( $settings['cf7_redirect_external'] ) ) {
							?>
							<script>
									var theform = document.querySelector('.dexencore-cf7-<?php echo $v_veriable; ?>');
										theform.addEventListener( 'wpcf7mailsent', function( event ) {
										location = '
										<?php
										if ( ! empty( $settings['cf7_redirect_external'] ) ) {
											echo $settings['cf7_redirect_external'];
										} else {
											echo get_permalink( $settings['cf7_redirect_page'] );
										}
										?>
										';
									}, false );
							</script>

							<?php
							$v_veriable++;
						}
						?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="contact-info-three">
						<?php foreach($contact_list as $contact) {
							$title_2 = $contact['title_2'];
							$address = $contact['address'];
							$link_name = $contact['link_name'];
							$link = $contact['link']['url'];
							?>
                        <div class="single-info">
                            <h4><?php echo wp_kses_post( $title_2 ); ?></h4>
							<div class="text"><?php echo wp_kses_post( $address ); ?></div>
							<?php if(!empty($link)) { ?>
							<a class="link-btn" href="<?php echo esc_url( $link ); ?>"><?php echo wp_kses_post( $link_name ); ?></a> 
							<?php }?>                           
						</div>
						<?php }?>
                    </div>
                </div>
            </div>                    
        </div>
    </section>

	
		<?php
	}
}
\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Form() );
