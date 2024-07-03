<?php
add_filter('woocommerce_show_page_title', '__return_false');

function action_woocommerce_before_main_content()
{
    echo ___hero();
}


add_action('woocommerce_before_main_content', 'action_woocommerce_before_main_content');
