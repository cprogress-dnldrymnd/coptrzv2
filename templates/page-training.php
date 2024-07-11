<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Training 
/* Template Post Type: product
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