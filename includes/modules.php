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
        $hero .= do_shortcode("[__heading class='large-heading' tag='h1' heading='$hero_heading_val']");
        $hero .= do_shortcode("[__description description='$hero_description']");
        $hero .= "</div></section>";
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
            $section_attribute = _attributes(array(
                'class' => 'section section-' . $key,
                'id' => $section_id ? $section_id : 'section-' . $key
            ));

            $sections_var .= "<section $section_attribute>";

            $sections_var .= "</section>";
        }
    }
}

function _attributes($attributes)
{
    if ($attributes) {
        $attribute_val = '';
        $class_attr = "class='";
        $id_attr = "id='";
        foreach ($attributes as $attribute) {
            if ($attribute[0] == 'class') {
                $class_attr .= $attribute[1] . ' ';
            } else {
                $attribute_val .= $attribute[0] . "='$attribute[1]'";
            }
        }
        $class_attr .= "'";
        $id_attr .= "'";

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
