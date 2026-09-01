<?php
	$btn_style_menu_donate               = '';
	$header_tob_bar_style                = goodsoul_get_options( 'header_tob_bar_style' );
	$goodsoul_theme_metabox_header_style = get_post_meta( get_queried_object_id(), 'goodsoul_theme_metabox_header_style', true );
if ( ! empty( $goodsoul_theme_metabox_header_style ) ) :
	if ( $goodsoul_theme_metabox_header_style == 1 ) :
		$btn_style_menu_donate = 'btn-style-one';
		elseif ( $goodsoul_theme_metabox_header_style == 2 ) :
			$btn_style_menu_donate = 'btn-style-four';
		elseif ( $goodsoul_theme_metabox_header_style == 3 ) :
			$btn_style_menu_donate = 'btn-style-nine donate-box-btn';
		elseif ( $goodsoul_theme_metabox_header_style == 4 ) :
			$btn_style_menu_donate = '';
		elseif ( $goodsoul_theme_metabox_header_style == 4 ) :
			$btn_style_menu_donate = '';
		endif;
	else :
		if ( $header_tob_bar_style == 1 ) :
			$btn_style_menu_donate = 'btn-style-one';
		elseif ( $header_tob_bar_style == 2 ) :
			$btn_style_menu_donate = 'btn-style-four';
		elseif ( $header_tob_bar_style == 3 ) :
			$btn_style_menu_donate = 'btn-style-nine donate-box-btn';
		elseif ( $header_tob_bar_style == 4 ) :
			$btn_style_menu_donate = '';
		elseif ( $header_tob_bar_style == 5 ) :
			$btn_style_menu_donate = '';
		endif;
	endif;
	$header_cart_icon          = goodsoul_get_options( 'header_cart_icon' );
	$header_donate_button      = goodsoul_get_options( 'header_donate_button' );
	$header_donate_button_text = goodsoul_get_options( 'header_donate_button_text' );
	$header_donate_button_link = goodsoul_get_options( 'header_donate_button_link' );
	$header_search_button      = goodsoul_get_options( 'header_search_button' );
	$header_menu_button        = goodsoul_get_options( 'header_menu_button' );
	$header_payment_content    = goodsoul_get_options( 'header_payment_content' );
	$header_style              = goodsoul_get_options( 'header_style' );
	$header_button_class       = 'btn-style-one';
	if ( $header_style == '2' ) :
		$header_button_class = 'btn-style-one';
		else :
			if ( $header_tob_bar_style == 1 ) :
				$btn_style_menu_donate = 'btn-style-one';
			elseif ( $header_tob_bar_style == 2 ) :
				$btn_style_menu_donate = 'btn-style-four';
			elseif ( $header_tob_bar_style == 3 ) :
				$btn_style_menu_donate = 'btn-style-nine donate-box-btn';
			elseif ( $header_tob_bar_style == 4 ) :
				$btn_style_menu_donate = '';
			elseif ( $header_tob_bar_style == 5 ) :
				$btn_style_menu_donate = '';
			endif;
	endif;
		$header_cart_icon          = goodsoul_get_options( 'header_cart_icon' );
		$header_donate_button      = goodsoul_get_options( 'header_donate_button' );
		$header_donate_button_text = goodsoul_get_options( 'header_donate_button_text' );
		$header_donate_button_link = goodsoul_get_options( 'header_donate_button_link' );
		$header_search_button      = goodsoul_get_options( 'header_search_button' );
		$header_menu_button        = goodsoul_get_options( 'header_menu_button' );
		$header_style              = goodsoul_get_options( 'header_style' );
		$header_button_class       = 'btn-style-one';
		if ( $header_style == '2' ) :
			$header_button_class = 'btn-style-one';
	elseif ( $header_style == '3' ) :
		$header_button_class = 'btn-style-five';
	endif;
	?>
<?php if ( $header_search_button === '1' ) : ?>
	<?php get_template_part( 'components/search-popup/search-popup' ); ?>
<?php endif; ?>

<?php if ( $goodsoul_theme_metabox_header_style === '3' && ! empty( $goodsoul_theme_metabox_header_style ) ) : ?>
<?php elseif ( $goodsoul_theme_metabox_header_style === '4' && ! empty( $goodsoul_theme_metabox_header_style ) ) : ?>
<?php else : ?>
	<?php if ( $header_cart_icon === '1' ) : ?>
<div class="cart-btn">
	<div class="cart-icon">
		<?php
		if ( class_exists( 'WooCommerce' ) ) {
			$cart_url = wc_get_cart_url();
			?>
	<a class="top_cart_bt" href="<?php echo esc_url( $cart_url ); ?>">
	<span class="flaticon-bag gdsoul-icon-cart"></span>
	
		<span class="item-count">
			<?php
						$count = WC()->cart->cart_contents_count;
			if ( $count > 0 ) {
				echo esc_html( $count );
			} else {
				echo '0';}
			?>
		</span>
	</a>
	<?php } ?>
	</div>
</div>
	<?php endif; ?>
<?php endif; ?>
<?php if ( $goodsoul_theme_metabox_header_style === '3' && ! empty( $goodsoul_theme_metabox_header_style ) ) : ?>
<?php elseif ( $goodsoul_theme_metabox_header_style === '4' && ! empty( $goodsoul_theme_metabox_header_style ) ) : ?>
<?php elseif ( $goodsoul_theme_metabox_header_style === '5' && ! empty( $goodsoul_theme_metabox_header_style ) ) : ?>
<?php else : ?>
	<?php if ( $header_menu_button === '1' ) : ?>
<div class="navbar-btn-wrap">
	<button class="anim-menu-btn">
		<i class="flaticon-menu"></i>
	</button>
</div>
	<?php endif; ?>
<?php endif; ?>

<?php if ( $goodsoul_theme_metabox_header_style === '3' && ! empty( $goodsoul_theme_metabox_header_style ) ) : ?>
	<?php if ( $header_donate_button === '1' ) : ?>
		<div class="link-btn">
			<a href="<?php echo esc_url( $header_donate_button_link ); ?>#menu-donation" class="theme-btn btn-style-nine donate-box-btn"><span class="flaticon-heart-2"></span><?php echo wp_kses( $header_donate_button_text, 'code_contxt' ); ?></a>
		</div>
		<div class="make-payment">
			<form action="#">
				<div class="left-content">
					<div class="payment">
						<select class="filters-selec form-controlt selectmenu" name="form_subject" id="ui-id-1" style="display: none;">
							<option value="*">$10</option>
							<option value=".category-1">$10</option>
							<option value=".category-2">$20</option>
							<option value=".category-3">$50</option>
						</select><span tabindex="0" id="ui-id-1-button" role="combobox" aria-expanded="false" aria-autocomplete="list" aria-owns="ui-id-1-menu" aria-haspopup="true" class="ui-selectmenu-button ui-selectmenu-button-closed ui-corner-all ui-button ui-widget" aria-activedescendant="ui-id-4" aria-labelledby="ui-id-4" aria-disabled="false"><span class="ui-selectmenu-icon ui-icon ui-icon-triangle-1-s"></span><span class="ui-selectmenu-text">$10</span></span>
					</div>
					<div class="payment-time">
						<select class="filters-selec form-controlt selectmenu" name="form_subject" id="ui-id-2" style="display: none;">
							<option value="*">One Time</option>
							<option value=".category-1">2nd Time</option>
							<option value=".category-2">3rd Time</option>
						</select><span tabindex="0" id="ui-id-2-button" role="combobox" aria-expanded="false" aria-autocomplete="list" aria-owns="ui-id-2-menu" aria-haspopup="true" class="ui-selectmenu-button ui-selectmenu-button-closed ui-corner-all ui-button ui-widget" aria-activedescendant="ui-id-8" aria-labelledby="ui-id-8" aria-disabled="false"><span class="ui-selectmenu-icon ui-icon ui-icon-triangle-1-s"></span><span class="ui-selectmenu-text">One Time</span></span>
					</div>
				</div>
				<button type="submit">Make</button>
			</form>
		</div>
	<?php endif; ?>
<?php elseif ( $goodsoul_theme_metabox_header_style === '5' && ! empty( $goodsoul_theme_metabox_header_style ) ) : ?>
<?php elseif ( $goodsoul_theme_metabox_header_style === '4' && ! empty( $goodsoul_theme_metabox_header_style ) ) : ?>
	<?php if ( $header_donate_button === '1' ) : ?>
<div class="link-btn">
	<a href="<?php echo esc_url( $header_donate_button_link ); ?>" class="theme-btn btn-style-fourteen donate-box-btn">
		<span><?php echo wp_kses( $header_donate_button_text, 'code_contxt' ); ?></span>
	</a>
</div>
	<?php endif; ?>
<?php else : ?>
	<?php if ( $header_donate_button === '1' ) : ?>
<div class="link-btn">
	<a href="<?php echo esc_url( $header_donate_button_link ); ?>"
		class="theme-btn <?php echo esc_attr( $btn_style_menu_donate ); ?>">
		<span><?php echo wp_kses( $header_donate_button_text, 'code_contxt' ); ?></span>
	</a>
</div>
	<?php endif; ?>
<?php endif; ?>
