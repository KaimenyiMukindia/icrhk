<style>
/*design 20*/
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs{
    float: left;
	border-bottom:0px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
    margin-bottom: 10px;
    float: none !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:focus {
	outline: 0px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a {
    background:transparent !important;
    border-radius:0;
    border:none;
    font-size: <?php echo $tabs_title_size;?>px;
    color: <?php echo $tabs_title_font_clr;?>;
    padding: 16px 22px 10px;
	font-weight:<?php echo intval($tabs_title_icon_font_weight/100)*100;?>;
	font-family:<?php echo $all_tabs_font_family;?>;
	text-align:center;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover{
    background:transparent !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a i{
    color:<?php echo $tabs_title_icon_clr;?>;
	font-size: <?php echo $tabs_icon_size;?>px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a i{
    color:<?php echo $select_tabs_icon_clr;?> !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active > a,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active > a:focus,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active > a:hover {
    border: none;
    border-bottom: 2px solid <?php echo $select_tabs_btn_border_color;?>;
    color:<?php echo $select_tabs_title_clr;?> !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:after{
    content: " ";
    position: absolute;
    bottom: 0%;
    left: 0;
	right:0;
	margin:auto;
	width:0px;
    border-width: 7px;
    border-style: solid;
    border-color: rgba(136, 183, 213, 0) rgba(136, 183, 213, 0) <?php echo $select_tabs_btn_border_color;?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
    padding:12px;
    color:<?php echo $tabs_desc_font_clr;?>;
    font-size: <?php echo $tabs_des_size;?>px;
    margin-top:0;
    border:none;
	font-family:<?php echo $all_tabs_font_family;?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content .tab-pane p{
	color:<?php echo $tabs_desc_font_clr; ?>;
    font-size: <?php echo $tabs_des_size; ?>px;
	font-family:<?php echo $all_tabs_font_family;?>;
	background: none;
    border:none;
	line-height:1.6;
	padding:0;
	text-align:left;
}
@media only screen and (max-width: 480px) {
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs{
		float:none !important;
	}
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
        width:100% !important;
		margin-left:0;
		margin-right:0;
    }
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
       margin-bottom:10px;
    }
    
}
</style>