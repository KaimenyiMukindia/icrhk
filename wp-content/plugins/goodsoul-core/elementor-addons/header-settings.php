<?php

namespace SmartDataSoft\HeaderSettings;

use Elementor\Utils;

class headerSettings {

	public static function getHeaderSettings( $obj ) {
		$obj->add_control(
			'section_header_position',
			[
				'label' => esc_html__( 'Header text Position', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => esc_html__( 'Left', 'plugin-domain' ),
					'centered' => esc_html__( 'Center', 'plugin-domain' ),
					'right' => esc_html__( 'Right', 'plugin-domain' ),
				],
			]
		);
        $obj->add_control(
            'sub_title_section',
            [
                'label' => esc_html__( 'Section Sub-title', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Type your content here', 'plugin-domain' ),
            ]
        );
        $obj->add_control(
            'title_section',
            [
                'label' => esc_html__( 'Section title', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Type your content here', 'plugin-domain' ),
            ]
        );
        $obj->add_control(
            'content_section',
            [
                'label' => esc_html__( 'Section Content', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'placeholder' => esc_html__( 'Type your content here', 'plugin-domain' ),
            ]
        );
    
        $obj->add_control(
            'section_button',
            [
                'label' => esc_html__('Button Hide or Show', 'plugin-domain'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'plugin-domain'),
                'label_off' => __('Hide', 'plugin-domain'),
                'return_value' => 'no'
            ]
        );
        $obj->add_control(
            'button_title',
            [
                'label' => esc_html__( 'Button text', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Type your content here', 'plugin-domain' ),
                'condition' => [
                    'section_button' => 'no',
                ],
            ]
        );
        $obj->add_control(
            'button_link_section', [
                'label' => esc_html__( 'Button link', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::URL,
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                ],
                'condition' => [
                    'section_button' => 'no',
                ],
            ]
        );
        $obj->add_control(
            'section_button_two',
            [
                'label' => esc_html__('Button two Hide or Show', 'plugin-domain'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'plugin-domain'),
                'label_off' => __('Hide', 'plugin-domain'),
                'return_value' => 'no'
            ]
        );
        $obj->add_control(
            'button_title_two',
            [
                'label' => esc_html__( 'Button two text', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Type your content here', 'plugin-domain' ),
                'condition' => [
                    'section_button_two' => 'no',
                ],
            ]
        );
        $obj->add_control(
            'button_link_two', [
                'label' => esc_html__( 'Button two link', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::URL,
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                ],
                'condition' => [
                    'section_button_two' => 'no',
                ],
            ]
        );
	}

	public static function getHeaderInfo($settings) {
		$section_header_position = $settings['section_header_position'];
        $sub_title_section = $settings['sub_title_section'];
        $title_section = $settings['title_section'];
        $content_section = $settings['content_section'];
        $button_title = $settings['button_title'];
        $button_link_section_link = $settings['button_link_section']['url'];
        $target_section = $settings['button_link_section']['is_external'] ? ' target=_blank' : '';
        $nofollow_section = $settings['button_link_section']['nofollow'] ? ' rel=nofollow' : '';
        $button_title_two = $settings['button_title_two'];
        $button_link_two = $settings['button_link_two']['url'];
        $target_section_two = $settings['button_link_two']['is_external'] ? ' target=_blank' : '';
        $nofollow_section_two = $settings['button_link_two']['nofollow'] ? ' rel=nofollow' : '';
	?>
            <?php if($sub_title_section || $title_section || $content_section || $button_title) : ?>
				<div class="sec-title <?php echo esc_attr($section_header_position); ?>">
					<?php if($sub_title_section) : ?>
						<div class="sub-title">
							<?php echo wp_kses_post($sub_title_section); ?>
						</div>
					<?php endif;?>
					<?php if($title_section) : ?>
						<h2><?php echo wp_kses_post($title_section); ?></h2>
					<?php endif;?>
					<?php if($content_section) : ?>
						<div class="text"><?php echo wp_kses_post($content_section); ?></div>
					<?php endif;?>
					<?php if($button_title) : ?>
						<div class="link-box clearfix">
                            <?php if($button_title_two) : ?>
                                <a 
                                    class="theme-btn btn-style-three"
                                    href="<?php echo esc_url($button_link_two); ?>"
                                    <?php
                                        echo esc_attr($target_section_two . $nofollow_section_two);
                                    ?>>
                                    <span class="btn-title"><?php echo wp_kses_post($button_title_two); ?></span>
                                </a>
                            <?php endif;?>
							<a 
								class="theme-btn btn-style-one"
								href="<?php echo esc_url($button_link_section_link); ?>"
								<?php
									echo esc_attr($target_section . $nofollow_section);
								?>>
								<span class="btn-title"><?php echo wp_kses_post($button_title); ?></span>
							</a>
						</div>
					<?php endif;?>
				</div>
            <?php endif;?>
	<?php
			
		}
}