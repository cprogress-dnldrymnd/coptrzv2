<?php
function action_woocommerce_before_main_content()
{
    echo ___hero();
}


add_action('woocommerce_before_main_content', 'action_woocommerce_before_main_content');

function action_woocommerce_before_shop_loop() {
    echo 'xsdsds';
}

add_action('woocommerce_before_shop_loop', 'action_woocommerce_before_shop_loop');