<style>
<?php if($sb_set_image_size>54) 
		$sb_set_image_size=54;
	  if($sb_set_icon_size>54) 
		$sb_set_icon_size=54;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    border: <?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_brdr_clr;?>;
    border-radius: 10px;
    margin-top:50px;
    padding: 25px 20px;
    position: relative;
	background-color: <?php echo $sb_set_bg_clr; ?>;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    width: 60px;
    height: 60px;
    border-radius: 5px;
    background: <?php echo $sb_set_icon_bg_clr;?>;
    color: <?php echo $sb_set_icon_clr;?>;
	position: absolute;
    top: -30px;
    right: 40px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
   position:absolute;
   top:50%;
   left:50%;
   color: <?php echo $sb_set_icon_clr;?>;
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
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon:before{
    content: "";
    width: 100%;
    height: 100%;
    background: #fff;
    position: absolute;
    top: -20px;
    left: -45px;
    opacity: 0.1;
    transform: rotate(137deg) scale(1.5) skew(29deg) translate(-1px);
	-ms-transform: rotate(137deg) scale(1.5) skew(29deg) translate(-1px);
	-webkit-transform: rotate(137deg) scale(1.5) skew(29deg) translate(-1px);
	-moz-transform: rotate(137deg) scale(1.5) skew(29deg) translate(-1px);
	-o-transform: rotrotate(137deg) scale(1.5) skew(29deg) translate(-1px);
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon:after{
    content: "";
    border-top: 11px solid <?php echo $sb_set_icon_bg_clr;?>;
    border-right: 11px solid transparent;
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -4px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title{
    font-size: <?php echo $sb_set_title_size;?>px;
    color: <?php echo $sb_set_title_clr;?>;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_description{
    font-size: <?php echo $sb_set_des_size;?>px;
    color: <?php echo $sb_set_des_clr;?>;
    line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more{
    font-size: <?php echo $sb_set_link_size;?>px;
	color: <?php echo $sb_set_link_clr;?>;
	-webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more i{
    font-size: <?php echo $sb_set_link_size;?>px;
	color: <?php echo $sb_set_link_clr;?>;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> a:hover{
	-webkit-box-shadow:none;
	box-shadow:none;
	color: <?php echo $sb_set_hover_link_clr;?> !important;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> a:hover i{
	color: <?php echo $sb_set_hover_link_clr;?> !important;
}
@media only screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{ margin-bottom: 30px; }
}
@media only screen and (max-width: 767px){
    .wpsm_serviceBox_<?php echo $PostId;?>{ margin-bottom: 40px; }
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
			border-color: <?php echo $sb_all_contents_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon{
			background: <?php echo $sb_all_contents_icon_bg_clr;?>;
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon i{
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon:after{
			border-top-color: <?php echo $sb_all_contents_icon_bg_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_title{
			color:<?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_description{
			color:<?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read_more,
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read_more i{
			color: <?php echo $sb_all_contents_link_clr;?>;
		}
		
	<?php
	$k++;	
	}
}
?>			
</style>