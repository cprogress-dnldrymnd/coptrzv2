<section class="call-to-action">
    <div class="container <?= $container_width_class ?>">
        <div class="inner position-relative rounded-corner overflow-hidden <?= $classes ?>" style="<?= $style_attribute .$container_width_style_attribute ?>" id="<?= $module_id ?>">
            <?php
            _background_image(array(
                'baground_image' => $baground_image,
                'background_image_class' => $background_image_class,
                'background_overlay_image' => $background_overlay_image,
            ));
            ?>
            <div class="row g-5 <?= $classes_row ?> position-relative">
                <div class="col-lg-7">
                    <div class="column-holder content-margin <?= $classes_text_color ?>">
                        <?= do_shortcode('[_heading heading="' . $module['heading'] . '" class="big-heading" tag="' . $module['tag'] . '"]') ?>
                        <?= do_shortcode("[_description description='" . $module['description'] . "']") ?>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="column-holder text-lg-end">
                        <?= do_shortcode('[_button class="' . $module['button_style'] . '" id="' . $module['button_url'] . '" custom_url="' . $module['button_url_custom'] . '" button_type="' . $module['button_type'] . '" button_text="' . $module['button_text'] . '" ]'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>