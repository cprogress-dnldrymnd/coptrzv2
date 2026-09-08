<?php

/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 *
 * Registers the `coptrz/hero` block's PHP half: the render filter, the
 * attrs -> ___hero_render() args mapper, and the memoized post_content reader
 * other hooks (action_body_class(), hero_form_redirect() in hooks.php) use to
 * dual-read the block without waiting for it to render.
 *
 * RENDER MODE — the hero renders INLINE, at the block's position in
 * post_content, for every post type EXCEPT the ones listed in
 * coptrz_hero_hoisted_post_types() (currently just `post`). Inline is what
 * makes the block's position in the editor actually matter — move it, and the
 * hero moves. It's safe because converted content on every other type renders
 * as a direct child of `<main>` (no Bootstrap `.container`/`.col-*` in the
 * way) — verified per template at the point this was written. `post` is the
 * one type where the_content() sits inside a constrained column
 * (single-post.php: `.col-lg-7.px-5`), so it stays HOISTED: the block is
 * storage only there (coptrz_render_hero_block() returns ''), and
 * ___hero_modules() (includes/modules.php) keeps rendering at that template's
 * existing hero position, reading the block's attributes via
 * coptrz_hero_block_attrs() when present and falling back to the legacy Hero
 * post-meta otherwise (that fallback is universal, not just for the hoisted
 * type). This dual-mode design is what keeps every phase of the hero -> block
 * migration revertible without template changes for the hoisted type, while
 * letting every other type place the hero anywhere in the block order.
 *
 * The JS half is assets/js/coptrz-hero-block.js, enqueued (with the
 * coptrzHero localized option registry from coptrz_hero_field_options() in
 * functions.php) from digitally_disruptive_enqueue_swiper_editor_assets().
 */

/**
 * The `coptrz/hero` block's attributes for a post, or NULL when the post has
 * no hero block — callers then fall back to the legacy meta path. Memoized
 * per post per request; has_block() short-circuits parse_blocks() for the
 * (large, during migration) set of unconverted posts.
 *
 * @param int $post_id 0 = the current queried object.
 * @return array|null
 */
function coptrz_hero_block_attrs($post_id = 0)
{
    static $cache = array();

    $post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
    if (!$post_id) {
        return null;
    }
    if (array_key_exists($post_id, $cache)) {
        return $cache[$post_id];
    }

    if (!has_block('coptrz/hero', $post_id)) {
        return $cache[$post_id] = null;
    }

    $found   = null;
    $content = (string) get_post_field('post_content', $post_id);
    foreach (parse_blocks($content) as $block) {
        if (!empty($block['blockName']) && $block['blockName'] === 'coptrz/hero') {
            $found = (isset($block['attrs']) && is_array($block['attrs'])) ? $block['attrs'] : array();
            break;
        }
    }

    return $cache[$post_id] = $found;
}

/**
 * coptrz/hero block attributes -> the ___hero_render() args shape (see
 * modules.php). Mirrors ___hero_args_from_meta() field-for-field so both
 * produce identical rendered output for equivalent input — background/type
 * are both always resolved regardless of backgroundType (matching the legacy
 * field's conditional_logic, which only hides the irrelevant one in the UI),
 * hero_form_image keeps the guides-only featured-image fallback, and
 * hide_on_list/cpd_maker/tquk_logo stay meta-only (unrelated containers, not
 * part of the Hero block).
 *
 * @param array $attrs
 * @param int   $post_id
 * @param string|false $hero_alignment_args Per-template fallback default.
 * @param string|false $hero_height_args    Per-template fallback default.
 * @return array
 */
function coptrz_hero_args_from_block($attrs, $post_id, $hero_alignment_args = false, $hero_height_args = false)
{
    $attrs = is_array($attrs) ? $attrs : array();

    $hero_heading     = do_shortcode(isset($attrs['heading']) ? $attrs['heading'] : '');
    $hero_description = do_shortcode(isset($attrs['description']) ? $attrs['description'] : '');
    // ___hero_append_event_datetime() lives in modules.php, which is skipped in
    // admin under the blocks-editor template — guard even though the only
    // caller of this function (___hero_modules()) already implies it's loaded.
    if (function_exists('___hero_append_event_datetime')) {
        $hero_description = ___hero_append_event_datetime($post_id, $hero_description);
    }

    $hero_height    = !empty($attrs['height']) ? $attrs['height'] : $hero_height_args;
    $hero_alignment = !empty($attrs['alignment']) ? $attrs['alignment'] : $hero_alignment_args;

    $buttons = array();
    if (!empty($attrs['buttons']) && is_array($attrs['buttons'])) {
        foreach ($attrs['buttons'] as $row) {
            $row = is_array($row) ? $row : array();
            $buttons[] = array(
                'button_type'       => isset($row['type']) ? $row['type'] : '',
                'button_text'       => isset($row['text']) ? $row['text'] : '',
                'button_url'        => isset($row['postId']) ? $row['postId'] : 0,
                'button_url_custom' => isset($row['urlCustom']) ? $row['urlCustom'] : '',
                'button_style'      => isset($row['style']) ? $row['style'] : 'button-accent',
                'button_target'     => isset($row['target']) ? $row['target'] : 'target="_self"',
            );
        }
    }

    // hero_form_image: guides fall back to the featured image only when no
    // image was explicitly chosen — matches ___hero_args_from_meta().
    $form_image_id = !empty($attrs['formImageId']) ? (int) $attrs['formImageId'] : 0;
    if (get_post_type($post_id) == 'guides') {
        $hero_form_image = $form_image_id ? $form_image_id : get_post_thumbnail_id($post_id);
    } else {
        $hero_form_image = $form_image_id;
    }

    // __form() reads $form[0]['id'] / isset($form_product[0]['id']) — the raw
    // Carbon association row shape, reproduced here from the flat block attrs.
    $hero_form         = !empty($attrs['formId']) ? array(array('id' => (int) $attrs['formId'])) : array();
    $hero_form_product = !empty($attrs['formProductId']) ? array(array('id' => (int) $attrs['formProductId'])) : array();

    return array(
        'id'                             => $post_id,
        'hero_heading'                   => $hero_heading,
        'hero_description'               => $hero_description,
        'hero_hidden'                    => !empty($attrs['hidden']),
        'hero_background'                => !empty($attrs['backgroundId']) ? (int) $attrs['backgroundId'] : 0,
        'hero_background_youtube'        => isset($attrs['backgroundYoutube']) ? $attrs['backgroundYoutube'] : '',
        'hero_background_type'           => isset($attrs['backgroundType']) ? $attrs['backgroundType'] : 'self-hosted',
        'hero_background_tablet'         => !empty($attrs['backgroundTabletId']) ? (int) $attrs['backgroundTabletId'] : 0,
        'hero_background_mobile'         => !empty($attrs['backgroundMobileId']) ? (int) $attrs['backgroundMobileId'] : 0,
        'hero_background_youtube_tablet' => isset($attrs['backgroundTabletYoutube']) ? $attrs['backgroundTabletYoutube'] : '',
        'hero_background_youtube_mobile' => isset($attrs['backgroundMobileYoutube']) ? $attrs['backgroundMobileYoutube'] : '',
        'hero_height'                    => $hero_height,
        'hero_alignment'                 => $hero_alignment,
        'breadcrumbs_hidden'             => !empty($attrs['breadcrumbsHidden']),
        'buttons'                        => $buttons,
        'hide_on_list'                   => get__post_meta_by_id($post_id, 'hide_on_list'),
        'hero_form_enable'               => !empty($attrs['formEnable']),
        'hero_form_image'                => $hero_form_image,
        'hero_form_heading'              => isset($attrs['formHeading']) ? $attrs['formHeading'] : '',
        'hero_form_description'          => do_shortcode(isset($attrs['formDescription']) ? $attrs['formDescription'] : ''),
        'hero_form_style'                => isset($attrs['formStyle']) ? $attrs['formStyle'] : '',
        'hero_form'                      => $hero_form,
        'hero_form_type'                 => isset($attrs['formType']) ? $attrs['formType'] : '',
        'hero_form_product'              => $hero_form_product,
        'hero_form_script'               => isset($attrs['formScript']) ? $attrs['formScript'] : '',
        'hero_form_redirect_type'        => isset($attrs['formRedirectType']) ? $attrs['formRedirectType'] : '',
        'hero_form_document_redirect_id' => !empty($attrs['formDocumentRedirectId']) ? (int) $attrs['formDocumentRedirectId'] : false,
        'is_product'                     => function_exists('is_product') ? is_product() : false,
        'cpd_maker'                      => get__post_meta_by_id($post_id, 'cpd_maker'),
        'tquk_logo'                      => get__post_meta_by_id($post_id, 'tquk_logo'),
    );
}

/**
 * Post types where the hero stays HOISTED (rendered by ___hero_modules() at
 * the template's existing position) rather than inline where the block sits —
 * because the_content() renders inside a width-constraining wrapper there.
 * See this file's docblock. Filterable so a future template change can opt a
 * type in or out without editing this file.
 *
 * @return string[]
 */
function coptrz_hero_hoisted_post_types()
{
    return apply_filters('coptrz_hero_hoisted_post_types', array('post'));
}

/**
 * Whether $post_id's hero should render inline, at the block's position —
 * true when it has a coptrz/hero block AND its post type isn't hoisted. False
 * for both hoisted types and posts with no hero block at all (the latter
 * render via the legacy meta fallback in ___hero_modules() regardless of
 * mode, so "false" there isn't itself a signal to hoist anything).
 *
 * @param int $post_id
 * @return bool
 */
function coptrz_hero_renders_inline($post_id)
{
    return coptrz_hero_block_attrs($post_id) !== null
        && !in_array(get_post_type($post_id), coptrz_hero_hoisted_post_types(), true);
}

/**
 * Render filter. Inline post types render the hero here, at the block's
 * position; hoisted post types keep the block storage-only (''), rendered
 * instead by ___hero_modules() at the template's existing position. Guarded
 * with function_exists() since modules.php (___hero_render(),
 * coptrz_hero_args_from_block() is in THIS file so always available) is
 * skipped in admin under the blocks-editor template.
 */
function coptrz_render_hero_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/hero') {
        return $block_content;
    }

    // On product taxonomy archives the main query is products; prefer the
    // linked producttaxonomypages CPT so hero/breadcrumbs use that post.
    $post_id = 0;
    if (function_exists('is_product_taxonomy')
        && is_product_taxonomy()
        && function_exists('__get_product_taxonomy_page')
    ) {
        $term = get_queried_object();
        if ($term && !empty($term->term_id)) {
            $post_id = (int) __get_product_taxonomy_page($term->term_id);
        }
    }
    if (!$post_id) {
        $post_id = (int) get_the_ID();
    }

    if (!$post_id || !coptrz_hero_renders_inline($post_id) || !function_exists('___hero_render')) {
        return '';
    }

    $attrs = isset($block['attrs']) && is_array($block['attrs']) ? $block['attrs'] : array();
    return ___hero_render(coptrz_hero_args_from_block($attrs, $post_id));
}
add_filter('render_block', 'coptrz_render_hero_block', 10, 2);

/**
 * Auto-inserts an empty coptrz/hero block as the first block of every NEW
 * post of the applicable types (coptrz_hero_post_types(),
 * includes/hero-converter.php) — "first block on every page where it's
 * applicable". WordPress applies `default_content` once, when the "Add New"
 * screen creates the initial auto-draft (get_default_post_to_edit()), so this
 * only ever seeds a brand-new editor session — never touches an existing
 * post's content, converted or not. Deliberately shipped only after the
 * hero -> block migration (see includes/hero-converter.php) is under way, so
 * new and existing posts behave the same way.
 */
function coptrz_hero_default_content($content, $post)
{
    if (!function_exists('coptrz_hero_post_types') || !in_array($post->post_type, coptrz_hero_post_types(), true)) {
        return $content;
    }
    if (trim((string) $content) !== '') {
        return $content;
    }
    return '<!-- wp:coptrz/hero /-->';
}
add_filter('default_content', 'coptrz_hero_default_content', 10, 2);
