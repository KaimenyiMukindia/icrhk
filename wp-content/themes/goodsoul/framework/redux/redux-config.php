<?php
/**
 * ReduxFramework Barebones Sample Config File
 * For full documentation, please visit: http://docs.reduxframework.com/
 */
if ( ! class_exists( 'Redux' ) ) {
	return;
}

// This is your option name where all the Redux data is stored.
$opt_prefix = 'goodsoul_';
$opt_name   = 'goodsoul_options';
/**
 * ---> SET ARGUMENTS
 * All the possible arguments for Redux.
 * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
 * */
$theme = wp_get_theme(); // For use with some settings. Not necessary.
$args  = array(
	// TYPICAL -> Change these values as you need/desire
	'opt_name'             => $opt_name,
	// This is where your data is stored in the database and also becomes your global variable name.
	'display_name'         => $theme->get( 'Name' ),
	// Name that appears at the top of your panel
	'display_version'      => $theme->get( 'Version' ),
	// Version that appears at the top of your panel
	'menu_type'            => 'menu',
	// Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
	'allow_sub_menu'       => true,
	// Show the sections below the admin menu item or not
	'menu_title'           => esc_html__( 'Goodsoul Options', 'goodsoul' ),
	'page_title'           => esc_html__( 'Goodsoul Options', 'goodsoul' ),
	// You will need to generate a Google API key to use this feature.
	// Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
	'google_api_key'       => '',
	// Set it you want google fonts to update weekly. A google_api_key value is required.
	'google_update_weekly' => false,
	// Must be defined to add google fonts to the typography module
	'async_typography'     => true,
	// Use a asynchronous font on the front end or font string
	// 'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
	'admin_bar'            => true,
	// Show the panel pages on the admin bar
	'admin_bar_icon'       => 'dashicons-portfolio',
	// Choose an icon for the admin bar menu
	'admin_bar_priority'   => 50,
	// Choose an priority for the admin bar menu
	'global_variable'      => '',
	// Set a different name for your global variable other than the opt_name
	'dev_mode'             => false,
	// Show the time the page took to load, etc
	'update_notice'        => true,
	// If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
	'customizer'           => true,
	// Enable basic customizer support
	// 'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
	// 'disable_save_warn' => true,                    // Disable the save warning when a user changes a field
	// OPTIONAL -> Give you extra features
	'page_priority'        => null,
	// Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
	'page_parent'          => 'themes.php',
	// For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
	'page_permissions'     => 'manage_options',
	// Permissions needed to access the options panel.
	'menu_icon'            => '',
	// Specify a custom URL to an icon
	'last_tab'             => '',
	// Force your panel to always open to a specific tab (by id)
	'page_icon'            => 'icon-themes',
	// Icon displayed in the admin panel next to your menu_title
	'page_slug'            => '_options',
	// Page slug used to denote the panel
	'save_defaults'        => true,
	// On load save the defaults to DB before user clicks save or not
	'default_show'         => false,
	// If true, shows the default value next to each field that is not the default value.
	'default_mark'         => '',
	// What to print by the field's title if the value shown is default. Suggested: *
	'show_import_export'   => true,
	// Shows the Import/Export panel when not used as a field.
	// CAREFUL -> These options are for advanced use only
	'transient_time'       => 60 * MINUTE_IN_SECONDS,
	'output'               => true,
	// Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
	'output_tag'           => true,
	// Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
	// 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.
	// FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
	'database'             => '',
	// possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
	'use_cdn'              => true,
	// If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin yourself and run locally or embed it in your code.
	// 'compiler'             => true,
);
Redux::setArgs( $opt_name, $args );
Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'Base theme option', 'goodsoul' ),
		'id'     => 'base_theme_option',
		'desc'   => esc_html__( 'Chnage Base theme option here', 'goodsoul' ),
		'icon'   => 'el el-home',
		'fields' => array(
			array(
				'id'      => $opt_prefix . 'preloader_on_off',
				'type'    => 'switch',
				'title'   => esc_html__( 'Preloader on off switch', 'goodsoul' ),
				'default' => false,
				'on'      => esc_html__( 'Enable', 'goodsoul' ),
				'off'     => esc_html__( 'Disable', 'goodsoul' ),
			),
			array(
				'id'      => $opt_prefix . 'back_to_top_on_off',
				'type'    => 'switch',
				'title'   => esc_html__( 'Back To Top on off switch', 'goodsoul' ),
				'default' => false,
				'on'      => esc_html__( 'Enable', 'goodsoul' ),
				'off'     => esc_html__( 'Disable', 'goodsoul' ),
			),
			array(
				'id'      => $opt_prefix . 'event_full_width',
				'type'    => 'switch',
				'title'   => esc_html__( 'Event Section full width', 'goodsoul' ),
				'default' => false,
				'on'      => esc_html__( 'Enable', 'goodsoul' ),
				'off'     => esc_html__( 'Disable', 'goodsoul' ),
			),
			array(
				'id'      => $opt_prefix . 'header_sidear_text',
				'type'    => 'textarea',
				'title'   => esc_html__( 'sidebar Header', 'goodsoul' ),
				'default' => 'To improve learning environment in primary schools',
			),
			array(
				'id'      => $opt_prefix . 'header_sidear_content',
				'type'    => 'textarea',
				'title'   => esc_html__( 'sidebar content', 'goodsoul' ),
				'default' => 'We denounce with righteous indignation and dislike men who we are to beguiled demoralized by the charms of pleasures that moment, so we blinded desires, that they indignation.',
			),
			array(
				'id'    => $opt_prefix . 'header_payment_content',
				'type'  => 'textarea',
				'title' => esc_html__( 'Payment content', 'goodsoul' ),
			),
			array(
				'id'      => $opt_prefix . 'header_sidear_text2',
				'type'    => 'textarea',
				'title'   => esc_html__( 'News update title', 'goodsoul' ),
				'default' => 'News & Updates',

			),
			array(
				'id'      => $opt_prefix . 'header_sidear_text3',
				'type'    => 'textarea',
				'title'   => esc_html__( 'Subscribe', 'goodsoul' ),
				'default' => 'Newsletter Subscription',
			),
			array(
				'id'    => $opt_prefix . 'header_sidear_shortcode',
				'type'  => 'textarea',
				'title' => esc_html__( 'Newsletter Sidebar Shortcode', 'goodsoul' ),
			),
		),
	)
);
Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'Header Top Bar option', 'goodsoul' ),
		'id'     => 'header_top_bar',
		'desc'   => esc_html__( 'Change Header Top Bar here', 'goodsoul' ),
		'icon'   => 'el el-home',
		'fields' => array(
			array(
				'id'      => $opt_prefix . 'header_tob_bar_style',
				'type'    => 'select',
				'title'   => esc_html__( 'Header style', 'goodsoul' ),
				'options' => array(
					'1' => esc_html__( 'Header  one', 'goodsoul' ),
					'2' => esc_html__( 'Header  two', 'goodsoul' ),
					'3' => esc_html__( 'Header  three', 'goodsoul' ),
					'4' => esc_html__( 'Header  four', 'goodsoul' ),
					'5' => esc_html__( 'Header  five', 'goodsoul' ),
					'6' => esc_html__( 'Header  six', 'goodsoul' ),
				),
				'default' => '1',
			),
			array(
				'required' => array(
					array( $opt_prefix . 'header_tob_bar_style', '!=', '1' ),
					array( $opt_prefix . 'header_tob_bar_style', '!=', '2' ),
					array( $opt_prefix . 'header_tob_bar_style', '!=', '5' ),
				),
				'id'       => $opt_prefix . 'header_topbar_login',
				'type'     => 'ace_editor',
				'title'    => esc_html__( 'topbar info link and text', 'goodsoul' ),
				'subtitle' => esc_html__( 'copy free icon code, visit: ', 'goodsoul' ) . '<a href="https://fontawesome.com/icons?d=gallery" target="_blank">fontawesome</a>',
			),
			array(
				'required' => array(
					array( $opt_prefix . 'header_tob_bar_style', '!=', '2' ),
					array( $opt_prefix . 'header_tob_bar_style', '!=', '5' ),
				),
				'id'       => $opt_prefix . 'header_topbar_social_onoff',
				'type'     => 'switch',
				'title'    => esc_html__( 'Topbar Social on off switch', 'goodsoul' ),
				'default'  => false,
				'on'       => esc_html__( 'Enable', 'goodsoul' ),
				'off'      => esc_html__( 'Disable', 'goodsoul' ),
			),
			array(
				'required' => array( $opt_prefix . 'header_topbar_social_onoff', '=', '1' ),
				'id'       => $opt_prefix . 'header_topbar_social',
				'type'     => 'ace_editor',
				'title'    => esc_html__( 'topbar social link and text', 'goodsoul' ),
				'subtitle' => esc_html__( 'copy free icon code, visit: ', 'goodsoul' ) . '<a href="https://fontawesome.com/icons?d=gallery" target="_blank">fontawesome</a>',
			),
			array(
				'required' => array(
					array( $opt_prefix . 'header_tob_bar_style', '!=', '5' ),
				),
				'id'       => $opt_prefix . 'header_info_bar',
				'type'     => 'sortable',
				'title'    => esc_html__( 'topbar info', 'goodsoul' ),
				'mode'     => 'text',
				'options'  => array(
					'1' => '123 4561 5523',
					'2' => 'info@goodsoulcharity.com',
				),
			),
			array(
				'required' => array(
					array( $opt_prefix . 'header_tob_bar_style', '!=', '5' ),
					array( $opt_prefix . 'header_tob_bar_style', '!=', '3' ),
					array( $opt_prefix . 'header_tob_bar_style', '!=', '4' ),
				),
				'id'       => $opt_prefix . 'header_title_text_option',
				'type'     => 'text',
				'title'    => esc_html__( 'Topbar Title Text', 'goodsoul' ),
				'mode'     => 'text',
			),
			array(
				'required' => array(
					array( $opt_prefix . 'header_tob_bar_style', '!=', '5' ),
					array( $opt_prefix . 'header_tob_bar_style', '!=', '3' ),
					array( $opt_prefix . 'header_tob_bar_style', '!=', '4' ),
				),
				'id'       => $opt_prefix . 'header_top_button_text',
				'type'     => 'text',
				'title'    => esc_html__( 'Topbar Button Text', 'goodsoul' ),
				'mode'     => 'text',
			),
			array(
				'required' => array(
					array( $opt_prefix . 'header_tob_bar_style', '!=', '5' ),
					array( $opt_prefix . 'header_tob_bar_style', '!=', '3' ),
					array( $opt_prefix . 'header_tob_bar_style', '!=', '4' ),
				),
				'id'       => $opt_prefix . 'header_top_donate_site_link',
				'type'     => 'text',
				'title'    => esc_html__( 'Topbar Donate Site link', 'goodsoul' ),
				'mode'     => 'text',
			),
			array(
				'required' => array(
					array( $opt_prefix . 'header_tob_bar_style', '!=', '5' ),
					array( $opt_prefix . 'header_tob_bar_style', '!=', '4' ),
				),
				'id'       => $opt_prefix . 'header_top_language_setting',
				'type'     => 'ace_editor',
				'title'    => esc_html__( 'Language Select', 'goodsoul' ),
			),
			array(
				'required' => array(
					array( $opt_prefix . 'header_tob_bar_style', '=', '4' ),
				),
				'id'       => $opt_prefix . 'header_top_location_setting',
				'type'     => 'ace_editor',
				'title'    => esc_html__( 'Location Select', 'goodsoul' ),
			),
			array(
				'required' => array(
					array( $opt_prefix . 'header_tob_bar_style', '!=', '3' ),
					array( $opt_prefix . 'header_tob_bar_style', '!=', '4' ),
				),
				'id'       => $opt_prefix . 'header_cart_icon',
				'type'     => 'switch',
				'title'    => esc_html__( 'header cart icon on off switch', 'goodsoul' ),
				'default'  => false,
				'on'       => esc_html__( 'Enable', 'goodsoul' ),
				'off'      => esc_html__( 'Disable', 'goodsoul' ),
			),
			array(
				'id'      => $opt_prefix . 'header_search_button',
				'type'    => 'switch',
				'title'   => esc_html__( 'header search on off switch', 'goodsoul' ),
				'default' => false,
				'on'      => esc_html__( 'Enable', 'goodsoul' ),
				'off'     => esc_html__( 'Disable', 'goodsoul' ),
			),
			array(
				'required' => array(
					array( $opt_prefix . 'header_tob_bar_style', '!=', '5' ),
					array( $opt_prefix . 'header_tob_bar_style', '!=', '3' ),
					array( $opt_prefix . 'header_tob_bar_style', '!=', '4' ),
				),
				'id'       => $opt_prefix . 'header_menu_button',
				'type'     => 'switch',
				'title'    => esc_html__( 'header menu icon switch', 'goodsoul' ),
				'default'  => false,
				'on'       => esc_html__( 'Enable', 'goodsoul' ),
				'off'      => esc_html__( 'Disable', 'goodsoul' ),
			),
			array(
				'required' => array(
					array( $opt_prefix . 'header_tob_bar_style', '!=', '5' ),
				),
				'id'       => $opt_prefix . 'header_donate_button',
				'type'     => 'switch',
				'title'    => esc_html__( 'header donate button on off switch', 'goodsoul' ),
				'default'  => false,
				'on'       => esc_html__( 'Enable', 'goodsoul' ),
				'off'      => esc_html__( 'Disable', 'goodsoul' ),
			),
			array(
				'required' => array(
					array( $opt_prefix . 'header_tob_bar_style', '!=', '5' ),
				),
				'required' => array( $opt_prefix . 'header_donate_button', '=', '1' ),
				'id'       => $opt_prefix . 'header_donate_button_text',
				'type'     => 'text',
				'title'    => esc_html__( 'donate button text', 'goodsoul' ),
				'default'  => esc_html__( 'Donate Now', 'goodsoul' ),
			),
			array(
				'required' => array( $opt_prefix . 'header_donate_button', '=', '1' ),
				'id'       => $opt_prefix . 'header_donate_button_link',
				'type'     => 'text',
				'title'    => esc_html__( 'donate button link', 'goodsoul' ),
			),
			array(
				'id'      => $opt_prefix . 'header_topbar_onoff',
				'type'    => 'switch',
				'title'   => esc_html__( 'header topbar on off switch', 'goodsoul' ),
				'default' => 0,
				'on'      => esc_html__( 'Enable', 'goodsoul' ),
				'off'     => esc_html__( 'Disable', 'goodsoul' ),
			),
		),
	)
);

Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'sticky header Menu', 'goodsoul' ),
		'id'     => 'sticky_header_menu_option',
		'desc'   => esc_html__( 'Change Header Menu option here', 'goodsoul' ),
		'icon'   => 'el el-home',
		'fields' => array(
			array(
				'id'      => $opt_prefix . 'sticky_header_on',
				'type'    => 'switch',
				'title'   => esc_html__( 'sticky header on off switch', 'goodsoul' ),
				'default' => false,
				'on'      => esc_html__( 'Enable', 'goodsoul' ),
				'off'     => esc_html__( 'Disable', 'goodsoul' ),
			),
			array(
				'id'    => $opt_prefix . 'sticky_header_logo',
				'type'  => 'media',
				'url'   => true,
				'title' => esc_html__( 'sticky header logo', 'goodsoul' ),
			),
		),
	)
);
Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'Responsive Menu', 'goodsoul' ),
		'id'     => 'responsive_menu_option',
		'desc'   => esc_html__( 'Change Responsive Menu option here', 'goodsoul' ),
		'icon'   => 'el el-home',
		'fields' => array(
			array(
				'id'    => $opt_prefix . 'sticky_header_logos',
				'type'  => 'media',
				'url'   => true,
				'title' => esc_html__( 'sticky header logo', 'goodsoul' ),
			),
			array(
				'id'      => $opt_prefix . 'responsive_menu_social_onoff',
				'type'    => 'switch',
				'title'   => esc_html__( 'responsive menu Social on off switch', 'goodsoul' ),
				'default' => 0,
				'on'      => esc_html__( 'Enable', 'goodsoul' ),
				'
            off'          => esc_html__( 'Disable', 'goodsoul' ),
			),
			array(
				'required' => array( $opt_prefix . 'responsive_menu_social_onoff', '=', '1' ),
				'id'       => $opt_prefix . 'responsive_menu_social_icon',
				'type'     => 'ace_editor',
				'title'    => esc_html__( 'responsive menu link and icon', 'goodsoul' ),
				'subtitle' => esc_html__( 'copy free icon code, visit: ', 'goodsoul' ) . '<a href="https://fontawesome.com/icons?d=gallery" target="_blank">fontawesome</a>',
			),
		),
	)
);
Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'Blog option', 'goodsoul' ),
		'id'     => 'blog_area',
		'desc'   => esc_html__( 'Change blog option here', 'goodsoul' ),
		'icon'   => 'el el-home',
		'fields' => array(
			array(
				'id'      => $opt_prefix . 'base_theme',
				'type'    => 'switch',
				'title'   => esc_html__( 'Theme base', 'goodsoul' ),
				'default' => 0,
				'on'      => esc_html__( 'Enable', 'goodsoul' ),
				'off'     => esc_html__( 'Disable', 'goodsoul' ),
			),
			array(
				'id'      => $opt_prefix . 'blog_style',
				'type'    => 'select',
				'title'   => esc_html__( 'Blog style', 'goodsoul' ),
				'options' => array(
					'1' => esc_html__( 'Blog style one', 'goodsoul' ),
					'2' => esc_html__( 'Blog style two', 'goodsoul' ),
					'3' => esc_html__( 'Blog style three', 'goodsoul' ),
				),
			),
			array(
				'id'    => $opt_prefix . 'blog_page_header_img',
				'type'  => 'media',
				'url'   => true,
				'title' => esc_html__( 'Blog page header bg', 'goodsoul' ),
			),
			array(
				'id'      => $opt_prefix . 'hide_info_post',
				'type'    => 'switch',
				'title'   => esc_html__( 'Info post', 'goodsoul' ),
				'default' => 0,
				'on'      => esc_html__( 'Enable', 'goodsoul' ),
				'off'     => esc_html__( 'Disable', 'goodsoul' ),
			),
			array(
				'id'      => $opt_prefix . 'hide_date',
				'type'    => 'switch',
				'title'   => esc_html__( 'Date hide', 'goodsoul' ),
				'default' => 0,
				'on'      => esc_html__( 'Enable', 'goodsoul' ),
				'off'     => esc_html__( 'Disable', 'goodsoul' ),
			),
			array(
				'id'      => $opt_prefix . 'blog_page_header',
				'type'    => 'switch',
				'title'   => esc_html__( 'Blog page header on off switch', 'goodsoul' ),
				'default' => 0,
				'on'      => esc_html__( 'Enable', 'goodsoul' ),
				'off'     => esc_html__( 'Disable', 'goodsoul' ),
			),
			array(
				'required' => array( $opt_prefix . 'blog_page_header', '=', '1' ),
				'id'       => $opt_prefix . 'blog_page_header_text',
				'type'     => 'text',
				'title'    => esc_html__( 'Blog page header text', 'goodsoul' ),
				'default'  => esc_html__( 'Blog Page', 'goodsoul' ),
			),
			array(
				'required' => array( $opt_prefix . 'blog_page_header', '=', '1' ),
				'id'       => $opt_prefix . 'blog_page_breadcrumbs',
				'type'     => 'text',
				'title'    => esc_html__( 'Blog page header text', 'goodsoul' ),
				'default'  => esc_html__( 'News Room', 'goodsoul' ),
			),
		),
	)
);
Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'Blog Single option', 'goodsoul' ),
		'id'     => 'blog_signle_area',
		'desc'   => esc_html__( 'Change blog Single option here', 'goodsoul' ),
		'icon'   => 'el el-home',
		'fields' => array(
			array(
				'id'      => $opt_prefix . 'blog_single_next_preview',
				'type'    => 'switch',
				'title'   => esc_html__( 'blog single next pre view', 'goodsoul' ),
				'default' => 0,
				'on'      => esc_html__( 'Enable', 'goodsoul' ),
				'off'     => esc_html__( 'Disable', 'goodsoul' ),
			),
			array(
				'id'      => $opt_prefix . 'blog_signle_share',
				'type'    => 'switch',
				'title'   => esc_html__( 'blog single post share on off switch', 'goodsoul' ),
				'default' => false,
				'on'      => esc_html__( 'Enable', 'goodsoul' ),
				'off'     => esc_html__( 'Disable', 'goodsoul' ),
			),
			array(
				'required' => array( $opt_prefix . 'blog_signle_share', '=', '1' ),
				'id'       => $opt_prefix . 'blog_signle_share_text',
				'type'     => 'text',
				'title'    => esc_html__( 'blog signle share  text', 'goodsoul' ),
				'default'  => esc_html__( ' Share : ', 'goodsoul' ),
			),
			array(
				'id'    => $opt_prefix . 'blog_single_page_header_img',
				'type'  => 'media',
				'url'   => true,
				'title' => esc_html__( 'Blog single page header bg', 'goodsoul' ),
			),
			array(
				'id'      => $opt_prefix . 'blog_single_page_header',
				'type'    => 'switch',
				'title'   => esc_html__( 'Blog single page header on off switch', 'goodsoul' ),
				'default' => 0,
				'on'      => esc_html__( 'Enable', 'goodsoul' ),
				'off'     => esc_html__( 'Disable', 'goodsoul' ),
			),
			array(
				'required' => array( $opt_prefix . 'blog_single_page_header', '=', '1' ),
				'id'       => $opt_prefix . 'blog_single_page_header_text',
				'type'     => 'text',
				'title'    => esc_html__( 'Blog single page header text', 'goodsoul' ),
				'default'  => esc_html__( 'Blog Single Post', 'goodsoul' ),
			),
			array(
				'required' => array( $opt_prefix . 'blog_single_page_header', '=', '1' ),
				'id'       => $opt_prefix . 'blog_single_page_breadcrumbs',
				'type'     => 'text',
				'title'    => esc_html__( 'Blog single page breadcrumbs text', 'goodsoul' ),
				'default'  => esc_html__( 'Our Blog', 'goodsoul' ),
			),
			array(
				'id'      => $opt_prefix . 'blog_single_page_view',
				'type'    => 'switch',
				'title'   => esc_html__( 'Blog single page view', 'goodsoul' ),
				'default' => 0,
				'on'      => esc_html__( 'Enable', 'goodsoul' ),
				'off'     => esc_html__( 'Disable', 'goodsoul' ),
			),
		),
	)
);
Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'Footer option', 'goodsoul' ),
		'id'     => 'goodsoul_footer_area',
		'desc'   => esc_html__( 'Change footer option here', 'goodsoul' ),
		'icon'   => 'el el-home',
		'fields' => array(
			array(
				'id'    => $opt_prefix . 'footer_logo_image',
				'type'  => 'media',
				'title' => __( 'Footer logo Image', 'goodsoul' ),
				'url'   => true,
			),
			array(
				'id'      => $opt_prefix . 'footer_tob_bar_style',
				'type'    => 'select',
				'title'   => esc_html__( 'footer style', 'goodsoul' ),
				'options' => array(
					'style_1' => esc_html__( 'footer  one', 'goodsoul' ),
					'style_2' => esc_html__( 'footer  two', 'goodsoul' ),
					'style_3' => esc_html__( 'footer  three', 'goodsoul' ),
					'style_4' => esc_html__( 'footer  four', 'goodsoul' ),
					'style_5' => esc_html__( 'footer  five', 'goodsoul' ),
				),
				'default' => 'style_1',
			),
			array(
				'id'    => $opt_prefix . 'upper_content',
				'type'  => 'select',
				'title' => esc_html__( 'Upper Content Style', 'goodsoul' ),
				// 'options' => get_elementor_library(),
			),
			array(
				'required' => array( $opt_prefix . 'footer_tob_bar_style', '=', array( 'style_1', 'style_2', 'style_4', 'style_5' ) ),
				'id'       => $opt_prefix . 'footer_copyright',
				'type'     => 'textarea',
				'title'    => esc_html__( 'Copyright text', 'goodsoul' ),
			),
			array(
				'required' => array( $opt_prefix . 'footer_tob_bar_style', '=', array( 'style_1', 'style_3', 'style_5' ) ),
				'id'       => $opt_prefix . 'footer_social_area_one',
				'type'     => 'ace_editor',
				'title'    => esc_html__( 'Add Your Link and Text', 'goodsoul' ),
			),
			array(
				'id'    => $opt_prefix . 'newsletter_shortcode',
				'type'  => 'text',
				'title' => esc_html__( 'Newsletter Shortcode', 'goodsoul' ),
			),
		),
	)
);

Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'Color option', 'goodsoul' ),
		'id'     => 'goodsoul_color_area',
		'desc'   => esc_html__( 'Change footer option here', 'goodsoul' ),
		'icon'   => 'el el-home',
		'fields' => array(
			array(
				'id'          => $opt_prefix . 'main_color',
				'type'        => 'color',
				'title'       => __( 'Primary Color', 'goodsoul' ),
				'subtitle'    => __( 'Pick a color for the theme (default: #fff).', 'goodsoul' ),
				'default'     => '#ed6221',
				'validate'    => 'color',
				'transparent' => false,
			),
		),
	)
);

Redux::setSection(
	$opt_name,
	array(
		'title'            => esc_html__( 'Typography', 'goodsoul' ),
		'id'               => 'fonts_settings',
		'desc'             => esc_html__( 'Typography', 'goodsoul' ),
		'customizer_width' => '400px',
		'icon'             => 'el el-font',
		'fields'           => array(
			array(
				'id'       => 'enable_typography',
				'type'     => 'switch',
				'title'    => esc_html__( 'Typography', 'goodsoul' ),
				'subtitle' => esc_html__( 'Enable or Disable Typography', 'goodsoul' ),
				'default'  => false,
				'off'      => esc_html__( 'Disable', 'goodsoul' ),
				'on'       => esc_html__( 'Enable', 'goodsoul' ),
			),
			array(
				'required'   => array( 'enable_typography', '=', '1' ),
				'id'         => $opt_prefix . '-body_typography',
				'type'       => 'typography',
				'title'      => esc_html__( 'Body Typography', 'goodsoul' ),
				'subtitle'   => esc_html__( 'Select body font family, size, line height, color and weight.', 'goodsoul' ),
				'text-align' => false,
				'subsets'    => false,
				'output'     => array( 'body' ),

			),
			array(
				'required'   => array( 'enable_typography', '=', '1' ),
				'id'         => $opt_prefix . '-heading-1-typography',
				'type'       => 'typography',
				'title'      => esc_html__( 'H1 Font', 'goodsoul' ),
				'subtitle'   => esc_html__( 'Select heading font family and weight.', 'goodsoul' ),
				'google'     => true,
				'text-align' => false,
				'output'     => array( 'h1' ),
			),
			array(
				'required'   => array( 'enable_typography', '=', '1' ),
				'id'         => $opt_prefix . '-heading-2-typography',
				'type'       => 'typography',
				'title'      => esc_html__( 'H2 Font', 'goodsoul' ),
				'subtitle'   => esc_html__( 'Select heading font family and weight.', 'goodsoul' ),
				'google'     => true,
				'text-align' => false,
				'output'     => array( 'h2' ),

			),
			array(
				'required'   => array( 'enable_typography', '=', '1' ),
				'id'         => $opt_prefix . '-heading-3-typography',
				'type'       => 'typography',
				'title'      => esc_html__( 'H3 Font', 'goodsoul' ),
				'subtitle'   => esc_html__( 'Select heading font family and weight.', 'goodsoul' ),
				'google'     => true,
				'text-align' => false,
				'output'     => array( 'h3' ),
			),
			array(
				'required'   => array( 'enable_typography', '=', '1' ),
				'id'         => $opt_prefix . '-heading-4-typography',
				'type'       => 'typography',
				'title'      => esc_html__( 'H4 Font', 'goodsoul' ),
				'subtitle'   => esc_html__( 'Select heading font family and weight.', 'goodsoul' ),
				'google'     => true,
				'text-align' => false,
				'output'     => array( 'h4' ),
			),
			array(
				'required'   => array( 'enable_typography', '=', '1' ),
				'id'         => $opt_prefix . '-heading-5-typography',
				'type'       => 'typography',
				'title'      => esc_html__( 'H5 Font', 'goodsoul' ),
				'subtitle'   => esc_html__( 'Select heading font family and weight.', 'goodsoul' ),
				'google'     => true,
				'text-align' => false,
				'output'     => array( 'h5' ),
			),
			array(
				'required'   => array( 'enable_typography', '=', '1' ),
				'id'         => $opt_prefix . '-heading-6-typography',
				'type'       => 'typography',
				'title'      => esc_html__( 'H6 Font', 'goodsoul' ),
				'subtitle'   => esc_html__( 'Select heading font family and weight.', 'goodsoul' ),
				'google'     => true,
				'text-align' => false,
				'output'     => array( 'h6' ),
			),

		),
	)
);


Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'Campaign Page Text', 'goodsoul' ),
		'id'     => 'goodsoul_campaign_page',
		'desc'   => esc_html__( 'Change Campaign Page Text Here', 'goodsoul' ),
		'icon'   => 'el el-home',
		'fields' => array(
			array(
				'id'      => $opt_prefix . 'cam_raised',
				'type'    => 'text',
				'title'   => __( 'RAISED Text', 'goodsoul' ),
				'default' => '',
			),
			array(
				'id'      => $opt_prefix . 'cam_goal',
				'type'    => 'text',
				'title'   => __( 'GOAL text', 'goodsoul' ),
				'default' => '',
			),
			array(
				'id'      => $opt_prefix . 'cam_description',

				'type'    => 'text',
				'title'   => __( 'Description text', 'goodsoul' ),
				'default' => '',
			),
			array(
				'id'      => $opt_prefix . 'cam_donate_form_wrapper',
				'type'    => 'text',
				'title'   => __( 'Donate Form Title', 'goodsoul' ),
				'default' => '',
			),
			array(
				'id'      => $opt_prefix . 'cam_donate_form_wrapper_sub',
				'type'    => 'text',
				'title'   => __( 'Donate Form Sub Title', 'goodsoul' ),
				'default' => '',
			),
		),
	)
);
