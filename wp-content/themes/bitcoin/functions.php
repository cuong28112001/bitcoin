<?php
/**
 * Bitcoin-event functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Bitcoin-event
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.1' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function Bitcoin_event_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on Bitcoin-event, use a find and replace
		* to change 'Bitcoin-event' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'Bitcoin-event', get_template_directory() . '/languages' );

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

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-header' => esc_html__( 'Menu Header', 'Bitcoin-event' ),
			'menu-footer' => esc_html__( 'Menu Footer', 'Bitcoin-event' ),
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
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'Bitcoin_event_custom_background_args',
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
}
add_action( 'after_setup_theme', 'Bitcoin_event_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function Bitcoin_event_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'Bitcoin_event_content_width', 640 );
}
add_action( 'after_setup_theme', 'Bitcoin_event_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function Bitcoin_event_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'Bitcoin-event' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'Bitcoin-event' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'Bitcoin_event_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function Bitcoin_event_scripts() {
	wp_enqueue_style( 'Bitcoin-event-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'Bitcoin-event-style', 'rtl', 'replace' );

	wp_enqueue_style('normalize', get_template_directory_uri() . '/assets/css/normalize.min.css', array(), '1.1', 'all');
	wp_enqueue_style('jquery-ui', get_template_directory_uri() . '/assets/css/jquery-ui.min.css', array(), '1.1', 'all');
	wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', array(), '1.1', 'all');
	wp_enqueue_style('splide', get_template_directory_uri() . '/assets/css/splide.min.css', array(), '1.1', 'all');
	wp_enqueue_style('font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.min.css', array(), '1.1', 'all');
	wp_enqueue_style('slick', get_template_directory_uri() . '/assets/css/slick.css', array(), '1.1', 'all');
	wp_enqueue_style('slick-theme', get_template_directory_uri() . '/assets/css/slick-theme.css', array(), '1.1', 'all');
	wp_enqueue_style('fancy-box', get_template_directory_uri() . '/assets/css/fancybox.css', array(), '1.1', 'all');
	wp_enqueue_style('main', get_template_directory_uri() . '/assets/css/main.css', array(), '1.4', 'all');
	wp_enqueue_script('jquery');
	// wp_enqueue_script( 'Bitcoin-event-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );
	// wp_enqueue_script('fancy-box-js', get_template_directory_uri() . '/assets/js/jquery.fancybox.min.js', array('jquery'), 1.2, true);
	wp_enqueue_script('fancy-box-umd-js', get_template_directory_uri() . '/assets/js/fancybox.umd.js', array('jquery'), 1.2, true);
	wp_enqueue_script('bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.bundle.min.js', array('jquery'), 1.1, true);
	wp_enqueue_script('modernizr', get_template_directory_uri() . '/assets/js/vendor/modernizr-2.8.3-respond-1.4.2.min.js', array('jquery'), 1.1, true);
	wp_enqueue_script('slick', get_template_directory_uri() . '/assets/js/slick.min.js', array('jquery'), 1.1, true);
	wp_enqueue_script('splitType', get_template_directory_uri() . '/assets/js/splitType.js', array('jquery'), 1.1, true);
	wp_enqueue_script('splide', get_template_directory_uri() . '/assets/js/splide.min.js', array('jquery'), 1.1, true);
	wp_enqueue_script('splide-extend', get_template_directory_uri() . '/assets/js/splide-extension-auto-scroll.min.js', array('jquery'), 1.1, true);
	wp_enqueue_script('aos', get_template_directory_uri() . '/assets/js/mapoid.js', array('jquery'), 1.1, true);
	wp_enqueue_script('gsap', get_template_directory_uri() . '/assets/js/gsap.min.js', array('jquery'), 1.1, true);
	wp_enqueue_script('ScrollTrigger', get_template_directory_uri() . '/assets/js/ScrollTrigger.min.js', array('jquery'), 1.1, true);
	wp_enqueue_script('main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), 1.3, true);


}
add_action( 'wp_enqueue_scripts', 'Bitcoin_event_scripts' );
function replace_core_jquery_version()
{
    wp_deregister_script('jquery');
    wp_register_script('jquery', get_template_directory_uri() . "/assets/js/jquery/jquery.min.js", array(), '3.6.0');
}
add_action('wp_enqueue_scripts', 'replace_core_jquery_version');
/**
 * Implement the Custom Header feature.
 */

function add_menu_link_class($atts, $item, $args)
{
    if (property_exists($args, 'link_class')) {
        $atts['class'] = $args->link_class;
    }
    return $atts;
}
add_filter('nav_menu_link_attributes', 'add_menu_link_class', 1, 3);

function add_additional_class_on_li($classes, $item, $args)
{
    if (isset($args->add_li_class)) {
        $classes[] = $args->add_li_class;
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'add_additional_class_on_li', 1, 3);


add_filter( 'intermediate_image_sizes_advanced', 'prefix_remove_default_images' );
// Remove default image sizes here.
function prefix_remove_default_images( $sizes ) {
    unset( $sizes['medium']); // 300px
    unset( $sizes['large']); // 1024px
    unset( $sizes['medium_large']); // 768px
    return $sizes;
}

function cc_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

// Remove <p> and <br/> from Contact Form 7
add_filter('wpcf7_autop_or_not', '__return_false');

//Function add theme option header/footer
if (function_exists('acf_add_options_page')) {

    acf_add_options_page(array(
        'page_title' => 'Bitcoin General Settings',
        'menu_title' => 'Bitcoin Settings',
        'menu_slug' => 'theme-general-settings',
        'capability' => 'edit_posts',
		'icon_url' => 'dashicons-screenoptions',
        'redirect' => false
    ));

}


function coins_post_type()
{

    $label = array(
        'name' => 'Coin', //Tên post type dạng số nhiều
        'singular_name' => 'Coin' //Tên post type dạng số ít
    );


    /*
     * Biến $args là những tham số quan trọng trong Post Type
     */
    $args = array(
        'labels' => $label, //Gọi các label trong biến $label ở trên
        'description' => 'Post type coin', //Mô tả của post type
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            // 'author',
            'thumbnail',
            // 'comments',
            // 'trackbacks',
            'revisions',
            'sticky',
            // 'custom-fields'
        ), //Các tính năng được hỗ trợ trong post type
        'taxonomies' => array( 'coin_cat' ), //Các taxonomy được phép sử dụng để phân loại nội dung
        'hierarchical' => false, //Cho phép phân cấp, nếu là false thì post type này giống như Post, true thì giống như Page
        'public' => true, //Kích hoạt post type
        'show_ui' => true, //Hiển thị khung quản trị như Post/Page
        'show_in_menu' => true, //Hiển thị trên Admin Menu (tay trái)
        'show_in_nav_menus' => true, //Hiển thị trong Appearance -> Menus
        'show_in_admin_bar' => true, //Hiển thị trên thanh Admin bar màu đen.
        'menu_position' => 10, //Thứ tự vị trí hiển thị trong menu (tay trái)
        //'menu_icon' => '', //Đường dẫn tới icon sẽ hiển thị
        'can_export' => true, //Có thể export nội dung bằng Tools -> Export
        'has_archive' => true, //Cho phép lưu trữ (month, date, year)
        'exclude_from_search' => false, //Loại bỏ khỏi kết quả tìm kiếm
        'publicly_queryable' => true, //Hiển thị các tham số trong query, phải đặt true
        'capability_type' => 'post' //
    );

    
    register_post_type('coin', $args); //Tạo post type với slug tên là sanpham và các tham số trong biến $args ở trên


}
/* Kích hoạt hàm tạo custom post type */
add_action('init', 'coins_post_type');

function create_coin_cat_taxonomy() {
    $labels = array(
        'name'              => ('Coin Categories'),
        'singular_name'     => ('Coin Category'),
        'search_items'      => ('Search Coin Categories'),
        'all_items'         => ('All Coin Categories'),
        'parent_item'       => ('Parent Coin Category'),
        'parent_item_colon' => ('Parent Coin Category:'),
        'edit_item'         => ('Edit Coin Category'),
        'update_item'       => ('Update Coin Category'),
        'add_new_item'      => ('Add New Coin Category'),
        'new_item_name'     => ('New Coin Category Name'),
        'menu_name'         => ('Coin Categories'),
    );

    // Arguments for the custom taxonomy
    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'coin-category'),
    );

    // Register the custom taxonomy
    register_taxonomy('coin_cat', array( 'coin'), $args);
}

add_action('init', 'create_coin_cat_taxonomy');


function create_gallery_post_type() {
    $labels = array(
        'name' => 'Gallery',
        'singular_name' => 'Gallery'
    );

    $args = array(
        'labels' => $labels,
        'description' => 'Post type gallery',
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'thumbnail',
            'revisions'
        ),
        'taxonomies' => array('gallery_cat'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 10,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post'
    );

    register_post_type('gallery', $args);
}

add_action('init', 'create_gallery_post_type');

function create_gallery_cat_taxonomy() {
    $labels = array(
        'name'              => 'Gallery Categories',
        'singular_name'     => 'Gallery Category',
        'search_items'      => 'Search Gallery Categories',
        'all_items'         => 'All Gallery Categories',
        'parent_item'       => 'Parent Gallery Category',
        'parent_item_colon' => 'Parent Gallery Category:',
        'edit_item'         => 'Edit Gallery Category',
        'update_item'       => 'Update Gallery Category',
        'add_new_item'      => 'Add New Gallery Category',
        'new_item_name'     => 'New Gallery Category Name',
        'menu_name'         => 'Gallery Categories',
    );

    // Arguments for the custom taxonomy
    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'gallery-category'),
    );

    // Register the custom taxonomy
    register_taxonomy('gallery_cat', array('gallery'), $args);
}

add_action('init', 'create_gallery_cat_taxonomy');


function custom_excerpt_more($more) { 
	global $post; 
	return '... <a href="' . get_permalink($post->ID) . '" class="read-more">...</a>'; 
} 
add_filter('excerpt_more', 'custom_excerpt_more');

function custom_excerpt_length($length) 
{ return 20; 
} 
add_filter('excerpt_length', 'custom_excerpt_length', 999);


function is_valid_password($password) {
    if (strlen($password) < 8) {
        return "Mật khẩu phải có ít nhất 8 ký tự.";
    }

    $lowercase = preg_match('/[a-z]/', $password);
    $uppercase = preg_match('/[A-Z]/', $password);
    $number = preg_match('/[0-9]/', $password);
    $special = preg_match('/[!@#$%^&*]/', $password);

    $strength = $lowercase + $uppercase + $number + $special;
    if ($strength < 3) {
        return "Mật khẩu phải chứa ít nhất 3 trong số các loại ký tự sau: chữ thường, chữ in hoa, số hoặc ký tự đặc biệt.";
    }

    return true;
}

function custom_breadcrumb() {

    $page_for_posts = get_option('page_for_posts');
    if ($page_for_posts) {
        echo '<li><a href="' . get_permalink($page_for_posts) . '">' . get_the_title($page_for_posts) . '</a></li>';
    }

    $categories = get_the_category();
    if ($categories) {
        foreach ($categories as $category) {
            echo '<li><a href="' . get_category_link($category->term_id) . '">' . $category->name . '</a></li>';
            break;
        }
    }

}
function redirect_blog_page_template($template) {

    if (is_home()) {
        $blog_page_id = get_option('page_for_posts');
        if ($blog_page_id) {
            $new_template = locate_template(['page-templates/page-news.php']);
            if ($new_template) {
                return $new_template;
            }
        }
    }
    return $template;
}
add_filter('template_include', 'redirect_blog_page_template');

function custom_breadcrumb_single() {
    echo '<li><a href="' . home_url() . '">Trang chủ</a></li>';
    custom_breadcrumb();
    echo '<li>' . get_the_title() . '</li>';
}

function get_reading_time($post_id) {
    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $reading_speed = 200; 
    $minutes = ceil($word_count / $reading_speed);
    return $minutes . ' phút đọc';
}

function remove_admin_bar_for_non_admins() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'remove_admin_bar_for_non_admins');

function restrict_admin_access() {
    if (is_admin() && !current_user_can('edit_posts')) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('admin_init', 'restrict_admin_access');