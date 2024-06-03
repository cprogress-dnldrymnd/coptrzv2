<section class="logo-slider <?= $classes ?>" id="<?= $module_id ?>">
    <div class="container-fluid p-0">
        <?php if ($module['display_heading_description'] && ($module['heading'] || $module['description'])) {  ?>
            <div class="section-heading-description content-margin mb-5 <?= $module['text_align'] ?>">
                <?= do_shortcode('[_heading heading="' . $module['heading'] . '" class="' . $module['size'] . '"]') ?>
                <?= do_shortcode('[_description description="' . $module['description'] . '" ]') ?>
            </div>
        <?php } ?>
        <div class="logo-slider-box">
            <div class="swiper mySwiper-logoSwiper">
                <div class="swiper-wrapper text-center align-items-center">
                    <?php foreach ($module['images'] as $logo) { ?>
                        <div class="swiper-slide">
                            <?= do_shortcode('[_image id="' . $logo . '"]'); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>