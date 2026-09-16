<style>
<?php if($sb_set_image_size>50) 
		$sb_set_image_size=50;
	  if($sb_set_icon_size>50) 
		$sb_set_icon_size=50;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    text-align: center;
	margin-top:20px;
	background: <?php echo $sb_set_bg_clr;?>;
	<?php echo $service_box_font_family;?>
	padding:20px 15px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    width:100px;
    height:100px;
    line-height: 100px;
    font-size: <?php echo $sb_set_icon_size;?>px;
    border: 1px solid <?php echo $sb_set_icon_brdr_clr;?>;
    border-radius: 50%;
    margin:0 auto 45px;
    color:<?php echo $sb_set_icon_clr;?>;
	position:relative;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
   color: <?php echo $sb_set_icon_clr;?>;
   position:absolute;
   top:50%;
   left:50%;
   transition: all 0.3s ease 0s;
   -ms-transition:all 0.3s ease 0s;
	-webkit-transition:all 0.3s ease 0s;
	-moz-transition:all 0.3s ease 0s;
	-o-transition:all 0.3s ease 0s;
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
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon > i,
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon > img{
    transform:scale(1.3);
	-ms-transform:scale(1.3);
	-webkit-transform:scale(1.3);
	-moz-transform:scale(1.3);
	-o-transform:scale(1.3);
	
    transition: all 0.3s ease-out 0s;
	-ms-transition: all 0.3s ease-out 0s;
	-webkit-transition: all 0.3s ease-out 0s;
	-moz-transition: all 0.3s ease-out 0s;
	-o-transition: all 0.3s ease-out 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3.wpsm_title{
    font-size: <?php echo $sb_set_title_size;?>px;
    font-weight:400;
    margin-bottom: 20px;
    color:<?php echo $sb_set_title_clr;?>;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p{
    color: <?php echo $sb_set_des_clr;?>;
	font-size:<?php echo $sb_set_des_size;?>px;
    line-height: 1.6;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read{
    padding: 30px 0 10px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a{
    background: <?php echo $sb_set_link_bg_clr;?>;
    padding: 15px 30px;
    font-weight:600;
	font-size:<?php echo $sb_set_link_size;?>px;
	line-height:<?php echo $sb_set_link_size+3;?>px;
    color: <?php echo $sb_set_link_clr;?> !important;
	-webkit-box-shadow:none;
	box-shadow:none;
	display:inline-block;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a i{
    font-weight:600;
	font-size:<?php echo $sb_set_link_size;?>px;
	color: <?php echo $sb_set_link_clr;?> !important;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a:hover{
    background: <?php echo $sb_set_hover_link_bg_clr;?> !important;
    color: <?php echo $sb_set_hover_link_clr;?> !important;
    text-decoration: none;
	-webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a:hover i{
    color: <?php echo $sb_set_hover_link_clr;?> !important;
}
@media screen and (max-width: 990px){
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
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon {
			border-color: <?php echo $sb_all_contents_icon_brdr_clr;?>;
			color: <?php echo $sb_all_contents_icon_clr;?>;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read a{
			background: <?php echo $sb_all_contents_link_bg_clr;?> !important;
			color: <?php echo $sb_all_contents_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read a i{
			color: <?php echo $sb_all_contents_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read a:hover{
			background: <?php echo $sb_set_hover_link_bg_clr;?> !important;
			color: <?php echo $sb_set_hover_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read a:hover i{
			color: <?php echo $sb_set_hover_link_clr;?> !important;
		}
	<?php
	$k++;	
	}
}
?>
</style>