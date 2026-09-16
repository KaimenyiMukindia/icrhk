<style>
<?php if($sb_set_image_size>135) 
		$sb_set_image_size=135;
	  if($sb_set_icon_size>135) 
		$sb_set_icon_size=135;
?>
.wpsm_serviceBox_<?php echo $PostId;?>{
	background:<?php echo $sb_set_bg_clr;?>;
    text-align: center;
	margin-top: 30px;
	padding:20px 15px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon
{
    width: 229px;
    height: 229px;
    line-height: 229px;
    display: block;
    overflow: hidden;
    position: relative;
    margin: 0 auto 20px;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon strong
{
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-width: 13px;
    border-style: solid;
    /*border-color: hsl(201, 94%, 74%) hsla(0, 0%, 0%, 0) hsla(0, 0%, 0%, 0) hsl(201, 94%, 74%);*/
	border-color: <?php echo $sb_set_hover_icon_brdr_clr;?> hsla(0, 0%, 0%, 0) hsla(0, 0%, 0%, 0) <?php echo $sb_set_hover_icon_brdr_clr;?> !important;
	border-radius: 400px;
    transform: rotate(-45deg);
	-ms-transform: rotate(-45deg);
	-webkit-transform: rotate(-45deg);
	-moz-transform: rotate(-45deg);
	-o-transform: rotate(-45deg);
	
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
    z-index: 1;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon:hover strong
{
    transform: rotate(315deg);
	-ms-transform: rotate(315deg);
	-webkit-transform: rotate(315deg);
	-moz-transform: rotate(315deg);
	-o-transform: rotate(315deg);
	
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon span
{
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-width: 13px;
    border-style: solid;
    border-color: <?php echo $sb_set_icon_brdr_clr;?>;
    border-radius: 400px;
    z-index: 0;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon img, .wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon i{
   color: <?php echo $sb_set_icon_clr;?>;
   position:absolute;
   top:50%;
   left:50%;
   transform: rotateY(0deg);
   -ms-transform: rotateY(0deg);
	-webkit-transform: rotateY(0deg);
	-moz-transform: rotateY(0deg);
	-o-transform: rotateY(0deg);
   
   transition: all 0.5s ease 0s;
   -ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
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
		width: <?php echo $sb_set_image_size;?>px;
		height: <?php echo $sb_set_image_size;?>px;
		margin-top: -<?php echo  intval($sb_set_image_size/2);?>px;
		margin-left: -<?php echo intval($sb_set_image_size/2);?>px; 
				
	}
<?php 
} ?>

.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon:hover .icons i,
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_icon:hover .icons img{
    transform: rotateY(360deg);
	-ms-transform: rotateY(360deg);
	-webkit-transform: rotateY(360deg);
	-moz-transform: rotateY(360deg);
	-o-transform: rotateY(360deg);
	
    transition: all 0.5s ease 0s;
	-ms-transition: all 0.5s ease 0s;
	-webkit-transition: all 0.5s ease 0s;
	-moz-transition: all 0.5s ease 0s;
	-o-transition: all 0.5s ease 0s;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content .wpsm_title{
	font-size: <?php echo $sb_set_title_size;?>px;
	color: <?php echo $sb_set_title_clr;?>;	
	line-height:1.6;
	padding:0;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_service_content p{
    font-size: <?php echo $sb_set_des_size;?>px;
    color: <?php echo $sb_set_des_clr;?>;
    line-height: 1.6;
    margin:30px 0;
    padding: 0 20px;
	<?php echo $service_box_font_family;?>
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more{
    background: <?php echo $sb_set_link_bg_clr;?>;
    color: <?php echo $sb_set_link_clr;?>;
    line-height: <?php echo $sb_set_link_size+3;?>px;
	font-size: <?php echo $sb_set_link_size;?>px;
    padding: 15px 32px;
    display: inline-block;
    transition: all 0.2s ease 0s;
	-ms-transition: all 0.2s ease 0s;
	-webkit-transition: all 0.2s ease 0s;
	-moz-transition: all 0.2s ease 0s;
	-o-transition: all 0.2s ease 0s;
	-webkit-box-shadow:none;
	box-shadow:none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more:hover{
    background:<?php echo $sb_set_hover_link_bg_clr;?> !important;
	color: <?php echo $sb_set_hover_link_clr;?> !important;
    text-decoration: none;
	-webkit-box-shadow:none;
	box-shadow:none;
}
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more i{
    color: <?php echo $sb_set_link_clr;?>;
    font-size: <?php echo $sb_set_link_size;?>px;
	vertical-align:middle !important;
 }
.wpsm_serviceBox_<?php echo $PostId;?> .wpsm_read_more:hover i{
    color: <?php echo $sb_set_hover_link_clr;?> !important;
 }
@media screen and (max-width: 990px){
    .wpsm_serviceBox_<?php echo $PostId;?>{
        margin-bottom: 35px;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_service_icon span{
			border-color: <?php echo $sb_all_contents_icon_brdr_clr;?>;
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
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read_more:hover{
			background:<?php echo $sb_set_hover_link_bg_clr;?> !important;
			color: <?php echo $sb_set_hover_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read_more i{
			color: <?php echo $sb_all_contents_link_clr;?> !important;
		}
		#wpsm_serviceBox_<?php echo $PostId."_".$k;?> .wpsm_read_more:hover i{
			color: <?php echo $sb_set_hover_link_clr;?> !important;
		}
		
		
	<?php
	$k++;	
	}
}
?>	
</style>