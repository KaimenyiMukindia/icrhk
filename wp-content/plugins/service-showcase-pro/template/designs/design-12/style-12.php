<style>
<?php if($sb_set_image_size>100) 
		$sb_set_image_size=100;
	  if($sb_set_icon_size>100) 
		$sb_set_icon_size=100;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    text-align: center;
    margin-top: 20px;
	padding:15px;
	background-color: <?php echo $sb_set_bg_clr; ?>;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
	position:relative;
    width: 150px;
    height: 150px;
    border-radius: 50%;
    display: inline-block;
    border: 1px solid <?php echo $sb_set_icon_brdr_clr;?>;
    margin-bottom: 20px;
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon{
    background: <?php echo $sb_set_hover_icon_bg_clr;?> !important;
	border: 1px solid <?php echo $sb_set_hover_icon_brdr_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
   transition: all 0.5s ease 0s;
   -ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
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
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
<?php if($sb_set_same_icon_img_width=='no')
	{
		?>
		width: <?php echo $sb_set_image_size;?>px;
		height: <?php echo $sb_set_image_size;?>px;
		margin-top: -<?php echo  intval($sb_set_image_size/2);?>px;
		margin-left: -<?php echo intval($sb_set_image_size/2);?>px; 
		<?php 
	}?>
}

.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon i,
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon img{
    transform: rotateY(360deg);
	-ms-transform: rotateY(360deg);
	-webkit-transform: rotateY(360deg);
	-moz-transform: rotateY(360deg);
	-o-transform: rotateY(360deg);
    color: <?php echo $sb_set_hover_icon_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title{
    font-size: <?php echo $sb_set_title_size;?>px;
    color:<?php echo $sb_set_title_clr;?>;
    margin: 0 0 15px 0;
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_title{
    color: <?php echo $sb_set_hover_title_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title:after{
    content: "\f00d";
    display: block;
    font-family: "fontawesome";
    font-size: 15px;
    margin-top: 14px;
    color: <?php echo $sb_set_icon_clr;?>;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_description{
    font-size: <?php echo $sb_set_des_size;?>px;
    color: <?php echo $sb_set_des_clr;?>;
    line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read{
    padding: 30px 0;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a{
    border: 4px solid <?php echo $sb_set_brdr_clr;?>;
    padding: 9px 25px !important;
    font-weight:600;
	font-size:<?php echo $sb_set_link_size;?>px;
    color: <?php echo $sb_set_link_clr;?> !important;
	background: <?php echo $sb_set_link_bg_clr;?>;
   	-webkit-box-shadow:none;
	box-shadow:none;
	border-radius:4px;
	text-transform:none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a i{
	color: <?php echo $sb_set_link_clr;?> !important;
	font-size:<?php echo $sb_set_link_size;?>px;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a:before,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a:after{
	content:"";
	position:relative;
	width:0;
	font-size:0;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a:hover{
    background: <?php echo $sb_set_hover_link_bg_clr;?> !important;
    color: <?php echo $sb_set_hover_link_clr;?> !important;
    text-decoration: none;
	padding: 9px 25px !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a:hover i{
	color: <?php echo $sb_set_hover_link_clr;?> !important;
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
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon i{
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_title,
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_title:after{
			color:<?php echo $sb_all_contents_title_clr;?>;
		}
		
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_description{
			color:<?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read a{
			border-color: <?php echo $sb_all_contents_brdr_clr;?>;
			color: <?php echo $sb_all_contents_link_clr;?> !important;
			background: <?php echo $sb_all_contents_link_bg_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read a i{
			color: <?php echo $sb_all_contents_link_clr;?> !important;
		}
		
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read a:hover{
			background: <?php echo $sb_set_hover_link_bg_clr;?> !important;
			color: <?php echo $sb_set_hover_link_clr;?> !important;
			text-decoration: none;
			padding: 9px 25px !important;
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