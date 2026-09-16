<?php
add_shortcode( 'TABS_PRO', 'TABS_pro_ShortCode' );
function TABS_PRO_ShortCode( $Id ) {
	ob_start();	
	
	if(!isset($Id['id'])) {
		$WPSM_Tabs_ID = "";
	} 
	else {
		$WPSM_Tabs_ID = $Id['id'];
	}

	if ( defined( 'ET_CORE_VERSION' ) ) {
		echo '<div class="et_smooth_scroll_disabled" >';
			require("content.php"); 
        echo '</div>';
	} else{
		require("content.php"); 
	}

	wp_reset_query();
    return ob_get_clean();
}
?>