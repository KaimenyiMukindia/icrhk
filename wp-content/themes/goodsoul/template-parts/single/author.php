<?php 
    global $post;
    $display_name = get_the_author_meta('display_name', $post->post_author);
    $user_description = get_the_author_meta('user_description', $post->post_author);
    $user_avatar = get_avatar($post->post_author, 170);

?>

<?php if (isset($user_description) && !empty($user_description)) { ?>
    <div class="author-box">
        <div class="wrapper-area">
            <div class="img-box">
                <?php echo wp_kses($user_avatar, 'code_contxt'); ?>
            </div>
            <h3><?php echo wp_kses($display_name, 'code_contxt'); ?></h3>
            <div class="text">
                <?php echo wp_kses($user_description, 'code_contxt'); ?>
            </div>
        </div>
    </div>
<?php } ?>