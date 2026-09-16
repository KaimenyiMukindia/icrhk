<style>
.wpsm_serviceBox_<?php echo $PostId;?>{
	background:<?php echo $sb_set_bg_clr;?>;
    text-align: center;
    padding: 40px 16px 30px;
    border: 1px solid transparent;
    transition:all 0.3s ease 0s;
	-webkit-transition: all 0.3s ease 0s;
	position: relative;
    z-index: 1;
	margin-top: 30px;
}
.wpsm_serviceBox_<?php echo $PostId;?>:after,
.wpsm_serviceBox_<?php echo $PostId;?>:before{
    content: "";
    position: absolute;
    top:0;
    left:0;
    right: 0;
    bottom: 0;
    transition:all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?>:after{
    border-bottom: <?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_brdr_clr;?>;
    border-top: <?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_brdr_clr;?>;
    transform: scaleX(0);
	-webkit-transform: scaleX(0);
	transform-origin: 0 100% 0;
	-webkit-transform-origin: 0 100% 0;
	z-index: -1;
}
.wpsm_serviceBox_<?php echo $PostId;?>:before{
    border-left: <?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_brdr_clr;?>;
    border-right: <?php echo $sb_set_brdr_size;?>px solid <?php echo $sb_set_brdr_clr;?>;
    transform: scaleY(0);
	-webkit-transform: scaleY(0);
	transform-origin: 100% 0 0;
	-webkit-transform-origin: 100% 0 0;
	z-index: -1;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover:after{
    transform: scaleX(1);
	-webkit-transform: scaleX(1);
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover:before{
    transform: scaleY(1);
	-webkit-transform: scaleY(1);
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon
{
	margin:0 auto 20px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i
<?php if($sb_set_same_icon_img_width=='yes'){?>,.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img<?php }?>
{
	width: <?php echo $sb_set_icon_size;?>px;
	height: <?php echo $sb_set_icon_size;?>px;
	font-size: <?php echo $sb_set_icon_size;?>px;
	margin:0 auto;
	color:<?php echo $sb_set_icon_clr;?>;
    transition: all 0.3s ease 0s;
	-webkit-transition: all 0.3s ease 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img{
	margin:auto;
<?php if($sb_set_same_icon_img_width=='no')
{
	?>
	width: <?php echo $sb_set_image_size;?>px;
	height: <?php echo $sb_set_image_size;?>px;
	<?php 	
}?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3{
    margin-bottom: 25px;
	font-size: <?php echo $sb_set_title_size;?>px;
    letter-spacing: 4px;
    color:<?php echo $sb_set_title_clr;?>;
    text-decoration: none;
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content h3:hover{
    color:<?php echo $sb_set_hover_title_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p{
    color:<?php echo $sb_set_des_clr;?>;
    line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
	font-size: <?php echo $sb_set_des_size;?>px;
	margin:0 auto 15px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read{
    margin-top: 20px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a{
    border:1px solid <?php echo $sb_set_link_bg_clr;?>;
    border-radius: 50%;
    color:<?php echo $sb_set_link_clr;?>;
	display: inline-block !important;
	text-align: center;
    text-decoration: none;
    transition:all 0.3s ease 0s;
	-webkit-transition: all 0.3s ease 0s;
	opacity: 0;
	font-size: <?php echo $sb_set_link_size;?>px;
	-webkit-box-shadow:none;
	box-shadow:none;
	padding:4px 7px;
	<?php
	if($sb_set_link_type=='1' || $sb_set_link_type=='3')
	{	?>
		min-height:35px;
		padding:8px;
		height: 100%;
		border-radius:5px;
		width:45%;
		line-height: 35px;
		<?php
	}
	?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a:hover,
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon i{
    color:<?php echo $sb_set_hover_icon_clr;?> !important;
    border-color: <?php echo $sb_set_hover_icon_brdr_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_read a{
    opacity: 1;
    transition: all 0.3s ease 0s;
	-webkit-transition: all 0.3s ease 0s;
	-webkit-box-shadow:none;
	box-shadow:none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a i{
    color:<?php echo $sb_set_link_clr;?>;
	font-size: <?php echo $sb_set_link_size;?>px;
	vertical-align:middle !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read a:hover i{
    color:<?php echo $sb_set_hover_icon_clr;?> !important;
 }

@media screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{
        margin-bottom:10px;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:after{
			border-bottom-color: <?php echo $sb_all_contents_brdr_clr;?>;
			border-top-color: <?php echo $sb_all_contents_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:before{
			border-left-color: <?php echo $sb_all_contents_brdr_clr;?>;
			border-right-color: <?php echo $sb_all_contents_brdr_clr;?>;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read a{
			border-color:<?php echo $sb_all_contents_link_bg_clr;?>;
			color:<?php echo $sb_all_contents_link_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read a i{
			color:<?php echo $sb_all_contents_link_clr;?>;
		}
	<?php
	$k++;	
	}
}
?>
</style>