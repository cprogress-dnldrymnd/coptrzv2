<section class="call-to-action">
    <div class="container <?= $container_width_class ?>" style="<?= $container_width_style_attribute ?>">
        <div class="inner position-relative rounded-corner overflow-hidden <?= $classes ?>" style="<?= $style_attribute ?>" id="<?= $fields_id ?>">
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
                        <?= do_shortcode('[_heading heading_prefix="' . $fields['heading_prefix'] . '" heading_suffix="' . $fields['heading_suffix'] . '" heading="' . $fields['heading'] . '" class="' . ($fields['size'] ? $fields['size'] : 'big-heading') . '" tag="' . $fields['tag'] . '"]') ?>
                        <?= do_shortcode("[_description description='" . _format_text($fields['description']) . "']") ?>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="column-holder text-lg-end">
                        <?= do_shortcode('[_button class="' . $fields['button_style'] . '" id="' . $fields['button_url'] . '" custom_url="' . $fields['button_url_custom'] . '" button_type="' . $fields['button_type'] . '" button_text="' . $fields['button_text'] . '" ]'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>