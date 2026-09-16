<style>
<?php if($sb_set_image_size>80) 
		$sb_set_image_size=80;
	  if($sb_set_icon_size>80) 
		$sb_set_icon_size=80;
?>
.wpsm_serviceBox_<?php echo $PostId;?> {
    background: <?php echo $sb_set_bg_clr;?>;
    text-align: center;
    padding: 0 0 25px;
    box-shadow: 0 2px 5px 0 #888282;
    border-top: <?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_brdr_clr;?>;
    margin-top: 70px;
	margin-bottom: 15px;
	
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon {
    width: 130px;
    height: 130px;
    line-height: 144px;
    border-radius: 50%;
    background: <?php echo $sb_set_icon_bg_clr;?>;
    border: 5px solid <?php echo $sb_set_icon_brdr_clr;?>;
    margin: -65px auto 0;
	position:relative;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
   color: <?php echo $sb_set_icon_clr;?>;
   position:absolute;
   top:50%;
   left:50%;
   transition: all 0.5s ease 0s;
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
		width: <?php echo $sb_set_image_size;?>px;
		height: <?php echo $sb_set_image_size;?>px;
		margin-top: -<?php echo  intval($sb_set_image_size/2);?>px;
		margin-left: -<?php echo intval($sb_set_image_size/2);?>px; 
				
	}
<?php 
} ?>
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content{
    padding: 0 25px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3{
    color: <?php echo $sb_set_title_clr;?>;
    font-weight: bold;
    margin: 30px 0 10px;
	font-size:<?php echo $sb_set_title_size;?>px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p{
    font-weight: lighter;
    color: <?php echo $sb_set_des_clr;?>;
    margin: 0 0 20px;
	font-size:<?php echo $sb_set_des_size;?>px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read{
    padding: 30px 0;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a{
    border: 4px solid <?php echo $sb_set_brdr_clr;?> !important;
    padding: 9px 25px !important;
    font-weight:600;
    font-size: <?php echo $sb_set_link_size;?>px;
	background: <?php echo $sb_set_link_bg_clr; ?> !important;
    color: <?php echo $sb_set_link_clr; ?> !important;
    -webkit-box-shadow:none;
	box-shadow:none;
	border-radius:4px;
	text-transform:none;
	letter-spacing:0;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a i{
	color: <?php echo $sb_set_link_clr; ?> !important;
	font-size: <?php echo $sb_set_link_size;?>px;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read .wpsm_read_more:before,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read .wpsm_read_more:after{
	content:"";
	position:relative;
	width:0;
	font-size:0;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_read .wpsm_read_more{
    background: <?php echo $sb_set_hover_link_bg_clr; ?> !important;
    color: <?php echo $sb_set_hover_link_clr; ?> !important;
	border-color:<?php echo $sb_set_hover_link_clr; ?> !important;
	-webkit-box-shadow:none;
	box-shadow:none;
	letter-spacing:0;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_read a i{
	color: <?php echo $sb_set_hover_link_clr; ?> !important;
}
@media screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{  margin-top: 80px; }
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
			border-top-color: <?php echo $sb_all_contents_brdr_clr;?>;
    
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon {
			border-color: <?php echo $sb_all_contents_icon_brdr_clr;?>;
			background: <?php echo $sb_all_contents_icon_bg_clr;?>;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read a{
			border-color: <?php echo $sb_all_contents_brdr_clr;?> !important;
			background: <?php echo $sb_all_contents_link_bg_clr; ?> !important;
			color: <?php echo $sb_all_contents_link_clr; ?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read a i{
			color: <?php echo $sb_all_contents_link_clr; ?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:hover .wpsm_read .wpsm_read_more{
			background: <?php echo $sb_set_hover_link_bg_clr; ?> !important;
			color: <?php echo $sb_set_hover_link_clr; ?> !important;
			border-color:<?php echo $sb_set_hover_link_clr; ?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:hover .wpsm_read a i{
			color: <?php echo $sb_set_hover_link_clr; ?> !important;
		}
	<?php
	$k++;	
	}
}
?>
</style>