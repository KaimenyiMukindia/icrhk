<style>
.wpsm_serviceBox_<?php echo $PostId;?>{
    text-align: center;
    margin-top: 40px;
    padding: 0 15px 20px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    width: 80px;
    height: 80px;
    line-height: 80px;
    display: inline-block;
    color: <?php echo $sb_set_icon_clr;?>;
    border: 1px solid <?php echo $sb_set_icon_brdr_clr;?>;
    margin-bottom: 35px;
    position: relative;
    transform: rotate(-45deg);
	-ms-transform: rotate(-45deg);
	-webkit-transform: rotate(-45deg);
	-moz-transform: rotate(-45deg);
	-o-transform: rotate(-45deg);
	
    transition: all 0.3s ease 0s;
	-ms-transition: all 0.3s ease 0s;
	-webkit-transition: all 0.3s ease 0s;
	-moz-transition: all 0.3s ease 0s;
	-o-transition: all 0.3s ease 0s;
	
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon{
    background: <?php echo $sb_set_hover_icon_bg_clr;?> !important;
    border-color: <?php echo $sb_set_hover_icon_brdr_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon:after{
    content: "";
    width: 100%;
    height: 100%;
    box-shadow: 0 0 0 3px <?php echo $sb_set_hover_icon_brdr_clr;?> !important;
    position: absolute;
    top: -5px;
    left: -5px;
    opacity: 0;
    padding: 5px;
    transform: scale(1.2);
	-ms-transform: scale(1.2);
	-webkit-transform: scale(1.2);
	-moz-transform: scale(1.2);
	-o-transform: scale(1.2);
	
    box-sizing: content-box;
    transition: all 0.2s ease 0s;
	-ms-transition: all 0.2s ease 0s;
	-webkit-transition: all 0.2s ease 0s;
	-moz-transition: all 0.2s ease 0s;
	-o-transition: all 0.2s ease 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon:after{
    transform: scale(1);
	-ms-transform: scale(1);
	-webkit-transform: scale(1);
	-moz-transform: scale(1);
	-o-transform: scale(1);
    opacity: 1;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
   transform: rotate(45deg);
   -ms-transform: rotate(45deg);
	-webkit-transform: rotate(45deg);
	-moz-transform: rotate(45deg);
	-o-transform: rotate(45deg);
   
   color: <?php echo $sb_set_icon_clr;?>;
   position:absolute;
   top:50%;
   left:50%;
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
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon i{
	 color: <?php echo $sb_set_hover_icon_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title{
    font-size: <?php echo $sb_set_title_size;?>px;
    color: <?php echo $sb_set_title_clr;?>;
    margin: 0 0 15px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_description{
    font-size: <?php echo $sb_set_des_size;?>px;
    color: <?php echo $sb_set_des_clr;?>;
	margin: 0 0 15px;
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
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more:hover{
    -webkit-box-shadow:none;
	box-shadow:none;
	color: <?php echo $sb_set_hover_link_clr;?> !important;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more i{
    font-size: <?php echo $sb_set_link_size;?>px;
	color: <?php echo $sb_set_link_clr;?>;
	vertical-align:middle !important;
 }
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more:hover i{
    color: <?php echo $sb_set_hover_link_clr;?> !important;
}
@media only screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{ margin-bottom: 20px; }
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon{
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