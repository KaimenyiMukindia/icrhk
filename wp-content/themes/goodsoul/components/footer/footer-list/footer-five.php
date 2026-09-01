<footer class="main-footer style-five padding-top-f">
	<div class="footer-bottom-two">
		<div class="auto-container">
			<div class="row m-0 justify-content-between">
				<div class="copy-right-text">
					<?php
						$footer_copyright = goodsoul_get_options( 'footer_copyright' );
					if ( $footer_copyright != '' ) :
						echo wp_kses( $footer_copyright, 'code_contxt' );
						else :
							$footer_copyright_deafult = '© 2009–2020 All Rights Reserved by <a class="theme-color-five" href="#">Goodsoul.</a>';
							echo wp_kses( $footer_copyright_deafult, 'code_contxt' );
						endif;
						?>
				</div>
				<?php $footer_social_area = goodsoul_get_options( 'footer_social_area' ); if ( $footer_social_area ) : ?>
					<ul class="footer-menu">
						<?php
							echo wp_kses( $footer_social_area, 'code_contxt' );
						?>
					</ul>
				<?php endif; ?>
			</div>                
		</div>
	</div>
	<?php
		$back_to_top_on_off = goodsoul_get_options( 'back_to_top_on_off' );
	if ( $back_to_top_on_off == 1 ) :
		do_action( 'back_to_top' );
		endif;
	?>
</footer>
