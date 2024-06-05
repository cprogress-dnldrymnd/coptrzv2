<section class="logo-slider position-relative <?= $classes ?>" id="<?= $module_id ?>" style="<?= $style_attribute ?>">
    <?php
    _background_image(array(
        'baground_image' => $baground_image,
        'background_image_class' => $background_image_class,
        'background_overlay_image' => $background_overlay_image,
    ));
    ?>
    <div class="container-fluid position-relative p-0 <?= $container_width_class ?>">
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