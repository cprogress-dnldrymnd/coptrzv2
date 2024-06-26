<?php
/*-----------------------------------------------------------------------------------*/
/* Define the version so we can easily replace it throughout the theme
/*-----------------------------------------------------------------------------------*/
define('coptz_version', 4.4);
define('theme_dir', get_template_directory_uri() . '/');
define('assets_dir', theme_dir . 'assets/');
define('image_dir', assets_dir . 'images/');
define('vendor_dir', assets_dir . 'coptrz_vendors/');
/*-----------------------------------------------------------------------------------*/
/* After Theme Setup
/*-----------------------------------------------------------------------------------*/
function action_after_setup_theme()
{
	add_theme_support('post-thumbnails');
	add_theme_support('woocommerce');

	global $theme_settings, $popups_id;

	$popups_id[] = 268179;

	$theme_settings = array(
		array(
			'id'    => 'general_settings',
			'label' => 'General Settings'
		),
		array(
			'id'    => 'brand_details',
			'label' => 'Brand Details'
		),

	);
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

function get__post_meta_by_id($id, $value)
{
	if (function_exists('carbon_get_post_meta')) {
		return carbon_get_post_meta($id, $value);
	}
}
function get__theme_option($value)
{
	return get_option('_' . $value);
}

/*-----------------------------------------------------------------------------------*/
/* Enqueue Styles and Scripts
/*-----------------------------------------------------------------------------------*/
function enqueue_scripts()
{
	wp_enqueue_style('coptz-style', theme_dir . 'style.css', NULL, coptz_version);

	wp_enqueue_script('coptz-bootstrap-js', vendor_dir . 'bootstrap/bootstrap.min.js');

	wp_register_script('coptz-swiper', vendor_dir . 'swiper/swiper-bundle.min.js');

	wp_register_script('coptz-data-fancybox', vendor_dir . 'fancybox/jquery.fancybox.min.js');

	$data = array();
	if (is_product_taxonomy()) {
		$term = get_queried_object();
		$data['page']['type'] = 'taxonomy';
		$data['page']['page_url'] = get_term_link($term->term_id, $term->taxonomy);
	} else {
		$data['page']['type'] = 'page';
		$data['page']['page_url'] = get_permalink();
	}
	if (!is_product() && !is_checkout()) {
		wp_register_script('coptz', assets_dir . 'javascripts/main.js', ['jquery', 'coptz-swiper'], coptz_version);
		wp_localize_script('coptz', 'data', $data);
		wp_enqueue_script('coptz');
	}
	if (is_product()) {
		$product = wc_get_product(get_the_ID());
		if ($product->get_type() == 'variable') {
			$gtin = array();
			$available_variations = $product->get_available_variations();
			$data['price'] = array();
			foreach ($available_variations as $key => $value) {
				$product_var = wc_get_product($value['variation_id']);
				$price = $product_var->get_price_html();
				$data['price']['p_' . $value['variation_id']] = $price;
			}
		}

		wp_register_script('single-product', assets_dir . 'javascripts/single-product.js', ['jquery', 'coptz-data-fancybox', 'coptz-swiper'], coptz_version);
		wp_localize_script('single-product', 'data', $data);
		wp_enqueue_script('single-product');
	}

	if (is_checkout()) {
		wp_enqueue_style('intl-tel', 'https://cdn.jsdelivr.net/npm/intl-tel-input@21.2.7/build/css/intlTelInput.css', NULL, coptz_version);
		wp_enqueue_style('checkout-style', assets_dir . 'stylesheets/checkout/checkout.css', NULL, coptz_version);
		wp_enqueue_script('intl-tel', 'https://cdn.jsdelivr.net/npm/intl-tel-input@21.2.7/build/js/intlTelInput.min.js', NULL, coptz_version);
		wp_register_script('checkout-js', assets_dir . 'javascripts/checkout.js', ['jquery'], coptz_version);

		$countries_obj = new WC_Countries();

		// Get the array of allowed countries (key = country code, value = country name)
		$allowed_countries = $countries_obj->get_allowed_countries();
		$countries = [];
		foreach ($allowed_countries as $key => $country) {
			$countries[] = $key;
		}
		wp_localize_script('checkout-js', 'countries', $countries);
		wp_enqueue_script('checkout-js');
	}
}

add_action('wp_enqueue_scripts', 'enqueue_scripts', 99999); // Register this fxn and allow Wordpress to call it automatcally in the header




/*-----------------------------------------------------------------------------------*/
/* Require Files
/*-----------------------------------------------------------------------------------*/
require_once('includes/_required_files.php');