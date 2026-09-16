<?php
class wpsm_tabs_pro {
	private static $instance;
    public static function forge() {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }
	
	private function __construct() {
		add_action('admin_enqueue_scripts', array(&$this, 'wpsm_tabs_pro_admin_scripts'));
		$this->Extension();
        if (is_admin()) {
			add_action('init', array(&$this, 'tabs_pro_register_cpt'), 1);
			add_action('add_meta_boxes', array(&$this, 'wpsm_tabs_pro_meta_boxes_group'));
			add_action('admin_init', array(&$this, 'wpsm_tabs_pro_meta_boxes_group'), 1);
			add_action('save_post', array(&$this, 'add_tabs_pro_meta_box_save'), 9, 1);
			add_action('save_post', array(&$this, 'tabs_pro_settings_meta_box_save'), 9, 1);
		}
    }
	
	// admin scripts
	public function wpsm_tabs_pro_admin_scripts(){
		if(get_post_type()=="tabs_pro"){
			
			wp_enqueue_media();
			
			wp_enqueue_script('jquery-ui-datepicker');
			//color-picker css n js
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style('thickbox');
			wp_enqueue_script( 'wpsm_tabs_pro-color-pic', wpshopmart_tabs_pro_directory_url.'assets/js/color-picker.js', array( 'wp-color-picker' ), false, true );
			wp_enqueue_script( 'wpsm_tabs_pro-modernizr', wpshopmart_tabs_pro_directory_url.'assets/js/modernizr.js' );
			wp_enqueue_style('wpsm_tabs_pro-panel-style', wpshopmart_tabs_pro_directory_url.'assets/css/panel-style.css');
			wp_enqueue_style('wpsm_tabs_pro-sidebar', wpshopmart_tabs_pro_directory_url.'assets/css/sidebar.css');
			 wp_enqueue_script('wpsm_tabs_pro-media-uploads',wpshopmart_tabs_pro_directory_url.'assets/js/media-upload-script.js',array('media-upload','thickbox','jquery')); 
			
			  
			//font awesome css
			wp_enqueue_style('wpsm_tabs_pro-font-awesome', wpshopmart_tabs_pro_directory_url.'assets/css/font-awesome/css/font-awesome.min.css');
			wp_enqueue_style('wpsm_tabs_pro_bootstrap', wpshopmart_tabs_pro_directory_url.'assets/css/bootstrap.css');
			wp_enqueue_style('wpsm_tabs_pro_font-awesome-picker', wpshopmart_tabs_pro_directory_url.'assets/css/fontawesome-iconpicker.css');
			wp_enqueue_style('wpsm_tabs_pro_jquery-css', wpshopmart_tabs_pro_directory_url .'assets/css/ac_jquery-ui.css');
			
			//line editor
			wp_enqueue_style('wpsm_tabs_pro_line-edtor', wpshopmart_tabs_pro_directory_url.'assets/css/jquery-linedtextarea.css');
			wp_enqueue_script( 'wpsm_tabs_pro-line-edit-js', wpshopmart_tabs_pro_directory_url.'assets/js/jquery-linedtextarea.js');
			
			wp_enqueue_script( 'wpsm_tabs_pro_bootstrap-js', wpshopmart_tabs_pro_directory_url.'assets/js/bootstrap.js');
			wp_enqueue_style('wpsm_tabs_pro_bootstrap-for-tab', wpshopmart_tabs_pro_directory_url.'assets/css/bootstrap-front.css');
			//tooltip
			wp_enqueue_style('wpsm_tabs_pro_tooltip', wpshopmart_tabs_pro_directory_url.'assets/tooltip/darktooltip.css');
			wp_enqueue_script( 'wpsm_tabs_pro-tooltip-js', wpshopmart_tabs_pro_directory_url.'assets/tooltip/jquery.darktooltip.js');
			
			// settings
			wp_enqueue_style('wpsm_tabs_pro_settings-css', wpshopmart_tabs_pro_directory_url.'assets/css/settings.css');
			
			//icon picker	
			wp_enqueue_style('wpsm_tabs_pro_remodal-css', wpshopmart_tabs_pro_directory_url .'assets/modal/remodal.css');
			wp_enqueue_style('wpsm_tabs_pro_remodal-default-theme-css', wpshopmart_tabs_pro_directory_url .'assets/modal/remodal-default-theme.css');
			wp_enqueue_script('wpsm_tabs_pro_min-js',wpshopmart_tabs_pro_directory_url.'assets/modal/remodal.min.js',array('jquery'), false, true);
			
			//icon picker
			wp_enqueue_script('tabs_pro_admin_font-icon-picker-js_all',wpshopmart_tabs_pro_directory_url.'assets/js/mul-type-icon-picker/icon-picker.js');
			wp_enqueue_style('tabs_pro_admin_font-icon-picker_all', wpshopmart_tabs_pro_directory_url.'assets/css/mul-type-icon-picker/icon-picker.css');	
			wp_enqueue_style('tabs_pro_admin_font-icon-picker-glyphicon_style',wpshopmart_tabs_pro_directory_url.'assets/css/mul-type-icon-picker/picker/glyphicon.css');
			wp_enqueue_style('tabs_pro_admin_font-icon-picker-dashicons_style',wpshopmart_tabs_pro_directory_url.'assets/css/mul-type-icon-picker/picker/dashicons.css');
			
			//Code Mirrer
			wp_enqueue_style('tabs_pro_admin__codemirror-css', wpshopmart_tabs_pro_directory_url.'assets/codex/codemirror.css');
			wp_enqueue_style('tabs_pro_admin__ambiance', wpshopmart_tabs_pro_directory_url.'assets/codex/ambiance.css');
			wp_enqueue_style('tabs_pro_admin__show-hint-css', wpshopmart_tabs_pro_directory_url.'assets/codex/show-hint.css');
			
			wp_enqueue_script('tabs_pro_admin_codemirror-js',wpshopmart_tabs_pro_directory_url.'assets/codex/codemirror.js',array('jquery'));
			wp_enqueue_script('tabs_pro_admin_css-js',wpshopmart_tabs_pro_directory_url.'assets/codex/css.js',array('jquery'));
			wp_enqueue_script('tabs_pro_admin_hint-js',wpshopmart_tabs_pro_directory_url.'assets/codex/css-hint.js',array('jquery'));
			
			wp_enqueue_script('jquery-ui-accordion');
		}
	}
	
	public function tabs_pro_register_cpt(){
		require_once('cpt-reg.php');
		add_filter( 'manage_edit-tabs_pro_columns', array(&$this, 'tabs_pro_columns' )) ;
		add_action( 'manage_tabs_pro_posts_custom_column', array(&$this, 'tabs_pro_manage_columns' ), 10, 2 );
	}
	
	function tabs_pro_columns( $columns ){
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __( 'Tabs' ),
            'shortcode' => __( 'Tabs Shortcode' ),
            'date' => __( 'Date' )
        );
        return $columns;
    }

    function tabs_pro_manage_columns( $column, $post_id ){
        global $post;
        switch( $column ) {
          case 'shortcode' :
            echo '<input style="width:225px" type="text" value="[TABS_PRO id='.$post_id.']" readonly="readonly" />';
            break;
          default :
            break;
        }
    }

    public function Extension() {
    	if (!function_exists('is_plugin_active')) {
	        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
	    }
	    if (is_plugin_active('woocommerce/woocommerce.php')) :
	       new \TABS_RES_PLUGINS\Extension\WooCommerce\WooCommerce();
	    endif;
	}
	
	// metaboxes
	public function wpsm_tabs_pro_meta_boxes_group(){
		add_meta_box('tabs_templates', __('Select Tabs Design', wpshopmart_tabs_pro_text_domain), array(&$this, 'wpsm_tabs_pro_design_select_function'), 'tabs_pro', 'normal', 'low' );
		add_meta_box('tabs_pro_add', __('Add Tabs Panel', wpshopmart_tabs_pro_text_domain), array(&$this, 'wpsm_add_tabs_pro_meta_box_function'), 'tabs_pro', 'normal', 'low' );
		add_meta_box ('tabs_pro_shortcode', __('Tabs Shortcode', wpshopmart_tabs_pro_text_domain), array(&$this, 'wpsm_pic_tabs_pro_shortcode'), 'tabs_pro', 'normal', 'low');
		//add_meta_box('tabs_pro_preview', __('Check Preview', wpshopmart_tabs_pro_text_domain), array(&$this, 'wpsm_tabs_pro_preview_meta_box_function'), 'tabs_pro', 'side', 'low');
		//add_meta_box('tabs_pro_rateus', __('Rate Us If You Like This Plugin', wpshopmart_tabs_pro_text_domain), array(&$this, 'wpsm_tabs_pro_rateus_meta_box_function'), 'tabs_pro', 'side', 'low');
		add_meta_box('tabs_pro_setting', __('Tabs Settings', wpshopmart_tabs_pro_text_domain), array(&$this, 'wpsm_add_tabs_pro_setting_meta_box_function'), 'tabs_pro', 'side', 'low');
		
	}
	public function wpsm_tabs_pro_design_select_function(){
		require_once('designs.php');
	}
	
	public function wpsm_add_tabs_pro_meta_box_function($post){
		require_once('add-tabs.php');
	}
	
	public function wpsm_pic_tabs_pro_shortcode(){
		?>
		<div class="wpsm_site_sidebar_widget_title">
			<h4><?php _e('Tabs Pro Shortcode',wpshopmart_tabs_pro_text_domain); ?></h4>
		</div>
		<p><?php _e("Use below shortcode in any Page/Post to publish your Tabs", wpshopmart_tabs_pro_text_domain);?></p>
		<input readonly="readonly" type="text" value="<?php echo "[TABS_PRO id=".get_the_ID()."]"; ?>">
		<?php
			 $PostId = get_the_ID();
			$Settings = unserialize(get_post_meta( $PostId, 'Tabs_pro_Settings', true));
			if(isset($Settings['custom_css'])){  
				 $custom_css   = $Settings['custom_css'];
			}
			else{
			$custom_css="";
			}		
		?>
		
		<br><br>
		<div>
			<h3>To activate widget into any widget area</H3>
			<p><a href="<?php get_site_url();?>./widgets.php" >Click Here</a>. </p>
			<p>Find <b>Tabs Pro Widget </b> and place it to your widget area. Select any Tabs from the list and then save changes.</p>
		</div>	
		
		<style>
		.customcss-title {
			background: #31a3dd;
			padding: 24px;
			margin: 0px;
			font-weight: 900;
			color: #fff;
			font-size: 29px;
			text-align: center;
			text-transform: uppercase;
		}
		</style>
		<h3 class="customcss-title">Custom Css</h3>
		<textarea name="custom_css" id="custom_css" style="width:100% !important ;height:300px;background:#ECECEC;"><?php echo $custom_css ; ?></textarea>
		<p style="
				background: #000;
				margin: 0px;
				text-align: Center;
				color: #fff;
				padding: 10px;
			">Enter Css without <strong>&lt;style&gt; &lt;/style&gt; </strong> tag
		</p>
		<br>
		<?php if(isset($Settings['custom_css'])){ ?> 
		<h3>Add This Tab settings as default setting for new tabs</h3>
		<div class="">
			<a  class="button button-primary button-hero" name="updte_wpsm_tabs_r_default_settings" id="updte_wpsm_tabs_r_default_settings" onclick="wpsm_update_default()">Update Default Settings</a>
		</div>	
		<?php } ?>
		<script>
		  var editor = CodeMirror.fromTextArea(document.getElementById("custom_css"), {
		   lineNumbers: true,
		   styleActiveLine: true,
			matchBrackets: true,
			hint:true,
			theme : 'ambiance',
			extraKeys: {"Ctrl-Space": "autocomplete"},
		  });
	  
		</script>
		<?php 
	}
	
	public function wpsm_tabs_pro_preview_meta_box_function($post){
		?>
		Please update first then check preview
		<br><br>
		<style>
		.wpsm_panel-body, .wpsm_panel-title{
			
			text-align: initial !important;
		}
		</style>
		<?php 
			$PostId = get_the_ID();
			$Settings = unserialize(get_post_meta( $PostId, 'Tabs_pro_Settings', true));
			if(isset($Settings['custom_css']))
			{
			?> 
				<a type="button" class="button button-primary button-hero" data-remodal-target="preview_modal" href="#"  >Check Preview </a>
				<div class="remodal" data-remodal-options=" closeOnOutsideClick: false" data-remodal-id="preview_modal" role="dialog" aria-labelledby="modal1Title" aria-describedby="modal1Desc">
					<button data-remodal-action="close" class="remodal-close" aria-label="Close"></button>
					<div>
						<h2 id="modal1Title">Tabs Pro Preview</h2>
						
						<?php
						$WPSM_Tabs_ID = $post->ID;;
						require_once('preview/content.php'); ?>
						<br>
						<h3>Note : Any Shortcode are not detect in Preview mode. That's not mean it's not work, Check plugin on your page or post.  </h3>
					
					</div>
					<br>
					<button data-remodal-action="confirm" class="remodal-confirm" >OK</button>
				</div>
			<?php 
			}
	}
	
	public function wpsm_add_tabs_pro_setting_meta_box_function($post){
		require_once('settings.php');
	}
	
	
	public function add_tabs_pro_meta_box_save($PostID) {
		require('data-post/tabs-save-data.php');
    }
	
	public function tabs_pro_settings_meta_box_save($PostID){
		require('data-post/tabs-settings-save-data.php');
	}
	
	
}
global $wpsm_tabs_pro;
$wpsm_tabs_pro = wpsm_tabs_pro::forge();

 ?>