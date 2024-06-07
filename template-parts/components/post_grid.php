<?php
$post_elements = $module['post_elements'];
?>

<div class="post-grid h-100 rounded-corner p-5 post-<?= $id ?> <?= $class ?>" style="--border-radius: 10px; --padding: 20%">
    <div class="content-margin h-100">
        <?php
        foreach ($post_elements as $el) {
            $type = $el['_type'];
            switch ($type) {
                case 'post_title':
                    if ($el['size']) {
                        $class .= $el['size'] . ' ';
                    }
                    if ($el['text_color']) {
                        $class .= $el['text_color'] . ' ';
                    }

                    if ($el['text_color_custom']) {
                        $style_attribute .= '--color: ' . $el['text_color_custom'];
                    }
                    echo do_shortcode('[_heading style="' . $style_attribute . '" class="' . $class . '" heading="' . get_the_title($id) . '" tag="h3"]');
                    break;
                case 'post_title':
                    $class = $el['text_color'];
                    echo do_shortcode('[_heading class="' . $class . '" heading="' . get_the_title($id) . '" tag="h3"]');
                    break;
            }
        }

        ?>
        <?= do_shortcode('[_image class="image-absolute image-absolute-contain" id="' . get_post_thumbnail_id($id) . '"]'); ?>
        <?= do_shortcode('[_heading heading="' . get_the_title($id) . '" tag="h3"]') ?>
        <?= do_shortcode('[_description description="' . _format_text(get_the_excerpt($id)) . '" ]') ?>
        <?php if (get_post_type($id) == '3dmodellibraries') { ?>
            <?php
            $captured_by = get__post_meta_by_id($id, 'captured_by');
            ?>
            <div class="button-box button-accent">
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