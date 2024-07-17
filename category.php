<?php

/**
 * The template for displaying the home/index page.
 * This template will also be called in any case where the Wordpress engine 
 * doesn't know which template to use (e.g. 404 error)
 */
get_header(); ?>

<?php
$post_type = get_queried_object()->name;
$SVG = new SVG;
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
?>

<section class="archive-posts md-padding-top md-padding-bottom border-top-default" id="posts">
    <?php
    echo ___posts_header($key, $title, $category, $class);

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
</section>

<?php get_footer(); ?>