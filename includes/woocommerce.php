<?php



/**
 * Remove the breadcrumbs 
 */

add_action('init', 'woo_remove_wc_breadcrumbs');

function woo_remove_wc_breadcrumbs()
{

    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
}


/**
 * @snippet       Plus Minus Quantity Buttons @ WooCommerce Product Page & Cart
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 5
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */



// -------------

// 1. Show plus minus buttons



add_action('woocommerce_after_quantity_input_field', 'bbloomer_display_quantity_plus');



function bbloomer_display_quantity_plus()
{
    global $product;

    if (!is_cart() && !is_checkout()) {
        echo '<button type="button" class="plus">+</button>';
    }
}



add_action('woocommerce_before_quantity_input_field', 'bbloomer_display_quantity_minus');



function bbloomer_display_quantity_minus()
{
    global $product;
    if (!is_cart() && !is_checkout()) {
        echo '<button type="button" class="minus">-</button>';
    }
}


function wt_mime_types($mime_types)
{
    $mime_types['webp'] = 'image/webp'; //Adding webp extension
    return $mime_types;
}
add_filter('woocommerce_rest_allowed_image_mime_types', 'wt_mime_types', 1, 1);



//remove description tab
add_filter('woocommerce_product_tabs', 'my_remove_description_tab', 11);

function my_remove_description_tab($tabs)
{
    unset($tabs['description']);
    return $tabs;
}


/**
 * ENQUIRE NOW TAB
 */
add_filter('woocommerce_product_tabs', 'woo_new_product_tab');
function woo_new_product_tab($tabs)
{
    $button_type = get__post_meta('button_type');
    if ($button_type == 'link_to_form') {

        // Adds the new tab


        $tabs['enquire_now'] = array(
            'title'    => __('Enquire Now', 'woocommerce'),
            'priority' => 50,
            'callback' => 'woo_enquire_now_tab'
        );
    }

    return $tabs;
}
function woo_enquire_now_tab()
{

    // The new tab content
    $enquire_now_form = get__post_meta('enquire_now_form');
?>
    <div class="heading-box text-center">
        <h2>ENQUIRE NOW</h2>
    </div>
    <div class="form-box" id="product-enquire-now">
        <?= do_shortcode($enquire_now_form) ?>
    </div>
    <?php


}

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);

add_action('woocommerce_single_product_summary', 'woocommerce_template_loop_add_to_cart', 30);

function single_product_components()
{
    if (is_product()) {

        ob_start();
    ?>
        <section class="product-tabs md-padding background-white" id="product-tabs">
            <div class="container">
                <?php wc_get_template('single-product/tabs/tabs.php'); ?>
            </div>
        </section>
        <section class="related-products md-padding" id="related-products">
            <div class="container">
                <?= do_shortcode('[related_products limit="4"]') ?>
            </div>
        </section>
    <?php
        return ob_get_clean();
    }
}

function single_product_summary($product_id)
{
    if (is_product()) {
        ob_start();
        $SVG = new SVG;
        $GetData = new GetData;
        $product_id = get_the_ID();
        $product = wc_get_product($product_id);
        $name = $product->get_name();
        $main_image = $product->get_image_id() ? $product->get_image_id() : get__theme_option('placeholder_image');
        $images = $product->get_gallery_image_ids();
        $type = $product->get_type();
        $price = $product->get_price_html();
        $variation_radio = false;
        $variation_radio_value = false;
        if ($type == 'variable') {
            $count = count($product->get_children());
            if ($count == 2) {
                $variation_radio = true;
                $variation_radio_value = $product->get_children();
            }
        }
        $hide_product_summary = get__post_meta_by_id($product_id, 'hide_product_summary');

        $page_template = get_page_template_slug();


        if (!$hide_product_summary && $page_template != 'templates/page-training.php') {
            include(get_stylesheet_directory() . '/template-parts/woocommerce/product-details/product-details.php');
        }
        include(get_stylesheet_directory() . '/template-parts/woocommerce/product-details/product-featured-video.php');
        include(get_stylesheet_directory() . '/template-parts/woocommerce/product-details/product-specifications.php');

        return ob_get_clean();
    }
}


/**
 * @snippet       Get Current Variation ID @ WooCommerce Single Product
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 5
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */

add_action('woocommerce_before_add_to_cart_quantity', 'bbloomer_display_dropdown_variation_add_cart');

function bbloomer_display_dropdown_variation_add_cart()
{
    global $product;
    if ($product->is_type('variable')) {
        wc_enqueue_js("
         $( 'input.variation_id' ).change( function(){
            if( '' != $(this).val() ) {
               var var_id = $(this).val();
		        jQuery('.ajax_add_to_cart').attr('data-product_id', var_id);
            }
         });
      ");
    }
}

function request_info_button($button_class = 'button-secondary')
{
    ob_start();
    ?>
    <div class="button-box button-bordered">
        <a id="request-info-modal" href="#general-enquiry" url="<?= get_permalink() ?>">
            <span class="text">REQUEST INFO</span>
        </a>
    </div>
<?php
    return ob_get_clean();
}


//add_action('woocommerce_after_add_to_cart_button', 'request_info_button');
add_action('woocommerce_after_add_to_cart_button', 'product_buttons');

function more_payment_options()
{
    global $hide_after_add_to_cart;
    if (!$hide_after_add_to_cart) {
        $GetData = new GetData;
        global $product;
        echo $GetData->after_add_to_cart($product->get_id());
        echo '<div class="more-payment-options">
    <span class="d-flex align-items-center more-payment-options-btn"><span class="text">MORE PURCHASE OPTIONS</span> <span class="line"></span> </span>   
    <div class="more-payment-options-holder"><div class="inner"></div> </div> 
    </div>';
    }
}

add_action('woocommerce_after_add_to_cart_button', 'more_payment_options');


// Add new stock status options
function filter_woocommerce_product_stock_status_options($status)
{
    // Add new statuses
    $status['pre_order'] = __('Pre order', 'woocommerce');

    return $status;
}
add_filter('woocommerce_product_stock_status_options', 'filter_woocommerce_product_stock_status_options', 10, 1);

// Availability text
function filter_woocommerce_get_availability_text($availability, $product)
{
    // Get stock status
    switch ($product->get_stock_status()) {
        case 'pre_order':
            $availability = __('Pre order', 'woocommerce');
            break;
    }

    return $availability;
}
add_filter('woocommerce_get_availability_text', 'filter_woocommerce_get_availability_text', 10, 2);

// Availability CSS class
function filter_woocommerce_get_availability_class($class, $product)
{
    // Get stock status
    switch ($product->get_stock_status()) {
        case 'pre_order':
            $class = 'pre-order';
            break;
    }

    return $class;
}
add_filter('woocommerce_get_availability_class', 'filter_woocommerce_get_availability_class', 10, 2);


add_action('coptrz_woocommerce_upsell_display', 'woocommerce_upsell_display');


/**
 * @snippet Move & Change Number of Cross-Sells @ WooCommerce Cart
 * @how-to Get CustomizeWoo.com FREE
 * @sourcecode https://businessbloomer.com/?p=20449
 * @author Rodolfo Melogli
 * @testedwith WooCommerce 2.6.2
 */


// ---------------------------------------------
// Remove Cross Sells From Default Position 

remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');


// ---------------------------------------------
// Add them back UNDER the Cart Table

add_action('woocommerce_after_cart', 'woocommerce_cross_sell_display');


// ---------------------------------------------
// Display Cross Sells on 3 columns instead of default 4
add_filter('woocommerce_cross_sells_columns', 'bbloomer_change_cross_sells_columns');

function bbloomer_change_cross_sells_columns($columns)
{
    return 4;
}


// ---------------------------------------------
// Display Only 3 Cross Sells instead of default 4

add_filter('woocommerce_cross_sells_total', 'bbloomer_change_cross_sells_product_no');

function bbloomer_change_cross_sells_product_no($columns)
{
    return 4;
}


function variable_images($product, $size = 'large', $fancybox = true)
{
    ob_start();
?>
    <?php if ($product->get_type() == 'variable') { ?>
        <?php $get_children = $product->get_children(); ?>
        <?php if ($get_children) { ?>
            <?php $key = 1; ?>
            <?php foreach ($get_children  as $child) { ?>
                <?php $variation_image =  get_post_thumbnail_id($child, 'large'); ?>
                <?php if ($variation_image) { ?>
                    <div class="swiper-slide swiper-slider-variation-<?= $child ?>" key=<?= $key ?>>
                        <div class="image-box">
                            <?php if (is_product()) { ?>
                                <?php if ($fancybox) { ?>
                                    <a class="fancybox-gallery-img" rel="group1" href="<?= wp_get_attachment_image_url($variation_image, 'full') ?>" data-fancybox="product-gallery">
                                    <?php } ?>
                                    <img src="<?= wp_get_attachment_image_url($variation_image, $size) ?>" />
                                    <?php if ($fancybox) { ?>
                                    </a>
                                <?php } ?>

                            <?php } else { ?>
                                <img src="<?= wp_get_attachment_image_url($variation_image, $size) ?>" />
                            <?php } ?>
                        </div>
                    </div>
                    <?php $key++; ?>

                <?php } ?>
            <?php } ?>
        <?php } ?>
    <?php } ?>
<?php
    return ob_get_clean();
}


function product_gallery($main_image, $images, $product)
{
    if ($images || $product->get_type() == 'variable') {
        $wrapper_class_1 = 'swiper mySwiperMain';
        $wrapper_class_2 = 'swiper-wrapper';
        $wrapper_class_3 = 'swiper-slide';
    } else {
        $wrapper_class_1 = '';
        $wrapper_class_2 = '';
        $wrapper_class_3 = '';
    }
?>
    <div class="product-gallery" image_count=<?= count($images) + 1 ?>>

        <div style="--swiper-navigation-color: #000; --swiper-pagination-color: #000" class="<?= $wrapper_class_1 ?> background-light">

            <?php
            echo product_tags(get_the_ID());
            ?>

            <div class="<?= $wrapper_class_2 ?>">

                <div class="<?= $wrapper_class_3 ?>">

                    <div class="image-box">
                        <?php if (is_product()) { ?>
                            <a class="fancybox-gallery-img" rel="group1" href="<?= wp_get_attachment_image_url($main_image, 'full') ?>" data-fancybox="product-gallery">
                                <img src="<?= wp_get_attachment_image_url($main_image, 'large') ?>" />
                            </a>
                        <?php } else { ?>
                            <img src="<?= wp_get_attachment_image_url($main_image, 'large') ?>" />

                        <?php } ?>
                    </div>


                </div>
                <?php if ($images) { ?>
                    <?php foreach ($images as $image) { ?>
                        <div class="swiper-slide">
                            <div class="image-box">
                                <?php if (is_product()) { ?>
                                    <a class="fancybox-gallery-img" rel="group1" href="<?= wp_get_attachment_image_url($image, 'full') ?>" data-fancybox="product-gallery">
                                        <img src="<?= wp_get_attachment_image_url($image, 'large') ?>" />
                                    </a>
                                <?php } else { ?>
                                    <img src="<?= wp_get_attachment_image_url($image, 'large') ?>" />
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                <?php } ?>

                <?= variable_images($product) ?>

            </div>
            <?php if ($images) { ?>
                <div class="swiper-button-next d-none d-sm-flex"></div>

                <div class="swiper-button-prev d-none d-sm-flex"></div>

            <?php } ?>

        </div>
        <?php if ($images || $product->get_type() == 'variable') { ?>
            <div thumbsSlider="" class="swiper mySwiperThumb">

                <div class="swiper-wrapper">

                    <div class="swiper-slide">

                        <div class="image-box">

                            <img src="<?= wp_get_attachment_image_url($main_image, 'thumbnail') ?>" />

                        </div>

                    </div>



                    <?php foreach ($images as $image) { ?>

                        <div class="swiper-slide">

                            <div class="image-box">

                                <img src="<?= wp_get_attachment_image_url($image, 'thumbnail') ?>" />

                            </div>

                        </div>



                    <?php } ?>

                    <?= variable_images($product, 'thumbnail', false) ?>

                </div>


                <div class="swiper-pagination-holder d-flex d-sm-none">
                    <div class="swiper-pagination"></div>
                </div>

            </div>

        <?php } ?>

    </div>
    <?php
}


add_action('woocommerce_product_after_variable_attributes', 'rudr_fields', 10, 3);

function rudr_fields($loop, $variation_data, $variation)
{

    $template = get_page_template_slug();
    if ($template == 'templates/page-training.php') {
        woocommerce_wp_select(
            array(
                'id'            => '_delivery_method[' . $loop . ']',
                'label'         => 'Delivery Method',
                'wrapper_class' => 'form-row',
                'value'         => get_post_meta($variation->ID, '_delivery_method', true),
                'options'       => array(
                    ''    => 'Select Delivery Method',
                    'online'    => 'Online',
                    'classroom' => 'Classroom',
                )
            )
        );
        woocommerce_wp_text_input(
            array(
                'id'            => '_start_date[' . $loop . ']',
                'label'         => 'Start Date',
                'wrapper_class' => 'form-row',
                'placeholder'   => 'Type here...',
                'desc_tip'      => 'true',
                'description'   => 'Training Start Date',
                'type'          => 'date',
                'value'         => get_post_meta($variation->ID, '_start_date', true)
            )
        );
        woocommerce_wp_text_input(
            array(
                'id'            => '_end_date[' . $loop . ']',
                'label'         => 'End Date',
                'wrapper_class' => 'form-row',
                'placeholder'   => 'Type here...',
                'desc_tip'      => 'true',
                'description'   => 'Training End Date',
                'type'          => 'date',
                'value'         => get_post_meta($variation->ID, '_end_date', true)
            )
        );
    }
}

add_action('woocommerce_save_product_variation', 'rudr_save_fields', 10, 2);

function rudr_save_fields($variation_id, $loop)
{

    // Text Field
    $_delivery_method = !empty($_POST['_delivery_method'][$loop]) ? $_POST['_delivery_method'][$loop] : '';
    update_post_meta($variation_id, '_delivery_method', sanitize_text_field($_delivery_method));

    // Textarea Field
    $_start_date = !empty($_POST['_start_date'][$loop]) ? $_POST['_start_date'][$loop] : '';
    update_post_meta($variation_id, '_start_date', sanitize_textarea_field($_start_date));

    // Select Field
    $_end_date = !empty($_POST['_end_date'][$loop]) ? $_POST['_end_date'][$loop] : '';
    update_post_meta($variation_id, '_end_date', sanitize_text_field($_end_date));
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

    foreach (WC()->cart->get_cart_contents() as $key => $values) {

        $type = $values['data']->get_type();

        if ($type == 'variation') {
            $id = $values['data']->get_parent_id();
        } else {
            $id = $values['data']->get_id();
        }

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
        unset($rates['flat_rate:2']);
    } else {
        if ($in_cart) {
            unset($rates['free_shipping:9']);
        }
    }
    // Only unset rates if free_shipping is available
    if (isset($rates['free_shipping:9']) && !isset($rates['flat_rate:2'])) {
        unset($rates['local_pickup:3']);
    }

    if (isset($rates['flat_rate:2'])) {
        unset($rates['free_shipping:9']);
    }
    return $rates;
}


remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

/**
 * WooCommerce Loop Product Thumbs
 **/
if (!function_exists('woocommerce_template_loop_product_thumbnail')) {
    function woocommerce_template_loop_product_thumbnail()
    {
        echo "<div class='image-box'>";
        echo woocommerce_get_product_thumbnail('large');
        echo "</div>";
    }
}


function product_tags($id)
{
    ob_start();
    echo '<div class="product-tag text-end row g-2">';
    if (has_term('black-friday', 'product_tag', $id)) {
        echo '<div class="col-12"> <span class="background-primary">BLACK FRIDAY</span> </div>';
    }
    if (has_term('cyber-monday', 'product_tag', $id)) {
        echo '<div class="col-12"> <span class="background-accent">CYBER MONDAY</span> </div>';
    }
    echo '</div>';
    return ob_get_clean();
}


function action_woocommerce_after_shop_loop_item_title()
{
    if (_is_shop_archive()) {
        global $product;
        $pa_brands = $product->get_attribute('pa_brands');
        $category = get_the_terms($product->get_id(), 'product_cat');
        $data = array(
            'sku' => $product->get_sku(),
            'name' => $product->get_name(),
            'brand' => $pa_brands,
            'category' => $category[0]->name,
        );

        if ($product->get_price()) {
            $data['price'] = _price_format($product->get_price());
        }
    ?>
        <div class="product-data d-none">
            <?= json_encode($data) ?>
        </div>
        <div class="after-title">
            <?= icon_list() ?>
        </div>
    <?php
    }
}


add_action('woocommerce_after_shop_loop_item_title', 'action_woocommerce_after_shop_loop_item_title');

function _is_shop_archive()
{
    if (is_product_category() || is_shop()) {
        return true;
    } else {
        return false;
    }
}

function icon_list()
{
    ob_start();
    global $product;
    $SVG = new SVG;
    ?>
    <div class="icon-list">
        <ul class="list-inline">
            <?php if (_is_shop_archive()) { ?>
                <?php if ($product->get_stock_status() == 'instock') { ?>
                    <li class="d-flex align-items-center">
                        <?php SVG::check_v2() ?>
                        <span>In stock</span>
                    </li>
                <?php } else if ($product->get_stock_status() == 'onbackorder') { ?>
                    <li class="d-flex align-items-center">
                        <?php SVG::check_v2() ?>
                        <span>On backorder</span>
                    </li>
                <?php } else { ?>
                    <li class="d-flex align-items-center">
                        <?php SVG::close_v2() ?>
                        <span>Currently out of stock</span>
                    </li>
                <?php } ?>
            <?php } ?>


            <?php if (is_product()) { ?>
                <?php
                $finance_available = get__post_meta_by_id($product->get_id(), 'finance_available');
                $business_invoicing = get__post_meta_by_id($product->get_id(), 'business_invoicing');
                $lead_time = get__post_meta_by_id($product->get_id(), 'lead_time');
                ?>
                <?php if ($finance_available) { ?>
                    <li>
                        <?php SVG::check_v2() ?>
                        <span>0% financing available</span>
                    </li>
                <?php } ?>
                <?php if ($business_invoicing) { ?>
                    <li>
                        <?php SVG::check_v2() ?>
                        <span>Business invoicing</span>
                    </li>
                <?php } ?>
                <?php if ($lead_time) { ?>
                    <li>
                        <?php SVG::check_v2() ?>
                        <span> <strong>Lead Time: </strong> <?= $lead_time ?></span>
                    </li>
                <?php } ?>
            <?php } ?>

            <?php if (!has_term(array(1427, 776, 32, 789), 'product_cat')) { ?>
                <li class="align-items-center d-none">
                    <svg class="me-2" xmlns="http://www.w3.org/2000/svg" width="16.199" height="16.199" viewBox="0 0 16.199 16.199">
                        <path id="Icon_awesome-check-circle" data-name="Icon awesome-check-circle" d="M16.761,8.662a8.1,8.1,0,1,1-8.1-8.1A8.1,8.1,0,0,1,16.761,8.662ZM7.725,12.951l6.009-6.009a.523.523,0,0,0,0-.739L13,5.463a.523.523,0,0,0-.739,0l-4.9,4.9L5.068,8.076a.523.523,0,0,0-.739,0l-.739.739a.523.523,0,0,0,0,.739l3.4,3.4a.523.523,0,0,0,.739,0Z" transform="translate(-0.563 -0.563)" />
                    </svg>
                    <span>FREE delivery within 3-5 business days.</span>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php
    return ob_get_clean();
}




add_filter('gettext', 'change_readmore_text', 20, 3);

function change_readmore_text($translated_text, $text, $domain)
{
    if (!is_admin() && $domain === 'woocommerce' && $translated_text === 'Read more') {
        $translated_text = 'View';
    }
    return $translated_text;
}

/*
add_filter('woocommerce_add_to_cart_redirect', 'redirect_to_checkout');

function redirect_to_checkout()
{
    if (isset($_GET['buy-now']) && $_GET['buy-now'] == true) {
        global $woocommerce;
        $woocommerce->cart->empty_cart();
        $product_id = $_GET['add-to-cart'];
        if ($woocommerce->cart->get_cart_contents_count() == 0) {
            $woocommerce->cart->add_to_cart($product_id);
        }

        $checkout_url = $woocommerce->cart->get_checkout_url();
        return $checkout_url;
    }
}
*/
/**
 * Change number of products that are displayed per page (shop page)
 */

add_filter('loop_shop_per_page', 'new_loop_shop_per_page', 20);
function new_loop_shop_per_page($cols)
{
    // $cols contains the current number of products per page based on the value stored on Options -> Reading
    // Return the number of products you wanna show per page.
    $cols = 18;

    return $cols;
}



function product_buttons()
{
    global $product;
    $rental = get_post_meta($product->get_id(), '_wcrp_rental_products_rental', true);
?>

    <?php if ($product->is_in_stock() && !isset($_GET['rent']) && $rental != 'yes') { ?>
        <?php if ($product->get_type() == 'simple') { ?>
            <div class="button-box button-bordered">
                <?php if ($product->is_purchasable()) { ?>
                    <input type="hidden" name="buy_now_id" value="<?= $product->get_id() ?>">
                    <button class="w-100 buy-now simple">
                        BUY NOW
                        <svg class="spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                            <path fill="currentColor" d="M304 48a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zm0 416a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zM48 304a48 48 0 1 0 0-96 48 48 0 1 0 0 96zm464-48a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zM142.9 437A48 48 0 1 0 75 369.1 48 48 0 1 0 142.9 437zm0-294.2A48 48 0 1 0 75 75a48 48 0 1 0 67.9 67.9zM369.1 437A48 48 0 1 0 437 369.1 48 48 0 1 0 369.1 437z"></path>
                        </svg>
                    </button>
                <?php } else { ?>
                    <?= request_info_button('button-bordered') ?>
                <?php } ?>
            </div>
        <?php } else { ?>
            <?php if (!is_single()) { ?>
                <?= request_info_button('button-bordered') ?>
            <?php } ?>
        <?php } ?>
    <?php } else { ?>
        <?= request_info_button('button-bordered') ?>
    <?php } ?>
<?php
}

remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);


function convert_products_to_virtual()
{

    ob_start();

    $args = array(
        'post_type'             => 'product',
        'post_status'           => 'publish',
        'posts_per_page'        => '-1',
        'tax_query'             => array(
            array(
                'taxonomy'      => 'product_cat',
                'field' => 'term_id', //This is optional, as it defaults to 'term_id'
                'terms'         => array(789),
                'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
            ),
        )
    );
    $products = new WP_Query($args);
    echo '<table class="table">';
    echo '<tr>';

    echo '<td>Name</td>';
    echo '<td>Is Virtual</td>';
    echo '<td>Product Type</td>';
    echo '<td>Children</td>';

    echo '</tr>';

    while ($products->have_posts()) {
        $products->the_post();
        global $product;

        $type = $product->get_type();

        if ($type == 'simple') {

            update_post_meta(get_the_ID(), '_virtual', 'yes');

            echo '<tr>';

            echo '<td>';
            echo the_title();
            echo '</td>';

            echo '<td>';
            echo get_post_meta(get_the_ID(), '_virtual', true);
            echo '</td>';

            echo '<td>';
            echo $type;
            echo '</td>';

            echo '</tr>';
        }
        if ($type == 'variable') {
            $child = $product->get_children();

            echo '<tr>';

            echo '<td>';
            echo the_title();
            echo '</td>';

            echo '<td>';
            echo get_post_meta(get_the_ID(), '_virtual', true);
            echo '</td>';

            echo '<td>';
            echo $type;
            echo '</td>';

            echo '<td>';

            foreach ($child as $c) {
                update_post_meta($c, '_virtual', 'yes');

                echo $c . ' - ' . get_post_meta($c, '_virtual', true);

                //echo '<pre>';
                //var_dump(get_post_meta($c));
                //echo '</pre>';
                echo '<br>';
            }
            echo '</td>';

            echo '</tr>';
        }
    }
    echo '</table>';


    return ob_get_clean();
}

add_shortcode('convert_products_to_virtual', 'convert_products_to_virtual');


/*
// Replacing the Place order button when total volume exceed 68 m3
add_filter('woocommerce_order_button_html', 'replace_order_button_html', 10, 2);
function replace_order_button_html($order_button)
{
    $chosen_shipping_methods = WC()->session->get('chosen_shipping_methods');
    // remove button if there is no chosen shipping method

    $has_non_virtual = false;
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        // Check if there are non-virtual products
        if (!$cart_item['data']->is_virtual()) {
            $has_non_virtual = true;
        }
    }

    if ($has_non_virtual == true) {
        if ($chosen_shipping_methods[0] == false) {
            $order_button_text = __("PLACE ORDER", "woocommerce");
            //$style = ' style="color:#fff;cursor:not-allowed;background-color:#999;"';
            return '<div class="cant-deliver"> <p>We can’t currently deliver outside the UK without more details please get in touch  with our team on 0330 111 7177.</p> </div><a class="button alt" name="woocommerce_checkout_place_order" id="place_order" >' . esc_html($order_button_text) . '</a>';
        } else {
            return $order_button;
        }
    } else {
        return $order_button;
    }
}
*/

/**
 * @snippet       Add Custom Field @ WooCommerce Checkout Page
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @testedwith    WooCommerce 6
 * @community     https://businessbloomer.com/club/
 */

add_action('woocommerce_billing_fields', 'bbloomer_add_custom_checkout_field');

function bbloomer_add_custom_checkout_field($fields)
{

    $fields['sector'] = array(
        'label' => __('Sector', 'woocommerce'), // Add custom field label
        'required' => true, // if field is required or not
        'clear' => false, // add clear or not
        'type' => 'select', // add field type
        'select2' => true,
        'options'    => array(
            ''            => 'Select Sector',
            'Agriculture & Forestry'            => 'Agriculture & Forestry',
            'Military & Defence'    => 'Military & Defence', //
            'Surveying & Construction'        => 'Surveying & Construction',
            'Research & Education' => 'Research & Education',
            'Oil & Gas' => 'Oil & Gas',
            'Government Body' => 'Government Body',
            'Asset Integrity & Inspection' => 'Asset Integrity & Inspection',
            'Marine' => 'Marine',
            'Film & Media' => 'Film & Media',
            'Public Safety' => 'Public Safety',
            'Energy & Renewables' => 'Energy & Renewables',
            'Sport' => 'Sport',
            'Security' => 'Security',
            'Drone Service Provider' => 'Drone Service Provider',
            'Hobbyist' => 'Hobbyist',
            'Other' => 'Other',
        ),
    );

    return $fields;
}

add_action('show_user_profile', 'extra_user_profile_fields', 10, 1);
add_action('edit_user_profile', 'extra_user_profile_fields', 10, 1);

function extra_user_profile_fields($user)
{
    $options = array(
        ''            => 'Select Sector',
        'Agriculture & Forestry'            => 'Agriculture & Forestry',
        'Military & Defence'    => 'Military & Defence', //
        'Surveying & Construction'        => 'Surveying & Construction',
        'Research & Education' => 'Research & Education',
        'Oil & Gas' => 'Oil & Gas',
        'Government Body' => 'Government Body',
        'Asset Integrity & Inspection' => 'Asset Integrity & Inspection',
        'Marine' => 'Marine',
        'Film & Media' => 'Film & Media',
        'Public Safety' => 'Public Safety',
        'Energy & Renewables' => 'Energy & Renewables',
        'Sport' => 'Sport',
        'Security' => 'Security',
        'Drone Service Provider' => 'Drone Service Provider',
        'Hobbyist' => 'Hobbyist',
        'Other' => 'Other',
    );
?>
    <h3><?php _e("Additional Customer Information", "blank"); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="sector"><?php _e("Sector"); ?></label></th>
            <td>
                <select name="sector" id="sector">
                    <?php foreach ($options as $key => $option) { ?>
                        <?php
                        $sector =  get_the_author_meta('sector', $user->ID);
                        if ($sector == $key) {
                            $selected = 'selected';
                        } else {
                            $selected = '';
                        }
                        ?>
                        <option value="<?= $key ?>" <?= $selected ?>><?= $option ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
    </table>
    <?php }

add_action('personal_options_update', 'save_extra_user_profile_fields');
add_action('edit_user_profile_update', 'save_extra_user_profile_fields');

function save_extra_user_profile_fields($user_id)
{
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    update_user_meta($user_id, 'sector', $_POST['sector']);
}

/*
add_filter("woocommerce_checkout_fields", "custom_override_checkout_fields", 99999);
function custom_override_checkout_fields($fields)
{
    $fields['billing']['billing_first_name']['priority'] = 1;
    $fields['billing']['billing_last_name']['priority'] = 2;
    $fields['billing']['billing_company']['priority'] = 3;
    $fields['billing']['sector']['priority'] = 4;
    $fields['billing']['billing_country']['priority'] = 5;
    $fields['billing']['billing_phone']['priority'] = 6;
    $fields['billing']['billing_state']['priority'] = 7;
    $fields['billing']['billing_address_1']['priority'] = 8;
    $fields['billing']['billing_address_2']['priority'] = 9;
    $fields['billing']['billing_city']['priority'] = 10;
    $fields['billing']['billing_postcode']['priority'] = 11;
    $fields['billing']['billing_email']['priority'] = 12;


    $fields['billing']['billing_phone']['required'] = true;
    $fields['billing']['billing_phone']['label'] = __('Phone', 'woocommerce');
    $fields['billing']['billing_company']['label'] = __('Company name', 'woocommerce');
    $fields['billing']['billing_company']['required'] = true;




    return $fields;
}
*/
add_action('woocommerce_checkout_process', 'bbloomer_validate_new_checkout_field');

function bbloomer_validate_new_checkout_field()
{
    if (!$_POST['sector']) {
        wc_add_notice('Please select a sector', 'error');
    } else {
        update_user_meta(get_current_user_id(), 'sector', $_POST['sector']);
    }
}

add_action('woocommerce_checkout_update_order_meta', 'bbloomer_save_new_checkout_field');

function bbloomer_save_new_checkout_field($order_id)
{
    if ($_POST['sector']) update_post_meta($order_id, 'sector', esc_attr($_POST['sector']));
}

add_action('woocommerce_thankyou', 'bbloomer_show_new_checkout_field_thankyou');

function bbloomer_show_new_checkout_field_thankyou($order_id)
{
    if (get_post_meta($order_id, 'sector', true)) echo '<p><strong>Sector:</strong> ' . get_post_meta($order_id, 'sector', true) . '</p>';
}

add_action('woocommerce_admin_order_data_after_billing_address', 'bbloomer_show_new_checkout_field_order');

function bbloomer_show_new_checkout_field_order($order)
{
    $order_id = $order->get_id();
    if (get_post_meta($order_id, 'sector', true)) echo '<p><strong>Sector:</strong> ' . get_post_meta($order_id, 'sector', true) . '</p>';
}

add_action('woocommerce_email_after_order_table', 'bbloomer_show_new_checkout_field_emails', 20, 4);

function bbloomer_show_new_checkout_field_emails($order, $sent_to_admin, $plain_text, $email)
{
    if (get_post_meta($order->get_id(), 'sector', true)) echo '<p><strong>Sector:</strong> ' . get_post_meta($order->get_id(), 'sector', true) . '</p>';
}

/**
 * Flux checkout - move fields to step 1 (customer details step).
 */
function flux_move_fields_to_details_step($customer_details_fields)
{
    $customer_details_fields[] = 'sector';
    $customer_details_fields[] = 'phone';

    return $customer_details_fields;
}

add_filter('flux_checkout_details_fields', 'flux_move_fields_to_details_step');




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
            jQuery('.woocommerce-loop-product__link').click(function(e) {
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
                $products = jQuery('.products .product');
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
        <!--
        <script>
            jQuery('body').on('added_to_cart', function() {
                //ga4_add_to_cart_single();
            });
            jQuery('.buy-now.simple').click(function(e) {
                //ga4_add_to_cart_single();
            });

            function ga4_add_to_cart_single() {
                $data = jQuery('.product-data').text();
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
                            quantity: 1
                        }]
                    }
                });
            }
        </script>-->
    <?php
    } else if (is_product()) {
    ?>
        <script>
            jQuery('body').on('added_to_cart', function() {
                ga4_add_to_cart_single();
            });
            jQuery('.buy-now.simple').click(function(e) {
                ga4_add_to_cart_single();
            });

            function ga4_add_to_cart_single() {
                quantity = jQuery('input[name="quantity"]').val();
                $data = jQuery('.product-data').text();
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
                            quantity: parseInt(quantity)
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


function _price_format($price)
{
    if ($price) {
        return round(preg_replace("/[^0-9\.]/", '', $price), 2);
    }
}
