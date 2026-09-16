<?php


	function goodsoul_remove_hooks_woocommerce() {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
	remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );

	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar' );

}

add_action( 'init', 'goodsoul_remove_hooks_woocommerce' );

add_filter( 'woocommerce_show_page_title', '__return_false' );

add_filter( 'woocommerce_template_loop_product_title', '__return_false' );

add_filter( 'woocommerce_product_add_to_cart_text', 'goodsoul_change_text_woo' );
function goodsoul_change_text_woo() {
	 return '<span class="fa fa-shopping-cart"></span><div class="toltip-content"><p>Add to Cart</p></div>';
}

add_filter( 'woocommerce_sale_flash', 'goodsoul_custom_hide_sales_flash' );
function goodsoul_custom_hide_sales_flash() {
	 return false;
}

add_filter( 'woocommerce_get_price_html', 'goodsoul_change_default_price_html', 100, 2 );
function goodsoul_change_default_price_html( $price, $product ) {
	if ( $product->get_price() > 0 ) {
		if ( $product->get_sale_price() && $product->get_regular_price() ) {
			$from = $product->get_regular_price();
			$to   = $product->get_sale_price();
			return '<span class="reg-price">' . ( ( is_numeric( $from ) ) ? wc_price( $from ) : $from ) . ' </span>
			<span class="">' . ( ( is_numeric( $to ) ) ? wc_price( $to ) : $to ) . '</span>';
		} else {
			$to = $product->get_price();
			return '<span class="">' . ( ( is_numeric( $to ) ) ? wc_price( $to ) : $to ) . '</span>';
		}
	} else {
		return '0';
	}
}

add_filter( 'get_product_search_form', 'goodsoul_custom_product_searchform' );
/**
 * Filter WooCommerce  Search Field
 */
function goodsoul_custom_product_searchform( $form ) {
	$form = '<div class="single-sidebar search-sidebar">
				<form class="search-form" role="search" method="get" id="searchform" action="' . esc_url( home_url( '/' ) ) . '">
					<input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="' . esc_html__( 'Search...', 'goodsoul' ) . '" /> 
					<button type="submit"><i class="fa fa-search"></i></button>                          
						<input type="hidden" name="post_type" value="product" />
				</form>
			</div>';
	return $form;
}
add_action( 'woocommerce_before_main_content', 'goodsoul_before_main_content', 10 );
function goodsoul_before_main_content() {
	 $show_show_page_title = get_post_meta( get_queried_object_id(), 'goodsoul_show_page_title', true );
	if ( ! is_home() && ! is_front_page() ) {
		if ( $show_show_page_title != 'off' ) {
			?>
				<!-- Page Banner Section -->
				<section class="page-title woo-banner" >
					<div class="auto-container">
						<div class="content-box">
							<h1><?php woocommerce_page_title(); ?></h1>
							<ul class="bread-crumb">
								<li><a class="home" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span class="fa fa-home"></span></a></li>
								<li>
									<?php
										$produc_brd = 'Products';
										echo wp_kses( $produc_brd, 'code_contxt' );
									?>
								</li>
							</ul>
							
						</div>
					</div>
				</section>
			<?php
		}
	}
}

add_action( 'woocommerce_before_shop_loop', 'goodsoul_before_shop_loop' );
function goodsoul_before_shop_loop() {
	?>
<div  id="shop-area" class="main-shop-area">
	<div class="container">
		<div class="row">
			<!--why_Item-->
	<?php if ( is_active_sidebar( 'woo_shop_sideber' ) ) { ?>
			<div class="col-lg-8 col-md-12">
	<?php } else { ?>
				<div class="col-lg-12">
	<?php } ?>

					<div class="shop-content">
						<div class="row">
							<div class="col-md-12">
								<div class="showing-result-shorting">
									<div class="shorting">
										<?php
												$options = array(
													'menu_order' => esc_html__( 'Default sorting', 'goodsoul' ),
													'popularity' => esc_html__( 'Sort by popularity', 'goodsoul' ),
													'rating'     => esc_html__( 'Sort by average rating', 'goodsoul' ),
													'date'       => esc_html__( 'Sort by latest', 'goodsoul' ),
													'price'      => esc_html__( 'Sort by price: low to high', 'goodsoul' ),
													'price-desc' => esc_html__( 'Sort by price: high to low', 'goodsoul' ),
												);
											?>
											<form class="woocommerce-ordering" method="get">
												<select id="ui-id-1" class="selectmenu orderby" name="orderby">
													<?php foreach ( $options as $id => $name ) { ?>
													<option class='ordering-class' value="<?php echo esc_attr( $id ); ?>"
														<?php selected( $name, $id ); ?>>
														<?php echo esc_html( $name ); ?></option>
													<?php } ?>
												</select>
											</form>
									</div>
									<div class="showing">
										<p>
											<?php
												$total    = (int) wc_get_loop_prop( 'total' );
												$per_page = (int) wc_get_loop_prop( 'per_page' );
												$current  = (int) wc_get_loop_prop( 'current_page' );
												if ( $total <= $per_page || -1 === $per_page ) {
													/* translators: %d: total results */
													printf( _n( 'Showing the single Result.', 'Showing all <span>%d</span> results.', $total, 'goodsoul' ), $total );
												} else {
													$first = ( $per_page * $current ) - $per_page + 1;
													$last  = min( $total, $per_page * $current );
													/* translators: 1: first result 2: last result 3: total results */
													printf( _nx( 'Showing the single result.', 'Showing <span>%1$d&ndash;%2$d</span> of <span>%3$d</span> result.', $total, 'with first and last result', 'goodsoul' ), $first, $last, $total );
												}
											?>
										</p>
									</div> 
								</div>     
							</div>
						</div>



						<?php
}
add_action( 'woocommerce_before_shop_loop', 'goodsoul_before_shop_loop_sidebar' );
function goodsoul_before_shop_loop_sidebar() {
	?>
						<div class="row clearfix main-tolar-prot new-device-skd">
										<?php
}

add_action( 'woocommerce_before_shop_loop_item', 'goodsoul_before_shop_loop_item' );
function goodsoul_before_shop_loop_item() {
	?>
			<div class="single-product-item text-center">
				<div class="inner-box">
							<?php
}

add_action( 'woocommerce_before_shop_loop_item_title', 'goodsoul_before_shop_loop_item_title_new' );
function goodsoul_before_shop_loop_item_title_new() {
	 global $product;

	?>
							<div class="img-holder">
								<?php
								global $product;
								$attachment_ids[0] = get_post_thumbnail_id( $product->get_id() );
								$attachment        = wp_get_attachment_image_src( $attachment_ids[0], 'full' );
								?>
								<img class="lazy-image" src="<?php echo esc_url( $attachment[0] ); ?>" data-src="<?php echo esc_url( $attachment[0] ); ?>" alt="<?php echo esc_attr_e( 'ALT', 'goodsoul' ); ?>" />
							
								<?php
								$newness_days = 7;
								$created      = strtotime( $product->get_date_created() );
								if ( ( time() - ( 60 * 60 * 24 * $newness_days ) ) < $created ) {
									?>
									<div class="tag-banner"><?php echo esc_html__( 'New', 'goodsoul' ); ?></div>
									<?php
								}
								if ( $product->is_on_sale() ) :
									?>
									<?php echo apply_filters( 'woocommerce_sale_flash', '<div class="tag-banner">' . esc_html__( 'Sale', 'goodsoul' ) . '</div>', $product ); ?>
								<?php endif; ?>
							</div>
							<div class="title-holder text-center">
							<div class="static-content">
								<?php
									}

									add_action( 'woocommerce_shop_loop_item_title', 'goodsoul_shop_loop_item_title', 1 );
									function goodsoul_shop_loop_item_title() {
										global $product;
										$link  = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );
										$title = apply_filters( 'woocommerce_loop_product_title', get_the_title(), $product );
										?>
																	<h3 class="title text-center"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></h3>

																			<?php

									}

									add_action( 'woocommerce_after_shop_loop_item', 'goodsoul_after_shop_loop_item', 1 );
									function goodsoul_after_shop_loop_item() {
								?>
							</div>
							<div class="overlay-content">
								<ul class="clearfix">
									<li>
										<?php
											woocommerce_template_loop_add_to_cart();
										?>
									</li>
								</ul>
							</div>
							</div>
						</div>
			</div>
						<?php
}


add_action( 'woocommerce_after_shop_loop', 'goodsoul_after_shop_loop' );
function goodsoul_after_shop_loop() {
	 global $wp_query;
	?>
						</div>
						<div class="pagination-box">
						<?php
						$big  = 999999999;
						$args = array(
							'base'               => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
							'format'             => '?paged=%#%',
							'total'              => $wp_query->max_num_pages,
							'current'            => max( 1, get_query_var( 'paged' ) ),
							'show_all'           => false,
							'end_size'           => 1,
							'mid_size'           => 2,
							'prev_next'          => true,
							'prev_text'          => '<span class="fa fa-angle-left"></span>',
							'next_text'          => '<span class="fa fa-angle-right"></span>',
							'type'               => 'plain',
							'add_args'           => false,
							'add_fragment'       => '',
							'before_page_number' => '',
							'after_page_number'  => '',
						);
						echo paginate_links( $args );
						?>
						</div>
					</div>
				</div>
				<?php if ( is_active_sidebar( 'woo_shop_sideber' ) ) { ?>
					<div class="col-lg-4 col-md-8">
						<div class="shop-sidebar-wrapper">
							<?php
								dynamic_sidebar( 'woo_shop_sideber' );
							?>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
										<?php
}
	get_template_part( 'woo-other' );

?>
