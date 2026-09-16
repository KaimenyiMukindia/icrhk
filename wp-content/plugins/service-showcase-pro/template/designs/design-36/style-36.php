<style>
<?php if($sb_set_image_size>90) 
		$sb_set_image_size=90;
	  if($sb_set_icon_size>90) 
		$sb_set_icon_size=90;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    background: <?php echo $sb_set_bg_clr;?>;
    margin-top:20px;
	text-align: center;
    padding: 30px 5px;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    background:<?php echo $sb_set_icon_bg_clr;?>;
    height: 130px;
    width: 130px;
    border-radius:50%;
    display: inline-block;
    margin-bottom:30px;
	position:relative;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
   color: <?php echo $sb_set_icon_clr;?>;
   position:absolute;
   top:50%;
   left:50%;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i
<?php if($sb_set_same_icon_img_width=='yes'){?>,.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img<?php }?>
{
	width: <?php echo $sb_set_icon_size;?>px;
	height: <?php echo $sb_set_icon_size;?>px;
	margin-top: -<?php echo  intval($sb_set_icon_size/2);?>px;
	margin-left: -<?php echo intval($sb_set_icon_size/2);?>px;
	font-size: <?php echo $sb_set_icon_size;?>px;
}
<?php if($sb_set_same_icon_img_width=='no')
{ 	
	?>
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
		max-width:none;
		width: <?php echo $sb_set_image_size;?>px;
		height: <?php echo $sb_set_image_size;?>px;
		margin-top: -<?php echo  intval($sb_set_image_size/2);?>px;
		margin-left: -<?php echo intval($sb_set_image_size/2);?>px; 
					
	}
<?php 
} ?>
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content{
	padding:5px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3{
    color: <?php echo $sb_set_title_clr;?>;
    font-size: <?php echo $sb_set_title_size;?>px;
    font-weight: 600;
    margin-top: 0;
	margin-bottom:15px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p{
    color:<?php echo $sb_set_des_clr;?>;
    margin-bottom:15px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
    font-size:<?php echo $sb_set_des_size;?>px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .btn{
	<?php echo $service_box_font_family;?>
    color:<?php echo $sb_set_link_clr;?> !important;
	border:<?php echo $sb_set_brdr_size; ?>px solid <?php echo $sb_set_link_clr;?> !important;
	background:<?php echo $sb_set_link_bg_clr;?> !important;
	-webkit-box-shadow:none;
	box-shadow:none;
    font-size:<?php echo $sb_set_link_size;?>px;
	padding:7px 20px !important;
	border-radius:4px;
	text-transform:none;
	letter-spacing:0;
}
.wpsm_serviceBox_<?php echo $PostId;?> .btn i{
	color:<?php echo $sb_set_link_clr;?> !important;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .btn:before,
.wpsm_serviceBox_<?php echo $PostId;?> .btn:after{
	content:"";
}
.wpsm_serviceBox_<?php echo $PostId;?> .btn:hover{
    -webkit-box-shadow:none;
	box-shadow:none;
	letter-spacing:0;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon {
			background:<?php echo $sb_all_contents_icon_bg_clr;?>;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .btn{
			color:<?php echo $sb_all_contents_link_clr;?> !important;
			border-color:<?php echo $sb_all_contents_link_clr;?> !important;
			background:<?php echo $sb_all_contents_link_bg_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .btn i{
			color:<?php echo $sb_all_contents_link_clr;?> !important;
		}
	<?php
	$k++;	
	}
}
?>
</style>