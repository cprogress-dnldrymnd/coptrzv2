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
            'class' => 'large-heading',
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
            $section_attribute = _attributes(array(
                array('class', 'section section-' . $key),
                array('id', $section_id ? $section_id : 'section-' . $key,),
            ));

            $sections_var .= "<section $section_attribute>";
            $sections_var .= "<div class='container'>";

            foreach ($section_items as $items) {
                $type = $items['_type'];
                $has_suffix = $items['has_suffix'];
                $has_prefix = $items['has_prefix'];
                $has_custom_heading_settings = $items['has_custom_heading_settings'];
                $heading = $items['heading'];
                $prefix = $items['prefix'];
                $suffix = $items['suffix'];
                $tag = $items['tag'];
                $size = $items['size'];
                $text_color = $items['text_color'];
                $text_color_custom = $items['text_color_custom'];

                switch ($type) {
                    case 'heading':
                        $attributes_args = array();
                        if ($has_custom_heading_settings) {
                            if ($tag) {
                                $attributes_args[] = array('tag', $tag);
                            }
                            if ($size) {
                                $attributes_args[] = array('size', $size);
                            }
                            if ($text_color) {
                                $attributes_args[] = array('text_color', $text_color);
                            }
                        }
                        $attributes = _attributes($attributes_args);
                        $sections_var .= do_shortcode("[__heading $attributes heading='$heading']");
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

function _attributes($attributes)
{
    if ($attributes) {
        $attribute_val = '';
        $class_attr_arr = [];
        foreach ($attributes as $attribute) {
            if ($attribute[0] == 'class') {
                $class_attr_arr[] .= $attribute[1];
            } else {
                $attribute_val .= $attribute[0] . "='$attribute[1]'";
            }
        }
        if ($class_attr_arr) {
            $class_val = implode(' ', $class_attr_arr);
            $class_attr = "class='$class_val'";
        }


        $attribute_val .= $class_attr;
        return $attribute_val;
    }
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

function _bg_image($hero_background)
{
    $mime_type =  get_post_mime_type($hero_background);

    if (str_contains($mime_type, 'video')) {
        return do_shortcode("[__video class='background-image background-overlay' video_id='$hero_background']");
    } else {
        return do_shortcode("[__image class='background-image background-overlay' image_id='$hero_background']");
    }
}
