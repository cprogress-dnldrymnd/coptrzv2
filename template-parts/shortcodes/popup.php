<!-- Modal -->
<?php
$args = array(
    'p' => $id,
);
$query = new WP_Query($args);
while ($query->have_posts()) {
    $query->the_post();
?>

    <div class="modal fade modal-v2 popup-form" id="modal-<?= $id ?>" tabindex="-1" aria-labelledby="modalSearchLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content background-white">
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-lg-6">
                            <div class="form-holder px-4 py-5 h-100 d-flex align-items-center">
                                <div class="form-inner w-100">
                                    <?= do_shortcode(get_the_content(NULL, false, $id)) ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 bg-image">
                            <div class="position-relative h-100">
                                <img src="<?= get_the_post_thumbnail_url($id, 'large') ?>" alt="<?= get_the_title($id) ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>