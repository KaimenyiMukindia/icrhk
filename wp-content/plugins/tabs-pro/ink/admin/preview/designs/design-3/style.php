<style>
/*design 3*/
<?php 
//design 3
if($tabs_button_align=="left" || $tabs_button_align=="center")
{
	$border_side="left";
	$border_opp_side="right";
}
else
{
	$border_side="right";
	$border_opp_side="left";
}
?>
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs{
    border-bottom:0px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
    border-<?php echo $border_opp_side; ?>:1px solid <?php echo $tabs_btn_border_color; ?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:last-child{
    border-<?php echo $border_opp_side; ?>:0px solid <?php echo $tabs_btn_border_color; ?> !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:first-child a{
    border-<?php echo $border_side; ?>:1px solid <?php echo $tabs_btn_border_color; ?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a {
    color: <?php echo $tabs_title_font_clr; ?>;
    background:<?php echo $tabs_title_bg_clr;?>;
    border-radius:0;
    font-size:<?php echo $tabs_title_size; ?>px;
    margin-right:-1px;
    padding: 5.5px 30px;
    border-top:1px solid <?php echo $tabs_btn_border_color; ?>;
    border-bottom: 1px solid <?php echo $tabs_btn_border_color; ?>;
	font-weight:<?php echo intval($tabs_title_icon_font_weight/100)*100;?>;
	font-family:<?php echo $all_tabs_font_family;?>;
	text-align:center;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:first-child a{
    border-top-<?php echo $border_side; ?>-radius: 5px;
	border-bottom-<?php echo $border_side; ?>-radius: 5px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:last-child a{
   border-top-<?php echo $border_opp_side; ?>-radius: 5px;
	border-bottom-<?php echo $border_opp_side; ?>-radius: 5px;
    border-<?php echo $border_opp_side; ?>:1px solid <?php echo $tabs_btn_border_color; ?>;
}

.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active > a,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs > li.active > a:focus,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs > li.active > a:hover ,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover{
    border: none;
    color:<?php echo $select_tabs_title_clr; ?> !important;
    background:<?php echo $select_tabs_bg_clr; ?> !important;
    border-top:1px solid <?php echo $select_tabs_bg_clr; ?>;
    border-bottom: 1px solid <?php echo $select_tabs_bg_clr; ?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:last-child.active a:after,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:last-child a:after{
    border: none;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:last-child a:after,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:last-child a:hover:before,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:last-child.active a:before,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:last-child a:before{
    border-<?php echo $border_side; ?>: none;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a i{
	color: <?php echo $tabs_title_icon_clr;?>;
	font-size: <?php echo $tabs_icon_size;?>px;
}

.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a i,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover i{
	color: <?php echo $select_tabs_icon_clr;?> !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
    padding:12px;
    color:<?php echo $tabs_desc_font_clr; ?>;
    margin-top:2%;
    font-size: <?php echo $tabs_des_size; ?>px;
    border: <?php echo $tabs_border_size; ?>px solid <?php echo $tabs_border_color; ?>;
	font-family:<?php echo $all_tabs_font_family;?>;
	line-height:1.6;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content .tab-pane p{
	color:<?php echo $tabs_desc_font_clr; ?>;
    font-size: <?php echo $tabs_des_size; ?>px;
    border:none;
	font-family:<?php echo $all_tabs_font_family;?>;
	line-height:1.6;
	background:none;
	padding:0;
	text-align:left;
}
<?php
if($tab_ind_clr_enable=='yes')
	{	
		$k=1;
		 foreach($tabs_data as $single_data)
		{
			 $indvid_tabs_title_bg_clr = $single_data['indvid_tabs_title_bg_clr'];
			 ?>
			 .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:nth-child(<?php echo $k; ?>) a:before
			 {
				border-<?php echo $border_side; ?>-color:<?php echo $indvid_tabs_title_bg_clr; ?>;
			 }
			 <?php
			 $k++; 
		}
	}

?>

@media only screen and (max-width: 600px){
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
		width: 100% !important; 
		border:none;
	}
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
       border: 1px solid <?php echo $tabs_btn_border_color;?>;
	   margin-right:0px;
	}
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a,
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a,
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover{
		border-radius:0 !important;
	}
	
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
        margin-bottom:10px;
	}
	
	<?php if($tabs_border_size>0){?>
		.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
			border: 1px solid <?php echo $tabs_border_color;?> !important;
		}
	<?php }?>
	
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:after,
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:before{
		border:0 !important;
		width:0;
	}
}

</style>