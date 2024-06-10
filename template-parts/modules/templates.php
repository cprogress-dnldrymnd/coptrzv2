<?php
echo 'tesmplate';
foreach ($module['template'] as $template) {
    $modules = get__post_meta_by_id($template['id'], 'modules');
    echo modules($modules);
}
var_dump($module['templates']);

