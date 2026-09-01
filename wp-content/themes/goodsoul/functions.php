<?php
/**
 * goodsoul functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package goodsoul
 */
defined( 'GOODSOUL_THEME_URI' ) or define( 'GOODSOUL_THEME_URI', get_template_directory_uri() );
define( 'GOODSOUL_THEME_DRI', get_template_directory() );
define( 'GOODSOUL_IMG_URL', GOODSOUL_THEME_URI . '/assets/images/' );
define( 'GOODSOUL_CSS_URL', GOODSOUL_THEME_URI . '/assets/css/' );
define( 'GOODSOUL_JS_URL', GOODSOUL_THEME_URI . '/assets/js/' );
define( 'GOODSOUL_FRAMEWORK_DRI', GOODSOUL_THEME_DRI . '/framework/' );

if ( ! function_exists( 'cer_get_registration_form_markup' ) ) {
	$cer_plugin_file = WP_PLUGIN_DIR . '/custom-event-registration/custom-event-registration.php';
	if ( file_exists( $cer_plugin_file ) ) {
		require_once $cer_plugin_file;
	}
}

require_once GOODSOUL_FRAMEWORK_DRI . 'plugin-list.php';
require_once GOODSOUL_FRAMEWORK_DRI . 'styles/index.php';
require_once GOODSOUL_FRAMEWORK_DRI . 'scripts/index.php';
add_action( 'after_setup_theme', function() {
	require_once GOODSOUL_FRAMEWORK_DRI . 'redux/redux-config.php';
});
require_once GOODSOUL_FRAMEWORK_DRI . 'tgm/class-tgm-plugin-activation.php';
require_once GOODSOUL_FRAMEWORK_DRI . 'tgm/config-tgm.php';
require_once GOODSOUL_FRAMEWORK_DRI . 'meta-box/config-meta-box.php';
require_once GOODSOUL_FRAMEWORK_DRI . 'meta-box/meta-box-conditional-logic/meta-box-conditional-logic.php';
require_once GOODSOUL_THEME_DRI . '/woocommerce-functions.php';
require_once GOODSOUL_THEME_DRI . '/assets/css/custom_style.php';


/**
 * Theme option compatibility.
 */
if ( ! function_exists( 'goodsoul_get_options' ) ) :
	function goodsoul_get_options( $key ) {
		global $goodsoul_options;
		$opt_pref = 'goodsoul_';
		if ( empty( $goodsoul_options ) ) {
			$goodsoul_options = get_option( $opt_pref . 'options' );
		}
		$index = $opt_pref . $key;
		if ( ! isset( $goodsoul_options[ $index ] ) ) {
			return '';
		}
		return $goodsoul_options[ $index ];
	}
endif;

if ( ! function_exists( 'goodsoul_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function goodsoul_setup() {
		/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on love us, use a find and replace
		* to change 'goodsoul' to the name of your theme in all the template files.
		*/
		load_theme_textdomain( 'goodsoul', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
		add_theme_support( 'title-tag' );

		/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
		add_theme_support( 'post-thumbnails' );

		add_image_size( 'goodsoul-blog-grid', 220, 310, true ); // 220 pixels wide by 180 pixels tall, hard crop mode
		add_image_size( 'goodsoul-blog-masonary', 220, 310, true ); // 220 pixels wide by 180 pixels tall, hard crop mode
		add_image_size( 'goodsoul-blog-withsidebar', 6, 310, true ); // 220 pixels wide by 180 pixels tall, hard crop mode
		add_image_size( 'goodsoul-single-thumbnail', 740, 414, true ); // 220 pixels wide by 180 pixels tall, hard crop mode

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary', 'goodsoul' ),
			)
		);
		register_nav_menus(
			array(
				'footer-middle' => __( 'Footer Middle Menu', 'goodsoul' ),
				'footer-right'  => __( 'Footer Right Menu', 'goodsoul' ),
			)
		);

		/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		// Set up the WordPress core custom background feature.
		add_theme_support(
			'custom-background',
			apply_filters(
				'goodsoul_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

		add_theme_support( 'woocommerce' );

		add_image_size( 'event-sidebar-img', 130, 121, true );

	}
endif;
add_action( 'after_setup_theme', 'goodsoul_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function goodsoul_content_width() {
	 // This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'goodsoul_content_width', 640 );
}
add_action( 'after_setup_theme', 'goodsoul_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function goodsoul_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Blog Sidebar', 'goodsoul' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'goodsoul' ),
			'before_widget' => '<div id="%1$s" class="sidebar-widget  %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title sidebar-title">',
			'after_title'   => '</h4>',
			'before_title'  => '<div class="sidebar-title"><h4>',
			'after_title'   => '</h4></div>',
		)
	);
	if ( class_exists( 'woocommerce' ) ) {
		register_sidebar(
			array(
				'name'          => esc_html__( 'Woo Shop Sidebar', 'goodsoul' ),
				'id'            => 'woo_shop_sideber',
				'before_widget' => '<div class="%2$s single-sidebar-box" id="%1$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="shop-sidebar-title"><h3>',
				'after_title'   => '</h3></div>',
			)
		);
	}
}
add_action( 'widgets_init', 'goodsoul_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function goodsoul_scripts() {
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	$css_path = get_template_directory() . '/dist/output.css';
	if ( file_exists( $css_path ) ) {
		wp_enqueue_style(
			'goodsoul-tailwind',
			get_template_directory_uri() . '/dist/output.css',
			array(),
			filemtime( $css_path )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'goodsoul_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';
/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	include get_template_directory() . '/inc/jetpack.php';
}

/**
 * preloader compatibility.
 */
function goodsoul_preloader_fun() {     ?>
			<div class="loader-wrap">
		<div class="preloader"></div>
		<div class="layer layer-one"><span class="overlay"></span></div>
		<div class="layer layer-two"><span class="overlay"></span></div>        
		<div class="layer layer-three"><span class="overlay"></span></div>        
	</div>
	<?php
}
add_action( 'goodsoul_preloader', 'goodsoul_preloader_fun' );
/**
 * back to top compatibility.
 */
function goodsoul_back_to_top_fun() {
	?>
		<div class="scroll-to-top scroll-to-target style-five" data-target="html"><span class="icon flaticon-next"></span></div>
	<?php
}
add_action( 'back_to_top', 'goodsoul_back_to_top_fun' );
/**
 * google font compatibility.
 */
function goodsoul_google_font() {
	$protocol   = is_ssl() ? 'https' : 'http';
	$subsets    = 'latin,cyrillic-ext,latin-ext,cyrillic,greek-ext,greek,vietnamese';
	$variants   = ':300,300i,400,400i,500,500i,700,700i';
	$query_args = array(
		'family' => 'Prata|Rubik' . $variants,
		'family' => 'Prata' . $variants . '%7CRubik' . $variants,
		'subset' => $subsets,
	);
	$font_url   = add_query_arg( $query_args, $protocol . '://fonts.googleapis.com/css' );
	wp_enqueue_style( 'goodsoul-google-fonts', $font_url, array(), null );
}
add_action( 'init', 'goodsoul_google_font' );
/**
 * is_blog compatibility.
 */
function is_blog() {
	if ( ( is_archive() ) || ( is_author() ) || ( is_category() ) || ( is_home() ) || ( is_single() ) || ( is_tag() ) ) {
		return true;
	} else {
		return false;
	}
}
/**
 * excerpt_length compatibility.
 */
function goodsoul_excerpt_length( $length ) {
	return 39;
}
add_filter( 'excerpt_length', 'goodsoul_excerpt_length', 999 );

function goodsoul_add_query_vars_filter( $vars ) {
	$vars[] = 'header_type';
	$vars[] = 'skin_color';
	$vars[] = 'blog_style';
	return $vars;
}
add_filter( 'query_vars', 'goodsoul_add_query_vars_filter' );

// Disable REST API link tag
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );

// Disable oEmbed Discovery Links
remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );

// Disable REST API link in HTTP headers
remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );

function goodsoul_elementor_library() {
	$goodsoul_pageslist = get_posts(
		array(
			'post_type'      => 'elementor_library',
			'posts_per_page' => -1,
		)
	);
	$goodsoul_pagearray = array();
	if ( ! empty( $goodsoul_pageslist ) ) {
		foreach ( $goodsoul_pageslist as $page ) {
			$goodsoul_pagearray[ $page->ID ] = $page->post_title;
		}
	}
	return $goodsoul_pagearray;
}

function goodsoul_custom_css() {
	$blog_single_page_header_img_url = '';
	$blog_single_page_header_img     = goodsoul_get_options( 'blog_single_page_header_img' );
	if ( isset( $blog_single_page_header_img ) && ! empty( $blog_single_page_header_img ) ) :
		$blog_single_page_header_img_url = $blog_single_page_header_img['url'];
	endif;

	$blog_page_header_img_url = '';
	$blog_page_header_img     = goodsoul_get_options( 'blog_page_header_img' );
	if ( isset( $blog_page_header_img ) && ! empty( $blog_page_header_img ) ) :
		$blog_page_header_img_url = $blog_page_header_img['url'];
	endif;

	$events_single_bg = '';

	$events_single_bg_metabox     = get_post_meta( get_queried_object_id(), 'goodsoul_metabox_event_meta_image', true );
	$events_single_bg_metabox_url = wp_get_attachment_image_src( $events_single_bg_metabox, 'full' );

	if ( isset( $events_single_bg_metabox_url ) && ! empty( $events_single_bg_metabox_url ) ) :
		$events_single_bg = $events_single_bg_metabox_url[0];
	endif;

	if ( isset( $events_single_bg ) && ! empty( $events_single_bg ) ) :
		$events_single_bg = $events_single_bg;
	endif;

	$main_color = '#ed6121';

	$color_options = get_post_meta( get_queried_object_id(), 'color_options', true );
	if ( isset( $color_options ) && ! empty( $color_options ) ) :
		$main_color = $color_options;
	else :
		$main_color = goodsoul_get_options( 'main_color' );
	endif;

	$featured_img_url             = get_the_post_thumbnail_url( get_queried_object_id(), 'full' );
	$woo_banner                   = ( GOODSOUL_IMG_URL . 'bg-banner-1.jpg' );
	$goodsoul_custom_inline_style = '';
	$goodsoul_c_custom_css        = '
		:root {
			--main-one-color: ' . esc_attr( $main_color ) . ";
		}
		.chariable-single-bg {
			background-image: url('" . esc_url( $featured_img_url ) . "');
		}
		.events-section-bg {
			background-image: url('" . esc_url( $events_single_bg ) . "');
		}
		.page-bread-cumb {
			background-image: url('" . esc_url( $featured_img_url ) . "');
		}
		.woo-banner{
			background-image: url('" . esc_url( $woo_banner ) . "');
		}
		.brd-event .image-layer {
			background-image: url('" . esc_url( $blog_page_header_img_url ) . "');
		}
		.breadcrumb-blog-bg {
			background-image: url('" . esc_url( $blog_page_header_img_url ) . "');
		}
		.breadcrumb-blog-single-bg {
			background-image: url('" . esc_url( $blog_single_page_header_img_url ) . "');
		}
		";

	if ( function_exists( 'goodsoul_get_custom_styles' ) ) {
		$goodsoul_custom_inline_style = goodsoul_get_custom_styles();
	}
	wp_add_inline_style( 'goodsoul-style', $goodsoul_custom_inline_style );

	wp_add_inline_style( 'goodsoul-style', $goodsoul_c_custom_css );
}
add_action( 'wp_enqueue_scripts', 'goodsoul_custom_css', 20 );


function get_elementor_library() {
	$pageslist = get_posts(
		array(
			'post_type'      => 'elementor_library',
			'posts_per_page' => -1,
		)
	);
	$pagearray = array();
	if ( ! empty( $pageslist ) ) {
		foreach ( $pageslist as $page ) {
			$pagearray[ $page->ID ] = $page->post_title;
		}
	}
	return $pagearray;
}

