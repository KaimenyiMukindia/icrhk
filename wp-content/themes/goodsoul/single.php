<?php 
    get_header();
    $blog_single_page_header_img = goodsoul_get_options('blog_single_page_header_img');
	$blog_single_page_header = goodsoul_get_options('blog_single_page_header');
	$blog_single_page_header_text = goodsoul_get_options('blog_single_page_header_text');
    $blog_single_page_breadcrumbs = goodsoul_get_options('blog_single_page_breadcrumbs');
    if (is_active_sidebar('sidebar-1')) :
        $blog_class = 'col-lg-8';
    else :
        $blog_class = 'col-lg-12';
    endif;
?>

<?php
    $blog_single_page_header = goodsoul_get_options('blog_single_page_header');
    $blog_single_page_header_text = goodsoul_get_options('blog_single_page_header_text');
    $blog_single_page_breadcrumbs = goodsoul_get_options('blog_single_page_breadcrumbs');
?>
<?php if( $blog_single_page_header == 1 ) : ?>
    <section class="page-title breadcrumb-blog-single-bg">
        <div class="auto-container">
            <div class="content-box">
                <h1><?php echo wp_kses($blog_single_page_header_text, 'code_contxt'); ?></h1>
                <?php if($blog_single_page_breadcrumbs != '') : ?>
                    <ul class="bread-crumb">
                        <li><a class="home" href="<?php echo esc_url(home_url('/')); ?>"><span class="fa fa-home"></span></a></li>
                        <li><?php echo wp_kses($blog_single_page_breadcrumbs, 'code_contxt'); ?></li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>


    <div class="sidebar-page-container">
        <div class="auto-container">
            <div class="row">
                <div class="<?php echo esc_attr($blog_class); ?>">
                    <div class="post-wrapper">
                        <?php while (have_posts()) :
                            the_post(); 
                        ?>
                            <?php get_template_part('template-parts/single/content', get_post_format()); ?>
                            <div class="comments-area">
                                <?php 
                                    if (comments_open() || get_comments_number()) :
                                        comments_template();
                                    endif;
                                ?>
                            </div>
                        <?php endwhile; ?>
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
    
<?php get_footer();?>