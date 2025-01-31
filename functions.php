<?php
/**
 * Omega Travel Agents functions and definitions
 * @package Omega Travel Agents
 */

if ( ! function_exists( 'omega_travel_agents_after_theme_support' ) ) :

	function omega_travel_agents_after_theme_support() {
		
		add_theme_support( 'automatic-feed-links' );

		add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
        add_theme_support('woocommerce', array(
            'gallery_thumbnail_image_width' => 300,
        ));

        load_theme_textdomain( 'omega-travel-agents', get_template_directory() . '/languages' );

		add_theme_support(
			'custom-background',
			array(
				'default-color' => 'ffffff',
			)
		);

		$GLOBALS['content_width'] = apply_filters( 'omega_travel_agents_content_width', 1140 );
		
		add_theme_support( 'post-thumbnails' );

		add_theme_support(
			'custom-logo',
			array(
				'height'      => 270,
				'width'       => 90,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);
		
		add_theme_support( 'title-tag' );

		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'script',
				'style',
			)
		);

		add_theme_support( 'post-formats', array(
		    'video',
		    'audio',
		    'gallery',
		    'quote',
		    'image'
		) );
		
		add_theme_support( 'align-wide' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'wp-block-styles' );

	}

endif;

add_action( 'after_setup_theme', 'omega_travel_agents_after_theme_support' );

/**
 * Register and Enqueue Styles.
 */
function omega_travel_agents_register_styles() {

	wp_enqueue_style( 'dashicons' );

    $omega_travel_agents_theme_version = wp_get_theme()->get( 'Version' );
	$omega_travel_agents_fonts_url = omega_travel_agents_fonts_url();
    if( $omega_travel_agents_fonts_url ){
    	require_once get_theme_file_path( 'lib/custom/css/wptt-webfont-loader.php' );
        wp_enqueue_style(
			'omega-travel-agents-google-fonts',
			wptt_get_webfont_url( $omega_travel_agents_fonts_url ),
			array(),
			$omega_travel_agents_theme_version
		);
    }

    wp_enqueue_style( 'swiper', get_template_directory_uri() . '/lib/swiper/css/swiper-bundle.min.css');
    wp_enqueue_style( 'owl.carousel', get_template_directory_uri() . '/lib/custom/css/owl.carousel.min.css');
	wp_enqueue_style( 'omega-travel-agents-style', get_stylesheet_uri(), array(), $omega_travel_agents_theme_version );

	wp_enqueue_style( 'omega-travel-agents-style', get_stylesheet_uri() );
	require get_parent_theme_file_path( '/custom_css.php' );
	wp_add_inline_style( 'omega-travel-agents-style',$omega_travel_agents_custom_css );

	$omega_travel_agents_css = '';

	if ( get_header_image() ) :

		$omega_travel_agents_css .=  '
			.main-header{
				background-image: url('.esc_url(get_header_image()).');
				-webkit-background-size: cover !important;
				-moz-background-size: cover !important;
				-o-background-size: cover !important;
				background-size: cover !important;
			}';

	endif;

	wp_add_inline_style( 'omega-travel-agents-style', $omega_travel_agents_css );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}	

	wp_enqueue_script( 'imagesloaded' );
    wp_enqueue_script( 'masonry' );
	wp_enqueue_script( 'swiper', get_template_directory_uri() . '/lib/swiper/js/swiper-bundle.min.js', array('jquery'), '', 1);
	wp_enqueue_script( 'omega-travel-agents-custom', get_template_directory_uri() . '/lib/custom/js/theme-custom-script.js', array('jquery'), '', 1);
	wp_enqueue_script( 'owl.carousel', get_template_directory_uri() . '/lib/custom/js/owl.carousel.js', array('jquery'), '', 1);

    // Global Query
    if( is_front_page() ){

    	$omega_travel_agents_posts_per_page = absint( get_option('posts_per_page') );
        $omega_travel_agents_c_paged = ( get_query_var( 'page' ) ) ? absint( get_query_var( 'page' ) ) : 1;
        $omega_travel_agents_posts_args = array(
            'posts_per_page'        => $omega_travel_agents_posts_per_page,
            'paged'                 => $omega_travel_agents_c_paged,
        );
        $omega_travel_agents_posts_qry = new WP_Query( $omega_travel_agents_posts_args );
        $omega_travel_agents_max = $omega_travel_agents_posts_qry->max_num_pages;

    }else{
        global $wp_query;
        $omega_travel_agents_max = $wp_query->max_num_pages;
        $omega_travel_agents_c_paged = ( get_query_var( 'paged' ) > 1 ) ? get_query_var( 'paged' ) : 1;
    }

    $omega_travel_agents_default = omega_travel_agents_get_default_theme_options();
    $omega_travel_agents_pagination_layout = get_theme_mod( 'omega_travel_agents_pagination_layout',$omega_travel_agents_default['omega_travel_agents_pagination_layout'] );
}

add_action( 'wp_enqueue_scripts', 'omega_travel_agents_register_styles',200 );

function omega_travel_agents_admin_enqueue_scripts_callback() {
    if ( ! did_action( 'wp_enqueue_media' ) ) {
    wp_enqueue_media();
    }
    wp_enqueue_script('omega-travel-agents-uploaderjs', get_stylesheet_directory_uri() . '/lib/custom/js/uploader.js', array(), "1.0", true);
}
add_action( 'admin_enqueue_scripts', 'omega_travel_agents_admin_enqueue_scripts_callback' );

/**
 * Register navigation menus uses wp_nav_menu in five places.
 */
function omega_travel_agents_menus() {

	$omega_travel_agents_locations = array(
		'omega-travel-agents-primary-menu'  => esc_html__( 'Primary Menu', 'omega-travel-agents' ),
	);

	register_nav_menus( $omega_travel_agents_locations );
}

add_action( 'init', 'omega_travel_agents_menus' );

add_filter('loop_shop_columns', 'omega_travel_agents_loop_columns');
if (!function_exists('omega_travel_agents_loop_columns')) {
	function omega_travel_agents_loop_columns() {
		$omega_travel_agents_columns = get_theme_mod( 'omega_travel_agents_per_columns', 3 );
		return $omega_travel_agents_columns;
	}
}

add_filter( 'loop_shop_per_page', 'omega_travel_agents_per_page', 20 );
function omega_travel_agents_per_page( $omega_travel_agents_cols ) {
  	$omega_travel_agents_cols = get_theme_mod( 'omega_travel_agents_product_per_page', 9 );
	return $omega_travel_agents_cols;
}

require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/classes/class-svg-icons.php';
require get_template_directory() . '/classes/class-walker-menu.php';
require get_template_directory() . '/inc/customizer/customizer.php';
require get_template_directory() . '/inc/custom-functions.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/classes/body-classes.php';
require get_template_directory() . '/inc/widgets/widgets.php';
require get_template_directory() . '/inc/metabox.php';
require get_template_directory() . '/inc/pagination.php';
require get_template_directory() . '/lib/breadcrumbs/breadcrumbs.php';
require get_template_directory() . '/lib/custom/css/dynamic-style.php';
require get_template_directory() . '/inc/TGM/tgm.php';

/**
 * For Admin Page
 */
if (is_admin()) {
    require get_template_directory() . '/inc/get-started/get-started.php';
}

if (! defined( 'OMEGA_TRAVEL_AGENTS_DOCS_PRO' ) ){
define('OMEGA_TRAVEL_AGENTS_DOCS_PRO',__('https://layout.omegathemes.com/steps/pro-omega-travel-agents/','omega-travel-agents'));
}
if (! defined( 'OMEGA_TRAVEL_AGENTS_BUY_NOW' ) ){
define('OMEGA_TRAVEL_AGENTS_BUY_NOW',__('https://www.omegathemes.com/products/travel-agents-wordpress-theme','omega-travel-agents'));
}
if (! defined( 'OMEGA_TRAVEL_AGENTS_SUPPORT_FREE' ) ){
define('OMEGA_TRAVEL_AGENTS_SUPPORT_FREE',__('https://wordpress.org/support/theme/omega-travel-agents/','omega-travel-agents'));
}
if (! defined( 'OMEGA_TRAVEL_AGENTS_REVIEW_FREE' ) ){
define('OMEGA_TRAVEL_AGENTS_REVIEW_FREE',__('https://wordpress.org/support/theme/omega-travel-agents/reviews/#new-post/','omega-travel-agents'));
}
if (! defined( 'OMEGA_TRAVEL_AGENTS_DEMO_PRO' ) ){
define('OMEGA_TRAVEL_AGENTS_DEMO_PRO',__('https://layout.omegathemes.com/omega-travel-agents/','omega-travel-agents'));
}
if (! defined( 'OMEGA_TRAVEL_AGENTS_LITE_DOCS_PRO' ) ){
define('OMEGA_TRAVEL_AGENTS_LITE_DOCS_PRO',__('https://layout.omegathemes.com/steps/free-omega-travel-agents/','omega-travel-agents'));
}

function omega_travel_agents_remove_customize_register() {
    global $wp_customize;

    $wp_customize->remove_setting( 'display_header_text' );
    $wp_customize->remove_control( 'display_header_text' );

}

add_action( 'customize_register', 'omega_travel_agents_remove_customize_register', 11 );

// Apply styles based on customizer settings

function omega_travel_agents_customizer_css() {
    ?>
    <style type="text/css">
        <?php
        $omega_travel_agents_footer_widget_background_color = get_theme_mod('omega_travel_agents_footer_widget_background_color');
        if ($omega_travel_agents_footer_widget_background_color) {
            echo '.footer-widgetarea { background-color: ' . esc_attr($omega_travel_agents_footer_widget_background_color) . '; }';
        }

        $omega_travel_agents_footer_widget_background_image = get_theme_mod('omega_travel_agents_footer_widget_background_image');
        if ($omega_travel_agents_footer_widget_background_image) {
            echo '.footer-widgetarea { background-image: url(' . esc_url($omega_travel_agents_footer_widget_background_image) . '); }';
        }
        $omega_travel_agents_copyright_font_size = get_theme_mod('omega_travel_agents_copyright_font_size');
        if ($omega_travel_agents_copyright_font_size) {
            echo '.footer-copyright { font-size: ' . esc_attr($omega_travel_agents_copyright_font_size) . 'px;}';
        }
        ?>
    </style>
    <?php
}
add_action('wp_head', 'omega_travel_agents_customizer_css');

function omega_travel_agents_radio_sanitize(  $omega_travel_agents_input, $omega_travel_agents_setting  ) {
	$omega_travel_agents_input = sanitize_key( $omega_travel_agents_input );
	$omega_travel_agents_choices = $omega_travel_agents_setting->manager->get_control( $omega_travel_agents_setting->id )->choices;
	return ( array_key_exists( $omega_travel_agents_input, $omega_travel_agents_choices ) ? $omega_travel_agents_input : $omega_travel_agents_setting->default );
}
require get_template_directory() . '/inc/general.php';