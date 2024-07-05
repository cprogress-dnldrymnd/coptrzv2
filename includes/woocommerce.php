<?php
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);

function action_woocommerce_before_main_content()
{
    if (is_product_taxonomy()) {
        echo ___hero_product_taxonomy();
    } else if (is_product()) {
        $single_product_content = get__post_meta('single_product_content');
        echo $single_product_content;
    }
}


add_action('woocommerce_before_main_content', 'action_woocommerce_before_main_content');

function action_woocommerce_before_shop_loop()
{
    echo '<section class="product-archive-loop sm-padding-top lg-padding-bottom border-top-default no-overflow">';
    echo '<div class="container">';
    echo '<div class="row">';
    echo '<div class="col-lg-3">';
    /**
     * Hook: woocommerce_sidebar.
     *
     * @hooked woocommerce_get_sidebar - 10
     */
    do_action('woocommerce_sidebar');
    echo '</div>';
    echo '<div class="col-lg-9">';
}

add_action('woocommerce_before_shop_loop', 'action_woocommerce_before_shop_loop');

function action_woocommerce_after_shop_loop()
{
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</section>';
}

add_action('woocommerce_after_shop_loop', 'action_woocommerce_after_shop_loop');


/**
 * WooCommerce Loop Product Thumbs
 **/

remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

if (!function_exists('woocommerce_template_loop_product_thumbnail')) {
    function woocommerce_template_loop_product_thumbnail()
    {
        echo "<div class='wc-img-wrapper'>";
        echo woocommerce_get_product_thumbnail();
        echo "</div>";
    }
}
//Add DIV end element after shop loop item
add_action('woocommerce_after_shop_loop_item', 'action_woocommerce_after_shop_loop_item', 10, 0);
function action_woocommerce_after_shop_loop_item()
{
    echo '<div class="button-box button-bordered mt-3"><a href="' . get_the_permalink() . '">View Product</a></div>';
    echo "</div>";
};
//Add DIV start element before shop loop item
add_action('woocommerce_before_shop_loop_item', 'action_woocommerce_before_shop_loop_item', 10, 0);
function action_woocommerce_before_shop_loop_item()
{
    echo "<div class='product-inner rounded-10px border-default h-100'>";
};


remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');

function brands_filter()
{
    $brands = get_terms(array(
        'taxonomy'   => 'pa_brands',
        'hide_empty' => true,
    ));

    $html = "<div class='brands-filter'>";
    $html .= "<div class='row'>";
    foreach ($brands as $brand) {
        $link = get_term_link($brand->term_id);
        $logo = carbon_get_term_meta($brand->term_id, 'image');
        $logo_url = wp_get_attachment_image_url($logo, 'medium');
        if ($logo_url) {
            $html .= "<div class='col-auto'>";
            $html .= "<a class='border-default d-flex rounded-corner overflow-hidden position-relative' href='$link'>";
            $html .= "<img src='$logo_url'>";
            $html .= "</a>";
            $html .= "</div>";
        }
    }
    $html .= "</div>";
    $html .= "</div>";

    return $html;
}

add_shortcode('brands_filter', 'brands_filter');

//remove product title only
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);


add_filter('woocommerce_single_product_image_thumbnail_html', 'custom_remove_product_link');
function custom_remove_product_link($html)
{
    return strip_tags($html, '<div><img>');
}

/**
 * @snippet       Move upsells - WooCommerce Single Product
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 3.5.7
 * @community     https://businessbloomer.com/club/
 */

remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);

add_action('woocommerce_single_product_summary', 'woocommerce_upsell_display', 39);


/**
 * @snippet       Translate "You may also like..." - WooCommerce Single Product
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 4.1.1
 * @community     https://businessbloomer.com/club/
 */

add_filter('woocommerce_product_upsells_products_heading', 'bbloomer_translate_may_also_like');

function bbloomer_translate_may_also_like()
{
    return 'COPTRZ Recommended Accessories:';
}

/**
 * Output radio buttons on WooCommerce variations.
 */
add_filter('woocommerce_dropdown_variation_attribute_options_html', static function ($html, $args) {
    /** @var array $args */
    $args = wp_parse_args(apply_filters('woocommerce_dropdown_variation_attribute_options_args', $args), [
        'options'          => false,
        'attribute'        => false,
        'product'          => false,
        'selected'         => false,
        'name'             => '',
        'id'               => '',
        'class'            => '',
        'show_option_none' => __('Choose an option', 'woocommerce'),
    ]);

    /** @var WC_Product_Variable $product */
    $options          = $args['options'];
    $product          = $args['product'];
    $attribute        = $args['attribute'];
    $name             = $args['name'] ?: 'attribute_' . sanitize_title($attribute);
    $id               = $args['id'] ?: sanitize_title($attribute);
    $class            = $args['class'];
    $show_option_none = (bool)$args['show_option_none'];
    // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.
    $show_option_none_text = $args['show_option_none'] ?: __('Choose an option', 'woocommerce');

    // Get selected value.
    if ($attribute && $product instanceof WC_Product && $args['selected'] === false) {
        $selected_key     = 'attribute_' . sanitize_title($attribute);
        $args['selected'] = isset($_REQUEST[$selected_key]) ? wc_clean(wp_unslash($_REQUEST[$selected_key]))
            : $product->get_variation_default_attribute($attribute); // WPCS: input var ok, CSRF ok, sanitization ok.
    }

    if (empty($options) && !empty($product) && !empty($attribute)) {
        $attributes = $product->get_variation_attributes();
        $options    = $attributes[$attribute];
    }

    $radios = '<div class="custom-wc-variations" xx>';
    $radios .= '<div class="row">';

    if (!empty($options)) {
        foreach ($options as $option) {
            $radios .= '<div class="col-12">'; //col
            $checked = sanitize_title($args['selected']) === $args['selected'] ? checked(
                $args['selected'],
                sanitize_title($option),
                false
            ) : checked($args['selected'], $option, false);
            $radios  .= '<input type="radio" name="custom_' . esc_attr($name) . '" data-value="' . esc_attr($option) . '" id="'
                . esc_attr($name) . '_' . esc_attr($option) . '" data-variation-name="' . esc_attr($name) . '" ' . $checked . '>';
            $radios  .= '<label for="' . esc_attr($name) . '_' . esc_attr($option) . '">';
            $radios  .= esc_html(apply_filters('woocommerce_variation_option_name', $option));
            $radios  .= '</label>';
            $radios .= '</div>'; //end-col

        }
    }

    $radios .= '</div>';
    $radios .= '</div>';

    return $html . $radios;
}, 20, 2);
