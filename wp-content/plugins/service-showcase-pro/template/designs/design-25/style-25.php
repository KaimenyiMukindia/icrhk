<style>
<?php if($sb_set_image_size>66) 
		$sb_set_image_size=66;
	  if($sb_set_icon_size>66) 
		$sb_set_icon_size=66;
	
	  if($sb_set_brdr_size>3)
	  {
		  $sb_set_image_size-=$sb_set_brdr_size;
		  $sb_set_icon_size-=$sb_set_brdr_size;
	  }
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
   	border: <?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_brdr_clr;?>;
    background:<?php echo $sb_set_bg_clr;?>;
	margin-top:90px;
    text-align: center;
    padding: 25px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    width:100px;
    height:100px;
    border: <?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_icon_brdr_clr;?>;
    text-align: center;
    line-height: 100px;
    font-size: <?php echo $sb_set_icon_size;?>px;
    background: <?php echo $sb_set_icon_bg_clr;?>;
    transform: rotate(-45deg);
	-ms-transformtransform: rotate(-45deg);
	-webkit-transform: rotate(-45deg);
	-moz-transform: rotate(-45deg);
	-o-transform: rotate(-45deg);
	margin: -<?php echo ($sb_set_brdr_size/2)+75;?>px auto -20px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
   color: <?php echo $sb_set_icon_clr;?>;
   position:absolute;
   top:50%;
   left:50%;
   transform: rotate(45deg);
   -ms-transform: rotate(45deg);
	-webkit-transform: rotate(45deg);
	-moz-transform: rotate(45deg);
	-o-transform: rotate(45deg);
	
   transition:all 0.3s ease-out;
   -ms-transition: all 0.3s ease-out;
	-webkit-transition: all 0.3s ease-out;
	-moz-transition: all 0.3s ease-out;
	-o-transition: all 0.3s ease-out;
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
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon{
    background: <?php echo $sb_set_hover_icon_bg_clr;?> !important;
    border-color: <?php echo $sb_set_hover_icon_brdr_clr;?> !important;
    transition:all 0.3s ease-out;
	-ms-transition: all 0.3s ease-out;
	-webkit-transition: all 0.3s ease-out;
	-moz-transition: all 0.3s ease-out;
	-o-transition: all 0.3s ease-out;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon i{
    color:<?php echo $sb_set_hover_icon_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content{
    margin-top: 60px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3{
    text-transform: capitalize;
    color:<?php echo $sb_set_title_clr;?>;
    font-size: <?php echo $sb_set_title_size;?>px;
	margin-bottom:15px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3:after{
    content: "";
    border-top: 1px solid <?php echo $sb_set_des_clr;?>;
    border-bottom: 1px solid <?php echo $sb_set_des_clr;?>;
    width: 70px;
    height:4px;
    display:block;
    margin: 10px auto 20px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p{
    color:<?php echo $sb_set_des_clr;?>;
    margin-bottom:15px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
	font-size:<?php echo $sb_set_des_size;?>px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read{
    margin-top: 20px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a{
    color:<?php echo $sb_set_link_clr;?>;
    text-transform: capitalize;
	font-size:<?php echo $sb_set_link_size;?>px;
	-webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_read a{
    color:<?php echo $sb_set_hover_link_clr;?> !important;
    transition:all 0.3s ease-out;
	-ms-transition: all 0.3s ease-out;
	-webkit-transition: all 0.3s ease-out;
	-moz-transition: all 0.3s ease-out;
	-o-transition: all 0.3s ease-out;
	-webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a i{
    color:<?php echo $sb_set_link_clr;?>;
    font-size:<?php echo $sb_set_link_size;?>px;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_read a i{
    color:<?php echo $sb_set_hover_link_clr;?> !important;
    transition:all 0.3s ease-out;
	-ms-transition: all 0.3s ease-out;
	-webkit-transition: all 0.3s ease-out;
	-moz-transition: all 0.3s ease-out;
	-o-transition: all 0.3s ease-out;
}
@media only screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{
        margin-top: 90px;
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
			border: <?php echo $sb_all_contents_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon {
			border-color: <?php echo $sb_all_contents_icon_brdr_clr;?>;
			background:<?php echo $sb_all_contents_icon_bg_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon span i{
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon .wpsm_title{
			color:<?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_description{
			color:<?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read a,
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read a i{
			color: <?php echo $sb_all_contents_link_clr;?>;
		}
		
	<?php
	$k++;	
	}
}
?>
</style>