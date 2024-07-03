<?php
add_filter('woocommerce_show_page_title', 'bbloomer_hide_shop_page_title');
function bbloomer_hide_shop_page_title($title)
{
    if (is_shop()) $title = false;
    return $title;
}

function action_woocommerce_before_main_content()
{
    echo ___hero();
}


add_action('woocommerce_before_main_content', 'action_woocommerce_before_main_content');
