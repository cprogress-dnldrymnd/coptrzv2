<?php

remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);

function action_woocommerce_before_main_content()
{
    if (is_product_taxonomy()) {
        echo do_shortcode(___hero_product_taxonomy());
        $product_category_page = __get_product_taxonomy_page(get_queried_object()->term_id);

        if ($product_category_page) {
            global $product_taxonomy_page;
            $product_taxonomy_page[] = $product_category_page;
            echo do_shortcode(get_post_meta($product_category_page, '_sections_html', true));
            $term_id = get_queried_object()->term_id;

            $product_slider_args['numberposts'] = -1;
            $product_slider_args['post_type'] = 'product';
            $product_slider_args['fields'] = 'ids';
            $product_slider_args['post_status'] = 'publish';
            $product_slider_args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $term_id
            );
            $products = get_posts($product_slider_args);
            echo do_shortcode( __linked_products($products, false, false, 'swiper-series', 'Series Product Range', true, true) );

            $product_accessories_slider_args['tax_query']['relation'] = 'AND';
            $product_accessories_slider_args['numberposts'] = -1;
            $product_accessories_slider_args['post_type'] = 'product';
            $product_accessories_slider_args['fields'] = 'ids';
            $product_accessories_slider_args['post_status'] = 'publish';
            $product_accessories_slider_args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $term_id
            );
            $product_accessories_slider_args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => 30
            );
            $products = get_posts($product_accessories_slider_args);
            echo do_shortcode(__linked_products($products, false, false, 'swiper-series-acc', 'Accessories', true, true));

        }
    }
    else if (is_product()) {
        echo ___hero_modules();
        echo __product_specifications();
        echo do_shortcode(get_post_meta(get_the_ID(), '_sections_html', true));
    }
}

add_action('woocommerce_before_main_content', 'action_woocommerce_before_main_content');

function action_woocommerce_after_main_content()
{
    if (is_product_taxonomy()) {
        $term = get_queried_object();
        if ($term->taxonomy == 'pa_brands') {

            $product_cat = get_terms(array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => true,
                'parent'     => 0
            ));
            foreach ($product_cat as $cat) {
                $product_slider_args = [];

                $product_slider_args['numberposts'] = -1;
                $product_slider_args['post_type'] = 'product';
                $product_slider_args['fields'] = 'ids';

                $product_slider_args['tax_query'][] = array(
                    'taxonomy' => 'pa_brands',
                    'field'    => 'term_id',
                    'terms'    => $term->term_id,
                );
                $product_slider_args['tax_query'][] = array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $cat->term_id
                );
                $products = get_posts($product_slider_args);
                if ($products) {
                    echo __linked_products($products, 'Shop Full Range', get_term_link($cat->term_id), 'swiper-cat-' . $cat->term_id, $cat->name, false, true, true);
                }
            }
        }
    }
}

add_action('woocommerce_after_main_content', 'action_woocommerce_after_main_content');

function action_woocommerce_after_single_product_summary()
{
    $related_training = get__post_meta('related_training');
    $compatible_payloads = get__post_meta('compatible_payloads');
    $accessories = get__post_meta('accessories');
    $softwares = get__post_meta('softwares');
    $drones = get__post_meta('drones');

    echo do_shortcode(get_post_meta(get_the_ID(), '_sections_after_main_html', true));

    if ($drones) {
        echo __linked_products(__get_assoc_post_ids($drones), 'All Drones', '/product-category/drones/', 'swiper-drones', 'Drones');
    }

    if ($related_training) {

        echo __linked_products(__get_assoc_post_ids($related_training), 'All Trainings', '/product-category/training/', 'swiper-payloads', 'Related Training');
    }

    if ($softwares) {
        echo __linked_products(__get_assoc_post_ids($softwares), 'All Softwares', '/product-category/softwares/', 'swiper-softwares', 'Softwares');
    }

    if ($compatible_payloads) {
        echo __linked_products(__get_assoc_post_ids($compatible_payloads), 'All Payloads', '/product-category/payloads-and-attachments/', 'swiper-payloads', 'Compatible Payloads');
    }

    if ($accessories) {
        echo __linked_products(__get_assoc_post_ids($accessories), 'All Accessories', '/product-category/accessories-and-parts/', 'swiper-accessories', 'Accessories');
    }
}

add_action('woocommerce_after_single_product_summary', 'action_woocommerce_after_single_product_summary');

function __get_assoc_post_ids($posts, $post_arr = [])
{
    foreach ($posts as $post) {
        $post_status = get_post_status($post['id']);
        if ($post_status == 'publish' || $post_status == 'private') {
            $post_arr[] = $post['id'];
        }
    }

    return $post_arr;
}

function action_woocommerce_after_single_product()
{
    $related_guides = get__post_meta('related_guides');
    $related_casestudies = get__post_meta('related_casestudies');

    if ($related_guides) {
        $data = array(
            'col'      => false,
            'featured' => false,
            'style'    => 'style-1',
            'taxonomy' => 'guides_category',
            'elements' => array('image', 'category', 'title', 'excerpt', 'button'),
        );
        echo do_shortcode(__related_posts($related_guides, $data, 'Related Guides'));
    }


    if ($related_casestudies) {
        $data = array(
            'col'      => false,
            'featured' => false,
            'style'    => 'style-1',
            'taxonomy' => 'casestudies_category',
            'elements' => array('image', 'category', 'title', 'excerpt', 'button'),
        );
        echo do_shortcode(__related_posts($related_casestudies, $data, 'Related Case Studies'));
    }
}

add_action('woocommerce_after_single_product', 'action_woocommerce_after_single_product');


function action_woocommerce_before_shop_loop()
{


    echo '<section class="product-archive-loop md-padding-top md-padding-bottom border-top-default no-overflow">';
    echo '<div class="container">';
}

add_action('woocommerce_before_shop_loop', 'action_woocommerce_before_shop_loop');

function action_woocommerce_after_shop_loop()
{
    $product_category_page = __get_product_taxonomy_page(get_queried_object()->term_id);
    if (!$product_category_page) {
        echo '</div>';
        echo '</section>';
    }
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
        echo "<div class='wc-img-wrapper rounded-corner'>";
        echo woocommerce_get_product_thumbnail();
        echo "</div>";
    }
}
//Add DIV end element after shop loop item
add_action('woocommerce_after_shop_loop_item', 'action_woocommerce_after_shop_loop_item', 10, 0);
function action_woocommerce_after_shop_loop_item()
{
    echo "</div>";
}
;
//Add DIV start element before shop loop item
add_action('woocommerce_before_shop_loop_item', 'action_woocommerce_before_shop_loop_item', 10, 0);
function action_woocommerce_before_shop_loop_item()
{
    $product_category_page = false;
    $class = '';
    if (is_product_taxonomy()) {
        $product_category_page = __get_product_taxonomy_page(get_queried_object()->term_id);
    }
    if ($product_category_page) {
        $class = 'rounded-10px border-default p-20px';
    }
    echo "<div class='product-inner h-100 $class'>";
    if (!$product_category_page) {
        echo "<div class='product-info'>";
    }
}
;


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
        $html .= "<label for='variation-$child' class='variation-label status-style-2 w-100 $stock_status_variation'>";
        $html .= "<div class='inner product-inner d-flex align-items-center w-100 p-20px rounded-corner'>";
        $html .= "<div class='col-auto'>";
        $html .= __image(array(
            'image_id' => $thumbnail,
            'class'    => _attribute('class', array('variation-image')),
            'size'     => 'thumbnail'
        ));
        $html .= '</div>';

        $html .= "<div class='col'>";
        $html .= "<div class='info-box'>";
        $html .= __heading(array(
            'heading' => $variation_name,
            'tag'     => 'h5'
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
    if (!is_product())
        return;
    echo '<button type="button" class="minus" >-</button>';
}

add_action('woocommerce_after_quantity_input_field', 'bbloomer_display_quantity_plus');

function bbloomer_display_quantity_plus()
{
    if (!is_product())
        return;
    echo '<button type="button" class="plus" >+</button>';
}


function buy_now_button()
{
    $product_id = get_the_ID();
    $html = '<div class="button-box button-bordered buy-now-button d-none">';
    $html .= "<button class='buy-now-trigger buy-now-trigger-main' data-target='$product_id'>";
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
    $product_attributes = get__post_meta_by_id($id, 'product_attributes');

    $specs = array();

    foreach ($products as $product) {
        $pa_specifications = get_the_terms($product['id'], 'pa_specifications');
        foreach ($pa_specifications as $specification) {
            $specs[$specification->term_id] = $specification->name;
        }
    }

    $html = "<div class='product-compare'>";
    $html .= "<div class='comparison products-specifications products-specifications-v2'>"; //products-specifications
    $html .= "<div class='row g-10px'>";
    $html .= "<div class='col-lg-3'>";
    $html .= __heading(array(
        'heading' => get_the_title($id),
    ));

    $html .= "</div>";
    foreach ($products as $product) {
        $html .= "<div class='col-lg-3'>";
        $html .= "<div class='product-inner h-100 d-flex flex-column'>";
        $html .= _product_grid_display($product['id']);

        $html .= "<div class='row-services-spec-mobile row g-3 d-lg-none mt-4 mb-5'>";
        foreach ($product_attributes as $product_attribute) {
            $taxonomy_details = get_taxonomy($product_attribute);
            $product_attribute_values = get_the_terms($product['id'], $product_attribute);

            $html .= "<div class='col-12'>";
            $html .= "<div class='inner inner-specs-list  h-100 d-flex align-items-center justify-content-center'>"; //inner
            $html .= $taxonomy_details->labels->singular_name . ': ';
            foreach ($product_attribute_values as $product_attribute_value) {
                $html .= $product_attribute_value->name;
            }
            $html .= "</div>";
            $html .= "</div>";
        }

        $html .= "</div>";

        $html .= "</div>";
        $html .= "</div>";
    }

    $html .= "</div>";

    foreach ($product_attributes as $product_attribute) {

        $taxonomy_details = get_taxonomy($product_attribute);

        $html .= "<div class='row g-10px d-none d-lg-flex'>"; //specs-row

        $html .= "<div class='col-3'>"; //specs-row-col
        $html .= "<div class='inner h-100 d-flex align-items-center'>"; //inner
        $html .= "<div class='me-3 text-accent'>"; //icon
        $html .= $SVG->battery();
        $html .= "</div>"; //end-icon

        if ($product_attribute == 'weight') {
            $heading = 'Weight';
        }
        else {
            $heading = $taxonomy_details->labels->singular_name;
        }

        $html .= __heading(array(
            'heading' => $heading,
            'class'   => _attribute('class', array('mb-0')),
            'tag'     => 'h5',
        ));
        $html .= "</div>"; //end-inner
        $html .= "</div>"; //end-specs-row-col


        foreach ($products as $product) {
            $product_attribute_values = get_the_terms($product['id'], $product_attribute);


            $html .= "<div class='col-3'>";
            $html .= "<div class='inner inner-specs-list  h-100 d-flex align-items-center justify-content-center'>"; //inner

            if ($product_attribute == 'weight') {
                $product = wc_get_product($product['id']);
                $html .= $product->get_weight();
            }
            else {

                foreach ($product_attribute_values as $product_attribute_value) {
                    $html .= $product_attribute_value->name;
                }
            }


            $html .= "</div>";
            $html .= "</div>";
        }


        $html .= "</div>";
    }


    $html .= "</div>"; //end products-specifications

    $html .= "</div>";


    return $html;
}

function __product_compare_oldd($id)
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

    $html = "<div class='product-compare'>";
    $html .= "<div class='comparison products-specifications products-specifications-v2'>"; //products-specifications
    $html .= "<div class='row g-10px'>";
    $html .= "<div class='col-lg-3'>";
    $html .= __heading(array(
        'heading' => get_the_title($id),
    ));

    $html .= "</div>";

    foreach ($products as $product) {
        $html .= "<div class='col-lg-3'>";
        $html .= "<div class='product-inner  d-flex flex-column'>";
        $html .= _product_grid_display($product['id']);

        $html .= "<div class='row-services-spec-mobile d-lg-none mt-4'>";
        foreach ($specs as $key => $spec) {
            $icon = get__term_meta($key, 'icon');
            $mime_type = get_post_mime_type($icon);
            $html .= "<div class='row g-10px mb-10px'>"; //specs-row

            $html .= "<div class='col-8'>"; //specs-row-col
            $html .= "<div class='inner h-100 d-flex align-items-center'>"; //inner
            if (str_contains($mime_type, 'svg')) {
                $html .= __icon(array(
                    'id'    => $icon,
                    'class' => _attribute('class', array('me-3 text-accent'))
                ));
            }
            else {
                $html .= __image(array(
                    'image_id' => $icon,
                    'class'    => _attribute('class', array('me-3 text-accent'))
                ));
            }
            $html .= __heading(array(
                'heading' => $spec,
                'class'   => _attribute('class', array('mb-0 text-primary')),
                'tag'     => 'h5',
            ));
            $html .= "</div>"; //end-inner
            $html .= "</div>"; //end-specs-row-col

            $spec_product = array();

            $pa_specifications = get_the_terms($product['id'], 'pa_specifications');
            foreach ($pa_specifications as $specification) {
                $spec_product[$specification->term_id] = $specification->name;
            }


            $html .= "<div class='col-4'>";
            $html .= "<div class='inner inner-specs-list  h-100 d-flex align-items-center justify-content-center'>"; //inner

            if (array_key_exists($key, $spec_product)) {
                $html .= "<div class='active'>";
                $html .= $SVG->check();
                $html .= "</div>";
            }
            else {
                $html .= "<div class='not-active'>";
                $html .= $SVG->xmark();
                $html .= "</div>";
            }

            $html .= "</div>";
            $html .= "</div>";


            $html .= "</div>"; //end-specs-row
        }

        $html .= "</div>";

        $html .= "</div>";
        $html .= "</div>";
    }

    $html .= "</div>";

    foreach ($specs as $key => $spec) {
        $icon = get__term_meta($key, 'icon');
        $mime_type = get_post_mime_type($icon);
        $html .= "<div class='row g-10px d-none d-lg-flex'>"; //specs-row

        $html .= "<div class='col-3'>"; //specs-row-col
        $html .= "<div class='inner h-100 d-flex align-items-center'>"; //inner
        if (str_contains($mime_type, 'svg')) {
            $html .= __icon(array(
                'id'    => $icon,
                'class' => _attribute('class', array('me-3 text-accent'))
            ));
        }
        else {
            $html .= __image(array(
                'image_id' => $icon,
                'class'    => _attribute('class', array('me-3 text-accent'))
            ));
        }
        $html .= __heading(array(
            'heading' => $spec,
            'class'   => _attribute('class', array('mb-0')),
            'tag'     => 'h5',
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
            $html .= "<div class='inner inner-specs-list  h-100 d-flex align-items-center justify-content-center'>"; //inner

            if (array_key_exists($key, $spec_product)) {
                $html .= "<div class='active'>";
                $html .= $SVG->check();
                $html .= "</div>";
            }
            else {
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

    $html .= "</div>";


    return $html;
}



function _product_grid_display($id)
{
    if (get_post_type($id) == 'product') {
        $product = wc_get_product($id);
        $title = $product->get_name();
        $permalink = get_the_permalink($id);
        $post_thumnail = get_the_post_thumbnail_url($id, 'medium');
        $stock_status = $product->get_stock_status();
        $status = get_post_status($id);

        $html = "<ul class='products h-100 custom-product-grid h-100 m-0 p-0'>";
        $html .= "<li class='product h-100 m-0 p-0 w-100 h-100 post-$id $stock_status'>";
        $html .= "<div class='product-inner h-100 p-20px rounded-10px border-default h-100 bg-white'>";
        if ($status == 'publish') {
            $html .= "<a href='$permalink' class='woocommerce-LoopProduct-link woocommerce-loop-product__link'>";
        }
        $html .= "<div class='wc-img-wrapper'>";
        $html .= "<img width='300' height='225' src='$post_thumnail' class='attachment-woocommerce_thumbnail size-woocommerce_thumbnail' alt='$title' decoding='async'>";
        $html .= "</div>";
        $html .= "<h2 class='woocommerce-loop-product__title mb-0'>$title</h2>";
        // $html .= $product->get_price_html();
        $html .= '<span class="status d-block mb-2 mt-2"></span>';
        if ($status == 'publish') {
            $html .= "</a>";
        }

        if ($status == 'publish') {
            $html .= "<div class='product-buttons'>";
            $html .= "<div class='button-box button-bordered'><a href='$permalink'>View Product</a></div>";
            $html .= "</div>";
        }
        $html .= "</div>";
        $html .= "</li>";
        $html .= "</ul>";

        return $html;
    }
}


/**
 * Remove product page tabs
 */
add_filter('woocommerce_product_tabs', 'my_remove_all_product_tabs', 98);

function my_remove_all_product_tabs($tabs)
{
    unset($tabs['description']);          // Remove the description tab
    unset($tabs['reviews']);             // Remove the reviews tab
    unset($tabs['additional_information']);      // Remove the additional information tab
    return $tabs;
}


function __linked_products($field, $button_text, $button_link, $id, $title, $shorcode = false, $include_section = true, $is_slider = true, $section_id = 'Related-Products')
{

    if ($include_section) {
        $html = "<section class='related-products-slider border-top-default md-padding-top md-padding-bottom' id='$section_id'>";
    }
    else {
        $html = "<div class='related-products-slider'>";
    }

    $html .= "<h2 class='text-center px-20px'>$title</h2>";

    if ($is_slider) {
        $html .= "<div class='container extend-right'>"; //end-container
        $html .= "<div class='swiper-holder'>"; //swiper-holder
        $html .= "<div class='swiper swiper-linked-products' id='$id'>"; //swiper
        $html .= "<div class='swiper-wrapper'>"; //swiper-wrapper
    }
    else {
        $html .= "<div class='container'>"; //container
        $html .= "<div class='row g-4'>"; //row
    }

    foreach ($field as $product_id) {

        if ($is_slider) {
            $html .= "<div class='swiper-slide'>"; //swiper-slide
        }
        else {
            $html .= "<div class='col-lg-3 col-md-6'>"; //col
        }
        if ($shorcode == false) {
            $html .= _product_grid_display($product_id);
        }
        else {
            $html .= "[product_grid_display id='$product_id']";
        }
        $html .= '</div>'; //end-swiper-slide // col

    }
    if ($is_slider) {
        $html .= '</div>'; //end-swiper-wrapper
        $html .= '</div>'; //end-swiper
        $html .= '</div>'; //end-swiper-holder
        $html .= '</div>'; //end-container
    }
    else {
        $html .= '</div>'; //end-row
        $html .= '</div>'; //end-container
    }
    if ($is_slider) {

        $html .= "<div class='container no-extend mt-4'><div class='row g-4 justify-content-between align-items-center'> <div class='col-auto'> <div class='swiper-nav d-inline-flex'> <div class='swiper-button-prev' id='swiper-prev-$id'></div> <div class='swiper-button-next' id='swiper-next-$id'></div> </div> </div>";

        if ($button_text) {
            $html .= "<div class='col-auto'>";
            $html .= "<div class='button-box button-accent'> <a href='$button_link'>$button_text</a>";
            $html .= "</div>";
            $html .= "</div> "; //end-col
        }
    }

    $html .= "</div> "; //end-row

    $html .= "</div> "; //en-container



    if ($include_section) {
        $html .= '</section>';
    }
    else {
        $html .= '</div>';
    }

    return $html;
}





function __get_product_taxonomy_page($id)
{
    $args = array(
        'numberposts' => -1,
        'post_type'   => 'producttaxonomypages',
        'fields'      => 'ids',
        'meta_query'  => array(
            array(
                'key'   => '_product_term_id',
                'value' => $id,
            ),
        ),
    );
    $product_page = get_posts($args);

    if ($product_page) {
        return $product_page[0];
    }
}

function date_compare_latest($element1, $element2)
{
    $datetime1 = strtotime($element1['datetime']);
    $datetime2 = strtotime($element2['datetime']);
    if ($datetime1 == $datetime2) {
        return 0;
    }

    return ($datetime1 < $datetime2) ? -1 : 1;
}

function date_compare_oldest($element1, $element2)
{
    $datetime1 = strtotime($element1['datetime']);
    $datetime2 = strtotime($element2['datetime']);
    if ($datetime1 == $datetime2) {
        return 0;
    }

    return ($datetime1 > $datetime2) ? -1 : 1;
}



function custom_product_variation_training($product_id, $delivery_method = 'online-self-paced', $sortby = 'latest', $location = false)
{
    $SVG = new SVG;
    $product = wc_get_product($product_id);
    $children = $product->get_children();

    $child_array = [];
    foreach ($children as $child) {
        $variation = wc_get_product($child);
        $product_attribute = $variation->get_attributes();
        $product_attribute_array = [];
        foreach ($product_attribute as $key => $attr) {
            $product_attribute_array[$key] = $attr;
        }

        if ($product_attribute_array['pa_delivery-methods'] == $delivery_method) {
            $child_array[] = array(
                'product_id'             => $child,
                'product_attributes'     => $variation->get_attributes(),
                'sku'                    => $variation->get_sku(),
                'price'                  => $variation->get_price_html(),
                'stock_status_variation' => $variation->get_stock_status(),
                'location'               => $product_attribute_array['pa_location'],
                'datetime'               => $product_attribute_array['date']
            );
        }
    }

    usort($child_array, 'date_compare_latest');

    if ($location) {
        $child_array_val = array_filter($child_array, function ($var) use ($location) {
            return ($var['location'] == $location);
        });
    }
    else {
        $child_array_val = $child_array;
    }

    // return var_dump($child_array);

    $children_chunk = array_chunk($child_array_val, 4);

    $html = '<div class="product-custom-variation product-training-variation">';

    if ($child_array_val) {
        $html .= "<div class='swiper swiper-training'>"; //swiper
        $html .= "<div class='swiper-wrapper'>"; //swiper-wrapper


        foreach ($children_chunk as $children) {
            $html .= '<div class="swiper-slide">'; //swiper-slide
            $html .= '<div class="row g-4">'; //row
            foreach ($children as $child) {
                $product_id = $child['product_id'];
                $sku = $child['sku'];
                $price = $child['price'];
                $product_attributes = $child['product_attributes'];
                $stock_status_variation = $child['stock_status_variation'];

                $variation_name = '';
                $lastElement = end($product_attributes);

                foreach ($product_attributes as $key => $attr) {
                    $variation_name .= $attr . ' ';
                    if ($attr != $lastElement) {
                        $variation_name .= ' | ';
                    }
                    $product_attribute_array[$key] = $attr;
                }

                $json = json_encode($product_attribute_array);


                $html .= '<div class="col-lg-6">';


                $html .= "<input stock='$stock_status_variation' type='radio'  id='variation-$product_id' data_variations='$json' value='$product_id'  name='variation-radio'>";
                $html .= "<label for='variation-$product_id' class='variation-label status-style-2 w-100 h-100'>"; //label
                $html .= "<div class='inner product-inner w-100 p-20px rounded-corner  h-100 d-flex flex-column justify-content-between'>"; //inner

                $html .= "<div class='info-box d-flex flex-column justify-content-between'>";

                if ($product_attribute_array['date'] || $product_attribute_array['pa_location']) {
                    $html .= "<div class='row g-3 justify-content-between mb-3'>";

                    if ($product_attribute_array['date']) {
                        $html .= "<div class='col-auto'>";
                        if ($product_attribute_array['date'] != 'N/A') {
                            $html .= "<span class='date smaller-text text-white bg-accent py-1 px-2'>";
                            $html .= _date_format($product_attribute_array['date'], true);
                            $html .= '</span>';
                        }
                        $html .= '</div>';
                    }

                    if ($product_attribute_array['pa_location']) {
                        $html .= "<div class='col-auto'>";
                        $html .= "<span class='location smaller-text '>";
                        $html .= $SVG->location();
                        $html .= $product_attribute_array['pa_location'];
                        $html .= '</span>';
                        $html .= '</div>';
                    }
                    $html .= '</div>';
                }
                $html .= __heading(array(
                    'heading' => $product_attribute_array['course-type'],
                    'tag'     => 'h3'
                ));
                /*
                $html .= '<div>';
                $html .= $price;
                $html .= '</div>';
*/
                $html .= '</div>';

                $html .= "<div class='button-box button-bordered mt-3'>";
                $html .= "<a href='?add-to-cart=$product_id' data-quantity='1' class='button product_type_simple add_to_cart_button ajax_add_to_cart' data-product_id='$product_id' data-product_sku='$sku' rel='nofollow'>Add to basket</a>";
                $html .= '</div>';

                $html .= '</div>'; //inner
                $html .= '</label>'; //label
                $html .= '</div>';
            }
            $html .= '</div>'; //end-row
            $html .= '</div>'; //swiper-slide

        }
        $html .= '</div>'; //end-swiper-wrapper
        $html .= '<div class="swiper-nav d-flex justify-content-start mt-5">'; // swipernav
        $html .= '<div class="swiper-button-prev"></div>';
        $html .= '<div class="swiper-button-next"></div>';
        $html .= '</div>'; //end swipernav
        $html .= '</div>'; //end-swiper
    }
    else {
        $html .= '<h2 class="my-5">No training found.</h2>';
    }
    $html .= '</div>';

    echo $html;
}


function __drone_servicing()
{
    $SVG = new SVG;
    $servicing_heading = get__theme_option('servicing_heading');
    $servicing_description = get__theme_option('servicing_description');
    $servicing_drones = get__theme_option('servicing_drones');

    $specs = array();

    foreach ($servicing_drones as $servicing_drone) {
        $specs[] = $servicing_drone['_type'];
        $service_features = $servicing_drone['service_features'];
        foreach ($service_features as $service_feature) {
            $specs[$service_feature['_type']] = $service_feature['_type'];
        }
    }
    $html = "<div class='product-compare drone-servicing' >";
    $html .= "<div class='comparison products-specifications products-specifications-v2'>"; //products-specifications
    $html .= "<div class='row g-10px row-services'>";
    $html .= "<div class='col-lg-3'>";
    $html .= __heading(array(
        'heading' => $servicing_heading,
        'class'   => _attribute('class', array('mb-3')),
    ));

    $html .= __description(array(
        'description' => $servicing_description,
        'class'       => _attribute('class', array('description-box')),
    ));
    $html .= "</div>";

    foreach ($servicing_drones as $key => $drone) {
        $position = $key % 3;

        if ($position == 0) {
            $class = 'bg-gray';
            $button_class = 'button-primary';
        }
        else if ($position == 1) {
            $class = 'bg-accent';
            $button_class = 'button-primary';
        }
        else if ($position == 2) {
            $class = 'bg-black';
            $button_class = 'button-accent';
        }


        $service_name = $drone['service_name'];
        $service_subheading = $drone['service_subheading'];
        $service_price = $drone['service_price'];
        $html .= "<div class='col-lg-3'>";
        $html .= "<div class='service-box rounded-corner p-3 d-flex justify-content-between flex-column text-white h-100 $class'>";
        $html .= __heading(array(
            'heading' => $service_name,
            'tag'     => 'h3',
            'suffix'  => $service_subheading
        ));

        $html .= "<div class='row-services-spec-mobile d-lg-none mt-4'>";
        foreach ($specs as $key => $spec) {
            $html .= "<div class='row g-10px mb-10px'>";

            if ($spec != '_') {
                $html .= "<div class='col-8'>"; //specs-row-col
                $html .= "<div class='inner h-100 d-flex align-items-center'>"; //inner
                $html .= "<div class='icon-box me-3 text-accent'>";
                $html .= $SVG->$key();
                $html .= "</div>";
                $html .= __heading(array(
                    'heading' => ucwords($spec),
                    'class'   => _attribute('class', array('mb-0 text-primary')),
                    'tag'     => 'h5',
                ));
                $html .= "</div>"; //end-inner
                $html .= "</div>"; //end-specs-row-col

                $spec_services = array();
                foreach ($drone['service_features'] as $service_feature) {
                    $spec_services[$service_feature['_type']] = $service_feature['quantity'];
                }

                $html .= "<div class='col-4'>";
                $html .= "<div class='inner inner-specs-list  h-100 d-flex align-items-center justify-content-center'>"; //inner

                if (array_key_exists($key, $spec_services)) {
                    $quantity = $spec_services[$key];
                    $html .= "<div class='active d-flex align-items-center'> ";
                    if ($quantity && $quantity > 0) {
                        $html .= "<span class='qty ms-2 text-primary d-flex align-items-center'> ";
                        $html .= "<span class='fw-'medium'>$quantity</span>";
                        $html .= "</span>";
                    }
                    else {
                        $html .= $SVG->check();
                    }
                    $html .= "</div>";
                }
                else {
                    $html .= "<div class='not-active d-flex align-items-center'>";
                    $html .= $SVG->xmark();
                    $html .= "</div>";
                }

                $html .= "</div>";
                $html .= "</div>";
            }
            $html .= "</div>";
        }
        $html .= "</div>";



        $html .= "<div class='price-button mt-5'>";
        $html .= "<div class='price mb-3'>£$service_price <span>Excl. VAT</span></div>";
        $html .= __button(array(
            'button_type'       => 'custom',
            'button_text'       => 'Request Service',
            'button_url_custom' => '#hero',
            'button_style'      => $button_class,
        ));

        $html .= "</div>";

        $html .= "</div>";
        $html .= "</div>";
    }

    $html .= "</div>";

    foreach ($specs as $key => $spec) {
        if ($spec != '_') {
            $html .= "<div class='row g-10px d-none d-lg-flex'>"; //specs-row

            $html .= "<div class='col-3'>"; //specs-row-col
            $html .= "<div class='inner h-100 d-flex align-items-center'>"; //inner
            $html .= "<div class='icon-box me-3 text-accent'>";
            $html .= $SVG->$key();
            $html .= "</div>";
            $html .= __heading(array(
                'heading' => ucwords($spec),
                'class'   => _attribute('class', array('mb-0')),
                'tag'     => 'h5',
            ));
            $html .= "</div>"; //end-inner
            $html .= "</div>"; //end-specs-row-col

            foreach ($servicing_drones as $drone) {
                $spec_services = array();
                foreach ($drone['service_features'] as $service_feature) {
                    $spec_services[$service_feature['_type']] = $service_feature['quantity'];
                }

                $html .= "<div class='col-3'>";
                $html .= "<div class='inner inner-specs-list  h-100 d-flex align-items-center justify-content-center'>"; //inner

                if (array_key_exists($key, $spec_services)) {

                    $quantity = $spec_services[$key];

                    $html .= "<div class='active d-flex align-items-center'> ";
                    if ($quantity && $quantity > 0) {
                        $html .= "<span class='qty ms-2 text-primary d-flex align-items-center'> ";
                        $html .= "<span class='fw-'medium'>$quantity</span>";
                        $html .= "</span>";
                    }
                    else {
                        $html .= $SVG->check();
                    }
                    $html .= "</div>";
                }
                else {
                    $html .= "<div class='not-active d-flex align-items-center'>";
                    $html .= $SVG->xmark();
                    $html .= "</div>";
                }

                $html .= "</div>";
                $html .= "</div>";
            }



            $html .= "</div>"; //end-specs-row
        }
    }

    $html .= "<div class='row g-10px row-services  d-none d-lg-flex'>";
    $html .= "<div class='col-lg-3'>";
    $html .= "</div>";
    foreach ($servicing_drones as $key => $drone) {
        $position = $key % 3;

        if ($position == 0) {
            $class = 'bg-gray';
            $button_class = 'button-primary';
        }
        else if ($position == 1) {
            $class = 'bg-accent';
            $button_class = 'button-primary';
        }
        else if ($position == 2) {
            $class = 'bg-black';
            $button_class = 'button-accent';
        }

        $html .= "<div class='col-lg-3'>";
        $html .= "<div class='service-box rounded-corner p-3 d-flex justify-content-between flex-column text-white h-100 $class'>";

        $html .= __button(array(
            'button_type'       => 'custom',
            'button_text'       => 'Request Service',
            'button_url_custom' => '#hero',
            'button_style'      => $button_class,
        ));


        $html .= "</div>";
        $html .= "</div>";
    }
    $html .= "</div>";



    $html .= "</div>"; //end products-specifications

    $html .= "</div>";


    return $html;
}


function __three_year_servicing_plans()
{
    $SVG = new SVG;
    $servicing_heading = get__theme_option('servicing_3_year_heading');
    $servicing_description = get__theme_option('servicing_3_year_description');
    $servicing_drones = get__theme_option('servicing_3_year_drones');

    $specs = array();

    foreach ($servicing_drones as $servicing_drone) {
        $specs[] = $servicing_drone['_type'];
        $plan_features = $servicing_drone['plan_features'];
        foreach ($plan_features as $plan_feature) {
            $specs[$plan_feature['_type']] = $plan_feature['_type'];
        }
    }
    $html = "<div class='product-compare drone-servicing  drone-plans' >";
    $html .= "<div class='comparison products-specifications products-specifications-v2'>"; //products-specifications
    $html .= "<div class='row g-10px row-plans row-plans-main'>";
    $html .= "<div class='col-lg-3'>";
    $html .= __heading(array(
        'heading' => $servicing_heading,
        'class'   => _attribute('class', array('mb-3')),
    ));

    $html .= __description(array(
        'description' => $servicing_description,
        'class'       => _attribute('class', array('description-box')),
    ));


    $html .= '<div class="filter-style-1"><div class="filter-box bg-light rounded-corner"> <div class="row"> <div class="col-6"> <input name="drone_size" value="small-drone" type="radio" id="small-drone" checked> <label class="rounded-corner w-100 text-center" for="small-drone">Small Drone</label> </div> <div class="col-6"> <input name="drone_size" value="large-drone" type="radio" id="large-drone"> <label class="rounded-corner w-100 text-center" for="large-drone">Large Drone</label> </div> </div> </div></div>';

    $html .= "</div>";


    foreach ($servicing_drones as $key => $drone) {
        $position = $key % 3;

        if ($position == 0) {
            $class = 'bg-gray';
            $button_class = 'button-primary';
        }
        else if ($position == 1) {
            $class = 'bg-accent';
            $button_class = 'button-primary';
        }
        else if ($position == 2) {
            $class = 'bg-black';
            $button_class = 'button-accent';
        }


        $plan_name = $drone['plan_name'];
        $plan_subheading = $drone['plan_subheading'];
        $plan_price = $drone['plan_price'];
        $plan_price_large = $drone['plan_price_large'];
        $plan_description = $drone['plan_description'];
        $plan_features = $drone['plan_features'];
        if ($plan_features) {
            $class .= ' justify-content-between';
        }
        else {
            $class .= ' justify-content-start';
        }
        $html .= "<div class='col-lg-3'>";
        $html .= "<div class='plan-box rounded-corner p-3 d-flex  flex-column text-white h-100 $class'>";
        $html .= __heading(array(
            'heading' => $plan_name,
            'tag'     => 'h3',
            'suffix'  => $plan_subheading
        ));

        if ($plan_features) {
            $html .= "<div class='row-plans-spec-mobile d-lg-none mt-4'>";
            foreach ($specs as $key => $spec) {
                $html .= "<div class='row g-10px mb-10px'>";

                if ($spec != '_') {
                    $html .= "<div class='col-8'>"; //specs-row-col
                    $html .= "<div class='inner h-100 d-flex align-items-center'>"; //inner
                    $html .= "<div class='icon-box me-3 text-accent'>";
                    $html .= $SVG->$key();
                    $html .= "</div>";
                    $html .= __heading(array(
                        'heading' => str_replace('_', ' ', ucwords($spec)),
                        'class'   => _attribute('class', array('mb-0 text-primary')),
                        'tag'     => 'h5',
                    ));
                    $html .= "</div>"; //end-inner
                    $html .= "</div>"; //end-specs-row-col

                    $spec_plans = array();
                    foreach ($plan_features as $plan_feature) {
                        $spec_plans[$plan_feature['_type']] = $plan_feature['custom_text'];
                    }

                    $html .= "<div class='col-4'>";
                    $html .= "<div class='inner inner-specs-list  h-100 d-flex align-items-center justify-content-center'>"; //inner

                    if (array_key_exists($key, $spec_plans)) {
                        $custom_text = $spec_plans[$key];
                        $html .= "<div class='active d-flex align-items-center'> ";
                        if ($custom_text) {
                            $html .= "<span class='qty ms-2 text-primary d-flex align-items-center'> ";
                            $html .= "<span class='fw-'medium'>$custom_text</span>";
                            $html .= "</span>";
                        }
                        else {
                            $html .= $SVG->check();
                        }
                        $html .= "</div>";
                    }
                    else {
                        $html .= "<div class='not-active d-flex align-items-center'>";
                        $html .= $SVG->xmark();
                        $html .= "</div>";
                    }



                    $html .= "</div>";
                    $html .= "</div>";
                }
                $html .= "</div>";
            }
            $html .= "</div>";
        }



        $html .= "<div class='price-button mt-5'>";
        $html .= "<div class='price mb-3 small-drone-price'>£$plan_price <span>Excl. VAT</span></div>";
        $html .= "<div class='price mb-3 large-drone-price'>£$plan_price_large <span>Excl. VAT</span></div>";
        if ($plan_description) {
            $html .= __description(array(
                'description' => $plan_description,
                'class'       => _attribute('class', array('description-box plan-description')),
            ));
        }
        $html .= __button(array(
            'button_type'       => 'custom',
            'button_text'       => 'Request Service',
            'button_url_custom' => '#hero',
            'button_style'      => $button_class,
        ));




        $html .= "</div>";


        $html .= "</div>";



        $html .= "</div>";
    }

    $html .= "</div>";

    foreach ($specs as $key => $spec) {
        if ($spec != '_') {
            $html .= "<div class='row g-10px d-none d-lg-flex'>"; //specs-row

            $html .= "<div class='col-3'>"; //specs-row-col
            $html .= "<div class='inner h-100 d-flex align-items-center'>"; //inner
            $html .= "<div class='icon-box me-3 text-accent'>";
            $html .= $SVG->$key();
            $html .= "</div>";
            $html .= __heading(array(
                'heading' => str_replace('_', ' ', ucwords($spec)),
                'class'   => _attribute('class', array('mb-0')),
                'tag'     => 'h5',
            ));
            $html .= "</div>"; //end-inner
            $html .= "</div>"; //end-specs-row-col

            foreach ($servicing_drones as $drone) {
                $spec_plans = array();
                foreach ($drone['plan_features'] as $plan_feature) {
                    $spec_plans[$plan_feature['_type']] = $plan_feature['custom_text'];
                }

                $html .= "<div class='col-3'>";
                $html .= "<div class='inner inner-specs-list  h-100 d-flex align-items-center justify-content-center'>"; //inner

                if (array_key_exists($key, $spec_plans)) {

                    $custom_text = $spec_plans[$key];

                    $html .= "<div class='active d-flex align-items-center'> ";
                    if ($custom_text && $custom_text > 0) {
                        $html .= "<span class='qty ms-2 text-primary d-flex align-items-center'> ";
                        $html .= "<span class='fw-'medium'>$custom_text</span>";
                        $html .= "</span>";
                    }
                    else {
                        $html .= $SVG->check();
                    }
                    $html .= "</div>";
                }
                else {
                    $html .= "<div class='not-active d-flex align-items-center'>";
                    $html .= $SVG->xmark();
                    $html .= "</div>";
                }

                $html .= "</div>";
                $html .= "</div>";
            }



            $html .= "</div>"; //end-specs-row
        }
    }

    $html .= "<div class='row g-10px row-plans  d-none d-lg-flex'>";
    $html .= "<div class='col-lg-3'>";
    $html .= "</div>";
    foreach ($servicing_drones as $key => $drone) {
        $position = $key % 3;

        if ($position == 0) {
            $class = 'bg-gray';
            $button_class = 'button-primary';
        }
        else if ($position == 1) {
            $class = 'bg-accent';
            $button_class = 'button-primary';
        }
        else if ($position == 2) {
            $class = 'bg-black';
            $button_class = 'button-accent';
        }

        $html .= "<div class='col-lg-3'>";
        $html .= "<div class='plan-box rounded-corner p-3 d-flex justify-content-between flex-column text-white h-100 $class'>";

        $html .= __button(array(
            'button_type'       => 'custom',
            'button_text'       => 'Request Service',
            'button_url_custom' => '#hero',
            'button_style'      => $button_class,
        ));


        $html .= "</div>";
        $html .= "</div>";
    }
    $html .= "</div>";



    $html .= "</div>"; //end products-specifications

    $html .= "</div>";


    return $html;
}

function __remote_support()
{
    $remote_support_heading = get__theme_option('remote_support_heading');
    $remote_support_description = get__theme_option('remote_support_description');
    $remote_supports = get__theme_option('remote_supporrt');

    $html = "<div class='remote-supports'>";
    $html .= __heading(array(
        'heading' => $remote_support_heading,
        'class'   => _attribute('class', array('text-center')),
        'tag'     => 'h2',
    ));
    $html .= __description(array(
        'description' => $remote_support_description,
        'class'       => _attribute('class', array('description-box text-center mb-4')),
    ));
    $html .= "<div class='row g-4'>";

    foreach ($remote_supports as $remote_support) {
        $heading = $remote_support['remote_support_heading'];
        $description = $remote_support['remote_support_description'];
        $price = $remote_support['remote_support_price'];
        $icon = $remote_support['remote_support_icon'];
        $icon_text = $remote_support['remote_support_icon_text'];
        $html .= "<div class='col-lg-6'>";
        $html .= "<div class='support-box rounded-corner h-100 px-4 pb-4 content-margin text-white text-center'>";
        $html .= "<div class='support-box-icon d-inline-flex align-items-center'>";
        $html .= __icon(array(
            'id'    => $icon,
            'class' => _attribute('class', array('me-3 text-accent'))
        ));
        $html .= "<span>$icon_text</span>";

        $html .= "</div>";

        $html .= __heading(array(
            'heading' => $heading,
            'class'   => _attribute('class', array('mb-0 text-center')),
            'tag'     => 'h3',
        ));
        $html .= __description(array(
            'description' => $description,
            'class'       => _attribute('class', array('description-box text-center')),
        ));

        $html .= "<div class='price mb-3'>£$price <span>Excl. VAT</span></div>";


        $html .= __button(array(
            'button_type'       => 'custom',
            'button_text'       => 'Request Service',
            'button_url_custom' => '#hero',
            'button_style'      => 'button-primary',
        ));

        $html .= "</div>";
        $html .= "</div>";
    }

    $html .= "</div>";

    return $html;
}

remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);

/**
 * @snippet       Remove Sorting Dropdown @ WooCommerce Shop & Archives
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 7
 * @community     https://businessbloomer.com/club/
 */

remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);


function woocommerce_disable_shop_page()
{
    global $post;
    if (is_shop()):
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
    endif;
}
add_action('wp', 'woocommerce_disable_shop_page');


add_filter('woocommerce_gallery_thumbnail_size', function ($size) {
    return 'medium';
});