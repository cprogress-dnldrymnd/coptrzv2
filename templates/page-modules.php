<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Modules 
/* Template Post Type: page, guides
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<div class="modules">
    <?php
    echo ___hero_modules();
    echo do_shortcode(get_post_meta(get_the_ID(), '_sections_html', true));
    ?>
</div>

<?php get_footer(); ?>