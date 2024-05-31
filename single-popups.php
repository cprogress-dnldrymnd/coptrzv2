<?php get_header() ?>
<?php while (have_posts()) : the_post(); ?>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal">
        Launch Popup
    </button>
    <!-- Modal -->
    <div class="modal fade modal-v2 popup-form" id="modal" tabindex="-1" aria-labelledby="modalSearchLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content background-white">
                <div class="modal-body p-0">
                    <div class="row">
                        <div class="col-lg-6">

                        </div>
                        <div class="col-lg-6">
                            <img src="<?= get_the_post_thumbnail_url() ?>" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endwhile; ?>
<?php get_footer() ?>