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
        }
    } else if (is_product()) {
        echo ___hero_modules();
        echo __product_specifications();


        if (get_the_ID() != 271236 && has_term(32, 'product_cat')) {
            echo do_shortcode('[layouts id=299719]');
        }

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
        echo __linked_products(__get_assoc_post_ids($drones), __('All Drones', 'coptrz-theme'), get_term_link(27, 'product_cat'), 'swiper-drones', __('Drones', 'coptrz-theme'));
    }

    if ($related_training) {

        echo __linked_products(__get_assoc_post_ids($related_training), __('All Trainings', 'coptrz-theme'), get_term_link(32, 'product_cat'), 'swiper-payloads', __('Related Training', 'coptrz-theme'));
    }

    if ($softwares) {
        echo __linked_products(__get_assoc_post_ids($softwares), __('All Softwares', 'coptrz-theme'), get_term_link(776, 'product_cat'), 'swiper-softwares', __('Software', 'coptrz-theme'));
    }

    if ($compatible_payloads) {
        echo __linked_products(__get_assoc_post_ids($compatible_payloads), __('All Payloads', 'coptrz-theme'), get_term_link(29, 'product_cat'), 'swiper-payloads', __('Compatible Payloads', 'coptrz-theme'));
    }

    if ($accessories) {
        echo __linked_products(__get_assoc_post_ids($accessories), __('All Accessories', 'coptrz-theme'), get_term_link(30, 'product_cat'), 'swiper-accessories', __('Accessories', 'coptrz-theme'));
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
};
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


/**
 * Output radio buttons on WooCommerce variations.
 */

function custom_product_variation()
{
    global $product;

    $children = $product->get_children();
    $main_thumbnail = get_post_thumbnail_id($product->get_id());

    if ($children) {

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
            if ($description) {
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
            }
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



function request_info()
{
    $id = apply_filters('wpml_object_id', 299743, 'post');
    $html = '<div class="button-box button-bordered request-info">';
    $html .= "<a class='rounded-10px ' data-bs-toggle='modal' data-bs-target='#modal-$id' target='_self'>";
    $html .= __('Request Info', 'coptrz-theme');
    $html .= '</a>';
    $html .= '</div>';
    echo $html;
}
add_action('woocommerce_after_add_to_cart_button', 'request_info', 20);

function __product_compare($id)
{
    $SVG = new SVG;
    $products = get__post_meta_by_id($id, 'products');
    $product_attributes = get__post_meta_by_id($id, 'product_attributes');

    $specs = array();

    foreach ($products as $product) {
        $pa_specifications = get_the_terms($product['id'], 'pa_specifications');
        if ($pa_specifications) {
            foreach ($pa_specifications as $specification) {
                $specs[$specification->term_id] = $specification->name;
            }
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
            if ($taxonomy_details) {
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
        } else {
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
            } else {

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
            } else {
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
            } else {
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
        } else {
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

        $data_encode = _single_product_data($id);

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
            $html .= "<div class='button-box button-bordered'><a class='product-btn' href='$permalink'> <span class='product-data d-none'>$data_encode</span> View Product</a></div>";
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
    } else {
        $html = "<div class='related-products-slider'>";
    }

    $html .= "<h2 class='text-center px-20px'>$title</h2>";

    if ($is_slider) {
        $html .= "<div class='container extend-right'>"; //end-container
        $html .= "<div class='swiper-holder'>"; //swiper-holder
        $html .= "<div class='swiper swiper-linked-products' id='$id'>"; //swiper
        $html .= "<div class='swiper-wrapper'>"; //swiper-wrapper
    } else {
        $html .= "<div class='container'>"; //container
        $html .= "<div class='row g-4'>"; //row
    }

    foreach ($field as $product_id) {

        if ($is_slider) {
            $html .= "<div class='swiper-slide'>"; //swiper-slide
        } else {
            $html .= "<div class='col-lg-3 col-md-6'>"; //col
        }
        if ($shorcode == false) {
            $html .= _product_grid_display($product_id);
        } else {
            $html .= "[product_grid_display id='$product_id']";
        }
        $html .= '</div>'; //end-swiper-slide // col

    }
    if ($is_slider) {
        $html .= '</div>'; //end-swiper-wrapper
        $html .= '</div>'; //end-swiper
        $html .= '</div>'; //end-swiper-holder
        $html .= '</div>'; //end-container
    } else {
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
    } else {
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



function custom_product_variation_training($product_id, $delivery_method = 'online-self-paced', $location = false, $post_type_key)
{
    $SVG = new SVG;
    $product = wc_get_product($product_id);
    $children = $product->get_children();
    $data_encode = _single_product_data($product_id);

    $child_array = [];
    foreach ($children as $child) {
        $variation = wc_get_product($child);
        $product_attribute = $variation->get_attributes();
        $product_attribute_array = [];
        foreach ($product_attribute as $key => $attr) {
            $product_attribute_array[$key] = $attr;
        }

        if ($product_attribute_array['pa_delivery-methods'] == $delivery_method) {
            $date = explode(':', $product_attribute_array['date']);
            $date_start = $date[0];
            $date_end = $date[1];
            $month = $date[2];
            $year = $date[3];
            $date_format = $date_start . '-' . $month . '-' . $year;

            $child_array[] = array(
                'product_id'             => $child,
                'product_attributes'     => $variation->get_attributes(),
                'sku'                    => $variation->get_sku(),
                'price'                  => $variation->get_price_html(),
                'stock_status_variation' => $variation->get_stock_status(),
                'location'               => $product_attribute_array['pa_location'],
                'datetime'               => $date_format,
                'date_start'               => $date_start,
                'date_end'               => $date_end,
                'month'               => $month,
                'year'               => $year,
            );
        }
    }

    usort($child_array, 'date_compare_latest');

    if ($location) {
        $child_array_val = array_filter($child_array, function ($var) use ($location) {
            return ($var['location'] == $location);
        });
    } else {
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
                $date_start = $child['date_start'];
                $date_end = $child['date_end'];
                $month = $child['month'];
                $year = $child['year'];

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
                            if (current_user_can('administrator')) {
                                $html .= $month;
                            } else {
                                $html .= $product_attribute_array['date'];
                            }
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

                $html .= '<div>';
                $html .= $price;
                $html .= '</div>';

                $html .= '</div>';

                $html .= "<div class='button-box button-bordered mt-3'>";


                if ($post_type_key == 'product') {
                    $html .= "<a href='?add-to-cart=$product_id' data-quantity='1' class='product-btn button product_type_simple add_to_cart_button ajax_add_to_cart' data-product_id='$product_id' data-product_sku='$sku' rel='nofollow'><span class='product-data d-none'>$data_encode</span> Add to basket</a>";
                } else {
                    $basket_url =   wc_get_cart_url();
                    $html .= "<a class='w-100' href='$basket_url?add-to-cart=$product_id'>Buy now</a>";
                }



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
    } else {
        $html .= '<h2 class="my-5">No training found.</h2>';
    }
    $html .= '</div>';

    echo $html;
}


function training_template($product_id = 'default')
{
    ob_start();
    if ($product_id == 'default') {
        $id = get_the_ID();
        global $product;
    } else {
        $id = $product_id;
        $product = wc_get_product($product_id);
    }
?>
    <section class="training-product md-padding-top md-padding-bottom border-top-default" id="Book-Course">
        <input type="hidden" name="product_id" value="<?= $id ?>">
        <input type="hidden" name="post_type_key" value="<?= get_post_type() ?>">
        <div class="container">
            <h2 class="text-center">Book a GVC <br> Training Course</h2>
            <div class="post-archive-header">
                <div class="container">
                    <div class="inner border-bottom-default sm-padding-bottom sm-margin-bottom">
                        <div class="row g-3 justify-content-between align-items-end">
                            <div class="col-auto">
                                <div class="filter-style-1">
                                    <p class="fw-medium medium-text">Select delivery method:</p>
                                    <div class="filter-box bg-light rounded-corner">
                                        <div class="row">
                                            <div class="col-auto">
                                                <input name="delivery_method" value="online-self-paced" type="radio" id="online" checked>
                                                <label class="rounded-corner trigger-training-ajax" for="online">Online Self-paced</label>
                                            </div>
                                            <div class="col-auto">
                                                <input name="delivery_method" value="classroom" type="radio" id="classroom">
                                                <label class="rounded-corner trigger-training-ajax" for="classroom">Classroom</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="row g-3 align-items-center">
                                    <div class="col-auto col-location d-none">
                                        <?php
                                        $children = $product->get_children();
                                        $locations = [];
                                        foreach ($children as $child) {
                                            $variation = wc_get_product($child);
                                            $product_attribute = $variation->get_attributes();
                                            foreach ($product_attribute as $key => $attr) {
                                                if ($key == 'pa_location') {
                                                    $locations[] = $attr;
                                                }
                                            }
                                        }
                                        $locations = array_unique($locations);
                                        ?>

                                        <select name="location" class="trigger-training-ajax-location">
                                            <option value="">Location: All</option>
                                            <?php foreach ($locations as $location) { ?>
                                                <?php if ($location != 'online') { ?>
                                                    <option value="<?= $location ?>" class="text-capitalize">Location: <?= $location ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="training-list ajax-loading">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="loading-results p-5 text-center"> <svg class="spin" xmlns="http://www.w3.org/2000/svg" id="Group_27" data-name="Group 27" width="123" height="123" viewBox="0 0 123 123">
                                <g id="Ellipse_2" data-name="Ellipse 2" fill="none" stroke="#2DA1FF" stroke-width="2">
                                    <circle cx="61.5" cy="61.5" r="61.5" stroke="none"></circle>
                                    <circle cx="61.5" cy="61.5" r="60.5" fill="none"></circle>
                                </g>
                                <circle id="Ellipse_8" data-name="Ellipse 8" cx="6.5" cy="6.5" r="6.5" transform="translate(30 55)" fill="none" stroke="#2DA1FF" stroke-width="3"></circle>
                                <circle id="Ellipse_9" data-name="Ellipse 9" cx="6.5" cy="6.5" r="6.5" transform="translate(55 55)" fill="none" stroke="#2DA1FF" stroke-width="3"></circle>
                                <circle id="Ellipse_10" data-name="Ellipse 10" cx="6.5" cy="6.5" r="6.5" transform="translate(80 55)" fill="none" stroke="#2DA1FF" stroke-width="3"></circle>
                            </svg></div>
                        <div id="results">
                            <?= custom_product_variation_training($id, 'online-self-paced', false, get_post_type()) ?>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <?php $SVG = new SVG; ?>
                        <div class="image-box training-map-holder position-relative">
                            <?= $SVG->uk() ?>
                            <span title="Edinburgh" class="trigger-location-change" id="edinburgh" value="edinburgh"><?= $SVG->location() ?><div class='pulse'></div></span>
                            <span title="Leeds" class="trigger-location-change" id="leeds" value="leeds"><?= $SVG->location() ?><div class='pulse'></div></span>
                            <span title="Rugby" class="trigger-location-change" id="rugby" value="rugby"><?= $SVG->location() ?><div class='pulse'></div></span>
                            <span title="Cardiff" class="trigger-location-change" id="cardiff" value="cardiff"><?= $SVG->location() ?><div class='pulse'></div></span>
                            <span title="Kent" class="trigger-location-change" id="kent" value="kent"><?= $SVG->location() ?><div class='pulse'></div></span>
                            <span title="Hampshire" class="trigger-location-change" id="hampshire" value="hampshire"><?= $SVG->location() ?><div class='pulse'></div></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
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
        } else if ($position == 1) {
            $class = 'bg-accent';
            $button_class = 'button-primary';
        } else if ($position == 2) {
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
                    } else {
                        $html .= $SVG->check();
                    }
                    $html .= "</div>";
                } else {
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
                    } else {
                        $html .= $SVG->check();
                    }
                    $html .= "</div>";
                } else {
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
        } else if ($position == 1) {
            $class = 'bg-accent';
            $button_class = 'button-primary';
        } else if ($position == 2) {
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
        } else if ($position == 1) {
            $class = 'bg-accent';
            $button_class = 'button-primary';
        } else if ($position == 2) {
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
        } else {
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
                        } else {
                            $html .= $SVG->check();
                        }
                        $html .= "</div>";
                    } else {
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
                    } else {
                        $html .= $SVG->check();
                    }
                    $html .= "</div>";
                } else {
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
        } else if ($position == 1) {
            $class = 'bg-accent';
            $button_class = 'button-primary';
        } else if ($position == 2) {
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


function product_guides()
{
    $product_guide = get__post_meta('product_guide');
    if ($product_guide) {
        $title = get_the_title();
        $pdf_url = wp_get_attachment_url($product_guide);
        $html = "<div class='download-guide mt-5 bg-dark rounded-corner p-4'>";
        $html .= "<h4 class='text-white mb-4'>Download $title guide.</h4>";
        $html .= "<div class='button-box button-accent request-info'><a class='rounded-10px'href='$pdf_url' target='_blank'>Download</a></div>";
        $html .= "</div>";
        echo $html;
    }
}



/**
 * @snippet       Disable Free Shipping if Cart has Shipping Class
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @testedwith    WooCommerce 6
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */

add_filter('woocommerce_package_rates', 'bbloomer_hide_free_shipping_for_shipping_class', 9999, 2);

function bbloomer_hide_free_shipping_for_shipping_class($rates, $package)
{
    $in_cart = false;
    $shipping_class_target = array(
        1220,
        1221,
        1222,
        761
    );
    $free_shipping = false;
    $free_shipping_val = '';

    $product_ids = array();

    foreach (WC()->cart->get_cart_contents() as $key => $values) {

        $type = $values['data']->get_type();

        if ($type == 'variation') {
            $id = $values['data']->get_parent_id();
        } else {
            $id = $values['data']->get_id();
        }

        $product_ids[] = $id;

        if (has_term(array(32, 789, 776), 'product_cat', $id)) {
            $free_shipping_val .= 'true';
        } else {
            $free_shipping_meta = get_post_meta($id, '_free_shipping', true);


            if ($free_shipping_meta) {
                $free_shipping_val .= 'true';
            } else {
                $free_shipping_val .= 'false';
            }
        }
    }

    foreach (WC()->cart->get_cart_contents() as $key => $values) {
        $type = $values['data']->get_type();
        if ($type == 'variation') {
            $id = $values['data']->get_parent_id();
        } else {
            $id = $values['data']->get_id();
        }
        $_free_shipping_product_id = get_post_meta($id, '_free_shipping_product_id', true);
        if ($_free_shipping_product_id) {
            if (in_array($_free_shipping_product_id, $product_ids)) {
                $free_shipping_val .= 'true';
            } else {
                $free_shipping_val .= 'false';
            }
        }

        if (in_array($values['data']->get_shipping_class_id(), $shipping_class_target)) {
            $in_cart = true;
            break;
        }
    }


    if (str_contains($free_shipping_val, 'false')) {
        $free_shipping = false;
    } else {
        $free_shipping = true;
    }


    if ($free_shipping == true) {
        unset($rates['flat_rate:12']);
    } else {
        if ($in_cart) {
            unset($rates['free_shipping:10']);
        }
    }
    // Only unset rates if free_shipping is available
    if (isset($rates['free_shipping:10']) && !isset($rates['flat_rate:12'])) {
        unset($rates['local_pickup:13']);
    }

    if (isset($rates['flat_rate:12'])) {
        unset($rates['free_shipping:10']);
    }
    return $rates;
}



add_filter('woocommerce_get_price_suffix', 'custom_price_suffix', 999, 4);
function custom_price_suffix($html, $product, $price, $qty)
{
    $type = $product->get_type();

    if ($type == 'simple') {
        $vat_inclusive = get__post_meta_by_id($product->get_id(), 'vat_inclusive');
    } else {
        $vat_inclusive = get__post_meta_by_id($product->get_parent_id(), 'vat_inclusive');
    }

    if ($vat_inclusive) {
        return  ' ' .  __('Incl. VAT', 'woocommerce');
    } else
        return $html;
}
add_action('woocommerce_before_calculate_totals', 'rudr_custom_price_refresh');

function rudr_custom_price_refresh($cart_object)
{

    $check_id = 371549;
    $product_id_to_add = 61196;
    $product_ids = array();
    foreach ($cart_object->get_cart() as $item) {
        $product_ids[] = $item['product_id'];
    }
    foreach ($cart_object->get_cart() as $item) {
        if (in_array($check_id, $product_ids)) {
            WC()->cart->add_to_cart(14, 1, 0, array());
            if (array_key_exists('custom_price', $item)) {
                $item['data']->set_price($item['custom_price']);
            }
            if (!in_array($product_id_to_add, $product_ids)) {
                $product = wc_get_product($product_id_to_add);
                WC()->cart->add_to_cart($product_id_to_add, 1, 0, array(), array('custom_price' => 0, 'original_price' => $product->get_price()));
            }
        } else {
            if (array_key_exists('original_price', $item)) {
                $item['data']->set_price($item['original_price']);
            }
        }
    }
}



function _single_product_data($product_id)
{
    $product = wc_get_product($product_id);

    $pa_brands = $product->get_attribute('pa_brands');
    $category = get_the_terms($product->get_id(), 'product_cat');
    if ($product->get_price()) {
        $data['price']    = $product->get_price();
    }
    $data['sku']      = $product->get_sku();
    $data['name']     = $product->get_name();
    if ($pa_brands) {
        $data['brand']    = $pa_brands;
    }
    $data['category'] = $category[0]->name;

    return json_encode($data);
}


function ga4()
{
    if (is_product_taxonomy() || is_shop()) {
        if (is_product_taxonomy()) {
            $term_name = get_queried_object()->name;
            $term_id = get_queried_object()->term_id;
        } else {
            $term_name = 'Shop';
            $term_id = 'Shop';
        }
    ?>
        <script>
            jQuery('.product-btn').click(function(e) {
                $data = jQuery(this).find('.product-data').text();
                productObj = JSON.parse($data);
                ga4_select_item(productObj);
            });

            function ga4_select_item(productObj) {
                window.dataLayer = window.dataLayer || [];
                dataLayer.push({
                    event: "select_item",
                    ecommerce: {
                        item_list_name: productObj.category,
                        items: [{
                            item_id: productObj.sku, // This should be a unique Identifier
                            item_name: productObj.name,
                            index: 0, // Item Index - index starts at 0, 1st product = 0
                            item_brand: productObj.brand, // Product Brand
                            item_category: productObj.category,
                            price: productObj.price,
                            quantity: 1
                        }]
                    }
                });
            }
        </script>
        <script>
            jQuery(document).ready(function() {
                ga4_view_item_list();
            });

            function ga4_view_item_list() {
                $products = jQuery('li.product');
                items = [];
                index = 0;
                $products.each(function(index, element) {
                    $data = jQuery(this).find('.product-data').text();
                    productObjs = JSON.parse($data);
                    items.push({
                        item_id: productObjs.sku, // This should be a unique Identifier
                        item_name: productObjs.name,
                        index: index, // Item Index - index starts at 0, 1st product = 0
                        item_brand: productObjs.brand, // Product Brand
                        item_category: productObjs.category,
                        price: productObjs.price,
                        quantity: 1
                    });
                    index++;
                });

                window.dataLayer = window.dataLayer || [];
                dataLayer.push({
                    event: "view_item_list",
                    ecommerce: {
                        item_list_id: '<?= $term_id ?>',
                        item_list_name: '<?= $term_name ?>',
                        items: items
                    }
                });
            }
        </script>
    <?php
    } else if (is_product()) {
    ?>
        <script>
            jQuery('body').on('added_to_cart', function() {
                ga4_add_to_cart_single();
                console.log('product-added-to-cart');
            });

            function ga4_add_to_cart_single() {
                quantity = jQuery('input[name="quantity"]').val();
                if (quantity) {
                    quantity_val = quantity;
                } else {
                    quantity_val = 1;
                }
                $data = jQuery('.main-product-data.product-data').text();
                productObj = JSON.parse($data);
                window.dataLayer = window.dataLayer || [];
                dataLayer.push({
                    event: "add_to_cart",
                    ecommerce: {
                        item_list_name: productObj.category,
                        items: [{
                            item_id: productObj.sku, // This should be a unique Identifier
                            item_name: productObj.name,
                            index: 0, // Item Index - index starts at 0, 1st product = 0
                            item_brand: productObj.brand, // Product Brand
                            item_category: productObj.category,
                            price: productObj.price,
                            quantity: parseInt(quantity_val)
                        }]
                    }
                });
            }
        </script>
        <script>
            jQuery(document).ready(function() {
                $data = jQuery('.product-data').text();
                productObj = JSON.parse($data);
                ga4_view_item(productObj);
            });

            function ga4_view_item(productObj) {
                window.dataLayer = window.dataLayer || [];
                dataLayer.push({
                    event: "view_item",
                    ecommerce: {
                        item_list_name: productObj.category,
                        items: [{
                            item_id: productObj.sku, // This should be a unique Identifier
                            item_name: productObj.name,
                            index: 0, // Item Index - index starts at 0, 1st product = 0
                            item_brand: productObj.brand, // Product Brand
                            item_category: productObj.category,
                            price: productObj.price,
                            quantity: 1
                        }]
                    }
                });
            }
        </script>

    <?php
    } else if (is_checkout()  && !(is_wc_endpoint_url('order-pay') || is_wc_endpoint_url('order-received'))) {
    ?>
        <script>
            jQuery(document).ready(function() {
                ga4_begin_checkout();
            });

            function ga4_begin_checkout() {
                window.dataLayer = window.dataLayer || [];
                dataLayer.push({
                    event: "begin_checkout",
                    ecommerce: {
                        items: <?= json_encode(_cart_data()) ?>
                    }
                });
            }
        </script>
        <?php
    } else if (is_wc_endpoint_url('order-received')) {
        global $wp;

        $order_id = absint($wp->query_vars['order-received']);

        if (!get_post_meta($order_id, '_thankyou_action_done', true)) {
            $order    = wc_get_order($order_id);
            $key = 0;
            $items = array();
            $transaction_id = $order_id;
            $value = round($order->get_total(), 2);
            $tax = round($order->get_total_tax(), 2);
            $coupons = $order->get_coupon_codes();

            foreach ($order->get_items() as $item_id => $item) {
                $product = $item->get_product();
                $pa_brands = $product->get_attribute('pa_brands');
                $get_category = get_the_terms($product->get_id(), 'product_cat');
                $category = $get_category ? $get_category[0]->name : '';
                $items_val = array();

                $items_val['item_id']       = $product->get_sku();
                $items_val['item_name']     = $product->get_name();
                $items_val['index']         = $key;
                $items_val['item_brand']    = $pa_brands;
                $items_val['item_category'] = $category;
                $items_val['price']         = round($item->get_subtotal(), 2);
                $items_val['quantity']      = $product->get_sku();


                if ($coupons) {
                    if (count($coupons) == 1) {
                        $items_val['coupon'] = $coupons[0];
                    } else {
                        $items_val['coupon'] = json_encode($coupons);
                    }
                }
                $items[] = $items_val;
                $key++;
            }
        ?>
            <script>
                console.log('<?= get_post_meta($order_id, '_thankyou_action_done', true)  ?>');
                console.log('<?= $transaction_id ?>');
                console.log('<?= $value ?>');
                console.log('<?= $tax ?>');
                console.log('<?= json_encode($items) ?>');

                <?php if ($coupons) { ?>
                    <?php if (count($coupons) == 1) { ?>
                        var coupon = '<?= $coupons[0] ?>'
                    <?php } else { ?>
                        var coupon = <?= json_encode($coupons) ?>,
                        <?php } ?>
                    <?php } else { ?>
                        var coupon = '';
                    <?php } ?>
                    window.dataLayer = window.dataLayer || [];
                    dataLayer.push({
                        event: "purchase",
                        ecommerce: {
                            transaction_id: '<?= $transaction_id ?>', // This should be a unique ID and only should be used once with every purchase.
                            value: <?= $value ?>, // Total value of product after +Tax, -Discount, +Shipping,
                            tax: <?= $tax ?>,
                            currency: "GBP",
                            coupon: coupon,
                            items: <?= json_encode($items) ?>
                        }
                    });
            </script>
            <script>
                <?php if ($coupons) { ?>
                    <?php if (count($coupons) == 1) { ?>
                        var coupon = '<?= $coupons[0] ?>'
                    <?php } else { ?>
                        var coupon = <?= json_encode($coupons) ?>,
                        <?php } ?>
                    <?php } else { ?>
                        var coupon = '';
                    <?php } ?>
                    window.dataLayer = window.dataLayer || [];
                    dataLayer.push({
                        event: "add_payment_info",
                        ecommerce: {
                            currency: "GBP",
                            value: <?= $value ?>, // Total value of product after +Tax, -Discount, +Shipping,
                            coupon: coupon,
                            payment_type: "<?= $order->get_payment_method_title() ?>",
                            items: <?= json_encode($items) ?>

                        }
                    });
            </script>
<?php
            update_post_meta($order_id, '_thankyou_action_done', true);
        }
    }
}


add_action('wp_footer', 'ga4');


function _cart_data()
{
    $data = array();
    $key = 0;
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

        if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
            $pa_brands = $_product->get_attribute('pa_brands');
            $category = get_the_terms($_product->get_id(), 'product_cat');
            $data[] = array(
                'item_id' => $_product->get_sku(),
                'item_name' => $_product->get_name(),
                'index' => $key,
                'item_brand' => $pa_brands,
                'item_category' => $category[0]->name,
                'price' => _price_format(WC()->cart->get_product_subtotal($_product, $cart_item['quantity'])),
                'quantity' => intval($cart_item['quantity'])
            );
        }
        $key++;
    }

    return $data;
}
