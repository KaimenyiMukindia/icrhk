<style>
.wpsm_serviceBox_<?php echo $PostId;?>{
	border-bottom: <?php if($sb_set_brdr_size!=""){echo $sb_set_brdr_size;}else {echo "4";}?>px solid <?php echo $sb_set_brdr_clr;?>;
    border-right:  <?php if($sb_set_brdr_size!=""){echo $sb_set_brdr_size;}else {echo "4";}?>px solid <?php echo $sb_set_brdr_clr;?>;
	background: <?php echo $sb_set_bg_clr;?>;
    padding: 30px 40px 27px;
    transition: all 0.3s ease-in-out 0s;
	margin-top:20px;
	position: relative;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover{
    border-color: <?php echo $sb_set_hover_brdr_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    display: block;
    width: <?php echo $sb_set_icon_size+25;?>px;
    height: <?php echo $sb_set_icon_size+25;?>px;
    border-radius: 50%;
    background: <?php echo $sb_set_icon_bg_clr;?>;
    text-align: center;
    float: left;
    color: <?php echo $sb_set_icon_clr;?>;
    margin: 0 20px 0 0;
    transition: all 0.3s ease-in-out 0s;
	position:relative;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon span img, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon span i{
    position:absolute;
	top: 50%;
	left: 50%;	
	color: <?php echo $sb_set_icon_clr;?>;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon span i
<?php if($sb_set_same_icon_img_width=='yes'){?>,.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon span img<?php }?>
{	
	border-radius:50%;
	width: <?php echo $sb_set_icon_size;?>px;
	height: <?php echo $sb_set_icon_size;?>px;
	margin-top: -<?php echo  intval($sb_set_icon_size/2);?>px;
	margin-left: -<?php echo intval($sb_set_icon_size/2);?>px;
	font-size: <?php echo $sb_set_icon_size;?>px;
}
<?php if($sb_set_same_icon_img_width=='no')
{ ?>
	.wpsm_serviceBox_<?php echo $PostId;?> .img-wpsm_service_icon{
		width: <?php echo $sb_set_image_size+10;?>px;
		height: <?php echo $sb_set_image_size+10;?>px;
	}
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon span img{
		border-radius:50%;
		width: <?php echo $sb_set_image_size;?>px;
		height: <?php echo $sb_set_image_size;?>px;
		margin-top: -<?php echo  intval($sb_set_image_size/2);?>px;
		margin-left: -<?php echo intval($sb_set_image_size/2);?>px; 
				
	}
<?php 
} ?>	
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title{
	display: inline-block;
    font-size: <?php if($sb_set_title_size!=""){echo $sb_set_title_size;}else {echo "20";}?>px;
    color: <?php echo $sb_set_title_clr;?>;
    border-bottom: <?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_brdr_clr;?>;
    padding-bottom: 6px;
    margin: 0 0 20px 0;
    transition: all 0.3s ease-in-out 0s;
	line-height:1.6;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_title{
    border-color: <?php echo $sb_set_hover_brdr_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_description{
    font-size: <?php if($sb_set_des_size!=""){echo $sb_set_des_size;}else {echo "18";}?>px;
    color: <?php echo $sb_set_des_clr;?>;
    margin: 0 0 11px 0;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>	
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more{
    display: inline-block;
    font-size: <?php if($sb_set_link_size!=""){echo $sb_set_link_size;}else {echo "14";}?>px;
    font-weight: 500;
    color: <?php echo $sb_set_link_clr;?>;
    text-transform: capitalize;
	-webkit-box-shadow:none;
	box-shadow:none;
	letter-spacing: 0;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more:hover{
    letter-spacing: 0.3px;
    color: <?php echo $sb_set_hover_link_clr;?> !important;
	letter-spacing: 0;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more i{
	font-size: <?php echo $sb_set_link_size;?>px;
    color: <?php echo $sb_set_link_clr;?>;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more:hover i{
     color: <?php echo $sb_set_hover_link_clr;?> !important;
}
@media only screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{ margin-bottom: 30px; }
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
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon{
			background: <?php echo $sb_all_contents_icon_bg_clr;?>;
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon span i{
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_title{
			color:<?php echo $sb_all_contents_title_clr;?>;
			border-color: <?php echo $sb_all_contents_brdr_clr;?>;
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