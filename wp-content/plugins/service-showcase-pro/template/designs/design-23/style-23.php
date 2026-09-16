<style>
<?php if($sb_set_image_size>85) 
		$sb_set_image_size=85;
	  if($sb_set_icon_size>85) 
		$sb_set_icon_size=85;
?>
.wpsm_serviceBox_<?php echo $PostId;?> {
    padding: 15px 15px 25px;
    margin:75px auto 10px;
    text-align: center;
    cursor: pointer;
    border-radius: 4px;
    background:<?php echo $sb_set_bg_clr;?>;
    border:<?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_brdr_clr;?>;
    border-bottom-width:<?php echo $sb_set_brdr_size+2;?>px ;
    position:relative;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    width:<?php echo $sb_set_icon_size+50;?>px;
    height:<?php echo $sb_set_icon_size+50;?>px;
    line-height:<?php echo $sb_set_icon_size+50;?>px;
    border-radius:4px;
    border:<?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_icon_brdr_clr;?>;
    background:<?php echo $sb_set_icon_bg_clr;?>;
    color:<?php echo $sb_set_icon_clr;?>;
    margin:-<?php echo ($sb_set_icon_size/2)+45;?>px auto 20px;
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
	max-width:none;
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
		margin:-<?php echo ($sb_set_image_size/2)+45;?>px auto 20px;
		width:<?php echo $sb_set_image_size+50;?>px;
		height:<?php echo $sb_set_image_size+50;?>px;
	}
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
    background:<?php echo $sb_set_hover_icon_bg_clr;?> !important;
    
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon i{
	color:<?php echo $sb_set_hover_icon_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title {
    font-size: <?php echo $sb_set_title_size;?>px;
    font-weight:normal;
    letter-spacing:0.7px;
    position: relative;
    margin:20px 0 10px 0;
    padding:10px 0;
    background:none;
    overflow:hidden;
    color:<?php echo $sb_set_title_clr;?>;
	line-height:1.6;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title:before{
    content:"";
    background:<?php echo $sb_set_icon_brdr_clr;?>;
    width:0;
    height:2px;
    position:absolute;
    bottom:0;
    left:50%;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title:after{
    content:"";
    background:<?php echo $sb_set_icon_brdr_clr;?>;
    width:0;
    height:2px;
    position:absolute;
    bottom:0;
    right:50%;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_title:after,
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_title:before{
    width:100%;
}
.wpsm_serviceBox_<?php echo $PostId;?>,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title:before,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title:after,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more i{
    transition: all 0.5s ease-in-out;
	-ms-transition: all 0.5s ease-in-out;
	-webkit-transition: all 0.5s ease-in-out;
	-moz-transition: all 0.5s ease-in-out;
	-o-transition: all 0.5s ease-in-out;
	
}
.wpsm_serviceBox_<?php echo $PostId;?> p {
    font-size: <?php echo $sb_set_des_size;?>px;
	color:<?php echo $sb_set_des_clr;?>;
    margin:0 0 15px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more{
    color:<?php echo $sb_set_link_clr;?>;
	font-size: <?php echo $sb_set_link_size;?>px;
	-webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more:hover{
   -webkit-box-shadow:none;
	box-shadow:none;
	color:<?php echo $sb_set_hover_link_clr;?> !important;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more i{
    color:<?php echo $sb_set_link_clr;?>;
	font-size: <?php echo $sb_set_link_size;?>px;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more:hover i{
  color:<?php echo $sb_set_hover_link_clr;?> !important;
}
@media screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{
        margin:70px auto 0;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon{
			border-color:<?php echo $sb_all_contents_icon_brdr_clr;?>;
			background:<?php echo $sb_all_contents_icon_bg_clr;?>;
			color:<?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon i{
			color:<?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_title{
			color:<?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_description{
			color:<?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read_more,
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read_more i{
			color: <?php echo $sb_all_contents_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read_more:hover,
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read_more:hover i{
		   color:<?php echo $sb_set_hover_link_clr;?> !important;
		}
		
	<?php
	$k++;	
	}
}
?>
</style>