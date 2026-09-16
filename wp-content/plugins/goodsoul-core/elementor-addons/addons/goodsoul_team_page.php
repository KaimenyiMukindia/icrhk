<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
use \Elementor\Icons_Manager;

class GoodSoul_Team_Page extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_team_page';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Team Page', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'team_content',
			array(
				'label' => esc_html__( 'Team List', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();
		
		$repeater->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Join our team' ),
			)
		);
		$repeater->add_control(
			'tagline',
			array(
				'label'   => esc_html__( 'Tagline', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'We are always looking for volunteers' ),
			)
		);
		$repeater->add_control(
			'button_name',
			array(
				'label'   => esc_html__( 'Button Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Join Now' ),
			)
		);
		$repeater->add_control(
			'button_link',
			array(
				'label'         => esc_html__( 'Page link', 'goodsoul-core' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);
		$repeater->add_control(
			'image',
			array(
				'label'   => __( 'Member image', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'name',
			array(
				'label'   => esc_html__( 'Member Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Benjie Alphonso' ),
			)
		);
		$repeater->add_control(
			'designation',
			array(
				'label'   => esc_html__( 'Member designation', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Founder' ),
			)
		);
		$this->add_control(
			'member_list',
			array(
				'label'   => esc_html__( 'Member List', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(
						'list_title'   => __( 'Member #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Member #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Member #3', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'team_contents',
			array(
				'label' => esc_html__( 'Team Slider', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'slide_option',
			array(
				'label'        => __( 'Show Slider', 'goodsoul-core' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$repeater_2 = new Repeater();
		$repeater_2->add_control(
			'image',
			array(
				'label'   => __( 'Member image', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater_2->add_control(
			'name',
			array(
				'label'   => esc_html__( 'Member Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Benjie Alphonso' ),
			)
		);
		$repeater_2->add_control(
			'designation',
			array(
				'label'   => esc_html__( 'Member designation', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Founder' ),
			)
		);

		$repeater_2->add_control(
			'facebook_link',
			array(
				'label'         => esc_html__( 'Member Facebook link', 'goodsoul-core' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);

		$repeater_2->add_control(
			'twitter_link',
			array(
				'label'         => esc_html__( 'Member Twitter link', 'goodsoul-core' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);
		$repeater_2->add_control(
			'linkedin_link',
			array(
				'label'         => esc_html__( 'Member LinkedIn link', 'goodsoul-core' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);

		$repeater_2->add_control(
			'page_link',
			array(
				'label'         => esc_html__( 'Member Page link', 'goodsoul-core' ),
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
			'member_list_2',
			array(
				'label'   => esc_html__( 'Members', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater_2->get_controls(),
				'default' => array(
					array(
						'list_title'   => __( 'Member #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Member #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Member #3', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Member #4', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Member #5', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
		);

		$this->end_controls_section();
	}
	protected function render() {
		$settings      = $this->get_settings_for_display();
		$member_list   = $settings['member_list'];
		$member_list_2 = $settings['member_list_2'];
		$slide_option  = $settings['slide_option'];
		?>


	<!-- Team Section Three -->
	<section class="team-section-three">
		<div class="auto-container">
			<div class="row">
				<!-- Team Blokc One -->
			<?php
			foreach ( $member_list as $member ) {
				$image = ( $member['image']['id'] != '' ) ? wp_get_attachment_url( $member['image']['id'], 'full' ) : $member['image']['url'];
				$name        = $member['name'];
				$designation = $member['designation'];
				$tagline       = $member['tagline'];
				$title         = $member['title'];
				$button_name   = $member['button_name'];
				$button_link   = isset($member['button_link']) ? $member['button_link']['url'] : '';
				?>
				<div class="col-lg-4 team-block-three">
					<div class="inner-box wow fadeInDown" data-wow-delay="200ms">
						<div class="image">
							<a href="#"><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></a>
							<div class="overlay">
								<div>
									<h4><?php echo wp_kses_post( $title ); ?></h4>
									<div class="text"><?php echo wp_kses_post( $tagline ); ?></div>
								<?php if ( ! empty( $button_link ) ) { ?>
									<div class="link-btn"><a href="<?php echo esc_url( $button_link ); ?>" class="theme-btn btn-style-one"><span><?php echo wp_kses_post( $button_name ); ?></span></a></div>
									<?php } ?>
								</div>                                    
							</div>
						</div>
						<div class="lower-content">
							<h4> <a href="<?php echo esc_url( $button_link ); ?>"><?php echo wp_kses_post( $name ); ?></a></h4>
							<div class="designation"><?php echo wp_kses_post( $designation ); ?></div>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</section>
	<!-- Team Section Two -->
		<?php
		if ( $slide_option === 'yes' ) {
			?>
	<section class="team-section-two style-two">
		<div class="auto-container">
			<div class="wrapper-box">
				<div class="five-item-carousel owl-theme owl-carousel owl-nav-none owl-dot-style-one">
					<!-- Team Blokc Two -->
					<?php
					foreach ( $member_list_2 as $member ) {
						$image = ( $member['image']['id'] != '' ) ? wp_get_attachment_url( $member['image']['id'], 'full' ) : $member['image']['url'];
						$name          = $member['name'];
						$designation   = $member['designation'];
						$facebook_link = $member['facebook_link']['url'];
						$twitter_link  = $member['twitter_link']['url'];
						$linkedin_link = $member['linkedin_link']['url'];
						$page_link     = $member['page_link']['url'];
						?>
					<div class="team-block-two">
						<div class="inner-box">
							<div class="image"><a href="<?php echo esc_url( $page_link ); ?>"><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></a></div>
							<div class="overlay">
								<div class="author-info">
									<h4> <a href="<?php echo esc_url( $page_link ); ?>"><?php echo wp_kses_post( $name ); ?></a></h4>
									<div class="designation"><?php echo wp_kses_post( $designation ); ?></div>
								</div>                 
								<ul class="social-icon-two">
									<?php if ( ! empty( $facebook_link ) ) { ?>
									<li><a href="<?php echo esc_url( $facebook_link ); ?>"><span class="fa fa-facebook"></span></a></li>
									<?php } ?>
									<?php if ( ! empty( $twitter_link ) ) { ?>
									<li><a href="<?php echo esc_url( $twitter_link ); ?>"><span class="fa fa-twitter"></span></a></li>
									<?php } ?>
									<?php if ( ! empty( $linkedin_link ) ) { ?>
									<li><a href="<?php echo esc_url( $linkedin_link ); ?>"><span class="fa fa-linkedin"></span></a></li>
									<?php } ?>
								</ul>
							</div>
						</div>
					</div>
					<?php }; ?>
				</div>                
			</div>
		</div>
	</section>
	<?php } ?>
						<?php
	}
}

						\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Team_Page() );
