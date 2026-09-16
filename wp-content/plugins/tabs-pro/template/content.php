<?php 
$post_type = "tabs_pro";
$AllTabs = array(  'p' => $WPSM_Tabs_ID, 'post_type' => $post_type, 'orderby' => 'ASC');

$loop = new WP_Query( $AllTabs );

while ( $loop->have_posts() ) : $loop->the_post();
	//get the post id
	$post_id = get_the_ID();
	$Tabs_Settings = unserialize(get_post_meta( $post_id, 'Tabs_pro_Settings', true));
	
	if(count($Tabs_Settings)) 
	{
		$option_names = array(
			"templates_presets"	=>"1",
			"templates_v"	=>"1",
			"templates_h"	=>"1",
			"tab_ind_clr_enable"	=>"no",
			"tabs_title_bg_clr"   => "#e8e8e8",
			"tabs_title_icon_clr" => "#000000",
			"tabs_title_font_clr" => "#000000",
			"select_tabs_bg_clr"   => "#ffffff",
			"select_tabs_icon_clr" => "#000000",
			"select_tabs_title_clr" => "#000000",
			"tabs_desc_bg_clr"    => "#ffffff",
			"tabs_desc_font_clr"  => "#000000",
			"tabs_border_color"      =>"#000000",
			"tabs_btn_border_color"	  =>"#d3d3d3",
			"select_tabs_btn_border_color"=>"#000000",
			
			"tabs_title_size"         => "14",
			"tabs_title_icon_font_weight" => "12",
			"tabs_icon_size"         => "14",
			"tabs_des_size"     		 => "16",
			"tabs_border_size"         => "16",
			"font_family_group"   		=> "Default Fonts",
			"all_tabs_font_family"     	 => "Open Sans",
			
			"show_tabs_title_icon" => "1",
			"tabs_button_align"		=> "left",
			"show_tabs_icon_postion" => "left",
			"show_tabs_icon_align" => "inline",
			"tabs_icon_format" => "icon",
			"tabs_custom_image_size" => "2",
			"tab_img_icon_w" => "12",
			"tab_img_icon_h" => "12",
			
			"tabs_mob_disply_option" => "1",
			"tabs_styles"      =>1,
			"tabs_desc_animation"      =>"fadeIn",
			"tabs_content_height_option"      =>"1",
			"tabs_content_height"      =>"12",
			"tabs_content_bar_bg_clr"	=>"#000000",
			"tabs_content_bar_hndl_bg_clr" =>"#ffffff",
			"tabs_content_bar_width"	=>"6",
			"tabs_button_width_option"      =>"1",
			"tabs_button_width"      =>"12",
			"tabs_on_hover" => "no",
			"tabs_number" => "1",
			
			"custom_css"      =>""
			);
			foreach($option_names as $option_name => $default_value) {
				if(isset($Tabs_Settings[$option_name])) 
					${"" . $option_name}  = $Tabs_Settings[$option_name];
				else
					${"" . $option_name}  = $default_value;
			}
	}
		
	$tabs_data = unserialize(get_post_meta( $post_id, 'wpsm_tabs_pro_data', true));
	$TotalCount =  get_post_meta( $post_id, 'wpsm_tabs_pro_count', true );
	if($tabs_number<=$TotalCount){
		$tabs_number = $tabs_number;
	}
	else{
		$tabs_number= 1;
	}
	$i=1;
	$j=1;
	if($TotalCount>0) 
	{	
		if($font_family_group=="Google Fonts"){ ?> 
			<script src="https://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js"></script>
			<script type="text/javascript">
			  WebFont.load({
				google: {
				  families: ['<?php echo $all_tabs_font_family; ?>'] 
				}
			  });
			</script>
		<?php } 
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
		require("style.php");
		require_once("tabs-pro-functions.php");
		
		//content animation
		$var_tabs_desc_animation="";
		$var_desc_animate_when_auto_h="";
		if($tabs_desc_animation!='0')
			$var_tabs_desc_animation="animated ".$tabs_desc_animation;
		if($tabs_content_height_option=="1")  //auto Height
			$var_desc_animate_when_auto_h=$var_tabs_desc_animation;
		
		//Fetch Main Design Files
		if((($templates_h=='3' || $templates_h=='7') && $templates_presets=='1') || ($templates_v=='19' && $templates_presets=='2'))
		{	
			if($templates_presets=='1')
				require("designs/design-".$templates_h."/index-".$templates_h.".php"); 
			else
				require("designs/design-".$templates_v."/index-".$templates_v.".php"); 
		}
		else
		{	
			if($templates_presets=='1')
			require("designs/design-".$templates_h."/style.php"); 
			else
			require("designs/design-".$templates_v."/style.php"); 
			
			require("common-index.php"); 
		}
	}
	else{
		echo "<h3> No tabs Found </h3>";
	}
endwhile; 
?>