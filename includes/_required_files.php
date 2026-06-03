<?php
require_once('schema.php');
require_once('post-types.php');

/**
 * Conditional File Loading Execution
 * Intercepts the loading sequence and excludes specific structural files 
 * when the 'page-blocks-editor.php' template is active in the current context.
 */
$is_blocks_editor = function_exists('dd_is_blocks_editor_template_active') && dd_is_blocks_editor_template_active();

if (!$is_blocks_editor) {
    require_once('elements.php');
    require_once('modules.php');
    require_once('ajax.php');
    require_once('svg.php');
} else {
    if (!is_admin()) {
        require_once('elements.php');
        require_once('modules.php');
        require_once('ajax.php');
        require_once('svg.php');
    }
}

require_once('shortcodes.php');
require_once('hooks.php');
require_once('theme-widgets.php');
require_once('menus.php');
require_once('woocommerce.php');
require_once('customizer.php');
require_once('marquee.php');

#require_once('page-templates/style-1/fields.php');