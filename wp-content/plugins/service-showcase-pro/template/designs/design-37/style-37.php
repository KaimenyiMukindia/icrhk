<style>
<?php if($sb_set_image_size>85) 
		$sb_set_image_size=85;
	  if($sb_set_icon_size>85) 
		$sb_set_icon_size=85;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
	margin-top:20px;
	padding: 30px 0;
	display:inline-block;
	padding:10px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    background:<?php echo $sb_set_icon_bg_clr;?>;
    height: <?php echo $sb_set_icon_size*2;?>px;
    width: <?php echo $sb_set_icon_size*2;?>px;
    border-radius:50%;
    text-align: center;
    float: left;
	position:relative;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
   color: <?php echo $sb_set_icon_clr;?>;
   position:absolute;
   top:50%;
   left:50%;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content {
    margin-left: <?php echo ($sb_set_icon_size*2)+20;?>px;
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
	.wpsm_serviceBox_<?php echo $PostId;?> .img-wpsm_service_content{
		margin-left: <?php echo ($sb_set_image_size*2)+20;?>px;
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

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3{
    color: <?php echo $sb_set_title_clr;?>;
    font-size: <?php echo $sb_set_title_size;?>px;
    font-weight: 600;
    margin-top: 0;
	clear:none;
	margin-bottom:15px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p{
    color:<?php echo $sb_set_des_clr;?>;
	font-size:<?php echo $sb_set_des_size;?>px;
    margin-bottom:15px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more{
    color: <?php echo $sb_set_link_clr;?>;
    text-decoration: none;
	 font-size: <?php echo $sb_set_link_size;?>px;
    margin-left: 5px;
	-webkit-box-shadow:none;
	box-shadow:none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more i{
    color: <?php echo $sb_set_link_clr;?>;
    font-size: <?php echo $sb_set_link_size;?>px;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more:hover{
   	-webkit-box-shadow:none;
	box-shadow:none;
}
@media screen and (max-width: 480px){
    .wpsm_serviceBox_<?php echo $PostId;?>{
        margin-bottom: 30px;
		width:100%;
    }
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
		float:none;
		margin:auto;
	}
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content{
		margin:15px 0 0 0 ;
		text-align:center;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more,
        #wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more i{
			color: <?php echo $sb_all_contents_link_clr;?> !important;
		}
		
	<?php
	$k++;	
	}
}
?>
</style>