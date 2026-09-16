<div style=" overflow: hidden;padding: 10px;">
	<style>
	.html_editor_button{
		border-radius:0px;
		background-color: #9C9C9C;
		border-color: #9C9C9C;
		margin-bottom:20px;
	}
	
	</style>
	
	<div class="wpsm_site_sidebar_widget_title">
		<h4>Add Tabs Here</h2>
	</div>
	<input type="hidden" name="tabs_pro_save_data_action" value="tabs_pro_save_data_action" />
	<ul class="clearfix wpsm-tabs-panel-group" id="tabs_pro_panel" role="tablist" aria-multiselectable="true">
	<?php
		$De_Settings = unserialize(get_option('Tabs_pro_default_Settings'));
		$PostId = $post->ID;
		$Settings = unserialize(get_post_meta( $PostId, 'Tabs_pro_Settings', true));
		$option_names = array(
		"tab_ind_clr_enable"	=> $De_Settings['tab_ind_clr_enable'],
		"show_tabs_title_icon" => $De_Settings['show_tabs_title_icon'],
		"tabs_icon_format" => $De_Settings['tabs_icon_format'],
		);
			
		foreach($option_names as $option_name => $default_value) {
			if(isset($Settings[$option_name])) 
				${"" . $option_name}  = $Settings[$option_name];
			else
				${"" . $option_name}  = $default_value;
		}

		$i=1;
		$All_data = unserialize(get_post_meta( $post->ID, 'wpsm_tabs_pro_data', true));
		$TotalCount =  get_post_meta( $post->ID, 'wpsm_tabs_pro_count', true );
		if($TotalCount) 
		{
			if($TotalCount!=-1)
			{
				foreach($All_data as $single_data)
				{
					 $tabs_title = stripslashes(base64_decode($single_data['tabs_title']));
					 $tabs_title_link = stripslashes($single_data['tabs_title_link']);
					 $tabs_title_icon = $single_data['tabs_title_icon'];
					 $tabs_desc = stripslashes(base64_decode($single_data['tabs_desc']));
					 $test_pro_icon_image = $single_data['test_pro_icon_image'];
					 $indvid_tabs_title_bg_clr = $single_data['indvid_tabs_title_bg_clr'];
					 $indvid_tabs_title_font_clr = $single_data['indvid_tabs_title_font_clr'];
					 $indvid_tabs_title_icon_clr = $single_data['indvid_tabs_title_icon_clr'];
					
					?>
					<li class="wpsm_ac-panel single_color_box panel panel-default" >
						<div class="panel-heading" role="tab" id="wpsm-heading<?php echo $i;?>">
							<h4 class="panel-title">
								<i class="fa fa-bars tabs-pro-move"></i>
								<a class="wpsm_accordion_handle wpsm-collapsed <?php if($i!=1){echo "collapsed";}?>" role="button" data-toggle="collapse" data-parent="#tabs_pro_panel" href="#wpsm-collapse<?php echo $i;?>" aria-expanded="true" aria-controls="wpsm-collapse<?php echo $i;?>"></a>
								<span class="wpsm_tabs_header" id="wpsm-tabs-header-<?php echo $i;?>"><?php echo $tabs_title; ?></span>
								<a class="remove_button" href="#delete" id="remove_bt" ><i class="fa fa-trash-o"></i></a>
							</h4>
						</div>
						<div id="wpsm-collapse<?php echo $i;?>" class="panel-collapse collapse <?php if($i==1){echo "in";}?>" role="tabpanel" aria-labelledby="wpsm-heading<?php echo $i;?>">
							<div class="panel-body">
								<span class="ac_label"><?php _e('Tab Title',wpshopmart_tabs_pro_text_domain); ?></span>
								<input type="text" id="tabs_title[]" name="tabs_title[]" value="<?php echo $tabs_title; ?>" placeholder="Enter Tab Title Here" class="wpsm_ac_label_text wpsm_tabs_pro_title">
								<span class="ac_label"><?php _e('Tab Title Link',wpshopmart_tabs_pro_text_domain); ?></span>
								<input type="text" id="tabs_title_link[]" name="tabs_title_link[]" value="<?php echo $tabs_title_link; ?>" placeholder="Enter Tab Title Link Here" class="wpsm_ac_label_text">
								<p class="linknote"><strong>Note:</strong><i> URL for the Active tab will not work and when you use URL, tab title redirect to URL and tab description will not open.</i></p>

								<span class="ac_label"><?php _e('Link to Tab',wpshopmart_tabs_pro_text_domain); ?></span>
								<p class="linknote"><strong>Note:</strong><i> First, show this tab post to any page or post then do right click on tab title at front (for which you want to create URL for redirect to user here) and click on "Copy link address". Now you can use copied URL to anywhere to redirect the user. </i></p>

								<span class="ac_label"><?php _e('Tab Description',wpshopmart_tabs_pro_text_domain); ?></span>
								<?php
								$editor_settings = array('wpautop' => false,'textarea_name'=>'tabs_desc[]');
								//$editor_settingsnew=
								$editor_id = 'tabs_desc'.$i;
								wp_editor( $tabs_desc, $editor_id,$editor_settings);
								?>
								<div class="col-md-12" style="padding-left:0;margin-top:10px;">
									<div class="col-md-6 icon_icon_cls" style="padding-left:0;<?php if($show_tabs_title_icon=="2" || $tabs_icon_format=="image"){echo "display:none;";}?>">
										<span class="ac_label"><?php _e('Tab Icon',wpshopmart_tabs_pro_text_domain); ?></span>
										<div class="form-group input-group">
											<input  class="form-control regular-text" id="tabs_title_icon[]" name="tabs_title_icon[]" value="<?php echo  $tabs_title_icon; ?>" type="text" readonly="readonly" />
											<span style="display:table-cell;width:1%;" class="input-group-addon icon-picker <?php echo  $tabs_title_icon; ?>" id="preview_icon_picker_example_icon<?php echo $i;?>" data-target="#"></span>
										</div>
									</div>
											
									<div class="form-group col-md-6 icon_img_size_settings_cls" style="padding-left:0;<?php if($show_tabs_title_icon=="2" || $tabs_icon_format=="icon"){echo "display:none;";}?>">
										<span class="ac_label"><?php _e('Tab Icon Image',wpshopmart_tabs_pro_text_domain); ?></span>
										<div class="input-group">
											<span class="input-group-addon" style="padding:4px;">
												<img class="wpsm-input-group-addon-img team-img-responsive" src="<?php echo $test_pro_icon_image;?>" />
											</span>
											<input type="button" name="" value="Upload Image" class="wpsm-input-group-addon-img-btn form-control btn btn-primary"  onclick="wpsm_media_upload(this)"/>
											<input style="display:block;width:100%" type="hidden"  name="test_pro_icon_image[]"  value="<?php echo $test_pro_icon_image;?>"  readonly="readonly" placeholder="No Media Selected" />
											<input style="display:block;width:100%" type="hidden"  name="test_pro_icon_image_id[]" class="wpsm_ac_label_text"  value=""  readonly="readonly" placeholder="No Media Selected" />
										</div>
									</div>
									
								</div>
								<div class="form-group tabs_ind_clr_option_class" <?php if($tab_ind_clr_enable=="no"){ ?>style="display:none" <?php } ?>>
									<span class="ac_label"><?php _e('Tabs Individual Color Settings',wpshopmart_tabs_pro_text_domain); ?></span>
									<div class="col-md-12 ind_clr_settings_container">
										<div class="col-md-4">
											<div class="margin-10">
												<span class="ac_label"><?php _e('Tabs Button Background Color',wpshopmart_tabs_pro_text_domain); ?></span>
												<input id="indvid_title_clr" name="indvid_tabs_title_bg_clr[]" type="text" value="<?php echo $indvid_tabs_title_bg_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
											</div>
										</div>
										<div class="col-md-4">				
											<div class="margin-10">
												<span class="ac_label"><?php _e('Tabs Title Color',wpshopmart_tabs_pro_text_domain); ?></span>
												<input id="indvid_title_bg_clr" name="indvid_tabs_title_font_clr[]" type="text" value="<?php echo $indvid_tabs_title_font_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
											</div>
										</div>
										<div class="col-md-4">
											<div class="margin-10">
												<span class="ac_label"><?php _e('Tabs Icon Color',wpshopmart_tabs_pro_text_domain); ?></span>
												<input id="tabs_title_icon_clr" name="indvid_tabs_title_icon_clr[]" type="text" value="<?php echo $indvid_tabs_title_icon_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
											</div>
										</div>
										
									</div>
								</div>
								
									
								
							</div>
						</div>
					</li>
					<?php 
					$i++;
				} // end of foreach
			}else{
			echo "<h2>No Tabs Found</h2>";
			}
		}
		else 
		{
			  for($i=1; $i<=3; $i++)
			  {
				  ?>
				 <li class="wpsm_ac-panel single_color_box panel panel-default" >
						<div class="panel-heading" role="tab" id="wpsm-heading<?php echo $i;?>">
							<h4 class="panel-title">
								<i class="fa fa-bars tabs-pro-move"></i>
								<a class="wpsm_accordion_handle wpsm-collapsed collapsed" role="button" data-toggle="collapse" data-parent="#tabs_pro_panel" href="#wpsm-collapse<?php echo $i;?>" aria-expanded="true" aria-controls="wpsm-collapse<?php echo $i;?>">
								</a><span>Sample Title</span>
								<a class="remove_button" href="#delete" id="remove_bt" ><i class="fa fa-trash-o"></i></a>
							</h4>
						</div>
						<div id="wpsm-collapse<?php echo $i;?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="wpsm-heading<?php echo $i;?>">
							<div class="panel-body">
								<span class="ac_label"><?php _e('Tab Title',wpshopmart_tabs_pro_text_domain); ?></span>
								<input type="text" id="tabs_title[]" name="tabs_title[]" value="Sample Title" placeholder="Enter Tab Title Here" class="wpsm_ac_label_text wpsm_tabs_pro_title">
								<span class="ac_label"><?php _e('Tab Title Link',wpshopmart_tabs_pro_text_domain); ?></span>
								<input type="text" id="tabs_title_link[]" name="tabs_title_link[]" value="" placeholder="Enter Tab Title Link Here" class="wpsm_ac_label_text">
								<p class="linknote"><strong>Note:</strong><i> URL for the Active tab will not work and when you use URL, tab title redirect to URL and tab description will not open.</i></p>

								<span class="ac_label"><?php _e('Link to Tab',wpshopmart_tabs_pro_text_domain); ?></span>
								<p class="linknote"><strong>Note:</strong><i> First, show this tab post to any page or post then do right click on tab title at front (for which you want to create URL for redirect to user here) and click on "Copy link address". Now you can use copied URL to anywhere to redirect the user. </i></p>

								<span class="ac_label"><?php _e('Tab Description',wpshopmart_tabs_pro_text_domain); ?></span>
								<?php
								$editor_settings = array('wpautop' => false,'textarea_name'=>'tabs_desc[]');
								$editor_id = 'tabs_desc'.$i;
								wp_editor( 'Sample Description', $editor_id,$editor_settings);
								?>
								
								<div class="col-md-12" style="padding-left:0;margin-top:10px;">
									<div class="col-md-6 icon_icon_cls" style="padding-left:0;<?php if($show_tabs_title_icon=="2" || $tabs_icon_format=="image"){echo "display:none;";}?>">
										<span class="ac_label"><?php _e('Tab Icon',wpshopmart_tabs_pro_text_domain); ?></span>				
										<div class="form-group input-group" style="">
											<input style="width:100%;" class="form-control regular-text" id="tabs_title_icon[]" name="tabs_title_icon[]" value="fa fa-laptop" type="text" readonly="readonly" />
											<span style="display:table-cell;width:1%;" class="wpsm_tabs_icon_picker input-group-addon icon-picker fa fa-laptop" id="" data-target="#"></span>
										</div>
									</div>
								
									<div class="form-group col-md-6 icon_img_size_settings_cls" style="padding-left:0;<?php if($show_tabs_title_icon=="2" || $tabs_icon_format=="icon"){echo "display:none;";}?>">
										<span class="ac_label"><?php _e('Tab Icon Image',wpshopmart_tabs_pro_text_domain); ?></span>
										<div class="input-group">
											<span class="input-group-addon" style="padding:4px;">
												<img class="wpsm-input-group-addon-img team-img-responsive" src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/images/default-image.png' ?>" />
											</span>
										<input type="button" name="" value="Upload Image" class="wpsm-input-group-addon-img-btn form-control btn btn-primary"  onclick="wpsm_media_upload(this)"/>
										<input style="display:block;width:100%" type="hidden"  name="test_pro_icon_image[]"  value="<?php echo wpshopmart_tabs_pro_directory_url.'assets/images/default-image.png' ?>"  readonly="readonly" placeholder="No Media Selected" />
										<input style="display:block;width:100%" type="hidden"  name="test_pro_icon_image_id[]" class="wpsm_ac_label_text"  value=""  readonly="readonly" placeholder="No Media Selected" />
										</div>
									</div>

								</div>
								
								
								<div class="margin-10 tabs_ind_clr_option_class" <?php if($tab_ind_clr_enable=="no"){ ?>style="display:none" <?php } ?>>
									<span class="ac_label"><?php _e('Tabs Individual Color Settings',wpshopmart_tabs_pro_text_domain); ?></span>
									<div class="col-md-12 ind_clr_settings_container" >
										<div class="col-md-4">
											<div class="margin-10">
												<span class="ac_label"><?php _e('Tabs Button Background Color',wpshopmart_tabs_pro_text_domain); ?></span>
												<input id="indvid_tabs_title_bg_clr" name="indvid_tabs_title_bg_clr[]" type="text" value="#ff6699" class="my-color-field" data-default-color="#ffffff" />
											</div>
										</div>
										<div class="col-md-4">				
											<div class="margin-10">
												<span class="ac_label"><?php _e('Tabs Title Color',wpshopmart_tabs_pro_text_domain); ?></span>
												<input id="indvid_tabs_title_font_clr" name="indvid_tabs_title_font_clr[]" type="text" value="#ff9966" class="my-color-field" data-default-color="#ffffff" />
											</div>
										</div>
										<div class="col-md-4">
											<div class="margin-10">
												<span class="ac_label"><?php _e('Tabs Icon Color',wpshopmart_tabs_pro_text_domain); ?></span>
												<input id="tabs_title_icon_clr" name="indvid_tabs_title_icon_clr[]" type="text" value="#99ff66" class="my-color-field" data-default-color="#ffffff" />
											</div>
										</div>
									</div>	
								</div>
								
							</div>	
						</div>
					</li>
				 <?php
			}
		}
	?>
	</ul>
	

	<div style="display:block;margin-top:20px;overflow:hidden;width: 100%;float:left;">
		<a class="wpsm_ac-panel add_wpsm_ac_new" id="add_new_ac" onclick="add_new_content()"   >
			<?php _e('Add New Tabs', wpshopmart_tabs_pro_text_domain); ?>
		</a>
		<a  style="float: left;padding:10px !important;background:#31a3dd;" class="add_wpsm_ac_new delete_all_acc" id="delete_all_colorbox"    >
			<i style="font-size:57px;"class="fa fa-trash-o"></i>
			<span style="display:block"><?php _e('Delete All',wpshopmart_tabs_pro_text_domain); ?></span>
		</a>
	</div>
	
</div>

<?php require('add-tabs-js-footer.php'); ?>