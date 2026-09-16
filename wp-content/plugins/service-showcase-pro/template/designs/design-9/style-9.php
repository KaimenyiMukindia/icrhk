<style>
<?php if($sb_set_image_size>70) 
		$sb_set_image_size=70;
	  if($sb_set_icon_size>70) 
		$sb_set_icon_size=70;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    position: relative;
    overflow: hidden;
    z-index: 1;
	padding:15px;
	margin-bottom:20px;
	background: <?php echo $sb_set_bg_clr;?>;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_title{
    font-size: <?php echo $sb_set_title_size; ?>px;
    font-weight: 800;
    color: <?php echo $sb_set_title_clr; ?>;
    margin-bottom: 15px;
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content .wpsm_title{
    color: <?php echo $sb_set_hover_title_clr; ?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_description{
    font-size: <?php echo $sb_set_des_size; ?>px;
    color: <?php echo $sb_set_des_clr; ?>;
    margin-bottom: 20px;
    z-index: 1;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
	margin-top:15px;
	position:relative;
	min-height:50px;
	margin-bottom: 20px;
}

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i.default_icon{
    color: <?php echo $sb_set_icon_clr; ?>;
    position: absolute;
    bottom: 1px;
    right: 10px;
    z-index: -1;
    transform: rotate(45deg) scale(1);
	-ms-transform: rotate(45deg) scale(1);
	-webkit-transform: rotate(45deg) scale(1);
	-moz-transform: rotate(45deg) scale(1);
	-o-transform: rotate(45deg) scale(1);
	
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon i.default_icon{
    transform: rotate(0deg) scale(0.8);
	-ms-transform: rotate(0deg) scale(0.8);
	-webkit-transform: rotate(0deg) scale(0.8);
	-moz-transform: rotate(0deg) scale(0.8);
	-o-transform: rotate(0deg) scale(0.8);
}	
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
    position: absolute;
    bottom: 1px;
    right: 10px;
    z-index: -1;
    transform: rotate(45deg) scale(1);
	-ms-transform: rotate(45deg) scale(1);
	-webkit-transform: rotate(45deg) scale(1);
	-moz-transform: rotate(45deg) scale(1);
	-o-transform: rotate(45deg) scale(1);
	
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
}	
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon img{
	 transform: rotate(0deg) scale(0.8);
	-ms-transform: rotate(0deg) scale(0.8);
	-webkit-transform: rotate(0deg) scale(0.8);
	-moz-transform: rotate(0deg) scale(0.8);
	-o-transform: rotate(0deg) scale(0.8);
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i.default_icon
<?php if($sb_set_same_icon_img_width=='yes'){?>,.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img<?php }?>
{
	width: <?php echo $sb_set_icon_size;?>px;
	height: <?php echo $sb_set_icon_size;?>px;
	font-size: <?php echo $sb_set_icon_size;?>px;
}
<?php if($sb_set_same_icon_img_width=='no')
{ 	
	?>
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
		width: <?php echo $sb_set_image_size;?>px;
		height: <?php echo $sb_set_image_size;?>px;
	}
<?php 
} ?>
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon .wpsm_read_more{
    display: inline-block;
    padding: 12px 15px;
    background: <?php echo $sb_set_link_bg_clr; ?>;
    font-size: <?php echo $sb_set_link_size; ?>px !important;
    color: <?php echo $sb_set_link_clr; ?> !important;
    border-radius: 0 20px;
	-webkit-box-shadow:none;
	box-shadow:none;
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
	text-align:center;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon .wpsm_read_more i{
    font-size: <?php echo $sb_set_link_size; ?>px;
    color: <?php echo $sb_set_link_clr; ?> !important;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon .wpsm_read_more{
    background: <?php echo $sb_set_hover_link_bg_clr; ?> !important;
    color: <?php echo $sb_set_hover_link_clr; ?> !important;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon .wpsm_read_more i{
    color: <?php echo $sb_set_hover_link_clr; ?> !important;
}
@media only screen and (max-width: 320px){
    .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon .default_icon{
		position:relative;
		margin-bottom:10px;
		margin-left:10px;
	}
	
}

<?php
//Individual Colors Settings
if($sb_ind_clr_enable=="yes")
{
	$k=1;
	foreach($sb_all_data as $sb_single_set_data)
	{ 	
		$sb_all_contents_bg_clr			=$sb_single_set_data['sb_all_contents_bg_clr'];
		$sb_all_contents_brdr_clr		=$sb_single_set_data['sb_all_contents_brdr_clr'];
		
		$sb_all_contents_icon_clr		=$sb_single_set_data['sb_all_contents_icon_clr'];
		$sb_all_contents_icon_bg_clr	=$sb_single_set_data['sb_all_contents_icon_bg_clr'];
		$sb_all_contents_icon_brdr_clr	=$sb_single_set_data['sb_all_contents_icon_brdr_clr'];
		
		$sb_all_contents_title_clr		=$sb_single_set_data['sb_all_contents_title_clr'];
		$sb_all_contents_des_clr		=$sb_single_set_data['sb_all_contents_des_clr'];
		
		$sb_all_contents_link_clr		=$sb_single_set_data['sb_all_contents_link_clr'];
		$sb_all_contents_link_bg_clr	=$sb_single_set_data['sb_all_contents_link_bg_clr'];
		
		$var_sb_bg_clr="rgba(". HextoR($sb_all_contents_bg_clr).",".HextoG($sb_all_contents_bg_clr).",".HextoB($sb_all_contents_bg_clr).",".$sb_set_bg_opacity.")";
		?>
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>{
			background: <?php echo $var_sb_bg_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon i{
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_title{
			color:<?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_description{
			color:<?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon .wpsm_read_more{
			color: <?php echo $sb_all_contents_link_clr;?> !important;
			background:<?php echo $sb_all_contents_link_bg_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon .wpsm_read_more i{
			color: <?php echo $sb_all_contents_link_clr;?> !important;
		}
		
	<?php
	$k++;	
	}
}
?>	
</style>
