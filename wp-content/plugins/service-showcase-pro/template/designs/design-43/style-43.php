<style>
<?php if($sb_set_image_size>150) 
		$sb_set_image_size=150;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
	position: relative;
	overflow: hidden;
	margin-bottom:10px;
	perspective:1000px;
	-webkit-perspective:1000px;
	margin-top:20px;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
	width: 100%;
	min-height: 220px;
	padding: 20px;
	text-align: center;
	transition: all .5s ease;
	-ms-transition: all .5s ease;
	-webkit-transition: all .5s ease;
	-moz-transition: all .5s ease;
	-o-transition: all .5s ease;
	background-color: <?php echo $sb_set_bg_clr;?>;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content{
	position: absolute;
	top: 0;
	left: 0;
	z-index: 1;
	opacity: 0;
	width: 100%;
	min-height: 220px;
	padding: 20px;
	text-align: center;
	transition: all .5s ease;
	-ms-transition: all .5s ease;
	-webkit-transition: all .5s ease;
	-moz-transition: all .5s ease;
	-o-transition: all .5s ease;
	background-color: <?php echo $sb_set_hover_bg_clr;?>;
	backface-visibility:hidden;
	transform-style: preserve-3d;
   -webkit-transform: translateX(110px) rotateY(-180deg);
	-moz-transform: translateX(110px) rotateY(-180deg);
	-ms-transform: translateX(110px) rotateY(-180deg);
	-o-transform: translateX(110px) rotateY(-180deg);
	transform: translateX(110px) rotateY(-180deg);
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon .front-content{
	position: relative;
	top:<?php echo $sb_set_icon_size+100;?>px; 
		
	-webkit-transform: translateY(-50%);
	-moz-transform: translateY(-50%);
	-ms-transform: translateY(-50%);
	-o-transform: translateY(-50%);
	transform: translateY(-50%);
	text-align:center;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon .front-content i {
	color: <?php echo $sb_set_icon_clr;?>;
	font-weight: normal;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon .front-content img {
	margin:auto auto;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon .front-content i
<?php if($sb_set_same_icon_img_width=='yes'){?>,.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon .front-content img<?php }?>
{
	width: <?php echo $sb_set_icon_size;?>px;
	height: <?php echo $sb_set_icon_size;?>px;
	font-size: <?php echo $sb_set_icon_size;?>px;
	margin-bottom: 20px;
}
<?php if($sb_set_same_icon_img_width=='no')
{ 	
	?>
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon .img-wpsm_service_icon{
		top:<?php echo $sb_set_image_size+100;?>px; 
	}
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon .img-wpsm_service_icon img{
		width: <?php echo $sb_set_image_size;?>px;
		height: <?php echo $sb_set_image_size;?>px;
		margin-bottom: 20px;
	}
	<?php 
} ?>
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon .front-content h3.wpsm_title {
	font-size: <?php echo $sb_set_title_size;?>px;
	color: <?php echo $sb_set_title_clr;?>;
	text-align: center;
	margin-bottom: 15px;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3.wpsm_title {
	font-size: <?php echo $sb_set_title_size;?>px;
	font-weight: 700;
	color: <?php echo $sb_set_hover_title_clr;?>;
	margin-bottom:10px;
	margin-top:15px;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p {
	font-size: <?php echo $sb_set_des_size;?>px;
	color: <?php echo $sb_set_hover_des_clr;?>;
	margin:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read{
	padding: 30px 0 15px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read a{
	background: <?php echo $sb_set_hover_link_bg_clr;?>;
	padding: 15px 32px;
	font-weight:600;
	font-size:<?php echo $sb_set_link_size;?>px;
	color: <?php echo $sb_set_hover_link_clr;?> !important;
	line-height:<?php echo $sb_set_link_size+3;?>px;
	-webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read a i{
	font-weight:600;
	font-size:<?php echo $sb_set_link_size;?>px;
	color: <?php echo $sb_set_hover_link_clr;?> !important;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon{
	opacity: 0;
	 -webkit-transform: translateX(-110px) rotateY(-180deg);
	-moz-transform: translateX(-110px) rotateY(-180deg);
	-ms-transform: translateX(-110px) rotateY(-180deg);
	-o-transform: translateX(-110px) rotateY(-180deg);
	transform: translateX(-110px) rotateY(-180deg);
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content {
	opacity: 1;
  -webkit-transform: rotateY(0);
	-moz-transform: rotateY(0);
	-ms-transform: rotateY(0);
	-o-transform: rotateY(0);
	transform: rotateY(0);
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
		
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon {
			background-color: <?php echo $var_sb_bg_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon .front-content i {
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon .front-content h3.wpsm_title {
			color: <?php echo $sb_all_contents_title_clr;?>;
		}
	<?php
	$k++;	
	}
}
?>

</style>