<?php get_header(); ?>
<?php 
if (false === get_template_part('template-parts/single/single', get_post_type())) {
    the_content();
} 
?>
<?php get_footer(); ?>