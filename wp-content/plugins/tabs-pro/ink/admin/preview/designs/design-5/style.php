<style>
/*design 5*/
<?php
switch ($tabs_button_align)
{
	case "left":
			?>
			.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:first-child a
			{
				border-bottom-left-radius:20px;
			}
			<?php
			break;
	case "right":
			?>
			.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:first-child a
			{
				border-top-right-radius:20px;
			}
			<?php
			break;
	case "center":
			?>
			.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:first-child a
			{
				border-bottom-left-radius:20px;
			}
			.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:last-child a
			{
				border-top-right-radius:20px;
			}
			<?php
			break;
}
?>

.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs{
    border-bottom:0 none;
    background: <?php echo $tabs_title_bg_clr;?>;
    border-radius: 0 20px 0 20px;
	margin:0 !important;
	padding:0 !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
		margin-bottom:0;
	}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
    background: transparent !important;
    border-radius: 0;
    font-size: <?php echo $tabs_title_size;?>px;
    border: none;
    color: <?php echo $tabs_title_font_clr;?>;
    padding: 12px 22px;
	font-weight:<?php echo intval($tabs_title_icon_font_weight/100)*100;?>;
	font-family:<?php echo $all_tabs_font_family;?>;
	text-align:center;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a i{
    /* margin-right:10px; */
    color:<?php echo $tabs_title_icon_clr;?>;
	font-size: <?php echo $tabs_icon_size;?>px;
}

.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a{
    border: 0 none;
    background:<?php echo $select_tabs_bg_clr;?>  !important;
    color:<?php echo $select_tabs_title_clr;?>  !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a i{
    color:<?php echo $select_tabs_icon_clr;?>  !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:after{
    content: "";
    position: absolute;
    left: 0;
	 right:0;
	margin:auto;
	width:0px; 
    bottom: -14px;
    border: 7px solid transparent;
    border-top: 7px solid <?php echo $select_tabs_bg_clr;?> !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
    padding:12px;
    color:<?php echo $tabs_desc_font_clr;?>;
    font-size: <?php echo $tabs_des_size;?>px;
    line-height:1.6;
    margin-top: 25px;
    border-bottom:<?php echo $tabs_border_size;?>px solid <?php echo $tabs_border_color;?>;
	font-family:<?php echo $all_tabs_font_family;?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content .tab-pane p{
	color:<?php echo $tabs_desc_font_clr; ?>;
    font-size: <?php echo $tabs_des_size; ?>px;
	font-family:<?php echo $all_tabs_font_family;?>;
	background:none;
    border:none;
	line-height:1.6;
	padding:0;
	text-align:left;
}
@media only screen and (max-width: 600px) {
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs,
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
        width:100%;
        background:transparent;
    }
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:first-child a,
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:last-child a
	{
		border-bottom-left-radius:0px;
		border-top-right-radius:0px;
	}
	
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a{
        border-radius:7px 7px 0 0;
    }
    
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
        margin-bottom:8px;
        
		background: <?php echo $tabs_title_bg_clr;?> !important;
    }
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:after{
		width:0;
	}
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:after{
        border:none;
    }
}
</style>