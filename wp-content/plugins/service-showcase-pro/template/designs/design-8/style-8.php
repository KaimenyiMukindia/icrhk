<style>
<?php if($sb_set_image_size>60) 
		$sb_set_image_size=60;
	  if($sb_set_icon_size>60) 
		$sb_set_icon_size=60;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    border: <?php echo $sb_set_brdr_size; ?>px solid <?php echo $sb_set_brdr_clr; ?>;
	background:<?php echo $sb_set_bg_clr;?>;
    padding: 30px 30px 30px 90px;
    position: relative;
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
	margin-bottom:20px;
	margin-top: 20px;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover{
    border-color: <?php echo $sb_set_hover_brdr_clr; ?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?>:before,
.wpsm_serviceBox_<?php echo $PostId;?>:after{
    content: "";
    display: block;
    width: 50px;
    height: <?php echo $sb_set_brdr_size+4; ?>px;
    background: <?php echo $sb_set_hover_brdr_clr; ?> !important;
    position: absolute;
    left: 0;
    opacity: 0;
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?>:before{
    top: -<?php echo $sb_set_brdr_size+2; ?>px;
}
.wpsm_serviceBox_<?php echo $PostId;?>:after{
    bottom: -<?php echo $sb_set_brdr_size+2; ?>px;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover:before,
.wpsm_serviceBox_<?php echo $PostId;?>:hover:after{
    left: 40px;
    opacity: 1;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    display: inline-block;
    position: absolute;
    top: 33%;
	left: 23px;
    color: <?php echo $sb_set_icon_clr; ?>;
    opacity: 0.3;
    transition: all 0.5s ease-in 0s;
	-ms-transition: all 0.5s ease-in 0s;
	-webkit-transition: all 0.5s ease-in 0s;
	-moz-transition: all 0.5s ease-in 0s;
	-o-transition: all 0.5s ease-in 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i
<?php if($sb_set_same_icon_img_width=='yes'){?>,.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img<?php }?>
{
	color: <?php echo $sb_set_icon_clr;?>;
	width: <?php echo $sb_set_icon_size;?>px;
	height: <?php echo $sb_set_icon_size;?>px;
	font-size: <?php echo $sb_set_icon_size;?>px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
   	<?php if($sb_set_same_icon_img_width=='no')
	{
		?>
		width: <?php echo $sb_set_image_size;?>px;
		height: <?php echo $sb_set_image_size;?>px;
		<?php 
	}?>
	
}


.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon{
    opacity: 1;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title{
    font-size: <?php echo $sb_set_title_size; ?>px;
    font-weight: 700;
    color: <?php echo $sb_set_title_clr; ?>;
    margin-bottom: 15px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
    
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_description{
    font-size: <?php echo $sb_set_des_size; ?>px;
    color: <?php echo $sb_set_des_clr; ?>;
     margin-bottom: 15px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more{
	font-size: <?php echo $sb_set_link_size; ?>px;
    background: <?php echo $sb_set_link_bg_clr; ?>;
	border-color: <?php echo $sb_set_link_bg_clr; ?>;
    color: <?php echo $sb_set_link_clr; ?> !important;
    padding: 10px 35px;
	-webkit-box-shadow:none;
	box-shadow:none;
    transition: all 0.6s ease-in-out;
    -webkit-transition: all 0.6s ease-in-out;
    border-radius:4px;
	
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more i{
	font-size: <?php echo $sb_set_link_size; ?>px;
	color: <?php echo $sb_set_link_clr; ?> !important;
	 transition: all 0.6s ease-in-out;
    -webkit-transition: all 0.6s ease-in-out;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more:before,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more:after{
	content:'';
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_read_more{
    background: <?php echo $sb_set_hover_link_bg_clr; ?> !important;
	border-color: <?php echo $sb_set_hover_link_bg_clr; ?> !important;
    color: <?php echo $sb_set_hover_link_clr;?> !important;
	-webkit-box-shadow:none;
	box-shadow:none;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_read_more i{
	color: <?php echo $sb_set_hover_link_clr; ?> !important;
}
@media only screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{ margin-bottom: 30px; }
}
@media only screen and (max-width: 480px){
    .wpsm_serviceBox_<?php echo $PostId;?>{ 
		margin-bottom: 30px; 
		padding: 20px 15px 20px 15px;
	}
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
		display:block;
		position:relative;
		top:10px;
		left:0;
		margin-bottom:15px;
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
			 border-color: <?php echo $sb_all_contents_brdr_clr; ?>;
			 background: <?php echo $var_sb_bg_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon, 
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon i{
			color:<?php echo $sb_all_contents_icon_clr; ?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_title{
			color:<?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_description{
			color:<?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more{
			color: <?php echo $sb_all_contents_link_clr;?> !important;
			border-color: <?php echo $sb_all_contents_link_bg_clr;?>;
			background: <?php echo $sb_all_contents_link_bg_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more i{
			color: <?php echo $sb_all_contents_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:hover .wpsm_read_more{
			background: <?php echo $sb_set_hover_link_bg_clr; ?> !important;
			border-color: <?php echo $sb_set_hover_link_bg_clr; ?> !important;
			color: <?php echo $sb_set_hover_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:hover .wpsm_read_more i{
			color: <?php echo $sb_set_hover_link_clr; ?> !important;
		}
		
	<?php
	$k++;	
	}
}
?>			
</style>