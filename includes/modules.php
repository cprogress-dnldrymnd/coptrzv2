<?php
function action_module_content()
{
    // Check if a post was updated (add your specific conditions here)
    if (did_action('post_updated')) {
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (_is_module()) {
            $post_content = '<!-- wp:html -->';

            $post_content .= ___hero();
            $post_content .= ___sections();


            $post_content .= '<!-- /wp:html -->';

            $my_post = array(
                'ID'           => get_the_ID(),
                'post_content' => $post_content,
            );

            // Update the post into the database
            wp_update_post($my_post);
        }
    }
}
add_action('shutdown', 'action_module_content');

function ___hero()
{
    $hero_heading = get__post_meta('hero_heading');
    $hero_heading = get__post_meta('hero_heading');
    $hero_description = _format_text(get__post_meta('hero_description'));
    $hero_hidden = get__post_meta('hero_hidden');
    $hero_background = get__post_meta('hero_background');
    $hero_heading_val = $hero_heading ? $hero_heading : get_the_title();
    if (!$hero_hidden) {
        $hero = "<section class='hero pb-50px text-center rounded-10px bg-primary overflow-hidden text-white d-flex align-items-end mx-20px position-relative'>";
        $hero .= _bg_image($hero_background);
        $hero .= "<div class='container'>";
        $hero .= __heading(array(
            'heading' => $hero_heading_val,
            'tag' => 'h1',
            'class' => _attribute('class', array('large-heading')),
            ''
        ));
        $hero .= __description(array(
            'description' => $hero_description,
            'class' => _attribute('class', array('description-box', 'medium-text')),
        ));
        $hero .= "</div>";
        $hero .= "</section>";
        return $hero;
    }
}

function ___sections()
{
    $sections = get__post_meta('sections');
    $html = '';
    foreach ($sections as $key => $section) {
        $disable_section = $section['disable_section'];
        if (!$disable_section) {
            $section_id = $section['section_id'];
            $section_items = $section['section_items'];
            $section_styles = $section['section_styles'];
            $section_id_val  = $section_id ? $section_id : 'section-' . $key;

            $classes[] = 'section';
            $classes[] = 'section-' . $key;
            $styles = [];
            $container_styles = [];
            foreach ($section_styles as $section_style) {
                $type = $section_style['_type'];
                switch ($type) {
                    case 'padding':
                        $classes[] = $section_style['padding_top'];
                        $classes[] = $section_style['padding_bottom'];
                        $classes[] = $section_style['padding_left'];
                        $classes[] = $section_style['padding_right'];
                        break;
                    case 'margin':
                        $classes[] = $section_style['margin_top'];
                        $classes[] = $section_style['margin_bottom'];
                        $classes[] = $section_style['margin_left'];
                        $classes[] = $section_style['margin_right'];
                        break;
                    case 'custom_class':
                        $classes[] = $section_style['custom_class'];
                        break;
                    case 'alignment':
                        $classes[] = $section_style['align_items'];
                        $classes[] = $section_style['justify_content'];
                        $classes[] = $section_style['text_align'];
                        if ($section_style['align_items'] || $section_style['justify_content']) {
                            $classes[] = 'd-flex';
                        }
                        break;
                    case 'text_color':
                        $text_color_custom = $section_style['text_color_custom'];
                        $classes[] = $section_style['text_color'];
                        if ($text_color_custom) {
                            $styles[] = 'color: ' . $text_color_custom;
                        }
                        break;
                    case 'background_color':
                        $background_color_custom = $section_style['background_color_custom'];
                        $classes[] = $section_style['background_color'];
                        if ($background_color_custom) {
                            $styles[] = 'background-color: ' . $background_color_custom;
                        }
                        break;
                    case 'background_image':
                        $background_image = $section_style['background_image'];
                        $classes[] = $section_style['background_attachment'];
                        $classes[] = $section_style['background_size'];
                        $classes[] = $section_style['background_repeat'];
                        if ($background_image) {
                            $styles[] = 'background-image: url(' . wp_get_attachment_image_url($background_image, 'full') . ')';
                        }
                        break;
                    case 'container_width':
                        $classes[] = $section_style['container_width'];
                        if ($section_style['custom_container_width']) {
                            $container_styles[] = 'max-width: ' . $section_style['custom_container_width'];
                        }
                        break;
                    case 'border':
                        if ($section_style['border_radius']) {
                            $styles[] = '--border-radius: ' . $section_style['border_radius'];
                            $classes[] = 'rounded-corner';
                        }
                        break;
                }
            }


            $id = _attribute('id', array($section_id_val));
            $classes_attr = _attribute('class', $classes);
            if ($styles) {
                $styles_val = _attribute('style', $styles, ';');
            }

            if ($container_styles) {
                $container_styles_val = _attribute('style', $container_styles, ';');
            }


            $section_attribute = _attributes(array($classes_attr, $id, $styles_val));

            $html .= "<section $section_attribute>";
            $html .= "<div class='container' $container_styles_val>";

            foreach ($section_items as $key => $items) {
                $type = $items['_type'];
                switch ($type) {
                    case 'heading':
                        $html .= ____heading_modules($items);
                        break;
                    case 'columns':
                        $html .= ____columns_modules($items, $id . $key);
                        break;
                    case 'description':
                        $html .= __description(array(
                            'description' => $items['description'],
                            'class' => _attribute('class', array('description-box'))
                        ));
                        break;
                }
            }

            $html .= "</div>";
            $html .= "</section>";
        }
    }
    return $html;
}
function ____gallery_modules($data)
{
    $gallery = $data['gallery'];
    $gallery_style = $data['gallery_style'];
    $number_of_slides = $data['number_of_slides'];
    $number_of_slides_tablet = $data['number_of_slides_tablet'];
    $number_of_slides_mobile = $data['number_of_slides_mobile'];
    $image_args = [];
    if ($gallery) {
        $html  = "<div class='gallery $gallery_style'>";

        if ($gallery_style == 'logo-slider') {
            $image_args['class'] = _attribute('class', array('swiper-slide'));

            $number_of_slides_attr = _attribute('number_of_slides', array($number_of_slides));
            $number_of_slides_tablet_attr = _attribute('number_of_slides_tablet', array($number_of_slides_tablet));
            $number_of_slides_mobile_attr = _attribute('number_of_slides_mobile', array($number_of_slides_mobile));
            $attributes = _attributes($number_of_slides_attr, $number_of_slides_tablet_attr, $number_of_slides_mobile_attr);

            $html .= "<div class='swiper swiper-logo-slider' $attributes";
            $html .= '<div class="swiper-wrapper">';
        } else {
            $html .= '<div class="row g-5">';
            $image_args['class'] = _attribute('class', array('col-lg-4'));
        }

        foreach ($gallery as $image) {
            $image_args['image_id'] = $image;

            $html .= __image($image_args);
        }
        if ($gallery_style == 'logo-slider') {
            $html  .= "</div>";
            $html  .= "</div>";
        } else {
            $html  .= "</div>";
        }
        $html  .= "<div>";
    }

    return $html;
}
function ____columns_modules($items, $id)
{
    $columns = $items['columns'];
    $column_styles = $items['column_styles'];
    $individual_column_settings = $items['individual_column_settings'];
    $classes = [];
    $styles = [];
    $container_styles = [];
    if (!$individual_column_settings) {
        foreach ($column_styles as $column_style) {
            $type = $column_style['_type'];
            switch ($type) {
                case 'padding':
                    $classes[] = $column_style['padding_top'];
                    $classes[] = $column_style['padding_bottom'];
                    $classes[] = $column_style['padding_left'];
                    $classes[] = $column_style['padding_right'];
                    break;
                case 'margin':
                    $classes[] = $column_style['margin_top'];
                    $classes[] = $column_style['margin_bottom'];
                    $classes[] = $column_style['margin_left'];
                    $classes[] = $column_style['margin_right'];
                    break;
                case 'custom_class':
                    $classes[] = $column_style['custom_class'];
                    break;
                case 'alignment':
                    $classes[] = $column_style['align_items'];
                    $classes[] = $column_style['justify_content'];
                    $classes[] = $column_style['text_align'];
                    if ($column_style['align_items'] || $column_style['justify_content']) {
                        $classes[] = 'd-flex';
                    }
                    break;
                case 'text_color':
                    $text_color_custom = $column_style['text_color_custom'];
                    $classes[] = $column_style['text_color'];
                    if ($text_color_custom) {
                        $styles[] = 'color: ' . $text_color_custom;
                    }
                    break;
                case 'background_color':
                    $background_color_custom = $column_style['background_color_custom'];
                    $classes[] = $column_style['background_color'];
                    if ($background_color_custom) {
                        $styles[] = 'background-color: ' . $background_color_custom;
                    }
                    break;
                case 'background_image':
                    $background_image = $column_style['background_image'];
                    $classes[] = $column_style['background_attachment'];
                    $classes[] = $column_style['background_size'];
                    $classes[] = $column_style['background_repeat'];
                    if ($background_image) {
                        $styles[] = 'background-image: url(' . wp_get_attachment_image_url($background_image, 'full') . ')';
                    }
                    break;
                case 'container_width':
                    $classes[] = $column_style['container_width'];
                    if ($column_style['custom_container_width']) {
                        $container_styles[] = 'max-width: ' . $column_style['custom_container_width'];
                    }
                    break;
                case 'border':
                    $border_style = $column_style['border_style'];

                    if ($column_style['border_radius']) {
                        $styles[] = '--border-radius: ' . $column_style['border_radius'];
                        $classes[] = 'rounded-corner';
                    }

                    if ($border_style == 'border-custom') {
                        $border_color = $column_style['border_color'];
                        $border_color_custom = $column_style['border_color_custom'];
                        $border_width = $column_style['border_width'];
                        if ($border_color == 'border-custom-color') {
                            $classes[] = $column_style['border_color'];
                        } else {
                            $styles[] = 'border-color: ' . $border_color_custom;
                        }
                        if ($border_width) {
                            $styles[] = 'border-width: ' . $border_width;
                        }
                    } else {
                        $classes[] = 'border-default';
                    }

                    break;
            }
        }
    }


    $classes[] = 'column-holder';
    $classes[] = 'content-margin';

    if ($styles) {
        $styles_val = _attribute('style', $styles, ';');
    }

    if ($classes) {
        $classes_val = _attribute('class', $classes, ' ');
    }
    $column_attributes = _attributes(array($classes_val, $styles_val));

    $html = "<div class='row g-4'>";
    foreach ($columns as $column) {
        $items = $column['items'];

        $html .= '<div class="col">';

        $html .= "<div $column_attributes>";
        foreach ($items as $item) {
            $type = $item['_type'];
            switch ($type) {
                case 'heading':
                    $html .= ____heading_modules($item);
                    break;
                case 'icon':
                    $html .= ____icon_modules($item);
                    break;
                case 'description':
                    $html .= __description(array(
                        'description' => $item['description'],
                        'class' => _attribute('class', array('description-box'))
                    ));
                    break;
                case 'gallery':
                    $html .= ____gallery_modules(array(
                        'id' => $item['id'],
                        'gallery' => $item['gallery'],
                        'gallery_style' => $item['gallery_style'],
                        'number_of_slides' => $item['number_of_slides'],
                        'number_of_slides_tablet' => $item['number_of_slides_tablet'],
                        'number_of_slides_mobile' => $item['number_of_slides_mobile'],
                    ));
                    break;
            }
        }
        $html .= '</div>';
        $html .= '</div>';
    }
    $html .= '</div>';

    return $html;
}

function ____icon_modules($items)
{
    $icon_data['id'] = $items['icon'];
    $icon_color = $items['icon_color'];
    $icon_color_custom = $items['icon_color_custom'];
    $icon_width = $items['icon_width'];
    $icon_height = $items['icon_height'];
    $classes[] = 'icon-box';
    $styles = [];
    if ($icon_color) {
        $classes[] = $icon_color;
    }

    if ($icon_color == 'text-custom') {
        $styles[] = 'color: ' . $icon_color_custom;
    } else {
        if ($icon_color) {
            $classes[] = $icon_color;
        }
    }

    if ($icon_width) {
        $styles[] = '--width: ' . $icon_width;
    }
    if ($icon_height) {
        $styles[] = '--height: ' . $icon_height;
    }

    if ($classes) {
        $icon_data['class'] = _attribute('class', $classes);
    }
    if ($styles) {
        $icon_data['styles'] = _attribute('style', $styles, ';');
    }

    return _icon($icon_data);
}
function ____heading_modules($items)
{
    $has_suffix = $items['has_suffix'];
    $has_prefix = $items['has_prefix'];
    $has_custom_heading_settings = $items['has_custom_heading_settings'];
    $heading = $items['heading'];
    $prefix = $items['prefix'];
    $suffix = $items['suffix'];
    $tag = $items['tag'];
    $size = $items['size'];
    $text_color = $items['text_color'];
    $text_align = $items['text_align'];
    $text_color_custom = $items['text_color_custom'];
    $heading_data['heading'] = $heading;

    $classes = [];
    $styles = [];
    if ($has_custom_heading_settings) {
        if ($tag) {
            $heading_data['tag'] = $tag;
        }
        if ($size) {
            $classes[] = $size;
        }
        if ($text_align) {
            $classes[] = $text_align;
        }

        if ($text_color == 'text-custom') {
            $styles[] = 'color: ' . $text_color_custom;
        } else {
            if ($text_color) {
                $classes[] = $text_color;
            }
        }
    }


    if ($has_suffix && $suffix) {
        $heading_data['suffix'] = $suffix;
        $classes[] = 'heading-box';
    }
    if ($has_prefix && $prefix) {
        $heading_data['prefix'] = $prefix;
        $classes[] = 'heading-box';
    }
    if ($classes) {
        $heading_data['class'] = _attribute('class', $classes);
    }
    if ($styles) {
        $heading_data['styles'] = _attribute('style', $styles, ';');
    }
    return __heading($heading_data);
}

function _styles()
{
}
function _attribute($name, $attributes, $separator = ' ')
{
    $html = "$name='";
    $html .= implode($separator, array_unique($attributes));
    $html .= "'";
    return $html;
}

function _attributes($attributes)
{
    $html = '';
    foreach ($attributes as $attribute) {
        $html .= $attribute;
    }

    return $html;
}


function _format_text($text)
{
    return htmlentities($text);
}

function _is_module()
{
    if (get_page_template_slug() == 'templates/page-modules.php') {
        return true;
    } else {
        return false;
    }
}


function _output_svg_from_url($url)
{
    $content = file_get_contents($url);

    // Output the sanitized SVG
    return $content;
}
