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
            $section_id_val  = $section_id ? $section_id : 'section-' . $key;

            $classes[] = 'section';
            $classes[] = 'section-' . $key;
            
            $id = _attribute('id', array($section_id_val));
            $classes = _attribute('class', $classes);

            $section_attribute = _attributes(array($classes, $id));

            $sections_var .= "<section $section_attribute>";
            $sections_var .= "<div class='container'>";

            foreach ($section_items as $items) {
                $type = $items['_type'];
                switch ($type) {
                    case 'heading':
                        $sections_var .= ____heading_modules($items);
                        break;
                }
                $sections_var .= $type;
            }

            $sections_var .= "</div>";
            $sections_var .= "</section>";
        }
    }
    return $sections_var;
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
