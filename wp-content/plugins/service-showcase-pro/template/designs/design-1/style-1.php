<!--Design 1 -->
<style>
<?php if($sb_set_image_size>40) 
		$sb_set_image_size=40;
	  if($sb_set_icon_size>40) 
		$sb_set_icon_size=40;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    text-align: center;
    margin-top: 60px;
	position: relative;
    z-index: 1;
	margin-bottom:20px;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    width: 78px;
    height: 78px;
    border-radius:3px;
    background: <?php echo $sb_set_icon_brdr_clr;?>;
    margin: 0 auto;
    position: absolute;
    top: -34px;
    left: 0;
    right: 0;
    z-index: 1;
    transition: all 0.3s ease-out 0s;
	-ms-transition: all 0.3s ease-out 0s;
	-webkit-transition: all 0.3s ease-out 0s;
	-moz-transition: all 0.3s ease-out 0s;
	-o-transition: all 0.3s ease-out 0s;
}

.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon{
    transform: rotate(45deg);
	-ms-transform: rotate(45deg); /* IE 9 */
    -webkit-transform: rotate(45deg); /* Chrome, Safari, Opera */
	-o-transform: rotate(45deg); 
    -moz-transform: rotate(45deg); 
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon span{
    display: inline-block;
    width: 60px;
    height: 60px;
    line-height: 60px;
    border-radius:3px;
    background: <?php echo $sb_set_icon_bg_clr;?>;
    font-size: <?php echo $sb_set_icon_size;?>px;
    color: <?php echo $sb_set_icon_clr;?>;
    margin: auto;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
	transition: all 0.3s ease-out 0s;
	-ms-transition: all 0.3s ease-out 0s;
	-webkit-transition: all 0.3s ease-out 0s;
	-moz-transition: all 0.3s ease-out 0s;
	-o-transition: all 0.3s ease-out 0s;
	
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon span img, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon span i{
	color: <?php echo $sb_set_icon_clr;?>;
	transition: all 0.3s ease-out 0s;
	-ms-transition: all 0.3s ease-out 0s;
	-webkit-transition: all 0.3s ease-out 0s;
	-moz-transition: all 0.3s ease-out 0s;
	-o-transition: all 0.3s ease-out 0s;
	position:absolute;
	top: 50%;
	left: 50%;
 }
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon span i
<?php if($sb_set_same_icon_img_width=='yes'){?>,.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon span img<?php }?>
{
	width: <?php echo $sb_set_icon_size;?>px;
	height: <?php echo $sb_set_icon_size;?>px;
	margin-top: -<?php echo  intval($sb_set_icon_size/2);?>px;
	margin-left: -<?php echo intval($sb_set_icon_size/2);?>px;
	font-size: <?php echo $sb_set_icon_size;?>px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon span img{
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
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon span i{
    transform: rotate(-45deg);
	-ms-transform: rotate(-45deg); /* IE 9 */
    -webkit-transform: rotate(-45deg); /* Chrome, Safari, Opera */
	-moz-transform: rotate(-45deg);
    -o-transform: rotate(-45deg);
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon span img{
    transform: rotate(-45deg);
	-ms-transform: rotate(-45deg); /* IE 9 */
    -webkit-transform: rotate(-45deg); /* Chrome, Safari, Opera */
	-moz-transform: rotate(-45deg); 
    -o-transform: rotate(-45deg); 
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content{
    background: <?php echo $sb_set_bg_clr;?>;
	margin-bottom: 25px;
    border: 1px solid <?php echo $sb_set_brdr_clr;?>;
    border-radius: 3px;
    padding: 55px 15px;
    position: relative;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content:before{
    content: "";
    display: block;
    width: 80px;
    height: 80px;
    border: 1px solid <?php echo $sb_set_brdr_clr;?>;
    border-radius: 3px;
    margin: 0 auto;
    position: absolute;
    top: -37px;
    left: 0;
    right: 0;
    z-index: -1;
    transition: all 0.3s ease-out 0s;
	-ms-transition: all 0.3s ease-out 0s;
	-webkit-transition: all 0.3s ease-out 0s;
	-moz-transition: all 0.3s ease-out 0s;
	-o-transition: all 0.3s ease-out 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content:before{
    transform: rotate(45deg);
	-ms-transform: rotate(45deg); /* IE 9 */
    -webkit-transform: rotate(45deg); /* Chrome, Safari, Opera */
	-o-transform: rotate(45deg); 
    -moz-transform: rotate(45deg); 
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title{
    font-size: <?php if($sb_set_title_size!=""){echo $sb_set_title_size;}else {echo "20";}?>px;
    font-weight: 500;
    color: <?php echo $sb_set_title_clr;?>;
    margin: 15px 0 20px 0;
    position: relative;
    transition: all 0.3s ease-out 0s;
	-ms-transition: all 0.3s ease-out 0s;
	-webkit-transition: all 0.3s ease-out 0s;
	-moz-transition: all 0.3s ease-out 0s;
	-o-transition: all 0.3s ease-out 0s;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_title{
    color: <?php echo $sb_set_hover_title_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_description{
    font-size: <?php if($sb_set_des_size!=""){echo $sb_set_des_size;}else {echo "14";}?>px;
    font-weight: 500;
    line-height: 1.6;
	padding:0;
    margin-bottom: <?php echo intval(($sb_set_link_size+$sb_set_des_size)/2)+20;?>px;
	color:<?php echo $sb_set_des_clr;?>;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more{
    display: block;
    width: 40px;
    height: 40px;
    line-height: 40px;
    border-radius: 50%;
    background: <?php echo $sb_set_link_bg_clr;?>;
    border: 1px solid <?php echo $sb_set_brdr_clr;?>;
    font-size: <?php if($sb_set_link_size!=""){echo $sb_set_link_size;}else {echo "12";}?>px;
    color: <?php echo $sb_set_link_clr;?>;
    margin: 0 auto;
    position: absolute;
    bottom: -26px;
    left: 0;
    right: 0;
    transition: all 0.3s ease-out 0s;
	-ms-transition: all 0.3s ease-out 0s;
	-webkit-transition: all 0.3s ease-out 0s;
	-moz-transition: all 0.3s ease-out 0s;
	-o-transition: all 0.3s ease-out 0s;
	-webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
	<?php
		if($sb_set_link_type=='1' || $sb_set_link_type=='3')
		{	?>
			border-radius:5px;
			width:<?php echo $sb_set_link_size+30;?>%;
			height:auto;
			padding:7px 10px;
			<?php
		}
	?>
}

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more:hover{
     color: <?php echo $sb_set_hover_link_clr;?> !important;
	 background: <?php echo $sb_set_hover_link_bg_clr;?> !important;
	 text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more i{
	font-size: <?php echo $sb_set_link_size;?>px;
    color: <?php echo $sb_set_link_clr;?>;
	transition: all 0.3s ease-out 0s;
	-ms-transition: all 0.3s ease-out 0s;
	-webkit-transition: all 0.3s ease-out 0s;
	-moz-transition: all 0.3s ease-out 0s;
	-o-transition: all 0.3s ease-out 0s;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more:hover i{
     color: <?php echo $sb_set_hover_link_clr;?> !important;
}
@media only screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{ margin-bottom: 80px; }
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
			background: <?php echo $sb_all_contents_icon_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon span{
			background: <?php echo $sb_all_contents_icon_bg_clr;?>;
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon span i{
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content{
			background: <?php echo $var_sb_bg_clr;?>;
			border-color:<?php echo $sb_all_contents_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content:before{
			border-color: <?php echo $sb_all_contents_brdr_clr;?>;
		}
		
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_title{
			color:<?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_description{
			color:<?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more{
			background: <?php echo $sb_all_contents_link_bg_clr;?>;
			border-color: <?php echo $sb_all_contents_brdr_clr;?>;
			color: <?php echo $sb_all_contents_link_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more i{
			color: <?php echo $sb_all_contents_link_clr;?>;
		}
	<?php
	$k++;	
	}
}
?>
</style>