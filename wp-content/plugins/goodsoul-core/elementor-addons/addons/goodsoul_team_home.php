<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Repeater;
use \Elementor\Icons_Manager;
use \Elementor\Plugin;

class GoodSoul_Team_Home extends Widget_Base {

	public function get_name() {
		return 'goodsoul_team_home';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Team Home', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'team_contenta',
			array(
				'label' => esc_html__( 'Team', 'goodsoul-core' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'select_layout',
			array(
				'label'   => esc_html__( 'Select Layout', 'goodsoul-core' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'style_1' => esc_html__( 'Style 1', 'goodsoul-core' ),
					'style_2' => esc_html__( 'Style 2', 'goodsoul-core' ),
					'style_3' => esc_html__( 'Style 3', 'goodsoul-core' ),
					'style_4' => esc_html__( 'Style 4', 'goodsoul-core' ),
					'style_5' => esc_html__( 'Style 5', 'goodsoul-core' ),

				),
				'default' => esc_html__( 'style_1', 'goodsoul-core' ),
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'     => __( 'Choose Icon', 'goodsoul-core' ),
				'type'      => Controls_Manager::ICONS,
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
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Team behind goodsoul' ),
				'condition' => array( 'select_layout' => 'style_2' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'     => esc_html__( 'Title', 'goodsoul-core' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Team behind goodsoul' ),
				'condition' => array(
					'select_layout' => array( 'style_1', 'style_2', 'style_3', 'style_5' ),
				),
			)
		);
		$this->add_control(
			'content',
			array(
				'label'     => esc_html__( 'Text', 'goodsoul-core' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => __( 'Our work would not be possible without the work of our dedicated volunteers.' ),
				'condition' => array(
					'select_layout' => array( 'style_1', 'style_5' ),
				),
			)
		);
		$this->add_control(
			'button_name',
			array(
				'label'     => esc_html__( 'Button Name', 'goodsoul-core' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Meet All Members' ),
				'condition' => array(
					'select_layout' => array( 'style_1', 'style_5' ),
				),
			)
		);
		$this->add_control(
			'button_link',
			array(
				'label'         => esc_html__( 'Page link', 'goodsoul-core' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
				'condition'     => array(
					'select_layout' => array( 'style_1', 'style_5' ),
				),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'image',
			array(
				'label'   => __( 'Member image', 'goodsoul-core' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'name',
			array(
				'label'   => esc_html__( 'Member Name', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Benjie Alphonso' ),
			)
		);
		$repeater->add_control(
			'designation',
			array(
				'label'   => esc_html__( 'Member designation', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Founder' ),
			)
		);

		$repeater->add_control(
			'facebook_link',
			array(
				'label'         => esc_html__( 'Member Facebook link', 'goodsoul-core' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);
		$repeater->add_control(
			'twitter_link',
			array(
				'label'         => esc_html__( 'Member Twitter link', 'goodsoul-core' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);
		$repeater->add_control(
			'skype_link',
			array(
				'label'         => esc_html__( 'Member Skype link', 'goodsoul-core' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);
		$repeater->add_control(
			'linkedin_link',
			array(
				'label'         => esc_html__( 'Member LinkedIn link', 'goodsoul-core' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);

		$repeater->add_control(
			'page_link',
			array(
				'label'         => esc_html__( 'Member Page link', 'goodsoul-core' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);

		goodsoul_get_animation_control( $repeater );

		$this->add_control(
			'member_list',
			array(
				'label'     => esc_html__( 'Member List', 'goodsoul-core' ),
				'type'      => Controls_Manager::REPEATER,
				'fields'    => $repeater->get_controls(),
				'default'   => array(
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
				'condition' => array(
					'select_layout' => array( 'style_1', 'style_5' ),
				),
			)
		);

		$repeater_2 = new Repeater();
		$repeater_2->add_control(
			'image',
			array(
				'label'   => __( 'Member image', 'goodsoul-core' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater_2->add_control(
			'name',
			array(
				'label'   => esc_html__( 'Member Name', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Benjie Alphonso' ),
			)
		);
		$repeater_2->add_control(
			'designation',
			array(
				'label'   => esc_html__( 'Member designation', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Founder' ),
			)
		);

		$repeater_2->add_control(
			'facebook_link',
			array(
				'label'         => esc_html__( 'Member Facebook link', 'goodsoul-core' ),
				'type'          => Controls_Manager::URL,
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
				'type'          => Controls_Manager::URL,
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
				'type'          => Controls_Manager::URL,
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
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);

		goodsoul_get_animation_control( $repeater_2 );

		$this->add_control(
			'member_list_2',
			array(
				'label'     => esc_html__( 'Member List', 'goodsoul-core' ),
				'type'      => Controls_Manager::REPEATER,
				'fields'    => $repeater_2->get_controls(),
				'default'   => array(
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
				'condition' => array(
					'select_layout' => array( 'style_4', 'style_2', 'style_3' ),
				),
			)
		);

		$this->end_controls_section();
	}
	protected function render() {
		$settings      = $this->get_settings_for_display();
		$select_layout = $settings['select_layout'];
		$tagline       = $settings['tagline'];
		$title         = $settings['title'];
		$content       = $settings['content'];
		$button_name   = $settings['button_name'];
		$button_link   = ! empty( $settings['button_link']['url'] ) ? $settings['button_link']['url'] : '';
		$member_list   = $settings['member_list'];
		$member_list_2 = $settings['member_list_2'];
		$icon          = $settings['icon'];
		?>
		<?php
		if ( $select_layout == 'style_1' ) {
			?>
<section class="team-section">
	<div class="auto-container">
		<div class="row m-0 justify-content-md-between align-items-end">
			<div class="sec-title light">
				<h1><?php echo wp_kses_post( $title ); ?></h1>
				<div class="text"><?php echo wp_kses_post( $content ); ?></div>
			</div>
			<!--Link Btn-->
			<?php if ( ! empty( $button_link ) ) { ?>
			<div class="link-btn mb-50">
				<a href="<?php echo esc_url( $button_link ); ?>"
					class="theme-btn btn-style-one"><span><?php echo wp_kses_post( $button_name ); ?></span></a>
			</div>
			<?php } ?>
		</div>
		<div class="wrapper-box">
			<div class="row">
				<!-- Team Blokc One -->
			<?php
			foreach ( $member_list as $member ) {
				$image                = ( $member['image']['id'] ) ? wp_get_attachment_url( $member['image']['id'], 'full' ) : '#';
				$name                 = $member['name'];
				$designation          = $member['designation'];
				$facebook_link        = $member['facebook_link']['url'];
				$twitter_link         = $member['twitter_link']['url'];
				$skype_link           = $member['skype_link']['url'];
				$linkedin_link        = $member['linkedin_link']['url'];
				$page_link            = $member['page_link']['url'];
				$animation_class      = $member['animation_class'];
				$animation_delay_time = $member['addon_animation_delay_time'];
				?>
				<div class="col-lg-4 team-block-one">
					<div class="inner-box wow <?php echo esc_attr( $animation_class ); ?>"
						data-wow-delay="<?php echo esc_attr( $animation_delay_time ); ?>">
						<div class="image"><a href="<?php echo esc_url( $page_link ); ?>"><img
									src="<?php echo esc_url( $image ); ?>"
									alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></a></div>
						<div class="lower-content">
							<h4> <a href="<?php echo esc_url( $page_link ); ?>"><?php echo wp_kses_post( $name ); ?></a>
							</h4>
							<div class="designation"><?php echo wp_kses_post( $designation ); ?></div>
						</div>
						<ul class="social-icon-two">
				<?php if ( ! empty( $facebook_link ) ) { ?>
							<li><a href="<?php echo esc_url( $facebook_link ); ?>"><span
										class="fa fa-facebook"></span></a></li>
				<?php } ?>
				<?php if ( ! empty( $twitter_link ) ) { ?>
							<li><a href="<?php echo esc_url( $twitter_link ); ?>"><span class="fa fa-twitter"></span></a>
							</li>
				<?php } ?>
				<?php if ( ! empty( $skype_link ) ) { ?>
							<li><a href="<?php echo esc_url( $skype_link ); ?>"><span class="fa fa-skype"></span></a></li>
				<?php } ?>
				<?php if ( ! empty( $linkedin_link ) ) { ?>
							<li><a href="<?php echo esc_url( $linkedin_link ); ?>"><span
										class="fa fa-linkedin"></span></a></li>
				<?php } ?>
						</ul>
					</div>
				</div>
			<?php } ?>
			</div>
		</div>
	</div>
</section>
		<?php } ?>
		<?php
		if ( $select_layout == 'style_2' ) {
			?>
<section class="team-section-two">
	<div class="auto-container">
		<div class="sec-title text-center">
			<h5><?php echo wp_kses_post( $tagline ); ?></h5>
			<h1><?php echo wp_kses_post( $title ); ?></h1>
		</div>
		<div class="wrapper-box">
			<div class="four-item-carousel owl-theme owl-carousel owl-nav-none owl-dot-style-one">
			<?php
			foreach ( $member_list_2 as $member ) {
				$image         = ( $member['image']['id'] != '' ) ? wp_get_attachment_url( $member['image']['id'], 'full' ) : $member['image']['url'];
				$name          = $member['name'];
				$designation   = $member['designation'];
				$facebook_link = $member['facebook_link']['url'];
				$twitter_link  = $member['twitter_link']['url'];
				$linkedin_link = $member['linkedin_link']['url'];
				$page_link     = $member['page_link']['url'];
				?>
				<!-- Team Blokc Two -->
				<div class="team-block-two">
					<div class="inner-box">
						<div class="image"><a href="<?php echo esc_url( $page_link ); ?>"><img
									src="<?php echo esc_url( $image ); ?>"
									alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></a></div>
						<div class="overlay">
							<div class="author-info">
								<h4> <a href="<?php echo esc_url( $page_link ); ?>"><?php echo wp_kses_post( $name ); ?></a>
								</h4>
								<div class="designation"><?php echo wp_kses_post( $designation ); ?></div>
							</div>
							<ul class="social-icon-two">
				<?php if ( ! empty( $facebook_link ) ) { ?>
								<li><a href="<?php echo esc_url( $facebook_link ); ?>"><span
											class="fa fa-facebook"></span></a></li>
				<?php } ?>
				<?php if ( ! empty( $twitter_link ) ) { ?>
								<li><a href="<?php echo esc_url( $twitter_link ); ?>"><span
											class="fa fa-twitter"></span></a></li>
				<?php } ?>
				<?php if ( ! empty( $linkedin_link ) ) { ?>
								<li><a href="<?php echo esc_url( $linkedin_link ); ?>"><span
											class="fa fa-linkedin"></span></a></li>
				<?php } ?>
							</ul>
						</div>
					</div>
				</div>
			<?php } ?>
			</div>
		</div>
	</div>
</section>
		<?php } ?>
		<?php
		if ( $select_layout == 'style_3' ) {
			?>
<!-- Team Section Five -->
<section class="team-section-five">
	<div class="auto-container">
		<div class="sec-title text-center style-two">
			<?php if ( ! empty( $icon ) ) { ?>
			<div class="icon-box"><span class="<?php echo $icon['value']; ?>"></span></div>
			<?php } ?>
			<h5><?php echo wp_kses_post( $tagline ); ?></h5>
			<h1><?php echo wp_kses_post( $title ); ?></h1>
		</div>
		<div class="wrapper-box">
			<div class="four-item-carousel owl-theme owl-carousel owl-nav-none owl-dot-style-one">
			<?php
			foreach ( $member_list_2 as $member ) {
				$image                = ( $member['image']['id'] != '' ) ? wp_get_attachment_url( $member['image']['id'], 'full' ) : $member['image']['url'];
				$name                 = $member['name'];
				$designation          = $member['designation'];
				$facebook_link        = $member['facebook_link']['url'];
				$twitter_link         = $member['twitter_link']['url'];
				$linkedin_link        = $member['linkedin_link']['url'];
				$page_link            = $member['page_link']['url'];
				$animation_class      = $member['animation_class'];
				$animation_delay_time = $member['addon_animation_delay_time'];
				?>
				<!-- Team Blokc five -->
				<div class="team-block-five">
					<div class="inner-box wow <?php echo esc_attr( $animation_class ); ?>" data-wow-delay="<?php echo esc_attr( $animation_delay_time ); ?>">
						<div class="image"><a href="<?php echo esc_url( $page_link ); ?>"><img
									src="<?php echo esc_url( $image ); ?>"
									alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></a></div>
						<div class="lower-content">
							<h4> <a href="<?php echo esc_url( $page_link ); ?>"><?php echo wp_kses_post( $name ); ?></a>
							</h4>
							<div class="designation"><?php echo wp_kses_post( $designation ); ?></div>
						</div>
						<div class="overlay-content">
							<h4> <a href="<?php echo esc_url( $page_link ); ?>"><?php echo wp_kses_post( $name ); ?></a>
							</h4>
							<div class="designation"><?php echo wp_kses_post( $designation ); ?></div>

							<ul class="social-icon-two">
				<?php if ( ! empty( $facebook_link ) ) { ?>
								<li><a href="<?php echo esc_url( $facebook_link ); ?>"><span
											class="fa fa-facebook"></span></a></li>
				<?php } ?>
				<?php if ( ! empty( $twitter_link ) ) { ?>
								<li><a href="<?php echo esc_url( $twitter_link ); ?>"><span
											class="fa fa-twitter"></span></a></li>
				<?php } ?>
				<?php if ( ! empty( $linkedin_link ) ) { ?>
								<li><a href="<?php echo esc_url( $linkedin_link ); ?>"><span
											class="fa fa-linkedin"></span></a></li>
				<?php } ?>
							</ul>
						</div>
					</div>
				</div>
			<?php } ?>
			</div>
		</div>
	</div>
</section>
		<?php } ?>
		<?php
		if ( $select_layout == 'style_4' ) {
			?>
<!-- Team Section Six -->
<section class="team-section-six">
	<div class="auto-container">
		<div class="wrapper-box">
			<div class="five-item-carousel owl-theme owl-carousel owl-nav-none owl-dot-style-one">
			<?php
			foreach ( $member_list_2 as $member ) {
				$image         = ( $member['image']['id'] != '' ) ? wp_get_attachment_url( $member['image']['id'], 'full' ) : $member['image']['url'];
				$name          = $member['name'];
				$designation   = $member['designation'];
				$facebook_link = $member['facebook_link']['url'];
				$twitter_link  = $member['twitter_link']['url'];
				$linkedin_link = $member['linkedin_link']['url'];
				$page_link     = $member['page_link']['url'];
				?>
				<!-- Team Blokc Six -->
				<div class="team-block-six">
					<div class="inner-box">
						<div class="image"><a href="<?php echo esc_url( $page_link ); ?>"><img
									src="<?php echo esc_url( $image ); ?>"
									alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></a></div>
						<div class="overlay">
							<div class="author-info">
								<h4> <a href="<?php echo esc_url( $page_link ); ?>"><?php echo wp_kses_post( $name ); ?></a>
								</h4>
								<div class="designation"><?php echo wp_kses_post( $designation ); ?></div>
							</div>
							<ul class="social-icon-two">
				<?php if ( ! empty( $facebook_link ) ) { ?>
								<li><a href="<?php echo esc_url( $facebook_link ); ?>"><span
											class="fa fa-facebook"></span></a></li>
				<?php } ?>
				<?php if ( ! empty( $twitter_link ) ) { ?>
								<li><a href="<?php echo esc_url( $twitter_link ); ?>"><span
											class="fa fa-twitter"></span></a></li>
				<?php } ?>
				<?php if ( ! empty( $linkedin_link ) ) { ?>
								<li><a href="<?php echo esc_url( $linkedin_link ); ?>"><span
											class="fa fa-linkedin"></span></a></li>
				<?php } ?>
							</ul>
						</div>
					</div>
				</div>
			<?php } ?>
			</div>
		</div>
	</div>
</section>

		<?php } ?>
		<?php
		if ( $select_layout == 'style_5' ) {
			?>
<!-- Team Section -->
<section class="team-section style-two">
	<div class="auto-container">
		<div class="sec-title text-center">
			<h1><?php echo wp_kses_post( $title ); ?></h1>
			<div class="text"><?php echo wp_kses_post( $content ); ?></div>
		</div>
		<div class="row">
			<?php
			foreach ( $member_list as $member ) {
				$image                = ( $member['image']['id'] != '' ) ? wp_get_attachment_url( $member['image']['id'], 'full' ) : $member['image']['url'];
				$name                 = $member['name'];
				$designation          = $member['designation'];
				$facebook_link        = $member['facebook_link']['url'];
				$twitter_link         = $member['twitter_link']['url'];
				$skype_link           = $member['skype_link']['url'];
				$linkedin_link        = $member['linkedin_link']['url'];
				$page_link            = $member['page_link']['url'];
				$animation_class      = $member['animation_class'];
				$animation_delay_time = $member['addon_animation_delay_time'];
				?>
			<!-- Team Blokc One -->
			<div class="col-lg-4 team-block-one">
				<div class="inner-box wow <?php echo esc_attr( $animation_class ); ?>" data-wow-delay="<?php echo esc_attr( $animation_delay_time ); ?>">
					<div class="image"><a href="<?php echo esc_url( $page_link ); ?>"><img
								src="<?php echo esc_url( $image ); ?>"
								alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>"></a></div>
					<div class="lower-content">
						<h4> <a href="<?php echo esc_url( $page_link ); ?>"><?php echo wp_kses_post( $name ); ?></a></h4>
						<div class="designation"><?php echo wp_kses_post( $designation ); ?></div>
					</div>
					<ul class="social-icon-two">
				<?php if ( ! empty( $facebook_link ) ) { ?>
						<li><a href="<?php echo esc_url( $facebook_link ); ?>"><span class="fa fa-facebook"></span></a>
						</li>
				<?php } ?>
				<?php if ( ! empty( $twitter_link ) ) { ?>
						<li><a href="<?php echo esc_url( $twitter_link ); ?>"><span class="fa fa-twitter"></span></a></li>
				<?php } ?>
				<?php if ( ! empty( $skype_link ) ) { ?>
						<li><a href="<?php echo esc_url( $skype_link ); ?>"><span class="fa fa-skype"></span></a></li>
				<?php } ?>
				<?php if ( ! empty( $linkedin_link ) ) { ?>
						<li><a href="<?php echo esc_url( $linkedin_link ); ?>"><span class="fa fa-linkedin"></span></a>
						</li>
				<?php } ?>
					</ul>
				</div>
			</div>
			<?php } ?>
		</div>
			<?php if ( ! empty( $button_link ) ) { ?>
		<div class="link-btn text-center"><a href="<?php echo esc_url( $button_link ); ?>"
				class="theme-btn btn-style-one"><span><?php echo wp_kses_post( $button_name ); ?></span></a></div>
			<?php } ?>
	</div>
</section>
		<?php } ?>
		<?php
	}
}
Plugin::instance()->widgets_manager->register( new \GoodSoul_Team_Home() );
