<section class="post-details">
    <div class="container content-margin">
        <?= do_shortcode('[_image class="blog-image image-absolute rounded-corner overflow-hidden" id="' . get_post_thumbnail_id(get_the_ID()) . '"]') ?>
        <?= do_shortcode('[_description description="' . _format_text(get_the_excerpt()) . '" ]') ?>

        <div class="row g-4 justify-content-between">
            <div class="col-auto">
                <?= do_shortcode('[blog_meta]') ?>
            </div>
            <div class="col-auto">
                <?= do_shortcode('   [post_link]') ?>
            </div>
        </div>
    </div>
</section>

<section class="post-content background-secondary scrolling-section-v2">
    <div class="container-fluid p-0">
        <div class="row gx-0 gy-4">
            <div class="col-description col-left <?= get_the_post_thumbnail() ? 'col-lg-6' : 'col-12' ?>">
                <div class="column-holder lg-padding">
                    <div class="description-box content-margin">
                        <?php the_content() ?>
                    </div>
                </div>
            </div>
            <?php if (get_the_post_thumbnail()) { ?>
                <div class="col-lg-6 col-image col-right">
                    <div class="column-holder ">
                        <div class="image-box">
                            <?php the_post_thumbnail('full') ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>