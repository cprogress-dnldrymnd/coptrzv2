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

add_action('wp_enqueue_scripts', 'enqueue_scripts', 99999);

/**
 * Enqueue the block extension script in the editor.
 */
function digitally_disruptive_enqueue_swiper_editor_assets()
{
	wp_enqueue_script(
		'dd-query-swiper-editor',
		get_template_directory_uri() . '/assets/js/query-swiper-editor.js', // Adjust path
		array('wp-blocks', 'wp-element', 'wp-hooks', 'wp-editor', 'wp-components', 'wp-block-editor'),
		filemtime(get_template_directory() . '/assets/js/query-swiper-editor.js'),
		true
	);
	wp_enqueue_script(
		'dd-extend-custom-css',
		get_template_directory_uri() . '/assets/js/extend-custom-css.js', // Adjust path
		array('wp-blocks', 'wp-element', 'wp-hooks', 'wp-editor', 'wp-components', 'wp-block-editor'),
		filemtime(get_template_directory() . '/assets/js/extend-custom-css.js'),
		true
	);
}
add_action('enqueue_block_editor_assets', 'digitally_disruptive_enqueue_swiper_editor_assets');

/**
 * Intercept the rendered HTML of blocks containing our target class and format them for Swiper.js.
 *
 * @param string $block_content The raw HTML content of the block.
 * @param array  $block         The parsed block data array.
 * @return string Modified block HTML.
 */
function digitally_disruptive_render_swiper_query($block_content, $block)
{

	// Bail early if the block doesn't possess the required class
	if (empty($block['attrs']['className']) || strpos($block['attrs']['className'], 'query-loop-swiper-js') === false) {
		return $block_content;
	}

	$attrs = $block['attrs'];

	/**
	 * EXTRACT ATTRIBUTES WITH STRICT DEFAULTS
	 * Matches the default values registered in the JS block schema.
	 */
	$slides_desktop = isset($attrs['swiperSlidesDesktop']) ? (float) $attrs['swiperSlidesDesktop'] : 4;
	$slides_tablet  = isset($attrs['swiperSlidesTablet']) ? (float) $attrs['swiperSlidesTablet'] : 2;
	$slides_mobile  = isset($attrs['swiperSlidesMobile']) ? (float) $attrs['swiperSlidesMobile'] : 1;
	$space_between  = isset($attrs['swiperSpaceBetween']) ? (int) $attrs['swiperSpaceBetween'] : 20;

	$is_loop        = isset($attrs['swiperLoop']) ? (bool) $attrs['swiperLoop'] : true;
	$has_pagination = isset($attrs['swiperPagination']) ? (bool) $attrs['swiperPagination'] : true;
	$has_navigation = isset($attrs['swiperNavigation']) ? (bool) $attrs['swiperNavigation'] : false;
	$has_autoplay   = isset($attrs['swiperAutoplay']) ? (bool) $attrs['swiperAutoplay'] : false;
	$delay          = isset($attrs['swiperDelay']) ? (int) $attrs['swiperDelay'] : 3000;

	// 1. Construct the Swiper Initialization Object
	$swiper_config = array(
		'spaceBetween'  => $space_between,
		'loop'          => $is_loop,
		'breakpoints'   => array(
			320  => array('slidesPerView' => $slides_mobile),
			768  => array('slidesPerView' => $slides_tablet),
			1024 => array('slidesPerView' => $slides_desktop),
		),
	);

	if ($has_autoplay) {
		$swiper_config['autoplay'] = array(
			'delay'                => $delay,
			'disableOnInteraction' => false,
		);
	}
	if ($has_pagination) {
		$swiper_config['pagination'] = array('el' => '.swiper-pagination', 'clickable' => true);
	}
	if ($has_navigation) {
		$swiper_config['navigation'] = array('nextEl' => '.swiper-button-next', 'prevEl' => '.swiper-button-prev');
	}

	$tags = new WP_HTML_Tag_Processor($block_content);

	// 2. Target the .wp-block-query div to become the main .swiper container
	while ($tags->next_tag()) {
		$class = $tags->get_attribute('class');
		if ($class && preg_match('/\bwp-block-query\b/', $class)) {
			$tags->add_class('swiper');
			$tags->set_attribute('data-swiper-config', wp_json_encode($swiper_config));
			break;
		}
	}

	// 3. Add 'swiper-wrapper' class to the internal <ul> container
	$tags = new WP_HTML_Tag_Processor($tags->get_updated_html());
	while ($tags->next_tag(array('tag_name' => 'ul'))) {
		$class = $tags->get_attribute('class');
		if ($class && strpos($class, 'wp-block-post-template') !== false) {
			$tags->add_class('swiper-wrapper');
			break;
		}
	}

	// 4. Process all <li> elements inside the loop and append 'swiper-slide'
	$tags = new WP_HTML_Tag_Processor($tags->get_updated_html());
	while ($tags->next_tag(array('tag_name' => 'li'))) {
		$class = $tags->get_attribute('class');
		if ($class && strpos($class, 'wp-block-post') !== false) {
			$tags->add_class('swiper-slide');
		}
	}

	$html = $tags->get_updated_html();

	// 5. Inject Navigation / Pagination Elements directly after the </ul>
	$controls_html = '';
	if ($has_pagination) {
		$controls_html .= '<div class="swiper-pagination"></div>';
	}
	if ($has_navigation) {
		$controls_html .= '<div class="swiper-button-prev"></div><div class="swiper-button-next"></div>';
	}

	if (! empty($controls_html)) {
		$html = preg_replace('/(<\/ul>)/i', '$1' . $controls_html, $html, 1);
	}

	return $html;
}
add_filter('render_block', 'digitally_disruptive_render_swiper_query', 10, 2);

/**
 * Intercept the block, scope the custom CSS declarations, and inject the style tag.
 *
 * @param string $block_content The raw HTML content of the block.
 * @param array  $block         The parsed block data array.
 * @return string Modified block HTML with inline scoped styles.
 */
function digitally_disruptive_render_custom_css( $block_content, $block ) {
    
    // Bail early if no CSS exists or if it's not a Group block variation
    if ( empty( $block['attrs']['ddCustomCSS'] ) || 'core/group' !== $block['blockName'] ) {
        return $block_content;
    }

    $raw_css = $block['attrs']['ddCustomCSS'];
    
    // Generate a secure, unique ID for this specific block instance
    $unique_id = 'dd-css-' . substr( md5( uniqid( wp_rand(), true ) ), 0, 8 );

    // Strip HTML tags to prevent XSS injection
    $sanitized_css = wp_strip_all_tags( $raw_css );

    /**
     * ARCHITECTURAL CHANGE: The Wrapping Model
     * Automatically wrap the user's raw CSS properties inside the unique class selector.
     */
    $scoped_css = sprintf( '.%s { %s }', $unique_id, $sanitized_css );

    // Inject the unique class into the block's main HTML wrapper
    $tags = new WP_HTML_Tag_Processor( $block_content );
    if ( $tags->next_tag() ) {
        $tags->add_class( $unique_id );
    }
    $updated_content = $tags->get_updated_html();

    // Construct the scoped style block
    $style_tag = sprintf( 
        '<style id="%s">%s</style>', 
        esc_attr( $unique_id . '-style' ), 
        $scoped_css 
    );

    return $style_tag . $updated_content;
}
add_filter( 'render_block', 'digitally_disruptive_render_custom_css', 10, 2 );
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

add_action('wp', function () {

	if (!is_product()) return;

	global $post;

	$related = carbon_get_post_meta($post->ID, 'crb_related_products');

	if (!empty($related)) {
		remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
	}
});

add_action('woocommerce_after_single_product_summary', function () {

	if (!is_product()) return;

	global $post;

	$related = carbon_get_post_meta($post->ID, 'crb_related_products');

	// ❌ If empty → let WooCommerce handle it
	if (empty($related)) return;

	// 🔥 Extract product IDs from Carbon Fields structure
	$related_ids = array_map(function ($item) {
		return $item['id'];
	}, $related);

	$query = new WP_Query([
		'post_type'      => 'product',
		'post__in'       => $related_ids,
		'orderby'        => 'post__in', // 👈 keeps manual order
		'posts_per_page' => count($related_ids),
	]);

	if ($query->have_posts()) {
		echo '<section class="related products md-padding-top md-padding-bottom border-top-default">';
		echo '<div class="container">';
		echo '<h2 class="text-center">Related products</h2>';
		echo '<ul class="products columns-4">';

		while ($query->have_posts()) {
			$query->the_post();

			// ✅ Uses your existing product card layout
			wc_get_template_part('content', 'product');
		}

		echo '</ul></div></section>';
	}

	wp_reset_postdata();
}, 20);

add_action('wp_footer', 'inject_popup_modal');
function inject_popup_modal()
{
	// Only show this on the relevant Brand Archive pages
	if (is_tax('pa_brands', 'skyshyld')) {
		echo do_shortcode('[popup id=419151]');
	} else if (is_tax('pa_brands', 'avy')) {
		echo do_shortcode('[popup id=419411]');
	}
}
