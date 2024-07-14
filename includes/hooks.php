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

    $pages = get__posts('page');
    $select_page = '<label style="display: block" class="cf-field__label" >Select Page</label><select class="select-page-selector">';
    foreach ($pages as $key => $page) {
        $select_page .= '<option value="' . $key . '"> ' . $page . ' </option>';
    }
    $select_page .= '</select>';
    $selector['page'] = $select_page;


    $posts = get__posts('post');
    $select_post = '<label style="display: block" class="cf-field__label" >Select Post</label><select class="select-page-selector">';
    foreach ($posts as $key => $post) {
        $select_post .= '<option value="' . $key . '"> ' . $post . ' </option>';
    }
    $select_post .= '</select>';
    $selector['post'] = $select_post;


    $solutions = get__posts('solutions');
    $select_solution = '<label style="display: block" class="cf-field__label">Select Solution</label><select class="select-page-selector">';
    foreach ($solutions as $key => $solution) {
        $select_solution .= '<option value="' . $key . '"> ' . $solution . ' </option>';
    }
    $select_solution .= '</select>';
    $selector['solutions'] = $select_solution;


    $popups = get__posts('popups');
    $select_popup = '<label style="display: block" class="cf-field__label">Select Popup</label><select class="select-page-selector">';
    foreach ($popups as $key => $popup) {
        $select_popup .= '<option value="' . $key . '"> ' . $popup . ' </option>';
    }
    $select_popup .= '</select>';
    $selector['popups'] = $select_popup;


    $products = get__posts('product');
    $select_product = '<label style="display: block" class="cf-field__label">Select product</label><select class="select-page-selector">';
    foreach ($products as $key => $product) {
        $select_product .= '<option value="' . $key . '"> ' . $product . ' </option>';
    }
    $select_product .= '</select>';
    $selector['product'] = $select_product;


    $guides = get__posts('guides');
    $select_guide = '<label style="display: block" class="cf-field__label">Select guide</label><select class="select-page-selector">';
    foreach ($guides as $key => $guide) {
        $select_guide .= '<option value="' . $key . '"> ' . $guide . ' </option>';
    }
    $select_guide .= '</select>';
    $selector['guides'] = $select_guide;


    $casestudies = get__posts('casestudies');
    $select_casestudies = '<label style="display: block" class="cf-field__label">Select casestudies</label><select class="select-page-selector">';
    foreach ($casestudies as $key => $casestudies) {
        $select_casestudies .= '<option value="' . $key . '"> ' . $casestudies . ' </option>';
    }
    $select_casestudies .= '</select>';
    $selector['casestudies'] = $select_casestudies;


    wp_enqueue_style('admin', get_template_directory_uri() . '/admin/css/admin.css');
    wp_register_script('admin', get_template_directory_uri() . '/admin/js/admin.js');
    wp_localize_script('admin', 'selector', $selector);
    wp_enqueue_script('admin');
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


function action_popups()
{

    global $popups_id;
    $popups = array_unique($popups_id);
    $args = array(
        'post_type' => 'popups',
        'include' => $popups,
        'fields' => 'ids',
    );
    $posts = get_posts($args);

    foreach ($posts as $post) {
        echo __popup($post);
    }

    if (current_user_can('administrator')) {
        global $layouts_global;
        $layouts_global_val = "<div class='ab-sub-wrapper'>";
        $layouts_global_val .= "<ul role='menu' id='wp-admin-bar-layouts-menu-default' class='ab-submenu'>";
        foreach ($layouts_global as $layout) {
            $title = get_the_title($layout);
            $link = get_edit_post_link($layout);
    
            $layouts_global_val .= "<li>";
            $layouts_global_val .= "<a class='ab-item' role='menuitem' href='$link'>$title</a>";
            $layouts_global_val .= "</li>";
        }
        $layouts_global_val .= "</ul>";
        $layouts_global_val .= "</div>";
    ?>
        <script>
            jQuery(document).ready(function() {
                jQuery("<?= $layouts_global_val ?>").appendTo('#wp-admin-bar-layouts-menu');
            });
        </script>
    <?php
    }
}

add_action('wp_footer', 'action_popups');



/**
 * Add a new admin bar menu item.
 *
 * @param WP_Admin_Bar $admin_bar Admin bar reference.
 */
function my_plugin_add_admin_bar_items($admin_bar)
{
    if (!is_admin()) {
        $admin_bar->add_menu(
            array(
                'id'    => 'layouts-menu',
                'title' => 'Layouts',
                'href'  => false,
                'meta'  => array(
                    'class' => 'layouts-menu',
                    'title' => 'Layouts',
                ),
            )
        );
    }
}
add_action('admin_bar_menu', 'my_plugin_add_admin_bar_items', 999999);
