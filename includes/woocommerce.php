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
    echo "</div>";
};
//Add DIV start element before shop loop item
add_action('woocommerce_before_shop_loop_item', 'action_woocommerce_before_shop_loop_item', 10, 0);
function action_woocommerce_before_shop_loop_item()
{
    echo "<div class='product-inner rounded-10px border-default h-100'>";
};


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

function product_specifications()
{
    echo __product_specifications(true);
}
add_action('woocommerce_before_variations_form', 'product_specifications');

/**
 * Output radio buttons on WooCommerce variations.
 */

function custom_product_variation()
{
    global $product;

    $children = $product->get_children();
    $main_thumbnail = get_post_thumbnail_id($product->get_id());

    $html = '<div class="product-custom-variation">';
    $html .= '<div class="select-variant fw-medium mb-20px">Select a variant:</div>';
    $html .= '<div class="accordion" id="accordionVariation">';
    $html .= '<div class="row">';

    foreach ($children as $child) {
        $variation = wc_get_product($child);
        $product_attribute = $variation->get_attributes();
        $variation_name = '';
        $lastElement = end($product_attribute);

        $product_attribute_array = array();
        foreach ($product_attribute as $key => $attr) {
            $variation_name .= $attr . ' ';
            if ($attr != $lastElement) {
                $variation_name .= ' | ';
            }

            $product_attribute_array[$key] = $attr;
        }

        $json = json_encode($product_attribute_array);


        $description = $variation->get_description();
        $variation_thumbnail = get_post_thumbnail_id($child);
        $thumbnail = $variation_thumbnail ? $variation_thumbnail : $main_thumbnail;
        $stock_status_variation = $variation->get_stock_status();
        $price = $variation->get_price_html();
        $html .= '<div class="col-12">';
        $html .= "<input stock='$stock_status_variation' type='radio'  id='variation-$child' data_variations='$json' value='$child'  name='variation-radio'>";
        $html .= "<label for='variation-$child' class='variation-label w-100 $stock_status_variation'>";
        $html .= "<div class='inner product-inner d-flex align-items-center w-100 p-20px rounded-corner'>";
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

/**
 * Remove hooked actions from single product template to remove unwanted elements
 *
 */
function remove_single_product_elements()
{
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
}
add_action('woocommerce_before_single_product', 'remove_single_product_elements');

/**
 * @snippet       Plus Minus Quantity Buttons @ WooCommerce Single Product Page
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 8
 * @community     https://businessbloomer.com/club/
 */

add_action('woocommerce_before_quantity_input_field', 'bbloomer_display_quantity_minus');

function bbloomer_display_quantity_minus()
{
    if (!is_product()) return;
    echo '<button type="button" class="minus" >-</button>';
}

add_action('woocommerce_after_quantity_input_field', 'bbloomer_display_quantity_plus');

function bbloomer_display_quantity_plus()
{
    if (!is_product()) return;
    echo '<button type="button" class="plus" >+</button>';
}


function buy_now_button()
{
    $html = '<div class="button-box button-bordered buy-now-button">';
    $html .= '<button>';
    $html .= 'Buy Now';
    $html .= '</button>';
    $html .= '</div>';
    echo $html;
}
add_action('woocommerce_after_add_to_cart_button', 'buy_now_button', 20);


function __product_compare($id)
{
    $SVG = new SVG;
    $products = get__post_meta_by_id($id, 'products');

    $specs = array();

    foreach ($products as $product) {
        $pa_specifications = get_the_terms($product['id'], 'pa_specifications');
        foreach ($pa_specifications as $specification) {
            $specs[$specification->term_id] = $specification->name;
        }
    }

    $html = "<section class='product-compare bg-light lg-padding-top lg-padding-bottom'>";
    $html .= "<div class='container'>"; //container
    $html .= "<div class='row'>";
    $html .= "<div class='col-lg-3'>";
    $html .= __heading(array(
        'heading' => get_the_title($id),
    ));
    $html .= "</div>";

    foreach ($products as $product) {
        $html .= "<div class='col-lg-3'>";
        $html .= _product_grid_display($product['id']);
        $html .= "</div>";
    }

    $html .= "</div>";

    $html .= "<div class='comparison products-specifications products-specifications-v2'>"; //products-specifications
    foreach ($specs as $key => $spec) {
        $icon = get__term_meta($key, 'icon');
        $mime_type =  get_post_mime_type($icon);
        $html .= "<div class='row'>"; //specs-row

        $html .= "<div class='col-3'>"; //specs-row-col
        $html .= "<div class='inner h-100 d-flex align-items-center'>"; //inner
        if (str_contains($mime_type, 'svg')) {
            $html .= __icon(array(
                'id' => $icon,
                'class' => _attribute('class', array('me-3 text-accent'))
            ));
        } else {
            $html .= __image(array(
                'image_id' => $icon,
                'class' => _attribute('class', array('me-3 text-accent'))
            ));
        }
        $html .= __heading(array(
            'heading' => $spec,
            'class' => _attribute('class', array('mb-0')),
            'tag' => 'h5',
        ));
        $html .= "</div>"; //end-inner
        $html .= "</div>"; //end-specs-row-col

        foreach ($products as $product) {
            $spec_product = array();

            $pa_specifications = get_the_terms($product['id'], 'pa_specifications');
            foreach ($pa_specifications as $specification) {
                $spec_product[$specification->term_id] = $specification->name;
            }



            $html .= "<div class='col-3'>";
            $html .= "<div class='inner   h-100 d-flex align-items-center justify-content-center'>"; //inner

            if (array_key_exists($key, $spec_product)) {
                $html .= "<div class='active'>";
                $html .= $SVG->check();
                $html .= "</div>";
            } else {
                $html .= "<div class='not-active'>";
                $html .= $SVG->xmark();
                $html .= "</div>";
            }

            $html .= "</div>";
            $html .= "</div>";
        }



        $html .= "</div>"; //end-specs-row
    }
    $html .= "</div>"; //end products-specifications

    $html .= "</div>"; //end-container
    $html .= "</section>";


    return $html;
}


function _product_grid_display($id)
{
    $product = wc_get_product($id);
    $title = $product->get_name();
    $permalink = get_the_permalink($id);
    $html = "<ul class='products custom-product-grid'>";
    $html = "<li class='product type-product post-61545 status-private first instock'>";
    $html .= "<div class='product-inner rounded-10px border-default h-100'>";
    $html .= "<a href='$permalink' class='woocommerce-LoopProduct-link woocommerce-loop-product__link'>";

    $html .= "<div class='wc-img-wrapper'>";
    $html .= "<img width='300' height='225' src='' class='attachment-woocommerce_thumbnail size-woocommerce_thumbnail' alt='' decoding='async'>";
    $html .= "</div>";
    $html .= "<h2 class='woocommerce-loop-product__title'>$title</h2>";


    $html .= "</a>";
    $html .= "</div>";
    $html .= "</li>";
    $html .= "</ul>";

    return $html;
}
