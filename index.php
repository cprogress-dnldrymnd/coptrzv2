<?php

/**
 * The template for displaying the home/index page.
 * This template will also be called in any case where the Wordpress engine 
 * doesn't know which template to use (e.g. 404 error)
 */
get_header(); ?>


<?php
if (is_home()) {
    $post_key = 'post';
    $key = 'post_';
    $title = 'All Posts';
    $has_featured = true;
    $has_filter = true;
    $archive_title = 'Blog';
    $category = 'category';
    $data = array(
        'col' => true,
        'featured' => false,
        'style' => 'style-1',
        'elements' => array('image', 'category', 'date', 'title', 'excerpt', 'button')
    );
} else if (is_post_type_archive('events')) {
    $SVG = new SVG;
    $post_key = 'events';
    $key = 'events_';
    $title = false;
    $has_featured = false;
    $has_filter = true;
    $archive_title = 'Events';
    $category = 'events_category';
    $class = 'border-bottom-default sm-padding-bottom sm-margin-bottom';

    $data = array(
        'col' => true,
        'featured' => false,
        'style' => 'style-2',
        'elements' => array('image', 'category', 'title', 'excerpt', 'button'),
    );
} else if (is_post_type_archive('capabilities')) {
    $key = 'capabilities_';
    $post_key = 'capabilities';
    $title = false;
    $has_featured = false;
    $has_filter = false;
    $archive_title = 'Capabilities';
    $post_style = 'style-1';
    $data = array(
        'col' => true,
        'featured' => false,
        'style' => 'style-2',
        'elements' => array('image', 'category', 'title', 'excerpt', 'button'),
    );
}
echo do_shortcode(___hero_archive($key, $archive_title));
if ($has_featured) {
    echo ___featured($key);
}
?>

<section class="archive-posts md-padding-top md-padding-bottom border-top-default">
    <?php
    if ($has_filter) {
        echo ___posts_header($key, $title, $category, $class);
    }
    ?>
    <div class="post-grid-holder">
        <div class="container">
            <div class="row g-4 same-image-height">
                <?php
                while (have_posts()) {
                    the_post();
                    $data['id'] = get_the_ID();
                    if ($key == 'events_') {
                        $data['additional_content'] = _events_additional_content(get_the_ID());
                    }
                    echo __post_box_blog($data);
                }
                ?>
            </div>
        </div>
    </div>
</section>

<?php
$args = array(
    'post_type'  => 'layouts',
    'meta_query' => array(
        array(
            'key'   => '_display_location_archive',
            'value' => $post_type,
        )
    )
);
$layouts = get_posts($args);

if ($layouts) {
    foreach ($layouts as $layout) {
        echo do_shortcode(get_the_content(NULL, false, $layout->ID));
    }
}
?>

<?php get_footer(); ?>