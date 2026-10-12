<?php

/**
 * Accessible search modal with public post-type filtering.
 *
 * Provides the [theme_search_modal] shortcode / coptrz/search-modal block trigger,
 * injects the dialog markup into wp_footer, and wires the front-end search query
 * so ?post_type= is honoured on the main search.
 *
 * Plugin/Snippet Author: Digitally Disruptive - Donald Raymundo
 *
 * @package DigitallyDisruptive
 */

/**
 * Dialog element id shared by triggers, aria-controls, and footer markup.
 *
 * @return string
 */
function coptrz_search_modal_id()
{
    return 'coptrz-search-modal';
}

/**
 * Request-scoped flag: whether the modal markup + JS are needed this request.
 *
 * @param bool|null $set Optional. Pass true to mark as needed; omit to read.
 * @return bool
 */
function coptrz_search_modal_needed($set = null)
{
    static $needed = false;

    if (null !== $set) {
        $needed = (bool) $set;
    }

    return $needed;
}

/**
 * Public, searchable post types suitable for filter chips (excludes attachment
 * and non-queryable internals such as documents / globalpostboxes).
 *
 * @return array<string, string> Slug => singular label.
 */
function coptrz_searchable_post_types()
{
    static $cache = null;

    if (null !== $cache) {
        return $cache;
    }

    $objects = get_post_types(
        array(
            'public'             => true,
            'exclude_from_search' => false,
        ),
        'objects'
    );

    $skip = array('attachment', 'globalpostboxes', 'documents', 'landingpages', 'testimonials');
    $cache = array();

    foreach ($objects as $slug => $obj) {
        if (in_array($slug, $skip, true)) {
            continue;
        }
        if (isset($obj->publicly_queryable) && ! $obj->publicly_queryable) {
            continue;
        }
        $label = ! empty($obj->labels->singular_name)
            ? $obj->labels->singular_name
            : $obj->label;
        $cache[ $slug ] = $label;
    }

    /**
     * Filter the searchable post types shown in the modal / results filters.
     *
     * @param array<string, string> $cache Slug => label.
     */
    $cache = apply_filters('coptrz_searchable_post_types', $cache);

    return $cache;
}

/**
 * Inline SVG magnifying-glass icon for the search trigger.
 *
 * @return string
 */
function coptrz_search_modal_icon_svg()
{
    return '<svg class="coptrz-search-modal__icon" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M20 20L16.5 16.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
}

/**
 * Mark assets needed and enqueue the modal script (safe to call mid-render).
 *
 * @return void
 */
function coptrz_search_modal_enqueue_assets()
{
    coptrz_search_modal_needed(true);

    if (wp_script_is('coptrz-search-modal', 'enqueued')) {
        return;
    }

    $path = get_template_directory() . '/assets/js/search-modal.js';
    if (! file_exists($path)) {
        return;
    }

    wp_enqueue_script(
        'coptrz-search-modal',
        get_template_directory_uri() . '/assets/js/search-modal.js',
        array(),
        defined('coptz_version') ? coptz_version : filemtime($path),
        true
    );
}

/**
 * Render the accessible search trigger button.
 *
 * @param array|string $atts Shortcode / block attributes. Supports `label`.
 * @return string
 */
function coptrz_search_modal_trigger($atts = array())
{
    $atts = shortcode_atts(
        array(
            'label' => '',
        ),
        $atts,
        'theme_search_modal'
    );

    coptrz_search_modal_enqueue_assets();

    $modal_id = coptrz_search_modal_id();
    $label    = trim((string) $atts['label']);
    $aria     = $label
        ? $label
        : __('Open search', 'coptrz-theme');

    $html  = '<div class="coptrz-search-modal-trigger-wrap">';
    $html .= '<button type="button"';
    $html .= ' class="coptrz-search-modal-trigger header-icon d-inline-flex align-items-center"';
    $html .= ' data-bs-toggle="modal"';
    $html .= ' data-bs-target="#' . esc_attr($modal_id) . '"';
    $html .= ' aria-controls="' . esc_attr($modal_id) . '"';
    $html .= ' aria-label="' . esc_attr($aria) . '"';
    $html .= '>';
    $html .= coptrz_search_modal_icon_svg();
    if ('' !== $label) {
        $html .= '<span class="coptrz-search-modal-trigger__label">' . esc_html($label) . '</span>';
    }
    $html .= '</button>';
    $html .= '</div>';

    return $html;
}

/**
 * Shortcode callback for [theme_search_modal].
 *
 * @param array|string $atts Shortcode attributes.
 * @return string
 */
function coptrz_theme_search_modal_shortcode($atts = array())
{
    return coptrz_search_modal_trigger($atts);
}
add_shortcode('theme_search_modal', 'coptrz_theme_search_modal_shortcode');

/**
 * Build post-type filter controls (radio pills + mobile select).
 *
 * Shared by the modal dialog and the search results page form.
 *
 * @param string $selected Currently selected post type slug, or empty for all.
 * @param string $name     Input name attribute (default post_type).
 * @param string $id_prefix Prefix for input ids to avoid collisions.
 * @return string
 */
function coptrz_search_post_type_filters_html($selected = '', $name = 'post_type', $id_prefix = 'coptrz-pt')
{
    $types    = coptrz_searchable_post_types();
    $selected = is_string($selected) ? $selected : '';

    if ($selected && ! isset($types[ $selected ])) {
        $selected = '';
    }

    $html  = '<fieldset class="coptrz-search-filters">';
    $html .= '<legend class="screen-reader-text">' . esc_html__('Filter by content type', 'coptrz-theme') . '</legend>';

    // Mobile / narrow: native select (no name — JS syncs value onto the radios below).
    $html .= '<label class="coptrz-search-filters__select-label" for="' . esc_attr($id_prefix) . '-select">';
    $html .= '<span class="screen-reader-text">' . esc_html__('Content type', 'coptrz-theme') . '</span>';
    $html .= '<select class="coptrz-search-filters__select" id="' . esc_attr($id_prefix) . '-select" data-coptrz-search-type-select>';
    $html .= '<option value=""' . selected($selected, '', false) . '>' . esc_html__('All Content', 'coptrz-theme') . '</option>';
    foreach ($types as $slug => $label) {
        $html .= '<option value="' . esc_attr($slug) . '"' . selected($selected, $slug, false) . '>' . esc_html($label) . '</option>';
    }
    $html .= '</select>';
    $html .= '</label>';

    // Desktop: radio pills (these are what submit as post_type).
    $html .= '<div class="coptrz-search-filters__pills" role="presentation">';

    $all_id = $id_prefix . '-all';
    $html  .= '<label class="coptrz-search-filters__pill" for="' . esc_attr($all_id) . '">';
    $html  .= '<input type="radio" name="' . esc_attr($name) . '" id="' . esc_attr($all_id) . '" value="" data-coptrz-search-type-radio' . checked($selected, '', false) . ' />';
    $html  .= '<span>' . esc_html__('All Content', 'coptrz-theme') . '</span>';
    $html  .= '</label>';

    foreach ($types as $slug => $label) {
        $input_id = $id_prefix . '-' . sanitize_html_class($slug);
        $html    .= '<label class="coptrz-search-filters__pill" for="' . esc_attr($input_id) . '">';
        $html    .= '<input type="radio" name="' . esc_attr($name) . '" id="' . esc_attr($input_id) . '" value="' . esc_attr($slug) . '" data-coptrz-search-type-radio' . checked($selected, $slug, false) . ' />';
        $html    .= '<span>' . esc_html($label) . '</span>';
        $html    .= '</label>';
    }

    $html .= '</div>';
    $html .= '</fieldset>';

    return $html;
}

/**
 * Render a complete search form (input + post-type filters).
 *
 * @param array $args {
 *     @type string $s           Current search term.
 *     @type string $post_type   Selected post type slug.
 *     @type string $id_prefix   Prefix for filter input ids.
 *     @type string $input_id    Id of the search text input.
 *     @type string $form_class  Extra form class(es).
 * }
 * @return string
 */
function coptrz_search_form_html($args = array())
{
    $args = wp_parse_args(
        $args,
        array(
            's'          => '',
            'post_type'  => '',
            'id_prefix'  => 'coptrz-pt',
            'input_id'   => 'coptrz-search-input',
            'form_class' => '',
        )
    );

    $classes = trim('coptrz-search-form ' . $args['form_class']);

    $html  = '<form class="' . esc_attr($classes) . '" role="search" method="get" action="' . esc_url(home_url('/')) . '">';
    $html .= '<div class="coptrz-search-form__field">';
    $html .= '<label class="screen-reader-text" for="' . esc_attr($args['input_id']) . '">' . esc_html__('Search for:', 'coptrz-theme') . '</label>';
    $html .= '<input type="search" id="' . esc_attr($args['input_id']) . '" class="coptrz-search-form__input" name="s" value="' . esc_attr($args['s']) . '" placeholder="' . esc_attr__('Search…', 'coptrz-theme') . '" autocomplete="off" required />';
    $html .= '<button type="submit" class="coptrz-search-form__submit">' . esc_html__('Search', 'coptrz-theme') . '</button>';
    $html .= '</div>';
    $html .= coptrz_search_post_type_filters_html($args['post_type'], 'post_type', $args['id_prefix']);
    $html .= '</form>';

    return $html;
}

/**
 * Inject the Bootstrap search modal into the footer once per request when needed.
 *
 * @return void
 */
function coptrz_search_modal_markup()
{
    if (! coptrz_search_modal_needed()) {
        return;
    }

    // Ensure script is printed even if enqueued late (after wp_head).
    coptrz_search_modal_enqueue_assets();

    $modal_id = coptrz_search_modal_id();
    $title_id = $modal_id . '-title';

    echo '<div class="modal fade coptrz-search-modal" id="' . esc_attr($modal_id) . '" tabindex="-1" aria-labelledby="' . esc_attr($title_id) . '" aria-hidden="true">';
    echo '<div class="modal-dialog modal-dialog-centered">';
    echo '<div class="modal-content rounded-corner">';
    echo '<div class="modal-header">';
    echo '<h2 class="modal-title h5 mb-0" id="' . esc_attr($title_id) . '">' . esc_html__('Search', 'coptrz-theme') . '</h2>';
    echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="' . esc_attr__('Close search', 'coptrz-theme') . '"></button>';
    echo '</div>';
    echo '<div class="modal-body">';
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with escaping helpers.
    echo coptrz_search_form_html(
        array(
            's'          => get_search_query(),
            'post_type'  => coptrz_current_search_post_type(),
            'id_prefix'  => 'coptrz-modal-pt',
            'input_id'   => 'coptrz-search-modal-input',
            'form_class' => 'coptrz-search-form--modal',
        )
    );
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
add_action('wp_footer', 'coptrz_search_modal_markup', 40);

/**
 * Current search post_type query var / GET value, if valid.
 *
 * @return string
 */
function coptrz_current_search_post_type()
{
    $raw = '';
    if (isset($_GET['post_type'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $raw = sanitize_key(wp_unslash($_GET['post_type'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    } else {
        $qv = get_query_var('post_type');
        if (is_string($qv)) {
            $raw = sanitize_key($qv);
        } elseif (is_array($qv) && 1 === count($qv)) {
            $raw = sanitize_key(reset($qv));
        }
    }

    if ('' === $raw) {
        return '';
    }

    $types = coptrz_searchable_post_types();
    return isset($types[ $raw ]) ? $raw : '';
}

/**
 * Honour ?post_type= on the main front-end search query.
 *
 * When no type is selected ("All Content"), constrain the query to
 * coptrz_searchable_post_types() so internals like globalpostboxes /
 * landingpages / testimonials never appear in results.
 *
 * @param WP_Query $query The query instance.
 * @return void
 */
function coptrz_search_modal_pre_get_posts($query)
{
    if (is_admin() || ! $query->is_main_query() || ! $query->is_search()) {
        return;
    }

    $post_type = coptrz_current_search_post_type();
    if ($post_type) {
        $query->set('post_type', $post_type);
        return;
    }

    $allowed = array_keys(coptrz_searchable_post_types());
    if ($allowed) {
        $query->set('post_type', $allowed);
    }
}
add_action('pre_get_posts', 'coptrz_search_modal_pre_get_posts', 5);

/**
 * Force the theme search.php template for all front-end searches.
 *
 * WooCommerce's template loader can route ?s=…&post_type=product to
 * archive-product.php; keep filtered searches on our dedicated results UI.
 *
 * @param string $template Path to the template WordPress resolved.
 * @return string
 */
function coptrz_search_force_search_template($template)
{
    if (is_admin() || ! is_search()) {
        return $template;
    }

    $search = locate_template('search.php');
    return $search ? $search : $template;
}
add_filter('template_include', 'coptrz_search_force_search_template', 99);

/**
 * Enqueue modal JS on search results (filter UI) even without a trigger on-page.
 *
 * @return void
 */
function coptrz_search_modal_enqueue_on_search()
{
    if (is_search()) {
        coptrz_search_modal_enqueue_assets();
    }
}
add_action('wp_enqueue_scripts', 'coptrz_search_modal_enqueue_on_search', 20);

/**
 * Server-side render for the coptrz/search-modal block.
 *
 * @param string $block_content Block HTML (unused; save is null).
 * @param array  $block         Parsed block.
 * @return string
 */
function coptrz_render_search_modal_block($block_content, $block)
{
    if (empty($block['blockName']) || 'coptrz/search-modal' !== $block['blockName']) {
        return $block_content;
    }

    $attrs = isset($block['attrs']) ? $block['attrs'] : array();
    $label = isset($attrs['label']) ? (string) $attrs['label'] : '';

    return coptrz_search_modal_trigger(
        array(
            'label' => $label,
        )
    );
}
add_filter('render_block', 'coptrz_render_search_modal_block', 10, 2);

/**
 * Register a Block Pattern that inserts the search-modal trigger.
 *
 * @return void
 */
function coptrz_register_search_modal_pattern()
{
    if (! function_exists('register_block_pattern')) {
        return;
    }

    if (function_exists('register_block_pattern_category')) {
        register_block_pattern_category(
            'coptrz',
            array(
                'label' => __('Coptrz', 'coptrz-theme'),
            )
        );
    }

    register_block_pattern(
        'coptrz/search-modal-trigger',
        array(
            'title'       => __('Search Modal Trigger', 'coptrz-theme'),
            'description' => __('Accessible search button that opens a post-type-filtered search dialog.', 'coptrz-theme'),
            'categories'  => array('coptrz', 'header'),
            'keywords'    => array('search', 'modal', 'find'),
            'content'     => '<!-- wp:coptrz/search-modal /-->',
        )
    );
}
add_action('init', 'coptrz_register_search_modal_pattern');
