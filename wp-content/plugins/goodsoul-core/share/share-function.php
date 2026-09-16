<?php
    function goodsoul_share_button_fun() {
?>
    <div class="share-icon style-two post-share-icon">
        <div class="share-btn"><img src="<?php echo get_template_directory_uri().'/assets/';?>images/resource/dotted.png" alt=""></div>
        <ul class="social-icon-three">
            <li>
                <a onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_url(get_permalink()); ?>"><span class="fa fa-facebook-f"></span></a>
            </li>
            <li>
                <a onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" href="https://twitter.com/home?status=<?php echo urlencode(get_the_title()); ?>-<?php echo esc_url(get_permalink()); ?>"><span class="fa fa-twitter"></span></a>
            </li>
            <li>
                <a onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" href="https://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo esc_url(get_permalink()); ?>" target="_blank">
                    <span class="fa fa-linkedin"></span>
                </a>
            </li>
            <li>
                <a href="mailto:?Subject=<?php echo urlencode(get_the_title()); ?>&amp;Body=I%20saw%20this%20and%20thought%20of%20you!%20<?php echo esc_url(get_permalink()); ?>"><span class="fa fa-envelope"></span></a>
            </li>
        </ul>
    </div>
    <div class="text"><?php _e("Share This Post","goodsoul-core")?></div>
<?php
}
add_action('goodsoul_share_button', 'goodsoul_share_button_fun');
    function goodsoul_share_button_page() {
?>
    <div class="share-icon style-two post-share-icon">
        <div class="share-btn"><img src="<?php echo get_template_directory_uri().'/assets/';?>images/resource/dotted.png" alt=""></div>
        <ul class="social-icon-three">
            <li>
                <a onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_url(get_permalink()); ?>"><span class="fa fa-facebook-f"></span></a>
            </li>
            <li>
                <a onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" href="https://twitter.com/home?status=<?php echo urlencode(get_the_title()); ?>-<?php echo esc_url(get_permalink()); ?>"><span class="fa fa-twitter"></span></a>
            </li>
            <li>
                <a onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" href="https://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo esc_url(get_permalink()); ?>" target="_blank">
                    <span class="fa fa-linkedin"></span>
                </a>
            </li>
        </ul>
    </div>
<?php
}
add_action('goodsoul_share_button_page', 'goodsoul_share_button_page');

