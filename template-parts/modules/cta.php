<section class="call-to-action">
    <div class="container">
        <div class="inner <?= $classes ?>" id="<?= $module_id ?>">
            <div class="row">
                <div class="col-lg-6">
                    <div class="column-holder">
                        <?= do_shortcode('[_heading heading="' . $module['heading'] . '" class="big-heading" tag="' . $module['tag'] . '"]') ?>
                        <?= do_shortcode("[_description description='" . $module['description'] . "']") ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="column-holder">
                    <?= do_shortcode('[_button id="' . $module['button_url'] . '" custom_url="' . $module['button_url_custom'] . '" button_type="' . $module['button_type'] . '" button_text="' . $module['button_text'] . '" ]'); ?>
                </div>
            </div>

        </div>
    </div>
</section>