<?php
/**
 * Plugin Name: Tabs Pro wpshopmart
 * Version: 5.6
 * Description:  Add Unlimited Tabs with 20+ design templates.
 * Author: wpshopmart
 * Author URI: http://www.wpshopmart.com
 * Plugin URI: http://www.wpshopmart.com
 *
 */

/**
 * DEFINE PATHS
 */
define("wpshopmart_tabs_pro_directory_url", plugin_dir_url(__FILE__));
define('wpshopmart_tabs_pro_directory_path', plugin_dir_path(__FILE__));
define("wpshopmart_tabs_pro_text_domain", "wpsm_tabs_pro");
define('RESPONSIVE_TABS_PRO_URL', plugins_url('/', __FILE__));
define('RESPONSIVE_TABS_PRO_PLUGIN_VERSION', '5.6');
define('RESPONSIVE_TABS_PRO_WOOCOMMERCE_POST_TYPE', 'responsive_woo_tabs');

/**
 * PLUGIN Install
 */
require_once("ink/install/installation.php");


function wpsm_tabs_pro_default_data() {
	
	$Settings_Array = serialize( array(
				"templates_presets"	=>"1",
				"templates_h"	=>"1",
				"templates_v"	=>"14",
				"tab_ind_clr_enable"	=>"no",
				"tabs_title_bg_clr"   => "#e8e8e8",
				"tabs_title_icon_clr" => "#948e8e",
				"tabs_title_font_clr" => "#948e8e",
				"select_tabs_bg_clr"   => "#d0d0d0",
				"select_tabs_icon_clr" => "#948e8e",
				"select_tabs_title_clr" => "#948e8e",
				"tabs_desc_bg_clr"    => "#ffffff",
				"tabs_desc_font_clr"  => "#b2b2b2",
				"tabs_border_color"      =>"#e8e8e8",
				"tabs_btn_border_color"	  =>"#d3d3d3",
				"select_tabs_btn_border_color"=>"#000000",
				
				"tabs_title_size"         => "21",
				"tabs_title_icon_font_weight" => "300",
				"tabs_icon_size"         => "21",
				"tabs_des_size"     		 => "20",
				"tabs_border_size"         => "1",
				"font_family_group"     	=> "Default Fonts",
				"all_tabs_font_family"     	 => "Open Sans",
				
				"show_tabs_title_icon" => "1",
				"tabs_button_align"		=> "left",
				"show_tabs_icon_postion" => "left",
				"show_tabs_icon_align" => "inline",
				"tabs_icon_format" => "icon",
				"tabs_custom_image_size" => "2",
				"tab_img_icon_w" => "20",
				"tab_img_icon_h" => "20",
				
				"tabs_mob_disply_option" => "1",
				"tabs_styles"      =>1,
				"tabs_desc_animation"      =>"fadeIn",
				"tabs_content_height_option"      =>"1",
				"tabs_content_height"      =>"200",
				"tabs_content_bar_bg_clr"	=>"#ffffff",
				"tabs_content_bar_hndl_bg_clr" =>"#000000",
				"tabs_content_bar_width"	=>"2",
				"tabs_button_width_option"      =>"1",
				"tabs_button_width"      =>"50",
				"tabs_on_hover" => "no",
				"tabs_number" => "1",
				
				"custom_css"      =>"",
				
				) );

		add_option('Tabs_pro_default_Settings', $Settings_Array);
}
register_activation_hook( __FILE__, 'wpsm_tabs_pro_default_data' );


/**
 * Including composer autoloader globally.
 *
 * @since 3.1.0
 */
require_once wpshopmart_tabs_pro_directory_path . 'autoloader.php';


/**
 * CPT CLASS
 */
 
require_once("ink/admin/menu.php");

/**
 * SHORTCODE
 */
 
 require_once("template/shortcode.php");

 /**
 * Widget
 **/
 /**
WIDGET
*/
 require_once("ink/widget/widget.php");
 

// ==============================================
//	Add Links in Plugins Table
// ==============================================

// Add settings link on plugin page
function wpsm_tabs_pro_settings_link($links) { 
  $settings_link = '<a href="edit.php?post_type=tabs_pro">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'wpsm_tabs_pro_settings_link' );

 
?>