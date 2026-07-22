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
 * (assets/js/coptrz-*-block.js) and flattened, typed attributes (not an opaque
 * blob) so the block is genuinely editable in Gutenberg. This file converts
 * those attributes back into the row-array shape the ORIGINAL legacy render
 * function expects and calls it directly, so output is byte-identical to
 * ___sections() (modules.php) whichever path — converted or hand-authored —
 * produced the block. See section-converter.php's `coptrz_block_item_mappers()`
 * for the item => block conversion (the inverse direction).
 *
 * Every renderer is guarded with function_exists()/shortcode_exists()/
 * class_exists() because modules.php/elements.php/svg.php are not loaded in
 * admin under the blocks-editor template (dd_is_blocks_editor_template_active()).
 */

/**
 * Normalises a picker attribute that may be either the modern [{id,title}, …]
 * shape (IdTokenPicker) or a legacy plain int[] shape (blocks converted before
 * this pass, or before the product-slider picker attribute type changed) into
 * a flat int[] of ids.
 */
function coptrz_block_picker_ids($value)
{
    $out = array();
    foreach ((array) $value as $v) {
        if (is_array($v) && isset($v['id'])) {
            $out[] = (int) $v['id'];
        } elseif (is_numeric($v)) {
            $out[] = (int) $v;
        }
    }
    return $out;
}

function coptrz_render_gallery_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/gallery') {
        return $block_content;
    }
    if (!function_exists('____gallery_modules')) {
        return $block_content;
    }
    $attrs = isset($block['attrs']) ? $block['attrs'] : array();
    $ids   = isset($attrs['galleryIds']) && is_array($attrs['galleryIds']) ? array_map('intval', $attrs['galleryIds']) : array();
    if (empty($ids)) {
        return $block_content;
    }

    $h_spacing = isset($attrs['horizontalSpacing']) ? (string) $attrs['horizontalSpacing'] : '';
    $v_spacing = isset($attrs['verticalSpacing']) ? (string) $attrs['verticalSpacing'] : '';

    return ____gallery_modules(array(
        'id'                      => wp_unique_id('gallery-'),
        'gallery'                 => $ids,
        'gallery_style'           => isset($attrs['galleryStyle']) ? (string) $attrs['galleryStyle'] : 'grid',
        'number_of_slides'        => isset($attrs['numberOfSlides']) ? $attrs['numberOfSlides'] : '',
        'number_of_slides_tablet' => isset($attrs['numberOfSlidesTablet']) ? $attrs['numberOfSlidesTablet'] : '',
        'number_of_slides_mobile' => isset($attrs['numberOfSlidesMobile']) ? $attrs['numberOfSlidesMobile'] : '',
        'column_width'            => isset($attrs['columnWidth']) ? (string) $attrs['columnWidth'] : '',
        'column_width_tablet'     => isset($attrs['columnWidthTablet']) ? (string) $attrs['columnWidthTablet'] : '',
        'column_width_mobile'     => isset($attrs['columnWidthMobile']) ? (string) $attrs['columnWidthMobile'] : '',
        // Registry option values are bare numbers ('6','5',…,'20px','0') so one
        // list serves both axes (functions.php, coptrz_legacy_block_field_options())
        // — prefix back to the gx-/gy- utility classes ____gallery_modules() expects.
        'vertical_spacing'        => $v_spacing !== '' ? 'gy-' . $v_spacing : '',
        'horizontal_spacing'      => $h_spacing !== '' ? 'gx-' . $h_spacing : '',
        'same_image_height'       => !empty($attrs['sameImageHeight']),
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
        $term_ids = coptrz_block_picker_ids(isset($attrs['categoryIds']) ? $attrs['categoryIds'] : array());
        $args['tax_query']['relation'] = 'AND';
        $args['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $term_ids,
        );
        $brand_ids = coptrz_block_picker_ids(isset($attrs['brandIds']) ? $attrs['brandIds'] : array());
        if (!empty($brand_ids)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'pa_brands',
                'field'    => 'term_id',
                'terms'    => $brand_ids,
            );
        }
    } elseif ($source_type === 'manually') {
        $args['include'] = coptrz_block_picker_ids(isset($attrs['productIds']) ? $attrs['productIds'] : array());
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

/**
 * `tabs` attribute is [{heading, description}, …], authored via RichText in
 * the editor — already real HTML, not bare textarea newlines — so autop is
 * disabled here (___tab_modules()'s third param, modules.php). The
 * section-converter's `tabs` mapper runs wpautop() ONCE at conversion time on
 * legacy textarea descriptions before storing them, so both paths end up with
 * real HTML in the attribute and render identically from here on.
 */
function coptrz_render_tabs_legacy_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/tabs-legacy') {
        return $block_content;
    }
    if (!function_exists('___tab_modules')) {
        return $block_content;
    }
    $attrs = isset($block['attrs']) ? $block['attrs'] : array();
    $tabs  = isset($attrs['tabs']) && is_array($attrs['tabs']) ? $attrs['tabs'] : array();
    if (empty($tabs)) {
        return $block_content;
    }
    return ___tab_modules($tabs, wp_unique_id('tabs-'), false);
}
add_filter('render_block', 'coptrz_render_tabs_legacy_block', 10, 2);

/**
 * Custom-source `items` are RichText-authored (real HTML already) so autop is
 * disabled for them; FAQ-sourced descriptions (raw post_content) always keep
 * wpautop regardless — __accordion_module() (modules.php) enforces that split
 * itself based on accordion_source, the `autop` flag here only ever applies to
 * the custom branch.
 */
function coptrz_render_accordion_legacy_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/accordion-legacy') {
        return $block_content;
    }
    if (!function_exists('__accordion_module')) {
        return $block_content;
    }
    $attrs  = isset($block['attrs']) ? $block['attrs'] : array();
    $source = isset($attrs['source']) ? (string) $attrs['source'] : '';

    return __accordion_module(array(
        'accordion'        => isset($attrs['items']) && is_array($attrs['items']) ? $attrs['items'] : array(),
        'accordion_source' => $source,
        'faqs'             => isset($attrs['faqs']) && is_array($attrs['faqs']) ? $attrs['faqs'] : array(),
        'faqs_category'    => isset($attrs['faqsCategory']) && is_array($attrs['faqsCategory']) ? $attrs['faqsCategory'] : array(),
        'open_first_item'  => !empty($attrs['openFirstItem']),
        'module_id'        => wp_unique_id('accordion-'),
        'with_border'      => !empty($attrs['withBorder']),
        'lower_opacity'    => !empty($attrs['lowerOpacity']),
        'autop'            => false,
    ));
}
add_filter('render_block', 'coptrz_render_accordion_legacy_block', 10, 2);

/**
 * Each drone's `features` attribute is a fixed map of
 * {drone,battery,controller,payload} => {enabled, quantity} — only `enabled`
 * specs are emitted as `service_features` rows (with their `_type` intact,
 * since __drone_servicing() dispatches SVG icon lookups on it); an absent spec
 * renders as an X in the comparison table, matching legacy's
 * "feature not in the array at all" state exactly (woocommerce.php).
 */
function coptrz_render_drone_servicing_grid_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/drone-servicing-grid') {
        return $block_content;
    }
    if (!function_exists('__drone_servicing') || !class_exists('SVG') || !function_exists('__heading')) {
        return $block_content;
    }
    $attrs  = isset($block['attrs']) ? $block['attrs'] : array();
    $drones = isset($attrs['drones']) && is_array($attrs['drones']) ? $attrs['drones'] : array();
    if (empty($drones)) {
        return $block_content;
    }

    $servicing_drones = array();
    foreach ($drones as $d) {
        $features = isset($d['features']) && is_array($d['features']) ? $d['features'] : array();
        $service_features = array();
        foreach ($features as $type => $f) {
            if (!empty($f['enabled'])) {
                $service_features[] = array(
                    '_type'    => (string) $type,
                    'quantity' => isset($f['quantity']) ? $f['quantity'] : '',
                );
            }
        }
        $servicing_drones[] = array(
            '_type'              => '_',
            'service_name'       => isset($d['serviceName']) ? (string) $d['serviceName'] : '',
            'service_subheading' => isset($d['serviceSubheading']) ? (string) $d['serviceSubheading'] : '',
            'service_price'      => isset($d['servicePrice']) ? (string) $d['servicePrice'] : '',
            'service_features'   => $service_features,
        );
    }

    return __drone_servicing(
        isset($attrs['heading']) ? (string) $attrs['heading'] : '',
        isset($attrs['description']) ? (string) $attrs['description'] : '',
        $servicing_drones
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
    $attrs = isset($block['attrs']) ? $block['attrs'] : array();
    if (empty($attrs['showCountdown'])) {
        return $block_content;
    }
    return do_shortcode('[event_countdown]');
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

/**
 * Column-width is now a per-box editable field (columnWidth/columnWidthTablet/
 * columnWidthMobile) rather than a class string frozen at conversion time — the
 * legacy 3-posts/col-md-6 special case (modules.php) is only ever applied ONCE,
 * to seed the initial value each box gets when the section-converter's
 * `global_post_box_selection` mapper creates it (section-converter.php), since
 * that special case depends on the whole selection's post COUNT, not something
 * a single box can (or needs to, after that point) know about.
 */
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

    $col = array();
    foreach (array('columnWidth', 'columnWidthTablet', 'columnWidthMobile') as $key) {
        if (!empty($attrs[$key])) {
            $col[] = (string) $attrs[$key];
        }
    }

    return __post_box(array(
        'id'                => $post_id,
        'featured'          => false,
        'tag'               => 'h4',
        'description_class' => 'excerpt-no-limit mb-0__related_posts',
        'elements'          => array('image', 'title', 'content'),
        'col'               => !empty($col) ? $col : 'col-lg-4 col-md-6',
    ));
}
add_filter('render_block', 'coptrz_render_global_post_box_block', 10, 2);
