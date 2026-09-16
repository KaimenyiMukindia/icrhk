
<li class="single_service_box_li">
	<div id="delete_contents<?php echo $i;?>" class='row mycpt_col content_box'>
		<input type='hidden' name='sb_all_contents_ids[]' id='sb_all_contents_ids<?php echo $i;?>' value='<?php if($sb_all_contents_ids){echo $sb_all_contents_ids;} else {echo $i;}?>' />
		<div class="col-md-4">
			<div>
			<h4> Title</h4>
			<input type='text' name='sb_all_contents_title[]' id='sb_all_contents_title<?php echo $i;?>' value='<?php if($sb_all_contents_title){echo $sb_all_contents_title;}?>' placeholder='Sample title' style='width:100%;' class='form-control'/>
			</div>

			<div>
			<h4> Description</h4>
			<textarea  name='sb_all_contents_description[]' id='sb_all_contents_description<?php echo $i;?>' placeholder='Sample description' style='width:100%;height:100px;' class='form-control'><?php if($sb_all_contents_description){echo $sb_all_contents_description;}?></textarea>
			</div>
			
			<div class="sb_ind_clr_option_class" <?php if($sb_ind_clr_enable=="no"){ ?>style="display:none;" <?php } ?>>
				<h4> Individual Color Settings</h4>
				<a type="button" class="btn btn-primary btn-block " data-target="#color_modal_<?php echo $i; ?>" href="#" data-toggle="modal"  >Individual Color Settings</a>
			</div>
		</div>
					
		<div class="col-md-4" >
			<div>
				<h4> Display Icon / Image</h4>
				<select class="form-control" name='sb_all_contents_btn_img_or_icon[]' id='sb_all_contents_btn_img_or_icon<?php echo $i;?>'>
					<option id="content_btn_link_open" value="Icon" <?php if($sb_all_contents_btn_img_or_icon=="Icon"){echo "selected";}?>>Icon</option>
					<option id="content_btn_link_open" value="Image" <?php if($sb_all_contents_btn_img_or_icon=="Image"){echo "selected";}?>>Image</option>
				</select>
			</div>
						
			<h4> Service Box Icon</h4>
			<div class="form-group input-group wpsm_input_group" style="">
			<input  class="form-control regular-text" id="sb_all_contents_icons<?php echo $i;?>" name="sb_all_contents_icons[]" value="<?php if($sb_all_contents_icons){echo $sb_all_contents_icons;} ?>" type="text" readonly="readonly" />
			<span style="" class="wpsm_input_icon_group_addon input-group-addon icon-picker <?php if($sb_all_contents_icons){ echo $sb_all_contents_icons;} ?>" id="preview_icon_picker_example_icon<?php echo $i;?>" data-target="#sb_all_contents_icons<?php echo $i;?>"></span>
			</div>
			
			<div>
			<h4> Service Box Image</h4>
				<div class="input-group">
					<span class="input-group-addon" style="padding:1px;">
						<img style="width:40px;height:30px;" class="team-img-responsive" src="<?php if($sb_all_contents_images){ echo $sb_all_contents_images;}// else {echo service_box_directory_url."assets/images/Responsive_Web.png";}?>" />
					</span>
				<input style="width:100%;" type="button" id="" name="" value="Upload Image" class="form-control btn btn-primary"  onclick="service_box_media_upload(this)"/>
				<input style="display:block;width:100%" type="hidden"  name="sb_all_contents_images[]" id="sb_all_contents_images<?php echo $i;?>" class=""  value="<?php if($sb_all_contents_images){echo $sb_all_contents_images;} else {echo service_box_directory_url."assets/images/Responsive_Web.png";}?>"  readonly="readonly" placeholder="No Media Selected" />
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div>
			<h4> Do you want to display link ?</h4>
			<select class="form-control" name='sb_all_contents_btn_link_yes_no[]' id='sb_all_contents_btn_link_yes_no<?php echo $i;?>'>
				<option id="content_btn_link" value="Yes" <?php if($sb_all_contents_btn_link_yes_no=="Yes"){echo "selected";}?>>Yes</option>
				<option id="content_btn_link" value="No" <?php if($sb_all_contents_btn_link_yes_no=="No"){echo "selected";}?>>No</option>
			</select>
			</div>

			<div>
			<h4> Link Text</h4>
			<input type='text' name='sb_all_contents_btn_link_text[]' id='sb_all_contents_btn_link_text<?php echo $i;?>' value='<?php if($sb_all_contents_btn_link_text){echo $sb_all_contents_btn_link_text;}?>' placeholder='Read More' style='width:100%;' class='form-control'/>
			</div>

			<div>
			<h4> Link URL / Address</h4>
			<input type='text' name='sb_all_contents_btn_link_url[]' id='sb_all_contents_btn_link_url<?php echo $i;?>' value='<?php if($sb_all_contents_btn_link_url){echo $sb_all_contents_btn_link_url;}?>' placeholder='Sample URL' style='width:100%;' class='form-control'/>
			</div>
			
			<div>
			<h4> Open Link In New Tab</h4>
			<select class="form-control" name='sb_all_contents_btn_link_open_in[]' id='sb_all_contents_btn_link_open_in<?php echo $i;?>'>
				<option id="content_btn_link_open" value="Yes" <?php if($sb_all_contents_btn_link_open_in=="Yes"){echo "selected";}?>>Yes</option>
				<option id="content_btn_link_open" value="No" <?php if($sb_all_contents_btn_link_open_in=="No"){echo "selected";}?>>No</option>
			</select>
			</div>
		</div>
		
		<div style="position: absolute; right: 13px; top: 10px;opacity:1;" id="delete_icon<?php echo $i;?>" class="delete_icon delete_content_li" onclick='delete_content(<?php echo $i;?>)'>
											<i class="fa fa-trash-o fa-2x" ></i>
		</div>

	</div>


	<!----------------Color Code Modal--------------->
									
	<div class="modal fade"  id="color_modal_<?php echo $i; ?>"  role="dialog" >
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Individual Service Box Color Settings</h4>
				</div>
				<div class="modal-body" style="display:inline-block">
					<!-- Icon color-->		
					<div class="col-md-12" style="text-align:left">
						<div class="col-md-6">
							<div class="margin-10">
								<span class="ac_label"><?php echo 'Icon Color'; ?></span>
								<input id="sb_all_contents_icon_clr" name="sb_all_contents_icon_clr[]" type="text" value="<?php echo $sb_all_contents_icon_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
							</div>
						</div>
						<div class="col-md-6">				
							<div class="margin-10">
								<span class="ac_label"><?php echo 'Icon Background Color'; ?></span>
								<input id="sb_all_contents_icon_bg_clr" name="sb_all_contents_icon_bg_clr[]" type="text" value="<?php echo $sb_all_contents_icon_bg_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
							</div>
						</div>
					</div>	
					<!-- Title color-->		
					<div class="col-md-12" style="text-align:left">
						<div class="col-md-6">
							<div class="margin-10">
								<span class="ac_label"><?php echo 'Title Color'; ?></span>
								<input id="sb_all_contents_title_clr" name="sb_all_contents_title_clr[]" type="text" value="<?php echo $sb_all_contents_title_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="margin-10">
								<span class="ac_label"><?php echo 'Description Color'; ?></span>
								<input id="sb_all_contents_des_clr" name="sb_all_contents_des_clr[]" type="text" value="<?php echo $sb_all_contents_des_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
							</div>
						</div>
					</div>	
					<div class="col-md-12" style="text-align:left">		
						<div class="col-md-6">
							<div class="margin-10">
								<span class="ac_label"><?php echo 'Link Font Color'; ?></span>
								<input id="sb_all_contents_link_clr" name="sb_all_contents_link_clr[]" type="text" value="<?php echo $sb_all_contents_link_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="margin-10">
								<span class="ac_label"><?php echo 'Link Background Color'; ?></span>
								<input id="sb_all_contents_link_bg_clr" name="sb_all_contents_link_bg_clr[]" type="text" value="<?php echo $sb_all_contents_link_bg_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
							</div>
						</div>
					</div>
					
					<!-- bg color-->
					<div class="col-md-12" style="text-align:left">	
						<div class="col-md-6">
							<div class="margin-10">
								<span class="ac_label"><?php echo 'Icon Border Color'; ?></span>
								<input id="sb_all_contents_icon_brdr_clr" name="sb_all_contents_icon_brdr_clr[]" type="text" value="<?php echo $sb_all_contents_icon_brdr_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="margin-10">
								<span class="ac_label"><?php echo 'Service Box Border Color'; ?></span>
								<input id="sb_all_contents_brdr_clr" name="sb_all_contents_brdr_clr[]" type="text" value="<?php echo $sb_all_contents_brdr_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
							</div>
						</div>
						
						
					</div>
					
					<!-- Icon brdr color-->
					<div class="col-md-12" style="text-align:left">		
						<div class="col-md-6">
							<div class="margin-10">
								<span class="ac_label"><?php echo 'Service Box Background Color'; ?></span>
								<input id="sb_all_contents_bg_clr" name="sb_all_contents_bg_clr[]" type="text" value="<?php echo $sb_all_contents_bg_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="remodal-cancel btn btn-primary" data-dismiss="modal">Cancel</button>
					<button type="button" class="remodal-confirm btn btn-primary" data-dismiss="modal">Ok</button>
				</div>
			</div>
		</div>
	</div>
</li>