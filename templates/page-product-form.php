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
</div>
<?php
get_footer();
?>