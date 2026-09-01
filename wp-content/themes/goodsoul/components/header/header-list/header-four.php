<header class="main-header header-style-four">
    <?php get_template_part('components/header/header-top/header-top-four'); ?>
    <div class="header-upper style-four">
        <div class="auto-container">
            <div class="wrapper-box">
                <?php
                    $header_topbar_social_onoff = goodsoul_get_options('header_topbar_social_onoff');
                    $header_topbar_social = goodsoul_get_options('header_topbar_social');
                    $header_tob_bar_style = goodsoul_get_options('header_tob_bar_style');
                    $goodsoul_theme_metabox_header_style = get_post_meta(get_queried_object_id(), 'goodsoul_theme_metabox_header_style', true);
                    if($goodsoul_theme_metabox_header_style === '4' && !empty($goodsoul_theme_metabox_header_style) || $header_tob_bar_style === '4' ) :
                        ?>
                            <div class="left-column">
                                <?php if($header_topbar_social_onoff === '1' ) : ?>
                                    <ul class="social-icon-five">
                                        <?php
                                            if($header_topbar_social) : 
                                                echo wp_kses($header_topbar_social, 'code_contxt'); 
                                            endif;
                                        ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php 
                    endif;
                ?>
                <?php get_template_part('components/header/main-menu'); ?>
            </div>
        </div>
    </div>
    <?php get_template_part('components/header/sticky-header'); ?>
    <?php get_template_part('components/header/mobile-menu'); ?>
</header>