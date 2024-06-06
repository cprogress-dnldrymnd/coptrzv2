<div class="post-grid h-100 rounded-corner p-5 post-<?= $id ?> <?= $class ?>" style="--border-radius: 10px; --padding: 20%">
    <div class="content-margin h-100">
        <?= do_shortcode('[_image class="image-absolute image-absolute-contain" id="' . get_post_thumbnail_id($id) . '"]'); ?>
        <?= do_shortcode('[_heading heading="' . get_the_title($id) . '" tag="h3"]') ?>
        <?= do_shortcode('[_description description="' . _format_text(get_the_excerpt($id)) . '" ]') ?>
        <?php if (get_post_type($id) == '3dmodellibraries') { ?>
            <?php
            $captured_by = get__post_meta_by_id($id, 'captured_by');
            ?>
            <div class="button-box button-accent" >
                <button description='<?= _format_text(get_the_content(null, false, $id)) ?>' title="<?= get_the_title($id) ?>" id="modal-button-<?= $id ?>" data-bs-toggle="modal" data-bs-target="#modal-<?= $popup_id ?>">
                    View Model
                </button>
            </div>

            <?php if ($captured_by) { ?>
                <p>
                    Captured by
                </p>
                <?= do_shortcode('[_image id="' . $captured_by . '"]'); ?>
            <?php } ?>
        <?php } else { ?>

            <?= do_shortcode('[_button id="' . $id . '"  button_type="' . get_post_type($id) . '" button_text="Read More" ]'); ?>

        <?php } ?>
    </div>
</div>