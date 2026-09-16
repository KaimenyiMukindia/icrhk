<script>

jQuery(document).ready(function(){
	//Change Accordion Header Dynamiclly
	dynamic_change_header();
	
	//sorttable
	jQuery('#tabs_pro_panel').sortable({
		revert: true,
		handle: '.tabs-pro-move' 
	});
	
	//accordion
	jQuery(document).on('click', '.panel-title .wpsm_accordion_handle', function()
	{	
			if(jQuery(this).hasClass('wpsm-collapsed'))
			{	
				jQuery(".wpsm_accordion_handle").addClass('collapsed wpsm-collapsed');
				jQuery(this).removeClass('collapsed wpsm-collapsed');
				
				jQuery('.panel-collapse').removeClass('in');
				jQuery(this).parents('.panel-heading').siblings('.panel-collapse').addClass('in');
			}
			else
			{
				jQuery(".wpsm_accordion_handle").addClass('collapsed wpsm-collapsed');
				jQuery('.panel-collapse').removeClass('in');
			}
		
		
	})	
			
	// to add editor on textarea
		tinyMCE.init({
			toolbar: [
        "formatselect,bold,italic,blockquote,bullist,numlist,alignleft,aligncenter,alignright,link,unlink,fullscreen,wp_adv,undo,redo"
        
			],
			mode : "none",
			statusbar: false,
			menubar: false,
			statusbar: true,
			setup: function (editor) {
				editor.on('change', function () {
					editor.save();
				});
				
			},
		});
		
	

});
	var j = 1000;
	function add_new_content(){
	//var unique_key = jQuery.now();
	var output = 	'<li class="wpsm_ac-panel single_color_box panel-default">'+
			'<div class="panel-heading" role="tab" id="wpsm-heading'+j+'">'+
				'<h4 class="panel-title">'+
					'<i class="fa fa-bars tabs-pro-move"></i>'+
					'<a class="wpsm_accordion_handle wpsm-collapsed collapsed" role="button" data-toggle="collapse" data-parent="#tabs_pro_panel" href="#wpsm-collapse'+j+'" aria-expanded="true" aria-controls="wpsm-collapse'+j+'"></a>'+
					'<span class="wpsm_tabs_header" id="wpsm-tabs-header-'+j+'">Sample Title</span>'+
					'<a class="remove_button" href="#delete" id="remove_bt" ><i class="fa fa-trash-o"></i></a>'+
				'</h4>'+
			'</div>'+
			'<div id="wpsm-collapse'+j+'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="wpsm-heading'+j+'">'+
				'<div class="panel-body">'+
							
					'<span class="ac_label"><?php _e("Tab Title",wpshopmart_tabs_pro_text_domain); ?></span>'+
					'<input type="text" id="tabs_title[]" name="tabs_title[]" value="Sample Title" placeholder="Enter Tab Title Here" class="wpsm_ac_label_text wpsm_tabs_pro_title">'+
					'<span class="ac_label"><?php _e("Tab Title Link",wpshopmart_tabs_pro_text_domain); ?></span>'+
					'<input type="text" id="tabs_title_link[]" name="tabs_title_link[]" value="" placeholder="Enter Tab Title Link Here" class="wpsm_ac_label_text">'+

					'<p class="linknote"><strong>Note:</strong><i> URL for the Active tab will not work and when you use URL, tab title redirect to URL and tab description will not open.</i></p>'+

					'<span class="ac_label"><?php _e("Link to Tab",wpshopmart_tabs_pro_text_domain); ?></span>'+
					'<p class="linknote"><strong>Note:</strong><i> First, show this tab post to any page or post then do right click on tab title at front (for which you want to create URL for redirect to user here) and click on "Copy link address". Now you can use copied URL to anywhere to redirect the user. </i></p>'+

					'<span class="ac_label"l><?php _e("Tab Description",wpshopmart_tabs_pro_text_domain); ?></span>'+
					'<textarea  id="tabs_pro_desc-'+j+'" name="tabs_desc[]"  placeholder="Enter Tab Description Here" class="wpsm_ac_label_text">Sample Description</textarea>'+
					'<div class="col-md-12" style="padding-left:0;margin-top:10px;">'+
						'<div class="col-md-6 icon_icon_cls" style="padding-left:0;">'+
							'<span class="ac_label"><?php _e("Tab Icon",wpshopmart_tabs_pro_text_domain); ?></span>'+
							'<div class="form-group input-group" >'+
								'<input  class="form-control regular-text" id="tabs_title_icon[]" name="tabs_title_icon[]" value="fa fa-laptop" type="text" readonly="readonly" />'+
								'<span style="display:table-cell;width:1%;" class="input-group-addon icon-picker fa fa-laptop" data-target="#"></span>'+
							'</div>'+
						'</div>'+
						'<div class="form-group col-md-6 icon_img_size_settings_cls" style="padding-left:0;">'+
							'<span class="ac_label"><?php _e('Tab Icon Image',wpshopmart_tabs_pro_text_domain); ?></span>'+
							'<div class="input-group">'+
								'<span class="input-group-addon" style="padding:4px;">'+
									'<img class="wpsm-input-group-addon-img team-img-responsive" src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/images/default-image.png' ?>" />'+
								'</span>'+
							'<input type="button" name="" value="Upload Image" class="wpsm-input-group-addon-img-btn form-control btn btn-primary"  onclick="wpsm_media_upload(this)"/>'+
							'<input style="display:block;width:100%" type="hidden"  name="test_pro_icon_image[]"  value="<?php echo wpshopmart_tabs_pro_directory_url.'assets/images/default-image.png' ?>"  readonly="readonly" placeholder="No Media Selected" />'+
							'<input style="display:block;width:100%" type="hidden"  name="test_pro_icon_image_id[]" class="wpsm_ac_label_text"  value=""  readonly="readonly" placeholder="No Media Selected" />'+
							'</div>'+
						'</div>'+
					'</div>'+
					
					'<div class="margin-10 tabs_ind_clr_option_class" style="display:none;">'+
						'<span class="ac_label"><?php _e('Tabs Individual Color Settings',wpshopmart_tabs_pro_text_domain); ?></span>'+
						'<div class="col-md-12 ind_clr_settings_container">'+
							'<div class="col-md-4">'+
								'<div class="margin-10">'+
									'<span class="ac_label"><?php _e('Tabs Button Background Color',wpshopmart_tabs_pro_text_domain); ?></span>'+
									'<input id="indvid_tabs_title_bg_clr" name="indvid_tabs_title_bg_clr[]" type="text" value="#ff6699" class="my-color-field" data-default-color="#ffffff" />'+
								'</div>'+
							'</div>'+
							'<div class="col-md-4">	'+			
								'<div class="margin-10">'+
									'<span class="ac_label"><?php _e('Tabs Title Color',wpshopmart_tabs_pro_text_domain); ?></span>'+
									'<input id="indvid_tabs_title_font_clr" name="indvid_tabs_title_font_clr[]" type="text" value="#ff9966" class="my-color-field" data-default-color="#ffffff" />'+
								'</div>'+
							'</div>'+
							'<div class="col-md-4">'+
								'<div class="margin-10">'+
									'<span class="ac_label"><?php _e('Tabs Icon Color',wpshopmart_tabs_pro_text_domain); ?></span>'+
									'<input id="tabs_title_icon_clr" name="indvid_tabs_title_icon_clr[]" type="text" value="#99ff66" class="my-color-field" data-default-color="#ffffff" />'+
								'</div>'+
							'</div>'+
						'</div>	'+
					'</div>'+
		
				'</div>	'+
			'</div>'+
		'</li>';
	jQuery(output).hide().appendTo("#tabs_pro_panel").slideDown("slow");
	tinyMCE.execCommand('mceAddEditor', false, 'tabs_pro_desc-'+j);
	//jQuery( "#tabs_pro_panel" ).accordion( "refresh" );
	j++;
	hide_color_setting();
	fn_tabs_icon_setting();
	jQuery('.icon-picker').iconPicker();
	jQuery('.my-color-field').wpColorPicker();
	dynamic_change_header();
	}
	
</script>
<script>
	jQuery(function(jQuery)
		{
			var tabs_pro = 
			{
				tabs_pro_ul: '',
				init: function() 
				{
					this.tabs_pro_ul = jQuery('#tabs_pro_panel');

					this.tabs_pro_ul.on('click', '.remove_button', function() {
					if (confirm('Are you sure you want to delete this?')) {
						jQuery(this).parents("li:first").slideUp(600, function() {
							jQuery(this).remove();
						});
					}
					return false;
					});
					 jQuery('#delete_all_colorbox').on('click', function() {
						if (confirm('Are you sure you want to delete all the Tabs?')) {
							jQuery(".single_color_box").slideUp(600, function() {
								jQuery(".single_color_box").remove();
							});
							jQuery('html, body').animate({ scrollTop: 0 }, 'fast');
							
						}
						return false;
					});
					
			   }
			};
		tabs_pro.init();
	});
</script>
<script>
	function open_editor(id){
		var value = jQuery("#"+id).closest('li').find('textarea').val();
		jQuery("#get_text-html").click();
		jQuery("#get_text").val(value);
		jQuery("#get_id").val(jQuery("#"+id).attr('id'));
	 }
	
	
	function insert_html(){
		jQuery("#get_text-html").click();
		var html_text = jQuery("#get_text").val();
		var id = jQuery("#get_id").val();
		jQuery("#"+id).closest('li').find('textarea').val(html_text);
			
	}
	
	//change header
	function dynamic_change_header(){
		jQuery('.wpsm_tabs_pro_title').on('input', function() {
			cur_text=jQuery(this).val();
			jQuery(this).parents("li").find('.panel-heading').find("span").text(cur_text);
		});
	}
</script>