<?php
    if ( class_exists( 'woocommerce' ) ) :
        ?>
            <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                <input id="<?php echo esc_attr(uniqid('search-form-')); ?>" placeholder="<?php esc_attr_e('Search...', 'goodsoul');?>" type="text" value="<?php echo get_search_query(); ?>" name="s" required="required"/>
                <button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
            </form>
        <?php 
    else :
        ?>
            <div class="sidebar-widget search-box">
                <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="form-group">
                        <input type="search" id="<?php echo esc_attr(uniqid('search-form-')); ?>" class="search-field"
                            placeholder="<?php esc_attr_e('Search', 'goodsoul');?>" value="<?php echo get_search_query(); ?>" name="s" required="required"/>
                        <button type="submit" class="search-btn"><span class="icon flaticon-search-1"></span></button>
                    </div>
                </form>
            </div>
        <?php
    endif;
