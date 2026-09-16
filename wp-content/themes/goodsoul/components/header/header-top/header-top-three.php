<?php
$header_topbar_onoff         = goodsoul_get_options( 'header_topbar_onoff' );
$header_tob_bar_style        = goodsoul_get_options( 'header_tob_bar_style' );
$header_topbar_social_onoff  = goodsoul_get_options( 'header_topbar_social_onoff' );
$header_topbar_social        = goodsoul_get_options( 'header_topbar_social' );
$header_info_bar             = goodsoul_get_options( 'header_info_bar' );
$header_title_text_option    = goodsoul_get_options( 'header_title_text_option' );
$header_top_button_text      = goodsoul_get_options( 'header_top_button_text' );
$header_top_donate_site_link = goodsoul_get_options( 'header_top_donate_site_link' );
$header_top_language_setting = goodsoul_get_options( 'header_top_language_setting' );
$header_topbar_login         = goodsoul_get_options( 'header_topbar_login' );
?>
<?php if ( $header_topbar_onoff ) : ?>
	<div class="top-bar style-three">
		<div class="auto-container">
			<div class="wrapper-box">
				<div class="left-content">
					<div class="language-switcher">
						<div class="languages">
						<?php
						if ( $header_top_language_setting ) :
							echo do_shortcode( $header_top_language_setting );
							endif;
						?>
						</div>
					</div>
					<?php if ( $header_info_bar ) { ?>
						<ul class="contact-info">
							<?php if ( $header_info_bar[2] ) : ?>
								<li><span class="flaticon-mail"></span><a href="<?php echo esc_url( $header_info_bar[2] ); ?>"><?php echo wp_kses( $header_info_bar[2], 'code_contxt' ); ?></a></li>
							<?php endif; ?>
							<?php if ( $header_info_bar[1] ) : ?>
								<li><span class="flaticon-phone"></span><a href="tel:+211456789"><?php echo wp_kses( $header_info_bar[1], 'code_contxt' ); ?></a></li>
							<?php endif; ?>
						</ul>
					<?php } ?>
				</div>
				<div class="right-content">
					<?php
					if ( $header_topbar_login ) :
						echo wp_kses( $header_topbar_login, 'code_contxt' );
						endif;
					?>
					<ul class="social-icon-one">
					<?php if ( $header_topbar_social_onoff === '1' ) : ?>
						<?php
						if ( $header_topbar_social ) :
							echo wp_kses( $header_topbar_social, 'code_contxt' );
							endif;
						?>
					<?php endif; ?>
					</ul>
				</div>
			</div>                
		</div>
	</div>
	<?php
endif;
