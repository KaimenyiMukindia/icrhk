<style>
<?php if($sb_set_image_size>56) 
		$sb_set_image_size=56;
	  if($sb_set_icon_size>56) 
		$sb_set_icon_size=56;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    text-align: center;
	margin-top: 20px;
	padding:20px;
	border:<?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_brdr_clr;?>;
	background:<?php echo $sb_set_bg_clr;?>;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon::before{
    right: 79px;
    top: 33px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon::after {
    left:79px;
    top:33px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon::before, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon::after {
    background: <?php echo $sb_set_icon_bg_clr;?> none repeat scroll 0 0;
    border-bottom: 1px solid <?php echo $sb_set_icon_brdr_clr;?>;
    border-top: 1px solid <?php echo $sb_set_icon_brdr_clr;?>;
    content: " ";
    height: 14px;
    position: absolute;
    width: 45px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon {
    background: <?php echo $sb_set_icon_bg_clr;?> none repeat scroll 0 0;
    border: 1px solid <?php echo $sb_set_icon_brdr_clr;?>;
    border-radius: 82px;
    display: inline-block;
    height: 82px;
    line-height: 92px;
    margin-bottom: 28px;
    position: relative;
    width: 82px;
    text-align: center;
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
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3 {
    color:<?php echo $sb_set_title_clr;?>;
    font-size: <?php echo $sb_set_title_size;?>px;
    line-height: 1.6;
    margin-bottom: 27px;
    margin-top: 0;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p {
    color: <?php echo $sb_set_des_clr;?>;
    font-size: <?php echo $sb_set_des_size;?>px;
    line-height: 1.6;
    margin: 0;
    padding: 0 15px;
	margin-bottom:20px;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon::before, 
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon::after, 
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon{
    border-color: <?php echo $sb_set_hover_icon_brdr_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon i{
    color: <?php echo $sb_set_hover_icon_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more{
    display: inline-block;
    font-size: <?php echo $sb_set_link_size;?>px;
    color: <?php echo $sb_set_link_clr;?> !important;
    text-transform: capitalize;
    padding: 5px 10px;
    border-top: 1px solid <?php echo $sb_set_link_clr;?>;
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
	-webkit-box-shadow:none;
	box-shadow:none;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content .wpsm_read_more{
    color: <?php echo $sb_set_hover_link_clr;?> !important;
    border-top-color: <?php echo $sb_set_hover_link_clr;?> !important;
	-webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more i{
    font-size: <?php echo $sb_set_link_size;?>px;
    color: <?php echo $sb_set_link_clr;?> !important;
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content .wpsm_read_more i{
    color: <?php echo $sb_set_hover_link_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon::before, 
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon::after,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img, 
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i,
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon i{
	transition: all 0.4s ease 0s;
	-ms-transition: all 0.4s ease 0s;
	-webkit-transition: all 0.4s ease 0s;
	-moz-transition: all 0.4s ease 0s;
	-o-transition: all 0.4s ease 0s;
}
@media only screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{
        margin-bottom: 20px;
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
			border-color:<?php echo $sb_all_contents_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon::before, 
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon::after {
			background: <?php echo $sb_all_contents_icon_bg_clr;?> none repeat scroll 0 0;
			border-bottom-color: <?php echo $sb_all_contents_icon_brdr_clr;?>;
			border-top-color: <?php echo $sb_all_contents_icon_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon {
			background: <?php echo $sb_all_contents_icon_bg_clr;?> none repeat scroll 0 0;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more{
			color: <?php echo $sb_all_contents_link_clr;?> !important;
			border-top-color: <?php echo $sb_all_contents_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:hover .wpsm_service_content .wpsm_read_more{
			color: <?php echo $sb_set_hover_link_clr;?> !important;
			border-top-color: <?php echo $sb_set_hover_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more i{
			color: <?php echo $sb_all_contents_link_clr;?> !important;
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