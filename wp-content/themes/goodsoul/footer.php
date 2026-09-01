<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package goodsoul
 */


$goodsoul_metabox_footer_style     = get_post_meta( get_queried_object_id(), 'goodsoul_theme_metabox_show_footer', true );
$goodsoul_metabox_footer_content   = get_post_meta( get_queried_object_id(), 'goodsoul_theme_metabox_footer_content', true );
$goodsoul_metabox_footer_copyright = get_post_meta( get_queried_object_id(), 'goodsoul_theme_metabox_footer_copyright', true );
$goodsoul_metabox_footer1_link     = get_post_meta( get_queried_object_id(), 'goodsoul_theme_metabox_footer1_link', true );
$goodsoul_metabox_footer3_link     = get_post_meta( get_queried_object_id(), 'goodsoul_theme_metabox_footer3_link', true );
$goodsoul_metabox_footer5_link     = get_post_meta( get_queried_object_id(), 'goodsoul_theme_metabox_footer5_link', true );
$goodsoul_metabox_footer_shortcode = get_post_meta( get_queried_object_id(), 'goodsoul_theme_metabox_footer_shortcode', true );
$footer_style                      = goodsoul_get_options( 'footer_tob_bar_style' );
if ( ! empty( $goodsoul_metabox_footer_style ) ) {
	$footer_style = $goodsoul_metabox_footer_style;
}
  $upper_content = goodsoul_get_options( 'upper_content' );
if ( ! empty( $goodsoul_metabox_footer_content ) ) {
	$upper_content = $goodsoul_metabox_footer_content;
}
  $copyright_text = goodsoul_get_options( 'footer_copyright' );
if ( ! empty( $goodsoul_metabox_footer_copyright ) ) {
	$copyright_text = $goodsoul_metabox_footer_copyright;
}
$footer_social_area = goodsoul_get_options( 'footer_social_area_one' );

if ( $goodsoul_metabox_footer_style == 'style_1' && ! empty( $goodsoul_metabox_footer1_link ) ) {
	$footer_social_area = $goodsoul_metabox_footer1_link;
} elseif ( $goodsoul_metabox_footer_style == 'style_3' && ! empty( $goodsoul_metabox_footer3_link ) ) {
	$footer_social_area = $goodsoul_metabox_footer3_link;
} elseif ( $goodsoul_metabox_footer_style == 'style_5' && ! empty( $goodsoul_metabox_footer5_link ) ) {
	$footer_social_area = $goodsoul_metabox_footer5_link;
}
$logo_Image        = goodsoul_get_options( 'footer_logo_image' );
$footer_logo_image = '';
if ( isset( $logo_Image ) && ! empty( $logo_Image ) ) {
	$footer_logo_image = $logo_Image['url'];
}

  $goodsoul_theme_metabox_footer_logo = get_post_meta( get_queried_object_id(), 'goodsoul_theme_metabox_footer_logo', array( 'size' => 'full' ) );
if ( ! empty( $goodsoul_theme_metabox_footer_logo ) ) {
	$footer_logo_image = wp_get_attachment_url( $goodsoul_theme_metabox_footer_logo );
}
$back_to_top          = goodsoul_get_options( 'back_to_top_on_off' );
$newsletter_shortcode = goodsoul_get_options( 'newsletter_shortcode' );
if ( $goodsoul_metabox_footer_style == 'style_4' || $goodsoul_metabox_footer_style == 'style_6' && ! empty( $goodsoul_metabox_footer_shortcode ) ) {
	$newsletter_shortcode = $goodsoul_metabox_footer_shortcode;
}
$footer_template = '';

if ( class_exists( '\Elementor\Plugin' ) ) {
	$pluginElementor = \Elementor\Plugin::instance();
	$footer_template = $pluginElementor->frontend->get_builder_content( $upper_content );
}
?>
<?php
if ( $footer_style == 'style_1' ) {
	$footer_class = ' ';
} elseif ( $footer_style == 'style_2' ) {
	$footer_class = 'style-two';
} elseif ( $footer_style == 'style_3' ) {
	$footer_class = 'style-three';
} elseif ( $footer_style == 'style_4' ) {
	$footer_class = 'style-four';
} elseif ( $footer_style == 'style_5' ) {
	$footer_class = 'style-five';
} elseif ( $footer_style == 'style_6' ) {
	$footer_class = 'style_new_one style-four';
	$bottom_class = 'footer-bottom-custom';
}
?>
<footer class="main-footer <?php echo esc_attr( $footer_class ); ?>">
	<div class="auto-container">
	
		<?php
		if ( $footer_template != '' ) {
			?>
		
		<div class="widget-wrapper">
			<?php echo do_shortcode( $footer_template ); ?>
		</div>
			<?php
		}
		?>
		<?php if ( $footer_style == 'style_1' ) { ?>
		<div class="footer-bottom">
			<div class="left-content">
				<?php if ( $footer_logo_image != '' ) { ?>
				<div class="icon">
					<img src="<?php echo esc_url( $footer_logo_image ); ?>" alt="<?php esc_attr_e( 'footer icon', 'goodsoul' ); ?>">
				</div>
				<?php } ?>
				<div class="copyright-text">
					<?php
					if ( $copyright_text != '' ) {
						echo wp_kses( $copyright_text, 'code_contxt' );
					} else {
						$footer_copyright_deafult = '&copy; 2019–2020 All Rights Reserved by <a href="#">Goodsoul.</a>';
						echo wp_kses( $footer_copyright_deafult, 'code_contxt' );
					}
					?>
				</div>
			</div>
			<div class="right-content">
				<ul class="social-icon-three">
					<?php echo wp_kses( $footer_social_area, 'code_contxt' ); ?>
				</ul>
			</div>
		</div>
	</div>
	<?php } elseif ( $footer_style == 'style_2' ) { ?>
	<div class="footer-bottom-two">
		<div class="copy-right-text">
			<?php
			if ( $copyright_text != '' ) {
				echo wp_kses( $copyright_text, 'code_contxt' );
			} else {
				$footer_copyright_deafult = '&copy; 2019–2020 All Rights Reserved by <a href="#">Goodsoul.</a>';
				echo wp_kses( $footer_copyright_deafult, 'code_contxt' );
			}
			?>
		</div>
	</div>
	<?php } ?>
	</div>
	<?php if ( $footer_style == 'style_3' ) { ?>
			<div class="footer-bottom-three">
				<div class="auto-container">
					<ul class="social-icon-four">
					<?php echo wp_kses( $footer_social_area, 'code_contxt' ); ?>
					</ul>
				</div>
			</div>
	<?php } elseif ( $footer_style == 'style_4' || $footer_style == 'style_6' ) { ?>
			<div class="footer-bottom-two <?php echo esc_attr( $bottom_class ); ?>">
				<div class="auto-container">
					<?php if ( ! empty( $newsletter_shortcode ) ) : ?>
					<div class="newsletter-one">
						<?php echo do_shortcode( $newsletter_shortcode ); ?>
					</div>
					<?php endif; ?>
					<div class="copy-right-text">
						<?php
						if ( $copyright_text != '' ) {
							echo wp_kses( $copyright_text, 'code_contxt' );
						} else {
							$footer_copyright_deafult = '&copy; 2019–2020 All Rights Reserved by Goodsoul.';
							echo wp_kses( $footer_copyright_deafult, 'code_contxt' );
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php } elseif ( $footer_style == 'style_5' ) { ?>
	<div class="footer-bottom-two">
		<div class="auto-container">
			<div class="row m-0 justify-content-between">
				<div class="copy-right-text">
					<?php
					if ( $copyright_text != '' ) {
						echo wp_kses( $copyright_text, 'code_contxt' );
					} else {
						$footer_copyright_deafult = '&copy; 2019–2020 All Rights Reserved by Goodsoul.';
						echo wp_kses( $footer_copyright_deafult, 'code_contxt' );
					}
					?>
				</div>
				<ul class="footer-menu">
				<?php echo wp_kses( $footer_social_area, 'code_contxt' ); ?>
				</ul>
			</div>
		</div>
	</div>
	<?php } ?>
</footer>
		<?php
		if ( goodsoul_get_options( 'back_to_top_on_off' ) == '1' ) :
			?>
			<div class="scroll-to-top scroll-to-target" data-target="html"><span class="icon flaticon-arrow"></span></div>
			<?php
		endif;
		?>
<?php wp_footer(); ?>
</body>
</html>
