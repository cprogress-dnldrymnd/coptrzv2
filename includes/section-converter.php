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
 * Whether ___sections() should render the frozen output instead of the legacy
 * builder for this post + field. True when the post is flagged converted, or —
 * for products — whenever the HTML repeater already holds rows (so the repeater
 * is the natural editing surface for new products too).
 *
 * @param int    $post_id
 * @param string $id  sections | sections_after_main
 * @return bool
 */
function coptrz_sections_should_route($post_id, $id)
{
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
/*  Rendering for converted posts (called by ___sections())                  */
/* ========================================================================= */

/**
 * Render the frozen output for a converted post + field id.
 *
 *  - product       -> concatenate the `{$id}_html` repeater rows.
 *  - other types    -> the body lives in post_content as Custom HTML blocks; the
 *    primary `sections` call returns the filtered content, and the secondary
 *    `sections_after_main` call returns '' (its blocks were appended to content).
 *
 * @param string $id       sections | sections_after_main
 * @param int    $post_id
 * @return string
 */
function coptrz_render_converted_sections($id, $post_id)
{
    if (get_post_type($post_id) === 'product') {
        $rows = get__post_meta_by_id($post_id, $id . '_html');
        if (!is_array($rows)) {
            return '';
        }
        $out = '';
        foreach ($rows as $row) {
            if (!empty($row['html'])) {
                $out .= $row['html'];
            }
        }
        return $out;
    }

    if ($id === 'sections') {
        $content = get_post_field('post_content', $post_id);
        return apply_filters('the_content', $content);
    }

    return ''; // sections_after_main already merged into post_content.
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
}

/* ========================================================================= */
/*  Conversion engine                                                         */
/* ========================================================================= */

/**
 * Convert one post's sections into frozen HTML.
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
            // Render this single section live, then freeze (resolve shortcodes).
            $html = trim(do_shortcode(___sections($field, $post_id, $key)));
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

/**
 * IDs of posts that still have unconverted sections (for the bulk runner).
 *
 * @param string $post_type '' = all section-bearing types.
 * @return int[]
 */
function coptrz_get_posts_with_sections($post_type = '')
{
    $query = new WP_Query(array(
        'post_type'      => $post_type ? $post_type : coptrz_section_post_types(),
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'meta_query'     => array(
            'relation' => 'AND',
            array('key' => '_sections|||0|value', 'compare' => 'EXISTS'),
            array('key' => COPTRZ_SECTIONS_CONVERTED_FLAG, 'compare' => 'NOT EXISTS'),
        ),
    ));
    return array_map('intval', $query->posts);
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
        __('Convert Sections to HTML', 'coptrz-theme'),
        'coptrz_render_section_converter_box',
        $post_type,
        'side',
        'high'
    );
});

/**
 * Per-post convert box: dry-run preview + convert, via admin-ajax.
 *
 * @param WP_Post $post
 * @return void
 */
function coptrz_render_section_converter_box($post)
{
    $converted = coptrz_sections_is_converted($post->ID);
    $nonce     = wp_create_nonce('coptrz_convert_sections');
    ?>
    <div class="coptrz-conv" data-post="<?php echo (int) $post->ID; ?>" data-nonce="<?php echo esc_attr($nonce); ?>">
        <?php if ($converted) : ?>
            <p style="color:#1a7f37;font-weight:600;margin-top:0;">✓ <?php esc_html_e('Already converted.', 'coptrz-theme'); ?></p>
            <p class="description"><?php esc_html_e('The original section data is preserved. Edit the content via the block editor (or the HTML repeater for products).', 'coptrz-theme'); ?></p>
        <?php else : ?>
            <p class="description" style="margin-top:0;"><?php esc_html_e('Freeze this post’s sections into static HTML. Original data is kept.', 'coptrz-theme'); ?></p>
            <p>
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
        function run(dry) {
            var out = box.querySelector('.coptrz-conv__out');
            out.textContent = '…';
            var body = new URLSearchParams({
                action: 'coptrz_convert_sections',
                nonce: box.dataset.nonce,
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
        var d = box.querySelector('.coptrz-conv__dry'); if (d) { d.addEventListener('click', function () { run(true); }); }
        var g = box.querySelector('.coptrz-conv__go'); if (g) { g.addEventListener('click', function () { if (confirm('Convert this post’s sections to HTML?')) { run(false); } }); }
    })();
    </script>
    <?php
}

add_action('wp_ajax_coptrz_convert_sections', function () {
    check_ajax_referer('coptrz_convert_sections', 'nonce');
    $post_id = isset($_POST['post']) ? (int) $_POST['post'] : 0;
    if (!$post_id || !current_user_can('edit_post', $post_id)) {
        wp_send_json_error('Permission denied.');
    }
    $dry = !empty($_POST['dry']) && $_POST['dry'] === '1';
    wp_send_json_success(coptrz_convert_post_sections($post_id, $dry));
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
 * Bulk runner page: pick a post type, dry-run to list candidates, then convert.
 *
 * @return void
 */
function coptrz_render_bulk_converter_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $post_type = isset($_REQUEST['ptype']) ? sanitize_key($_REQUEST['ptype']) : '';
    $action    = isset($_POST['coptrz_bulk_action']) ? sanitize_key($_POST['coptrz_bulk_action']) : '';
    $did       = array();

    if ($action && check_admin_referer('coptrz_bulk_convert')) {
        $ids = coptrz_get_posts_with_sections($post_type);
        $limit = 50; // safety cap per run
        foreach (array_slice($ids, 0, $limit) as $pid) {
            $did[] = coptrz_convert_post_sections($pid, ($action === 'dry'));
        }
    }

    $candidates = coptrz_get_posts_with_sections($post_type);
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Convert Sections to HTML', 'coptrz-theme'); ?></h1>
        <p class="description">
            <?php esc_html_e('Freezes the legacy page-builder sections into static HTML. Non-product posts receive Gutenberg Custom HTML blocks; products receive a sortable HTML repeater. Original data is preserved.', 'coptrz-theme'); ?>
        </p>

        <form method="post">
            <?php wp_nonce_field('coptrz_bulk_convert'); ?>
            <p>
                <label><?php esc_html_e('Post type', 'coptrz-theme'); ?>
                    <select name="ptype">
                        <option value=""><?php esc_html_e('All section types', 'coptrz-theme'); ?></option>
                        <?php foreach (coptrz_section_post_types() as $pt) :
                            $obj = get_post_type_object($pt); if (!$obj) { continue; } ?>
                            <option value="<?php echo esc_attr($pt); ?>" <?php selected($post_type, $pt); ?>>
                                <?php echo esc_html($obj->labels->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </p>
            <p>
                <strong><?php echo (int) count($candidates); ?></strong>
                <?php esc_html_e('unconverted post(s) with sections match (max 50 processed per run).', 'coptrz-theme'); ?>
            </p>
            <p>
                <button class="button" name="coptrz_bulk_action" value="dry"><?php esc_html_e('Dry run', 'coptrz-theme'); ?></button>
                <button class="button button-primary" name="coptrz_bulk_action" value="convert"
                        onclick="return confirm('<?php echo esc_js(__('Convert all matching posts? Original data is kept.', 'coptrz-theme')); ?>');">
                    <?php esc_html_e('Convert matching posts', 'coptrz-theme'); ?>
                </button>
            </p>
        </form>

        <?php if (!empty($did)) : ?>
            <h2><?php echo $action === 'dry' ? esc_html__('Dry run results', 'coptrz-theme') : esc_html__('Conversion results', 'coptrz-theme'); ?></h2>
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
                        <td><?php echo esc_html($r['target']); ?></td>
                        <td><?php echo (int) $total; ?></td>
                        <td><?php echo esc_html(implode(' ', $r['warnings'])); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}
