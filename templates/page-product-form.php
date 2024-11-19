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
    ?>
    <?php get_template_part('template-parts/product-form/section-video') ?>
    <?php get_template_part('template-parts/product-form/section-1') ?>
    <?php get_template_part('template-parts/product-form/section-2') ?>
    <?php get_template_part('template-parts/product-form/section-3') ?>
    <?php get_template_part('template-parts/product-form/section-4') ?>
</div>

<div class="main-product-data product-data d-none">
    <?= _single_product_data(get_the_ID()) ?>
</div>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class('', $product); ?>>
    <section class="product-main md-padding-top md-padding-bottom border-top-default no-overflow" id="buy-now">
        <div class="container">
            <h2 class="text-center">Buy <?php the_title() ?></h2>
            <div class="row g-4">
                <div class="col-7 position-relative">
                    <?php
                    /**
                     * Hook: woocommerce_before_single_product_summary.
                     *
                     * @hooked woocommerce_show_product_sale_flash - 10
                     * @hooked woocommerce_show_product_images - 20
                     */
                    do_action('woocommerce_before_single_product_summary');
                    ?>
                </div>
                <div class="col-5">
                    <div class="summary entry-summary">
                        <?php
                        /**
                         * Hook: woocommerce_single_product_summary.
                         *
                         * @hooked woocommerce_template_single_title - 5
                         * @hooked woocommerce_template_single_rating - 10
                         * @hooked woocommerce_template_single_price - 10
                         * @hooked woocommerce_template_single_excerpt - 20
                         * @hooked woocommerce_template_single_add_to_cart - 30
                         * @hooked woocommerce_template_single_meta - 40
                         * @hooked woocommerce_template_single_sharing - 50
                         * @hooked WC_Structured_Data::generate_product_data() - 60
                         */
                        do_action('woocommerce_single_product_summary');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php

    $product_guide = get__post_meta('product_guide');
    if ($product_guide) {
        $title = get_the_title();
        $pdf_url = wp_get_attachment_url($product_guide)
    ?>

        <section class="product-guide-section small-container sm-padding bg-dark ">
            <div class="container">
                <div class="download-guide rounded-corner">
                    <div class="row g-4 align-items-end justify-content-between">
                        <div class="col-auto">
                            <h3 class="text-white m-0">Download spec sheet for <br><?= $title ?></h3>
                        </div>
                        <div class="col-auto">
                            <div class="button-box button-accent request-info"><a class="rounded-10px" href="<?= $pdf_url ?>" target="_blank">Download</a></div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

    <?php } ?>

    <?php
    /**
     * Hook: woocommerce_after_single_product_summary.
     *
     * @hooked woocommerce_output_product_data_tabs - 10
     * @hooked woocommerce_upsell_display - 15
     * @hooked woocommerce_output_related_products - 20
     */
    do_action('woocommerce_after_single_product_summary');
    ?>
</div>
<?php do_action('woocommerce_after_single_product'); ?>

<?php
get_footer();
?>