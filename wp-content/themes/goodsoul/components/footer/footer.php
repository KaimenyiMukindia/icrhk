<?php
    $footer_copyright_text = goodsoul_get_options('footer_copyright');
    $footer_copyright_link = goodsoul_get_options('footer_copyright_link');
    $footer_logo_image = goodsoul_get_options('footer_logo_image');
    $footer_social_icon = goodsoul_get_options('footer_social_area');
    if(isset($footer_social_icon) && $footer_social_icon != ''){
        $footer_social = $footer_social_icon;
    }
?>
<!-- Main Footer -->

<!--Scroll to top-->
<div class="scroll-to-top scroll-to-target" data-target="html"><span class="icon flaticon-arrow"></span></div>
<!--...end footer area....-->    