<style>
<?php if($sb_set_image_size>85) 
		$sb_set_image_size=85;
	  if($sb_set_icon_size>85) 
		$sb_set_icon_size=85;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    border: <?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_brdr_clr;?>;
	background-color: <?php echo $sb_set_bg_clr;?>;
    border-radius: 0px 30px;
    padding: 25px 10px;
    text-align: center;
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
	margin-top:20px;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover{
    background-color: <?php echo $sb_set_hover_bg_clr;?> !important;
    border-color: <?php echo $sb_set_hover_brdr_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    width: <?php echo $sb_set_icon_size*2;?>px;
    height: <?php echo $sb_set_icon_size*2;?>px;
    background-color: <?php echo $sb_set_icon_bg_clr;?>;
    border-radius: 0 20px;
    margin: 0 auto 25px;
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
	position:relative;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon{
    background-color: <?php echo $sb_set_hover_icon_bg_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
   color: <?php echo $sb_set_icon_clr;?>;
   position:absolute;
   top:50%;
   left:50%;
   transition: all 0.5s ease 0s;
   -ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
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
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon i{
    color: <?php echo $sb_set_hover_icon_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title{
    font-size: <?php echo $sb_set_title_size;?>px;
    font-weight: bold;
    color: <?php echo $sb_set_title_clr;?>;
    margin: 0 0 18px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
    text-transform: capitalize;
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_title{
    color: <?php echo $sb_set_hover_title_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title:after{
    content: "";
    width: 25%;
    border-top: 1px solid <?php echo $sb_set_title_clr;?>;
    display: block;
    margin: 15px auto;
    transition: all 0.8s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_title:after{
    width: 80%;
    border-color: <?php echo $sb_set_hover_title_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_description{
    font-size: <?php echo $sb_set_des_size;?>px;
    color: <?php echo $sb_set_des_clr;?>;
    margin-bottom: 30px;
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_description{
    color: <?php echo $sb_set_hover_des_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more{
    font-size: <?php echo $sb_set_link_size;?>px;
    color: <?php echo $sb_set_link_clr;?>;
    background-color: <?php echo $sb_set_link_bg_clr;?>;
    border-radius: 0 20px;
    padding: 15px 30px;
    display: inline-block;
    letter-spacing: 1px;
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
	-webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more:hover{
    background: <?php echo $sb_set_hover_link_bg_clr;?> !important;
	color: <?php echo $sb_set_hover_link_clr;?> !important;
	-webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more i{
    font-size: <?php echo $sb_set_link_size;?>px;
    color: <?php echo $sb_set_link_clr;?>;
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more:hover i{
    color: <?php echo $sb_set_hover_link_clr;?> !important;
}
@media only screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{ margin-bottom: 30px; }
}
@media only screen and (max-width: 400px){
    .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
		width: 60px !important;
		height: 60px !important;	
	}
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i,
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
		position:relative;
		width: 28px !important;
		height: 28px !important;
		font-size: 28px !important;
		position:absolute;
	   top:50%;
	   left:50%;
	   margin-left:-14px;
	   margin-top:-14px;
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
			border-color: <?php echo $sb_all_contents_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon{
			background-color: <?php echo $sb_all_contents_icon_bg_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon i{
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_title{
			color:<?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_title:after{
			border-top-color: <?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_description{
			color:<?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read_more{
			color: <?php echo $sb_all_contents_link_clr;?>;
			background-color: <?php echo $sb_all_contents_link_bg_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read_more i{
			color: <?php echo $sb_all_contents_link_clr;?>;
		}
	<?php
	$k++;	
	}
}
?>		
</style>