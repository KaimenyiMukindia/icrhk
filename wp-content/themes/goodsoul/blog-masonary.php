  
 <!-- Blog Section -->
 <section class="blog-section fullwidth">
		<div class="auto-container">
			<div class="row masonry-layout">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					?>
				<!-- News Block One -->
				<div class="col-lg-2 col-md-4 col-sm-6 news-block-one">
					<div class="inner-box wow fadeInUp" data-wow-delay="200ms">
						<div class="category"><?php goodsoul_category_list(); ?></div>
						<div class="image">
							<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail(); ?>
							</a>
							<div class="post-meta-info">
								<a href="<?php the_permalink(); ?>"><span class="flaticon-eye"></span>
									<?php
									$post_views_count = get_post_meta( get_queried_object_id(), 'post_views_count', true );
									if ( ! empty( $post_views_count ) ) {
										echo wp_kses( $post_views_count, 'code_contxt' );
									}
									?>
								</a>
								<a href="<?php the_permalink(); ?>"><span class="flaticon-comment"></span></a><?php goodsoul_comments_count(); ?>
							</div>
						</div>
						<div class="lower-content">
							<div class="date"><?php the_date(); ?></div>
							<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
							<div class="author-info">
								<div class="image"><?php goodsoul_author_image(); ?></div>
								<div class="author-title"><?php goodsoul_posted_by(); ?></div>
					<?php
					do_action( 'goodsoul_share_button' );
					?>
							</div>
						</div>
					</div>
				</div>
				<!-- News Block One -->
					<?php
				endwhile;
			endif;
			?>
			</div>
		</div>
	</section>
	<?php get_footer(); ?>
