<?php
/**
 * Plugin/Snippet Name: Drag-and-drop post order
 * Description: List-table and dedicated Reorder-page drag-and-drop for Guides
 *              and Case Studies. Writes WordPress menu_order so the front-end
 *              archive sort (action_pre_get_posts → menu_order ASC, date DESC)
 *              matches the admin order.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Post types that support list-table / Reorder-page drag reorder.
 *
 * @return string[]
 */
function coptrz_post_order_types()
{
    return array('guides', 'casestudies');
}

/**
 * Menu slug for a type's dedicated Reorder page.
 *
 * @param string $post_type
 * @return string
 */
function coptrz_post_order_page_slug($post_type)
{
    return 'coptrz-order-' . $post_type;
}

/**
 * Default the Guides / Case Studies admin list to menu_order ASC so the table
 * matches the public archive. Leaves user-chosen sortable columns alone.
 *
 * @param WP_Query $query
 * @return void
 */
function coptrz_post_order_admin_list_query($query)
{
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $post_type = $query->get('post_type');
    if (!in_array($post_type, coptrz_post_order_types(), true)) {
        return;
    }

    // Only the list table (edit.php), not meta-box AJAX or other admin queries.
    global $pagenow;
    if ($pagenow !== 'edit.php') {
        return;
    }

    // Respect an explicit sort from the column headers.
    if (!empty($_GET['orderby'])) {
        return;
    }

    // Match front archive: menu_order ASC, then newest date among ties.
    $query->set('orderby', array(
        'menu_order' => 'ASC',
        'date'       => 'DESC',
    ));
}
add_action('pre_get_posts', 'coptrz_post_order_admin_list_query');

/**
 * Register a Reorder submenu under each supported CPT.
 *
 * @return void
 */
function coptrz_post_order_register_menus()
{
    foreach (coptrz_post_order_types() as $post_type) {
        $obj = get_post_type_object($post_type);
        if (!$obj) {
            continue;
        }

        $label = $obj->labels->name ? $obj->labels->name : $post_type;
        // These CPTs use capability_type => page, so edit_posts maps to edit_pages.
        $cap = !empty($obj->cap->edit_posts) ? $obj->cap->edit_posts : 'edit_posts';

        add_submenu_page(
            'edit.php?post_type=' . $post_type,
            sprintf(__('Reorder %s', 'coptrz-theme'), $label),
            __('Reorder', 'coptrz-theme'),
            $cap,
            coptrz_post_order_page_slug($post_type),
            'coptrz_post_order_render_page'
        );
    }
}
add_action('admin_menu', 'coptrz_post_order_register_menus');

/**
 * Render the dedicated Reorder page for the current CPT submenu.
 *
 * @return void
 */
function coptrz_post_order_render_page()
{
    $post_type = isset($_GET['post_type']) ? sanitize_key(wp_unslash($_GET['post_type'])) : '';
    // Fallback: parse from page slug (coptrz-order-{type}).
    if (!$post_type && !empty($_GET['page'])) {
        $page = sanitize_key(wp_unslash($_GET['page']));
        if (strpos($page, 'coptrz-order-') === 0) {
            $post_type = substr($page, strlen('coptrz-order-'));
        }
    }

    if (!in_array($post_type, coptrz_post_order_types(), true)) {
        wp_die(esc_html__('Invalid post type.', 'coptrz-theme'));
    }

    $obj = get_post_type_object($post_type);
    $cap = ($obj && !empty($obj->cap->edit_posts)) ? $obj->cap->edit_posts : 'edit_posts';
    if (!current_user_can($cap)) {
        wp_die(esc_html__('You do not have permission to access this page.', 'coptrz-theme'));
    }

    $label = $obj && $obj->labels->name ? $obj->labels->name : $post_type;

    $posts = get_posts(array(
        'post_type'              => $post_type,
        'post_status'            => 'publish',
        'posts_per_page'         => -1,
        'orderby'                => array(
            'menu_order' => 'ASC',
            'date'       => 'DESC',
        ),
        'no_found_rows'          => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => false,
    ));

    echo '<div class="wrap coptrz-order-page">';
    echo '<h1>' . esc_html(sprintf(__('Reorder %s', 'coptrz-theme'), $label)) . '</h1>';
    echo '<p class="description">' . esc_html__('Drag items to set the archive order. Changes save automatically. Items marked Hidden stay off the public archive list.', 'coptrz-theme') . '</p>';

    if (empty($posts)) {
        echo '<p>' . esc_html__('No published posts found.', 'coptrz-theme') . '</p>';
        echo '</div>';
        return;
    }

    echo '<ul id="coptrz-order-list" class="coptrz-order-list">';
    foreach ($posts as $post) {
        $hidden = false;
        if (function_exists('get__post_meta_by_id')) {
            $hidden = (bool) get__post_meta_by_id($post->ID, 'hide_on_list');
        } else {
            $hidden = get_post_meta($post->ID, '_hide_on_list', true) === 'yes';
        }

        $edit = get_edit_post_link($post->ID, 'raw');
        $title = get_the_title($post) !== '' ? get_the_title($post) : __('(no title)', 'coptrz-theme');

        echo '<li class="coptrz-order-item" data-id="' . esc_attr((string) $post->ID) . '">';
        echo '<span class="coptrz-order-grip" aria-hidden="true">⋮⋮</span>';
        echo '<span class="coptrz-order-title">' . esc_html($title) . '</span>';
        if ($hidden) {
            echo ' <span class="coptrz-order-badge">' . esc_html__('Hidden', 'coptrz-theme') . '</span>';
        }
        if ($edit) {
            echo ' <a class="coptrz-order-edit" href="' . esc_url($edit) . '">' . esc_html__('Edit', 'coptrz-theme') . '</a>';
        }
        echo '</li>';
    }
    echo '</ul>';
    echo '</div>';
}

/**
 * Enqueue sortable script on list screens and dedicated Reorder pages.
 *
 * @param string $hook
 * @return void
 */
function coptrz_post_order_enqueue($hook)
{
    $post_type = '';
    $mode      = '';
    $offset    = 0;

    $screen = function_exists('get_current_screen') ? get_current_screen() : null;

    // Dedicated Reorder page: e.g. guides_page_coptrz-order-guides
    foreach (coptrz_post_order_types() as $type) {
        if ($hook === $type . '_page_' . coptrz_post_order_page_slug($type)) {
            $post_type = $type;
            $mode      = 'page';
            $offset    = 0;
            break;
        }
    }

    // Classic list table.
    if (!$post_type && $hook === 'edit.php' && $screen && in_array($screen->post_type, coptrz_post_order_types(), true)) {
        $post_type = $screen->post_type;
        $mode      = 'list';

        $per_page = (int) get_user_option('edit_' . $post_type . '_per_page');
        if ($per_page < 1) {
            $per_page = 20;
        }
        $paged  = isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1;
        $offset = ($paged - 1) * $per_page;
    }

    if (!$post_type || !$mode) {
        return;
    }

    $ver = defined('coptz_version') ? coptz_version : '1.0';

    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script(
        'coptrz-post-order',
        get_template_directory_uri() . '/admin/js/post-order.js',
        array('jquery', 'jquery-ui-sortable'),
        $ver,
        true
    );

    wp_localize_script('coptrz-post-order', 'coptrzPostOrder', array(
        'ajaxUrl'  => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('coptrz_save_post_order'),
        'postType' => $post_type,
        'offset'   => $offset,
        'mode'     => $mode,
        'i18n'     => array(
            'saved'  => __('Order saved.', 'coptrz-theme'),
            'failed' => __('Could not save order.', 'coptrz-theme'),
        ),
    ));
}
add_action('admin_enqueue_scripts', 'coptrz_post_order_enqueue');

/**
 * Persist menu_order for an ordered list of post IDs.
 *
 * @return void
 */
function coptrz_save_post_order()
{
    if (!check_ajax_referer('coptrz_save_post_order', 'nonce', false)) {
        wp_send_json_error(array('message' => 'Invalid nonce.'), 403);
    }

    $post_type = isset($_POST['post_type']) ? sanitize_key(wp_unslash($_POST['post_type'])) : '';
    if (!in_array($post_type, coptrz_post_order_types(), true)) {
        wp_send_json_error(array('message' => 'Invalid post type.'), 400);
    }

    $obj = get_post_type_object($post_type);
    $cap = ($obj && !empty($obj->cap->edit_posts)) ? $obj->cap->edit_posts : 'edit_posts';
    if (!current_user_can($cap)) {
        wp_send_json_error(array('message' => 'Forbidden.'), 403);
    }

    $ids = isset($_POST['order']) ? (array) $_POST['order'] : array();
    $ids = array_values(array_filter(array_map('absint', $ids)));
    if (empty($ids)) {
        wp_send_json_error(array('message' => 'No posts.'), 400);
    }

    $offset = isset($_POST['offset']) ? max(0, (int) $_POST['offset']) : 0;

    foreach ($ids as $index => $post_id) {
        $post = get_post($post_id);
        if (!$post || $post->post_type !== $post_type) {
            continue;
        }
        if (!current_user_can('edit_post', $post_id)) {
            continue;
        }

        wp_update_post(array(
            'ID'         => $post_id,
            'menu_order' => $offset + $index,
        ));
    }

    wp_send_json_success(array('message' => 'Order saved.'));
}
add_action('wp_ajax_coptrz_save_post_order', 'coptrz_save_post_order');
