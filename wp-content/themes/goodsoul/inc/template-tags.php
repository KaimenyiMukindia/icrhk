<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package goodsoul
 */

if ( ! function_exists( 'goodsoul_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	
	function goodsoul_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date. */
			esc_html_x( ' %s', 'post date', 'goodsoul' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		echo '' . $posted_on . '';

	}
endif;

if ( ! function_exists( 'goodsoul_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function goodsoul_posted_by() {
		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( '%s', 'post author', 'goodsoul' ),
			'<a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a>'
		);

		echo '' . $byline . ''; // WPCS: XSS OK.

	}
endif;
if ( ! function_exists( 'goodsoul_author_image' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function goodsoul_author_image() {
		$get_author_id = get_the_author_meta('ID');
    	$get_author_gravatar = get_avatar_url($get_author_id, array('size' => 30));
		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( '%s', 'Author image', 'goodsoul' ),
			'<img alt="Author image" src="'.esc_url($get_author_gravatar).'" >'
		);

		echo '' . $byline . ''; // WPCS: XSS OK.

	}
endif;
/*
 * Set post views count using post meta//functions.php
 */
function goodsoul_post_views($postID) {
    $countKey = 'post_views_count';
    $count = get_post_meta($postID, $countKey, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $countKey);
        add_post_meta($postID, $countKey, '1');
    }else{
        $count++;
        update_post_meta($postID, $countKey, $count);
    }
}
if (!function_exists('goodsoul_comments_count')) :

    function goodsoul_comments_count() {
        if (get_comments_number(get_queried_object_id()) == 0) {
            $comments_count = '<a href="' . esc_url(get_permalink()) . '" >' . get_comments_number(get_queried_object_id()) . "" . '</a>';
        }
        elseif (get_comments_number(get_queried_object_id()) > 1) {
            $comments_count = '<a href="' . esc_url(get_permalink()) . '" >' . get_comments_number(get_queried_object_id()) . "" . '</a>';
        } else {
            $comments_count = '<a href="' . esc_url(get_permalink()) . '#comments" >' . get_comments_number(get_queried_object_id()) . "" . '</a>';
        }
        echo sprintf(esc_html('%s'), $comments_count); // WPCS: XSS OK.
    }

endif;
if (!function_exists('goodsoul_category_list')) :

    function goodsoul_category_list() {
        if ('post' === get_post_type()) {
            $tags_list = get_the_category_list(esc_html__(' ', 'goodsoul'));
            if ($tags_list) {
                printf($tags_list); // WPCS: XSS OK.
            }
        }
    }

endif;
if (!function_exists('goodsoul_tag_list')) :

    function goodsoul_tag_list() {
        if ('post' === get_post_type()) {
            $tags_list = get_the_tag_list(esc_html__(' ', 'goodsoul'));
            if ($tags_list) {
                printf($tags_list); // WPCS: XSS OK.
            }
        }
    }

endif;

if ( ! function_exists( 'goodsoul_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function goodsoul_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'goodsoul' ) );
			if ( $categories_list ) {
				/* translators: 1: list of categories. */
				printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'goodsoul' ) . '</span>', $categories_list ); // WPCS: XSS OK.
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'goodsoul' ) );
			if ( $tags_list ) {
				/* translators: 1: list of tags. */
				printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'goodsoul' ) . '</span>', $tags_list ); // WPCS: XSS OK.
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'goodsoul' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				)
			);
			echo '</span>';
		}

		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'goodsoul' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

if ( ! function_exists( 'goodsoul_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 */
	function goodsoul_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
			?>

			<div class="post-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div><!-- .post-thumbnail -->

		<?php else : ?>

		<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php
			the_post_thumbnail( 'post-thumbnail', array(
				'alt' => the_title_attribute( array(
					'echo' => false,
				) ),
			) );
			?>
		</a>

		<?php
		endif; // End is_singular().
	}
endif;

if (!function_exists('goodsoul_comments')) {

    function goodsoul_comments($comment, $args, $depth) {
        extract($args, EXTR_SKIP);
        $args['reply_text'] = esc_html__('Reply', 'goodsoul');
        $class = '';
        if ($depth > 1) {
            $class = '';
        }
        if ($depth == 1) {
            $child_html_el = '<ul><li>';
            $child_html_end_el = '</li></ul>';
        }

        if ($depth >= 2) {
            $child_html_el = '<li>';
            $child_html_end_el = '</li>';
        }
        ?>
        <div class="comment-box" id="comment-<?php comment_ID(); ?>">
            <?php if ($comment->comment_type != 'trackback' && $comment->comment_type != 'pingback') { ?>
                <div class="comment ">
                <?php } else { ?>
                    <div class="comment yes-ping">
                    <?php } ?>
                    <?php if ($comment->comment_type != 'trackback' && $comment->comment_type != 'pingback') { ?>
                        <div class="author-thumb">
                            <?php print get_avatar($comment, 80, null, null, array('class' => array())); ?>
                        </div>
                    <?php } ?>
                    <div class="comment-info">
						<h4 class="name"><?php echo get_comment_author_link(); ?></h4>
						<div class="time"><?php comment_time(get_option('date_format')); ?></div>
                    </div>
                    <div class="text">
                        <?php comment_text(); ?>
					</div>
					 <?php 
							$replyBtn = 'reply-btn';
							echo preg_replace( '/comment-reply-link/', 'comment-reply-link ' . $replyBtn, 
								get_comment_reply_link(array_merge( $args, array(
									'reply_text' => esc_html__('Reply', 'goodsoul'),
									'depth' => $depth,
									'max_depth' => $args['max_depth']))), 1 
							); 
						?>
					<?php
					
					
					?>
                </div>
            </div>
            <?php
        }

    }


 