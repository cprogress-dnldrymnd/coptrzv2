<?php get_header(); ?>
<?php
if (false === get_template_part('template-parts/single/single', get_post_type())) {
    echo ___hero_modules('text-start', 'small-hero');
    echo do_shortcode(___sections('sections', get_the_ID()));
}
?>
<?php get_footer(); ?>