<section class="post-details">
    <div class="container content-margin">
        <?php
        if (has_excerpt()) {
            echo do_shortcode('[_description description="' . _format_text(custom_excerpt_length(get_the_excerpt()), 200) . '" ]');
        }
        ?>
        <?= do_shortcode('[_image class="blog-image image-absolute rounded-corner overflow-hidden" id="' . get_post_thumbnail_id(get_the_ID()) . '"]') ?>

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

<section class="post-content-v2 md-padding no-overflow">
    <div class="container">
        <div class="row g-5">
            <div class="col col-post-nav">
                <div class="column-holder">
                    <?= do_shortcode('[_heading heading="Contents"]') ?>
                    <ul id="post-navigation" class="d-flex flex-wrap list-inline">

                    </ul>
                </div>
            </div>
            <div class="col-lg-7 col-post-content">
                <div class="column-holder the-content content-margin" id="post-content">
                    <?php the_content() ?>
                </div>
            </div>
            <div class="col col-sidebar">
                <div class="column-holder" id="blog-single-sidebar">
                    <?php dynamic_sidebar('blog_single_sidebar') ?>
                </div>
            </div>
        </div>
    </div>
</section>