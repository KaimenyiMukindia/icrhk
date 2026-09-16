<style>
/* design 13 */

.wpsm_tab_<?php echo $post_id; ?>{
    margin-top: 30px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs{
    border:none;
    border-bottom: 1px solid <?php echo $tabs_btn_border_color; ?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs{
	margin-bottom:0px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
	margin-bottom:-1px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
    padding: 15px 15px;
    border:1px solid <?php echo $tabs_btn_border_color; ?>;
	border-top: 2px solid <?php echo $tabs_btn_border_color; ?>;
    border-right: 0px none;
    background: <?php echo $tabs_title_bg_clr; ?>;
    color:<?php echo $tabs_title_font_clr; ?>;
	font-size:<?php echo $tabs_title_size; ?>px;
    border-radius: 0px;
    margin-right: 0px;
    transition: all 0.3s ease-in 0s;
	font-weight:<?php echo intval($tabs_title_icon_font_weight/100)*100;?>;
	font-family:<?php echo $all_tabs_font_family;?>;
	text-align:center;
}
/* .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover{
    border-bottom-color: <?php echo $tabs_btn_border_color; ?>;
    border-right: 0px none;
    background: <?php echo $select_tabs_btn_border_color; ?> !important;
	color:<?php echo $tabs_title_font_clr; ?> !important; 
} */
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a i{
    color: <?php echo $tabs_title_icon_clr; ?>;
	 font-size:<?php echo $tabs_icon_size; ?>px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a i{
	color: <?php echo $select_tabs_icon_clr; ?> !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:last-child{
    border-right:1px solid <?php echo $tabs_btn_border_color; ?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:focus,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:hover{
    border-top: 3px solid <?php echo $select_tabs_btn_border_color; ?>;
    border-right: 1px solid <?php echo $tabs_btn_border_color; ?>;
	border-left: 1px solid <?php echo $tabs_btn_border_color; ?>;
	margin-top: -15px;
    color: <?php echo $select_tabs_title_clr; ?> !important;
    padding: 22px 15px;
	background:transparent !important;
	border-bottom-color: transparent ;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
    padding: 20px;
    line-height: 1.6;
    box-shadow:0px 1px 0px #808080 !important;
	font-size: <?php echo $tabs_des_size;?>px;
    color: <?php echo $tabs_desc_font_clr;?>;
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
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content h3{
    margin-top: 0;
}
@media only screen and (max-width: 767px){
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
        width:100% !important;
        margin-bottom: 10px;
    }
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a,
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover{
        padding: 15px;
		border-bottom: 1px solid <?php echo $tabs_btn_border_color; ?>;
		border-right: 1px solid <?php echo $tabs_btn_border_color; ?>;
    }
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a,
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:focus,
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:hover{
        padding: 15px;
        margin-top: 0;
		border-bottom: 1px solid <?php echo $tabs_btn_border_color; ?>;
    }
	
}


</style>