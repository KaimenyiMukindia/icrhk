<style>
/* design 15 */
<?php 
//design 15
if($tabs_button_align=="left" || $tabs_button_align=="center")
	$border_side="left";
else
	$border_side="right";
?>
	
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs {
	border:none;
}
.wpsm_tab_<?php echo $post_id; ?> a:hover,
.wpsm_tab_<?php echo $post_id; ?> a:focus{
	outline: none;
	text-decoration: none;
}
.wpsm_tab_<?php echo $post_id; ?>{
	padding-top:10px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:focus {
	outline: 0px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs{
	float: left;
	margin-top:0;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
	margin-bottom: 4px;
	float: none !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
	font-size: <?php echo $tabs_title_size; ?>px;
    color: <?php echo $tabs_title_font_clr; ?>;
    background: <?php echo $tabs_title_bg_clr; ?> !important;
    border-radius: 0;
    padding: 16px;
    margin-right: 0;
    border-top: 1px solid <?php echo $tabs_btn_border_color; ?> !important;
	border-bottom: 1px solid <?php echo $tabs_btn_border_color; ?> !important;
    position: relative;
    transition: all 0.3s ease 0s;
	font-weight:<?php echo intval($tabs_title_icon_font_weight/100)*100;?>;
	font-family:<?php echo $all_tabs_font_family;?>;
	text-align:center;
	border-<?php echo $border_side; ?>: 1px solid <?php echo $tabs_btn_border_color; ?>;
}

.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:focus,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:hover{
	color: <?php echo $select_tabs_title_clr; ?> !important;
	background: <?php echo $tabs_title_bg_clr; ?> !important;
	border-<?php echo $border_side; ?>: 1px solid <?php echo $tabs_btn_border_color; ?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:before,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:before{
	content: "";
	width: 100%;
	position: absolute;
	top: 0;
	left: 0;
	border-top: 4px solid <?php echo $select_tabs_btn_border_color; ?>;
	opacity: 0;
	transition: all 0.5s ease 0s;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:before,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:hover:before{
	top: -3px;
	opacity: 1;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover:before{
	top: -4px;
	opacity: 1;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:after,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:after{
	content: "";
	position: absolute;
	top: 2px;
	left: 0;
	right:0;
	margin:auto;
	width:0px;
	border: 7px solid transparent;
	border-top: 7px solid <?php echo $select_tabs_btn_border_color; ?>;
	opacity: 0;
	transition: all 0.5s ease 0s;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:after,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover:after{
	top: -1px;
	opacity: 1;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a i{
	font-size: <?php echo $tabs_icon_size; ?>px;
	color: <?php echo $tabs_title_icon_clr; ?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a i{
	color: <?php echo $select_tabs_icon_clr; ?> !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
	font-size: <?php echo $tabs_des_size; ?>px;
	color: <?php echo $tabs_desc_font_clr; ?>;
	line-height: 1.6;
	padding: 20px;
	border: <?php echo $tabs_border_size; ?>px solid <?php echo $tabs_border_color; ?>;
	font-family:<?php echo $all_tabs_font_family;?>;
	background: <?php echo $tabs_desc_bg_clr; ?>;
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
@media only screen and (max-width: 480px){
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{ 
		width: 100% !important; 
	}
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs{
		float:none !important;
	}
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{ 
		border-left: 1px solid <?php echo $tabs_btn_border_color; ?>;
		border-right: 1px solid <?php echo $tabs_btn_border_color; ?>;
	}
}
</style>