<?php

/**
 * The template for displaying the home/index page.
 * This template will also be called in any case where the Wordpress engine 
 * doesn't know which template to use (e.g. 404 error)
 */
get_header(); // This fxn gets the header.php file and renders it 
?>

<section class="archive-grid-v2 md-padding">
    <div class="section-heading-description content-margin mb-5 text-center">
        <?= do_shortcode('[_heading heading="Explore our Industry Solutions"]') ?>
        <?= do_shortcode('[_description description="Before we can help, you need to tell us a bit about you. Which sector below suits your business needs the most? Then we’ll introduce you to your industry expert." ]') ?>
    </div>

</section>

<?php if (have_posts()) { ?>
    <div class="row">
        <?php while (have_posts()) {
            the_post() ?>
            <div class="col-lg-4">
                <?= do_shortcode('[post_grid class="background-primary" id="' . get_the_ID() . '"]') ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>
<?php get_footer(); // This fxn gets the footer.php file and renders it 
?>