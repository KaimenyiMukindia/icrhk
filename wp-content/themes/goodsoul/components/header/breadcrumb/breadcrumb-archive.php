
<?php
    if ( class_exists( 'woocommerce' ) && is_woocommerce() ) : 
    else : 
        ?>
            <section class="page-title breadcrumb-blog-single-bg">
                <div class="auto-container">
                    <div class="content-box">
                        <h1><?php echo wp_kses(get_the_archive_title(), 'code_contxt'); ?></h1>
                    </div>
                </div>
            </section>
        <?php 
	endif;