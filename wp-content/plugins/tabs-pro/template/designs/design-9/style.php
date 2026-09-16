<style>
/* Design -9 */
.wpsm_tab_<?php echo $post_id; ?> a:hover,
.wpsm_tab_<?php echo $post_id; ?> a:focus{
    text-decoration: none;
    outline: none;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs{
    border-bottom: 0 none;
    background: transparent;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
	margin-bottom:0;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
    font-size: <?php echo $tabs_title_size; ?>px;
    color: <?php echo $tabs_title_font_clr; ?>;
    background: transparent;
    border: none;
    padding: 12px 22px;
    border-radius: 30px;
    position: relative;
	font-weight:<?php echo intval($tabs_title_icon_font_weight/100)*100;?>;
	font-family:<?php echo $all_tabs_font_family;?>;
	text-align:center;
	vertical-align:middle;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a{
    border: 0 none;
    background: <?php echo $select_tabs_bg_clr; ?> !important;
    color: <?php echo $select_tabs_title_clr; ?> !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:after{
    content: "";
    position: absolute;
    left: 0;
	right:0;
	margin:auto;
	width:0px;
    bottom: -14px;
    border: 7px solid transparent;
    border-top: 7px solid <?php echo $select_tabs_bg_clr; ?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
    padding: 12px 18px;
    font-size: <?php echo $tabs_des_size; ?>px;
    color: <?php echo $tabs_desc_font_clr; ?>;
    line-height: 1.6;
    margin-top: 20px;
    background: <?php echo $tabs_desc_bg_clr; ?>;
    border-bottom: <?php echo $tabs_border_size; ?>px solid <?php echo $tabs_border_color; ?>;
	font-family:<?php echo $all_tabs_font_family;?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content .tab-pane p{
	color:<?php echo $tabs_desc_font_clr; ?>;
    font-size: <?php echo $tabs_des_size; ?>px;
	font-family:<?php echo $all_tabs_font_family;?>;
	background:<?php echo $tabs_desc_bg_clr; ?>;
    border:none;
	line-height:1.6;
	padding:0;
	text-align:left;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a i{
	/*padding-left:8px;
	padding-right:8px;*/
	color:<?php echo $tabs_title_icon_clr; ?>;
	font-size:<?php echo $tabs_icon_size; ?>px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a i{
	color:<?php echo $select_tabs_icon_clr; ?> !important;
}

@media only screen and (max-width: 600px) {
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
        width: 100% !important;
        margin-bottom: 10px;
    }
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
        border: 1px solid <?php echo $tabs_btn_border_color; ?>;
    }
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:after{
        border:none;
    }
}
</style>