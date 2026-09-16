<?php
add_action('widgets_init', 'solustrid_latest_post');

function solustrid_latest_post() {
    register_widget('LoveusLatestPost');
}

class LoveusLatestPost extends WP_Widget {

    private $defaults = array();

    function __construct() {
        $this->defaults = array(
            'title' => esc_html__('Latest News', 'loveuscore'),
            'number' => 2,
        );
        parent::__construct('widget_latest_posts_tab', esc_html__('LoveUs Latest News', 'loveuscore'));
    }

    function update($new_instance, $old_instance) {
        $defaults = $this->defaults;
        $instance = $old_instance;
        $instance['title'] = esc_attr($new_instance['title']);
        $instance['number'] = intval($new_instance['number']);
        return $instance;
    }

    function form($instance) {
        $instance = wp_parse_args((array) $instance, $this->defaults);
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'loveuscore'); ?></label>
            <input type="text" name="<?php echo esc_attr($this->get_field_name('title')); ?>"  value="<?php echo esc_attr($title); ?>" class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" />
        </p>
        <p>
            <label for="<?php print esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e('Number of posts:', 'loveuscore'); ?>
                <input class="widefat" id="<?php print esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="text" value="<?php echo isset($instance['number']) ? esc_attr($instance['number']) : ''; ?>" />
            </label>
        </p>
        <?php
    }

    function widget($args, $instance) {
        $instance = wp_parse_args((array) $instance, $this->defaults);
        extract($args);
        $number = isset($instance['number']) ? $instance['number'] : 2;
        $title = $instance['title'];
        ?>
        <section  class="widget widget_latest_post sidebar-widget popular-posts">
        <h2 class="widget-title"><?php echo $title; ?></h2>
        <?php
        $query_args = array(
            'posts_per_page' => $number,
            'no_found_rows' => true,
            'post_status' => 'publish',
            'ignore_sticky_posts' => true
        );
        $query = new WP_Query($query_args);

        if ($query->have_posts()) {
            while ($query->have_posts()) :
                $query->the_post();
                ?>
                <!--News Widget Block-->
                <div class="news-post">
                    <div class="post-thumb"><a href="<?php esc_url(the_permalink()); ?>"><?php echo the_post_thumbnail(); ?></a></div>
                    <div class="date"><span class="fa fa-calendar-alt"></span> <?php echo get_the_date(); ?></div>
                    <h4><a href="<?php esc_url(the_permalink()); ?>"><?php the_title(); ?></a></h4>
                </div>
                <?php
            endwhile;
            wp_reset_query();
        }
        ?>
        </section>
        <?php
    }

}
