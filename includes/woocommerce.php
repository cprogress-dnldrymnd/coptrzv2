<?php
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);

function action_woocommerce_before_main_content()
{
    echo ___hero();
}


add_action('woocommerce_before_main_content', 'action_woocommerce_before_main_content');

function action_woocommerce_before_shop_loop()
{
    echo '<section class="product-archive-loop sm-padding-top lg-padding-bottom border-top-default">';
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
    echo "<div class='product-inner rounded-10px border-default p-20px'>";
};
