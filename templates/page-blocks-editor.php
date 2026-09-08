<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Blocks Editor
/* Template Post Type: page, industries, guides, capabilities, casestudies, product, producttaxonomypages
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
 <?php
    while (have_posts()) :
        the_post();
        if (function_exists('coptrz_sections_render_converted') && coptrz_sections_render_converted(get_the_ID())) {
            // Section-converter output: render the frozen blocks WITHOUT
            // the_content's wpautop (which mangles frozen Custom HTML
            // snapshots) — same path templates/page-gutenberg.php uses. A
            // hero block (if any) renders inline at its own position either
            // way (coptrz_render_hero_block(), includes/hero-block.php) —
            // this template has no separate ___hero_modules() call.
            echo coptrz_render_converted_sections('sections', get_the_ID());
        } else {
            the_content();
        }
    endwhile; // End of the loop.
    wp_reset_postdata();
    ?>
<?php get_footer(); ?>
