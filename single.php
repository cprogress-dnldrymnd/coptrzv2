<?php get_header(); ?>
<?php
echo ___hero_modules('text-start', 'small-hero');
if (false === get_template_part('template-parts/single/single', get_post_type())) {
    the_content();
}
?>
<?php get_footer(); ?>