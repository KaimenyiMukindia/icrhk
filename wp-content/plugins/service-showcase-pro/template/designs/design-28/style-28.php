<style>
<?php 
//$var_sb_bg_clr_temp
if($sb_set_bg_opacity=='1')
{
	$var_sb_bg_clr_shadow1_op="0.90";
	$var_sb_bg_clr_shadow2_op="0.80";
}
else
{	
	$var_sb_bg_clr_shadow1_op=$sb_set_bg_opacity-0.10;
	$var_sb_bg_clr_shadow2_op=$sb_set_bg_opacity-0.20;
}
$var_sb_bg_clr_shadow1="rgba(". HextoR($var_sb_bg_clr_temp).",".HextoG($var_sb_bg_clr_temp).",".HextoB($var_sb_bg_clr_temp).",".$var_sb_bg_clr_shadow1_op.")";
$var_sb_bg_clr_shadow2="rgba(". HextoR($var_sb_bg_clr_temp).",".HextoG($var_sb_bg_clr_temp).",".HextoB($var_sb_bg_clr_temp).",".$var_sb_bg_clr_shadow2_op.")";
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
    text-align: center;
    margin-bottom: 25px;
	margin-top: 15px;
	background-color: <?php echo $sb_set_bg_clr;?>;
    box-shadow: 0 0.0625em 0.1875em 0 hsla(0, 0%, 0%, 0.1), 0 0.5em 0 -0.25em <?php echo $var_sb_bg_clr_shadow1;?>, 0 0.5em 0.1875em -0.25em hsla(0, 0%, 0%, 0.1), 0 1em 0 -0.5em <?php echo $var_sb_bg_clr_shadow2;?>, 0 1em 0.1875em -0.5em hsla(0, 0%, 0%, 0.1);
    padding: 25px;
    color: <?php echo $sb_set_icon_clr;?>;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
    color: <?php echo $sb_set_icon_clr;?>;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
	margin:0 auto;
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
    margin: 7px 0 10px 0;
    text-transform:capitalize;
	font-size: <?php echo $sb_set_title_size;?>px;
	color: <?php echo $sb_set_title_clr;?>;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p{
    margin-bottom:15px;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
	font-size: <?php echo $sb_set_des_size;?>px;
	color: <?php echo $sb_set_des_clr;?>;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read .btn{
	font-size: <?php echo $sb_set_link_size;?>px;
	color: <?php echo $sb_set_link_clr;?> !important;
	border-color: <?php echo $sb_set_link_clr;?> !important;
	background: <?php echo $sb_set_link_bg_clr;?> !important;
	-webkit-box-shadow:none;
	box-shadow:none;
	padding:5px 15px !important;
	border-radius:4px;
	letter-spacing:0;
	text-transform:none;
	letter-spacing:0;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read .btn i{
	color: <?php echo $sb_set_link_clr;?> !important;
	vertical-align:middle !important;
	font-size: <?php echo $sb_set_link_size;?>px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read .btn:before,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read .btn:after{
	content:"";
}
@media only screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{
        margin-bottom: 25px;
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
			box-shadow: 0 0.0625em 0.1875em 0 hsla(0, 0%, 0%, 0.1), 0 0.5em 0 -0.25em <?php echo $var_sb_bg_clr;?>, 0 0.5em 0.1875em -0.25em hsla(0, 0%, 0%, 0.1), 0 1em 0 -0.5em <?php echo $var_sb_bg_clr;?>, 0 1em 0.1875em -0.5em hsla(0, 0%, 0%, 0.1);
			color: <?php echo $sb_all_contents_icon_clr;?>;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read .btn{
			color: <?php echo $sb_all_contents_link_clr;?> !important;
			border-color: <?php echo $sb_all_contents_link_clr;?> !important;
			background: <?php echo $sb_all_contents_link_bg_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read .btn i{
			color: <?php echo $sb_all_contents_link_clr;?> !important;
		}
	<?php
	$k++;	
	}
}
?>
</style>