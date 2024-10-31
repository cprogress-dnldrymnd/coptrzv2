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
<div class="main-product-data product-data d-none">
    <?= _single_product_data(get_the_ID()) ?>
</div>
<?= training_template() ?>
<div class="modules">
    <?php
    echo do_shortcode(___sections('sections_after_main', get_the_ID()));
    ?>
</div>
<?php get_footer(); ?>