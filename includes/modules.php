<?php

function modules_styles()
{
    global $styles;
?>
    <style id="module-styles">
        <?= var_dump($styles) ?>
    </style>
<?php
}
add_action('wp_head', 'modules_styles');