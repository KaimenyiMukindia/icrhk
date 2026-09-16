<?php 

$PostId = $post->ID;
$sb_settings = unserialize(get_post_meta( $PostId, 'service_box_settings', true));
$Default_Settings = unserialize(get_option('service_box_pro_default_Settings'));
$option_names = array(
		"sb_all_contents_view_type"								=>$Default_Settings['sb_all_contents_view_type'],
		"sb_all_contents_design_no"								=>$Default_Settings['sb_all_contents_design_no'],
		"sb_set_sec_title_y_n"									=>$Default_Settings['sb_set_sec_title_y_n'],
		"sb_set_sec_title_clr"									=>$Default_Settings['sb_set_sec_title_clr'],
		"sb_set_sec_title_size"									=>$Default_Settings['sb_set_sec_title_size'],
		"sb_set_sec_bg_type"									=>$Default_Settings['sb_set_sec_bg_type'],
		"sb_set_sec_bg_opacity"									=>$Default_Settings['sb_set_sec_bg_opacity'],
		"sb_set_sec_bg_clr"										=>$Default_Settings['sb_set_sec_bg_clr'],
		"sb_set_sec_bg_img"										=>$Default_Settings['sb_set_sec_bg_img'],
		"sb_set_sec_bg_size"									=>$Default_Settings['sb_set_sec_bg_size'],
		"sb_set_sec_bg_repeat"									=>$Default_Settings['sb_set_sec_bg_repeat'],
		"sb_set_sec_bg_position"								=>$Default_Settings['sb_set_sec_bg_position'],
		"sb_set_sec_bg_overlay_y_n"								=>$Default_Settings['sb_set_sec_bg_overlay_y_n'],
		"sb_set_sec_bg_Parallax_y_n"							=>$Default_Settings['sb_set_sec_bg_Parallax_y_n'],
		"sb_set_sec_bg_img_over_clr"							=>$Default_Settings['sb_set_sec_bg_img_over_clr'],
		"sb_set_sec_hr_padding"									=>$Default_Settings['sb_set_sec_hr_padding'],
		"sb_set_sec_ver_padding"								=>$Default_Settings['sb_set_sec_ver_padding'],
		"sb_ind_clr_enable"										=>$Default_Settings['sb_ind_clr_enable'],
		"sb_set_bg_opacity"										=>$Default_Settings['sb_set_bg_opacity'],
		"sb_set_icon_clr"										=>$Default_Settings['sb_set_icon_clr'],
		"sb_set_hover_icon_clr"									=>$Default_Settings['sb_set_hover_icon_clr'],       
		"sb_set_icon_bg_clr"									=>$Default_Settings['sb_set_icon_bg_clr'],                 
		"sb_set_hover_icon_bg_clr"  							=>$Default_Settings['sb_set_hover_icon_bg_clr'],                  
		"sb_set_title_clr"   									=>$Default_Settings['sb_set_title_clr'],                  
		"sb_set_hover_title_clr"  								=>$Default_Settings['sb_set_hover_title_clr'],                  
		"sb_set_des_clr"										=>$Default_Settings['sb_set_des_clr'],                  
		"sb_set_hover_des_clr"									=>$Default_Settings['sb_set_hover_des_clr'],                  
		"sb_set_link_clr"										=>$Default_Settings['sb_set_link_clr'],                  
		"sb_set_hover_link_clr"									=>$Default_Settings['sb_set_hover_link_clr'],                  
		"sb_set_link_bg_clr"									=>$Default_Settings['sb_set_link_bg_clr'],                  
		"sb_set_hover_link_bg_clr"								=>$Default_Settings['sb_set_hover_link_bg_clr'],                  
		"sb_set_bg_clr"    										=>$Default_Settings['sb_set_bg_clr'],                  
		"sb_set_hover_bg_clr"									=>$Default_Settings['sb_set_hover_bg_clr'],                  
		"sb_set_brdr_clr"										=>$Default_Settings['sb_set_brdr_clr'],                  
		"sb_set_hover_brdr_clr"									=>$Default_Settings['sb_set_hover_brdr_clr'],                   
		"sb_set_icon_brdr_clr"   								=>$Default_Settings['sb_set_icon_brdr_clr'],                   
		"sb_set_hover_icon_brdr_clr" 							=>$Default_Settings['sb_set_hover_icon_brdr_clr'],                  
		"sb_set_same_icon_img_width"							=>$Default_Settings['sb_set_same_icon_img_width'],
		"sb_set_title_size"										=>$Default_Settings['sb_set_title_size'],
		"sb_set_icon_size"										=>$Default_Settings['sb_set_icon_size'],
		"sb_set_image_size"										=>$Default_Settings['sb_set_image_size'],
		"sb_set_des_size"										=>$Default_Settings['sb_set_des_size'],
		"sb_set_link_size"										=>$Default_Settings['sb_set_link_size'],
		"sb_set_enable_family"									=>$Default_Settings['sb_set_enable_family'],
		"sb_set_font_family"									=>$Default_Settings['sb_set_font_family'],
		"sb_set_font_family_grp"								=>$Default_Settings['sb_set_font_family_grp'],
		"sb_set_show_icon_title"								=>$Default_Settings['sb_set_show_icon_title'],
		"sb_set_box_layout"										=>$Default_Settings['sb_set_box_layout'],
		"sb_set_lod_animation"									=>$Default_Settings['sb_set_lod_animation'],
		"sb_set_same_height"									=>$Default_Settings['sb_set_same_height'],
		"sb_set_brdr_yes_no"									=>$Default_Settings['sb_set_brdr_yes_no'],
		"sb_set_brdr_size"										=>$Default_Settings['sb_set_brdr_size'],
		"sb_set_link_open_in"									=>$Default_Settings['sb_set_link_open_in'],
		"sb_set_link_type"										=>$Default_Settings['sb_set_link_type'],
		"sb_set_link_icon"										=>$Default_Settings['sb_set_link_icon'],
		"sb_set_link_icon_position"								=>$Default_Settings['sb_set_link_icon_position'],
		"sb_set_carousel_no_of_items"							=>$Default_Settings['sb_set_carousel_no_of_items'],
		"sb_set_carousel_loop"									=>$Default_Settings['sb_set_carousel_loop'],
		"sb_set_carousel_Mouse_drag_enabled"					=>$Default_Settings['sb_set_carousel_Mouse_drag_enabled'],
		"sb_set_carousel_touch_drag_enabled"					=>$Default_Settings['sb_set_carousel_touch_drag_enabled'],
		"sb_set_carousel_nav_type"								=>$Default_Settings['sb_set_carousel_nav_type'],
		"sb_set_carousel_nav_btn_type"							=>$Default_Settings['sb_set_carousel_nav_btn_type'],
		"sb_set_carousel_nav_btn_icon_type"						=>$Default_Settings['sb_set_carousel_nav_btn_icon_type'],
		"sb_set_carousel_nav_dots_type"							=>$Default_Settings['sb_set_carousel_nav_dots_type'],
		"sb_set_carousel_nav_dots_shape"						=>$Default_Settings['sb_set_carousel_nav_dots_shape'],
		"sb_set_dots_bg_clr"									=>$Default_Settings['sb_set_dots_bg_clr'],
		"sb_set_hover_dots_bg_clr"								=>$Default_Settings['sb_set_hover_dots_bg_clr'],
		"sb_set_nav_clr"										=>$Default_Settings['sb_set_nav_clr'],
		"sb_set_nav_bg_clr"										=>$Default_Settings['sb_set_nav_bg_clr'],
		"sb_set_hover_nav_clr"									=>$Default_Settings['sb_set_hover_nav_clr'],
		"sb_set_hover_nav_bg_clr"								=>$Default_Settings['sb_set_hover_nav_bg_clr'],
		"sb_set_carousel_nav_right_text"						=>$Default_Settings['sb_set_carousel_nav_right_text'],
		"sb_set_carousel_nav_left_text"							=>$Default_Settings['sb_set_carousel_nav_left_text'],
		"sb_set_carousel_nav_btn_size"							=>$Default_Settings['sb_set_carousel_nav_btn_size'],
		"sb_set_carousel_autoplay"								=>$Default_Settings['sb_set_carousel_autoplay'],
		"sb_set_carousel_autoplay_speed_y_n"					=>$Default_Settings['sb_set_carousel_autoplay_speed_y_n'],
		"sb_set_carousel_autoplay_speed"						=>$Default_Settings['sb_set_carousel_autoplay_speed'],
		"sb_set_carousel_autoplay_interval_time_out"			=>$Default_Settings['sb_set_carousel_autoplay_interval_time_out'],
		"sb_set_carousel_autoplay_hover_pause"					=>$Default_Settings['sb_set_carousel_autoplay_hover_pause'],
		"sb_set_carousel_nav_speed_y_n"							=>$Default_Settings['sb_set_carousel_nav_speed_y_n'],
		"sb_set_carousel_dots_speed_y_n"						=>$Default_Settings['sb_set_carousel_dots_speed_y_n'],
		"sb_set_carousel_nav_position"							=>$Default_Settings['sb_set_carousel_nav_position'],
		);

//getting value(default/save)
foreach($option_names as $option_name => $default_value) 
{
	if(isset($sb_settings[$option_name])) 
	{
		${"" . $option_name}  = $sb_settings[$option_name];
	}
	else
	{
		${"" . $option_name}  = $default_value;
	}
}
 
?>
<style>
.op_cl_icon_box{
	margin-bottom:15px;
	width:100%;
}
.sel-icon-wrapper{
	-webkit-box-shadow: 1px 1px 11px rgba(0, 0, 0, 0.5);
    box-shadow: 1px 1px 11px rgba(0, 0, 0, 0.5);
	
}
.sel-icon-wrapper.active{
	border : 3px solid #489643;
}
.sel-icon-wrapper:hover{
	border : 3px solid #489643;
}
.sel-icon-wrapper.active .checked{
	
	opacity:1;
}
.sel-icon-wrapper .lefti,
.sel-icon-wrapper .righti{
	display:inline-block;
	width:44%;
	padding:15px 0;
	text-align:center;
	margin:0;
}
.sel-icon-wrapper .lefti{
	width:48%;
	border-right: 1px solid #C5C2C2;
}

.sel-icon-wrapper .checked {
	position: absolute;
    background: #489643;
	color: #fff;
    top: -6px;
    right: 7px;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    text-align: center;
    line-height: 20px;
	opacity:0;
}

</style>

<style>

#sb_pro_accordion_id .panel{
    border: none;
    box-shadow: none;
    border-radius: 0;
    margin-bottom: 15px;
}
#sb_pro_accordion_id .panel-heading{
    padding: 0;
}
#sb_pro_accordion_id .panel-title a{
    display: block;
    font-size: 16px;
    font-weight: bold;
    line-height: 24px;
    color: #fff;
    background: #5f62ba;
    border: 2px solid #5f62ba;
    padding: 15px 20px 15px 47px;
    position: relative;
    transition: all 0.5s ease 0s;
	text-decoration: none;
}
#sb_pro_accordion_id .panel-title a.collapsed{
    background: #fff;
    border-color: #ddd;
    color: #888;
}
#sb_pro_accordion_id .panel-title a:before{
    content: "\f106";
    font-family: 'FontAwesome';
    font-size: 18px;
    position: absolute;
    top: 30%;
    left: 20px;
    transition: all 0.3s ease 0s;
}
#sb_pro_accordion_id .panel-title a.collapsed:before{
    content: "\f107";
}
#sb_pro_accordion_id .panel-body{
    font-size: 16px;
    color: #888;
    line-height: 25px;
    border: 2px solid #5f62ba;
    border-top: none;
    padding: 10px 15px;
}
#sb_pro_accordion_id .panel-title a:hover,
#sb_pro_accordion_id .panel-title a:focus{
    text-decoration: none;
    outline: none;
}
.wpsm_sb_tooltip_div{
	color:#fff !important;
	padding:10px;
	text-align:center;
	
}
.sb_tooltip_img{
	width:400px;
	
}
.sb_tooltip_img_sec_bg{
	width:200px;
}

</style>
<Script>
function wpsm_sb_update_default(){
	 jQuery.ajax({
		url: location.href,
		type: "POST",
		data : {
			    'service_box_pro_action123':'default_settings_action',
			     },
                success : function(data){
									alert("Default Settings Updated");
									location.reload(true);
                                   }	
	});
	
}
</script>
<?php

if(isset($_POST['service_box_pro_action123']) == "default_settings_action")
	{	$Settings_Array2 = serialize( array(
			"sb_all_contents_view_type"							=> $sb_all_contents_view_type,
			"sb_all_contents_design_no"							=> $sb_all_contents_design_no,
			"sb_set_sec_title_y_n"								=> $sb_set_sec_title_y_n,
			"sb_set_sec_title_clr"								=> $sb_set_sec_title_clr,
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
			"sb_set_hover_icon_bg_clr"  	=> $sb_set_hover_icon_bg_clr,            
			"sb_set_title_clr"   			=> $sb_set_title_clr,                
			"sb_set_hover_title_clr"  		=> $sb_set_hover_title_clr,                  
			"sb_set_des_clr"				=> $sb_set_des_clr,                  
			"sb_set_hover_des_clr"			=> $sb_set_hover_des_clr,                
			"sb_set_link_clr"				=> $sb_set_link_clr,                
			"sb_set_hover_link_clr"			=> $sb_set_hover_link_clr,                  
			"sb_set_link_bg_clr"			=> $sb_set_link_bg_clr,                 
			"sb_set_hover_link_bg_clr"		=> $sb_set_hover_link_bg_clr,                  
			"sb_set_bg_clr"    				=> $sb_set_bg_clr,                  
			"sb_set_hover_bg_clr"			=> $sb_set_hover_bg_clr,                 
			"sb_set_brdr_clr"				=> $sb_set_brdr_clr,                  
			"sb_set_hover_brdr_clr"			=> $sb_set_hover_brdr_clr,                 
			"sb_set_icon_brdr_clr"   		=> $sb_set_icon_brdr_clr,               
			"sb_set_hover_icon_brdr_clr" 	=> $sb_set_hover_icon_brdr_clr,                
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
			"sb_set_link_open_in"			=> $sb_set_link_open_in,
			"sb_set_link_type"				=> $sb_set_link_type,
			"sb_set_link_icon"				=> $sb_set_link_icon,
			"sb_set_link_icon_position"				=> $sb_set_link_icon_position,
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
			"sb_set_carousel_nav_left_text"							=> $sb_set_carousel_nav_left_text,
			"sb_set_carousel_nav_btn_size"							=> $sb_set_carousel_nav_btn_size,
			"sb_set_carousel_autoplay"								=> $sb_set_carousel_autoplay,
			"sb_set_carousel_autoplay_speed_y_n"					=> $sb_set_carousel_autoplay_speed_y_n,
			"sb_set_carousel_autoplay_speed"						=> $sb_set_carousel_autoplay_speed,
			"sb_set_carousel_autoplay_interval_time_out"			=> $sb_set_carousel_autoplay_interval_time_out,
			"sb_set_carousel_autoplay_hover_pause"					=> $sb_set_carousel_autoplay_hover_pause,
			"sb_set_carousel_nav_speed_y_n"							=> $sb_set_carousel_nav_speed_y_n,
			"sb_set_carousel_dots_speed_y_n"						=> $sb_set_carousel_dots_speed_y_n,
			"sb_set_carousel_nav_position"							=> $sb_set_carousel_nav_position,
			"custom_css"											=> $custom_css
			
			) );

	update_option('service_box_pro_default_Settings', $Settings_Array2);
}
 ?>
<input type="hidden" id="sb_set_hidden" name="sb_set_hidden" value="its_set_hidden">
	<div class="row">
        <div class="">
            <div class="panel-group" id="sb_pro_accordion_id" role="tablist" aria-multiselectable="true">
				<!-- ***************************************************************************************************************
								 SERVICE BOX SECTION SETTINGS
				****************************************************************************************************************** -->								
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingOne">
                        <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#sb_pro_accordion_id" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" >
                                <span class="left-icon"></span>
                                SERVICE BOX SECTION SETTINGS
                            </a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
						<div class="panel-body">
							<table class="form-table acc_table">
								<tbody>
									
									<!-- Section Title-->
									<tr>
										<th scope="row"><label><?php _e('Display Section Title'); ?></label>
										<a  class="ac_tooltip" href="#help" data-tooltip="#sec_title_y_n_tp"><i class="fa fa-lightbulb-o"></i></a>
										
										</th>
										<td>
											<div class="switch">
												<input type="radio" id="enable_sec_title_y_n" class="switch-input" name="sb_set_sec_title_y_n" value="yes"  <?php if($sb_set_sec_title_y_n == 'yes' ) { echo "checked"; } ?>  >
												<label for="enable_sec_title_y_n" class="switch-label switch-label-off"><?php _e('Yes'); ?></label>
												<input type="radio" id="disable_sec_title_y_n" class="switch-input" name="sb_set_sec_title_y_n" value="no"   <?php if($sb_set_sec_title_y_n == 'no' ) { echo "checked"; } ?> >
												<label for="disable_sec_title_y_n" class="switch-label switch-label-on"><?php _e('No'); ?></label>
												<span class="switch-selection"></span>
											</div>
											<!-- Tooltip -->
											<div id="sec_title_y_n_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('Enable or disable to display section title here'); ?></h2>
													<img class="sb_tooltip_img" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_section_title.png'; ?>"/>
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Section title Colour-->
									<tr class="" >
										<th scope="row"><label><?php _e('Section Title Colour'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#service_box_sec_title_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
										</th>
										<td>
											<input id="sb_set_sec_title_clr" name="sb_set_sec_title_clr" type="text" value="<?php echo $sb_set_sec_title_clr;  ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="service_box_sec_title_clr_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('Section Title Colour'); ?></h2>
													<img class="sb_tooltip_img" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_section_title.png'; ?>"/>
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Section Horizontal Paddinng-->
									<tr class="setting_color">
										<th><label><?php _e('Section Title Font Size'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#sb_set_sec_title_size_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div id="sb_set_sec_title_size_id" class="size-slider" ></div>
											<input type="text" class="slider-text" id="sb_set_sec_title_size" name="sb_set_sec_title_size"  readonly="readonly">
											<!-- Tooltip -->
											<div id="sb_set_sec_title_size_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;">You can update Section Title Font Size from here. Just Scroll it to change size.</h2>
												
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Section Horizontal Paddinng-->
									<tr class="setting_color">
										<th><label><?php _e('Section Left and Right Padding'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#sb_set_sec_hr_paddding_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div id="sb_set_sec_hr_padding_id" class="size-slider" ></div>
											<input type="text" class="slider-text" id="sb_set_sec_hr_padding" name="sb_set_sec_hr_padding"  readonly="readonly">
											<!-- Tooltip -->
											<div id="sb_set_sec_hr_paddding_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;">You can update Section Left and Right Padding from here. Just Scroll it to change size.</h2>
												
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Section Vartical Paddinng-->
									<tr class="setting_color">
										<th><label><?php _e('Section Top and Bottom Padding'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#sb_set_sec_ver_paddding_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div id="sb_set_sec_ver_padding_id" class="size-slider" ></div>
											<input type="text" class="slider-text" id="sb_set_sec_ver_padding" name="sb_set_sec_ver_padding"  readonly="readonly">
											<!-- Tooltip -->
											<div id="sb_set_sec_ver_paddding_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;">You can update Section Top and Bottom Padding from here. Just Scroll it to change size.</h2>
												
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Section Background Type-->
									<tr>
										<th scope="row"><label><?php _e('Section Background Type'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#sec_type_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_sec_bg_type" value="1"  <?php if($sb_set_sec_bg_type == '1' ) { echo "checked"; } ?> onclick="fn_sec_bg_type()"/> Transparent </span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_sec_bg_type"  value="2" <?php if($sb_set_sec_bg_type == '2' ) { echo "checked"; } ?> onclick="fn_sec_bg_type()"/> Color </span>
											<span style="display:block;"><input type="radio" name="sb_set_sec_bg_type"  value="3" <?php if($sb_set_sec_bg_type == '3' ) { echo "checked"; } ?> onclick="fn_sec_bg_type()"/> Image </span>
											
											<!-- Tooltip -->
											<div id="sec_type_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('Service Box Section Background Type '); ?></h2>
													<div style="display:inline-block;">
														<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_section_bg_trans.png'; ?>">
														<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_section_bg_img.png'; ?>">
														<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_section_bg_clr.png'; ?>">
													</div>
												</div>
											</div>
										</td>
									</tr>
									
									
									
									<!-- Section Background Colour-->
									<tr class="section_bg_clr_cls" style="<?php if($sb_set_sec_bg_type=="1" || $sb_set_sec_bg_type=="3"){echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Section Background Colour'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#service_box_sec_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
										</th>
										<td>
											<input id="sb_set_sec_bg_clr" name="sb_set_sec_bg_clr" type="text" value="<?php echo $sb_set_sec_bg_clr;  ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="service_box_sec_clr_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('Section Background Colour'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_section_bg_clr.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Section Background Image-->
									<tr class="section_bg_img_cls" style="<?php if($sb_set_sec_bg_type=="1" || $sb_set_sec_bg_type=="2"){echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Section Background Image'); ?></label>
											<div>
												<div>
													<span  style="padding:1px;">
														<img class="img-responsive" src="<?php if($sb_set_sec_bg_img){ echo $sb_set_sec_bg_img;}?>" />
													</span>
													
													<input style="width:100%;" type="button" id="" name="" value="Upload Image" class="form-control btn btn-primary"  onclick="service_box_media_upload(this)"/>
													<input style="display:block;width:100%" type="hidden"  name="sb_set_sec_bg_img" id="sb_set_sec_bg_img" class=""  value="<?php if($sb_set_sec_bg_img){echo $sb_set_sec_bg_img;} else {echo service_box_directory_url."assets/images/section_bg.jpg";}?>"  readonly="readonly" placeholder="No Media Selected" />
												</div>
											</div>
										</th>
										
									</tr>
									
									<!-- Section Background Image Size-->
									<tr class="section_bg_img_cls" style="<?php if($sb_set_sec_bg_type=="1" || $sb_set_sec_bg_type=="2"){echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Section Background Image Size'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#sec_bg_size_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_sec_bg_size" value="auto"  <?php if($sb_set_sec_bg_size == 'auto' ) { echo "checked"; } ?> /> Auto </span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_sec_bg_size"  value="cover" <?php if($sb_set_sec_bg_size == 'cover' ) { echo "checked"; } ?> /> Cover </span>
											<span style="display:block;"><input type="radio" name="sb_set_sec_bg_size"  value="contain" <?php if($sb_set_sec_bg_size == 'contain' ) { echo "checked"; } ?> /> Contain </span>
											
											<!-- Tooltip -->
											<div id="sec_bg_size_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('You can update section background image size from here '); ?></h2>
													
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Section Background Image Repeat-->
									<tr class="section_bg_img_cls" style="<?php if($sb_set_sec_bg_type=="1" || $sb_set_sec_bg_type=="2"){echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Section Background Image Repeat'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#sec_bg_repeat_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_sec_bg_repeat"  value="repeat"  <?php if($sb_set_sec_bg_repeat == 'repeat' ) { echo "checked"; } ?> /> Repeat Both(Horizontal & Vertical) </span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_sec_bg_repeat"  value="repeat-x" <?php if($sb_set_sec_bg_repeat == 'repeat-x' ) { echo "checked"; } ?> /> Repeat Horizontal Only </span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_sec_bg_repeat"  value="repeat-y" <?php if($sb_set_sec_bg_repeat == 'repeat-y' ) { echo "checked"; } ?> /> Repeat Vertical Only </span>
											<span style="display:block;">				   <input type="radio" name="sb_set_sec_bg_repeat"  value="no-repeat" <?php if($sb_set_sec_bg_repeat == 'no-repeat' ) { echo "checked"; } ?> /> No Repeat </span>
											
											<!-- Tooltip -->
											<div id="sec_bg_repeat_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('You can repeat section background image in X-axis or Y-axis or Both.'); ?></h2>
													
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Section Background Image Position-->		
									<tr class="section_bg_img_cls" style="<?php if($sb_set_sec_bg_type=="1" || $sb_set_sec_bg_type=="2"){echo "display:none;";}?>">
										<th><label><?php _e('Section Background Image Position'); ?> </label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#sec_bg_position_tp"><i class="fa fa-lightbulb-o"></i></a>
										</th>
										<td>
											
											<select name="sb_set_sec_bg_position" id="sb_set_sec_bg_position" class="standard-dropdown" style="width:100%" >
												
													<option value="left top"  	  <?php if($sb_set_sec_bg_position == 'left top') 	  { echo "selected"; } ?>>Left Top</option>
													<option value="left center"   <?php if($sb_set_sec_bg_position == 'left center')  { echo "selected"; } ?>>Left Center</option>
													<option value="left bottom"   <?php if($sb_set_sec_bg_position == 'left bottom')  { echo "selected"; } ?>>Left Bottom</option>
													<option value="center top"    <?php if($sb_set_sec_bg_position == 'center top')   { echo "selected"; } ?>>Center Top</option>
													<option value="center center" <?php if($sb_set_sec_bg_position == 'center center'){ echo "selected"; } ?>>Center Center</option>
													<option value="center bottom" <?php if($sb_set_sec_bg_position == 'center bottom'){ echo "selected"; } ?>>Center Bottom</option>
													<option value="right top"  	  <?php if($sb_set_sec_bg_position == 'right top') 	  { echo "selected"; } ?>>Right Top</option>
													<option value="right center"  <?php if($sb_set_sec_bg_position == 'right center') { echo "selected"; } ?>>Right Center</option>
													<option value="right bottom"  <?php if($sb_set_sec_bg_position == 'right bottom') { echo "selected"; } ?>>Right Bottom</option>
													
											</select>
											<!-- Tooltip -->
											<div id="sec_bg_position_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('You can select section background image position from here.'); ?></h2>
												</div>
											</div>
										</td>
									</tr>
									<!--Section Background Parallax Scrolling Effect-->	
									<tr class="section_bg_img_cls" style="<?php if($sb_set_sec_bg_type=="1" || $sb_set_sec_bg_type=="2"){echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Section Background Parallax Scrolling Effect'); ?></label>
										<a  class="ac_tooltip" href="#help" data-tooltip="#sec_bg_Parallax_y_n_tp"><i class="fa fa-lightbulb-o"></i></a>
										
										</th>
										<td>
											<div class="switch">
												<input type="radio" id="enable_sec_bg_Parallax_y_n" class="switch-input" name="sb_set_sec_bg_Parallax_y_n" value="yes"  <?php if($sb_set_sec_bg_Parallax_y_n == 'yes' ) { echo "checked"; } ?>  >
												<label for="enable_sec_bg_Parallax_y_n" class="switch-label switch-label-off"><?php _e('Yes'); ?></label>
												<input type="radio" id="disable_sec_bg_Parallax_y_n" class="switch-input" name="sb_set_sec_bg_Parallax_y_n" value="no"   <?php if($sb_set_sec_bg_Parallax_y_n == 'no' ) { echo "checked"; } ?> >
												<label for="disable_sec_bg_Parallax_y_n" class="switch-label switch-label-on"><?php _e('No'); ?></label>
												<span class="switch-selection"></span>
											</div>
											<!-- Tooltip -->
											<div id="sec_bg_Parallax_y_n_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div" style="max-width:300px;">
													<h2 style="color:#fff !important;"><?php _e('Enable or disable section background image parallax scrolling effect'); ?></h2>
												</div>
											</div>
										</td>
									</tr>
									<!--Section Background Image Overlay-->	
									<tr class="section_bg_img_cls" style="<?php if($sb_set_sec_bg_type=="1" || $sb_set_sec_bg_type=="2"){echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Section Background Image Overlay'); ?></label>
										<a  class="ac_tooltip" href="#help" data-tooltip="#sec_bg_overlay_y_n_tp"><i class="fa fa-lightbulb-o"></i></a>
										
										</th>
										<td>
											<div class="switch" >
												<input type="radio" id="enable_sec_bg_overlay_y_n" class="switch-input" name="sb_set_sec_bg_overlay_y_n" value="yes"  <?php if($sb_set_sec_bg_overlay_y_n == 'yes' ) { echo "checked"; } ?>  onchange="fn_sec_bg_type()">
												<label for="enable_sec_bg_overlay_y_n" class="switch-label switch-label-off"><?php _e('Yes'); ?></label>
												<input type="radio" id="disable_sec_bg_overlay_y_n" class="switch-input" name="sb_set_sec_bg_overlay_y_n" value="no"   <?php if($sb_set_sec_bg_overlay_y_n == 'no' ) { echo "checked"; } ?> onchange="fn_sec_bg_type()">
												<label for="disable_sec_bg_overlay_y_n" class="switch-label switch-label-on"><?php _e('No'); ?></label>
												<span class="switch-selection"></span>
											</div>
											<!-- Tooltip -->
											<div id="sec_bg_overlay_y_n_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div" style="max-width:300px;">
													<h2 style="color:#fff !important;"><?php _e('Enable or disable section background image overlay colour'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_section_bg_img_overlay.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Section Background Image Overlay Colour-->
									<tr class="section_bg_img_overlay_cls" style="<?php if($sb_set_sec_bg_type=="1" || $sb_set_sec_bg_type=="2" || $sb_set_sec_bg_overlay_y_n=="no"){echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Section Background Image Overlay Colour'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#service_box_sec_bg_img_over_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
										</th>
										<td>
											<input id="sb_set_sec_bg_img_over_clr" name="sb_set_sec_bg_img_over_clr" type="text" value="<?php echo $sb_set_sec_bg_img_over_clr;  ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="service_box_sec_bg_img_over_clr_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div" style="max-width:300px;">
													<h2 style="color:#fff !important;"><?php _e('Section Background Image Overlay Colour'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_section_bg_img_overlay.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									<!-- Section Background Opacity-->
									<tr class="setting_color section_bg_img_overlay_cls" style="<?php if($sb_set_sec_bg_type=="1" || $sb_set_sec_bg_type=="2" || $sb_set_sec_bg_overlay_y_n=="no"){echo "display:none;";}?>">
										<th><label><?php _e('Section Background Image Overlay Colour Opacity'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#sb_set_sec_bg_opacity_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div id="sb_set_sec_bg_opacity_id" class="size-slider" ></div>
											<input type="text" class="slider-text" id="sb_set_sec_bg_opacity" name="sb_set_sec_bg_opacity"  readonly="readonly">
											<!-- Tooltip -->
											<div id="sb_set_sec_bg_opacity_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div" style="max-width:300px;">
													<h2 style="color:#fff !important;">You can update Section Background Image Overlay Colour Opacity from here. Just Scroll it to change size.</h2>
												
												</div>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
                        </div>
                    </div>
                </div>
				<!-- ***************************************************************************************************************
								Color SETTINGS
				****************************************************************************************************************** -->								
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingTwo">
                        <h4 class="panel-title">
                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#sb_pro_accordion_id" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" >
                                <span class="left-icon"></span>
                                SERVICE BOX COLOR SETTINGS
                            </a>
                        </h4>
                    </div>
					
                    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
						<div class="panel-body">
							<table class="form-table acc_table">
								<tbody>
									<!-- Service Box Background Opacity-->
									<tr class="setting_color">
										<th><label><?php _e('Service Box Background Color Opacity'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#sb_set_bg_opacity_tp"><i class="fa fa-lightbulb-o"></i></a>
										</th>
										<td>
											<div id="sb_set_bg_opacity_id" class="size-slider" ></div>
											<input type="text" class="slider-text" id="sb_set_bg_opacity" name="sb_set_bg_opacity"  readonly="readonly">
											<!-- Tooltip -->
											<div id="sb_set_bg_opacity_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;">You can update Service Box Background Colour Opacity from here. Just Scroll it to change size.</h2>
												
												</div>
											</div>
										</td>
									</tr>
									<!-- Enable/Disable Individual Color -->
									<tr>
										<th scope="row"><label><?php _e('Enable Individual Color Option '); ?></label>
										<a  class="ac_tooltip" href="#help" data-tooltip="#cb_ind_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
										
										</th>
										<td>
											<div class="switch">
												<input type="radio" class="switch-input" name="sb_ind_clr_enable" value="yes" id="enable_sb_ind_clr_enable" <?php if($sb_ind_clr_enable == 'yes' ) { echo "checked"; } ?>  onchange="hide_color_setting()">
												<label for="enable_sb_ind_clr_enable" class="switch-label switch-label-off"><?php _e('Yes'); ?></label>
												<input type="radio" class="switch-input" name="sb_ind_clr_enable" value="no" id="disable_sb_ind_clr_enable"  <?php if($sb_ind_clr_enable == 'no' ) { echo "checked"; } ?> onchange="hide_color_setting()">
												<label for="disable_sb_ind_clr_enable" class="switch-label switch-label-on"><?php _e('No'); ?></label>
												<span class="switch-selection"></span>
											</div>
											<!-- Tooltip -->
											<div id="cb_ind_clr_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('Enable your individual color settings from here, if you want to show different color for each service box'); ?></h2>
												</div>
											</div>
										</td>
									</tr>
									
									
									<!-- Icon Color -->
									<tr class="sb_ind_clr_enable_class" <?php if($sb_ind_clr_enable=="yes"){ ?> style="display:none" <?php } ?>>
										<th scope="row"><label><?php _e('Icon Colour'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#service_box_icon_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
										</th>
										<td>
											<input id="sb_set_icon_clr" name="sb_set_icon_clr" type="text" value="<?php echo $sb_set_icon_clr;  ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="service_box_icon_clr_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('Service Box Icon Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_title_clr.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									<!-- Hover Icon Color -->
									<tr class="sb_ind_clr_enable_class" <?php if($sb_ind_clr_enable=="yes"){ ?> style="display:none" <?php } ?>>
										<th scope="row"><label><?php _e('Hover Icon Color'); ?></label>
											<a  class="ac_tooltip" href="#colorbox_desc_font_clr_tp" data-tooltip="#colorbox_desc_font_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<input id="sb_set_hover_icon_clr" name="sb_set_hover_icon_clr" type="text" value="<?php echo $sb_set_hover_icon_clr; ?>" class="my-color-field" data-default-color="#000000" />
											<!-- Tooltip -->
											<div id="colorbox_desc_font_clr_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('Service Box Mouse Over Icon Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_title_clr.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Icon BG Color -->
									<tr class="sb_ind_clr_enable_class" <?php if($sb_ind_clr_enable=="yes"){ ?> style="display:none" <?php } ?>>
										<th scope="row"><label><?php _e('Icon Background Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#servicebox_icon_bg_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<input id="sb_set_icon_bg_clr" name="sb_set_icon_bg_clr" type="text" value="<?php echo $sb_set_icon_bg_clr; ?>" class="my-color-field" data-default-color="#e8e8e8" />
											<!-- Tooltip -->
											<div id="servicebox_icon_bg_clr_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('Service Box Icon Background Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_icon_bg_clr.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									<!-- Hover Icon BG Color -->
									<tr class="sb_ind_clr_enable_class" <?php if($sb_ind_clr_enable=="yes"){ ?> style="display:none" <?php } ?>>
										<th scope="row"><label><?php _e('Hover Icon Background Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#colorbox_desc_bg_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<input id="sb_set_hover_icon_bg_clr" name="sb_set_hover_icon_bg_clr" type="text" value="<?php echo $sb_set_hover_icon_bg_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="colorbox_desc_bg_clr_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('Service Box Mouse Over Icon Background Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_icon_bg_clr.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
									
									<!--Icon Brdr Color -->
									<tr class="sb_ind_clr_enable_class" <?php if($sb_ind_clr_enable=="yes"){ ?> style="display:none" <?php } ?>>
										<th scope="row"><label><?php _e('Icon Border Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#cb_border_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<input id="sb_set_icon_brdr_clr" name="sb_set_icon_brdr_clr" type="text" value="<?php echo $sb_set_icon_brdr_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="cb_border_clr_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('Icon Border Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_icon_brdr_clr.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
									
									<!--Hover Icon Brdr Color -->
									<tr class="sb_ind_clr_enable_class" <?php if($sb_ind_clr_enable=="yes"){ ?> style="display:none" <?php } ?>>
										<th scope="row"><label><?php _e('Hover Icon Border Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#hover_cb_border_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<input id="sb_set_hover_icon_brdr_clr" name="sb_set_hover_icon_brdr_clr" type="text" value="<?php echo $sb_set_hover_icon_brdr_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="hover_cb_border_clr_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('On Mouse Over Icon Border Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_icon_brdr_clr.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									<!-- Title Color -->
									<tr class="sb_ind_clr_enable_class" <?php if($sb_ind_clr_enable=="yes"){ ?> style="display:none" <?php } ?>>
										<th scope="row"><label><?php _e('Title Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#cb_icon_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<input id="sb_set_title_clr" name="sb_set_title_clr" type="text" value="<?php echo $sb_set_title_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="cb_icon_clr_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('Service Box Title Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_title_clr_new.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
									
									<!-- Hover Title Color -->
									<tr class="sb_ind_clr_enable_class" <?php if($sb_ind_clr_enable=="yes"){ ?> style="display:none" <?php } ?>>
										<th scope="row"><label><?php _e('Hover Title Colour'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#cb_hover_title_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<input id="sb_set_hover_title_clr" name="sb_set_hover_title_clr" type="text" value="<?php echo $sb_set_hover_title_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="cb_hover_title_clr_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('On Mouse Over Service Box Title Colour'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_hover_title_clr_new.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Des Color -->
									<tr class="sb_ind_clr_enable_class" <?php if($sb_ind_clr_enable=="yes"){ ?> style="display:none" <?php } ?>>
										<th scope="row"><label><?php _e('Description Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#cb_set_des_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<input id="sb_set_des_clr" name="sb_set_des_clr" type="text" value="<?php echo $sb_set_des_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="cb_set_des_clr_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('Service Box Description Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_des_clr.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Hover Des Color -->
									<tr class="sb_ind_clr_enable_class" <?php if($sb_ind_clr_enable=="yes"){ ?> style="display:none" <?php } ?>>
										<th scope="row"><label><?php _e('Hover Description Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#cb_set_hover_des_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<input id="sb_set_hover_des_clr" name="sb_set_hover_des_clr" type="text" value="<?php echo $sb_set_hover_des_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="cb_set_hover_des_clr_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('On Mouse Over Service Box Description Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_hover_des_clr.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
									
									<!-- Link Color -->
									<tr class="sb_ind_clr_enable_class" <?php if($sb_ind_clr_enable=="yes"){ ?> style="display:none" <?php } ?>>
										<th scope="row"><label><?php _e('Link Button Font Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#cb_set_link_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<input id="sb_set_link_clr" name="sb_set_link_clr" type="text" value="<?php echo $sb_set_link_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="cb_set_link_clr_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('Service Box Link Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_link_clr.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
									
									<!-- Hover Link Color -->
									<tr class="sb_ind_clr_enable_class" <?php if($sb_ind_clr_enable=="yes"){ ?> style="display:none" <?php } ?>>
										<th scope="row"><label><?php _e('Hover Link Button Font Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#cb_set_hover_link_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<input id="sb_set_hover_link_clr" name="sb_set_hover_link_clr" type="text" value="<?php echo $sb_set_hover_link_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="cb_set_hover_link_clr_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('On Mouse Over Service Box Link Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_hover_link_clr.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Link bg Color -->
									<tr class="sb_ind_clr_enable_class" <?php if($sb_ind_clr_enable=="yes"){ ?> style="display:none" <?php } ?>>
										<th scope="row"><label><?php _e('Link Button Background Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#cb_set_link_bg_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<input id="sb_set_link_bg_clr" name="sb_set_link_bg_clr" type="text" value="<?php echo $sb_set_link_bg_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="cb_set_link_bg_clr_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div">
													<h2 style="color:#fff !important;"><?php _e('Service Box Link Background Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_link_bg_clr.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
									
									
									<!-- Hover Link bg Color -->
									<tr class="sb_ind_clr_enable_class" <?php if($sb_ind_clr_enable=="yes"){ ?> style="display:none" <?php } ?>>
										<th scope="row"><label><?php _e('Hover Link Button Background Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#cb_set_hover_link_bg_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<input id="sb_set_hover_link_bg_clr" name="sb_set_hover_link_bg_clr" type="text" value="<?php echo $sb_set_hover_link_bg_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="cb_set_hover_link_bg_clr_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width:300px;text-align:center;">
													<h2 style="color:#fff !important;"><?php _e('On Mouse Over Service Box Link Background Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_link_bg_clr.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
									
									<!-- bg Color -->
									<tr class="sb_ind_clr_enable_class" <?php if($sb_ind_clr_enable=="yes"){ ?> style="display:none" <?php } ?>>
										<th scope="row"><label><?php _e('Service Box Background Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#cb_set_bg_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<input id="sb_set_bg_clr" name="sb_set_bg_clr" type="text" value="<?php echo $sb_set_bg_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="cb_set_bg_clr_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width:300px;text-align:center;">
													<h2 style="color:#fff !important;"><?php _e('Service Box Background Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_bg_clr.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
									
									<!-- Hover bg Color -->
									<tr class="sb_ind_clr_enable_class" <?php if($sb_ind_clr_enable=="yes"){ ?> style="display:none" <?php } ?>>
										<th scope="row"><label><?php _e('Hover Service Box Background Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#cb_set_hover_bg_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<input id="sb_set_hover_bg_clr" name="sb_set_hover_bg_clr" type="text" value="<?php echo $sb_set_hover_bg_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="cb_set_hover_bg_clr_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width:300px;text-align:center;">
													<h2 style="color:#fff !important;"><?php _e('On Mouse Over Service Box Background Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_hover_bg_clr.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Brdr Color -->
									<tr class="sb_ind_clr_enable_class" <?php if($sb_ind_clr_enable=="yes"){ ?> style="display:none" <?php } ?>>
										<th scope="row"><label><?php _e('Service Box Border Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#cb_set_brdr_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<input id="sb_set_brdr_clr" name="sb_set_brdr_clr" type="text" value="<?php echo $sb_set_brdr_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="cb_set_brdr_clr_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width:300px;text-align:center;">
													<h2 style="color:#fff !important;"><?php _e('Service Box Border Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_brdr_clr.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
									
									<!-- Hover Brdr Color -->
									<tr class="sb_ind_clr_enable_class" <?php if($sb_ind_clr_enable=="yes"){ ?> style="display:none" <?php } ?>>
										<th scope="row"><label><?php _e('Hover Service Box Border Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#cb_set_hover_brdr_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<input id="sb_set_hover_brdr_clr" name="sb_set_hover_brdr_clr" type="text" value="<?php echo $sb_set_hover_brdr_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="cb_set_hover_brdr_clr_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width:250px;text-align:center;">
													<h2 style="color:#fff !important;"><?php _e('On Mouse Over Service Box Border Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_brdr_clr.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
								</tbody>	
							</table>
						</div>
                    </div>
                </div>
				<!-- ***************************************************************************************************************
								SERVICE BOX FONT FAMILY & SIZE SETTINGS
				****************************************************************************************************************** -->								
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingThree">
                        <h4 class="panel-title">
                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#sb_pro_accordion_id" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree" >
                                <span class="left-icon"></span>
                                SERVICE BOX FONT FAMILY & SIZE SETTINGS
                            </a>
                        </h4>
                    </div>
                    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
						<div class="panel-body">
							<table class="form-table acc_table">
								<tbody>
									
									<tr>
										<th scope="row"><label><?php _e('Same width to icon and image'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#same_width_icon_img_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div class="switch">
												<input type="radio" class="switch-input" name="sb_set_same_icon_img_width" value="yes" id="enable_same_icon_img_width" <?php if($sb_set_same_icon_img_width == 'yes' ) { echo "checked"; } ?>  onchange="same_width_img_icon()">
												<label for="enable_same_icon_img_width" class="switch-label switch-label-off"><?php _e('Yes'); ?></label>
												<input type="radio" class="switch-input" name="sb_set_same_icon_img_width" value="no" id="disable_same_icon_img_width"  <?php if($sb_set_same_icon_img_width == 'no' ) { echo "checked"; } ?> onchange="same_width_img_icon()">
												<label for="disable_same_icon_img_width" class="switch-label switch-label-on"><?php _e('No'); ?></label>
												<span class="switch-selection"></span>
											</div>
											<!-- Tooltip -->
											<div id="same_width_icon_img_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width: 300px;">
													<h2 style="color:#fff !important;"><?php _e('Enable Or disbale same size for service box icon and image'); ?></h2>
													
												</div>
											</div>
											<br />
											<!--label>Note: Same height is not apply if Masonry Effect is enable and loading animation applied</label-->
										</td>
									</tr>
									<tr class="setting_color">
										<th><label><?php _e('Icon Size'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#sb_set_icon_size_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div id="sb_set_icon_size_id" class="size-slider" ></div>
											<input type="text" class="slider-text" id="sb_set_icon_size" name="sb_set_icon_size"  readonly="readonly">
											<!-- Tooltip -->
											<div id="sb_set_icon_size_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width: 300px;">
													<h2 style="color:#fff !important;">You can update Icon Font Size from here. Just Scroll it to change size.</h2>
												
												</div>
											</div>
										</td>
									</tr>
									<tr id="image_size_id" class="setting_color" style="<?php if($sb_set_same_icon_img_width=='yes'){echo "display:none";}?>">
										<th><label><?php _e('Image Width'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#sb_set_image_size_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div id="sb_set_image_size_id" class="size-slider" ></div>
											<input type="text" class="slider-text" id="sb_set_image_size" name="sb_set_image_size"  readonly="readonly">
											<!-- Tooltip -->
											<div id="sb_set_image_size_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width: 300px;">
													<h2 style="color:#fff !important;">You can update Image Size from here. Just Scroll it to change size.</h2>
												
												</div>
											</div>
										</td>
									</tr>
									
									<tr class="setting_color">
										<th><label><?php _e('Title Font Size'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#sb_set_title_size_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div id="sb_set_title_size_id" class="size-slider" ></div>
											<input type="text" class="slider-text" id="sb_set_title_size" name="sb_set_title_size"  readonly="readonly" >
											<!-- Tooltip -->
											<div id="sb_set_title_size_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width: 300px;">
													<h2 style="color:#fff !important;">You can update Title Font Size from here. Just Scroll it to change size.</h2>
												
												</div>
											</div>
										</td>
									</tr>
									
									
									
									<tr class="setting_color">
										<th><label><?php _e('Description Font Size'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#sb_set_des_size_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div id="sb_set_des_size_id" class="size-slider" ></div>
											<input type="text" class="slider-text" id="sb_set_des_size" name="sb_set_des_size"  readonly="readonly">
											<!-- Tooltip -->
											<div id="sb_set_des_size_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width: 300px;">
													<h2 style="color:#fff !important;">You can update Description Font Size from here. Just Scroll it to change size.</h2>
													
												</div>
											</div>
										</td>
									</tr>
									
									<tr class="setting_color">
										<th><label><?php _e('Link Text Font Size'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#sb_set_link_size_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div id="sb_set_link_size_id" class="size-slider" ></div>
											<input type="text" class="slider-text" id="sb_set_link_size" name="sb_set_link_size"  readonly="readonly">
											<!-- Tooltip -->
											<div id="sb_set_link_size_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width: 300px;">
													<h2 style="color:#fff !important;">You can update Link Text Font Size from here. Just Scroll it to change size.</h2>
													
												</div>
											</div>
										</td>
									</tr>
									<tr class="setting_color">
										<th><label><?php _e('Service Box Border Size'); ?> </label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#sb_set_brdr_size_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div id="sb_set_brdr_size_id" class="size-slider" ></div>
											<input type="text" class="slider-text" id="sb_set_brdr_size" name="sb_set_brdr_size"  readonly="readonly">
											<!-- Tooltip -->
											<div id="sb_set_brdr_size_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width: 300px;">
													<h2 style="color:#fff !important;">You can update service box border Size from here. Just Scroll it to change size.</h2>
													
												</div>
											</div>
										</td>
									</tr>
									
									<tr>
										<th scope="row"><label><?php _e('Enable Font Family'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#font_family_allow_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											
											<div class="switch">
												<input type="radio" class="switch-input" name="sb_set_enable_family" value="yes" id="enable_font_family_allow" <?php if($sb_set_enable_family == 'yes' ) { echo "checked"; } ?>   >
												<label for="enable_font_family_allow" class="switch-label switch-label-off"><?php _e('Yes'); ?></label>
												<input type="radio" class="switch-input" name="sb_set_enable_family" value="no" id="disable_font_family_allow"  <?php if($sb_set_enable_family =='no') { echo "checked"; } ?> >
												<label for="disable_font_family_allow" class="switch-label switch-label-on"><?php _e('No'); ?></label>
												<span class="switch-selection"></span>
											</div>
											<!-- Tooltip -->
											<div id="font_family_allow_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width: 300px;">
													<h2 style="color:#fff !important;"><?php _e('Enable Font Family From here, if you want to apply your theme font family on box then disable it.'); ?></h2>
												</div>
											</div>
										</td>
									</tr>
									<tr >
										<th><label><?php _e('Font Style/Family'); ?></label> 
											<a  class="ac_tooltip" href="#help" data-tooltip="#font_family_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<?php 
											require_once("font-family.php");
											?>	
											<input type="hidden" name="sb_set_font_family" id="sb_set_font_family" value="<?php echo $sb_set_font_family;  ?>" />
											<input type="hidden" name="sb_set_font_family_grp" id="sb_set_font_family_grp" value="<?php echo $sb_set_font_family_grp;  ?>" />
											<!-- Tooltip -->
											<div id="font_family_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width: 300px;">
													<h2 style="color:#fff !important;">You can update Title and Description Font Family/Style from here. Select any one form these options.</h2>
												
												</div>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
                    </div>
                </div>
				<!-- ***************************************************************************************************************
								SERVICE BOX LAYOUT, STYLE & LINK SETTINGS
				****************************************************************************************************************** -->								
				<div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingFour">
                        <h4 class="panel-title">
                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#sb_pro_accordion_id" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour" >
                                <span class="left-icon"></span>
                                SERVICE BOX LAYOUT, STYLE & LINK SETTINGS
                            </a>
                        </h4>
                    </div>
                    <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
						<div class="panel-body">
							<table class="form-table acc_table">
								<tbody>
									<tr >
										<th><label><?php _e('Service Box Design Layout'); ?> </label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#enable_box_layout_tp"><i class="fa fa-lightbulb-o"></i></a>
										</th>
										<td>
											<select name="sb_set_box_layout" id="sb_set_box_layout" class="standard-dropdown" style="width:100%" >
													<option value="12"  <?php if($sb_set_box_layout == '12') { echo "selected"; } ?>>One Column Layout</option>
													<option value="6"  <?php if($sb_set_box_layout == '6') { echo "selected"; } ?>>Two Column Layout</option>
													<option value="4"  <?php if($sb_set_box_layout == '4') { echo "selected"; } ?>>Three Column Layout</option>
													<option value="3"  <?php if($sb_set_box_layout == '3') { echo "selected"; } ?>>Four Column Layout</option>
													<option value="5"  <?php if($sb_set_box_layout == '5') { echo "selected"; } ?>>Five Column Layout</option>
													<option value="2"  <?php if($sb_set_box_layout == '2') { echo "selected"; } ?>>Six Column Layout</option>
													<option value="8"  <?php if($sb_set_box_layout == '8') { echo "selected"; } ?>>Eight Column Layout</option>
													<option value="10"  <?php if($sb_set_box_layout == '10') { echo "selected"; } ?>>Ten Column Layout</option>
													
											</select>
											<!-- Tooltip -->
											<div id="enable_box_layout_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width:250px;">
													<h2 style="color:#fff !important;"><?php _e('Select number of Service Box you want to display in a row '); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_column_layout.png'; ?> ">
													
												</div>
											</div>
										</td>
									</tr>
									
									
									
									<tr>
										<th scope="row"><label><?php _e('Enable Service Box Same Height'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#colorbox_hight_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div class="switch">
												<input type="radio" class="switch-input" name="sb_set_same_height" value="yes" id="enable_colorbox_same_height" <?php if($sb_set_same_height == 'yes' ) { echo "checked"; } ?>  >
												<label for="enable_colorbox_same_height" class="switch-label switch-label-off"><?php _e('Yes'); ?></label>
												<input type="radio" class="switch-input" name="sb_set_same_height" value="no" id="disable_colorbox_same_height"  <?php if($sb_set_same_height == 'no' ) { echo "checked"; } ?> >
												<label for="disable_colorbox_same_height" class="switch-label switch-label-on"><?php _e('No'); ?></label>
												<span class="switch-selection"></span>
											</div>
											<!-- Tooltip -->
											<div id="colorbox_hight_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;">
													<h2 style="color:#fff !important;"><?php _e('Enable Or Disbale Same Height Option For Service Box Here'); ?></h2>
													<img style="width:500px;" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_same_height.png'; ?>">
												</div>
											</div>
											<br />
											<!--label>Note: Same height is not apply if Masonry Effect is enable and loading animation applied</label-->
										</td>
									</tr>
									<tr style="display:none;">
										<th scope="row"><label><?php _e('Open Link In New Tab'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#link_in_new_tab_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div class="switch">
												<input type="radio" class="switch-input" name="sb_set_link_open_in" value="yes" id="enable_link_in_new_tab" <?php if($sb_set_link_open_in == 'yes' ) { echo "checked"; } ?>  >
												<label for="enable_link_in_new_tab" class="switch-label switch-label-off"><?php _e('Yes'); ?></label>
												<input type="radio" class="switch-input" name="sb_set_link_open_in" value="no" id="disable_link_in_new_tab"  <?php if($sb_set_link_open_in == 'no' ) { echo "checked"; } ?> >
												<label for="disable_link_in_new_tab" class="switch-label switch-label-on"><?php _e('No'); ?></label>
												<span class="switch-selection"></span>
											</div>
											<!-- Tooltip -->
											<div id="link_in_new_tab_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;">
													<h2 style="color:#fff !important;"><?php _e('Want to Open link in new tab ,set here'); ?></h2>
												</div>
											</div>
										</td>
									</tr>
									
									<tr>
										<th scope="row"><label><?php _e('Link Type '); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#link_type_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_link_type"  value="1" <?php if($sb_set_link_type == '1' ) { echo "checked"; } ?> onclick="fn_sb_link_type()" /> Only Text </span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_link_type"  value="2" <?php if($sb_set_link_type == '2' ) { echo "checked"; } ?> onclick="fn_sb_link_type()" /> Only Icon </span>
											
											<span style="display:block"><input type="radio" name="sb_set_link_type" value="3"  <?php if($sb_set_link_type == '3' ) { echo "checked"; } ?> onclick="fn_sb_link_type()" /> Both Text and Icon </span>
											
											<!-- Tooltip -->
											<div id="link_type_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;">
													<h2 style="color:#fff !important;"><?php _e('Service Box Link Type '); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_link_icon.png'; ?> ">
												</div>
											</div>
										</td>
									</tr>
									<tr class="sb_link_icon_cls" style="<?php if($sb_set_link_type=='1'){echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Service Box Link Icon '); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#sb_link_icon_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div class="form-group input-group wpsm_input_group" style="">
											<input  class="form-control regular-text" id="sb_set_link_icon" name="sb_set_link_icon" value="<?php if($sb_set_link_icon){echo $sb_set_link_icon;} ?>" type="text" readonly="readonly" />
											<span style="" class="wpsm_input_icon_group_addon input-group-addon icon-picker <?php if($sb_set_link_icon){ echo $sb_set_link_icon;} ?>" id="sb_set_link_icon_span" data-target="#sb_set_link_icon"></span>
											</div>
											<!-- Tooltip -->
											<div id="sb_link_icon_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;">
													<h2 style="color:#fff !important;"><?php _e('Service Box Link Icon '); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_link_icon.png'; ?> ">
												</div>
											</div>
										</td>
									</tr>
									<tr class="sb_link_icon_pos_cls" style="<?php if($sb_set_link_type=='1' || $sb_set_link_type=='2'){echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Service Box Link Icon Position '); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#sb_link_icon_type_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_link_icon_position"  value="before" <?php if($sb_set_link_icon_position == 'before' ) { echo "checked"; } ?> /> Before Link Text </span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_link_icon_position"  value="after" <?php if($sb_set_link_icon_position == 'after' ) { echo "checked"; } ?> /> After Link Text </span>
											<!-- Tooltip -->
											<div id="sb_link_icon_type_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;">
													<h2 style="color:#fff !important;"><?php _e('Service Box Link Icon Position'); ?></h2>
													<div style="display:inline-block;">
														<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_link_icon.png'; ?> "/>
														<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_link_icon_2.png'; ?> "/>
													</div>
												</div>
											</div>
										</td>
									</tr>
								</tbody>	
							</table>
						</div>
                    </div>
                </div>
				<!-- ***************************************************************************************************************
								Carousel Settings
				****************************************************************************************************************** -->								
				<div class="panel panel-default carousel_view_settings_cls"  style="<?php if($sb_all_contents_view_type=='grid'){echo "display:none;";}?>">
                    <div class="panel-heading" role="tab" id="headingFive">
                        <h4 class="panel-title">
                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#sb_pro_accordion_id" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive" >
                                <span class="left-icon"></span>
                                SERVICE BOX CAROUSEL SETTINGS
                            </a>
                        </h4>
                    </div>
                    <div id="collapseFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFive">
                        <div class="panel-body">
							<table class="form-table acc_table carousel_view_settings_cls" style="<?php if($sb_all_contents_view_type=='grid'){echo "display:none;";}?>">
								<tbody>		
									<!-- Carousel Loop -->
									<tr>
										<th scope="row"><label><?php _e('Infinity loop'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#Carousel_loop_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div class="switch">
												<input type="radio" class="switch-input" name="sb_set_carousel_loop" value="true" id="sb_set_carousel_loop_yes" <?php if($sb_set_carousel_loop == 'true' ) { echo "checked"; } ?>  >
												<label for="sb_set_carousel_loop_yes" class="switch-label switch-label-off"><?php _e('Yes'); ?></label>
												<input type="radio" class="switch-input" name="sb_set_carousel_loop" value="false" id="sb_set_carousel_loop_no"  <?php if($sb_set_carousel_loop == 'false' ) { echo "checked"; } ?> >
												<label for="sb_set_carousel_loop_no" class="switch-label switch-label-on"><?php _e('No'); ?></label>
												<span class="switch-selection"></span>
											</div>
											<!-- Tooltip -->
											<div id="Carousel_loop_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;">
													<h2 style="color:#fff !important;"><?php _e('Enable/Disable Carousel Scroll Infinite Time'); ?></h2>
												</div>
											</div>
										</td>
									</tr>
									<!-- Carousel Mouse drag enabled -->
									<tr>
										<th scope="row"><label><?php _e('Mouse drag enabled'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#Carousel_Mouse_drag_enabled_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div class="switch">
												<input type="radio" class="switch-input" name="sb_set_carousel_Mouse_drag_enabled" value="true" id="sb_set_carousel_Mouse_drag_enabled_yes" <?php if($sb_set_carousel_Mouse_drag_enabled == 'true' ) { echo "checked"; } ?>  >
												<label for="sb_set_carousel_Mouse_drag_enabled_yes" class="switch-label switch-label-off"><?php _e('Yes'); ?></label>
												<input type="radio" class="switch-input" name="sb_set_carousel_Mouse_drag_enabled" value="false" id="sb_set_carousel_Mouse_drag_enabled_no"  <?php if($sb_set_carousel_Mouse_drag_enabled == 'false' ) { echo "checked"; } ?> >
												<label for="sb_set_carousel_Mouse_drag_enabled_no" class="switch-label switch-label-on"><?php _e('No'); ?></label>
												<span class="switch-selection"></span>
											</div>
											<!-- Tooltip -->
											<div id="Carousel_Mouse_drag_enabled_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;">
													<h2 style="color:#fff !important;"><?php _e('Enable/Disable Carousel slide from mouse drag'); ?></h2>
												</div>
											</div>
										</td>
									</tr>
									<!-- Carousel touch drag enabled -->
									<tr style="display:none">
										<th scope="row"><label><?php _e('Touch drag enabled'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#Carousel_touch_drag_enabled_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div class="switch">
												<input type="radio" class="switch-input" name="sb_set_carousel_touch_drag_enabled" value="true" id="sb_set_carousel_touch_drag_enabled_yes" <?php if($sb_set_carousel_touch_drag_enabled == 'true' ) { echo "checked"; } ?>  >
												<label for="sb_set_carousel_touch_drag_enabled_yes" class="switch-label switch-label-off"><?php _e('Yes'); ?></label>
												<input type="radio" class="switch-input" name="sb_set_carousel_touch_drag_enabled" value="false" id="sb_set_carousel_touch_drag_enabled_no"  <?php if($sb_set_carousel_touch_drag_enabled == 'false' ) { echo "checked"; } ?> >
												<label for="sb_set_carousel_touch_drag_enabled_no" class="switch-label switch-label-on"><?php _e('No'); ?></label>
												<span class="switch-selection"></span>
											</div>
											<!-- Tooltip -->
											<div id="Carousel_touch_drag_enabled_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;">
													<h2 style="color:#fff !important;"><?php _e('Want to Open link in new tab ,set here'); ?></h2>
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Carousel Autoplay -->
									<tr >
										<th scope="row"><label><?php _e('Autoplay Enable'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#Carousel_autoplay_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div class="switch">
												<input type="radio" class="switch-input" name="sb_set_carousel_autoplay" value="true" id="sb_set_carousel_autoplay_yes" <?php if($sb_set_carousel_autoplay == 'true' ) { echo "checked"; } ?>  >
												<label for="sb_set_carousel_autoplay_yes" class="switch-label switch-label-off"><?php _e('Yes'); ?></label>
												<input type="radio" class="switch-input" name="sb_set_carousel_autoplay" value="false" id="sb_set_carousel_autoplay_no"  <?php if($sb_set_carousel_autoplay == 'false' ) { echo "checked"; } ?> >
												<label for="sb_set_carousel_autoplay_no" class="switch-label switch-label-on"><?php _e('No'); ?></label>
												<span class="switch-selection"></span>
											</div>
											<!-- Tooltip -->
											<div id="Carousel_autoplay_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;">
													<h2 style="color:#fff !important;"><?php _e('Enable/Disable Carousel Auto Slide'); ?></h2>
												</div>
											</div>
										</td>
									</tr>
									<!-- Carousel Autoplay Hover Pause -->
									<tr >
										<th scope="row"><label><?php _e('Autoplay Hover Pause'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#Carousel_autoplay_hover_pause_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div class="switch">
												<input type="radio" class="switch-input" name="sb_set_carousel_autoplay_hover_pause" value="true" id="sb_set_carousel_autoplay_hover_pause_yes" <?php if($sb_set_carousel_autoplay_hover_pause == 'true' ) { echo "checked"; } ?>  >
												<label for="sb_set_carousel_autoplay_hover_pause_yes" class="switch-label switch-label-off"><?php _e('Yes'); ?></label>
												<input type="radio" class="switch-input" name="sb_set_carousel_autoplay_hover_pause" value="false" id="sb_set_carousel_autoplay_hover_pause_no"  <?php if($sb_set_carousel_autoplay_hover_pause == 'false' ) { echo "checked"; } ?> >
												<label for="sb_set_carousel_autoplay_hover_pause_no" class="switch-label switch-label-on"><?php _e('No'); ?></label>
												<span class="switch-selection"></span>
											</div>
											<!-- Tooltip -->
											<div id="Carousel_autoplay_hover_pause_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;">
													<h2 style="color:#fff !important;"><?php _e('Stop Carousel Auto Slide on mouse over'); ?></h2>
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Carousel Autoplay speed btn -->
									<tr >
										<th scope="row"><label><?php _e('Autoplay / Navigation/ Dots Speed Enable'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#Carousel_autoplay_speed_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div class="switch">
												<input type="radio" class="switch-input" name="sb_set_carousel_autoplay_speed_y_n" value="true" id="sb_set_carousel_autoplay_speed_y_n_yes" <?php if($sb_set_carousel_autoplay_speed_y_n == 'true' ) { echo "checked"; } ?>  >
												<label for="sb_set_carousel_autoplay_speed_y_n_yes" class="switch-label switch-label-off"><?php _e('Yes'); ?></label>
												<input type="radio" class="switch-input" name="sb_set_carousel_autoplay_speed_y_n" value="false" id="sb_set_carousel_autoplay_speed_y_n_no"  <?php if($sb_set_carousel_autoplay_speed_y_n == 'false' ) { echo "checked"; } ?> >
												<label for="sb_set_carousel_autoplay_speed_y_n_no" class="switch-label switch-label-on"><?php _e('No'); ?></label>
												<span class="switch-selection"></span>
											</div>
											<!-- Tooltip -->
											<div id="Carousel_autoplay_speed_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;">
													<h2 style="color:#fff !important;"><?php _e('Enable/Disable Autoplay or Navigation or Dots Speed'); ?></h2>
												</div>
											</div>
										</td>
									</tr>
									<!-- Carousel Autoplay Speed -->
									<tr class="setting_color">
										<th><label><?php _e('Autoplay / Navigation/ Dots Speed'); ?> </label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#carousel_autoplay_speed_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div id="sb_set_carousel_autoplay_speed_id" class="size-slider" ></div>
											<input type="text" class="slider-text" id="sb_set_carousel_autoplay_speed" name="sb_set_carousel_autoplay_speed"  readonly="readonly">
											<!-- Tooltip -->
											<div id="carousel_autoplay_speed_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width: 300px;">
													<h2 style="color:#fff !important;">You can update Carousel Slide Speed from here. Just Scroll it to change size.</h2>
													
												</div>
											</div>
										</td>
									</tr>
									<!-- Carousel Autoplay interval timeout. -->
									<tr class="setting_color">
										<th><label><?php _e('Autoplay interval timeout'); ?> </label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#carousel_autoplay_interval_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div id="sb_set_carousel_autoplay_interval_time_out_id" class="size-slider" ></div>
											<input type="text" class="slider-text" id="sb_set_carousel_autoplay_interval_time_out" name="sb_set_carousel_autoplay_interval_time_out"  readonly="readonly">
											<!-- Tooltip -->
											<div id="carousel_autoplay_interval_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width: 300px;">
													<h2 style="color:#fff !important;">You can update Carousel Autoplay Time Interval between two slide from here. Just Scroll it to change size.</h2>
													
												</div>
											</div>
										</td>
									</tr>
									<!-- Carousel Navigation type. -->
									<tr>
										<th scope="row"><label><?php _e('Carousel Navigation Type'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#Carousel_nav_type_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_type"  value="1" <?php if($sb_set_carousel_nav_type == '1' ) { echo "checked"; } ?> onclick="fn_carousel_nav_type()"/> Only Buttons </span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_type"  value="2" <?php if($sb_set_carousel_nav_type == '2' ) { echo "checked"; } ?> onclick="fn_carousel_nav_type()"/> Only Dots/Bullets</span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_type"  value="3" <?php if($sb_set_carousel_nav_type == '3' ) { echo "checked"; } ?> onclick="fn_carousel_nav_type()"/> Both Buttons and Dots</span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_type"  value="4" <?php if($sb_set_carousel_nav_type == '4' ) { echo "checked"; } ?> onclick="fn_carousel_nav_type()"/> None </span>
											
											<!-- Tooltip -->
											<div id="Carousel_nav_type_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;text-align:center;max-width:300px;">
													<h2 style="color:#fff !important;"><?php _e('Service Box Carousel Navigation Type'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_Carousel_nav_type.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Carousel Navigation Button type. -->
									<tr class="sb_set_carousel_nav_type_button_class" id="sb_set_carousel_nav_type_button_id" style="<?php if($sb_set_carousel_nav_type == '2' || $sb_set_carousel_nav_type == '4' ) {echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Service Box Carousel Navigation Button Type'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#Carousel_nav_btn_type_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_btn_type"  value="1" <?php if($sb_set_carousel_nav_btn_type == '1' ) { echo "checked"; } ?> onclick="fn_carousel_nav_type()"/> Only Text </span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_btn_type"  value="2" <?php if($sb_set_carousel_nav_btn_type == '2' ) { echo "checked"; } ?> onclick="fn_carousel_nav_type()"/> Only Icon</span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_btn_type"  value="3" <?php if($sb_set_carousel_nav_btn_type == '3' ) { echo "checked"; } ?> onclick="fn_carousel_nav_type()"/> Both Text and Icon</span>
											
											
											<!-- Tooltip -->
											<div id="Carousel_nav_btn_type_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;text-align:center;max-width:300px;">
													<h2 style="color:#fff !important;"><?php _e('Service Box Carousel Navigation Button Type'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_Carousel_nav_btn_type.png'; ?>">
													
												</div>
											</div>
										</td>
									</tr>
									<!-- Carousel Navigation btn icon type. -->
									<tr class="ap_cl_group sb_set_carousel_nav_type_button_class sb_set_carousel_nav_btn_type_icon_class" style="<?php if($sb_set_carousel_nav_type == '2' || $sb_set_carousel_nav_type == '4' ||  $sb_set_carousel_nav_btn_type=='1' ) {echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Carousel Navigation Button Icon Type'); ?></label>
										<a  class="ac_tooltip" href="#help" data-tooltip="#Carousel_nav_btn_icon_type_tp"><i class="fa fa-lightbulb-o"></i></a>
											</th>
										<td>
											<div class="col-md-6 col-sm-6 op_cl_icon_box">
												<div class="sb_btn_sel_icon_wrapper sel-icon-wrapper <?php if($sb_set_carousel_nav_btn_icon_type == '1' ) { echo "active"; } ?>" id="sb_btn_icon_1" onclick="select_btn_icon(1)">
													<div class="checked"><i class="fa fa-check"></i></div>
													<div class="lefti"><i class="fa fa-angle-double-left"></i></div>
													<div class="righti"><i class="fa fa-angle-double-right"></i></div>
													<input type="radio" name="sb_set_carousel_nav_btn_icon_type" id="sb_set_carousel_nav_btn_icon_type1" value="1"  <?php if($sb_set_carousel_nav_btn_icon_type == '1' ) { echo "checked"; } ?> style="display:none;"   />
												</div>	
											</div>
											<div class="col-md-6 col-sm-6 op_cl_icon_box">
												<div class="sb_btn_sel_icon_wrapper sel-icon-wrapper <?php if($sb_set_carousel_nav_btn_icon_type == '2' ) { echo "active"; } ?>" id="sb_btn_icon_2" onclick="select_btn_icon(2)">
													<div class="checked"><i class="fa fa-check"></i></div>
													<div class="lefti"><i class="fa fa-arrow-left"></i></div>
													<div class="righti"><i class="fa fa-arrow-right"></i></div>
													<input type="radio" name="sb_set_carousel_nav_btn_icon_type" id="sb_set_carousel_nav_btn_icon_type2" value="2"  <?php if($sb_set_carousel_nav_btn_icon_type == '2' ) { echo "checked"; } ?> style="display:none;"   />
												</div>	
											</div>
											<div class="col-md-6 col-sm-6 op_cl_icon_box">
												<div class="sb_btn_sel_icon_wrapper sel-icon-wrapper <?php if($sb_set_carousel_nav_btn_icon_type == '3' ) { echo "active"; } ?>" id="sb_btn_icon_3" onclick="select_btn_icon(3)">
													<div class="checked"><i class="fa fa-check"></i></div>
													<div class="lefti"><i class="fa fa-angle-left"></i></div>
													<div class="righti"><i class="fa fa-angle-right"></i></div>
													<input type="radio" name="sb_set_carousel_nav_btn_icon_type" id="sb_set_carousel_nav_btn_icon_type3" value="3"  <?php if($sb_set_carousel_nav_btn_icon_type == '3' ) { echo "checked"; } ?> style="display:none;"   />
												</div>	
											</div>
											<div class="col-md-6 col-sm-6 op_cl_icon_box">
												<div class="sb_btn_sel_icon_wrapper sel-icon-wrapper <?php if($sb_set_carousel_nav_btn_icon_type == '4' ) { echo "active"; } ?>" id="sb_btn_icon_4" onclick="select_btn_icon(4)">
													<div class="checked"><i class="fa fa-check"></i></div>
													<div class="lefti"><i class="fa fa-chevron-left"></i></div>
													<div class="righti"><i class="fa fa-chevron-right"></i></div>
													<input type="radio" name="sb_set_carousel_nav_btn_icon_type" id="sb_set_carousel_nav_btn_icon_type4" value="4"  <?php if($sb_set_carousel_nav_btn_icon_type == '4' ) { echo "checked"; } ?> style="display:none;"   />
												</div>	
											</div>
											<!-- Tooltip -->
											<div id="Carousel_nav_btn_icon_type_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;text-align:center;max-width:300px;">
													<h2 style="color:#fff !important;"><?php _e('Service Box Carousel Navigation Button Icon Type'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_Carousel_nav_btn_type.png'; ?>">
													
												</div>
											</div>
											
											
											
										</td>
									</tr>
									<!-- Carousel Navigation Right Text . -->
									<tr class="box_link_enable_class sb_set_carousel_nav_type_button_class sb_set_carousel_nav_btn_type_txt_class" style="<?php if($sb_set_carousel_nav_type == '2' || $sb_set_carousel_nav_type == '4' ||  $sb_set_carousel_nav_btn_type=='2' ) {echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Carousel Navigation Button Right Text'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#Carousel_nav_right_text_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div class="">
												<input type="text" class="form-control" name="sb_set_carousel_nav_right_text" value="<?php if($sb_set_carousel_nav_right_text) { echo $sb_set_carousel_nav_right_text; } ?>" id="sb_set_carousel_rewind_yes">
												
											</div>
											<!-- Tooltip -->
											<div id="Carousel_nav_right_text_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;text-align:center;max-width:300px;">
													<h2 style="color:#fff !important;"><?php _e('Service Box Carousel Navigation Button Right Text'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_Carousel_nav_btn_type.png'; ?>">
													
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Carousel Navigation Left Text . -->
									<tr class="box_link_enable_class sb_set_carousel_nav_type_button_class sb_set_carousel_nav_btn_type_txt_class" style="<?php if($sb_set_carousel_nav_type == '2' || $sb_set_carousel_nav_type == '4' ||  $sb_set_carousel_nav_btn_type=='2' ) {echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Carousel Navigation Button Left Text'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#Carousel_nav_left_text_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div class="">
												<input type="text" class="form-control" name="sb_set_carousel_nav_left_text" value="<?php if($sb_set_carousel_nav_left_text) { echo $sb_set_carousel_nav_left_text; } ?>" id="sb_set_carousel_rewind_yes">
												
											</div>
											<!-- Tooltip -->
											<div id="Carousel_nav_left_text_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;text-align:center;max-width:300px;">
													<h2 style="color:#fff !important;"><?php _e('Service Box Carousel Navigation Button Left Text'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_Carousel_nav_btn_type.png'; ?>">
													
												</div>
											</div>
										</td>
									</tr>
									<!-- Carousel Navigation Btn size. -->
									<tr class="sb_set_carousel_nav_type_button_class carousel_nav_btn_color" id="" style="<?php if($sb_set_carousel_nav_type == '2' || $sb_set_carousel_nav_type == '4' ) {echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Carousel Navigation Button Size'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#Carousel_nav_Button_type_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_btn_size"  value="1" <?php if($sb_set_carousel_nav_btn_size == '1' ) { echo "checked"; } ?> /> Small </span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_btn_size"  value="2" <?php if($sb_set_carousel_nav_btn_size == '2' ) { echo "checked"; } ?> /> Medium</span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_btn_size"  value="3" <?php if($sb_set_carousel_nav_btn_size == '3' ) { echo "checked"; } ?> /> Large</span>
											
											
											<!-- Tooltip -->
											<div id="Carousel_nav_Button_type_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;text-align:center;max-width:300px;">
													<h2 style="color:#fff !important;"><?php _e('Service Box Carousel Navigation Button Size'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_Carousel_nav_btn_type.png'; ?>">
													
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Navigation Font Color -->
									<tr class="sb_set_carousel_nav_type_button_class carousel_nav_btn_color" style="<?php if($sb_set_carousel_nav_type == '2' || $sb_set_carousel_nav_type == '4') {echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Carousel Navigation Button Font Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#service_box_nav_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
										</th>
										<td>
											<input id="sb_set_nav_clr" name="sb_set_nav_clr" type="text" value="<?php echo $sb_set_nav_clr;  ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="service_box_nav_clr_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;text-align:center;max-width:300px;">
													<h2 style="color:#fff !important;"><?php _e('Service Box Carousel Navigation Button Font Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_Carousel_nav_btn_type.png'; ?>">
													
												</div>
											</div>
										</td>
									</tr>
									<!-- Navigation Hover Font Color -->
									<tr class="sb_set_carousel_nav_type_button_class carousel_nav_btn_color" style="<?php if($sb_set_carousel_nav_type == '2' || $sb_set_carousel_nav_type == '4') {echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Carousel Navigation Button Hover Font Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#service_box_hover_nav_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
										</th>
										<td>
											<input id="sb_set_hover_nav_clr" name="sb_set_hover_nav_clr" type="text" value="<?php echo $sb_set_hover_nav_clr;  ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="service_box_hover_nav_clr_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;text-align:center;max-width:300px;">
													<h2 style="color:#fff !important;"><?php _e('Service Box Carousel on mouse over Navigation Button Font Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_Carousel_nav_btn_type.png'; ?>">
													
												</div>
											</div>
										</td>
									</tr>
									<!-- Navigation BAckground Color -->
									<tr class="sb_set_carousel_nav_type_button_class carousel_nav_btn_color" style="<?php if($sb_set_carousel_nav_type == '2' || $sb_set_carousel_nav_type == '4'  ) {echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Carousel Navigation Button Background Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#service_box_nav_bg_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
										</th>
										<td>
											<input id="sb_set_nav_bg_clr" name="sb_set_nav_bg_clr" type="text" value="<?php echo $sb_set_nav_bg_clr;  ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="service_box_nav_bg_clr_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width:300px;text-align:center;">
													<h2 style="color:#fff !important;"><?php _e('Service Box Carousel Navigation Button Background Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_Carousel_nav_btn_type.png'; ?>">
													
												</div>
											</div>
										</td>
									</tr>
									<!-- Navigation Btn Hover BAckground Color -->
									<tr class="sb_set_carousel_nav_type_button_class carousel_nav_btn_color" style="<?php if($sb_set_carousel_nav_type == '2' || $sb_set_carousel_nav_type == '4' ) {echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Carousel Navigation Button Hover Background Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#service_box_hover_nav_bg_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
										</th>
										<td>
											<input id="sb_set_hover_nav_bg_clr" name="sb_set_hover_nav_bg_clr" type="text" value="<?php echo $sb_set_hover_nav_bg_clr;  ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="service_box_hover_nav_bg_clr_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;max-width:300px;text-align:center;">
													<h2 style="color:#fff !important;"><?php _e('Service Box Carousel On Mouse Over Navigation Button Background Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_Carousel_nav_btn_type.png'; ?>">
													
												</div>
											</div>
										</td>
									</tr>
									<!-- Carousel Navigation Btn Position. -->
									<tr class="sb_set_carousel_nav_pos_class" style="<?php if($sb_set_carousel_nav_type=='2' || $sb_set_carousel_nav_type=='4'){echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Carousel Navigation Button Position'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#Carousel_nav_position_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_position"  value="1" <?php if($sb_set_carousel_nav_position == '1' ) { echo "checked"; } ?> /> Bottom Center </span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_position"  value="2" <?php if($sb_set_carousel_nav_position == '2' ) { echo "checked"; } ?> /> Bottom Left</span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_position"  value="3" <?php if($sb_set_carousel_nav_position == '3' ) { echo "checked"; } ?> /> Bottom Right</span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_position"  value="4" <?php if($sb_set_carousel_nav_position == '4' ) { echo "checked"; } ?> /> Top Center </span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_position"  value="5" <?php if($sb_set_carousel_nav_position == '5' ) { echo "checked"; } ?> /> Top Left </span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_position"  value="6" <?php if($sb_set_carousel_nav_position == '6' ) { echo "checked"; } ?> /> Top Right </span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_position"  value="7" <?php if($sb_set_carousel_nav_position == '7' ) { echo "checked"; } ?> /> On Items </span>
											<!-- Tooltip -->
											<div id="Carousel_nav_position_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;text-align:center;max-width:300px;">
													<h2 style="color:#fff !important;"><?php _e('Service Box Carousel Navigation Button Position'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_Carousel_nav_btn_pos1.png'; ?>">
													<br>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_Carousel_nav_btn_pos2.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									
									<!-- Carousel Navigation Dots size. -->
									<tr class="sb_set_carousel_nav_type_dots_class" id="" style="<?php if($sb_set_carousel_nav_type == '1' || $sb_set_carousel_nav_type == '4' ) {echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Carousel Navigation Dots Size'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#Carousel_nav_dots_type_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_dots_type"  value="1" <?php if($sb_set_carousel_nav_dots_type == '1' ) { echo "checked"; } ?> /> Small </span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_dots_type"  value="2" <?php if($sb_set_carousel_nav_dots_type == '2' ) { echo "checked"; } ?> /> Medium</span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_dots_type"  value="3" <?php if($sb_set_carousel_nav_dots_type == '3' ) { echo "checked"; } ?> /> Large</span>
											
											
											<!-- Tooltip -->
											<div id="Carousel_nav_dots_type_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;text-align:center;max-width:300px;">
													<h2 style="color:#fff !important;"><?php _e('Carousel Navigation Dots Size'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_Carousel_nav_dots.png'; ?>">
													
												</div>
											</div>
										</td>
									</tr>
									<!-- Carousel Navigation Dots shape. -->
									<tr class="sb_set_carousel_nav_type_dots_class" id="" style="<?php if($sb_set_carousel_nav_type == '1' || $sb_set_carousel_nav_type == '4' ) {echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Carousel Navigation Dots Shape'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#Carousel_nav_dots_shape_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_dots_shape"  value="1" <?php if($sb_set_carousel_nav_dots_shape == '1' ) { echo "checked"; } ?>/>  Circle</span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_dots_shape"  value="2" <?php if($sb_set_carousel_nav_dots_shape == '2' ) { echo "checked"; } ?> /> Ractangle</span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_dots_shape"  value="3" <?php if($sb_set_carousel_nav_dots_shape == '3' ) { echo "checked"; } ?> /> Square</span>
											<span style="display:block;margin-bottom:10px"><input type="radio" name="sb_set_carousel_nav_dots_shape"  value="4" <?php if($sb_set_carousel_nav_dots_shape == '4' ) { echo "checked"; } ?> /> Oval </span>
											
											
											<!-- Tooltip -->
											<div id="Carousel_nav_dots_shape_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;text-align:center;max-width:300px;">
													<h2 style="color:#fff !important;"><?php _e('Carousel Navigation Dots Shape'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_Carousel_nav_dots.png'; ?>">
													
												</div>
											</div>
										</td>
									</tr>
									<!-- Navigation Dots BAckground Color -->
									<tr class="sb_set_carousel_nav_type_dots_class" style="<?php if($sb_set_carousel_nav_type == '1' || $sb_set_carousel_nav_type == '4' ) {echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Carousel Navigation Dots Background Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#service_box_dots_bg_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
										</th>
										<td>
											<input id="sb_set_dots_bg_clr" name="sb_set_dots_bg_clr" type="text" value="<?php echo $sb_set_dots_bg_clr;  ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="service_box_dots_bg_clr_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;text-align:center;max-width:300px;">
													<h2 style="color:#fff !important;"><?php _e('Carousel Navigation Dots Background Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_Carousel_nav_dots.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									<!-- Navigation Hover Dots BAckground Color -->
									<tr class="sb_set_carousel_nav_type_dots_class" style="<?php if($sb_set_carousel_nav_type == '1' || $sb_set_carousel_nav_type == '4' ) {echo "display:none;";}?>">
										<th scope="row"><label><?php _e('Carousel Navigation Dots Hover Background Color'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#service_box_hover_dots_bg_clr_tp"><i class="fa fa-lightbulb-o"></i></a>
										</th>
										<td>
											<input id="sb_set_hover_dots_bg_clr" name="sb_set_hover_dots_bg_clr" type="text" value="<?php echo $sb_set_hover_dots_bg_clr;  ?>" class="my-color-field" data-default-color="#ffffff" />
											<!-- Tooltip -->
											<div id="service_box_hover_dots_bg_clr_tp" style="display:none;">
												<div class="wpsm_sb_tooltip_div" style="color:#fff !important;padding:10px;text-align:center;max-width:300px;">
													<h2 style="color:#fff !important;"><?php _e('Carousel Navigation Dots Hover Background Color'); ?></h2>
													<img class="sb_tooltip_img_sec_bg" src="<?php echo service_box_directory_url.'assets/tooltip/img/sb_Carousel_nav_dots.png'; ?>">
												</div>
											</div>
										</td>
									</tr>
									<!-- Carousel Navigation speed btn -->
									<tr style="display:none" >
										<th scope="row"><label><?php _e('Navigation Speed Enable'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#Carousel_nav_speed_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div class="switch">
												<input type="radio" class="switch-input" name="sb_set_carousel_nav_speed_y_n" value="true" id="sb_set_carousel_nav_speed_y_n_yes" <?php if($sb_set_carousel_nav_speed_y_n == 'true' ) { echo "checked"; } ?>  >
												<label for="sb_set_carousel_nav_speed_y_n_yes" class="switch-label switch-label-off"><?php _e('Yes'); ?></label>
												<input type="radio" class="switch-input" name="sb_set_carousel_nav_speed_y_n" value="false" id="sb_set_carousel_nav_speed_y_n_no"  <?php if($sb_set_carousel_nav_speed_y_n == 'false' ) { echo "checked"; } ?> >
												<label for="sb_set_carousel_nav_speed_y_n_no" class="switch-label switch-label-on"><?php _e('No'); ?></label>
												<span class="switch-selection"></span>
											</div>
											<!-- Tooltip -->
											<div id="Carousel_nav_speed_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;">
													<h2 style="color:#fff !important;"><?php _e('Want to Open link in new tab ,set here'); ?></h2>
												</div>
											</div>
										</td>
									</tr>
									
									
									<!-- Carousel Dots speed btn -->
									<tr  style="display:none">
										<th scope="row"><label><?php _e('Dots Speed Enable'); ?></label>
											<a  class="ac_tooltip" href="#help" data-tooltip="#Carousel_dots_speed_tp"><i class="fa fa-lightbulb-o"></i></a>
											
										</th>
										<td>
											<div class="switch">
												<input type="radio" class="switch-input" name="sb_set_carousel_dots_speed_y_n" value="true" id="sb_set_carousel_dots_speed_y_n_yes" <?php if($sb_set_carousel_dots_speed_y_n == 'true' ) { echo "checked"; } ?>  >
												<label for="sb_set_carousel_dots_speed_y_n_yes" class="switch-label switch-label-off"><?php _e('Yes'); ?></label>
												<input type="radio" class="switch-input" name="sb_set_carousel_dots_speed_y_n" value="false" id="sb_set_carousel_dots_speed_y_n_no"  <?php if($sb_set_carousel_dots_speed_y_n == 'false' ) { echo "checked"; } ?> >
												<label for="sb_set_carousel_dots_speed_y_n_no" class="switch-label switch-label-on"><?php _e('No'); ?></label>
												<span class="switch-selection"></span>
											</div>
											<!-- Tooltip -->
											<div id="Carousel_dots_speed_tp" style="display:none;">
												<div style="color:#fff !important;padding:10px;">
													<h2 style="color:#fff !important;"><?php _e('Want to Open link in new tab ,set here'); ?></h2>
												</div>
											</div>
										</td>
									</tr>
									
									
							</tbody>
							</table>
						</div>
                    </div>
                </div>
            </div>
        </div>
    </div>



<script>
jQuery('.ac_tooltip').darkTooltip({
		opacity:1,
		gravity:'east',
		size:'small'
	});
</script>
		
<script>
function hide_shadow(){
	 value = jQuery("input[name=colorbox_shadow]:checked").val();
	 if(value=="yes"){
		jQuery(".colorbox_shadow_class").show(500);
	 }else{
		jQuery(".colorbox_shadow_class").hide(500);
	 }
}

function hide_color_setting(){
	value = jQuery("input[name=sb_ind_clr_enable]:checked").val();
	if(value=="no"){
		jQuery(".sb_ind_clr_enable_class").show(500);
		jQuery(".sb_ind_clr_option_class").hide(500);
	}else{
		jQuery(".sb_ind_clr_enable_class").hide(500);
		jQuery(".sb_ind_clr_option_class").show(500);
	}
}

function hide_link(){
	value = jQuery("input[name=box_link_enable]:checked").val();
	if(value=="yes"){
		jQuery(".box_link_enable_class").show(500);
	}else{
		jQuery(".box_link_enable_class").hide(500);
	}
}
</script>

<Script>
function fn_sec_bg_type()
{
value = jQuery("input[name=sb_set_sec_bg_type]:checked").val();
value_2=jQuery("input[name=sb_set_sec_bg_overlay_y_n]:checked").val();

	switch(value)
	{
		case "1":  //Transparent
				jQuery(".section_bg_clr_cls").hide(500);
				jQuery(".section_bg_img_cls").hide(500);
				jQuery(".section_bg_img_overlay_cls").hide(500);
		break;
		case "2":  //Color
				jQuery(".section_bg_img_cls").hide(500);
				jQuery(".section_bg_img_overlay_cls").hide(500);
				jQuery(".section_bg_clr_cls").show(500);
		break;
		case "3":  //Image
				jQuery(".section_bg_clr_cls").hide(500);
				jQuery(".section_bg_img_cls").show(500);
				if(value_2=="yes"){
						jQuery(".section_bg_img_overlay_cls").show(500);
				}else{
						jQuery(".section_bg_img_overlay_cls").hide(500);
				}
		break;
	}
}
//carousel navigation type Click
function fn_carousel_nav_type()
{
	value = jQuery("input[name=sb_set_carousel_nav_type]:checked").val();
	value_2=jQuery("input[name=sb_set_carousel_nav_btn_type]:checked").val();
	switch(value)
	{
		case "1":  //Only buttons
				jQuery("#sb_set_carousel_nav_type_button_id").show(500);
				jQuery(".sb_set_carousel_nav_type_dots_class").hide(500);
				jQuery(".carousel_nav_btn_color").show(500);
				jQuery(".sb_set_carousel_nav_pos_class").show(500);
				
				fn_show_hide_btn_txt_icon(value_2);
		break;
		case "2":  //Only Dots
				jQuery(".sb_set_carousel_nav_type_dots_class").show(500);
				jQuery(".sb_set_carousel_nav_type_button_class").hide(500);
				jQuery(".sb_set_carousel_nav_pos_class").hide(500);
		break;
		case "3":  //Both Btn+Dots 
				jQuery("#sb_set_carousel_nav_type_button_id").show(500);
				jQuery(".carousel_nav_btn_color").show(500);
				jQuery(".sb_set_carousel_nav_type_dots_class").show(500);
				jQuery(".sb_set_carousel_nav_pos_class").show(500);
				
				fn_show_hide_btn_txt_icon(value_2);
		break;
		default:
				jQuery(".sb_set_carousel_nav_type_button_class").hide(500);
				jQuery(".sb_set_carousel_nav_type_dots_class").hide(500);
				jQuery(".sb_set_carousel_nav_pos_class").hide(500);
	}
}
//Nested fn
function fn_show_hide_btn_txt_icon(btn_type_val){
	switch(btn_type_val)
		{
			case "1":  //Only Txt
					jQuery(".sb_set_carousel_nav_btn_type_txt_class").show(500);
					jQuery(".sb_set_carousel_nav_btn_type_icon_class").hide(500);
			break;
			case "2":  //Only Icon
					jQuery(".sb_set_carousel_nav_btn_type_icon_class").show(500);
					jQuery(".sb_set_carousel_nav_btn_type_txt_class").hide(500);
			break;
			case "3":  //Only Both Txt+Icon
					jQuery(".sb_set_carousel_nav_btn_type_txt_class").show(500);
					jQuery(".sb_set_carousel_nav_btn_type_icon_class").show(500);
			break;
		}
}

//select navigation btn icon  type
function select_btn_icon(id){
	jQuery(".sb_btn_sel_icon_wrapper").removeClass("active");
	jQuery("#sb_btn_icon_"+id).addClass("active");
	jQuery("#sb_set_carousel_nav_btn_icon_type"+id).prop( "checked", true );
}

//same width of image and icon
function same_width_img_icon()
{
	value = jQuery("input[name=sb_set_same_icon_img_width]:checked").val();
	 if(value=="no"){
		jQuery("#image_size_id").show(500);
	 }else{
		jQuery("#image_size_id").hide(500);
	 }
}  
</script>
<script>
function fn_sb_link_type()
{
value = jQuery("input[name=sb_set_link_type]:checked").val();

	switch(value)
	{
		case "1":  //text
				jQuery(".sb_link_icon_cls").hide(500);
				jQuery(".sb_link_icon_pos_cls").hide(500);
		break;
		case "2":  //Icon
				jQuery(".sb_link_icon_cls").show(500);
		break;
		case "3":  //Both
				jQuery(".sb_link_icon_cls").show(500);
				jQuery(".sb_link_icon_pos_cls").show(500);
		break;
	}
}
</script>
<Script>
//Section title size 
jQuery(function() {	
		jQuery( "#sb_set_sec_title_size_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 200,
		min: 20,
		slide: function( event, ui ) {
		jQuery( "#sb_set_sec_title_size" ).val( ui.value );
		}
		});
		jQuery( "#sb_set_sec_title_size_id" ).slider("value",<?php if($sb_set_sec_title_size){echo $sb_set_sec_title_size;}else{echo "30";} ?>);
		jQuery( "#sb_set_sec_title_size" ).val( jQuery( "#sb_set_sec_title_size_id" ).slider( "value") );
});
</script>

<Script>		
//Section BG Opacity 
jQuery(function() {	
		jQuery( "#sb_set_sec_bg_opacity_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 100,
		min: 10,
		slide: function( event, ui ) {
		jQuery( "#sb_set_sec_bg_opacity" ).val( ui.value );
		}
		});
		jQuery( "#sb_set_sec_bg_opacity_id" ).slider("value",<?php if($sb_set_sec_bg_opacity){echo $sb_set_sec_bg_opacity;}else{echo "75";} ?>);
		jQuery( "#sb_set_sec_bg_opacity" ).val( jQuery( "#sb_set_sec_bg_opacity_id" ).slider( "value") );
});
</script>
<Script>		
//Section BG Opacity 
jQuery(function() {	
		jQuery( "#sb_set_bg_opacity_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 100,
		min: 0,
		slide: function( event, ui ) {
		jQuery( "#sb_set_bg_opacity" ).val( ui.value );
		}
		});
		jQuery( "#sb_set_bg_opacity_id" ).slider("value",<?php if($sb_set_bg_opacity=="0"){echo "0";}elseif($sb_set_bg_opacity!="0"){echo $sb_set_bg_opacity;}else{echo "75";} ?>);
		jQuery( "#sb_set_bg_opacity" ).val( jQuery( "#sb_set_bg_opacity_id" ).slider( "value") );
});
</script>
<Script>
 //Section Hr Padding 
jQuery(function() {	
		jQuery( "#sb_set_sec_hr_padding_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 200,
		min: 1,
		slide: function( event, ui ) {
		jQuery( "#sb_set_sec_hr_padding" ).val( ui.value );
		}
		});
		jQuery( "#sb_set_sec_hr_padding_id" ).slider("value",<?php if($sb_set_sec_hr_padding){echo $sb_set_sec_hr_padding;}else{echo "15";} ?>);
		jQuery( "#sb_set_sec_hr_padding" ).val( jQuery( "#sb_set_sec_hr_padding_id" ).slider( "value") );
}); 
</script>

<Script> 
  //Section Vertical Padding 
jQuery(function() {	
		jQuery( "#sb_set_sec_ver_padding_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 200,
		min: 1,
		slide: function( event, ui ) {
		jQuery( "#sb_set_sec_ver_padding" ).val( ui.value );
		}
		});
		jQuery( "#sb_set_sec_ver_padding_id" ).slider("value",<?php if($sb_set_sec_ver_padding){echo $sb_set_sec_ver_padding;}else{echo "15";} ?>);
		jQuery( "#sb_set_sec_ver_padding" ).val( jQuery( "#sb_set_sec_ver_padding_id" ).slider( "value") );
});
</script>

<Script>
//carousel Autoplay Interval
jQuery(function() {	
		jQuery( "#sb_set_carousel_autoplay_speed_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 9999,
		min: 100,
		slide: function( event, ui ) {
		jQuery( "#sb_set_carousel_autoplay_speed" ).val( ui.value );
		}
		});
		jQuery( "#sb_set_carousel_autoplay_speed_id" ).slider("value",<?php if($sb_set_carousel_autoplay_speed){echo $sb_set_carousel_autoplay_speed;}else{echo "1000";} ?>);
		jQuery( "#sb_set_carousel_autoplay_speed" ).val( jQuery( "#sb_set_carousel_autoplay_speed_id" ).slider( "value") );
});
</script>

<Script>
//carousel Autoplay Interval
jQuery(function() {	
    jQuery( "#sb_set_carousel_autoplay_interval_time_out_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 5000,
		min: 200,
		slide: function( event, ui ) {
		jQuery( "#sb_set_carousel_autoplay_interval_time_out" ).val( ui.value );
		}
		});
		jQuery( "#sb_set_carousel_autoplay_interval_time_out_id" ).slider("value",<?php if($sb_set_carousel_autoplay_interval_time_out){echo $sb_set_carousel_autoplay_interval_time_out;}else{echo "1000";} ?>);
		jQuery( "#sb_set_carousel_autoplay_interval_time_out" ).val( jQuery( "#sb_set_carousel_autoplay_interval_time_out_id" ).slider( "value") );
});
</script>

<Script>
jQuery(function() {
	hide_color_setting();
	jQuery( "#sb_set_title_size_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 40,
		min:10,
		slide: function( event, ui ) {
		jQuery( "#sb_set_title_size" ).val( ui.value );
	  }
		});
		
		jQuery( "#sb_set_title_size_id" ).slider("value",<?php if($sb_set_title_size){echo $sb_set_title_size;}else{echo "1000";} ?> );
		jQuery( "#sb_set_title_size" ).val( jQuery( "#sb_set_title_size_id" ).slider( "value") );

});
</script>
<Script>

 //minimum flake size script
  jQuery(function() {
    jQuery( "#sb_set_icon_size_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 150,
		min:10,
		slide: function( event, ui ) {
		jQuery( "#sb_set_icon_size" ).val( ui.value );
      }
		});
		
		jQuery( "#sb_set_icon_size_id" ).slider("value",<?php if($sb_set_icon_size){echo $sb_set_icon_size;}else{echo "1000";} ?>);
		jQuery("#sb_set_icon_size").val( jQuery( "#sb_set_icon_size_id" ).slider( "value") );
    
  });
</script>
<Script>

 //minimum flake size script
  jQuery(function() {
    jQuery( "#sb_set_image_size_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 200,
		min:10,
		slide: function( event, ui ) {
		jQuery( "#sb_set_image_size" ).val( ui.value );
      }
		});
		
		jQuery( "#sb_set_image_size_id" ).slider("value",<?php if($sb_set_image_size){echo $sb_set_image_size;}else{echo "1000";} ?>);
		jQuery("#sb_set_image_size").val( jQuery( "#sb_set_image_size_id" ).slider( "value") );
    
  });
</script>
<Script>

 //minimum flake size script
  jQuery(function() {
    jQuery( "#sb_set_des_size_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 30,
		min:5,
		slide: function( event, ui ) {
		jQuery( "#sb_set_des_size" ).val( ui.value );
      }
		});
		
		jQuery( "#sb_set_des_size_id" ).slider("value",<?php echo $sb_set_des_size; ?>);
		jQuery( "#sb_set_des_size" ).val( jQuery( "#sb_set_des_size_id" ).slider( "value") );
    
  });
</script>
<Script>

 //minimum flake size script
  jQuery(function() {
    jQuery( "#sb_set_link_size_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 30,
		min:5,
		slide: function( event, ui ) {
		jQuery( "#sb_set_link_size" ).val( ui.value );
      }
		});
		
		jQuery( "#sb_set_link_size_id" ).slider("value",<?php echo $sb_set_link_size; ?>);
		jQuery( "#sb_set_link_size" ).val( jQuery( "#sb_set_link_size_id" ).slider( "value") );
    
  });
</script>

<Script>

 //minimum flake size script
  jQuery(function() {
    jQuery( "#sb_set_brdr_size_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 10,
		min:0,
		slide: function( event, ui ) {
		jQuery( "#sb_set_brdr_size" ).val( ui.value );
      }
		});
		
		jQuery( "#sb_set_brdr_size_id" ).slider("value",<?php echo $sb_set_brdr_size; ?>);
		jQuery( "#sb_set_brdr_size" ).val( jQuery( "#sb_set_brdr_size_id" ).slider( "value") );
    
  });
</script> 