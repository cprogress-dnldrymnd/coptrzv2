<?php
/*-----------------------------------------------------------------------------------*/
/* Define the version so we can easily replace it throughout the theme
/*-----------------------------------------------------------------------------------*/
define('coptz_version', 4.4);
define('theme_dir', get_template_directory_uri() . '/');
define('assets_dir', theme_dir . 'assets/');
define('image_dir', assets_dir . 'images/');
define('vendor_dir', assets_dir . 'vendor/');
/*-----------------------------------------------------------------------------------*/
/* After Theme Setup
/*-----------------------------------------------------------------------------------*/

function action_after_setup_theme()
{
	add_theme_support('post-thumbnails');
	add_theme_support('woocommerce');
	//add_theme_support('wc-product-gallery-zoom');
	//add_theme_support('wc-product-gallery-lightbox');
	//add_theme_support('wc-product-gallery-slider');

	require_once('vendor/autoload.php');
	\Carbon_Fields\Carbon_Fields::boot();


	global $popups_id, $layouts_global, $product_taxonomy_page;

	$popups_id[] = 268179;
}
add_action('after_setup_theme', 'action_after_setup_theme');

/*-----------------------------------------------------------------------------------*/
/* Register Carbofields
/*-----------------------------------------------------------------------------------*/
add_action('carbon_fields_register_fields', 'tissue_paper_register_custom_fields');
function tissue_paper_register_custom_fields()
{
	require_once('includes/post-meta.php');
}
function get__post_meta($value)
{
	if (function_exists('carbon_get_the_post_meta')) {
		return carbon_get_the_post_meta($value);
	}
}

function get__term_meta($term_id, $value)
{
	if (function_exists('get_term_meta')) {
		return get_term_meta($term_id, '_' . $value, true);
	}
}

function get___term_meta($term_id, $value)
{
	if (function_exists('carbon_get_term_meta')) {
		return carbon_get_term_meta($term_id,  $value);
	}
}

function get__post_meta_by_id($id, $value)
{
	if (function_exists('carbon_get_post_meta')) {
		return carbon_get_post_meta($id, $value);
	}
}
function get__theme_option($value)
{
	return carbon_get_theme_option($value);
}

/*-----------------------------------------------------------------------------------*/
/* Enqueue Styles and Scripts
/*-----------------------------------------------------------------------------------*/
function enqueue_scripts()
{
	//wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
	//wp_enqueue_style('intl-tel', 'https://cdn.jsdelivr.net/npm/intl-tel-input@21.2.7/build/css/intlTelInput.css', NULL, coptz_version);

	wp_enqueue_script('swiper', vendor_dir . 'swiper/js/swiper-bundle.min.js');
	wp_enqueue_script('bootstrap', vendor_dir . 'bootstrap/js/bootstrap.min.js');
	wp_enqueue_script('intlTelInput', vendor_dir.'intlTelInput/js/intlTelInput.min.js');
	wp_register_script('main', assets_dir . 'js/main.js', NULL, coptz_version);
	wp_localize_script(
		'main',
		'ajax_object',
		array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'checkout_url' => wc_get_checkout_url(),
		)
	);
	wp_enqueue_script('main');

	if (is_product()) {
		wp_register_script('single-product', assets_dir . 'js/single-product.js', NULL, coptz_version);
		wp_localize_script(
			'single-product',
			'ajax_object',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'checkout_url' => wc_get_checkout_url(),
			)
		);
		wp_enqueue_script('single-product');
	}

	if (get_post_type() == 'events') {
		wp_enqueue_script('single-event', assets_dir . 'js/single-event.js', NULL, coptz_version);
	}

	if (is_checkout()) {
		wp_enqueue_style('checkout-style', assets_dir . 'scss/checkout/checkout.css', NULL, coptz_version);
		wp_register_script('checkout-js', assets_dir . 'js/checkout.js', ['jquery'], coptz_version);

		$countries_obj = new WC_Countries();

		// Get the array of allowed countries (key = country code, value = country name)
		$allowed_countries = $countries_obj->get_allowed_countries();
		$countries = [];
		foreach ($allowed_countries as $key => $country) {
			$countries[] = $key;
		}

		wp_localize_script('checkout-js', 'countries', $countries);
		wp_enqueue_script('checkout-js');
	} else {
		wp_enqueue_style('style', theme_dir . 'style.css', NULL, coptz_version);
	}
}

add_action('wp_enqueue_scripts', 'enqueue_scripts', 99999); // Register this fxn and allow Wordpress to call it automatcally in the header



/*-----------------------------------------------------------------------------------*/
/* Require Files
/*-----------------------------------------------------------------------------------*/
require_once('includes/_required_files.php');


function canonical() {
	if(is_single() || is_page()) {
		return get_the_permalink();
	} else if(is_tax()) {
		$term_link = get_term_link(get_queried_object()->term_id);
		return $term_link;
	} else if(is_post_type_archive()) {
		$archive_link = get_post_type_archive_link(get_post_type());
		return $archive_link;
	} else if(is_home()) {
		$blog_url = get_permalink( get_option( 'page_for_posts' ) ); 
		return $blog_url;
	}
}