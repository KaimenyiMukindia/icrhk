<?php
/**
 * Template part for displaying events single.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package goodsoul
 */

    $blog_signle_tag = goodsoul_get_options('blog_signle_tag');
    $blog_signle_tag_text = goodsoul_get_options('blog_signle_tag_text');
    $blog_signle_share = goodsoul_get_options('blog_signle_share');
    $blog_signle_share_text = goodsoul_get_options('blog_signle_share_text');

    $is_no_post_thumb = '';
    if (!has_post_thumbnail()) {
        $is_no_post_thumb = 'no-post-thumb';
    }
?>
<div class="event-details <?php echo esc_attr($is_no_post_thumb); ?>">
    <div class="inner-box">
        <div class="image-box">
            <figure class="image">
                <?php if (has_post_thumbnail()) : ?>
                    <?php goodsoul_post_thumbnail(); ?>
                <?php endif; ?>
            </figure>
            <div class="date"><?php echo get_the_date( 'j' ); ?> <span class="month"><?php echo get_the_date( 'M' ); ?></span></div>
        </div>
        <div class="lower-content">
            <h2><?php the_title(); ?></h2>
            <ul class="info clearfix">
                <li><span class="icon far fa-clock"></span> 9.00 AM - 11.00 PM</li>
                <li><span class="icon fa fa-map-marker-alt"></span> 29 Newyork City</li>
            </ul>
            <div class="text-content">
                <?php the_content(); ?>
            </div>
        </div>
    </div>
</div>