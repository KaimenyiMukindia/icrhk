<style>
<?php if($sb_set_image_size>46) 
		$sb_set_image_size=46;
	  if($sb_set_icon_size>46) 
		$sb_set_icon_size=46;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    padding:30px 0;
    text-align: center;
    background: <?php echo $sb_set_bg_clr;?>;
    color: <?php echo $sb_set_title_clr;?>;
	margin-top:20px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon {
    background: none repeat scroll 0 0 <?php echo $sb_set_icon_bg_clr;?>;
    border-radius: 100px;
    height: 70px;
    line-height: 73px;
    width: 70px;
    margin: 0 auto 15px;
	position:relative;
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
.wpsm_serviceBox_<?php echo $PostId;?> h3 {
    /*font: 600 17px/18px "arial";*/
	font-weight:600;
	font-size: <?php echo $sb_set_title_size;?>px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
	color: <?php echo $sb_set_title_clr;?>;
	margin-bottom:15px;
}
.wpsm_serviceBox_<?php echo $PostId;?> p {
    /*font: lighter 14px/21px;*/
	font-weight:lighter;
	font-size:<?php echo $sb_set_des_size;?>px;
	color:<?php echo $sb_set_des_clr;?>;
	line-height:1.6;
	<?php echo $service_box_font_family;?>
    padding: 0 20px;
    margin-bottom:15px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more{
    display: block;
    width: 100%;
    background: <?php echo $sb_set_link_bg_clr;?>;
    font-size: <?php echo $sb_set_link_size;?>px;
    font-weight: 600;
    color: <?php echo $sb_set_link_clr;?>;/*#eba133*/
    padding: 10px;
    border-left: 2px solid <?php echo $sb_set_bg_clr;?>;
    border-right: 2px solid <?php echo $sb_set_bg_clr;?>;
	-webkit-box-shadow:none;
	box-shadow:none;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content .wpsm_read_more{
     color: <?php echo $sb_set_hover_link_clr;?>;
	 background: <?php echo $sb_set_hover_link_bg_clr;?>;
	 -webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more i{
    font-size: <?php echo $sb_set_link_size;?>px;
    font-weight: 600;
    color: <?php echo $sb_set_link_clr;?>;/*#eba133*/
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content .wpsm_read_more i{
     color: <?php echo $sb_set_hover_link_clr;?>;
}
@media screen and (max-width: 767px){
    .wpsm_serviceBox_<?php echo $PostId;?>{
        margin-bottom: 30px;
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
			color: <?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon {
			background: none repeat scroll 0 0 <?php echo $sb_all_contents_icon_bg_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon i{
		   color: <?php echo $sb_all_contents_icon_clr;?>;
		 }
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> h3 {
			color: <?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> p {
			color:<?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more{
			background: <?php echo $sb_all_contents_link_bg_clr;?>;
			color: <?php echo $sb_all_contents_link_clr;?>;
			border-left-color: <?php echo $var_sb_bg_clr;?>;
			border-right-color: <?php echo $var_sb_bg_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:hover .wpsm_service_content .wpsm_read_more{
			 color: <?php echo $sb_set_hover_link_clr;?> !important;
			 background: <?php echo $sb_set_hover_link_bg_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more i{
			color: <?php echo $sb_all_contents_link_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:hover .wpsm_service_content .wpsm_read_more i{
			 color: <?php echo $sb_set_hover_link_clr;?> !important;
		}
	<?php
	$k++;	
	}
}
?>
</style>