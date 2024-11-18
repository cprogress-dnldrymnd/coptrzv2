<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Product Form 
/* Template Post Type: product
/*-----------------------------------------------------------------------------------*/
?>
<?php
get_header();

?>
<div class="modules">
    <?php
    echo do_shortcode(___hero_modules());
    echo get_page_template_slug();
    ?>
    <?php get_template_part('template-parts/product-form/section-1') ?>
</div>
<?php
get_footer();
?>