<?php
/**
 * Plugin/Snippet Name: Convert to Blocks (Sections + Hero)
 * Description: Retires two legacy Carbon-Fields-shaped editing surfaces by
 *              freezing them into native Gutenberg blocks in one action:
 *                - the "sections" page-builder (`sections` / `sections_after_main`
 *                  complex meta) -> Gutenberg blocks appended to post_content
 *                  (Custom HTML for anything that can't map natively); products
 *                  get a lightweight sortable HTML repeater instead, since they
 *                  have no rendered use of post_content directly.
 *                - the "Hero" post-meta box (includes/post-meta.php,
 *                  __hero_fields() etc, see includes/hero-converter.php) ->
 *                  a `coptrz/hero` block PREPENDED as the first block, but only
 *                  when it's verified to render identically to the current
 *                  meta-driven hero (coptrz_hero_dry_run_check(),
 *                  includes/hero-converter.php) — otherwise that part is
 *                  skipped and the post keeps rendering its hero from meta,
 *                  which stays a permanent, correct fallback either way.
 *              A post converts whichever of the two parts actually apply to it
 *              — sections and hero are independent per-post, so `post`/`guides`
 *              (hero only) and `layouts`/`producttaxonomypages` (sections only)
 *              are all still covered by the SAME single action, alongside the
 *              post types that have both.
 *
 *              Both original data sources are KEPT (conversion is reversible)
 *              and the legacy builder meta box is hidden. Sections-rendering is
 *              routed by `___sections()` (see modules.php) via
 *              coptrz_sections_is_converted() — that flag's meaning is
 *              unchanged by the hero merge; a hero-only conversion never sets
 *              it. Hero-rendering is routed by ___hero_modules() (modules.php)
 *              via coptrz_hero_block_attrs() (includes/hero-block.php), keyed
 *              off the block's presence in post_content, not a flag —
 *              `_coptrz_hero_converted` (includes/hero-converter.php) is
 *              bookkeeping only, read by nothing at render time.
 *
 *              Tools provided: a per-post "Convert to Blocks" meta box (with
 *              dry-run) and a search-and-select bulk runner under
 *              Tools > Convert to Blocks (50/run cap). A read-only "remaining
 *              by post type" table sits above the search as a progress
 *              overview only — there is deliberately no batch "convert all
 *              remaining" action; every post must be converted (or reverted)
 *              individually, to avoid a wholesale sitewide conversion run.
 *
 *              Caveat: freezing sections is a SNAPSHOT. Dynamic widgets (post
 *              grids, sliders) keep working visually (main.js re-inits by
 *              class) but no longer auto-update; embedded forms/popups that
 *              rely on per-request nonces become static. Review dynamic
 *              sections before converting.
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

/** Post meta flag marking a converted post's legacy sections/hero data as permanently deleted. */
const COPTRZ_LEGACY_PURGED_FLAG = '_coptrz_legacy_purged';

/** Source page-builder fields, in render order. */
function coptrz_section_source_fields()
{
    return array('sections', 'sections_after_main');
}

/** Post types that can carry sections. Still used on its own where the check
 * is specifically about legacy `_sections` data (the admin_notices legacy-edit
 * hatch below); the combined admin surface uses coptrz_convertible_post_types(). */
function coptrz_section_post_types()
{
    return array(
        'page', 'product', 'layouts', 'capabilities', 'casestudies',
        'producttaxonomypages', 'industries', 'events', 'rentals', 'landingpages',
    );
}

/**
 * The union of coptrz_section_post_types() and coptrz_hero_post_types()
 * (includes/hero-converter.php) — drives the single "Convert to Blocks" admin
 * surface (meta box registration + bulk search post types). Sections and hero
 * are independent per post: `post`/`guides` only ever have a hero to convert,
 * `layouts`/`producttaxonomypages` only ever have sections, and everything
 * else may have either or both — coptrz_convert_post_to_blocks() decides per
 * post which parts actually apply.
 *
 * @return string[]
 */
function coptrz_convertible_post_types()
{
    $hero = function_exists('coptrz_hero_post_types') ? coptrz_hero_post_types() : array();
    return array_values(array_unique(array_merge(coptrz_section_post_types(), $hero)));
}

/**
 * Whether a post has any legacy `sections` / `sections_after_main` row data.
 * Complex root fields are stored per-cell (e.g. `_sections|||0|value`, one row
 * per cell) by the Carbon-fields-shaped meta shim — there is no bare
 * `_sections` postmeta row, so a plain `metadata_exists('post', $id,
 * '_sections')` check (the bug this replaces) always returns false. Mirrors
 * the same `Key_Formatter::load_root_map()` lookup the legacy-data
 * admin_notices hook below already uses.
 *
 * @param int $post_id
 * @return bool
 */
function coptrz_post_has_sections_data($post_id)
{
    foreach (coptrz_section_source_fields() as $field_name) {
        if (!empty(\CoptrzTheme\MetaShim\Key_Formatter::load_root_map('post', $post_id, $field_name))) {
            return true;
        }
    }
    return false;
}

/**
 * What's pending for a post: a subset of `['hero', 'sections']`, empty when
 * there's nothing left to convert (or nothing applicable). Used by the bulk
 * search (to filter + label results) and mirrors — but doesn't replace — the
 * per-part skip checks inside coptrz_convert_post_to_blocks() itself, which
 * re-verify at conversion time (e.g. the hero identical-render check) rather
 * than trusting this cheap estimate.
 *
 * @param int $post_id
 * @return string[]
 */
function coptrz_post_conversion_state($post_id)
{
    $post = get_post($post_id);
    if (!$post) {
        return array();
    }

    $pending = array();

    if (function_exists('coptrz_hero_is_converted')
        && function_exists('coptrz_hero_has_content')
        && in_array($post->post_type, coptrz_hero_post_types(), true)
        && !coptrz_hero_is_converted($post_id)
        && coptrz_hero_has_content($post_id)
    ) {
        $pending[] = 'hero';
    }

    if (in_array($post->post_type, coptrz_section_post_types(), true)
        && !coptrz_sections_is_converted($post_id)
        && coptrz_post_has_sections_data($post_id)
    ) {
        $pending[] = 'sections';
    }

    return $pending;
}

/**
 * Builds a correlated-EXISTS WHERE fragment (scoped to ONE given post type,
 * against a `wp_posts p` alias) selecting posts that still have something
 * pending — used per-type (never across a mixed post_type list, where "hero
 * not converted" would wrongly match a hero-inapplicable type) by
 * coptrz_conversion_pending_count() and coptrz_conversion_pending_ids().
 *
 * Deliberately NOT a WP_Query `meta_query`: an OR of two EXISTS clauses on
 * different meta keys (has `_sections` OR has `_sections_after_main`)
 * compiles to two UNKEYED `wp_postmeta` LEFT JOINs — the meta_key check lands
 * in WHERE, not the JOIN's ON clause — so each join matches every meta row of
 * every post. With Carbon Fields' meta-per-post counts on this site that's a
 * posts × meta-per-post² cartesian scan (confirmed via EXPLAIN and a timed
 * repro: minutes per post type, 504ing the Tools page). Correlated EXISTS
 * subqueries hit the `meta_key` index directly and can't multiply rows.
 *
 * @param string $post_type
 * @return array{0:string,1:array} [$where_sql ('' if nothing applicable to
 *         this type), $prepare_args for its %s placeholders, in appearance order]
 */
function coptrz_conversion_pending_where($post_type)
{
    global $wpdb;

    $hero_applicable    = function_exists('coptrz_hero_post_types') && in_array($post_type, coptrz_hero_post_types(), true);
    $section_applicable = in_array($post_type, coptrz_section_post_types(), true);

    $branches = array();
    $args     = array();

    if ($hero_applicable && defined('COPTRZ_HERO_CONVERTED_FLAG') && function_exists('coptrz_hero_has_content_where')) {
        list($hero_where, $hero_args) = coptrz_hero_has_content_where('p');
        $branches[] = "( NOT EXISTS (SELECT 1 FROM {$wpdb->postmeta} h WHERE h.post_id = p.ID AND h.meta_key = %s)
                         AND {$hero_where} )";
        $args[]     = COPTRZ_HERO_CONVERTED_FLAG;
        array_push($args, ...$hero_args);
    }

    if ($section_applicable) {
        $branches[] = "( EXISTS (SELECT 1 FROM {$wpdb->postmeta} s WHERE s.post_id = p.ID AND s.meta_key IN (%s, %s))
                         AND NOT EXISTS (SELECT 1 FROM {$wpdb->postmeta} c WHERE c.post_id = p.ID AND c.meta_key = %s) )";
        array_push($args, '_sections|||0|value', '_sections_after_main|||0|value', COPTRZ_SECTIONS_CONVERTED_FLAG);
    }

    if (empty($branches)) {
        return array('', array());
    }

    return array('(' . implode(' OR ', $branches) . ')', $args);
}

/** Post statuses eligible for conversion, shared by every pending query below. */
function coptrz_convertible_post_statuses()
{
    return array('publish', 'private', 'draft', 'pending', 'future');
}

/**
 * How many posts of ONE post type still have something pending. Backs the
 * read-only "remaining by post type" progress table on the bulk page. An
 * estimate — see coptrz_conversion_pending_where()'s docblock.
 *
 * @param string $post_type
 * @return int
 */
function coptrz_conversion_pending_count($post_type)
{
    global $wpdb;

    list($where, $args) = coptrz_conversion_pending_where($post_type);
    if ($where === '') {
        return 0;
    }

    $statuses  = coptrz_convertible_post_statuses();
    $in_status = implode(',', array_fill(0, count($statuses), '%s'));

    $sql = "SELECT COUNT(*) FROM {$wpdb->posts} p
            WHERE p.post_type = %s AND p.post_status IN ({$in_status}) AND {$where}";

    return (int) $wpdb->get_var($wpdb->prepare($sql, array_merge(array($post_type), $statuses, $args)));
}

/**
 * How many posts of each convertible post type still have something pending.
 * Backs the read-only "remaining by post type" progress table on the bulk
 * page. An estimate — see coptrz_conversion_pending_where()'s docblock.
 *
 * @return array<string,int> post_type => remaining count
 */
function coptrz_conversion_remaining_counts()
{
    $out = array();
    foreach (coptrz_convertible_post_types() as $post_type) {
        $out[$post_type] = coptrz_conversion_pending_count($post_type);
    }
    return $out;
}

/**
 * Whether $content contains the signature of the wp_update_post()/wp_slash()
 * bug (fixed in coptrz_convert_post_to_blocks() and
 * coptrz_revert_post_to_blocks()): wp_update_post()/update_post_meta() both
 * call wp_unslash() on their input internally, and prior to the fix, block
 * comment JSON (which contains literal backslash-escapes like <, produced by
 * wp-includes/blocks.php serialize_block_attributes()) was being written
 * without wp_slash() first, so every escaped character was silently stripped —
 * `<p>` became the literal text `u003cpu003e` on the frontend, instead of `<p>`.
 *
 * True if any of the six escape signatures (u003c, u003e, u0026, u002du002d,
 * u005c, u0022 — the stripped forms of <, >, &, --, \, and \") appears
 * anywhere in $content. These are not naturally-occurring English substrings,
 * so a false positive here is effectively impossible; this is a diagnostic
 * check, not a strict parser, so no attempt is made to bound the match to
 * inside a specific block comment.
 *
 * Used both by coptrz_find_corrupted_conversions() (site-wide listing) and
 * coptrz_purge_post_legacy_data() (refuses to purge a corrupted post, since
 * purging removes the only repair path — see that function's docblock).
 *
 * @param string $content
 * @return bool
 */
function coptrz_content_is_corrupted($content)
{
    $signatures = array('u003c', 'u003e', 'u0026', 'u002du002d', 'u005c', 'u0022');
    foreach ($signatures as $sig) {
        if (strpos((string) $content, $sig) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * Finds posts whose stored post_content contains the wp_slash() corruption
 * signature (coptrz_content_is_corrupted()). Only posts already fixed writes
 * can occur for are checked (coptrz_convertible_post_types()). Repair is:
 * Tools > Convert to Blocks > Revert, then Convert, on each listed post — NOT
 * an in-place text patch (a stripped \n is ambiguous with a literal trailing
 * "n", so recovery from the corrupted string alone can't be exact; the
 * untouched `_sections`/Hero meta this theme keeps around lets a fresh
 * conversion regenerate the correct content instead) — which is why
 * coptrz_purge_post_legacy_data() refuses to run on a post listed here,
 * UNLESS force-purged (its $force param) — that path is what `unrepairable`
 * reports: once true, the "Revert, then Convert" repair above no longer
 * works for that post (the meta it would regenerate from is gone on purpose),
 * so the caller should stop suggesting it.
 *
 * @return array<array{id:int,title:string,type:string,unrepairable:bool}>
 */
function coptrz_find_corrupted_conversions()
{
    global $wpdb;
    $post_types = coptrz_convertible_post_types();
    if (empty($post_types)) {
        return array();
    }

    $type_placeholders = implode(',', array_fill(0, count($post_types), '%s'));
    $sql = "SELECT ID, post_title, post_type, post_content FROM {$wpdb->posts}
            WHERE post_type IN ({$type_placeholders})
              AND post_status IN ('publish', 'private', 'draft', 'pending', 'future')
              AND post_content LIKE %s";
    $rows = $wpdb->get_results($wpdb->prepare($sql, array_merge($post_types, array('%u00%'))));

    $found = array();
    foreach ((array) $rows as $row) {
        if (coptrz_content_is_corrupted($row->post_content)) {
            $found[] = array(
                'id'           => (int) $row->ID,
                'title'        => $row->post_title,
                'type'         => $row->post_type,
                // A force-purged corrupted post (coptrz_purge_post_legacy_data(),
                // $force) no longer HAS a "Revert, then Convert" repair path —
                // the legacy data that would need is gone on purpose. Flagged
                // here so the Tools-page notice stops telling the admin to run
                // repair steps that can't work anymore.
                'unrepairable' => coptrz_post_is_legacy_purged((int) $row->ID),
            );
        }
    }
    return $found;
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
 * Whether a post's legacy sections/hero data has been permanently deleted via
 * the per-post box's Purge action (coptrz_purge_post_legacy_data()). Once set,
 * Revert/"Preview original"/the legacy public fallback are no longer possible
 * for the purged part(s) — there is nothing left to revert to.
 *
 * @param int $post_id
 * @return bool
 */
function coptrz_post_is_legacy_purged($post_id)
{
    return get_post_meta($post_id, COPTRZ_LEGACY_PURGED_FLAG, true) === 'yes';
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
 * coptrz_convert_post_sections() / coptrz_convert_post_to_blocks())
 * and must stay pure, or a preview/public-fallback request could re-trigger a
 * conversion and duplicate content.
 *
 * @param int $post_id
 * @return bool
 */
function coptrz_sections_legacy_override($post_id)
{
    if (is_admin() || !coptrz_sections_is_converted($post_id) || coptrz_post_is_legacy_purged($post_id)) {
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
 * Whether the CURRENT admin request should be shown the legacy Sections
 * builder meta box, which is otherwise retired as an editing surface (see
 * coptrz_register_html_sections_fields() / the coptrz_meta_shim_field_visible
 * filter below). Request-scoped only, never persisted — mirrors the frontend
 * ?coptrz_preview=original hatch (coptrz_sections_legacy_override()) so a
 * reverted post, or one with bad legacy data that needs fixing before
 * conversion, can still be edited.
 *
 * @param int $post_id
 * @return bool
 */
function coptrz_sections_legacy_edit_override($post_id)
{
    return isset($_GET['coptrz_edit'])
        && $_GET['coptrz_edit'] === 'legacy'
        && $post_id
        && current_user_can('edit_post', $post_id);
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
 * A converted `page` still carries the Blocks Editor page template
 * (templates/page-blocks-editor.php), which would render the converted
 * post_content via the_content() even once should_route() says "legacy" —
 * and that template has no ___hero_modules() call at all (the hero renders
 * inline from its block there). Force the Modules template for the duration
 * of the override so ___hero_modules() + ___sections() (the legacy path)
 * render instead. Other post types keep their bespoke single templates, which
 * already route through ___sections() — should_route() alone is sufficient
 * for them.
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
 * surface — hide_fields() above blocklists both globally.
 *
 * The legacy `sections` builder is retired as an editing surface entirely: all
 * future content changes go through the converted surface (native blocks, or
 * the HTML repeater for products), so it stays hidden even on unconverted
 * posts. It's only brought back per-post via the ?coptrz_edit=legacy admin
 * hatch (coptrz_sections_legacy_edit_override()) — e.g. after a revert, or to
 * fix bad legacy data before converting.
 *
 * The product HTML repeater is a converted editing surface (not legacy), so it
 * keeps reappearing for any product not yet on blocks mode.
 */
add_filter('coptrz_meta_shim_field_visible', function ($visible, $field_name, $post_id) {
    if (in_array($field_name, coptrz_section_source_fields(), true)) {
        return coptrz_sections_legacy_edit_override($post_id);
    }
    if (in_array($field_name, coptrz_html_sections_field_names(), true)
        && get_post_meta($post_id, '_coptrz_sections_mode', true) !== 'blocks'
    ) {
        return true;
    }
    return $visible;
}, 10, 3);

/**
 * Surface the ?coptrz_edit=legacy hatch on the edit screen of any post still
 * carrying legacy `_sections` data, since the meta box it unlocks is otherwise
 * invisible with no indication it can be reached. Shows a plain notice with a
 * link to enable it, or — while the hatch is active — a warning that edits
 * made there won't reach the front end once the post is converted.
 */
add_action('admin_notices', function () {
    global $post;
    if (!($post instanceof WP_Post) || !in_array($post->post_type, coptrz_section_post_types(), true)) {
        return;
    }
    if (!current_user_can('edit_post', $post->ID)) {
        return;
    }

    $has_legacy_data = false;
    foreach (coptrz_section_source_fields() as $field_name) {
        $rows = \CoptrzTheme\MetaShim\Key_Formatter::load_root_map('post', $post->ID, $field_name);
        if (!empty($rows)) {
            $has_legacy_data = true;
            break;
        }
    }
    if (!$has_legacy_data) {
        return;
    }

    if (coptrz_sections_legacy_edit_override($post->ID)) {
        ?>
        <div class="notice notice-warning">
            <p><?php esc_html_e('Editing the legacy Sections builder — this data is only used as a fallback for unconverted posts and will not appear once this post is converted.', 'coptrz-theme'); ?></p>
        </div>
        <?php
        return;
    }

    $edit_url = add_query_arg('coptrz_edit', 'legacy', get_edit_post_link($post->ID, 'raw'));
    ?>
    <div class="notice notice-info">
        <p>
            <?php esc_html_e('This post has legacy Sections builder data. The builder is hidden by default — content changes should use the converted editing surface.', 'coptrz-theme'); ?>
            <a href="<?php echo esc_url($edit_url); ?>"><?php esc_html_e('Edit legacy Sections anyway', 'coptrz-theme'); ?></a>
        </p>
    </div>
    <?php
});

/* ========================================================================= */
/*  Conversion engine                                                         */
/* ========================================================================= */

/**
 * Convert one post's sections into frozen HTML.
 *
 * Both admin UIs (the per-post meta box and the Tools > Convert to Blocks bulk
 * runner) now route to this ONLY for `product` posts, which have no block editor
 * and so use the `{$field}_html` repeater below. Every other post type routes to
 * coptrz_convert_post_to_blocks() instead — this function's non-product
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
            // wp_update_post() -> wp_insert_post() calls wp_unslash() on the
            // postarr internally (it expects SLASHED input, matching how
            // $_POST arrives) — block comment JSON produced by
            // serialize_blocks() contains literal backslash-escapes (<
            // etc, see wp-includes/blocks.php serialize_block_attributes())
            // that wp_unslash() would otherwise strip, corrupting every
            // escaped character in every block attribute. wp_slash() here
            // cancels that out.
            wp_update_post(array('ID' => $post_id, 'post_content' => wp_slash($new_content)));
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

/* ========================================================================= */
/*  Rich-text HTML → native blocks (DOMDocument-based)                       */
/* ========================================================================= */
/*
 * Backs the `description` mapper (and, via coptrz_inline_html(), the `heading`
 * mapper). Carbon Fields rich-text values routinely already contain
 * block-level HTML (<p>, <ul>, headings) — the bug this fixes was hand-wrapping
 * that raw HTML in a SECOND <p class="description-box"> for core/paragraph,
 * producing invalid nested markup (<p><p>…</p></p>, or worse <p><ul>…</ul></p>)
 * that fails Gutenberg's save()-vs-stored-markup validation the next time the
 * post is opened in the editor. Using DOMDocument's HTML parser to split the
 * source into one native block PER top-level node sidesteps this at the root:
 * whatever a browser would do to "recover" stray nesting in the source (e.g.
 * auto-close an inner <p>, making it a sibling) is exactly what libxml's HTML
 * parser does too, so the blocks this emits are always well-formed by
 * construction — never a hand-maintained validity assumption.
 */

/**
 * Parse an HTML fragment into a DOMDocument, wrapped in a `#coptrz-root`
 * container so top-level bare text nodes are reachable the same way as
 * top-level elements. Returns null if the DOM extension is unavailable or the
 * fragment fails to parse into anything.
 *
 * @param string $html
 * @return array{0:DOMDocument,1:DOMElement}|null
 */
function coptrz_parse_html_fragment($html)
{
    if (!class_exists('DOMDocument')) {
        return null;
    }
    $doc = new DOMDocument();
    $prev_setting = libxml_use_internal_errors(true);
    // The `<?xml encoding="UTF-8">` prefix is the standard idiom that makes
    // DOMDocument::loadHTML() treat the fragment as UTF-8 instead of mangling
    // multibyte characters (em dashes, degree signs, curly quotes — all
    // present in this theme's real content) into mojibake.
    $doc->loadHTML(
        '<?xml encoding="UTF-8"><div id="coptrz-root">' . $html . '</div>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();
    libxml_use_internal_errors($prev_setting);

    $root = $doc->getElementById('coptrz-root');
    return $root ? array($doc, $root) : null;
}

/**
 * Serialize a DOM node's CHILDREN (not the node itself) back to an HTML string.
 *
 * @param DOMDocument $doc
 * @param DOMNode     $node
 * @return string
 */
function coptrz_dom_inner_html($doc, $node)
{
    $html = '';
    foreach ($node->childNodes as $child) {
        $html .= $doc->saveHTML($child);
    }
    return $html;
}

/**
 * Reduce arbitrary rich-text HTML to INLINE markup only (no <p>/<div>/<ul>/
 * <h*>/etc) — for contexts whose save() only ever wraps RichText content in a
 * single tag, so a stray block-level element in the source can't nest inside
 * it the same way the description bug above did. Block-level/unrecognised
 * elements are unwrapped (their inline content is kept, the wrapping tag is
 * dropped); recognised inline formatting tags are preserved verbatim.
 *
 * @param string $html
 * @return string
 */
function coptrz_inline_html($html)
{
    $html = trim((string) $html);
    if ($html === '') {
        return '';
    }

    $parsed = coptrz_parse_html_fragment($html);
    if ($parsed === null) {
        return wp_strip_all_tags($html);
    }
    list($doc, $root) = $parsed;

    static $inline_tags = array('a', 'strong', 'b', 'em', 'i', 'span', 'sub', 'sup', 'u', 's', 'mark', 'code', 'abbr');

    $flatten = function ($node) use (&$flatten, $doc, $inline_tags) {
        $out = '';
        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMText) {
                $out .= $doc->saveHTML($child);
                continue;
            }
            if (!($child instanceof DOMElement)) {
                continue;
            }
            $tag = strtolower($child->tagName);
            if ($tag === 'br') {
                $out .= '<br>';
                continue;
            }
            if (in_array($tag, $inline_tags, true)) {
                $out .= $doc->saveHTML($child);
                continue;
            }
            // Block-level (or unrecognised) element → unwrap, keep its content.
            $out .= $flatten($child);
        }
        return $out;
    };

    return trim($flatten($root));
}

/**
 * Convert one top-level DOM node into a parsed-block array. Returns null for
 * nodes with nothing to emit (blank text, empty tags).
 *
 * @param DOMDocument $doc
 * @param DOMNode     $node
 * @return array|null
 */
function coptrz_html_node_to_block($doc, $node)
{
    if ($node instanceof DOMText) {
        $text = trim($node->wholeText);
        return $text === '' ? null : coptrz_block('core/paragraph', array(), '<p>' . esc_html($text) . '</p>');
    }

    if (!($node instanceof DOMElement)) {
        return null;
    }

    $tag = strtolower($node->tagName);

    if ($tag === 'p') {
        $inner = trim(coptrz_dom_inner_html($doc, $node));
        return $inner === '' ? null : coptrz_block('core/paragraph', array(), '<p>' . $inner . '</p>');
    }

    if (preg_match('/^h([1-6])$/', $tag, $m)) {
        $level = (int) $m[1];
        $inner = trim(coptrz_inline_html(coptrz_dom_inner_html($doc, $node)));
        if ($inner === '') {
            return null;
        }
        return coptrz_block(
            'core/heading',
            array('level' => $level),
            '<h' . $level . ' class="wp-block-heading">' . $inner . '</h' . $level . '>'
        );
    }

    if ($tag === 'ul' || $tag === 'ol') {
        $ordered = ($tag === 'ol');
        $items = array();
        foreach ($node->childNodes as $child) {
            if (!($child instanceof DOMElement) || strtolower($child->tagName) !== 'li') {
                continue;
            }
            $li_inner = trim(coptrz_inline_html(coptrz_dom_inner_html($doc, $child)));
            if ($li_inner === '') {
                continue;
            }
            $items[] = coptrz_block('core/list-item', array(), '<li>' . $li_inner . '</li>');
        }
        if (empty($items)) {
            return null;
        }
        // Preserve a custom class carried on the source <ul>/<ol> (Carbon's
        // rich-text editor lets authors add one — e.g. "styled-cheklist" on the
        // DJI Care Enterprise lists this fixes) so converting doesn't silently
        // drop styling. `className` is a universal block-support attribute, so
        // this needs no ddCustomCSS/render_block whitelisting (core/list isn't
        // on that whitelist).
        $extra_class = trim((string) $node->getAttribute('class'));
        $attrs    = $ordered ? array('ordered' => true) : array();
        $list_cls = trim('wp-block-list ' . $extra_class);
        if ($extra_class !== '') {
            $attrs['className'] = $extra_class;
        }
        $tag_name  = $ordered ? 'ol' : 'ul';
        $tag_open  = '<' . $tag_name . ' class="' . esc_attr($list_cls) . '">';
        $tag_close = '</' . $tag_name . '>';
        return coptrz_block_container('core/list', $attrs, $tag_open, $tag_close, $items);
    }

    // Anything else (table, blockquote, figure, stray div, …) → a lossless
    // snapshot of just THIS node, so the rest of the description can still go
    // native — mirrors the whole-section Custom HTML fallback, one level down.
    $outer = $doc->saveHTML($node);
    return $outer === '' ? null : coptrz_block('core/html', array(), $outer);
}

/**
 * Convert an HTML fragment (already wpautop()'d / wp_kses_post()'d) into an
 * array of parsed-block arrays, one per top-level node. Falls back to a
 * single Custom HTML block if the fragment can't be parsed at all (DOM
 * extension missing, or genuinely empty after parsing) — always valid, just
 * not native, same fallback the whole-section snapshot already relies on.
 *
 * @param string $html
 * @return array
 */
function coptrz_html_to_blocks($html)
{
    $html = trim((string) $html);
    if ($html === '') {
        return array();
    }

    $parsed = coptrz_parse_html_fragment($html);
    if ($parsed === null) {
        return array(coptrz_block('core/html', array(), $html));
    }
    list($doc, $root) = $parsed;

    $blocks = array();
    foreach ($root->childNodes as $node) {
        $block = coptrz_html_node_to_block($doc, $node);
        if ($block !== null) {
            $blocks[] = $block;
        }
    }

    return $blocks ?: array(coptrz_block('core/html', array(), $html));
}

/**
 * Convert a single Bootstrap column class (e.g. `col-lg-6`, `col-md`, `col`,
 * `col-auto`) into its span on a 12-track CSS grid, for the `columns` item
 * mapper's grid conversion (coptrz_block_item_mappers()). A numbered class
 * (`col-{bp}-{n}` or bare `col-{n}`) maps to `n` directly; an un-numbered
 * class (`col`, `col-{bp}`, `col-auto`, or empty — Bootstrap's auto/equal-fill
 * column) maps to an even share of the row (`12 / $col_count`, rounded),
 * matching how those columns actually render side by side in the legacy
 * Bootstrap `.row`.
 *
 * @param string $class     e.g. 'col-lg-6', or '' if unset
 * @param int    $col_count number of columns in the row (for the auto-fill fallback)
 * @return int 1-12
 */
function coptrz_bootstrap_col_span($class, $col_count)
{
    $class = trim((string) $class);
    if ($class !== '' && preg_match('/^col(?:-(?:sm|md|lg|xl|xxl))?-(\d+)$/', $class, $m)) {
        return max(1, min(12, (int) $m[1]));
    }
    $col_count = max(1, (int) $col_count);
    return max(1, min(12, (int) round(12 / $col_count)));
}

/**
 * Derive a column's per-breakpoint Bootstrap width classes (converted to grid
 * spans by the caller — see coptrz_bootstrap_col_span()) and its inner
 * `.column-holder` styling, from its `column_styles` complex, mirroring the
 * per-column switch in
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
 * @return array{column_widths:array{desktop:string,tablet:string,mobile:string},holder_classes:string[],holder_css:string[]}
 */
function coptrz_column_wrapper_data($col, array $shared_styles, $individual_column_settings, $mobile_styling, $same_image_height = false, $image_fit = '', $image_padding = '')
{
    $column_widths  = array('desktop' => '', 'tablet' => '', 'mobile' => '');
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
                if (!empty($style['column_width'])) {
                    $column_widths['desktop'] = $style['column_width'];
                }
                if (!empty($style['column_width_tablet'])) {
                    $column_widths['tablet'] = $style['column_width_tablet'];
                }
                if (!empty($style['column_width_mobile'])) {
                    $column_widths['mobile'] = $style['column_width_mobile'];
                }
                break;
        }
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
        'column_widths'   => $column_widths,
        'holder_classes'  => $dedupe($holder_classes),
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
 *     coptrz/* block) so they stay live/editable. tabs/accordion/icon/spec_box/
 *     divider/cf7 map to the theme's own dynamic coptrz/*-legacy blocks
 *     (save:null, rendered server-side by includes/legacy-blocks.php) — used for
 *     element types with theme-specific markup no core block reproduces.
 *   - Self-hosted video maps to the native core/video block (byte-parity notes
 *     in the mapper itself, since core/video is a static block); YouTube video
 *     maps to core/html (core's oEmbed-based YouTube embed can't reproduce the
 *     theme's custom autoplay/loop/mute iframe params).
 *   - related_post / related_products currently have no mapper → any section
 *     containing one snapshots to Custom HTML.
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

    // Heading → core/heading. $text is normalised to INLINE-only markup via
    // coptrz_inline_html() before being wrapped in the single <hN> save()
    // produces — a stray block-level element in the Carbon field (a <p>, a
    // pasted <div>) would otherwise nest inside the heading tag the same way
    // it broke core/paragraph below, so it's unwrapped here rather than left
    // as a latent validation risk.
    $map['heading'] = function ($item) {
        $tag   = strtolower(isset($item['tag']) ? (string) $item['tag'] : 'h2');
        $level = (int) filter_var($tag, FILTER_SANITIZE_NUMBER_INT) ?: 2;
        $text  = isset($item['heading']) ? (string) $item['heading'] : '';
        if ($text === '') {
            return null;
        }
        $inner = trim(coptrz_inline_html(wp_kses_post($text)));
        if ($inner === '') {
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
        $html = '<h' . $level . ' class="' . esc_attr($cls) . '">' . $inner . '</h' . $level . '>';
        return array(coptrz_block('core/heading', $attrs, $html));
    };

    // Description → core/paragraph for a single plain paragraph (the common
    // case, and the shape that already validates), or core/group.description-box
    // (matching __description()'s own <div class="description-box">, modules.php)
    // containing native inner blocks when the source is richer than one
    // paragraph — multiple paragraphs, a list, a heading. Previously this always
    // hand-wrapped the raw field value in a SINGLE <p class="description-box">,
    // which produced invalid nested markup (<p><p>…</p></p>, or <p><ul>…</ul></p>)
    // whenever the Carbon value already contained block-level HTML — see the
    // "Rich-text HTML → native blocks" section above for how the split is done.
    $map['description'] = function ($item) {
        $text = isset($item['description']) ? (string) $item['description'] : '';
        if ($text === '') {
            return null;
        }

        $inner_blocks = coptrz_html_to_blocks(wpautop(wp_kses_post($text)));
        if (empty($inner_blocks)) {
            return null;
        }

        $extra = array();
        if (!empty($item['description_alignment'])) {
            $extra[] = $item['description_alignment'];
        }
        if (!empty($item['description_size'])) {
            $extra[] = $item['description_size'];
        }
        $cls = trim('description-box ' . implode(' ', $extra));

        // ddCustomCSS is this theme's existing per-block Custom CSS mechanism
        // (functions.php, digitally_disruptive_render_custom_css(), already
        // whitelisted for core/group and core/paragraph) — used here instead of
        // a hand-serialized `style` attribute, which would risk a future
        // save()-mismatch the same way the raw inline `style=""` this replaces
        // already did (that attribute has no equivalent in either block's
        // supported attribute schema).
        $custom_css = !empty($item['description_width'])
            ? 'max-width: ' . trim((string) $item['description_width']) . ';'
            : '';

        // Simple case: exactly one paragraph → keep the flat core/paragraph
        // shape (byte-identical to what already validates on plain-text cards).
        if (count($inner_blocks) === 1 && $inner_blocks[0]['blockName'] === 'core/paragraph') {
            $attrs = array('className' => $cls);
            if ($custom_css !== '') {
                $attrs['ddCustomCSS'] = $custom_css;
            }
            // Re-tag the parsed paragraph's own <p> with the description-box
            // class list. Safe string surgery: coptrz_html_node_to_block()
            // (just above) is the sole producer of this innerHTML and always
            // emits exactly '<p>' . $inner . '</p>' with no attributes of its own.
            $p_inner = preg_replace('/^<p>(.*)<\/p>$/s', '$1', $inner_blocks[0]['innerHTML']);
            $html    = '<p class="' . esc_attr($cls) . '">' . $p_inner . '</p>';
            return array(coptrz_block('core/paragraph', $attrs, $html));
        }

        // Rich case: wrap the native inner blocks in the same .description-box
        // <div> __description() itself renders.
        $attrs = array('className' => $cls, 'layout' => array('type' => 'default'));
        if ($custom_css !== '') {
            $attrs['ddCustomCSS'] = $custom_css;
        }
        $div_cls = trim('wp-block-group ' . $cls);
        return array(coptrz_block_container(
            'core/group',
            $attrs,
            '<div class="' . esc_attr($div_cls) . '">',
            '</div>',
            $inner_blocks
        ));
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

    // Self-hosted video → native core/video (a real, editable block-editor Video
    // widget, per request — previously this mapped to core/html). YouTube stays
    // core/html: core's own YouTube embed goes through oEmbed and can't
    // reproduce the theme's custom autoplay/loop/mute iframe params, and there
    // are no YouTube items in the sections this was written for.
    //
    // core/video BYTE-PARITY (it's a static block — unlike core/html, its
    // stored markup must match save() exactly or this reintroduces "invalid
    // content"). Per wp-includes/blocks/video/block.json: `autoplay`, `controls`
    // (default true), `loop`, `muted`, `poster`, `preload` (default 'metadata'),
    // `src`, `playsInline` all have `"source":"attribute"` — save() reads them
    // FROM the <video> tag's own HTML attributes, so none of them belong in the
    // block's JSON attrs (only `id`/`className`, which have no `source`, do —
    // same split the existing `image` mapper above already relies on for
    // core/image's `url`/`alt` vs `id`/`sizeSlug`). save()'s JSX prop order is
    // autoPlay, controls, loop, muted, poster, preload, src, playsInline; boolean
    // props serialize as a bare attribute when true and are omitted when false
    // (poster/preload/playsInline are always empty/default here, so always
    // omitted) — giving `<video autoplay loop muted src="…">` when autoplay, or
    // `<video controls src="…">` otherwise, exactly mirroring __video()'s own
    // autoplay-loop-muted vs controls-only split (elements.php).
    //
    // Class list: modules.php has TWO `case 'video':` renderers with different
    // classes — the top-level section-item switch (~L1005-1017, no rounded-corner)
    // vs the per-column-item switch inside ____columns_modules() (~L2654-2666,
    // always 'video-box rounded-corner overflow-hidden'). This registry has no
    // way to tell which context called it (both the top-level dispatch and the
    // `columns` mapper above call every mapper the same way), so this matches
    // the COLUMN-context classes — that's the shape a video-in-a-feature-card
    // actually needs (the concrete case that forced whole-section snapshots).
    // A bare top-level `video` item (not inside `columns`) would gain
    // 'rounded-corner overflow-hidden' it didn't have before — a minor visual
    // difference (rounded/clipped corners), not a content bug; flag via the
    // 'coptrz_block_item_mappers' filter if that turns out to matter somewhere.
    $map['video'] = function ($item) {
        $video_type = isset($item['video_type']) ? (string) $item['video_type'] : '';
        $autoplay   = !empty($item['autoplay']);
        $class      = 'video-box rounded-corner overflow-hidden' . ($video_type !== '' ? ' ' . $video_type : '');

        if ($video_type === 'youtube') {
            $youtube_id = isset($item['youtube_video_id']) ? (string) $item['youtube_video_id'] : '';
            if ($youtube_id === '') {
                return null;
            }
            $parameters = $autoplay
                ? '?loop=1&controls=0&rel=0&playsinline=1&autoplay=1&mute=1&controls=0&playlist=' . $youtube_id
                : '';
            $src  = 'https://www.youtube.com/embed/' . $youtube_id . $parameters;
            $html = '<div class="' . esc_attr($class) . '"><iframe src="' . esc_url($src) . '"></iframe></div>';
            return array(coptrz_block('core/html', array(), $html));
        }

        $video_id = (int) (isset($item['video']) ? $item['video'] : 0);
        if (!$video_id) {
            return null;
        }
        $video_url = wp_get_attachment_url($video_id);
        if (!$video_url) {
            return null;
        }
        $bool_attrs = $autoplay ? 'autoplay loop muted' : 'controls';
        $fig_cls    = trim('wp-block-video ' . $class);
        $attrs      = array('id' => $video_id, 'className' => $class);
        $html       = '<figure class="' . esc_attr($fig_cls) . '">'
            . '<video ' . $bool_attrs . ' src="' . esc_url($video_url) . '"></video>'
            . '</figure>';
        return array(coptrz_block('core/video', $attrs, $html));
    };

    // Icon → coptrz/icon-legacy. _____icon_modules() (modules.php) inlines the
    // selected SVG file's contents into a `.icon-box` wrapper (colour/size via
    // CSS custom properties) — no core block reproduces that (core/image would
    // emit an <img>, losing the inline-SVG recolouring), so this uses the same
    // dynamic legacy-wrapper pattern (save:null, rendered server-side) as
    // coptrz/accordion-legacy etc. above. `iconUrl` is editor-preview-only —
    // the render callback re-derives the SVG from `iconId`, not from this.
    $map['icon'] = function ($item) {
        $icon_id = (int) (isset($item['icon']) ? $item['icon'] : 0);
        if (!$icon_id) {
            return null;
        }
        $icon_url = wp_get_attachment_url($icon_id);
        $attrs = coptrz_json_safe_array(array(
            'iconId'          => $icon_id,
            'iconUrl'         => $icon_url ? $icon_url : '',
            'iconColor'       => isset($item['icon_color']) ? (string) $item['icon_color'] : '',
            'iconColorCustom' => isset($item['icon_color_custom']) ? (string) $item['icon_color_custom'] : '',
            'iconWidth'       => isset($item['icon_width']) ? (string) $item['icon_width'] : '',
            'iconHeight'      => isset($item['icon_height']) ? (string) $item['icon_height'] : '',
        ));
        if ($attrs === null) {
            return null;
        }
        return array(coptrz_block('coptrz/icon-legacy', $attrs));
    };

    // Spec Box → coptrz/spec-box-legacy. __spec_box_module() (modules.php) emits
    // a Bootstrap row of label/value spec cells with theme-specific classing —
    // no core block equivalent.
    $map['spec_box'] = function ($item) {
        $rows  = isset($item['spec_box']) && is_array($item['spec_box']) ? $item['spec_box'] : array();
        $specs = array();
        foreach ($rows as $row) {
            $specs[] = array(
                'label' => isset($row['spec_label']) ? (string) $row['spec_label'] : '',
                'value' => isset($row['spec_value']) ? (string) $row['spec_value'] : '',
            );
        }
        if (empty($specs)) {
            return null;
        }
        $attrs = coptrz_json_safe_array(array('specs' => $specs));
        if ($attrs === null) {
            return null;
        }
        return array(coptrz_block('coptrz/spec-box-legacy', $attrs));
    };

    // Divider → coptrz/divider-legacy. __divider_module() (modules.php) emits a
    // plain <hr> with margin-utility + border-color classes; `border_color_custom`/
    // `border_width` are Carbon fields the renderer never reads, so they have no
    // attribute here either.
    $map['divider'] = function ($item) {
        $attrs = coptrz_json_safe_array(array(
            'marginTop'    => isset($item['margin_top']) ? (string) $item['margin_top'] : '',
            'marginBottom' => isset($item['margin_bottom']) ? (string) $item['margin_bottom'] : '',
            'marginLeft'   => isset($item['margin_left']) ? (string) $item['margin_left'] : '',
            'marginRight'  => isset($item['margin_right']) ? (string) $item['margin_right'] : '',
            'borderColor'  => isset($item['border_color']) ? (string) $item['border_color'] : '',
        ));
        if ($attrs === null) {
            return null;
        }
        return array(coptrz_block('coptrz/divider-legacy', $attrs));
    };

    // Cf7 → coptrz/cf7-legacy. __cf7_module() (modules.php) wraps a
    // [contact-form-7 id='…'] embed in a `.form-box $style` div; `formId` is
    // the CF7 form's POST ID (the Carbon `association` field's id), not the
    // unit-tag hash.
    $map['cf7'] = function ($item) {
        $form_id = isset($item['form'][0]['id']) ? (int) $item['form'][0]['id'] : 0;
        if (!$form_id) {
            return null;
        }
        $attrs = coptrz_json_safe_array(array(
            'formId'    => $form_id,
            'formTitle' => (string) get_the_title($form_id),
            'style'     => isset($item['style']) ? (string) $item['style'] : '',
        ));
        if ($attrs === null) {
            return null;
        }
        return array(coptrz_block('coptrz/cf7-legacy', $attrs));
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

    // Gallery → coptrz/gallery, flattened typed attributes (not an opaque
    // blob) so the block is genuinely editable — see coptrz-gallery-block.js /
    // coptrz_render_gallery_block() (includes/legacy-blocks.php). Spacing
    // values are stored as bare numbers (registry: coptrz_legacy_block_field_
    // options()) so the gx-/gy- prefix is stripped here and re-added at render.
    $map['gallery'] = function ($item) {
        if (empty($item['gallery']) || !is_array($item['gallery'])) {
            return null;
        }
        $strip_prefix = function ($v, $prefix) {
            $v = (string) $v;
            return strpos($v, $prefix) === 0 ? substr($v, strlen($prefix)) : $v;
        };
        $attrs = coptrz_json_safe_array(array(
            'galleryIds'          => array_map('intval', $item['gallery']),
            'galleryStyle'        => isset($item['gallery_style']) ? (string) $item['gallery_style'] : 'grid',
            'numberOfSlides'       => isset($item['number_of_slides']) ? (string) $item['number_of_slides'] : '',
            'numberOfSlidesTablet' => isset($item['number_of_slides_tablet']) ? (string) $item['number_of_slides_tablet'] : '',
            'numberOfSlidesMobile' => isset($item['number_of_slides_mobile']) ? (string) $item['number_of_slides_mobile'] : '',
            'columnWidth'          => isset($item['column_width']) ? (string) $item['column_width'] : 'col-lg',
            'columnWidthTablet'    => isset($item['column_width_tablet']) ? (string) $item['column_width_tablet'] : '',
            'columnWidthMobile'    => isset($item['column_width_mobile']) ? (string) $item['column_width_mobile'] : '',
            'horizontalSpacing'    => $strip_prefix(isset($item['horizontal_spacing']) ? $item['horizontal_spacing'] : '', 'gx-'),
            'verticalSpacing'      => $strip_prefix(isset($item['vertical_spacing']) ? $item['vertical_spacing'] : '', 'gy-'),
            'sameImageHeight'      => !empty($item['same_image_height']),
        ));
        if ($attrs === null) {
            return null;
        }
        return array(coptrz_block('coptrz/gallery', $attrs));
    };

    // Resolves a list of Carbon association rows ({id, …}) into the
    // [{id, title}, …] shape IdTokenPicker/SinglePostPicker expect. $resolver
    // is called with the id and must return a display title.
    $resolve_picker_ids = function ($rows, $resolver) {
        $out = array();
        foreach ((array) $rows as $row) {
            if (empty($row['id'])) {
                continue;
            }
            $id = (int) $row['id'];
            $out[] = array('id' => $id, 'title' => (string) call_user_func($resolver, $id));
        }
        return $out;
    };
    $resolve_term_title = function ($id, $taxonomy) {
        $term = get_term($id, $taxonomy);
        return ($term && !is_wp_error($term)) ? $term->name : '';
    };

    // Product Slider → coptrz/product-slider. Stores SOURCE FIELDS, not a
    // frozen query — see coptrz_render_product_slider_block() in
    // includes/legacy-blocks.php for why (the `main_query` source depends on
    // the live request).
    $map['product_slider'] = function ($item) use ($resolve_picker_ids, $resolve_term_title) {
        $source_type = isset($item['source_type']) ? (string) $item['source_type'] : '';
        $category_ids = $resolve_picker_ids(
            !empty($item['source']) && is_array($item['source']) ? $item['source'] : array(),
            function ($id) use ($resolve_term_title) { return $resolve_term_title($id, 'product_cat'); }
        );
        $brand_ids = $resolve_picker_ids(
            !empty($item['brand']) && is_array($item['brand']) ? $item['brand'] : array(),
            function ($id) use ($resolve_term_title) { return $resolve_term_title($id, 'pa_brands'); }
        );
        $product_ids = $resolve_picker_ids(
            !empty($item['products']) && is_array($item['products']) ? $item['products'] : array(),
            function ($id) { return get_the_title($id); }
        );
        $attrs = coptrz_json_safe_array(array(
            'sourceType'  => $source_type,
            'categoryIds' => $category_ids,
            'brandIds'    => $brand_ids,
            'productIds'  => $product_ids,
            'numberposts' => isset($item['numberposts']) ? (string) $item['numberposts'] : '',
            'heading'     => isset($item['heading']) ? (string) $item['heading'] : '',
            'buttonText'  => isset($item['button_text']) ? (string) $item['button_text'] : '',
            'buttonUrl'   => isset($item['button_url']) ? (string) $item['button_url'] : '',
        ));
        if ($attrs === null) {
            return null;
        }
        return array(coptrz_block('coptrz/product-slider', $attrs));
    };

    // Tabs (Bootstrap nav-tabs) → coptrz/tabs-legacy. Distinct from the native
    // dd/tabs block — this is a frozen-renderer wrapper, not a conversion onto
    // it. Descriptions are wpautop()'d ONCE here — the block's RichText editor
    // stores real HTML directly (rendered with autop DISABLED, see
    // coptrz_render_tabs_legacy_block()), so a legacy textarea's bare newlines
    // must become real <p> tags now or they're lost forever.
    $map['tabs'] = function ($item) {
        if (empty($item['tabs']) || !is_array($item['tabs'])) {
            return null;
        }
        $tabs = array();
        foreach ($item['tabs'] as $tab) {
            $tabs[] = array(
                'heading'     => isset($tab['heading']) ? (string) $tab['heading'] : '',
                'description' => wpautop(isset($tab['description']) ? (string) $tab['description'] : ''),
            );
        }
        $attrs = coptrz_json_safe_array(array('tabs' => $tabs));
        if ($attrs === null) {
            return null;
        }
        return array(coptrz_block('coptrz/tabs-legacy', $attrs));
    };

    // Accordion → coptrz/accordion-legacy. Distinct from core/details — this is
    // a frozen-renderer wrapper that keeps the FAQs-by-selection / FAQs-by-
    // category dynamic sourcing intact. Custom-source descriptions are
    // wpautop()'d ONCE here, same rationale as `tabs` above.
    $map['accordion'] = function ($item) use ($resolve_picker_ids, $resolve_term_title) {
        $items = array();
        if (!empty($item['accordion']) && is_array($item['accordion'])) {
            foreach ($item['accordion'] as $row) {
                $items[] = array(
                    'heading'     => isset($row['heading']) ? (string) $row['heading'] : '',
                    'description' => wpautop(isset($row['description']) ? (string) $row['description'] : ''),
                );
            }
        }
        $faqs = $resolve_picker_ids(
            !empty($item['faqs']) && is_array($item['faqs']) ? $item['faqs'] : array(),
            function ($id) { return get_the_title($id); }
        );
        $faqs_category = $resolve_picker_ids(
            !empty($item['faqs_category']) && is_array($item['faqs_category']) ? $item['faqs_category'] : array(),
            function ($id) use ($resolve_term_title) { return $resolve_term_title($id, 'faqs_category'); }
        );
        $attrs = coptrz_json_safe_array(array(
            'items'         => $items,
            'source'        => isset($item['accordion_source']) ? (string) $item['accordion_source'] : '',
            'faqs'          => $faqs,
            'faqsCategory'  => $faqs_category,
            'openFirstItem' => !empty($item['open_first_item']),
            'withBorder'    => !empty($item['with_border']),
            'lowerOpacity'  => !empty($item['lower_opacity']),
        ));
        if ($attrs === null) {
            return null;
        }
        return array(coptrz_block('coptrz/accordion-legacy', $attrs));
    };

    // Drone Servicing Grid → coptrz/drone-servicing-grid. Each drone's FIXED
    // four specs (drone/battery/controller/payload — post-meta.php's
    // `set_duplicate_groups_allowed(false)`) become an `enabled`+`quantity` map;
    // __drone_servicing() (woocommerce.php) treats a spec's ABSENCE from
    // service_features (not an empty value) as "not available", so `enabled`
    // must reflect presence, not truthiness of quantity.
    $map['drone_servicing_grid'] = function ($item) {
        if (empty($item['servicing_drones']) || !is_array($item['servicing_drones'])) {
            return null;
        }
        $spec_types = array('drone', 'battery', 'controller', 'payload');
        $drones = array();
        foreach ($item['servicing_drones'] as $row) {
            $features = array();
            foreach ($spec_types as $type) {
                $features[$type] = array('enabled' => false, 'quantity' => '');
            }
            if (!empty($row['service_features']) && is_array($row['service_features'])) {
                foreach ($row['service_features'] as $feature) {
                    $type = isset($feature['_type']) ? (string) $feature['_type'] : '';
                    if (isset($features[$type])) {
                        $features[$type] = array(
                            'enabled'  => true,
                            'quantity' => isset($feature['quantity']) ? $feature['quantity'] : '',
                        );
                    }
                }
            }
            $drones[] = array(
                'serviceName'       => isset($row['service_name']) ? (string) $row['service_name'] : '',
                'serviceSubheading' => isset($row['service_subheading']) ? (string) $row['service_subheading'] : '',
                'servicePrice'      => isset($row['service_price']) ? (string) $row['service_price'] : '',
                'features'          => $features,
            );
        }
        $attrs = coptrz_json_safe_array(array(
            'heading'     => isset($item['servicing_heading']) ? (string) $item['servicing_heading'] : '',
            'description' => isset($item['servicing_description']) ? (string) $item['servicing_description'] : '',
            'drones'      => $drones,
        ));
        if ($attrs === null) {
            return null;
        }
        return array(coptrz_block('coptrz/drone-servicing-grid', $attrs));
    };

    // Events Widget → coptrz/events-widget.
    $map['events_widget'] = function ($item) {
        $has_countdown = false;
        if (!empty($item['events_widget']) && is_array($item['events_widget'])) {
            foreach ($item['events_widget'] as $sub) {
                if (isset($sub['_type']) && $sub['_type'] === 'countdown') {
                    $has_countdown = true;
                    break;
                }
            }
        }
        if (!$has_countdown) {
            return null;
        }
        return array(coptrz_block('coptrz/events-widget', array('showCountdown' => true)));
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
    // stay deletable/reorderable/re-configurable in the editor. Column-width is
    // now a per-box EDITABLE field on each child block, not a class string
    // frozen at conversion time — the legacy 3-posts/col-md-6 special case
    // (modules.php ~L1081-1093) is applied ONCE here, to seed the initial
    // columnWidthTablet value every box gets, since it depends on the whole
    // selection's post COUNT, not something a single box can know on its own.
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
        if ($column_width_tablet && count($manual_posts) == 3 && $column_width_tablet == 'col-md-6') {
            $column_width_tablet = 'col-md-12';
        }

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
                'postId'            => (int) $pid,
                'postTitle'         => (string) get_the_title($pid),
                'columnWidth'       => $column_width,
                'columnWidthTablet' => $column_width_tablet,
                'columnWidthMobile' => $column_width_mobile,
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

    // Columns → when every column shares the SAME Bootstrap width at every
    // breakpoint, a CSS Grid: an outer core/group (layout type 'grid') whose
    // columnCount is 12 / that shared span (col-lg-4 on every column →
    // columnCount 3), with plain core/group cells (no col-* class, no
    // per-column CSS) for columns. col-md-* / mobile col-* drive
    // ddGridColumnsTablet / ddGridColumnsMobile — the theme's EXISTING
    // per-breakpoint grid-count override (dd_group_grid_responsive_render(),
    // functions.php, already wired for any core/group with layout.type ===
    // 'grid'), so no new render plumbing is needed. A row whose columns do
    // NOT all share the same width at every breakpoint falls back to the
    // original core/columns + core/column[] output (each column keeps its
    // own col-* className) — a single columnCount can't represent unequal
    // columns. Each column's items recurse through this same registry; if
    // any sub-item can't be mapped the whole section falls back to a Custom
    // HTML snapshot.
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
        $col_count = count($raw_cols);

        $individual_column_settings = !empty($item['individual_column_settings']);
        $shared_styles = (!$individual_column_settings && !empty($item['column_styles']) && is_array($item['column_styles']))
            ? $item['column_styles']
            : array();
        $mobile_styling = isset($item['mobile_styling']) ? (string) $item['mobile_styling'] : '';

        // Gather pass: recurse each column's items and derive its wrapper
        // data once, shared by both branches below.
        $columns = array();
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

            // Per-column styling (.column-holder) is orthogonal to the width
            // uniformity check below — it may legitimately differ between
            // columns even when every column shares the same grid width.
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

            $columns[] = array(
                'inner'  => $inner,
                'widths' => $col_data['column_widths'],
            );
        }

        if (empty($columns)) {
            return null;
        }

        // Row-level wrapper classes, matching the non-slider $row_class build
        // at modules.php:2021-2050 (minus the raw 'row'/'g-4'/'g-xs-10px'
        // bootstrap grid/gutter classes, which assume a `.row` flex context
        // neither branch below needs — block gap / the grid handle gutters).
        $row_classes = array();
        foreach (array('align_items', 'justify_content', 'horizontal_spacing', 'vertical_spacing') as $k) {
            if (!empty($item[$k])) {
                $row_classes[] = (string) $item[$k];
            }
        }
        $align = implode(' ', $row_classes);

        // Uniform only when every column shares the exact same desktop /
        // tablet / mobile width triple — that's the only case where a single
        // grid columnCount per breakpoint faithfully represents the row.
        $signatures = array();
        foreach ($columns as $c) {
            $w = $c['widths'];
            $signatures[] = $w['desktop'] . '|' . $w['tablet'] . '|' . $w['mobile'];
        }
        $uniform = count(array_unique($signatures)) === 1;

        if ($uniform) {
            $widths = $columns[0]['widths'];
            $span_to_count = function ($width) use ($col_count) {
                $span = coptrz_bootstrap_col_span($width, $col_count);
                return max(1, min(12, (int) round(12 / $span)));
            };

            $cols_attrs = array('layout' => array(
                'type'        => 'grid',
                'columnCount' => $span_to_count($widths['desktop']),
            ));
            if ($widths['tablet'] !== '') {
                $cols_attrs['ddGridColumnsTablet'] = (string) $span_to_count($widths['tablet']);
            }
            if ($widths['mobile'] !== '') {
                $cols_attrs['ddGridColumnsMobile'] = (string) $span_to_count($widths['mobile']);
            }
            if ($align !== '') {
                $cols_attrs['className'] = $align;
            }
            $wrap_cls = trim('wp-block-group ' . $align);

            $col_blocks = array();
            foreach ($columns as $c) {
                $col_blocks[] = coptrz_block_container(
                    'core/group',
                    array('layout' => array('type' => 'default')),
                    '<div class="wp-block-group">',
                    '</div>',
                    $c['inner']
                );
            }

            return array(coptrz_block_container(
                'core/group',
                $cols_attrs,
                '<div class="' . esc_attr($wrap_cls) . '">',
                '</div>',
                $col_blocks
            ));
        }

        // Mixed widths → the original core/columns + core/column[] output,
        // each column keeping its own Bootstrap width class(es).
        $col_blocks = array();
        foreach ($columns as $c) {
            $widths      = $c['widths'];
            $col_classes = array_values(array_filter(
                array($widths['desktop'], $widths['tablet'], $widths['mobile']),
                function ($v) {
                    return trim((string) $v) !== '';
                }
            ));
            if (empty($col_classes)) {
                $col_classes[] = 'col';
            }
            $col_class_attr = implode(' ', $col_classes);
            $col_wrap_cls   = trim('wp-block-column ' . $col_class_attr);

            $col_blocks[] = coptrz_block_container(
                'core/column',
                array('className' => $col_class_attr),
                '<div class="' . esc_attr($col_wrap_cls) . '">',
                '</div>',
                $c['inner']
            );
        }

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
 * Convert one post to blocks — whichever of sections and hero actually apply
 * to it (see coptrz_post_conversion_state() for the applicability rules; a
 * post with neither is reported skipped). Each part is independent and
 * independently skippable, so re-running this on a post that already has one
 * part converted only attempts the other.
 *
 * Sections: emits per-element blocks appended to post_content (mirrors the
 * older HTML-repeater path, coptrz_convert_post_sections(), kept only for
 * products still on `mode = 'html'`). Sets COPTRZ_SECTIONS_CONVERTED_FLAG (so
 * ___sections() rendering routes identically — this flag's meaning is
 * unchanged by the hero merge) plus `_coptrz_sections_mode` = 'blocks',
 * preserves `_sections`, and switches `page` posts to the Gutenberg template.
 * Products: refused if post_content (minus any hero block — see
 * coptrz_content_without_hero()) is non-empty, since it isn't rendered
 * anywhere today and conversion would silently publish it. Once accepted,
 * `sections` and `sections_after_main` are joined with a
 * `coptrz/section-split` marker so coptrz_product_content_split() can hand
 * each half back to its own template slot (before/after the buy box).
 *
 * Hero: prepends a `coptrz/hero` block built from the current Hero meta
 * (coptrz_hero_conversion_plan(), includes/hero-converter.php), but ONLY when
 * that block is verified to render identically to the current meta-driven
 * hero first — otherwise this part is skipped (with a warning) and the post
 * keeps rendering its hero from meta, which stays a correct, permanent
 * fallback either way (see ___hero_modules(), modules.php). Sets
 * COPTRZ_HERO_CONVERTED_FLAG — bookkeeping only; nothing at render time reads
 * it, the hero renders off the block's presence in post_content.
 *
 * @param int  $post_id
 * @param bool $dry_run
 * @return array
 */
function coptrz_convert_post_to_blocks($post_id, $dry_run = false)
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
        'hero'     => 'not_applicable',
        'warnings' => array(),
        'skipped'  => false,
    );

    if (!$src) {
        $report['warnings'][] = 'Post not found.';
        $report['skipped'] = true;
        return $report;
    }

    // ---- Hero part: a pure decision, independent of section state ----
    $hero_plan = function_exists('coptrz_hero_conversion_plan')
        ? coptrz_hero_conversion_plan($post_id)
        : array('status' => 'not_applicable', 'markup' => '');
    $report['hero'] = $hero_plan['status'];
    if ($hero_plan['status'] === 'not_identical') {
        $report['warnings'][] = 'Hero: the block would not render identically to the current meta-driven hero — skipped; the page keeps rendering its hero from meta.';
    } elseif ($hero_plan['status'] === 'renderer_missing') {
        $report['warnings'][] = 'Hero: renderer unavailable in this admin context — retry from a normal (non blocks-editor-template) edit screen.';
    }
    $hero_prefix = ($hero_plan['status'] === 'ready') ? $hero_plan['markup'] : '';

    // ---- Sections part: only if not already converted ----
    $sections_already_converted = coptrz_sections_is_converted($post_id);
    $products_refused           = false;
    $blocks_by_field            = array();

    if ($sections_already_converted) {
        $report['warnings'][] = 'Sections already converted — skipped to avoid duplicating content.';
    } else {
        // Products have no rendered use of post_content today (all WooCommerce
        // tabs are disabled — see 'my_remove_all_product_tabs' in
        // woocommerce.php), so a product carrying a leftover description in
        // post_content would silently gain a visible one the moment sections
        // are appended after it. Refuse rather than the generic soft warning
        // below, so that content is dealt with deliberately first. A leading
        // coptrz/hero block (existing, or the one this run is about to
        // prepend) doesn't count as "content" for this check — it's
        // storage-only and renders nowhere in post_content itself
        // (coptrz_render_hero_block(), includes/hero-block.php).
        $content_for_check = function_exists('coptrz_content_without_hero')
            ? coptrz_content_without_hero((string) $src->post_content)
            : (string) $src->post_content;

        if ($src->post_type === 'product' && trim($content_for_check) !== '') {
            $products_refused = true;
            $report['warnings'][] = 'This product has a non-empty description (post_content) that is not rendered anywhere on the front end. Converting would publish it, appended before/after the frozen sections. Clear or relocate it first, then convert.';
        } else {
            global $post;
            $prev_post = $post;
            $post = $src;
            setup_postdata($post);

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

            wp_reset_postdata();
            $post = $prev_post;
        }
    }

    // ---- Assemble: existing content, sections appended, hero prepended ----
    $existing = (string) $src->post_content;
    $is_product = ($src->post_type === 'product');
    $did_sections = !empty($blocks_by_field);

    $body = $existing;
    if ($did_sections) {
        $existing_for_check = function_exists('coptrz_content_without_hero')
            ? coptrz_content_without_hero($existing)
            : $existing;
        if (!$is_product && trim($existing_for_check) !== '') {
            // Products already refused above when post_content (minus hero) is
            // non-empty, so $existing is always '' (or just a hero block) here.
            $report['warnings'][] = 'post_content was not empty — section blocks appended after existing content.';
        }

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
                $body .= $blocks_by_field['sections'] . "\n\n" . serialize_blocks(array(coptrz_block('coptrz/section-split')));
                if (!empty($blocks_by_field['sections_after_main'])) {
                    $body .= "\n\n" . $blocks_by_field['sections_after_main'];
                }
            } elseif (!empty($blocks_by_field['sections_after_main'])) {
                $body .= $blocks_by_field['sections_after_main'];
            }
        } else {
            foreach (coptrz_section_source_fields() as $field) {
                if (!empty($blocks_by_field[$field])) {
                    $body .= ($body !== '' ? "\n\n" : '') . $blocks_by_field[$field];
                }
            }
        }
    }
    // The exact suffix this run added — captured before the hero prefix below
    // so it stays a pure "sections only" value, matching what a later revert
    // needs (see coptrz_revert_post_to_blocks()).
    $sections_suffix = $did_sections ? substr($body, strlen($existing)) : '';

    $did_hero    = ($hero_prefix !== '');
    $new_content = $did_hero ? ($hero_prefix . ($body !== '' ? "\n\n" . $body : '')) : $body;

    if (!$did_hero && !$did_sections) {
        $report['skipped'] = true;
        if (!$sections_already_converted && !$products_refused && empty($report['counts'])) {
            $report['warnings'][] = 'No active sections found to convert.';
        }
        return $report;
    }

    if ($dry_run) {
        $report['preview'] = $new_content;
        return $report;
    }

    // ---- Write: backups first, so a later revert can restore byte-identical
    // content instead of guessing. COPTRZ_PRE_CONVERT_CONTENT is write-once —
    // the FIRST conversion (whichever part triggers it) captures the post's
    // true pre-conversion state; a later run adding the other part must not
    // overwrite it with content that already includes the first part. ----
    // All four writes below (three meta + post_content) are wp_slash()'d:
    // update_post_meta()/wp_update_post() both call wp_unslash() internally
    // (they expect input shaped like $_POST, i.e. already slashed), and
    // $sections_suffix/$hero_prefix/$new_content are serialize_blocks() output
    // full of literal backslash-escapes (< etc — see
    // wp-includes/blocks.php serialize_block_attributes()) that would
    // otherwise be silently stripped, corrupting every escaped character in
    // every block attribute. These four MUST be slashed together: the revert
    // comparison at coptrz_revert_post_to_blocks() reconstructs $expected from
    // the same three meta values and compares it against the live
    // post_content column, so if only post_content were fixed here that
    // comparison would break for every future conversion.
    if (!metadata_exists('post', $post_id, COPTRZ_PRE_CONVERT_CONTENT)) {
        update_post_meta($post_id, COPTRZ_PRE_CONVERT_CONTENT, wp_slash($existing));
    }
    if ($did_sections) {
        update_post_meta($post_id, COPTRZ_CONVERTED_BLOCKS, wp_slash($sections_suffix));
    }
    if ($did_hero && defined('COPTRZ_HERO_BLOCK_PREFIX')) {
        update_post_meta($post_id, COPTRZ_HERO_BLOCK_PREFIX, wp_slash($hero_prefix));
    }
    wp_update_post(array('ID' => $post_id, 'post_content' => wp_slash($new_content)));

    if ($src->post_type === 'page' && $did_sections) {
        // page-blocks-editor.php (not page-gutenberg.php) — pure the_content(),
        // no ___hero_modules() call. Correct for a converted page either way:
        // the hero (if any) now renders INLINE from its block position
        // (coptrz_render_hero_block(), includes/hero-block.php) rather than
        // needing a template-level hero call.
        $report['template'] = 'templates/page-blocks-editor.php';
        update_post_meta($post_id, COPTRZ_PRE_CONVERT_TEMPLATE, (string) get_post_meta($post_id, '_wp_page_template', true));
        update_post_meta($post_id, '_wp_page_template', 'templates/page-blocks-editor.php');
    }

    if ($did_sections) {
        update_post_meta($post_id, COPTRZ_SECTIONS_CONVERTED_FLAG, 'yes');
        update_post_meta($post_id, '_coptrz_sections_mode', 'blocks');
    }
    if ($did_hero && defined('COPTRZ_HERO_CONVERTED_FLAG')) {
        update_post_meta($post_id, COPTRZ_HERO_CONVERTED_FLAG, 'yes');
        $report['hero'] = 'converted';
    }

    return $report;
}

/**
 * Revert a converted post back to the legacy sections builder AND/OR the
 * legacy Hero meta box — whichever parts were actually converted (a post
 * where only one part was ever converted still reverts cleanly; the other
 * part's flag/meta simply isn't present to begin with).
 *
 *  - product, sections mode 'html', no hero -> post_content/template were
 *    never touched by conversion; just clears the sections_html /
 *    sections_after_main_html repeater.
 *  - everything else -> restores post_content from the
 *    COPTRZ_PRE_CONVERT_CONTENT backup when available (this is the state
 *    before EITHER part was ever converted, so it correctly removes both).
 *    Posts converted before this backup existed have none: this regenerates
 *    the section block markup via a dry run and subtracts it as a suffix of
 *    the (hero-stripped) current post_content instead, so any content that
 *    predated conversion survives. If that suffix doesn't match, aborts
 *    rather than guessing. `page` also gets its pre-conversion
 *    _wp_page_template back (falling back to the Modules template when no
 *    backup exists) — only when sections were actually converted.
 *
 * In every case, deletes both converted flags + all backup meta. `_sections` /
 * `_sections_after_main` and the Hero post-meta are never touched by
 * conversion, so both legacy editing surfaces resume immediately once their
 * flag is gone (see coptrz_sections_should_route() and
 * coptrz_hero_block_attrs(), includes/hero-block.php).
 *
 * @param int  $post_id
 * @param bool $dry_run  When true, report what would change but write nothing.
 * @return array Report: warnings, skipped, and (dry-run) the restored content/template.
 */
function coptrz_revert_post_to_blocks($post_id, $dry_run = false)
{
    $src = get_post($post_id);
    $report = array(
        'post_id'  => (int) $post_id,
        'title'    => $src ? $src->post_title : '',
        'type'     => $src ? $src->post_type : '',
        'target'   => 'Legacy sections builder + Hero meta box',
        'counts'   => array(), // kept for shape-compatibility with the bulk results table
        'warnings' => array(),
        'skipped'  => false,
    );

    if (!$src) {
        $report['warnings'][] = 'Post not found.';
        $report['skipped'] = true;
        return $report;
    }

    $sections_converted = coptrz_sections_is_converted($post_id);
    $hero_converted      = function_exists('coptrz_hero_is_converted') && coptrz_hero_is_converted($post_id);

    if (!$sections_converted && !$hero_converted) {
        $report['skipped'] = true;
        $report['warnings'][] = 'Not converted — nothing to revert.';
        return $report;
    }

    // Purge (coptrz_purge_post_legacy_data()) deletes the backups this
    // function restores from, INCLUDING the untouched _sections/Hero meta the
    // no-backup regeneration path below would otherwise fall back to —
    // without this guard that path would silently "succeed" by stripping the
    // hero block and leaving the (now unsourced) converted section blocks in
    // place, discarding content rather than restoring it.
    if (coptrz_post_is_legacy_purged($post_id)) {
        $report['skipped'] = true;
        $report['warnings'][] = 'Legacy data was purged — nothing to revert to.';
        return $report;
    }

    // Only an HTML-mode product with no hero conversion (never touched
    // post_content at all) reverts by just clearing its repeater — a
    // block-mode product, or any post with a hero conversion, falls through
    // to the restore path below, same as everything else.
    $mode = get_post_meta($post_id, '_coptrz_sections_mode', true);
    $is_html_product = ($src->post_type === 'product' && $sections_converted && $mode !== 'blocks' && !$hero_converted);

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
            $sections_suffix  = $sections_converted ? (string) get_post_meta($post_id, COPTRZ_CONVERTED_BLOCKS, true) : '';
            $hero_prefix      = ($hero_converted && defined('COPTRZ_HERO_BLOCK_PREFIX')) ? (string) get_post_meta($post_id, COPTRZ_HERO_BLOCK_PREFIX, true) : '';
            $body             = $backup_content . $sections_suffix;
            $expected         = ($hero_prefix !== '') ? ($hero_prefix . ($body !== '' ? "\n\n" . $body : '')) : $body;
            if ($existing_content !== $expected) {
                $report['warnings'][] = 'Content was edited since conversion — restoring the pre-conversion backup anyway; review the result before republishing.';
            }
            $restored_content = $backup_content;
        } else {
            // No backup (converted before this feature shipped): strip a
            // leading hero block, then regenerate the exact section block
            // markup via a dry run and subtract it as a suffix. The
            // regenerated string never includes the separator joining it to
            // whatever content preceded it (that depends on whether the
            // ORIGINAL content was empty — the very thing being recovered) so
            // try both.
            $without_hero = function_exists('coptrz_content_without_hero')
                ? coptrz_content_without_hero($existing_content)
                : $existing_content;

            $dry = coptrz_convert_post_to_blocks($post_id, true);
            $chunks = array();
            foreach (coptrz_section_source_fields() as $field) {
                if (!empty($dry['html'][$field])) {
                    $chunks[] = implode("\n\n", $dry['html'][$field]);
                }
            }
            $regenerated = implode("\n\n", $chunks);

            $restored_content = null;
            if ($regenerated === '') {
                $restored_content = $without_hero;
            } else {
                foreach (array("\n\n" . $regenerated, $regenerated) as $suffix) {
                    if (substr($without_hero, -strlen($suffix)) === $suffix) {
                        $restored_content = substr($without_hero, 0, strlen($without_hero) - strlen($suffix));
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
            // Same wp_unslash()-on-write gotcha as the convert path above —
            // $restored_content may itself contain backslash-escaped block
            // attribute JSON (e.g. when $regenerated came from a dry-run
            // conversion), so it must be re-slashed before wp_update_post().
            wp_update_post(array('ID' => $post_id, 'post_content' => wp_slash($restored_content)));
        }

        if ($src->post_type === 'page' && $sections_converted) {
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
        delete_post_meta($post_id, COPTRZ_PRE_CONVERT_TEMPLATE);
        delete_post_meta($post_id, COPTRZ_CONVERTED_BLOCKS);
        delete_post_meta($post_id, COPTRZ_SERVE_LEGACY_PUBLIC);
        delete_post_meta($post_id, COPTRZ_PRE_CONVERT_CONTENT);
        if (defined('COPTRZ_HERO_CONVERTED_FLAG')) {
            delete_post_meta($post_id, COPTRZ_HERO_CONVERTED_FLAG);
        }
        if (defined('COPTRZ_HERO_BLOCK_PREFIX')) {
            delete_post_meta($post_id, COPTRZ_HERO_BLOCK_PREFIX);
        }
    }

    return $report;
}

/* ========================================================================= */
/*  Purge — permanently delete legacy data on a converted post                */
/* ========================================================================= */

/**
 * Which of `['sections', 'hero']` a post can still have permanently deleted:
 * converted AND its legacy data hasn't already been purged/emptied. Per-part,
 * same as coptrz_post_conversion_state() — a post with only one part
 * converted can only purge that part.
 *
 * @param int $post_id
 * @return string[]
 */
function coptrz_post_purgeable_parts($post_id)
{
    $post = get_post($post_id);
    if (!$post) {
        return array();
    }

    $parts = array();

    if (in_array($post->post_type, coptrz_section_post_types(), true)
        && coptrz_sections_is_converted($post_id)
        && coptrz_post_has_sections_data($post_id)
    ) {
        $parts[] = 'sections';
    }

    if (function_exists('coptrz_hero_post_types') && function_exists('coptrz_hero_is_converted') && function_exists('coptrz_hero_has_content')
        && in_array($post->post_type, coptrz_hero_post_types(), true)
        && coptrz_hero_is_converted($post_id)
        && coptrz_hero_has_content($post_id)
    ) {
        $parts[] = 'hero';
    }

    return $parts;
}

/**
 * Permanently deletes a converted post's legacy sections/hero data — the
 * Purge action in the per-post box. Irreversible: once a part is purged,
 * Revert, "Preview original", and the logged-out legacy fallback are no
 * longer possible for it — coptrz_revert_post_to_blocks() and
 * coptrz_sections_legacy_override() both check coptrz_post_is_legacy_purged()
 * and refuse/no-op accordingly.
 *
 * Safe to delete because nothing at render time reads it back once converted:
 * ___sections() routes through coptrz_sections_should_route(), which stays
 * true once COPTRZ_SECTIONS_CONVERTED_FLAG is set — a flag this function
 * deliberately does NOT clear, unlike revert. ___hero_modules() and the two
 * dual-readers in hooks.php (action_body_class(), hero_form_redirect()) all
 * prefer coptrz_hero_block_attrs() over meta whenever a coptrz/hero block is
 * present in post_content — true for the hoisted `post` type too, since
 * hoisting only moves WHERE the hero renders, not what it reads from (see
 * includes/hero-block.php's docblock). hide_on_list / cpd_maker / tquk_logo
 * are declared on OTHER containers (post-meta.php), so they're absent from
 * coptrz_hero_meta_field_names() and this function can't touch them even
 * though the block path still reads them from meta.
 *
 * Refuses (skipped, no writes) when:
 *  - nothing is purgeable — coptrz_post_purgeable_parts() is empty, whether
 *    because the post was never converted or because it's already purged
 *    (never bypassable — there is nothing left to delete either way);
 *  - post_content is corrupted (coptrz_content_is_corrupted() — the
 *    wp_slash() bug signature also listed by
 *    coptrz_find_corrupted_conversions()) — purging would destroy the only
 *    repair path, since repair is Revert-then-Convert regenerating fresh
 *    content from this same legacy data. Bypassable via $force: see below.
 *
 * Deliberately does NOT touch: `sections_html` / `sections_after_main_html`
 * (the product HTML-mode repeater — still the live rendering surface when
 * `_coptrz_sections_mode` = 'html', see coptrz_render_converted_sections());
 * COPTRZ_SECTIONS_CONVERTED_FLAG / COPTRZ_HERO_CONVERTED_FLAG /
 * `_coptrz_sections_mode` (these ROUTE rendering — clearing them would send
 * ___sections() / ___hero_modules() back to the now-empty legacy builders);
 * `post_content` itself, force or not — purge NEVER writes post_content.
 *
 * @param int  $post_id
 * @param bool $dry_run  When true, report what would be deleted but write nothing.
 * @param bool $force    When true, purge a corrupted post anyway (see
 *                        coptrz_render_section_converter_box()'s Force Purge
 *                        block for the required UI warning). Deliberately
 *                        DESTROYS the post's only repair path: post_content
 *                        keeps its corrupted `u003c`-style garbage forever,
 *                        since Revert-then-Convert (the only thing that can
 *                        fix it) needs the exact legacy meta this deletes.
 *                        Never touches post_content itself — force only
 *                        widens which posts DELETE_ROOT runs against, never
 *                        what it runs against post_content.
 * @return array Report: post_id, title, type, purged (parts actually purged), warnings, skipped.
 */
function coptrz_purge_post_legacy_data($post_id, $dry_run = false, $force = false)
{
    $src = get_post($post_id);
    $report = array(
        'post_id'  => (int) $post_id,
        'title'    => $src ? $src->post_title : '',
        'type'     => $src ? $src->post_type : '',
        'target'   => 'Legacy sections/hero meta (permanent deletion)',
        'purged'   => array(),
        'warnings' => array(),
        'skipped'  => false,
    );

    if (!$src) {
        $report['warnings'][] = 'Post not found.';
        $report['skipped'] = true;
        return $report;
    }

    $parts = coptrz_post_purgeable_parts($post_id);
    if (empty($parts)) {
        $report['skipped'] = true;
        $report['warnings'][] = coptrz_post_is_legacy_purged($post_id)
            ? 'Already purged — nothing left to delete.'
            : 'Nothing purgeable — this post has no converted part with legacy data still present.';
        return $report;
    }

    if (!$force && coptrz_content_is_corrupted((string) $src->post_content)) {
        $report['skipped'] = true;
        $report['warnings'][] = 'This post is listed under "Corrupted conversions" — purging would remove the only repair path (Revert, then Convert). Fix that first, or force-purge to permanently keep the corrupted content as-is.';
        return $report;
    }

    if ($force) {
        $report['warnings'][] = 'Forced: post_content was left as-is (still corrupted, if it was) — this cannot be repaired afterward, since the legacy data Revert-then-Convert needs is gone.';
    }

    if (in_array('sections', $parts, true)) {
        $report['purged'][] = 'sections';
        if (!$dry_run) {
            foreach (coptrz_section_source_fields() as $field) {
                \CoptrzTheme\MetaShim\Key_Formatter::delete_root('post', $post_id, $field);
            }
            delete_post_meta($post_id, COPTRZ_CONVERTED_BLOCKS);
            delete_post_meta($post_id, COPTRZ_PRE_CONVERT_TEMPLATE);
            delete_post_meta($post_id, COPTRZ_SERVE_LEGACY_PUBLIC);
        }
    }

    if (in_array('hero', $parts, true)) {
        $report['purged'][] = 'hero';
        if (!$dry_run) {
            foreach (coptrz_hero_meta_field_names() as $field) {
                \CoptrzTheme\MetaShim\Key_Formatter::delete_root('post', $post_id, $field);
            }
            if (defined('COPTRZ_HERO_BLOCK_PREFIX')) {
                delete_post_meta($post_id, COPTRZ_HERO_BLOCK_PREFIX);
            }
        }
    }

    // Key_Formatter::delete_root()'s only other caller (persist_root()) always
    // follows it with flush_cache() itself, so delete_root() doesn't do so on
    // its own — this function is the first caller that reads the SAME post's
    // meta again (via coptrz_post_purgeable_parts() below) within the same
    // request as the delete, so it must flush explicitly or that re-check
    // silently rereads Key_Formatter's stale in-memory map and never
    // observes the deletion. Confirmed by testing: without this, the "fully
    // purged" flag never got set, `_coptrz_pre_convert_content` was never
    // cleaned up, and — worse — coptrz_revert_post_to_blocks()'s purged-guard
    // never tripped, leaving Revert clickable and silently destructive
    // (restoring stale/empty content) on an already-purged post.
    if (!$dry_run) {
        \CoptrzTheme\MetaShim\Key_Formatter::flush_cache('post', $post_id);
    }

    // COPTRZ_PRE_CONVERT_CONTENT is the shared write-once backup for BOTH
    // parts (see coptrz_convert_post_to_blocks()) — only safe to drop once a
    // fresh coptrz_post_purgeable_parts() call confirms nothing purgeable
    // remains, i.e. this run finished the job rather than doing one of two
    // parts on a post converted for both.
    if (!$dry_run && empty(coptrz_post_purgeable_parts($post_id))) {
        delete_post_meta($post_id, COPTRZ_PRE_CONVERT_CONTENT);
        update_post_meta($post_id, COPTRZ_LEGACY_PURGED_FLAG, 'yes');
    }

    return $report;
}

/* ========================================================================= */
/*  Admin UI — per-post meta box                                              */
/* ========================================================================= */

/**
 * Conversion status label shown next to the post title on every convertible
 * post type's list table (Pages, Products, …) — the same "— Private" /
 * "— Posts Page" slot core uses, via the display_post_states filter. Lets an
 * editor see what still needs converting without opening each post.
 *
 * Checked in this order: pending (either part) wins over "converted" — a post
 * with its hero converted but sections still outstanding should read "Needs
 * converting", matching what the meta box would still offer. Purged only
 * applies once nothing is pending, since a post can have one part converted
 * (and purged) while the other is still mid-migration.
 *
 * Both underlying checks (coptrz_post_conversion_state() ->
 * coptrz_post_has_sections_data() / coptrz_hero_has_content()) read only from
 * WP's per-object meta cache, already primed for the whole list query — this
 * adds no extra queries per row.
 *
 * @param array   $states
 * @param WP_Post $post
 * @return array
 */
add_filter('display_post_states', function ($states, $post) {
    if (!in_array($post->post_type, coptrz_convertible_post_types(), true)) {
        return $states;
    }

    if (!empty(coptrz_post_conversion_state($post->ID))) {
        $states['coptrz_convert'] = '<span style="color:#996800;font-weight:600;">' . __('Needs converting', 'coptrz-theme') . '</span>';
        return $states;
    }

    $converted = coptrz_sections_is_converted($post->ID)
        || (function_exists('coptrz_hero_is_converted') && coptrz_hero_is_converted($post->ID));
    if (!$converted) {
        return $states;
    }

    if (coptrz_post_is_legacy_purged($post->ID)) {
        $states['coptrz_convert'] = '<span style="color:#646970;">' . __('Converted (purged)', 'coptrz-theme') . '</span>';
    } else {
        $states['coptrz_convert'] = '<span style="color:#1a7f37;">' . __('Converted', 'coptrz-theme') . '</span>';
    }

    return $states;
}, 10, 2);

add_action('add_meta_boxes', function ($post_type, $post) {
    if (!in_array($post_type, coptrz_convertible_post_types(), true)) {
        return;
    }

    // Already-converted posts keep the box — the Revert/Purge controls (or,
    // once purged, the terminal "converted from the old editor" message)
    // live in it. Purge deliberately never clears these flags, so a fully
    // purged post is still $converted here.
    $converted = coptrz_sections_is_converted($post->ID)
        || (function_exists('coptrz_hero_is_converted') && coptrz_hero_is_converted($post->ID));

    if (!$converted) {
        $has_sections = in_array($post_type, coptrz_section_post_types(), true)
            && coptrz_post_has_sections_data($post->ID);
        $has_hero = in_array($post_type, coptrz_hero_post_types(), true)
            && function_exists('coptrz_hero_has_content')
            && coptrz_hero_has_content($post->ID);

        if (!$has_sections && !$has_hero) {
            return;
        }
    }

    add_meta_box(
        'coptrz-section-converter',
        __('Convert to Blocks', 'coptrz-theme'),
        'coptrz_render_section_converter_box',
        $post_type,
        'side',
        'high'
    );
}, 10, 2);

/**
 * Per-post convert box: dry-run preview + convert, via admin-ajax. Converts
 * whichever of sections/hero apply to this post — see
 * coptrz_convert_post_to_blocks(). Every post type — products included, now
 * that they have a block editor too, see coptrz_enable_product_block_editor()
 * in includes/woocommerce.php.
 *
 * Three states: not converted (convert UI), converted (today's UI, plus a
 * Purge block when there's still legacy data a manage_options user can
 * delete — coptrz_post_purgeable_parts()), and fully purged (terminal —
 * see coptrz_post_is_legacy_purged(), a message only, no buttons: there is
 * nothing left to act on, and re-showing Revert/Preview/Convert controls
 * would imply actions that no longer work). Within the converted state, a
 * post whose content is already corrupted ($purge_blocked_corrupt,
 * coptrz_content_is_corrupted()) gets a tucked-away "Force purge anyway"
 * disclosure instead of the normal Purge buttons — see
 * coptrz_purge_post_legacy_data()'s $force param docs for why this is a
 * strictly worse action than normal Purge (it gives up the post's only
 * remaining repair path) and must never be the easy/default option.
 *
 * @param WP_Post $post
 * @return void
 */
function coptrz_render_section_converter_box($post)
{
    $sections_converted = coptrz_sections_is_converted($post->ID);
    $hero_converted     = function_exists('coptrz_hero_is_converted') && coptrz_hero_is_converted($post->ID);
    $converted          = $sections_converted || $hero_converted;
    $fully_purged       = $converted && coptrz_post_is_legacy_purged($post->ID);
    $purgeable          = $fully_purged ? array() : coptrz_post_purgeable_parts($post->ID);
    // coptrz_purge_post_legacy_data() already refuses on corrupted content
    // (see its docblock) — checked here too so the box explains why instead
    // of showing a button that dry-run would immediately refuse anyway.
    $purge_blocked_corrupt = !empty($purgeable) && coptrz_content_is_corrupted((string) $post->post_content);
    $can_purge          = current_user_can('manage_options');
    $nonce              = wp_create_nonce('coptrz_convert_sections');
    $revert_nonce       = wp_create_nonce('coptrz_revert_sections');
    $purge_nonce        = wp_create_nonce('coptrz_purge_sections');
    $mode               = get_post_meta($post->ID, '_coptrz_sections_mode', true);
    ?>
    <div class="coptrz-conv" data-post="<?php echo (int) $post->ID; ?>" data-nonce="<?php echo esc_attr($nonce); ?>" data-revert-nonce="<?php echo esc_attr($revert_nonce); ?>" data-purge-nonce="<?php echo esc_attr($purge_nonce); ?>">
        <?php if ($fully_purged) : ?>
            <p style="color:#646970;font-weight:600;margin-top:0;">
                <?php esc_html_e('This post/page was converted from the old editor.', 'coptrz-theme'); ?>
            </p>
            <p class="description" style="margin-bottom:0;">
                <?php esc_html_e('The legacy section/hero data has been permanently deleted. Edit the content via the block editor above.', 'coptrz-theme'); ?>
            </p>
        <?php elseif ($converted) : ?>
            <p style="color:#1a7f37;font-weight:600;margin-top:0;">
                ✓ <?php
                    if ($sections_converted && $hero_converted) {
                        esc_html_e('Sections and hero converted.', 'coptrz-theme');
                    } elseif ($hero_converted) {
                        esc_html_e('Hero converted.', 'coptrz-theme');
                    } else {
                        esc_html_e('Sections converted.', 'coptrz-theme');
                    }
                ?>
            </p>
            <p class="description">
                <?php if ($sections_converted && $mode === 'html') : ?>
                    <?php esc_html_e('The original section data is preserved. Edit the content via the "Page Sections (HTML)" repeater below.', 'coptrz-theme'); ?>
                <?php else : ?>
                    <?php esc_html_e('The original data is preserved. Edit the content — including the Hero block, if converted — via the block editor above.', 'coptrz-theme'); ?>
                <?php endif; ?>
            </p>

            <?php if ($sections_converted) : ?>
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
            <?php endif; ?>

            <hr style="margin:10px 0;" />
            <p class="description" style="margin-top:0;"><?php esc_html_e('Revert restores the pre-conversion content (and template, for pages), and clears the conversion.', 'coptrz-theme'); ?></p>
            <p style="margin-bottom:6px;">
                <button type="button" class="button coptrz-conv__dry-revert"><?php esc_html_e('Dry run revert', 'coptrz-theme'); ?></button>
                <button type="button" class="button coptrz-conv__revert" style="color:#b32d2e;"><?php esc_html_e('Revert', 'coptrz-theme'); ?></button>
            </p>
            <div class="coptrz-conv__out" style="font:12px/1.5 monospace;max-height:220px;overflow:auto;"></div>

            <?php if ($can_purge && $purge_blocked_corrupt) : ?>
            <hr style="margin:10px 0;" />
            <p class="description" style="margin-top:0;">
                <?php esc_html_e('Purge is unavailable: this post is listed under "Corrupted conversions" on the Tools > Convert to Blocks page. Revert, then Convert, to fix it before purging.', 'coptrz-theme'); ?>
            </p>
            <details style="margin:6px 0 0;">
                <summary style="cursor:pointer;color:#b32d2e;font-weight:600;"><?php esc_html_e('Force purge anyway (not recommended)', 'coptrz-theme'); ?></summary>
                <p class="description" style="color:#b32d2e;">
                    <?php esc_html_e('This post’s content already shows the corruption (literal text like "u003cpu003e" instead of real HTML on the front end). Force purge deletes the legacy section/hero data regardless — the ONLY thing that can fix the corruption — so the post is left broken PERMANENTLY, with no way to regenerate correct content except rewriting it by hand in the block editor. Only use this if you have already accepted that, or don’t care about this post’s current content.', 'coptrz-theme'); ?>
                </p>
                <p style="margin-bottom:6px;">
                    <button type="button" class="button coptrz-conv__dry-force-purge"><?php esc_html_e('Dry run force purge', 'coptrz-theme'); ?></button>
                    <button type="button" class="button coptrz-conv__force-purge" style="color:#fff;background:#b32d2e;border-color:#b32d2e;"><?php esc_html_e('Force purge (permanent, unrepairable)', 'coptrz-theme'); ?></button>
                </p>
            </details>
            <?php elseif ($can_purge && !empty($purgeable)) : ?>
            <hr style="margin:10px 0;" />
            <p class="description" style="margin-top:0;color:#b32d2e;">
                <?php esc_html_e('Purge permanently deletes the original section/hero data for the converted part(s) of this post. This cannot be undone: Revert, "Preview original", and the logged-out legacy fallback all stop working. Only do this once you have confirmed the converted version is correct.', 'coptrz-theme'); ?>
            </p>
            <p style="margin-bottom:6px;">
                <button type="button" class="button coptrz-conv__dry-purge"><?php esc_html_e('Dry run purge', 'coptrz-theme'); ?></button>
                <button type="button" class="button coptrz-conv__purge" style="color:#fff;background:#b32d2e;border-color:#b32d2e;"><?php esc_html_e('Purge', 'coptrz-theme'); ?></button>
            </p>
            <?php endif; ?>
        <?php else : ?>
            <p class="description" style="margin-top:0;">
                <?php esc_html_e('Freeze this post’s sections and/or hero into native Gutenberg blocks — whichever apply to this post type (any section that can’t map natively is frozen as Custom HTML instead; the hero only converts if it would render identically). Original data is kept (reversible). Products with an existing description (post_content) are refused — clear it first.', 'coptrz-theme'); ?>
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
        function run(action, nonce, dry, force) {
            var out = box.querySelector('.coptrz-conv__out');
            out.textContent = '…';
            var body = new URLSearchParams({
                action: action,
                nonce: nonce,
                post: box.dataset.post,
                dry: dry ? '1' : '0',
                force: force ? '1' : '0'
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
                if (confirm('<?php echo esc_js(__('Convert this post to blocks?', 'coptrz-theme')); ?>')) { run('coptrz_convert_sections', box.dataset.nonce, false); }
            });
        });
        box.querySelectorAll('.coptrz-conv__dry-revert').forEach(function (b) {
            b.addEventListener('click', function () { run('coptrz_revert_sections', box.dataset.revertNonce, true); });
        });
        box.querySelectorAll('.coptrz-conv__revert').forEach(function (b) {
            b.addEventListener('click', function () {
                if (confirm('<?php echo esc_js(__('Revert this post to the legacy builders? The converted blocks will be removed from post_content. This cannot be undone except via a post revision.', 'coptrz-theme')); ?>')) { run('coptrz_revert_sections', box.dataset.revertNonce, false); }
            });
        });
        box.querySelectorAll('.coptrz-conv__dry-purge').forEach(function (b) {
            b.addEventListener('click', function () { run('coptrz_purge_sections', box.dataset.purgeNonce, true); });
        });
        box.querySelectorAll('.coptrz-conv__purge').forEach(function (b) {
            b.addEventListener('click', function () {
                if (confirm('<?php echo esc_js(__('Permanently delete the original section/hero data for this post? This cannot be undone — Revert and Preview original will stop working.', 'coptrz-theme')); ?>')) { run('coptrz_purge_sections', box.dataset.purgeNonce, false); }
            });
        });
        box.querySelectorAll('.coptrz-conv__dry-force-purge').forEach(function (b) {
            b.addEventListener('click', function () { run('coptrz_purge_sections', box.dataset.purgeNonce, true, true); });
        });
        box.querySelectorAll('.coptrz-conv__force-purge').forEach(function (b) {
            b.addEventListener('click', function () {
                if (confirm('<?php echo esc_js(__('Force purge this CORRUPTED post? The corrupted content will be left exactly as-is, PERMANENTLY — there will be no way left to regenerate it correctly.', 'coptrz-theme')); ?>')
                    && confirm('<?php echo esc_js(__('Really sure? This is not the normal Purge — it deletes the one thing that could still fix this post.', 'coptrz-theme')); ?>')
                ) { run('coptrz_purge_sections', box.dataset.purgeNonce, false, true); }
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
    // Converts whichever of sections/hero apply to this post — see
    // coptrz_convert_post_to_blocks(). Products now get the block editor too
    // (see coptrz_enable_product_block_editor(), includes/woocommerce.php),
    // so every post type converts to native blocks.
    $report = coptrz_convert_post_to_blocks($post_id, $dry);
    wp_send_json_success($report);
});

add_action('wp_ajax_coptrz_revert_sections', function () {
    check_ajax_referer('coptrz_revert_sections', 'nonce');
    $post_id = isset($_POST['post']) ? (int) $_POST['post'] : 0;
    if (!$post_id || !current_user_can('edit_post', $post_id)) {
        wp_send_json_error('Permission denied.');
    }
    $dry = !empty($_POST['dry']) && $_POST['dry'] === '1';
    $report = coptrz_revert_post_to_blocks($post_id, $dry);
    wp_send_json_success($report);
});

/**
 * Purge is irreversible (unlike convert/revert, which stay edit_post-gated
 * throughout this file), so it's held to the same manage_options bar as the
 * Tools > Convert to Blocks page itself, not just per-post edit capability.
 * The meta box mirrors this — see coptrz_render_section_converter_box() below,
 * which only prints the Purge controls for a manage_options user.
 */
add_action('wp_ajax_coptrz_purge_sections', function () {
    check_ajax_referer('coptrz_purge_sections', 'nonce');
    $post_id = isset($_POST['post']) ? (int) $_POST['post'] : 0;
    if (!$post_id || !current_user_can('manage_options') || !current_user_can('edit_post', $post_id)) {
        wp_send_json_error('Permission denied.');
    }
    $dry   = !empty($_POST['dry']) && $_POST['dry'] === '1';
    $force = !empty($_POST['force']) && $_POST['force'] === '1';
    $report = coptrz_purge_post_legacy_data($post_id, $dry, $force);
    wp_send_json_success($report);
});

/* ========================================================================= */
/*  Admin UI — bulk runner (Tools > Convert to Blocks)                        */
/* ========================================================================= */

add_action('admin_menu', function () {
    add_management_page(
        __('Convert to Blocks', 'coptrz-theme'),
        __('Convert to Blocks', 'coptrz-theme'),
        'manage_options',
        'coptrz-convert-to-blocks',
        'coptrz_render_bulk_converter_page'
    );
});

/**
 * AJAX: search convertible posts (coptrz_convertible_post_types() — the union
 * of section- and hero-applicable types) by name, returning id/title/post-type
 * and a `pending` label (sections, hero, or both) so the runner page can build
 * an explicit selection. Over-fetches title matches and filters/labels them in
 * PHP via coptrz_post_conversion_state() — cheap at this scale (a title
 * search's raw match count), and avoids a meta_query that can't correctly
 * express "hero not converted" scoped to hero-applicable types only when the
 * candidate list also includes hero-inapplicable types (see
 * coptrz_conversion_pending_where()'s docblock, which IS scoped and used
 * instead for the per-type "convert remaining" batch action below).
 *
 * Default (`mode=convert`): only posts with something pending are returned.
 * `mode=revert` inverts this to already-converted posts (either part).
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
        'post_type'           => coptrz_convertible_post_types(),
        'post_status'         => array('publish', 'private', 'draft', 'pending', 'future'),
        's'                   => $q,
        'posts_per_page'      => 50,
        'no_found_rows'       => true,
        'ignore_sticky_posts' => true,
        'orderby'             => 'title',
        'order'               => 'ASC',
    ));

    $out = array();
    foreach ($query->posts as $p) {
        if ($mode === 'revert') {
            $parts = array();
            if (coptrz_sections_is_converted($p->ID)) {
                $parts[] = 'sections';
            }
            if (function_exists('coptrz_hero_is_converted') && coptrz_hero_is_converted($p->ID)) {
                $parts[] = 'hero';
            }
        } else {
            $parts = coptrz_post_conversion_state($p->ID);
        }
        if (empty($parts)) {
            continue;
        }

        $obj = get_post_type_object($p->post_type);
        $out[] = array(
            'id'         => (int) $p->ID,
            'title'      => $p->post_title !== '' ? $p->post_title : ('#' . $p->ID),
            'type_label' => $obj ? $obj->labels->singular_name : $p->post_type,
            'pending'    => implode(' + ', $parts),
        );
        if (count($out) >= 20) {
            break;
        }
    }
    wp_send_json_success($out);
});

/**
 * Convert-by-search runner page: search posts by name (across every
 * convertible post type), pick the exact ones to freeze, then dry-run or
 * convert just those — the only way to convert or revert a post from this
 * page (50/run cap). A read-only "remaining by post type" table is shown
 * above the search as a progress overview only; there is deliberately no
 * batch "convert all remaining" action here, to avoid a wholesale sitewide
 * conversion run.
 *
 * @return void
 */
function coptrz_render_bulk_converter_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $action    = isset($_POST['coptrz_bulk_action']) ? sanitize_key($_POST['coptrz_bulk_action']) : '';
    $is_revert = ($action === 'revert' || $action === 'revert_dry');
    $ids_raw   = isset($_POST['coptrz_ids']) ? sanitize_text_field(wp_unslash($_POST['coptrz_ids'])) : '';
    $did       = array();
    $limit     = 50; // safety cap per run

    if ($action && check_admin_referer('coptrz_bulk_convert')) {
        // Always an explicit selection of post IDs (gathered via the name search).
        preg_match_all('/\d+/', $ids_raw, $m);
        $ids = array_values(array_unique(array_map('intval', $m[0])));
        $dry = ($action === 'dry' || $action === 'revert_dry');
        foreach (array_slice($ids, 0, $limit) as $pid) {
            $did[] = $is_revert ? coptrz_revert_post_to_blocks($pid, $dry) : coptrz_convert_post_to_blocks($pid, $dry);
        }
    }

    $search_nonce = wp_create_nonce('coptrz_search_sections');
    $remaining    = coptrz_conversion_remaining_counts();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Convert to Blocks', 'coptrz-theme'); ?></h1>
        <p class="description">
            <?php esc_html_e('Freezes legacy page-builder sections AND/OR the Hero meta box into native Gutenberg blocks — whichever apply to a given post (any section that can’t map natively is frozen as Custom HTML instead; the hero only converts if it would render identically to the current meta-driven hero). Products with an existing description (post_content) are refused for the sections part — clear it first, then re-run. Original data is preserved, so a conversion can be reverted from the same tool.', 'coptrz-theme'); ?>
        </p>

        <?php $corrupted = coptrz_find_corrupted_conversions(); ?>
        <?php if (!empty($corrupted)) : ?>
            <div class="notice notice-error" style="padding: 1em;">
                <h2 style="margin-top:0;"><?php esc_html_e('Corrupted conversions found', 'coptrz-theme'); ?></h2>
                <p>
                    <?php esc_html_e('These posts were converted before a wp_slash() bug was fixed (block attributes containing <, >, &, --, or backslashes were silently stripped on save — e.g. a tab description rendering as literal "u003cpu003e" instead of an HTML tag). Revert, then Convert, each post below to regenerate it correctly from the original section/hero data. Any edits made to these posts in the block editor since they were converted will be lost by reverting. Posts marked "unrepairable" had their legacy data force-purged and can no longer be fixed this way — the content must be rewritten by hand.', 'coptrz-theme'); ?>
                </p>
                <ul style="list-style: disc; padding-left: 2em;">
                    <?php foreach ($corrupted as $c) : ?>
                        <li>
                            <a href="<?php echo esc_url(get_edit_post_link($c['id'])); ?>"><?php echo esc_html($c['title'] ?: ('#' . $c['id'])); ?></a>
                            <span class="description"> — <?php echo esc_html($c['type']); ?></span>
                            <?php if (!empty($c['unrepairable'])) : ?>
                                <strong style="color:#b32d2e;"> — <?php esc_html_e('unrepairable (legacy data force-purged)', 'coptrz-theme'); ?></strong>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

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
                    <th><?php esc_html_e('Hero', 'coptrz-theme'); ?></th>
                    <th><?php esc_html_e('Notes', 'coptrz-theme'); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($did as $r) :
                    $total = array_sum((array) ($r['counts'] ?? array())); ?>
                    <tr>
                        <td><a href="<?php echo esc_url(get_edit_post_link($r['post_id'])); ?>"><?php echo esc_html($r['title'] ?: ('#' . $r['post_id'])); ?></a></td>
                        <td><?php echo esc_html($r['type']); ?></td>
                        <td>
                            <?php echo esc_html($r['target'] ?? ''); ?>
                            <?php if (!empty($r['template'])) : ?>
                                <br /><span class="description"><?php echo esc_html__('Template →', 'coptrz-theme') . ' ' . esc_html($r['template']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo (int) $total; ?></td>
                        <td><?php echo esc_html($r['hero'] ?? ''); ?></td>
                        <td><?php echo esc_html(implode(' ', (array) $r['warnings'])); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <h2 style="margin-top:2em;"><?php esc_html_e('Remaining to convert, by post type', 'coptrz-theme'); ?></h2>
        <p class="description"><?php esc_html_e('Progress overview only — posts are converted individually, via the per-post "Convert to Blocks" box or the search below. "Remaining" is an estimate — a post may still be skipped at conversion time (e.g. a hero that wouldn’t render identically as a block).', 'coptrz-theme'); ?></p>
        <table class="widefat" style="max-width:400px;">
            <thead><tr><th><?php esc_html_e('Post Type', 'coptrz-theme'); ?></th><th><?php esc_html_e('Remaining', 'coptrz-theme'); ?></th></tr></thead>
            <tbody>
            <?php foreach ($remaining as $post_type => $count) :
                $obj = get_post_type_object($post_type);
                $label = $obj ? $obj->labels->name : $post_type;
            ?>
                <tr>
                    <td><?php echo esc_html($label); ?></td>
                    <td><?php echo (int) $count; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h2 style="margin-top:2em;"><?php esc_html_e('Convert or revert specific posts', 'coptrz-theme'); ?></h2>
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
                        onclick="return confirm('<?php echo esc_js(__('Revert the selected posts to the legacy builders? Converted blocks will be removed from post_content.', 'coptrz-theme')); ?>');">
                    <?php esc_html_e('Revert selected posts', 'coptrz-theme'); ?>
                </button>
            </p>
        </form>
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
                        type.textContent = ' (' + it.type_label + (it.pending ? ' — ' + it.pending : '') + ')';
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
