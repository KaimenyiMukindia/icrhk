<style>
<?php if($sb_set_image_size>85) 
		$sb_set_image_size=85;
	  if($sb_set_icon_size>85) 
		$sb_set_icon_size=85;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    background:<?php echo $sb_set_bg_clr;?>;
    padding: 35px 0 20px;
    text-align: center;
	margin-top: 20px;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover{
    background: <?php echo $sb_set_hover_bg_clr;?> !important;
    box-shadow:0 0px 10px #C9C9C9;
	-webkit-box-shadow:0 0px 10px #C9C9C9;
    transition:all 0.4s ease-in-out;
    -webkit-transition:all 0.4s ease-in-out;
    -moz-transition:all 0.4s ease-in-out;
    -ms-transition:all 0.4s ease-in-out;
    -o-transition:all 0.4s ease-in-out;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    width: <?php echo $sb_set_icon_size*2;?>px;
    height: <?php echo $sb_set_icon_size*2;?>px;
    border-radius: 50%;
    background: <?php echo $sb_set_icon_bg_clr;?>;
    color: <?php echo $sb_set_icon_clr;?>;
    font-weight:lighter;
	line-height:<?php echo $sb_set_icon_size*2;?>px;
    margin: 0 auto 20px;
	position:relative;
}

.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon{
    background: <?php echo $sb_set_hover_icon_bg_clr;?> !important;
    transition:all 0.4s ease-in-out;
    -webkit-transition:all 0.4s ease-in-out;
    -moz-transition:all 0.4s ease-in-out;
    -ms-transition:all 0.4s ease-in-out;
    -o-transition:all 0.4s ease-in-out;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon i{
	color: <?php echo $sb_set_hover_icon_clr;?> !important;
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
		width: <?php echo $sb_set_image_size*2;?>px;
		height: <?php echo $sb_set_image_size*2;?>px;
	}
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
		width: <?php echo $sb_set_image_size;?>px;
		height: <?php echo $sb_set_image_size;?>px;
		margin-top: -<?php echo  intval($sb_set_image_size/2);?>px;
		margin-left: -<?php echo intval($sb_set_image_size/2);?>px; 
				
	}
<?php 
} ?>
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_title{
    color: <?php echo $sb_set_hover_title_clr;?>;
    font-weight:lighter;
	font-size:<?php echo $sb_set_title_size;?>px;
	line-height:<?php echo $sb_set_title_size+4;?>px;	
	display: block;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_title span{
    color: <?php echo $sb_set_title_clr;?>;
    font-weight:lighter;
	font-size:<?php echo $sb_set_title_size;?>px;
	line-height:<?php echo $sb_set_title_size+4;?>px;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_description{
    color: <?php echo $sb_set_des_clr;?>;
    padding: 0 16px;
    font-weight:lighter;
	font-size: <?php echo $sb_set_des_size;?>px;
	line-height:1.6;
	 margin:25px 0 20px;
	 <?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a {
    background: <?php echo $sb_set_link_bg_clr;?>;
    display: inline-block;
    padding: 10px 20px;
    color: <?php echo $sb_set_link_clr;?>;
    font-weight:500;
	font-size: <?php echo $sb_set_link_size;?>px;
	-webkit-box-shadow:none;
	box-shadow:none;
	margin:0 auto 20px;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a:hover{
    background: <?php echo $sb_set_hover_link_bg_clr;?>;
    color: <?php echo $sb_set_hover_link_clr;?>;
    text-decoration: none;
	-webkit-box-shadow:none;
	box-shadow:none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a i {
    color: <?php echo $sb_set_link_clr;?>;
    font-weight:500;
	font-size: <?php echo $sb_set_link_size;?>px;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a:hover i{
    color: <?php echo $sb_set_hover_link_clr;?>;
}
@media screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{
        margin-bottom: 60px;
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
			color: <?php echo $sb_all_contents_icon_clr;?>;
			background: <?php echo $sb_all_contents_icon_bg_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon i{
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_title span{
			color:<?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_description{
			color:<?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read a {
			background: <?php echo $sb_all_contents_link_bg_clr;?> !important;
			color: <?php echo $sb_all_contents_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read a:hover{
			background: <?php echo $sb_set_hover_link_bg_clr;?> !important;
			color: <?php echo $sb_set_hover_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read a i{
			color: <?php echo $sb_all_contents_link_clr;?> !important;
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