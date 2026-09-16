<?php
	$PostId = get_the_ID();
	$De_Settings = unserialize(get_option('Tabs_pro_default_Settings'));
	$Tabs_Settings = unserialize(get_post_meta( $PostId, 'Tabs_pro_Settings', true));
	
	$option_names = array(
		"templates_presets"	=> $De_Settings['templates_presets'],
		"templates_h"   	=> $De_Settings['templates_h'],
		"templates_v"   	=> $De_Settings['templates_v'],
		);
		
	foreach($option_names as $option_name => $default_value) {
		if(isset($Tabs_Settings[$option_name])) 
			${"" . $option_name}  = $Tabs_Settings[$option_name];
		else
			${"" . $option_name}  = $default_value;
	}
?>

<div class="tabs_pro_admin_wrapper">
	<div class="wpsm_site_sidebar_widget_title">
		<h4>Select Presets</h2>
	</div>
	<div class="col-md-3">
		<div class="demoftr">	
			<span class="checked_temp_preset checked_temp_radio" id="checked_temp_preset_1" <?php if($templates_presets!=1) { ?>  style="display:none" <?php } ?>><i class="fa fa-check"></i></span>
			<div class="wpsm_home_portfolio_showcase">
				<img class="wpsm_img_responsive ftr_img" src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/images/horizontal.png'?>">
			</div>
			<div class="wpsm_home_portfolio_links">
				<h3 class="text-center pull-left">Horizontal Tabs</h3>
				<button type="button" <?php if($templates_presets==1) { ?> disabled="disabled" <?php } ?> class="pull-right btn btn-primary design_btn_preset design_select_btn" id="templates_btn_preset_1" onclick="select_preset('1')"><?php if($templates_presets==1){  echo "Selected"; } else { echo "Select"; } ?></button>
				<input type="radio" name="templates_presets" id="templates_presets_1" value="1" <?php if($templates_presets=='1'){  echo "checked"; } ?>  style="display:none">
		
			</div>	
		</div>	
	</div>
	<div class="col-md-3">
		<div class="demoftr">	
			<span class="checked_temp_preset checked_temp_radio" id="checked_temp_preset_2" <?php if($templates_presets!=2) { ?>  style="display:none" <?php } ?>><i class="fa fa-check"></i></span>
			<div class="wpsm_home_portfolio_showcase">
				<img class="wpsm_img_responsive ftr_img" src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/images/vertical.png'?>">
			</div>
			<div class="wpsm_home_portfolio_links">
			<h3 class="text-center pull-left">Vertical Tabs</h3>
			<button type="button" <?php if($templates_presets==2) { ?> disabled="disabled" <?php } ?> class="pull-right btn btn-primary design_btn_preset design_select_btn" id="templates_btn_preset_2" onclick="select_preset('2')"><?php if($templates_presets==2){  echo "Selected"; } else { echo "Select"; } ?></button>
			<input type="radio" name="templates_presets" id="templates_presets_2" value="2" <?php if($templates_presets=='2'){  echo "checked"; } ?> style="display:none" >
		
			</div>	
		</div>	
	</div>
</div>

<div class="tabs_pro_admin_wrapper">	
	<div class="wpsm_site_sidebar_widget_title">
		<h4>Select Styles</h2>
	</div>
	
	<a href="#0" id="cd-btn-h" class="btn btn btn-primary btn-lg btn-select-design-button btn-select-design-button-1" <?php if($templates_presets!=1) { ?>  style="display:none" <?php } ?>>Select Horizontal Designs</a>
	<a href="#0"  id="cd-btn-v" class="btn btn btn-primary btn-lg  btn-select-design-button btn-select-design-button-2" <?php if($templates_presets!=2) { ?>  style="display:none" <?php } ?>>Select Vertical Designs</a>
</div>
	<div class="cd-panel from-right" id="cd-panel-h">
		<header class="cd-panel-header">
			<h1>Horizontal Designs</h1>
			<a href="#0" class="cd-panel-close" id="cd-panel-close-h">Close</a>
		</header>

		<div class="cd-panel-container">
			<div class="cd-panel-content">
			<?php for($i=1;$i<=13;$i++){ ?>
				<div class="col-md-12">
					<div class="demoftr">	
						<span class="checked_temp_h checked_temp_radio" id="checked_temp_h_<?php echo $i; ?>" <?php if($templates_h!=$i) { ?>  style="display:none" <?php } ?> ><i class="fa fa-check"></i></span>
						<div class="wpsm_home_portfolio_showcase">
							<img class="wpsm_img_responsive ftr_img" src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/images/design/tabs-'.$i.'.png'?>">
							<span><a target="_new" href="http://demo.wpshopmart.com/accordion-pro/tabs-pro-<?php echo $i; ?>/">Demo</a></span>
						</div>
						<div class="wpsm_home_portfolio_links">
							<h3 class="text-center pull-left">Design <?php echo $i; ?></h3>
							<button type="button" <?php if($templates_h==$i) { ?> disabled="disabled" <?php } ?> class="pull-right btn btn-primary design_btn_h design_select_btn" id="templates_btn_h_<?php echo $i; ?>" onclick="select_template_h('<?php echo $i; ?>')"><?php if($templates_h==$i){  echo "Selected"; } else { echo "Select"; } ?></button>
							<input type="radio" name="templates_h" id="design_h_<?php echo $i; ?>" value="<?php echo $i; ?>" <?php if($templates_h==$i){  echo "checked"; } ?> style="display:none">
						</div>		
					</div>		
				</div>
			<?php } ?>
			</div> <!-- cd-panel-content -->
		</div> <!-- cd-panel-container -->
	</div> <!-- cd-panel -->
	<div class="tabs_pro_admin_wrapper">
		<div class="cd-panel from-right" id="cd-panel-v">
			<header class="cd-panel-header">
				<h1>Vertical Designs</h1>
				<a href="#0" class="cd-panel-close" id="cd-panel-close-v">Close</a>
			</header>

			<div class="cd-panel-container">
				<div class="cd-panel-content">
					<?php for($i=14;$i<=20;$i++){ ?>
						<div class="col-md-12">
							<div class="demoftr">	
								<span class="checked_temp_v checked_temp_radio" id="checked_temp_v_<?php echo $i; ?>" <?php if($templates_v!=$i) { ?>  style="display:none" <?php } ?> ><i class="fa fa-check"></i></span>
								<div class="wpsm_home_portfolio_showcase">
									<img class="wpsm_img_responsive ftr_img" src="<?php echo wpshopmart_tabs_pro_directory_url.'assets/images/design/tabs-'.$i.'.png'?>">
									<span><a target="_new" href="http://demo.wpshopmart.com/accordion-pro/tabs-pro-<?php echo $i; ?>/">Demo</a></span>
								</div>
								<div class="wpsm_home_portfolio_links">
									<h3 class="text-center pull-left">Design <?php echo $i; ?></h3>
									<button type="button" <?php if($templates_v==$i) { ?> disabled="disabled" <?php } ?> class="pull-right btn btn-primary design_btn_v design_select_btn" id="templates_btn_v_<?php echo $i; ?>" onclick="select_template_v('<?php echo $i; ?>')"><?php if($templates_v==$i){  echo "Selected"; } else { echo "Select"; } ?></button>
									<input type="radio" name="templates_v" id="design_v_<?php echo $i; ?>" value="<?php echo $i; ?>" <?php if($templates_v==$i){  echo "checked"; } ?> style="display:none">
								</div>		
							</div>	
						</div>
					<?php } ?>
				
				</div> <!-- cd-panel-content -->
			</div> <!-- cd-panel-container -->
		</div> <!-- cd-panel -->
	</div>

<script>

function select_template_h(id)
{
	
	jQuery(".design_btn_h").attr('style','');
	jQuery(".design_btn_h").prop("disabled", false);
	jQuery(".design_btn_h").text("Select");
	
	jQuery(".checked_temp_h").hide();
	jQuery("#checked_temp_h_"+id).show();
	
	
	jQuery("#templates_btn_h_"+id).attr('disabled','disabled');
	jQuery("#templates_btn_h_"+id).attr('style','background:#F50000;border-color:#F50000;');
	jQuery("#templates_btn_h_"+id).text("Selected");
	 jQuery("#design_h_"+id).prop( "checked", true );
	
}
function select_template_v(id)
{
	
	jQuery(".design_btn_v").attr('style','');
	jQuery(".design_btn_v").prop("disabled", false);
	jQuery(".design_btn_v").text("Select");
	
	jQuery(".checked_temp_v").hide();
	jQuery("#checked_temp_v_"+id).show();
	
	
	jQuery("#templates_btn_v_"+id).attr('disabled','disabled');
	jQuery("#templates_btn_v_"+id).attr('style','background:#F50000;border-color:#F50000;');
	jQuery("#templates_btn_v_"+id).text("Selected");
	 jQuery("#design_v_"+id).prop( "checked", true );
}

function select_preset(id)
{
	
	jQuery(".design_btn_preset").attr('style','');
	jQuery(".design_btn_preset").prop("disabled", false);
	jQuery(".design_btn_preset").text("Select");
	
	jQuery(".checked_temp_preset").hide();
	jQuery("#checked_temp_preset_"+id).show();
	
	jQuery(".checked_temp_preset").hide();
	jQuery("#checked_temp_preset_"+id).show();
	
	jQuery(".btn-select-design-button").hide();
	
	jQuery(".btn-select-design-button-"+id).show();
	
	
	
	jQuery("#templates_btn_preset_"+id).attr('disabled','disabled');
	jQuery("#templates_btn_preset_"+id).attr('style','background:#F50000;border-color:#F50000;');
	jQuery("#templates_btn_preset_"+id).text("Selected");
	 jQuery("#templates_presets_"+id).prop( "checked", true );
	 if(id=="2")
		jQuery(".wpsm_tabs_btn_width_cls").hide(600); 
	else
		jQuery(".wpsm_tabs_btn_width_cls").show(600); 
}

jQuery(document).ready(function($){
	//open the lateral panel
	$('#cd-btn-h').on('click', function(event){
		event.preventDefault();
		$('#cd-panel-h').addClass('is-visible');
	});
	//clode the lateral panel
	$('#cd-panel-h').on('click', function(event){
		if( $(event.target).is('#cd-panel-h') || $(event.target).is('#cd-panel-close-h') ) { 
			$('#cd-panel-h').removeClass('is-visible');
			event.preventDefault();
		}
	});
	//open the lateral panel
	$('#cd-btn-v').on('click', function(event){
		event.preventDefault();
		$('#cd-panel-v').addClass('is-visible');
	});
	//clode the lateral panel
	$('.cd-panel').on('click', function(event){
		if( $(event.target).is('#cd-panel-v') || $(event.target).is('#cd-panel-close-v') ) { 
			$('#cd-panel-v').removeClass('is-visible');
			event.preventDefault();
		}
	});
	
});

</script>