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
