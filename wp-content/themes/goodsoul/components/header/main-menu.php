<?php
    $header_cart_icon = goodsoul_get_options('header_cart_icon');
    $header_donate_button = goodsoul_get_options('header_donate_button');
    $main_menu_right = '';
    if(!$header_cart_icon && !$header_donate_button ) :
        $main_menu_right = 'main-menu-right';
    endif;
?>
<div class="right-column">
    <div class="option-wrapper">
        <div class="nav-outer">
            <nav class="main-menu navbar-expand-xl navbar-dark <?php echo esc_attr($main_menu_right); ?>">
                <div class="collapse navbar-collapse">        
                    <?php
                        if ( has_nav_menu( 'primary' ) ||  has_nav_menu( 'menu-1' )  ) { 
							
                               $themeloc='';
                          
								if(has_nav_menu( 'menu-1' )){
									$themeloc='menu-1';
								}
								if(has_nav_menu( 'primary' )){
									$themeloc='primary';
								}
								wp_nav_menu( array(
									'theme_location' => $themeloc,
									'menu_class'      => 'navigation clearfix',
									'container'       => '',
								) );
                             }else{ 
							
                                wp_nav_menu( array(
                                    'menu_class'      => 'navigation clearfix',
                                    'container'       => 'ul',
                                ) );
                        }
                    ?>
                </div>    
            </nav>
        </div>
        <?php get_template_part('components/header/menu-info'); ?>
    </div>
</div>