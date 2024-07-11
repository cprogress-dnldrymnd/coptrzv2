<?php
echo ___hero_modules();
echo do_shortcode(___sections('sections', get_the_ID()));

$related_products_heading = get__post_meta('related_products_heading');
$related_products = get__post_meta('related_products');
echo __linked_products($accessories, $related_products_heading, '#', 'swiper-accessories', 'Accessories');