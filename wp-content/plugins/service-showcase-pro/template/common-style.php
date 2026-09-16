<style>
	.wpsm_sb_container_<?php echo $PostId;?>
	{
		position: relative;
		overflow: hidden;
		display:block;
		width:100%;
		<?php 
		if($sb_set_sec_bg_type=='3')
		{
		?>
			background-size: <?php echo $sb_set_sec_bg_size;?>;
			background-position: <?php echo $sb_set_sec_bg_position;?>;
			background-repeat: <?php echo $sb_set_sec_bg_repeat;?>;
			background-attachment: <?php if($sb_set_sec_bg_Parallax_y_n=='yes'){echo "fixed";}else{echo "scroll";}?>;
			background-image: url("<?php echo $sb_set_sec_bg_img;?>");
		<?php 
		} ?>
	}	
	.wpsm_sb_section_overlay_<?php echo $PostId;?> 
	{
	   padding-left:<?php echo $sb_set_sec_hr_padding;?>px;
	   padding-right:<?php echo $sb_set_sec_hr_padding;?>px;
	   padding-top:<?php echo $sb_set_sec_ver_padding;?>px;
	   padding-bottom:<?php echo $sb_set_sec_ver_padding;?>px;
	   <?php 
	    if($sb_set_sec_bg_type=='1')
		{ 
			?>
			background:transparent;
			<?php 
		}
	   elseif($sb_set_sec_bg_type=='2')
		{ 
			?>
			background-color:<?php echo $sb_set_sec_bg_clr;?>;
			<?php 
		}
		elseif($sb_set_sec_bg_type=='3' && $sb_set_sec_bg_overlay_y_n=='yes')  
		{
			 if($sb_set_sec_bg_opacity<=99){ ?>
				background-color: rgba(<?php echo HextoR($sb_set_sec_bg_img_over_clr).",".HextoG($sb_set_sec_bg_img_over_clr).",".HextoB($sb_set_sec_bg_img_over_clr);?>, 0.<?php echo $sb_set_sec_bg_opacity;?>);<?php } 
			 else{ ?>
				background-color: rgb(<?php echo HextoR($sb_set_sec_bg_img_over_clr).",".HextoG($sb_set_sec_bg_img_over_clr).",".HextoB($sb_set_sec_bg_img_over_clr);?>);<?php }
		}?>	
	}
	
	<?php //wpsm_title
	if($sb_set_sec_title_y_n=='yes')
	{?>
		.wpsm_sb_section_title_<?php echo $PostId;?> h3
		{	
			margin-top:15px !important;
			margin-bottom:40px !important;
			display:block;
			overflow:hidden;
			color:<?php echo $sb_set_sec_title_clr;?>;
			font-size:<?php echo $sb_set_sec_title_size;?>px;
			font-weight:500;
			text-align: center;
			line-height:normal;
		}
	<?php
	}
	//titlr end
	?>
	
	.wpsm_sb_wrapper_<?php echo $PostId;?>{
		display:block;
		overflow:hidden;
	}
	@media screen and (max-width: 1200px){
		<?php if($sb_set_sec_hr_padding>30 && $sb_set_sec_ver_padding>30){
					$var_hr_padding=$sb_set_sec_hr_padding;
					$var_ver_padding=$sb_set_sec_ver_padding;
			  }else{
					$var_hr_padding=30;
					$var_ver_padding=30;
			  }
		?>
		.wpsm_sb_section_overlay_<?php echo $PostId;?>{  padding:<?php echo $var_ver_padding;?>px <?php echo $var_hr_padding;?>px; }
	}
	@media screen and (max-width: 990px){
		.wpsm_sb_section_overlay_<?php echo $PostId;?>{  padding:12px; }
	}
	@media screen and (max-width: 600px){
		.wpsm_sb_section_overlay_<?php echo $PostId;?>{  padding:5px; }
	}
	
	.wpsm_sb_container_<?php echo $PostId;?> .glyphicon{
		display:inline-block;
	}
	<?php
	//carousel style
	if($sb_all_contents_view_type=="carousel")
	{	
		/* *******************************************************************************************************
							Navigation Btn Size and shape
		********************************************************************************************************* */	
		?>
		.wpsm_sb_container_<?php echo $PostId;?> .owl-nav .owl-prev,
		.wpsm_sb_container_<?php echo $PostId;?> .owl-nav .owl-next
		{	
			font-weight:700;
			<?php
			switch($sb_set_carousel_nav_btn_size)
			{	
				case(1):  //Small*******************************************
					?>
						font-size:14px;
					<?php
				break;
				case(2):  //Medium
					?>
						font-size:16px;padding:6px 12px;
					<?php	
				break;
				case(3):  //Large
					?>
						font-size:18px;padding:8px 15px;
					<?php	
				break;
				
			}
			?>
		}
		
		<?php
		/* *******************************************************************************************************
					navigation btn position & Btn Color
		********************************************************************************************************* */												
		if($sb_set_carousel_nav_type=='1' || $sb_set_carousel_nav_type=='3')
		{
			if($sb_set_carousel_nav_position!=1)
			{ 
				switch($sb_set_carousel_nav_position)
				{	
					case(2):  //bottom left
						?>			
						.wpsm_sb_container_<?php echo $PostId;?> .owl-nav{text-align:left;}
						<?php
					break;
					case(3):  //bottom right
						?>			
						.wpsm_sb_container_<?php echo $PostId;?> .owl-nav{text-align:right;}
						<?php
					break;
					case(4):  //top center
						?>			
						.wpsm_sb_container_<?php echo $PostId;?> .owl-nav{position:absolute;top:0;width:100%;margin:auto;}
						#wpsm_service_box_carousel_<?php echo $PostId;?> .owl-stage-outer{margin-top:50px;}
						<?php
					break;
					case(5):	//top left
						?>			
						.wpsm_sb_container_<?php echo $PostId;?> .owl-nav{position:absolute;top:0;}
						#wpsm_service_box_carousel_<?php echo $PostId;?> .owl-stage-outer{margin-top:50px;}
						<?php
					break;
					case(6):	//top right
						?>			
						.wpsm_sb_container_<?php echo $PostId;?> .owl-nav{position:absolute;top:0;right:0;}
						#wpsm_service_box_carousel_<?php echo $PostId;?> .owl-stage-outer{margin-top:50px;}
						<?php
					break;
					case(7):	//On Items
						?>
								
						.wpsm_sb_container_<?php echo $PostId;?> .owl-nav {position:absolute;width:100%;top:45%;}
						.wpsm_sb_container_<?php echo $PostId;?> .owl-nav .owl-next{position:relative;float:right;vertical-align:middle;}
						.wpsm_sb_container_<?php echo $PostId;?> .owl-nav .owl-prev{position:relative;float:left;}
						<?php
					break;
				}//switch end
			}
			//navigation color
			?>
			.wpsm_sb_container_<?php echo $PostId;?> .owl-nav .owl-prev{color:<?php echo $sb_set_nav_clr;?>;background-color:<?php echo $sb_set_nav_bg_clr;?>;}
			.wpsm_sb_container_<?php echo $PostId;?> .owl-nav .owl-next{color:<?php echo $sb_set_nav_clr;?>;background-color:<?php echo $sb_set_nav_bg_clr;?>;}
			.wpsm_sb_container_<?php echo $PostId;?> .owl-nav .owl-prev:hover{color:<?php echo $sb_set_hover_nav_clr;?>;background-color:<?php echo $sb_set_hover_nav_bg_clr;?>;}
			.wpsm_sb_container_<?php echo $PostId;?> .owl-nav .owl-next:hover{color:<?php echo $sb_set_hover_nav_clr;?>;background-color:<?php echo $sb_set_hover_nav_bg_clr;?>;}
			
			<?php
		}
		/* *******************************************************************************************************
						Adding Navigation Dots & Dots Color
		********************************************************************************************************* */	
		if($sb_set_carousel_nav_type=='2' || $sb_set_carousel_nav_type=='3')  //For Either dots 
		{
			switch($sb_set_carousel_nav_dots_type)
				{	
					case(1):  //Small
							switch($sb_set_carousel_nav_dots_shape)
							{	
								case(2):  //Ractangle
									?>
									.wpsm_sb_container_<?php echo $PostId;?> .owl-dots .owl-dot span{width:25px;border-radius:0;}
									<?php	
								break;
								case(3):  //Square
									?>
									.wpsm_sb_container_<?php echo $PostId;?> .owl-dots .owl-dot span{border-radius:0;}
									<?php	
								break;
								case(4):  //Oval
									?>
									.wpsm_sb_container_<?php echo $PostId;?> .owl-dots .owl-dot span{width:25px;}
									<?php	
								break;
							}	
					break;
					case(2):  //Medium
							switch($sb_set_carousel_nav_dots_shape)
							{	
								case(1):  //Circle
									?>
									.wpsm_sb_container_<?php echo $PostId;?> .owl-dots .owl-dot span{width:22px;height:22px;}
									<?php	
								break;
								case(2):  //Ractangle
									?>
									.wpsm_sb_container_<?php echo $PostId;?> .owl-dots .owl-dot span{width:22px;height:15px;border-radius:0;}
									<?php	
								break;
								case(3):  //Square
									?>
									.wpsm_sb_container_<?php echo $PostId;?> .owl-dots .owl-dot span{width:20px;height:20px;border-radius:0;}
									<?php	
								break;
								case(4):  //Oval
									?>
									.wpsm_sb_container_<?php echo $PostId;?> .owl-dots .owl-dot span{width:22px;height:15px;}
									<?php	
								break;
							}	
					break;
					case(3):  //Large
							switch($sb_set_carousel_nav_dots_shape)
							{	
								case(1):  //Circle
									?>
									.wpsm_sb_container_<?php echo $PostId;?> .owl-dots .owl-dot span{width:25px;height:25px;}
									<?php	
								break;
								case(2):  //Ractangle
									?>
									.wpsm_sb_container_<?php echo $PostId;?> .owl-dots .owl-dot span{width:28px;height:20px;border-radius:0;}
									<?php	
								break;
								case(3):  //Square
									?>
									.wpsm_sb_container_<?php echo $PostId;?> .owl-dots .owl-dot span{width:25px;height:25px;border-radius:0;}
									<?php	
								break;
								case(4):  //Oval
									?>
									.wpsm_sb_container_<?php echo $PostId;?> .owl-dots .owl-dot span{width:27px;height:20px;border-radius:12px;}
									<?php	
								break;
							}	
					break;
					
				}
			?>
			.wpsm_sb_container_<?php echo $PostId;?> .owl-theme .owl-dots .owl-dot span{
				background-color:<?php echo $sb_set_dots_bg_clr;?>;
			}
			.wpsm_sb_container_<?php echo $PostId;?> .owl-theme .owl-dots .owl-dot.active span, 
			.wpsm_sb_container_<?php echo $PostId;?> .owl-theme .owl-dots .owl-dot:hover span{
				background-color:<?php echo $sb_set_hover_dots_bg_clr;?>;
			}
			<?php
		}
	}//caraousel if end
	
?>	

</style>