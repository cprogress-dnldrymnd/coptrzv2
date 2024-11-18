<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Product Form 
/* Template Post Type: page
/*-----------------------------------------------------------------------------------*/
?>
<?php
get_header();

?>
<div class="modules">
    <?php
    echo do_shortcode(___hero_modules());
    ?>
    <div class="product-form">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3">

                </div>
                <div class="col-lg-9">
                    <?php get_template_part('template-parts/product-form/section-1') ?>
                </div>
            </div>
        </div>
    </div>

</div>
<?php
get_footer();
?>