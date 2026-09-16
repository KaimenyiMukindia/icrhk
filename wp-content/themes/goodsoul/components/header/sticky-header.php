<?php
	$sticky_header_on                          = goodsoul_get_options( 'sticky_header_on' );
	$sticky_header_logo                        = goodsoul_get_options( 'sticky_header_logo' );
	$goodsoul_theme_metabox_sticky_header_logo = get_post_meta( get_queried_object_id(), 'goodsoul_theme_metabox_header_sticky_logo', array( 'size' => 'full' ) );

?>
<?php if ( isset( $sticky_header_on ) && $sticky_header_on == '1' ) : ?>
<!-- Sticky Header  -->
		<!--End Header Upper-->
		<div class="sticky-header">
			<div class="auto-container">
				<div class="wrapper-box">
					<div class="logo-column">
						<div class="logo-box">
						<?php if ( ! empty( $goodsoul_theme_metabox_sticky_header_logo ) ) { ?>
							<div class="logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( wp_get_attachment_url( $goodsoul_theme_metabox_sticky_header_logo ) ); ?>" alt="<?php esc_attr_e( 'Logo', 'goodsoul' ); ?>" title="<?php esc_attr_e( 'Logo', 'goodsoul' ); ?>"></a></div>
							
						<?php } elseif ( $sticky_header_logo['url'] ) { ?>
								<div class="logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( $sticky_header_logo['url'], 'goodsoul' ); ?>" alt="<?php esc_attr_e( 'Logo', 'goodsoul' ); ?>" title="<?php esc_attr_e( 'Logo', 'goodsoul' ); ?>"></a></div>
							<?php } else { ?>
								<div class="logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( get_template_directory() . 'assets/images/logo-5.png' ); ?>" alt="<?php esc_attr_e( 'Logo', 'goodsoul' ); ?>" title="<?php esc_attr_e( 'Logo', 'goodsoul' ); ?>"></a></div>
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
<!-- End Sticky Menu -->
<?php endif; ?>
