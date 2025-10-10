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
	global $popups_id, $layouts_global, $product_taxonomy_page;
	$popups_id = [];
	$layouts_global = [];
	$product_taxonomy_page = [];
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
		return carbon_get_term_meta($term_id, $value);
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
	wp_enqueue_style('intl-tel', 'https://cdn.jsdelivr.net/npm/intl-tel-input@21.2.7/build/css/intlTelInput.css', NULL, coptz_version);
	wp_enqueue_script('intlTelInput', 'https://cdn.jsdelivr.net/npm/intl-tel-input@21.2.7/build/js/intlTelInput.js', NULL, coptz_version);

	//wp_enqueue_script('swiper', vendor_dir . 'swiper/js/swiper-bundle.min.js');
	wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js');
	wp_enqueue_script('bootstrap', vendor_dir . 'bootstrap/js/bootstrap.min.js');
	//wp_enqueue_script('intlTelInput', vendor_dir.'intlTelInput/js/intlTelInput.min.js');
	wp_register_script('main', assets_dir . 'js/main.js', NULL, coptz_version);
	wp_localize_script(
		'main',
		'ajax_object',
		array(
			'ajax_url' => admin_url('admin-ajax.php'),
		)
	);
	wp_enqueue_script('main');

	if (is_product() || get_post_type() == 'rentals' || get_post_type() == 'landingpages') {
		wp_register_script('single-product', assets_dir . 'js/single-product.js', NULL, coptz_version);
		wp_localize_script(
			'single-product',
			'ajax_object',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
			)
		);
		wp_enqueue_script('single-product');
	}

	if (get_post_type() == 'events') {
		wp_enqueue_script('single-event', assets_dir . 'js/single-event.js', NULL, coptz_version);
	}
	/*
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
		}
		else {
			wp_enqueue_style('style', theme_dir . 'style.css', NULL, coptz_version);
		}
	*/
	if (get_page_template_slug() != 'templates/page-old-landing.php') {
		wp_enqueue_style('style', theme_dir . 'style.css', NULL, coptz_version);
	} else {
		wp_enqueue_style('landing-style', theme_dir . 'landing.css', NULL, coptz_version);
	}
}

add_action('wp_enqueue_scripts', 'enqueue_scripts', 99999); // Register this fxn and allow Wordpress to call it automatcally in the header



/*-----------------------------------------------------------------------------------*/
/* Require Files
/*-----------------------------------------------------------------------------------*/
require_once('includes/_required_files.php');


function canonical()
{
	if (is_single() || is_page()) {
		return get_the_permalink();
	} else if (is_tax() || is_category()) {
		$term_link = get_term_link(get_queried_object()->term_id);
		return $term_link;
	} else if (is_post_type_archive()) {
		$archive_link = get_post_type_archive_link(get_post_type());
		return $archive_link;
	} else if (is_home()) {
		$blog_url = get_permalink(get_option('page_for_posts'));
		return $blog_url;
	} else {
		$term_link = get_term_link(get_queried_object()->term_id);
		return $term_link;
	}
}

/*
function action_validate_email()
{
	if (get_the_ID() == 372958) {
?>
		<script>
			document.addEventListener('wpcf7submit', function(event) {
				if (event.detail.contactFormId === 372961) {
					let emailField = event.detail.inputs.find(input => input.name === 'email'); // Replace 'email' with the actual name of your email field
					if (emailField) {
						let email = emailField.value;
						if (!email.endsWith('@mod.gov.uk')) {
							event.detail.valid = false;
							let emailError = document.createElement('span');
							emailError.className = 'wpcf7-not-valid-tip';
							emailError.style.color = 'red';
							emailError.textContent = 'Please use an @mod.gov.uk email address.';

							let emailInput = document.querySelector('input[name="email"]'); // or the correct selector for your email input
							if (emailInput) {
								let existingError = emailInput.parentNode.querySelector('.wpcf7-not-valid-tip');
								if (existingError) {
									existingError.remove();
								}
								emailInput.parentNode.appendChild(emailError);
							}

						} else {
							let emailInput = document.querySelector('input[name="email"]');
							if (emailInput) {
								let existingError = emailInput.parentNode.querySelector('.wpcf7-not-valid-tip');
								if (existingError) {
									existingError.remove();
								}
							}
						}
					}
				}
			}, false);

			document.addEventListener('DOMContentLoaded', function() {
				const emailInput = document.querySelector('.realtime-email-check');

				if (emailInput) {
					emailInput.addEventListener('input', function() {
						const email = this.value;
						const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Basic email regex

						if (emailRegex.test(email)) {
							// Valid email: You can add visual feedback here (e.g., green border, checkmark)
							this.classList.remove('invalid-email');
							this.classList.add('valid-email');
						} else if (email.length > 0) {
							// Invalid email and not empty: Add visual feedback (e.g., red border, error message)
							this.classList.remove('valid-email');
							this.classList.add('invalid-email');

						} else {
							//Empty field reset feedback
							this.classList.remove('invalid-email');
							this.classList.remove('valid-email');
						}
					});
				}
			});
		</script>
<?php
	}
}

add_action('wp_footer', 'action_validate_email');
*/



add_filter('wpcf7_validate_email*', 'wpcf7_validate_mod_gov_uk', 20, 2);

function wpcf7_validate_mod_gov_uk($result, $tag)
{
	if ($tag->name == 'email_mod') { // Replace 'your-email' with your actual email field name
		$value = isset($_POST[$tag->name]) ? trim($_POST[$tag->name]) : '';
		if (! filter_var($value, FILTER_VALIDATE_EMAIL) || ! preg_match('/@mod\.gov\.uk$/', $value)) {
			$result->invalidate($tag, 'Please use a @mod.gov.uk email address');
		}
	}
	return $result;
}

function action_validate_email()
{
	if (get_the_ID() == 372958) {
?>
		<script>
			jQuery(document).ready(function() {
				jQuery('input[name="email_mod"]').on('input', function() {
					$val = jQuery(this).val();
					jQuery('input[name="email"]').val($val);
				});
			});
		</script>
<?php
	}
}

add_action('wp_footer', 'action_validate_email');

/**
 * WordPress Email Sending API - Static Test Version
 * 
 * Installation: Copy this entire code to your theme's functions.php file
 * Location: wp-content/themes/your-theme/functions.php
 * 
 * API Endpoint: POST https://coptrz.com/wp-json/api/v1/send-email
 * 
 * This will send a static test email when you hit the endpoint
 * No JSON body required - just POST to the URL
 */

// Register REST API endpoint for sending emails
add_action('rest_api_init', function() {
    register_rest_route('api/v1', '/send-email', array(
        'methods' => 'POST',
        'callback' => 'coptrz_send_test_email',
        'permission_callback' => '__return_true',
    ));
});

/**
 * Send static test email
 */


function coptrz_send_test_email($request) {
    // Get all request data
    $all_params = $request->get_params();

    // Check if line_items and properties exist
    $send_mail = false;
    $basic_details = '';
    $properties_html = '';

    if (isset($all_params['line_items'][0]['properties']) && !empty($all_params['line_items'][0]['properties'])) {
        $send_mail = true;

        // Collect basic details from the payload
        $order_number   = $all_params['order_number'] ?? '';
        $customer_name  = ($all_params['billing_address']['first_name'] ?? '') . ' ' . ($all_params['billing_address']['last_name'] ?? '');
        $customer_email = $all_params['email'] ?? '';
        $product_name   = $all_params['line_items'][0]['name'] ?? '';
        $product_price  = $all_params['line_items'][0]['price'] ?? '';
        $order_total    = $all_params['total_price'] ?? '';

        $basic_details = "
        <h3>Order Details</h3>
        <p><strong>Order #:</strong> {$order_number}</p>
        <p><strong>Customer:</strong> {$customer_name}</p>
        <p><strong>Email:</strong> {$customer_email}</p>
        <p><strong>Product:</strong> {$product_name}</p>
        <p><strong>Price:</strong> INR {$product_price}</p>
        ";

        $properties = $all_params['line_items'][0]['properties'];
        if (!empty($properties)) {
            $properties_html .= "<h3>Product Properties</h3>";
            $properties_html .= "<table border='1' cellpadding='6' cellspacing='0' style='border-collapse: collapse; width:100%;'>";
            $properties_html .= "<thead><tr><th align='left'>Name</th><th align='left'>Value</th></tr></thead><tbody>";

            foreach ($properties as $prop) {
                $name = $prop['name'] ?? '';
                $value = $prop['value'] ?? '';

                // If value looks like a URL (e.g. insurance image), make it a clickable link
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    $value = "<a href='{$value}' target='_blank'>{$value}</a>";
                }

                $properties_html .= "<tr><td>{$name}</td><td>{$value}</td></tr>";
            }

            $properties_html .= "</tbody></table>";
        }
    }

    // If no properties, stop function (don’t send)
    if (!$send_mail) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'No properties found in line_items. Email not sent.'
        ), 200);
    }

    // =====================
    // STATIC EMAIL DATA
    // =====================
    $to = 'sam@digitallydisruptive.co.uk';
    $subject = 'New Shopify Order with Properties';
    $from = 'noreply@coptrz.com';
    $from_name = 'Coptrz Test';
    $reply_to = 'support@coptrz.com';

    // Static + dynamic message
    $message = "
    <h1>New Order Notification</h1>
    <p>This order contains custom properties.</p>
    {$basic_details}
    {$properties_html}
    <hr>

    ";

    // Build headers
    $headers = array();
    $headers[] = "From: {$from_name} <{$from}>";
    $headers[] = "Reply-To: {$reply_to}";
    $headers[] = "Content-Type: text/html; charset=UTF-8";

    // Wrap message in HTML template
    $email_body = "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>{$subject}</title>
    </head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f4f4f4;'>
        <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 10px;'>
            {$message}
        </div>
    </body>
    </html>";

    // Send email using WordPress wp_mail
    $mail_sent = wp_mail($to, $subject, $email_body, $headers);

    // Return response
    if ($mail_sent) {
        return new WP_REST_Response(array(
            'success' => true,
            'message' => 'Email sent successfully (properties found).',
            'data' => array(
                'to' => $to,
                'subject' => $subject,
                'order_number' => $order_number,
                'customer' => $customer_name,
                'product' => $product_name,
                'sent_at' => current_time('mysql'),
            )
        ), 200);
    } else {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Failed to send email'
        ), 500);
    }
}


/**
 * Optional: Customize default email from address
 */
add_filter('wp_mail_from', function($original_email_address) {
    return 'noreply@coptrz.com';
});

/**
 * Optional: Customize default email from name
 */
add_filter('wp_mail_from_name', function($original_email_from) {
    return 'Coptrz';
});

/**
 * Optional: Log email errors for debugging
 */
add_action('wp_mail_failed', function($wp_error) {
    error_log('WordPress Email Error: ' . $wp_error->get_error_message());
});

/**
 * Optional: Add CORS headers for API
 */
add_action('rest_api_init', function() {
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
    add_filter('rest_pre_serve_request', function($value) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        return $value;
    });
}, 15);