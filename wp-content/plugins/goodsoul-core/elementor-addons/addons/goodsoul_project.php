<?php
/**
 * Elementor Banner slider one
 *
 * @since 1.0.0
 */
use \Elementor\Repeater;
use \Elementor\Icons_Manager;

class GoodSoul_Project extends \Elementor\Widget_Base {
	public function get_name() {
		return 'goodsoul_project';
	}
	public function get_title() {
		return esc_html__( 'GoodSoul Project', 'goodsoul-core' );
	}
	public function get_icon() {
		return 'fa fa-object-ungroup';
	}
	public function get_categories() {
		return array( 'goodsoulcore' );
	}
	protected function register_controls() {
		$this->start_controls_section(
			'project_content',
			array(
				'label' => esc_html__( 'Projects', 'goodsoul-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'The trusted choice of donors' ),
			)
		);
		$this->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Text', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'The right way to live as a human being, Just help to those people really need your help.' ),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'image',
			array(
				'label'   => __( 'Project image', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label'   => __( 'Choose Icon', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-star',
					'library' => 'solid',
				),
			)
		);

		$repeater->add_control(
			'name',
			array(
				'label'   => esc_html__( 'Name', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Children' ),
			)
		);

		$repeater->add_control(
			'page_link',
			array(
				'label'         => esc_html__( 'Page Link', 'goodsoul-core' ),
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
			'style',
			array(
				'label'   => __( 'Display Style', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'two'   => __( 'Two', 'goodsoul-core' ),
					'three' => __( 'Three', 'goodsoul-core' ),
					'grid'  => __( 'Grid', 'goodsoul-core' ),
				),
				'default' => 'two',

			)
		);

		$this->add_control(
			'project_list',
			array(
				'label'   => esc_html__( 'project List', 'goodsoul-core' ),
				'type'    => \Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(
						'list_title'   => __( 'Project #1', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Project #2', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Project #3', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Project #4', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
					array(
						'list_title'   => __( 'Project #5', 'goodsoul-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'goodsoul-core' ),
					),
				),
			)
		);

		$this->end_controls_section();

	}
	protected function render() {
		$settings     = $this->get_settings_for_display();
		$title        = $settings['title'];
		$content      = $settings['content'];
		$project_list = $settings['project_list'];
		$number_of_coloumns = $settings['style'];
		$column             = 1;
		$coloumn_class      = '';
		if ( $number_of_coloumns == 'two' ) {
			$coloumn_class = 'col-lg-6';
		} elseif ( $number_of_coloumns == 'three' ) {
			$coloumn_class = 'col-lg-4';
		} elseif ( $number_of_coloumns == 'grid' ) {
			$coloumn_class = 'grid';
		}
		?>


	<!-- Projects Section -->
	<section class="projects-section">
		<div class="auto-container">
			<div class="sec-title text-center">
				<h1><?php echo wp_kses_post( $title ); ?></h1>
				<div class="text"><?php echo wp_kses_post( $content ); ?></div>
			</div>
			<div class="row">
				<!-- Project Block One -->
				<?php
				$oddrow     = true;
				$imagecount = 0;
				foreach ( $project_list as $project ) {
					$image     = ($project['image']['id'] != '' ) ? wp_get_attachment_url( $project['image']['id'], 'full' ) : $project['image']['url'];
					$name      = $project['name'];
					$page_link = $project['page_link']['url'];
					$icon      = $project['icon'];
					?>
				<div class="
					<?php
					if ( $coloumn_class == 'grid' ) {
						if ( $oddrow == true ) {
							echo 'col-lg-6';
							$imagecount++;
							if ( $imagecount == 2 ) {
								$oddrow = false;
							}
						} elseif ( $oddrow == false ) {
							echo 'col-lg-4';
							$imagecount++;
							if ( $imagecount == 5 ) {
								$oddrow     = true;
								$imagecount = 1;
							}
						}
					} else {
						echo $coloumn_class;
					}
					?>
				 col-md-12 project-block-one">
					<div class="inner-box">
						<div class="image">
						<a href="<?php echo esc_url( $page_link ); ?>">
							<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_html__( 'Alt', 'goodsoul-core' ); ?>">
						</a>	
							<div class="icon-box">
								<span class="<?php echo $icon['value']; ?>"></span>
							</div>
						</div>
						<h3><a href="<?php echo esc_url( $page_link ); ?>"><?php echo wp_kses_post( $name ); ?></a></h3>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</section>
	
						<?php
	}
}

						\Elementor\Plugin::instance()->widgets_manager->register( new \GoodSoul_Project() );
