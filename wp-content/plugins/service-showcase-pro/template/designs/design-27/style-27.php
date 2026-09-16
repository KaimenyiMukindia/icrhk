<style>
<?php if($sb_set_image_size>65) 
		$sb_set_image_size=65;
	  if($sb_set_icon_size>65) 
		$sb_set_icon_size=65;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    text-align:center;
    background:<?php echo $sb_set_bg_clr;?>;
    border:<?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_brdr_clr;?>;
    padding:20px;
    text-align:center;
    margin-bottom: <?php echo $sb_set_link_size+30;?>px;
	margin-top:15px;
}
.wpsm_serviceBox_<?php echo $PostId;?> h3.wpsm_title{
    color: <?php echo $sb_set_title_clr;?>;
    font-size: <?php echo $sb_set_title_size;?>px;
    margin:0;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon {
    width:<?php echo $sb_set_icon_size*2.5;?>px;
    height:<?php echo $sb_set_icon_size*2.5;?>px;
    border-radius:50%;
    background-color: <?php echo $sb_set_icon_bg_clr;?>;
    line-height:<?php echo $sb_set_icon_size*2.5;?>px;
    margin:30px auto 15px;
    transition:all 0.3s ease-in-out 0.3s;
	-ms-transition: all 0.3s ease-in-out 0.3s;
	-webkit-transition: all 0.3s ease-in-out 0.3s;
	-moz-transition: all 0.3s ease-in-out 0.3s;
	-o-transition: all 0.3s ease-in-out 0.3s;
	position:relative;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
   color: <?php echo $sb_set_icon_clr;?>;
   position:absolute;
	top: 50%;
	left: 50%;
	transition:all 0.3s ease-in-out 0.3s;
	-ms-transition: all 0.3s ease-in-out 0.3s;
	-webkit-transition: all 0.3s ease-in-out 0.3s;
	-moz-transition: all 0.3s ease-in-out 0.3s;
	-o-transition: all 0.3s ease-in-out 0.3s;
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
		width:<?php echo $sb_set_image_size*2.5;?>px;
		height:<?php echo $sb_set_image_size*2.5;?>px;
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
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon i,
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon img{
    transform: rotate(360deg);
	-ms-transform: rotate(360deg);
	-webkit-transform: rotate(360deg);
	-moz-transform: rotate(360deg);
	-o-transform: rotate(360deg);
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p{
    color:<?php echo $sb_set_des_clr;?>;
    font-size:<?php echo $sb_set_des_size;?>px;
	margin-bottom:15px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a{
	position: absolute;
    /* bottom: -<?php echo $sb_set_link_size+5;?>px; */
    left: 0;
    right: 0;
	margin: 0 auto;
	<?php
	if($sb_set_link_type=='1' || $sb_set_link_type=='3')
	{	?>
		border-radius:5px;
		width:45%;
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
	background:<?php echo $sb_set_link_bg_clr;?>;
    box-shadow: 0 1px 0 hsla(0, 0%, 100%, 0.3) inset, 0 1px 3px hsla(0, 0%, 0%, 0.1);
    border-color:transparent !important;
    color:<?php echo $sb_set_link_clr;?> !important;
    font-size:<?php echo $sb_set_link_size;?>px;
    font-weight:bold;
	text-transform:none;
	letter-spacing:0;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a i{
	color:<?php echo $sb_set_link_clr;?> !important;
	font-size:<?php echo $sb_set_link_size;?>px;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a:before,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a:after{
	content:"";
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a:hover{
    background:<?php echo $sb_set_hover_link_bg_clr;?>;
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
@media screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a{
        bottom:-<?php echo 10+($sb_set_link_size/2);?>px;
    }
}
@media screen and (max-width: 480px){
    .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a{
        width:70%;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon {
			background-color: <?php echo $sb_all_contents_icon_bg_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon i{
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_title{
			color:<?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_description{
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