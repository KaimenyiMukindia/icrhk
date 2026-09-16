<?php
add_shortcode( 'servicebox_sc', 'Service_box_ShortCode' );
function Service_box_ShortCode( $Id ) {
	ob_start();	
	if(!isset($Id['id'])) 
	 {
		$sb_shortcode_id = "";
	 } 
	else 
	{
		$sb_shortcode_id = $Id['id'];
	}
	require("content.php"); 
	
	wp_reset_postdata();
    return ob_get_clean();
}
?>