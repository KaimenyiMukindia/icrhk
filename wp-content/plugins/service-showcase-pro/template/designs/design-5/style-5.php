<style>
.wpsm_serviceBox_<?php echo $PostId;?>{
    text-align: center;
	padding:8px 8px 25px 8px;
	background:<?php echo $sb_set_bg_clr;?>;
	margin-bottom:30px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    width: 90px;
    height: 90px;
    color: <?php echo $sb_set_icon_clr;?>;
    margin: 20px auto;
    position: relative;
	border:1px solid rgba(255,255,255,0);
}

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
   position:absolute;
   top: 50%;
   left: 50%;
   color: <?php echo $sb_set_icon_clr;?>;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i
<?php if($sb_set_same_icon_img_width=='yes'){?>,.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img<?php }?>
{
	<?php if($sb_set_icon_size<=50)
			{
				?>
				width: <?php echo $sb_set_icon_size;?>px;
				height: <?php echo $sb_set_icon_size;?>px;
				margin-top: -<?php echo  intval($sb_set_icon_size/2);?>px;
				margin-left: -<?php echo intval($sb_set_icon_size/2);?>px; 
				font-size: <?php echo $sb_set_icon_size;?>px;
				<?php 
			}
			else
			{
				?>
				width: 50px;
				height: 50px;
				margin-top: -25px;
				margin-left: -25px; 
				font-size: 50px;
				<?php 
			}
		?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
   	<?php if($sb_set_same_icon_img_width=='no')
	{
		if($sb_set_image_size<=50)
			{
				?>
				width: <?php echo $sb_set_image_size;?>px;
				height: <?php echo $sb_set_image_size;?>px;
				margin-top: -<?php echo  intval($sb_set_image_size/2);?>px;
				margin-left: -<?php echo intval($sb_set_image_size/2);?>px; 
				<?php 
			}
			else
			{
				?>
				width: 50px;
				height: 50px;
				margin-top: -25px;
				margin-left: -25px; 
				<?php 
			}
	}?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon:before,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon:after{
    content: "";
    width: 100%;
    height: 100%;
    border-radius: 50%;
    position: absolute;
    top: 0;
    left: -2px;
    transition: all 0.33s ease-out 0s;
	-ms-transition: all 0.33s ease-out 0s;
	-webkit-transition: all 0.33s ease-out 0s;
	-moz-transition: all 0.33s ease-out 0s;
	-o-transition: all 0.33s ease-out 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon:before{
    border: 2px solid <?php echo $sb_set_icon_brdr_clr;?>;/*#00a79c*/
    top: -4px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon:after{
    border: 2px solid <?php echo $sb_set_brdr_clr;?>;/*#ff794a*/
    top: 4px;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon:before{
    top: 4px;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon:after{
    top: -4px;
}


.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title{
    font-size: <?php echo $sb_set_title_size;?>px;
    font-weight: bold;
    color: <?php echo $sb_set_title_clr;?>;
    margin-bottom: 15px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_description{
    font-size: <?php echo $sb_set_des_size;?>px;
    color: <?php echo $sb_set_des_clr;?>;
	margin-bottom: 15px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more{
    display: inline-block;
    padding: 10px 30px;
    border: 1px solid <?php echo $sb_set_link_bg_clr;?>;
    font-weight: 600;
    color: <?php echo $sb_set_link_clr;?>;
	font-size: <?php echo $sb_set_link_size;?>px;
    position: relative;
    z-index: 1;
	-webkit-box-shadow:none;
	box-shadow:none;
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
	text-decoration: none;
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
    z-index: -1;
    transition: all 0.3s ease 0s;
	-ms-transition: all 0.3s ease 0s;
	-webkit-transition: all 0.3s ease 0s;
	-moz-transition: all 0.3s ease 0s;
	-o-transition: all 0.3s ease 0s;
}

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more i{
	font-size: <?php echo $sb_set_link_size;?>px;
    color: <?php echo $sb_set_link_clr;?>;
	transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
	vertical-align:middle !important;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon,
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon i{
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon:before{
			border: 2px solid <?php echo $sb_all_contents_icon_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon:after{
			border: 2px solid <?php echo $sb_all_contents_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_title{
			color:<?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_description{
			color:<?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more{
			border: 1px solid <?php echo $sb_all_contents_link_bg_clr;?>;
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
		