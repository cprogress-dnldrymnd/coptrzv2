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
}
echo do_shortcode(___hero_archive($key));
echo ___featured($key);
?>

<section class="archive-posts md-padding-top md-padding-bottom">
    <?php
    echo ___posts_header($title, 'category');
    ?>
    <div class="post-grid-holder">
        <div class="container">
            <div class="row g-4">
                <?php
                while (have_posts()) {
                    the_post();
                    __post_box_blog(get_the_ID(), true);
                }
                ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>