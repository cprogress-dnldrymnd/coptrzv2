<?php

/**
 * The template for displaying the home/index page.
 * This template will also be called in any case where the Wordpress engine 
 * doesn't know which template to use (e.g. 404 error)
 */
get_header(); ?>


<?php
if (is_home()) {
    $key = 'post_';
    $title = 'All Posts';
    $has_featured = true;
} else if(is_post_type_archive('events')) {
    $key = 'events';
    $title = 'All Events';
    $has_featured = false;

}
echo do_shortcode(___hero_archive($key));
if($has_featured) {
    echo ___featured($key);
}
?>

<section class="archive-posts md-padding-top md-padding-bottom border-top-default">
    <?php
    echo ___posts_header($title, 'category');
    ?>
    <div class="post-grid-holder">
        <div class="container">
            <div class="row g-4 same-image-height">
                <?php
                while (have_posts()) {
                    the_post();
                    echo __post_box_blog(get_the_ID(), true);
                }
                ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>