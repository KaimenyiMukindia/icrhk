<script>


jQuery(function(jQuery)
{
		/* **************highlight &  select view********************************************************* */
		jQuery("#box1" ).click(function(e)
		{	
			var view_radio_id="#service_box_radio1";
			jQuery('.carousel_view_settings_cls').show();		//showing Carousel Settings
			if(jQuery(view_radio_id).prop('checked')==false)
			{
				jQuery(view_radio_id).prop('checked',true);				//Radio Btn checked
				jQuery("#box2" ).css("background-color","");			//Disable Grid
				jQuery("#check_icon2").hide();							//Grid Icon Hide
				jQuery("#check_icon1").show();							//Carousel ICon Show
				jQuery(this).css("background-color","rgb(76,175,180)"); //Carousel Enable Color
			}
				
		});
		
		jQuery("#box2" ).click(function(e)  	
		{	var view_radio_id="#service_box_radio2";
			jQuery('.carousel_view_settings_cls').hide();		//Hideing Carousel Settings
			if(jQuery(view_radio_id).prop('checked')==false)
			{
				jQuery(view_radio_id).prop('checked',true);				//Radio Btn checked
				jQuery("#box1" ).css("background-color","");			//Disable Carousel
				jQuery("#check_icon1").hide();							//Carousel Icon Hide
				jQuery("#check_icon2").show();							//Grid ICon Show
				jQuery(this).css("background-color","rgb(76,175,180)"); //Grid Enable Color
			}
				
		});

});	

//sorting
jQuery(document).ready(function(){
  jQuery('#content_holder').sortable({
  revert: true,
  });
});
</script>

<script>

jQuery(document).ready(function($){
	//open the lateral panel
	$('#service_box_btn_modal_grid').on('click', function(event){
		event.preventDefault();
		$('#sb-cd-panel').addClass('is-visible');
	});
	//clode the lateral panel
	$('#sb-cd-panel').on('click', function(event){
		if( $(event.target).is('#sb-cd-panel') || $(event.target).is('#sb-cd-panel-close') ) { 
			$('#sb-cd-panel').removeClass('is-visible');
			event.preventDefault();
		}
	});
	
	
});


function select_template_design(id)
{
	
	jQuery(".design_select_btn").attr('style','');
	jQuery(".design_select_btn").prop("disabled", false);
	jQuery(".design_select_btn").text("Select");
	
	jQuery(".design_checked_icon").hide();
	jQuery("#design_checked_icon_"+id).show();
	
	
	jQuery("#templates_btn_select_"+id).attr('disabled','disabled');
	jQuery("#templates_btn_select_"+id).attr('style','background:#F50000;border-color:#F50000;');
	jQuery("#templates_btn_select_"+id).text("Selected");
	jQuery("#sb_all_contents_design_no_"+id).prop( "checked", true );
	
}
</script>
<script>
// *********************** Add New Contents *****************************************

var tot_count = <?php if($sb_TotalCount){ if($sb_TotalCount!=-1){echo $sb_TotalCount;}else{ echo "0";}}else{echo "2";} ?>;

function add_new_sb_content(){
	tot_count=tot_count+1;
	var value = jQuery("input[name=sb_ind_clr_enable]:checked").val();
	<?php
	  //getting only variable names
	foreach($data_option_names as $data_option_name => $data_default_value) 
		{	
			${"" . $data_option_name}=$data_default_value;
			?>
			var <?php echo $data_option_name."=\"".${"" . $data_option_name}."\"";?>;
			<?php
		}
	?>		
	var dynamic_data_all=''+
	"<li class='single_service_box_li'><div id='delete_contents"+tot_count+"' class='row mycpt_col content_box'>"+
				"<input type='hidden' name='sb_all_contents_ids[]' id='sb_all_contents_ids"+tot_count+"' value='"+tot_count+"'/>"+
				
					"<div class='col-md-4'>"+
							"<div>"+
							"<h4> Title</h4>"+
							"<input type='text' name='sb_all_contents_title[]' id='sb_all_contents_title"+tot_count+"' value='"+sb_all_contents_title+"' placeholder='Sample title' style='width:100%;' class='form-control'/>"+
							"</div>"+

							"<div>"+
							"<h4> Description</h4>"+
							"<textarea  name='sb_all_contents_description[]' id='sb_all_contents_description"+tot_count+"' placeholder='Sample description' style='width:100%;height:100px;' class='form-control'>"+sb_all_contents_description+"</textarea>"+
							"</div>"+
							
							"<div class='sb_ind_clr_option_class' style='display:none;'>"+
										"<h4> Individual Color Settings</h4>"+
										
										"<a type='button' class='btn btn-primary btn-block ' data-target='#color_modal_"+tot_count+"' href='#' data-toggle='modal'  >Individual Color Settings</a>"+
										
							"</div>"+

					"</div>"+
					
					"<div class='col-md-4' >"+
						"<div>"+
									"<h4> Display Icon / Image</h4>"+
									"<select class='form-control' name='sb_all_contents_btn_img_or_icon[]' id='sb_all_contents_btn_img_or_icon"+tot_count+"'>"+
										"<option id='content_btn_link_open' value='Icon' selected>Icon</option>"+
										"<option id='content_btn_link_open' value='Image'>Image</option>"+
									"</select>"+
						"</div>"+
						
						"<h4> Service Box Icon</h4>"+
						"<div class='form-group input-group wpsm_input_group'>"+
						"<input  class='form-control regular-text' id='sb_all_contents_icons"+tot_count+"' name='sb_all_contents_icons[]' value='"+sb_all_contents_icons+"' type='text' readonly='readonly' />"+
						"<span style='' class='wpsm_input_icon_group_addon input-group-addon icon-picker "+sb_all_contents_icons+"' id='preview_icon_picker_example_icon"+tot_count+"' data-target='#sb_all_contents_icons"+tot_count+"'></span>"+
						"</div>"+
						
						
						"<div>"+
						"<h4> Service Box Image</h4>"+
							"<div class='input-group'>"+
							  "<span class='input-group-addon' style='padding:1px;'>"+
									"<img style='width:40px;height:30px;' class='team-img-responsive' src='"+sb_all_contents_images+"' />"+
							  "</span>"+
							 "<input style='width:100%;' type='button' value='Upload Image' class='form-control btn btn-primary'  onclick='service_box_media_upload(this)'/>"+
							"<input style='display:block;width:100%' type='hidden'  name='sb_all_contents_images[]' id='sb_all_contents_images"+tot_count+"'   value='"+sb_all_contents_images+"'  readonly='readonly' placeholder='No Media Selected' />"+
							"</div>"+
						"</div>"+
						
					"</div>"+
					
					
					
					"<div class='col-md-4'>"+
							"<div>"+
							"<h4> Do you want to display link ?</h4>"+
							"<select class='form-control' name='sb_all_contents_btn_link_yes_no[]' id='sb_all_contents_btn_link_yes_no"+tot_count+"'>"+
								"<option id='content_btn_link' value='Yes'>Yes</option>"+
								"<option id='content_btn_link' value='No' selected>No</option>"+
							"</select>"+
							"</div>"+

							"<div>"+
							"<h4> Link Text</h4>"+
							"<input type='text' name='sb_all_contents_btn_link_text[]' id='sb_all_contents_btn_link_text"+tot_count+"' value='"+sb_all_contents_btn_link_text+"' placeholder='Read More' style='width:100%;' class='form-control'/>"+
							"</div>"+

							"<div>"+
							"<h4> Link URL / Address</h4>"+
							"<input type='text' name='sb_all_contents_btn_link_url[]' id='sb_all_contents_btn_link_url"+tot_count+"' value='"+sb_all_contents_btn_link_url+"' placeholder='Sample URL' style='width:100%;' class='form-control'/>"+
							"</div>"+
							
							"<div>"+
							"<h4> Open Link In New Tab</h4>"+
							"<select class='form-control' name='sb_all_contents_btn_link_open_in[]' id='sb_all_contents_btn_link_open_in"+tot_count+"'>"+
								"<option id='content_btn_link_open' value='Yes' selected>Yes</option>"+
								"<option id='content_btn_link_open' value='No' >No</option>"+
							"</select>"+
							"</div>"+
					"</div>"+
			
			
			"<div style='position: absolute; right: 13px; top: 10px;opacity:1;' id='delete_icon"+tot_count+"' class='delete_icon' onclick='delete_content("+tot_count+")'>"+
					"<i class='fa fa-trash-o fa-2x' ></i>"+
			"</div>"+
		"</div>"+
		<!----------------Color Code Modal--------------->
		"<div class='modal fade'  id='color_modal_"+tot_count+"'  role='dialog' >"+
			"<div class='modal-dialog modal-lg' role='document'>"+
				"<div class='modal-content'>"+
					"<div class='modal-header'>"+
						"<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>"+
						"<h4 class='modal-title' id='myModalLabel'>Individual Service Box Color Settings</h4>"+
					"</div>"+
					"<div class='modal-body' style='display:inline-block'>"+
							
						
						"<div class='col-md-12' style='text-align:left'>"+
							"<div class='col-md-6'>"+
								"<div class='margin-10'>"+
									"<span class='ac_label'>Icon Color</span>"+
									"<input id='sb_all_contents_icon_clr' name='sb_all_contents_icon_clr[]' type='text' value='"+sb_all_contents_icon_clr+"' class='my-color-field' data-default-color='#ffffff' />"+
								"</div>"+
							"</div>"+
							"<div class='col-md-6'>"+
								"<div class='margin-10'>"+
									"<span class='ac_label'>Icon Background Color</span>"+
									"<input id='sb_all_contents_icon_bg_clr' name='sb_all_contents_icon_bg_clr[]' type='text' value='"+sb_all_contents_icon_bg_clr+"' class='my-color-field' data-default-color='#ffffff' />"+
								"</div>"+
							"</div>"+
							
							
						"</div>	"+
						
						
						
						"<div class='col-md-12' style='text-align:left'>"+
							"<div class='col-md-6'>"+
								"<div class='margin-10'>"+
									"<span class='ac_label'>Title Color</span>"+
									"<input id='sb_all_contents_title_clr' name='sb_all_contents_title_clr[]' type='text' value='"+sb_all_contents_title_clr+"' class='my-color-field' data-default-color='#ffffff' />"+
								"</div>"+
							"</div>"+
							"<div class='col-md-6'>"+
								"<div class='margin-10'>"+
									"<span class='ac_label'>Description Color</span>"+
									"<input id='sb_all_contents_des_clr' name='sb_all_contents_des_clr[]' type='text' value='"+sb_all_contents_des_clr+"' class='my-color-field' data-default-color='#ffffff' />"+
								"</div>"+
							"</div>"+
							
						"</div>	"+
							
						"<div class='col-md-12' style='text-align:left'>"+
							"<div class='col-md-6'>"+
								"<div class='margin-10'>"+
									"<span class='ac_label'>Link Font Color</span>"+
									"<input id='sb_all_contents_link_clr' name='sb_all_contents_link_clr[]' type='text' value='"+sb_all_contents_link_clr+"' class='my-color-field' data-default-color='#ffffff' />"+
								"</div>"+
							"</div>"+
							
							"<div class='col-md-6'>"+
								"<div class='margin-10'>"+
									"<span class='ac_label'>Link Background Color</span>"+
									"<input id='sb_all_contents_link_bg_clr' name='sb_all_contents_link_bg_clr[]' type='text' value='"+sb_all_contents_link_bg_clr+"' class='my-color-field' data-default-color='#ffffff' />"+
								"</div>"+
							"</div>"+
						"</div>"+
						
						
						"<div class='col-md-12' style='text-align:left'>"+		
							"<div class='col-md-6'>"+
								"<div class='margin-10'>"+
									"<span class='ac_label'>Service Box Background Color</span>"+
									"<input id='sb_all_contents_bg_clr' name='sb_all_contents_bg_clr[]' type='text' value='"+sb_all_contents_bg_clr+"' class='my-color-field' data-default-color='#ffffff' />"+
								"</div>"+
							"</div>"+
							"<div class='col-md-6'>"+
								"<div class='margin-10'>"+
									"<span class='ac_label'>Service Box Border Color</span>"+
									"<input id='sb_all_contents_brdr_clr' name='sb_all_contents_brdr_clr[]' type='text' value='"+sb_all_contents_brdr_clr+"' class='my-color-field' data-default-color='#ffffff' />"+
								"</div>"+
							"</div>"+
							
							
						"</div>"+
						
						"<div class='col-md-12' style='text-align:left'>		"+
							"<div class='col-md-6'>"+
								"<div class='margin-10'>"+
									"<span class='ac_label'>Icon Border Color</span>"+
									"<input id='sb_all_contents_icon_brdr_clr' name='sb_all_contents_icon_brdr_clr[]' type='text' value='"+sb_all_contents_icon_brdr_clr+"' class='my-color-field' data-default-color='#ffffff' />"+
								"</div>"+
							"</div>"+
							
							
						"</div>"+
					"</div>"+
					"<div class='modal-footer'>"+
						"<button type='button' class='remodal-cancel btn btn-primary' data-dismiss='modal'>Cancel</button>"+
						"<button type='button' class='remodal-confirm btn btn-primary' data-dismiss='modal'>Ok</button>"+
					"</div>"+
				"</div>"+
			"</div>"+
		"</div>"+ 
	 "</li>"; 
	  jQuery(dynamic_data_all).hide().appendTo("#content_holder").slideDown("slow");
	  jQuery('.icon-picker').iconPicker();
	  jQuery('.my-color-field').wpColorPicker();
	  jQuery('#wpsm_no_sb_found_id').hide(500);
	  jQuery('#btn_delete_all_con').show(500);
	   if(value=='yes'){
		  jQuery('.sb_ind_clr_option_class').show(); 
		}
	
}
							 
//delete content
function delete_content(id){
	var delete_content_id="#delete_contents"+id;
	if(confirm("Do you want to delete this service box ?"))
	{
		jQuery(delete_content_id).slideUp('slow', function(){ jQuery(delete_content_id).remove(); });
		tot_count=tot_count-1;
		if(tot_count==0){
			jQuery('#wpsm_no_sb_found_id').show(500);
			jQuery('#btn_delete_all_con').hide(500);
		}
	}
	
	
}	

function delete_all_content(){
	//var delete_content_id="#delete_contents"+id;	
	if(confirm("Do you want to delete all service box ?"))
	{
		jQuery(".single_service_box_li").slideUp('slow', function(){ jQuery(".single_service_box_li").remove(); });
		jQuery('#wpsm_no_sb_found_id').show(500);
		jQuery('#btn_delete_all_con').hide(500);
		tot_count=0;
	}
	
}	
</script>