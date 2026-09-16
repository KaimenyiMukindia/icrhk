<style>
<?php if($sb_set_image_size>30) 
		$sb_set_image_size=30;
	  if($sb_set_icon_size>30) 
		$sb_set_icon_size=30;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    border: <?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_brdr_clr;?>;
    box-shadow: 5px 5px 0 rgba(<?php echo HextoR($sb_set_hover_brdr_clr).",".HextoG($sb_set_hover_brdr_clr).",".HextoB($sb_set_hover_brdr_clr);?>, 0.6);
    padding: 20px;
	background:<?php echo $sb_set_bg_clr;?> none repeat scroll 0 0;
	margin-top:20px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3{
    margin: <?php echo 35-$sb_set_title_size;?>px 0 30px 80px;
    color: <?php echo $sb_set_title_clr;?>;
    font-size: <?php echo $sb_set_title_size;?>px;
    font-weight: 600;
	clear:none;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
	float: left;
	background: <?php echo $sb_set_icon_bg_clr;?>;
    box-shadow: 5px 5px 0 hsla(0, 0%, 0%, 0.1);
	color: <?php echo $sb_set_icon_clr;?>;
    font-size: <?php echo $sb_set_icon_size;?>px;
    font-weight: 600;
    height: <?php echo $sb_set_icon_size*2;?>px;
    text-align: center;
    width: <?php echo $sb_set_icon_size*2;?>px;
    transition:all 0.2s ease-in-out;
	-ms-transition:all 0.2s ease-in-out;
	-webkit-transition: all 0.2s ease-in-out;
	-moz-transition:all 0.2s ease-in-out;
	-o-transition:all 0.2s ease-in-out;
	position:relative;
	margin-top:<?php echo 30-$sb_set_icon_size;?>px;
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
		width:<?php echo $sb_set_image_size*2;?>px;
		height:<?php echo $sb_set_image_size*2;?>px;
		margin-top:<?php echo 30-$sb_set_image_size;?>px;
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
    background: <?php echo $sb_set_hover_icon_bg_clr;?> none repeat scroll 0 0 !important;
    border-radius: 50%;
    transform: translate(0px, 3px);
	-ms-transform: translate(0px, 3px);
	-webkit-transform: translate(0px, 3px);
	-moz-transform: translate(0px, 3px);
	-o-transform: translate(0px, 3px);
	transition:all 0.5s ease-in-out;
	-ms-transition:all 0.5s ease-in-out;
	-webkit-transition:all 0.5s ease-in-out;
	-moz-transition:all 0.5s ease-in-out;
	-o-transition:all 0.5s ease-in-out;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon i{
	color: <?php echo $sb_set_hover_icon_clr;?> !important;
}

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p{
    font-size: <?php echo $sb_set_des_size;?>px;
    line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
    margin-top: 20px;
	color:<?php echo $sb_set_des_clr;?>;
	margin-bottom:0;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a{
    font-size: <?php echo $sb_set_link_size;?>px;
    margin-top: 10px;
	color:<?php echo $sb_set_link_clr;?>;
	-webkit-box-shadow:none;
	box-shadow:none;
	margin-bottom:15px;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a i{
    font-size: <?php echo $sb_set_link_size;?>px;
    color:<?php echo $sb_set_link_clr;?>;
	vertical-align:middle !important;
}
@media only screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{
        margin-bottom: 20px;
    }
}
@media only screen and (max-width: 480px){
    .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
        float:none;
    }
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3{
		margin: 15px auto;
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
			border-color: <?php echo $sb_all_contents_brdr_clr;?>;
			box-shadow: 5px 5px 0 rgba(<?php echo HextoR($sb_all_contents_bg_clr).",".HextoG($sb_all_contents_bg_clr).",".HextoB($sb_all_contents_bg_clr);?>, 0.6);
		}
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content a,
        #wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content a i{
			color: <?php echo $sb_all_contents_link_clr;?> !important;
		}
		
	<?php
	$k++;	
	}
}
?>
</style>