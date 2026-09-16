<div class="wpsm_sb_container_<?php echo $PostId;?>">
	<div class="wpsm_sb_section_overlay_<?php echo $PostId;?>">
		<?php echo $wpsm_title;?>
		<!-- Wrapper div start -->
		<div class='<?php echo $sb_wrapper_cls;?>' id='<?php echo $sb_wrapper_id;?>'> 
			<?php $i=1;
			
			foreach($sb_all_data as $sb_single_set_data)
			{  	
				foreach($data_option_names as $data_option_name => $data_default_value) 
				{	
					${"" . $data_option_name} = $sb_single_set_data[$data_option_name];							
				}
				//********************************variable declaration**************************
				$sb_link_icon="";
				$link_target="";
				$sb_link_text="";
				$sb_grid_clear_both="";
				$wpsm_sb_link="";
				if($sb_all_contents_btn_link_yes_no=="Yes")
				{
					 //Getting Link Target
					if($sb_all_contents_btn_link_open_in=='Yes'){$link_target="target='_blank'";}
					
				    switch($sb_set_link_type)
					{
						case "1":
							$sb_link_text=$sb_all_contents_btn_link_text;
					    break;
						case "2":
							$sb_link_text="<i class='$sb_set_link_icon'></i>";
						break;
						case "3":
							if($sb_set_link_icon_position=='before'){
								$sb_link_text="<i class='$sb_set_link_icon'></i>&nbsp;&nbsp;".$sb_all_contents_btn_link_text;
							}
							else{
								$sb_link_text=$sb_all_contents_btn_link_text."&nbsp;&nbsp;<i class='$sb_set_link_icon'></i>";
							}
						break;
					}
				    $wpsm_sb_link="<a class='wpsm_read_more btn btn-default' href='$sb_all_contents_btn_link_url' $link_target>$sb_link_text</a>";
			    }
				
				//For Grid
				if($sb_all_contents_view_type=="grid"){$sb_grid_clear_both= "<div style='clear:both'></div>";}
					
				?>
				<div class="<?php echo $sb_Layout_Cls;?>">
					<div class="wpsm_serviceBox_<?php echo $PostId;?>" id="wpsm_serviceBox_<?php echo $PostId."_".$i;?>">
						<div class="wpsm_service_icon" >
							<?php if($sb_all_contents_btn_img_or_icon=='Icon'){?>
								<i class='<?php echo $sb_all_contents_icons;?>'></i><?php } 
							else { ?>
								<img src='<?php echo $sb_all_contents_images;?>' style=''/><?php } ?>
						</div>
						<div class="wpsm_service_content">
							<h3 class="wpsm_title"><?php echo $sb_all_contents_title;?></h3>
							<p class="wpsm_description"><?php echo $sb_all_contents_description;?></p>
							<?php echo $wpsm_sb_link;?>
							
						</div>				
					</div>
				</div>
				<?php if($i%$row==0)  //for grid
						echo $sb_grid_clear_both;
			 $i++;
			}//for end
					?>
		</div> <!-- Carouel/Grid Wrapper end-->
	</div>	<!-- Overlay end-->
</div>	<!-- Sb Container end-->

 
<script>
jQuery(document).ready(function($){
<?php 
/* *******************************************************************************************************
                                                Carousel
********************************************************************************************************* */	

if($sb_all_contents_view_type=="carousel")
{?>
		  
	  var owl = $(".wpsm_service_box_carousel_<?php echo $PostId;?>");
	  owl.owlCarousel({
		margin: 10,
		mouseDrag: <?php echo $sb_set_carousel_Mouse_drag_enabled;?>,
		nav: <?php echo $carousel_nav_type;?>,
		dots:<?php echo $carousel_dots_type ;?>,
		loop: <?php echo $carousel_loop;?>,
		responsiveClass: true,
		responsiveBaseElement:'#wpsm_service_box_carousel_<?php echo $PostId;?>',
		responsiveRefreshRate:500,
		navText:["<?php echo $nav_left_text;?>","<?php echo $nav_right_text;?>"],
		autoplay:<?php echo $carousel_autoplay;?>,
		autoplayTimeout:<?php echo $autoplay_interval_time_out;?>,
		smartSpeed:<?php echo $autoplay_speed;?>,
		autoplayHoverPause:<?php echo $autoplay_hover_pause;?>,
		responsive: {
		  0: {
			items: 1
		  },
		  500: {
			items: 2
		  },
		  800: {
			items: 3
		  },
		  1200: {
			items: <?php echo $no_of_items;?>
		  }
		}
	  })
            
			
<?php 
}//carousel Append end 


//***********************************************making same height
if($sb_set_same_height=="yes") 
{ 	?>
	//jQuery( window ).load(function() {
	var col = new jColumn();
	col.jcolumn('wpsm_serviceBox_<?php echo $PostId;?>');
	//alert(<?php echo $PostId;?>);
	//});
<?php 
} 
?>
	

});//ready end	
</script>					