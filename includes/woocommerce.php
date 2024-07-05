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

function custom_product_variation()
{
    global $product;

    $name = $product->get_name();
    $children = $product->get_children();
    $main_thumbnail = get_post_thumbnail_id($product->get_id());

    $html = '<div class="product-custom-variation">';
    $html .= '<div class="select-variant fw-medium mb-20px">Select a variant:</div>';
    $html .= '<div class="accordion" id="accordionVariation">';
    $html .= '<div class="row">';

    foreach ($children as $child) {
        $variation = wc_get_product($child);
        $variation_name = $variation->get_name();
        $product_attribute = $variation->get_attributes();
        $variations = 'data-variations="[';
        $i = 0;
        $numItems = count($product_attribute);
        foreach ($product_attribute as $key => $attr) {

            $variations .=  '&#34;' . $key . '|' . $attr . '&#34;';
            if (++$i != $numItems) {
                $variations .= ',';
            }
        }
        $variations .= ']"';

        $variation_name = str_replace($name . ' - ', '', $variation_name);
        $description = $variation->get_description();
        $variation_thumbnail = get_post_thumbnail_id($child);
        $thumbnail = $variation_thumbnail ? $variation_thumbnail : $main_thumbnail;
        $stock_status_variation = $variation->get_stock_status();
        $price = $variation->get_price_html();
        $html .= '<div class="col-12">';
        $html .= "<input stock='$stock_status_variation' type='radio' id='variation-$child' $variations value='$child'  name='variation-radio'>";
        $html .= "<label for='variation-$child' class='variation-label w-100'>";
        $html .= "<div class='inner d-flex w-100 p-20px rounded-corner status-$stock_status_variation'>";
        $html .= "<div class='col-auto'>";
        $html .= __image(array(
            'image_id' => $thumbnail,
            'class' => _attribute('class', array('variation-image')),
            'size' => 'thumbnail'
        ));
        $html .= '</div>';

        $html .= "<div class='col'>";
        $html .= "<div class='info-box'>";
        $html .= __heading(array(
            'heading' => $variation_name,
            'tag' => 'h5'
        ));
        $html .= $price;
        $html .= '<div class="accordion-item">'; //accordion-item
        $html .= "<div class='accordion-header' id='heading-variation-$child'> <button class='small-text fw-medium accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#collapse-variation-$child' aria-expanded='false' aria-controls='collapse-variation-$child'> Package Contents </button> </div>";

        $html .= "<div id='collapse-variation-$child' class='accordion-collapse collapse' aria-labelledby='heading-variation-$child' data-bs-parent='#accordionVariation'>";
        $html .= '<div class="accordion-body">';
        $html .= __description(array(
            'description' => $description
        ));
        $html .= '</div>';
        $html .= '</div>';


        $html .= '</div>'; //end-accordion-item

        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';
        $html .= '</label>';
        $html .= '</div>';
    }
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';


    echo $html;
}


add_action('woocommerce_before_variations_form', 'custom_product_variation');
