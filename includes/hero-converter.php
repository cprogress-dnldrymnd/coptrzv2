<?php
/**
 * Plugin/Snippet Name: Hero -> Block — data layer
 * Description: Pure helpers for migrating the legacy per-post "Hero" post-meta
 *              container (includes/post-meta.php, __hero_fields() /
 *              __hero_button_fields() / __hero_form_fields()) into a
 *              `coptrz/hero` block. This file makes no writes and registers no
 *              admin UI of its own — coptrz_hero_conversion_plan() is the
 *              single entry point, called from includes/section-converter.php's
 *              coptrz_convert_post_to_blocks(), which is the one action that
 *              converts both sections and hero (whichever apply to a given
 *              post) and owns the actual post_content write, backups, and
 *              admin surface (per-post meta box + Tools > Convert to Blocks).
 *
 *              The Hero post-meta is never deleted by conversion — only
 *              post_content changes (a `coptrz/hero` block gets prepended).
 *              Rendering is routed by ___hero_modules() (includes/modules.php)
 *              via coptrz_hero_block_attrs() (includes/hero-block.php), keyed
 *              off the block's presence in post_content — so converted and
 *              unconverted posts render identically either way, and
 *              COPTRZ_HERO_CONVERTED_FLAG below is bookkeeping only (read by
 *              nothing at render time, only by the converter's own
 *              already-converted / remaining-count checks).
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

if (!defined('ABSPATH')) {
    exit;
}

/** Post meta flag marking a post as hero-converted. Bookkeeping only. */
const COPTRZ_HERO_CONVERTED_FLAG = '_coptrz_hero_converted';

/** Backup: the exact coptrz/hero block markup a conversion prepended. */
const COPTRZ_HERO_BLOCK_PREFIX = '_coptrz_hero_block';

/**
 * The 10 post types the legacy Hero post_meta container targets
 * (includes/post-meta.php — Container::make('post_meta', 'Hero')).
 */
function coptrz_hero_post_types()
{
    return array(
        'page', 'product', 'post', 'capabilities', 'casestudies',
        'industries', 'events', 'guides', 'rentals', 'landingpages',
    );
}

/**
 * @param int $post_id
 * @return bool
 */
function coptrz_hero_is_converted($post_id)
{
    return get_post_meta($post_id, COPTRZ_HERO_CONVERTED_FLAG, true) === 'yes';
}

/**
 * Strips a leading coptrz/hero block from $content, if present. Used by
 * section-converter.php's non-empty-content guards so a hero-converted post
 * isn't wrongly treated as "has content" — the hero block is storage only and
 * renders nowhere in post_content itself (coptrz_render_hero_block(),
 * includes/hero-block.php).
 *
 * @param string $content
 * @return string
 */
function coptrz_content_without_hero($content)
{
    $content = (string) $content;
    $blocks  = parse_blocks($content);
    if (empty($blocks) || empty($blocks[0]['blockName']) || $blocks[0]['blockName'] !== 'coptrz/hero') {
        return $content;
    }
    array_shift($blocks);
    return serialize_blocks($blocks);
}

/**
 * The exact serialized markup of a leading coptrz/hero block in $content, or
 * '' when there isn't one.
 *
 * @param string $content
 * @return string
 */
function coptrz_content_hero_block_markup($content)
{
    $content = (string) $content;
    $blocks  = parse_blocks($content);
    if (empty($blocks) || empty($blocks[0]['blockName']) || $blocks[0]['blockName'] !== 'coptrz/hero') {
        return '';
    }
    return trim(serialize_block($blocks[0]));
}

/**
 * The per-post-type fallback `(alignment, height)` a template's
 * ___hero_modules() call site passes today (see page.php, single-rentals.php,
 * single.php, template-parts/single/single-events.php,
 * template-parts/single/single-post.php — the literal argument lists there
 * are this function's source of truth; keep both in sync). ___hero_render()
 * only applies these when the corresponding meta value is empty.
 *
 * Needed because the hero now renders INLINE for every type except the
 * hoisted ones (coptrz_hero_hoisted_post_types(), includes/hero-block.php) —
 * an inline render has no template call site to supply a fallback, so
 * coptrz_hero_meta_to_attrs() bakes the effective value into the block's
 * attrs at conversion time instead. `post` (hoisted) doesn't strictly need
 * this — its call site still runs on every render — but is included anyway
 * so this function stays a complete, template-independent mirror.
 *
 * @param string $post_type
 * @return array{alignment:string|false,height:string|false}
 */
function coptrz_hero_template_defaults($post_type)
{
    $map = array(
        // single.php's get_template_part() fallback branch — casestudies and
        // guides have no template-parts/single/single-{type}.php of their own.
        'casestudies' => array('alignment' => 'text-start', 'height' => 'small-hero'),
        'guides'      => array('alignment' => 'text-start', 'height' => 'small-hero'),
        'rentals'     => array('alignment' => 'text-start', 'height' => 'small-hero'),
        'events'      => array('alignment' => 'text-center', 'height' => ''),
        'post'        => array('alignment' => 'text-start', 'height' => 'small-hero'),
    );
    return isset($map[$post_type]) ? $map[$post_type] : array('alignment' => false, 'height' => false);
}

/**
 * Legacy Hero post-meta -> the `coptrz/hero` block's attribute shape — the
 * inverse of coptrz_hero_args_from_block() (includes/hero-block.php). Used to
 * build the block coptrz_hero_conversion_plan() proposes, and (via
 * coptrz_hero_dry_run_check()) to verify it will render identically first.
 *
 * @param int $post_id
 * @return array
 */
function coptrz_hero_meta_to_attrs($post_id)
{
    $buttons = array();
    foreach ((array) get__post_meta_by_id($post_id, 'buttons') as $row) {
        $button_url = isset($row['button_url']) ? $row['button_url'] : '';
        $post_id_val = ($button_url !== '' && is_numeric($button_url)) ? (int) $button_url : 0;
        $buttons[] = array(
            'type'      => isset($row['button_type']) ? $row['button_type'] : '',
            'text'      => isset($row['button_text']) ? $row['button_text'] : '',
            'postId'    => $post_id_val,
            'postTitle' => $post_id_val ? get_the_title($post_id_val) : '',
            'urlCustom' => isset($row['button_url_custom']) ? $row['button_url_custom'] : '',
            'style'     => isset($row['button_style']) ? $row['button_style'] : 'button-accent',
            'target'    => isset($row['button_target']) ? $row['button_target'] : 'target="_self"',
        );
    }

    $hero_form           = get__post_meta_by_id($post_id, 'hero_form');
    $form_id             = isset($hero_form[0]['id']) ? (int) $hero_form[0]['id'] : 0;
    $hero_form_product   = get__post_meta_by_id($post_id, 'hero_form_product');
    $form_product_id     = isset($hero_form_product[0]['id']) ? (int) $hero_form_product[0]['id'] : 0;
    $hero_form_doc       = get__post_meta_by_id($post_id, 'hero_form_document_redirect');
    $form_doc_id         = isset($hero_form_doc[0]['id']) ? (int) $hero_form_doc[0]['id'] : 0;
    $hero_form_pdf       = get__post_meta_by_id($post_id, 'hero_form_pdf_redirect');
    $form_pdf_id         = is_numeric($hero_form_pdf) ? (int) $hero_form_pdf : 0;
    $hero_background     = get__post_meta_by_id($post_id, 'hero_background');
    $background_id       = is_numeric($hero_background) ? (int) $hero_background : 0;
    $hero_form_image     = get__post_meta_by_id($post_id, 'hero_form_image');
    $form_image_id       = is_numeric($hero_form_image) ? (int) $hero_form_image : 0;

    // Bake the template's per-post-type fallback into the stored value when
    // the meta is empty — see coptrz_hero_template_defaults()'s docblock.
    $defaults      = coptrz_hero_template_defaults(get_post_type($post_id));
    $raw_height    = (string) get__post_meta_by_id($post_id, 'hero_height');
    $raw_alignment = (string) get__post_meta_by_id($post_id, 'hero_alignment');
    $baked_height    = ($raw_height !== '') ? $raw_height : (($defaults['height'] !== false) ? $defaults['height'] : '');
    $baked_alignment = ($raw_alignment !== '') ? $raw_alignment : (($defaults['alignment'] !== false) ? $defaults['alignment'] : '');

    return array(
        'hidden'                    => (bool) get__post_meta_by_id($post_id, 'hero_hidden'),
        'breadcrumbsHidden'         => (bool) get__post_meta_by_id($post_id, 'breadcrumbs_hidden'),
        'heading'                   => (string) get__post_meta_by_id($post_id, 'hero_heading'),
        'description'               => (string) get__post_meta_by_id($post_id, 'hero_description'),
        'backgroundType'            => (string) (get__post_meta_by_id($post_id, 'hero_background_type') ?: 'self-hosted'),
        'backgroundId'              => $background_id,
        'backgroundUrl'             => $background_id ? (string) wp_get_attachment_url($background_id) : '',
        'backgroundYoutube'         => (string) get__post_meta_by_id($post_id, 'hero_background_youtube'),
        'height'                    => $baked_height,
        'alignment'                 => $baked_alignment,
        'buttons'                   => $buttons,
        'formEnable'                => (bool) get__post_meta_by_id($post_id, 'hero_form_enable'),
        'formImageId'               => $form_image_id,
        'formImageUrl'              => $form_image_id ? (string) wp_get_attachment_url($form_image_id) : '',
        'formHeading'               => (string) get__post_meta_by_id($post_id, 'hero_form_heading'),
        'formDescription'           => (string) get__post_meta_by_id($post_id, 'hero_form_description'),
        'formRedirectType'          => (string) get__post_meta_by_id($post_id, 'hero_form_redirect_type'),
        'formPdfRedirectId'         => $form_pdf_id,
        'formPdfRedirectUrl'        => $form_pdf_id ? (string) wp_get_attachment_url($form_pdf_id) : '',
        'formDocumentRedirectId'    => $form_doc_id,
        'formDocumentRedirectTitle' => $form_doc_id ? get_the_title($form_doc_id) : '',
        'formRedirectUrl'           => (string) get__post_meta_by_id($post_id, 'hero_form_redirect_url'),
        'formStyle'                 => (string) get__post_meta_by_id($post_id, 'hero_form_style'),
        'formType'                  => (string) get__post_meta_by_id($post_id, 'hero_form_type'),
        'formScript'                => (string) get__post_meta_by_id($post_id, 'hero_form_script'),
        'formId'                    => $form_id,
        'formTitle'                 => $form_id ? get_the_title($form_id) : '',
        'formProductId'             => $form_product_id,
        'formProductTitle'          => $form_product_id ? get_the_title($form_product_id) : '',
    );
}

/**
 * True when a post's Hero meta carries real content — i.e. the hero is more
 * than the empty title-fallback every hero-type post otherwise renders. Used to
 * decide whether the per-post "Convert to Blocks" box has a hero worth
 * converting (section-converter.php's add_meta_boxes gate), and — since it's
 * cheap enough to run per-row — the post-list "Needs converting" status label
 * (coptrz_render_conversion_state_column(), section-converter.php).
 *
 * Reads the six raw signals directly via get__post_meta_by_id() rather than
 * routing through coptrz_hero_meta_to_attrs(): that function additionally
 * resolves get_the_title()/wp_get_attachment_url() for buttons, forms, and
 * backgrounds — real content, real queries — which this function has no need
 * for, since it only ever checks non-emptiness. Kept behaviourally identical
 * to that resolved shape: height/alignment are excluded because
 * coptrz_hero_template_defaults() bakes non-empty per-type defaults into them,
 * so they're never a reliable "author put something here" signal; `buttons` is
 * checked as the raw complex-field row array (non-empty rows = non-empty
 * attrs['buttons'], the same signal coptrz_hero_meta_to_attrs() would produce
 * without needing the per-row title lookups it does).
 *
 * MUST be kept in sync with coptrz_hero_has_content_where() below, its SQL
 * mirror.
 *
 * @param int $post_id
 * @return bool
 */
function coptrz_hero_has_content($post_id)
{
    if ((bool) get__post_meta_by_id($post_id, 'hero_hidden')) {
        return false;
    }

    $background = get__post_meta_by_id($post_id, 'hero_background');

    return (string) get__post_meta_by_id($post_id, 'hero_heading') !== ''
        || (string) get__post_meta_by_id($post_id, 'hero_description') !== ''
        || (is_numeric($background) && (int) $background !== 0)
        || (string) get__post_meta_by_id($post_id, 'hero_background_youtube') !== ''
        || !empty(get__post_meta_by_id($post_id, 'buttons'))
        || (bool) get__post_meta_by_id($post_id, 'hero_form_enable');
}

/**
 * SQL mirror of coptrz_hero_has_content() — a correlated-EXISTS WHERE
 * fragment (against a `{$alias}` alias on wp_posts) matching posts whose Hero
 * meta carries real content, i.e. NOT hero_hidden AND at least one of
 * heading/description/background/background_youtube/buttons/form_enable is
 * set. MUST be kept in sync with coptrz_hero_has_content() by hand — there is
 * no single source of truth, since one runs in PHP per-post (on real Carbon
 * values via get__post_meta_by_id()) and this one runs as SQL over raw
 * postmeta rows.
 *
 * Storage formats (confirmed against production data): checkboxes
 * (`hero_hidden`, `hero_form_enable`) store `'yes'` or `''`
 * (meta-shim Writer::normalize()); `hero_background` stores an attachment ID
 * string, `'0'`/`''` meaning none; the `buttons` complex field's first row
 * cell is stored under the key `_buttons|||0|value`.
 *
 * Used by coptrz_conversion_pending_where() (section-converter.php) for the
 * bulk page's "remaining" counts — a correlated EXISTS/NOT EXISTS pair against
 * {$wpdb->postmeta}, not a meta_query, per the performance note in that
 * function's docblock (an OR of unkeyed LEFT JOINs is a cartesian scan at this
 * site's meta-per-post scale).
 *
 * @param string $alias wp_posts table alias to correlate against.
 * @return array{0:string,1:array} [$where_sql, $prepare_args]
 */
function coptrz_hero_has_content_where($alias = 'p')
{
    global $wpdb;

    $sql = "NOT EXISTS (SELECT 1 FROM {$wpdb->postmeta} hh WHERE hh.post_id = {$alias}.ID AND hh.meta_key = %s AND hh.meta_value = %s)
            AND EXISTS (SELECT 1 FROM {$wpdb->postmeta} hc WHERE hc.post_id = {$alias}.ID AND (
                (hc.meta_key IN (%s, %s, %s) AND hc.meta_value <> '')
                OR (hc.meta_key = %s AND hc.meta_value NOT IN ('', '0'))
                OR (hc.meta_key = %s AND hc.meta_value <> '')
                OR (hc.meta_key = %s AND hc.meta_value = %s)
            ))";

    $args = array(
        '_hero_hidden', 'yes',
        '_hero_heading', '_hero_description', '_hero_background_youtube',
        '_hero_background',
        '_buttons|||0|value',
        '_hero_form_enable', 'yes',
    );

    return array($sql, $args);
}

/**
 * The strongest available "would this convert cleanly" check: render the hero
 * BOTH ways — from the live meta (___hero_args_from_meta()) and from the
 * block attributes a conversion would write
 * (coptrz_hero_meta_to_attrs() -> coptrz_hero_args_from_block()) — through the
 * same pure ___hero_render() (includes/modules.php), and compare the actual
 * HTML. This is stronger than diffing the two args arrays: the meta and block
 * representations legitimately differ in shape for the same data (e.g. a raw
 * Carbon association row vs a flat block ID), which would cause false
 * mismatches an HTML diff doesn't have.
 *
 * @param int $post_id
 * @return array{identical:bool,html_meta:string,html_block:string,attrs:array}
 */
function coptrz_hero_dry_run_check($post_id)
{
    $attrs = coptrz_hero_meta_to_attrs($post_id);

    // coptrz_hero_meta_to_attrs() bakes each post type's template fallback
    // into $attrs's height/alignment (see coptrz_hero_template_defaults()),
    // since the inline render has no template call site to supply one. The
    // meta-path comparison must apply the SAME defaults here, or every post
    // of a type with a non-empty default (rentals, events, casestudies,
    // guides) would falsely report not_identical.
    $defaults  = coptrz_hero_template_defaults(get_post_type($post_id));
    $html_meta  = ___hero_render(___hero_args_from_meta($post_id, $defaults['alignment'], $defaults['height']));
    $html_block = ___hero_render(coptrz_hero_args_from_block($attrs, $post_id));

    return array(
        'identical'  => ($html_meta === $html_block),
        'html_meta'  => $html_meta,
        'html_block' => $html_block,
        'attrs'      => $attrs,
    );
}

/**
 * Decides whether/how a post's Hero meta should become a `coptrz/hero` block
 * — a pure decision, no writes (the caller, coptrz_convert_post_to_blocks() in
 * includes/section-converter.php, does the actual prepend). Statuses:
 *
 *   not_applicable    post type has no Hero container (coptrz_hero_post_types())
 *   already_converted COPTRZ_HERO_CONVERTED_FLAG already set
 *   has_block         post_content already has a coptrz/hero block
 *   renderer_missing  ___hero_render() etc not loaded in this admin context
 *                      (dd_is_blocks_editor_template_active() — never guess here)
 *   not_identical     the block would not render the same as the current
 *                      meta-driven hero (coptrz_hero_dry_run_check())
 *   ready             safe to prepend; `markup` holds the serialized block
 *
 * @param int $post_id
 * @return array{status:string,markup:string}
 */
function coptrz_hero_conversion_plan($post_id)
{
    $plan = array('status' => 'not_applicable', 'markup' => '');

    $post = get_post($post_id);
    if (!$post || !in_array($post->post_type, coptrz_hero_post_types(), true)) {
        return $plan;
    }
    if (coptrz_hero_is_converted($post_id)) {
        $plan['status'] = 'already_converted';
        return $plan;
    }
    if (has_block('coptrz/hero', $post_id)) {
        $plan['status'] = 'has_block';
        return $plan;
    }
    if (!function_exists('___hero_render') || !function_exists('___hero_args_from_meta') || !function_exists('coptrz_hero_args_from_block')) {
        $plan['status'] = 'renderer_missing';
        return $plan;
    }

    $check = coptrz_hero_dry_run_check($post_id);
    if (!$check['identical']) {
        $plan['status'] = 'not_identical';
        return $plan;
    }

    $plan['status'] = 'ready';
    $plan['markup'] = trim(serialize_blocks(array(coptrz_block('coptrz/hero', coptrz_json_safe_array($check['attrs'])))));
    return $plan;
}

/* ========================================================================= */
/*  Admin UI — hide the Hero meta box once a post's hero is converted        */
/* ========================================================================= */

/**
 * Every root field name across the Hero container's three tabs
 * (__hero_fields() / __hero_button_fields() / __hero_form_fields(),
 * includes/post-meta.php) — the complete set is_container_render_hidden()
 * (meta-shim/Container_Admin.php) needs, since it only hides a container when
 * EVERY non-display field on it is blocklisted. Memoized; '' when post-meta.php
 * isn't loaded in this request (skipped in admin under the blocks-editor
 * template — see dd_is_blocks_editor_template_active()), in which case there's
 * no Hero container to hide anyway.
 *
 * @return string[]
 */
function coptrz_hero_meta_field_names()
{
    static $names = null;
    if ($names !== null) {
        return $names;
    }

    $names = array();
    if (function_exists('__hero_fields') && function_exists('__hero_button_fields') && function_exists('__hero_form_fields')) {
        foreach (array_merge(__hero_fields(), __hero_button_fields(), __hero_form_fields()) as $field) {
            if ($field instanceof \CoptrzTheme\MetaShim\Field) {
                $names[] = $field->name;
            }
        }
    }
    return $names;
}

/**
 * Hides the (post-side) Hero meta box once a post's hero has been converted
 * to a coptrz/hero block — same mechanism section-converter.php uses for the
 * legacy sections builder (coptrz_register_html_sections_fields() +
 * the coptrz_meta_shim_field_visible filter below). Called from
 * tissue_paper_register_custom_fields() (functions.php), after post-meta.php
 * registers and before Container_Admin::boot(), same call site/ordering as
 * coptrz_register_html_sections_fields().
 *
 * Does NOT touch the term_meta Hero container (product_cat/pa_brands) —
 * is_container_render_hidden() is only consulted by
 * Container_Admin::register_post_meta_boxes(); the term-side render path
 * (register_term_hooks()/render_term_fields()) never checks the blocklist.
 *
 * @return void
 */
function coptrz_register_hero_hidden_fields()
{
    if (!class_exists('CoptrzTheme\\MetaShim\\Container_Admin')) {
        return;
    }
    $names = coptrz_hero_meta_field_names();
    if (empty($names)) {
        return;
    }
    \CoptrzTheme\MetaShim\Container_Admin::hide_fields($names);
}

add_filter('coptrz_meta_shim_field_visible', function ($visible, $field_name, $post_id) {
    if (in_array($field_name, coptrz_hero_meta_field_names(), true)) {
        return !coptrz_hero_is_converted($post_id);
    }
    return $visible;
}, 10, 3);
