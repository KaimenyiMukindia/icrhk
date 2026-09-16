<?php
class wpsm_service_box_pro {
	private static $instance;
    public static function forge() {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }

	private function __construct() {
		//1. adding script
		add_action('admin_enqueue_scripts', array(&$this, 'service_box_admin_scripts'));
		if (is_admin()) {
			//2. register CPT
			add_action( 'init', array(&$this, 'service_box_custom_post_type'), 1);

			//4 .Adding Meta Boxes (Displaying Meta box at back end)
			add_action('add_meta_boxes',array(&$this, 'service_box_dynamic_meta_box'));
			
			//6. saving data of back end(of meta boxes)
			add_action('save_post', array(&$this, 'fn_service_box_save_contents'), 9, 1);
			add_action('save_post', array(&$this, 'fn_service_box_save_settings'), 9, 1);
		}
	}
		

		//1. adding script
		public function service_box_admin_scripts()
		{	
			if(get_post_type()=="servicebox"){
			require_once('script.php');
			}
		}

		//2. register CPT
		public function service_box_custom_post_type()
		{
			require_once('cpt_reg.php');
			//3. manage column 
			add_filter( 'manage_servicebox_posts_columns',array(&$this, 'service_box_columns' ) ) ;
			add_action( 'manage_servicebox_posts_custom_column',array(&$this, 'service_box_manage_columns' ), 10, 2);
		}

		//3. manage column 
		function service_box_columns( $columns )
		{
			$columns = array(
				'cb' => '<input type="checkbox" />',
				'title' => __( 'Service Box' ),
				'shortcode' => __( 'Service Box Shortcode' ),
				'date' => __( 'Date' )
			);
			return $columns;
		}
			
		function service_box_manage_columns( $column){
			global $post;
			$post_id=get_the_ID();
			switch( $column ) {
			  case 'shortcode' :
				echo '<input style="width:225px" type="text" value="[servicebox_sc id='.$post_id.']" readonly="readonly" />';
				break;
			  default :
				break;
			}
		}

		//4 .Adding Meta Boxes (Displaying Meta box at back end)	
		public function service_box_dynamic_meta_box()
		{
			add_meta_box('service_box_main_meta_box','Service Box',array(&$this, 'fn_service_box_main_meta_box'),'servicebox','normal','high');
			add_meta_box('service_box_main_box_right','Service Box Setting',array(&$this, 'service_box_main_meta_box_setting'),'servicebox','side','low');
			add_meta_box('service_box_pro_shortcode', 'Service Box Shortcode', array(&$this, 'service_box_pro_shortcode'), 'servicebox', 'normal', 'low');
		}
		
			public function fn_service_box_main_meta_box($post)
			{
				require_once('add-service-box.php');
			}
			public function service_box_main_meta_box_setting($post)
			{
				require_once('settings.php');
			}
		
		
		
		//6. saving data of back end(of meta boxes)
		public function fn_service_box_save_contents($PostId)
		{
		require_once('data-post/save-data.php');

		}

		public function fn_service_box_save_settings($PostId)
		{
		require_once('data-post/save-settings.php');

		}
		
		public function service_box_pro_shortcode(){
		?>
		<div class="wpsm_site_sidebar_widget_title" style="margin-top: 20px;">
			<h4><?php _e('Tabs Pro Shortcode',wpshopmart_service_box_pro_text_domain); ?></h4>
		</div>
		<p><?php _e("Use below shortcode in any Page/Post to publish your Service Box", wpshopmart_service_box_pro_text_domain);?></p>
		<input readonly="readonly" type="text" value="<?php echo "[servicebox_sc id=".get_the_ID()."]"; ?>">
		<?php
		 $PostId = get_the_ID();
		$Settings = unserialize(get_post_meta( $PostId, 'service_box_settings', true));
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
			<p>Find <b>Service Box Pro Widget </b> and place it to your widget area. Select any Service Box from the list and then save changes.</p>
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
			">Enter Css without <strong>&lt;style&gt; &lt;/style&gt; </strong> tag</p>
		<br>
		<?php if(isset($Settings['custom_css'])){ ?> 
		<h3>Add This Service Box settings as default setting for new Service Boxes</h3>
		<div class="">
			<a  class="button button-primary button-hero" name="updte_wpsm_tabs_r_default_settings" id="updte_wpsm_tabs_r_default_settings" onclick="wpsm_sb_update_default()">Update Default Settings</a>
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

} //class end
global $wpsm_service_box_pro;
$wpsm_service_box_pro = wpsm_service_box_pro::forge();
?>