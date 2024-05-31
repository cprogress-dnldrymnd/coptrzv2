<?php
global $product_id_global;
$DisplayData = new DisplayData;
$GetData = new GetData;
//$product_modal_description = get__post_meta_by_id($product_id, 'product_modal_description');
$description = $product->get_short_description();
$product_id_global = $product_id;
$price = $GetData->product_price($product_id);

$product = wc_get_product($product_id);
$pa_brands = $product->get_attribute('pa_brands');
$category = get_the_terms($product_id, 'product_cat');
$stock_status = $product->get_stock_status();
$product_type = $product->get_type();


$data['sku']      = $product->get_sku();
if ($product->get_price()) {
    $data['price']    = _price_format($product->get_price());
}
$data['name']     = $product->get_name();
$data['brand']    = $pa_brands;
$data['category'] = $category[0]->name;

?>

<div class="product-data d-none">
    <?= json_encode($data) ?>
</div>
<section class="product-summary no-overflow <?= is_single() ? 'sm-padding' : '' ?>" id="product-summary">

    <div class="container medium-container">

        <?php wc_print_notices(); ?>

        <div class="row g-5">

            <div class="col-lg-5">

                <div class="column-holder position-sticky">
                    <?= product_gallery($main_image, $images, $product) ?>
                </div>

            </div>

            <div class="col-lg-7">

                <div class="column-holder content-margin-small column-right">

                    <?= do_shortcode('[breadcrumbs post_type="product" post_type_label="Shop"]') ?>

                    <div class="heading-box">
                        <h2>
                            <?= get_the_title() ?>
                        </h2>
                        <?php
                        if ($product_type == 'simple') {
                            if ($stock_status == 'onbackorder') {
                                echo '<span class="stock-status mb-4 d-inline-block ' . $stock_status . '"> On backorder </span>';
                            } else 	if ($stock_status == 'instock') {
                                echo '<span class="stock-status mb-4 d-inline-block ' . $stock_status . '"> In stock </span>';
                            } else 	if ($stock_status == 'outofstock') {
                                echo '<span class="stock-status mb-4 d-inline-block ' . $stock_status . '"> Out of stock </span>';
                            } else {
                                echo '<span class="stock-status outofstock mb-4 d-inline-block"> Pre order </span>';
                            }
                        }
                        ?>
                    </div>
                    <div class="price-box price-box-main">
                        <?= $price ?>
                        <?php if ($type == 'variable') { ?>
                            <?php if (!current_user_can('administrator')) { ?>
                                <div class="variation-price mt-2"></div>
                                <div class="variation-description mt-2"></div>
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <?= icon_list() ?>

                    <div class="description-box content-margin">

                        <?php
                        /*
                        if ($type == 'modal') {
                            $description = $product_modal_description ? $product_modal_description : $product->get_short_description();
                        } else {
                            if (is_product()) {
                                $description = $product->get_short_description();
                            } else {
                                $description = $product_modal_description . $product->get_short_description();
                            }
                        }*/
                        ?>

                        <?= wpautop($description) ?>

                    </div>

                    <?php
                    echo $GetData->rental($product_id);
                    echo $GetData->add_to_cart($product_id);
                    ?>

                    <?php
                    do_action('coptrz_woocommerce_upsell_display');
                    //echo $GetData->need_help($product_id);
                    ?>
                </div>

            </div>
        </div>

    </div>

</section>


<section class="sticky-add-to-cart product-summary">
    <div class="container">
        <div class="row align-items-center justify-content-between">
            <div class="col-auto">
                <div class="heading-box">
                    <h2>
                        <?= get_the_title() ?>
                    </h2>
                </div>
                <div class="price-box">
                    <?= $price ?>
                    <?php if ($type == 'variable') { ?>
                        <?php if (!current_user_can('administrator')) { ?>
                            <div class="variation-price mt-2"></div>
                            <div class="variation-description mt-2"></div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="col-auto">
                <div class="inner product-add-to-cart-holder">
                    <div class="add-to-cart-box">
                        <div class="cart" id="sticky-add-to-cart">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-auto col-olark d-none">
                <div class="button-box button-primary">
                    <a href="javascript:void(0);" id="olark-custom-button" onclick="olark('api.box.expand')" class="d-flex align-items-center " tabindex="0" aria-label="Chat with us" aria-controls="olark-container" aria-expanded="false">
                        <svg viewBox="0 0 34 31" width="34" height="31" aria-hidden="true" role="img" fill="none">
                            <path fill-rule="evenodd" clip-rule="eventodd" d="M8.61516 22.4845L3.35656 21.9322C2.01727 21.7915 1 20.6622 1 19.3154V6.44089C1 5.09407 2.01727 3.96471 3.35656 3.82401C9.10121 3.22057 23.8176 1.67437 30.0934 1.01451C30.8353 0.936259 31.5749 1.17703 32.1294 1.67588C32.6832 2.17473 33 2.88575 33 3.63139C33 8.31288 33 17.4426 33 22.1249C33 22.8705 32.6832 23.5808 32.1294 24.0804C31.5749 24.5792 30.8353 24.8192 30.0934 24.7417L14.8925 23.1444C13.4403 26.7304 10.0259 29.038 5.674 29.5414C5.45881 29.5624 5.25565 29.4375 5.17664 29.2359C5.09839 29.0342 5.1646 28.8048 5.33765 28.6746C7.20739 27.2518 8.36687 25.3655 8.61516 22.4845Z" stroke="white" stroke-width="2"></path>
                            <circle cx="17" cy="13" r="2" fill="white"></circle>
                            <circle cx="25" cy="13" r="2" fill="white"></circle>
                            <circle cx="9" cy="13" r="2" fill="white"></circle>
                        </svg>
                        <span>Chat with us</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    jQuery(document).ready(function() {
        product_summary();
        sticky_add_to_cart();
        //olark_button();
    });

    function olark_button() {

        jQuery('#olark-custom-button').click(function(e) {
            jQuery('.col-olark').toggleClass('olark-active');

        });
    }

    function product_summary() {
        jQuery('#ppcp-messages').appendTo('.more-payment-options .inner');
        jQuery('.ppc-button-wrapper').appendTo('.more-payment-options .inner');


        jQuery('.more-payment-options-btn').click(function(e) {
            jQuery('.more-payment-options').toggleClass('active');
            e.preventDefault();
        });

        jQuery('.wcrp-rental-products-rental-form').insertAfter('.add_to_cart_button + .button-box');

        if (jQuery('.purchase-rental').length > 0) {
            jQuery('.add_to_cart_button').removeClass('ajax_add_to_cart');

            jQuery('.purchase-rental a').click(function(e) {
                jQuery('.purchase-rental').toggleClass('active');
            });
        }
    }

    function sticky_add_to_cart() {
        if (jQuery('.add_to_cart_button').length > 0) {
            if (jQuery('.wcrp-rental-products-rental-form-wrap').length > 0) {
                jQuery('.product-summary .quantity').clone().addClass('pseudo-quantity').appendTo('#sticky-add-to-cart');
                jQuery('.product-summary .add_to_cart_button').clone().addClass('pseudo-add-to-cart woocommerce-button').removeClass('single_add_to_cart_button add_to_cart_button').removeAttr('disabled').removeAttr('style').appendTo('#sticky-add-to-cart');
            } else {
                jQuery('.product-summary .quantity').clone().appendTo('#sticky-add-to-cart');
                jQuery('.product-summary .add_to_cart_button').clone().appendTo('#sticky-add-to-cart');
            }

            jQuery('.product-summary .product-add-to-cart-holder .button-box').clone().addClass('d-none d-sm-block').appendTo('#sticky-add-to-cart');
        }
        jQuery(document).on('click', '.pseudo-add-to-cart', function(e) {
            if (jQuery('.product-summary .add_to_cart_button[disabled]').length > 0) {
                if (jQuery('.wcrp-rental-products-rental-form-wrap').length > 0) {
                    alert('Please select rental dates');
                } else {
                    alert('Please select product variation');
                }
            } else {
                jQuery('.product-summary .add_to_cart_button').click();
            }
            e.preventDefault();
        });
    }
</script>