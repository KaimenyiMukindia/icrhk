<?php
if(isset($PostID) && isset($_POST['tabs_pro_setting_save_action'])) {
	$templates_h          		= sanitize_option('templates_h',$_POST['templates_h']);
	$templates_v          		= sanitize_option('templates_v',$_POST['templates_v']);
	$templates_presets         	= sanitize_option('templates_presets',$_POST['templates_presets']);
	
	$tab_ind_clr_enable   		  = sanitize_option('tab_ind_clr_enable',$_POST['tab_ind_clr_enable']);
	$tabs_title_bg_clr            = sanitize_text_field($_POST['tabs_title_bg_clr']);
	$select_tabs_bg_clr           = sanitize_text_field($_POST['select_tabs_bg_clr']);
	$tabs_title_font_clr          = sanitize_text_field($_POST['tabs_title_font_clr']);
	$select_tabs_title_clr        = sanitize_text_field($_POST['select_tabs_title_clr']);
	$tabs_title_icon_clr          = sanitize_text_field($_POST['tabs_title_icon_clr']);
	$select_tabs_icon_clr         = sanitize_text_field($_POST['select_tabs_icon_clr']);
	$tabs_desc_font_clr           = sanitize_text_field($_POST['tabs_desc_font_clr']);
	$tabs_desc_bg_clr 		      = sanitize_text_field($_POST['tabs_desc_bg_clr']);
	$tabs_border_color            = sanitize_option('tabs_border_color',$_POST['tabs_border_color']);
	$tabs_btn_border_color		  = sanitize_option('tabs_btn_border_color',$_POST['tabs_btn_border_color']);
	$select_tabs_btn_border_color = sanitize_option('select_tabs_btn_border_color',$_POST['select_tabs_btn_border_color']);
	
	$tabs_title_size              = sanitize_text_field($_POST['tabs_title_size']);
	$tabs_title_icon_font_weight  = sanitize_text_field($_POST['tabs_title_icon_font_weight']);
	$tabs_icon_size               = sanitize_text_field($_POST['tabs_icon_size']);
    $tabs_des_size                = sanitize_text_field($_POST['tabs_des_size']);
	$tabs_border_size             = sanitize_option('tabs_border_size',$_POST['tabs_border_size']);
	$font_family_group			  = sanitize_text_field($_POST['font_family_group']);
	$all_tabs_font_family         = sanitize_text_field($_POST['all_tabs_font_family']);
	
	$show_tabs_title_icon 		  = sanitize_option('show_tabs_title_icon', $_POST['show_tabs_title_icon']);
	$tabs_button_align 		  		= sanitize_option('tabs_button_align', $_POST['tabs_button_align']);
	$show_tabs_icon_postion 		= sanitize_option('show_tabs_icon_postion', $_POST['show_tabs_icon_postion']);
	$show_tabs_icon_align 	      = sanitize_option('show_tabs_icon_align', $_POST['show_tabs_icon_align']);
	$tabs_icon_format 	      = sanitize_option('tabs_icon_format', $_POST['tabs_icon_format']);
	$tabs_custom_image_size 	      = sanitize_option('tabs_custom_image_size', $_POST['tabs_custom_image_size']);
	$tab_img_icon_w            = sanitize_text_field($_POST['tab_img_icon_w']);
	$tab_img_icon_h            = sanitize_text_field($_POST['tab_img_icon_h']);
	
	$tabs_mob_disply_option 	  = sanitize_option('tabs_mob_disply_option', $_POST['tabs_mob_disply_option']);
	$tabs_styles          		  = sanitize_option('tabs_styles',$_POST['tabs_styles']);
	$tabs_desc_animation          = sanitize_text_field($_POST['tabs_desc_animation']);
	$tabs_content_height_option 	  = sanitize_option('tabs_content_height_option', $_POST['tabs_content_height_option']);
	$tabs_content_height          = sanitize_text_field($_POST['tabs_content_height']);
	$tabs_content_bar_bg_clr		= sanitize_text_field($_POST['tabs_content_bar_bg_clr']);
	$tabs_content_bar_hndl_bg_clr 	= sanitize_text_field($_POST['tabs_content_bar_hndl_bg_clr']);
	$tabs_content_bar_width			= sanitize_text_field($_POST['tabs_content_bar_width']);
	$tabs_button_width_option 	  = sanitize_option('tabs_button_width_option', $_POST['tabs_button_width_option']);
	$tabs_button_width          = sanitize_text_field($_POST['tabs_button_width']);
	$tabs_number          = sanitize_text_field($_POST['tabs_number']);
	$tabs_on_hover 	  			= sanitize_option('tabs_on_hover', $_POST['tabs_on_hover']);
	
	$custom_css                   = stripslashes($_POST['custom_css']);
	
				
			
			$Settings_Array = serialize( array(
				"templates_h"			=> $templates_h,
				"templates_v"			=> $templates_v,
				"templates_presets"			=> $templates_presets,
				
				"tab_ind_clr_enable"	=> $tab_ind_clr_enable,
				"tabs_title_bg_clr"   => $tabs_title_bg_clr,
				"select_tabs_bg_clr"   => $select_tabs_bg_clr,
				"tabs_title_font_clr" => $tabs_title_font_clr,
				"select_tabs_title_clr" => $select_tabs_title_clr,
				"tabs_title_icon_clr" => $tabs_title_icon_clr,
				"select_tabs_icon_clr" => $select_tabs_icon_clr,
				"tabs_desc_font_clr"  => $tabs_desc_font_clr,
				"tabs_desc_bg_clr"    => $tabs_desc_bg_clr,
				"tabs_border_color"      =>$tabs_border_color,
				"tabs_btn_border_color"	  =>$tabs_btn_border_color,
				"select_tabs_btn_border_color"=>$select_tabs_btn_border_color,
				
				"tabs_title_size"         => $tabs_title_size,
				"tabs_title_icon_font_weight" => $tabs_title_icon_font_weight,
				"tabs_icon_size"         => $tabs_icon_size,
				"tabs_des_size"     		 => $tabs_des_size,
				"tabs_border_size"         => $tabs_border_size,
				"font_family_group"         => $font_family_group,
				"all_tabs_font_family"     	 => $all_tabs_font_family,
				
				"show_tabs_title_icon" => $show_tabs_title_icon,
				"tabs_button_align" => $tabs_button_align,
				"show_tabs_icon_postion" => $show_tabs_icon_postion,
				"show_tabs_icon_align" => $show_tabs_icon_align,
				"tabs_icon_format" => $tabs_icon_format,
				"tabs_custom_image_size" => $tabs_custom_image_size,
				"tab_img_icon_w" => $tab_img_icon_w,
				"tab_img_icon_h" => $tab_img_icon_h,
				
				"tabs_mob_disply_option" => $tabs_mob_disply_option,
				"tabs_styles"      => $tabs_styles,
				"tabs_desc_animation"      => $tabs_desc_animation,
				"tabs_content_height_option"      => $tabs_content_height_option,
				"tabs_content_height"      => $tabs_content_height,
				"tabs_content_bar_bg_clr"	=>$tabs_content_bar_bg_clr,
				"tabs_content_bar_hndl_bg_clr" =>$tabs_content_bar_hndl_bg_clr,
				"tabs_content_bar_width"	=>$tabs_content_bar_width,
				"tabs_button_width_option"      => $tabs_button_width_option,
				"tabs_button_width"      => $tabs_button_width,
				"tabs_on_hover"      => $tabs_on_hover,
				"tabs_number"      => $tabs_number,
				"custom_css"	=> $custom_css
				) );

			update_post_meta($PostID, 'Tabs_pro_Settings', $Settings_Array);
		}
?>
