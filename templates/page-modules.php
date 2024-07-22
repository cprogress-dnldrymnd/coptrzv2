<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Modules 
/* Template Post Type: page, guides, industries
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<div class="modules">
    <?php
    echo ___hero_modules();
    echo do_shortcode(___sections('sections', get_the_ID()));
    ?>
</div>

<?php get_footer(); ?>