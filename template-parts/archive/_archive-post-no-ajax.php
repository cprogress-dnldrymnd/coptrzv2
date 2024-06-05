<section class="archive-grid-v2 md-padding">
    <div class="container">
        <div class="section-heading-description content-margin mb-5 text-center">
            <?= do_shortcode('[_heading heading="Explore our Industry Solutions"]') ?>
            <?= do_shortcode('[_description description="Before we can help, you need to tell us a bit about you. Which sector below suits your business needs the most? Then we’ll introduce you to your industry expert." ]') ?>
        </div>

        <?php if (have_posts()) { ?>
            <div class="row g-5">
                <?php while (have_posts()) {
                    the_post() ?>
                    <div class="col-lg-3">
                        <div class="post-grid h-100 background-white overflow-hidden rounded-corner post-<?= get_the_ID() ?>" style="--border-radius: 15px; --padding: 30%">
                            <div class="content-margin h-100">
                                <?= do_shortcode('[_image class="image-absolute" id="' . get_post_thumbnail_id(get_the_ID()) . '"]'); ?>
                                <div class="content-box content-margin p-4">
                                    <?= do_shortcode('[_heading heading="' . get_the_title() . '" tag="h3" class="small-heading"]') ?>
                                    <?php if (get_the_excerpt()) { ?>
                                        <?= do_shortcode('[_description description="' . custom_excerpt_length(get_the_excerpt(), 20) . '" ]') ?>
                                    <?php } ?>

                                    <?= do_shortcode('[_button class="button-accent" id="' . get_the_ID() . '"  button_type="' . get_post_type() . '" button_text="LEARN MORE" ]'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</section>