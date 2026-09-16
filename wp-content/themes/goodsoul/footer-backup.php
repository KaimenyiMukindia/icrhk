<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package goodsoul
 */
?>

<?php
    $footer_top_elemenr = goodsoul_get_options('footer_top_elemenr');
    $goodsoul_theme_metabox_show_footer_ele = get_post_meta(get_queried_object_id(), 'goodsoul_theme_metabox_show_footer_ele', true);
    
    if (class_exists("\\Elementor\\Plugin")) {
        $pluginElementor = \Elementor\Plugin::instance();
        $footer_top_elemenr = $pluginElementor->frontend->get_builder_content($footer_top_elemenr);
    }

    if($goodsoul_theme_metabox_show_footer_ele == 'off') :
    else :
        echo sprintf(__('%s', 'goodsoul'), $footer_top_elemenr);
    endif;
    



    $footer_tob_bar_style = goodsoul_get_options('footer_tob_bar_style');
    $goodsoul_theme_metabox_show_footer = get_post_meta(get_queried_object_id(), 'goodsoul_theme_metabox_show_footer', true);
    if(!empty($goodsoul_theme_metabox_show_footer)) :
        if ($goodsoul_theme_metabox_show_footer == 1 ) :
            get_template_part('components/footer/footer-list/footer-one');
        elseif ($goodsoul_theme_metabox_show_footer == 2 ) :
            get_template_part('components/footer/footer-list/footer-two');
        elseif ($goodsoul_theme_metabox_show_footer == 3 ) :
            get_template_part('components/footer/footer-list/footer-three');
        elseif ($goodsoul_theme_metabox_show_footer == 4 ) :
            get_template_part('components/footer/footer-list/footer-four');
        elseif ($goodsoul_theme_metabox_show_footer == 5 ) :
            get_template_part('components/footer/footer-list/footer-five');
        elseif ($goodsoul_theme_metabox_show_footer == 6 ) :
        endif;
    else : 
        if ($footer_tob_bar_style == 1 ) :
            get_template_part('components/footer/footer-list/footer-one');
        elseif ($footer_tob_bar_style == 2 ) :
            get_template_part('components/footer/footer-list/footer-two');
        elseif ($footer_tob_bar_style == 3 ) :
            get_template_part('components/footer/footer-list/footer-three');
        elseif ($footer_tob_bar_style == 4 ) :
            get_template_part('components/footer/footer-list/footer-four');
        elseif ($footer_tob_bar_style == 5 ) :
            get_template_part('components/footer/footer-list/footer-five');
        else :
            get_template_part('components/footer/footer-list/footer-one');
        endif;
    endif;
?>

<?php
    $goodsoul_theme_metabox_show_backtoo_top = get_post_meta(get_queried_object_id(), 'goodsoul_theme_metabox_show_backtoo_top', true);
    if($goodsoul_theme_metabox_show_backtoo_top == 'on') :
?>
    <div class="scroll-to-top scroll-to-target" data-target="html"><span class="icon flaticon-arrow"></span></div>
<?php
    endif;
?>
<?php wp_footer(); ?>

</body>
</html>
