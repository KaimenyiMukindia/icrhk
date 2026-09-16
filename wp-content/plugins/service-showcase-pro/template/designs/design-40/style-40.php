<style>
<?php if($sb_set_image_size>85) 
		$sb_set_image_size=85;
	  if($sb_set_icon_size>85) 
		$sb_set_icon_size=85;
	 if($sb_set_brdr_size>2)
	 {
		 $sb_set_image_size-=$sb_set_brdr_size;
		 $sb_set_icon_size-=$sb_set_brdr_size;
	 }
?>
.wpsm_serviceBox_<?php echo $PostId;?> {
    background: <?php echo $sb_set_bg_clr;?>;
    text-align: center;
    padding: 0 0 25px;
    box-shadow: 0 2px 5px 0 #888282;
    margin-top: 130px;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover {
    background: <?php echo $sb_set_hover_bg_clr;?> !important;
    border-color: <?php echo $sb_set_hover_brdr_clr;?> !important;
	color:<?php echo $sb_set_hover_title_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon i, .wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon img{
    transform: rotate(360deg);
	-ms-transform:rotate(360deg);
	-webkit-transform:rotate(360deg);
	-moz-transform:rotate(360deg);
	-o-transform:rotate(360deg);
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content h3{
    color:<?php echo $sb_set_hover_title_clr;?> !important;
}

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
	color: <?php echo $sb_set_icon_clr;?>;
	position:relative;
	top:-50px;
    width: 130px;
    height: 130px;
    line-height: 146px;
    border-radius: 50%;
    background: <?php echo $sb_set_icon_bg_clr;?>;
    border: <?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_icon_brdr_clr;?>;
    margin: -65px auto 0;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
   color: <?php echo $sb_set_icon_clr;?>;
   position:absolute;
   top:50%;
   left:50%;
   transition: all 0.3s ease-in-out 0s;
   -ms-transition: all 0.3s ease-in-out 0s;
	-webkit-transition: all 0.3s ease-in-out 0s;
	-moz-transition: all 0.3s ease-in-out 0s;
	-o-transition: all 0.3s ease-in-out 0s;
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
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content{
    padding: 0 25px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3.wpsm_title{
    color: <?php echo $sb_set_title_clr;?>;
    /*font: bold 24px/40px 'arial';*/
	font-weight:bold;
	font-size:<?php echo $sb_set_title_size;?>px;
    line-height:<?php echo $sb_set_title_size+10;?>px;
	margin: 30px 0 10px;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p{
    color: <?php echo $sb_set_des_clr;?>;
	font-weight:lighter;
	font-size:<?php echo $sb_set_des_size;?>px;
    line-height:1.6;
    margin: 0 0 20px;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content p{
    color:<?php echo $sb_set_hover_des_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read{
    padding: 30px 0;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a{
    background: <?php echo $sb_set_link_bg_clr;?>;
    padding: 15px 32px;
    /*font: 600 12px/18px "arial";*/
	font-weight:600;
	font-size:<?php echo $sb_set_link_size;?>px;
    color: <?php echo $sb_set_link_clr;?>;
	line-height:<?php echo $sb_set_link_size+3;?>px;
    -webkit-box-shadow:none;
	box-shadow:none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a i{
    font-weight:600;
	font-size:<?php echo $sb_set_link_size;?>px;
    color: <?php echo $sb_set_link_clr;?>;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a:hover{
    background: <?php echo $sb_set_hover_link_bg_clr;?> !important;
    color: <?php echo $sb_set_hover_link_clr;?> !important;
    text-decoration: none;
	webkit-box-shadow:none;
	box-shadow:none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a:hover i{
    color: <?php echo $sb_set_hover_link_clr;?> !important;
 }
@media screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{  margin-top: 150px; }
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
			color: <?php echo $sb_all_contents_icon_clr;?>;
			background: <?php echo $sb_all_contents_icon_bg_clr;?>;
			border-color: <?php echo $sb_all_contents_icon_brdr_clr;?>;
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
			background: <?php echo $sb_all_contents_link_bg_clr;?>;
			color: <?php echo $sb_all_contents_link_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read a i{
			color: <?php echo $sb_all_contents_link_clr;?>;
		}
		
	<?php
	$k++;	
	}
}
?>
</style>