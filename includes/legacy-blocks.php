<?php

/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 *
 * Render filters for the "legacy wrapper" blocks: native editor equivalents of
 * section-builder items that have no first-class native block (Gallery, Product
 * Slider, Tabs, Accordion, Drone Servicing Grid, Events Widget, Product, Product
 * Compare, Global Post Box). Each block registers client-side with `save: null`
 * (assets/js/coptrz-*-block.js) and is rendered here by calling the same
 * function ___sections() (modules.php) already calls for that item type, so
 * output is byte-identical to the legacy renderer. See section-converter.php's
 * `coptrz_block_item_mappers()` for the item => block conversion.
 *
 * Every renderer is guarded with function_exists()/shortcode_exists()/
 * class_exists() because modules.php/elements.php/svg.php are not loaded in
 * admin under the blocks-editor template (dd_is_blocks_editor_template_active()).
 */

function coptrz_render_gallery_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/gallery') {
        return $block_content;
    }
    if (!function_exists('____gallery_modules')) {
        return $block_content;
    }
    $attrs  = isset($block['attrs']) ? $block['attrs'] : array();
    $legacy = isset($attrs['legacy']) && is_array($attrs['legacy']) ? $attrs['legacy'] : array();
    if (empty($legacy['gallery'])) {
        return $block_content;
    }
    return ____gallery_modules(array(
        'id'                      => wp_unique_id('gallery-'),
        'gallery'                 => (array) $legacy['gallery'],
        'gallery_style'           => isset($legacy['gallery_style']) ? $legacy['gallery_style'] : '',
        'number_of_slides'        => isset($legacy['number_of_slides']) ? $legacy['number_of_slides'] : '',
        'number_of_slides_tablet' => isset($legacy['number_of_slides_tablet']) ? $legacy['number_of_slides_tablet'] : '',
        'number_of_slides_mobile' => isset($legacy['number_of_slides_mobile']) ? $legacy['number_of_slides_mobile'] : '',
        'column_width'            => isset($legacy['column_width']) ? $legacy['column_width'] : '',
        'column_width_tablet'     => isset($legacy['column_width_tablet']) ? $legacy['column_width_tablet'] : '',
        'column_width_mobile'     => isset($legacy['column_width_mobile']) ? $legacy['column_width_mobile'] : '',
        'vertical_spacing'        => isset($legacy['vertical_spacing']) ? $legacy['vertical_spacing'] : '',
        'horizontal_spacing'      => isset($legacy['horizontal_spacing']) ? $legacy['horizontal_spacing'] : '',
        'same_image_height'       => isset($legacy['same_image_height']) ? $legacy['same_image_height'] : false,
    ));
}
add_filter('render_block', 'coptrz_render_gallery_block', 10, 2);

/**
 * Product Slider stores its SOURCE FIELDS, not a frozen WP_Query args array —
 * the legacy `main_query` source type resolves the current taxonomy/post at
 * render time (modules.php ~L992-1009), so freezing the args at conversion
 * time would bake in the wrong (or empty) result. This rebuilds the same args
 * shape modules.php builds, then calls __linked_products() (woocommerce.php)
 * directly — the same function the legacy [product_slider] shortcode
 * delegates to — with a fresh wp_unique_id() (the swiper element is bound by
 * `#id` in main.js, so ids must be unique when the block is used more than once).
 */
function coptrz_render_product_slider_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/product-slider') {
        return $block_content;
    }
    if (!function_exists('__linked_products')) {
        return $block_content;
    }
    $attrs = isset($block['attrs']) ? $block['attrs'] : array();

    $source_type = isset($attrs['sourceType']) ? (string) $attrs['sourceType'] : '';
    $numberposts = isset($attrs['numberposts']) && $attrs['numberposts'] !== '' ? $attrs['numberposts'] : -1;

    $args = array(
        'numberposts' => $numberposts,
        'post_type'   => 'product',
        'fields'      => 'ids',
        'post_status' => 'publish',
    );

    if ($source_type === 'category') {
        $term_ids = isset($attrs['categoryIds']) ? array_map('intval', (array) $attrs['categoryIds']) : array();
        $args['tax_query']['relation'] = 'AND';
        $args['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $term_ids,
        );
        $brand_ids = isset($attrs['brandIds']) ? array_map('intval', (array) $attrs['brandIds']) : array();
        if (!empty($brand_ids)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'pa_brands',
                'field'    => 'term_id',
                'terms'    => $brand_ids,
            );
        }
    } elseif ($source_type === 'manually') {
        $args['include'] = isset($attrs['productIds']) ? array_map('intval', (array) $attrs['productIds']) : array();
    } else {
        // main_query: resolve from the live request, same as modules.php.
        if (function_exists('is_product_taxonomy') && is_product_taxonomy()) {
            $term_id = get_queried_object()->term_id;
        } elseif (isset($_GET['post'])) {
            $term_id = get_post_meta((int) $_GET['post'], 'product_tax', true);
        } else {
            $term_id = false;
        }
        if ($term_id) {
            $args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $term_id,
            );
        }
    }

    $products = get_posts($args);

    return do_shortcode(__linked_products(
        $products,
        isset($attrs['buttonText']) ? (string) $attrs['buttonText'] : '',
        isset($attrs['buttonUrl']) ? (string) $attrs['buttonUrl'] : '',
        wp_unique_id('swiper-'),
        isset($attrs['heading']) ? (string) $attrs['heading'] : '',
        true,
        false
    ));
}
add_filter('render_block', 'coptrz_render_product_slider_block', 10, 2);

function coptrz_render_tabs_legacy_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/tabs-legacy') {
        return $block_content;
    }
    if (!function_exists('___tab_modules')) {
        return $block_content;
    }
    $attrs  = isset($block['attrs']) ? $block['attrs'] : array();
    $legacy = isset($attrs['legacy']) && is_array($attrs['legacy']) ? $attrs['legacy'] : array();
    $tabs   = isset($legacy['tabs']) && is_array($legacy['tabs']) ? $legacy['tabs'] : array();
    if (empty($tabs)) {
        return $block_content;
    }
    return ___tab_modules($tabs, wp_unique_id('tabs-'));
}
add_filter('render_block', 'coptrz_render_tabs_legacy_block', 10, 2);

function coptrz_render_accordion_legacy_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/accordion-legacy') {
        return $block_content;
    }
    if (!function_exists('__accordion_module')) {
        return $block_content;
    }
    $attrs  = isset($block['attrs']) ? $block['attrs'] : array();
    $legacy = isset($attrs['legacy']) && is_array($attrs['legacy']) ? $attrs['legacy'] : array();
    return __accordion_module(array(
        'accordion'        => isset($legacy['accordion']) ? $legacy['accordion'] : false,
        'accordion_source' => isset($legacy['accordion_source']) ? $legacy['accordion_source'] : false,
        'faqs'             => isset($legacy['faqs']) ? $legacy['faqs'] : false,
        'faqs_category'    => isset($legacy['faqs_category']) ? $legacy['faqs_category'] : false,
        'open_first_item'  => isset($legacy['open_first_item']) ? $legacy['open_first_item'] : false,
        'module_id'        => wp_unique_id('accordion-'),
        'with_border'      => isset($legacy['with_border']) ? $legacy['with_border'] : false,
        'lower_opacity'    => isset($legacy['lower_opacity']) ? $legacy['lower_opacity'] : false,
    ));
}
add_filter('render_block', 'coptrz_render_accordion_legacy_block', 10, 2);

function coptrz_render_drone_servicing_grid_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/drone-servicing-grid') {
        return $block_content;
    }
    if (!function_exists('__drone_servicing') || !class_exists('SVG') || !function_exists('__heading')) {
        return $block_content;
    }
    $attrs  = isset($block['attrs']) ? $block['attrs'] : array();
    $legacy = isset($attrs['legacy']) && is_array($attrs['legacy']) ? $attrs['legacy'] : array();
    if (empty($legacy['servicing_drones'])) {
        return $block_content;
    }
    return __drone_servicing(
        isset($legacy['servicing_heading']) ? (string) $legacy['servicing_heading'] : '',
        isset($legacy['servicing_description']) ? (string) $legacy['servicing_description'] : '',
        (array) $legacy['servicing_drones']
    );
}
add_filter('render_block', 'coptrz_render_drone_servicing_grid_block', 10, 2);

function coptrz_render_events_widget_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/events-widget') {
        return $block_content;
    }
    if (!shortcode_exists('event_countdown')) {
        return $block_content;
    }
    $attrs  = isset($block['attrs']) ? $block['attrs'] : array();
    $legacy = isset($attrs['legacy']) && is_array($attrs['legacy']) ? $attrs['legacy'] : array();
    $items  = isset($legacy['events_widget']) && is_array($legacy['events_widget']) ? $legacy['events_widget'] : array();

    $html = '';
    foreach ($items as $sub) {
        if (isset($sub['_type']) && $sub['_type'] === 'countdown') {
            $html .= do_shortcode('[event_countdown]');
        }
    }
    return $html !== '' ? $html : $block_content;
}
add_filter('render_block', 'coptrz_render_events_widget_block', 10, 2);

function coptrz_render_product_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/product') {
        return $block_content;
    }
    if (!shortcode_exists('product_add_to_cart')) {
        return $block_content;
    }
    $attrs      = isset($block['attrs']) ? $block['attrs'] : array();
    $product_id = isset($attrs['productId']) ? (int) $attrs['productId'] : 0;
    if (!$product_id) {
        return $block_content;
    }
    $is_training = !empty($attrs['isTraining']) ? 'true' : 'false';
    return do_shortcode("[product_add_to_cart id='{$product_id}' is_training='{$is_training}']");
}
add_filter('render_block', 'coptrz_render_product_block', 10, 2);

function coptrz_render_product_compare_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/product-compare') {
        return $block_content;
    }
    if (!shortcode_exists('product_compare')) {
        return $block_content;
    }
    $attrs      = isset($block['attrs']) ? $block['attrs'] : array();
    $compare_id = isset($attrs['compareId']) ? (int) $attrs['compareId'] : 0;
    if (!$compare_id) {
        return $block_content;
    }
    return do_shortcode("[product_compare id='{$compare_id}']");
}
add_filter('render_block', 'coptrz_render_product_compare_block', 10, 2);

function coptrz_render_global_post_box_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/global-post-box') {
        return $block_content;
    }
    if (!function_exists('__post_box')) {
        return $block_content;
    }
    $attrs   = isset($block['attrs']) ? $block['attrs'] : array();
    $post_id = isset($attrs['postId']) ? (int) $attrs['postId'] : 0;
    if (!$post_id) {
        return $block_content;
    }
    $col_classes = isset($attrs['colClasses']) ? trim((string) $attrs['colClasses']) : '';
    return __post_box(array(
        'id'                => $post_id,
        'featured'          => false,
        'tag'               => 'h4',
        'description_class' => 'excerpt-no-limit mb-0__related_posts',
        'elements'          => array('image', 'title', 'content'),
        'col'               => $col_classes !== '' ? explode(' ', $col_classes) : 'col-lg-4 col-md-6',
    ));
}
add_filter('render_block', 'coptrz_render_global_post_box_block', 10, 2);
