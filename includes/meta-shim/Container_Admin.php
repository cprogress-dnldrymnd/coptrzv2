<?php
/**
 * Plugin/Snippet Name: Meta Shim - Container Admin
 * Description: The admin half of the meta shim: renders native WordPress meta
 *              boxes / options pages / term fields for every registered Container,
 *              and persists submissions in Carbon Fields' exact pipe-delimited key
 *              format (via Key_Formatter) so no data migration is required.
 *
 *              Rendering is plain server-side HTML; all interactivity (tabs,
 *              repeater add/duplicate/reorder/collapse/delete, media pickers,
 *              association search, conditional logic) is handled by the
 *              lightweight vanilla script assets/js/admin-meta-boxes.js. There is
 *              a single, explicit input-name convention shared by render + save:
 *
 *                  cms_fields[<root>]                              (scalar)
 *                  cms_fields[<root>][]                            (multi/association)
 *                  cms_fields[<root>][<i>][_type]                 (complex row group)
 *                  cms_fields[<root>][<i>][<sub>]                 (complex sub-field)
 *                  cms_fields[<root>][<i>][<sub>][<j>][_type] ... (nested complex)
 *
 *              save() walks the registered Field tree against the posted array and
 *              re-indexes rows 0..n, exactly as Carbon Fields did, so reordering
 *              and deletion are loss-free.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

namespace CoptrzTheme\MetaShim;

if (!defined('ABSPATH')) {
    exit;
}

class Container_Admin
{
    /** Top-level $_POST key holding every shim input. */
    const INPUT_ROOT = 'cms_fields';

    /** $_POST key for nav-menu-item fields (namespaced by menu item id). */
    const NAV_INPUT_ROOT = 'menu_item_cms';

    /** Nonce action/name. */
    const NONCE_ACTION = 'coptrz_meta_shim_save';
    const NONCE_NAME   = 'coptrz_meta_shim_nonce';

    /**
     * Wire every registered container into the WordPress admin lifecycle.
     * Called once on init (see functions.php bootstrap).
     *
     * @return void
     */
    public static function boot()
    {
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));

        // Post meta boxes + save.
        add_action('add_meta_boxes', array(__CLASS__, 'register_post_meta_boxes'));
        add_action('save_post', array(__CLASS__, 'save_post'), 10, 2);

        // Theme options pages + save.
        add_action('admin_menu', array(__CLASS__, 'register_options_pages'));
        add_action('admin_init', array(__CLASS__, 'maybe_save_options'));

        // Term meta fields + save (registered per taxonomy referenced by where()).
        self::register_term_hooks();

        // Nav menu item fields + save (rendered inside the Menus editor).
        add_action('wp_nav_menu_item_custom_fields', array(__CLASS__, 'render_nav_fields'), 10, 2);
        add_action('wp_update_nav_menu_item', array(__CLASS__, 'save_nav_item'), 10, 2);

        // Association field AJAX search.
        add_action('wp_ajax_coptrz_meta_search', array(__CLASS__, 'ajax_search'));
    }

    /* ===================================================================== */
    /*  Asset loading                                                        */
    /* ===================================================================== */

    /**
     * Enqueue the vanilla JS/CSS admin UI + the media frames it relies on.
     *
     * @return void
     */
    public static function enqueue_assets()
    {
        $base = get_template_directory_uri();
        $ver  = defined('coptz_version') ? coptz_version : '1.0';

        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style(
            'coptrz-meta-shim',
            $base . '/assets/css/admin-meta-boxes.css',
            array('wp-color-picker'),
            $ver
        );
        wp_enqueue_script(
            'coptrz-meta-shim',
            $base . '/assets/js/admin-meta-boxes.js',
            array('jquery', 'wp-color-picker', 'jquery-ui-sortable'),
            $ver,
            true
        );
        wp_localize_script('coptrz-meta-shim', 'CoptrzMetaShim', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('coptrz_meta_search'),
        ));
    }

    /* ===================================================================== */
    /*  POST META                                                            */
    /* ===================================================================== */

    /**
     * Register a meta box for each post_meta/nav_menu_item container whose
     * display conditions match the current edit screen.
     *
     * @param string $post_type
     * @return void
     */
    public static function register_post_meta_boxes($post_type)
    {
        foreach (Container::$containers as $container) {
            if ($container->type !== 'post_meta') {
                continue; // nav_menu_item etc. are not standard post edit screens.
            }
            if (!self::post_conditions_match($container, $post_type)) {
                continue;
            }

            add_meta_box(
                'coptrz-' . $container->id,
                $container->title,
                array(__CLASS__, 'render_post_meta_box'),
                $post_type,
                $container->context ?: 'normal',
                $container->priority ?: 'default',
                array('container' => $container)
            );
        }
    }

    /**
     * Evaluate a container's where()/or_where() OR-of-AND condition sets against
     * the current post type / template. Only the keys actually used by this theme
     * (post_type, post_template) are implemented; unknown keys pass through.
     *
     * @param Container $container
     * @param string    $post_type
     * @return bool
     */
    protected static function post_conditions_match($container, $post_type)
    {
        $sets = array_filter($container->condition_sets);
        if (empty($sets)) {
            return true; // No conditions -> always show.
        }

        $template = '';
        $pid = 0;
        if (isset($_GET['post'])) {
            $pid = (int) $_GET['post'];
        } elseif (isset($_POST['post_ID'])) {
            $pid = (int) $_POST['post_ID']; // present during a meta-box save
        }
        if ($pid) {
            $template = (string) get_post_meta($pid, '_wp_page_template', true);
        }

        foreach ($sets as $set) {
            $set_ok = true;
            foreach ($set as $cond) {
                $subject = null;
                switch ($cond['key']) {
                    case 'post_type':
                        $subject = $post_type;
                        break;
                    case 'post_template':
                        $subject = $template ?: 'default';
                        break;
                    default:
                        continue 2; // Unknown key: ignore this condition.
                }
                if (!self::compare($subject, $cond['compare'], $cond['value'])) {
                    $set_ok = false;
                    break;
                }
            }
            if ($set_ok) {
                return true; // Any satisfied OR-set shows the box.
            }
        }
        return false;
    }

    /**
     * Render a post meta box.
     *
     * @param \WP_Post $post
     * @param array    $metabox
     * @return void
     */
    public static function render_post_meta_box($post, $metabox)
    {
        $container = $metabox['args']['container'];
        self::render_container($container, 'post', $post->ID);
    }

    /**
     * Persist a post container's fields.
     *
     * @param int      $post_id
     * @param \WP_Post $post
     * @return void
     */
    public static function save_post($post_id, $post)
    {
        if (!self::can_save($post_id)) {
            return;
        }
        foreach (Container::$containers as $container) {
            if ($container->type !== 'post_meta') {
                continue; // nav_menu_item etc. are not standard post edit screens.
            }
            if (!self::post_conditions_match($container, $post->post_type)) {
                continue;
            }
            self::save_container($container, 'post', $post_id);
        }
    }

    /* ===================================================================== */
    /*  THEME OPTIONS                                                         */
    /* ===================================================================== */

    /**
     * Register one admin page per theme_options container.
     *
     * @return void
     */
    public static function register_options_pages()
    {
        foreach (Container::$containers as $container) {
            if ($container->object_type !== 'option') {
                continue;
            }
            add_menu_page(
                $container->title,
                $container->title,
                'manage_options',
                'coptrz-' . $container->id,
                function () use ($container) {
                    echo '<div class="wrap"><h1>' . esc_html($container->title) . '</h1>';
                    echo '<form method="post" action="">';
                    wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);
                    echo '<input type="hidden" name="coptrz_options_container" value="' . esc_attr($container->id) . '" />';
                    self::render_container($container, 'option', 0);
                    submit_button();
                    echo '</form></div>';
                },
                'dashicons-admin-generic'
            );
        }
    }

    /**
     * Handle theme-options form submissions (posts back to the same page).
     *
     * @return void
     */
    public static function maybe_save_options()
    {
        if (!isset($_POST['coptrz_options_container']) || !current_user_can('manage_options')) {
            return;
        }
        if (!isset($_POST[self::NONCE_NAME]) || !wp_verify_nonce($_POST[self::NONCE_NAME], self::NONCE_ACTION)) {
            return;
        }
        $container_id = sanitize_text_field($_POST['coptrz_options_container']);
        foreach (Container::$containers as $container) {
            if ($container->object_type === 'option' && $container->id === $container_id) {
                self::save_container($container, 'option', 0);
                break;
            }
        }
    }

    /* ===================================================================== */
    /*  TERM META                                                            */
    /* ===================================================================== */

    /**
     * Hook edit-term forms + save for every taxonomy referenced by a term_meta
     * container's where()/or_where() conditions.
     *
     * @return void
     */
    protected static function register_term_hooks()
    {
        $taxonomies = array();
        foreach (Container::$containers as $container) {
            if ($container->object_type !== 'term') {
                continue;
            }
            foreach ($container->condition_sets as $set) {
                foreach ($set as $cond) {
                    if ($cond['key'] === 'term_taxonomy') {
                        foreach ((array) $cond['value'] as $tax) {
                            $taxonomies[$tax] = true;
                        }
                    }
                }
            }
        }

        foreach (array_keys($taxonomies) as $taxonomy) {
            add_action($taxonomy . '_edit_form_fields', array(__CLASS__, 'render_term_fields'), 10, 2);
            add_action('edited_' . $taxonomy, array(__CLASS__, 'save_term'), 10, 1);
        }
    }

    /**
     * Render term_meta containers on the edit-term screen.
     *
     * @param \WP_Term $term
     * @param string   $taxonomy
     * @return void
     */
    public static function render_term_fields($term, $taxonomy)
    {
        foreach (Container::$containers as $container) {
            if ($container->object_type !== 'term') {
                continue;
            }
            if (!self::term_conditions_match($container, $taxonomy)) {
                continue;
            }
            echo '<tr class="form-field"><th colspan="2"><h2>' . esc_html($container->title) . '</h2></th></tr>';
            echo '<tr class="form-field"><td colspan="2">';
            wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);
            self::render_container($container, 'term', $term->term_id);
            echo '</td></tr>';
        }
    }

    /**
     * Persist term containers.
     *
     * @param int $term_id
     * @return void
     */
    public static function save_term($term_id)
    {
        if (!isset($_POST[self::NONCE_NAME]) || !wp_verify_nonce($_POST[self::NONCE_NAME], self::NONCE_ACTION)) {
            return;
        }
        if (!current_user_can('manage_categories')) {
            return;
        }
        $term = get_term($term_id);
        if (!$term || is_wp_error($term)) {
            return;
        }
        foreach (Container::$containers as $container) {
            if ($container->object_type === 'term' && self::term_conditions_match($container, $term->taxonomy)) {
                self::save_container($container, 'term', $term_id);
            }
        }
    }

    /**
     * @param Container $container
     * @param string    $taxonomy
     * @return bool
     */
    protected static function term_conditions_match($container, $taxonomy)
    {
        $sets = array_filter($container->condition_sets);
        if (empty($sets)) {
            return true;
        }
        foreach ($sets as $set) {
            foreach ($set as $cond) {
                if ($cond['key'] === 'term_taxonomy' && self::compare($taxonomy, $cond['compare'], $cond['value'])) {
                    return true;
                }
            }
        }
        return false;
    }

    /* ===================================================================== */
    /*  NAV MENU ITEM                                                         */
    /* ===================================================================== */

    /**
     * Render nav_menu_item container fields inside the Menus editor for one item.
     * Inputs are namespaced by menu-item id so the bulk menu form submits them
     * per item: menu_item_cms[<item_id>][<field>]...
     *
     * @param int      $item_id
     * @param \WP_Post $item
     * @return void
     */
    public static function render_nav_fields($item_id, $item)
    {
        foreach (Container::$containers as $container) {
            if ($container->type !== 'nav_menu_item') {
                continue;
            }
            echo '<div class="cms-container cms-nav-fields" data-cms-container>';
            foreach ($container->fields as $field) {
                if (!($field instanceof Field)) {
                    continue;
                }
                $value = Reader::read('post', $item_id, $field->name);
                View::field($field, $value, self::NAV_INPUT_ROOT . '[' . $item_id . '][' . $field->name . ']');
            }
            echo '</div>';
        }
    }

    /**
     * Persist nav_menu_item fields for a single menu item on menu save.
     *
     * @param int $menu_id
     * @param int $item_id
     * @return void
     */
    public static function save_nav_item($menu_id, $item_id)
    {
        if (!current_user_can('edit_theme_options')) {
            return;
        }
        if (!isset($_POST[self::NAV_INPUT_ROOT][$item_id])) {
            return;
        }
        $posted = wp_unslash($_POST[self::NAV_INPUT_ROOT][$item_id]);

        foreach (Container::$containers as $container) {
            if ($container->type !== 'nav_menu_item') {
                continue;
            }
            foreach ($container->fields as $field) {
                if (!($field instanceof Field) || $field->is_display_only() || !array_key_exists($field->name, $posted)) {
                    continue;
                }
                $flat = Writer::build_flat($field, array($field->name), array(), $posted[$field->name]);
                Key_Formatter::persist_root('post', $item_id, $field->name, $flat);
            }
        }
    }

    /* ===================================================================== */
    /*  SAVE CORE (tree -> flat CF keys)                                     */
    /* ===================================================================== */

    /**
     * Save every root field of a container for one object.
     *
     * @param Container $container
     * @param string    $object_type
     * @param int       $object_id
     * @return void
     */
    public static function save_container($container, $object_type, $object_id)
    {
        $posted = isset($_POST[self::INPUT_ROOT]) ? wp_unslash($_POST[self::INPUT_ROOT]) : array();

        foreach ($container->fields as $field) {
            if (!($field instanceof Field) || $field->is_display_only()) {
                continue;
            }
            // Only persist fields actually present in this submission so that two
            // containers sharing a screen don't wipe each other's data.
            if (!array_key_exists($field->name, $posted)) {
                continue;
            }
            $flat = Writer::build_flat($field, array($field->name), array(), $posted[$field->name]);
            Key_Formatter::persist_root($object_type, $object_id, $field->name, $flat);
        }
    }

    /* ===================================================================== */
    /*  Shared guards / helpers                                              */
    /* ===================================================================== */

    /**
     * Standard save-guard: skip autosaves/revisions, verify nonce + caps.
     *
     * @param int $post_id
     * @return bool
     */
    protected static function can_save($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return false;
        }
        if (wp_is_post_revision($post_id)) {
            return false;
        }
        if (!isset($_POST[self::NONCE_NAME]) || !wp_verify_nonce($_POST[self::NONCE_NAME], self::NONCE_ACTION)) {
            return false;
        }
        return current_user_can('edit_post', $post_id);
    }

    /**
     * Compare helper for both container display conditions and field
     * conditional logic.
     *
     * @param mixed  $subject
     * @param string $compare
     * @param mixed  $value
     * @return bool
     */
    protected static function compare($subject, $compare, $value)
    {
        switch (strtoupper($compare)) {
            case '!=':
                return $subject != $value;
            case 'IN':
                return in_array($subject, (array) $value, true);
            case 'NOT IN':
                return !in_array($subject, (array) $value, true);
            case '=':
            default:
                return $subject == $value;
        }
    }

    /* ===================================================================== */
    /*  Association AJAX search                                              */
    /* ===================================================================== */

    /**
     * AJAX endpoint backing association fields. Returns matching posts/terms as
     * { value:"post:subtype:id", label } objects.
     *
     * @return void
     */
    public static function ajax_search()
    {
        check_ajax_referer('coptrz_meta_search', 'nonce');

        $search = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
        $types  = isset($_GET['types']) ? json_decode(wp_unslash($_GET['types']), true) : array();
        $results = array();

        foreach ((array) $types as $type) {
            if (!isset($type['type'])) {
                continue;
            }
            if ($type['type'] === 'post' && !empty($type['post_type'])) {
                $query = new \WP_Query(array(
                    'post_type'      => $type['post_type'],
                    's'              => $search,
                    'posts_per_page' => 20,
                    'post_status'    => 'publish',
                    'fields'         => 'ids',
                ));
                foreach ($query->posts as $pid) {
                    $results[] = array(
                        'value' => 'post:' . $type['post_type'] . ':' . $pid,
                        'label' => get_the_title($pid) . ' (' . $type['post_type'] . ')',
                    );
                }
            } elseif ($type['type'] === 'term' && !empty($type['taxonomy'])) {
                $terms = get_terms(array(
                    'taxonomy'   => $type['taxonomy'],
                    'search'     => $search,
                    'number'     => 20,
                    'hide_empty' => false,
                ));
                foreach ((array) $terms as $term) {
                    if (is_wp_error($term)) {
                        continue;
                    }
                    $results[] = array(
                        'value' => 'term:' . $type['taxonomy'] . ':' . $term->term_id,
                        'label' => $term->name . ' (' . $type['taxonomy'] . ')',
                    );
                }
            }
        }

        wp_send_json($results);
    }

    /* ===================================================================== */
    /*  RENDERING — delegated to the View helper for readability             */
    /* ===================================================================== */

    /**
     * Render a whole container (tabs + fields) for an object.
     *
     * @param Container $container
     * @param string    $object_type
     * @param int       $object_id
     * @return void
     */
    public static function render_container($container, $object_type, $object_id)
    {
        if ($object_type === 'post') {
            wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);
        }
        View::container($container, $object_type, $object_id);
    }
}
