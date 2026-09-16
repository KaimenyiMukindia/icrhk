<style>
.wpsm_serviceBox_<?php echo $PostId;?>{
    text-align: center;
    padding:70px 20px;
    border-bottom:<?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_brdr_clr;?>;
    background:<?php echo $sb_set_bg_clr;?>;
    transition:all 0.5s ease-in-out;
    margin: 10px -15px;
	width:auto;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
	margin-bottom:15px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
    color: <?php echo $sb_set_icon_clr;?>;
    text-align: center;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
	margin:auto auto;
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
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3{
    color: <?php echo $sb_set_title_clr;?>;
    font-size:<?php echo $sb_set_title_size;?>px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
	margin-bottom:15px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p{
    color:<?php echo $sb_set_des_clr;?>;
	font-size:<?php echo $sb_set_des_size;?>px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
	margin-bottom:15px;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content h3, .wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon i{
    color: <?php echo $sb_set_hover_title_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover{
    background:<?php echo $sb_set_brdr_clr;?> !important;
    transition:all 0.5s ease-in-out;
	-ms-transition:all 0.5s ease-in-out;
	-webkit-transition:all 0.5s ease-in-out;
	-moz-transition:all 0.5s ease-in-out;
	-o-transition:all 0.5s ease-in-out;
    color:<?php echo $sb_set_hover_des_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_content p{
	color:<?php echo $sb_set_hover_des_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a{
    color:<?php echo $sb_set_link_clr;?> !important;
	background:<?php echo $sb_set_link_bg_clr;?> !important;
	border-color:<?php echo $sb_set_link_bg_clr;?> !important;
	font-size:<?php echo $sb_set_link_size;?>px;
	-webkit-box-shadow:none;
	box-shadow:none;
	padding:7px 15px !important;
	border-radius:4px;
	text-transform:none;
	letter-spacing:0;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a i{
    color:<?php echo $sb_set_link_clr;?> !important;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a:before,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content a:after{
	content:"";
}
@media screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{
        margin-bottom: 30px;
    }
}
@media screen and (max-width: 767px){
    .wpsm_serviceBox_<?php echo $PostId;?>{
        margin: 0 0 30px 0;
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
			border-bottom-color:<?php echo $sb_all_contents_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:hover{
			background: <?php echo $sb_all_contents_brdr_clr;?> !important;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content a{
			color:<?php echo $sb_all_contents_link_clr;?> !important;
			background:<?php echo $sb_all_contents_link_bg_clr;?> !important;
			border-color:<?php echo $sb_all_contents_link_bg_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content a i{
			color:<?php echo $sb_all_contents_link_clr;?> !important;
		}
	<?php
	$k++;	
	}
}
?>
</style>