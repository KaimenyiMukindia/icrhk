<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package goodsoul
 */


$blog_style = goodsoul_get_options('blog_style');

$blog_col = 'col-lg-12 col-md-12';
if ($blog_style == '2') :
	$blog_col = 'col-lg-6 col-md-6';
endif;
$is_no_post_thumb = '';
if (!has_post_thumbnail()) :
    $is_no_post_thumb = 'no-post-thumb';
endif;
?>
<!--News Block-->
<div class="news-block <?php echo esc_attr($blog_col . ' ' . $is_no_post_thumb); ?> col-sm-12">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="inner-box">
			<?php if (has_post_thumbnail()) : ?>
				<div class="image-box">
					<div class="image">
						<?php goodsoul_post_thumbnail(); ?>
					</div>
				</div>
			<?php endif; ?>
			<div class="lower-content">
				<?php if (is_sticky()) { ?>
					<div class="sticky_post_icon" title="<?php esc_attr_e('Sticky Post', 'goodsoul') ?>"><span class="fas fa-map-pin"></span></div>
				<?php } ?>
				<?php if ($blog_style == '2') : ?>
					<div class="date-two"><?php echo get_the_date( 'j' ); ?> <span class="month"><?php echo get_the_date( 'M' ); ?></span></div>
				<?php else : ?>
					<div class="date">
						<?php goodsoul_posted_on(); ?>
					</div>
				<?php endif; ?>
                <?php
                    if (is_singular()) :
                        the_title('<h3 class="entry-title">', '</h3>');
                    else :
                        the_title('<h3 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h3>');
                    endif;
                ?>
				<div class="text">
					<?php
						if (get_option('rss_use_excerpt')) {
							the_excerpt();
						} else {
							the_excerpt();
						}
						wp_link_pages(array(
							'before' => '<div class="page-links">',
							'after' => '</div>',
						));
					?>
				</div>
				<div class="post-meta">
					<ul class="clearfix">
						<li><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><span class="icon fa fa-user"></span><?php echo esc_html( get_the_author() ); ?></a></li>
						<li><a href="<?php echo esc_url(get_permalink()); ?>"><span class="icon fa fa-comments"></span></a><?php goodsoul_comments_count(); ?></li>
					</ul>
				</div>
			</div>
		</div>
	</article>
</div>