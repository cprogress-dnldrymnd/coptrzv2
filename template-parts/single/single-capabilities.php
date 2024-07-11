<?php
echo ___hero_modules();
echo do_shortcode(___sections('sections', get_the_ID()));

$related_products_heading = get__post_meta('related_products_heading');
$related_products = get__post_meta('related_products');
$related_casestudies_heading = get__post_meta('related_casestudies_heading');
$related_casestudies = get__post_meta('related_casestudies');


$related_products_array = array();
foreach ($related_products as $related_product) {
    $related_products_array[] = $related_product['id'];
}
echo __linked_products($related_products_array, false, false, false, $related_products_heading, false, true, false);


if ($related_casestudies) {
    $data = array(
        'col' => true,
        'featured' => false,
        'style' => 'style-2',
        'elements' => array('image', 'category', 'title', 'excerpt', 'button'),
    );
    echo do_shortcode(__related_posts($related_casestudies, $related_casestudies_heading, $data));
}
