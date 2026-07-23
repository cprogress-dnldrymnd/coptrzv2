<?php
require_once('schema.php');
require_once('post-types.php');

/**
 * Conditional File Loading Execution
 * Intercepts the loading sequence and excludes specific structural files 
 * when the 'page-blocks-editor.php' template is active in the current context.
 */
$is_blocks_editor = function_exists('dd_is_blocks_editor_template_active') && dd_is_blocks_editor_template_active();

// elements.php/svg.php are pure function/class definitions with no side
// effects, and several always-loaded files (shortcodes.php, header-blocks.php)
// depend on them unconditionally — see their function_exists() guards for the
// modules.php/ajax.php functions that DO stay conditional below.
require_once('elements.php');
require_once('svg.php');

if (!$is_blocks_editor) {
    require_once('modules.php');
    require_once('ajax.php');
} else {
    if (!is_admin()) {
        require_once('modules.php');
        require_once('ajax.php');
    }
}

require_once('shortcodes.php');
require_once('legacy-blocks.php');
require_once('header-blocks.php');
require_once('hero-block.php');
require_once('hooks.php');
require_once('theme-widgets.php');
require_once('menus.php');
require_once('woocommerce.php');
require_once('customizer.php');
require_once('marquee.php');
require_once('wpml-eraser.php');

#require_once('page-templates/style-1/fields.php');