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
 * Wrap already-serialized child block markup in a core/group that carries the
 * section's existing utility classes, so the theme CSS reproduces the look
 * (the user-chosen "Group with existing classes" strategy).
 *
 * @param string $class            space-separated utility classes (may be '')
 * @param string $inner_serialized serialized inner blocks
 * @return string
 */
function coptrz_block_group($class, $inner_serialized)
{
    $class = trim((string) $class);
    $attrs = array('tagName' => 'section', 'layout' => array('type' => 'constrained'));
    if ($class !== '') {
        $attrs['className'] = $class;
    }
    $div_class = trim('wp-block-group ' . $class);
    return '<!-- wp:group ' . wp_json_encode($attrs) . " -->\n"
        . '<section class="' . esc_attr($div_class) . '">' . "\n"
        . $inner_serialized . "\n"
        . "</section>\n<!-- /wp:group -->";
}

/**
 * Best-effort section utility classes for the group wrapper. INCOMPLETE on
 * purpose: only the author-set custom class is carried for now; the full
 * section_styles → class mapping (padding/background/container width) comes from
 * the conversion guide. (Most sections are not fully-native yet, so they snapshot
 * with their real wrapper intact regardless.)
 *
 * @param array $section
 * @return string
 */
function coptrz_section_classes($section)
{
    return isset($section['section_class']) ? trim((string) $section['section_class']) : '';
}

/**
 * Element-type → mapper registry.
 *
 * Each mapper is callable($item): array[]|null
 *   - Returns an array of parsed-block arrays (each via coptrz_block /
 *     coptrz_block_container), or null when this item type cannot be mapped
 *     (which causes the enclosing section to snapshot to Custom HTML).
 *   - Dynamic elements (layouts, global_widgets, product_compare, etc.) map to
 *     core/shortcode blocks so they stay live.
 *   - Elements with no native block equivalent and no shortcode form (post_grid,
 *     tabs, accordion) are intentionally absent → section snapshots as HTML.
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
                $blocks[] = coptrz_block('core/shortcode', array(), "[layouts id='{$id}']");
            }
        }
        return empty($blocks) ? null : $blocks;
    };

    // Global widgets — each sub-widget maps to its registered shortcode.
    $map['global_widgets'] = function ($item) {
        $widgets = isset($item['global_widgets']) && is_array($item['global_widgets']) ? $item['global_widgets'] : array();
        $sc_map  = array(
            'brands_logo_slider'       => '[brands_logo_slider]',
            'reviews'                  => '[reviews]',
            'drone_servicing'          => '[drone_servicing]',
            'three_year_servicing_plans' => '[three_year_servicing_plans]',
            'remote_support'           => '[remote_support]',
            'testimonials'             => '[testimonials]',
            'latest_from_coptrz'       => '[latest_from_coptrz]',
        );
        $blocks = array();
        foreach ($widgets as $w) {
            $type = isset($w['_type']) ? $w['_type'] : '';
            if ($type === 'case_study_slider') {
                $style    = isset($w['style']) ? (string) $w['style'] : '';
                $sc       = $style ? "[case_study_slider_grid style='{$style}']" : '[case_study_slider_grid]';
            } elseif (isset($sc_map[$type])) {
                $sc = $sc_map[$type];
            } else {
                continue; // unknown widget; skip silently
            }
            $blocks[] = coptrz_block('core/shortcode', array(), $sc);
        }
        return empty($blocks) ? null : $blocks;
    };

    // Product compare → [product_compare id='N'].
    $map['product_compare'] = function ($item) {
        $cp  = isset($item['compareproducts']) && is_array($item['compareproducts']) ? $item['compareproducts'] : array();
        $id  = !empty($cp[0]['id']) ? (int) $cp[0]['id'] : 0;
        return $id ? array(coptrz_block('core/shortcode', array(), "[product_compare id='{$id}']")) : null;
    };

    /* ------------------------------------------------------------------ */
    /*  Composite: buttons                                                 */
    /* ------------------------------------------------------------------ */

    // Buttons → core/buttons (wrapper) + core/button (per item).
    // Popup buttons are skipped (they need Bootstrap modal JS, not a link).
    $map['buttons'] = function ($item) {
        $raw_btns = isset($item['buttons']) && is_array($item['buttons']) ? $item['buttons'] : array();
        if (empty($raw_btns)) {
            return null;
        }
        $btn_blocks = array();
        foreach ($raw_btns as $btn) {
            $type  = isset($btn['button_type']) ? (string) $btn['button_type'] : 'custom';
            $text  = isset($btn['button_text']) ? (string) $btn['button_text'] : '';
            $style = isset($btn['button_style']) ? (string) $btn['button_style'] : '';
            if ($type === 'popups' || $text === '') {
                continue; // popup triggers need Bootstrap JS; skip
            }
            if ($type === 'page') {
                $pid = (int) (isset($btn['button_url']) ? $btn['button_url'] : 0);
                $url = $pid ? (string) get_permalink($pid) : '';
            } else {
                $url = isset($btn['button_url_custom']) ? (string) $btn['button_url_custom'] : '';
            }
            $blank     = isset($btn['button_target']) && strpos((string) $btn['button_target'], '_blank') !== false;
            $btn_attrs = array();
            if ($url !== '') {
                $btn_attrs['url'] = $url;
            }
            if ($style !== '') {
                $btn_attrs['className'] = $style;
            }
            if ($blank) {
                $btn_attrs['linkTarget'] = '_blank';
                $btn_attrs['rel']        = 'noreferrer noopener';
            }
            $link_cls  = trim('wp-block-button__link wp-element-button ' . $style);
            $link_attr = ($url !== '' ? ' href="' . esc_url($url) . '"' : '')
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
        $mappers   = coptrz_block_item_mappers(); // safe: static already populated
        $col_blocks = array();

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

            // Extract column_width from column_styles complex.
            $col_width = '';
            if (!empty($col['column_styles']) && is_array($col['column_styles'])) {
                foreach ($col['column_styles'] as $cs) {
                    if (isset($cs['_type']) && $cs['_type'] === 'column_width' && !empty($cs['column_width'])) {
                        $col_width = (string) $cs['column_width'];
                        break;
                    }
                }
            }
            $col_attrs   = $col_width ? array('className' => $col_width) : array();
            $col_wrap_cls = trim('wp-block-column ' . $col_width);
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
        $align      = isset($item['align_items']) ? (string) $item['align_items'] : '';
        $cols_attrs = $align ? array('className' => $align) : array();
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
 * Convert one section to block markup.
 *  - Every item natively mapped → its blocks wrapped in a section core/group.
 *  - Otherwise → a single Custom HTML snapshot of the whole section (identical to
 *    the HTML converter; keeps shortcodes/[layouts] literal & dynamic).
 *
 * @return array{0:string,1:bool} [markup, was_native]
 */
function coptrz_section_to_blocks($section, $post_id, $field, $key)
{
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

    if ($all_native && !empty($blocks)) {
        $inner = serialize_blocks($blocks);
        return array(coptrz_block_group(coptrz_section_classes($section), $inner), true);
    }

    // Fallback: freeze the whole section as a Custom HTML block.
    $html = trim(___sections($field, $post_id, $key));
    $html = preg_replace('/\[product_add_to_cart\s+id=([\'"])\1[^\]]*\]/', '', $html);
    if ($html === '') {
        return array('', false);
    }
    return array("<!-- wp:html -->\n" . $html . "\n<!-- /wp:html -->", false);
}

/**
 * Convert one non-product post's sections into native blocks (mode "blocks").
 * Mirrors coptrz_convert_post_sections() but emits per-element blocks. Sets the
 * same converted flag (so rendering routes identically) plus `_coptrz_sections_mode`
 * = 'blocks' for reporting, preserves `_sections`, and switches `page` posts to
 * the Gutenberg template.
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
    if ($src->post_type === 'product') {
        $report['warnings'][] = 'Native-block conversion is non-product only (products use the HTML repeater).';
        return $report;
    }
    if (!$dry_run && coptrz_sections_is_converted($post_id)) {
        $report['skipped'] = true;
        $report['warnings'][] = 'Already converted — skipped to avoid duplicating content.';
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
            list($markup, $was_native) = coptrz_section_to_blocks($section, $post_id, $field, $key);
            if ($markup === '') {
                continue;
            }
            $chunks[] = $markup;
            $was_native ? $native++ : $snap++;
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
        if ($src->post_type === 'page') {
            $report['template'] = 'templates/page-gutenberg.php';
            if (!$dry_run) {
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
            <p class="description" style="margin-top:0;"><?php esc_html_e('Freeze this post’s sections. Original data is kept (reversible).', 'coptrz-theme'); ?></p>
            <p style="margin-bottom:6px;">
                <strong style="display:block;"><?php esc_html_e('Custom HTML', 'coptrz-theme'); ?></strong>
                <button type="button" class="button coptrz-conv__dry" data-mode="html"><?php esc_html_e('Dry run', 'coptrz-theme'); ?></button>
                <button type="button" class="button button-primary coptrz-conv__go" data-mode="html"><?php esc_html_e('Convert', 'coptrz-theme'); ?></button>
            </p>
            <?php if ($post->post_type !== 'product') : ?>
            <p style="margin-bottom:6px;">
                <strong style="display:block;"><?php esc_html_e('Native blocks', 'coptrz-theme'); ?></strong>
                <button type="button" class="button coptrz-conv__dry" data-mode="blocks"><?php esc_html_e('Dry run', 'coptrz-theme'); ?></button>
                <button type="button" class="button button-primary coptrz-conv__go" data-mode="blocks"><?php esc_html_e('Convert', 'coptrz-theme'); ?></button>
            </p>
            <?php endif; ?>
            <div class="coptrz-conv__out" style="font:12px/1.5 monospace;max-height:220px;overflow:auto;"></div>
        <?php endif; ?>
    </div>
    <script>
    (function () {
        var box = document.currentScript.previousElementSibling;
        if (!box || !box.classList.contains('coptrz-conv')) { return; }
        function run(dry, mode) {
            var out = box.querySelector('.coptrz-conv__out');
            out.textContent = '…';
            var body = new URLSearchParams({
                action: 'coptrz_convert_sections',
                nonce: box.dataset.nonce,
                post: box.dataset.post,
                dry: dry ? '1' : '0',
                mode: mode || 'html'
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
            b.addEventListener('click', function () { run(true, this.dataset.mode); });
        });
        box.querySelectorAll('.coptrz-conv__go').forEach(function (b) {
            b.addEventListener('click', function () {
                var mode = this.dataset.mode;
                var msg = mode === 'blocks' ? 'Convert this post’s sections to native blocks?' : 'Convert this post’s sections to HTML?';
                if (confirm(msg)) { run(false, mode); }
            });
        });
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
    $dry  = !empty($_POST['dry']) && $_POST['dry'] === '1';
    $mode = (isset($_POST['mode']) && $_POST['mode'] === 'blocks') ? 'blocks' : 'html';
    $report = ($mode === 'blocks')
        ? coptrz_convert_post_sections_to_blocks($post_id, $dry)
        : coptrz_convert_post_sections($post_id, $dry);
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
    $mode    = (isset($_POST['coptrz_mode']) && $_POST['coptrz_mode'] === 'blocks') ? 'blocks' : 'html';
    $did     = array();

    if ($action && check_admin_referer('coptrz_bulk_convert')) {
        // Always an explicit selection of post IDs (gathered via the name search).
        preg_match_all('/\d+/', $ids_raw, $m);
        $ids   = array_values(array_unique(array_map('intval', $m[0])));
        $limit = 50; // safety cap per run
        foreach (array_slice($ids, 0, $limit) as $pid) {
            $did[] = ($mode === 'blocks')
                ? coptrz_convert_post_sections_to_blocks($pid, ($action === 'dry'))
                : coptrz_convert_post_sections($pid, ($action === 'dry'));
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
                <label><strong><?php esc_html_e('Convert to:', 'coptrz-theme'); ?></strong>
                    <select name="coptrz_mode">
                        <option value="html" <?php selected($mode, 'html'); ?>><?php esc_html_e('Custom HTML blocks', 'coptrz-theme'); ?></option>
                        <option value="blocks" <?php selected($mode, 'blocks'); ?>><?php esc_html_e('Native Gutenberg blocks (non-product)', 'coptrz-theme'); ?></option>
                    </select>
                </label>
                <span class="description"><?php esc_html_e('Native blocks map each element to a core block where available, snapshotting the rest.', 'coptrz-theme'); ?></span>
            </p>

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
