<style>
.wpsm_serviceBox_<?php echo $PostId;?>{
    position:relative;
    <?php echo $service_box_font_family;?>
	display: flex;
	display:-webkit-flex;
    display:-webkit-box;
    display:-moz-flex;
    display:-moz-box;
    display:-ms-flexbox;
    min-height:150px;
	margin-bottom:20px;
	margin-top:20px;
	width:100%;
}
.wpsm_serviceBox_<?php echo $PostId;?> .responsive-wpsm_service_icon{
	display:none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    background: <?php echo $sb_set_bg_clr;?>;
     padding: 10px 7px 0;
    text-align: center;
    width:25%;
	min-width:80px;
	
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
    text-align: center;
	margin:auto;
	color: <?php echo $sb_set_icon_clr;?>;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i
<?php if($sb_set_same_icon_img_width=='yes'){?>,.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img<?php }?>
{
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
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content{
    background: <?php echo $sb_set_bg_clr;?>;
    padding: 15px;
    min-height: 152px;
	line-height:1.4;
	width:75%;
	position:relative;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3{
    color: <?php echo $sb_set_title_clr;?>;
    font-size: <?php echo $sb_set_title_size;?>px;
    margin: 0;
    padding: 0;
    font-weight: 600;
	line-height:1.6;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a{
    background: <?php echo $sb_set_link_bg_clr;?> !important;
    color:<?php echo $sb_set_link_clr;?> !important;
	font-size: <?php echo $sb_set_link_size;?>px;
    margin-top:15px;
	margin-bottom:8px;
    border:none !important;
    border-radius:0 !important;
	padding:7px 15px !important;
	-webkit-box-shadow:none;
	box-shadow:none;
	text-transform:none;
	letter-spacing:0;
	display: block;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a i{
    color:<?php echo $sb_set_link_clr;?> !important;
	font-size: <?php echo $sb_set_link_size;?>px;
	vertical-align:middle !important;
 }
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p{
    color: <?php echo $sb_set_des_clr;?>;
    margin-bottom:20px !important;
    font-size: <?php echo $sb_set_des_size;?>px;
    margin: 0;
    padding: 5px 0 5px;
    font-weight: 300;
	<?php echo $service_box_font_family;?>
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
		display:none !important;
		width:100%;
		text-align:center;
		padding:15px;
		border:none;
		clear:both;
	}
	.wpsm_serviceBox_<?php echo $PostId;?> .responsive-wpsm_service_icon{
		display:block !important;
	}
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content{
		width:100%;
		float:none;
		border:none;
		padding:10px;
		text-align:center;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon,
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content{
			background: <?php echo $var_sb_bg_clr;?>;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content a,
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content a i{
			color: <?php echo $sb_all_contents_link_clr;?> !important;
		}
		
	<?php
	$k++;	
	}
}
?>
</style>