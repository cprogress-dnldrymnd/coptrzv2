<section class="logo-slider">
    <div class="container">

        <div class="section-heading-description">
            <?= do_shortcode('[_heading heading="' . $module['heading'] . '" class="big-heading"]') ?>
            <?= do_shortcode('[_description description="' . $module['description'] . '" ]') ?>
        </div>

        <div class="logo-slider-box md-padding">
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