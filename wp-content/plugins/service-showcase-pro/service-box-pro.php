<?php
/*
Plugin Name: Service Showcase Pro
Plugin URI: https://wpshopmart.com/
Description: Service Box Pro is most flexible WordPress plugin available to create and manage your Service Section with drag and drop feature..
Author: wpshopmart
Version: 5.6
Author URI: http://dazzlersoftware.com/about-us/
*/

define("service_box_directory_url", plugin_dir_url(__FILE__));
define("wpshopmart_service_box_pro_text_domain", "wpsm_service_box_pro");

/**************** Plugin Install ******************************************/	
require_once("ink/install/installation.php");


function wpsm_service_box_pro_default_data() {
	
	$Settings_Array = serialize( array(
		"sb_all_contents_view_type"				=>"grid",
		"sb_all_contents_design_no"				=>"1",
		"sb_set_sec_title_y_n"					=>"yes",
		"sb_set_sec_title_clr"					=>"#000000",
		"sb_set_sec_title_size"					=>"40",
		"sb_set_sec_bg_type"					=>"1",
		"sb_set_sec_bg_opacity"					=>"75",
		"sb_set_sec_bg_clr"						=>"#d4d9de",
		"sb_set_sec_bg_img"						=>service_box_directory_url."assets/images/section_bg.jpg",
		"sb_set_sec_bg_size"					=>"cover",
		"sb_set_sec_bg_repeat"					=>"no-repeat",
		"sb_set_sec_bg_position"				=>"left top",
		"sb_set_sec_bg_overlay_y_n"				=>"yes",
		"sb_set_sec_bg_Parallax_y_n"			=>"yes",
		"sb_set_sec_bg_img_over_clr"			=>"#000000",
		"sb_set_sec_hr_padding"					=>"15",
		"sb_set_sec_ver_padding"				=>"15",
		"sb_ind_clr_enable"				=>"no",
		"sb_set_bg_opacity"				=>"75",
		"sb_set_icon_clr"				=>"#ffffff",                  
		"sb_set_hover_icon_clr"			=>"#ffffff",       
		"sb_set_icon_bg_clr"			=>"#8466b7",                 
		"sb_set_hover_icon_bg_clr"  	=>"#8466b7",                  
		"sb_set_title_clr"   			=>"#000000",                  
		"sb_set_hover_title_clr"  		=>"#8466b7",                  
		"sb_set_des_clr"				=>"#5f5d5d",                  
		"sb_set_hover_des_clr"			=>"#d3d3d3",                  
		"sb_set_link_clr"				=>"#ffffff",                  
		"sb_set_hover_link_clr"			=>"#8466b7",                  
		"sb_set_link_bg_clr"			=>"#8466b7",                  
		"sb_set_hover_link_bg_clr"		=>"#ffffff",                  
		"sb_set_bg_clr"    				=>"#ffffff",                  
		"sb_set_hover_bg_clr"			=>"#f0386b",                  
		"sb_set_brdr_clr"				=>"#d3d3d3",                  
		"sb_set_hover_brdr_clr"			=>"#41d7f7",                   
		"sb_set_icon_brdr_clr"   		=>"#ffffff",                   
		"sb_set_hover_icon_brdr_clr" 	=>"#ffffff",                  
		"sb_set_same_icon_img_width"	=>"yes",
		"sb_set_title_size"				=>"20",
		"sb_set_icon_size"				=>"30",
		"sb_set_image_size"				=>"30",
		"sb_set_des_size"				=>"14",
		"sb_set_link_size"				=>"12",
		"sb_set_enable_family"			=>"no",
		"sb_set_font_family"			=>"Vollkorn",
		"sb_set_font_family_grp"		=>"Default Fonts",
		"sb_set_show_icon_title"		=>"1",
		"sb_set_box_layout"				=>"6",
		"sb_set_lod_animation"			=>"0",
		"sb_set_same_height"			=>"yes",
		"sb_set_brdr_yes_no"			=>"no",
		"sb_set_brdr_size"				=>"2",
		"sb_set_link_open_in"			=>"yes",
		"sb_set_link_type"				=>"1",
		"sb_set_link_icon"				=>"fa fa-plus",
		"sb_set_link_icon_position"		=>"before",
		"sb_set_carousel_no_of_items"							=>"3",
		"sb_set_carousel_loop"									=>"false",
		"sb_set_carousel_Mouse_drag_enabled"					=>"true",
		"sb_set_carousel_touch_drag_enabled"					=>"true",
		"sb_set_carousel_nav_type"								=>"1",
		"sb_set_carousel_nav_btn_type"							=>"1",
		"sb_set_carousel_nav_btn_icon_type"						=>"1",
		"sb_set_carousel_nav_dots_type"							=>"1",
		"sb_set_carousel_nav_dots_shape"						=>"1",
		"sb_set_dots_bg_clr"									=>"#dd3333",
		"sb_set_hover_dots_bg_clr"								=>"#8224e3",
		"sb_set_nav_clr"										=>"#ffffff",
		"sb_set_nav_bg_clr"										=>"#000000",
		"sb_set_hover_nav_clr"									=>"#ffffff",
		"sb_set_hover_nav_bg_clr"								=>"#869791",
		"sb_set_carousel_nav_right_text"						=>"Next",
		"sb_set_carousel_nav_left_text"							=>"Prev",
		"sb_set_carousel_nav_btn_size"							=>"1",
		"sb_set_carousel_autoplay"								=>"false",
		"sb_set_carousel_autoplay_speed_y_n"					=>"false",
		"sb_set_carousel_autoplay_speed"						=>"250",
		"sb_set_carousel_autoplay_interval_time_out"			=>"5000",
		"sb_set_carousel_autoplay_hover_pause"					=>"false",
		"sb_set_carousel_nav_speed_y_n"							=>"false",
		"sb_set_carousel_dots_speed_y_n"						=>"false",
		"sb_set_carousel_nav_position"							=>"1",
		"custom_css"											=>""
	));
	
delete_option('service_box_pro_default_Settings');	//default settings	
add_option('service_box_pro_default_Settings', $Settings_Array);	//default settings
}
register_activation_hook( __FILE__, 'wpsm_service_box_pro_default_data' );

/*********************CPT/Input Box/Settings/back end **********************/								
require_once('ink/admin/menu.php');


/************* Genrate Shorcode data/Front end **********************/	
 
 require_once("template/shortcode.php");
 
 
 /****************Widget **************************/	
 require_once("ink/widget/widget.php");
?>