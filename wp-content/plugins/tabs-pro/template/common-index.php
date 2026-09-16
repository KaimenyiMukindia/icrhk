<?php 
//For ALL Designs

/*$url_tab_id = $_GET['tab_id'];
print_r($url_tab_id);die;*/

 ?>
<div id="wpsm_tab_container_<?php echo $post_id; ?>">
	<div class="wpsm_tab_<?php echo $post_id; ?>"  role="tabpanel">
		
		<!-- wpshopmart tabs pro menu -->
		<ul class="wpsm_nav wpsm_nav-tabs" role="tablist" id="myTab_<?php echo $post_id; ?>">
			<?php foreach($tabs_data as $single_data)
			{
				$tabs_title = stripslashes(base64_decode($single_data['tabs_title']));
				$tabs_title_link = stripslashes($single_data['tabs_title_link']);
				$tabs_title_icon = $single_data['tabs_title_icon'];
				$test_pro_icon_image = $single_data['test_pro_icon_image'];
				$indvid_tabs_title_bg_clr = $single_data['indvid_tabs_title_bg_clr'];
				$indvid_tabs_title_font_clr = $single_data['indvid_tabs_title_font_clr'];
				$indvid_tabs_title_icon_clr = $single_data['indvid_tabs_title_icon_clr'];	
				//Tab Open On Hover
				if($tabs_on_hover=='yes')
				$var_fn_name='onmouseover="fn_open_tab_on_hover(this,\' #tabs_desc_'.$post_id.'_'.$i.' \','.$post_id.')"';
				else
				$var_fn_name="";
				
				//Individual Colors
				if($tab_ind_clr_enable=='yes')
				$var_ind_tabs_bg_and_title_clr='color:'.$indvid_tabs_title_font_clr.';background:'.$indvid_tabs_title_bg_clr;
				else
				$var_ind_tabs_bg_and_title_clr="";
				
				
				?>
				<li id="tabs_li_<?php echo $post_id; ?>_<?php echo $i; ?>" role="presentation" class="<?php if($i==$tabs_number){ echo "active"; } ?>">
					<?php if ($tabs_title_link == "" || $i == $tabs_number): ?>
						<a href="#tabs_desc_<?php echo $post_id; ?>_<?php echo $i; ?>" aria-controls="home" role="tab" data-toggle="tab" <?php echo $var_fn_name; ?> style="<?php echo $var_ind_tabs_bg_and_title_clr; ?>">
							<?php call_fn_tabs_btn($post_id,$i,$show_tabs_title_icon,$tabs_icon_format,$show_tabs_icon_postion,$tabs_title,$tabs_title_icon,$test_pro_icon_image,$tabs_custom_image_size,$tab_img_icon_w,$tab_img_icon_h,$tab_ind_clr_enable,$indvid_tabs_title_icon_clr); ?>
						</a>
					<?php else: ?>
						<a href="<?php echo $tabs_title_link; ?>" target="_blank" <?php echo $var_fn_name; ?> style="<?php echo $var_ind_tabs_bg_and_title_clr; ?>">
							<?php call_fn_tabs_btn($post_id,$i,$show_tabs_title_icon,$tabs_icon_format,$show_tabs_icon_postion,$tabs_title,$tabs_title_icon,$test_pro_icon_image,$tabs_custom_image_size,$tab_img_icon_w,$tab_img_icon_h,$tab_ind_clr_enable,$indvid_tabs_title_icon_clr); ?>
						</a>
					<?php endif; ?>
				</li>
				<?php 
				$i++;
			}
			?>  
		</ul>
		
		<!-- wpshopmart tabs pro Menu Content -->
		<div class="wpsm_tab_content tabs">
			<?php foreach($tabs_data as $single_data)
			{
				$tabs_desc = stripslashes(base64_decode($single_data['tabs_desc']));
				?> 
				<div role="tabpanel" class="tab-pane <?php echo $var_desc_animate_when_auto_h; if($j==$tabs_number){ ?> in active <?php } ?>" id="tabs_desc_<?php echo $post_id; ?>_<?php echo $j; ?>">
					<?php  echo do_shortcode($tabs_desc); ?>
				</div>
				
				<?php
					if($tabs_content_height_option=="2")
					{?>
					<script>
						(function(jQuery){
							jQuery(window).on("load",function(){
								//jQuery("#tabs_desc_<php echo $post_id; ?>_<php echo $j; ?>").mCustomScrollbar({scrollbarPosition: "outside",theme: "dark-3"});
								jQuery("#tabs_desc_<?php echo $post_id; ?>_<?php echo $j; ?>").mCustomScrollbar({theme: "tabs_pro_scroll"});
								jQuery("#tabs_desc_<?php echo $post_id; ?>_<?php echo $j; ?>").children('.mCustomScrollBox').children('.mCSB_container').addClass('<?php echo $var_tabs_desc_animation;?>');
							});
						})(jQuery);
						
					</script>
					<?php 
					}
				$j++;
			}
			?>  
		</div>
		
	</div> 
</div>
<?php call_fn_open_tab_on_hover($post_id,$tabs_on_hover); ?>

<script type="text/javascript">

	jQuery('#myTab_<?php echo $post_id; ?> li a').click(function(e)
	{
	    jQuery(this).attr('href', jQuery(this).attr('href')); // not added in url
	});

	<?php for($k=1;$k<=$i;$k++) { ?>


		var id = jQuery('#tabs_desc_<?php echo $post_id; ?>_<?php echo $k; ?>').attr('id');
		
		if(window.location.hash == '#'+id && location.hash != ""){
			jQuery('#tabs_li_<?php echo $post_id; ?>_<?php echo $tabs_number; ?>').removeClass('active');
			jQuery('#tabs_desc_<?php echo $post_id; ?>_<?php echo $tabs_number; ?>').removeClass('active');

			jQuery('#myTab_<?php echo $post_id; ?> li:first').removeClass('active');
			jQuery('#wpsm_tab_container_<?php echo $post_id; ?> .wpsm_tab_content div:first').removeClass('active');
			jQuery('#tabs_li_<?php echo $post_id; ?>_<?php echo $k; ?>').addClass('active');
		    jQuery('#tabs_desc_<?php echo $post_id; ?>_<?php echo $k; ?>').addClass('active');

		    jQuery('html,body').animate({
				scrollTop: jQuery('#wpsm_tab_container_<?php echo $post_id; ?>').offset().top -150
			}, 800);
		}

	<?php } ?>

</script>

