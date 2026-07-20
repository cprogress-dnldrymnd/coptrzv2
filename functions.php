<?php
/*-----------------------------------------------------------------------------------*/
/* Define the version so we can easily replace it throughout the theme
/*-----------------------------------------------------------------------------------*/
define('coptz_version', 5.5);
define('theme_dir', get_template_directory_uri() . '/');
define('assets_dir', theme_dir . 'assets/');
define('image_dir', assets_dir . 'images/');
define('vendor_dir', assets_dir . 'vendor/');
/*-----------------------------------------------------------------------------------*/
/* After Theme Setup
/*-----------------------------------------------------------------------------------*/

function action_after_setup_theme()
{
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce');
    global $popups_id, $layouts_global, $product_taxonomy_page;
    $popups_id = [];
    $layouts_global = [];
    $product_taxonomy_page = [];
}
add_action('after_setup_theme', 'action_after_setup_theme');

/*-----------------------------------------------------------------------------------*/
/* Meta Shim — native replacement for Carbon Fields (read/write + admin UI)
/*
/* Loads the standalone shim (includes/meta-shim/), registers every field
/* definition (includes/post-meta.php now builds CoptrzTheme\MetaShim\Container /
/* Field) and boots the native meta boxes. This supersedes the old
/* carbon_fields_register_fields bootstrap; Carbon Fields can be deactivated once
/* verified. See includes/meta-shim/ and the self-test in
/* includes/meta-shim/self-test.php.
/*-----------------------------------------------------------------------------------*/
require_once __DIR__ . '/includes/meta-shim/Key_Formatter.php';
require_once __DIR__ . '/includes/meta-shim/Field.php';
require_once __DIR__ . '/includes/meta-shim/Container.php';
require_once __DIR__ . '/includes/meta-shim/View.php';
require_once __DIR__ . '/includes/meta-shim/Writer.php';
require_once __DIR__ . '/includes/meta-shim/Container_Admin.php';
require_once __DIR__ . '/includes/meta-reader.php';
require_once __DIR__ . '/includes/section-converter.php';
if (is_admin()) {
    // Transitional parity checker (inert unless ?coptrz_meta_selftest=<id>).
    require_once __DIR__ . '/includes/meta-shim/self-test.php';
}

/**
 * Register all custom field definitions through the meta shim, then wire up the
 * native admin UI. Runs early on every request so the field-definition index is
 * available to both front-end reads (the Reader) and the admin renderer/saver.
 *
 * The blocks-editor exclusion preserves the previous behaviour: when the
 * page-blocks-editor template is active, field definitions are only loaded on
 * the front end (never in wp-admin).
 */
function tissue_paper_register_custom_fields()
{
    $is_blocks_editor = function_exists('dd_is_blocks_editor_template_active') && dd_is_blocks_editor_template_active();
    if (!$is_blocks_editor) {
        require_once('includes/post-meta.php');
    } else {
        if (!is_admin()) {
            require_once('includes/post-meta.php');
        }
    }
    // Register the product HTML-sections repeater + retire the legacy builder UI,
    // then boot the admin lifecycle.
    if (function_exists('coptrz_register_html_sections_fields')) {
        coptrz_register_html_sections_fields();
    }
    // Register the always-on Layout / Custom CSS / Hide Before Footer boxes
    // regardless of the blocks-editor branch above, so they appear on every
    // template (including page-blocks-editor.php, on which post-meta.php is
    // skipped in admin). MUST run before boot() so the containers are indexed.
    coptrz_register_global_layout_fields();
    \CoptrzTheme\MetaShim\Container_Admin::boot();
}
add_action('after_setup_theme', 'tissue_paper_register_custom_fields', 20);

/**
 * Register the per-page "Layout" side box (Hide Header / Hide Footer), the
 * site-wide "Custom CSS" box, and the "Hide Before Footer Layout" side box
 * through the native meta shim — unconditionally, on every request.
 *
 * These live here rather than in includes/post-meta.php because that file is not
 * loaded in admin when the page-blocks-editor.php template is active (see
 * dd_is_blocks_editor_template_active), which would hide these controls on those
 * pages. Registering them from tissue_paper_register_custom_fields (which always
 * runs, on both branches) keeps the containers indexed on every template and on
 * the frontend, so header.php / footer.php / action_wp_head() can read the
 * hide_header / hide_footer / hidden_layouts / custom_css meta. Keep the fields
 * defined ONLY here to avoid a duplicate container (they were removed from
 * includes/post-meta.php for this reason).
 */
function coptrz_register_global_layout_fields()
{
    if (!class_exists('\CoptrzTheme\MetaShim\Container')) {
        return;
    }

    // Per-page Hide Header / Hide Footer.
    \CoptrzTheme\MetaShim\Container::make('post_meta', 'Layout')
        ->where('post_type', '=', 'page')
        ->or_where('post_type', '=', 'post')
        ->or_where('post_type', '=', 'product')
        ->or_where('post_type', '=', 'guides')
        ->or_where('post_type', '=', 'casestudies')
        ->or_where('post_type', '=', 'industries')
        ->or_where('post_type', '=', 'capabilities')
        ->or_where('post_type', '=', 'events')
        ->or_where('post_type', '=', 'rentals')
        ->or_where('post_type', '=', 'landingpages')
        ->set_context('side')
        ->add_fields(array(
            \CoptrzTheme\MetaShim\Field::make('checkbox', 'hide_header', __('Hide Header'))
                ->set_help_text('Hide the site header on this page.'),
            \CoptrzTheme\MetaShim\Field::make('checkbox', 'hide_footer', __('Hide Footer'))
                ->set_help_text('Hide the site footer on this page.')
        ));

    // Site-wide Custom CSS (every post type).
    \CoptrzTheme\MetaShim\Container::make('post_meta', __('Custom CSS'))
        ->set_priority('low')
        ->add_fields(array(
            \CoptrzTheme\MetaShim\Field::make('textarea', 'custom_css', __('Custom CSS'))
                ->set_classes('inline-field')
        ));

    // Hide Before Footer Layout — options are published `layouts` posts flagged
    // for the before_footer display location; footer.php excludes the chosen ids
    // via get__post_meta('hidden_layouts').
    $before_footer_options = array();
    $layouts = get_posts(array(
        'numberposts' => -1,
        'post_type'   => 'layouts',
        'fields'      => 'ids',
        'orderby'     => 'menu_order',
        'order'       => 'ASC',
        'meta_query'  => array(
            array(
                'key'   => '_display_location',
                'value' => 'before_footer',
            ),
        ),
    ));
    foreach ($layouts as $layout) {
        $before_footer_options[$layout] = get_the_title($layout);
    }

    \CoptrzTheme\MetaShim\Container::make('post_meta', 'Hide Before Footer Layout')
        ->where('post_type', '=', 'page')
        ->or_where('post_type', '=', 'guides')
        ->or_where('post_type', '=', 'casestudies')
        ->or_where('post_type', '=', 'events')
        ->or_where('post_type', '=', 'landingpages')
        ->set_context('side')
        ->add_fields(array(
            \CoptrzTheme\MetaShim\Field::make('set', 'hidden_layouts', __(''))
                ->set_options($before_footer_options)
        ));
}
// NOTE: these wrappers now delegate to the native meta shim (coptrz_get_*),
// which reconstructs the identical nested arrays Carbon Fields returned. Keep
// using these wrappers throughout the theme rather than calling the shim direct.
function get__post_meta($value)
{
    return coptrz_get_the_post_meta($value);
}

function get__term_meta($term_id, $value)
{
    // Unchanged: native single read for simple (scalar) term fields.
    return get_term_meta($term_id, '_' . $value, true);
}

function get___term_meta($term_id, $value)
{
    return coptrz_get_term_meta($term_id, $value);
}

function get__post_meta_by_id($id, $value)
{
    return coptrz_get_post_meta($id, $value);
}
function get__theme_option($value)
{
    return coptrz_get_theme_option($value);
}

/*-----------------------------------------------------------------------------------*/
/* Enqueue Styles and Scripts
/*-----------------------------------------------------------------------------------*/
function enqueue_scripts()
{
    //wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
    wp_enqueue_style('intl-tel', 'https://cdn.jsdelivr.net/npm/intl-tel-input@21.2.7/build/css/intlTelInput.css', NULL, coptz_version);
    wp_enqueue_script('intlTelInput', 'https://cdn.jsdelivr.net/npm/intl-tel-input@21.2.7/build/js/intlTelInput.js', NULL, coptz_version);

    //wp_enqueue_script('swiper', vendor_dir . 'swiper/js/swiper-bundle.min.js');
    wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js');
    wp_enqueue_script('bootstrap', vendor_dir . 'bootstrap/js/bootstrap.min.js');
    //wp_enqueue_script('intlTelInput', vendor_dir.'intlTelInput/js/intlTelInput.min.js');
    wp_register_script('main', assets_dir . 'js/main.js', NULL, coptz_version);
    wp_localize_script(
        'main',
        'ajax_object',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
        )
    );
    wp_enqueue_script('main');

    if (is_product() || get_post_type() == 'rentals' || get_post_type() == 'landingpages') {
        wp_register_script('single-product', assets_dir . 'js/single-product.js', NULL, coptz_version);
        wp_localize_script(
            'single-product',
            'ajax_object',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
            )
        );
        wp_enqueue_script('single-product');
    }

    if (get_post_type() == 'events') {
        wp_enqueue_script('single-event', assets_dir . 'js/single-event.js', NULL, coptz_version);
    }
    /*
		if (is_checkout()) {
			wp_enqueue_style('checkout-style', assets_dir . 'scss/checkout/checkout.css', NULL, coptz_version);
			wp_register_script('checkout-js', assets_dir . 'js/checkout.js', ['jquery'], coptz_version);

			$countries_obj = new WC_Countries();

			// Get the array of allowed countries (key = country code, value = country name)
			$allowed_countries = $countries_obj->get_allowed_countries();
			$countries = [];
			foreach ($allowed_countries as $key => $country) {
				$countries[] = $key;
			}

			wp_localize_script('checkout-js', 'countries', $countries);
			wp_enqueue_script('checkout-js');
		}
		else {
			wp_enqueue_style('style', theme_dir . 'style.css', NULL, coptz_version);
		}
	*/
    if (get_page_template_slug() != 'templates/page-old-landing.php') {
        wp_enqueue_style('style', theme_dir . 'style.css', NULL, coptz_version);
    } else {
        wp_enqueue_style('landing-style', theme_dir . 'landing.css', NULL, coptz_version);
    }
}

add_action('wp_enqueue_scripts', 'enqueue_scripts', 99999);

/**
 * Enqueue the block extension script in the editor.
 */
function digitally_disruptive_enqueue_swiper_editor_assets()
{
    wp_enqueue_script(
        'dd-query-swiper-editor',
        get_template_directory_uri() . '/assets/js/extend-swiper.js', // Adjust path
        array('wp-blocks', 'wp-element', 'wp-hooks', 'wp-editor', 'wp-components', 'wp-block-editor'),
        filemtime(get_template_directory() . '/assets/js/extend-swiper.js'),
        true
    );
    wp_enqueue_script(
        'dd-extend-custom-css',
        get_template_directory_uri() . '/assets/js/extend-custom-css.js', // Adjust path
        array('wp-blocks', 'wp-element', 'wp-hooks', 'wp-editor', 'wp-components', 'wp-block-editor'),
        filemtime(get_template_directory() . '/assets/js/extend-custom-css.js'),
        true
    );

    wp_enqueue_script(
        'dd-faq-schema-extension',
        get_template_directory_uri() . '/assets/js/extend-accordion.js', // Adjust path
        array('wp-blocks', 'wp-element', 'wp-hooks', 'wp-editor', 'wp-components', 'wp-block-editor'),
        filemtime(get_template_directory() . '/assets/js/extend-accordion.js'),
        true
    );


    wp_enqueue_script(
        'dd-tabs-block-js',
        get_template_directory_uri() . '/assets/js/dd-tabs-block.js', // Adjust path
        array('wp-blocks', 'wp-element', 'wp-hooks', 'wp-editor', 'wp-components', 'wp-block-editor'),
        filemtime(get_template_directory() . '/assets/js/dd-tabs-block.js'),
        true
    );

    wp_enqueue_script(
        'dd-button-popup-extension',
        get_template_directory_uri() . '/assets/js/extend-button-popup.js',
        array('wp-blocks', 'wp-element', 'wp-hooks', 'wp-editor', 'wp-components', 'wp-block-editor', 'wp-api-fetch'),
        filemtime(get_template_directory() . '/assets/js/extend-button-popup.js'),
        true
    );

    wp_enqueue_script(
        'dd-cf7-pdf-block',
        get_template_directory_uri() . '/assets/js/dd-cf7-pdf-block.js',
        array('wp-blocks', 'wp-element', 'wp-hooks', 'wp-editor', 'wp-components', 'wp-block-editor', 'wp-api-fetch'),
        filemtime(get_template_directory() . '/assets/js/dd-cf7-pdf-block.js'),
        true
    );

    wp_enqueue_script(
        'dd-cover-responsive',
        get_template_directory_uri() . '/assets/js/extend-cover-responsive.js',
        array('wp-blocks', 'wp-element', 'wp-hooks', 'wp-editor', 'wp-components', 'wp-block-editor'),
        filemtime(get_template_directory() . '/assets/js/extend-cover-responsive.js'),
        true
    );

    wp_enqueue_script(
        'dd-responsive-layout',
        get_template_directory_uri() . '/assets/js/extend-responsive-layout.js',
        array('wp-blocks', 'wp-element', 'wp-hooks', 'wp-editor', 'wp-components', 'wp-block-editor'),
        filemtime(get_template_directory() . '/assets/js/extend-responsive-layout.js'),
        true
    );
}
add_action('enqueue_block_editor_assets', 'digitally_disruptive_enqueue_swiper_editor_assets');

/**
 * Render filter: injects Bootstrap modal-trigger attributes into core/button
 * blocks that have a ddPopupId set, and renders the popup modal HTML inline.
 * Uses a static array so the modal is output only once even when multiple
 * buttons on the same page target the same popup.
 */
function dd_button_popup_render($block_content, $block)
{
    static $rendered_popups = [];

    $popup_id = isset($block['attrs']['ddPopupId']) ? (int) $block['attrs']['ddPopupId'] : 0;
    if (!$popup_id) return $block_content;

    // Replace the <a> with a <button> (strips href, adds modal trigger attrs).
    $block_content = preg_replace_callback(
        '/<a\b([^>]*)>(.*?)<\/a>/s',
        function ($m) use ($popup_id) {
            $attrs = preg_replace('/\s*href=["\'][^"\']*["\']/', '', $m[1]);
            return '<button type="button"' . $attrs . ' data-bs-toggle="modal" data-bs-target="#modal-' . $popup_id . '">' . $m[2] . '</button>';
        },
        $block_content,
        1
    );

    // Render the popup modal HTML once per unique popup ID, appended after the button.
    // Guard against admin/REST context where modules.php (and __popup) is not loaded.
    if (function_exists('__popup') && !in_array($popup_id, $rendered_popups)) {
        $rendered_popups[] = $popup_id;
        $block_content .= do_shortcode('[popup id="' . $popup_id . '"]');
    }

    return $block_content;
}
add_filter('render_block_core/button', 'dd_button_popup_render', 10, 2);

/**
 * Render filter: gives the core Cover block per-breakpoint background images.
 * Reads the ddMobileImageUrl / ddTabletImageUrl attributes set by the
 * "Responsive Background" editor panel (assets/js/extend-cover-responsive.js).
 *
 * Two rendering forms of core/cover are handled:
 *  1. Default: the media is an <img class="wp-block-cover__image-background">.
 *     We wrap it in a <picture> and prepend <source media> elements so the
 *     browser natively swaps the image per viewport. The original <img> (with
 *     its focal-point object-position + srcset) stays as the desktop fallback.
 *  2. Fixed/Repeated background: the media is a
 *     <span class="wp-block-cover__image-background ..." style="background-image:url(...)">
 *     with no <img>. We tag the wrapper with a unique class and inject scoped
 *     <style> @media rules that override background-image at each breakpoint.
 *
 * Breakpoints follow the theme's SCSS responsive() mixin:
 *   mobile <=767px, tablet 768-991px, desktop >=992px (the block's own image).
 */
function dd_cover_responsive_render($block_content, $block)
{
    $attrs       = isset($block['attrs']) ? $block['attrs'] : array();
    $mobile_url  = isset($attrs['ddMobileImageUrl']) ? trim((string) $attrs['ddMobileImageUrl']) : '';
    $tablet_url  = isset($attrs['ddTabletImageUrl']) ? trim((string) $attrs['ddTabletImageUrl']) : '';
    $hide_mobile = !empty($attrs['ddHideImageMobile']);
    $hide_tablet = !empty($attrs['ddHideImageTablet']);

    if ($hide_mobile) {
        $mobile_url = '';
    }
    if ($hide_tablet) {
        $tablet_url = '';
    }

    if ($mobile_url === '' && $tablet_url === '' && !$hide_mobile && !$hide_tablet) {
        return $block_content;
    }

    $has_img = (bool) preg_match('/<img\b[^>]*\bwp-block-cover__image-background\b[^>]*>/', $block_content);

    // Case 1: <img> background — wrap it in <picture> with <source> overrides for any surviving images.
    if ($has_img && ($mobile_url !== '' || $tablet_url !== '')) {
        $build_sources = function ($esc) use ($mobile_url, $tablet_url) {
            $sources = '';
            if ($mobile_url !== '') {
                $sources .= '<source media="(max-width: 767px)" srcset="' . $esc($mobile_url) . '">';
            }
            if ($tablet_url !== '') {
                $sources .= '<source media="(min-width: 768px) and (max-width: 991px)" srcset="' . $esc($tablet_url) . '">';
            }
            return $sources;
        };
        $block_content = preg_replace_callback(
            '/<img\b[^>]*\bwp-block-cover__image-background\b[^>]*>/',
            function ($m) use ($build_sources) {
                return '<picture>' . $build_sources('esc_url') . $m[0] . '</picture>';
            },
            $block_content,
            1
        );
    }

    // Build scoped CSS: Case-2 background-image overrides, plus hide-breakpoint rules.
    $css = '';

    if (!$has_img) {
        // Fixed/repeated background (inline background-image, no <img>) — scoped overrides.
        if ($mobile_url !== '') {
            $css .= '@media (max-width:767px){SCOPE .wp-block-cover__image-background{background-image:url(' . esc_url($mobile_url) . ')!important}}';
        }
        if ($tablet_url !== '') {
            $css .= '@media (min-width:768px) and (max-width:991px){SCOPE .wp-block-cover__image-background{background-image:url(' . esc_url($tablet_url) . ')!important}}';
        }
    }

    $hide_selectors = 'SCOPE > picture,SCOPE .wp-block-cover__image-background,SCOPE .wp-block-cover__background';
    if ($hide_mobile) {
        $css .= '@media (max-width:767px){' . $hide_selectors . '{display:none!important}}';
    }
    if ($hide_tablet) {
        $css .= '@media (min-width:768px) and (max-width:991px){' . $hide_selectors . '{display:none!important}}';
    }

    if ($css === '') {
        return $block_content;
    }

    // Tag the wrapper with a unique scope class so the CSS above only applies to this instance.
    static $counter = 0;
    $counter++;
    $scope = 'dd-cover-resp-' . $counter;

    $tagged = preg_replace(
        '/(<div\b[^>]*\bclass=")([^"]*\bwp-block-cover\b[^"]*)(")/',
        '$1$2 ' . $scope . '$3',
        $block_content,
        1,
        $count
    );
    if (!$count) {
        return $block_content; // Unexpected markup — leave untouched.
    }
    $block_content = $tagged;

    $css = str_replace('SCOPE', '.' . $scope, $css);

    dd_custom_css_collector($css);

    return $block_content;
}
add_filter('render_block_core/cover', 'dd_cover_responsive_render', 10, 2);

function dd_columns_stack_tablet_render($block_content, $block)
{
    if (empty($block['attrs']['ddStackOnTablet'])) {
        return $block_content;
    }

    // Match the columns element by class, not position: the generic `render_block`
    // filter runs before this block-specific one, so a block that also has Custom CSS
    // arrives here with `digitally_disruptive_render_custom_css()`'s <style> tag
    // already prepended — an unqualified next_tag() would land the class on that.
    $tags = new WP_HTML_Tag_Processor($block_content);
    if (! $tags->next_tag(array('class_name' => 'wp-block-columns'))) {
        return $block_content;
    }
    $tags->add_class('dd-stack-tablet');
    return $tags->get_updated_html();
}
add_filter('render_block_core/columns', 'dd_columns_stack_tablet_render', 10, 2);

/**
 * `ddGridColumnsTablet` / `ddGridColumnsMobile` (registered client-side in
 * assets/js/extend-responsive-layout.js) let editors override the column
 * count of a core/group block set to the Grid layout variation at tablet
 * (<=991px) and mobile (<=767px). Only fires for `layout.type === 'grid'`,
 * and bails when `isSwiperSlider` is enabled since
 * `digitally_disruptive_render_universal_swiper()` (below) strips the grid
 * layout entirely to build a carousel — the two are mutually exclusive.
 * Follows the same scoped-<style> strategy as
 * `digitally_disruptive_render_custom_css()`, since the column count is an
 * arbitrary value rather than a fixed class: core prints its own
 * `grid-template-columns` in a <head> stylesheet at equal specificity, so
 * `!important` is required to win regardless of source order.
 */
function dd_group_grid_responsive_render($block_content, $block)
{
    $attrs = isset($block['attrs']) ? $block['attrs'] : array();

    if (! isset($attrs['layout']['type']) || $attrs['layout']['type'] !== 'grid') {
        return $block_content;
    }

    if (! empty($attrs['isSwiperSlider'])) {
        return $block_content;
    }

    $tablet_cols = ! empty($attrs['ddGridColumnsTablet']) ? (int) $attrs['ddGridColumnsTablet'] : 0;
    $mobile_cols = ! empty($attrs['ddGridColumnsMobile']) ? (int) $attrs['ddGridColumnsMobile'] : 0;

    if ($tablet_cols < 1 && $mobile_cols < 1) {
        return $block_content;
    }

    $unique_id = 'dd-grid-' . substr(md5(uniqid(wp_rand(), true)), 0, 8);

    $css = '';
    if ($tablet_cols >= 1) {
        $css .= sprintf(
            '@media (max-width: 991px) { .%1$s { grid-template-columns: repeat(%2$d, minmax(0, 1fr)) !important; } } ',
            $unique_id,
            $tablet_cols
        );
    }
    if ($mobile_cols >= 1) {
        $css .= sprintf(
            '@media (max-width: 767px) { .%1$s { grid-template-columns: repeat(%2$d, minmax(0, 1fr)) !important; } } ',
            $unique_id,
            $mobile_cols
        );
    }

    // Match by class rather than position — see the note in
    // dd_columns_stack_tablet_render() above: a leading <style> tag from
    // `digitally_disruptive_render_custom_css()` may already be prepended here.
    $tags = new WP_HTML_Tag_Processor($block_content);
    if (! $tags->next_tag(array('class_name' => 'wp-block-group'))) {
        return $block_content;
    }
    $tags->add_class($unique_id);
    $updated_content = $tags->get_updated_html();

    dd_custom_css_collector($css);

    return $updated_content;
}
add_filter('render_block_core/group', 'dd_group_grid_responsive_render', 10, 2);

/**
 * The `dd/cf7-pdf-form` block (registered client-side in
 * assets/js/dd-cf7-pdf-block.js). Its save() emits the literal
 * `[contact-form-7 id="…" pdf_url="…"]` shortcode into the post content, but this
 * `render_block` filter is the authoritative renderer: it rebuilds the shortcode
 * from the block's *attributes* (reliably stored in the block-comment JSON) and
 * runs it through do_shortcode(). Reconstructing from attributes — rather than
 * trusting the saved inner markup — means an instance still renders correctly even
 * if it was created under an earlier version of the block (whose saved markup was
 * empty), without needing a manual re-save. The emitted shortcode is byte-for-byte
 * what a hand-typed one is, so the Dynamic Text Extension `pdf_url` field and the
 * existing register_cf7_pdf_url_attribute / dd_attach_cf7_pdf_url_to_email logic
 * all behave identically. The two /dd/v1 REST endpoints in includes/hooks.php only
 * back the editor dropdowns.
 */
function dd_render_cf7_pdf_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'dd/cf7-pdf-form') {
        return $block_content;
    }

    $attrs   = isset($block['attrs']) ? $block['attrs'] : array();
    $form_id = isset($attrs['formId']) ? trim((string) $attrs['formId']) : '';
    if ($form_id === '') {
        return $block_content;
    }

    $title = isset($attrs['formTitle']) ? (string) $attrs['formTitle'] : '';
    $pdf   = isset($attrs['pdfUrl']) ? (string) $attrs['pdfUrl'] : '';
    $speak = isset($attrs['speakUrl']) ? (string) $attrs['speakUrl'] : '';

    // For the Document source, resolve the PDF and speak-to-an-expert URLs fresh
    // from the document so they're always current (and correct even if the block
    // was configured before these values existed). Only override when a value is
    // found, so a stored value is never clobbered with an empty one.
    $source = isset($attrs['pdfSource']) ? $attrs['pdfSource'] : 'media';
    $doc_id = isset($attrs['pdfDocumentId']) ? (int) $attrs['pdfDocumentId'] : 0;
    if ($source === 'document' && $doc_id > 0) {
        if (function_exists('dd_document_file_url')) {
            $doc_pdf = dd_document_file_url($doc_id);
            if ($doc_pdf !== '') {
                $pdf = $doc_pdf;
            }
        }
        if (function_exists('dd_document_speak_url')) {
            $doc_speak = dd_document_speak_url($doc_id);
            if ($doc_speak !== '') {
                $speak = $doc_speak;
            }
        }
    }

    $shortcode = '[contact-form-7 id="' . esc_attr($form_id) . '"';
    if ($title !== '') {
        $shortcode .= ' title="' . esc_attr($title) . '"';
    }
    if ($pdf !== '') {
        $shortcode .= ' pdf_url="' . esc_attr($pdf) . '"';
    }
    if ($speak !== '') {
        $shortcode .= ' speak_to_an_expert_url="' . esc_attr($speak) . '"';
    }
    $shortcode .= ']';

    return do_shortcode($shortcode);
}
add_filter('render_block', 'dd_render_cf7_pdf_block', 10, 2);

/**
 * Enqueues the frontend scripts and styles (Frontend only).
 * * @return void
 */
function dd_enqueue_tabs_frontend_assets(): void
{
    // Only load on the frontend, not in the block editor iframe.
    if (! is_admin()) {

        wp_enqueue_script(
            'dd-tabs-frontend-js',
            get_template_directory_uri() . '/assets/js/dd-tabs-frontend.js', // Adjust path
            array('wp-blocks', 'wp-element', 'wp-hooks', 'wp-editor', 'wp-components', 'wp-block-editor'),
            filemtime(get_template_directory() . '/assets/js/dd-tabs-frontend.js'),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'dd_enqueue_tabs_frontend_assets');

/**
 * Universal Swiper Rendering Engine.
 * Intercepts both Query Loops and structural Group/Grid blocks to inject Swiper.js DOM requirements.
 *
 * @param string $block_content The raw HTML content of the block.
 * @param array  $block         The parsed block data array.
 * @return string Modified block HTML ready for Swiper initialization.
 */
function digitally_disruptive_render_universal_swiper($block_content, $block)
{

    $allowed_blocks = array('core/group', 'core/query');

    // Bail early if it is not a targeted block type
    if (! in_array($block['blockName'], $allowed_blocks, true)) {
        return $block_content;
    }

    // Backward Compatibility: Check if the new toggle is checked OR if the legacy CSS class exists
    $has_legacy_class  = (! empty($block['attrs']['className']) && strpos($block['attrs']['className'], 'query-loop-swiper-js') !== false);
    $is_swiper_enabled = ! empty($block['attrs']['isSwiperSlider']) || $has_legacy_class;

    if (! $is_swiper_enabled) {
        return $block_content;
    }

    $attrs = $block['attrs'];

    /**
     * 1. Extract Attributes with Strict Defaults
     */
    $slides_desktop = isset($attrs['swiperSlidesDesktop']) ? (float) $attrs['swiperSlidesDesktop'] : 4;
    $slides_tablet  = isset($attrs['swiperSlidesTablet']) ? (float) $attrs['swiperSlidesTablet'] : 2;
    $slides_mobile  = isset($attrs['swiperSlidesMobile']) ? (float) $attrs['swiperSlidesMobile'] : 1;
    $space_between  = isset($attrs['swiperSpaceBetween']) ? (int) $attrs['swiperSpaceBetween'] : 20;

    $is_loop        = isset($attrs['swiperLoop']) ? (bool) $attrs['swiperLoop'] : true;
    $has_pagination = isset($attrs['swiperPagination']) ? (bool) $attrs['swiperPagination'] : true;
    $has_navigation = isset($attrs['swiperNavigation']) ? (bool) $attrs['swiperNavigation'] : false;
    $has_autoplay   = isset($attrs['swiperAutoplay']) ? (bool) $attrs['swiperAutoplay'] : false;
    $delay          = isset($attrs['swiperDelay']) ? (int) $attrs['swiperDelay'] : 3000;

    // Construct the JSON Configuration Object
    $swiper_config = array(
        'spaceBetween'  => $space_between,
        'loop'          => $is_loop,
        'breakpoints'   => array(
            320  => array('slidesPerView' => $slides_mobile),
            768  => array('slidesPerView' => $slides_tablet),
            1024 => array('slidesPerView' => $slides_desktop),
        ),
    );

    if ($has_autoplay) {
        $swiper_config['autoplay'] = array('delay' => $delay, 'disableOnInteraction' => false);
    }
    if ($has_pagination) {
        $swiper_config['pagination'] = array('el' => '.swiper-pagination', 'clickable' => true);
    }
    if ($has_navigation) {
        $swiper_config['navigation'] = array('nextEl' => '.swiper-button-next', 'prevEl' => '.swiper-button-prev');
    }

    /**
     * 2. Process the Controls
     */
    $controls_html = '';
    if ($has_pagination) $controls_html .= '<div class="swiper-pagination"></div>';
    if ($has_navigation) $controls_html .= '<div class="swiper-button-prev"></div><div class="swiper-button-next"></div>';

    /**
     * 3. DOM Structural Manipulation based on Block Type
     */
    if ($block['blockName'] === 'core/query') {

        // QUERY LOOP ARCHITECTURE (Uses native nested <ul> and <li>)
        $tags = new WP_HTML_Tag_Processor($block_content);
        if ($tags->next_tag()) {
            $tags->add_class('swiper');
            $tags->add_class('is-swiper-slider'); // Injects the requested global Swiper indicator class
            $tags->set_attribute('data-swiper-config', wp_json_encode($swiper_config));
        }

        $tags = new WP_HTML_Tag_Processor($tags->get_updated_html());
        while ($tags->next_tag(array('tag_name' => 'ul'))) {
            $class = $tags->get_attribute('class');
            if ($class && strpos($class, 'wp-block-post-template') !== false) {
                $tags->add_class('swiper-wrapper');

                // Strip native Gutenberg layout classes that break Swiper's horizontal flex track
                $tags->remove_class('is-layout-grid');
                $tags->remove_class('wp-block-post-template-is-layout-grid');
                $tags->remove_class('is-layout-flex');
                $tags->remove_class('wp-block-post-template-is-layout-flex');

                // Dynamically remove Gutenberg's native "columns-X" structural enforcers
                for ($i = 1; $i <= 6; $i++) {
                    $tags->remove_class('columns-' . $i);
                }

                break;
            }
        }

        $tags = new WP_HTML_Tag_Processor($tags->get_updated_html());
        while ($tags->next_tag(array('tag_name' => 'li'))) {
            $class = $tags->get_attribute('class');
            if ($class && strpos($class, 'wp-block-post') !== false) {
                $tags->add_class('swiper-slide');
            }
        }

        $html = $tags->get_updated_html();
        return preg_replace('/(<\/ul>)/i', '$1' . $controls_html, $html, 1);
    } else {

        // GROUP / GRID ARCHITECTURE (Requires dynamic DOM wrapping)
        $tags = new WP_HTML_Tag_Processor($block_content);
        if ($tags->next_tag()) {
            $tags->add_class('swiper');
            $tags->add_class('is-swiper-slider'); // Injects the requested global Swiper indicator class
            $tags->set_attribute('data-swiper-config', wp_json_encode($swiper_config));

            // Strip native WordPress flex/grid classes to prevent structural layout conflicts with Swiper
            $tags->remove_class('is-layout-grid');
            $tags->remove_class('wp-block-group-is-layout-grid');
            $tags->remove_class('is-layout-flex');
            $tags->remove_class('wp-block-group-is-layout-flex');
        }
        $html = $tags->get_updated_html();

        // Physically split the HTML to wrap the inner child blocks
        $first_tag_end = strpos($html, '>') + 1;
        $last_tag_start = strrpos($html, '</');

        if ($first_tag_end !== false && $last_tag_start !== false) {
            $opening = substr($html, 0, $first_tag_end);
            $inner   = substr($html, $first_tag_end, $last_tag_start - $first_tag_end);
            $closing = substr($html, $last_tag_start);

            /**
             * Synchronous Execution Tag:
             * This strictly maps `.swiper-slide` to all direct children of the dynamic wrapper instantaneously
             * during browser HTML parsing, ensuring the DOM is pristine before Swiper initializes.
             */
            $slide_injector = '<script>Array.from(document.currentScript.previousElementSibling.children).forEach(function(el){ el.classList.add("swiper-slide"); });</script>';

            $wrapped_inner = '<div class="swiper-wrapper">' . $inner . '</div>' . $slide_injector;

            return $opening . $wrapped_inner . $controls_html . $closing;
        }

        return $html;
    }
}
add_filter('render_block', 'digitally_disruptive_render_universal_swiper', 10, 2);



/**
 * Intercepts block rendering to dynamically inject custom field values.
 * This method guarantees the correct Post ID by extracting it from the Gutenberg Block Context
 * rather than relying on the global $post object, which frequently leaks the parent page ID.
 *
 * @param string   $block_content The generated HTML content of the block.
 * @param array    $block         The parsed block data structure.
 * @param WP_Block $instance      The live block instance containing the block context.
 * @return string Modified block HTML.
 */
function dd_inject_query_loop_meta_via_class($block_content, $block, $instance)
{
    // Bail early if the block doesn't have a custom class assigned.
    if (empty($block['attrs']['className'])) {
        return $block_content;
    }

    // Look for our specific trigger class pattern (e.g., 'dd-meta-price')
    if (preg_match('/dd-meta-([\w-]+)/', $block['attrs']['className'], $matches)) {
        $meta_key = $matches[1];

        // CRITICAL FIX: Extract the Post ID directly from the nested Block Context.
        // If we aren't inside a query loop context, fallback to the standard get_the_ID().
        $post_id = isset($instance->context['postId']) ? $instance->context['postId'] : get_the_ID();

        if (! $post_id) {
            return $block_content;
        }

        // Retrieve the custom field value.
        $meta_value = get_post_meta($post_id, $meta_key, true);

        // If the meta field is empty, return an empty string to remove the block from the DOM cleanly.
        if (empty($meta_value)) {
            return '';
        }

        // Isolate the block's outer HTML tags to preserve Gutenberg styling (colors, typography, margins).
        // This ensures any design settings applied in the editor remain intact.
        $first_close_bracket = strpos($block_content, '>');
        $last_open_bracket   = strrpos($block_content, '<');

        if ($first_close_bracket !== false && $last_open_bracket !== false && $first_close_bracket < $last_open_bracket) {
            $opening_tag = substr($block_content, 0, $first_close_bracket + 1);
            $closing_tag = substr($block_content, $last_open_bracket);

            // Construct the final output: Opening Tag + Sanitized Meta Value + Closing Tag.
            return $opening_tag . esc_html($meta_value) . $closing_tag;
        }
    }

    return $block_content;
}
add_filter('render_block', 'dd_inject_query_loop_meta_via_class', 10, 3);


/**
 * Shared collector for CSS compiled by the per-block Custom CSS panel and the
 * Cover/Grid responsive-override render filters below (see
 * digitally_disruptive_render_custom_css(), dd_group_grid_responsive_render(),
 * dd_cover_responsive_render()). Each pushes its scoped CSS here instead of
 * printing its own inline <style> tag next to the block; dd_flush_consolidated_css()
 * later drops the combined result into <head> as one
 * <style id="dd-consolidated-custom-css"> block.
 *
 * @param string|null $css CSS to append, or null to read back the buffer.
 * @return string[] The accumulated CSS strings.
 */
function dd_custom_css_collector($css = null)
{
    static $buffer = array();
    if ($css !== null && $css !== '') {
        $buffer[] = $css;
    }
    return $buffer;
}

/**
 * Reserves the spot in <head> where the consolidated CSS will land. Runs late
 * (priority 999) so it sits after core's own block-support styles, giving our
 * rules a source-order edge in addition to the !important they already carry
 * where needed.
 */
function dd_consolidated_css_placeholder()
{
    echo '<!--DD_CONSOLIDATED_CSS-->';
}
add_action('wp_head', 'dd_consolidated_css_placeholder', 999);

/**
 * Starts a full-page output buffer so CSS collected while blocks render
 * (after <head> has already been sent) can still be spliced into <head>
 * before the response reaches the browser/cache. Skipped for admin/REST/AJAX/
 * cron/feed requests, which never render the front-end blocks that populate
 * dd_custom_css_collector() and don't need the placeholder rewritten.
 */
function dd_start_css_buffer()
{
    if (
        is_admin() || is_feed() || wp_doing_ajax()
        || (defined('REST_REQUEST') && REST_REQUEST)
        || (defined('DOING_CRON') && DOING_CRON)
        || (function_exists('wp_is_json_request') && wp_is_json_request())
    ) {
        return;
    }
    ob_start('dd_flush_consolidated_css');
}
add_action('template_redirect', 'dd_start_css_buffer');

/**
 * Output buffer callback: swaps the <!--DD_CONSOLIDATED_CSS--> marker left by
 * dd_consolidated_css_placeholder() for a single <style> containing everything
 * collected by dd_custom_css_collector() during this request's block rendering.
 *
 * @param string $html The fully rendered page HTML.
 * @return string HTML with the consolidated <style> in <head>.
 */
function dd_flush_consolidated_css($html)
{
    $parts = dd_custom_css_collector();

    if (empty($parts)) {
        return str_replace('<!--DD_CONSOLIDATED_CSS-->', '', $html);
    }

    $style = '<style id="dd-consolidated-custom-css">' . implode('', $parts) . '</style>';

    if (strpos($html, '<!--DD_CONSOLIDATED_CSS-->') !== false) {
        return str_replace('<!--DD_CONSOLIDATED_CSS-->', $style, $html);
    }

    if (strpos($html, '</head>') !== false) {
        return str_replace('</head>', $style . '</head>', $html);
    }

    return $html . $style;
}

/**
 * Intercept the block, scope the hybrid custom CSS declarations across breakpoints, and inject the style tag.
 *
 * @param string $block_content The raw HTML content of the block.
 * @param array  $block         The parsed block data array.
 * @return string Modified block HTML with inline scoped styles.
 */
function digitally_disruptive_render_custom_css($block_content, $block)
{

    // Expanded backend whitelist mirroring the JavaScript implementation
    $allowed_blocks = array(
        'core/group',
        'core/separator',
        'core/image',
        'core/heading',
        'core/paragraph',
        'core/button',
        'core/columns',
        'core/column'
    );

    // Bail early if the block type is not whitelisted
    if (! in_array($block['blockName'], $allowed_blocks, true)) {
        return $block_content;
    }

    $has_desktop = ! empty($block['attrs']['ddCustomCSS']);
    $has_tablet  = ! empty($block['attrs']['ddCustomCSSTablet']);
    $has_mobile  = ! empty($block['attrs']['ddCustomCSSMobile']);

    // Bail if no custom CSS exists in any viewport
    if (! $has_desktop && ! $has_tablet && ! $has_mobile) {
        return $block_content;
    }

    $unique_id = 'dd-css-' . substr(md5(uniqid(wp_rand(), true)), 0, 8);

    /**
     * HYBRID PARSER CLOSURE
     * Centralized logic to execute the hybrid parsing cleanly for any input string.
     * * @param string $raw_css The unparsed CSS string from the block attribute.
     * @param string $uid     The unique class identifier for the current block.
     * @return string         Compiled and scoped CSS string.
     */
    $compile_hybrid_css = function ($raw_css, $uid) {
        $sanitized_css = wp_strip_all_tags($raw_css);

        // 1. Extract all advanced blocks (e.g., "SELECTOR img { border-radius: 50%; }")
        preg_match_all('/SELECTOR[^{]*{[^}]*}/', $sanitized_css, $matches);
        $advanced_blocks = $matches[0];

        // 2. Isolate base properties by stripping the advanced blocks out of the string
        $base_properties = trim(preg_replace('/SELECTOR[^{]*{[^}]*}/', '', $sanitized_css));

        $scoped_css = '';
        if (! empty($base_properties)) {
            $scoped_css .= sprintf('.%s { %s } ', $uid, $base_properties);
        }

        foreach ($advanced_blocks as $block_rule) {
            $scoped_css .= str_replace('SELECTOR', '.' . $uid, $block_rule) . ' ';
        }

        return $scoped_css;
    };

    // Compile Final CSS Payload
    $final_css = '';

    if ($has_desktop) {
        $final_css .= $compile_hybrid_css($block['attrs']['ddCustomCSS'], $unique_id);
    }

    if ($has_tablet) {
        $final_css .= sprintf(
            '@media (max-width: 991px) { %s } ',
            $compile_hybrid_css($block['attrs']['ddCustomCSSTablet'], $unique_id)
        );
    }

    if ($has_mobile) {
        $final_css .= sprintf(
            '@media (max-width: 767px) { %s } ',
            $compile_hybrid_css($block['attrs']['ddCustomCSSMobile'], $unique_id)
        );
    }

    // Inject the unique class into the block's outermost container tag
    $tags = new WP_HTML_Tag_Processor($block_content);
    if ($tags->next_tag()) {
        $tags->add_class($unique_id);
    }
    $updated_content = $tags->get_updated_html();

    // Push into the shared collector instead of printing an inline <style> here —
    // dd_flush_consolidated_css() emits it in <head> alongside every other block's CSS.
    dd_custom_css_collector($final_css);

    return $updated_content;
}
add_filter('render_block', 'digitally_disruptive_render_custom_css', 10, 2);



/**
 * Intercepts block rendering to dynamically inject FAQPage schema if the toggle is enabled.
 * * @param string $block_content The original HTML output of the block.
 * @param array  $block         The parsed block array including attributes.
 * @return string               The modified block content with injected JSON-LD schema.
 */
function dd_render_accordion_with_schema($block_content, $block)
{
    // 1. Isolate the target block and check the custom attribute flag
    if ('core/accordion' !== $block['blockName'] || empty($block['attrs']['enableFaqSchema'])) {
        return $block_content;
    }

    if (empty($block['innerBlocks'])) {
        return $block_content;
    }

    $faq_entities = array();

    // 2. Loop through the 'core/accordion-item' wrappers
    foreach ($block['innerBlocks'] as $item) {

        if ('core/accordion-item' === $item['blockName'] && ! empty($item['innerBlocks'])) {
            $question_text = '';
            $answer_html   = '';

            // 3. Look inside the Item for the Heading and the Panel
            foreach ($item['innerBlocks'] as $inner_element) {

                // Extract the Question
                if ('core/accordion-heading' === $inner_element['blockName']) {
                    // We use innerHTML and strip tags to get the pure text string
                    $question_text = trim(wp_strip_all_tags($inner_element['innerHTML']));
                }

                // Extract the Answer
                if ('core/accordion-panel' === $inner_element['blockName']) {
                    // We compile the panel natively to capture all paragraphs, lists, and formatting
                    $answer_html = render_block($inner_element);
                }
            }

            // 4. Construct the FAQ entity if both pieces of data exist
            if (! empty($question_text) && ! empty(trim($answer_html))) {
                $faq_entities[] = array(
                    '@type'          => 'Question',
                    'name'           => $question_text,
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => wp_kses_post(trim($answer_html)), // Sanitize the compiled HTML
                    ),
                );
            }
        }
    }

    // 5. Inject Schema into the DOM
    if (! empty($faq_entities)) {
        $schema = array(
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => $faq_entities,
        );

        $schema_script  = "\n\n";
        $schema_script .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";

        return $block_content . $schema_script;
    }

    return $block_content;
}
add_filter('render_block', 'dd_render_accordion_with_schema', 10, 2);

/*-----------------------------------------------------------------------------------*/
/* Environment & Template Detection Engine
/*-----------------------------------------------------------------------------------*/

/**
 * Resolves the current Post ID early in the WordPress load cycle.
 * Functions reliably across Frontend, Admin, and REST API (Gutenberg) contexts.
 *
 * @return int|false Returns the Post ID if successfully resolved, otherwise false.
 */
function dd_get_early_post_id()
{
    // 1. Admin Context (Classic Editor or Standard WP Admin)
    if (is_admin()) {
        if (isset($_GET['post'])) {
            return absint($_GET['post']);
        }
        if (isset($_POST['post_ID'])) {
            return absint($_POST['post_ID']);
        }
    }

    // 2. REST API Context (Gutenberg Block Editor Architecture)
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/wp-json/wp/v2/') !== false) {
        if (isset($_GET['post_id'])) {
            return absint($_GET['post_id']);
        }
        // Extract ID from the REST endpoint route
        if (preg_match('/\/wp\/v2\/(?:pages|posts)\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            return absint($matches[1]);
        }
    }

    // 3. Frontend Context (Early Execution before $wp_query is populated)
    if (! is_admin() && isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI'])) {
        if (isset($_GET['page_id'])) {
            return absint($_GET['page_id']);
        }
        if (isset($_GET['p'])) {
            return absint($_GET['p']);
        }

        global $wpdb;
        $request_path = trim(parse_url(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])), PHP_URL_PATH), '/');

        if (empty($request_path)) {
            return absint(get_option('page_on_front'));
        }

        $slug    = basename($request_path);
        $post_id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type IN ('page', 'post') AND post_status IN ('publish', 'private') LIMIT 1",
            $slug
        ));

        if ($post_id) {
            return absint($post_id);
        }
    }

    return false;
}

/**
 * Determines if the current execution context is assigned the 'templates/page-blocks-editor.php' template.
 *
 * @return bool True if the template is active, false otherwise.
 */
function dd_is_blocks_editor_template_active()
{
    $post_id = dd_get_early_post_id();

    if ($post_id) {
        $template = get_post_meta($post_id, '_wp_page_template', true);

        // Ensure this exactly matches the relative path stored by WordPress
        return ('templates/page-blocks-editor.php' === $template);
    }

    return false;
}

/*-----------------------------------------------------------------------------------*/
/* Require Files
/*-----------------------------------------------------------------------------------*/
require_once('includes/_required_files.php');


function canonical()
{
    if (is_single() || is_page()) {
        return get_the_permalink();
    } else if (is_tax() || is_category()) {
        $term_link = get_term_link(get_queried_object()->term_id);
        return $term_link;
    } else if (is_post_type_archive()) {
        $archive_link = get_post_type_archive_link(get_post_type());
        return $archive_link;
    } else if (is_home()) {
        $blog_url = get_permalink(get_option('page_for_posts'));
        return $blog_url;
    } else {
        $term_link = get_term_link(get_queried_object()->term_id);
        return $term_link;
    }
}

/*
function action_validate_email()
{
	if (get_the_ID() == 372958) {
?>
		<script>
			document.addEventListener('wpcf7submit', function(event) {
				if (event.detail.contactFormId === 372961) {
					let emailField = event.detail.inputs.find(input => input.name === 'email'); // Replace 'email' with the actual name of your email field
					if (emailField) {
						let email = emailField.value;
						if (!email.endsWith('@mod.gov.uk')) {
							event.detail.valid = false;
							let emailError = document.createElement('span');
							emailError.className = 'wpcf7-not-valid-tip';
							emailError.style.color = 'red';
							emailError.textContent = 'Please use an @mod.gov.uk email address.';

							let emailInput = document.querySelector('input[name="email"]'); // or the correct selector for your email input
							if (emailInput) {
								let existingError = emailInput.parentNode.querySelector('.wpcf7-not-valid-tip');
								if (existingError) {
									existingError.remove();
								}
								emailInput.parentNode.appendChild(emailError);
							}

						} else {
							let emailInput = document.querySelector('input[name="email"]');
							if (emailInput) {
								let existingError = emailInput.parentNode.querySelector('.wpcf7-not-valid-tip');
								if (existingError) {
									existingError.remove();
								}
							}
						}
					}
				}
			}, false);

			document.addEventListener('DOMContentLoaded', function() {
				const emailInput = document.querySelector('.realtime-email-check');

				if (emailInput) {
					emailInput.addEventListener('input', function() {
						const email = this.value;
						const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Basic email regex

						if (emailRegex.test(email)) {
							// Valid email: You can add visual feedback here (e.g., green border, checkmark)
							this.classList.remove('invalid-email');
							this.classList.add('valid-email');
						} else if (email.length > 0) {
							// Invalid email and not empty: Add visual feedback (e.g., red border, error message)
							this.classList.remove('valid-email');
							this.classList.add('invalid-email');

						} else {
							//Empty field reset feedback
							this.classList.remove('invalid-email');
							this.classList.remove('valid-email');
						}
					});
				}
			});
		</script>
<?php
	}
}

add_action('wp_footer', 'action_validate_email');
*/



add_filter('wpcf7_validate_email*', 'wpcf7_validate_mod_gov_uk', 20, 2);

function wpcf7_validate_mod_gov_uk($result, $tag)
{
    if ($tag->name == 'email_mod') { // Replace 'your-email' with your actual email field name
        $value = isset($_POST[$tag->name]) ? trim($_POST[$tag->name]) : '';
        if (! filter_var($value, FILTER_VALIDATE_EMAIL) || ! preg_match('/@mod\.gov\.uk$/', $value)) {
            $result->invalidate($tag, 'Please use a @mod.gov.uk email address');
        }
    }
    return $result;
}

function action_validate_email()
{
    if (get_the_ID() == 372958) {
?>
        <script>
            jQuery(document).ready(function() {
                jQuery('input[name="email_mod"]').on('input', function() {
                    $val = jQuery(this).val();
                    jQuery('input[name="email"]').val($val);
                });
            });
        </script>
<?php
    }
}

add_action('wp_footer', 'action_validate_email');

add_action('wp', function () {

    if (!is_product()) return;

    global $post;

    $related = coptrz_get_post_meta($post->ID, 'crb_related_products');

    if (!empty($related)) {
        remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
    }
});

add_action('woocommerce_after_single_product_summary', function () {

    if (!is_product()) return;

    global $post;

    $related = coptrz_get_post_meta($post->ID, 'crb_related_products');

    // ❌ If empty → let WooCommerce handle it
    if (empty($related)) return;

    // 🔥 Extract product IDs from Carbon Fields structure
    $related_ids = array_map(function ($item) {
        return $item['id'];
    }, $related);

    $query = new WP_Query([
        'post_type'      => 'product',
        'post__in'       => $related_ids,
        'orderby'        => 'post__in', // 👈 keeps manual order
        'posts_per_page' => count($related_ids),
    ]);

    if ($query->have_posts()) {
        echo '<section class="related products md-padding-top md-padding-bottom border-top-default">';
        echo '<div class="container">';
        echo '<h2 class="text-center">Related products</h2>';
        echo '<ul class="products columns-4">';

        while ($query->have_posts()) {
            $query->the_post();

            // ✅ Uses your existing product card layout
            wc_get_template_part('content', 'product');
        }

        echo '</ul></div></section>';
    }

    wp_reset_postdata();
}, 20);

add_action('wp_footer', 'inject_popup_modal');
function inject_popup_modal()
{
    // Only show this on the relevant Brand Archive pages
    if (is_tax('pa_brands', 'skyshyld')) {
        echo do_shortcode('[popup id=419151]');
    } else if (is_tax('pa_brands', 'avy')) {
        echo do_shortcode('[popup id=419411]');
    } else if (is_page(421860)) {
        echo do_shortcode('[popup id=421954]');
    } else if (is_page(420090)) {
        echo do_shortcode('[popup id=422026]');
    }
}


/**
 * Increases the autosave interval to reduce database writes and AJAX requests during editing.
 *
 * @return void
 */
function dd_optimize_autosave_interval()
{
    if (! defined('AUTOSAVE_INTERVAL')) {
        // Set autosave to 2 minutes instead of the default 60 seconds
        define('AUTOSAVE_INTERVAL', 120);
    }
}
add_action('init', 'dd_optimize_autosave_interval');
/**
 * Throttles the WordPress Heartbeat API in the block editor.
 * 
 * By modifying the Heartbeat rate, we reduce the frequency of admin-ajax.php 
 * requests, which frees up browser resources and prevents typing lag.
 *
 * @param array $settings The Heartbeat API settings array.
 * @return array Modified settings with a slower interval.
 */
add_filter('heartbeat_settings', function ($settings) {
    // Set the heartbeat interval to 60 seconds (maximum allowed via this filter)
    $settings['interval'] = 60;
    return $settings;
});

/**
 * Disables the block directory search to prevent external API calls when typing in the block inserter.
 *
 * @return void
 */
function dd_disable_block_directory_search()
{
    remove_action('enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets');
}
add_action('admin_init', 'dd_disable_block_directory_search');

/**
 * Shortcode: [rpc_course_dates]
 * Fetches and caches Shopify course dates.
 */
function rpc_get_shopify_course_dates() {

    // Check cache (1 hour)
    $dates = get_transient('rpc_shopify_course_dates');

    if ($dates === false) {
        $response = wp_remote_get(
            'https://shop.coptrz.com/products/rpc-l1-drone-training-course-in-person-classroom.js',
            array(
                'timeout' => 15,
                'headers' => array(
                    'Accept' => 'application/json',
                ),
            )
        );

        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $body = wp_remote_retrieve_body($response);
            $product = json_decode($body, true);

            $dates = array();

            if (!empty($product['variants'])) {
                foreach ($product['variants'] as $variant) {
                    if (!empty($variant['available'])) {
                        $dates[] = sanitize_text_field($variant['title']);
                    }
                }
            }

            // Cache for 1 hour.
            set_transient('rpc_shopify_course_dates', $dates, HOUR_IN_SECONDS);
        } else {
            $dates = array();
        }
    }

    if (empty($dates)) {
        return '<p>No upcoming classroom dates are currently available.</p>';
    }

    ob_start();
    ?>
    <div class="rpc-course-dates">
        <ul>
            <?php foreach ($dates as $date) : ?>
                <li><?php echo esc_html($date); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('rpc_course_dates', 'rpc_get_shopify_course_dates');