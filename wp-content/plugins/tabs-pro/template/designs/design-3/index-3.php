<?php 
//Design 3
require('style.php'); ?>
<div id="wpsm_tab_container_<?php echo $post_id; ?>">
	<div class="wpsm_tab_<?php echo $post_id; ?>"  role="tabpanel">
		
		<!-- wpshopmart tabs pro menu -->
		<ul class="wpsm_nav wpsm_nav-tabs" role="tablist" id="myTab_<?php echo $post_id; ?>">
			<?php foreach($tabs_data as $single_data)
			{
				 $tabs_title = stripslashes(base64_decode($single_data['tabs_title']));
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
					<a href="#tabs_desc_<?php echo $post_id; ?>_<?php echo $i; ?>" aria-controls="home" role="tab" data-toggle="tab" <?php echo $var_fn_name; ?> style="<?php echo $var_ind_tabs_bg_and_title_clr; ?>">
						<?php call_fn_tabs_btn($post_id,$i,$show_tabs_title_icon,$tabs_icon_format,$show_tabs_icon_postion,$tabs_title,$tabs_title_icon,$test_pro_icon_image,$tabs_custom_image_size,$tab_img_icon_w,$tab_img_icon_h,$tab_ind_clr_enable,$indvid_tabs_title_icon_clr); ?>
					</a>
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
								//jQuery("#tabs_desc_<?php echo $post_id; ?>_<?php echo $j; ?>").mCustomScrollbar({scrollbarPosition: "outside",theme: "dark-3"});
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

<?php 
//design 3
if($tabs_button_align=="left" || $tabs_button_align=="center")
{
	$border_side="left";
	$border_opp_side="right";
}
else
{
	$border_side="right";
	$border_opp_side="left";
}
?>

<script>
	jQuery(document).ready(function(){
		var max_h=20;
		var li_height;
		<?php
			$k=1;
			 foreach($tabs_data as $single_data) 
			{
				 ?>
				 li_height=jQuery('.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li#tabs_li_<?php echo $post_id; ?>_<?php echo $k; ?>').height();
				 if(li_height>max_h)
					 {
						max_h= li_height;
					 }
				<?php
				 $k++; 
			}
		?>
		//var li_height=jQuery('.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li').height();
		li_height=max_h;
		li_height=li_height/2;
		li_margin=li_height-1;
		li_border=li_height-2;
		li_height=li_height+"px";
		li_border=li_border+"px";
		
		var my_style='.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a {'+
							'height:'+(li_margin+1)*2+'px;'+
					 '}'+
					 
					 '.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:after,'+
					 '.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:hover a:after,'+
					 '.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:after{'+
							'content:"";'+
							'border-<?php echo $border_side; ?>: '+li_height+ ' solid <?php echo $select_tabs_bg_clr; ?>;'+
							'border-top: '+li_height+ ' solid transparent;'+
							'border-bottom: '+li_height+ ' solid transparent;'+
							'position: absolute;'+
							'top: 0;'+
							'bottom:0;'+
							'<?php echo $border_opp_side; ?>: -'+li_margin+ 'px;'+
							'z-index:1;'+
					  '}'+
					  
					  '.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:after{'+
							'border-<?php echo $border_side; ?>: '+li_height+ ' solid <?php echo $tabs_btn_border_color; ?>;'+
					  '}'+
					  
					  
					  
					  '.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:before{'+
							'border-bottom: '+li_border+ ' solid rgba(0, 0, 0, 0);'+
							'border-<?php echo $border_side; ?>: '+li_border+ ' solid <?php echo $tabs_title_bg_clr; ?>;'+
							'border-top: '+li_border+ ' solid rgba(0, 0, 0, 0);'+
							'content: "";'+
							'position: absolute;'+
							'<?php echo $border_opp_side; ?>: -'+(li_margin-2)+ 'px;'+
							'top: 1px;'+
							'bottom:1px;'+
							'z-index: 2;'+
						'}'+
						
						'.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:before,'+
						'.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:hover:before{'+
							'border-<?php echo $border_side; ?>: '+li_border+ ' solid <?php echo $select_tabs_bg_clr; ?> !important;'+
						'}'+
						
						'.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:last-child.active a:after,'+
						'.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:last-child a:after{'+
							'border: none !important;'+
						'}'+
						'.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:last-child a:after,'+
						'.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:last-child a:hover:before,'+
						'.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:last-child.active a:before,'+
						'.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:last-child a:before{'+
							'border-<?php echo $border_side; ?>: none !important;'+
						'}'+
						
						'.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a:hover:before{'+
							'border-<?php echo $border_side; ?>: '+li_border+ ' solid <?php echo $select_tabs_bg_clr; ?> !important;'+
						'}';
		
		jQuery('head').append('<style>'+my_style+'</style>');
			//alert(my_style); 
	});
</script>