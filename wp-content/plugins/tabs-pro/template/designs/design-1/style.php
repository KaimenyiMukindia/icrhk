<style>
/*Design 1 */
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a,
.wpsm_tab_<?php echo $post_id; ?> a:hover,
.wpsm_tab_<?php echo $post_id; ?> a:focus{
    outline: none;
    text-decoration: none;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs{
    background: transparent;
	border-bottom:0px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
    text-align: center;
    margin-right: 0px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
    font-size: <?php echo $tabs_title_size;?>px;
    color: <?php echo $tabs_title_font_clr;?>;
    padding: 15px 15px;
    background: <?php echo $tabs_title_bg_clr;?>;
    margin-right: 0;
    border-radius: 0;
    border: none;
    position: relative;
    transition: all 0.5s ease 0s;
	font-weight:<?php echo intval($tabs_title_icon_font_weight/100)*100;?>;
	font-family:<?php echo $all_tabs_font_family;?>;
	text-align:center;
	vertical-align:middle;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover{
    background: <?php echo $select_tabs_bg_clr;?> !important;
    color: <?php echo $select_tabs_title_clr;?> !important;
    border: none;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a i{
	/*padding-left:8px;
	padding-right:8px;*/
	color: <?php echo $tabs_title_icon_clr;?>;
	font-size: <?php echo $tabs_icon_size;?>px;
}

.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a i,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover i{
	color: <?php echo $select_tabs_icon_clr;?> !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
    font-size: <?php echo $tabs_des_size;?>px;
    color: <?php echo $tabs_desc_font_clr;?>;
	background: <?php echo $tabs_desc_bg_clr;?>;
    padding: 25px 35px;
    border: <?php echo $tabs_border_size;?>px solid <?php echo $tabs_border_color;?>;
	font-family:<?php echo $all_tabs_font_family;?>;
	line-height:1.6;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content .tab-pane p{
    font-size: <?php echo $tabs_des_size;?>px;
    color: <?php echo $tabs_desc_font_clr;?>;
	background: <?php echo $tabs_desc_bg_clr;?>;
    font-family:<?php echo $all_tabs_font_family;?>;
	line-height:1.6;
	padding:0;
	text-align:left;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content h3{
    font-size: 20px;
    font-weight: bold;
    margin-top: 0;
}

@media only screen and (max-width: 600px){
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{ 
		width: 100% !important; 
	}
	<?php if($tabs_border_size>0){?>
		.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
			border: 1px solid <?php echo $tabs_border_color;?>;
		}
	<?php }?>
}
</style>