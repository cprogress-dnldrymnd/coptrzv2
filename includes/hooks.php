<?php
function add_svg_support($mimes)
{
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'add_svg_support');

function action_wp_head()
{
?>
    <style id="wp-head">
        <?php
        if (isset($_GET['prev'])) {
            echo '#wpadminbar{ display: none !important }';
        }
        ?>
    </style>
<?php
}

add_action('wp_head', 'action_wp_head');

/*-----------------------------------------------------------------------------------*/
/* Admin Settings
/*-----------------------------------------------------------------------------------*/

function action_admin_enqueue_scripts($hook)
{
	wp_enqueue_style('admin', get_template_directory_uri() . '/admin/css/admin.css');
    wp_enqueue_script('admin', get_template_directory_uri() . '/admin/js/admin.js');
}
add_action('admin_enqueue_scripts', 'action_admin_enqueue_scripts');
/*-----------------------------------------------------------------------------------*/
/* Code Miror
/*-----------------------------------------------------------------------------------*/
add_action('admin_enqueue_scripts', 'codemirror_enqueue_scripts');

function codemirror_enqueue_scripts($hook)
{
    $cm_settings = array(
        'ce_css'  => wp_enqueue_code_editor(array('type' => 'text/css', 'codemirror' => array('autoRefresh' => true))),
        'ce_html' => wp_enqueue_code_editor(array('type' => 'text/html', 'codemirror' => array('autoRefresh' => true)))
    );
    wp_localize_script('jquery', 'cm_settings', $cm_settings);

    wp_enqueue_style('wp-codemirror');
}


function action_admin_head()
{
?>
    <style>
      

        <?php
        if (_is_module() || get_post_type() == 'producttaxonomypages' || get_post_type() == 'layouts') {
            echo '.wp-block-post-content { display: none !important }';
            echo '.edit-post-header__toolbar, .editor-preview-dropdown__toggle, button[aria-controls="tabs-0-edit-post/block-view"] { display: none !important; }';
        }

        ?>
    </style>

<?php
}
add_action('admin_head', 'action_admin_head');

