<?php get_header(); ?>
<?php
the_content();
if (false === get_template_part('template-parts/single/single', get_post_type())) {
    
}
?>
<?php get_footer(); ?>