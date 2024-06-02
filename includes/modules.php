<?php

function modules_styles()
{

    $modules = get__post_meta('modules');

    echo '<style id="module-styles">';
    foreach ($modules as $key => $module) {
        $module_id = 'module-' . get_the_ID() . '-' . $key;
        echo '#' . $module_id . '{';

        echo '}';
    }
    echo '</style>';
}
add_action('modules_styles', 'modules_styles');
