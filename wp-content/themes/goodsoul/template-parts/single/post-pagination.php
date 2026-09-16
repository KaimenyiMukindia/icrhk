<div class="blog-post-pagination">
    <div class="wrapper-box">
    <?php
        $goodsoul_prev_post = get_adjacent_post(false, '', true);
        $goodsoul_next_post = get_adjacent_post(false, '', false);
        if (!empty($goodsoul_prev_post)) { ?>
            <div class="prev-post">
                <a href="<?php echo esc_url(get_permalink($goodsoul_prev_post->ID)); ?>"><?php _e('Previous Post','goodsoul'); ?> </a>
                <h4><?php echo esc_html($goodsoul_prev_post->post_title); ?></h4>
            </div>
        <?php } ?>
        <?php if (!empty($goodsoul_next_post)) {  ?>   
            <div class="page-view"><span class="fa fa-th"></span></div>
            <div class="next-post">
                <a href="<?php echo esc_url(get_permalink($goodsoul_next_post->ID)); ?>"><?php _e('Next Topic','goodsoul'); ?></a>
                <h4><?php echo esc_html($goodsoul_next_post->post_title); ?></h4>
            </div>
        <?php } ?>
    </div>
</div>