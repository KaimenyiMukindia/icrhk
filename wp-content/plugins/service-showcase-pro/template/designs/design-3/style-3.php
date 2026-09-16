<style>
.wpsm_serviceBox_<?php echo $PostId;?>{
    text-align: center;
    padding: 10px;
    overflow: hidden;
    position: relative;
	margin-bottom:30px;
	background:<?php echo $sb_set_bg_clr;?>;/*#5e3448*/
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content{
   	border: <?php echo $sb_set_brdr_size;?>px double rgba(<?php echo HextoR($sb_set_brdr_clr).",".HextoG($sb_set_brdr_clr).",".HextoB($sb_set_brdr_clr);?>, 0.6);
	padding: 40px 30px 20px;
    position: relative;
    transition: all 0.3s ease-in-out 0s;
	-ms-transition: all 0.3s ease-in-out 0s;
	-webkit-transition: all 0.3s ease-in-out 0s;
	-moz-transition: all 0.3s ease-in-out 0s;
	-o-transition: all 0.3s ease-in-out 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content{
	border: <?php echo $sb_set_brdr_size;?>px double <?php echo $sb_set_hover_brdr_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_service_icon_i{
    font-size: <?php echo $sb_set_icon_size;?>px;
	width:<?php echo $sb_set_icon_size;?>px;
	height:<?php echo $sb_set_icon_size;?>px;
    color: <?php echo $sb_set_icon_clr;?>;
    margin-bottom: 15px;
    transition: all 0.3s ease-in-out 0s;
	-ms-transition: all 0.3s ease-in-out 0s;
	-webkit-transition: all 0.3s ease-in-out 0s;
	-moz-transition: all 0.3s ease-in-out 0s;
	-o-transition: all 0.3s ease-in-out 0s;
}

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content img{
  
	transition: all 0.3s ease-in-out 0s; 
	-ms-transition: all 0.3s ease-in-out 0s;
	-webkit-transition: all 0.3s ease-in-out 0s;  
	-moz-transition: all 0.3s ease-in-out 0s;
	-o-transition: all 0.3s ease-in-out 0s;  
	margin:0 auto 15px;
  <?php if($sb_set_same_icon_img_width=='yes')
		{?>
			width:<?php echo $sb_set_icon_size;?>px;
			height:<?php echo $sb_set_icon_size;?>px;
		<?php
		}
		else
		{?>
			width:<?php echo $sb_set_image_size;?>px;
			height:<?php echo $sb_set_image_size;?>px;
		<?php
		}
		?>
}

.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content .wpsm_service_icon_i{
    transform: rotate(360deg);
	-ms-transform: rotate(360deg);
	-webkit-transform: rotate(360deg);
	-moz-transform: rotate(360deg);
	-o-transform: rotate(360deg);
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content img{
	transform: rotate(360deg);
	-ms-transform: rotate(360deg);
	-webkit-transform: rotate(360deg);
	-moz-transform: rotate(360deg);
	-o-transform: rotate(360deg);
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title{
    font-size: <?php echo $sb_set_title_size;?>px;
    font-weight: 700;
    color: <?php echo $sb_set_title_clr;?>;
    margin-bottom: 15px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_description{
    font-size: <?php echo $sb_set_des_size;?>px;
    color: <?php echo $sb_set_des_clr;?>;
    line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .icon-bg{
    font-size: 250px;
	width:250px;
	height:250px;
    color: rgba(<?php echo HextoR($sb_set_icon_clr).",".HextoG($sb_set_icon_clr).",".HextoB($sb_set_icon_clr);?>, 0.3);
    line-height: 250px;
    position: absolute;
    bottom: 0;
    right: -30px;
    transition: all 0.3s ease-in-out 0s;
	-ms-transition: all 0.3s ease-in-out 0s;
	-webkit-transition: all 0.3s ease-in-out 0s;
	-moz-transition: all 0.3s ease-in-out 0s;
	-o-transition: all 0.3s ease-in-out 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?> #img_big
{
	opacity:0.3;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .icon-bg{
    transform: rotate(360deg);
	-ms-transform: rotate(360deg);
	-webkit-transform: rotate(360deg);
	-moz-transform: rotate(360deg);
	-o-transform: rotate(360deg);
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more{
	color:<?php echo $sb_set_link_clr;?>;
	font-size: <?php echo $sb_set_link_size;?>px;
	-webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more:hover,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more:hover i{
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more i{
	font-size: <?php echo $sb_set_link_size;?>px;
    color: <?php echo $sb_set_link_clr;?>;
	vertical-align:middle !important;
}

@media only screen and (max-width: 990px){
    .serviceBox{ margin-bottom: 30px; }
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content {
			border-color: rgba(<?php echo HextoR($sb_all_contents_brdr_clr).",".HextoG($sb_all_contents_brdr_clr).",".HextoB($sb_all_contents_brdr_clr);?>, 0.6);
		}
		
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content i{
			color: <?php echo $sb_all_contents_icon_clr;?>;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .icon-bg{
			color: rgba(<?php echo HextoR($sb_all_contents_icon_clr).",".HextoG($sb_all_contents_icon_clr).",".HextoB($sb_all_contents_icon_clr);?>, 0.3);
		}
	<?php
	$k++;	
	}
}
?>			
</style>