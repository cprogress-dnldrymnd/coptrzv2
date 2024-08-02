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


    $industries = get__posts('industries');
    $select_solution = '<label style="display: block" class="cf-field__label">Select Solution</label><select class="select-page-selector">';
    foreach ($industries as $key => $solution) {
        $select_solution .= '<option value="' . $key . '"> ' . $solution . ' </option>';
    }
    $select_solution .= '</select>';
    $selector['industries'] = $select_solution;


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
        'orderby' => 'title',
        'order' => 'ASC',
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


function action__wp_footer()
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
        global $layouts_global, $product_taxonomy_page, $popups_id;



        $layouts_global_val = "<div class='ab-sub-wrapper'>";
        $layouts_global_val .= "<ul role='menu' id='wp-admin-bar-layouts-menu-default' class='ab-submenu'>";
        if ($product_taxonomy_page) {
            $product_tax_page = array_unique($product_taxonomy_page);
            foreach ($product_tax_page as $tax_page) {
                $title = get_the_title($tax_page) . ' [Term Page]';
                $link = get_edit_post_link($tax_page);
                $layouts_global_val .= "<li>";
                $layouts_global_val .= "<a class='ab-item' role='menuitem' href='$link'>$title</a>";
                $layouts_global_val .= "</li>";
            }
        }
        if ($layouts_global) {
            $layouts = array_unique($layouts_global);

            foreach ($layouts as $layout) {
                $title = get_the_title($layout) . ' [Layout]';
                $link = get_edit_post_link($layout);

                $layouts_global_val .= "<li>";
                $layouts_global_val .= "<a class='ab-item' role='menuitem' href='$link'>$title</a>";
                $layouts_global_val .= "</li>";
            }
        }
        if ($popups_id) {
            $popups = array_unique($popups_id);
            foreach ($popups as $popup) {
                $title = get_the_title($popup) . ' [Popup]';
                $link = get_edit_post_link($popup);

                $layouts_global_val .= "<li>";
                $layouts_global_val .= "<a class='ab-item' role='menuitem' href='$link'>$title</a>";
                $layouts_global_val .= "</li>";
            }
        }





        $layouts_global_val .= "</ul>";
        $layouts_global_val .= "</div>";
    ?>
        <script>
            jQuery(document).ready(function() {
                jQuery("<?= $layouts_global_val ?>").appendTo('#wp-admin-bar-layouts-menu');
            });
            jQuery('#download-gvc').appendTo('.the-content > *:nth-child(2)');
        </script>

    <?php
    }
}

add_action('wp_footer', 'action__wp_footer');

function hero_form_redirect()
{
    $hero_form_enable = get__post_meta('hero_form_enable');
    $hero_form_redirect_type = get__post_meta('hero_form_redirect_type');
    $hero_form_pdf_redirect = get__post_meta('hero_form_pdf_redirect');
    $hero_form_redirect_url = get__post_meta('hero_form_redirect_url');
    $hero_form = get__post_meta('hero_form');
    $form_id = $hero_form[0]['id'];

    if ($hero_form_redirect_type == 'pdf') {
        $redirect = wp_get_attachment_url($hero_form_pdf_redirect);
    } else {
        $redirect = $hero_form_redirect_url;
    }

    if ($hero_form_enable) {
    ?>
        <script>
            document.addEventListener('wpcf7mailsent', function(event) {
                setTimeout(function() {
                    if (<?= $form_id ?> == event.detail.contactFormId) {
                        window.open('<?= $redirect ?>', '_blank');
                    }
                }, 3000);
            }, false);
        </script>
<?php
    }
}

add_action('wp_footer', 'hero_form_redirect');


/**
 * Add a new admin bar menu item.
 *
 * @param WP_Admin_Bar $admin_bar Admin bar reference.
 */
function action_layout_menu($admin_bar)
{
    if (!is_admin()) {
        $admin_bar->add_menu(
            array(
                'id'    => 'layouts-menu',
                'title' => 'Layouts',
                'href'  => false,
                'meta'  => array(
                    'class' => 'menupop layouts-menu',
                    'title' => 'Layouts',
                ),
            )
        );
    }
}
add_action('admin_bar_menu', 'action_layout_menu', 999999);


function action_pre_get_posts($query)
{
    if (!is_admin() && $query->is_main_query()) {
        $query->set('post_status', 'publish');
        $query->set('orderby', 'menu_order');
        $query->set('order', 'ASC');

        if (is_post_type_archive('industries') || is_post_type_archive('guides')) {
            $meta_query[] = [
                'key' => '_hide_on_list',
                'value' => 'yes',
                'compare' => 'NOT IN',
            ];

            $query->set('meta_query', $meta_query);
        }
        if (is_post_type_archive('industries') || is_post_type_archive('capabilities')) {
            $query->set('posts_per_page', -1);
        } else if (is_post_type_archive('events') || is_tax('events_category')) {
            $meta_query[] = [
                'key'     => '_event_start_datetime',
                'value'   => date('Y-m-d'),
                'compare' => '>=',
                'type'    => 'DATETIME'
            ];
            $query->set('meta_query', $meta_query);
            $query->set('orderby', 'meta_value');
            $query->set('order', 'ASC');
        } else if (is_home()) {
            $query->set('orderby', 'date');
            $query->set('order', 'DESC');
        } else {
            if (isset($_GET['posts_per_page'])) {
                $query->set('posts_per_page', $_GET['posts_per_page']);
            }
        }
    }
    return;
}
add_action('pre_get_posts', 'action_pre_get_posts', 1);


function action_body_class($classes)
{
    $hero_hidden = get__post_meta('hero_hidden');
    $header_background = get__post_meta('header_background');

    $product_category_page = false;
    if (is_product_taxonomy()) {
        $product_category_page = __get_product_taxonomy_page(get_queried_object()->term_id);
        if (!$product_category_page) {
            $classes[] = 'hide-price';
            $classes[] = 'hide-stock';
            $classes[] = 'product-loop-style-1';
        }
    }
    if ($hero_hidden) {
        $classes[] = 'hero-hidden';
    }
    if ($header_background) {
        $classes[] = "hero-$header_background";
    }


    return $classes;
}

add_filter('body_class', 'action_body_class');


add_filter('wpcf7_form_tag_data_option', function ($data, $options, $args) {
    $data = [];
    foreach ($options as $option) {
        if ($option === 'checkbox_options') {
            $data = array_merge($data, ['Checkbox Option A', 'Checkbox Option B']);
        }
    }
    return $data;
}, 10, 3);
