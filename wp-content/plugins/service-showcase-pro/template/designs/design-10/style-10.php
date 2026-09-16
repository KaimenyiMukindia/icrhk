<style>
<?php if($sb_set_image_size>70) 
		$sb_set_image_size=70;
	  if($sb_set_icon_size>70) 
		$sb_set_icon_size=70;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    text-align: center;
    margin: 10px -15px;
    padding: 50px 30px;
	background-color: <?php echo $sb_set_bg_clr; ?>;
	margin-bottom:15px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    width: 100px;
    height: 100px;
    border-radius: 50%;
	line-height: 100px;
    border: 1px solid <?php echo $sb_set_icon_brdr_clr; ?>;
    font-size: <?php echo $sb_set_icon_size; ?>px;
    display: inline-block;
    margin-bottom: 30px;
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
	
	color: <?php echo $sb_set_icon_clr; ?>;
	position:relative;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon{
    box-shadow: 0 0 0 15px <?php echo $sb_set_icon_brdr_clr; ?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
   position:absolute;
   top: 50%;
   left: 50%;	
   color: <?php echo $sb_set_icon_clr; ?>;
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
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
   	<?php if($sb_set_same_icon_img_width=='no')
	{
		?>
		width: <?php echo $sb_set_image_size;?>px;
		height: <?php echo $sb_set_image_size;?>px;
		margin-top: -<?php echo  intval($sb_set_image_size/2);?>px;
		margin-left: -<?php echo intval($sb_set_image_size/2);?>px; 
		<?php 
	}?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title{
    font-size: <?php echo $sb_set_title_size; ?>px;
    font-weight: 500;
    margin: 0 0 15px;
	color: <?php echo $sb_set_title_clr; ?>;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_description{
    font-size: <?php echo $sb_set_des_size; ?>px;
     margin: 0 0 15px;
	color: <?php echo $sb_set_des_clr; ?>;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read .wpsm_read_more{
	font-size: <?php echo $sb_set_link_size; ?>px;
    background: <?php echo $sb_set_link_bg_clr; ?>;
    color: <?php echo $sb_set_link_clr; ?> !important;
	border-color: <?php echo $sb_set_link_bg_clr; ?> !important;
    padding: 10px 35px;
	-webkit-box-shadow:none;
	box-shadow:none;
    transition: all 0.6s ease-in-out;
    -webkit-transition: all 0.6s ease-in-out;
    -moz-transition: all 0.6s ease-in-out;
    -ms-transition: all 0.6s ease-in-out;
    -o-transition: all 0.6s ease-in-out;
	margin-top:20px;
	border-radius:4px;
	text-transform:none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read .wpsm_read_more i{ 
	color: <?php echo $sb_set_link_clr; ?> !important;
	transition: all 0.6s ease-in-out;
    -webkit-transition: all 0.6s ease-in-out;
    -moz-transition: all 0.6s ease-in-out;
    -ms-transition: all 0.6s ease-in-out;
    -o-transition: all 0.6s ease-in-out;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read .wpsm_read_more:before,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read .wpsm_read_more:after{
	content:'';
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_read .wpsm_read_more{
    background: <?php echo $sb_set_hover_link_bg_clr; ?> !important;
    color: <?php echo $sb_set_hover_link_clr; ?> !important;
	border-color: <?php echo $sb_set_hover_link_bg_clr; ?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_read .wpsm_read_more i{
	color: <?php echo $sb_set_hover_link_clr; ?> !important;
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
			border-color: <?php echo $sb_all_contents_icon_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon i{
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_title{
			color:<?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_description{
			color:<?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read .wpsm_read_more{
			background: <?php echo $sb_all_contents_link_bg_clr; ?> !important;
			color: <?php echo $sb_all_contents_link_clr; ?> !important;
			border-color: <?php echo $sb_all_contents_link_bg_clr; ?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read .wpsm_read_more i{ 
			color: <?php echo $sb_all_contents_link_clr; ?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:hover .wpsm_read .wpsm_read_more{
			background: <?php echo $sb_set_hover_link_bg_clr; ?> !important;
			color: <?php echo $sb_set_hover_link_clr; ?> !important;
			border-color: <?php echo $sb_set_hover_link_bg_clr; ?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:hover .wpsm_read .wpsm_read_more i{
			color: <?php echo $sb_set_hover_link_clr; ?> !important;
		}
		
	<?php
	$k++;	
	}
}
?>	
</style>