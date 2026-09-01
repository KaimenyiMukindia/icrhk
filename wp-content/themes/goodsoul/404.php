<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package goodsoul
 */

get_header();
?>
<?php
    $goodsoul_theme_metabox_page_meta_box = get_post_meta(get_queried_object_id(), 'goodsoul_theme_metabox_breadcrumb_text', true);
?>
<section class="page-title breadcrumb-blog-single-bg">
    <div class="auto-container">
        <div class="content-box">
            <h1><?php esc_html_e('Error Page','goodsoul');?></h1>
            <?php if($goodsoul_theme_metabox_page_meta_box != '') : ?>
                <ul class="bread-crumb">
                    <li><a class="home" href="<?php echo esc_url(home_url('/')); ?>"><span class="fa fa-home"></span></a></li>
                    <li><?php echo wp_kses($goodsoul_theme_metabox_page_meta_box, 'code_contxt'); ?></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</section>
<section class="news-section error-area-404">
	<div class="auto-container">
		<div class="row">
			<div class="col-md-12">
				<div class="no-results not-found error-404 not-found sidebar">
						<h1><?php esc_html_e('404', 'goodsoul'); ?></h1>
						<h2>
							<?php esc_html_e('Oops! That page can not be found.', 'goodsoul'); ?>
						</h2>
						<p><?php esc_html_e('It looks like nothing was found at this location. Maybe try a search?', 'goodsoul'); ?></p>
						<div class="nothing-found-search">
						<?php
							get_search_form();
						?>
						</div>
				</div><!-- .no-results -->
			</div>
		</div>
	</div>
</section>
<?php
get_footer();
