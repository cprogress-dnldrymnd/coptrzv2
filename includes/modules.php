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
add_action('modules_styles', 'modules_styles');
