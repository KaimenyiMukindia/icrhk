<?php 
wp_enqueue_media();
wp_enqueue_style('service_box_admin_boot_style',service_box_directory_url.'assets/css/bootstrap.css');
wp_enqueue_style('service_box_admin_custum-style',service_box_directory_url.'assets/css/style.css');
wp_enqueue_style('service_box_admin_custum_settings',service_box_directory_url.'assets/css/settings.css');
wp_enqueue_script('jquery');

wp_enqueue_script('service_box_admin_boot_script',service_box_directory_url.'assets/js/bootstrap.js');

//Sidebar
wp_enqueue_style('service_box_admin_design_modal_style', service_box_directory_url.'assets/css/sidebar.css');

//icon picker
wp_enqueue_style('service_box_admin_font_awesome', service_box_directory_url.'assets/css/font-awesome/css/font-awesome.min.css');	
	
wp_enqueue_script('service_box_admin_font-icon-picker-js_all',service_box_directory_url.'assets/js/icon_picker/icon-picker.js');
wp_enqueue_style('service_box_admin_font-icon-picker_all', service_box_directory_url.'assets/css/icon_picker/icon-picker.css');	
wp_enqueue_style('service_box_admin_font-icon-picker-glyphicon_style',service_box_directory_url.'assets/css/icon_picker/picker/glyphicon.css');
wp_enqueue_style('service_box_admin_font-icon-picker-dashicons_style',service_box_directory_url.'assets/css/icon_picker/picker/dashicons.css');
//Media upload
wp_enqueue_script('service_box_admin_media_upload', service_box_directory_url.'assets/js/media-upload-script.js');	

//color picker
wp_enqueue_style( 'wp-color-picker' ); 
wp_enqueue_script('service_box_admin_color_picker_js', service_box_directory_url.'assets/js/color-picker.js', array( 'wp-color-picker' ), false, true );

//Code Mirrer
wp_enqueue_style('service_box_admin_codemirror-css', service_box_directory_url.'assets/codex/codemirror.css');
wp_enqueue_style('service_box_admin_ambiance', service_box_directory_url.'assets/codex/ambiance.css');
wp_enqueue_style('service_box_admin_show-hint-css', service_box_directory_url.'assets/codex/show-hint.css');

wp_enqueue_script('service_box_admin_codemirror-js',service_box_directory_url.'assets/codex/codemirror.js',array('jquery'));
wp_enqueue_script('service_box_admin_css-js',service_box_directory_url.'assets/codex/css.js',array('jquery'));
wp_enqueue_script('service_box_admin_hint-js',service_box_directory_url.'assets/codex/css-hint.js',array('jquery'));

// Same Height
wp_enqueue_script('service_box_admin_same_height_script',service_box_directory_url.'assets/js/same_height/jcolumn.min.js');

//tooltip
wp_enqueue_style('service_box_admin_tooltip', service_box_directory_url.'assets/tooltip/darktooltip.css');
wp_enqueue_script( 'service_box_admin_tooltip_js', service_box_directory_url.'assets/tooltip/jquery.darktooltip.js');

//font slider(for size slider)
wp_enqueue_style('service_box_admin_font_slider_style', service_box_directory_url.'assets/css/font_slider.css');
?>