<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package goodsoul
 * 
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>
	<?php
		// You can start editing here -- including this comment!
		if ( have_comments() ) :
			$comment_close = '';
			if ( ! comments_open() ) :
				$comment_close = 'comment-close';
			endif;
			?>
			<div id="comments" class="comments-area <?php echo esc_attr($comment_close); ?>">
				<div class="group-title">
					<h3>
						<?php
						$goodsoul_comment_count = get_comments_number();
						if ('1' === $goodsoul_comment_count) {
							printf(
									/* translators: 1: title. */
									esc_html__('One Comment', 'goodsoul')
							);
						} else {
							printf(// WPCS: XSS OK.
									/* translators: 1: comment count number, 2: title. */
									esc_html(_nx('%1$s Comment', '%1$s Comments ', $goodsoul_comment_count, 'comments title', 'goodsoul'), 'goodsoul'), number_format_i18n($goodsoul_comment_count)
							);
						}
						?>
					</h3>
				</div>
				<?php
					if (have_comments()) :
						the_comments_navigation();
				?>
						<?php
							wp_list_comments( array(
								'style'      => 'ol',
								'callback' => 'goodsoul_comments',
								'short_ping' => true,
							) );
						?>
					<?php
						the_comments_navigation();
						// If comments are closed and there are comments, let's leave a little note, shall we?
						if ( ! comments_open() ) :
							?>
							<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'goodsoul' ); ?></p>
							<?php
						endif;
					endif;
				?>
			</div>
	<?php
		endif; // Check for have_comments().
	$is_no_post_thumb = '';
	if (!have_comments()) {
		$is_no_post_thumb = 'no-comment';
	}
	if ( comments_open() ) :
?>
<div class="comment-form default-form <?php echo esc_attr($is_no_post_thumb); ?>">
	<div class="row clearfix">
		<?php
			$user = wp_get_current_user();
			$goodsoul_user_identity = $user->display_name;
			$req = get_option('require_name_email');
			$aria_req = $req ? " aria-required='true'" : '';
			$formargs = array(
				'id_submit' => 'submit',
				'title_reply' => esc_html__('Leave a Reply', 'goodsoul'),
				'title_reply_to' => esc_html__('Leave a Reply to %s', 'goodsoul'),
				'cancel_reply_link' => esc_html__('Cancel Reply', 'goodsoul'),
				'label_submit' => esc_html__('Post Comment', 'goodsoul'),
				'comment_notes_before' => '<p class="email-not-publish">' .
				esc_html__('Your email address will not be published.', 'goodsoul') . ( $req ? '<span class="required">*</span>' : '' ) .
				'</p>',
				'submit_button' => '<button type="submit" name="%1$s" id="%2$s" class="%3$s theme-btn btn-style-one"><span class="btn-title">%4$s</span></button>',
				'comment_field' => '<div class="row"><div class="col-md-12 form-group"><textarea id="comment" name="comment" placeholder="' . esc_attr__('Your Comments *', 'goodsoul') . '"  >' .
				'</textarea></div></div>',
				'must_log_in' => '<div>' .
				sprintf(
						wp_kses(__('You must be <a href="%s">logged in</a> to post a comment.', 'goodsoul'), array('a' => array('href' => array()))), wp_login_url(apply_filters('the_permalink', get_permalink()))
				) . '</div>',
				'logged_in_as' => '<div class="logged-in-as">' .
				sprintf(
						wp_kses(__('Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="%4$s">Log out?</a>', 'goodsoul'), array('a' => array('href' => array()))), esc_url(admin_url('profile.php')), $goodsoul_user_identity, wp_logout_url(apply_filters('the_permalink', get_permalink())), esc_attr__('Log out of this account', 'goodsoul')
				) . '</div>',
				
				'comment_notes_after' => '',
				'fields' => apply_filters('comment_form_default_fields', array(
					'author' =>
					'<div class="row"><div class="col-md-6 form-group">' 
					. '<input id="author"  name="author" placeholder="' . esc_attr__('Your Name *', 'goodsoul') . '" type="text" value="' . esc_attr($commenter['comment_author']) .
					'" size="30"' . $aria_req . ' /></div>',
					'email' =>
					'<div class="col-md-6 form-group">'
					. '<input id="email" name="email" type="text"  placeholder="' . esc_attr__('Your Email *', 'goodsoul') . '" value="' . esc_attr($commenter['comment_author_email']) .
					'" size="30"' . $aria_req . ' /></div></div>'
						)
				),
			);
		?>
		<?php
			comment_form($formargs);
		?>
	</div>
</div>
<?php endif; ?>