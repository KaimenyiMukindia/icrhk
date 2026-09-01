<?php
	$responsive_logo                           = goodsoul_get_options( 'sticky_header_logos' );
	$responsive_menu_social_onoff              = goodsoul_get_options( 'responsive_menu_social_onoff' );
	$responsive_menu_social_icon               = goodsoul_get_options( 'responsive_menu_social_icon' );
	$goodsoul_theme_metabox_header_mobile_logo = get_post_meta( get_queried_object_id(), 'goodsoul_theme_metabox_header_mobile_logo', array( 'size' => 'full' ) );

?>
<!-- Mobile Menu  -->
<div class="mobile-menu style-one">
	<div class="menu-box">
	<?php if ( ! empty( $goodsoul_theme_metabox_header_mobile_logo ) ) { ?>
		<div class="logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( wp_get_attachment_url( $goodsoul_theme_metabox_header_mobile_logo ) ); ?>" alt="<?php _e( 'responsive icon', 'goodsoul' ); ?>"></a></div>
	<?php } elseif ( isset( $responsive_logo['url'] ) && $responsive_logo['url'] != '' ) { ?>
			<div class="logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( $responsive_logo['url'], 'goodsoul' ); ?>" alt="<?php _e( 'responsive icon', 'goodsoul' ); ?>"></a></div>
		<?php } else { ?>
			<div class="logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( GOODSOUL_IMG_URL . 'logo-vsg.svg' ); ?>" alt="<?php esc_attr_e( 'Logo', 'goodsoul' ); ?>"></a></div>
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
		<div class="search-box-outer">
	<div class="dropdown">
		<button class="search-box-btn dropdown-toggle" type="button" id="dropdownMenu4" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fa fa-search"></span></button>
		<ul class="dropdown-menu pull-right search-panel" aria-labelledby="dropdownMenu4">
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
	</div>  
</div>
<!-- End Mobile Menu -->
<div class="nav-overlay">
	<div class="cursor"></div>
	<div class="cursor-follower"></div>
</div>
