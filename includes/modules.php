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
    $hero_background_url = wp_get_attachment_image_url($hero_background, 'full');
    $hero_heading_val = $hero_heading ? $hero_heading : get_the_title();
    if (!$hero_hidden) {
        global $styles;
        $styles[] = ".hero{ background-image: $hero_background_url}";
        $hero = "<section class='hero rounded-10px bg-primary text-white d-flex align-items-end'><div class='container'>";
        $hero .= do_shortcode("[__heading heading='$hero_heading_val']");
        $hero .= do_shortcode("[__description description='$hero_description']");
        $hero .= "</div></section>";
        return $hero;
    }
}

function _attributes($attributes)
{
    if ($attributes) {
        $attribute_val = '';

        foreach ($attributes as $attribute) {
            $attribute_val .= $attribute[0] . '="' . $attribute[1] . '"';
        }
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
