<?php

function _format_text($text)
{
    return htmlentities($text);
}
function output_svg_from_url($url)
{
    $content = file_get_contents($url);

    // Output the sanitized SVG
    echo $content;
}

function _elements($data, $module_id, $same_height_images)
{
    ob_start();
    foreach ($data as $d) {
        $type = $d['_type'];
        $class = '';
        $style_attribute = '';
        switch ($type) {
            case 'heading':
                if ($d['size']) {
                    $class .= $d['size'] . ' ';
                }
                if ($d['text_color']) {
                    $class .= $d['text_color'] . ' ';
                }

                if ($d['text_color_custom']) {
                    $style_attribute .= '--color: ' . $d['text_color_custom'];
                }
                echo do_shortcode('[_heading heading_prefix="' . $d['prefix'] . '" heading_suffix="' . $d['suffix'] . '" tag="' . $d['tag'] . '" heading="' . $d['heading'] . '" class="' . $class . '" style="' . $style_attribute . '"]');
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

            case 'embed':
                $embed = $d['embed'];
                echo do_shortcode('[_embed src="' . $embed . '"]');
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

function modules($modules)
{
    ob_start();
    foreach ($modules as $key => $module) {
        $type = $module['_type'];
        $disable_module = $module['disable_module'];
        if (!$disable_module) {
            $module_id = $module['module_id'] ? $module['module_id'] : 'module-' . get_the_ID() . '-' . $key;
            $styles = $module['styles'];
            $classes = '';
            $style_attribute = '';
            $classes_row = '';
            $classes_text_color = '';
            $container_width_class = '';
            $container_width_style_attribute = '';
            $background_image_class = '';
            $baground_image = '';
            $background_overlay_image = '';
            $inner_class = '';
            $inner_style_attribute = '';
            if ($styles) {
                foreach ($styles as $style) {
                    $style_type = $style['_type'];
                    switch ($style_type) {
                        case 'background_color':
                            if ($style['background_color'] != 'background-custom') {
                                $classes .= ' ' . $style['background_color'];
                            } else {
                                $style_attribute = 'background-color: ' . $style['background_color_custom'] . ';';
                            }
                            break;
                        case 'padding':
                            $classes .= ' ' . $style['padding_top'] . ' ' . $style['padding_bottom'] . ' ' . $style['padding_left'] . ' ' . $style['padding_right'];
                            break;
                        case 'margin':
                            $classes .= ' ' . $style['margin_top'] . ' ' . $style['margin_bottom'] . ' ' . $style['margin_left'] . ' ' . $style['margin_right'];
                            break;
                        case 'border_radius':
                            $classes .= ' rounded-corner';
                            if ($style['border_radius']) {
                                $style_attribute .= 'border-radius: ' . $style['border_radius'] . ';';
                            }
                            break;
                        case 'custom_class':
                            $classes .= ' ' . $style['custom_class'];
                            break;
                        case 'alignment':
                            $classes .= ' ' . $style['text_align'];
                            $classes_row .= ' ' . $style['align_items'] . ' ' . $style['justify_content'];
                            break;
                        case 'background_image':
                            $baground_image .= $style['background_image'];
                            $classes .= ' ' . $style['background_size'] . ' ' . $style['background_attachment'] . ' ' . $style['background_repeat'];
                            break;
                        case 'background_overlay':
                            $background_overlay_type = $style['background_overlay_type'];
                            if ($background_overlay_type == 'image') {
                                $background_overlay_image .= $style['background_overlay_image'];
                                if ($style['background_overlay_image_opacity'] || $style['background_overlay_image_opacity'] == 0) {
                                    $style_attribute .= '--background-image-opacity: ' . $style['background_overlay_image_opacity'] . ';';
                                }
                                $background_image_class .= 'no-overlay';
                            } else if ($background_overlay_type == 'custom') {
                                $style_attribute .= '--background-overlay-custom: ' . $style['background_overlay_custom'] . ';';
                                $background_image_class .= 'custom-overlay';
                            }
                            $classes .= ' ' . $style['background_size'] . ' ' . $style['background_attachment'] . ' ' . $style['background_repeat'];
                            break;
                        case 'text_color':
                            $classes_text_color .= ' ' . $style['text_color'];
                            if ($style['text_color_custom']) {
                                $style_attribute .= 'color: ' . $style['text_color_custom'] . ';';
                            }
                            break;
                        case 'container_width':
                            $container_width_class .= ' ' . $style['container_width'];
                            if ($style['custom_container_width']) {
                                $container_width_style_attribute .= 'max-width: ' . $style['custom_container_width'] . ';';
                            }
                            break;
                        case 'max_width':
                            if ($style['max_width']) {
                                $inner_class = ' max-width';
                                $inner_style_attribute .= 'max-width: ' . $style['max_width'] . '; ';
                                if ($style['centred']) {
                                    $inner_class .= ' me-auto ms-auto';
                                }
                            }
                            break;
                    }
                }
            }
            include locate_template('template-parts/modules/' . $type . '.php');
        }
    }
    return ob_get_clean();
}
