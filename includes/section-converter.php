<?php
/**
 * Plugin/Snippet Name: Section -> HTML Converter
 * Description: Retires the dynamic "sections" page-builder (the `sections` /
 *              `sections_after_main` complex meta) by freezing each section into
 *              static HTML:
 *                - NON-product posts  -> Gutenberg "Custom HTML" blocks appended
 *                  to post_content (edited thereafter in the block editor).
 *                - product posts      -> a lightweight, sortable HTML repeater
 *                  meta field (`sections_html` / `sections_after_main_html`),
 *                  since products have no block editor.
 *
 *              The original `_sections` data is KEPT (conversion is reversible)
 *              and the legacy builder meta box is hidden. Rendering is routed by
 *              `___sections()` (see modules.php) via coptrz_sections_is_converted().
 *
 *              Tools provided: a per-post "Convert" meta box (with dry-run) and a
 *              bulk runner under Tools > Convert Sections.
 *
 *              Caveat: freezing is a SNAPSHOT. Dynamic widgets (post grids,
 *              sliders) keep working visually (main.js re-inits by class) but no
 *              longer auto-update; embedded forms/popups that rely on per-request
 *              nonces become static. Review dynamic sections before converting.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

if (!defined('ABSPATH')) {
    exit;
}

/** Post meta flag marking a post as converted. */
const COPTRZ_SECTIONS_CONVERTED_FLAG = '_coptrz_sections_converted';

/** Backup: post_content exactly as it was immediately before conversion (non-product). */
const COPTRZ_PRE_CONVERT_CONTENT = '_coptrz_pre_convert_content';

/** Backup: _wp_page_template exactly as it was immediately before conversion (page only). */
const COPTRZ_PRE_CONVERT_TEMPLATE = '_coptrz_pre_convert_template';

/** Backup: the exact block markup this conversion appended to post_content (non-product). */
const COPTRZ_CONVERTED_BLOCKS = '_coptrz_converted_blocks';

/** Per-post opt-in: serve the legacy (unconverted) render to logged-out visitors. */
const COPTRZ_SERVE_LEGACY_PUBLIC = '_coptrz_serve_legacy_public';

/** Source page-builder fields, in render order. */
function coptrz_section_source_fields()
{
    return array('sections', 'sections_after_main');
}

/** Post types that can carry sections (drives the bulk tool + per-post box). */
function coptrz_section_post_types()
{
    return array(
        'page', 'product', 'layouts', 'capabilities', 'casestudies',
        'producttaxonomypages', 'industries', 'events', 'rentals', 'landingpages',
    );
}

/**
 * @param int $post_id
 * @return bool
 */
function coptrz_sections_is_converted($post_id)
{
    return get_post_meta($post_id, COPTRZ_SECTIONS_CONVERTED_FLAG, true) === 'yes';
}

/**
 * Whether the CURRENT frontend request should see the legacy (unconverted)
 * render of an otherwise-converted post — either an admin previewing via
 * ?coptrz_preview=original, or a logged-out visitor on a post that has opted
 * into serving the public the legacy sections. Request-scoped only: never
 * persisted, never touches the converted flag or _sections data.
 *
 * Deliberately NOT consulted by coptrz_sections_is_converted() — that flag is
 * the write-path guard against double-conversion (see the skip checks in
 * coptrz_convert_post_sections() / coptrz_convert_post_sections_to_blocks())
 * and must stay pure, or a preview/public-fallback request could re-trigger a
 * conversion and duplicate content.
 *
 * @param int $post_id
 * @return bool
 */
function coptrz_sections_legacy_override($post_id)
{
    if (is_admin() || !coptrz_sections_is_converted($post_id)) {
        return false;
    }
    if (isset($_GET['coptrz_preview']) && $_GET['coptrz_preview'] === 'original'
        && current_user_can('edit_post', $post_id)
    ) {
        return true;
    }
    if (!is_user_logged_in() && get_post_meta($post_id, COPTRZ_SERVE_LEGACY_PUBLIC, true) === 'yes') {
        return true;
    }
    return false;
}

/**
 * Whether this request should render the frozen conversion — i.e. the post is
 * converted AND no legacy override (see coptrz_sections_legacy_override()) is
 * in effect. Used at render sites; coptrz_sections_is_converted() remains the
 * one used by write-path guards.
 *
 * @param int $post_id
 * @return bool
 */
function coptrz_sections_render_converted($post_id)
{
    return coptrz_sections_is_converted($post_id) && !coptrz_sections_legacy_override($post_id);
}

/**
 * Whether ___sections() should render the frozen output instead of the legacy
 * builder for this post + field. True when the post is flagged converted (and
 * no legacy override applies), or — for products — whenever the HTML repeater
 * already holds rows (so the repeater is the natural editing surface for new
 * products too).
 *
 * @param int    $post_id
 * @param string $id  sections | sections_after_main
 * @return bool
 */
function coptrz_sections_should_route($post_id, $id)
{
    if (coptrz_sections_legacy_override($post_id)) {
        return false;
    }
    if (coptrz_sections_is_converted($post_id)) {
        return true;
    }
    if (get_post_type($post_id) === 'product') {
        $rows = get__post_meta_by_id($post_id, $id . '_html');
        return is_array($rows) && !empty($rows);
    }
    return false;
}

/* ========================================================================= */
/*  Legacy override plumbing — preview link + public fallback                */
/* ========================================================================= */

/**
 * A converted `page` still carries the Gutenberg page template
 * (templates/page-gutenberg.php), whose else-branch would render the
 * converted post_content via the_content() even once should_route() says
 * "legacy". Force the Modules template for the duration of the override so
 * ___hero_modules() + ___sections() (the legacy path) render instead. Other
 * post types keep their bespoke single templates, which already route through
 * ___sections() — should_route() alone is sufficient for them.
 */
add_filter('template_include', function ($template) {
    if (is_admin() || !is_singular('page')) {
        return $template;
    }
    $post_id = get_queried_object_id();
    if (!$post_id || !coptrz_sections_legacy_override($post_id)) {
        return $template;
    }
    $modules_template = locate_template('templates/page-modules.php');
    return $modules_template ?: $template;
});

/**
 * Fixed banner shown only while a legacy override is rendering the original
 * (unconverted) sections, so the tab can't be mistaken for the live page.
 */
add_action('wp_body_open', function () {
    $post_id = get_queried_object_id();
    if (!$post_id || !coptrz_sections_legacy_override($post_id)) {
        return;
    }
    $live_url = get_permalink($post_id);
    ?>
    <div style="position:fixed;top:0;left:0;right:0;z-index:99999;background:#b32d2e;color:#fff;text-align:center;font:600 13px/1.6 -apple-system,sans-serif;padding:6px 12px;">
        <?php esc_html_e('Previewing ORIGINAL (unconverted) sections.', 'coptrz-theme'); ?>
        <?php if ($live_url) : ?>
            — <a href="<?php echo esc_url($live_url); ?>" style="color:#fff;text-decoration:underline;"><?php esc_html_e('View converted version', 'coptrz-theme'); ?></a>
        <?php endif; ?>
    </div>
    <?php
});

/* ========================================================================= */
/*  Rendering for converted posts (called by ___sections())                  */
/* ========================================================================= */

/**
 * Split a block-mode product's post_content on the `coptrz/section-split`
 * marker block (assets/js/coptrz-section-split-block.js), so ___sections()'s
 * two call sites — before and after the buy box (includes/woocommerce.php,
 * action_woocommerce_before_main_content() / action_woocommerce_after_single_product_summary())
 * — each get their own half of ONE post_content.
 *
 * Everything before the marker becomes the `sections` slot; everything from
 * the marker onward (the marker itself renders nothing — see
 * coptrz_render_section_split_block(), functions.php) becomes
 * `sections_after_main`. No marker present -> the whole thing is
 * `sections_after_main`, since that's the slot every product actually used
 * before conversion (includes/woocommerce.php:78).
 *
 * Memoized per post — both slots are read in the same request.
 *
 * @param int $post_id
 * @return array{before:string,after:string}
 */
function coptrz_product_content_split($post_id)
{
    static $cache = array();
    if (isset($cache[$post_id])) {
        return $cache[$post_id];
    }

    $blocks = parse_blocks((string) get_post_field('post_content', $post_id));

    $before     = array();
    $after      = array();
    $seen_split = false;
    foreach ($blocks as $block) {
        if (!$seen_split && isset($block['blockName']) && $block['blockName'] === 'coptrz/section-split') {
            $seen_split = true;
            continue;
        }
        if ($seen_split) {
            $after[] = $block;
        } else {
            $before[] = $block;
        }
    }

    if (!$seen_split) {
        $after  = $before;
        $before = array();
    }

    $cache[$post_id] = array(
        'before' => serialize_blocks($before),
        'after'  => serialize_blocks($after),
    );

    return $cache[$post_id];
}

/**
 * Render the frozen output for a converted post + field id.
 *
 *  - product, mode 'html'   -> concatenate the `{$id}_html` repeater rows.
 *  - product, mode 'blocks' -> post_content split on coptrz/section-split
 *    (coptrz_product_content_split()); each half rendered as blocks.
 *  - other types             -> the body lives in post_content as Gutenberg
 *    blocks; the primary `sections` call returns the filtered content, and the
 *    secondary `sections_after_main` call returns '' (its blocks were appended
 *    to content).
 *
 * @param string $id       sections | sections_after_main
 * @param int    $post_id
 * @return string
 */
function coptrz_render_converted_sections($id, $post_id)
{
    // Re-entrancy guard: a converted post whose frozen content executes a
    // shortcode that routes back into ___sections() for the SAME post (e.g. a
    // [layouts] embed that resolves to this same post) would otherwise recurse
    // forever — most visible on the front page. Bail on re-entry.
    static $rendering = array();
    $guard = $id . ':' . (int) $post_id;
    if (!empty($rendering[$guard])) {
        return '';
    }
    $rendering[$guard] = true;

    $out = '';

    if (get_post_type($post_id) === 'product') {
        if (get_post_meta($post_id, '_coptrz_sections_mode', true) === 'blocks') {
            $parts = coptrz_product_content_split($post_id);
            $half  = ($id === 'sections') ? $parts['before'] : $parts['after'];
            $out   = do_shortcode(do_blocks($half));
        } else {
            $rows = get__post_meta_by_id($post_id, $id . '_html');
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    if (!empty($row['html'])) {
                        $out .= $row['html'];
                    }
                }
            }
            $out = do_shortcode($out);
        }
    } elseif ($id === 'sections') {
        // The body lives in post_content as Custom HTML blocks. Render ONLY
        // blocks + shortcodes — deliberately NOT apply_filters('the_content'),
        // which runs wpautop (mangles frozen markup) and every third-party
        // the_content filter (some redirect/misbehave on the front page).
        $content = get_post_field('post_content', $post_id);
        $out     = do_shortcode(do_blocks($content));
    }
    // sections_after_main for non-products returns '' (merged into post_content).

    unset($rendering[$guard]);
    return $out;
}

/* ========================================================================= */
/*  Product HTML repeater fields + hiding the legacy builder                  */
/* ========================================================================= */

/**
 * Register the product-only sortable HTML repeater fields and block the legacy
 * `sections` / `sections_after_main` builder meta boxes from rendering. Called
 * after post-meta.php has registered (so the shim classes + index exist) and
 * before Container_Admin::boot().
 *
 * @return void
 */
function coptrz_register_html_sections_fields()
{
    if (!class_exists('CoptrzTheme\\MetaShim\\Container')) {
        return;
    }

    $Container = 'CoptrzTheme\\MetaShim\\Container';
    $Field     = 'CoptrzTheme\\MetaShim\\Field';
    $Admin     = 'CoptrzTheme\\MetaShim\\Container_Admin';

    $row_fields = function () use ($Field) {
        return array(
            $Field::make('text', 'label', __('Label', 'coptrz-theme'))
                ->set_help_text(__('Admin label only — not output on the front end.', 'coptrz-theme')),
            $Field::make('textarea', 'html', __('Section HTML', 'coptrz-theme')),
        );
    };

    $Container::make('post_meta', __('Page Sections (HTML)', 'coptrz-theme'))
        ->where('post_type', '=', 'product')
        ->add_fields(array(
            $Field::make('complex', 'sections_html', __('Sections', 'coptrz-theme'))
                ->add_fields($row_fields())
                ->set_header_template('<%- label %>')
                ->set_collapsed(true),
            $Field::make('complex', 'sections_after_main_html', __('Sections After Main', 'coptrz-theme'))
                ->add_fields($row_fields())
                ->set_header_template('<%- label %>')
                ->set_collapsed(true),
        ));

    // Retire the legacy page-builder UI (data + index are retained).
    $Admin::hide_fields(coptrz_section_source_fields());

    // Also hide this HTML repeater itself once a product has been converted
    // to native blocks (mode 'blocks') — it's edited via the block editor at
    // that point, and the repeater's rows are no longer read at render time
    // (coptrz_render_converted_sections()). Kept visible for products still on
    // mode 'html' and for not-yet-converted products, matching prior behavior.
    $Admin::hide_fields(coptrz_html_sections_field_names());
}

/**
 * Field names of the product-only HTML repeater (see
 * coptrz_register_html_sections_fields()), used both to register it and to
 * blocklist/un-blocklist it per post below.
 *
 * @return array
 */
function coptrz_html_sections_field_names()
{
    return array('sections_html', 'sections_after_main_html');
}

/**
 * Bring the legacy "sections" builder meta box, or the product HTML repeater,
 * back into view for a single post where it's still the active editing
 * surface — hide_fields() above blocklists both globally, since most posts
 * using these field names have moved past them (to the legacy builder for a
 * converted post, or to native blocks for a block-mode product).
 */
add_filter('coptrz_meta_shim_field_visible', function ($visible, $field_name, $post_id) {
    if (in_array($field_name, coptrz_section_source_fields(), true) && !coptrz_sections_is_converted($post_id)) {
        return true;
    }
    if (in_array($field_name, coptrz_html_sections_field_names(), true)
        && get_post_meta($post_id, '_coptrz_sections_mode', true) !== 'blocks'
    ) {
        return true;
    }
    return $visible;
}, 10, 3);

/* ========================================================================= */
/*  Conversion engine                                                         */
/* ========================================================================= */

/**
 * Convert one post's sections into frozen HTML.
 *
 * Both admin UIs (the per-post meta box and the Tools > Convert Sections bulk
 * runner) now route to this ONLY for `product` posts, which have no block editor
 * and so use the `{$field}_html` repeater below. Every other post type routes to
 * coptrz_convert_post_sections_to_blocks() instead — this function's non-product
 * branch (Gutenberg Custom HTML blocks appended to post_content) is unreachable
 * from the UI but left in place rather than partially gutted.
 *
 * @param int  $post_id
 * @param bool $dry_run  When true, render + report but write nothing.
 * @return array Report: per-field counts, target, warnings, and (dry-run) HTML.
 */
function coptrz_convert_post_sections($post_id, $dry_run = false)
{
    $src = get_post($post_id);
    $report = array(
        'post_id'   => (int) $post_id,
        'title'     => $src ? $src->post_title : '',
        'type'      => $src ? $src->post_type : '',
        'is_product'=> false,
        'counts'    => array(),
        'target'    => '',
        'warnings'  => array(),
        'skipped'   => false,
    );

    if (!$src) {
        $report['warnings'][] = 'Post not found.';
        return $report;
    }

    if (!$dry_run && coptrz_sections_is_converted($post_id)) {
        $report['skipped'] = true;
        $report['warnings'][] = 'Already converted — skipped to avoid duplicating content.';
        return $report;
    }

    $is_product = ($src->post_type === 'product');
    $report['is_product'] = $is_product;
    $report['target'] = $is_product ? 'product HTML repeater meta' : 'Gutenberg Custom HTML blocks';

    // Ensure get_the_ID()/loop-aware inner renderers resolve to this post.
    global $post;
    $prev_post = $post;
    $post = $src;
    setup_postdata($post);

    $blocks_by_field = array();

    foreach (coptrz_section_source_fields() as $field) {
        $sections = get__post_meta_by_id($post_id, $field);
        if (empty($sections) || !is_array($sections)) {
            continue;
        }

        $rows = array();
        foreach ($sections as $key => $section) {
            if (!empty($section['disable_section'])) {
                continue;
            }
            // Freeze the section markup but DO NOT expand shortcodes: global
            // widgets (e.g. [brands_logo_slider], [case_study_slider_grid]),
            // reusable [layouts id='N'] embeds, and any other shortcode stay
            // literal so they remain dynamic. They are resolved at render time —
            // products via the template's do_shortcode(___sections()),
            // non-products via the do_shortcode pass over the Custom HTML block.
            $html = trim(___sections($field, $post_id, $key));
            // Drop product widgets with no product selected: an empty
            // [product_add_to_cart id=''] renders nothing and would otherwise show
            // as literal text where do_shortcode is not applied.
            $html = preg_replace('/\[product_add_to_cart\s+id=([\'"])\1[^\]]*\]/', '', $html);
            if ($html === '') {
                continue;
            }
            $rows[] = array(
                'label' => !empty($section['title']) ? $section['title'] : ('Section ' . ($key + 1)),
                'html'  => $html,
            );
        }

        if (empty($rows)) {
            continue;
        }
        $report['counts'][$field] = count($rows);

        if ($is_product) {
            if (!$dry_run) {
                $value = array();
                foreach ($rows as $row) {
                    $value[] = array('_type' => '_', 'label' => $row['label'], 'html' => $row['html']);
                }
                coptrz_set_post_meta($post_id, $field . '_html', $value);
            }
        } else {
            $blocks = '';
            foreach ($rows as $row) {
                $blocks .= "<!-- wp:html -->\n" . $row['html'] . "\n<!-- /wp:html -->\n\n";
            }
            $blocks_by_field[$field] = $blocks;
        }

        if ($dry_run) {
            $report['html'][$field] = $rows;
        }
    }

    // Non-products: append the generated blocks to post_content (in order).
    if (!$is_product && !empty($blocks_by_field)) {
        $existing = (string) $src->post_content;
        if (trim($existing) !== '') {
            $report['warnings'][] = 'post_content was not empty — section blocks appended after existing content.';
        }
        $new_content = $existing;
        foreach (coptrz_section_source_fields() as $field) {
            if (!empty($blocks_by_field[$field])) {
                $new_content .= ($new_content !== '' ? "\n\n" : '') . $blocks_by_field[$field];
            }
        }
        if (!$dry_run) {
            wp_update_post(array('ID' => $post_id, 'post_content' => $new_content));
        }

        // Converted pages switch to the Gutenberg page template so their frozen
        // blocks render via the standard the_content() path (hero + content),
        // retiring the legacy Modules/sections builder template. Scoped to the
        // `page` type — page-gutenberg.php is a page template, and CPTs keep
        // their bespoke single templates (which already render the frozen content
        // through ___sections()).
        if ($src->post_type === 'page') {
            $report['template'] = 'templates/page-gutenberg.php';
            if (!$dry_run) {
                update_post_meta($post_id, '_wp_page_template', 'templates/page-gutenberg.php');
            }
        }
    }

    // Restore loop context.
    wp_reset_postdata();
    $post = $prev_post;

    if (!$dry_run && !empty($report['counts'])) {
        update_post_meta($post_id, COPTRZ_SECTIONS_CONVERTED_FLAG, 'yes');
    }

    if (empty($report['counts'])) {
        $report['warnings'][] = 'No active sections found to convert.';
    }

    return $report;
}

/* ========================================================================= */
/*  Native Gutenberg block conversion (mode: "blocks")                        */
/* ========================================================================= */
/*
 * Converts each section ELEMENT into the matching native block instead of a
 * single frozen Custom HTML block. Built around a per-element mapping registry
 * (coptrz_block_item_mappers) that is filled in from the project's element→block
 * guide. Any section whose items are not ALL natively mapped falls back to the
 * same whole-section Custom HTML snapshot the HTML converter produces (so output
 * is always valid and shortcodes/[layouts] stay literal & dynamic).
 *
 * Non-product only (native blocks need the block editor); products keep the HTML
 * repeater. Rendering is unchanged: a converted non-product renders its
 * post_content via do_blocks()+do_shortcode() (coptrz_render_converted_sections),
 * which handles native blocks and the core/shortcode fallbacks alike.
 */

/**
 * Recursively sanitise a Carbon item array before it's stored as a block
 * attribute. serialize_block_attributes() wp_json_encode()s the attrs array;
 * wp_json_encode() returns false on invalid UTF-8, which serialize_block()
 * then silently turns into an EMPTY attrs comment — total, silent data loss,
 * not an error. Key-preserving (nested `_type` discriminators, e.g. on
 * drone_servicing_grid's service_features rows, must survive unchanged).
 *
 * @return array|null null if the value can't be made JSON-safe at all.
 */
function coptrz_json_safe_array($value)
{
    if (is_array($value)) {
        $out = array();
        foreach ($value as $k => $v) {
            $key = is_string($k) ? wp_check_invalid_utf8($k, true) : $k;
            $out[$key] = coptrz_json_safe_array($v);
        }
        return $out;
    }
    if (is_object($value)) {
        return coptrz_json_safe_array((array) $value);
    }
    if (is_string($value)) {
        return wp_check_invalid_utf8($value, true);
    }
    if (is_scalar($value) || $value === null) {
        return $value;
    }
    return null;
}

/**
 * Build a leaf parsed-block array for serialize_block()/serialize_blocks().
 *
 * @param string $name       e.g. 'core/heading'
 * @param array  $attrs      block attributes (serialized to the JSON comment)
 * @param string $inner_html the block's save markup
 * @return array
 */
function coptrz_block($name, $attrs = array(), $inner_html = '')
{
    $inner_html = (string) $inner_html;
    return array(
        'blockName'    => $name,
        'attrs'        => is_array($attrs) ? $attrs : array(),
        'innerBlocks'  => array(),
        'innerHTML'    => $inner_html,
        'innerContent' => $inner_html === '' ? array() : array($inner_html),
    );
}

/**
 * Block with inner blocks (core/buttons, core/columns, core/column, core/group, …).
 * innerContent alternates static wrapper strings and null placeholders (one per
 * inner block), which is what serialize_block() / serialize_blocks() expect.
 *
 * @param string $name        e.g. 'core/columns'
 * @param array  $attrs       block attributes
 * @param string $wrap_open   opening HTML (e.g. '<div class="wp-block-columns">')
 * @param string $wrap_close  closing HTML (e.g. '</div>')
 * @param array  $inner_blocks array of parsed-block arrays
 * @return array
 */
function coptrz_block_container($name, $attrs, $wrap_open, $wrap_close, array $inner_blocks)
{
    $inner_content = array($wrap_open);
    foreach ($inner_blocks as $unused) {
        $inner_content[] = null;
    }
    $inner_content[] = $wrap_close;
    return array(
        'blockName'    => $name,
        'attrs'        => is_array($attrs) ? $attrs : array(),
        'innerBlocks'  => array_values($inner_blocks),
        'innerHTML'    => $wrap_open . $wrap_close,
        'innerContent' => $inner_content,
    );
}

/**
 * Derive a column's outer width classes and inner `.column-holder` styling
 * from its `column_styles` complex, mirroring the per-column switch in
 * ____columns_modules() (modules.php:2065-2224 for individual settings,
 * :1841-1931 for the shared-styles branch applied to every column when
 * `individual_column_settings` is off). The legacy renderer nests each
 * column's content in a `.column-holder` div carrying background/padding/
 * border/etc — that div is reproduced here as a `core/group` by the caller,
 * only when this returns non-empty `holder_classes`/`holder_css`, since an
 * unstyled column has nothing worth an extra nesting level for.
 *
 * @param array  $col                         one row of the `columns` complex
 * @param array  $shared_styles                the row-level `column_styles` complex, applied to
 *                                              every column when $individual_column_settings is false
 * @param bool   $individual_column_settings
 * @param string $mobile_styling               row-level `image_left`/`image_right` modifier — legacy
 *                                              only actually applies this in the shared-styles branch
 *                                              ($classes is reset to [] per-column in individual mode,
 *                                              wiping it out before it can take effect; modules.php:1836-1838
 *                                              vs :2061)
 * @param bool   $same_image_height
 * @param string $image_fit
 * @param string $image_padding
 * @return array{column_classes:string[],holder_classes:string[],holder_css:string[]}
 */
function coptrz_column_wrapper_data($col, array $shared_styles, $individual_column_settings, $mobile_styling, $same_image_height = false, $image_fit = '', $image_padding = '')
{
    $column_classes = array();
    $holder_classes = array('column-holder', 'content-margin', 'overflow-hidden', 'position-relative', 'h-100');
    $holder_css     = array();

    if ($individual_column_settings) {
        $styles = (isset($col['column_styles']) && is_array($col['column_styles'])) ? $col['column_styles'] : array();
    } else {
        $styles = $shared_styles;
        if ($mobile_styling !== '') {
            $holder_classes[] = $mobile_styling;
        }
    }

    foreach ($styles as $style) {
        $type = isset($style['_type']) ? $style['_type'] : '';
        switch ($type) {
            case 'padding':
                foreach (array('padding_top', 'padding_bottom', 'padding_left', 'padding_right') as $k) {
                    if (!empty($style[$k])) {
                        $holder_classes[] = $style[$k];
                    }
                }
                break;

            case 'margin':
                foreach (array('margin_top', 'margin_bottom', 'margin_left', 'margin_right') as $k) {
                    if (!empty($style[$k])) {
                        $holder_classes[] = $style[$k];
                    }
                }
                break;

            case 'custom_class':
                if (!empty($style['custom_class'])) {
                    $holder_classes[] = $style['custom_class'];
                }
                break;

            case 'alignment':
                foreach (array('align_items', 'justify_content', 'flex_direction') as $k) {
                    if (!empty($style[$k])) {
                        $holder_classes[] = $style[$k];
                    }
                }

                $text_align        = isset($style['text_align']) ? $style['text_align'] : '';
                $text_align_tablet = isset($style['text_align_tablet']) ? $style['text_align_tablet'] : '';
                $text_align_mobile = isset($style['text_align_mobile']) ? $style['text_align_mobile'] : '';

                if ($text_align !== '') {
                    $holder_classes[] = $text_align;
                }

                if (!$text_align_tablet) {
                    if ($text_align === 'text-lg-start') {
                        $holder_classes[] = 'text-md-start';
                    } elseif ($text_align === 'text-lg-center') {
                        $holder_classes[] = 'text-md-center';
                    } elseif ($text_align === 'text-lg-end') {
                        $holder_classes[] = 'text-md-end';
                    }
                } else {
                    $holder_classes[] = $text_align_tablet;
                }

                if (!$text_align_mobile) {
                    if ($text_align_tablet) {
                        if ($text_align_tablet === 'text-md-start') {
                            $holder_classes[] = 'text-start';
                        } elseif ($text_align_tablet === 'text-md-center') {
                            $holder_classes[] = 'text-center';
                        } elseif ($text_align_tablet === 'text-md-end') {
                            $holder_classes[] = 'text-end';
                        }
                    } else {
                        if ($text_align === 'text-lg-start') {
                            $holder_classes[] = 'text-start';
                        } elseif ($text_align === 'text-lg-center') {
                            $holder_classes[] = 'text-center';
                        } elseif ($text_align === 'text-lg-end') {
                            $holder_classes[] = 'text-end';
                        }
                    }
                } else {
                    $holder_classes[] = $text_align_mobile;
                }

                if (!empty($style['align_items']) || !empty($style['justify_content']) || !empty($style['flex_direction'])) {
                    $holder_classes[] = 'd-flex flex-wrap';
                }
                break;

            case 'text_color':
                if (!empty($style['text_color'])) {
                    $holder_classes[] = $style['text_color'];
                }
                if (!empty($style['text_color_custom'])) {
                    $holder_css[] = 'color: ' . $style['text_color_custom'];
                }
                break;

            case 'background_color':
                if (!empty($style['background_color'])) {
                    $holder_classes[] = $style['background_color'];
                }
                // Individual-settings branch gates the custom color on
                // background_color === 'bg-custom' (modules.php:2143); the
                // shared-styles branch doesn't (modules.php:1919) — an existing
                // discrepancy between the two legacy code paths, preserved here.
                if (!empty($style['background_color_custom'])) {
                    if (!$individual_column_settings || (isset($style['background_color']) && $style['background_color'] === 'bg-custom')) {
                        $holder_css[] = 'background-color: ' . $style['background_color_custom'];
                    }
                }
                break;

            case 'background_image':
                foreach (array('background_attachment', 'background_size', 'background_repeat') as $k) {
                    if (!empty($style[$k])) {
                        $holder_classes[] = $style[$k];
                    }
                }
                if (!empty($style['background_image'])) {
                    $url = wp_get_attachment_image_url((int) $style['background_image'], 'full');
                    if ($url) {
                        $holder_css[] = 'background-image: url(' . $url . ')';
                    }
                }
                break;

            case 'border':
                $border_radius = isset($style['border_radius']) ? $style['border_radius'] : '';
                if ($border_radius === 'custom') {
                    if (!empty($style['border_radius_custom'])) {
                        $holder_css[] = 'border-radius: ' . $style['border_radius_custom'];
                    }
                } elseif ($border_radius) {
                    $holder_classes[] = $border_radius;
                }

                $border_style = isset($style['border_style']) ? $style['border_style'] : '';
                if ($border_style === 'border-custom') {
                    $border_color = isset($style['border_color']) ? $style['border_color'] : '';
                    if ($border_color === 'border-custom-color') {
                        if (!empty($style['border_color_custom'])) {
                            $holder_css[] = 'border-color: ' . $style['border_color_custom'];
                        }
                    } elseif ($border_color) {
                        $holder_classes[] = $border_color;
                    }

                    $border_width = isset($style['border_width']) ? $style['border_width'] : '';
                    if ($border_width === 'custom') {
                        $holder_classes[] = 'border-width-custom';
                        foreach (array('top', 'right', 'bottom', 'left') as $side) {
                            $side_val = isset($style["border_width_{$side}"]) ? $style["border_width_{$side}"] : '';
                            if (!empty($side_val)) {
                                $holder_css[] = "border-{$side}-width: {$side_val}";
                            }
                        }
                    } else {
                        $holder_classes[] = 'border-default';
                    }
                } elseif ($border_style) {
                    $holder_classes[] = $border_style;
                }
                break;

            case 'column_width':
                foreach (array('column_width', 'column_width_tablet', 'column_width_mobile') as $k) {
                    if (!empty($style[$k])) {
                        $column_classes[] = $style[$k];
                    }
                }
                break;
        }
    }

    if (empty($column_classes)) {
        $column_classes[] = 'col';
    }

    if ($same_image_height) {
        $holder_classes[] = 'same-image-height';
        if ($image_fit !== '') {
            $holder_css[] = '--object-fit: ' . $image_fit;
        }
        if ($image_padding !== '') {
            $holder_css[] = '--image-padding: ' . $image_padding;
        }
    }

    // Only the fixed base classes → no author-supplied styling worth an
    // extra nesting level for; caller checks this to decide whether to emit
    // the .column-holder wrapper at all.
    $extra_holder_classes = array_diff($holder_classes, array('column-holder', 'content-margin', 'overflow-hidden', 'position-relative', 'h-100'));
    if (empty($extra_holder_classes) && empty($holder_css)) {
        $holder_classes = array();
    }

    $dedupe = function ($arr) {
        return array_values(array_unique(array_filter($arr, function ($c) {
            return trim((string) $c) !== '';
        })));
    };

    return array(
        'column_classes' => $dedupe($column_classes),
        'holder_classes' => $dedupe($holder_classes),
        'holder_css'      => $holder_css,
    );
}

/**
 * Derive the section's wrapper classes from `section_styles`, mirroring the
 * class-building switch in ___sections() (modules.php ~L521-753) so a native
 * conversion keeps the same look. Only class-based styling is reproduced here;
 * any style that ___sections() would otherwise render as a raw inline `style`
 * attribute (custom colors, background image/gradient/video, min-height,
 * custom border widths/colors, custom container max-width) has no safe
 * equivalent on core/group without risking editor block-validation mismatches,
 * so it's reported via `unmappable` and the caller snapshots the whole section
 * to Custom HTML instead — same fallback the item-mapper registry already uses.
 *
 * @param array $section
 * @return array{section_classes:string[],container_classes:string[],unmappable:bool}
 */
function coptrz_section_wrapper_data($section)
{
    $classes           = array('section');
    $container_classes = array('position-relative container-inner');
    $section_css       = array();
    $container_css     = array();
    $unmappable        = false;

    $section_class = isset($section['section_class']) ? trim((string) $section['section_class']) : '';
    if ($section_class !== '') {
        $classes[] = $section_class;
    }

    $section_styles = (isset($section['section_styles']) && is_array($section['section_styles']))
        ? $section['section_styles']
        : array();

    // Emits the section/container border pair modules.php:654-751 builds — radius,
    // then style (which, when 'border-custom', branches into color and a
    // possibly-per-side width) — shared between the section and container border
    // fields via $prefix ('' or 'container_') since the two are structurally
    // identical, just targeting different class/CSS buckets.
    $border_pair = function ($style, $prefix, &$classes, &$css) {
        $radius = isset($style["{$prefix}border_radius"]) ? $style["{$prefix}border_radius"] : '';
        if ($radius === 'custom') {
            if (!empty($style["{$prefix}border_radius_custom"])) {
                $css[] = 'border-radius: ' . $style["{$prefix}border_radius_custom"];
            }
        } elseif ($radius) {
            $classes[] = $radius;
        }

        $border_style = isset($style["{$prefix}border_style"]) ? $style["{$prefix}border_style"] : '';
        if ($border_style === 'border-custom') {
            $color = isset($style["{$prefix}border_color"]) ? $style["{$prefix}border_color"] : '';
            if ($color === 'border-custom-color') {
                if (!empty($style["{$prefix}border_color_custom"])) {
                    $css[] = 'border-color: ' . $style["{$prefix}border_color_custom"];
                }
            } elseif ($color) {
                $classes[] = $color;
            }

            $width = isset($style["{$prefix}border_width"]) ? $style["{$prefix}border_width"] : '';
            if ($width === 'custom') {
                $classes[] = 'border-width-custom';
                foreach (array('top', 'right', 'bottom', 'left') as $side) {
                    $side_val = isset($style["{$prefix}border_width_{$side}"]) ? $style["{$prefix}border_width_{$side}"] : '';
                    if (!empty($side_val)) {
                        $css[] = "border-{$side}-width: {$side_val}";
                    }
                }
            } else {
                $classes[] = 'border-default';
            }
        } elseif ($border_style) {
            $classes[] = $border_style;
        }
    };

    foreach ($section_styles as $style) {
        $type = isset($style['_type']) ? $style['_type'] : '';
        switch ($type) {
            case 'padding':
                foreach (array('padding_top', 'padding_bottom', 'padding_left', 'padding_right') as $k) {
                    if (!empty($style[$k])) {
                        $classes[] = $style[$k];
                    }
                }
                foreach (array('container_padding_top', 'container_padding_bottom', 'container_padding_left', 'container_padding_right') as $k) {
                    if (!empty($style[$k])) {
                        $container_classes[] = $style[$k];
                    }
                }
                break;

            case 'margin':
                foreach (array('margin_top', 'margin_bottom', 'margin_left', 'margin_right') as $k) {
                    if (!empty($style[$k])) {
                        $classes[] = $style[$k];
                    }
                }
                break;

            case 'custom_class':
                if (!empty($style['custom_class'])) {
                    $classes[] = $style['custom_class'];
                }
                break;

            case 'alignment':
                if (!empty($style['align_items'])) {
                    $classes[] = $style['align_items'];
                }
                if (!empty($style['justify_content'])) {
                    $classes[] = $style['justify_content'];
                }
                if (!empty($style['text_align'])) {
                    $classes[] = $style['text_align'];
                }
                if (!empty($style['align_items']) || !empty($style['justify_content'])) {
                    $classes[] = 'd-flex';
                }
                break;

            case 'text_color':
                if (!empty($style['text_color'])) {
                    $classes[] = $style['text_color'];
                }
                if (!empty($style['text_color_custom'])) {
                    $section_css[] = 'color: ' . $style['text_color_custom'];
                }
                break;

            case 'background_color':
                if (!empty($style['background_color_container'])) {
                    $container_classes[] = $style['background_color_container'];
                }
                if (!empty($style['background_color'])) {
                    $classes[] = $style['background_color'];
                }
                if (!empty($style['background_color_custom'])) {
                    $section_css[] = 'background-color: ' . $style['background_color_custom'];
                }
                break;

            case 'background_image':
                if (!empty($style['background_image'])) {
                    $url = wp_get_attachment_image_url((int) $style['background_image'], 'full');
                    if ($url) {
                        $section_css[] = 'background-image: url(' . $url . ')';
                    }
                }
                if (!empty($style['background_attachment'])) {
                    $classes[] = $style['background_attachment'];
                }
                if (!empty($style['background_size'])) {
                    $classes[] = $style['background_size'];
                }
                if (!empty($style['background_repeat'])) {
                    $classes[] = $style['background_repeat'];
                }
                break;

            case 'background_video':
                if (!empty($style['background']) || !empty($style['background_youtube'])) {
                    $unmappable = true; // __background() markup has no block equivalent.
                }
                break;

            case 'background_overlay':
                $overlay_type = isset($style['background_overlay_type']) ? $style['background_overlay_type'] : '';
                if ($overlay_type === 'image') {
                    $unmappable = true; // __image() overlay markup has no block equivalent.
                } elseif ($overlay_type === 'custom') {
                    if (!empty($style['background_overlay_custom'])) {
                        $section_css[] = '--background-overlay-custom: ' . $style['background_overlay_custom'];
                    }
                } elseif ($overlay_type) {
                    $classes[] = "background-overlay $overlay_type";
                }
                break;

            case 'background_gradient':
                $gradient = isset($style['background_gradient']) ? $style['background_gradient'] : '';
                if ($gradient === 'custom') {
                    $color_1 = isset($style['background_gradient_color_1']) ? $style['background_gradient_color_1'] : '';
                    $stop_1  = isset($style['background_gradient_stop_1']) ? $style['background_gradient_stop_1'] : '';
                    $color_2 = isset($style['background_gradient_color_2']) ? $style['background_gradient_color_2'] : '';
                    $stop_2  = isset($style['background_gradient_stop_2']) ? $style['background_gradient_stop_2'] : '';
                    if (isset($style['background_gradient_type']) && $style['background_gradient_type'] === 'radial-gradient') {
                        $gradient_css = "radial-gradient(circle, {$color_1} {$stop_1}, {$color_2} {$stop_2})";
                    } else {
                        $direction    = isset($style['background_gradient_direction']) ? $style['background_gradient_direction'] : '';
                        $gradient_css = "linear-gradient({$direction}, {$color_1} {$stop_1}, {$color_2} {$stop_2})";
                    }
                    $section_css[] = 'background: ' . $gradient_css;
                } elseif ($gradient) {
                    $classes[] = $gradient;
                }
                break;

            case 'container_width':
                if (!empty($style['container_width'])) {
                    $classes[] = $style['container_width'];
                }
                if (!empty($style['custom_container_width'])) {
                    $container_css[] = 'max-width: ' . $style['custom_container_width'];
                }
                break;

            case 'height':
                if (!empty($style['height'])) {
                    $section_css[] = 'min-height: ' . $style['height'];
                }
                break;

            case 'border':
                $border_pair($style, '', $classes, $section_css);
                $border_pair($style, 'container_', $container_classes, $container_css);
                break;
        }
    }

    return array(
        'section_classes'   => array_values(array_unique(array_filter($classes, function ($c) {
            return trim((string) $c) !== '';
        }))),
        'container_classes' => array_values(array_unique(array_filter($container_classes, function ($c) {
            return trim((string) $c) !== '';
        }))),
        'section_css'       => $section_css,
        'container_css'     => $container_css,
        'unmappable'        => $unmappable,
    );
}

/**
 * Wrap already-parsed inner blocks in <section class="…"> > .container
 * (> .container-inner, when more than the seeded entry) groups, matching the
 * wrapper ___sections() emits (modules.php L774-790) so converted sections keep
 * Bootstrap's `.container` — the theme's SCSS is written against it. Returns a
 * parsed-block array (via coptrz_block_container) ready for serialize_blocks().
 *
 * Any `section_css` / `container_css` declarations from coptrz_section_wrapper_data()
 * go on the matching group's `ddCustomCSS` attribute — the theme's existing
 * per-block Custom CSS mechanism (digitally_disruptive_render_custom_css() in
 * functions.php, already whitelisted for core/group). Being a plain string
 * attribute (not a block-support style) it carries zero editor-validation risk
 * and stays user-editable afterwards in the block's own Custom CSS panel.
 *
 * @param array  $wrapper_data from coptrz_section_wrapper_data()
 * @param array  $inner_blocks parsed-block arrays for the section content
 * @param string $title        section title; sets the outer group's List View
 *                              name (attributes.metadata.name) when non-empty
 * @return array
 */
function coptrz_block_group(array $wrapper_data, array $inner_blocks, $title = '')
{
    $section_classes   = $wrapper_data['section_classes'];
    $container_classes = $wrapper_data['container_classes'];
    $section_css       = isset($wrapper_data['section_css']) ? $wrapper_data['section_css'] : array();
    $container_css     = isset($wrapper_data['container_css']) ? $wrapper_data['container_css'] : array();

    $section_class_attr = implode(' ', $section_classes);
    $section_attrs = array('tagName' => 'section', 'layout' => array('type' => 'default'));
    if ($section_class_attr !== '') {
        $section_attrs['className'] = $section_class_attr;
    }
    if (!empty($section_css)) {
        $section_attrs['ddCustomCSS'] = implode('; ', $section_css) . ';';
    }
    $title = trim((string) $title);
    if ($title !== '') {
        $section_attrs['metadata'] = array('name' => $title);
    }
    $section_div_class = trim('wp-block-group ' . $section_class_attr);

    $container_attrs     = array('className' => 'container', 'layout' => array('type' => 'default'));
    $container_div_class = 'wp-block-group container';

    $content = $inner_blocks;

    // Extra .container-inner wrapper only when there's more than the seeded
    // entry — matches count($container_classes) > 1 at modules.php:788. Only
    // in this branch does container_css have anywhere to attach: matches
    // ___sections(), where $container_styles is likewise only ever applied to
    // this same conditionally-emitted div (modules.php:788-790).
    if (count($container_classes) > 1) {
        $inner_class_attr = implode(' ', $container_classes);
        $inner_attrs      = array('className' => $inner_class_attr, 'layout' => array('type' => 'default'));
        if (!empty($container_css)) {
            $inner_attrs['ddCustomCSS'] = implode('; ', $container_css) . ';';
        }
        $inner_div_class  = trim('wp-block-group ' . $inner_class_attr);
        $content = array(coptrz_block_container(
            'core/group',
            $inner_attrs,
            '<div class="' . esc_attr($inner_div_class) . '">',
            '</div>',
            $inner_blocks
        ));
    }

    $container_group = coptrz_block_container(
        'core/group',
        $container_attrs,
        '<div class="' . esc_attr($container_div_class) . '">',
        '</div>',
        $content
    );

    return coptrz_block_container(
        'core/group',
        $section_attrs,
        '<section class="' . esc_attr($section_div_class) . '">',
        '</section>',
        array($container_group)
    );
}

/**
 * Legacy `post_grid` section-item row → `coptrz/post-grid` block attributes.
 * The inverse of coptrz_post_grid_box_styles_rows() / coptrz_post_grid_elements_rows()
 * / coptrz_post_grid_attrs_to_data() (functions.php), which go the other
 * direction for rendering. Kept here (not functions.php) since it's only ever
 * used by the converter, not by anything render-related.
 *
 * @param array $item the `post_grid` section_items row (Carbon Fields shape)
 * @return array coptrz/post-grid block attributes
 */
function coptrz_post_grid_legacy_item_to_attrs($item)
{
    $attrs = array(
        'isSlider'      => !empty($item['is_slider']),
        'slidesDesktop' => (isset($item['number_of_slides']) && $item['number_of_slides'] !== '') ? (string) $item['number_of_slides'] : '6',
        'slidesTablet'  => isset($item['number_of_slides_tablet']) ? (string) $item['number_of_slides_tablet'] : '',
        'slidesMobile'  => isset($item['number_of_slides_mobile']) ? (string) $item['number_of_slides_mobile'] : '',
    );

    $box = array();
    foreach ((isset($item['post_box_styles']) && is_array($item['post_box_styles'])) ? $item['post_box_styles'] : array() as $row) {
        $t = isset($row['_type']) ? $row['_type'] : '';
        switch ($t) {
            case 'background_color':
                $box['backgroundColor']       = isset($row['background_color']) ? $row['background_color'] : '';
                $box['backgroundColorCustom'] = isset($row['background_color_custom']) ? $row['background_color_custom'] : '';
                break;
            case 'text_color':
                $box['textColor']       = isset($row['text_color']) ? $row['text_color'] : '';
                $box['textColorCustom'] = isset($row['text_color_custom']) ? $row['text_color_custom'] : '';
                break;
            case 'padding':
                $box['paddingTop']    = isset($row['padding_top']) ? $row['padding_top'] : '';
                $box['paddingBottom'] = isset($row['padding_bottom']) ? $row['padding_bottom'] : '';
                $box['paddingLeft']   = isset($row['padding_left']) ? $row['padding_left'] : '';
                $box['paddingRight']  = isset($row['padding_right']) ? $row['padding_right'] : '';
                break;
            case 'margin':
                $box['marginTop']    = isset($row['margin_top']) ? $row['margin_top'] : '';
                $box['marginBottom'] = isset($row['margin_bottom']) ? $row['margin_bottom'] : '';
                $box['marginLeft']   = isset($row['margin_left']) ? $row['margin_left'] : '';
                $box['marginRight']  = isset($row['margin_right']) ? $row['margin_right'] : '';
                break;
            case 'alignment':
                $box['alignItems']      = isset($row['align_items']) ? $row['align_items'] : '';
                $box['justifyContent']  = isset($row['justify_content']) ? $row['justify_content'] : '';
                $box['textAlign']       = isset($row['text_align']) ? $row['text_align'] : '';
                break;
            case 'column_width':
                $box['columnWidth']       = isset($row['column_width']) ? $row['column_width'] : '';
                $box['columnWidthTablet'] = isset($row['column_width_tablet']) ? $row['column_width_tablet'] : '';
                $box['columnWidthMobile'] = isset($row['column_width_mobile']) ? $row['column_width_mobile'] : '';
                break;
            case 'border':
                $box['borderRadius']       = isset($row['border_radius']) ? $row['border_radius'] : '';
                $box['borderRadiusCustom'] = isset($row['border_radius_custom']) ? $row['border_radius_custom'] : '';
                $box['borderStyle']        = isset($row['border_style']) ? $row['border_style'] : '';
                $box['borderColor']        = isset($row['border_color']) ? $row['border_color'] : '';
                $box['borderColorCustom']  = isset($row['border_color_custom']) ? $row['border_color_custom'] : '';
                $box['borderWidth']        = isset($row['border_width']) ? $row['border_width'] : '';
                $box['borderWidthTop']     = isset($row['border_width_top']) ? $row['border_width_top'] : '';
                $box['borderWidthRight']   = isset($row['border_width_right']) ? $row['border_width_right'] : '';
                $box['borderWidthBottom']  = isset($row['border_width_bottom']) ? $row['border_width_bottom'] : '';
                $box['borderWidthLeft']    = isset($row['border_width_left']) ? $row['border_width_left'] : '';
                break;
            case 'custom_class':
                $box['customClass'] = isset($row['custom_class']) ? $row['custom_class'] : '';
                break;
        }
    }
    $attrs['boxStyles'] = $box;

    $elements = array();
    foreach ((isset($item['post_elements']) && is_array($item['post_elements'])) ? $item['post_elements'] : array() as $row) {
        $t = isset($row['_type']) ? $row['_type'] : '';
        if ($t === 'post_title') {
            $elements[] = array(
                'type' => 'post_title',
                'textBefore' => isset($row['text_before']) ? $row['text_before'] : '',
                'textAfter'  => isset($row['text_after']) ? $row['text_after'] : '',
                'tag'        => isset($row['tag']) ? $row['tag'] : '',
                'textColor'  => isset($row['text_color']) ? $row['text_color'] : '',
                'textColorCustom' => isset($row['text_color_custom']) ? $row['text_color_custom'] : '',
            );
        } elseif ($t === 'featured_image') {
            $elements[] = array(
                'type' => 'featured_image',
                'size' => isset($row['size']) ? $row['size'] : '',
                'isBackgroundImage' => !empty($row['is_background_image']),
            );
        } elseif ($t === 'post_excerpt') {
            $elements[] = array('type' => 'post_excerpt');
        } elseif ($t === 'permalink') {
            $elements[] = array(
                'type' => 'permalink',
                'hideButtonOnMobile' => !empty($row['hide_button_on_mobile']),
                'buttonText' => isset($row['button_text']) ? $row['button_text'] : '',
                'buttonStyle' => isset($row['button_style']) ? $row['button_style'] : 'button-accent',
            );
        } elseif ($t === 'icon') {
            $elements[] = array(
                'type' => 'icon',
                'iconId' => isset($row['icon']) ? (int) $row['icon'] : 0,
                'iconColor' => isset($row['icon_color']) ? $row['icon_color'] : '',
                'iconColorCustom' => isset($row['icon_color_custom']) ? $row['icon_color_custom'] : '',
                'iconWidth' => isset($row['icon_width']) ? $row['icon_width'] : '',
                'iconHeight' => isset($row['icon_height']) ? $row['icon_height'] : '',
            );
        } elseif (strpos($t, 'custom_field_') === 0) {
            $elements[] = array(
                'type' => 'custom_field',
                'key' => isset($row['custom_field_key']) ? $row['custom_field_key'] : '',
                'fieldType' => isset($row['custom_field_type']) ? $row['custom_field_type'] : 'p',
                'wrapperClass' => isset($row['custom_field_class']) ? $row['custom_field_class'] : '',
            );
        }
    }
    $attrs['elements'] = $elements;

    $pt_row = (isset($item['post_type'][0]) && is_array($item['post_type'][0])) ? $item['post_type'][0] : array();
    $attrs['postType'] = isset($pt_row['_type']) ? $pt_row['_type'] : '';
    $attrs['source']   = isset($pt_row['source']) ? $pt_row['source'] : 'all';

    $manual = array();
    foreach ((isset($pt_row['post']) && is_array($pt_row['post'])) ? $pt_row['post'] : array() as $p) {
        if (!empty($p['id'])) {
            $manual[] = array('id' => (int) $p['id'], 'title' => get_the_title((int) $p['id']));
        }
    }
    $attrs['manualPosts'] = $manual;

    $cats = array();
    foreach ((isset($pt_row['category']) && is_array($pt_row['category'])) ? $pt_row['category'] : array() as $c) {
        if (!empty($c['id'])) {
            $term = get_term((int) $c['id']);
            $cats[] = array(
                'id'   => (int) $c['id'],
                'name' => ($term && !is_wp_error($term)) ? $term->name : ('#' . $c['id']),
            );
        }
    }
    $attrs['categoryTerms'] = $cats;

    return $attrs;
}

/**
 * Element-type → mapper registry.
 *
 * Each mapper is callable($item): array[]|null
 *   - Returns an array of parsed-block arrays (each via coptrz_block /
 *     coptrz_block_container), or null when this item type cannot be mapped
 *     (which causes the enclosing section to snapshot to Custom HTML).
 *   - Dynamic elements (layouts, global_widgets, product_compare, etc.) map to
 *     core/shortcode (or, for layouts/global_widgets/post_grid, their dedicated
 *     coptrz/* block) so they stay live/editable.
 *   - Elements with no native block equivalent and no shortcode form (tabs,
 *     accordion) are intentionally absent → section snapshots as HTML.
 *
 * Filterable via 'coptrz_block_item_mappers' so additional mappings can be added
 * from the element→block guide without editing this file.
 *
 * @return array<string,callable>
 */
function coptrz_block_item_mappers()
{
    static $map = null;
    if ($map !== null) {
        return apply_filters('coptrz_block_item_mappers', $map);
    }

    /* ------------------------------------------------------------------ */
    /*  Leaf mappers                                                        */
    /* ------------------------------------------------------------------ */

    // Raw HTML → core/html (lossless).
    $map['custom_html'] = function ($item) {
        $html = isset($item['custom_html']) ? (string) $item['custom_html'] : '';
        return $html === '' ? null : array(coptrz_block('core/html', array(), $html));
    };

    // Shortcode element → core/shortcode (stays dynamic).
    $map['shortcode'] = function ($item) {
        $sc = isset($item['shortcode']) ? trim((string) $item['shortcode']) : '';
        return $sc === '' ? null : array(coptrz_block('core/shortcode', array(), $sc));
    };

    // Heading → core/heading.
    $map['heading'] = function ($item) {
        $tag   = strtolower(isset($item['tag']) ? (string) $item['tag'] : 'h2');
        $level = (int) filter_var($tag, FILTER_SANITIZE_NUMBER_INT) ?: 2;
        $text  = isset($item['heading']) ? (string) $item['heading'] : '';
        if ($text === '') {
            return null;
        }
        $extra = array();
        if (!empty($item['text_align'])) {
            $extra[] = $item['text_align'];
        }
        if (!empty($item['size'])) {
            $extra[] = $item['size'];
        }
        if (!empty($item['text_color'])) {
            $extra[] = $item['text_color'];
        }
        $attrs     = array('level' => $level);
        $cls       = trim('wp-block-heading ' . implode(' ', $extra));
        if ($extra) {
            $attrs['className'] = implode(' ', $extra);
        }
        $html = '<h' . $level . ' class="' . esc_attr($cls) . '">' . wp_kses_post($text) . '</h' . $level . '>';
        return array(coptrz_block('core/heading', $attrs, $html));
    };

    // Description → core/paragraph.
    $map['description'] = function ($item) {
        $text = isset($item['description']) ? (string) $item['description'] : '';
        if ($text === '') {
            return null;
        }
        $classes = array('description-box');
        if (!empty($item['description_alignment'])) {
            $classes[] = $item['description_alignment'];
        }
        if (!empty($item['description_size'])) {
            $classes[] = $item['description_size'];
        }
        $style = !empty($item['description_width'])
            ? ' style="max-width:' . esc_attr($item['description_width']) . '"'
            : '';
        $cls   = implode(' ', $classes);
        $attrs = array('className' => $cls);
        $html  = '<p class="' . esc_attr($cls) . '"' . $style . '>' . wp_kses_post($text) . '</p>';
        return array(coptrz_block('core/paragraph', $attrs, $html));
    };

    // Image → core/image.
    $map['image'] = function ($item) {
        $att_id = (int) (isset($item['image']) ? $item['image'] : 0);
        if (!$att_id) {
            return null;
        }
        $size = isset($item['size']) && $item['size'] !== '' ? (string) $item['size'] : 'large';
        $src  = wp_get_attachment_image_src($att_id, $size);
        if (!$src) {
            return null;
        }
        $url = (string) $src[0];
        $alt = (string) get_post_meta($att_id, '_wp_attachment_image_alt', true);

        $extra = array();
        if (!empty($item['rounded_corners'])) {
            $extra[] = 'rounded-corner';
        }
        if (!empty($item['is_background_image'])) {
            $extra[] = 'background-image';
        }
        $attrs    = array('id' => $att_id, 'sizeSlug' => $size);
        if ($extra) {
            $attrs['className'] = implode(' ', $extra);
        }
        $fig_cls  = trim('wp-block-image size-' . sanitize_html_class($size) . ' ' . implode(' ', $extra));
        $img_sty  = '';
        if (!empty($item['custom_size'])) {
            $s = array();
            if (!empty($item['image_height'])) {
                $s[] = '--height:' . esc_attr($item['image_height']);
            }
            if (!empty($item['image_width'])) {
                $s[] = '--width:' . esc_attr($item['image_width']);
            }
            if ($s) {
                $img_sty = ' style="' . implode(';', $s) . '"';
            }
        }
        $html = '<figure class="' . esc_attr($fig_cls) . '">'
            . '<img src="' . esc_url($url) . '" alt="' . esc_attr($alt) . '"'
            . ' class="wp-image-' . $att_id . '"' . $img_sty . '/>'
            . '</figure>';
        return array(coptrz_block('core/image', $attrs, $html));
    };

    /* ------------------------------------------------------------------ */
    /*  Dynamic elements → core/shortcode (stay live after conversion)    */
    /* ------------------------------------------------------------------ */

    // [layouts id='N'] — reusable layout post; stays dynamic.
    $map['layouts'] = function ($item) {
        $layouts = isset($item['layouts']) && is_array($item['layouts']) ? $item['layouts'] : array();
        $blocks  = array();
        foreach ($layouts as $layout) {
            $id = isset($layout['id']) ? (int) $layout['id'] : 0;
            if ($id) {
                $blocks[] = coptrz_block('coptrz/layouts', array(
                    'layoutId'    => $id,
                    'layoutTitle' => get_the_title($id),
                ));
            }
        }
        return empty($blocks) ? null : $blocks;
    };

    // Global widgets — each sub-widget maps to its registered shortcode.
    $map['global_widgets'] = function ($item) {
        $widgets  = isset($item['global_widgets']) && is_array($item['global_widgets']) ? $item['global_widgets'] : array();
        $registry = function_exists('coptrz_global_widgets') ? coptrz_global_widgets() : array();
        $blocks   = array();
        foreach ($widgets as $w) {
            $type = isset($w['_type']) ? $w['_type'] : '';
            if (!isset($registry[$type])) {
                continue; // unknown/unregistered widget (e.g. the dji_*/parrot_* slugs); skip silently
            }
            $attrs = array('widget' => $type);
            if ($type === 'case_study_slider' && isset($w['style']) && $w['style'] !== '') {
                $attrs['style'] = (string) $w['style'];
            }
            $blocks[] = coptrz_block('coptrz/global-widget', $attrs);
        }
        return empty($blocks) ? null : $blocks;
    };

    // Product compare → coptrz/product-compare (native editor equivalent of
    // [product_compare id='N']; rendered by coptrz_render_product_compare_block()
    // in includes/legacy-blocks.php).
    $map['product_compare'] = function ($item) {
        $cp = isset($item['compareproducts']) && is_array($item['compareproducts']) ? $item['compareproducts'] : array();
        $id = !empty($cp[0]['id']) ? (int) $cp[0]['id'] : 0;
        if (!$id) {
            return null;
        }
        return array(coptrz_block('coptrz/product-compare', array(
            'compareId'    => $id,
            'compareTitle' => (string) get_the_title($id),
        )));
    };

    // Post Grid → coptrz/post-grid (post type/filter, box styles, and an
    // ordered element list — see coptrz_post_grid_legacy_item_to_attrs() above
    // and coptrz_render_post_grid_block() in functions.php for the render path).
    $map['post_grid'] = function ($item) {
        if (empty($item['post_type'][0]['_type'])) {
            return null; // no post type chosen — nothing meaningful to render.
        }
        if (!function_exists('coptrz_post_grid_legacy_item_to_attrs')) {
            return null;
        }
        return array(coptrz_block('coptrz/post-grid', coptrz_post_grid_legacy_item_to_attrs($item)));
    };

    /* ------------------------------------------------------------------ */
    /*  Legacy wrapper mappers — item types with no first-class native      */
    /*  block equivalent. Each hands the whole Carbon item back to a        */
    /*  render_block filter in includes/legacy-blocks.php, which calls the  */
    /*  same function ___sections() (modules.php) already uses for that     */
    /*  item type, so output is byte-identical. See coptrz_json_safe_array()*/
    /*  below — the item is sanitised before being stored so an invalid-UTF8*/
    /*  value can't silently drop the whole block attribute.                */
    /* ------------------------------------------------------------------ */

    // Gallery → coptrz/gallery.
    $map['gallery'] = function ($item) {
        if (empty($item['gallery'])) {
            return null;
        }
        $legacy = coptrz_json_safe_array($item);
        if ($legacy === null) {
            return null;
        }
        $style = isset($item['gallery_style']) ? (string) $item['gallery_style'] : '';
        $count = is_array($item['gallery']) ? count($item['gallery']) : 0;
        return array(coptrz_block('coptrz/gallery', array(
            'legacy'  => $legacy,
            'summary' => $count . ' image(s)' . ($style !== '' ? " ({$style})" : ''),
        )));
    };

    // Product Slider → coptrz/product-slider. Stores SOURCE FIELDS, not a
    // frozen query — see coptrz_render_product_slider_block() in
    // includes/legacy-blocks.php for why (the `main_query` source depends on
    // the live request).
    $map['product_slider'] = function ($item) {
        $source_type = isset($item['source_type']) ? (string) $item['source_type'] : '';
        $category_ids = array();
        if (!empty($item['source']) && is_array($item['source'])) {
            foreach ($item['source'] as $cat) {
                if (!empty($cat['id'])) {
                    $category_ids[] = (int) $cat['id'];
                }
            }
        }
        $brand_ids = array();
        if (!empty($item['brand']) && is_array($item['brand'])) {
            foreach ($item['brand'] as $brand) {
                if (!empty($brand['id'])) {
                    $brand_ids[] = (int) $brand['id'];
                }
            }
        }
        $product_ids = array();
        if (!empty($item['products']) && is_array($item['products'])) {
            foreach ($item['products'] as $product) {
                if (!empty($product['id'])) {
                    $product_ids[] = (int) $product['id'];
                }
            }
        }
        return array(coptrz_block('coptrz/product-slider', array(
            'sourceType'  => $source_type,
            'categoryIds' => $category_ids,
            'brandIds'    => $brand_ids,
            'productIds'  => $product_ids,
            'numberposts' => isset($item['numberposts']) ? (string) $item['numberposts'] : '',
            'heading'     => isset($item['heading']) ? (string) $item['heading'] : '',
            'buttonText'  => isset($item['button_text']) ? (string) $item['button_text'] : '',
            'buttonUrl'   => isset($item['button_url']) ? (string) $item['button_url'] : '',
        )));
    };

    // Tabs (Bootstrap nav-tabs) → coptrz/tabs-legacy. Distinct from the native
    // dd/tabs block — this is a frozen wrapper, not a conversion onto it.
    $map['tabs'] = function ($item) {
        if (empty($item['tabs']) || !is_array($item['tabs'])) {
            return null;
        }
        $legacy = coptrz_json_safe_array($item);
        if ($legacy === null) {
            return null;
        }
        return array(coptrz_block('coptrz/tabs-legacy', array('legacy' => $legacy)));
    };

    // Accordion → coptrz/accordion-legacy. Distinct from core/details — this is
    // a frozen wrapper that keeps the FAQs-by-selection / FAQs-by-category
    // dynamic sourcing intact.
    $map['accordion'] = function ($item) {
        $legacy = coptrz_json_safe_array($item);
        if ($legacy === null) {
            return null;
        }
        return array(coptrz_block('coptrz/accordion-legacy', array('legacy' => $legacy)));
    };

    // Drone Servicing Grid → coptrz/drone-servicing-grid. The nested
    // servicing_drones[].service_features[] rows MUST keep their `_type` key
    // (drone/battery/controller/payload) intact — __drone_servicing()
    // dispatches on it. coptrz_json_safe_array() is key-preserving.
    $map['drone_servicing_grid'] = function ($item) {
        if (empty($item['servicing_drones'])) {
            return null;
        }
        $legacy = coptrz_json_safe_array($item);
        if ($legacy === null) {
            return null;
        }
        return array(coptrz_block('coptrz/drone-servicing-grid', array('legacy' => $legacy)));
    };

    // Events Widget → coptrz/events-widget.
    $map['events_widget'] = function ($item) {
        if (empty($item['events_widget'])) {
            return null;
        }
        $legacy = coptrz_json_safe_array($item);
        if ($legacy === null) {
            return null;
        }
        return array(coptrz_block('coptrz/events-widget', array('legacy' => $legacy)));
    };

    // Product → coptrz/product ([product_add_to_cart id='N' is_training='…']).
    $map['product'] = function ($item) {
        $pid = !empty($item['product'][0]['id']) ? (int) $item['product'][0]['id'] : 0;
        if (!$pid) {
            return null; // legacy strips empty [product_add_to_cart id=''] tags.
        }
        return array(coptrz_block('coptrz/product', array(
            'productId'   => $pid,
            'productName' => (string) get_the_title($pid),
            'isTraining'  => !empty($item['is_training_template']),
        )));
    };

    // Global Post Box Selection → a core/group row (legacy row classes) with
    // one coptrz/global-post-box child per selected post, so individual boxes
    // stay deletable/reorderable in the editor. Column-width classes are
    // resolved HERE (convert time), including the legacy 3-posts/col-md-6
    // special case (modules.php ~L1085), which depends on the selection's
    // post COUNT — not something a single box can know on its own at render time.
    $map['global_post_box_selection'] = function ($item) {
        $source               = isset($item['source']) ? (string) $item['source'] : '';
        $column_width         = isset($item['column_width']) ? (string) $item['column_width'] : '';
        $column_width_tablet  = isset($item['column_width_tablet']) ? (string) $item['column_width_tablet'] : '';
        $column_width_mobile  = isset($item['column_width_mobile']) ? (string) $item['column_width_mobile'] : '';

        $manual_posts = isset($item['post']) && is_array($item['post']) ? $item['post'] : array();

        // Mirrors modules.php ~L1081-1093: the special case tests count() of
        // the MANUAL selection array, evaluated before a category source
        // reassigns the post list — so for a category source this special
        // case is (as in legacy) effectively never triggered.
        $col_classes = array();
        if ($column_width) {
            $col_classes[] = $column_width;
        }
        if ($column_width_tablet) {
            if (count($manual_posts) == 3 && $column_width_tablet == 'col-md-6') {
                $col_classes[] = 'col-md-12';
            } else {
                $col_classes[] = $column_width_tablet;
            }
        }
        if ($column_width_mobile) {
            $col_classes[] = $column_width_mobile;
        }
        $col_class_str = implode(' ', $col_classes);

        if ($source === 'category') {
            $category_ids = array();
            if (!empty($item['category']) && is_array($item['category'])) {
                foreach ($item['category'] as $cat) {
                    if (!empty($cat['id'])) {
                        $category_ids[] = (int) $cat['id'];
                    }
                }
            }
            $posts = get_posts(array(
                'post_type'   => 'globalpostboxes',
                'post_status' => 'publish',
                'fields'      => 'ids',
                'exclude'     => array(coptrz_converter_current_post_id()),
                'tax_query'   => array(
                    array(
                        'taxonomy' => 'global_post_boxes_category',
                        'field'    => 'term_id',
                        'terms'    => $category_ids,
                    ),
                ),
            ));
        } else {
            $posts = array();
            foreach ($manual_posts as $p) {
                if (!empty($p['id'])) {
                    $posts[] = (int) $p['id'];
                }
            }
        }

        if (empty($posts)) {
            return null;
        }

        $box_blocks = array();
        foreach ($posts as $pid) {
            $box_blocks[] = coptrz_block('coptrz/global-post-box', array(
                'postId'     => (int) $pid,
                'postTitle'  => (string) get_the_title($pid),
                'colClasses' => $col_class_str,
            ));
        }

        $group_attrs = array(
            'className'   => 'row g-4 justify-content-center same-image-height row-global-post',
            'ddCustomCSS' => '--image-padding: 35%;',
            'layout'      => array('type' => 'default'),
        );
        $wrap_open  = '<div class="wp-block-group row g-4 justify-content-center same-image-height row-global-post">';
        return array(coptrz_block_container('core/group', $group_attrs, $wrap_open, '</div>', $box_blocks));
    };

    /* ------------------------------------------------------------------ */
    /*  Composite: buttons                                                 */
    /* ------------------------------------------------------------------ */

    // Buttons → core/buttons (wrapper) + core/button (per item).
    // Popup buttons become core/button blocks with a ddPopupId attribute,
    // handled at render time by dd_button_popup_render() (functions.php) —
    // the same "Open Popup" mechanism the button block's editor extension uses
    // (assets/js/extend-button-popup.js). Post-reference types (page/product/
    // guides/casestudies/post/industries) all resolve button_url to a
    // permalink and are hidden when their target isn't published, mirroring
    // __button() (includes/elements.php).
    $map['buttons'] = function ($item) {
        $raw_btns = isset($item['buttons']) && is_array($item['buttons']) ? $item['buttons'] : array();
        if (empty($raw_btns)) {
            return null;
        }
        $btn_blocks = array();
        foreach ($raw_btns as $btn) {
            $type  = isset($btn['button_type']) ? (string) $btn['button_type'] : '';
            $text  = isset($btn['button_text']) ? (string) $btn['button_text'] : '';
            $style = isset($btn['button_style']) ? (string) $btn['button_style'] : '';
            // __button() renders nothing without a type or text (elements.php:301,349).
            if ($type === '' || $text === '') {
                continue;
            }

            $blank     = isset($btn['button_target']) && strpos((string) $btn['button_target'], '_blank') !== false;
            $btn_attrs = array();
            if ($style !== '') {
                $btn_attrs['className'] = $style;
            }
            if ($blank) {
                $btn_attrs['linkTarget'] = '_blank';
                $btn_attrs['rel']        = 'noreferrer noopener';
            }

            if ($type === 'popups') {
                $popup_id = (int) apply_filters(
                    'wpml_object_id',
                    isset($btn['button_url']) ? $btn['button_url'] : 0,
                    'post',
                    true
                );
                if (!$popup_id) {
                    continue;
                }
                $btn_attrs['ddPopupId'] = $popup_id;
                $link_cls = trim('wp-block-button__link wp-element-button ' . $style);
                $btn_html = '<div class="wp-block-button">'
                    . '<a class="' . esc_attr($link_cls) . '">'
                    . wp_kses_post($text) . '</a></div>';
                $btn_blocks[] = coptrz_block('core/button', $btn_attrs, $btn_html);
                continue;
            }

            if ($type === 'custom') {
                $url = isset($btn['button_url_custom']) ? (string) $btn['button_url_custom'] : '';
            } else {
                // page/product/guides/casestudies/post/industries all store a
                // post ID in button_url and resolve to its permalink.
                $pid = (int) (isset($btn['button_url']) ? $btn['button_url'] : 0);
                if (!$pid || get_post_status($pid) !== 'publish') {
                    continue; // __button() hides links to non-published targets
                }
                $url = (string) get_permalink($pid);
                // An `events` target with an external _event_url always opens
                // in a new tab (elements.php:315-325).
                if (get_post_type($pid) === 'events' && get_post_meta($pid, '_event_url', true)) {
                    $blank = true;
                    $btn_attrs['linkTarget'] = '_blank';
                    $btn_attrs['rel']        = 'noreferrer noopener';
                }
            }
            if ($url === '') {
                continue;
            }

            $btn_attrs['url'] = $url;
            $link_cls  = trim('wp-block-button__link wp-element-button ' . $style);
            $link_attr = ' href="' . esc_url($url) . '"'
                . ($blank ? ' target="_blank" rel="noreferrer noopener"' : '');
            $btn_html  = '<div class="wp-block-button">'
                . '<a class="' . esc_attr($link_cls) . '"' . $link_attr . '>'
                . wp_kses_post($text) . '</a></div>';
            $btn_blocks[] = coptrz_block('core/button', $btn_attrs, $btn_html);
        }
        if (empty($btn_blocks)) {
            return null;
        }
        $align     = isset($item['buttons_alignment']) ? (string) $item['buttons_alignment'] : '';
        $attrs     = $align ? array('className' => $align) : array();
        $wrap_cls  = trim('wp-block-buttons ' . $align);
        return array(coptrz_block_container(
            'core/buttons',
            $attrs,
            '<div class="' . esc_attr($wrap_cls) . '">',
            '</div>',
            $btn_blocks
        ));
    };

    /* ------------------------------------------------------------------ */
    /*  Composite: columns (recursive through the item registry)          */
    /* ------------------------------------------------------------------ */

    // Columns → core/columns + core/column[]. Each column's items recurse through
    // this same registry; if any sub-item can't be mapped the whole section
    // falls back to a Custom HTML snapshot.
    $map['columns'] = function ($item) {
        $raw_cols = isset($item['columns']) && is_array($item['columns']) ? $item['columns'] : array();
        if (empty($raw_cols)) {
            return null;
        }

        // ____columns_modules() (modules.php:1811) has no block equivalent for
        // its swiper markup — a slider row would otherwise silently convert
        // into a plain static column grid with the carousel gone.
        if (!empty($item['is_slider'])) {
            return null;
        }

        $mappers   = coptrz_block_item_mappers(); // safe: static already populated
        $col_blocks = array();

        $individual_column_settings = !empty($item['individual_column_settings']);
        $shared_styles = (!$individual_column_settings && !empty($item['column_styles']) && is_array($item['column_styles']))
            ? $item['column_styles']
            : array();
        $mobile_styling = isset($item['mobile_styling']) ? (string) $item['mobile_styling'] : '';

        foreach ($raw_cols as $col) {
            $col_items = isset($col['items']) && is_array($col['items']) ? $col['items'] : array();
            $inner     = array();
            foreach ($col_items as $sub) {
                $sub_type = isset($sub['_type']) ? $sub['_type'] : '';
                if (!isset($mappers[$sub_type])) {
                    return null; // unmapped sub-item → whole section snapshots
                }
                $result = call_user_func($mappers[$sub_type], $sub);
                if ($result === null) {
                    return null;
                }
                foreach ((array) $result as $b) {
                    $inner[] = $b;
                }
            }

            $col_data = coptrz_column_wrapper_data($col, $shared_styles, $individual_column_settings, $mobile_styling, !empty($item['same_image_height']), isset($item['image_fit']) ? $item['image_fit'] : '', isset($item['image_padding']) ? $item['image_padding'] : '');

            $col_class_attr = implode(' ', $col_data['column_classes']);
            $col_attrs      = $col_class_attr !== '' ? array('className' => $col_class_attr) : array();
            $col_wrap_cls   = trim('wp-block-column ' . $col_class_attr);

            $has_holder_styling = !empty($col_data['holder_classes']) || !empty($col_data['holder_css']);
            if ($has_holder_styling) {
                $holder_class_attr = implode(' ', $col_data['holder_classes']);
                $holder_attrs = array(
                    'className' => $holder_class_attr,
                    'layout'    => array('type' => 'default'),
                );
                if (!empty($col_data['holder_css'])) {
                    $holder_attrs['ddCustomCSS'] = implode('; ', $col_data['holder_css']) . ';';
                }
                $column_id = isset($col['column_id']) ? trim((string) $col['column_id']) : '';
                if ($column_id !== '') {
                    $holder_attrs['anchor'] = $column_id;
                }
                $holder_div_class = trim('wp-block-group ' . $holder_class_attr);
                $inner = array(coptrz_block_container(
                    'core/group',
                    $holder_attrs,
                    '<div class="' . esc_attr($holder_div_class) . '">',
                    '</div>',
                    $inner
                ));
            }

            $col_blocks[] = coptrz_block_container(
                'core/column',
                $col_attrs,
                '<div class="' . esc_attr($col_wrap_cls) . '">',
                '</div>',
                $inner
            );
        }

        if (empty($col_blocks)) {
            return null;
        }

        // Row-level wrapper classes, matching the non-slider $row_class build
        // at modules.php:2021-2050 (minus the raw 'row'/'g-4'/'g-xs-10px'
        // bootstrap grid/gutter classes, which assume a `.row` flex context
        // that core/columns' own layout doesn't provide).
        $row_classes = array();
        foreach (array('align_items', 'justify_content', 'horizontal_spacing', 'vertical_spacing') as $k) {
            if (!empty($item[$k])) {
                $row_classes[] = (string) $item[$k];
            }
        }
        $align      = implode(' ', $row_classes);
        $cols_attrs = $align !== '' ? array('className' => $align) : array();
        $wrap_cls   = trim('wp-block-columns ' . $align);
        return array(coptrz_block_container(
            'core/columns',
            $cols_attrs,
            '<div class="' . esc_attr($wrap_cls) . '">',
            '</div>',
            $col_blocks
        ));
    };

    return apply_filters('coptrz_block_item_mappers', $map);
}

/**
 * The post currently being converted, for mappers that need it (e.g.
 * `global_post_box_selection`'s category query excludes the current post, same
 * as modules.php's `'exclude' => get_the_ID()`). get_the_ID()/the main loop
 * aren't reliably available during conversion (admin AJAX / bulk runner), so
 * coptrz_section_to_blocks() sets this explicitly before invoking any mapper.
 * A plain get/set static rather than a mapper-signature change, since mappers
 * are called both directly and recursively (the `columns` mapper) with a
 * single-argument `($item)` contract shared with the `coptrz_block_item_mappers`
 * filter's extensibility.
 */
function coptrz_converter_current_post_id($set = null)
{
    static $post_id = 0;
    if ($set !== null) {
        $post_id = (int) $set;
    }
    return $post_id;
}

/**
 * Convert one section to block markup.
 *  - Every item natively mapped AND the section's styling is fully representable
 *    → its blocks wrapped in a section > .container core/group pair.
 *  - Otherwise → a single Custom HTML snapshot of the whole section (identical to
 *    the HTML converter; keeps shortcodes/[layouts] literal & dynamic).
 *
 * @return array{0:string,1:bool,2:string} [markup, was_native, snapshot_reason]
 */
function coptrz_section_to_blocks($section, $post_id, $field, $key)
{
    coptrz_converter_current_post_id($post_id);
    $items   = (isset($section['section_items']) && is_array($section['section_items'])) ? $section['section_items'] : array();
    $mappers = coptrz_block_item_mappers();

    $blocks     = array();
    $all_native = !empty($items);
    foreach ($items as $item) {
        $type = isset($item['_type']) ? $item['_type'] : '';
        if (!isset($mappers[$type])) {
            $all_native = false;
            break;
        }
        // Mappers return array[] (list of parsed-block arrays) or null.
        $result = call_user_func($mappers[$type], $item);
        if ($result === null) {
            $all_native = false;
            break;
        }
        foreach ((array) $result as $b) {
            $blocks[] = $b;
        }
    }

    $wrapper_data = coptrz_section_wrapper_data($section);
    $title        = isset($section['title']) ? (string) $section['title'] : '';

    if ($all_native && !empty($blocks) && !$wrapper_data['unmappable']) {
        $group = coptrz_block_group($wrapper_data, $blocks, $title);
        return array(serialize_blocks(array($group)), true, '');
    }

    // Fallback: freeze the whole section as a Custom HTML block. Reached either
    // because an item type has no block mapper, or because the section has a
    // background video/YouTube or an image overlay — the only section_styles
    // left that emit real markup (__background()/__image()) rather than a class
    // or a plain CSS declaration coptrz_block_group() can carry on ddCustomCSS.
    $reason = !$all_native
        ? 'an item type has no native block mapper'
        : 'the section has a background video/YouTube or image overlay, which has no native block equivalent';

    $html = trim(___sections($field, $post_id, $key));
    $html = preg_replace('/\[product_add_to_cart\s+id=([\'"])\1[^\]]*\]/', '', $html);
    if ($html === '') {
        return array('', false, $reason);
    }
    $html_attrs = ($title !== '') ? array('metadata' => array('name' => $title)) : array();
    return array(serialize_blocks(array(coptrz_block('core/html', $html_attrs, $html))), false, $reason);
}

/**
 * Convert one post's sections into native blocks (mode "blocks"). Emits
 * per-element blocks (mirrors the older HTML-repeater path,
 * coptrz_convert_post_sections(), which is kept only for products still on
 * `mode = 'html'`). Sets the same converted flag (so rendering routes
 * identically) plus `_coptrz_sections_mode` = 'blocks' for reporting,
 * preserves `_sections`, and switches `page` posts to the Gutenberg template.
 *
 * Products: refused if post_content is non-empty (see the guard below) since
 * it isn't rendered anywhere today and conversion would silently publish it.
 * Once accepted, `sections` and `sections_after_main` are joined with a
 * `coptrz/section-split` marker between them so coptrz_product_content_split()
 * can hand each half back to its own template slot (before/after the buy box).
 *
 * @param int  $post_id
 * @param bool $dry_run
 * @return array
 */
function coptrz_convert_post_sections_to_blocks($post_id, $dry_run = false)
{
    $src = get_post($post_id);
    $report = array(
        'post_id'  => (int) $post_id,
        'title'    => $src ? $src->post_title : '',
        'type'     => $src ? $src->post_type : '',
        'mode'     => 'blocks',
        'counts'   => array(),
        'native'   => array(),
        'snapshot' => array(),
        'target'   => 'Native Gutenberg blocks',
        'warnings' => array(),
        'skipped'  => false,
    );

    if (!$src) {
        $report['warnings'][] = 'Post not found.';
        return $report;
    }
    if (!$dry_run && coptrz_sections_is_converted($post_id)) {
        $report['skipped'] = true;
        $report['warnings'][] = 'Already converted — skipped to avoid duplicating content.';
        return $report;
    }
    // Products have no rendered use of post_content today (all WooCommerce tabs
    // are disabled — see 'my_remove_all_product_tabs' in woocommerce.php), so a
    // product carrying a leftover description in post_content would silently gain
    // a visible one the moment sections are appended after it. Refuse rather than
    // the generic soft warning below, so that content is dealt with deliberately
    // first (cleared, or moved into a section) rather than published as a
    // byproduct of conversion.
    if ($src->post_type === 'product' && trim((string) $src->post_content) !== '') {
        $report['skipped'] = true;
        $report['warnings'][] = 'This product has a non-empty description (post_content) that is not rendered anywhere on the front end. Converting would publish it, appended before/after the frozen sections. Clear or relocate it first, then convert.';
        return $report;
    }

    global $post;
    $prev_post = $post;
    $post = $src;
    setup_postdata($post);

    $blocks_by_field = array();

    foreach (coptrz_section_source_fields() as $field) {
        $sections = get__post_meta_by_id($post_id, $field);
        if (empty($sections) || !is_array($sections)) {
            continue;
        }
        $chunks = array();
        $native = 0;
        $snap   = 0;
        foreach ($sections as $key => $section) {
            if (!empty($section['disable_section'])) {
                continue;
            }
            list($markup, $was_native, $reason) = coptrz_section_to_blocks($section, $post_id, $field, $key);
            if ($markup === '') {
                continue;
            }
            $chunks[] = $markup;
            if ($was_native) {
                $native++;
            } else {
                $snap++;
                $label = !empty($section['title']) ? $section['title'] : ('Section ' . ($key + 1));
                $report['warnings'][] = "\"{$label}\" snapshotted to Custom HTML — {$reason}.";
            }
        }
        if (empty($chunks)) {
            continue;
        }
        $report['counts'][$field]   = count($chunks);
        $report['native'][$field]   = $native;
        $report['snapshot'][$field] = $snap;
        $blocks_by_field[$field]    = implode("\n\n", $chunks);
        if ($dry_run) {
            $report['html'][$field] = $chunks;
        }
    }

    if (!empty($blocks_by_field)) {
        $existing = (string) $src->post_content;
        $is_product = ($src->post_type === 'product');
        if (!$is_product && trim($existing) !== '') {
            // Products already refused above when post_content is non-empty, so
            // $existing is always '' here for a product.
            $report['warnings'][] = 'post_content was not empty — section blocks appended after existing content.';
        }
        $new_content = $existing;

        if ($is_product) {
            // Products render two slots either side of the buy box — `sections`
            // above it, `sections_after_main` below (includes/woocommerce.php,
            // action_woocommerce_before_main_content()/after_single_product_summary()).
            // A `coptrz/section-split` marker divides post_content between the
            // two (see coptrz_product_content_split()); it's only needed when
            // there IS a "before" chunk, since the split helper's no-marker
            // fallback already treats the whole content as the "after" slot —
            // the one every product actually uses today.
            if (!empty($blocks_by_field['sections'])) {
                $new_content .= $blocks_by_field['sections'] . "\n\n" . serialize_blocks(array(coptrz_block('coptrz/section-split')));
                if (!empty($blocks_by_field['sections_after_main'])) {
                    $new_content .= "\n\n" . $blocks_by_field['sections_after_main'];
                }
            } elseif (!empty($blocks_by_field['sections_after_main'])) {
                $new_content .= $blocks_by_field['sections_after_main'];
            }
        } else {
            foreach (coptrz_section_source_fields() as $field) {
                if (!empty($blocks_by_field[$field])) {
                    $new_content .= ($new_content !== '' ? "\n\n" : '') . $blocks_by_field[$field];
                }
            }
        }
        if (!$dry_run) {
            // Backups, so a later revert can restore byte-identical content /
            // template instead of guessing. Written before the actual update so
            // COPTRZ_CONVERTED_BLOCKS captures the exact suffix this run added.
            update_post_meta($post_id, COPTRZ_PRE_CONVERT_CONTENT, $existing);
            update_post_meta($post_id, COPTRZ_CONVERTED_BLOCKS, substr($new_content, strlen($existing)));
            wp_update_post(array('ID' => $post_id, 'post_content' => $new_content));
        }
        if ($src->post_type === 'page') {
            $report['template'] = 'templates/page-gutenberg.php';
            if (!$dry_run) {
                update_post_meta($post_id, COPTRZ_PRE_CONVERT_TEMPLATE, (string) get_post_meta($post_id, '_wp_page_template', true));
                update_post_meta($post_id, '_wp_page_template', 'templates/page-gutenberg.php');
            }
        }
    }

    wp_reset_postdata();
    $post = $prev_post;

    if (!$dry_run && !empty($report['counts'])) {
        update_post_meta($post_id, COPTRZ_SECTIONS_CONVERTED_FLAG, 'yes');
        update_post_meta($post_id, '_coptrz_sections_mode', 'blocks');
    }
    if (empty($report['counts'])) {
        $report['warnings'][] = 'No active sections found to convert.';
    }

    return $report;
}

/**
 * Revert a converted post back to the legacy "sections" page-builder.
 *
 *  - product, mode 'html'   -> post_content/template were never touched by
 *    conversion; just clears the sections_html / sections_after_main_html
 *    repeater.
 *  - everything else (incl. product, mode 'blocks') -> restores post_content
 *    from the COPTRZ_PRE_CONVERT_CONTENT backup when available (this also
 *    removes the coptrz/section-split marker, since it was appended as part
 *    of the same write). Posts converted before this backup existed have
 *    none: this regenerates the block markup via a dry run and subtracts it as
 *    an exact suffix of the current post_content instead, so any content that
 *    predated conversion survives. If that suffix doesn't match, aborts rather
 *    than guessing. `page` also gets its pre-conversion _wp_page_template back
 *    (falling back to the Modules template when no backup exists).
 *
 * In every case, deletes the converted flag + backup meta. `_sections` /
 * `_sections_after_main` are never touched by conversion, so the legacy render
 * resumes as soon as the flag is gone (see coptrz_sections_should_route()).
 *
 * @param int  $post_id
 * @param bool $dry_run  When true, report what would change but write nothing.
 * @return array Report: warnings, skipped, and (dry-run) the restored content/template.
 */
function coptrz_revert_post_sections($post_id, $dry_run = false)
{
    $src = get_post($post_id);
    $report = array(
        'post_id'  => (int) $post_id,
        'title'    => $src ? $src->post_title : '',
        'type'     => $src ? $src->post_type : '',
        'target'   => 'Legacy sections builder',
        'counts'   => array(), // kept for shape-compatibility with the bulk results table
        'warnings' => array(),
        'skipped'  => false,
    );

    if (!$src) {
        $report['warnings'][] = 'Post not found.';
        return $report;
    }

    if (!coptrz_sections_is_converted($post_id)) {
        $report['skipped'] = true;
        $report['warnings'][] = 'Not converted — nothing to revert.';
        return $report;
    }

    // Only an HTML-mode product (never touched post_content) reverts by just
    // clearing its repeater — a block-mode product (post_content was written,
    // same as any other converted post type) falls through to the restore
    // path below, same as everything else.
    $mode = get_post_meta($post_id, '_coptrz_sections_mode', true);
    $is_html_product = ($src->post_type === 'product' && $mode !== 'blocks');

    if ($is_html_product) {
        $report['restored'] = 'HTML repeater cleared; legacy sections resume.';
        if (!$dry_run) {
            coptrz_set_post_meta($post_id, 'sections_html', array());
            coptrz_set_post_meta($post_id, 'sections_after_main_html', array());
        }
    } else {
        $existing_content = (string) $src->post_content;
        $has_backup = metadata_exists('post', $post_id, COPTRZ_PRE_CONVERT_CONTENT);

        if ($has_backup) {
            $backup_content   = (string) get_post_meta($post_id, COPTRZ_PRE_CONVERT_CONTENT, true);
            $converted_blocks = (string) get_post_meta($post_id, COPTRZ_CONVERTED_BLOCKS, true);
            if ($existing_content !== $backup_content . $converted_blocks) {
                $report['warnings'][] = 'Content was edited since conversion — restoring the pre-conversion backup anyway; review the result before republishing.';
            }
            $restored_content = $backup_content;
        } else {
            // No backup (converted before this feature shipped): regenerate the
            // exact block markup for a dry run and subtract it as a suffix. The
            // regenerated string never includes the separator joining it to
            // whatever content preceded it (that depends on whether the ORIGINAL
            // content was empty — the very thing being recovered) so try both.
            $dry = coptrz_convert_post_sections_to_blocks($post_id, true);
            $chunks = array();
            foreach (coptrz_section_source_fields() as $field) {
                if (!empty($dry['html'][$field])) {
                    $chunks[] = implode("\n\n", $dry['html'][$field]);
                }
            }
            $regenerated = implode("\n\n", $chunks);

            $restored_content = null;
            if ($regenerated !== '') {
                foreach (array("\n\n" . $regenerated, $regenerated) as $suffix) {
                    if (substr($existing_content, -strlen($suffix)) === $suffix) {
                        $restored_content = substr($existing_content, 0, strlen($existing_content) - strlen($suffix));
                        break;
                    }
                }
            }

            if ($restored_content === null) {
                $report['skipped'] = true;
                $report['warnings'][] = 'No conversion backup found, and the regenerated block markup does not match the end of post_content — refusing to guess. Revert manually or restore from a post revision.';
                return $report;
            }
        }

        $report['restored'] = $restored_content;
        if (!$dry_run) {
            wp_update_post(array('ID' => $post_id, 'post_content' => $restored_content));
        }

        if ($src->post_type === 'page') {
            $had_template_backup = metadata_exists('post', $post_id, COPTRZ_PRE_CONVERT_TEMPLATE);
            $target_template = $had_template_backup
                ? (string) get_post_meta($post_id, COPTRZ_PRE_CONVERT_TEMPLATE, true)
                : 'templates/page-modules.php';
            $report['template'] = ($target_template !== '') ? $target_template : 'default';
            if (!$dry_run) {
                update_post_meta($post_id, '_wp_page_template', $target_template);
            }
        }
    }

    if (!$dry_run) {
        delete_post_meta($post_id, COPTRZ_SECTIONS_CONVERTED_FLAG);
        delete_post_meta($post_id, '_coptrz_sections_mode');
        delete_post_meta($post_id, COPTRZ_PRE_CONVERT_CONTENT);
        delete_post_meta($post_id, COPTRZ_PRE_CONVERT_TEMPLATE);
        delete_post_meta($post_id, COPTRZ_CONVERTED_BLOCKS);
        delete_post_meta($post_id, COPTRZ_SERVE_LEGACY_PUBLIC);
    }

    return $report;
}

/* ========================================================================= */
/*  Admin UI — per-post meta box                                              */
/* ========================================================================= */

add_action('add_meta_boxes', function ($post_type) {
    if (!in_array($post_type, coptrz_section_post_types(), true)) {
        return;
    }
    add_meta_box(
        'coptrz-section-converter',
        __('Convert Sections', 'coptrz-theme'),
        'coptrz_render_section_converter_box',
        $post_type,
        'side',
        'high'
    );
});

/**
 * Per-post convert box: dry-run preview + convert, via admin-ajax. Every post
 * type — products included, now that they have a block editor too, see
 * coptrz_enable_product_block_editor() in includes/woocommerce.php — converts
 * to native blocks via coptrz_convert_post_sections_to_blocks().
 *
 * @param WP_Post $post
 * @return void
 */
function coptrz_render_section_converter_box($post)
{
    $converted    = coptrz_sections_is_converted($post->ID);
    $nonce        = wp_create_nonce('coptrz_convert_sections');
    $revert_nonce = wp_create_nonce('coptrz_revert_sections');
    $mode         = get_post_meta($post->ID, '_coptrz_sections_mode', true);
    ?>
    <div class="coptrz-conv" data-post="<?php echo (int) $post->ID; ?>" data-nonce="<?php echo esc_attr($nonce); ?>" data-revert-nonce="<?php echo esc_attr($revert_nonce); ?>">
        <?php if ($converted) : ?>
            <p style="color:#1a7f37;font-weight:600;margin-top:0;">✓ <?php esc_html_e('Already converted.', 'coptrz-theme'); ?></p>
            <p class="description">
                <?php if ($mode === 'html') : ?>
                    <?php esc_html_e('The original section data is preserved. Edit the content via the "Page Sections (HTML)" repeater below.', 'coptrz-theme'); ?>
                <?php else : ?>
                    <?php esc_html_e('The original section data is preserved. Edit the content via the block editor above.', 'coptrz-theme'); ?>
                <?php endif; ?>
            </p>

            <p style="margin:10px 0 6px;">
                <a href="<?php echo esc_url(add_query_arg('coptrz_preview', 'original', get_permalink($post->ID))); ?>" class="button" target="_blank" rel="noopener">
                    <?php esc_html_e('Preview original (unconverted)', 'coptrz-theme'); ?>
                </a>
            </p>

            <?php wp_nonce_field('coptrz_serve_legacy_public', 'coptrz_serve_legacy_public_nonce'); ?>
            <p style="margin:0 0 10px;">
                <label>
                    <input type="checkbox" name="coptrz_serve_legacy_public" value="1" <?php checked(get_post_meta($post->ID, COPTRZ_SERVE_LEGACY_PUBLIC, true), 'yes'); ?> />
                    <?php esc_html_e('Show original sections to logged-out visitors', 'coptrz-theme'); ?>
                </label>
                <br /><span class="description"><?php esc_html_e('Logged-in users always see the converted blocks. Save the post to apply.', 'coptrz-theme'); ?></span>
            </p>

            <hr style="margin:10px 0;" />
            <p class="description" style="margin-top:0;"><?php esc_html_e('Revert restores the pre-conversion content and template, and clears the conversion.', 'coptrz-theme'); ?></p>
            <p style="margin-bottom:6px;">
                <button type="button" class="button coptrz-conv__dry-revert"><?php esc_html_e('Dry run revert', 'coptrz-theme'); ?></button>
                <button type="button" class="button coptrz-conv__revert" style="color:#b32d2e;"><?php esc_html_e('Revert', 'coptrz-theme'); ?></button>
            </p>
            <div class="coptrz-conv__out" style="font:12px/1.5 monospace;max-height:220px;overflow:auto;"></div>
        <?php else : ?>
            <p class="description" style="margin-top:0;">
                <?php esc_html_e('Freeze this post’s sections into native Gutenberg blocks (any section that can’t map natively is frozen as Custom HTML instead). Original data is kept (reversible). Products with an existing description (post_content) are refused — clear it first.', 'coptrz-theme'); ?>
            </p>
            <p style="margin-bottom:6px;">
                <button type="button" class="button coptrz-conv__dry"><?php esc_html_e('Dry run', 'coptrz-theme'); ?></button>
                <button type="button" class="button button-primary coptrz-conv__go"><?php esc_html_e('Convert', 'coptrz-theme'); ?></button>
            </p>
            <div class="coptrz-conv__out" style="font:12px/1.5 monospace;max-height:220px;overflow:auto;"></div>
        <?php endif; ?>
    </div>
    <script>
    (function () {
        var box = document.currentScript.previousElementSibling;
        if (!box || !box.classList.contains('coptrz-conv')) { return; }
        function run(action, nonce, dry) {
            var out = box.querySelector('.coptrz-conv__out');
            out.textContent = '…';
            var body = new URLSearchParams({
                action: action,
                nonce: nonce,
                post: box.dataset.post,
                dry: dry ? '1' : '0'
            });
            fetch(ajaxurl, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    out.textContent = JSON.stringify(res.data || res, null, 2);
                    if (!dry && res.success) { setTimeout(function () { location.reload(); }, 800); }
                })
                .catch(function (e) { out.textContent = 'Error: ' + e; });
        }
        box.querySelectorAll('.coptrz-conv__dry').forEach(function (b) {
            b.addEventListener('click', function () { run('coptrz_convert_sections', box.dataset.nonce, true); });
        });
        box.querySelectorAll('.coptrz-conv__go').forEach(function (b) {
            b.addEventListener('click', function () {
                if (confirm('<?php echo esc_js(__('Convert this post’s sections?', 'coptrz-theme')); ?>')) { run('coptrz_convert_sections', box.dataset.nonce, false); }
            });
        });
        box.querySelectorAll('.coptrz-conv__dry-revert').forEach(function (b) {
            b.addEventListener('click', function () { run('coptrz_revert_sections', box.dataset.revertNonce, true); });
        });
        box.querySelectorAll('.coptrz-conv__revert').forEach(function (b) {
            b.addEventListener('click', function () {
                if (confirm('<?php echo esc_js(__('Revert this post to the legacy sections builder? The converted blocks will be removed from post_content. This cannot be undone except via a post revision.', 'coptrz-theme')); ?>')) { run('coptrz_revert_sections', box.dataset.revertNonce, false); }
            });
        });
    })();
    </script>
    <?php
}

/**
 * Persist the "show original sections to logged-out visitors" checkbox from
 * the meta box above. A plain save_post handler (not routed through the meta
 * shim) since it's a single checkbox living alongside the revert controls,
 * not a Carbon-shaped field.
 */
add_action('save_post', function ($post_id) {
    if (!isset($_POST['coptrz_serve_legacy_public_nonce'])
        || !wp_verify_nonce($_POST['coptrz_serve_legacy_public_nonce'], 'coptrz_serve_legacy_public')
    ) {
        return;
    }
    if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_revision($post_id)) {
        return;
    }
    if (!current_user_can('edit_post', $post_id) || !coptrz_sections_is_converted($post_id)) {
        return;
    }
    if (!empty($_POST['coptrz_serve_legacy_public'])) {
        update_post_meta($post_id, COPTRZ_SERVE_LEGACY_PUBLIC, 'yes');
    } else {
        delete_post_meta($post_id, COPTRZ_SERVE_LEGACY_PUBLIC);
    }
});

add_action('wp_ajax_coptrz_convert_sections', function () {
    check_ajax_referer('coptrz_convert_sections', 'nonce');
    $post_id = isset($_POST['post']) ? (int) $_POST['post'] : 0;
    if (!$post_id || !current_user_can('edit_post', $post_id)) {
        wp_send_json_error('Permission denied.');
    }
    $dry = !empty($_POST['dry']) && $_POST['dry'] === '1';
    // Products now get the block editor too (see coptrz_enable_product_block_editor(),
    // includes/woocommerce.php), so every post type converts to native blocks —
    // coptrz_convert_post_sections_to_blocks() snapshots individual sections to
    // Custom HTML where they can't map natively, and refuses a product with a
    // non-empty post_content rather than guessing where to put it.
    $report = coptrz_convert_post_sections_to_blocks($post_id, $dry);
    wp_send_json_success($report);
});

add_action('wp_ajax_coptrz_revert_sections', function () {
    check_ajax_referer('coptrz_revert_sections', 'nonce');
    $post_id = isset($_POST['post']) ? (int) $_POST['post'] : 0;
    if (!$post_id || !current_user_can('edit_post', $post_id)) {
        wp_send_json_error('Permission denied.');
    }
    $dry = !empty($_POST['dry']) && $_POST['dry'] === '1';
    $report = coptrz_revert_post_sections($post_id, $dry);
    wp_send_json_success($report);
});

/* ========================================================================= */
/*  Admin UI — bulk runner (Tools > Convert Sections)                         */
/* ========================================================================= */

add_action('admin_menu', function () {
    add_management_page(
        __('Convert Sections', 'coptrz-theme'),
        __('Convert Sections', 'coptrz-theme'),
        'manage_options',
        'coptrz-convert-sections',
        'coptrz_render_bulk_converter_page'
    );
});

/**
 * AJAX: search section-bearing posts by name, returning id/title/post-type so the
 * runner page can build an explicit selection (replaces the old convert-everything
 * bulk query and the raw post-ID box).
 *
 * Default (`mode=convert`): only posts that actually NEED converting are returned —
 * they must hold section data (a `_sections` or `_sections_after_main` row exists,
 * in CF's `_<field>|||0|value` row-marker format) AND not already be flagged
 * converted. `mode=revert` inverts the flag condition to find already-converted
 * posts instead, for the revert side of the same runner.
 *
 * @return void
 */
add_action('wp_ajax_coptrz_search_sections_posts', function () {
    check_ajax_referer('coptrz_search_sections', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permission denied.');
    }
    $q = isset($_GET['q']) ? sanitize_text_field(wp_unslash($_GET['q'])) : '';
    if (function_exists('mb_strlen') ? mb_strlen($q) < 2 : strlen($q) < 2) {
        wp_send_json_success(array());
    }
    $mode = (isset($_GET['mode']) && $_GET['mode'] === 'revert') ? 'revert' : 'convert';

    $query = new WP_Query(array(
        'post_type'           => coptrz_section_post_types(),
        'post_status'         => array('publish', 'private', 'draft', 'pending', 'future'),
        's'                   => $q,
        'posts_per_page'      => 20,
        'no_found_rows'       => true,
        'ignore_sticky_posts' => true,
        'orderby'             => 'title',
        'order'               => 'ASC',
        'meta_query'          => array(
            'relation' => 'AND',
            // Has at least one section to freeze (either source field).
            array(
                'relation' => 'OR',
                array('key' => '_sections|||0|value', 'compare' => 'EXISTS'),
                array('key' => '_sections_after_main|||0|value', 'compare' => 'EXISTS'),
            ),
            array('key' => COPTRZ_SECTIONS_CONVERTED_FLAG, 'compare' => ($mode === 'revert') ? 'EXISTS' : 'NOT EXISTS'),
        ),
    ));

    $out = array();
    foreach ($query->posts as $p) {
        $obj = get_post_type_object($p->post_type);
        $out[] = array(
            'id'         => (int) $p->ID,
            'title'      => $p->post_title !== '' ? $p->post_title : ('#' . $p->ID),
            'type_label' => $obj ? $obj->labels->singular_name : $p->post_type,
        );
    }
    wp_send_json_success($out);
});

/**
 * Convert-by-search runner page: search posts by name (across every section post
 * type), pick the exact ones to freeze, then dry-run or convert just those. There
 * is no "convert everything" path — conversions are always an explicit selection.
 *
 * @return void
 */
function coptrz_render_bulk_converter_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $action     = isset($_POST['coptrz_bulk_action']) ? sanitize_key($_POST['coptrz_bulk_action']) : '';
    $is_revert  = ($action === 'revert' || $action === 'revert_dry');
    $ids_raw    = isset($_POST['coptrz_ids']) ? sanitize_text_field(wp_unslash($_POST['coptrz_ids'])) : '';
    $did        = array();

    if ($action && check_admin_referer('coptrz_bulk_convert')) {
        // Always an explicit selection of post IDs (gathered via the name search).
        preg_match_all('/\d+/', $ids_raw, $m);
        $ids   = array_values(array_unique(array_map('intval', $m[0])));
        $limit = 50; // safety cap per run
        $dry   = ($action === 'dry' || $action === 'revert_dry');
        foreach (array_slice($ids, 0, $limit) as $pid) {
            if ($is_revert) {
                $did[] = coptrz_revert_post_sections($pid, $dry);
                continue;
            }
            // Same as the per-post box's AJAX handler: every post type — products
            // included — now converts to native blocks.
            $did[] = coptrz_convert_post_sections_to_blocks($pid, $dry);
        }
    }

    $search_nonce = wp_create_nonce('coptrz_search_sections');
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Convert Sections', 'coptrz-theme'); ?></h1>
        <p class="description">
            <?php esc_html_e('Freezes the legacy page-builder sections into native Gutenberg blocks (any section that can’t map natively is frozen as Custom HTML instead). Products with an existing description (post_content) are refused — clear it first, then re-run. Original data is preserved, so a conversion can be reverted from the same tool.', 'coptrz-theme'); ?>
        </p>

        <form method="post" class="coptrz-bulk" data-nonce="<?php echo esc_attr($search_nonce); ?>">
            <?php wp_nonce_field('coptrz_bulk_convert'); ?>

            <p style="margin-bottom:.5em;">
                <label style="margin-right:1.5em;">
                    <input type="radio" name="coptrz_bulk_mode_ui" value="convert" checked />
                    <?php esc_html_e('Convert — posts that still need converting', 'coptrz-theme'); ?>
                </label>
                <label>
                    <input type="radio" name="coptrz_bulk_mode_ui" value="revert" />
                    <?php esc_html_e('Revert — already-converted posts', 'coptrz-theme'); ?>
                </label>
            </p>
            <input type="hidden" name="coptrz_bulk_mode" class="coptrz-bulk__mode" value="convert" />

            <p style="margin-bottom:.3em;">
                <label for="coptrz-bulk-search"><strong><?php esc_html_e('Search posts by name', 'coptrz-theme'); ?></strong></label>
            </p>
            <p style="position:relative;max-width:40em;">
                <input type="search" id="coptrz-bulk-search" class="regular-text" autocomplete="off" style="width:100%;"
                       placeholder="<?php esc_attr_e('Start typing a title…', 'coptrz-theme'); ?>" />
                <span class="spinner coptrz-bulk__spin" style="float:none;margin:0;position:absolute;right:6px;top:6px;"></span>
            </p>
            <ul class="coptrz-bulk__results" style="margin:0 0 1em;max-width:40em;"></ul>

            <h2 style="margin-bottom:.3em;"><?php esc_html_e('Selected posts', 'coptrz-theme'); ?></h2>
            <ul class="coptrz-bulk__selected" style="margin:0 0 1em;max-width:40em;"></ul>
            <input type="hidden" name="coptrz_ids" class="coptrz-bulk__ids" value="<?php echo esc_attr($ids_raw); ?>" />

            <p class="coptrz-bulk__actions-convert">
                <button class="button" name="coptrz_bulk_action" value="dry"><?php esc_html_e('Dry run', 'coptrz-theme'); ?></button>
                <button class="button button-primary" name="coptrz_bulk_action" value="convert"
                        onclick="return confirm('<?php echo esc_js(__('Convert the selected posts? Original data is kept.', 'coptrz-theme')); ?>');">
                    <?php esc_html_e('Convert selected posts', 'coptrz-theme'); ?>
                </button>
            </p>
            <p class="coptrz-bulk__actions-revert" style="display:none;">
                <button class="button" name="coptrz_bulk_action" value="revert_dry"><?php esc_html_e('Dry run revert', 'coptrz-theme'); ?></button>
                <button class="button button-primary" name="coptrz_bulk_action" value="revert" style="background:#b32d2e;border-color:#b32d2e;"
                        onclick="return confirm('<?php echo esc_js(__('Revert the selected posts to the legacy sections builder? Converted blocks will be removed from post_content.', 'coptrz-theme')); ?>');">
                    <?php esc_html_e('Revert selected posts', 'coptrz-theme'); ?>
                </button>
            </p>
        </form>

        <?php if (!empty($did)) :
            $heading = $is_revert
                ? (($action === 'revert_dry') ? __('Dry run revert results', 'coptrz-theme') : __('Revert results', 'coptrz-theme'))
                : (($action === 'dry') ? __('Dry run results', 'coptrz-theme') : __('Conversion results', 'coptrz-theme'));
        ?>
            <h2><?php echo esc_html($heading); ?></h2>
            <table class="widefat striped">
                <thead><tr>
                    <th><?php esc_html_e('Post', 'coptrz-theme'); ?></th>
                    <th><?php esc_html_e('Type', 'coptrz-theme'); ?></th>
                    <th><?php esc_html_e('Target', 'coptrz-theme'); ?></th>
                    <th><?php esc_html_e('Sections', 'coptrz-theme'); ?></th>
                    <th><?php esc_html_e('Notes', 'coptrz-theme'); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($did as $r) :
                    $total = array_sum($r['counts']); ?>
                    <tr>
                        <td><a href="<?php echo esc_url(get_edit_post_link($r['post_id'])); ?>"><?php echo esc_html($r['title'] ?: ('#' . $r['post_id'])); ?></a></td>
                        <td><?php echo esc_html($r['type']); ?></td>
                        <td>
                            <?php echo esc_html($r['target']); ?>
                            <?php if (!empty($r['template'])) : ?>
                                <br /><span class="description"><?php echo esc_html__('Template →', 'coptrz-theme') . ' ' . esc_html($r['template']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo (int) $total; ?></td>
                        <td><?php echo esc_html(implode(' ', $r['warnings'])); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <script>
    (function () {
        var form       = document.querySelector('form.coptrz-bulk');
        if (!form) { return; }
        var nonce      = form.getAttribute('data-nonce');
        var input      = form.querySelector('#coptrz-bulk-search');
        var results    = form.querySelector('.coptrz-bulk__results');
        var selList    = form.querySelector('.coptrz-bulk__selected');
        var idsField   = form.querySelector('.coptrz-bulk__ids');
        var modeField  = form.querySelector('.coptrz-bulk__mode');
        var modeInputs = form.querySelectorAll('input[name="coptrz_bulk_mode_ui"]');
        var actionsConvert = form.querySelector('.coptrz-bulk__actions-convert');
        var actionsRevert  = form.querySelector('.coptrz-bulk__actions-revert');
        var spin     = form.querySelector('.coptrz-bulk__spin');
        var selected = {}; // id -> label (label rendered via textContent, never HTML)

        function currentMode() { return modeField.value; }

        modeInputs.forEach(function (radio) {
            radio.addEventListener('change', function () {
                if (!radio.checked) { return; }
                modeField.value = radio.value;
                actionsConvert.style.display = (radio.value === 'revert') ? 'none' : '';
                actionsRevert.style.display  = (radio.value === 'revert') ? '' : 'none';
                // Candidate sets differ per mode — drop the selection rather than
                // risk reverting/converting a post picked under the other mode.
                selected = {};
                syncIds();
                renderSelected();
                results.innerHTML = '';
                input.value = '';
            });
        });

        function syncIds() { idsField.value = Object.keys(selected).join(','); }

        function renderSelected() {
            selList.innerHTML = '';
            var ids = Object.keys(selected);
            if (!ids.length) {
                var empty = document.createElement('li');
                empty.className = 'description';
                empty.textContent = '<?php echo esc_js(__('No posts selected yet.', 'coptrz-theme')); ?>';
                selList.appendChild(empty);
                return;
            }
            ids.forEach(function (id) {
                var li = document.createElement('li');
                li.style.margin = '.2em 0';
                var rm = document.createElement('button');
                rm.type = 'button';
                rm.className = 'button-link coptrz-bulk__rm';
                rm.setAttribute('data-id', id);
                rm.style.cssText = 'color:#b32d2e;text-decoration:none;margin-right:.5em;';
                rm.textContent = '×';
                var label = document.createElement('span');
                label.textContent = selected[id];
                li.appendChild(rm);
                li.appendChild(label);
                selList.appendChild(li);
            });
        }

        function add(id, label) {
            id = String(parseInt(id, 10));
            if (id === 'NaN' || selected[id]) { return; }
            selected[id] = label || ('#' + id);
            syncIds();
            renderSelected();
        }
        function remove(id) { delete selected[id]; syncIds(); renderSelected(); }

        // Re-seed from any IDs preserved across a submit.
        (idsField.value || '').split(/[^0-9]+/).forEach(function (id) { if (id) { add(id, '#' + id); } });
        renderSelected();

        var timer = null;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            var q = input.value.trim();
            if (q.length < 2) { results.innerHTML = ''; return; }
            timer = setTimeout(function () { runSearch(q); }, 300);
        });

        function runSearch(q) {
            spin.classList.add('is-active');
            var url = ajaxurl + '?action=coptrz_search_sections_posts&nonce=' + encodeURIComponent(nonce)
                + '&mode=' + encodeURIComponent(currentMode()) + '&q=' + encodeURIComponent(q);
            fetch(url, { credentials: 'same-origin' })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    spin.classList.remove('is-active');
                    results.innerHTML = '';
                    var items = (res && res.data) ? res.data : [];
                    if (!items.length) {
                        var none = document.createElement('li');
                        none.className = 'description';
                        none.textContent = '<?php echo esc_js(__('No matches.', 'coptrz-theme')); ?>';
                        results.appendChild(none);
                        return;
                    }
                    items.forEach(function (it) {
                        var li = document.createElement('li');
                        li.style.margin = '.2em 0';
                        var btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'button button-small';
                        btn.style.marginRight = '.5em';
                        btn.textContent = '<?php echo esc_js(__('Add', 'coptrz-theme')); ?>';
                        btn.addEventListener('click', function () { add(it.id, it.title + ' (' + it.type_label + ')'); });
                        var title = document.createElement('strong');
                        title.textContent = it.title;
                        var type = document.createElement('span');
                        type.style.color = '#646970';
                        type.textContent = ' (' + it.type_label + ')';
                        li.appendChild(btn);
                        li.appendChild(title);
                        li.appendChild(type);
                        results.appendChild(li);
                    });
                })
                .catch(function () {
                    spin.classList.remove('is-active');
                    results.innerHTML = '';
                    var err = document.createElement('li');
                    err.className = 'description';
                    err.textContent = '<?php echo esc_js(__('Search failed.', 'coptrz-theme')); ?>';
                    results.appendChild(err);
                });
        }

        selList.addEventListener('click', function (e) {
            var b = e.target.closest('.coptrz-bulk__rm');
            if (b) { remove(b.getAttribute('data-id')); }
        });
    })();
    </script>
    <?php
}
