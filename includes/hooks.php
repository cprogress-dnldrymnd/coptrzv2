<?php
function action_wp_head()
{
    $hero_hidden = get__post_meta('hero_hidden');
    if (!$hero_hidden) {
        $hero_background = get__post_meta('hero_background');
        $hero_background_url = wp_get_attachment_image_url($hero_background, 'full');
        $style = ".hero{ background-image: $hero_background_url}";
        echo '<style id="hero-styles">';
        echo $style;
        echo '</style>';
    }
}

add_action('wp_head', 'action_wp_head');
