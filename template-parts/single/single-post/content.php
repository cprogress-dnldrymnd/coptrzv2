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