<?php
//****************************Css Variables
switch($sb_set_box_layout)
	{	
		case(12):
			$row=1;
		break;
		case(6):
			$row=2;
		break;
		case(4):
			$row=3;
		break;
		case(3):
			$row=4;
		break;
		case(5):
			$row=5;
		break;
		case(2):
			$row=6;
		break;
		case(8):
			$row=8;
		break;
		case(10):
			$row=10;
		break;
	}

//Background Color	
if($sb_set_bg_opacity<=99)
	$sb_set_bg_opacity="0.".$sb_set_bg_opacity;
else
	$sb_set_bg_opacity="1";
$var_sb_bg_clr_temp=$sb_set_bg_clr;
$sb_set_bg_clr="rgba(". HextoR($sb_set_bg_clr).",".HextoG($sb_set_bg_clr).",".HextoB($sb_set_bg_clr).",".$sb_set_bg_opacity.")";

//Font Family
if($sb_set_enable_family=="yes") 
	$service_box_font_family ="font-family:".$sb_set_font_family.";";
 else
	$service_box_font_family=""; 
 

//*************************************Other Valriables********************************* 
 //gatting title
if($sb_set_sec_title_y_n=="yes")
{	
	if(get_the_title()=="")
	$wpsm_title ="<div class='wpsm_sb_section_title_".$PostId." text-center'><h3>No Title</h3></div>";
	else
	$wpsm_title ="<div class='wpsm_sb_section_title_".$PostId." text-center'><h3>".get_the_title()."</h3></div>";
}	
else
	$wpsm_title= "";

//Getting Carousel/Grid
if($sb_all_contents_view_type=="carousel")
{
	$sb_wrapper_cls= "wpsm_service_box_carousel_$PostId wpsm_sb_wrapper_$PostId owl-carousel owl-theme";
	$sb_wrapper_id="wpsm_service_box_carousel_$PostId";
}
else
{
	$sb_wrapper_cls= "wpsm_sb_wrapper_$PostId";
	$sb_wrapper_id="wpsm_service_box_grid_$PostId";
}
//getting Grid/Carousel Layout Class
if($sb_all_contents_view_type=="carousel")
	$sb_Layout_Cls= "item";
elseif(in_array($sb_set_box_layout, array("12", "6", "4", "3", "2")))
	$sb_Layout_Cls= "col-md-".$sb_set_box_layout ." col-sm-6";
else
	$sb_Layout_Cls=" wpsm_col-md-".$sb_set_box_layout." wpsm_col-lg-".$sb_set_box_layout;


//*************************************Carousel Valriables*********************************
if($sb_all_contents_view_type=="carousel")
{
	//Nav Type
	if($sb_set_carousel_nav_type=='1' || $sb_set_carousel_nav_type=='3'){$carousel_nav_type ='true';}
	else {$carousel_nav_type = "false";}

	//Dots Type
	if($sb_set_carousel_nav_type=='2' || $sb_set_carousel_nav_type=='3'){$carousel_dots_type = 'true';}
	else{$carousel_dots_type = "false";}

	//Loop
	if($sb_set_carousel_loop=='true'){$carousel_loop= "true";}
	else{$carousel_loop=  "false";}

	//Autoplay
	if($sb_set_carousel_autoplay){$carousel_autoplay= $sb_set_carousel_autoplay;}
	else {$carousel_autoplay= "false";}

	//autoplay time out
	if($sb_set_carousel_autoplay_interval_time_out){$autoplay_interval_time_out= $sb_set_carousel_autoplay_interval_time_out;}
	else{$autoplay_interval_time_out= "5000";}

	//Smart Speed
	 if($sb_set_carousel_autoplay_speed_y_n=='true')
	{
		if($sb_set_carousel_autoplay_speed){$autoplay_speed= $sb_set_carousel_autoplay_speed;}
	}
	else{$autoplay_speed= "250";}

	//Hover Pause
	if($sb_set_carousel_autoplay_hover_pause){$autoplay_hover_pause= $sb_set_carousel_autoplay_hover_pause;}
	else{$autoplay_hover_pause= "false";}

	//No Of items
	if($sb_set_box_layout && $row){$no_of_items= $row;}
	else{$no_of_items= "4";}
	
	
	
	/* *******************************************************************************************************
						Adding Navigation Btn
	********************************************************************************************************* */
	$nav_left_text="";
	$nav_right_text="";
	if($sb_set_carousel_nav_type=='1' || $sb_set_carousel_nav_type=='3') //for Only Btn/Both Buttons and Dots
	{	
		switch($sb_set_carousel_nav_btn_type)
		{	
			case(1):   //only text
				$nav_left_text= $sb_set_carousel_nav_left_text;
				$nav_right_text= $sb_set_carousel_nav_right_text;
			break;
			case(2):  //only icon
					switch($sb_set_carousel_nav_btn_icon_type)
					{	
						case(1):  //design 1
							$nav_left_text= "<i class='fa fa-angle-double-left wpsm_sb_carousel_btn_icon'></i>";
							$nav_right_text="<i class='fa fa-angle-double-right wpsm_sb_carousel_btn_icon'></i>";
						break;
						case(2):  //design 2
							$nav_left_text= "<i class='fa fa-arrow-left wpsm_sb_carousel_btn_icon'></i>";
							$nav_right_text="<i class='fa fa-arrow-right wpsm_sb_carousel_btn_icon'></i>";
						break;
						case(3):  //design 3
							$nav_left_text= "<i class='fa fa-angle-left wpsm_sb_carousel_btn_icon'></i>";
							$nav_right_text="<i class='fa fa-angle-right wpsm_sb_carousel_btn_icon'></i>";
						break;
						case(4):  //design 4
							$nav_left_text= "<i class='fa fa-chevron-left wpsm_sb_carousel_btn_icon'></i>";
							$nav_right_text="<i class='fa fa-chevron-right wpsm_sb_carousel_btn_icon'></i>";
						break;
					}
			break;
			case(3):  //Both Text + Icon
					switch($sb_set_carousel_nav_btn_icon_type)
					{	
						case(1):  //design 1
							$nav_left_text= "<i class='fa fa-angle-double-left wpsm_sb_carousel_btn_icon'></i> ".$sb_set_carousel_nav_left_text;
							$nav_right_text=$sb_set_carousel_nav_right_text." <i class='fa fa-angle-double-right wpsm_sb_carousel_btn_icon'></i>";
						break;
						case(2):  //design 2
							$nav_left_text= "<i class='fa fa-arrow-left wpsm_sb_carousel_btn_icon'></i> ".$sb_set_carousel_nav_left_text;
							$nav_right_text=$sb_set_carousel_nav_right_text." <i class='fa fa-arrow-right wpsm_sb_carousel_btn_icon'></i>";
						break;
						case(3):  //design 3
							$nav_left_text= "<i class='fa fa-angle-left wpsm_sb_carousel_btn_icon'></i> ".$sb_set_carousel_nav_left_text;
							$nav_right_text=$sb_set_carousel_nav_right_text." <i class='fa fa-angle-right wpsm_sb_carousel_btn_icon'></i>";
						break;
						case(4):  //design 4
							$nav_left_text= "<i class='fa fa-chevron-left wpsm_sb_carousel_btn_icon'></i> ".$sb_set_carousel_nav_left_text;
							$nav_right_text=$sb_set_carousel_nav_right_text." <i class='fa fa-chevron-right wpsm_sb_carousel_btn_icon'></i>";
						break;
					}
			break;
		}
		
	}
}//carousel if end
?>