<style>
<?php if($sb_set_image_size>54) 
		$sb_set_image_size=54;
	  if($sb_set_icon_size>54) 
		$sb_set_icon_size=54;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    background: <?php echo $sb_set_bg_clr;?>;
    <?php echo $service_box_font_family;?>
	text-align: center;
   	margin-top: 20px;
	margin-bottom: 50px;
    padding: 20px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
	top:15px;
	border-radius:50%;
    width: 80px;
    height: 80px;
    line-height: 80px;
    display: inline-block;
    border: 1px solid <?php echo $sb_set_icon_brdr_clr;?>;
    margin-bottom: 35px;
    position: relative;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
   color: <?php echo $sb_set_icon_clr;?>;
   position:absolute;
   top:50%;
   left:50%;
   -webkit-transition: background 0.2s, color 0.2s;
	-moz-transition: background 0.2s, color 0.2s;
	transition: background 0.2s, color 0.2s;
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
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon i{
     color: <?php echo $sb_set_hover_icon_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon:after{
    content: "";
    width: 100%;
    height: 100%;
    box-shadow: 0 0 0 3px <?php echo $sb_set_hover_icon_brdr_clr;?> !important;
    position: absolute;
    top: -5px;
    left: -5px;
    opacity: 1;
    padding: 5px;
    
	-webkit-transform: scale(1);
	-moz-transform: scale(1);
	-ms-transform: scale(1);
	transform: scale(1);
	
    box-sizing: content-box;
    transition: all 0.2s ease 0s;
	border-radius:50%;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon:after{
    -webkit-transform: scale(0);
	-moz-transform: scale(0);
	-ms-transform: scale(0);
	transform: scale(0);
	opacity: 0;
}

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i:after {
	padding: 7px;
	box-shadow: 0 0 0 4px #fff;
	-webkit-transition: -webkit-transform 0.2s, opacity 0.2s;
	-webkit-transform: scale(.8);
	-moz-transition: -moz-transform 0.2s, opacity 0.2s;
	-moz-transform: scale(.8);
	-ms-transform: scale(.8);
	transition: transform 0.2s, opacity 0.2s;
	transform: scale(.8);
	opacity: 0;
	
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_title{
    font-size: <?php echo $sb_set_title_size;?>px;
    color: <?php echo $sb_set_title_clr;?>;
    margin: 0 0 15px;
	<?php echo $service_box_font_family;?>
	line-height: 1.6;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_description{
    font-size: <?php echo $sb_set_des_size;?>px;
    color: <?php echo $sb_set_des_clr;?>;
    line-height: 1.6;
	margin-bottom:40px;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a{
	position: absolute;
	display:block;
	/* bottom:-<?php echo (20+$sb_set_link_size)/2;?>px; */
    left: 0;
    right: 0;
	margin: 0 auto;
	<?php
		if($sb_set_link_type=='1' || $sb_set_link_type=='3')
		{	?>
			border-radius:5px;
			width:50%;
			padding: 10px 20px !important;
			<?php
		}
		else
		{
			?>
			width: 50px;
			padding:5px !important;
			border-radius: 50%;
			<?php
		}
	?>
	background:<?php echo $sb_set_link_bg_clr;?> !important;
    box-shadow: 0 1px 0 hsla(0, 0%, 100%, 0.3) inset, 0 1px 3px hsla(0, 0%, 0%, 0.1);
    border-color:transparent !important;
    color:<?php echo $sb_set_link_clr;?> !important;
    font-size:<?php echo $sb_set_link_size;?>px;
    font-weight:bold;
    -webkit-box-shadow:none;
	box-shadow:none;
	letter-spacing:0;
	text-transform:none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a i{
	color:<?php echo $sb_set_link_clr;?> !important;
	vertical-align: middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a:before,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a:after{
	content:"";
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a:hover{
    background:<?php echo $sb_set_hover_link_bg_clr;?> !important;
	color:<?php echo $sb_set_hover_link_clr;?> !important;
    text-decoration:none;
    border-color:transparent;
	-webkit-box-shadow:none;
	box-shadow:none;
	letter-spacing:0;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a:hover i{
	color:<?php echo $sb_set_hover_link_clr;?> !important;
}
@media only screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{ margin-bottom: 30px; }
}
@media screen and (max-width: 480px){
    .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a{
		width:80%;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon{
			border-color: <?php echo $sb_all_contents_icon_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon i{
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_title{
			color:<?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_description{
			color:<?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content a{
			background:<?php echo $sb_all_contents_link_bg_clr;?> !important;
			color:<?php echo $sb_all_contents_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content a i{
			color:<?php echo $sb_all_contents_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content a:hover{
			background:<?php echo $sb_set_hover_link_bg_clr;?> !important;
			color:<?php echo $sb_set_hover_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content a:hover i{
			color:<?php echo $sb_set_hover_link_clr;?> !important;
		}
	<?php
	$k++;	
	}
}
?>
</style>