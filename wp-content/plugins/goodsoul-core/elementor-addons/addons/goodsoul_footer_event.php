<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Widget_Base;

class GoodSoul_Footer_Event extends Widget_Base {

	public function get_name() {
		return 'goodsoul_footer_event';
	}

	public function get_title() {
		return esc_html__( 'Goodsoul Footer Event', 'goodsoul-core' );
	}

	public function get_icon() {
		return 'fa fa-object-ungroup';
	}

	public function get_categories() {
		return array( 'goodsoulcore' );
	}

	private function get_category_list() {
		$options  = array();
		$taxonomy = 'tribe_events_cat';
		if ( ! empty( $taxonomy ) ) {
			$terms = get_terms(
				array(
					'parent'     => 0,
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
				)
			);
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( isset( $term ) ) {
						$options[''] = 'Select';
						if ( isset( $term->slug ) && isset( $term->name ) ) {
							$options[ $term->slug ] = $term->name;
						}
					}
				}
			}
		}
		return $options;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_program',
			array(
				'label' => esc_html__( 'Content', 'goodsoul-core' ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Upcoming Events',
			)
		);
		$this->add_control(
			'category_slug',
			array(
				'type'    => \Elementor\Controls_Manager::SELECT,
				'label'   => esc_html__( 'Category', 'goodsoul-core' ),
				'options' => $this->get_category_list(),
			)
		);

		$this->add_control(
			'number',
			array(
				'label'   => esc_html__( 'Number of Post', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 3,
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings       = $this->get_settings();
		$title          = $settings['title'];
		$category       = $settings['category_slug'];
		$number_of_post = (int) $settings['number'];
		global $post;
		$get_posts = tribe_get_events(
			array(
				'posts_per_page'=>$number_of_post,
				'tax_query'=> array(
					array(
						'taxonomy' => 'tribe_events_cat',
						'field' => 'slug',
						'terms' => $category
					)
				),
		)
		);
		?>
        
            <div class="event-widget footer-widget">
                <h4 class="widget-title"><?php echo  $title ; ?></h4>
                <?php
            foreach ( $get_posts as $post ) {
                ?>
                <div class="signle-event">
                    <div class="date"><?php echo tribe_get_start_date( $post, false, 'd' ); ?> <br><?php echo tribe_get_start_date( $post, false, 'M' ); ?></div>
                    <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                    <div class="location"><span class="flaticon-point"></span><?php echo tribe_get_address(); ?></div>
                </div>
                <?php } ?>
            </div>
		<?php
	}

	protected function content_template() {

	}

}

		Plugin::instance()->widgets_manager->register( new GoodSoul_Footer_Event() );
