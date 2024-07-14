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


function action_page_selector()
{
    $pages = get__posts('page');
    $select_page = '<label style="display: block" class="cf-field__label" >Select Page</label><select class="select-page-selector">';
    foreach ($pages as $key => $page) {
        $select_page .= '<option value="' . $key . '"> ' . $page . ' </option>';
    }
    $select_page .= '</select>';

    $posts = get__posts('post');
    $select_post = '<label style="display: block" class="cf-field__label" >Select Post</label><select class="select-page-selector">';
    foreach ($posts as $key => $post) {
        $select_post .= '<option value="' . $key . '"> ' . $post . ' </option>';
    }
    $select_post .= '</select>';

    $solutions = get__posts('solutions');
    $select_solution = '<label style="display: block" class="cf-field__label">Select Solution</label><select class="select-page-selector">';
    foreach ($solutions as $key => $solution) {
        $select_solution .= '<option value="' . $key . '"> ' . $solution . ' </option>';
    }
    $select_solution .= '</select>';

    $popups = get__posts('popups');
    $select_popup = '<label style="display: block" class="cf-field__label">Select Popup</label><select class="select-page-selector">';
    foreach ($popups as $key => $popup) {
        $select_popup .= '<option value="' . $key . '"> ' . $popup . ' </option>';
    }
    $select_popup .= '</select>';

    $products = get__posts('product');
    $select_product = '<label style="display: block" class="cf-field__label">Select product</label><select class="select-page-selector">';
    foreach ($products as $key => $product) {
        $select_product .= '<option value="' . $key . '"> ' . $product . ' </option>';
    }
    $select_product .= '</select>';

    $guides = get__posts('guides');
    $select_guide = '<label style="display: block" class="cf-field__label">Select guide</label><select class="select-page-selector">';
    foreach ($guides as $key => $guide) {
        $select_guide .= '<option value="' . $key . '"> ' . $guide . ' </option>';
    }
    $select_guide .= '</select>';

    $casestudies = get__posts('casestudies');
    $select_casestudies = '<label style="display: block" class="cf-field__label">Select casestudies</label><select class="select-page-selector">';
    foreach ($casestudies as $key => $casestudies) {
        $select_casestudies .= '<option value="' . $key . '"> ' . $casestudies . ' </option>';
    }
    $select_casestudies .= '</select>';
?>
    <script>
        jQuery(document).ready(function($) {
            console.log('sdsdsdsds');

            jQuery(document).on("change", '.trigger-selector select', function(event) {
                $value = jQuery(this).val();
                $selector = jQuery(this).parent().parent().parent().find('.page-selector');
                active_link_type($selector, $value)
                console.log('sdsdsdsds');
            });


            jQuery(document).on("change", '.trigger-selector-single select', function(event) {
                $value = jQuery(this).val();
                $selector = jQuery(this).parent().parent().next().next().next().find('.page-selector');
                active_link_type($selector, $value)
            });

            jQuery(document).on("change", '.select-page-selector', function(event) {
                $value = jQuery(this).val();
                $input = jQuery(this).parent().parent().parent().parent().parent().find('.field-url input');
                $input.val($value);
            });


            function active_link_type($selector, $value, $input = '') {
                if ($value == 'page') {
                    $selector.html('<?= $select_page ?>');
                } else if ($value == 'post') {
                    $selector.html('<?= $select_post ?>');
                } else if ($value == 'product') {
                    $selector.html('<?= $select_product ?>');
                } else if ($value == 'guides') {
                    $selector.html('<?= $select_guide ?>');
                } else if ($value == 'casestudies') {
                    $selector.html('<?= $select_casestudies ?>');
                } else if ($value == 'solutions') {
                    $selector.html('<?= $select_solution ?>');
                } else if ($value == 'popups') {
                    $selector.html('<?= $select_popup ?>');
                } else {
                    $selector.html('');
                }

                $selector.find('.select-page-selector').val($input);


            }

            setTimeout(function() {
                jQuery('.trigger-selector select').each(function(index, element) {
                    $value = jQuery(this).val();
                    $selector = jQuery(this).parent().parent().parent().find('.page-selector');
                    $input = jQuery(this).parent().parent().parent().find('.field-url input').val();
                    active_link_type($selector, $value, $input)
                });


                jQuery('.trigger-selector-single select').each(function(index, element) {
                    $value = jQuery(this).val();
                    $selector = jQuery(this).parent().parent().next().next().next().find('.page-selector');
                    $input = jQuery(this).parent().parent().next().next().find('input').val();
                    active_link_type($selector, $value, $input)
                });

            }, 2000);
        });
    </script>
<?php
}

add_action('admin_footer', 'action_page_selector');
function get__posts($post_type)
{
    $pages_array = array(); // Initialize an empty array

    $args = array(
        'post_type'      => $post_type, // Get only pages
        'posts_per_page' => -1, // Get all pages
        'post_status'    => 'publish', // Get only published pages
        'fields'         => 'ids', // Only retrieve post IDs for efficiency
    );

    $posts = get_posts($args);

    if ($posts) {
        foreach ($posts as $post) {
            $pages_array[$post] = get_the_title($post); // Add ID => title to the array
        }
    }

    return $pages_array;
}

function my_custom_popup()
{
    $html =  "<div class='admin-popup' id='wysiwyg-editor'>";
    $html .=  "<div class='close-admin-popup close-wysiwyg-trigger'></div>";
    $html .= "<div class='inner'>";
    $html .=  "<textarea id='wysiwyg-editor-field'></textarea>";
    $html .= "<div class='buttons'>";
    $html .=  "<a class='submit-wysiwyg-trigger button button-primary button-large'>Submit</a>";
    $html .=  "<a class='close-wysiwyg-button close-wysiwyg-trigger button button-secondary button-large'>Close</a>";
    $html .=  "</div>";

    $html .=  "</div>";
    $html .=  "</div>";
    echo $html;
}
add_action('admin_footer', 'my_custom_popup');


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
