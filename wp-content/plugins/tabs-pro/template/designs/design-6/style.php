<style>
/*design 6*/
<?php
if($tabs_border_size>=5)
	$var_tab_des_brdr_size=$tabs_border_size;
else
	$var_tab_des_brdr_size=5;
?>
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs{
    border-bottom: 0 none;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
    margin-right: 2px;
	margin-bottom: -1px;
    position: relative;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
    border: none;
    padding: 18px 25px;
    color:<?php echo $tabs_title_font_clr; ?>;
    background:<?php echo $tabs_title_bg_clr; ?>;
    border-radius:0;
	text-align:center;
	vertical-align:middle;
	font-size:<?php echo $tabs_title_size; ?>px;
	font-weight:<?php echo intval($tabs_title_icon_font_weight/100)*100;?>;
	font-family:<?php echo $all_tabs_font_family;?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a i{
    font-size:<?php echo $tabs_icon_size; ?>px;
    color:<?php echo $tabs_title_icon_clr; ?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:focus,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:hover{
    border: none;
    background: <?php echo $select_tabs_bg_clr; ?> !important;
    color:<?php echo $select_tabs_title_clr; ?> !important;
    transition:background 0.20s linear;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a i{
	color:<?php echo $select_tabs_icon_clr; ?> !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active:after {
    content: "";
    position: absolute;
    bottom: -15px;
    left: 0;
	right:0;
	margin:auto;
	width:0px;
    border: 15px solid transparent;
    border-bottom-color: <?php echo $select_tabs_bg_clr; ?> !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:after{
    content: "";
    position: absolute;
    top: 4px;
	bottom:4px;
    left: 4px;
	right:4px;
    border: 3px solid <?php echo $select_tabs_btn_border_color; ?> !important;
    display: block;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
    background: <?php echo $tabs_desc_bg_clr; ?>;
	color:<?php echo $tabs_desc_font_clr; ?>;
    line-height: 1.6;
    padding:20px 25px;
    margin-top: 15px;
	border: <?php echo $var_tab_des_brdr_size/5; ?>px solid <?php echo $tabs_border_color; ?>;
    border-top:<?php echo $var_tab_des_brdr_size; ?>px solid <?php echo $select_tabs_bg_clr; ?>;
    border-bottom:<?php echo $var_tab_des_brdr_size; ?>px solid <?php echo $select_tabs_bg_clr; ?>;
	font-size:<?php echo $tabs_des_size; ?>px;
	font-family:<?php echo $all_tabs_font_family;?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content .tab-pane p{
	color:<?php echo $tabs_desc_font_clr; ?>;
    font-size: <?php echo $tabs_des_size; ?>px;
	font-family:<?php echo $all_tabs_font_family;?>;
	background: <?php echo $tabs_desc_bg_clr; ?>;
    border:none;
	line-height:1.6;
	padding:0;
	text-align:left;
}
@media only screen and (max-width: 600px){
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
        width:100% !important;
        margin-bottom: 5px;
    }
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
        padding: 20px;
    }
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active:after {
        border:none;
    }
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:after{
        width:97%;
        left:1.5%;
    }
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
        margin-top: 5px;
    }
}
</style>