<?php
if(isset($PostID) && isset($_POST['tabs_pro_save_data_action']) ) {
			$TotalCount = count($_POST['tabs_title']);
			$All_data = array();
			if($TotalCount) {
				for($i=0; $i < $TotalCount; $i++) {
					$tabs_title = base64_encode(sanitize_text_field($_POST['tabs_title'][$i]));
					$tabs_title_link = sanitize_text_field($_POST['tabs_title_link'][$i]);
					$tabs_title_icon = sanitize_text_field($_POST['tabs_title_icon'][$i]);
					$tabs_desc = base64_encode($_POST['tabs_desc'][$i]);
					$test_pro_icon_image = stripslashes($_POST['test_pro_icon_image'][$i]);
					$indvid_tabs_title_bg_clr = sanitize_text_field($_POST['indvid_tabs_title_bg_clr'][$i]);
					$indvid_tabs_title_font_clr = sanitize_text_field($_POST['indvid_tabs_title_font_clr'][$i]);
					$indvid_tabs_title_icon_clr = sanitize_text_field($_POST['indvid_tabs_title_icon_clr'][$i]);

					$All_data[] = array(
						'tabs_title' => $tabs_title,
						'tabs_title_link' => $tabs_title_link,
						'tabs_title_icon' => $tabs_title_icon,
						'tabs_desc' => $tabs_desc,
						'test_pro_icon_image' => $test_pro_icon_image,
						'indvid_tabs_title_bg_clr' => $indvid_tabs_title_bg_clr,
						'indvid_tabs_title_font_clr' => $indvid_tabs_title_font_clr,
						'indvid_tabs_title_icon_clr' => $indvid_tabs_title_icon_clr,
					);
				}
				update_post_meta($PostID, 'wpsm_tabs_pro_data', serialize($All_data));
				update_post_meta($PostID, 'wpsm_tabs_pro_count', $TotalCount);
			} else {
				$TotalCount = -1;
				update_post_meta($PostID, 'wpsm_tabs_pro_count', $TotalCount);
				$All_data = array();
				update_post_meta($PostID, 'wpsm_tabs_pro_data', serialize($All_data));
			}
		}
 ?>