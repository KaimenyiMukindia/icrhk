<style>
<?php if($sb_set_image_size>60) 
		$sb_set_image_size=60;
	  if($sb_set_icon_size>60) 
		$sb_set_icon_size=60;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    min-height:200px;
	padding: 25px 30px;
	<?php echo $service_box_font_family;?>
	background-color: <?php echo $sb_set_bg_clr;?>;
	margin-top: 20px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
	width:100%;
	text-align:left;
	line-height:40px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
    text-align: center;
	color:<?php echo $sb_set_icon_clr;?>;
	margin:auto;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i
<?php if($sb_set_same_icon_img_width=='yes'){?>,.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img<?php }?>
{
	width: <?php echo $sb_set_icon_size;?>px;
	height: <?php echo $sb_set_icon_size;?>px;
	font-size: <?php echo $sb_set_icon_size;?>px;
	float:right;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
   	<?php if($sb_set_same_icon_img_width=='no')
	{
		?>
		float:right;
		width: <?php echo $sb_set_image_size;?>px;
		height: <?php echo $sb_set_image_size;?>px;
		<?php 
	}?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon h3{
	clear:none;
	color: <?php echo $sb_set_title_clr;?>;
    font-size: <?php echo $sb_set_title_size;?>px;
    margin: 0;
    padding: 0;
    font-weight: 600;
	position:relative;
	line-height:1.6;
	display:flex;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content{
	line-height:20px;
    border-radius: 3px 0 0 3px;
    min-height: 100px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p{
    color: <?php echo $sb_set_des_clr;?>;
    font-size: <?php echo $sb_set_des_size;?>px;
    margin-top: 10px;
    padding: 5px 0 5px;
    font-weight: 600;
	text-align:left;
	margin-bottom:20px;
	line-height:1.6;
	text-align: justify;
	<?php echo $service_box_font_family;?>
}

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more{
    color:<?php echo $sb_set_link_clr;?>;
	font-size: <?php echo $sb_set_link_size;?>px;
    border:none;
    border-radius:0;
	-webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more i{
    color:<?php echo $sb_set_link_clr;?>;
	font-size: <?php echo $sb_set_link_size;?>px;
	vertical-align:middle;
 }
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_read_more:hover{
    -webkit-box-shadow:none;
	box-shadow:none;
	text-decoration: none;
}

@media only screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{
        margin-bottom: 30px;
    }
}
@media only screen and (max-width: 480px){
	.wpsm_serviceBox_<?php echo $PostId;?>{
		padding: 7px 15px;
	}
	.wpsm_serviceBox_<?php echo $PostId;?> .pull-right{
		float:none !important;
		text-align:left;
	}
	.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon h3{
		display:block;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon i{
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon .wpsm_title{
			color:<?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_description{
			color:<?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more,
        #wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_read_more i{
			color: <?php echo $sb_all_contents_link_clr;?> !important;
		}
		
	<?php
	$k++;	
	}
}
?>
</style>