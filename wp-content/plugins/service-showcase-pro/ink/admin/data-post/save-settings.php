<?php

//saving main meta box data 
if( isset($PostId) &&  isset( $_POST['sb_set_hidden'] ) ) 
{
	
	//Getting View Type
	$sb_all_contents_view_type=sanitize_option('sb_all_contents_view_type',$_POST['sb_all_contents_view_type']);
	$sb_all_contents_design_no=sanitize_option('sb_all_contents_design_no',$_POST['sb_all_contents_design_no']);
	
	//getting section settings
	$sb_set_sec_title_y_n=		sanitize_option('sb_set_sec_title_y_n',$_POST['sb_set_sec_title_y_n']);
	$sb_set_sec_title_clr=		sanitize_text_field($_POST['sb_set_sec_title_clr']);
	$sb_set_sec_title_size=		sanitize_text_field($_POST['sb_set_sec_title_size']);
	$sb_set_sec_bg_type=		sanitize_option('sb_set_sec_bg_type',$_POST['sb_set_sec_bg_type']);
	$sb_set_sec_bg_opacity=		sanitize_text_field($_POST['sb_set_sec_bg_opacity']);
	$sb_set_sec_bg_clr=			sanitize_text_field($_POST['sb_set_sec_bg_clr']);
	$sb_set_sec_bg_img=			sanitize_text_field($_POST['sb_set_sec_bg_img']);
	$sb_set_sec_bg_size=		sanitize_option('sb_set_sec_bg_size',$_POST['sb_set_sec_bg_size']);
	$sb_set_sec_bg_repeat=		sanitize_option('sb_set_sec_bg_repeat',$_POST['sb_set_sec_bg_repeat']);
	$sb_set_sec_bg_position=	sanitize_option('sb_set_sec_bg_position',$_POST['sb_set_sec_bg_position']);
	$sb_set_sec_bg_overlay_y_n=	sanitize_option('sb_set_sec_bg_overlay_y_n',$_POST['sb_set_sec_bg_overlay_y_n']);
	$sb_set_sec_bg_Parallax_y_n=sanitize_option('sb_set_sec_bg_Parallax_y_n',$_POST['sb_set_sec_bg_Parallax_y_n']);
	$sb_set_sec_bg_img_over_clr=sanitize_text_field($_POST['sb_set_sec_bg_img_over_clr']);
	$sb_set_sec_hr_padding=		sanitize_text_field($_POST['sb_set_sec_hr_padding']);
	$sb_set_sec_ver_padding=	sanitize_text_field($_POST['sb_set_sec_ver_padding']);
	
	//Getting Colors 
	$sb_ind_clr_enable			=sanitize_option('sb_ind_clr_enable',$_POST['sb_ind_clr_enable']);
	$sb_set_bg_opacity			=sanitize_text_field($_POST['sb_set_bg_opacity']);
	$sb_set_icon_clr			=sanitize_text_field($_POST['sb_set_icon_clr']);
	$sb_set_hover_icon_clr		=sanitize_text_field($_POST['sb_set_hover_icon_clr']);
	$sb_set_icon_bg_clr			=sanitize_text_field($_POST['sb_set_icon_bg_clr']);
	$sb_set_hover_icon_bg_clr	=sanitize_text_field($_POST['sb_set_hover_icon_bg_clr']);
	$sb_set_title_clr			=sanitize_text_field($_POST['sb_set_title_clr']);
	$sb_set_hover_title_clr		=sanitize_text_field($_POST['sb_set_hover_title_clr']);
	$sb_set_des_clr				=sanitize_text_field($_POST['sb_set_des_clr']);
	$sb_set_hover_des_clr		=sanitize_text_field($_POST['sb_set_hover_des_clr']);
	$sb_set_link_clr			=sanitize_text_field($_POST['sb_set_link_clr']);
	$sb_set_hover_link_clr		=sanitize_text_field($_POST['sb_set_hover_link_clr']);
	$sb_set_link_bg_clr			=sanitize_text_field($_POST['sb_set_link_bg_clr']);
	$sb_set_hover_link_bg_clr	=sanitize_text_field($_POST['sb_set_hover_link_bg_clr']);
	$sb_set_bg_clr				=sanitize_text_field($_POST['sb_set_bg_clr']);
	$sb_set_hover_bg_clr		=sanitize_text_field($_POST['sb_set_hover_bg_clr']);
	$sb_set_brdr_clr			=sanitize_text_field($_POST['sb_set_brdr_clr']);
	$sb_set_hover_brdr_clr		=sanitize_text_field($_POST['sb_set_hover_brdr_clr']);
	$sb_set_icon_brdr_clr		=sanitize_text_field($_POST['sb_set_icon_brdr_clr']);
	$sb_set_hover_icon_brdr_clr	=sanitize_text_field($_POST['sb_set_hover_icon_brdr_clr']);
			
	//Getting Sizes
	$sb_set_same_icon_img_width	=sanitize_option('sb_set_same_icon_img_width',$_POST['sb_set_same_icon_img_width']);
	$sb_set_title_size			=sanitize_text_field($_POST['sb_set_title_size']);
	$sb_set_icon_size			=sanitize_text_field($_POST['sb_set_icon_size']);
	$sb_set_image_size			=sanitize_text_field($_POST['sb_set_image_size']);
	$sb_set_des_size			=sanitize_text_field($_POST['sb_set_des_size']);
	$sb_set_link_size			=sanitize_text_field($_POST['sb_set_link_size']);
			
   //Getting Font prop
	$sb_set_enable_family		=sanitize_option('sb_set_enable_family',$_POST['sb_set_enable_family']);
	$sb_set_font_family			=sanitize_text_field($_POST['sb_set_font_family']);
	$sb_set_font_family_grp		=sanitize_text_field($_POST['sb_set_font_family_grp']);
	
	
	$sb_set_show_icon_title=sanitize_option('sb_set_show_icon_title',$_POST['sb_set_show_icon_title']);
	$sb_set_box_layout=sanitize_option('sb_set_box_layout',$_POST['sb_set_box_layout']);
	$sb_set_lod_animation=sanitize_option('sb_set_lod_animation',$_POST['sb_set_lod_animation']);
	$sb_set_same_height=sanitize_option('sb_set_same_height',$_POST['sb_set_same_height']);
	$sb_set_brdr_yes_no=sanitize_option('sb_set_brdr_yes_no',$_POST['sb_set_brdr_yes_no']);
	$sb_set_brdr_size=sanitize_text_field($_POST['sb_set_brdr_size']);
	$sb_set_title_link_y_n=sanitize_option('sb_set_title_link_y_n',$_POST['sb_set_title_link_y_n']);
	$sb_set_link_open_in=sanitize_option('sb_set_link_open_in',$_POST['sb_set_link_open_in']);
	$sb_set_link_type=sanitize_option('sb_set_link_type',$_POST['sb_set_link_type']);
	$sb_set_link_icon=sanitize_text_field($_POST['sb_set_link_icon']);
	$sb_set_link_icon_position=sanitize_option('sb_set_link_icon_position',$_POST['sb_set_link_icon_position']);
	
	//Carousel Settings
	$sb_set_carousel_no_of_items=sanitize_text_field($_POST['sb_set_carousel_no_of_items']);
	$sb_set_carousel_loop=sanitize_option('sb_set_carousel_loop',$_POST['sb_set_carousel_loop']);
	$sb_set_carousel_Mouse_drag_enabled=sanitize_option('sb_set_carousel_Mouse_drag_enabled',$_POST['sb_set_carousel_Mouse_drag_enabled']);
	$sb_set_carousel_touch_drag_enabled=sanitize_option('sb_set_carousel_touch_drag_enabled',$_POST['sb_set_carousel_touch_drag_enabled']);
	$sb_set_carousel_nav_type=sanitize_option('sb_set_carousel_nav_type',$_POST['sb_set_carousel_nav_type']);
	$sb_set_carousel_nav_btn_type=sanitize_option('sb_set_carousel_nav_btn_type',$_POST['sb_set_carousel_nav_btn_type']);
	$sb_set_carousel_nav_btn_icon_type=sanitize_option('sb_set_carousel_nav_btn_icon_type',$_POST['sb_set_carousel_nav_btn_icon_type']);
	$sb_set_carousel_nav_dots_type=sanitize_option('sb_set_carousel_nav_dots_type',$_POST['sb_set_carousel_nav_dots_type']);
	$sb_set_carousel_nav_dots_shape=sanitize_option('sb_set_carousel_nav_dots_shape',$_POST['sb_set_carousel_nav_dots_shape']);
	$sb_set_dots_bg_clr=sanitize_text_field($_POST['sb_set_dots_bg_clr']);
	$sb_set_hover_dots_bg_clr=sanitize_text_field($_POST['sb_set_hover_dots_bg_clr']);
	$sb_set_nav_clr=sanitize_text_field($_POST['sb_set_nav_clr']);
	$sb_set_nav_bg_clr=sanitize_text_field($_POST['sb_set_nav_bg_clr']);
	$sb_set_hover_nav_clr=sanitize_text_field($_POST['sb_set_hover_nav_clr']);
	$sb_set_hover_nav_bg_clr=sanitize_text_field($_POST['sb_set_hover_nav_bg_clr']);
	$sb_set_carousel_nav_right_text=sanitize_text_field($_POST['sb_set_carousel_nav_right_text']);
	$sb_set_carousel_nav_left_text=sanitize_text_field($_POST['sb_set_carousel_nav_left_text']);
	$sb_set_carousel_nav_btn_size=sanitize_option('sb_set_carousel_nav_btn_size',$_POST['sb_set_carousel_nav_btn_size']);
	$sb_set_carousel_autoplay=sanitize_option('sb_set_carousel_autoplay',$_POST['sb_set_carousel_autoplay']);
	$sb_set_carousel_autoplay_speed_y_n=sanitize_option('sb_set_carousel_autoplay_speed_y_n',$_POST['sb_set_carousel_autoplay_speed_y_n']);
	$sb_set_carousel_autoplay_speed=sanitize_text_field($_POST['sb_set_carousel_autoplay_speed']);
	$sb_set_carousel_autoplay_interval_time_out=sanitize_text_field($_POST['sb_set_carousel_autoplay_interval_time_out']);
	$sb_set_carousel_autoplay_hover_pause=sanitize_option('sb_set_carousel_autoplay_hover_pause',$_POST['sb_set_carousel_autoplay_hover_pause']);
	$sb_set_carousel_nav_speed_y_n=sanitize_option('sb_set_carousel_nav_speed_y_n',$_POST['sb_set_carousel_nav_speed_y_n']);
	$sb_set_carousel_dots_speed_y_n=sanitize_option('sb_set_carousel_dots_speed_y_n',$_POST['sb_set_carousel_dots_speed_y_n']);
	$sb_set_carousel_nav_position=sanitize_option('sb_set_carousel_nav_position',$_POST['sb_set_carousel_nav_position']);
	$custom_css=stripcslashes($_POST['custom_css']);
	

	//checking blank sizes
	

	if($sb_set_title_size=="")
	$sb_set_title_size=20;

	if($sb_set_icon_size=="")
	$sb_set_icon_size=30;

	if($sb_set_image_size=="")
	$sb_set_image_size=30;

	if($sb_set_des_size=="")
	$sb_set_des_size=14;

	if($sb_set_link_size=="")
	$sb_set_link_size=12;

	if($sb_set_brdr_size=="")
	$sb_set_brdr_size=2;
	
	if($sb_set_sec_title_size=="")
	$sb_set_sec_title_size=50;
	
	if($sb_set_sec_bg_opacity=="")
	$sb_set_sec_bg_opacity=100;

	if($sb_set_sec_hr_padding=="")
	$sb_set_sec_hr_padding=15;

	if($sb_set_sec_ver_padding=="")
	$sb_set_sec_ver_padding=15;

			
	$servicebox_Settings_Array = serialize( array(

	"sb_all_contents_view_type"				=> $sb_all_contents_view_type,
	"sb_all_contents_design_no"				=> $sb_all_contents_design_no,

	"sb_set_sec_title_y_n"					=> $sb_set_sec_title_y_n,
	"sb_set_sec_title_clr"					=> $sb_set_sec_title_clr,
	"sb_set_sec_title_size"					=> $sb_set_sec_title_size,
	"sb_set_sec_bg_type"					=> $sb_set_sec_bg_type,
	"sb_set_sec_bg_opacity"					=> $sb_set_sec_bg_opacity,
	"sb_set_sec_bg_clr"						=> $sb_set_sec_bg_clr,
	"sb_set_sec_bg_img"						=> $sb_set_sec_bg_img,
	"sb_set_sec_bg_size"					=> $sb_set_sec_bg_size,
	"sb_set_sec_bg_repeat"					=> $sb_set_sec_bg_repeat,
	"sb_set_sec_bg_position"				=> $sb_set_sec_bg_position,
	"sb_set_sec_bg_overlay_y_n"				=> $sb_set_sec_bg_overlay_y_n,
	"sb_set_sec_bg_Parallax_y_n"			=> $sb_set_sec_bg_Parallax_y_n,
	"sb_set_sec_bg_img_over_clr"			=> $sb_set_sec_bg_img_over_clr,
	"sb_set_sec_hr_padding"					=> $sb_set_sec_hr_padding,
	"sb_set_sec_ver_padding"				=> $sb_set_sec_ver_padding,

	"sb_ind_clr_enable"				=> $sb_ind_clr_enable,
	"sb_set_bg_opacity"				=> $sb_set_bg_opacity,
	"sb_set_icon_clr"				=> $sb_set_icon_clr,
	"sb_set_hover_icon_clr"			=> $sb_set_hover_icon_clr,
	"sb_set_icon_bg_clr"			=> $sb_set_icon_bg_clr,
	"sb_set_hover_icon_bg_clr"		=> $sb_set_hover_icon_bg_clr,
	"sb_set_title_clr"				=> $sb_set_title_clr,
	"sb_set_hover_title_clr"		=> $sb_set_hover_title_clr,
	"sb_set_des_clr"				=> $sb_set_des_clr,
	"sb_set_hover_des_clr"			=> $sb_set_hover_des_clr,
	"sb_set_link_clr"				=> $sb_set_link_clr,
	"sb_set_hover_link_clr"			=> $sb_set_hover_link_clr,
	"sb_set_link_bg_clr"			=> $sb_set_link_bg_clr,
	"sb_set_hover_link_bg_clr"		=> $sb_set_hover_link_bg_clr,
	"sb_set_bg_clr"					=> $sb_set_bg_clr,
	"sb_set_hover_bg_clr"			=> $sb_set_hover_bg_clr,
	"sb_set_brdr_clr"				=> $sb_set_brdr_clr,
	"sb_set_hover_brdr_clr"			=> $sb_set_hover_brdr_clr,
	"sb_set_icon_brdr_clr"			=> $sb_set_icon_brdr_clr,
	"sb_set_hover_icon_brdr_clr"	=> $sb_set_hover_icon_brdr_clr,
	
	"sb_set_same_icon_img_width"	=> $sb_set_same_icon_img_width,
	"sb_set_title_size"				=> $sb_set_title_size,
	"sb_set_icon_size"				=> $sb_set_icon_size,
	"sb_set_image_size"				=> $sb_set_image_size,
	"sb_set_des_size"				=> $sb_set_des_size,
	"sb_set_link_size"				=> $sb_set_link_size,
	"sb_set_enable_family"			=> $sb_set_enable_family,
	"sb_set_font_family"			=> $sb_set_font_family,
	"sb_set_font_family_grp"		=> $sb_set_font_family_grp,

	"sb_set_show_icon_title"		=> $sb_set_show_icon_title,
	"sb_set_box_layout"				=> $sb_set_box_layout,
	"sb_set_lod_animation"			=> $sb_set_lod_animation,
	"sb_set_same_height"			=> $sb_set_same_height,

	"sb_set_brdr_yes_no"			=> $sb_set_brdr_yes_no,
	"sb_set_brdr_size"				=> $sb_set_brdr_size,

	"sb_set_title_link_y_n"			=> $sb_set_title_link_y_n,
	"sb_set_link_open_in"			=> $sb_set_link_open_in,
	"sb_set_link_type"				=> $sb_set_link_type,
	"sb_set_link_icon"				=> $sb_set_link_icon,
	"sb_set_link_icon_position"				=> $sb_set_link_icon_position,
	
	
	//Carousel Settings
	"sb_set_carousel_no_of_items"							=> $sb_set_carousel_no_of_items,
	"sb_set_carousel_loop"									=> $sb_set_carousel_loop,
	"sb_set_carousel_Mouse_drag_enabled"					=> $sb_set_carousel_Mouse_drag_enabled,
	"sb_set_carousel_touch_drag_enabled"					=> $sb_set_carousel_touch_drag_enabled,
	"sb_set_carousel_nav_type"								=> $sb_set_carousel_nav_type,
	"sb_set_carousel_nav_btn_type"							=> $sb_set_carousel_nav_btn_type,
	"sb_set_carousel_nav_btn_icon_type"						=> $sb_set_carousel_nav_btn_icon_type,
	"sb_set_carousel_nav_dots_type"							=> $sb_set_carousel_nav_dots_type,
	"sb_set_carousel_nav_dots_shape"						=> $sb_set_carousel_nav_dots_shape,
	"sb_set_dots_bg_clr"									=> $sb_set_dots_bg_clr,
	"sb_set_hover_dots_bg_clr"								=> $sb_set_hover_dots_bg_clr,
	
	"sb_set_nav_clr"										=> $sb_set_nav_clr,
	"sb_set_nav_bg_clr"										=> $sb_set_nav_bg_clr,
	"sb_set_hover_nav_clr"									=> $sb_set_hover_nav_clr,
	"sb_set_hover_nav_bg_clr"								=> $sb_set_hover_nav_bg_clr,
	"sb_set_carousel_nav_right_text"						=> $sb_set_carousel_nav_right_text,
	"sb_set_carousel_nav_left_text"						    => $sb_set_carousel_nav_left_text,
	
	"sb_set_carousel_nav_btn_size"							=> $sb_set_carousel_nav_btn_size,
	"sb_set_carousel_autoplay"								=> $sb_set_carousel_autoplay,
	"sb_set_carousel_autoplay_speed_y_n"					=> $sb_set_carousel_autoplay_speed_y_n,
	"sb_set_carousel_autoplay_speed"						=> $sb_set_carousel_autoplay_speed,
	"sb_set_carousel_autoplay_interval_time_out"			=> $sb_set_carousel_autoplay_interval_time_out,
	"sb_set_carousel_autoplay_hover_pause"					=> $sb_set_carousel_autoplay_hover_pause,
	"sb_set_carousel_nav_speed_y_n"						    => $sb_set_carousel_nav_speed_y_n,
	"sb_set_carousel_dots_speed_y_n"						=> $sb_set_carousel_dots_speed_y_n,
	"sb_set_carousel_nav_position"							=> $sb_set_carousel_nav_position,
	"custom_css"											=> $custom_css
	) );
	
	update_post_meta($PostId, 'service_box_settings', $servicebox_Settings_Array);
}
?>