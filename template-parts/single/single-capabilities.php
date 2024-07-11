<?php
echo ___hero_modules();
echo do_shortcode(___sections('sections', get_the_ID()));

$related_products_heading = get__post_meta('related_products_heading');
$related_products = get__post_meta('related_products');
$related_products_array = array();
foreach($related_products as $related_product) {
    $related_products_array[] = $related_product['id'];
} 
echo __linked_products($related_products_array, false, false, 'swiper-accessories', $related_products_heading, false, true, false);