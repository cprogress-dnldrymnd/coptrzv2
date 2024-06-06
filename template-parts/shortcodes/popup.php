<!-- Modal -->
<?php
if ($is_shortcode == true) {
    $id = $id;
} else {
    $id = get_the_ID();
}
$popup_layout = get__post_meta_by_id($id, 'popup_layout');
?>
<div class="modal fade modal-v2 popup-form" id="modal-<?= $id ?>" tabindex="-1" aria-labelledby="modalSearchLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content background-white">
            <div class="modal-body p-0">
                <?php if ($popup_layout == 'contact_form') { ?>
                    <div class="row g-0">
                        <div class="<?= get_the_post_thumbnail_url($id) ? 'col-lg-6 ' : 'col-12' ?>">
                            <div class="form-holder px-4 py-5 h-100 d-flex align-items-center">
                                <div class="form-inner w-100">
                                    <?php
                                    if ($is_shortcode == true) {
                                        echo do_shortcode(get_the_content(NULL, false, $id));
                                    } else {
                                        the_content();
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php if (get_the_post_thumbnail_url($id)) { ?>
                            <div class="<?= get_the_post_thumbnail_url($id) ? 'col-lg-6 ' : 'col-12' ?> bg-image">
                                <div class="position-relative h-100">
                                    <img src="<?= get_the_post_thumbnail_url($id, 'large') ?>" alt="<?= get_the_title($id) ?>">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else {  ?>
                    <div class="popup-content-default p-4">
                        <?php
                        if ($is_shortcode == true) {
                            echo do_shortcode(get_the_content(NULL, false, $id));
                        } else {
                            the_content();
                        }
                        ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>