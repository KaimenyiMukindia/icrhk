<?php
    $goodsoul_theme_metabox_page_meta_box = get_post_meta(get_queried_object_id(), 'goodsoul_theme_metabox_breadcrumb_text', true);
?>
<section class="page-title page-bread-cumb">
    <div class="auto-container">
        <div class="content-box">
            <h1><?php the_title();?></h1>
            <?php if($goodsoul_theme_metabox_page_meta_box != '') : ?>
                <ul class="bread-crumb">
                    <li><a class="home" href="<?php echo esc_url(home_url('/')); ?>"><span class="fa fa-home"></span></a></li>
                    <li><?php echo wp_kses($goodsoul_theme_metabox_page_meta_box, 'code_contxt'); ?></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</section>