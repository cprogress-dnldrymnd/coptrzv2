<?php get_header() ?>
<?php while (have_posts()) : the_post(); ?>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal">
        Launch Popup
    </button>
    <!-- Modal -->
    <div class="modal fade modal-v2 popup-form" id="modal" tabindex="-1" aria-labelledby="modalSearchLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content background-white">
                <div class="modal-body p-0">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="p-5">
                                <?php the_content() ?>
                            </div>
                        </div>
                        <div class="col-lg-6 bg-image position-relative">
                            <img src="<?= get_the_post_thumbnail_url(get_the_ID(), 'large') ?>" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endwhile; ?>
<?php get_footer() ?>