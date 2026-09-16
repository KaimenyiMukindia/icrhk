<?php 
add_action('plugins_loaded', 'wpsm_service_box_pro_tr');
function wpsm_service_box_pro_tr() 
{
	load_plugin_textdomain( wpshopmart_service_box_pro_text_domain, FALSE, dirname( plugin_basename(__FILE__)).'/languages/' );
}


/******** Front end script & Styles *******************************/								
require_once('front-scripts.php');

add_filter('widget_text', 'do_shortcode');
//add_action('media_buttons_context', 'wpsm_service_box_pro_editor_popup_content_button');
//add_action('admin_footer', 'wpsm_service_box_pro_editor_popup_content');

function wpsm_service_box_pro_editor_popup_content_button($context) 
{
	  $img = service_box_directory_url.'assets/images/wpsm_service_box_pro.png';
	  $container_id = 'SERVICE_BOX_PRO';
	  $title = 'Select Service Boxes to insert into post';
	  $context .= '<style>.wp_sb_pro_shortcode_button {
					background: #11CAA5 !important;
					border-color: #11CAA5 #11CAA5 #11CAA5 !important;
					-webkit-box-shadow: 0 1px 0 #11CAA5 !important;
					box-shadow: 0 1px 0 #11CAA5 !important;
					color: #fff;
					text-decoration: none;
					text-shadow: 0 -1px 1px #11CAA5 ,1px 0 1px #11CAA5,0 1px 1px #11CAA5,-1px 0 1px #11CAA5 !important;
					}</style>
					<a class="button button-primary wp_sb_pro_shortcode_button thickbox" title="Select Service Boxes to insert into post"    href="#TB_inline?width=400&inlineId='.$container_id.'">
						<span class="wp-media-buttons-icon" style="background: url('.$img.'); background-repeat: no-repeat; background-position: left bottom;"></span>
					Service Box Pro Shortcode
					</a>';
	  return $context;
}

function wpsm_service_box_pro_editor_popup_content() 
{
	?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#wpsm_service_box_pro_insert').on('click', function() {
			var id = jQuery('#wpsm_service_box_pro_insertselect option:selected').val();
			window.send_to_editor('<p>[servicebox_sc id=' + id + ']</p>');
			tb_remove();
		})
	});
	</script>
	
	<style>
	.wp_tabs_r_shortcode_button {
		background: #11CAA5; !important;
		border-color: #11CAA5; #11CAA5 #11CAA5 !important;
		-webkit-box-shadow: 0 1px 0 #11CAA5 !important;
		box-shadow: 0 1px 0 #11CAA5 !important;
		color: #fff !important;
		text-decoration: none;
		text-shadow: 0 -1px 1px #11CAA5 ,1px 0 1px #11CAA5,0 1px 1px #11CAA5,-1px 0 1px #11CAA5 !important;
	}
	</style>
	<div id="SERVICE_BOX_PRO" style="display:none;">
	  <h3>Select Service Boxes To Insert Into Post</h3>
	  <?php 
		
		$all_posts = wp_count_posts( 'servicebox')->publish;
		$args = array('post_type' => 'servicebox', 'posts_per_page' =>$all_posts);
		global $All_rac;
		$All_rac = new WP_Query( $args );			
		if( $All_rac->have_posts() ) { ?>	
			<select id="wpsm_service_box_pro_insertselect" style="width: 100%;margin-bottom: 20px;">
				<?php
				while ( $All_rac->have_posts() ) : $All_rac->the_post(); ?>
				<?php $title = get_the_title(); ?>
				<option value="<?php echo get_the_ID(); ?>"><?php if (strlen($title) == 0) echo 'No Title Found'; else echo $title;   ?></option>
				<?php
				endwhile; 
				?>
			</select>
			<button class='button primary wp_tabs_pro_shortcode_button' id='wpsm_service_box_pro_insert'><?php _e('Insert Service Boxes Shortcode', wpshopmart_service_box_pro_text_domain); ?></button>
			<?php
		} else {
			_e('No Service Box Found', wpshopmart_service_box_pro_text_domain);
		}
		?>
	</div>
	<?php
}
?>