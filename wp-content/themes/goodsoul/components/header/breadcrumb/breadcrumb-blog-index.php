<?php
    if (is_front_page()) {
        return false;    
    } else {
?>
    <?php
        $blog_page_header = goodsoul_get_options('blog_page_header');
        $blog_page_header_text = goodsoul_get_options('blog_page_header_text');
        $blog_page_breadcrumbs = goodsoul_get_options('blog_page_breadcrumbs');
    ?>
    <?php if( $blog_page_header == 1 ) : ?>
        <section class="page-title breadcrumb-blog-bg">
            <div class="auto-container">
                <div class="content-box">
                    <h1><?php echo wp_kses($blog_page_header_text, 'code_contxt'); ?></h1>
                    <?php if($blog_page_breadcrumbs != '') : ?>
                        <ul class="bread-crumb">
                            <li><a class="home" href="<?php echo esc_url(home_url('/')); ?>"><span class="fa fa-home"></span></a></li>
                            <li><?php echo wp_kses($blog_page_breadcrumbs, 'code_contxt'); ?></li>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
<?php } ?>
