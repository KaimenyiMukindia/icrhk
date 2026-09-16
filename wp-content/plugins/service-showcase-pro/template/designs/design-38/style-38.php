<style>
<?php if($sb_set_image_size>85) 
		$sb_set_image_size=85;
	  if($sb_set_icon_size>85) 
		$sb_set_icon_size=85;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    text-align: center;
	background:<?php echo $sb_set_bg_clr;?>;
	padding:20px 15px;
	margin:20px 0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
	border-radius:50%;
    width: <?php echo $sb_set_icon_size*2;?>px;
    height: <?php echo $sb_set_icon_size*2;?>px;
    line-height: <?php echo $sb_set_icon_size*2;?>px;
    border: <?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_icon_brdr_clr;?>;
    margin: 0 auto 30px;
    position: relative;
    z-index: 1;
	transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
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
	.wpsm_serviceBox_<?php echo $PostId;?> .img-wpsm_service_icon{
		height: <?php echo $sb_set_image_size*2;?>px;
		width: <?php echo $sb_set_image_size*2;?>px;
	}
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
		max-width:none;
		width: <?php echo $sb_set_image_size;?>px;
		height: <?php echo $sb_set_image_size;?>px;
		margin-top: -<?php echo  intval($sb_set_image_size/2);?>px;
		margin-left: -<?php echo intval($sb_set_image_size/2);?>px; 
					
	}
<?php 
} ?>
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon{
    border-color: <?php echo $sb_set_hover_icon_brdr_clr;?>;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon i{
    color: <?php echo $sb_set_hover_icon_clr;?>;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon:after{
	border-radius:50%;
    content: "";
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    background: <?php echo $sb_set_hover_icon_bg_clr;?>;
    z-index: -1;
    transform: scale(0);
	-ms-transform: scale(0);
	-webkit-transform: scale(0);
	-moz-transform: scale(0);
	-o-transform: scale(0);
	transition: all 0.3s ease 0s;
	-ms-transition: all 0.3s ease 0s;
	-webkit-transition: all 0.3s ease 0s;
	-moz-transition: all 0.3s ease 0s;
	-o-transition: all 0.3s ease 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon:after{
    transform: scale(1);
	-ms-transform: scale(1);
	-webkit-transform: scale(1);
	-moz-transform: scale(1);
	-o-transform: scale(1);
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title{
    font-size: <?php echo $sb_set_title_size;?>px;
    color: <?php echo $sb_set_title_clr;?>;
    margin: 0 0 10px 0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_description{
    font-size: <?php echo $sb_set_des_size;?>px;
    color :<?php echo $sb_set_des_clr;?>;
    line-height: 1.6;
    margin: 0 0 30px 0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more{
    display: inline-block;
    padding: 10px 30px;
    border: <?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_link_bg_clr;?>;
    font-weight: 600;
    color: <?php echo $sb_set_link_clr;?>;
	font-size:<?php echo $sb_set_link_size;?>px;
	position: relative;
    z-index: 1;
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more i{
    font-weight: 600;
    color: <?php echo $sb_set_link_clr;?>;
	font-size:<?php echo $sb_set_link_size;?>px;
	transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content .wpsm_read_more{
    color: <?php echo $sb_set_hover_link_clr;?>;
	border-color: <?php echo $sb_set_hover_link_clr;?>;
	-webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content .wpsm_read_more i{
    color: <?php echo $sb_set_hover_link_clr;?>;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more:after{
    content: "";
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    background: <?php echo $sb_set_link_bg_clr;?>;
    transform: scale(1);
	-ms-transform: scale(1);
	-webkit-transform: scale(1);
	-moz-transform: scale(1);
	-o-transform: scale(1);
	z-index: -1;
    transition: all 0.3s ease 0s;
	-ms-transition: all 0.3s ease 0s;
	-webkit-transition: all 0.3s ease 0s;
	-moz-transition: all 0.3s ease 0s;
	-o-transition: all 0.3s ease 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_read_more:after{
    transform: scale(0);
	-ms-transform: scale(0);
	-webkit-transform: scale(0);
	-moz-transform: scale(0);
	-o-transform: scale(0);
}
@media only screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{ margin-bottom: 30px; }
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon{
			border-color: <?php echo $sb_all_contents_icon_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon i{
		   color: <?php echo $sb_all_contents_icon_clr;?>;
		 }
		
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_title{
			color: <?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_description{
			color :<?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more{
			border-color: <?php echo $sb_all_contents_link_bg_clr;?>;
			color: <?php echo $sb_all_contents_link_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more i{
			color: <?php echo $sb_all_contents_link_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more:after{
			background: <?php echo $sb_all_contents_link_bg_clr;?>;
		}
		
	<?php
	$k++;	
	}
}
?>	
</style>