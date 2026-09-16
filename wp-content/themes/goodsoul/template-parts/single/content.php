<?php
/**
 * Template part for displaying posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package goodsoul
 */

    $blog_single_page_view = goodsoul_get_options('blog_single_page_view');
    $blog_signle_tag = goodsoul_get_options('blog_signle_tag');
    $blog_signle_tag_text = goodsoul_get_options('blog_signle_tag_text');
    $blog_signle_share = goodsoul_get_options('blog_signle_share');
    $blog_signle_share_text = goodsoul_get_options('blog_signle_share_text');
    $blog_single_next_preview = goodsoul_get_options('blog_single_next_preview');

    $hide_date = goodsoul_get_options('hide_date');

    $is_no_post_thumb = '';
    if (!has_post_thumbnail()) {
        $is_no_post_thumb = 'no-post-thumb';
    }
    
?>
<div class="single-blog-post">
    <div class="inner-box">
        <div class="top-content">
            <h2><?php the_title(); ?></h2>
            <div class="post-info">
                <div class="author">
                    <div class="author-thumb"><?php goodsoul_author_image();?></div>
                    <div class="author-title">
                        <?php goodsoul_posted_by(); ?>
                    </div>
                </div>
                <?php if($hide_date == '1') : ?>
                    <div class="date"><span class="flaticon-clock"></span><?php the_date();?></div>
                <?php endif; ?>
                <div class="category"><span class="flaticon-folder"></span><?php goodsoul_category_list(); ?></div>
                <?php if($blog_single_page_view == '1') : ?>
                    <div class="views">
                        <span class="flaticon-eye"></span>
                        <?php 
                                goodsoul_post_views(get_queried_object_id());
                                $post_views_count = get_post_meta( get_queried_object_id(), 'post_views_count', true );
                                if ( ! empty( $post_views_count ) ) {
                                    echo wp_kses($post_views_count, 'code_contxt');
                                }
                                echo esc_html__(' Views', 'goodsoul');
                        ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (has_post_thumbnail()) : ?>
                <div class="image">
                    <?php the_post_thumbnail(); ?>
                </div>
            <?php endif; ?>
            <div class="text"><?php the_content(); ?></div>
            <?php
                wp_link_pages(array(
                    'before' => '<div class="page-links">',
                    'after' => '</div>',
                ));
            ?>
            <?php if(get_the_tags() || has_action('goodsoul_share_button')) : ?>
                <div class="post-tag">
                    <div class="wrapper-box">
                        <?php if(get_the_tags()) : ?>
                            <div class="hint">
                                <?php
                                    if($blog_signle_tag_text != '') :
                                        echo esc_html($blog_signle_tag_text);
                                    else :
                                        echo esc_html__('Tags :', 'goodsoul');
                                    endif;
                                ?>
                            </div>
                            <div class="tag">
                                <?php goodsoul_tag_list(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php
                        do_action('goodsoul_share_button');
                    ?>
                </div> 
            <?php endif; ?>
        </div>
        <div class="bottom-content">
            <?php get_template_part('template-parts/single/author'); ?>
            
            <?php
                if($blog_single_next_preview) :
                    get_template_part('template-parts/single/post-pagination');
                endif;
            ?>
        </div>
    </div>
</div>