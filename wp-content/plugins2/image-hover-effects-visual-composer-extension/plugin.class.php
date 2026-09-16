<?php 
/**
* Plugin Main Class
*/
if ( ! defined( 'ABSPATH' ) ) exit; 
class VC_Image_Hover_Effects_Free  
{
	
	function __construct()
	{
		add_action('vc_before_init', array($this, 'wdo_option_settings'));
		add_shortcode( 'image-hover-effects-vc-free', array( $this, 'render_image_hover_shortcode' ) );
		add_action( 'init', array( $this, 'check_if_vc_is_install' ) );
		
	}

	function wdo_option_settings(){
		include 'includes/setting-options.php'; 


		$ihe_main_var = array(
			"name" => __("Image Hover Effects"),
			"base" => "image-hover-effects-vc-free",
			"category" => __('by labibahmed'),
			"description" => __('Insert Images with Hover Effects'),
			"params" => $settings_params 
		);

		vc_map($ihe_main_var);
	}

	function render_image_hover_shortcode($attrs, $content = null){ 
		extract(shortcode_atts( array(
		    'ihe_heading'					=> "",
		    "caption_url"					=> '',
		    "caption_url_target"			=> '',
		    "ihe_image"						=> '',
		    "caption_style"					=> 'circle',
		    "hover_effect"					=> 'effect1',
		    "caption_direction"				=> 'left_to_right',
		), $attrs));

		wp_enqueue_style( 'image-hover-css', plugins_url( 'css/image-hover.css' , __FILE__ ));

		$content = wpb_js_remove_wpautop($content, true);
		if ($ihe_image != '') {
			$image_url = wp_get_attachment_url( $ihe_image );		
		}
		ob_start();
		include 'includes/render_hover_effects.php'; 
		return ob_get_clean();
	}


	function check_if_vc_is_install(){
		if ( ! defined( 'WPB_VC_VERSION' ) ) {
            // Display notice that Visual Compser is required
            add_action('admin_notices', array( $this, 'showVcVersionNotice' ));
            return;
        }
	}

	function showVcVersionNotice() {
        $plugin_name = 'Image Hover Effects Visual Composer Extension';
        echo '
        <div class="updated">
          <p>'.sprintf(__('<strong>%s</strong> requires <strong><a href="http://codecanyon.net/item/visual-composer-page-builder-for-wordpress/242431?ref=labibahmed" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'ich-vc'), $plugin_name).'</p>
        </div>';
    }
}
 ?>