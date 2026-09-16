<header class="main-header">
    <?php get_template_part('components/header/header-top/header-top-one'); ?>
    <div class="header-upper">
        <div class="auto-container">
            <div class="wrapper-box">
                <?php
                    do_action('goodsoul_logo_fun');
                ?>
                <?php get_template_part('components/header/main-menu'); ?>
            </div>
        </div>
    </div>
    <?php get_template_part('components/header/sticky-header'); ?>
    <?php get_template_part('components/header/mobile-menu'); ?>
</header>