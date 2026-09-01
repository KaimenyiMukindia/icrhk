
<div class="widget-wrapper">
    <div class="row">
        <!-- Contact Widget -->
        <div class="col-lg-3 col-md-6 contact-widget footer-widget">
            <h4 class="widget-title">Contact</h4>
            <ul>
                <li>5404 Berrick Street, <br> 2nd cross, <br>Boston, MA 02115.</li>
                <li><a href="mailto:support@goodsoul.com">supportyou@goodsoul.com </a></li>
            </ul>   
            <h3><a href="tel:+211456789">+211 456 789</a></h3>                 
        </div>
        <!-- About Widget -->
        <?php 
            dynamic_sidebar('footer_sidebar_1'); 
             dynamic_sidebar('footer_sidebar_2');?>               
        <!-- </div> -->
        <!-- Newsletter Widget -->
        <div class="col-lg-3 col-md-6 newsletter-widget footer-widget">
            <h4 class="widget-title">Newsletter</h4>
            <div class="text">Subscribe us and get latest news & <br>upcoming events.</div>
            <form action="#">
                <input type="email" placeholder="Email Address...">
                <button class="submin-btn"><span class="flaticon-next"></span>Subscribe Us</button>
            </form>
        </div>
    </div>
</div>