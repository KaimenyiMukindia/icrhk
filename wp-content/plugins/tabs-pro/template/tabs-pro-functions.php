<?php
/* -------------------------- Tabs Btn Fn-------------------------------- */
if ( ! function_exists( 'call_fn_tabs_btn' ) ) 
{
	function call_fn_tabs_btn($post_id,
								$i,
								$show_tabs_title_icon,
								$tabs_icon_format,
								$show_tabs_icon_postion,
								$tabs_title,$tabs_title_icon,
								$test_pro_icon_image,
								$tabs_custom_image_size,
								$tab_img_icon_w,
								$tab_img_icon_h,
								$tab_ind_clr_enable,
								$indvid_tabs_title_icon_clr)
	{	
		//Individual Colors
		if($tab_ind_clr_enable=='yes')
		$var_ind_title_icon_clr=' style="color:'.$indvid_tabs_title_icon_clr.'"';
		else
		$var_ind_title_icon_clr="";
		
		
		switch($show_tabs_title_icon)
		{
			case 1:											//Title+Icon
				if($tabs_icon_format=="icon")  				//Tabs Icon 
				{	
					if($show_tabs_icon_postion=="left")  //Before Tab Title 
					{
						echo '<i class="'.$tabs_title_icon.' wpsm-icons"  '.$var_ind_title_icon_clr.' ></i>';
						echo $tabs_title; 
					}
					else 								 //After Tab Title
					{
						echo $tabs_title; 
						echo '<i class="'.$tabs_title_icon.' wpsm-icons"  '.$var_ind_title_icon_clr.' ></i>';
					}
				}
				else //Tabs Image
				{
					if($show_tabs_icon_postion=="left")  //Before Tab Title 
					{
						echo '<img src="'.$test_pro_icon_image.'" class="wpsm_tabs_img_icon" />';
						echo "  ".$tabs_title; 
					}
					else 								//After Tab Title
					{
						echo $tabs_title."  "; 
						echo '<img src="'.$test_pro_icon_image.'" class="wpsm_tabs_img_icon"/>';
					}
					
				}
				break;
			
			case 2:	//Only Title
				echo $tabs_title; 
				break;
			
			case 3: //Only Icon
				if($tabs_icon_format=="icon")  //Tabs Icon 
				{	
					echo '<i class="'.$tabs_title_icon.' wpsm-icons"></i>';
				}
				else //Tabs Image
				{
					echo '<img src="'.$test_pro_icon_image.'" class="wpsm_tabs_img_icon" />';
				}
				break;
		}//switch End
	}//Tabs btn Fn End

	/* ----------------------- Fn for Open Tabs On Hover-------------------------------- */
	function call_fn_open_tab_on_hover($post_id,$tabs_on_hover)
	{
		if($tabs_on_hover=='yes')
		{?>
			<script>
			function fn_open_tab_on_hover(cur_ele,id,post_id)
			{
				jQuery('.wpsm_tab_'+post_id).children('ul').children('li').removeClass("active");
				jQuery('.wpsm_tab_'+post_id+' .wpsm_tab_content:first').children('div').removeClass("in active");
				jQuery(cur_ele).parent('li').addClass("active");
				jQuery(id).addClass("in active");
			}
			</script>
		 <?php
		}
		
		
	}//fn end
}//if end
?>
