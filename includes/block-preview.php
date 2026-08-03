<?php

/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 *
 * Live-preview REST route for every `save: null` coptrz/* block whose real
 * markup only exists via a render_block filter: the "legacy wrapper" blocks
 * (coptrz/tabs-legacy, coptrz/accordion-legacy, coptrz/icon-legacy,
 * coptrz/spec-box-legacy, coptrz/divider-legacy, coptrz/cf7-legacy,
 * coptrz/gallery, coptrz/product-slider, coptrz/drone-servicing-grid,
 * coptrz/events-widget, coptrz/product, coptrz/product-compare,
 * coptrz/global-post-box — includes/legacy-blocks.php), coptrz/hero
 * (includes/hero-block.php), coptrz/global-widget / coptrz/post-grid /
 * coptrz/layouts (functions.php), and the header element blocks
 * (coptrz/site-logo, coptrz/header-menu, coptrz/header-icons,
 * coptrz/header-cta, coptrz/announcement-banner — includes/header-blocks.php).
 *
 * coptrz/section-split is the one server-rendered coptrz/* block deliberately
 * NOT in this registry — its renderer always returns '' by design (a
 * structural marker only, functions.php coptrz_render_section_split_block()),
 * so there is nothing to preview.
 *
 * Not core's ServerSideRender/`/wp/v2/block-renderer/*`: that route only
 * exists for blocks registered SERVER-SIDE via register_block_type() with a
 * render_callback, and every coptrz/* block here is registered client-side
 * only (see assets/js/coptrz-*-block.js). Rather than add a parallel
 * server-side registration + attribute schema per block (a second source of
 * truth that can drift from the JS `attributes`), this route calls each
 * block's EXISTING render function directly, from attributes the editor sends
 * — the front-end render_block chain is untouched.
 *
 * coptrz/hero is a deliberate exception to "call the existing render
 * function": coptrz_render_hero_block() (includes/hero-block.php) returns ''
 * for hoisted post types (currently just `post`) because the hero renders
 * elsewhere in the template for those — that gate is about front-end
 * PLACEMENT, not about whether the hero has renderable content. The preview
 * calls ___hero_render(coptrz_hero_args_from_block(...)) directly instead, so
 * `post` heroes preview too.
 */

/**
 * Block name -> renderer callable, `function (array $attrs, int $post_id): string`.
 * The whitelist for /dd/v1/block-preview — an unregistered block name is
 * rejected before any renderer runs. Filterable so a future block can be
 * added to the preview system without editing this file.
 *
 * @return array<string, callable>
 */
function coptrz_block_preview_renderers()
{
    $renderers = array(
        'coptrz/tabs-legacy' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_tabs_legacy_block')) {
                return '';
            }
            return coptrz_render_tabs_legacy_block('', array('blockName' => 'coptrz/tabs-legacy', 'attrs' => $attrs));
        },
        'coptrz/accordion-legacy' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_accordion_legacy_block')) {
                return '';
            }
            return coptrz_render_accordion_legacy_block('', array('blockName' => 'coptrz/accordion-legacy', 'attrs' => $attrs));
        },
        'coptrz/icon-legacy' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_icon_legacy_block')) {
                return '';
            }
            return coptrz_render_icon_legacy_block('', array('blockName' => 'coptrz/icon-legacy', 'attrs' => $attrs));
        },
        'coptrz/spec-box-legacy' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_spec_box_legacy_block')) {
                return '';
            }
            return coptrz_render_spec_box_legacy_block('', array('blockName' => 'coptrz/spec-box-legacy', 'attrs' => $attrs));
        },
        'coptrz/divider-legacy' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_divider_legacy_block')) {
                return '';
            }
            return coptrz_render_divider_legacy_block('', array('blockName' => 'coptrz/divider-legacy', 'attrs' => $attrs));
        },
        'coptrz/cf7-legacy' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_cf7_legacy_block')) {
                return '';
            }
            return coptrz_render_cf7_legacy_block('', array('blockName' => 'coptrz/cf7-legacy', 'attrs' => $attrs));
        },
        'coptrz/global-widget' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_global_widget_block')) {
                return '';
            }
            return coptrz_render_global_widget_block('', array('blockName' => 'coptrz/global-widget', 'attrs' => $attrs));
        },
        'coptrz/hero' => function ($attrs, $post_id) {
            if (!function_exists('___hero_render') || !function_exists('coptrz_hero_args_from_block')) {
                return '';
            }
            return ___hero_render(coptrz_hero_args_from_block($attrs, $post_id));
        },
        'coptrz/post-grid' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_post_grid_block')) {
                return '';
            }
            return coptrz_render_post_grid_block('', array('blockName' => 'coptrz/post-grid', 'attrs' => $attrs));
        },
        'coptrz/layouts' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_layouts_block')) {
                return '';
            }
            return coptrz_render_layouts_block('', array('blockName' => 'coptrz/layouts', 'attrs' => $attrs));
        },
        'coptrz/gallery' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_gallery_block')) {
                return '';
            }
            return coptrz_render_gallery_block('', array('blockName' => 'coptrz/gallery', 'attrs' => $attrs));
        },
        /**
         * Product Slider's `main_query` source resolves its category from
         * `is_product_taxonomy()`, else from `$_GET['post']` (the admin editor's
         * post id — see includes/legacy-blocks.php + the legacy mirror in
         * includes/modules.php). The preview REST request carries no `post` query
         * arg, so that branch would preview empty; shim `$_GET['post']` from this
         * route's own `post_id` for the duration of the call only.
         */
        'coptrz/product-slider' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_product_slider_block')) {
                return '';
            }
            $had_get_post = array_key_exists('post', $_GET);
            $prev_get_post = $had_get_post ? $_GET['post'] : null;
            if ($post_id) {
                $_GET['post'] = $post_id;
            }
            try {
                return coptrz_render_product_slider_block('', array('blockName' => 'coptrz/product-slider', 'attrs' => $attrs));
            } finally {
                if ($had_get_post) {
                    $_GET['post'] = $prev_get_post;
                } else {
                    unset($_GET['post']);
                }
            }
        },
        'coptrz/drone-servicing-grid' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_drone_servicing_grid_block')) {
                return '';
            }
            return coptrz_render_drone_servicing_grid_block('', array('blockName' => 'coptrz/drone-servicing-grid', 'attrs' => $attrs));
        },
        /**
         * [event_countdown] reads the event post's start/end meta off
         * get_the_ID() — the route's own $GLOBALS['post']/setup_postdata() setup
         * from `post_id` already covers that. The countdown JS is stripped by
         * this route's <script> filter below, so the digits render static (00s),
         * same static-approximation as every other JS-driven preview here.
         */
        'coptrz/events-widget' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_events_widget_block')) {
                return '';
            }
            return coptrz_render_events_widget_block('', array('blockName' => 'coptrz/events-widget', 'attrs' => $attrs));
        },
        'coptrz/product' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_product_block')) {
                return '';
            }
            return coptrz_render_product_block('', array('blockName' => 'coptrz/product', 'attrs' => $attrs));
        },
        'coptrz/product-compare' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_product_compare_block')) {
                return '';
            }
            return coptrz_render_product_compare_block('', array('blockName' => 'coptrz/product-compare', 'attrs' => $attrs));
        },
        'coptrz/global-post-box' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_global_post_box_block')) {
                return '';
            }
            return coptrz_render_global_post_box_block('', array('blockName' => 'coptrz/global-post-box', 'attrs' => $attrs));
        },
        'coptrz/site-logo' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_site_logo_block')) {
                return '';
            }
            return coptrz_render_site_logo_block('', array('blockName' => 'coptrz/site-logo', 'attrs' => $attrs));
        },
        'coptrz/header-menu' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_header_menu_block')) {
                return '';
            }
            return coptrz_render_header_menu_block('', array('blockName' => 'coptrz/header-menu', 'attrs' => $attrs));
        },
        'coptrz/header-icons' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_header_icons_block')) {
                return '';
            }
            return coptrz_render_header_icons_block('', array('blockName' => 'coptrz/header-icons', 'attrs' => $attrs));
        },
        'coptrz/header-cta' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_header_cta_block')) {
                return '';
            }
            return coptrz_render_header_cta_block('', array('blockName' => 'coptrz/header-cta', 'attrs' => $attrs));
        },
        'coptrz/announcement-banner' => function ($attrs, $post_id) {
            if (!function_exists('coptrz_render_announcement_banner_block')) {
                return '';
            }
            return coptrz_render_announcement_banner_block('', array('blockName' => 'coptrz/announcement-banner', 'attrs' => $attrs));
        },
    );

    return apply_filters('coptrz_block_preview_renderers', $renderers);
}

/**
 * POST /dd/v1/block-preview — renders one block's current (possibly unsaved)
 * attributes server-side for the editor canvas. Gated to edit_posts (same as
 * the other dd/v1 routes, hooks.php), plus edit_post on the specific post_id
 * when one is supplied.
 *
 * `modules.php` (which every renderer above needs) is skipped in admin only
 * when the blocks-editor template is active for the resolved post
 * (dd_is_blocks_editor_template_active(), functions.php) — its REST-context
 * post-id resolution matches `/wp-json/wp/v2/…` only, never
 * `/wp-json/dd/v1/…`, so that guard never fires for this route and
 * modules.php loads unconditionally. Every renderer still keeps its own
 * function_exists() guard regardless.
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response|WP_Error
 */
function coptrz_rest_block_preview($request)
{
    $name  = (string) $request->get_param('name');
    $attrs = $request->get_param('attributes');
    $attrs = is_array($attrs) ? $attrs : array();

    $renderers = coptrz_block_preview_renderers();
    if (!isset($renderers[$name])) {
        return new WP_Error('coptrz_block_preview_unknown_block', 'Unknown block for preview.', array('status' => 400));
    }

    $post_id = (int) $request->get_param('post_id');
    if ($post_id && !current_user_can('edit_post', $post_id)) {
        return new WP_Error('coptrz_block_preview_forbidden', 'You cannot preview this post.', array('status' => 403));
    }

    $restore_post = null;
    if ($post_id && get_post($post_id)) {
        $restore_post = isset($GLOBALS['post']) ? $GLOBALS['post'] : null;
        $GLOBALS['post'] = get_post($post_id);
        setup_postdata($GLOBALS['post']);
    } else {
        $post_id = 0;
    }

    try {
        ob_start();
        $html = (string) call_user_func($renderers[$name], $attrs, $post_id);
        ob_end_clean();
    } catch (Throwable $e) {
        ob_end_clean();
        $html = '';
    } finally {
        if ($post_id) {
            wp_reset_postdata();
            if ($restore_post) {
                $GLOBALS['post'] = $restore_post;
            } else {
                unset($GLOBALS['post']);
            }
        }
    }

    // <script>/<style type="application/ld+json"> never execute inside
    // React's dangerouslySetInnerHTML — strip them so the editor canvas
    // isn't left holding dead tabs/accordion bootstrap JS or a schema blob.
    // Plain <style> tags (e.g. ___tab_modules()'s per-instance scoped CSS)
    // are kept — the preview needs them.
    $html = (string) preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html);

    return rest_ensure_response(array(
        'html'     => $html,
        'rendered' => trim($html) !== '',
    ));
}

function coptrz_register_block_preview_rest_route()
{
    register_rest_route('dd/v1', '/block-preview', array(
        'methods'             => 'POST',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        },
        'callback'            => 'coptrz_rest_block_preview',
        'args'                => array(
            'name'       => array(
                'required'          => true,
                'validate_callback' => function ($value) {
                    return is_string($value) && isset(coptrz_block_preview_renderers()[$value]);
                },
            ),
            'attributes' => array('required' => false),
            'post_id'    => array('required' => false),
        ),
    ));
}
add_action('rest_api_init', 'coptrz_register_block_preview_rest_route');
