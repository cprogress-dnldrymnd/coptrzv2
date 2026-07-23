<?php

/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 *
 * Native Gutenberg block equivalents of the standalone header elements
 * (template-parts/header/*.php) — same `save: null` + server-side
 * `render_block` shape as legacy-blocks.php. These are drop-in editor blocks,
 * NOT a rewrite of the header itself: header.php still renders the header the
 * same way it always has, via header-left.php / header-menu.php /
 * header-right.php.
 *
 * coptrz_header_icons_html() and coptrz_header_cta_html() are the single
 * source of truth for the account/academy/cart/burger icon cluster and the
 * two theme-option CTA buttons — both header-right.php AND the
 * coptrz/header-icons + coptrz/header-cta blocks call them, so the blocks can
 * never drift from what the real header renders. coptrz_announcement_banner_html()
 * defaults reproduce the promo banner hardcoded in header.php, with the
 * link/image URLs overridable per block instance.
 *
 * site-logo and header-menu blocks delegate straight to the existing
 * `[site_logo]` shortcode (shortcodes.php) and `header_menu()` (menus.php) —
 * both are loaded unconditionally (not skipped in admin under the
 * blocks-editor template), so no function_exists guard is needed for those
 * two. elements.php/svg.php (SVG, __icon(), __button(), etc) also load
 * unconditionally now (_required_files.php) — only modules.php/ajax.php stay
 * conditional there, which is what the guards below (and in shortcodes.php)
 * protect against.
 */

/**
 * Account / Academy / Cart / mobile-menu-burger icon cluster — mirrors
 * template-parts/header/header-right.php's icon markup exactly.
 *
 * @param array $args showAccount, showAcademy, showCart, showBurger (bool, default true)
 * @return string
 */
function coptrz_header_icons_html($args = array())
{
    if (!class_exists('SVG')) {
        return '';
    }

    $args = wp_parse_args($args, array(
        'showAccount' => true,
        'showAcademy' => true,
        'showCart'    => true,
        'showBurger'  => true,
    ));

    $SVG  = new SVG;
    $html = '';

    if ($args['showAccount'] && get_post_type() != 'rentals' && get_the_ID() != 292371) {
        $html .= '<div class="col-auto d-flex align-items-center account">';
        $html .= '<a href="' . esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))) . '" class="header-icon account-icon text-white d-flex align-items-center" aria-label="My account">';
        $html .= $SVG->user();
        $html .= '</a>';
        $html .= '</div>';
    }

    if ($args['showAcademy']) {
        $html .= '<div class="col-auto d-flex align-items-center account">';
        $html .= '<a target="_blank" rel="noopener noreferrer" href="https://www.coptrzacademy-usp.io/login" class="header-icon account-icon text-white d-flex align-items-center" aria-label="Coptrz Academy login">';
        $html .= $SVG->academy();
        $html .= '</a>';
        $html .= '</div>';
    }

    if ($args['showCart']) {
        $html .= '<div class="col-auto d-flex align-items-center mini-cart me-3">';
        if (get_post_type() == 'rentals' || get_the_ID() == 292371 || get_the_ID() == 292384) {
            $html .= do_shortcode('[booqable_cart_button href="' . get_site_url() . '/rental-basket"]');
        }
        $html .= '</div>';
    }

    if ($args['showBurger']) {
        $html .= '<div class="col-auto d-flex align-items-center d-lg-none">';
        $html .= '<button class="menu-burger" type="button" data-bs-toggle="offcanvas" data-bs-target="#offCanvasMenu" aria-controls="offCanvasMenu">';
        $html .= '<div class="icon"><div class="menu"></div></div>';
        $html .= '</button>';
        $html .= '</div>';
    }

    return $html;
}

/**
 * The two theme-option header CTA buttons — mirrors
 * template-parts/header/header-right.php's button markup exactly, including
 * render order (Button Two, then Button One).
 *
 * @param array $args showButtonOne, showButtonTwo (bool, default true)
 * @return string
 */
function coptrz_header_cta_html($args = array())
{
    if (!function_exists('__button')) {
        return '';
    }

    $args = wp_parse_args($args, array(
        'showButtonOne' => true,
        'showButtonTwo' => true,
    ));

    $html = '';

    if ($args['showButtonTwo']) {
        $html .= do_shortcode(__button(array(
            'button_type'       => get__theme_option('header_2_button_type'),
            'button_text'       => get__theme_option('header_2_button_text'),
            'button_url'        => get__theme_option('header_2_button_url'),
            'button_url_custom' => get__theme_option('header_2_button_url_custom'),
            'button_style'      => get__theme_option('header_2_button_style') . ' col-auto button-accent button-small d-none d-lg-block buttton-sore button-mobile',
            'button_target'     => get__theme_option('header_2_button_target'),
        )));
    }

    if ($args['showButtonOne']) {
        $html .= do_shortcode(__button(array(
            'button_type'       => get__theme_option('header_button_type'),
            'button_text'       => get__theme_option('header_button_text'),
            'button_url'        => get__theme_option('header_button_url'),
            'button_url_custom' => get__theme_option('header_button_url_custom'),
            'button_style'      => get__theme_option('header_button_style') . ' col-auto button-accent button-small d-none d-lg-block button-mobile',
            'button_target'     => get__theme_option('header_button_target'),
        )));
    }

    return $html;
}

/**
 * The promo announcement banner — defaults reproduce the markup hardcoded
 * above <header> in header.php exactly; link/desktop-image/mobile-image are
 * overridable per block instance.
 *
 * @param array $args linkUrl, desktopImageUrl, mobileImageUrl (string)
 * @return string
 */
function coptrz_announcement_banner_html($args = array())
{
    $args = wp_parse_args($args, array(
        'linkUrl'         => 'https://shop.coptrz.com/collections/rpc-l1-part-a-summer-sale?utm_source=coptrz.com&utm_medium=email&utm_campaign=2026_website_summer-sale_coptrz-rpc-l1-part-a-courses_b2c',
        'desktopImageUrl' => 'https://coptrz.com/wp-content/uploads/2026/06/announcement-bar-desktop-2.svg',
        'mobileImageUrl'  => 'https://coptrz.com/wp-content/uploads/2026/06/announcement-bar-mobile.svg',
    ));

    if ($args['desktopImageUrl'] === '' && $args['mobileImageUrl'] === '') {
        return '';
    }

    $html = '<div class="banner-topbar rounded-corner bg-primary mx-20px mt-20px d-flex align-items-center justify-content-center text-center">';

    if ($args['desktopImageUrl'] !== '') {
        $html .= '<a href="' . esc_url($args['linkUrl']) . '" target="_blank" class="desktop-only">';
        $html .= '<img src="' . esc_url($args['desktopImageUrl']) . '" alt="RPC Promo Banner">';
        $html .= '</a>';
    }

    if ($args['mobileImageUrl'] !== '') {
        $html .= '<a href="' . esc_url($args['linkUrl']) . '" target="_blank" class="mobile-only d-none">';
        $html .= '<img src="' . esc_url($args['mobileImageUrl']) . '" alt="RPC Promo Banner">';
        $html .= '</a>';
    }

    $html .= '</div>';

    return $html;
}

function coptrz_render_site_logo_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/site-logo') {
        return $block_content;
    }
    if (!shortcode_exists('site_logo')) {
        return $block_content;
    }
    return do_shortcode('[site_logo]');
}
add_filter('render_block', 'coptrz_render_site_logo_block', 10, 2);

function coptrz_render_header_menu_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/header-menu') {
        return $block_content;
    }
    if (!function_exists('header_menu')) {
        return $block_content;
    }
    return header_menu();
}
add_filter('render_block', 'coptrz_render_header_menu_block', 10, 2);

function coptrz_render_header_icons_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/header-icons') {
        return $block_content;
    }
    $attrs = isset($block['attrs']) ? $block['attrs'] : array();
    return coptrz_header_icons_html(array(
        'showAccount' => !isset($attrs['showAccount']) || (bool) $attrs['showAccount'],
        'showAcademy' => !isset($attrs['showAcademy']) || (bool) $attrs['showAcademy'],
        'showCart'    => !isset($attrs['showCart']) || (bool) $attrs['showCart'],
        'showBurger'  => !isset($attrs['showBurger']) || (bool) $attrs['showBurger'],
    ));
}
add_filter('render_block', 'coptrz_render_header_icons_block', 10, 2);

function coptrz_render_header_cta_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/header-cta') {
        return $block_content;
    }
    $attrs = isset($block['attrs']) ? $block['attrs'] : array();
    return coptrz_header_cta_html(array(
        'showButtonOne' => !isset($attrs['showButtonOne']) || (bool) $attrs['showButtonOne'],
        'showButtonTwo' => !isset($attrs['showButtonTwo']) || (bool) $attrs['showButtonTwo'],
    ));
}
add_filter('render_block', 'coptrz_render_header_cta_block', 10, 2);

function coptrz_render_announcement_banner_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'coptrz/announcement-banner') {
        return $block_content;
    }
    $attrs = isset($block['attrs']) ? $block['attrs'] : array();

    $overrides = array();
    foreach (array('linkUrl', 'desktopImageUrl', 'mobileImageUrl') as $key) {
        if (isset($attrs[$key]) && $attrs[$key] !== '') {
            $overrides[$key] = (string) $attrs[$key];
        }
    }

    return coptrz_announcement_banner_html($overrides);
}
add_filter('render_block', 'coptrz_render_announcement_banner_block', 10, 2);
