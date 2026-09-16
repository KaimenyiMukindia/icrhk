<style>
<?php if($sb_set_image_size>130) 
		$sb_set_image_size=130;
	  if($sb_set_icon_size>130) 
		$sb_set_icon_size=130;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    text-align:center;
    background-color: <?php echo $sb_set_bg_clr;?>;
    padding: 25px;
    color: <?php echo $sb_set_title_clr;?>;
	<?php echo $service_box_font_family;?>
	border:<?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_bg_clr;?>;
	margin-top: 20px;
    position:relative;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover{
    background-color: <?php echo $sb_set_hover_bg_clr;?> !important;
	border-color:<?php echo $sb_set_hover_brdr_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
	margin-bottom:15px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
    font-size: <?php echo $sb_set_icon_size;?>px;
	color:<?php echo $sb_set_icon_clr;?>;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
	margin:auto auto;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i
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
		max-width:none;
		width: <?php echo $sb_set_image_size;?>px;
		height: <?php echo $sb_set_image_size;?>px;
	}
<?php 
} ?>

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3{
    margin: 7px 0 15px 0;
    text-transform:capitalize;
	font-size: <?php echo $sb_set_title_size;?>px;
	color:<?php echo $sb_set_title_clr;?>;
	<?php echo $service_box_font_family;?>
	line-height: 1.6;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p{
    margin-bottom:18px;
    line-height:1.6;
	font-size: <?php echo $sb_set_des_size;?>px;
	color:<?php echo $sb_set_des_clr;?>;
	<?php echo $service_box_font_family;?>
}

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read a{
	background-color: <?php echo $sb_set_link_bg_clr;?> !important;
	border-color:<?php echo $sb_set_link_bg_clr;?> !important;
	color:<?php echo $sb_set_link_clr;?> !important;
	font-size:<?php echo $sb_set_link_size;?>px !important;
	padding:7px 15px !important;
    -webkit-box-shadow:none;
	box-shadow:none;
	border-radius:4px;
	letter-spacing:0;
	text-transform:none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read a i{
	color:<?php echo $sb_set_link_clr;?> !important;
	font-size:<?php echo $sb_set_link_size;?>px !important;
	vertical-align: middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read a:before,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read a:after{
	content:"";
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content .wpsm_read a{
    background-color: <?php echo $sb_set_hover_link_bg_clr;?> !important;
	border-color:<?php echo $sb_set_hover_link_bg_clr;?> !important;
	color:<?php echo $sb_set_hover_link_clr;?> !important;
	-webkit-box-shadow:none;
	box-shadow:none;
	text-transform:none;
	letter-spacing:0;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content .wpsm_read a i{
    color:<?php echo $sb_set_hover_link_clr;?> !important;
	vertical-align: middle !important;
}
@media only screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{
        margin-bottom: 25px;
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
			color: <?php echo $sb_all_contents_title_clr;?>;
			border-color:<?php echo $sb_all_contents_bg_clr;?>;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read a{
			background-color: <?php echo $sb_all_contents_link_bg_clr;?> !important;
			border-color:<?php echo $sb_all_contents_link_bg_clr;?> !important;
			color:<?php echo $sb_all_contents_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read a i{
			color:<?php echo $sb_all_contents_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:hover .wpsm_service_content .wpsm_read a{
			background-color: <?php echo $sb_set_hover_link_bg_clr;?> !important;
			border-color:<?php echo $sb_set_hover_link_bg_clr;?> !important;
			color:<?php echo $sb_set_hover_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:hover .wpsm_service_content .wpsm_read a i{
			color:<?php echo $sb_set_hover_link_clr;?> !important;
		}
	<?php
	$k++;	
	}
}
?>
</style>