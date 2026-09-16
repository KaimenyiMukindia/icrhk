<style>
/*design 10*/
<?php 
//design 10
if($tabs_button_align=="left" || $tabs_button_align=="center")
	$border_side="left";
else
	$border_side="right";
?>

.wpsm_tab_<?php echo $post_id; ?> a:hover,
.wpsm_tab_<?php echo $post_id; ?>a:focus{
	outline: none;
	text-decoration: none;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs{
	border-bottom: 0 none;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
	margin-right: 2px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
	font-size: <?php echo $tabs_title_size; ?>px;
    color: <?php echo $tabs_title_font_clr; ?>;
    padding: 15px 15px;
    border: none;
    border-radius: 0;
    background: <?php echo $tabs_title_bg_clr; ?>;
    position: relative;
	font-weight:<?php echo intval($tabs_title_icon_font_weight/100)*100;?>;
	font-family:<?php echo $all_tabs_font_family;?>;
	text-align:center;
	vertical-align:middle;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:after{
	content: "";
	position: absolute;
	bottom: -12px;
	<?php echo $border_side; ?>: 0;
	border-bottom: 15px solid transparent;
	border-<?php echo $border_side; ?>: 15px solid <?php echo $select_tabs_bg_clr; ?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:focus,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:hover{
	border: none;
	background: <?php echo $select_tabs_bg_clr; ?> !important;
	color: <?php echo $select_tabs_title_clr; ?> !important;
	transition: all 0.20s linear 0s;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
	font-size: <?php echo $tabs_des_size; ?>px;
	color: <?php echo $tabs_desc_font_clr; ?>;
	border: 0 none;
	line-height: 1.6;
	padding: 5px 0;
	margin-top: 15px;
	background: <?php echo $tabs_desc_bg_clr; ?>;
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
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content h3{
	margin-top: 10px;
	font-size: 24px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a i{
	font-size: <?php echo $tabs_icon_size; ?>px;
	color: <?php echo $tabs_title_icon_clr; ?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a i{
	color: <?php echo $select_tabs_icon_clr; ?> !important;
}

@media only screen and (max-width: 600px){
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
		width: 100% !important;
		margin-bottom: 5px;
	}
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
		padding: 20px;
	}
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:after{
		border: none;
	}
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
		margin-top: 5px;
	}
}
                    
</style>