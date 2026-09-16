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
?>
<?php if ( $header_topbar_onoff ) : ?>
		<div class="top-bar theme-bg">
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
						<div class="text"><?php echo isset( $header_title_text_option ) ? $header_title_text_option : ''; ?> <a href="<?php echo esc_url( $header_top_donate_site_link ); ?>" class="donate-box-btn"><?php echo isset( $header_top_button_text ) ? $header_top_button_text : ''; ?></a></div>
					</div>
					<div class="right-content">
						<?php if ( $header_info_bar ) { ?>
							<ul class="contact-info">
								<?php if ( $header_info_bar[2] ) : ?>
									<li><span class="flaticon-mail"></span><a href="<?php echo esc_url( $header_info_bar[2] ); ?>"><?php echo wp_kses( $header_info_bar[2], 'code_contxt' ); ?></a></li>
								<?php endif; ?>
								<?php if ( $header_info_bar[1] ) : ?>
									<li><span class="flaticon-phone"></span><a href="tel:<?php echo esc_attr( $header_info_bar[1] ); ?>"><?php echo wp_kses( $header_info_bar[1], 'code_contxt' ); ?></a></li>
								<?php endif; ?>
							</ul>
						<?php } ?>    
						<?php if ( $header_topbar_social_onoff === '1' ) : ?>
							<ul class="social-icon-one">
								<?php
								if ( $header_topbar_social ) :
									echo wp_kses( $header_topbar_social, 'code_contxt' );
								endif;
								?>
							</ul>
						<?php endif; ?>
					</div>
				</div>                
			</div>
		</div>
<?php endif; ?>
