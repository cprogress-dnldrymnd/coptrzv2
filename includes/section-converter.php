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
        $rows = get__post_meta_by_id($post_id, $id . '_html');
        if (is_array($rows)) {
            foreach ($rows as $row) {
                if (!empty($row['html'])) {
                    $out .= $row['html'];
                }
            }
        }
        $out = do_shortcode($out);
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
 * AJAX: search section-bearing posts by name, returning id/title/post-type so the
 * runner page can build an explicit selection (replaces the old convert-everything
 * bulk query and the raw post-ID box).
 *
 * Only posts that actually NEED converting are returned: they must hold section
 * data (a `_sections` or `_sections_after_main` row exists, in CF's
 * `_<field>|||0|value` row-marker format) AND not already be flagged converted.
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
            // Not already converted.
            array('key' => COPTRZ_SECTIONS_CONVERTED_FLAG, 'compare' => 'NOT EXISTS'),
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

    $action  = isset($_POST['coptrz_bulk_action']) ? sanitize_key($_POST['coptrz_bulk_action']) : '';
    $ids_raw = isset($_POST['coptrz_ids']) ? sanitize_text_field(wp_unslash($_POST['coptrz_ids'])) : '';
    $did     = array();

    if ($action && check_admin_referer('coptrz_bulk_convert')) {
        // Always an explicit selection of post IDs (gathered via the name search).
        preg_match_all('/\d+/', $ids_raw, $m);
        $ids   = array_values(array_unique(array_map('intval', $m[0])));
        $limit = 50; // safety cap per run
        foreach (array_slice($ids, 0, $limit) as $pid) {
            $did[] = coptrz_convert_post_sections($pid, ($action === 'dry'));
        }
    }

    $search_nonce = wp_create_nonce('coptrz_search_sections');
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Convert Sections to HTML', 'coptrz-theme'); ?></h1>
        <p class="description">
            <?php esc_html_e('Freezes the legacy page-builder sections into static HTML. Non-product posts receive Gutenberg Custom HTML blocks; products receive a sortable HTML repeater. Original data is preserved.', 'coptrz-theme'); ?>
        </p>

        <form method="post" class="coptrz-bulk" data-nonce="<?php echo esc_attr($search_nonce); ?>">
            <?php wp_nonce_field('coptrz_bulk_convert'); ?>

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

            <p>
                <button class="button" name="coptrz_bulk_action" value="dry"><?php esc_html_e('Dry run', 'coptrz-theme'); ?></button>
                <button class="button button-primary" name="coptrz_bulk_action" value="convert"
                        onclick="return confirm('<?php echo esc_js(__('Convert the selected posts? Original data is kept.', 'coptrz-theme')); ?>');">
                    <?php esc_html_e('Convert selected posts', 'coptrz-theme'); ?>
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
        var form = document.querySelector('form.coptrz-bulk');
        if (!form) { return; }
        var nonce    = form.getAttribute('data-nonce');
        var input    = form.querySelector('#coptrz-bulk-search');
        var results  = form.querySelector('.coptrz-bulk__results');
        var selList  = form.querySelector('.coptrz-bulk__selected');
        var idsField = form.querySelector('.coptrz-bulk__ids');
        var spin     = form.querySelector('.coptrz-bulk__spin');
        var selected = {}; // id -> label (label rendered via textContent, never HTML)

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
            var url = ajaxurl + '?action=coptrz_search_sections_posts&nonce=' + encodeURIComponent(nonce) + '&q=' + encodeURIComponent(q);
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
