<?php
function _elements($data, $module_id, $same_height_images)
{
    ob_start();
    foreach ($data as $d) {
        $type = $d['_type'];
        switch ($type) {
            case 'heading':
                echo do_shortcode('[_heading tag="' . $d['tag'] . '" heading="' . $d['heading'] . '" class="' . $d['size'] . '"]');
                break;
            case 'description':
                echo do_shortcode("[_description description='" . $d['description'] . "']");
                break;
            case 'image':
                $same_height = $same_height_images ? 'same_height="true"' : 'same_height="false"';
                $rounded_corners = $d['rounded_corners'] ? 'true' : 'false';
                echo do_shortcode('[_image size="' . $d['size'] . '" rounded_corners="' . $rounded_corners . '" border_radius="' . $d['border_radius'] . '" ' . $same_height . '  id="' . $d['image'] . '" image_width="' . $d['image_width'] . '" image_height="' . $d['image_height'] . '"]');
                break;
            case 'button':
                echo do_shortcode('[_button class="' . $d['button_style'] . '" id="' . $d['button_url'] . '" button_url_custom="' . $d['button_url_custom'] . '" button_type="' . $d['button_type'] . '" button_text="' . $d['button_text'] . '" ]');
                break;
            case 'accordion':
                $accordion = $d['accordion'];
                include locate_template('template-parts/components/accordion.php');
                break;
            case 'custom_html':
                echo $d['custom_html'];
                break;
        }
    }
    return ob_get_clean();
}

function _background_image($baground_image, $background_image_class, $background_overlay_image)
{
    $return = '';
    if ($baground_image) {
        $return .= do_shortcode('[_image class="background-image ' . $background_image_class . '" id="' . $baground_image . '"]');
    }

    if ($background_overlay_image) {
        $return .= do_shortcode('[_image class="background-image-overlay" id="' . $background_overlay_image . '"]');
    }

    return $return;
}
