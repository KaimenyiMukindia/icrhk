<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package goodsoul
 */

get_header();

?>



<?php
    if (is_active_sidebar('sidebar-1')) :
        $blog_class = 'col-lg-8';
    else :
        $blog_class = 'col-lg-12';
    endif;
    $hide_date = goodsoul_get_options('hide_date');
    $hide_info_post = goodsoul_get_options('hide_info_post');
?>
<section class="page-title breadcrumb-blog-single-bg">
    <div class="auto-container">
        <div class="content-box">
            <?php if ( have_posts() ) : ?>
                    <h1 class="inner-banner__title"><?php printf( esc_html__( 'Search Results for: %s', 'goodsoul' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
            <?php else : ?>
                    <h1 class="inner-banner__title"><?php esc_html_e( 'Nothing Found', 'goodsoul' ); ?></h1>
            <?php endif; ?>
        </div>
    </div>
</section>
<div class="sidebar-page-container">
    <div class="auto-container">
        <div class="row">
            <div class="<?php echo esc_attr($blog_class); ?>">
                <div class="post-wrapper">
                    <?php 
                        if ( have_posts() ) : 
                            while ( have_posts() ) : the_post();
                            $post_thumb_class = 'no-post-thumb';
                            if (has_post_thumbnail()) {
                                $post_thumb_class = '';
                            }
                    ?>
                        <!-- News Block Three -->
                        <div class="news-block-three <?php echo esc_attr( $post_thumb_class ); ?>">
                            <?php if (is_sticky()) { ?>
                                <div class="sticky_post_icon" title="<?php esc_attr_e('Sticky Post', 'goodsoul') ?>"><span class="fa fa-map-pin"></span></div>
                            <?php } ?>
                            <div class="inner-box wow fadeInUp" data-wow-delay="200ms">
                                <div class="image">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail();?></a>
                                    <?php endif; ?>
                                    <?php if($hide_info_post == '1') : ?>
                                        <div class="post-meta-info">
                                            <a href="<?php the_permalink(); ?>"><span class="flaticon-eye"></span><?php 
                                                $post_views_count = get_post_meta( get_queried_object_id(), 'post_views_count', true );
                                                if ( ! empty( $post_views_count ) ) {
                                                    echo wp_kses($post_views_count, 'code_contxt');
                                                }
                                            ?></a>
                                            <a href="<?php the_permalink(); ?>"><span class="flaticon-comment"></span></a><?php goodsoul_comments_count(); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="lower-content">
                                    
                                    <h2><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2>
                                    <div class="post-info">
                                        <div class="author">
                                            <div class="image"><?php goodsoul_author_image(); ?></div>
                                            <div class="author-title"><?php goodsoul_posted_by(); ?></div>
                                        </div>
                                        <?php if($hide_date == '1') : ?>
                                            <div class="date"><span class="flaticon-clock"></span><?php the_date();?></div>
                                        <?php endif; ?>
										<?php if(get_the_category()) : ?>
                                        	<div class="category"><span class="flaticon-folder"></span><?php goodsoul_category_list(); ?></div>
										<?php endif; ?>
                                    </div>
                                    
                                    <div class="text"><?php the_excerpt() ?></div>
                                    <?php
                                        wp_link_pages(array(
                                            'before' => '<div class="page-links">',
                                            'after' => '</div>',
                                        ));
                                    ?>
                                    <div class="share-info">
                                        <div class="link-btn"><a href="<?php the_permalink();?>" class="theme-btn btn-style-one"><span><?php _e("Read More","goodsoul");?></span></a></div>
                                        <?php
                                            do_action('goodsoul_share_button');
                                        ?>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <!-- News Block Three -->
                    <?php
							endwhile; 
						else :
							get_template_part('template-parts/content', 'none');
						endif;
                    ?>
                    <?php if(get_the_posts_pagination()) : ?>
                        <div class="posts-pagination mb-30 mt-20">
                            <?php
                                the_posts_pagination(array(
                                    'mid_size' => 2,
                                    'prev_text' => '<span class="fa fa-angle-left"></span>',
                                    'next_text' => '<span class="fa fa-angle-right"></span>'
                                ));
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (is_active_sidebar('sidebar-1')) { ?>
                <div class="col-lg-4">
                    <?php get_sidebar(); ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php
get_footer();
