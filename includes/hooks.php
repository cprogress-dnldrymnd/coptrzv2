<?php
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
            background-color: var(--wp-admin-theme-color);
            color: #fff;
            font-family: Courier;
            font-size: 16px;
            padding: 5px;
            font-weight: bold;
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

        <?php
        if (_is_module()) {
            echo '.wp-block-post-content { display: none !important }';
            echo '.edit-post-header__toolbar, .editor-preview-dropdown__toggle, button[aria-controls="tabs-0-edit-post/block-view"] { display: none !important; }';
        }

        ?>
    </style>

<?php
}
add_action('admin_head', 'action_admin_head');
