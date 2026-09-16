<?php
if(isset($PostId) && isset($_POST['sb_all_contents_hidden']) ) 
	{	
		$sb_TotalCount = count($_POST['sb_all_contents_title']);
		
		$sb_All_data = array();
		if($sb_TotalCount) 
			{
				for($i=0; $i < $sb_TotalCount; $i++) 
				{
					$data_option_names = array(
						 "sb_all_contents_title"=>"Sample Title",
						 "sb_all_contents_description"=>"Sample Description",
						 "sb_all_contents_btn_img_or_icon"=>"Icon",
						 "sb_all_contents_icons"=>"fa fa-mobile-phone",
						 "sb_all_contents_images"=>service_box_directory_url."assets/images/Responsive_Web.png",
						 "sb_all_contents_btn_link_yes_no"=>"No",
						 "sb_all_contents_btn_link_text"=>"Read More",
						 "sb_all_contents_btn_link_url"=>"https://www.google.co.in/",
						 "sb_all_contents_btn_link_open_in"=>"Yes",
						 
						 //color
						"sb_all_contents_icon_clr"=>"#ff6699",
						"sb_all_contents_icon_bg_clr"=>"#ff6699",
						"sb_all_contents_title_clr"=>"#ff6699",
						"sb_all_contents_des_clr"=>"#ff6699",
						"sb_all_contents_link_clr"=>"#ff6699",
						"sb_all_contents_link_bg_clr"=>"#ff6699",
						"sb_all_contents_bg_clr"=>"#ff6699",
						"sb_all_contents_brdr_clr"=>"#ff6699",
						"sb_all_contents_icon_brdr_clr"=>"#ff6699"
						
					);
					foreach($data_option_names as $data_option_name => $data_default_value) 
						{	
							if($data_option_name!='sb_all_contents_images' || $data_option_name!='sb_all_contents_btn_link_url' || $data_option_name!='sb_all_contents_btn_link_yes_no')
							${"" . $data_option_name} = stripslashes(sanitize_text_field($_POST[$data_option_name][$i]));							
						}
					$sb_all_contents_images=sanitize_text_field($_POST['sb_all_contents_images'][$i]);
					$sb_all_contents_btn_link_url=sanitize_text_field($_POST['sb_all_contents_btn_link_url'][$i]);
					$sb_all_contents_btn_link_yes_no=sanitize_option('sb_all_contents_btn_link_yes_no',$_POST['sb_all_contents_btn_link_yes_no'][$i]);
					
					$sb_All_data[] = array(
						 "sb_all_contents_title"=>$sb_all_contents_title,
						 "sb_all_contents_description"=>$sb_all_contents_description,
						 "sb_all_contents_btn_img_or_icon"=>$sb_all_contents_btn_img_or_icon,
						 "sb_all_contents_icons"=>$sb_all_contents_icons,
						 "sb_all_contents_images"=>$sb_all_contents_images,
						 "sb_all_contents_btn_link_yes_no"=>$sb_all_contents_btn_link_yes_no,
						 "sb_all_contents_btn_link_text"=>$sb_all_contents_btn_link_text,
						 "sb_all_contents_btn_link_url"=>$sb_all_contents_btn_link_url,
						 "sb_all_contents_btn_link_open_in"=>$sb_all_contents_btn_link_open_in,
						 
						 //color
						"sb_all_contents_icon_clr"=>$sb_all_contents_icon_clr,
						"sb_all_contents_icon_bg_clr"=>$sb_all_contents_icon_bg_clr,
						"sb_all_contents_title_clr"=>$sb_all_contents_title_clr,
						"sb_all_contents_des_clr"=>$sb_all_contents_des_clr,
						"sb_all_contents_link_clr"=>$sb_all_contents_link_clr,
						"sb_all_contents_link_bg_clr"=>$sb_all_contents_link_bg_clr,
						"sb_all_contents_bg_clr"=>$sb_all_contents_bg_clr,
						"sb_all_contents_brdr_clr"=>$sb_all_contents_brdr_clr,
						"sb_all_contents_icon_brdr_clr"=>$sb_all_contents_icon_brdr_clr
						);//array end
				}//for end
				update_post_meta($PostId, 'sb_pro_all_data', serialize($sb_All_data));
				update_post_meta($PostId, 'sb_pro_total_contents', $sb_TotalCount);
			} 
		else 
			{
				$sb_TotalCount = -1;
				update_post_meta($PostId, 'sb_pro_total_contents', $sb_TotalCount);
				$sb_All_data = array();
				update_post_meta($PostId, 'sb_pro_all_data', serialize($sb_All_data));
			}
	}
?>