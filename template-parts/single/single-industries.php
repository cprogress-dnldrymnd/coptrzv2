<?php
$related_guides_heading = get__post_meta('related_guides_heading');
$related_guides = get__post_meta('related_guides');

$related_casestudies_heading = get__post_meta('related_casestudies_heading');
$related_casestudies = get__post_meta('related_casestudies');


echo ___hero_modules();
echo do_shortcode(___sections('sections', get_the_ID()));

if ($related_guides) {
    $data = array(
        'col' => false,
        'featured' => false,
        'taxonomy' => 'guides_category',
        'style' => 'style-1',
        'elements' => array('image', 'category', 'title', 'excerpt', 'button'),
    );
    echo do_shortcode(__related_posts($related_guides, $data, $related_guides_heading, 'Case-Studies'));
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
