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
    the_content();
    echo do_shortcode('[__heading heading="test"]');
   // echo modules($modules);
    ?>
</div>

<?php get_footer(); ?>