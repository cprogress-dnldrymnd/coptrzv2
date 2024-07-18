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
            echo do_shortcode(___sections('sections', $product_category_page));
        }
    } else if (is_product()) {
        echo ___hero_modules();
        echo __product_specifications();
        echo do_shortcode(___sections('sections', get_the_ID()));
    }
}

add_action('woocommerce_before_main_content', 'action_woocommerce_before_main_content');

function action_woocommerce_after_single_product_summary()
{
    $single_product_content_after = get__post_meta('single_product_content_after');
    $compatible_payloads = get_post_meta(get_the_ID(), 'compatible_payloads', true);
    $accessories = get_post_meta(get_the_ID(), 'accessories', true);

    echo do_shortcode(___sections('sections_after_main', get_the_ID()));

    if ($compatible_payloads) {
        echo __linked_products($compatible_payloads, 'All Payloads', '#', 'swiper-payloads', 'Compatible Payloads');
    }

    if ($compatible_payloads) {
        echo __linked_products($accessories, 'All Accessories', '#', 'swiper-accessories', 'Accessories');
    }
}

add_action('woocommerce_after_single_product_summary', 'action_woocommerce_after_single_product_summary');

function action_woocommerce_after_single_product()
{
    $related_guides = get__post_meta('related_guides');

    if ($related_guides) {
        $data = array(
            'col' => false,
            'featured' => false,
            'style' => 'style-1',
            'taxonomy' => 'guides_category',
            'elements' => array('image', 'category', 'title', 'excerpt', 'button'),
        );
        echo do_shortcode(__related_posts($related_guides,  $data, 'Related Guides'));
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

    $product_category_page = false;
    if (is_product_taxonomy()) {
        $product_category_page = __get_product_taxonomy_page(get_queried_object()->term_id);
    }
    if (!$product_category_page) {
        echo __description(array(
            'description' => get_the_excerpt(),
            'class' => _attribute('class', array('product-desc px-20px')),
        ));
    }
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
        $html .= "<label for='variation-$child' class='variation-label status-style-2 w-100 $stock_status_variation'>";
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
    $product_id = get_the_ID();
    $html = '<div class="button-box button-bordered buy-now-button">';
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
        $html .= "<div class='product-inner p-20px d-flex flex-column'>";
        $html .= _product_grid_display($product['id']);

        $html .= "<div class='row-services-spec-mobile d-lg-none mt-4'>";
        foreach ($specs as $key => $spec) {
            $icon = get__term_meta($key, 'icon');
            $mime_type =  get_post_mime_type($icon);
            $html .= "<div class='row g-10px mb-10px'>"; //specs-row

            $html .= "<div class='col-8'>"; //specs-row-col
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
                'class' => _attribute('class', array('mb-0 text-primary')),
                'tag' => 'h5',
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
        $mime_type =  get_post_mime_type($icon);
        $html .= "<div class='row g-10px d-none d-lg-flex'>"; //specs-row

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
        $stock_status =  $product->get_stock_status();
        $sku = $product->get_sku();
        $product_type = $product->get_type();
        $button_class = ($product_type == 'simple') ? 'col-sm-6' : 'col-12';

        $html = "<ul class='products custom-product-grid h-100 m-0 p-0'>";
        $html .= "<li class='product m-0 p-0 w-100 h-100 post-$id $stock_status'>";
        $html .= "<div class='product-inner p-20px rounded-10px border-default h-100 bg-white'>";
        $html .= "<a href='$permalink' class='woocommerce-LoopProduct-link woocommerce-loop-product__link'>";
        $html .= "<div class='wc-img-wrapper'>";
        $html .= "<img width='300' height='225' src='$post_thumnail' class='attachment-woocommerce_thumbnail size-woocommerce_thumbnail' alt='$title' decoding='async'>";
        $html .= "</div>";
        $html .= "<h2 class='woocommerce-loop-product__title mb-0'>$title</h2>";
        $html .= $product->get_price_html();
        $html .= '<span class="status d-block mb-2 mt-2"></span>';

        $html .= "</a>";

        $html .= "<div class='product-buttons'>";
        $html .= "<div class='row g-10px'>";


        if ($product_type == 'simple') {
            $html .= "<div class='$button_class'>";
            $html .= "<a href='?add-to-cart=$id' data-quantity='1' class='button product_type_simple add_to_cart_button ajax_add_to_cart' data-product_id='$id' data-product_sku='$sku' aria-label='Add to basket: “" . $title . "”' rel='nofollow'>Add to basket</a>";
            $html .= "</div>";
        }



        $html .= "<div class='$button_class'>";
        $html .= "<div class='button-box button-bordered'><a href='$permalink'>View Product</a></div>";
        $html .= "</div>";

        $html .= "</div>";

        $html .= "</div>";


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



// Hook for adding html to the product edit page under linked products
add_action('woocommerce_product_options_related', 'add_linked_custom_product_field');
function add_linked_custom_product_field()
{
    global $product_object; // UGH globals.
?>
    <div class="options_group ">
        <p class="form-field">
            <label for="compatible_payloads"><?php esc_html_e('Compatible Payloads', 'woocommerce'); ?></label>
            <select class="wc-product-search" multiple="multiple" style="width: 50%;" id="compatible_payloads" name="compatible_payloads[]" data-sortable="true" data-placeholder="<?php esc_attr_e('Search for a product&hellip;', 'woocommerce'); ?>" data-action="woocommerce_json_search_products">
                <?php
                $product_ids = !empty(get_post_meta($product_object->get_id(), 'compatible_payloads', true)) ? get_post_meta($product_object->get_id(), 'compatible_payloads', true) : array();
                foreach ($product_ids as $product_id) {
                    $product = wc_get_product($product_id);
                    if (is_object($product)) {
                        echo '<option value="' . esc_attr($product_id) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
                    }
                }
                ?>
            </select> <?php echo wc_help_tip(__('Select compatible payloads for this product.', 'woocommerce')); // WPCS: XSS ok. 
                        ?>
        </p>
    </div>

    <div class="options_group ">
        <p class="form-field">
            <label for="accessories"><?php esc_html_e('Accessories', 'woocommerce'); ?></label>
            <select class="wc-product-search" multiple="multiple" style="width: 50%;" id="accessories" name="accessories[]" data-sortable="true" data-placeholder="<?php esc_attr_e('Search for a product&hellip;', 'woocommerce'); ?>" data-action="woocommerce_json_search_products">
                <?php
                $product_ids = !empty(get_post_meta($product_object->get_id(), 'accessories', true)) ? get_post_meta($product_object->get_id(), 'accessories', true) : array();
                foreach ($product_ids as $product_id) {
                    $product = wc_get_product($product_id);
                    if (is_object($product)) {
                        echo '<option value="' . esc_attr($product_id) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
                    }
                }
                ?>
            </select> <?php echo wc_help_tip(__('Select accessories for this product.', 'woocommerce')); // WPCS: XSS ok. 
                        ?>
        </p>
    </div>
<?php
}

// Filter for saving custom product data
add_action('save_post_product', 'save_custom_product_options', 10, 3);
function save_custom_product_options($post_ID, $product, $update)
{
    $compatible_payloads = isset($_POST['compatible_payloads']) ? $_POST['compatible_payloads'] : array();
    update_post_meta($post_ID, 'compatible_payloads', $compatible_payloads);

    $accessories = isset($_POST['accessories']) ? $_POST['accessories'] : array();
    update_post_meta($post_ID, 'accessories', $accessories);
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

        $html .= "<div class='container mt-4'><div class='row g-4 justify-content-between align-items-center'> <div class='col-auto'> <div class='swiper-nav d-inline-flex'> <div class='swiper-button-prev' id='swiper-prev-$id'></div> <div class='swiper-button-next' id='swiper-next-$id'></div> </div> </div>";

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
        'post_type' => 'producttaxonomypages',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => '_product_tax',
                'value' => $id,
                'compare' => 'LIKE',
            ),
        ),
    );
    $product_page = get_posts($args);

    if ($product_page) {
        return $product_page[0];
    }
}


function custom_product_variation_training()
{
    global $product;
    $SVG = new SVG;
    $children = $product->get_children();

    $children_chunk = array_chunk($children, 4);

    $html = '<div class="product-custom-variation product-training-variation">';

    $html .= "<div class='swiper swiper-training'>"; //swiper
    $html .= "<div class='swiper-wrapper'>"; //swiper-wrapper


    foreach ($children_chunk as $children) {
        $html .= '<div class="swiper-slide">'; //swiper-slide
        $html .= '<div class="row g-4">'; //row
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

            $stock_status_variation = $variation->get_stock_status();
            $sku = $variation->get_sku();
            $price = $variation->get_price_html();
            $html .= '<div class="col-lg-6">';


            $html .= "<input stock='$stock_status_variation' type='radio'  id='variation-$child' data_variations='$json' value='$child'  name='variation-radio'>";
            $html .= "<label for='variation-$child' class='variation-label status-style-2 w-100 h-100'>"; //label
            $html .= "<div class='inner product-inner w-100 p-20px rounded-corner  h-100 d-flex flex-column justify-content-between'>"; //inner

            $html .= "<div class='info-box d-flex flex-column justify-content-between'>";

            if ($product_attribute_array['date'] || $product_attribute_array['pa_location']) {
                $html .= "<div class='row g-3 justify-content-between mb-3'>";

                if ($product_attribute_array['date']) {
                    $html .= "<div class='col-auto'>";
                    if ($product_attribute_array['date'] != 'N/A') {
                        $html .= "<span class='date smaller-text text-white bg-accent py-1 px-2'>";
                        $html .= _date_format($product_attribute_array['date']);
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
                'tag' => 'h3'
            ));
            $html .= '<div>';
            $html .= $price;
            $html .= '</div>';

            $html .= '</div>';

            $html .= "<div class='button-box button-bordered mt-3'>";
            $html .= "<a href='?add-to-cart=$child' data-quantity='1' class='button product_type_simple add_to_cart_button ajax_add_to_cart' data-product_id='$child' data-product_sku='$sku' rel='nofollow'>Add to basket</a>";
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
        'class' => _attribute('class', array('mb-3')),
    ));

    $html .= __description(array(
        'description' => $servicing_description,
        'class' => _attribute('class', array('description-box')),
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
            'tag' => 'h3',
            'suffix' => $service_subheading
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
                    'class' => _attribute('class', array('mb-0 text-primary')),
                    'tag' => 'h5',
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
                    $html .= $SVG->check();
                    if ($quantity > 1) {
                        $html .= "<span class='qty ms-2 text-primary d-flex align-items-center'> ";
                        $html .= "<span class='smaller-text me-2'>x</span> <span class='fw-'medium'>$quantity</span>";
                        $html .= "</span>";
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
            'button_type' => 'custom',
            'button_text' => 'Request Service',
            'button_url_custom' => '#',
            'button_style' => $button_class,
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
                'class' => _attribute('class', array('mb-0')),
                'tag' => 'h5',
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
                    $html .= $SVG->check();
                    if ($quantity > 1) {
                        $html .= "<span class='qty ms-2 text-primary d-flex align-items-center'> ";
                        $html .= "<span class='smaller-text me-2'>x</span> <span class='fw-'medium'>$quantity</span>";
                        $html .= "</span>";
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
            'button_type' => 'custom',
            'button_text' => 'Request Service',
            'button_url_custom' => '#',
            'button_style' => $button_class,
        ));


        $html .= "</div>";
        $html .= "</div>";
    }
    $html .= "</div>";



    $html .= "</div>"; //end products-specifications

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
