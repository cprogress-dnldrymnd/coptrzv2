<section class="logo-slider <?= $classes ?>">
    <div class="container">
        <div class="section-heading-description">
            <?= do_shortcode('[_heading heading="' . $module['heading'] . '" class="text-center mb-5" tag="h3"]') ?>
            <?= do_shortcode('[_description description="' . $module['description'] . '" ]') ?>
        </div>

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