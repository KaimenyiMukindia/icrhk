<?php 
function service_box_pro_front_script() {
    
		wp_enqueue_script('jquery');
		
		//font awesome css/Icon Picker
		wp_enqueue_style('service_box_front_font_awesome', service_box_directory_url.'assets/css/font-awesome/css/font-awesome.min.css');	
		wp_enqueue_style('service_box_front_end_font-icon-picker-glyphicon_style',service_box_directory_url.'assets/css/icon_picker/picker/glyphicon.css');
		wp_enqueue_style('service_box_front_end_font-icon-picker-dashicons_style',service_box_directory_url.'assets/css/icon_picker/picker/dashicons.css');
		
		//bootstrap
		wp_enqueue_style('service_box_front_boot_style',service_box_directory_url.'assets/css/bootstrap.css');
		wp_enqueue_script('service_box_front_boot_script',service_box_directory_url.'assets/js/bootstrap.js');
		
		// Carousel
		wp_enqueue_script('service_box_front_carousel_script',service_box_directory_url.'assets/js/carousel/owl.carousel.min.js');
		wp_enqueue_style('service_box_front_carousel_style_1', service_box_directory_url.'assets/css/carousel/owl.carousel.min.css');
		wp_enqueue_style('service_box_front_carousel_style_2', service_box_directory_url.'assets/css/carousel/owl.theme.default.min.css');
		
		// Same Height
		wp_enqueue_script('service_box_front_same_height_script',service_box_directory_url.'assets/js/same_height/jcolumn.min.js');
		
		//Layout Style (bootstrap md-5 to md-10)
		wp_enqueue_style('service_box_front_column_layout_style', service_box_directory_url.'assets/css/service-box-columns-layout.css');
}
add_action( 'wp_enqueue_scripts', 'service_box_pro_front_script' );
?>