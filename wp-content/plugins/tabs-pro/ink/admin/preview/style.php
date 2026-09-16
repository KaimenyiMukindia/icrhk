<style>
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a,
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover,
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:focus{
		outline: none;
		text-decoration: none;
	}
	#wpsm_tab_container_<?php echo $post_id; ?> a,
	#wpsm_tab_container_<?php echo $post_id; ?> a:hover{
		-webkit-box-shadow:none;
		box-shadow:none;
	}
	#wpsm_tab_container_<?php echo $post_id; ?>{
		overflow: hidden;
		display: block;
		width: 100%;
		border: 0px solid #ddd;
		margin-bottom: 30px;
		margin-top:20px;
	}
	#wpsm_tab_container_<?php echo $post_id; ?> ul{
		padding:0;
		margin:0;
	}
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
		margin-left:0;
	}
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a i{
		height:auto;
	}
	#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_nav li .wpsm_tabs_img_icon,
	#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_nav li .wpsm-icons{
		vertical-align:middle;
	}
	@media only screen and (max-width: 600px) {
		.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
			float:none;
		}
		
	}
	<?php 
	/* -------------------------------Horizontal Style ----------------------- */
	if($templates_presets=='1')
	{
		// Horizontal Tabs Btn Position
		if($tabs_button_align=="center" && $tabs_button_width_option!="2")
		{?>
			#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs
			{
			text-align:center;	
			}
			#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li
			{
			float:none;
			display:inline-block;
			margin-right: -4px;
			}
			
		<?php
		}
		elseif($tabs_button_align=="center" && $tabs_button_width_option=="2")
		{
		?>
			#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li
			{
			float:left !important;
			}
			
		<?php
		}
		elseif($tabs_button_align=="right")
		{
		?>
			#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li
			{
			float:right;
			}
			
		<?php
		}
		//Horizontal media queries
		?>
		@media only screen and (min-width: 601px) 
		{
			<?php
			if($tabs_button_align=="center")
			{ ?>
				#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs
				{
				text-align:center;	
				}
				#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li
				{
				float:none;
				display:inline-block;
				margin-right: -4px;
				}
				
			<?php
			}
			elseif($tabs_button_align=="right")
			{?>
				#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li
				{
				float:right;
				}
			<?php
			}
			elseif($tabs_button_align=="left")
			{?>
				#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li
				{
				float:left;
				}
			<?php
			}
			?>
		}
		
		<?php
		//Horizontal Tabs Btn Width
		if($tabs_button_width_option=="2")  //equal
		{?>
		#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
			width:<?php echo 100/$TotalCount;?>%;
			margin-right: 0;
		}
		<?php
		}
		elseif($tabs_button_width_option=="3") //custum
		{?>
		#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
			width:<?php echo $tabs_button_width;?>px;
		}
		<?php
		}
	}
	/* -------------------------------Vertical Style ----------------------- */
	else 
	{	
		//Vertical  Tabs Btn Position
		if($tabs_button_align=="right")
		{
			?>
			#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs
			{
			float:right;
			}
		<?php
		}
		
		//Vertical Tabs Btn Width
		if($tabs_button_width_option=="3") //custum
		{?>
		#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
			width:<?php echo $tabs_button_width;?>px;
		}
		<?php
		}
		
	}
	
	/* ----------------Common Style For Both Horizontal and vertical----------------- */
	//Icon Image Width and height
	?>
	#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_nav li .wpsm_tabs_img_icon{
		box-shadow:none;
		-webkit-box-shadow:none;
		<?php if($tabs_custom_image_size=="2")
		{?>
			width:<?php echo $tabs_title_size;?>px;
			height:<?php echo $tabs_title_size;?>px;
		<?php
		}
		elseif($tabs_custom_image_size=="3")
		{?>
			width:<?php echo $tab_img_icon_w;?>px;
			height:<?php echo $tab_img_icon_h;?>px;
		<?php
		}?>
	}
	
	#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a
	{
		<?php // Tabs Btn Style Soft/Noise/Bubble/Glass
		switch($tabs_styles)
		{
			case "1":
			?>
				background-image: none !important;
			<?php
			break;
			case "2":
			 ?>
				background-image: url(<?php echo wpshopmart_tabs_pro_directory_url.'assets/images/style-soft.png' ; ?>) !important;
				background-position: 0 0 !important;
				background-repeat: repeat-x !important;
			<?php
			break;
			case "3":
			?>
				background-image: url(<?php echo wpshopmart_tabs_pro_directory_url.'assets/images/style-noise.png' ; ?>) !important;
				background-position: 0 0 !important;
				background-repeat: repeat-x !important;
			<?php
			break;
			case "4":
			?>
				background-image: url(<?php echo wpshopmart_tabs_pro_directory_url.'assets/images/style-bubbles.png'; ?>) !important;
				background-position: 0 50% !important;
				background-repeat: repeat-x !important;
			<?php
			break;
			case "5":
			?>
				background-image: url(<?php echo wpshopmart_tabs_pro_directory_url.'assets/images/style-glass.png'; ?>) !important;
				background-position: 0 50% !important;
				background-repeat: repeat-x !important;
			<?php
			break;
		}
		?>
	}
	
	<?php
	//Tabs Content Height
	if($tabs_content_height_option=="2")
	{?>
		#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
			padding:25px 10px 25px 25px !important;
		}
		@media only screen and (max-width: 600px) {
			#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
				padding:15px 8px 15px 15px !important;
			}
		}
		#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content .tab-pane{
			height:<?php echo $tabs_content_height;?>px;
			overflow:auto;
		}
		
		/* -------------Scrolling CSS--------------------------- */
		#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .mCS-tabs_pro_scroll{
			
		}
		#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .mCS-tabs_pro_scroll.mCSB_scrollTools .mCSB_draggerRail{
			width: <?php echo $tabs_content_bar_width; ?>px;
			background-color: <?php echo $tabs_content_bar_bg_clr; ?>; 
			/* background-color: rgba(<?php echo HextoR($tabs_content_bar_bg_clr).",".HextoG($tabs_content_bar_bg_clr).",".HextoB($tabs_content_bar_bg_clr);?>,0.2);*/
		}
		#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .mCS-tabs_pro_scroll.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar{ 
			width: <?php echo $tabs_content_bar_width; ?>px; 
			background-color:<?php echo $tabs_content_bar_hndl_bg_clr; ?>;
			background-color: rgba(<?php echo HextoR($tabs_content_bar_hndl_bg_clr).",".HextoG($tabs_content_bar_hndl_bg_clr).",".HextoB($tabs_content_bar_hndl_bg_clr);?>,0.75); 
		}

		#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .mCS-tabs_pro_scroll.mCSB_scrollTools .mCSB_dragger:hover .mCSB_dragger_bar{ 
			background-color:<?php echo $tabs_content_bar_hndl_bg_clr; ?>; 
			background-color: rgba(<?php echo HextoR($tabs_content_bar_hndl_bg_clr).",".HextoG($tabs_content_bar_hndl_bg_clr).",".HextoB($tabs_content_bar_hndl_bg_clr);?>,0.85);  
		}

		#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .mCS-tabs_pro_scroll.mCSB_scrollTools .mCSB_dragger:active .mCSB_dragger_bar,
		#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_<?php echo $post_id; ?> .mCS-tabs_pro_scroll.mCSB_scrollTools .mCSB_dragger.mCSB_dragger_onDrag .mCSB_dragger_bar{
			background-color:<?php echo $tabs_content_bar_hndl_bg_clr; ?>; 
			background-color: rgba(<?php echo HextoR($tabs_content_bar_hndl_bg_clr).",".HextoG($tabs_content_bar_hndl_bg_clr).",".HextoB($tabs_content_bar_hndl_bg_clr);?>,0.9);  
		}
		
	<?php
	}
	
	//Tabs Btn Icon Align(inline/block/)
	?>
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a i,
	.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a img
	{	
		<?php
		if($show_tabs_icon_align=='block')
		{?>
			display:block;
			margin:8px auto 8px;
		<?php
		}
		else
		{
			if($show_tabs_icon_postion=="left")
			{
			?>	
			display:inline;
			margin:0 8px 0 0;
			<?php
			}
			else
			{
			?>	
			display:inline;
			margin:0 0 0 8px;
			<?php
			}	
		}
		?>
	}
	<?php echo $custom_css; ?>
</style>