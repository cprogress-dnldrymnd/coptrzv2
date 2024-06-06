<section class="logo-slider position-relative <?= $classes ?>" id="<?= $module_id ?>" style="<?= $style_attribute . $container_width_style_attribute ?>">
    <?php
    _background_image(array(
        'baground_image' => $baground_image,
        'background_image_class' => $background_image_class,
        'background_overlay_image' => $background_overlay_image,
    ));
    ?>
    <div class="container-fluid position-relative p-0 <?= $container_width_class ?> <?= $classes_text_color ?>">
        <?php
        _section_heading_description(array(
            'heading' => $module['heading'],
            'description' => $module['description'],
            'text_align' => $module['text_align'],
            'heading_prefix' => $module['heading_prefix'],
            'heading_suffix' => $module['heading_suffix'],
            'tag' => $module['tag'],
            'size' => $module['size'],
            'heading_with_line' => $module['heading_with_line'],
        ));

        $number_of_slides = $module['number_of_slides'] ?  $module['number_of_slides'] : 4;
        $number_of_slides_tablet = $module['number_of_slides_tablet'] ?  $module['number_of_slides_tablet'] : $number_of_slides;
        $number_of_slides_mobile = $module['number_of_slides_mobile'] ?  $module['number_of_slides_mobile'] : ($number_of_slides_tablet ? $number_of_slides_tablet : $number_of_slides);
        ?>
        <div class="logo-slider-box">
            <div class="swiper mySwiper-logoSwiper-Module" id="logo-slider-<?= $module_id ?>" number_of_slides="<?= $number_of_slides ?>" number_of_slides_tablet="<?= $number_of_slides_tablet ?>" number_of_slides_mobile="<?= $number_of_slides_mobile ?>">
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