<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package goodsoul
 */
	$preloader_on_off = goodsoul_get_options( 'preloader_on_off' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
	<?php
	if ( isset( $preloader_on_off ) && ( $preloader_on_off == 1 ) ) :
		do_action( 'goodsoul_preloader' );
		endif;
		get_template_part( 'components/header/header' );
		get_template_part( 'components/header/sidebar-menu' );
		$goodsoul_theme_metabox_show_breadcrumb = get_post_meta( get_queried_object_id(), 'goodsoul_theme_metabox_show_breadcrumb', true );
		
	if ( $goodsoul_theme_metabox_show_breadcrumb != 'off' ) {
		if ( is_blog() ) {
			if ( is_home() ) {
				get_template_part( 'components/header/breadcrumb/breadcrumb-blog-index' );
			} elseif ( is_archive() ) {
				get_template_part( 'components/header/breadcrumb/breadcrumb-archive' );
			}
		} else {
			if ( ! is_home() && ! is_front_page() && ! is_search() && ! is_404() ) {
				get_template_part( 'components/header/breadcrumb/breadcrumb-page' );
			}
		}
	}
	?>

