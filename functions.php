<?php
/*if (!session_id()) {
	session_start();
}*/
/*-----------------------------------------------------------------------------------*/
/* Define the version so we can easily replace it throughout the theme
/*-----------------------------------------------------------------------------------*/
define('coptz_version', 4.3);
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

	global $theme_settings;

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
	return carbon_get_the_post_meta($value);
}

function get__term_meta($term_id, $value)
{
	return get_term_meta($term_id, '_' . $value, true);
}

function get__post_meta_by_id($id, $value)
{
	return carbon_get_post_meta($id, $value, true);
}
function get__theme_option($value)
{
	return get_option('_' . $value);
}



function get__post_thumbnail_id($post_id)
{
	if (get_post_thumbnail_id($post_id)) {
		return get_post_thumbnail_id($post_id);
	} else {
		return get__theme_option('placeholder_image');
	}
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
		wp_enqueue_style('checkout-style', assets_dir . 'stylesheets/checkout/checkout.css', NULL, coptz_version);
		//wp_enqueue_script('checkout-js', assets_dir . 'javascripts/checkout.js', ['jquery'], coptz_version);
	}
}

add_action('wp_enqueue_scripts', 'enqueue_scripts', 99999); // Register this fxn and allow Wordpress to call it automatcally in the header


function action_wp_footer()
{
	$page_footer_scripts = get__post_meta('page_footer_scripts');
	if ($page_footer_scripts) {
		echo do_shortcode($page_footer_scripts);
	}
}
add_action('wp_footer', 'action_wp_footer');

/*-----------------------------------------------------------------------------------*/
/* Require Files
/*-----------------------------------------------------------------------------------*/
require_once('includes/_required_files.php');

/*-----------------------------------------------------------------------------------*/
/* Admin Settings
/*-----------------------------------------------------------------------------------*/
function action_admin_enqueue_scripts($hook)
{

	wp_enqueue_style('my_custom_script', get_template_directory_uri() . '/admin/css/admin-css.css', array(), '1.1');

	wp_enqueue_style('select_2_css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css', array(), '1.0');

	wp_enqueue_style('karla', 'https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,400;0,700;1,400;1,700&display=swap', array(), '1.0');

	wp_enqueue_script('select_2_js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js', array(), '1.0');

	wp_enqueue_script('admin_js', get_template_directory_uri() . '/admin/js/admin-js.js', array(), '1.3');
}
add_action('admin_enqueue_scripts', 'action_admin_enqueue_scripts');


/*-----------------------------------------------------------------------------------*/
/* Code Miror
/*-----------------------------------------------------------------------------------*/
add_action('admin_enqueue_scripts', 'codemirror_enqueue_scripts');

function codemirror_enqueue_scripts($hook)
{
	$cm_settings = array(
		'ce_css'  => wp_enqueue_code_editor(array('type' => 'text/css', 'codemirror' => array('autoRefresh' => true))),
		'ce_html' => wp_enqueue_code_editor(array('type' => 'text/html', 'codemirror' => array('autoRefresh' => true)))
	);
	wp_localize_script('jquery', 'cm_settings', $cm_settings);

	wp_enqueue_style('wp-codemirror');
}

function get_date_diff($post_id)
{
	$datetime1 = new DateTime(get_the_date('', $post_id));
	$datetime2 = new DateTime(''); // current date
	$interval = $datetime1->diff($datetime2);
	$days_ago = $interval->format('%a');
	if ($days_ago == 0) {
		return 'Today at ' . get_the_time('', $post_id);
	} else if ($days_ago == 1) {
		return $interval->format('%a day ago');
	} else {
		return $interval->format('%a days ago');
	}
}

function get_time_diff($post_id)
{
	$time1 = strtotime('08:00:00');
	$time2 = strtotime('09:30:00');
	$difference = round(abs($time2 - $time1) / 3600, 2);
	return $difference;
}
function custom_excerpt_length($content, $length = 50)
{
	// get the first 80 words from the content and added to the $abstract variable
	preg_match('/^([^.!?\s]*[\.!?\s]+){0,' . $length . '}/', strip_tags($content), $abstract);
	// pregmatch will return an array and the first 80 chars will be in the first element 
	return $abstract[0] . '...';
}
add_filter('excerpt_length', 'custom_excerpt_length', 999);



add_filter('mod_rewrite_rules', 'fix_rewritebase');
function fix_rewritebase($rules)
{
	$home_root = parse_url(home_url());
	if (isset($home_root['path'])) {
		$home_root = trailingslashit($home_root['path']);
	} else {
		$home_root = '/';
	}

	$wpml_root = parse_url(get_option('home'));
	if (isset($wpml_root['path'])) {
		$wpml_root = trailingslashit($wpml_root['path']);
	} else {
		$wpml_root = '/';
	}

	$rules = str_replace("RewriteBase $home_root", "RewriteBase $wpml_root", $rules);
	$rules = str_replace("RewriteRule . $home_root", "RewriteRule . $wpml_root", $rules);

	return $rules;
}

if (version_compare(phpversion(), '7.1', '>=')) {
	ini_set('precision', 17);
	ini_set('serialize_precision', -1);
}


function action_admin_footer()
{
	$pages = get__posts('page');
	$select_page = '<select name="select-page-selector">';
	foreach ($pages as $key => $page) {
		$select_page .= '<option value="' . $key . '"> ' . $page . ' </option>';
	}
	$select_page .= '</select>';

	$posts = get__posts('post');
	$select_post = '<select name="select-page-selector">';
	foreach ($posts as $key => $post) {
		$select_post .= '<option value="' . $key . '"> ' . $post . ' </option>';
	}
	$select_post .= '</select>';

	$solutions = get__solutions('solutions');
	$select_solution = '<select name="select-page-selector">';
	foreach ($solutions as $key => $solution) {
		$select_solution .= '<option value="' . $key . '"> ' . $solution . ' </option>';
	}
	$select_solution .= '</select>';

	$popups = get__solutions('popups');
	$select_popup = '<select name="select-page-selector">';
	foreach ($popups as $key => $popup) {
		$select_popup .= '<option value="' . $key . '"> ' . $popup . ' </option>';
	}
	$select_popup .= '</select>';
?>
	<script>
		jQuery(document).ready(function() {
			console.log('mama mo')

		});


		jQuery(document).on("change", '.trigger-selector select', function(event) {
			$value = jQuery(this).val();
			console.log($value);
			$selector = jQuery(this).parents('.cf-complex__group-body').find('.page-selector');
			if ($value == 'page') {
				$selector.html('<?= $select_page ?>');
			} else if ($value == 'post') {
				$selector.html('<?= $select_post ?>');
			} else if ($value == 'solutions') {
				$selector.html('<?= $select_solution ?>');
			} else if ($value == 'popups') {
				$selector.html('<?= $select_popup ?>');
			}
		});

		jQuery(document).on(".select-page-selector", '.cf-complex__tabs-item ', function(event) {
			console.log('xxxxx');


		});
	</script>
<?php
}

add_action('admin_footer', 'action_admin_footer');

function get__posts($post_type)
{
	$pages_array = array(); // Initialize an empty array

	$args = array(
		'post_type'      => $post_type, // Get only pages
		'posts_per_page' => -1, // Get all pages
		'post_status'    => 'publish', // Get only published pages
		'fields'         => 'ids', // Only retrieve post IDs for efficiency
	);

	$posts = get_posts($args);

	if ($posts) {
		foreach ($posts as $post) {
			$pages_array[$post] = get_the_title($post); // Add ID => title to the array
		}
	}

	return $pages_array;
}
