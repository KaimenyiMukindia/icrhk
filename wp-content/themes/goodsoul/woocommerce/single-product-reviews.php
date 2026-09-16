<?php
/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 9.7.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! comments_open() ) {
	return;
}

?>
<div id="reviews" class="woocommerce-Reviews">
	<div id="comments" class="review-box">
	<?php if ( have_comments() ) : ?>
		<ol class="commentlist">
		<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'woocommerce_comments' ) ) ); ?>
		</ol>

		<?php
		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
			echo '<nav class="woocommerce-pagination">';
			paginate_comments_links(
				apply_filters(
					'woocommerce_comment_pagination_args',
					array(
						'prev_text' => '&larr;',
						'next_text' => '&rarr;',
						'type'      => 'list',
					)
				)
			);
			echo '</nav>';
		endif;
		?>
	<?php else : ?>
		<p class="woocommerce-noreviews"><?php esc_html_e( 'There are no reviews yet.', 'goodsoul' ); ?></p>
	<?php endif; ?>
	</div>

	<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>

		<div class="review-form">
		<?php
		$commenter    = wp_get_current_commenter();
		$comment_form = array(
			/* translators: %s is product title */
			'title_reply'         => have_comments() ? esc_html__( 'Add Your Comments', 'goodsoul' ) : sprintf( esc_html__( 'Be the first to review &ldquo;%s&rdquo;', 'goodsoul' ), get_the_title() ),
			/* translators: %s is product title */
			'title_reply_to'      => esc_html__( 'Leave a Reply to %s', 'goodsoul' ),
			'title_reply_before'  => '<div class="shop-page-title">',
			'title_reply_after'   => '</div>',
			'comment_notes_after' => '',
			'label_submit'        => esc_html__( 'Submit', 'goodsoul' ),
			'logged_in_as'        => '',
			'comment_field'       => '',
		);

		$name_email_required = (bool) get_option( 'require_name_email', 1 );
		$fields              = array(
			'author' => array(
				'type'     => 'text',
				'value'    => $commenter['comment_author'],
				'required' => $name_email_required,
			),
			'email'  => array(
				'type'     => 'email',
				'value'    => $commenter['comment_author_email'],
				'required' => $name_email_required,
			),
		);

		$comment_form['fields'] = array();


		$comment_form['fields']['first_div'] = '<div class="row">';
		foreach ( $fields as $key => $field ) {

			$field_html = '<div class="col-lg-6"><p class="comment-form-' . esc_attr( $key ) . '">';

			$field_html .= '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $field['type'] ) . '" placeholder="' . esc_attr( $key ) . '"' . ' value="' . esc_attr( $field['value'] ) . '" size="30" ' . ( $field['required'] ? 'required' : '' ) . ' /></p></div>';

			$comment_form['fields'][ $key ] = $field_html;
		}

		$comment_form['fields']['last_div'] = '</div>';

		$comment_form['fields']['comment_field'] = '<p class="comment-form-comment"><textarea id="comment" placeholder="' . esc_attr__( 'Message', 'goodsoul' ) . '" name="comment" required></textarea></p>';


		$account_page_url = wc_get_page_permalink( 'myaccount' );
		if ( $account_page_url ) {
			/* translators: %s opening and closing link tags respectively */
			$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( esc_html__( 'You must be %1$slogged in%2$s to post a review.', 'goodsoul' ), '<a href="' . esc_url( $account_page_url ) . '">', '</a>' ) . '</p>';
		}

		if ( wc_review_ratings_enabled() ) {
			$comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating">' . esc_html__( 'Your rating', 'goodsoul' ) . ( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '</label><select name="rating" id="rating" required>
						<option value="">' . esc_html__( 'Rate&hellip;', 'goodsoul' ) . '</option>
						<option value="5">' . esc_html__( 'Perfect', 'goodsoul' ) . '</option>
						<option value="4">' . esc_html__( 'Good', 'goodsoul' ) . '</option>
						<option value="3">' . esc_html__( 'Average', 'goodsoul' ) . '</option>
						<option value="2">' . esc_html__( 'Not that bad', 'goodsoul' ) . '</option>
						<option value="1">' . esc_html__( 'Very poor', 'goodsoul' ) . '</option>
					</select></div>';
		}
		comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
		?>
		</div>

	<?php else : ?>
	<p class="woocommerce-verification-required">
		<?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'goodsoul' ); ?>
	</p>
	<?php endif; ?>
</div>
