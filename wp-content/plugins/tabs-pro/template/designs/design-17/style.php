<style>
/*design 17*/
<?php 
//design 17
if($tabs_button_align=="left" || $tabs_button_align=="center")
	$border_side="left";
else
	$border_side="right";
?>

 .wpsm_tab_<?php echo $post_id; ?> a:hover,
 .wpsm_tab_<?php echo $post_id; ?> a:focus{
    outline: none;
	text-decoration: none;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs{
    background: transparent;
	border-bottom:0px;
	float: left;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
    text-align: center;
   	margin:0;
	float: none !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
	font-size: <?php echo $tabs_title_size; ?>px;
	color: <?php echo $tabs_title_font_clr; ?>;
	background: <?php echo $tabs_title_bg_clr; ?>;
	border-radius: 0;
	border: none;
	border-<?php echo $border_side; ?>: 5px solid <?php echo $tabs_btn_border_color; ?>;
	margin: 0;
	padding: 15px 15px;
	position: relative;
	font-weight:<?php echo intval($tabs_title_icon_font_weight/100)*100;?>;
	font-family:<?php echo $all_tabs_font_family;?>;
	text-align:center;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover{
	background: <?php echo $select_tabs_bg_clr; ?> !important;
	color: <?php echo $select_tabs_title_clr; ?> !important;
	border-<?php echo $border_side; ?>-color: <?php echo $select_tabs_btn_border_color; ?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:focus,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:hover{
	background: <?php echo $select_tabs_bg_clr; ?> !important;
	color: <?php echo $select_tabs_title_clr; ?> !important;
	border: none;
	border-<?php echo $border_side; ?>: 5px solid <?php echo $select_tabs_btn_border_color; ?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
	font-size: <?php echo $tabs_des_size; ?>px;
	color: <?php echo $tabs_desc_font_clr; ?>;
	line-height: 1.6;
	background: <?php echo $tabs_desc_bg_clr; ?>;
	border: none;
	padding: 0px 15px;
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
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content h3{
	font-size: 24px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a i{
	/*padding-left:8px;
	padding-right:8px;*/
	color: <?php echo $tabs_title_icon_clr;?>;
	font-size: <?php echo $tabs_icon_size;?>px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a i,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover i{
	color: <?php echo $select_tabs_icon_clr;?> !important;
}
@media screen and (max-width: 480px){
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{ width:100% !important; }
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs{
		float:none !important;
		}
}
                    
</style>