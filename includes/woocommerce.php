<?php
function action_woocommerce_before_main_content() {
    echo 'mama mo';
}


add_action('woocommerce_before_main_content', 'action_woocommerce_before_main_content');