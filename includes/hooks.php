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
        .columns>.cf-field__body>.cf-complex__groups {
            display: flex
        }

        .columns>.cf-field__body>.cf-complex__groups>div {
            flex: 1;
            padding: 5px;
        }

        .cb-label.cb-label.cb-label {
            background-color: #555d66;
            color: #fff;
            padding: 5px;
            text-transform: uppercase;
        }

        .cb-label-end.cb-label-end.cb-label-end {
            background-color: var(--wp-admin-theme-color);
            padding: 5px;
        }

        .inline-field.inline-field:not([hidden]) {
            display: flex;
            flex-wrap: wrap;
        }

        .inline-field.inline-field .cf-field__head {
            flex: 0 0 10%;
        }

        .inline-field.inline-field .cf-field__body {
            flex: 0 0 90%;
        }

        .inline-field-wide-label.inline-field-wide-label .cf-field__head {
            flex: 0 0 15%;
        }

        .inline-field-wide-label.inline-field-wide-label .cf-field__body {
            flex: 0 0 85%;
        }


        .inline-field.inline-field .cf-field__help {
            margin-left: 10%;
        }

        .postbox-header {
            background-color: lightblue;
        }

        .edit-post-meta-boxes-area .postbox {
            margin-bottom: 10px;
        }

        .preview iframe {
            width: 100%;
            min-height: 100vh;
        }

        .cf-complex__inserter-menu.cf-complex__inserter-menu {
            z-index: 999 !important;
            width: 800px;
            flex-wrap: wrap;
            display: flex;
            padding: 30px;
            border-radius: 5px;
            background-color: #191e23;
        }

        .cf-complex__inserter-menu.cf-complex__inserter-menu[hidden] {
            display: none !important;
        }

        .cf-complex__inserter-menu.cf-complex__inserter-menu .cf-complex__inserter-item {
            flex: 0 0 calc(33.33333333% - 40px);
            padding: 15px;
            margin: 5px;
            border-radius: 5px;
            background-color: #007cba;
            color: #fff;
        }

        .cf-complex__inserter-menu.cf-complex__inserter-menu .cf-complex__inserter-item:hover {
            background-color: #fff;
            color: #191e23;
        }

        #wysiwyg-editor {
            position: fixed;
            z-index: 9999;
            background: rgba(0, 0, 0, .8);
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }


        #wysiwyg-editor:not(.active) {
            display: none;
        }

        #wysiwyg-editor .inner {
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            background-color: #fff;
            width: 100%;
            position: relative;
        }

        #wysiwyg-editor .inner .buttons {
            margin: 1rem;

        }

        .close-wysiwyg-editor {
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;

        }

        .submit-wysiwyg-trigger {
            margin-right: 1rem !important;
            padding: 15px 30px;
            line-height: 1;
            font-size: 15px;
        }

        #wysiwyg-editor .inner .buttons .button {}



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
