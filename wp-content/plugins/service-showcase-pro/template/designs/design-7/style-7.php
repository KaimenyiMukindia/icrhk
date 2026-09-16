<style>

.wpsm_serviceBox_<?php echo $PostId;?>{
    text-align: center;
    min-height: 320px;
	padding:20px;
	margin-bottom:15px;
	margin-top:15px;
	background:<?php echo $sb_set_bg_clr;?>;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    width: 50px;
    height: 86px;
    border-top: 3px solid <?php echo $sb_set_icon_brdr_clr;?>;
    border-bottom: 3px solid <?php echo $sb_set_icon_brdr_clr;?>;
    margin: 0 auto 25px;
    position: relative;
    transition: all 0.3s ease-in-out 0s;
	-ms-transition: all 0.3s ease-in-out 0s;
	-webkit-transition: all 0.3s ease-in-out 0s;
	-moz-transition: all 0.3s ease-in-out 0s;
	-o-transition: all 0.3s ease-in-out 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon:before{
    content: "";
    width: 36px;
    height: 36px;
    position: absolute;
    top: 22px;
    right: -18px;
    border-bottom: 3px solid <?php echo $sb_set_icon_brdr_clr;?>;
    border-right: 3px solid <?php echo $sb_set_icon_brdr_clr;?>;
    z-index: 1;
    transform: scaleY(1.7) rotate(-45deg);
	-ms-transform: scaleY(1.7) rotate(-45deg);
	-webkit-transform: scaleY(1.7) rotate(-45deg);
    transition: all 0.3s ease-in-out 0s;
	-ms-transition: all 0.3s ease-in-out 0s;
	-webkit-transition: all 0.3s ease-in-out 0s;
	-moz-transition: all 0.3s ease-in-out 0s;
	-o-transition: all 0.3s ease-in-out 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon:after{
    content: "";
    width: 36px;
    height: 36px;
    position: absolute;
    bottom: 22px;
    left: -18px;
    border-top: 3px solid <?php echo $sb_set_icon_brdr_clr;?>;
    border-left: 3px solid <?php echo $sb_set_icon_brdr_clr;?>;
    z-index: 1;
    transform: scaleY(1.7) rotate(-45deg);
	-ms-transform: scaleY(1.7) rotate(-45deg);
	-webkit-transform: scaleY(1.7) rotate(-45deg);
	
    transition: all 0.3s ease-in-out 0s;
	-ms-transition: all 0.3s ease-in-out 0s;
	-webkit-transition: all 0.3s ease-in-out 0s;
	-moz-transition: all 0.3s ease-in-out 0s;
	-o-transition: all 0.3s ease-in-out 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon,
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon:before,
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon:after{
    border-color: <?php echo $sb_set_hover_icon_brdr_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
    color: <?php echo $sb_set_icon_clr;?>;
 }

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
   position:absolute;
   top: 50%;
   left: 50%;	
   transition: all 0.3s ease-in-out 0s;
   -ms-transition: all 0.3s ease-in-out 0s;
   -webkit-transition: all 0.3s ease-in-out 0s;
    -moz-transition: all 0.3s ease-in-out 0s;
   -o-transition: all 0.3s ease-in-out 0s;
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
    margin-bottom: 20px;
    position: relative;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title:after{
    content: "";
    display: block;
    width: 0;
    height: 5px;
    margin: 0 auto;
    background: <?php echo $sb_set_hover_icon_brdr_clr;?> !important;
    transition: all 0.3s ease-in-out 0s;
	-ms-transition: all 0.3s ease-in-out 0s;
	-webkit-transition: all 0.3s ease-in-out 0s;
	-moz-transition: all 0.3s ease-in-out 0s;
	-o-transition: all 0.3s ease-in-out 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_title:after{
    width: 70px;
    margin: 15px auto 23px;
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
	 color: <?php echo $sb_set_link_clr;?>;
	 font-size:<?php echo $sb_set_link_size;?>px;
	-webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more i{
	 color: <?php echo $sb_set_link_clr;?>;
	 font-size:<?php echo $sb_set_link_size;?>px;
	 vertical-align:middle !important;
}	
@media only screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{ margin-bottom: 30px; }
}
@media only screen and (max-width: 767px){
    .wpsm_serviceBox_<?php echo $PostId;?>{ min-height: 300px; }
}
@media only screen and (max-width: 479px){
    .wpsm_serviceBox_<?php echo $PostId;?>{ min-height: 320px; }
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
			border-top: 3px solid <?php echo $sb_all_contents_icon_brdr_clr;?>;
			border-bottom: 3px solid <?php echo $sb_all_contents_icon_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon i{
			color: <?php echo $sb_all_contents_icon_clr;?>;
		 }
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon:before{
			border-bottom: 3px solid <?php echo $sb_all_contents_icon_brdr_clr;?>;
			border-right: 3px solid <?php echo $sb_all_contents_icon_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon:after{
			border-top: 3px solid <?php echo $sb_all_contents_icon_brdr_clr;?>;
			border-left: 3px solid <?php echo $sb_all_contents_icon_brdr_clr;?>;
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
		}
		
	<?php
	$k++;	
	}
}
?>		
</style>
