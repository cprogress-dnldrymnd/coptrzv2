<?php

/**
 * The template for displaying the home/index page.
 * This template will also be called in any case where the Wordpress engine 
 * doesn't know which template to use (e.g. 404 error)
 */
get_header(); // This fxn gets the header.php file and renders it 
?>

<?php get_template_part('template-parts/section/content-breadcrumbs'); ?>
<?php
get_template_part('template-parts/archive/archive-post-ajax');
?>

<?php get_footer(); // This fxn gets the footer.php file and renders it 
?>