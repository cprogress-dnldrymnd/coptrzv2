<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Blocks Editor
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
 <?php
    while (have_posts()) :
        the_post();
        the_content();
    endwhile; // End of the loop.
    wp_reset_postdata();
    ?>
<?php get_footer(); ?>