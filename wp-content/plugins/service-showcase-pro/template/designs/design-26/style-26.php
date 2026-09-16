<style>
<?php if($sb_set_image_size>80) 
		$sb_set_image_size=80;
	  if($sb_set_icon_size>80) 
		$sb_set_icon_size=80;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    text-align: center;
    margin-top: 50px;
	padding:20px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    border-left: 2px solid <?php echo $sb_set_icon_brdr_clr;?>;
    border-right: 2px solid <?php echo $sb_set_icon_brdr_clr;?>;
    width: 116px;
    height: 67px;
    margin: 0 auto;
	transform: rotate(0.0001deg);
	-ms-transform: rotate(0.0001deg);
	-webkit-transform: rotate(0.0001deg);
	-moz-transform: rotate(0.0001deg);
	-o-transform:rotate(0.0001deg);
    position: relative;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon:after,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon:before{
    content: "";
    width: 82px;
    height: 82px;
    border-bottom: 2.8284px solid <?php echo $sb_set_icon_brdr_clr;?>;
    border-left: 2.8284px solid <?php echo $sb_set_icon_brdr_clr;?>;
    position: absolute;
    left: 14.9878px;
    bottom: -41.0122px;
    transform: scaleY(0.5774) rotate(-45deg);
	-ms-transform: scaleY(0.5774) rotate(-45deg);
	-webkit-transform: scaleY(0.5774) rotate(-45deg);
	-moz-transform: scaleY(0.5774) rotate(-45deg);
	-o-transform: scaleY(0.5774) rotate(-45deg);
    z-index: 1;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon:before{
    border: 0px none;
    border-right: 2.8284px solid <?php echo $sb_set_icon_brdr_clr;?>;
    border-top: 2.8284px solid <?php echo $sb_set_icon_brdr_clr;?>;
    top: -41.0122px;
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
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
		max-width:none;
		width: <?php echo $sb_set_image_size;?>px;
		height: <?php echo $sb_set_image_size;?>px;
		margin-top: -<?php echo  intval($sb_set_image_size/2);?>px;
		margin-left: -<?php echo intval($sb_set_image_size/2);?>px; 
				
	}
<?php 
} ?>
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3.wpsm_title{
    font-size: <?php echo $sb_set_title_size;?>px;
    color:<?php echo $sb_set_title_clr;?>;
    margin-top: 60px;
    margin-bottom:15px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3:after{
    content: "";
    border:1px solid <?php echo $sb_set_icon_brdr_clr;?>;
    width: 40px;
    display: block;
    margin: 20px auto;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p{
    font-size: <?php echo $sb_set_des_size;?>px;
	color:<?php echo $sb_set_des_clr;?>;
	margin-bottom:15px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more{
	font-size: <?php echo $sb_set_link_size;?>px;
	color:<?php echo $sb_set_link_clr;?>;
	-webkit-box-shadow:none;
	box-shadow:none;	
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more i{
	font-size: <?php echo $sb_set_link_size;?>px;
	color:<?php echo $sb_set_link_clr;?>;
	vertical-align:middle !important;
}
@media screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{
        margin-bottom:20px;
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
		
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon {
			 border-left-color: <?php echo $sb_all_contents_icon_brdr_clr;?>;
			 border-right-color: <?php echo $sb_all_contents_icon_brdr_clr;?>;
		}
		
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon:after{
			border-bottom-color: <?php echo $sb_all_contents_icon_brdr_clr;?>;
			border-left-color: <?php echo $sb_all_contents_icon_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon:before{
			border-right-color:<?php echo $sb_all_contents_icon_brdr_clr;?>;
			border-top-color:<?php echo $sb_all_contents_icon_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon i{
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_title{
			color:<?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_title:after{
			border-color:<?php echo $sb_all_contents_icon_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_description{
			color:<?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more,
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more i{
			color: <?php echo $sb_all_contents_link_clr;?> ;
		}
		
	<?php
	$k++;	
	}
}
?>
</style>