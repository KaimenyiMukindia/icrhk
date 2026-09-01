<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 */
	get_header();
	$goodsoul_metabox_campaign_meta_image         = get_post_meta( get_queried_object_id(), 'goodsoul_metabox_campaign_meta_image', true );
	$goodsoul_metabox_campaign_meta_image_url     = wp_get_attachment_image_src( $goodsoul_metabox_campaign_meta_image, 'full' );
	if(is_array($goodsoul_metabox_campaign_meta_image_url)){
	$goodsoul_metabox_campaign_meta_image_url_url = $goodsoul_metabox_campaign_meta_image_url[0];
	}
	$goodsoul_metabox_featured_content            = get_post_meta( get_queried_object_id(), 'goodsoul_metabox_featured_content', true );

	$chariti_campaign = charitable_get_current_campaign();

	$campaign_description         = get_post_meta( get_queried_object_id(), '_campaign_description', true );
	$chariti_image_url            = get_the_post_thumbnail_url( $chariti_campaign->ID );
	$chariti_currency_helper      = charitable_get_currency_helper();
	$chariti_raised               = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_donated_amount() );
	$chariti_goal                 = $chariti_currency_helper->get_monetary_amount( $chariti_campaign->get_goal() );
	$chariti_post_title           = $chariti_campaign->post_title;
	$chariti_post_content         = $chariti_campaign->post_content;
	$chariti_percent              = $chariti_campaign->get_percent_donated_raw();
	$chariti_categories           = $chariti_campaign->get( 'categories', true );
	$chariti_post_page_link       = $chariti_campaign->guid;
	$raised_text                  = goodsoul_get_options( 'cam_raised' );
	$goal_text                    = goodsoul_get_options( 'cam_goal' );
	$description_text             = goodsoul_get_options( 'cam_description' );
	$donate_form_wrapper_text     = goodsoul_get_options( 'cam_donate_form_wrapper' );
	$donate_form_wrapper_text_sub = goodsoul_get_options( 'cam_donate_form_wrapper_sub' );

if ( is_active_sidebar( 'sidebar-1' ) ) :
	$blog_class = 'col-lg-8';
	else :
		$blog_class = 'col-lg-12';
	endif;
	?>

<section class="page-title chariable-single-bg">
	<div class="auto-container">
		<div class="content-box">
			<h1><?php the_title(); ?></h1>
		</div>
	</div>
</section>
<div class="cause-info">
	<div class="auto-container">
		<div class="wrapper-box">
			<div class="raised">
				<span>
				<?php
				if ( $raised_text != '' ) {
					echo sprintf( '%s', $raised_text );
				} else {
					esc_html_e( 'Raised:', 'goodsoul' );
				}
				?>
				</span>
				<br><?php echo wp_kses( $chariti_raised, 'code_contxt' ); ?></div>
			<div class="progress-block">
				<div class="inner-box">
					<div class="graph-outer">
						<input type="text" class="dial" data-fgColor="#ed6221" data-bgColor="#f0edea" data-width="70"
							data-height="70" data-linecap="normal" value="<?php echo esc_attr( $chariti_percent ); ?>">
						<div class="inner-text count-box"><span class="count-text"
								data-stop="<?php echo esc_attr( round( $chariti_percent ) ); ?>"
								data-speed="2000"></span><?php esc_html_e( '%', 'goodsoul' ); ?></div>
					</div>
				</div>
			</div>
			<?php if ( $chariti_campaign->get_goal() != "0"  ) { ?>
			<div class="goal">
				<span>
				<?php
					esc_html_e( 'Goal:', 'goodsoul' );
				?>
				</span>
				<br><?php echo wp_kses( $chariti_goal, 'code_contxt' ); ?></div>
			<?php } ?>
		</div>
	</div>
</div>
<div class="sidebar-page-container cause-details">
	<div class="auto-container">
		<div class="row">
			<div class="col-lg-8 content-column">
				<?php if(!empty($goodsoul_metabox_campaign_meta_image_url_url)){ ?>
				<div class="image mb-50"><img
						src="<?php echo esc_url( $goodsoul_metabox_campaign_meta_image_url_url ); ?>" alt="thumb"></div>
				<?php } ?>
				<div class="sec-title mb-40">
				<?php the_content(); ?>
				</div>
				<br>
				<div class="donate-form-area new-donate-form" id="menu-donation">
					<div class="donate-form-wrapper">
						<div class="sec-title">
							<h1>
	<?php
	if ( $donate_form_wrapper_text != '' ) {
		echo sprintf( '%s', $donate_form_wrapper_text );
	} else {
		esc_html_e( 'Donate us to achieve our goal', 'goodsoul' );
	}
	?>
							</h1>
							<div class="text">
								<?php
								if ( $donate_form_wrapper_text_sub != '' ) {
									echo sprintf( '%s', $donate_form_wrapper_text_sub );
								} else {
									esc_html_e( 'Beguiled and demoralized by the charms of pleasure of the moment, so by desire, that they cannot foresee.', 'goodsoul' );
								}
								?>
							</div>
						</div>

						<div class="donate-form default-form">
	<?php charitable_template_campaign_donation_form_in_page( $chariti_campaign ); ?>
						</div>
					</div>

				</div>
			</div>
	<?php if ( is_active_sidebar( 'sidebar-1' ) ) { ?>
			<div class="col-lg-4">
		<?php get_sidebar(); ?>
			</div>
	<?php } ?>
		</div>
	</div>
</div>
<?php
get_footer();
