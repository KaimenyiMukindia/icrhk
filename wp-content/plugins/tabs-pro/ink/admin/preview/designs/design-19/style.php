<style>
/*design 19*/
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
	float: left;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
    border-right:1px solid #ddd;
	float: none !important;
	margin-bottom:5px;
	/*margin-<?php echo $border_opp_side; ?>:23px;*/
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:focus {
	outline: 0px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a {
    color: <?php echo $tabs_title_font_clr; ?>;
    background: <?php echo $tabs_title_bg_clr; ?>;
    border-radius: 0;
    font-size: <?php echo $tabs_title_size; ?>px;
    margin-right: -1px;
    padding: 5.5px 20px;
    border-top: 1px solid <?php echo $tabs_btn_border_color; ?>;
    border-bottom: 1px solid <?php echo $tabs_btn_border_color; ?>;
    border-<?php echo $border_side; ?>: 1px solid <?php echo $tabs_btn_border_color; ?>;
	font-weight:<?php echo intval($tabs_title_icon_font_weight/100)*100;?>;
	font-family:<?php echo $all_tabs_font_family;?>;
	text-align:center;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
    /*border-radius: 5px 0 0 5px;*/
	border-top-<?php echo $border_side; ?>-radius:5px;
	border-bottom-<?php echo $border_side; ?>-radius:5px;
}

.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover{
    background:<?php echo $select_tabs_bg_clr; ?> !important;
	color:<?php echo $select_tabs_title_clr; ?> !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active > a,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs > li.active > a:focus,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs > li.active > a:hover {
    border: none;
    color:<?php echo $select_tabs_title_clr; ?> !important;
    background:<?php echo $select_tabs_bg_clr; ?> !important;
	border-<?php echo $border_side; ?>:1px solid <?php echo $select_tabs_bg_clr; ?>;
    border-top:1px solid <?php echo $select_tabs_bg_clr; ?>;
    border-bottom: 1px solid <?php echo $select_tabs_bg_clr; ?>;
}

.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
    padding:12px;
    color:<?php echo $tabs_desc_font_clr; ?>;
    margin-top:2%;
    font-size: <?php echo $tabs_des_size; ?>px;
    border: <?php echo $tabs_border_size; ?>px solid <?php echo $tabs_border_color; ?>;
	font-family:<?php echo $all_tabs_font_family;?>;
	background:<?php echo $tabs_desc_bg_clr;?>;
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
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a i{
	color:<?php echo $tabs_title_icon_clr;?>;
	font-size: <?php echo $tabs_icon_size;?>px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a i,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover i{
	color: <?php echo $select_tabs_icon_clr;?> !important;
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
        width:100% !important;
        margin-left:0 !important;
		margin:right:0 !important;
    }
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs{
		float:none !important;
	}
	
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a,
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a,
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover{
		border-radius:0px ;
	}
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover{
		border-color:<?php echo $select_tabs_bg_clr; ?>;
	}
}
</style>