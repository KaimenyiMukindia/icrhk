<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package goodsoul
 */

?>

<!-- Single Blog Post -->
<div class="single-blog-post">
    <div class="inner-box">
        <div class="top-content">
            <div class="text"><?php the_content(); ?></div>
            <?php
                wp_link_pages(array(
                    'before' => '<div class="page-links">',
                    'after' => '</div>',
                ));
            ?>
        </div>
	</div>
</div>