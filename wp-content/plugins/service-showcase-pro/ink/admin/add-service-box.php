<?php
 $PostId = $post->ID;
 $content_flag=0;
 $total_contents=2;
 $tot_designs=55;
 
//getting settings value(default/save)
$sb_settings = unserialize(get_post_meta( $PostId, 'service_box_settings', true));
$Default_Settings = unserialize(get_option('service_box_pro_default_Settings'));
$option_names = array(
		"sb_all_contents_view_type"								=>$Default_Settings['sb_all_contents_view_type'],
		"sb_all_contents_design_no"								=>$Default_Settings['sb_all_contents_design_no'],
		"sb_ind_clr_enable"										=>$Default_Settings['sb_ind_clr_enable'],
		"custom_css"											=>$Default_Settings['custom_css']
	);
foreach($option_names as $option_name => $default_value) 
{
	if(isset($sb_settings[$option_name])) 
		${"" . $option_name}  = $sb_settings[$option_name];
	else
		${"" . $option_name}  = $default_value;
}


//Getting Default Data	
$data_option_names = array(
	 "sb_all_contents_title"=>"Sample Title",
	 "sb_all_contents_description"=>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec feugiat.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec feugiat.",
	 "sb_all_contents_btn_img_or_icon"=>"Icon",
	 "sb_all_contents_icons"=>"fa fa-laptop",
	 "sb_all_contents_images"=>service_box_directory_url."assets/images/Responsive_Web.png",
	 "sb_all_contents_btn_link_yes_no"=>"Yes",
	 "sb_all_contents_btn_link_text"=>"Read More",
	 "sb_all_contents_btn_link_url"=>"https://www.google.co.in/",
	 "sb_all_contents_btn_link_open_in"=>"Yes",
	 
	"sb_all_contents_icon_clr"=>"#000000",
	"sb_all_contents_icon_bg_clr"=>"#ffffff",
	"sb_all_contents_title_clr"=>"#008b8b",
	"sb_all_contents_des_clr"=>"#d3d3d3",
	"sb_all_contents_link_clr"=>"#ffffff",
	"sb_all_contents_link_bg_clr"=>"#008b8b",
	"sb_all_contents_bg_clr"=>"#ffffff",
	"sb_all_contents_brdr_clr"=>"#d3d3d3",
	"sb_all_contents_icon_brdr_clr"=>"#000000"
	);
?>
<!-- ************************** Select Service BOX View(Grid/Carousel) ******************* -->
<div class="wpsm_sb_designs">
	<div class="row mycpt_row" style="padding: 10px;">
		<!--Select View-->
		<div class="wpsm_site_sidebar_widget_title">
			<h4>Select Presets</h2>
		</div>
		<div class='col-md-2 wpsm_sb_presets'>
			<div class="select_view">
				<div style=" position: relative;border:2px solid black;<?php if($sb_all_contents_view_type=='grid'){echo "background-color:rgb(76,175,180);";}?>" class='box' id="box2">
					<img src="<?php echo service_box_directory_url;?>assets/images/grid.png" style="width:80px; height:80px;border:2px solid black;margin:5px;" />
					<input type='radio' name="sb_all_contents_view_type" class="check_delete_im" value="grid" id="service_box_radio2" style="display:none;" <?php if($sb_all_contents_view_type=='grid'){echo "checked";}?>>
					<div style="position: absolute; right: 13px; top: 10px;opacity:1;<?php if($sb_all_contents_view_type!='grid'){echo "display:none;";}?>" id="check_icon2" class="check_icon">
						<i class="fa fa-check-circle fa-lg" ></i>
					</div>
				</div>
			<h4>Grid</h4>
			</div>
		</div>
		<div class='col-md-2 text-center wpsm_sb_presets'>
			<div class="select_view">
				<div style=" position: relative;border:2px solid black;<?php if($sb_all_contents_view_type=='carousel'){echo "background-color:rgb(76,175,180);";}?>" class='box' id="box1">
					<img src="<?php echo service_box_directory_url;?>assets/images/carousel.png" style="width:80px; height:80px;border:2px solid black;margin:5px;" />
					<input type='radio' name="sb_all_contents_view_type" class="check_delete_im" value="carousel" id="service_box_radio1" style="display:none;" <?php if($sb_all_contents_view_type=='carousel'){echo "checked";}?>>
					<div style="position: absolute; right: 13px; top: 10px;opacity:1;<?php if($sb_all_contents_view_type!='carousel'){echo "display:none;";}?>" id="check_icon1" class="check_icon">
						<i class="fa fa-check-circle fa-lg" ></i>
					</div>
				</div>
			<h4>Carousel</h4>
			</div>
		</div>
		<div class='col-md-8'></div>
	</div>
			
	<!-- ************************************ Select Service BOX Design **************************** -->
	<div class="row mycpt_row design" style="clear:both;padding: 10px;" id="design">
		<div class="wpsm_site_sidebar_widget_title">
			<h4>Select Styles</h2>
		</div>
		<div class='col-md-4'>
			<!-- Trigger/Open The Modal -->
			<a href="#0" id="service_box_btn_modal_grid" class="service_box_btn_modal btn btn-primary btn-lg" >Select  Designs</a>
		</div>		
		
		<div class="cd-panel from-right" id="sb-cd-panel">
			<header class="cd-panel-header">
				<h1>Service Box Designs</h1>
				<a href="#0" class="cd-panel-close" id="sb-cd-panel-close">Close</a>
			</header>

			<div class="cd-panel-container">
				<div class="cd-panel-content">
				<?php for($i=1;$i<=$tot_designs;$i++){ ?>
					<div class="col-md-6">
						<div class="demoftr">	
							<span class="design_checked_icon checked_temp_radio" id="design_checked_icon_<?php echo $i; ?>" <?php if($sb_all_contents_design_no!=$i) { ?>  style="display:none" <?php } ?> ><i class="fa fa-check"></i></span>
							<div class="wpsm_home_portfolio_showcase">
								<img class="wpsm_img_responsive ftr_img" src="<?php echo service_box_directory_url."assets/images/Designs/design-".$i.".png"; ?>">
								<span><a target="_new" href="http://demo.wpshopmart.com/accordion-pro/tabs-pro-<?php echo $i; ?>/">Demo</a></span>
							</div>
							<div class="wpsm_home_portfolio_links">
								<h3 class="text-center pull-left">Design <?php echo $i; ?></h3>
								<button type="button" <?php if($sb_all_contents_design_no==$i) { ?> disabled="disabled" <?php } ?> class="pull-right btn btn-primary design_select_btn" id="templates_btn_select_<?php echo $i; ?>" onclick="select_template_design('<?php echo $i; ?>')"><?php if($sb_all_contents_design_no==$i){  echo "Selected"; } else { echo "Select"; } ?></button>
								<input type="radio" name="sb_all_contents_design_no" id="sb_all_contents_design_no_<?php echo $i; ?>" value="<?php echo $i; ?>" <?php if($sb_all_contents_design_no==$i){  echo "checked"; } ?> style="display:none">
							</div>		
						</div>		
					</div>
				<?php 
					if($i%2==0) echo "<div style='clear:both;'></div>";
				} ?>
				</div> <!-- cd-panel-content -->
			</div> <!-- cd-panel-container -->
		</div> <!-- cd-panel -->
	<!--modal end-->
	</div>
</div>
<!-- end design selection--->
<div style="clear:both;"></div>

<!-- ********************************Service box ************************************** --->
<div class="service_box" style="margin-top:20px;padding: 10px;">
	<div class="wpsm_site_sidebar_widget_title">
		<h4>Add Service Box Here</h2>
	</div>
	<input type="hidden" name="sb_all_contents_hidden" value="itshidden">
	<ul id="content_holder">
		<?php 
		$i=1;
		$sb_all_data = unserialize(get_post_meta( $post->ID, 'sb_pro_all_data', true));
		$sb_TotalCount =  get_post_meta( $post->ID, 'sb_pro_total_contents', true );
		
		if($sb_TotalCount) 
		{
			if($sb_TotalCount!=-1)
			{
				foreach($sb_all_data as $sb_single_set_data)
				{  	
					
					foreach($data_option_names as $data_option_name => $data_default_value) 
					{	
						${"" . $data_option_name} = $sb_single_set_data[$data_option_name];							
					}
					require('append-contents.php'); 
					$i++;
					
				}
				?>
					<h2 id='wpsm_no_sb_found_id' style='display:none;'>No Service Box Found</h2>
				<?php
				
			}
			else
			{
				echo "<h2 id='wpsm_no_sb_found_id'>No Service Box Found</h2>";
			}
			
		}
		else  //If any value not found then load
		{
			for($i=1;$i<=2;$i++)
			{	
				
				foreach($data_option_names as $data_option_name => $data_default_value) 
				{	
					${"" . $data_option_name} = $data_default_value;	
				}
				require('append-contents.php'); 
				
			}
			?>
				<h2 id='wpsm_no_sb_found_id' style='display:none;'>No Service Box Found</h2>
			<?php
		}
		?>
	</ul>
	
</div>
		
<div class="row mycpt_row">
	<a class='col-md-5 btn btn-primary mycpt_col btn-lg' id="add_con" onclick="add_new_sb_content()">
				Add Service Box
	</a>
	<div class="col-md-2"></div>
	<a style="<?php if($sb_TotalCount==-1){echo "display:none;";}?>" class='col-md-5 btn btn-danger mycpt_col btn-lg' id="btn_delete_all_con" onclick="delete_all_content()">
				Delete all Service Box
	</a>
</div>
<div style="clear:both"></div>

<?php	  
require('footer-script.php');
?>