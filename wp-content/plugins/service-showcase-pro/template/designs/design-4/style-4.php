<style>
.wpsm_serviceBox_<?php echo $PostId;?>{
	border: <?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_brdr_clr;?>;
	text-align: center;
	padding: 40px 0;
	overflow: hidden;
	position: relative;
	z-index: 1;
	transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	background:<?php echo $sb_set_icon_bg_clr;?>;
	margin-top:20px;
}
.wpsm_serviceBox_<?php echo $PostId;?>:before,
.wpsm_serviceBox_<?php echo $PostId;?>:after{
	content: "";
	width: 200%;
	height: 200%;
	background: <?php echo $sb_set_bg_clr;?>;/*#eba133*/
	position: absolute;
	top: 180px;
	left: 0;
	z-index: -1;
	transform: rotate(-18deg);
	-webkit-transform: rotate(-18deg);
	transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?>:before{
	background: <?php echo $sb_set_hover_icon_bg_clr;?> !important;
	left: -120%;
	transform: rotate(24deg);
	-webkit-transform: rotate(24deg);
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover:before{
	transform: rotate(16deg);
	-webkit-transform: rotate(16deg);
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover:after{
	background: <?php echo $sb_set_hover_bg_clr;?> !important;/*#684f8e*/
	transform: rotate(-10deg);
	-webkit-transform: rotate(-10deg);
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
	line-height: 100px;
	margin-bottom: 100px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
	font-size: <?php echo $sb_set_icon_size;?>px;
	color: <?php echo $sb_set_icon_clr;?>;/*#684f8e*/
	
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon .dashicons,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon .glyphicon,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon .fa
{
	width:100%;
	height:100%;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
	margin:auto;
	<?php if($sb_set_same_icon_img_width=='yes')
	{
		?>
		width: <?php echo $sb_set_icon_size;?>px;
		height: <?php echo $sb_set_icon_size;?>px;
		<?php 
	}
	else
	{	
		if($sb_set_image_size<=125)
		{
		?>
		width: <?php echo $sb_set_image_size;?>px;
		height: <?php echo $sb_set_image_size;?>px;
		<?php 
		}
		else
		{
		?>
		width: 125px;
		height: 125px;
		<?php 
		}
	}?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content{
	line-height: 25px;
	padding: 0 1px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title{
	color: <?php echo $sb_set_title_clr;?>;
	font-size: <?php echo $sb_set_title_size;?>px;
	font-weight: 700;
	margin-bottom: 10px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_description{
	color: <?php echo $sb_set_des_clr;?>;
	font-size: <?php echo $sb_set_des_size;?>px;
	line-height:1.6;
	padding:0;
	margin-bottom: 15px;
	<?php echo $service_box_font_family;?>
	
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more{
	display: block;
	width: 100%;
	background: <?php echo $sb_set_link_bg_clr;?>;
	font-size: <?php echo $sb_set_link_size;?>px;
	font-weight: 600;
	color: <?php echo $sb_set_link_clr;?>;/*#eba133*/
	padding: 10px;
	border-left: 1px solid <?php echo $sb_set_bg_clr;?>;
	border-right: 1px solid <?php echo $sb_set_bg_clr;?>;
	-webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content .wpsm_read_more{
	border-color: <?php echo $sb_set_hover_bg_clr;?> !important;
	color: <?php echo $sb_set_hover_link_clr;?> !important;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more i{
	font-size: <?php echo $sb_set_link_size;?>px;
    color: <?php echo $sb_set_link_clr;?>;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content .wpsm_read_more i{
     color: <?php echo $sb_set_hover_link_clr;?> !important;
}
@media only screen and (max-width: 990px){
	.wpsm_serviceBox_<?php echo $PostId;?>{ margin-bottom: 30px; }
}
@media only screen and (max-width: 767px){
	.wpsm_serviceBox_<?php echo $PostId;?>:before,
	.wpsm_serviceBox_<?php echo $PostId;?>:after{
		top: 80px;
	}
}
@media only screen and (max-width: 480px){
	.wpsm_serviceBox_<?php echo $PostId;?>:before,
	.wpsm_serviceBox_<?php echo $PostId;?>:after{
		top: 140px;
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
			border-color: <?php echo $sb_all_contents_brdr_clr;?>;
			background: <?php echo $sb_all_contents_icon_bg_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:before,
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:after{
			background: <?php echo $var_sb_bg_clr;?>;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more,
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more i{
			color: <?php echo $sb_all_contents_link_clr;?>;
			background: <?php echo $sb_all_contents_link_bg_clr;?>;
		}
		
	<?php
	$k++;	
	}
}
?>				
</style>