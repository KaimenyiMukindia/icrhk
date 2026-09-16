<?php 
  $De_Settings = unserialize(get_option('Tabs_pro_default_Settings'));
  $PostId = $post->ID;
  $Settings = unserialize(get_post_meta( $PostId, 'Tabs_pro_Settings', true));
	if(!isset($De_Settings['tabs_number'])){
		$De_Settings['tabs_number'] = 1;
	}
	$option_names = array(
	"templates_presets"	=> $De_Settings['templates_presets'],
	"templates_h"   => $De_Settings['templates_h'],
	"templates_v"   => $De_Settings['templates_v'],
	"tab_ind_clr_enable"	=> $De_Settings['tab_ind_clr_enable'],
	"tabs_title_bg_clr"   => $De_Settings['tabs_title_bg_clr'],
	"select_tabs_bg_clr"   => $De_Settings['select_tabs_bg_clr'],
	"tabs_title_font_clr" => $De_Settings['tabs_title_font_clr'],
	"select_tabs_title_clr" => $De_Settings['select_tabs_title_clr'],
	"tabs_title_icon_clr" => $De_Settings['tabs_title_icon_clr'],
	"select_tabs_icon_clr" => $De_Settings['select_tabs_icon_clr'],
	"tabs_desc_font_clr"  => $De_Settings['tabs_desc_font_clr'],
	"tabs_desc_bg_clr"    => $De_Settings['tabs_desc_bg_clr'],
	"tabs_border_color"      =>$De_Settings['tabs_border_color'],
	"tabs_btn_border_color"    => $De_Settings['tabs_btn_border_color'],
	"select_tabs_btn_border_color"      =>$De_Settings['select_tabs_btn_border_color'],
	
	"tabs_title_size"         => $De_Settings['tabs_title_size'],
	"tabs_title_icon_font_weight" => $De_Settings['tabs_title_icon_font_weight'],
	"tabs_icon_size"         => $De_Settings['tabs_icon_size'],
	"tabs_des_size"     		 => $De_Settings['tabs_des_size'],
	"tabs_border_size"         => $De_Settings['tabs_border_size'],
	"font_family_group"     	=> $De_Settings['font_family_group'],
	"all_tabs_font_family"     	 => $De_Settings['all_tabs_font_family'],
	
	"show_tabs_title_icon" => $De_Settings['show_tabs_title_icon'],
	"tabs_button_align" => $De_Settings['tabs_button_align'],
	"show_tabs_icon_postion" => $De_Settings['show_tabs_icon_postion'],
	"show_tabs_icon_align" => $De_Settings['show_tabs_icon_align'],
	"tabs_icon_format" => $De_Settings['tabs_icon_format'],
	"tabs_custom_image_size" => $De_Settings['tabs_custom_image_size'],
	"tab_img_icon_w" => $De_Settings['tab_img_icon_w'],
	"tab_img_icon_h" => $De_Settings['tab_img_icon_h'],
	
	"tabs_mob_disply_option" => $De_Settings['tabs_mob_disply_option'],
	"tabs_styles"      =>$De_Settings['tabs_styles'],
	"tabs_desc_animation"      =>$De_Settings['tabs_desc_animation'],
	"tabs_content_height_option"      =>$De_Settings['tabs_content_height_option'],
	"tabs_content_height"      =>$De_Settings['tabs_content_height'],
	"tabs_content_bar_bg_clr"	=>$De_Settings['tabs_content_bar_bg_clr'],
	"tabs_content_bar_hndl_bg_clr" =>$De_Settings['tabs_content_bar_hndl_bg_clr'],
	"tabs_content_bar_width"	=>$De_Settings['tabs_content_bar_width'],
	"tabs_button_width_option"      =>$De_Settings['tabs_button_width_option'],
	"tabs_button_width"      =>$De_Settings['tabs_button_width'],
	"tabs_on_hover"      =>$De_Settings['tabs_on_hover'],
	"tabs_number"      =>$De_Settings['tabs_number'],
	"custom_css"      =>$De_Settings['custom_css']
	);
		
		foreach($option_names as $option_name => $default_value) {
			if(isset($Settings[$option_name])) 
				${"" . $option_name}  = $Settings[$option_name];
			else
				${"" . $option_name}  = $default_value;
		}
?>
<Script>
//Title size script
  jQuery(function() {
    jQuery( "#tabs_title_size_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 50,
		min:8,
		slide: function( event, ui ) {
		jQuery( "#tabs_title_size" ).val( ui.value );
      }
		});
		
		jQuery( "#tabs_title_size_id" ).slider("value",<?php if(isset ($tabs_title_size)) { echo $tabs_title_size ;} else {echo "19"; } ?> );
		jQuery( "#tabs_title_size" ).val( jQuery( "#tabs_title_size_id" ).slider( "value") );
    
  });
</script>

<Script>
//Icon size script
  jQuery(function() {
    jQuery( "#tabs_icon_size_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 50,
		min:8,
		slide: function( event, ui ) {
		jQuery( "#tabs_icon_size" ).val( ui.value );
      }
		});
		
		jQuery( "#tabs_icon_size_id" ).slider("value",<?php if(isset ($tabs_icon_size)) { echo $tabs_icon_size ;} else {echo "19"; } ?> );
		jQuery( "#tabs_icon_size" ).val( jQuery( "#tabs_icon_size_id" ).slider( "value") );
    
  });
</script>

<Script>
//Border size script
  jQuery(function() {
    jQuery( "#tabs_border_size_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 30,
		min:0,
		slide: function( event, ui ) {
		jQuery( "#tabs_border_size" ).val( ui.value );
      }
		});
		
		jQuery( "#tabs_border_size_id" ).slider("value",<?php if(isset ($tabs_border_size)) { echo $tabs_border_size ; } else { echo "19"; } ?>);
		jQuery( "#tabs_border_size" ).val( jQuery( "#tabs_border_size_id" ).slider( "value") );
    
  });
</script>  

<Script>
//Description size script
  jQuery(function() {
    jQuery( "#tabs_des_size_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 30,
		min:5,
		slide: function( event, ui ) {
		jQuery( "#tabs_des_size" ).val( ui.value );
      }
		});
		
		jQuery( "#tabs_des_size_id" ).slider("value",<?php if(isset ($tabs_des_size)) { echo $tabs_des_size ; } else { echo "19"; } ?>);
		jQuery( "#tabs_des_size" ).val( jQuery( "#tabs_des_size_id" ).slider( "value") );
    
  });
</script> 

<Script>
//Font Weight size script
  jQuery(function() {
    jQuery( "#tabs_title_icon_font_weight_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 900,
		min:100,
		slide: function( event, ui ) {
		jQuery( "#tabs_title_icon_font_weight" ).val( ui.value );
      }
		});
		
		jQuery( "#tabs_title_icon_font_weight_id" ).slider("value",<?php if(isset ($tabs_title_icon_font_weight)) { echo $tabs_title_icon_font_weight ; } else { echo "19"; } ?>);
		jQuery( "#tabs_title_icon_font_weight" ).val( jQuery( "#tabs_title_icon_font_weight_id" ).slider( "value") );
    
  });
</script> 

<Script>
//Scroll BAr Width script
  jQuery(function() {
    jQuery( "#tabs_content_bar_width_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 15,
		min:3,
		slide: function( event, ui ) {
		jQuery( "#tabs_content_bar_width" ).val( ui.value );
      }
		});
		
		jQuery( "#tabs_content_bar_width_id" ).slider("value",<?php if(isset ($tabs_content_bar_width)) { echo $tabs_content_bar_width ; } else { echo "6"; } ?>);
		jQuery( "#tabs_content_bar_width" ).val( jQuery( "#tabs_content_bar_width_id" ).slider( "value") );
		jQuery( "#tabs_content_bar_width" ).val( jQuery( "#tabs_content_bar_width_id" ).slider( "value") );
    
  });
</script> 

<Script>
function wpsm_update_default(){
	 jQuery.ajax({
		url: location.href,
		type: "POST",
		data : {
			    'tabs_pro_action123':'default_settins_action',
			     },
                success : function(data){
									alert("Default Settings Updated");
									location.reload(true);
                                   }	
	});
	
}
</script>
<Script>
function hide_color_setting(){
				
			 value = jQuery("input[name=tab_ind_clr_enable]:checked").val();
			 
			 if(value=="no"){
				//jQuery(".tabs_ind_clr_enable_class").show(500);
				jQuery(".tabs_ind_clr_option_class").hide(500);
				
			}else{
				
				//jQuery(".tabs_ind_clr_enable_class").hide(500);
				jQuery(".tabs_ind_clr_option_class").show(500);
			}
			
		}
function get_font_group(){
	
	 var family_group = jQuery('#all_tabs_font_family option:selected').closest('optgroup').prop('label');
		jQuery("#font_family_group").val(family_group);
		//alert(family_group);
	}	
</Script>

<Script>
// Hide and show icon Settings
function fn_tabs_icon_setting()
{
	 btn_dis_option_value = jQuery("input[name=show_tabs_title_icon]:checked").val();
	 btn_icon_format_value = jQuery("input[name=tabs_icon_format]:checked").val();
	 btn_icon_img_cus_h_w_value = jQuery("input[name=tabs_custom_image_size]:checked").val();
	 
	 switch(btn_dis_option_value)
	 {
		case "1":
			
			jQuery(".tabs_icon_settings_cls").show(500);
			if(btn_icon_format_value=='image')
			{
				jQuery(".icon_img_size_settings_cls").show(500);
				jQuery(".icon_icon_cls").hide(500);
				
				jQuery(".icon_img_cls").show(500);
				if(btn_icon_img_cus_h_w_value=='3') 
					jQuery(".icon_img_custum_h_W").show(500);
				else
					jQuery(".icon_img_custum_h_W").hide(500);
			}
			else
			{
				jQuery(".icon_img_size_settings_cls").hide(500);
				jQuery(".icon_icon_cls").show(500);
				jQuery(".icon_img_cls").hide(500);
			}
		break;
		case "2":
			jQuery(".tabs_icon_settings_cls").hide(500);
			jQuery(".icon_icon_cls").hide(500);
			jQuery(".icon_img_size_settings_cls").hide(500);
		break;
		case "3":
			jQuery(".tabs_icon_pos_and_align_cls").hide(500);
			jQuery(".tabs_icon_format_cls").show(500);
			if(btn_icon_format_value=='image')
			{
				jQuery(".icon_img_size_settings_cls").show(500);
				jQuery(".icon_icon_cls").hide(500);
				if(btn_icon_img_cus_h_w_value=='3') 
					jQuery(".icon_img_custum_h_W").show(500);
				else
					jQuery(".icon_img_custum_h_W").hide(500);
			}
			else
			{
				jQuery(".icon_img_size_settings_cls").hide(500);
				jQuery(".icon_icon_cls").show(500);
			}
		break;
	 }
	 
}
</Script>
<Script>
// Hide and show Content Custum Height and Scroll Bar Setting
function fn_tabs_contents_height_setting()
{
	 tabs_contents_height_set_val = jQuery("input[name=tabs_content_height_option]:checked").val();
	 
	 switch(tabs_contents_height_set_val)
	 {
		case "1":
			jQuery(".tabs_contents_height_setting_cls").hide(500);
		break;
		case "2":
			jQuery(".tabs_contents_height_setting_cls").show(500);
		break;
	 }
	 
}
		
</Script>
<?php

if(isset($_POST['tabs_pro_action123']) == "default_settins_action")
	{
	
		$Settings_Array2 = serialize( array(
				"templates_presets"	=> $templates_presets,
				"templates_h"	=> $templates_h,
				"templates_v"	=> $templates_v,
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
				"font_family_group"     	=> $font_family_group,
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
				"tabs_content_bar_bg_clr"      => $tabs_content_bar_bg_clr,
				"tabs_content_bar_hndl_bg_clr"      => $tabs_content_bar_hndl_bg_clr,
				"tabs_content_bar_width"      => $tabs_content_bar_width,
				"tabs_button_width_option"      => $tabs_button_width_option,
				"tabs_button_width"      => $tabs_button_width,
				"tabs_on_hover"      => $tabs_on_hover,
				"tabs_number"      => $tabs_number,
				"custom_css"      =>$custom_css
				) );

			update_option('Tabs_pro_default_Settings', $Settings_Array2);
}
 ?>
 <style>
 .ac_tooltip{
	 display:none;
 }
 .wpsm_site_sidebar_widget_title2 {
    margin-left: 9px;
    margin-right: 2px;
    background: #31a3dd;
    padding: 10px;
    font-size: 20px;
    text-transform: uppercase;
    text-align: center;
    border-bottom: 9px double #FFF;
    box-shadow: 8px 8px 15px rgba(0,0,0,.4);
    margin-bottom: 10px;
}
.wpsm_site_sidebar_widget_title2 h5 {
    color: #fff !important;
    margin: 0px !important;
}
.wp-color-result{
height:24px;
}
.selected_label_color {
    color: #31a3dd;
}
 </style>
<input type="hidden" name="tabs_pro_setting_save_action" value="tabs_pro_setting_save_action" />	
<table class="form-table acc_table">
	<tbody>
		<tr style="border-bottom:0px;">
			<th>
				<div class="wpsm_site_sidebar_widget_title2">
					<h5>Tab Color Settings</h5>
				</div>
			</th>
		</tr>
		<tr>
			<th scope="row"><label><?php _e('Enable Individual Color Option ',wpshopmart_tabs_pro_text_domain); ?></label>
			<a  class="ac_tooltip" href="#help" data-tooltip="#cb_ind_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
			
			</th>
			<td>
				<div class="switch">
					<input type="radio" class="switch-input" name="tab_ind_clr_enable" value="yes" id="enable_tab_ind_clr_enable" <?php if($tab_ind_clr_enable== 'yes'){echo "checked";} ?>  onchange="hide_color_setting()">
					<label for="enable_tab_ind_clr_enable" class="switch-label switch-label-off"><?php _e('Yes',wpshopmart_tabs_pro_text_domain); ?></label>
					<input type="radio" class="switch-input" name="tab_ind_clr_enable" value="no" id="disable_tab_ind_clr_enable" <?php if($tab_ind_clr_enable== 'no'){echo "checked";} ?>  onchange="hide_color_setting()">
					<label for="disable_tab_ind_clr_enable" class="switch-label switch-label-on"><?php _e('No',wpshopmart_tabs_pro_text_domain); ?></label>
					<span class="switch-selection"></span>
				</div>
				<!-- Tooltip -->
				<div id="cb_ind_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Enable your individual color settings from here, if you want to show different color for each box',wpshopmart_tabs_pro_text_domain); ?></h2>
					</div>
		    	</div>
			</td>
		</tr>
		<tr >
			<th scope="row"><label><?php _e('Tabs Button Background Color',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<input id="tabs_title_bg_clr" name="tabs_title_bg_clr" type="text" value="<?php echo $tabs_title_bg_clr; ?>" class="my-color-field" data-default-color="#e8e8e8" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_title_bg_clr_tp">help</a>
				<div id="tabs_r_title_bg_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Tabs Title Background Colour',wpshopmart_tabs_pro_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/tabs-bg.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		<tr>
			<th scope="row"><label><span class="selected_label_color"><?php _e('Selected/Hover ',wpshopmart_tabs_pro_text_domain); ?></span><?php _e('Tabs Button Background Color',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<input id="select_tabs_bg_clr" name="select_tabs_bg_clr" type="text" value="<?php echo $select_tabs_bg_clr; ?>" class="my-color-field" data-default-color="#e8e8e8" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_sel_bg_clr_tp">help</a>
				<div id="tabs_r_sel_bg_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Selected/Open Tabs Title Background Colour',wpshopmart_tabs_pro_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/sel-tab-color.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		<tr>
			<th scope="row"><label><?php _e('Tabs Title Color',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<input id="tabs_title_font_clr" name="tabs_title_font_clr" type="text" value="<?php echo $tabs_title_font_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_title_icon_clr_tp">help</a>
				<div id="tabs_r_title_icon_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Tabs Title Color',wpshopmart_tabs_pro_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/tabs-ft-color.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		<tr >
			<th scope="row"><label><span class="selected_label_color"><?php _e('Selected/Hover ',wpshopmart_tabs_pro_text_domain); ?></span><?php _e('Tabs Title Color',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<input id="select_tabs_title_clr" name="select_tabs_title_clr" type="text" value="<?php echo $select_tabs_title_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_sel_icon_clr_tp">help</a>
				<div id="tabs_r_sel_icon_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Selected/Open Tabs Title/Icon Font Colour',wpshopmart_tabs_pro_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/tabs-ft-color.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr >
			<th scope="row"><label><?php _e('Tabs Icon Color',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<input id="tabs_title_icon_clr" name="tabs_title_icon_clr" type="text" value="<?php echo $tabs_title_icon_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_title_icon_clr_tp">help</a>
				<div id="tabs_r_title_icon_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Tabs Title Colour',wpshopmart_tabs_pro_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/tabs-ft-color.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		<tr >
			<th scope="row"><label><span class="selected_label_color"><?php _e('Selected/Hover ',wpshopmart_tabs_pro_text_domain); ?></span><?php _e('Tabs Icon Color',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<input id="select_tabs_icon_clr" name="select_tabs_icon_clr" type="text" value="<?php echo $select_tabs_icon_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_sel_icon_clr_tp">help</a>
				<div id="tabs_r_sel_icon_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Selected/Open Tabs Title/Icon Font Colour',wpshopmart_tabs_pro_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/tabs-ft-color.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		<tr >
			<th scope="row"><label><?php _e('Tabs Description Font Color',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<input id="tabs_desc_font_clr" name="tabs_desc_font_clr" type="text" value="<?php echo $tabs_desc_font_clr; ?>" class="my-color-field" data-default-color="#000000" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_desc_font_clr_tp">help</a>
				<div id="tabs_r_desc_font_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Tabs Description Font Colour',wpshopmart_tabs_pro_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/noise.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		<tr >
			<th scope="row"><label><?php _e('Tabs Description Background Color',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<input id="tabs_desc_bg_clr" name="tabs_desc_bg_clr" type="text" value="<?php echo $tabs_desc_bg_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_desc_bg_clr_tp">help</a>
				<div id="tabs_r_desc_bg_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Tabs Description Background Color',wpshopmart_tabs_pro_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/desc-bg-color.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		<tr >
			<th scope="row"><label><?php _e('Tabs Description Border Color',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<input id="tabs_border_color" name="tabs_border_color" type="text" value="<?php echo $tabs_border_color; ?>" class="my-color-field" data-default-color="#e8e8e8" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_sel_bg_clr_tp">help</a>
				<div id="tabs_r_sel_bg_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Selected/Open Tabs Title Background Colour',wpshopmart_tabs_pro_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/sel-tab-color.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		<tr >
			<th scope="row"><label><?php _e('Tabs Button Border Color',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<input id="tabs_btn_border_color" name="tabs_btn_border_color" type="text" value="<?php echo $tabs_btn_border_color; ?>" class="my-color-field" data-default-color="#e8e8e8" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_sel_bg_clr_tp">help</a>
				<div id="tabs_r_sel_bg_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Selected/Open Tabs Title Background Colour',wpshopmart_tabs_pro_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/sel-tab-color.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		<tr >
			<th scope="row"><label><span class="selected_label_color"><?php _e('Selected ',wpshopmart_tabs_pro_text_domain); ?></span><?php _e('Tab Button Border Color',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<input id="select_tabs_btn_border_color" name="select_tabs_btn_border_color" type="text" value="<?php echo $select_tabs_btn_border_color; ?>" class="my-color-field" data-default-color="#e8e8e8" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_sel_bg_clr_tp">help</a>
				<div id="tabs_r_sel_bg_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Selected/Open Tabs Title Background Colour',wpshopmart_tabs_pro_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/sel-tab-color.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		<tr style="border-bottom:0px;">
			<th>
				<div class="wpsm_site_sidebar_widget_title2">
					<h5>Tab Font Size & Styles Settings</h5>
				</div>
			</th>
		</tr>
		<tr class="setting_color">
			<th><?php _e('Tabs Title Font Size',wpshopmart_tabs_pro_text_domain); ?> </th>
			<td>
				<div id="tabs_title_size_id" class="size-slider" ></div>
				<input type="text" class="slider-text" id="tabs_title_size" name="tabs_title_size"  readonly="readonly">
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_title_size_tp">help</a>
				<div id="tabs_title_size_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;max-width: 300px;">
						<h2 style="color:#fff !important;">You can update Tabs Title and Icon Font Size from here. Just Scroll it to change size.</h2>
					</div>
		    	</div>
			</td>
		</tr>
		<tr class="setting_color">
			<th><?php _e('Tabs Title Font Weight',wpshopmart_tabs_pro_text_domain); ?> </th>
			<td>
				<div id="tabs_title_icon_font_weight_id" class="size-slider" ></div>
				<input type="text" class="slider-text" id="tabs_title_icon_font_weight" name="tabs_title_icon_font_weight"  readonly="readonly">
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_title_size_tp">help</a>
				<div id="tabs_title_size_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;max-width: 300px;">
						<h2 style="color:#fff !important;">You can update Tabs Title and Icon Font Size from here. Just Scroll it to change size.</h2>
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr class="setting_color">
			<th><?php _e('Tabs Icon Size',wpshopmart_tabs_pro_text_domain); ?> </th>
			<td>
				<div id="tabs_icon_size_id" class="size-slider" ></div>
				<input type="text" class="slider-text" id="tabs_icon_size" name="tabs_icon_size"  readonly="readonly">
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#title_size_tp">help</a>
				<div id="title_size_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;max-width: 300px;">
						<h2 style="color:#fff !important;">You can update Tabs Title and Icon Font Size from here. Just Scroll it to change size.</h2>
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr class="setting_color">
			<th><?php _e('Tabs Description Font Size',wpshopmart_tabs_pro_text_domain); ?> </th>
			<td>
				<div id="tabs_des_size_id" class="size-slider" ></div>
				<input type="text" class="slider-text" id="tabs_des_size" name="tabs_des_size"  readonly="readonly">
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#des_size_tp">help</a>
				<div id="des_size_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;max-width: 300px;">
						<h2 style="color:#fff !important;">You can update Tabs Description/content Font Size from here. Just Scroll it to change size.</h2>
						
					</div>
		    	</div>
			</td>
		</tr>
		<tr class="setting_color">
			<th><?php _e('Tabs Border Size',wpshopmart_tabs_pro_text_domain); ?> </th>
			<td>
				<div id="tabs_border_size_id" class="size-slider" ></div>
				<input type="text" class="slider-text" id="tabs_border_size" name="tabs_border_size"  readonly="readonly">
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#title_size_tp">help</a>
				<div id="title_size_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;max-width: 300px;">
						<h2 style="color:#fff !important;">You can update Tabs Title and Icon Font Size from here. Just Scroll it to change size.</h2>
					</div>
		    	</div>
			</td>
		</tr>
		<tr >
			<th><?php _e('Tabs Font Style/Family',wpshopmart_tabs_pro_text_domain); ?> </th>
			<td>
				<select name="all_tabs_font_family" id="all_tabs_font_family" class="standard-dropdown" style="width:100%" onchange="get_font_group()">
					<?php if(!isset($all_tabs_font_family)) $all_tabs_font_family = "Open Sans";
						require_once("all-tabs-font-family.php");
					?>	
				</select>
				<input type="hidden" name="font_family_group" id="font_family_group" value="<?php echo $font_family_group; ?>" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#font_family_tp">help</a>
				<div id="font_family_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;max-width: 300px;">
						<h2 style="color:#fff !important;">You can update Tabs Title and Description Font Family/Style from here. Select any one form these options.</h2>
					
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr style="border-bottom:0px;">
			<th>
				<div class="wpsm_site_sidebar_widget_title2">
					<h5>Tab Buttons Alignment, Display Options & Positions Settings </h5>
				</div>
			</th>
		</tr>
		<tr>
			<th scope="row"><label><?php _e('Tabs Button Alignment',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_button_align" id="tabs_button_align" value="left" <?php if($tabs_button_align == 'left' ) { echo "checked"; } ?>  /> Left </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_button_align" id="tabs_button_align" value="right"  <?php if($tabs_button_align == 'right' ) { echo "checked"; } ?>/> Right </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_button_align" id="tabs_button_align" value="center"  <?php if($tabs_button_align == 'center' ) { echo "checked"; } ?>/> Center </span>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_title_icon_align_tp">help</a>
				<div id="tabs_r_title_icon_align_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Align your Tab Icon Position before title or after title',wpshopmart_tabs_pro_text_domain); ?></h2>
					</div>
		    	</div>
			</td>
		</tr>
		<tr>
			<th scope="row"><label><?php _e('Display Option For Title and Icon ',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="show_tabs_title_icon" id="show_tabs_title_icon" value="1" <?php if($show_tabs_title_icon == '1' ) { echo "checked"; } ?> onchange="fn_tabs_icon_setting()" /> Show Tabs Title + Icon (both) </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="show_tabs_title_icon" id="show_tabs_title_icon" value="2" <?php if($show_tabs_title_icon == '2' ) { echo "checked"; } ?> onchange="fn_tabs_icon_setting()" /> Show Only Tabs Title </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="show_tabs_title_icon" id="show_tabs_title_icon" value="3" <?php if($show_tabs_title_icon == '3' ) { echo "checked"; } ?> onchange="fn_tabs_icon_setting()" /> Show Only Icon </span>
				
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_title_icon_tp">help</a>
				<div id="tabs_r_title_icon_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Display Tabs Title And Icon ',wpshopmart_tabs_pro_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/tab-title.png'; ?>">
						<br>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/tab-icon.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr class="tabs_icon_settings_cls tabs_icon_pos_and_align_cls" style="<?php if($show_tabs_title_icon=='2' || $show_tabs_title_icon=='3'){echo "display:none;";}?>">
			<th scope="row"><label><?php _e('Tabs Icon Position ',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="show_tabs_icon_postion" id="show_tabs_icon_postion" value="left" <?php if($show_tabs_icon_postion == 'left' ) { echo "checked"; } ?> /> Before Tab Title </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="show_tabs_icon_postion" id="show_tabs_icon_postion" value="right" <?php if($show_tabs_icon_postion == 'right' ) { echo "checked"; } ?>/> After Tab Title </span>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_title_icon_align_tp">help</a>
				<div id="tabs_r_title_icon_align_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Align your Tab Icon Position before title or after title',wpshopmart_tabs_pro_text_domain); ?></h2>
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr class="tabs_icon_settings_cls tabs_icon_pos_and_align_cls" style="<?php if($show_tabs_title_icon=='2' || $show_tabs_title_icon=='3'){echo "display:none;";}?>">
			<th scope="row"><label><?php _e('Tabs Icon Alignment',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="show_tabs_icon_align" id="show_tabs_icon_align" value="inline" <?php if($show_tabs_icon_align == 'inline' ) { echo "checked"; } ?> /> Inline </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="show_tabs_icon_align" id="show_tabs_icon_align" value="block"  <?php if($show_tabs_icon_align == 'block' ) { echo "checked"; } ?>/> Block </span>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_title_icon_align_tp">help</a>
				<div id="tabs_r_title_icon_align_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Align your Tab Icon Position before title or after title',wpshopmart_tabs_pro_text_domain); ?></h2>
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr class="tabs_icon_settings_cls tabs_icon_format_cls" style="<?php if($show_tabs_title_icon=='2'){echo "display:none;";}?>">
			<th scope="row"><label><?php _e('Select Icon Format',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_icon_format" id="tabs_icon_format" value="image"  <?php if($tabs_icon_format == 'image' ) { echo "checked"; } ?> onchange="fn_tabs_icon_setting()" /> Custom Image Icons </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_icon_format" id="tabs_icon_format" value="icon" <?php if($tabs_icon_format == 'icon' ) { echo "checked"; } ?> onchange="fn_tabs_icon_setting()" /> Font Awesome/ Dash/ </br><span style="padding-left:23px;">Glyphicon Icons</span>  </span>
				
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_title_icon_align_tp">help</a>
				<div id="tabs_r_title_icon_align_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Align your Tab Icon Position before title or after title',wpshopmart_tabs_pro_text_domain); ?></h2>
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr class="icon_img_size_settings_cls" style="<?php if($show_tabs_title_icon=='2' || $tabs_icon_format=='icon'){echo "display:none;";}?>">
			<th scope="row"><label><?php _e('Custom Image Icon Size',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_custom_image_size" id="tabs_custom_image_size" value="1" <?php if($tabs_custom_image_size == '1' ) { echo "checked"; } ?> onchange="fn_tabs_icon_setting()" /> Default Image Size </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_custom_image_size" id="tabs_custom_image_size" value="2"  <?php if($tabs_custom_image_size == '2' ) { echo "checked"; } ?> onchange="fn_tabs_icon_setting()" /> Thumbnail</span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_custom_image_size" id="tabs_custom_image_size" value="3"  <?php if($tabs_custom_image_size == '3' ) { echo "checked"; } ?> onchange="fn_tabs_icon_setting()" /> Custom</span>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_title_icon_align_tp">help</a>
				<div id="tabs_r_title_icon_align_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Align your Tab Icon Position before title or after title',wpshopmart_tabs_pro_text_domain); ?></h2>
					</div>
		    	</div>
				<span class="icon_img_custum_h_W" style="<?php if($tabs_custom_image_size=='1' || $tabs_custom_image_size=='2'){echo "display:none;";}?>"> Width &nbsp; <input type="number" name="tab_img_icon_w" value="<?php echo $tab_img_icon_w; ?>" /> Px</span> <br />
				<span class="icon_img_custum_h_W" style="<?php if($tabs_custom_image_size=='1' || $tabs_custom_image_size=='2'){echo "display:none;";}?>"> Height  &nbsp;<input type="number" name="tab_img_icon_h" value="<?php echo $tab_img_icon_h; ?>"/> Px</span> 
			</td>
		</tr>
		<tr style="border-bottom:0px;">
			<th>
				<div class="wpsm_site_sidebar_widget_title2">
					<h5>Other Settings</h5>
				</div>
			</th>
		</tr>
		<tr style="display:none">
			<th scope="row"><label><?php _e('Tabs Mobile Display Option',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_mob_disply_option" id="tabs_mob_disply_option" value="1" <?php if($tabs_mob_disply_option == '1' ) { echo "checked"; } ?> /> As Accordion </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_mob_disply_option" id="tabs_mob_disply_option" value="2" <?php if($tabs_mob_disply_option == '2' ) { echo "checked"; } ?> />   As Tabs </span>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_styles_tp">help</a>
				<div id="tabs_r_styles_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Tab Styles',wpshopmart_tabs_pro_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/tab-title.png'; ?>">
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/soft.png'; ?>">
						<br>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/noise.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr>
			<th scope="row"><label><?php _e('Tabs Button Overlay Styles',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_styles" id="tabs_styles" value="1" <?php if($tabs_styles == '1' ) { echo "checked"; } ?> /> Default </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_styles" id="tabs_styles" value="2" <?php if($tabs_styles == '2' ) { echo "checked"; } ?>  /> Soft </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_styles" id="tabs_styles" value="3"  <?php if($tabs_styles == '3' ) { echo "checked"; } ?> /> Noise </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_styles" id="tabs_styles" value="4"  <?php if($tabs_styles == '4' ) { echo "checked"; } ?> /> Bubble </span>
				<span style="display:block"><input type="radio" name="tabs_styles" id="tabs_styles" value="5"  <?php if($tabs_styles == '5' ) { echo "checked"; } ?> /> Glass </span>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_styles_tp">help</a>
				<div id="tabs_r_styles_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Tab Styles',wpshopmart_tabs_pro_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/tab-title.png'; ?>">
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/soft.png'; ?>">
						<br>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/noise.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		<tr >
			<th><?php _e('Tabs Description Animation',wpshopmart_tabs_pro_text_domain); ?> </th>
			<td>
				<select name="tabs_desc_animation" id="tabs_desc_animation" class="standard-dropdown" style="width:100%" >
					<option value="0"  				<?php if($tabs_desc_animation == '0' ) { echo "selected"; } ?> >No Animation</option>
					<option value="fadeIn"  		<?php if($tabs_desc_animation == 'fadeIn' ) { echo "selected"; } ?> >fadeIn</option>
					<option value="fadeInLeft"    	<?php if($tabs_desc_animation == 'fadeInLeft' ) { echo "selected"; } ?> >fadeInLeft</option>
					<option value="fadeInRight"    	<?php if($tabs_desc_animation == 'fadeInRight' ) { echo "selected"; } ?> >fadeInRight</option>
					<option value="fadeInUp"    	<?php if($tabs_desc_animation == 'fadeInUp' ) { echo "selected"; } ?> >fadeInUp</option>
					<option value="fadeInDown"    	<?php if($tabs_desc_animation == 'fadeInDown' ) { echo "selected"; } ?> >fadeInDown</option>
					<option value="flip"    		<?php if($tabs_desc_animation == 'flip' ) { echo "selected"; } ?> >flip</option>
					<option value="flipInX"    		<?php if($tabs_desc_animation == 'flipInX' ) { echo "selected"; } ?> >flipInX</option>
					<option value="flipInY"    		<?php if($tabs_desc_animation == 'flipInY' ) { echo "selected"; } ?> >flipInY</option>
					<option value="flipOutX"    	<?php if($tabs_desc_animation == 'flipOutX' ) { echo "selected"; } ?> >flipOutX</option>
					<option value="flipOutY"    	<?php if($tabs_desc_animation == 'flipOutY' ) { echo "selected"; } ?> >flipOutY</option>
					<option value="zoomIn"    		<?php if($tabs_desc_animation == 'zoomIn' ) { echo "selected"; } ?> >ZoomIn</option>
					<option value="zoomInLeft"    	<?php if($tabs_desc_animation == 'zoomInLeft' ) { echo "selected"; } ?> >ZoomInLeft</option>
					<option value="zoomInRight"    	<?php if($tabs_desc_animation == 'zoomInRight' ) { echo "selected"; } ?> >ZoomInRight</option>
					<option value="zoomInUp"    	<?php if($tabs_desc_animation == 'zoomInUp' ) { echo "selected"; } ?> >ZoomInUp</option>
					<option value="zoomInDown"    	<?php if($tabs_desc_animation == 'zoomInDown' ) { echo "selected"; } ?> >ZoomInDown</option>
					<option value="bounce"    		<?php if($tabs_desc_animation == 'bounce' ) { echo "selected"; } ?> >bounce</option>
					<option value="bounceIn"    	<?php if($tabs_desc_animation == 'bounceIn' ) { echo "selected"; } ?> >bounceIn</option>
					<option value="bounceInLeft"    <?php if($tabs_desc_animation == 'bounceInLeft' ) { echo "selected"; } ?> >bounceInLeft</option>
					<option value="bounceInRight"   <?php if($tabs_desc_animation == 'bounceInRight' ) { echo "selected"; } ?> >bounceInRight</option>
					<option value="bounceInUp"    	<?php if($tabs_desc_animation == 'bounceInUp' ) { echo "selected"; } ?> >bounceInUp</option>
					<option value="bounceInDown"    <?php if($tabs_desc_animation == 'bounceInDown' ) { echo "selected"; } ?> >bounceInDown</option>
					<option value="flash"    		<?php if($tabs_desc_animation == 'flash' ) { echo "selected"; } ?> >flash</option>
					<option value="pulse"    		<?php if($tabs_desc_animation == 'pulse' ) { echo "selected"; } ?> >pulse</option>
					<option value="shake"    		<?php if($tabs_desc_animation == 'shake' ) { echo "selected"; } ?> >shake</option>
					<option value="swing"    		<?php if($tabs_desc_animation == 'swing' ) { echo "selected"; } ?> >swing</option>
					<option value="tada"    		<?php if($tabs_desc_animation == 'tada' ) { echo "selected"; } ?> >tada</option>
					<option value="wobble"    		<?php if($tabs_desc_animation == 'wobble' ) { echo "selected"; } ?> >wobble</option>
					<option value="lightSpeedIn"    <?php if($tabs_desc_animation == 'lightSpeedIn' ) { echo "selected"; } ?> >lightSpeedIn</option>
					<option value="rollIn"    		<?php if($tabs_desc_animation == 'rollIn' ) { echo "selected"; } ?> >rollIn</option>
					<option value="slideInDown"    		<?php if($tabs_desc_animation == 'slideInDown' ) { echo "selected"; } ?> >slideInDown</option>
					<option value="slideInLeft"    		<?php if($tabs_desc_animation == 'slideInLeft' ) { echo "selected"; } ?> >slideInLeft</option>
					<option value="slideInRight"    		<?php if($tabs_desc_animation == 'slideInRight' ) { echo "selected"; } ?> >slideInRight</option>
					<option value="slideInUp"    		<?php if($tabs_desc_animation == 'slideInUp' ) { echo "selected"; } ?> >slideInUp</option>
					<option value="rotateIn"    		<?php if($tabs_desc_animation == 'rotateIn' ) { echo "selected"; } ?> >rotateIn</option>
					<option value="rotateInDownLeft"    		<?php if($tabs_desc_animation == 'rotateInDownLeft' ) { echo "selected"; } ?> >rotateInDownLeft</option>
					<option value="rotateInDownRight"    		<?php if($tabs_desc_animation == 'rotateInDownRight' ) { echo "selected"; } ?> >rotateInDownRight</option>
					<option value="rotateInUpLeft"    		<?php if($tabs_desc_animation == 'rotateInUpLeft' ) { echo "selected"; } ?> >rotateInUpLeft</option>
					<option value="rotateInUpRight"    		<?php if($tabs_desc_animation == 'rotateInUpRight' ) { echo "selected"; } ?> >rotateInUpRight</option>
				</select>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_animation">help</a>
				<div id="tabs_r_animation" style="display:none;">
					<div style="color:#fff !important;padding:10px;max-width: 300px;">
						<h2 style="color:#fff !important;">Animation your tabs content on click , select your animation form here</h2>
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr>
			<th scope="row"><label><?php _e('Tabs Content Height',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_content_height_option" id="tabs_content_height_option" value="1" <?php if($tabs_content_height_option == '1' ) { echo "checked"; } ?> onchange="fn_tabs_contents_height_setting()" /> Auto </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_content_height_option" id="tabs_content_height_option" value="2" <?php if($tabs_content_height_option == '2' ) { echo "checked"; } ?> onchange="fn_tabs_contents_height_setting()" /> Custom</span>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_title_icon_align_tp">help</a>
				<div id="tabs_r_title_icon_align_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Align your Tab Icon Position before title or after title',wpshopmart_tabs_pro_text_domain); ?></h2>
					</div>
		    	</div>
				<span class="tabs_contents_height_setting_cls" style="<?php if($tabs_content_height_option=='1'){echo "display:none;";}?>"> Height &nbsp; <input type="number" name="tabs_content_height" value="<?php echo $tabs_content_height;?>" /> Px</span> <br />
				 
			</td>
		</tr>
		<tr class="tabs_contents_height_setting_cls" style="<?php if($tabs_content_height_option=='1'){echo "display:none;";}?>">
			<th scope="row"><label><?php _e('Content Scroll Bar Background Color',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<input id="tabs_content_bar_bg_clr" name="tabs_content_bar_bg_clr" type="text" value="<?php echo $tabs_content_bar_bg_clr; ?>" class="my-color-field" data-default-color="#e8e8e8" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_content_bar_bg_clr_tp">help</a>
				<div id="tabs_content_bar_bg_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Tabs Title Background Colour',wpshopmart_tabs_pro_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/tabs-bg.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		<tr class="tabs_contents_height_setting_cls" style="<?php if($tabs_content_height_option=='1'){echo "display:none;";}?>">
			<th scope="row"><label><?php _e('Content Scroll Bar Fore Color',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<input id="tabs_content_bar_hndl_bg_clr" name="tabs_content_bar_hndl_bg_clr" type="text" value="<?php echo $tabs_content_bar_hndl_bg_clr; ?>" class="my-color-field" data-default-color="#e8e8e8" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_content_bar_hndl_bg_clr_tp">help</a>
				<div id="tabs_content_bar_hndl_bg_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Tabs Title Background Colour',wpshopmart_tabs_pro_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/tabs-bg.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		<tr class="setting_color tabs_contents_height_setting_cls" style="<?php if($tabs_content_height_option=='1'){echo "display:none;";}?>">
			<th><?php _e('Content Scroll Bar Width',wpshopmart_tabs_pro_text_domain);?> </th>
			<td>
				<div id="tabs_content_bar_width_id" class="size-slider" ></div>
				<input type="text" class="slider-text" id="tabs_content_bar_width" name="tabs_content_bar_width"  readonly="readonly">
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_title_size_tp">help</a>
				<div id="tabs_title_size_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;max-width: 300px;">
						<h2 style="color:#fff !important;">You can update Tabs Title and Icon Font Size from here. Just Scroll it to change size.</h2>
					</div>
		    	</div>
			</td>
		</tr>
		<tr class="wpsm_tabs_btn_width_cls" style="<?php if($templates_presets==2){  echo "display:none;"; } ?>">
			<th scope="row"><label><?php _e('Tabs Button Width',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_button_width_option" id="tabs_button_width_option" value="1" <?php if($tabs_button_width_option == '1' ) { echo "checked"; } ?>  /> Auto </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_button_width_option" id="tabs_button_width_option" value="2" <?php if($tabs_button_width_option == '2' ) { echo "checked"; } ?> /> Equal </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_button_width_option" id="tabs_button_width_option" value="3" <?php if($tabs_button_width_option == '3' ) { echo "checked"; } ?> /> Custom</span>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_title_icon_align_tp">help</a>
				<div id="tabs_r_title_icon_align_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Align your Tab Icon Position before title or after title',wpshopmart_tabs_pro_text_domain); ?></h2>
					</div>
		    	</div>
				<span> Width &nbsp; <input type="number" name="tabs_button_width" value="<?php echo $tabs_button_width;?>" /> Px</span> <br />
				 
			</td>
		</tr>
		<tr>
			<th scope="row"><label><?php _e('Tab Open On Hover',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<div class="switch">
					<input type="radio" class="switch-input" name="tabs_on_hover" value="yes" id="enable_tabs_on_hover" <?php if($tabs_on_hover == 'yes' ) { echo "checked"; } ?>  >
					<label for="enable_tabs_on_hover" class="switch-label switch-label-off"><?php _e('Yes',wpshopmart_tabs_pro_text_domain); ?></label>
					<input type="radio" class="switch-input" name="tabs_on_hover" value="no" id="disable_tabs_on_hover" <?php if($tabs_on_hover == 'no' ) { echo "checked"; } ?>>
					<label for="disable_tabs_on_hover" class="switch-label switch-label-on"><?php _e('No',wpshopmart_tabs_pro_text_domain); ?></label>
					<span class="switch-selection"></span>
				</div>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_2_margin">help</a>
				<div id="tabs_r_2_margin" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Margin Between Two Tabs ',wpshopmart_tabs_pro_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/tooltip/img/margin-2-tab.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr class="wpsm_tabs_btn_width_cls">
			<th scope="row"><label><?php _e('Enter The Active tab number you want show open by default on load ',wpshopmart_tabs_pro_text_domain); ?></label></th>
			<td>
				<input id="tabs_number" name="tabs_number" type="number" value="<?php echo $tabs_number; ?>"  />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_title_icon_align_tp">help</a>
				<div id="tabs_r_title_icon_align_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Enter The number tabs you wantto show openby default on window load',wpshopmart_tabs_pro_text_domain); ?></h2>
					</div>
		    	</div>
				
				 
			</td>
		</tr>
		
	
		<script>
			jQuery('.ac_tooltip').darkTooltip({
					opacity:1,
					gravity:'east',
					size:'small'
				});
			
		</script>
	</tbody>
</table>