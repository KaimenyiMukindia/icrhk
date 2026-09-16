<?php
$sticky_header_on                          = goodsoul_get_options( 'sticky_header_on' );
$sticky_header_logo                        = goodsoul_get_options( 'sticky_header_logo' );
$responsive_logo                           = goodsoul_get_options( 'sticky_header_logos' );
$responsive_menu_social_onoff              = goodsoul_get_options( 'responsive_menu_social_onoff' );
$responsive_menu_social_icon               = goodsoul_get_options( 'responsive_menu_social_icon' );
$header_search_button                      = goodsoul_get_options( 'header_search_button' );
$header_info_bar                           = goodsoul_get_options( 'header_info_bar' );
$header_donate_button_text                 = goodsoul_get_options( 'header_donate_button_text' );
$header_donate_button_link                 = goodsoul_get_options( 'header_donate_button_link' );
$goodsoul_theme_metabox_sticky_header_logo = get_post_meta( get_queried_object_id(), 'goodsoul_theme_metabox_header_sticky_logo', array( 'size' => 'full' ) );
$goodsoul_theme_metabox_header_mobile_logo = get_post_meta( get_queried_object_id(), 'goodsoul_theme_metabox_header_mobile_logo', array( 'size' => 'full' ) );
?>
<header class="main-header header-style-new-one">

	<!-- Header Upper -->
	<div class="header-upper style-new-one">
		<div class="auto-full-container">
			<div class="wrapper-box">
				<?php do_action( 'goodsoul_logo_fun' ); ?>
				<div class="phone-box">
					<span class="flaticon-phone"></span><a href="tel:<?php echo esc_attr( $header_info_bar[1] ); ?>"><?php echo wp_kses( $header_info_bar[1], 'code_contxt' ); ?></a>
				</div>
				<div class="right-column">
					<div class="option-wrapper">
						<div class="nav-outer">

							<!-- Main Menu -->
							<nav class="main-menu navbar-expand-xl navbar-dark">

								<div class="collapse navbar-collapse">
									<?php
									if ( has_nav_menu( 'primary' ) || has_nav_menu( 'menu-1' ) ) {
										$themeloc = '';
										if ( has_nav_menu( 'menu-1' ) ) {
											$themeloc = 'menu-1';
										}
										if ( has_nav_menu( 'primary' ) ) {
											$themeloc = 'primary';
										}
										wp_nav_menu(
											array(
												'theme_location' => $themeloc,
												'menu_class' => 'navigation clearfix',
												'container'  => '',
											)
										);
									} else {
										wp_nav_menu(
											array(
												'menu_class' => 'navigation clearfix',
												'container'  => 'ul',
											)
										);
									}
									?>
								</div>
							</nav><!-- Main Menu End-->
						</div>
						<!--Search Box-->
						<div class="search-box-outer">
							<div class="dropdown">
								<button class="search-box-btn dropdown-toggle" type="button" id="dropdownMenu3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fa fa-search"></span></button>
								<ul class="dropdown-menu pull-right search-panel" aria-labelledby="dropdownMenu3">
									<li class="panel-outer">
										<div class="form-container">
											<form method="post" action="<?php echo esc_url( home_url( '/' ) ); ?>">
												<div class="form-group">
													<input type="search" name="s" value="" placeholder="<?php esc_attr_e( 'Search....', 'goodsoul' ); ?>" required="">
													<button type="submit" value="<?php esc_attr_e( 'Search Now!', 'goodsoul' ); ?>" class="search-btn"><span class="fa fa-search"></span></button>
												</div>
											</form>
										</div>
									</li>
								</ul>
							</div>
						</div>
						<?php
						if ( class_exists( 'WooCommerce' ) ) :
							$items_count = WC()->cart->get_cart_contents_count();
							?>
							<div class="cart-btn">
								<div class="cart-icon"><span class="flaticon-bag"></span>
									<?php if ( $items_count > 0 ) : ?>
										<span class="item-count"><?php echo esc_html( $items_count ); ?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							</div>
							<div class="navbar-btn-wrap">
								<button class="anim-menu-btn">
									<i class="flaticon-menu"></i>
								</button>
							</div>
							<div class="link-btn">
								<a href="<?php echo esc_url( $header_donate_button_link ); ?>" class="theme-btn btn-style-seventeen donate-box-btn"><span><i class="fa fa-arrow-circle-o-right"></i><?php echo esc_html( $header_donate_button_text ); ?></span></a>
							</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--End Header Upper-->


	<!--End Header Upper-->
	<?php if ( isset( $sticky_header_on ) && $sticky_header_on == '1' ) : ?>
		<div class="sticky-header style-four">
			<div class="auto-container">
				<div class="wrapper-box">
					<div class="logo-column">
						<div class="logo-box">
						<?php if ( ! empty( $goodsoul_theme_metabox_sticky_header_logo ) ) { ?>
							<div class="logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( wp_get_attachment_url( $goodsoul_theme_metabox_sticky_header_logo ) ); ?>" alt="<?php esc_attr_e( 'Logo', 'goodsoul' ); ?>" title="<?php esc_attr_e( 'Logo', 'goodsoul' ); ?>"></a></div>
						<?php } elseif ( $sticky_header_logo['url'] ) { ?>
								<div class="logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( $sticky_header_logo['url'], 'goodsoul' ); ?>" alt="<?php esc_attr_e( 'Logo', 'goodsoul' ); ?>" title="<?php esc_attr_e( 'Logo', 'goodsoul' ); ?>"></a></div>
							<?php } else { ?>
								<div class="logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( get_template_directory() . 'assets/images/logo-6.png' ); ?>" alt="<?php esc_attr_e( 'Logo', 'goodsoul' ); ?>" title="<?php esc_attr_e( 'Logo', 'goodsoul' ); ?>"></a></div>
							<?php } ?>
						</div>
					</div>
					<div class="menu-column">
						<div class="nav-outer">

							<div class="nav-inner">

								<!-- Main Menu -->
								<nav class="main-menu navbar-expand-xl navbar-dark">

									<div class="collapse navbar-collapse">
										<ul class="navigation">
										</ul>
									</div>
								</nav><!-- Main Menu End-->

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<!-- Mobile Menu  -->
	<div class="mobile-menu style-four">
		<div class="menu-box">
			<div class="logo">
				<?php
				if ( ! empty( $goodsoul_theme_metabox_header_mobile_logo ) ) {
					?>
					<div class="logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( wp_get_attachment_url( $goodsoul_theme_metabox_header_mobile_logo ) ); ?>" alt="<?php _e( 'responsive icon', 'goodsoul' ); ?>"></a></div>
					<?php
				} elseif ( has_custom_logo() ) {
					the_custom_logo();
				} else {
					?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( GOODSOUL_IMG_URL . 'logo-6.png' ); ?>" alt="<?php esc_attr_e( 'Logo', 'goodsoul' ); ?>"></a></div>
		<?php } ?>
		<!-- Main Menu -->
		<nav class="main-menu navbar-expand-xl navbar-dark">
			<div class="navbar-header">
				<!-- Toggle Button -->
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="flaticon-menu"></span>
				</button>
			</div>

			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navigation">

				</ul>
			</div>
		</nav>
		<!-- Main Menu End-->
		<!--Search Box-->
		<?php if ( $header_search_button === '1' ) : ?>
			<?php get_template_part( 'components/search-popup/search-popup2' ); ?>
		<?php endif; ?>

		</div>

	</div>
	<!-- End Mobile Menu -->

	<div class="nav-overlay">
		<div class="cursor"></div>
		<div class="cursor-follower"></div>
	</div>
</header>
