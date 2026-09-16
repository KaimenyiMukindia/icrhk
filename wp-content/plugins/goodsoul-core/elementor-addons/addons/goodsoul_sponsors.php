<?php

/**
 * Elementor donors list Loveus_sponsors__o
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
use \Elementor\Icons_Manager;

class GoodSoul_Sponsors extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_sponsors';
	}
	public function get_title() {
		return esc_html__( 'Goodsoul sponsors', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'mission_list',
			array(
				'label' => esc_html__( 'sponsors List', 'goodsoul-core' ),
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
					'style_4' => esc_html__( 'Style 4', 'goodsoul-core' ),

				),
				'default' => esc_html__( 'style_1', 'goodsoul-core' ),
			)
		);
		$this->add_control(
			'select_layout_class',
			array(
				'label'   => esc_html__( 'Select Layout class', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'style_class_1' => esc_html__( 'Style 1', 'goodsoul-core' ),
					'style_class_2' => esc_html__( 'Style 2', 'goodsoul-core' ),

				),
				'condition' => array( 'select_layout' => 'style_4' ),
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
				'condition' => array( 'select_layout' => 'style_3' ),
			)
		);
		$this->add_control(
			'tagline',
			array(
				'label'     => esc_html__( 'Tagline', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => __( 'Our Partners' ),
				'condition' => array(
					'select_layout' => array( 'style_2', 'style_3' ),
				),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'     => esc_html__( 'Title', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => __( 'Those who contributed to this <br>excellent service' ),
				'condition' => array(
					'select_layout' => array( 'style_2', 'style_3', 'style_4' ),
				),
			)
		);

		$this->add_control(
			'content',
			array(
				'label'     => esc_html__( 'Text', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => __( 'The following  list of people contributed their 100%  support to help our thousands of causes.' ),
				'condition' => array( 'select_layout' => 'style_4' ),
			)
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'mission_bottom_img',
			array(
				'label'   => esc_html__( 'sponsors image', 'plugin-domain' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);
		$repeater->add_control(
			'sponser_link',
			array(
				'label'         => esc_html__( 'Sponsor Page link', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::URL,
				'show_external' => true,
				'default' => array(
					'url'         => 'http://',
					'is_external' => '',
				),
			)
		);
		$this->add_control(
			'sponsors_list',
			array(
				'label'  => esc_html__( 'sponsors list', 'plugin-domain' ),
				'type'   => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default'   => array(
					array(
						'list_title'   => __( 'Sponser #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Sponser #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
		);

		
		$this->add_control(
			'footer',
			array(
				'label'     => esc_html__( 'Footer', 'goodsoul-core' ),
				'type'      => \Elementor\Controls_Manager::WYSIWYG,
				'default'   => __( 'To learn about becoming a partner, contact us at: <a href="mailto:support@goodsoul.com">support@goodsoul.com</a> or <a href="tel:+8889991234">1-888-999-1234</a>' ),
				'condition' => array(
					'select_layout' => array( 'style_2', 'style_3', 'style_4' ),
				),

			)
		);
		$this->end_controls_section();

	}
	protected function render() {
		$settings      = $this->get_settings_for_display();
		$select_layout = $settings['select_layout'];
		$sponsors_list = $settings['sponsors_list'];
		$tagline       = $settings['tagline'];
		$title         = $settings['title'];
		$content       = $settings['content'];
		$footer        = $settings['footer'];
		$icon          = $settings['icon'];
		$select_layout_class          = $settings['select_layout_class'];
		?>

					<!-- Client section -->
		<?php if ( $select_layout == 'style_1' ) { ?>
		<section class="client-section">
			<div class="auto-container">
				<div class="sponsors-carousel owl-carousel owl-theme owl-nav-none owl-dots-none">
				<?php if ( $sponsors_list ) : ?>
							<?php
							foreach ( $sponsors_list as $item ) {
								$image     = ($item['mission_bottom_img']['id'] != '' ) ? wp_get_attachment_url( $item['mission_bottom_img']['id'], 'full' ) : $item['mission_bottom_img']['url'];
								$link     = $item['sponser_link'];
								$url      = $link['url'];
								$target   = $link['is_external'] ? 'target="_blank"' : '';
								?>
					<div class="image" data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__('media partner', 'goodsoul-core'); ?>">
					<?php
							if ( $url ) {
								?>
							<a href="<?php echo $url; ?>"
								<?php
								if ( ! ( empty( $target ) ) ) {
									?>
					target="<?php echo $target; ?>" 
									<?php
								}
								?>><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></a></div>
					<?php }} ?>
							<?php endif; ?>
				</div>
			</div>
		</section>
		<?php } ?>

		<?php if ( $select_layout == 'style_2' ) { ?>
			<!-- Client section -->
	<section class="client-section style-two">
		<div class="auto-container">
			<div class="sec-title text-center">
				<h5><?php echo wp_kses_post( $tagline ); ?></h5>
				<h1><?php echo wp_kses_post( $title ); ?></h1>
			</div>
			<div class="sponsors-carousel owl-carousel owl-theme owl-nav-none owl-dots-none">
			<?php if ( $sponsors_list ) : ?>
							<?php
							foreach ( $sponsors_list as $item ) {
								$image     = ($item['mission_bottom_img']['id'] != '' ) ? wp_get_attachment_url( $item['mission_bottom_img']['id'], 'full' ) : $item['mission_bottom_img']['url'];
								$link     = $item['sponser_link'];
								$url      = $link['url'];
								$target   = $link['is_external'] ? 'target="_blank"' : '';
								?>
				<div class="image" data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__('media partner', 'goodsoul-core'); ?>">
				<?php
							if ( $url ) {
								?>
							<a href="<?php echo $url; ?>"
								<?php
								if ( ! ( empty( $target ) ) ) {
									?>
					target="<?php echo $target; ?>" 
									<?php
								}
								?>><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></a></div>
				<?php }} ?>
						<?php endif; ?>
			</div>
			<div class="text-two"><?php echo wp_kses_post( $footer ); ?></div>
		</div>
	</section>
			<?php } ?>

		<?php if ( $select_layout == 'style_3' ) { ?>
	 <!-- Client section -->
		<section class="client-section style-five">
		<div class="auto-container">
			<div class="sec-title text-center style-two">
			<?php if ( ! empty( $icon ) ) { ?>
				<div class="icon-box"><span class="<?php echo $icon['value']; ?>"></span></div>
				<?php } ?>
				<h5><?php echo wp_kses_post( $tagline ); ?></h5>
				<h1><?php echo wp_kses_post( $title ); ?></h1>
			</div>
			<div class="sponsors-carousel owl-carousel owl-theme owl-nav-none owl-dots-none">
			<?php if ( $sponsors_list ) : ?>
				<?php
				foreach ( $sponsors_list as $item ) {
					$image     = ($item['mission_bottom_img']['id'] != '' ) ? wp_get_attachment_url( $item['mission_bottom_img']['id'], 'full' ) : $item['mission_bottom_img']['url'];
					$link     = $item['sponser_link'];
					$url      = $link['url'];
					$target   = $link['is_external'] ? 'target="_blank"' : '';
					?>
				<div class="image" data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__('media partner', 'goodsoul-core'); ?>">
				<?php
							if ( $url ) {
								?>
							<a href="<?php echo $url; ?>"
								<?php
								if ( ! ( empty( $target ) ) ) {
									?>
					target="<?php echo $target; ?>" 
									<?php
								}
								?>><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></a></div>
				<?php }} ?>
				<?php endif; ?>
			</div>
			<div class="text-two"><?php echo wp_kses_post( $footer ); ?></div>
		</div>
	</section>
	
	<?php } ?>
		<?php
			if ( $select_layout == 'style_4' ) {
			$bg_class_client = '';
			if( $select_layout_class == 'style_class_2') {
				$bg_class_client = 'white-bg-color';
			}
		?>
		<!-- Client section -->
		<section class="client-section style-three <?php echo esc_attr($bg_class_client); ?>">
		<div class="auto-container">
			<div class="sec-title text-center">
				<h1><?php echo wp_kses_post( $title ); ?></h1>
				<div class="text"><?php echo wp_kses_post( $content ); ?></div>
			</div>
			<div class="sponsors-carousel owl-carousel owl-theme owl-nav-none owl-dots-none">
			<?php if ( $sponsors_list ) : ?>
							<?php
							foreach ( $sponsors_list as $item ) {
								$image     = ($item['mission_bottom_img']['id'] != '' ) ? wp_get_attachment_url( $item['mission_bottom_img']['id'], 'full' ) : $item['mission_bottom_img']['url'];
								$link     = $item['sponser_link'];
								$url      = $link['url'];
								$target   = $link['is_external'] ? 'target="_blank"' : '';
								?>
				<div class="image" data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__('media partner', 'goodsoul-core'); ?>">
				<?php
							if ( $url ) {
								?>
							<a href="<?php echo $url; ?>"
								<?php
								if ( ! ( empty( $target ) ) ) {
									?>
					target="<?php echo $target; ?>" 
									<?php
								}
								?>><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></a></div>
				<?php }} ?>
						<?php endif; ?>
			</div>
			<div class="text-two text-center"><?php echo wp_kses_post( $footer ); ?></div>
		</div>
	</section>
		<?php } ?>
		<?php
	}
}
\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Sponsors() );
