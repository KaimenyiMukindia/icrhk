<style>
/* design 4 */

.wpsm_tab_<?php echo $post_id; ?>{
	padding:20px;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs{
    border:none;
    padding: 0 0 10px;
    position: relative;
    perspective: 17em;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
	 margin-right:10px;
	margin-bottom:5px;
    box-shadow: 0 0  3px rgba(0,0,0,0.2);
	transform-origin: 0 0;
	animation: swing 1.5s ease-in-out infinite alternate;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:nth-child(1){
    animation-delay: 0.9s;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:nth-child(2){
    animation-delay: 0.6s;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:nth-child(3){
    animation-delay: 0.3s;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li:nth-child(4){
    animation-delay: 0.0s;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a{
    outline: none;
    padding:15px 15px;
    background:<?php echo $tabs_title_bg_clr; ?>;
    color:<?php echo $tabs_title_font_clr; ?>;
    text-transform:uppercase;
    border:1px solid <?php echo $tabs_btn_border_color;?>;
	font-size: <?php echo $tabs_title_size; ?>px;
    font-weight:<?php echo intval($tabs_title_icon_font_weight/100)*100;?>;
	font-family:<?php echo $all_tabs_font_family;?>;
	text-align:center;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li a i{
	font-size: <?php echo $tabs_icon_size; ?>px;
	color: <?php echo $tabs_title_icon_clr; ?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a i{
	color: <?php echo $select_tabs_icon_clr; ?> !important;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:focus,
.wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li.active a:hover{
    background: <?php echo $select_tabs_bg_clr; ?>  !important;
    color:<?php echo $select_tabs_title_clr; ?>  !important;
    border:1px solid <?php echo $select_tabs_btn_border_color; ?>;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
    padding:20px 25px;
    background:<?php echo $tabs_desc_bg_clr; ?>;
    border: <?php echo $tabs_border_size; ?>px solid <?php echo $tabs_border_color; ?>;
	text-transform:capitalize;
    color:<?php echo $tabs_desc_font_clr; ?>;
    font-size: <?php echo $tabs_des_size; ?>px;
	font-family:<?php echo $all_tabs_font_family;?>;
	line-height:1.6;
}
.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content .tab-pane p{
	color:<?php echo $tabs_desc_font_clr; ?>;
    font-size: <?php echo $tabs_des_size; ?>px;
	font-family:<?php echo $all_tabs_font_family;?>;
	background:<?php echo $tabs_desc_bg_clr; ?>;
    border:none;
	line-height:1.6;
	padding:0;
	text-align:left;
}
@keyframes swing {
    from {
		transform: rotateX(10deg) rotateZ(0deg);
		} 
	to {
		transform: rotateX(-5deg) rotateZ(0deg); 
		}
}
@media only screen and (max-width: 640px) {
    .wpsm_tab_<?php echo $post_id; ?> .wpsm_nav-tabs li{
        width:100%  !important;
		margin-bottom:10px;
    }
	<?php if($tabs_border_size>0){?>
		.wpsm_tab_<?php echo $post_id; ?> .wpsm_tab_content{
			border: 1px solid <?php echo $tabs_border_color;?>;
		}
	<?php }?>
	
}
</style>