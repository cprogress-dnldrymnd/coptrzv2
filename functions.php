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
	require_once( 'vendor/autoload.php' );
    \Carbon_Fields\Carbon_Fields::boot();
	
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
}

add_action('wp_enqueue_scripts', 'enqueue_scripts', 99999); // Register this fxn and allow Wordpress to call it automatcally in the header



/*-----------------------------------------------------------------------------------*/
/* Require Files
/*-----------------------------------------------------------------------------------*/
require_once('includes/_required_files.php');

function action_admin_head()
{
?>
	<style>
		.columns>.cf-field__body>.cf-complex__groups {
			display: flex
		}

		.columns>.cf-field__body>.cf-complex__groups> div {
			flex: 1;
			padding: 5px;
		}
	</style>
<?php
}

add_action('admin_head', 'action_admin_head');
