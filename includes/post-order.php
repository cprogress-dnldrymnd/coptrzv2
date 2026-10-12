<?php
/**
 * Plugin/Snippet Name: Drag-and-drop post order
 * Description: List-table drag-and-drop reordering for Guides and Case Studies.
 *              Writes WordPress menu_order so the front-end archive sort
 *              (action_pre_get_posts → menu_order ASC) matches the admin order.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Post types that support list-table drag reorder.
 *
 * @return string[]
 */
function coptrz_post_order_types()
{
    return array('guides', 'casestudies');
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
 * Enqueue sortable script on Guides / Case Studies list screens.
 *
 * @param string $hook
 * @return void
 */
function coptrz_post_order_enqueue($hook)
{
    if ($hook !== 'edit.php') {
        return;
    }

    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || !in_array($screen->post_type, coptrz_post_order_types(), true)) {
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

    $per_page = (int) get_user_option('edit_' . $screen->post_type . '_per_page');
    if ($per_page < 1) {
        $per_page = 20;
    }

    $paged = isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1;

    wp_localize_script('coptrz-post-order', 'coptrzPostOrder', array(
        'ajaxUrl'  => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('coptrz_save_post_order'),
        'postType' => $screen->post_type,
        'offset'   => ($paged - 1) * $per_page,
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

    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array('message' => 'Forbidden.'), 403);
    }

    $post_type = isset($_POST['post_type']) ? sanitize_key(wp_unslash($_POST['post_type'])) : '';
    if (!in_array($post_type, coptrz_post_order_types(), true)) {
        wp_send_json_error(array('message' => 'Invalid post type.'), 400);
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
