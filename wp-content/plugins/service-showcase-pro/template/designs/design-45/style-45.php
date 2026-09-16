<style>
<?php if($sb_set_image_size>66) 
		$sb_set_image_size=66;
	  if($sb_set_icon_size>66) 
		$sb_set_icon_size=66;
?>
.wpsm_sb_wrapper_<?php echo $PostId;?>{
	overflow:visible !important;
}
.wpsm_serviceBox_<?php echo $PostId;?>{
	position:absolute;
	padding-top:85px;
	top:0;
	left:0;
	right:0;
	bottom:0;
    background-color:<?php echo $sb_set_bg_clr;?>;
	text-align:center;
    /* display:inline-block; */
    border:0;
    position: relative;
    -webkit-transition: all 200ms ease-in;
    -webkit-transform: scale(1); 
    
	transition: all 200ms ease-in;
    transform: scale(1); 
	text-align: center;
	margin: 10px -15px;
	padding: 50px 30px;
    color: <?php echo $sb_set_des_clr;?>;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    width: 100px;
    height: 100px;
    line-height: 100px;
    border-radius: 50%;
    border: 1px solid <?php echo $sb_set_icon_brdr_clr;?>;
    display: inline-block;
    margin-bottom: 30px;
    transition: all 0.5s ease 0s;
	position:relative;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img, 
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
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
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon{
    box-shadow: 0 0 0 15px <?php echo $sb_set_icon_brdr_clr;?>;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title{
    font-size: <?php echo $sb_set_title_size;?>px;
	color:<?php echo $sb_set_title_clr;?>;
    font-weight: 900;
    margin: 0 0 15px;
	/* text-transform: uppercase; */
	<?php echo $service_box_font_family;?>
	line-height:1.6;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_description{
    font-size: <?php echo $sb_set_des_size;?>px;
    line-height: 1.6;
    margin-bottom: 15px;
	color:<?php echo $sb_set_des_clr;?>;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more{
    font-weight:600;
	font-size:<?php echo $sb_set_link_size;?>px;
    color: <?php echo $sb_set_link_clr;?> !important;
	line-height:30px;
	-webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more i{
    font-weight:600;
	font-size:<?php echo $sb_set_link_size;?>px;
    color: <?php echo $sb_set_link_clr;?> !important;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more:hover{
    -webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover
{
    box-shadow: 0px 0px 150px #000000;
    z-index: 2;
    -webkit-transition: all 200ms ease-in;
    -webkit-transform: scale(1.2);
    
	transition: all 200ms ease-in;
    transform: scale(1.2);
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
			color: <?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon {
			border-color: <?php echo $sb_all_contents_icon_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:hover .wpsm_service_icon{
			box-shadow: 0 0 0 15px <?php echo $sb_all_contents_icon_brdr_clr;?>;
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
			color: <?php echo $sb_all_contents_link_clr;?> !important;
		}
	<?php
	$k++;	
	}
}
?>
</style>