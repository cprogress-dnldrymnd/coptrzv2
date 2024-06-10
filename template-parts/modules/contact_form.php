<section class="contact-form position-relative <?= $classes ?>" id="<?= $module_id ?>" style="<?= $style_attribute  ?>">
    <?php
    _background_image(array(
        'baground_image' => $baground_image,
        'background_image_class' => $background_image_class,
        'background_overlay_image' => $background_overlay_image,
    ));
    ?>
    <div class="container position-relative <?= $container_width_class ?>" style="<?= $container_width_style_attribute ?>">
        <div class="inner">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <div class="column-holder content-margin max-width <?= $classes_text_color ?>">
                        <?= do_shortcode('[_heading heading_prefix="' . $module['heading_prefix'] . '" heading_suffix="' . $module['heading_suffix'] . '" heading="' . $module['heading'] . '" class="' . ($module['size'] ? $module['size'] : 'big-heading') . '" tag="' . $module['tag'] . '"]') ?>
                        <?= do_shortcode('[_description description="' . _format_text($module['description']) . '" ]') ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="column-holder content-margin form-holder form-style-2 rounded-corner">
                        <?= do_shortcode('[_heading heading="' . $module['form_heading'] . '" tag="h3" class="text-center color-black"]') ?>
                        <?= do_shortcode($module['contact_form_shortcode']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>