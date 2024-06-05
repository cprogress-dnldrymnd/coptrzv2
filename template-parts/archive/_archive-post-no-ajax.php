<section class="archive-grid-v2 md-padding">
    <div class="container">
        <div class="section-heading-description content-margin mb-5 text-center">
            <?= do_shortcode('[_heading heading="Explore our Industry Solutions"]') ?>
            <?= do_shortcode('[_description description="Before we can help, you need to tell us a bit about you. Which sector below suits your business needs the most? Then we’ll introduce you to your industry expert." ]') ?>
        </div>

        <?php if (have_posts()) { ?>
            <div class="row g-4">
                <?php while (have_posts()) {
                    the_post() ?>
                    <div class="col-lg-4">
                        <div class="post-grid h-100 rounded-corner post-<?= get_the_ID() ?>" style="--border-radius: 10px; --padding: 30%">
                            <div class="content-margin h-100">
                                <?= do_shortcode('[_image class="image-absolute" id="' . get_post_thumbnail_id(get_the_ID()) . '"]'); ?>
                                <?= do_shortcode('[_heading heading="' . get_the_title() . '" tag="h3"]') ?>
                                <?php if (get_the_excerpt()) { ?>
                                    <?= do_shortcode('[_description description="' . custom_excerpt_length(get_the_excerpt(), 20) . '" ]') ?>
                                <?php } ?>
                                <a href="<?= get_the_permalink() ?>">
                                    LEARN MORE
                                </a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</section>