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
            'class' => _attribute('class', array('description-box')),
        ));
        $hero .= "</div>";
        $hero .= "</section>";
        return $hero;
    }
}

function ___sections()
{
    $sections = get__post_meta('sections');
    $sections_var = '';
    foreach ($sections as $key => $section) {
        $disable_section = $section['disable_section'];
        if (!$disable_section) {
            $section_id = $section['section_id'];
            $section_items = $section['section_items'];
            $section_styles = $section['section_styles'];
            $section_id_val  = $section_id ? $section_id : 'section-' . $key;

            $classes[] = 'section';
            $classes[] = 'section-' . $key;

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
                        $classes[] = array_merge($classes, explode(" ", $section_style['custom_class']));
                        break;
                    case 'alignment':
                        $classes[] = $section_style['align_items'];
                        $classes[] = $section_style['justify_content'];
                        $classes[] = $section_style['text_align'];
                        break;
                    case 'alignment':
                        $classes[] = $section_style['align_items'];
                        $classes[] = $section_style['justify_content'];
                        $classes[] = $section_style['text_align'];
                        break;
                    case 'text_color':
                        $text_color_custom = $section_style['text_color_custom'];
                        $classes[] = $section_style['text_color'];
                        $classes[] = $section_style['justify_content'];
                        $classes[] = $section_style['text_align'];
                        if ($text_color_custom) {
                            $styles[] = 'color: ' . $text_color_custom;
                        }
                        break;
                }
            }


            $id = _attribute('id', array($section_id_val));
            $classes_attr = _attribute('class', $classes);
            if ($styles) {
                $styles = _attribute('style', $styles, ';');
            }

            $section_attribute = _attributes(array($classes_attr, $id, $styles));

            $sections_var .= "<section $section_attribute>";
            $sections_var .= "<div class='container'>";

            foreach ($section_items as $items) {
                $type = $items['_type'];
                switch ($type) {
                    case 'heading':
                        $sections_var .= ____heading_modules($items);
                        break;
                    case 'columns':
                        $sections_var .= ____columns_modules($items['columns']);
                        break;
                    case 'description':
                        $sections_var .= __description(array(
                            'description' => $items['description'],
                            'class' => _attribute('class', array('description-box'))
                        ));
                        break;
                }
            }

            $sections_var .= "</div>";
            $sections_var .= "</section>";
        }
    }
    return $sections_var;
}
function ____columns_modules($columns)
{
    $html = "<div class='row'>";
    foreach ($columns as $column) {
        $items = $column['items'];
        $html .= '<div class="col">';
        $html .= '<div class="column-holder content-margin">';
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
