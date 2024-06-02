<section class="contact-form <?= $classes ?>" id="<?= $module_id ?>">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <div class="column-holder content-margin">
                    <?= do_shortcode('[_heading heading="' . $module['heading'] . '" ]') ?>
                    <?= do_shortcode('[_description description="' . $module['description'] . '" ]') ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="column-holder content-margin form-holder form-style-2">
                    <?= do_shortcode($module['contact_form_shortcode']) ?>
                </div>
            </div>
        </div>
    </div>
</section>