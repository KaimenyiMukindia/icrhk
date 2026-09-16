<style>
<?php 
	$var_sb_icon_bg_clr="rgba(". HextoR($sb_set_icon_bg_clr).",".HextoG($sb_set_icon_bg_clr).",".HextoB($sb_set_icon_bg_clr).",".$sb_set_bg_opacity.")";
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
   display: flex;
	display:-webkit-flex;
    display:-webkit-box;
    display:-moz-flex;
    display:-moz-box;
    display:-ms-flexbox; 
	min-height:150px;
	position:relative;
	margin-bottom:30px;
	width:100%;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    background: <?php echo $var_sb_icon_bg_clr;?>;
    border-bottom:<?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_icon_brdr_clr;?>;
    color: <?php echo $sb_set_icon_clr;?>;
    padding: 20px 0 0;
    text-align: center;
    width:25%;
	min-width:80px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
	color: <?php echo $sb_set_icon_clr;?>;
    text-align: center;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
    margin:15px auto;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i
<?php if($sb_set_same_icon_img_width=='yes'){?>,.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img<?php }?>
{
	width: <?php echo $sb_set_icon_size;?>px;
	height: <?php echo $sb_set_icon_size;?>px;
	font-size: <?php echo $sb_set_icon_size;?>px;
}
<?php if($sb_set_same_icon_img_width=='no')
{ 	
	?>
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
		width: <?php echo $sb_set_image_size;?>px;
		height: <?php echo $sb_set_image_size;?>px;
	}
<?php 
} ?>
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content{
    background: <?php echo $sb_set_bg_clr;?>;
    border-bottom: <?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_brdr_clr;?>;
    border-radius: 3px 0 0 3px;
    padding: 25px;
    min-height: 152px;
	width:75%;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3{
    color: <?php echo $sb_set_title_clr;?>;
    font-size: <?php echo $sb_set_title_size;?>px;
    margin: 0 0 15px;
    padding: 0;
    font-weight: 600;
	line-height:1.6;
	<?php echo $service_box_font_family;?>
}

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p{
    color: <?php echo $sb_set_des_clr;?>;
    font-size: <?php echo $sb_set_des_size;?>px;
    padding: 5px 0 5px;
    font-weight: 600;
	margin-bottom:15px;
	line-height:1.6;
	<?php echo $service_box_font_family;?>
}

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read a{
    background: <?php echo $sb_set_link_bg_clr;?> !important;
    color:<?php echo $sb_set_link_clr;?> !important;
    margin-top:8px;
    border:none !important;
    border-radius:0 !important;
	font-size: <?php echo $sb_set_link_size;?>px;
	-webkit-box-shadow:none;
	box-shadow:none;
	padding:7px 15px !important;
	text-transform:none;
	letter-spacing:0;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read a i{
    color:<?php echo $sb_set_link_clr;?> !important;
	vertical-align:middle !important;
 }
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a:before,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a:after{
	content:"";
}
@media only screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{
        margin-bottom: 20px;
    }
}
@media only screen and (max-width: 568px){
    .wpsm_serviceBox_<?php echo $PostId;?>{
        display: block;
    }
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
		position:absolute;
		width:100%;
		top:0;
		left:0;
		float:none;
		text-align:left;
		padding:15px;
		border:none;
		border-left:<?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_icon_brdr_clr;?>;
		clear:both;
	}
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content{
		width:100%;
		float:none;
		border:none;
		padding-top:<?php echo $sb_set_icon_size+40;?>px;
		border-left: <?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_brdr_clr;?>;
	}
	<?php if($sb_set_same_icon_img_width=='no')
	{ 	
		?>
		.wpsm_serviceBox_<?php echo $PostId;?> .service-content2	{
			padding-top:<?php echo $sb_set_image_size;?>px;
		}
	<?php 
	} ?>
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
		$var_sb_icon_bg_clr="rgba(". HextoR($sb_all_contents_icon_bg_clr).",".HextoG($sb_all_contents_icon_bg_clr).",".HextoB($sb_all_contents_icon_bg_clr).",".$sb_set_bg_opacity.")";
		?>
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon{
			background: <?php echo $var_sb_icon_bg_clr;?>;
			border-bottom-color:<?php echo $sb_all_contents_icon_brdr_clr;?>;
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon i{
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content{
			background: <?php echo $var_sb_bg_clr;?>;
			border-bottom-color: <?php echo $sb_all_contents_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content h3{
			color: <?php echo $sb_all_contents_title_clr;?>;
		}

		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content p{
			color: <?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read a{
			background: <?php echo $sb_all_contents_link_bg_clr;?> !important;
			color:<?php echo $sb_all_contents_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read a i{
			color:<?php echo $sb_all_contents_link_clr;?> !important;
		}
		
	<?php
	$k++;	
	}
}
?>
</style>