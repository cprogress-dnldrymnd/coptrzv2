<?php
// Remove breadcrumbs from shop & categories
add_filter('woocommerce_before_main_content', 'remove_breadcrumbs');
function remove_breadcrumbs()
{
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
}

function action_woocommerce_before_main_content()
{
    echo ___hero();
}


add_action('woocommerce_before_main_content', 'action_woocommerce_before_main_content');

function action_woocommerce_before_shop_loop()
{
    echo '<section class="product-archive-loop">';
    echo '<div class="container">';
}

add_action('woocommerce_before_shop_loop', 'action_woocommerce_before_shop_loop');

function action_woocommerce_after_shop_loop()
{
    echo '</div>';
    echo '</section>';
}

add_action('woocommerce_after_shop_loop', 'action_woocommerce_after_shop_loop');
