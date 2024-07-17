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

global $archive_data;

$data['col'] = true;
$data['featured'] = false;

$elements_array[] = 'image';
$elements_array[] = 'title';
$elements_array[] = 'button';
if (is_home() || is_category()) {
    $key = 'post_';
    $title = 'All Posts';
    if (is_category()) {
        $has_featured = false;
        $data['cat'] = get_queried_object()->term_id;
    } else {
        $has_featured = true;
    }
    $has_filter = true;
    $archive_title = 'Blog';
    $category = 'category';
    $class = "mb-50px";

    $elements_array[] = 'category';
    $elements_array[] = 'date';
    $elements_array[] = 'excerpt';
    $data['elements'] = $elements_array;
    $data['taxonomy'] = $category;
    $data['post_type'] = 'post';
} else if (is_post_type_archive('events')) {
    $key = 'events_';
    $title = false;
    $has_featured = false;
    $has_filter = true;
    $archive_title = 'Events';
    $category = 'events_category';
    $class = 'border-bottom-default sm-padding-bottom sm-margin-bottom';

    $elements_array[] = 'category';
    $data['elements'] = $elements_array;
    $data['taxonomy'] = $category;
    $data['post_type'] = 'events';
} else if (is_post_type_archive('capabilities')) {
    $title = get__theme_option('capabilities_loop_section_title');
    $key = 'capabilities_';
    $title = $title ? $title : 'Commercial <br> Capabilities';
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
    $elements_array[] = 'category';
    $data['button_text'] = 'Learn More';
    $data['elements'] = $elements_array;
    $data['taxonomy'] = $category;
    $data['style'] = 'style-3';
} else if (is_post_type_archive('industries')) {
    $title = get__theme_option('industries_loop_section_title');
    $key = 'industries_';
    $title = $title ? $title : 'Commercial <br> Capabilities';
    $has_featured = false;
    $has_pagination = false;
    $has_filter = false;
    $class = "mb-50px";
    $archive_title = 'Industry Solutions';
    $elements_array[] = 'category';
    $data['button_text'] = 'Learn More';
    $data['elements'] = $elements_array;
} else if (is_post_type_archive('casestudies') || is_tax('casestudies_category')) {
    $key = 'casestudies_';
    $title = 'All Case Studies';
    $has_featured = false;
    $has_filter = true;
    $archive_title = 'Blog';
    $class = "mb-50px";
    $category = 'casestudies_category';
    $elements_array[] = 'category';
    $elements_array[] = 'excerpt';
    $data['button_text'] = 'Learn More';
    $data['elements'] = $elements_array;
    $data['taxonomy'] = $category;
    $data['post_type'] = 'casestudies';

    if (is_tax('casestudies_category')) {
        $data['tax_query'] = array(
            array(
                'taxonomy' => 'casestudies_category',
                'field' => 'term_id',
                'terms' => get_queried_object()->term_id,
            ),
        );
    }
} else if (is_post_type_archive('guides') || is_tax('guides_category')) {
    $key = 'guides_';
    $title = 'All Guides';
    $has_featured = false;
    $has_filter = true;
    $archive_title = 'Guides';
    $class = "mb-50px";
    $category = 'guides_category';
    $elements_array[] = 'category';
    $elements_array[] = 'excerpt';
    $data['button_text'] = 'Learn More';
    $data['elements'] = $elements_array;
    $data['taxonomy'] = $category;
    $data['post_type'] = 'guides';

    if (is_tax('guides_category')) {
        $data['tax_query'] = array(
            array(
                'taxonomy' => 'guides_category',
                'field' => 'term_id',
                'terms' => get_queried_object()->term_id,
            ),
        );
    }
}
echo do_shortcode(___hero_archive($key, $archive_title, $category));
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
$archive_data = $data;
?>

<section class="archive-posts md-padding-top md-padding-bottom border-top-default" id="posts" data='<?= json_encode($data) ?>'>
    <?php
    if ($has_filter) {
        echo ___posts_header($key, $title, $category, $class);
    }
    ?>
    <div class="post-grid-holder">
        <div class="container">
            <div class="loading-results p-5 text-center"> <svg class="spin" xmlns="http://www.w3.org/2000/svg" id="Group_27" data-name="Group 27" width="123" height="123" viewBox="0 0 123 123">
                    <g id="Ellipse_2" data-name="Ellipse 2" fill="none" stroke="#2DA1FF" stroke-width="2">
                        <circle cx="61.5" cy="61.5" r="61.5" stroke="none"></circle>
                        <circle cx="61.5" cy="61.5" r="60.5" fill="none"></circle>
                    </g>
                    <circle id="Ellipse_8" data-name="Ellipse 8" cx="6.5" cy="6.5" r="6.5" transform="translate(30 55)" fill="none" stroke="#2DA1FF" stroke-width="3"></circle>
                    <circle id="Ellipse_9" data-name="Ellipse 9" cx="6.5" cy="6.5" r="6.5" transform="translate(55 55)" fill="none" stroke="#2DA1FF" stroke-width="3"></circle>
                    <circle id="Ellipse_10" data-name="Ellipse 10" cx="6.5" cy="6.5" r="6.5" transform="translate(80 55)" fill="none" stroke="#2DA1FF" stroke-width="3"></circle>
                </svg></div>
            <?php
            if (!$has_filter && $title) {
                echo "<h2 class='text-center'>$title</h2>";
            }
            ?>
            <div id="results">
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

                            <select name="posts_per_page" id="posts_per_page" class="w-auto number-post-trigger">

                                <?php
                                $show_options = array(6, 12, 18, 24, 30);
                                foreach ($show_options as $option) {
                                    $selected = '';
                                    if (isset($_GET['posts_per_page'])) {
                                        if ($_GET['posts_per_page'] == $option) {
                                            $selected = 'selected';
                                        }
                                    } else {
                                        if (12 == $option) {
                                            $selected = 'selected';
                                        }
                                    }
                                    echo "<option $selected value='$option'>Show: $option</option>";
                                }
                                ?>


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