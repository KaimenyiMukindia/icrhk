<?php 
$post_type = "servicebox";

$all_service_box = array(  'p' => $sb_shortcode_id, 'post_type' => $post_type, 'orderby' => 'ASC');
$loop = new WP_Query( $all_service_box );
if($loop->have_posts())	
{
	while ( $loop->have_posts() ) : $loop->the_post();
		$PostId = get_the_ID();
		
		/*********************************************** ***********************************************************
					Fatching Settings
		************************************************************************************************************/															
		$sb_settings = unserialize(get_post_meta( $PostId, 'service_box_settings', true));
		$option_names = array(
			"sb_all_contents_view_type"								,
			"sb_all_contents_design_no"								,
			"sb_set_sec_title_y_n"									,
			"sb_set_sec_title_clr"									,
			"sb_set_sec_title_size"									,
			"sb_set_sec_bg_type"									,
			"sb_set_sec_bg_opacity"									,
			"sb_set_sec_bg_clr"										,
			"sb_set_sec_bg_img"										,
			"sb_set_sec_bg_size"									,
			"sb_set_sec_bg_repeat"									,
			"sb_set_sec_bg_position"								,
			"sb_set_sec_bg_overlay_y_n"								,
			"sb_set_sec_bg_Parallax_y_n"							,
			"sb_set_sec_bg_img_over_clr"							,
			"sb_set_sec_hr_padding"									,
			"sb_set_sec_ver_padding"								,
			"sb_ind_clr_enable"										,
			"sb_set_bg_opacity"										,
			"sb_set_icon_clr"										,
			"sb_set_hover_icon_clr"									,       
			"sb_set_icon_bg_clr"									,             
			"sb_set_hover_icon_bg_clr"  							,                 
			"sb_set_title_clr"   									,               
			"sb_set_hover_title_clr"  								,                 
			"sb_set_des_clr"										,                
			"sb_set_hover_des_clr"									,                
			"sb_set_link_clr"										,                
			"sb_set_hover_link_clr"									,                 
			"sb_set_link_bg_clr"									,              
			"sb_set_hover_link_bg_clr"								,                 
			"sb_set_bg_clr"    										,               
			"sb_set_hover_bg_clr"									,                  
			"sb_set_brdr_clr"										,               
			"sb_set_hover_brdr_clr"									,                  
			"sb_set_icon_brdr_clr"   								,                  
			"sb_set_hover_icon_brdr_clr" 							,                  
			"sb_set_same_icon_img_width"							,
			"sb_set_title_size"										,
			"sb_set_icon_size"										,
			"sb_set_image_size"										,
			"sb_set_des_size"										,
			"sb_set_link_size"										,
			"sb_set_enable_family"									,
			"sb_set_font_family"									,
			"sb_set_font_family_grp"								,
			"sb_set_show_icon_title"								,
			"sb_set_box_layout"										,
			"sb_set_lod_animation"									,
			"sb_set_same_height"									,
			"sb_set_brdr_yes_no"									,
			"sb_set_brdr_size"										,
			"sb_set_link_open_in"									,
			"sb_set_link_type"										,
			"sb_set_link_icon"										,
			"sb_set_link_icon_position"										,
			"sb_set_carousel_no_of_items"							,
			"sb_set_carousel_loop"									,
			"sb_set_carousel_Mouse_drag_enabled"					,
			"sb_set_carousel_touch_drag_enabled"					,
			"sb_set_carousel_nav_type"								,
			"sb_set_carousel_nav_btn_type"							,
			"sb_set_carousel_nav_btn_icon_type"						,
			"sb_set_carousel_nav_dots_type"							,
			"sb_set_carousel_nav_dots_shape"						,
			"sb_set_dots_bg_clr"									,
			"sb_set_hover_dots_bg_clr"								,

			"sb_set_nav_clr"										,
			"sb_set_nav_bg_clr"										,
			"sb_set_hover_nav_clr"									,
			"sb_set_hover_nav_bg_clr"								,
			"sb_set_carousel_nav_right_text"						,
			"sb_set_carousel_nav_left_text"							,
			"sb_set_carousel_nav_btn_size"							,
			"sb_set_carousel_autoplay"								,
			"sb_set_carousel_autoplay_speed_y_n"					,
			"sb_set_carousel_autoplay_speed"						,
			"sb_set_carousel_autoplay_interval_time_out"			,
			"sb_set_carousel_autoplay_hover_pause"					,
			"sb_set_carousel_nav_speed_y_n"							,
			"sb_set_carousel_dots_speed_y_n"						,
			"sb_set_carousel_nav_position"							,
			"custom_css"						
			);
			//getting value(default/save)
			foreach($option_names as $option_name) 
			{
				${"" . $option_name}  = $sb_settings[$option_name];
			}
			
			$data_option_names = array(
				 "sb_all_contents_title"=>"Sample title",
				 "sb_all_contents_description"=>"Sample Description",
				 "sb_all_contents_btn_img_or_icon"=>"Icon",
				 "sb_all_contents_icons"=>"fa fa-mobile-phone",
				 "sb_all_contents_images"=>service_box_directory_url."assets/images/Responsive_Web.png",
				 "sb_all_contents_btn_link_yes_no"=>"No",
				 "sb_all_contents_btn_link_text"=>"Read More",
				 "sb_all_contents_btn_link_url"=>"https://www.google.co.in/",
				 "sb_all_contents_btn_link_open_in"=>"Yes",
				 
				"sb_all_contents_icon_clr"=>"#000000",
				"sb_all_contents_icon_bg_clr"=>"#ffffff",
				"sb_all_contents_title_clr"=>"#008b8b",
				"sb_all_contents_des_clr"=>"#d3d3d3",
				"sb_all_contents_link_clr"=>"#ffffff",
				"sb_all_contents_link_bg_clr"=>"#008b8b",
				"sb_all_contents_bg_clr"=>"#ffffff",
				"sb_all_contents_brdr_clr"=>"#d3d3d3",
				"sb_all_contents_icon_brdr_clr"=>"#000000"
				);
			
			/*********************************************** ***********************************************************
						Fatching Contents Settings(Main Data)
			************************************************************************************************************/	
			$sb_all_data=array();
			$sb_all_data = unserialize(get_post_meta( $PostId, 'sb_pro_all_data', true));
			$sb_TotalCount =  get_post_meta( $PostId, 'sb_pro_total_contents', true );
			$total_contents=$sb_TotalCount;
			
			//Getting Google Fonts
			if($sb_set_enable_family=='yes' && $sb_set_font_family_grp=="Google Fonts")
			{?>	
				<script src="https://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js"></script>
				<script type="text/javascript">
				WebFont.load({
					google: {
					  families: ['<?php echo $sb_set_font_family; ?>'] 
					}
				  });
				</script>
			<?php 
			} 
			
			if($sb_TotalCount>0) //if have contents
			{
				//Hex Color to RGB Color Converstion
				if ( ! function_exists( 'HextoR' ) ) 
				{
					function HextoR($hex_color)
					{
					return intval(substr(trim($hex_color),1,2), 16);
					}
				}
				if ( ! function_exists( 'HextoG' ) ) 
				{
					function HextoG($hex_color)
					{
					return intval(substr(trim($hex_color),3,2), 16);
					}
				}
				if ( ! function_exists( 'HextoB' ) ) 
				{
					function HextoB($hex_color)
					{
					return intval(substr(trim($hex_color),5,2), 16);
					}
				}
				
				require('variables.php');   //getting only variable names
				require('common-style.php');
				require("designs/design-".$sb_all_contents_design_no."/style-".$sb_all_contents_design_no.".php");
				echo "<style>".$custom_css."</style>";
				require("designs/design-".$sb_all_contents_design_no."/index-".$sb_all_contents_design_no.".php");
			}
			else
			{
				echo "<h3> No Service Box Found </h3>";
			}
	endwhile;
}
/*else
{
	echo "<h3>Your Shortcode <b>[servicebox_sc id=".$sb_shortcode_id."]</b> is wrong.</h3>";
}*/
?>