<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	use Elementor\Controls_Manager;
	use Elementor\Plugin;
	use Elementor\Utils;
	use Elementor\Widget_Base;
	use \Elementor\Repeater;
	use \Elementor\Icons_Manager;

class Goodsoul_Footer_post extends Widget_Base {

	public function get_name() {
		return 'goodsoul_footer_blogs';
	}

	public function get_title() {
		return esc_html__( 'goodsoul Footer Post', 'goodsoul-core' );
	}

	public function get_icon() {
		return 'fa fa-object-ungroup';
	}

	public function get_categories() {
		return array( 'goodsoulcore' );
	}

	private function get_blog_categories() {
		$options  = array();
		$taxonomy = 'category';
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
			'section_blogs',
			array(
				'label' => esc_html__( 'Blogs', 'goodsoul-core' ),
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
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Trending Post',
			)
		);
		$this->add_control(
			'category_id',
			array(
				'type'    => \Elementor\Controls_Manager::SELECT,
				'label'   => esc_html__( 'Category', 'goodsoul-core' ),
				'options' => $this->get_blog_categories(),
			)
		);

		$this->add_control(
			'number',
			array(
				'label'   => esc_html__( 'Number of Post', 'goodsoul-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 2,
			)
		);

		$this->add_control(
			'order_by',
			array(
				'label'   => esc_html__( 'Order By', 'goodsoul-core' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'          => esc_html__( 'Date', 'goodsoul-core' ),
					'ID'            => esc_html__( 'ID', 'goodsoul-core' ),
					'author'        => esc_html__( 'Author', 'goodsoul-core' ),
					'title'         => esc_html__( 'Title', 'goodsoul-core' ),
					'modified'      => esc_html__( 'Modified', 'goodsoul-core' ),
					'rand'          => esc_html__( 'Random', 'goodsoul-core' ),
					'comment_count' => esc_html__( 'Comment count', 'goodsoul-core' ),
					'menu_order'    => esc_html__( 'Menu order', 'goodsoul-core' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order', 'goodsoul-core' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => array(
					'desc' => esc_html__( 'DESC', 'goodsoul-core' ),
					'asc'  => esc_html__( 'ASC', 'goodsoul-core' ),
				),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();
		$select_layout   = $settings['select_layout'];
		$title          = $settings['title'];
		$posts_per_page = $settings['number'];
		$order_by       = $settings['order_by'];
		$order          = $settings['order'];
		$pg_num         = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$args           = array(
			'post_type'      => array( 'post' ),
			'post_status'    => array( 'publish' ),
			'nopaging'       => false,
			'paged'          => $pg_num,
			'posts_per_page' => $posts_per_page,
			'category_name'  => $settings['category_id'],
			'orderby'        => $order_by,
			'order'          => $order,
		);
		$query          = new WP_Query( $args );

		?>
		<?php if ( $select_layout == 'style_1' ) { ?>
        <div class="post-widget footer-widget">
            <h4 class="widget-title"><?php echo $title; ?></h4>
            <?php
				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						$tags = wp_get_post_categories( get_the_ID() );
						?>
            <div class="post">
                <div class="date"><span class="flaticon-clock"></span><?php echo get_the_date( 'M d, Y' ); ?></div>
                <h4><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h4>
            </div>
            <?php
					}
					wp_reset_postdata();
				}
				?>
        </div>
			<?php } elseif ( $select_layout == 'style_2' ) { ?>
				<div class="post-widget footer-widget">
                        <h4 class="widget-title"><?php echo $title; ?></h4>
						<?php
						if ( $query->have_posts() ) {
							while ( $query->have_posts() ) {
						$query->the_post();
						$tags = wp_get_post_categories( get_the_ID() );
						?>
                        <div class="post">
                            <h4><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h4>
                            <div class="date"><span class="flaticon-clock"></span><?php echo get_the_date( 'M d, Y' ); ?></div>
                        </div>
						<?php
							}
							wp_reset_postdata();
						}
						?>
                    </div>
			<?php } ?>
		<?php
	}

	protected function content_template() {

	}
}

			Plugin::instance()->widgets_manager->register( new Goodsoul_Footer_post() );
