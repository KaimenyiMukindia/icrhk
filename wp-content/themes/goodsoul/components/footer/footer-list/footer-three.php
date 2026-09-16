<footer class="main-footer style-three">
    <div class="footer-bottom-three">
        <div class="auto-container">
            <ul class="social-icon-four">
                <?php
                    $footer_social_area_three = goodsoul_get_options('footer_social_area_three');
                    echo wp_kses($footer_social_area_three, 'code_contxt');
                ?>
            </ul>
        </div>
    </div>                
    <div class="scroll-to-top scroll-to-target style-three" data-target="html" style="display: block;"><span class="icon flaticon-next"></span></div>
</footer>
