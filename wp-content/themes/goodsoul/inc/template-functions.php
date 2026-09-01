<?php
function goodsoul_body_classes( $classes ) {
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}
    $base_theme = goodsoul_get_options( 'base_theme' );
	if ( $base_theme == 0 ) :
		$classes[] = 'goodsoul-base';
	else :
		$classes[] = '';
    endif;
    $event_full_width = goodsoul_get_options( 'event_full_width' );
	if ( $event_full_width == 0 ) :
		$classes[] = 'full-wdith-event-single';
	else :
		$classes[] = '';
    endif;
	return $classes;
}
add_filter( 'body_class', 'goodsoul_body_classes' );
function goodsoul_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'goodsoul_pingback_header' );
    if( ! function_exists('goodsoul_logo') ) :
        function goodsoul_logo() {
            ?>
                <div class="logo-column">
                    <div class="logo-box">
                        <div class="logo">
                            <?php
                                $goodsoul_theme_metabox_header_logo = get_post_meta(get_queried_object_id(), 'goodsoul_theme_metabox_header_logo', array( 'size' => 'full' ));
                                if(!empty($goodsoul_theme_metabox_header_logo)) {
                                    ?>
                                    <a href="<?php echo esc_url(home_url('/')); ?>">
                                        <img src="<?php echo esc_url( wp_get_attachment_url( $goodsoul_theme_metabox_header_logo ) );?>" alt="<?php esc_attr_e('Logo', 'goodsoul') ?>">
                                    </a> 
                                    <?php
                                } else {
                                    if (has_custom_logo()) {
                                        the_custom_logo();
                                    } elseif (!has_custom_logo()) {
                                        ?>
                                        <a href="<?php echo esc_url(home_url('/')); ?>">
                                            <img src="<?php echo esc_url(GOODSOUL_IMG_URL . 'logo-vsg.svg') ?>" alt="<?php esc_attr_e('Logo', 'goodsoul') ?>">
                                        </a> 
                                        <?php
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </div>
            <?php
        }
    endif;
    add_action('goodsoul_logo_fun', 'goodsoul_logo');
function goodsoul_kses_allowed_html($goodsoul_tags, $goodsoul_context) {
    switch ($goodsoul_context) {
        case 'code_contxt':
            $goodsoul_tags = array(
                'h3' => array('class' => array()),
                'img' => array('class' => array(), 'height' => array(), 'width' => array(), 'src' => array(), 'alt' => array()),
                'h4' => array('class' => array()),
                'a' => array('class' => array(), 'href' => array(), 'target' =>  array()),
                'li' => array('class' => array(), 'a' => array('class' => array())),
                'div' => array('class' => array()),
                'br' => array('class' => array()),
                'p' => array('class' => array()),
                'button' => array('class' => array()),
                'ul' => array('class' => array()),
                'em' => array('class' => array()),
                'h4' => array('class' => array()),
                'select' => array('class' => array()),
                'option' => array('class' => array()),
                'form' => array('class' => array()),
                'span' => array('class' => array()),
                'strong' => array('class' => array())
            );
            return $goodsoul_tags;
        case 'goodsoul_img':
            $goodsoul_tags = array(
                'img' => array('class' => array(), 'height' => array(), 'width' => array(), 'src' => array(), 'alt' => array())
            );
            return $goodsoul_tags;
        default:
            return $goodsoul_tags;
    }
}
add_filter('wp_kses_allowed_html', 'goodsoul_kses_allowed_html', 10, 2);