<section class="blog-section">
    <div class="auto-container">
        <div class="row">
            <?php 
                if ( have_posts() ) : 
                    while ( have_posts() ) : the_post();
            ?>
                <div class="col-lg-4 col-md-6 news-block-two style-three">
                    <div class="inner-box wow fadeInUp" data-wow-delay="200ms">
                        <div class="image">
                            <div class="category"><?php goodsoul_category_list(); ?></div>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail();?>
                            </a>
                            <div class="post-meta-info">
                                <a href="<?php the_permalink(); ?>"><span class="flaticon-eye"></span><?php 
                                    $post_views_count = get_post_meta( get_queried_object_id(), 'post_views_count', true );
                                    if ( ! empty( $post_views_count ) ) {
                                        echo wp_kses($post_views_count, 'code_contxt');
                                    }
                                ?></a>
                                <a href="<?php the_permalink(); ?>"><span class="flaticon-comment"></span></a><?php goodsoul_comments_count(); ?>
                            </div>
                        </div>
                        <div class="lower-content">
                            <div class="date"><span class="flaticon-clock"></span><?php the_date();?></div>
                            <h4><a href="<?php the_permalink();?>"><?php the_title();?></a></h4>
                            <div class="author-info">
                                <div class="image"><?php goodsoul_author_image(); ?></div>
                                <div class="author-title"><?php goodsoul_posted_by(); ?></div>
                                <?php
                                    do_action('goodsoul_share_button');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                    endwhile; 
                endif; 
            ?>
        </div>
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
</section>