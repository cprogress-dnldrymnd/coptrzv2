<?php
$key = array_search('permalink', $post_elements);
echo $key;
echo 'test';
echo '<pre>';

$arr = array_column($post_elements, '_type');
var_dump($arr);
echo '</pre>';


?>
<div class="post-grid post-grid-style-2 h-100 d-flex post-<?= $id ?> <?= $classes ?>" style="<?= $style_attribute_post ?>--padding: 40%">
    <div class="inner position-relative content-margin-small w-100" style="<?= $style_attribute_inner ?>">
        <?php
        foreach ($post_elements as $el) {
            $type = $el['_type'];
            switch ($type) {
                case 'post_title':
                    $class = '';
                    $style_attribute = '';
                    if ($el['size']) {
                        $class .= $el['size'] . ' ';
                    }
                    if ($el['text_color']) {
                        $class .= $el['text_color'] . ' ';
                    }

                    if ($el['text_color_custom']) {
                        $style_attribute .= '--color: ' . $el['text_color_custom'];
                    }
                    echo do_shortcode('[_heading size="' . $el['size'] . '" style="' . $style_attribute . '" class="' . $class . '" heading="' . get_the_title($id) . '" tag="h3"]');
                    break;
                case 'featured_image':
                    $rounded_corners = $el['rounded_corners'] ? 'true' : 'false';
                    $image_id = get_post_thumbnail_id($id);
                    echo do_shortcode('[_image class="image-absolute" size="' . $el['size'] . '" rounded_corners="' . $rounded_corners . '" border_radius="' . $el['border_radius'] . '" id="' . $image_id . '" ]');
                    break;
                case 'post_excerpt':
                    if (get_the_excerpt()) {
                        echo do_shortcode('[_description description="' . _format_text(custom_excerpt_length(get_the_excerpt())) . '" ]');
                    }
                    break;
                case 'permalink':
                    echo do_shortcode('[_button class="' . $el['button_style'] . '" id="' . $id . '"  button_type="' . get_post_type($id) . '" button_text="Read More" ]');
                    break;
                case 'custom_field_1':
                case 'custom_field_2':
                case 'custom_field_3':
                case 'custom_field_4':
                case 'custom_field_5':
                    $custom_field_type = $el['custom_field_type'];
                    switch ($custom_field_type) {
                        case 'text':
                            echo '<div class="custom-field ' . $el['custom_field_class'] . '">';
                            echo get_post_meta($id, $el['custom_field_key'], true);
                            echo '</div>';
                            break;
                    }
                    break;
            }
        }
        ?>
    </div>
</div>