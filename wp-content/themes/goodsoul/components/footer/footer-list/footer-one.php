<?php
$footer_copyright_one   = goodsoul_get_options( 'footer_copyright_one' );
$footer_social_area_one = goodsoul_get_options( 'footer_social_area_one' );
$footer_logo_image      = goodsoul_get_options( 'footer_logo_image' );
?>
<footer class="main-footer">
	<div class="auto-container">
		<div class="footer-bottom">
			<div class="left-content">
				<div class="icon"><img src="<?php echo esc_url( $footer_logo_image['url'] ); ?>" alt="<?php esc_attr_e( 'footer icon', 'goodsoul' ); ?>"></div>
				<div class="copyright-text">
					<?php
						$footer_copyright_one = goodsoul_get_options( 'footer_copyright_one' );
					if ( $footer_copyright_one != '' ) :
						echo wp_kses( $footer_copyright_one, 'code_contxt' );
						else :
							$footer_copyright_deafult = '© 2009–2020 All Rights Reserved by <a href="#">Goodsoul.</a> <br> Designed By <a href="#">Themekalia.</a>';
							echo wp_kses( $footer_copyright_deafult, 'code_contxt' );
						endif;
						?>
				</div>
			</div>
			<?php echo wp_kses( $footer_social_area_one, 'code_contxt' ); ?>
		</div>
	</div>
</footer>
