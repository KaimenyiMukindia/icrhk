<style>
<?php if($sb_set_image_size>54) 
		$sb_set_image_size=54;
	  if($sb_set_icon_size>54) 
		$sb_set_icon_size=54;
	  if($sb_set_title_size>22)
		 $sb_set_title_size=22;
?>
.wpsm_serviceBox_<?php echo $PostId;?> {
    padding:20px;
	margin-top: 20px;
    text-align: center;
	background: <?php echo $sb_set_bg_clr;?>;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon{
    width: 229px;
    height: 229px;
    border-radius: 50%;
    background: transparent;
    border: 13px solid <?php echo $sb_set_icon_brdr_clr;?>;
	margin: 0 auto 37px;
    padding: 35px 0 0;
    transition: all 0.25s ease 0s;
	-ms-transition: all 0.25s ease 0s;
	-webkit-transition: all 0.25s ease 0s;
	-moz-transition: all 0.25s ease 0s;
	-o-transition: all 0.25s ease 0s;
	position:relative;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon{
    background:<?php echo $sb_set_hover_icon_bg_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon span{
    border-bottom: 5px solid <?php echo $sb_set_icon_clr;?>;
    color: <?php echo $sb_set_icon_clr;?>;
	padding-bottom:1px;
    display: inline-block;
    /*font: bold 48px/54px "Arial Black";*/
	font-weight:bold;
	margin-bottom: 16px;
    transition: all 0.25s ease 0s;
	-ms-transition: all 0.25s ease 0s;
	-webkit-transition: all 0.25s ease 0s;
	-moz-transition: all 0.25s ease 0s;
	-o-transition: all 0.25s ease 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon span i{
	color: <?php echo $sb_set_icon_clr;?>;
	transition: all 0.25s ease 0s;
	-webkit-transition: all 0.25s ease 0s;
}

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon span i
<?php if($sb_set_same_icon_img_width=='yes'){?>,.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon span img<?php }?>
{
	width: <?php echo $sb_set_icon_size;?>px;
	height: <?php echo $sb_set_icon_size;?>px;
	font-size: <?php echo $sb_set_icon_size;?>px;
}

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon span img{
	margin-bottom:8px;
<?php if($sb_set_same_icon_img_width=='no')
{
	?>
	width: <?php echo $sb_set_image_size;?>px;
	height: <?php echo $sb_set_image_size;?>px;
	<?php 
	
}?>
}

.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon span{
    color: <?php echo $sb_set_hover_icon_clr;?> !important;
    border-bottom: 5px solid <?php echo $sb_set_hover_icon_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon span i{
    color: <?php echo $sb_set_hover_icon_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon h3{
    display: block;
    /*font: bold 18px/24px "Arial";*/
	font-weight:bold;
	margin: 0 0 15px;
    text-transform: capitalize;
    transition: all 0.25s ease 0s;
	-ms-transition: all 0.25s ease 0s;
	-webkit-transition: all 0.25s ease 0s;
	-moz-transition: all 0.25s ease 0s;
	-o-transition: all 0.25s ease 0s;
	padding:0;
	color: <?php echo $sb_set_title_clr;?>;
	font-size: <?php echo $sb_set_title_size;?>px;
	line-height:<?php echo $sb_set_title_size+6;?>px;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_service_icon h3{
    color: <?php echo $sb_set_hover_title_clr;?> !important;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p {
    color: <?php echo $sb_set_des_clr;?>;
   	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
	font-size: <?php echo $sb_set_des_size;?>px;
    margin-bottom: 29px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more {
    background: <?php echo $sb_set_link_bg_clr;?>;
    color: <?php echo $sb_set_link_clr;?> !important;
    display: inline-block;
    /*font: bold 16px/53px "Arial";*/
	font-weight:bold;
	line-height: <?php echo $sb_set_link_size+3;?>px;
	font-size: <?php echo $sb_set_link_size;?>px;
    padding: 15px 32px;
    text-transform: capitalize;
    transition: all 0.25s ease 0s;
	-ms-transition: all 0.25s ease 0s;
	-webkit-transition: all 0.25s ease 0s;
	-moz-transition: all 0.25s ease 0s;
	-o-transition: all 0.25s ease 0s;
	-webkit-box-shadow:none;
	box-shadow:none;
}
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_read_more{
    background: <?php echo $sb_set_hover_link_bg_clr;?> !important;
	color: <?php echo $sb_set_hover_link_clr;?> !important;
    text-decoration: none;
	-webkit-box-shadow:none;
	box-shadow:none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more i{
    color: <?php echo $sb_set_link_clr;?> !important;
    font-weight:bold;
	font-size: <?php echo $sb_set_link_size;?>px;
	vertical-align:middle !important;
 }
.wpsm_serviceBox_<?php echo $PostId;?>:hover .wpsm_read_more i{
    color: <?php echo $sb_set_hover_link_clr;?> !important;
 }
@media screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{
        margin-bottom: 30px;
		padding:3px;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon {
			border-color: <?php echo $sb_all_contents_icon_brdr_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon span{
			border-bottom-color: <?php echo $sb_all_contents_icon_clr;?>;
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon span i{
			color: <?php echo $sb_all_contents_icon_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon .wpsm_title{
			color:<?php echo $sb_all_contents_title_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_content .wpsm_description{
			color:<?php echo $sb_all_contents_des_clr;?>;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read_more {
			background: <?php echo $sb_all_contents_link_bg_clr;?> !important;
			color: <?php echo $sb_all_contents_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:hover .wpsm_read_more{
			background: <?php echo $sb_set_hover_link_bg_clr;?> !important;
			color: <?php echo $sb_set_hover_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read_more i{
			color: <?php echo $sb_all_contents_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?>:hover .wpsm_read_more i{
			color: <?php echo $sb_set_hover_link_clr;?> !important;
		}
	<?php
	$k++;	
	}
}
?>
</style>