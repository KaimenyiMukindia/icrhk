<style>
/*design 7*/
<?php 
//design 7
if($tabs_button_align=="left")
{
	$border_side="left";
	$margin_side="right";
}
elseif($tabs_button_align=="center")
{
	$border_side="center";
	$margin_side="right";
	?>
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
		margin-left: 10px;
		margin-right: 10px;
	}
	<?php
}
else
{
	$border_side="right";
	$margin_side="left";
}
?>

.wpsm_tab_<?php echo $post_id; ?> a:hover,
.wpsm_tab_<?php echo $post_id; ?> a:focus{
    text-decoration: none;
    outline: none;
}
.wpsm_tab_<?php echo $post_id; ?>{
	padding-top:10px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs {
    border-bottom:0 none;
	margin-bottom:0;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
    margin-<?php echo $margin_side;?>: 10px;
	margin-bottom:-1px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
    position: relative;
    padding: 15px;
    color: <?php echo $tabs_title_font_clr; ?>;
    font-size: <?php echo $tabs_title_size;?>px;
    z-index: 1;
	font-weight:<?php echo intval($tabs_title_icon_font_weight/100)*100;?>;
	font-family:<?php echo $all_tabs_font_family;?>;
	background:transparent !important;
	text-align:center;
	verticle-align:middle;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover{
    background:transparent;
    border:1px solid transparent;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:before{
    content: "";
    width:100%;
    height:100%;
    position:absolute;
    bottom: 8px;
    left:-1px;
    font-size:<?php echo $tabs_title_size;?>px;
	background: <?php echo $tabs_title_bg_clr; ?>;
    border: 1px solid <?php echo $tabs_btn_border_color; ?>;
    border-bottom: 0px none;
    border-radius: 10px 10px 0 0;
    transform-origin: <?php echo $border_side; ?> center 0;
    transform: perspective(4px) rotateX(2deg);
	
	-webkit-transform-origin: <?php echo $border_side; ?> center 0;
    -webkit-transform: perspective(4px) rotateX(2deg);
	z-index:-1;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:before{
    background: <?php echo $select_tabs_bg_clr; ?> !important;
	border-color:<?php echo $select_tabs_btn_border_color; ?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:focus,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:hover{
    border:1px solid transparent;
    background:transparent;
    color: <?php echo $select_tabs_title_clr; ?> !important;
    z-index: 2;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a i{
	color: <?php echo $tabs_title_icon_clr; ?>;
	font-size: <?php echo $tabs_icon_size;?>px;
	display:inline !important;
	margin:0 8px 0 8px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a i{
	color: <?php echo $select_tabs_icon_clr; ?> !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
    border: <?php echo $tabs_border_size; ?>px solid <?php echo $select_tabs_btn_border_color; ?>;
    padding: 20px;
    background:<?php echo $tabs_desc_bg_clr; ?>;
	color:<?php echo $tabs_desc_font_clr; ?>;
    line-height: 1.6;
	font-family:<?php echo $all_tabs_font_family;?>;
	font-size:<?php echo $tabs_des_size;?>px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content .tab-pane p{
	color:<?php echo $tabs_desc_font_clr; ?>;
    font-size: <?php echo $tabs_des_size; ?>px;
	font-family:<?php echo $all_tabs_font_family;?>;
	background:<?php echo $tabs_desc_bg_clr; ?>;
    border:none;
	line-height:1.6;
	padding:0;
	text-align:left;
}
<?php
if($tab_ind_clr_enable=='yes')
	{	
		$k=1;
		 foreach($tabs_data as $single_data)
		{
			 $indvid_tabs_title_bg_clr = $single_data['indvid_tabs_title_bg_clr'];
			 ?>
			 .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:nth-child(<?php echo $k; ?>) a:before
			 {
				background:<?php echo $indvid_tabs_title_bg_clr; ?>;
			 }
			 <?php
			 $k++; 
		}
	}

?>
@media only screen and (max-width: 767px) {
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
        padding: 15px 10px;
        font-size: 14px;
    }
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:before{
        bottom: 6px;
    }
}
@media only screen and (max-width: 568px) {
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
        width:100% !important;
        margin-bottom: 5px;
    }
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:before{
        bottom: 0;
        transform: none;
        border-bottom: 1px solid <?php echo $tabs_btn_border_color; ?>;
    }
}
</style>