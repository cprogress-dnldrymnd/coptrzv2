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
        echo '<script type="application/ld+json">' . wp_json_encode($final_json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}
add_action('wp_head', 'dd_inject_dynamic_schema', 99);

/**
 * Constructs the Course schema object dynamically for both standard Pages and WooCommerce Products.
 * Conditionally appends WooCommerce 'Offer' and 'AggregateRating' nodes only if the post is a product.
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 *
 * @param WP_Post $post The current global post object.
 * @return array|false The structured schema array, or false on failure.
 */
function dd_generate_course_schema($post)
{
    // 1. Determine if this post is a WooCommerce product
    $is_product = false;
    $product    = null;

    if ('product' === $post->post_type && function_exists('wc_get_product')) {
        $product    = wc_get_product($post->ID);
        $is_product = $product ? true : false;
    }

    // 2. Core Data Extraction (Valid for both Pages and Products)
    $course_name        = get_the_title($post->ID);
    $course_description = has_excerpt($post->ID) ? get_the_excerpt($post->ID) : wp_trim_words($post->post_content, 25);
    $provider_name      = get_bloginfo('name');
    $course_url         = get_permalink($post->ID);
    $shopify_product_link = get_post_meta($post->ID, '_shopify_product_link', true);
    if ($shopify_product_link) {
        $course_url = $shopify_product_link; // Override with Shopify link if provided
    }
    $thumbnail_url      = get_the_post_thumbnail_url($post->ID, 'full');

    // 3. Custom Meta Extraction
    $credential  = get_post_meta($post->ID, '_course_credential', true);
    $competency  = get_post_meta($post->ID, '_course_competency', true);
    $mode        = get_post_meta($post->ID, '_course_mode', true) ?: 'online';

    $teaches_raw = get_post_meta($post->ID, '_course_teaches', true);
    $teaches_arr = ! empty($teaches_raw) ? array_map('trim', explode('|', $teaches_raw)) : array();

    // 4. Construct the Base Schema Array
    $schema = array(
        '@context'          => 'https://schema.org',
        '@type'             => 'Course',
        'name'              => $course_name,
        'description'       => wp_strip_all_tags($course_description),
        'url'               => $course_url,
        'provider'          => array(
            '@type' => 'Organization',
            '@id'   => home_url('/#organization'),
            'name'  => $provider_name,
            'url'   => home_url('/'),
        ),
        'inLanguage'        => 'en-GB',
        'availableLanguage' => 'en-GB',
        'courseMode'        => $mode,
    );

    // Append Optional Nodes conditionally
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

    // 5. Build Base Course Instance
    $course_instance = array(
        '@type'      => 'CourseInstance',
        'name'       => $course_name . ' - ' . ucfirst($mode),
        'courseMode' => $mode,
    );

    // 6. Append WooCommerce Specific Data (Offers & Ratings) ONLY if it is a product
    if ($is_product) {
        $price    = $product->get_price();
        $currency = get_woocommerce_currency();
        $in_stock = $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock';

        // Append the Commercial Offer node to the Course Instance
        $course_instance['offers'] = array(
            '@type'              => 'Offer',
            'name'               => $course_name,
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
        );

        // Append Real WooCommerce Ratings dynamically to the main schema root
        $rating_count = $product->get_review_count();
        if ($rating_count > 0) {
            $schema['aggregateRating'] = array(
                '@type'       => 'AggregateRating',
                'ratingValue' => $product->get_average_rating(),
                'reviewCount' => $rating_count,
                'bestRating'  => '5',
                'worstRating' => '1',
            );
        }
    }

    // 7. Attach Course Instance to main schema and return
    $schema['hasCourseInstance'] = array($course_instance);

    return $schema;
}
/**
 * 1. Register the Meta Box conditionally based on taxonomy or post type.
 */
add_action('add_meta_boxes', 'dd_register_course_schema_meta_box', 10, 2);
function dd_register_course_schema_meta_box($post_type, $post)
{
    $display_box = false;

    // Condition A: It is a WooCommerce product inside the 'training' category
    if ('product' === $post_type && taxonomy_exists('product_cat') && has_term('training', 'product_cat', $post)) {
        $display_box = true;
    }

    // Condition B: Load on ALL standard pages so the client has the UI toggle available
    if ('page' === $post_type) {
        $display_box = true;
    }

    if ($display_box) {
        add_meta_box(
            'dd_course_schema_data',
            __('Course Schema Data (Google Rich Results)', 'dd-schema-injector'),
            'dd_render_course_schema_meta_box',
            $post_type,
            'normal',
            'high'
        );
    }
}

/**
 * 2. Render the HTML interface for the meta box in the backend.
 *
 * @param WP_Post $post The current global post object.
 */
function dd_render_course_schema_meta_box($post)
{
    // Generate a secure nonce for data validation upon saving
    wp_nonce_field('dd_save_course_schema', 'dd_course_schema_nonce');

    // Retrieve existing values from the database
    $is_course_page = get_post_meta($post->ID, '_is_course_page', true);
    $credential     = get_post_meta($post->ID, '_course_credential', true);
    $competency     = get_post_meta($post->ID, '_course_competency', true);
    $mode     = get_post_meta($post->ID, '_course_mode', true);
    $teaches        = get_post_meta($post->ID, '_course_teaches', true);

    // Inline CSS for clean rendering
    echo '<style>
		.dd-schema-field { margin-bottom: 20px; }
		.dd-schema-field label.dd-label { display: block; font-weight: 600; margin-bottom: 6px; }
		.dd-schema-field input[type="text"] { width: 100%; max-width: 100%; padding: 6px 10px; }
		.dd-schema-field .description { color: #50575e; font-size: 13px; margin-top: 4px; display: block; font-style: italic; }
	</style>';

    // Render: Toggle for Standard Pages Only
    if ('page' === $post->post_type) {
        echo '<div class="dd-schema-field" style="background: #f0f0f1; padding: 15px; border-left: 4px solid #2271b1;">';
        echo '<label style="font-weight: 600;">';
        echo '<input type="checkbox" name="_is_course_page" value="1" ' . checked(1, $is_course_page, false) . ' /> ';
        echo esc_html__('Enable Course Schema on this Page', 'dd-schema-injector');
        echo '</label>';
        echo '<span class="description" style="margin-top: 8px;">Check this box to designate this standard page as a Course. The schema will use the page title and the fields below to generate Google Rich Results.</span>';
        echo '</div>';
    }

    // Render: Educational Credential
    echo '<div class="dd-schema-field">';
    echo '<label class="dd-label" for="_course_credential">' . esc_html__('Educational Credential Awarded', 'dd-schema-injector') . '</label>';
    echo '<input type="text" id="_course_credential" name="_course_credential" value="' . esc_attr($credential) . '" />';
    echo '<span class="description">Enter the official certification the student receives upon completion. Example: "A2 Certificate of Competency (A2 CofC)" or "RPC-L1 Part A".</span>';
    echo '</div>';

    // Render: Competency Required
    echo '<div class="dd-schema-field">';
    echo '<label class="dd-label" for="_course_competency">' . esc_html__('Competency Required', 'dd-schema-injector') . '</label>';
    echo '<input type="text" id="_course_competency" name="_course_competency" value="' . esc_attr($competency) . '" />';
    echo '<span class="description">List any mandatory prerequisites required before starting this course. Example: "UK CAA Flyer ID". Leave blank if none.</span>';
    echo '</div>';

    // Render: Course Mode
    echo '<div class="dd-schema-field">';
    echo '<label class="dd-label" for="_course_mode">' . esc_html__('Course Mode', 'dd-schema-injector') . '</label>';
    echo '<input type="text" id="_course_mode" name="_course_mode" value="' . esc_attr($mode) . '" />';
    echo '<span class="description">Specify the delivery method. You must use one of these exact terms: "online", "onsite", or "blended".</span>';
    echo '</div>';

    // Render: Teaches
    echo '<div class="dd-schema-field">';
    echo '<label class="dd-label" for="_course_teaches">' . esc_html__('Teaches (Skills & Topics)', 'dd-schema-injector') . '</label>';
    echo '<input type="text" id="_course_teaches" name="_course_teaches" value="' . esc_attr($teaches) . '" />';
    echo '<span class="description">List the specific skills covered, separated by a pipe symbol (|). Example: "UK Air Law | Meteorology | Visual Line of Sight operations".</span>';
    echo '</div>';
}

/**
 * 3. Securely save the meta box data when the post is updated.
 *
 * @param int $post_id The ID of the post being saved.
 */
add_action('save_post', 'dd_save_course_schema_meta_data');
function dd_save_course_schema_meta_data($post_id)
{
    // Verify nonce for security
    if (! isset($_POST['dd_course_schema_nonce']) || ! wp_verify_nonce($_POST['dd_course_schema_nonce'], 'dd_save_course_schema')) {
        return;
    }

    // Prevent saving during automated background saves
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Verify user permissions
    if (! current_user_can('edit_post', $post_id)) {
        return;
    }

    // Handle the checkbox specifically (checkboxes don't send POST data if unchecked)
    if (isset($_POST['_is_course_page'])) {
        update_post_meta($post_id, '_is_course_page', 1);
    } else {
        delete_post_meta($post_id, '_is_course_page');
    }

    // Map backend text input names to sanitization callbacks
    $fields = array(
        '_course_credential' => 'sanitize_text_field',
        '_course_competency' => 'sanitize_text_field',
        '_course_mode'       => 'sanitize_text_field',
        '_course_teaches'    => 'sanitize_text_field',
    );

    // Iterate through, sanitize, and update database records
    foreach ($fields as $field => $sanitization_callback) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, call_user_func($sanitization_callback, $_POST[$field]));
        } else {
            delete_post_meta($post_id, $field);
        }
    }
}
