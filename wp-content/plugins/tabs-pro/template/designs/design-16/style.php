<style>
/*design 16*/
<?php 
//design 16
if($tabs_button_align=="left" || $tabs_button_align=="center")
	$border_side="right";
else
	$border_side="left";
?>

.wpsm_tab_<?php echo $post_id; ?> a:hover,
.wpsm_tab_<?php echo $post_id; ?> a:focus{
    text-decoration: none;
    outline: none;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs{
    background: transparent;
	border-bottom:0px;
	float: left;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
    text-align: center;
   	margin-left: 0;
	float: none !important;
	margin-<?php echo $border_side; ?>:21px;
	margin-bottom: 7px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
    padding: 15px 30px;
    font-size: <?php echo $tabs_title_size; ?>px;
    color: <?php echo $tabs_title_font_clr; ?>;
    background: <?php echo $tabs_title_bg_clr; ?>;
    border-radius: 50%;
    border: 1px solid <?php echo $tabs_btn_border_color; ?>;
    position: relative;
	font-weight:<?php echo intval($tabs_title_icon_font_weight/100)*100;?>;
	font-family:<?php echo $all_tabs_font_family;?>;
	text-align:center;
	
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover{
    background: <?php echo $select_tabs_bg_clr; ?> !important;
    color: <?php echo $select_tabs_title_clr; ?> !important;
    border-color: <?php echo $select_tabs_bg_clr; ?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:focus,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:hover{
    background: <?php echo $select_tabs_bg_clr; ?> !important;
    color: <?php echo $select_tabs_title_clr; ?> !important;
    border-color: <?php echo $select_tabs_bg_clr; ?>;
    transition: background 0.20s linear 0s;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:before,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:after{
    content: "";
    border: 13px solid transparent;
    border-<?php echo $border_side; ?>-color: <?php echo $tabs_btn_border_color; ?>;
    position: absolute;
    <?php echo $border_side; ?>: -24px;
    height:0px;
	top:0;
	bottom:0;
	margin:auto;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:after{
    border-<?php echo $border_side; ?>-color: <?php echo $tabs_desc_bg_clr; ?>;
    <?php echo $border_side; ?>: -25px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a i{
	/*padding-left:8px;
	padding-right:8px;*/
	color: <?php echo $tabs_title_icon_clr; ?>;
	font-size: <?php echo $tabs_icon_size; ?>px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a i,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover i{
	color: <?php echo $select_tabs_icon_clr;?> !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
    font-size: <?php echo $tabs_des_size; ?>px;
    color: <?php echo $tabs_desc_font_clr; ?>;
    background: <?php echo $tabs_desc_bg_clr; ?>;
    line-height: 1.6;
    padding: 20px 25px;
    margin-top: 25px;
    border: 1px solid <?php echo $tabs_btn_border_color; ?>;
	font-family:<?php echo $all_tabs_font_family;?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content .tab-pane p{
	color:<?php echo $tabs_desc_font_clr; ?>;
    font-size: <?php echo $tabs_des_size; ?>px;
	font-family:<?php echo $all_tabs_font_family;?>;
	background: <?php echo $tabs_desc_bg_clr; ?>;
    border:none;
	line-height:1.6;
	padding:0;
	text-align:left;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content h2{
    font-size: 30px;
}
@media only screen and (max-width: 480px){
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
        width:100% !important;
        margin-bottom: 5px;
		margin-left:0;
		margin:right:0;
	}
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs{
		float:none !important;
		}
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
        padding: 20px;
        /* border-radius: 0; */
    }
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:before,
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:after{
        border: none;
    }
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
        margin-top: 5px;
    }
}

</style>