<?php
$related_guides_heading = get__post_meta('related_guides_heading');
$related_guides = get__post_meta('related_guides');

$related_casestudies_heading = get__post_meta('related_casestudies_heading');
$related_casestudies = get__post_meta('related_casestudies');


echo ___hero_modules();
echo do_shortcode(___sections('sections', get_the_ID()));

if ($related_guides) {
    $related_guides_array = array();
    foreach ($related_guides as $related_guide) {
        $related_guides_array[] = $related_guide['id'];
    }
    echo __linked_products($related_guides_array, false, false, false, $related_guides_heading, false, true, false, 'Related-Guides');
}

if ($related_casestudies) {
    $data = array(
        'col' => false,
        'featured' => false,
        'taxonomy' => 'casestudies_category',
        'style' => 'style-1',
        'elements' => array('image', 'category', 'title', 'excerpt', 'button'),
    );
    echo do_shortcode(__related_posts($related_casestudies, $data, $related_casestudies_heading, 'Case-Studies'));
}

echo do_shortcode(___sections('sections_after_main', get_the_ID()));
