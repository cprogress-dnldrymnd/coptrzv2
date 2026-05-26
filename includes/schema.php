<?php

/**
 * Primary hook function to inject Course JSON-LD schema into the document head.
 * Evaluates the current query context to ensure it only fires on the correct product types.
 *
 * @return void
 */
function dd_inject_dynamic_schema()
{
    // Restrict to singular views
    if (! is_singular()) {
        return;
    }

    global $post;
    $schema_payloads = array();

    // Evaluate Course Schema for WooCommerce products in the 'training' category OR specific pages
    $is_training_product = is_singular('product') && has_term('training', 'product_cat');
    $is_course_page      = is_page() && get_post_meta($post->ID, '_is_course_page', true); // Custom field trigger for standard pages

    if ($is_training_product || $is_course_page) {
        $schema_payloads[] = dd_generate_course_schema($post);
    }

    // Output the schema payload to the DOM
    if (! empty($schema_payloads)) {
        $final_json = count($schema_payloads) === 1 ? $schema_payloads[0] : $schema_payloads;

        echo "\n";
        echo '<script test="test-type">' . wp_json_encode($final_json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}
add_action('wp_head', 'dd_inject_dynamic_schema', 99);

/**
 * Constructs the Course schema object dynamically for WooCommerce training products.
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 *
 * @param WP_Post $post The current global post object.
 * @return array|false The structured schema array, or false on failure.
 */
function dd_generate_course_schema($post)
{
    // Ensure WooCommerce is active
    if (! function_exists('wc_get_product')) {
        return false;
    }

    $product = wc_get_product($post->ID);
    if (! $product) {
        return false;
    }

    // 1. Core Data Extraction
    $course_description = has_excerpt($post->ID) ? get_the_excerpt($post->ID) : wp_trim_words($post->post_content, 25);
    $provider_name      = get_bloginfo('name');
    $course_url         = get_permalink($post->ID);
    $thumbnail_url      = get_the_post_thumbnail_url($post->ID, 'full');

    // 2. Custom Meta Extraction (Mapped via standard WP Custom Fields or ACF)
    $credential = get_post_meta($post->ID, '_educationalcredentialawarded', true);
    $competency = get_post_meta($post->ID, '_competencyrequired', true);
    $mode       = get_post_meta($post->ID, '_coursemode', true) ?: 'online';

    // Handle the 'teaches' array. In the WP backend, enter items separated by a pipe '|' character.
    $teaches_raw = get_post_meta($post->ID, '_teaches', true);
    $teaches_arr = ! empty($teaches_raw) ? array_map('trim', explode('|', $teaches_raw)) : array();

    // 3. Live WooCommerce Pricing & Inventory Data
    $price    = $product->get_price();
    $currency = get_woocommerce_currency();
    $in_stock = $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock';

    // 4. Construct the Base Schema Array
    $schema = array(
        '@context'                     => 'https://schema.org',
        '@type'                        => 'Course',
        'name'                         => $product->get_name(),
        'description'                  => wp_strip_all_tags($course_description),
        'url'                          => $course_url,
        'provider'                     => array(
            '@type' => 'Organization',
            '@id'   => home_url('/#organization'), // Explicitly uses the root URL, bypassing any /de/ ghost data
            'name'  => $provider_name,
            'url'   => home_url('/'),
        ),
        'inLanguage'                   => 'en-GB', // Hardcoded locale enforcement
        'availableLanguage'            => 'en-GB', // Hardcoded locale enforcement
        'courseMode'                   => $mode,
        'hasCourseInstance'            => array(
            array(
                '@type'      => 'CourseInstance',
                'name'       => $product->get_name() . ' - ' . ucfirst($mode),
                'courseMode' => $mode,
                'offers'     => array(
                    '@type'              => 'Offer',
                    'name'               => $product->get_name(),
                    'price'              => $price,
                    'priceCurrency'      => $currency,
                    'priceSpecification' => array(
                        '@type'                 => 'PriceSpecification',
                        'price'                 => $price,
                        'priceCurrency'         => $currency,
                        'valueAddedTaxIncluded' => true,
                    ),
                    'url'                => $course_url,
                    'availability'       => $in_stock,
                    'seller'             => array(
                        '@type' => 'Organization',
                        'name'  => $provider_name,
                    ),
                ),
            ),
        ),
    );

    // 5. Append Optional Nodes conditionally
    if ($thumbnail_url) {
        $schema['image'] = array('@id' => $course_url . '#primaryimage');
    }

    if (! empty($credential)) {
        $schema['educationalCredentialAwarded'] = $credential;
    }

    if (! empty($competency)) {
        $schema['competencyRequired'] = $competency;
    }

    if (! empty($teaches_arr)) {
        $schema['teaches'] = $teaches_arr;
    }

    return $schema;
}
