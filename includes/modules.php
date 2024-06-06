<?php

function _format_text($text)
{
    return str_replace("'", "&#39;", $text);
}
function output_svg_from_url($url)
{
    $content = file_get_contents($url);
    file_get_contents('/home/devcoptrz/public_html/wp-content/uploads/2024/06/rulers.svg')
    // Security: Sanitize SVG Content (Essential)
    $allowed_tags = array(
        'svg' => array('xmlns', 'width', 'height', 'viewbox', 'class'),
        'path' => array('d', 'fill', 'stroke', 'stroke-width'),
        'rect', 'circle', 'ellipse', 'line', 'polygon', 'polyline',
        'text', 'tspan' // Add more as needed
    );
    $content = wp_kses($content, $allowed_tags);

    // Output the sanitized SVG
    echo $content;
}
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
                echo do_shortcode("[_description description='" . _format_text($d['description']) . "']");
                break;
            case 'image':
                $same_height = $same_height_images ? 'same_height="true"' : 'same_height="false"';
                $rounded_corners = $d['rounded_corners'] ? 'true' : 'false';
                echo do_shortcode('[_image size="' . $d['size'] . '" rounded_corners="' . $rounded_corners . '" border_radius="' . $d['border_radius'] . '" ' . $same_height . '  id="' . $d['image'] . '" image_width="' . $d['image_width'] . '" image_height="' . $d['image_height'] . '"]');
                break;
            case 'icon':
                echo get_stylesheet_directory();
                echo do_shortcode('[_icon id="' . $d['icon'] . '" class="' . $d['icon_color'] . '" icon_color_custom="' . $d['icon_color_custom'] . '" icon_width="' . $d['icon_width'] . '" icon_height="' . $d['icon_height'] . '"]');
                break;
            case 'button':
                echo do_shortcode('[_button class="' . $d['button_style'] . '" id="' . $d['button_url'] . '" button_url_custom="' . $d['button_url_custom'] . '" button_type="' . $d['button_type'] . '" button_text="' . $d['button_text'] . '" ]');
                break;
            case 'accordion':
                $accordion = $d['accordion'];
                $accordion_source = $d['accordion_source'];
                $faqs = $d['faqs'];
                $faqs_category = $d['faqs_category'];
                $open_first_item = $d['open_first_item'];
                if ($accordion || $faqs || $faqs_category) {
                    include locate_template('template-parts/components/accordion.php');
                }
                break;
            case 'custom_html':
                echo $d['custom_html'];
                break;
            case 'number_counters':
                $number_counters = $d['number_counters'];
                if ($number_counters) {
                    include locate_template('template-parts/components/number_counters.php');
                }
                break;
        }
    }
    return ob_get_clean();
}

function _background_image($args)
{
    $return = '';
    if ($args['baground_image']) {
        $return .= do_shortcode('[_image class="background-image ' . $args['background_image_class'] . '" id="' . $args['baground_image'] . '"]');
    }

    if ($args['background_overlay_image']) {
        $return .= do_shortcode('[_image class="background-image-overlay" id="' . $args['background_overlay_image'] . '"]');
    }

    echo $return;
}

function _section_heading_description($args)
{
    $return = '';

    if ($args['heading']) {
        $return .= '<div class="section-heading-description content-margin mb-5 ' . $args['text_align'] . '">';
        $return .= do_shortcode('[_heading  heading_prefix="' . $args['heading_prefix'] . '" heading_suffix="' . $args['heading_suffix'] . '" text_left="' . $args['text_left'] . '"  tag="' . $args['tag'] . '" heading="' . $args['heading'] . '" class="' . $args['size'] . ' ' . ($args['heading_with_line'] ? 'heading-with-line' : '') . '"]');
        $return .= do_shortcode("[_description description='" . _format_text($args['description']) . "']");
        $return .= '</div>';
        echo $return;
    }
}
