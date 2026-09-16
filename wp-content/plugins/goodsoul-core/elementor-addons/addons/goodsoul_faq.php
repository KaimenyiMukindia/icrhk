<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
use \Elementor\Icons_Manager;

class GoodSoul_Faq extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_faq';
	}
	public function get_title() {
		return esc_html__( 'Goodsoul FAQ', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'general',
			array(
				'label' => esc_html__( 'General', 'goodsoul-core' ),
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
			'contact_content',
			array(
				'label'     => esc_html__( 'Cootact Content', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'condition' => array( 'select_layout' => 'style_2' ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'     => esc_html__( 'Title', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => __( 'Your donation to this fund <br>will help stop the virus s spread' ),
				'condition' => array( 'select_layout' => 'style_2' ),
			)
		);
		$this->add_control(
			'sub_title',
			array(
				'label'     => esc_html__( 'Sub Title', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => __( '“Generosity consists not of the sum given, but the manner in  <br>which it is bestowed.”' ),
				'condition' => array( 'select_layout' => 'style_2' ),
			)
		);
		$this->add_control(
			'description',
			array(
				'label'     => esc_html__( 'Description', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => __( 'How all this mistaken idea of denouncing pleasure and praising pain was born <br>and we will give you a complete account of the system.' ),
				'condition' => array( 'select_layout' => 'style_2' ),
			)
		);
		$this->add_control(
			'image',
			array(
				'label'     => __( 'Image', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'select_layout' => 'style_2' ),
			)
		);
		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'right_title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Give Donation' ),
			)
		);

		$repeater->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Content', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Which is the same as saying through shrinking from distinguish.' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'     => __( 'Item List', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::REPEATER,
				'fields'    => $repeater->get_controls(),
				'default'   => array(
					array(
						'list_title'   => __( 'Question #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Question #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
				'condition' => array( 'select_layout' => 'style_2' ),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'faq_content',
			array(
				'label' => esc_html__( 'FAQ', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'heading',
			array(
				'label'   => esc_html__( 'Heading', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Be part of a change <br> you want to see in the world' ),
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'question',
			array(
				'label'   => esc_html__( 'Question', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'How have you selected your top charities?' ),
			)
		);

		$repeater->add_control(
			'answer',
			array(
				'label'   => esc_html__( 'Answer', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'We denounce with righteous indignation and dislike men who are so beguiled and demoralized by the charms of pleasure of the moment, so blinded by desire, that they cannot foresee the pain and trouble that are bound to ensue.' ),
			)
		);

		$repeater->add_control(
			'is_active',
			[
				'label'        => __( 'Is Active??', 'goodsoul-core' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 0,

			]
		);

		$this->add_control(
			'faqs',
			array(
				'label'   => __( 'faqs', 'goodsoul-core' ),
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
		$settings      = $this->get_settings_for_display();
		$select_layout = $settings['select_layout'];
		$faqs          = $settings['faqs'];
		$items         = $settings['items'];
		if ( $select_layout == 'style_2' ) :

			$heading         = $settings['heading'];
			$contact_content = $settings['contact_content'];
			$title           = $settings['title'];
			$sub_title       = $settings['sub_title'];
			$description     = $settings['description'];

			$image = ( $settings['image']['id'] != '' ) ? wp_get_attachment_image_url( $settings['image']['id'], 'full' ) : $settings['image']['url'];
			if ( ! empty( $image ) ) {
				$this->add_render_attribute( 'image', 'src', $image );
				$this->add_render_attribute( 'image', 'alt', \Elementor\Control_Media::get_image_alt( $settings['image'] ) );
				$this->add_render_attribute( 'image', 'title', \Elementor\Control_Media::get_image_title( $settings['image'] ) );
				$image_html = \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, 'full', 'image' );
			}
		endif;
		?>

	<!--Faq section-->
		<?php if ( $select_layout == 'style_1' ) : ?>
	<section class="faq-section">
		<div class="auto-container">
			<div class="faq-content-box">
				<div class="accordion-box">
				<?php
				foreach ( $faqs as $faq ) {
					$question      = $faq['question'];
					$answer        = $faq['answer'];
					$is_active     = $faq['is_active'];
					$area_seelcted = '';
					$active_class  = '';
					if ( $is_active == 'yes' ) {
						  $active_class  = 'active';
						  $area_seelcted = 'collapsed';
					}
					?>
					<!--Start single accordion box-->
					<div class="accordion accordion-block <?php echo esc_attr( $active_class ); ?>">
						<div class="accord-btn"><span class="count"></span><h4><?php echo wp_kses_post( $question ); ?></h4></div>
						<div class="accord-content <?php echo esc_attr( $area_seelcted ); ?>">
							<div class="text"><?php echo wp_kses_post( $answer ); ?></div>
						</div>
					</div>
					<!--End single accordion box-->
				<?php } ?>
				</div>    
			</div>
		</div>
	</section>
		<?php elseif ( $select_layout == 'style_2' ) : ?>
	<section class="about-section-three style_new_one">
		<div class="auto-container">
			<div class="row">
				<div class="col-lg-6">
					<div class="image-box">
						<div class="image"><?php echo $image_html; ?></div>
						<div class="contact-box">
							<div class="icon-box"><span class="flaticon-phone-1"></span></div>
							<?php echo $contact_content; ?>
						</div>
					</div>
			
					<div class="content">
						<h1><?php echo $title; ?></h1>
						<h4><?php echo $sub_title; ?></h4>
						<div class="text"><?php echo $description; ?></div>
						<?php
						foreach ( $items as $item ) {
							$right_title = $item['right_title'];
							$content     = $item['content'];
							?>
						<div class="point-block">
							<span class="flaticon-tick"></span>
							<h4><?php echo $right_title; ?></h4>
							<div class="text"><?php echo $content; ?></div>
						</div>
						<?php } ?>
						
					</div>
				</div>
		  
				<div class="col-lg-6">
					<div class="content">
					<h1><?php echo $heading; ?></h1>
					</div>

					<div class="faq-content-box">
						<div class="accordion-box">
						<?php
						$count = 1;
						foreach ( $faqs as $faq ) {
							$question        = $faq['question'];
							$answer          = $faq['answer'];
							$active_class    = ( $count == 1 ) ? 'active' : '';
							$collapsed_class = ( $count == 1 ) ? 'collapsed' : '';

							?>
						
							<!--Start single accordion box-->
							<div class="accordion accordion-block <?php echo $active_class; ?>">
								<div class="accord-btn"><span class="count"></span><h4><?php echo wp_kses_post( $question ); ?></h4></div>
								<div class="accord-content <?php echo $collapsed_class; ?>">
									<div class="text"><?php echo wp_kses_post( $answer ); ?></div>
								</div>
							</div>
							
							<!--End single accordion box-->
					<?php $count++;} ?>
							
						</div>    
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>
							<?php
	}
}

						\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Faq() );
