<?php

function modules_styles()
{

    $modules = get__post_meta('modules');

    echo '<style id="module-styles">';
    foreach ($modules as $key => $module) {
        echo '#module'
    }
    echo '</style>';
}
add_action('wp_head', 'modules_styles');
