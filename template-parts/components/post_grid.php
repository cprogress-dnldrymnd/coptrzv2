<?php
$post_elements = $module['post_elements'];
?>

<div class="post-grid h-100 rounded-corner p-5 post-<?= $id ?> <?= $class ?>" style="--border-radius: 10px; --padding: 40%">
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
                case 'featured_image':
                    $rounded_corners = $el['rounded_corners'] ? 'true' : 'false';
                    $image_id = get_post_thumbnail_id($id);
                    echo do_shortcode('[_image class="image-absolute" size="' . $el['size'] . '" rounded_corners="' . $rounded_corners . '" border_radius="' . $el['border_radius'] . '" id="' . $image_id . '" ]');
                    break;
                case 'post_excerpt':
                        echo do_shortcode('[_description description="' . _format_text(get_the_excerpt()) . '" ]');
                    break;
                case 'permalink':
                    echo do_shortcode('[_button id="' . $id . '"  button_type="' . get_post_type($id) . '" button_text="Read More" ]');

                    break;
            }
        }
        ?>
    </div>
</div>