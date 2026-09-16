<section class="hidden-sidebar close-sidebar">
	<div class="wrapper-box">
		<div class="hidden-sidebar-close"><span class="flaticon-cross"></span></div>
		<div class="logo">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} elseif ( ! has_custom_logo() ) {
				?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<img src="<?php echo esc_url( GOODSOUL_IMG_URL . 'logo-vsg.svg' ); ?>" alt="<?php esc_attr_e( 'Logo', 'goodsoul' ); ?>">
					</a> 
					<?php
			}
			?>
		</div>
		<div class="content">
			<div class="about-widget-four sidebar-widget">
				<?php
					$header_sidear_text    = goodsoul_get_options( 'header_sidear_text' );
					$header_sidear_content = goodsoul_get_options( 'header_sidear_content' );
				?>
				<h3><?php echo wp_kses( $header_sidear_text, 'code_contxt' ); ?></h3>
				<div class="text"><?php echo wp_kses( $header_sidear_content, 'code_contxt' ); ?></div>
			
			</div>
			<div class="news-widget-two sidebar-widget">
				<?php
					$header_sidear_text2 = goodsoul_get_options( 'header_sidear_text2' );
				?>
					<div class="widget-title"><?php echo wp_kses( $header_sidear_text2, 'code_contxt' ); ?></div>



					<?php
						$args = array(
							'posts_per_page' => 2,
						);
						global $wpdb;
						$loop = new WP_Query( $args );
						$i    = 1;
						if ( $loop->have_posts() ) {
							while ( $loop->have_posts() ) :
								$loop->the_post();
								?>
						<div class="post-wrapper">
							<div class="image"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php goodsoul_post_thumbnail(); ?></a></div>
							<div class="category"><?php goodsoul_category_list(); ?></div>
							<h4><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h4>
						</div>
								<?php
								$i++;
							endwhile;
						} else {
							echo esc_html__( 'No products found', 'goodsoul' );
						}
						wp_reset_postdata();
						?>
			</div>
			<div class="newsletter-widget-two">
				<?php
					$header_sidear_text3     = goodsoul_get_options( 'header_sidear_text3' );
					$header_sidear_shortcode = goodsoul_get_options( 'header_sidear_shortcode' );
				?>
				<div class="widget-title"><?php echo wp_kses( $header_sidear_text3, 'code_contxt' ); ?></div>
				<?php echo do_shortcode( $header_sidear_shortcode ); ?>
				
			</div>
		</div>
	</div>
</section>
