<section class="post-details sm-padding-top sm-padding-bottom overflow-hidden">
    <div class="container-fluid">
        <div class="row g-0 justify-content-center">
            <div class="col-lg-7 px-5">
                <div class="row g-4 justify-content-between">
                    <div class="col-auto">
                        <?= do_shortcode('[blog_meta]') ?>
                    </div>
                    <div class="col-auto ">
                        <div class="d-flex justify-content-end">
                            <?= do_shortcode('[post_link]') ?>
                            <?= do_shortcode('[social_share]') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="post-content-v2 sm-padding-top sm-padding-bottom no-overflow border-top-default">
    <div class="container-fluid">
        <div class="row g-0">
            <div class="col col-post-nav">
                <div class="column-holder post-navigation-holder">
                    <h3>Contents</h3>
                    <ul id="post-navigation" class="d-flex flex-wrap list-inline">

                    </ul>
                </div>
            </div>
            <div class="col-lg-7 col-post-content px-5">
                <div class="column-holder the-content content-margin fw-light" id="post-content">
                    <?php the_content() ?>
                    <?= do_shortcode('[social_share]') ?>
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