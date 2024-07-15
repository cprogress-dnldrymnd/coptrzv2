<?php

/**
 * The template for displaying the home/index page.
 * This template will also be called in any case where the Wordpress engine 
 * doesn't know which template to use (e.g. 404 error)
 */
get_header(); ?>

<?php
$post_type = get_queried_object()->name;
$class = '';
$SVG = new SVG;
$has_pagination = true;
if (is_home()) {
    $key = 'post_';
    $title = 'All Posts';
    $has_featured = true;
    $has_filter = true;
    $archive_title = 'Blog';
    $category = 'category';
    $class = "mb-50px";
    $data = array(
        'col' => true,
        'featured' => false,
        'style' => 'style-1',
        'taxonomy' => $category,
        'elements' => array('image', 'category', 'date', 'title', 'excerpt', 'button')
    );
} else if (is_post_type_archive('events')) {
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
        'taxonomy' => $category,
        'elements' => array('image', 'category', 'title', 'excerpt', 'button'),
    );
} else if (is_post_type_archive('capabilities')) {
    $key = 'capabilities_';
    $title = 'Rare Commercial <br> Capabilities';
    $has_featured = false;
    $has_filter = false;
    $has_pagination = false;
    $archive_title = 'Capabilities';
    $data = array(
        'col' => true,
        'featured' => false,
        'bg_image' => true,
        'style' => 'style-3',
        'button_text' => 'Learn More',
        'elements' => array('image', 'title', 'button'),
    );
} else if (is_post_type_archive('solutions')) {
    $key = 'solutions_';
    $title = false;
    $has_featured = false;
    $has_pagination = false;
    $has_filter = false;
    $class = "mb-50px";
    $archive_title = 'Industry Solutions';
    $data = array(
        'col' => true,
        'featured' => false,
        'bg_image' => true,
        'style' => 'style-3',
        'button_text' => 'Learn More',
        'elements' => array('image', 'title', 'button'),
    );
} else if (is_post_type_archive('casestudies')) {
    $key = 'casestudies_';
    $title = 'All Case Studies';
    $has_featured = false;
    $has_filter = true;
    $archive_title = 'Blog';
    $class = "mb-50px";
    $category = 'casestudies_category';
    $data = array(
        'col' => true,
        'featured' => false,
        'style' => 'style-1',
        'taxonomy' => $category,
        'elements' => array('image', 'category', 'date', 'title', 'excerpt', 'button')
    );
} else if (is_post_type_archive('guides')) {
    $key = 'guides_';
    $title = 'All Guides';
    $has_featured = false;
    $has_filter = true;
    $archive_title = 'Guides';
    $class = "mb-50px";
    $category = 'guides_category';
    $data = array(
        'col' => true,
        'featured' => false,
        'style' => 'style-1',
        'taxonomy' => $category,
        'elements' => array('image', 'category', 'date', 'title', 'excerpt', 'button')
    );
}
echo do_shortcode(___hero_archive($key, $archive_title));
if ($has_featured &&  !is_paged()) {
    echo ___featured($key);
}


$args = array(
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key'   => '_display_location_archive',
            'value' => $post_type,
        ),
        array(
            'key'   => '_display_location_archive_position',
            'value' => 'above_loop',
        )
    )
);
echo do_shortcode(__layouts($args));
?>

<section class="archive-posts md-padding-top md-padding-bottom border-top-default" id="posts">
    <?php
    if ($has_filter) {
        echo ___posts_header($key, $title, $category, $class);
    }
    ?>
    <div class="post-grid-holder">
        <div class="container">
            <?php
            if (!$has_filter && $title) {
                echo "<h2 class='text-center'>$title</h2>";
            }
            ?>
            <div class="row g-4 same-image-height">
                <?php
                while (have_posts()) {
                    the_post();
                    $data['id'] = get_the_ID();
                    if ($key == 'events_') {
                        $data['additional_content'] = _events_additional_content(get_the_ID());
                    }
                    echo __post_box($data);
                }
                ?>
            </div>
        </div>
    </div>
    <?php if ($has_pagination) { ?>
        <div class="pagination">
            <div class="container">
                <div class="inner border-top-default sm-padding-top sm-margin-top">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <?php the_posts_pagination(array(
                                'mid_size'  => 2,
                                'next_text' => $SVG->chevron_right(),
                                'prev_text' => $SVG->chevron_left(),
                            )); ?>
                        </div>
                        <div class="col-lg-4 text-center text-md-end">
                            <select name="posts_per_page" id="posts_per_page" class="w-auto select-trigger-change">
                                <option value="12">Show: 8</option>
                                <option selected value="12">Show: 12</option>
                                <option value="16">Show: 16</option>
                                <option value="20">Show: 20</option>
                                <option value="24">Show: 24</option>
                                <option value="28">Show: 28</option>
                                <option value="32">Show: 32</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</section>

<?php
$args = array(
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key'   => '_display_location_archive',
            'value' => $post_type,
        ),
        array(
            'key'   => '_display_location_archive_position',
            'value' => 'below_loop',
        )
    )
);
echo do_shortcode(__layouts($args));
?>

<?php get_footer(); ?>