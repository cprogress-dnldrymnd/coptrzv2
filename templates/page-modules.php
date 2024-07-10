<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Modules 
/* Template Post Type: page, coptrztemplates
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<div class="modules">
    <?php
    echo do_shortcode(___sections('sections', get_the_ID()));
    ?>
</div>

<?php get_footer(); ?>