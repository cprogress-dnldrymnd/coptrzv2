<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Modules 
/* Template Post Type: page, coptrztemplates
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<?php
$modules = get__post_meta('modules');
?>


<div class="modules">
    <?php
    echo get_the_content();
    ?>
</div>

<?php get_footer(); ?>