<?php

/**
 * Configures theme support capabilities for the WordPress block editor.
 *
 * This function is hooked to 'after_setup_theme' to initialize custom editor styles
 * and register a restrictive or bespoke color palette for the Gutenberg UI, overriding
 * the core default colors.
 *
 * @return void
 */
function custom_theme_block_editor_setup()
{

    /**
     * Enable support for custom editor styles.
     * This allows the theme to load custom CSS into the block editor canvas.
     */
    add_theme_support('editor-styles');

    /**
     * Enqueue the primary stylesheet to the block editor.
     * WordPress handles wrapping these styles to prevent admin UI conflicts.
     * Assumes style.css is located in the theme root.
     */
    add_editor_style('style.css');

    /**
     * Register a custom color palette for the block editor.
     * Modifying this array updates the UI color swatches available to the user.
     * The 'slug' key dictates the dynamically generated CSS classes.
     */
    add_theme_support('editor-color-palette', array(
        array(
            'name'  => esc_html__('Primary', 'coptrz'),
            'slug'  => 'primary',
            'color' => '#000000', // Normalized from #0000 for solid black
        ),
        array(
            'name'  => esc_html__('Secondary', 'coptrz'),
            'slug'  => 'secondary',
            'color' => '#132446',
        ),
        array(
            'name'  => esc_html__('Tertiary', 'coptrz'),
            'slug'  => 'tertiary',
            'color' => '#0E1729',
        ),
        array(
            'name'  => esc_html__('Quaternary', 'coptrz'),
            'slug'  => 'quaternary',
            'color' => '#010817',
        ),
        array(
            'name'  => esc_html__('Quinary', 'coptrz'),
            'slug'  => 'quinary',
            'color' => '#0E1B35',
        ),
        array(
            'name'  => esc_html__('Senary', 'coptrz'),
            'slug'  => 'senary',
            'color' => '#071020',
        ),
        array(
            'name'  => esc_html__('Contrast / White', 'coptrz'),
            'slug'  => 'contrast-white',
            'color' => '#ffffff', // Expanded from #fff
        ),
        array(
            'name'  => esc_html__('Accent', 'coptrz'),
            'slug'  => 'accent',
            'color' => '#2DA1FF',
        ),
        array(
            'name'  => esc_html__('Light Gray', 'coptrz'),
            'slug'  => 'lightgray',
            'color' => '#CBCBCB',
        ),
        array(
            'name'  => esc_html__('Light Gray 2', 'coptrz'),
            'slug'  => 'lightgray2',
            'color' => '#DBDBDB',
        ),
        array(
            'name'  => esc_html__('Light Gray 3', 'coptrz'),
            'slug'  => 'lightgray3',
            'color' => '#EFEFEF',
        ),
        array(
            'name'  => esc_html__('Transparent', 'coptrz'),
            'slug'  => 'transparent',
            'color' => '#00000000',
        ),

    ));

    /**
     * Optional constraint: Disable the custom color picker completely.
     * Enforces strict adherence to the defined palette by preventing users
     * from inputting arbitrary hex codes.
     */
    add_theme_support('appearance-tools');
    add_theme_support('custom-spacing');
    add_theme_support('disable-custom-colors');
}

add_action('after_setup_theme', 'custom_theme_block_editor_setup');

function add_svg_support($mimes)
{
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'add_svg_support');

function action_wp_head()
{
    $custom_css = get__post_meta('custom_css');
    if ($custom_css) {
?>
        <style id="wp-head">
            <?php
            echo $custom_css;
            ?>
        </style>
    <?php
    }
}

add_action('wp_head', 'action_wp_head');

/*
 * Plugin/Snippet Author: Digitally Disruptive - Donald Raymundo
 *
 * Emit baseline security-response headers flagged by securityheaders.com /
 * Mozilla Observatory scans. Hooked on `send_headers` so they apply to every
 * WordPress-served response (front end + admin), not just the <head>.
 *
 * Coverage:
 *   - X-Content-Type-Options: nosniff            (stops MIME sniffing)
 *   - X-Frame-Options: SAMEORIGIN                (clickjacking, legacy header)
 *   - Content-Security-Policy: frame-ancestors   (clickjacking, modern header)
 *   - Referrer-Policy: strict-origin-when-cross-origin
 *   - Strict-Transport-Security                  (HTTPS only)
 *
 * The CSP is intentionally frame-ancestors-only: it governs framing but does NOT
 * restrict script-src/object-src, so it can't break inline theme/Woo/CF7/analytics
 * scripts. Scanners grade a frame-ancestors-only policy "unsafe" (no script-src),
 * which is accepted here in exchange for defense-in-depth alongside X-Frame-Options;
 * a real script-restricting CSP would need a nonce-based rollout.
 *
 * NOTE: On a LiteSpeed full-page-cache HIT these PHP headers may be bypassed.
 * For guaranteed coverage mirror them in .htaccess / server config too.
 */
function dd_send_security_headers()
{
    if (headers_sent()) {
        return;
    }

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header("Content-Security-Policy: frame-ancestors 'self'");
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // HSTS only over HTTPS. Conservative rollout per hstspreload.org guidance:
    // 6-month max-age, no includeSubDomains/preload yet — add those once every
    // subdomain is confirmed HTTPS-only, then submit to the preload list.
    if (is_ssl()) {
        header('Strict-Transport-Security: max-age=15768000');
    }
}
add_action('send_headers', 'dd_send_security_headers');

/*-----------------------------------------------------------------------------------*/
/* Admin Settings
/*-----------------------------------------------------------------------------------*/

function action_admin_enqueue_scripts($hook)
{

    $pages = get__posts('page');
    $select_page = '<label style="display: block" class="cf-field__label" >Select Page</label><select class="select-page-selector">';
    foreach ($pages as $key => $page) {
        $select_page .= '<option value="' . $key . '"> ' . $page . ' </option>';
    }
    $select_page .= '</select>';
    $selector['page'] = $select_page;


    $posts = get__posts('post');
    $select_post = '<label style="display: block" class="cf-field__label" >Select Post</label><select class="select-page-selector">';
    foreach ($posts as $key => $post) {
        $select_post .= '<option value="' . $key . '"> ' . $post . ' </option>';
    }
    $select_post .= '</select>';
    $selector['post'] = $select_post;


    $industries = get__posts('industries');
    $select_solution = '<label style="display: block" class="cf-field__label">Select Solution</label><select class="select-page-selector">';
    foreach ($industries as $key => $solution) {
        $select_solution .= '<option value="' . $key . '"> ' . $solution . ' </option>';
    }
    $select_solution .= '</select>';
    $selector['industries'] = $select_solution;


    $popups = get__posts('popups');
    $select_popup = '<label style="display: block" class="cf-field__label">Select Popup</label><select class="select-page-selector">';
    foreach ($popups as $key => $popup) {
        $select_popup .= '<option value="' . $key . '"> ' . $popup . ' </option>';
    }
    $select_popup .= '</select>';
    $selector['popups'] = $select_popup;


    $products = get__posts('product');
    $select_product = '<label style="display: block" class="cf-field__label">Select product</label><select class="select-page-selector">';
    foreach ($products as $key => $product) {
        $select_product .= '<option value="' . $key . '"> ' . $product . ' </option>';
    }
    $select_product .= '</select>';
    $selector['product'] = $select_product;


    $guides = get__posts('guides');
    $select_guide = '<label style="display: block" class="cf-field__label">Select guide</label><select class="select-page-selector">';
    foreach ($guides as $key => $guide) {
        $select_guide .= '<option value="' . $key . '"> ' . $guide . ' </option>';
    }
    $select_guide .= '</select>';
    $selector['guides'] = $select_guide;


    $casestudies = get__posts('casestudies');
    $select_casestudies = '<label style="display: block" class="cf-field__label">Select casestudies</label><select class="select-page-selector">';
    foreach ($casestudies as $key => $casestudies) {
        $select_casestudies .= '<option value="' . $key . '"> ' . $casestudies . ' </option>';
    }
    $select_casestudies .= '</select>';
    $selector['casestudies'] = $select_casestudies;


    wp_enqueue_style('admin', get_template_directory_uri() . '/admin/css/admin.css');
    wp_register_script('admin', get_template_directory_uri() . '/admin/js/admin.js');
    wp_localize_script('admin', 'selector', $selector);
    wp_enqueue_script('admin');
}
add_action('admin_enqueue_scripts', 'action_admin_enqueue_scripts');
/*-----------------------------------------------------------------------------------*/
/* Code Miror
/*-----------------------------------------------------------------------------------*/
add_action('admin_enqueue_scripts', 'codemirror_enqueue_scripts');

function codemirror_enqueue_scripts($hook)
{
    $cm_settings = array(
        'ce_css'  => wp_enqueue_code_editor(array('type' => 'text/css', 'codemirror' => array('autoRefresh' => true))),
        'ce_html' => wp_enqueue_code_editor(array('type' => 'text/html', 'codemirror' => array('autoRefresh' => true)))
    );
    wp_localize_script('jquery', 'cm_settings', $cm_settings);

    wp_enqueue_style('wp-codemirror');
}


function get__posts($post_type)
{
    $pages_array = array(); // Initialize an empty array

    $args = array(
        'post_type'      => $post_type, // Get only pages
        'orderby'        => 'title',
        'order'          => 'ASC',
        'posts_per_page' => -1, // Get all pages
        'post_status'    => 'publish', // Get only published pages
        'fields'         => 'ids', // Only retrieve post IDs for efficiency
    );

    $posts = get_posts($args);

    if ($posts) {
        foreach ($posts as $post) {
            $pages_array[$post] = get_the_title($post); // Add ID => title to the array
        }
    }

    return $pages_array;
}

function my_custom_popup()
{
    $html = "<div class='admin-popup' id='wysiwyg-editor'>";
    $html .= "<div class='close-admin-popup close-wysiwyg-trigger'></div>";
    $html .= "<div class='inner'>";
    $html .= "<textarea id='wysiwyg-editor-field'></textarea>";
    $html .= "<div class='buttons'>";
    $html .= "<a class='submit-wysiwyg-trigger button button-primary button-large'>Submit</a>";
    $html .= "<a class='close-wysiwyg-button close-wysiwyg-trigger button button-secondary button-large'>Close</a>";
    $html .= "</div>";

    $html .= "</div>";
    $html .= "</div>";
    echo $html;
}
add_action('admin_footer', 'my_custom_popup');


function action_admin_head()
{
    ?>
    <style>
        <?php
        /*
        if (_is_module() || get_post_type() == 'producttaxonomypages' || get_post_type() == 'layouts') {
            echo '.wp-block-post-content { display: none !important }';
            echo '.edit-post-header__toolbar, .editor-preview-dropdown__toggle, button[aria-controls="tabs-0-edit-post/block-view"] { display: none !important; }';
        }*/

        ?>.column-wpseo-focuskw,
        .column-wpseo-metadesc,
        .column-wpseo-title {
            display: none !important;
        }
    </style>

    <?php
}
add_action('admin_head', 'action_admin_head');


function action__wp_footer()
{
    if (!is_checkout()) {

        global $popups_id;
        $popups = array_unique($popups_id);

        foreach ($popups as $popup) {
            echo __popup($popup);
        }

        if (current_user_can('administrator')) {
            global $layouts_global, $product_taxonomy_page, $popups_id;
            $layouts_global_val = "<div class='ab-sub-wrapper'>";
            $layouts_global_val .= "<ul role='menu' id='wp-admin-bar-layouts-menu-default' class='ab-submenu'>";
            $layouts_global_val .= count($popups_id);
            if ($product_taxonomy_page) {
                $product_tax_page = array_unique($product_taxonomy_page);
                foreach ($product_tax_page as $tax_page) {
                    $title = get_the_title($tax_page) . ' [Term Page]';
                    $link = get_edit_post_link($tax_page);
                    $layouts_global_val .= "<li>";
                    $layouts_global_val .= "<a class='ab-item' role='menuitem' href='$link'>$title</a>";
                    $layouts_global_val .= "</li>";
                }
            }

            $_layouts = get_post_meta(get_the_ID(), '_layouts', true);
            $_layouts_val = $_layouts ? $_layouts : array();
            $layouts_global_arr = array_merge($layouts_global, $_layouts_val);
            if ($layouts_global_arr) {
                $layouts = [];
                foreach ($layouts_global_arr as $layout) {
                    $layouts[] = apply_filters('wpml_object_id', $layout, 'post');
                }

                $layouts = array_unique($layouts);

                foreach ($layouts as $layout) {
                    $title = get_the_title($layout) . ' [Layout]';
                    $link = get_edit_post_link($layout);

                    $layouts_global_val .= "<li>";
                    $layouts_global_val .= "<a class='ab-item' role='menuitem' href='$link'>$title</a>";
                    $layouts_global_val .= "</li>";
                }
            }
            if ($popups_id) {
                $popups = array_unique($popups_id);
                foreach ($popups as $popup) {
                    $title = get_the_title($popup) . ' [Popup]';
                    $link = get_edit_post_link($popup);

                    $layouts_global_val .= "<li>";
                    $layouts_global_val .= "<a class='ab-item' role='menuitem' href='$link'>$title</a>";
                    $layouts_global_val .= "</li>";
                }
            }





            $layouts_global_val .= "</ul>";
            $layouts_global_val .= "</div>";
    ?>
            <script>
                jQuery(document).ready(function() {
                    jQuery("<?= $layouts_global_val ?>").appendTo('#wp-admin-bar-layouts-menu');
                });
            </script>

        <?php
        }
        ?>
        <script>
            jQuery(document).ready(function() {
                jQuery('#download-gvc').appendTo('.the-content > *:nth-child(2)');
            });
        </script>
        <script>
            if (jQuery('#player').length > 0) {
                video_id = document.getElementById('player').getAttribute('video_id');
                if (video_id) {
                    // 2. This code loads the IFrame Player API code asynchronously.
                    var tag = document.createElement('script');

                    tag.src = "https://www.youtube.com/iframe_api";
                    var firstScriptTag = document.getElementsByTagName('script')[0];
                    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

                    // 3. This function creates an <iframe> (and YouTube player)
                    //    after the API code downloads.
                    var player;

                    function onYouTubeIframeAPIReady() {
                        player = new YT.Player('player', {
                            height: '100%',
                            width: '100%',
                            videoId: video_id,
                            playerVars: {
                                controls: 1,
                                showinfo: 0,
                                rel: 0,
                                autoplay: 1,
                                mute: 1,
                                playsinline: 1,
                                playlist: video_id,
                                loop: 1,
                            },
                            events: {
                                'onReady': onPlayerReady,
                                'onStateChange': function(event) {
                                    var YTP = event.target;
                                    if (event.data === 1) {
                                        var remains = YTP.getDuration() - YTP.getCurrentTime();
                                        if (this.rewindTO)
                                            clearTimeout(this.rewindTO);
                                        this.rewindTO = setTimeout(function() {
                                            YTP.seekTo(0);
                                        }, (remains - 1) * 1000);
                                    }
                                }
                            }
                        });

                        function onPlayerReady(event) {
                            setTimeout(function() {
                                jQuery('.background-image iframe').addClass('show');
                            }, 500);
                        }
                    }
                }
            }
        </script>
    <?php
    }
}

add_action('wp_footer', 'action__wp_footer');

function hero_form_redirect()
{
    $hero_form_enable = get__post_meta('hero_form_enable');
    $hero_form_redirect_type = get__post_meta('hero_form_redirect_type');
    $hero_form_pdf_redirect = get__post_meta('hero_form_pdf_redirect');
    $hero_form_document_redirect = get__post_meta('hero_form_document_redirect');
    $hero_form_redirect_url = get__post_meta('hero_form_redirect_url');
    $hero_form = get__post_meta('hero_form');
    $form_id = isset($hero_form[0]['id']) ? $hero_form[0]['id'] : false;
    $hero_form_document_redirect_id = isset($hero_form_document_redirect[0]['id']) ? $hero_form_document_redirect[0]['id'] : false;


    if ($hero_form_redirect_type == 'pdf') {
        $redirect = wp_get_attachment_url($hero_form_pdf_redirect);
    } else if ($hero_form_redirect_type == 'document') {
        $redirect = do_shortcode('[document_url id=' . $hero_form_document_redirect_id . ']');
    } else {
        $redirect = $hero_form_redirect_url;
    }

    if ($redirect) {
    ?>
        <script>
            document.addEventListener('wpcf7mailsent', function(event) {
                setTimeout(function() {
                    if (<?= $form_id ?> == event.detail.contactFormId) {
                        window.open('<?= $redirect ?>', '_blank');
                    }
                }, 3000);
            }, false);
        </script>
<?php
    }
}

add_action('wp_footer', 'hero_form_redirect');

/**
 * OpenAI Ads Conversion Tracking
 *
 * The Pixel ID lives globally under Theme Settings > OpenAI Ads, but the base
 * pixel itself only renders on pages that opt in via the "OpenAI Ads
 * Conversion" tab (see __openai_ads_conversion_fields() in post-meta.php) —
 * keeps the script off pages with no conversion configured. On those pages we
 * also listen for a successful Contact Form 7 submission matching the
 * configured form and report it as a conversion. Since CF7 submits over AJAX,
 * onclick/onsubmit handlers are unreliable, so we listen for the native
 * `wpcf7mailsent` event instead (fires only after a validated submission,
 * regardless of the form being inside a modal).
 */
function dd_inject_openai_ads_base_pixel()
{
    if (!get__theme_option('openai_ads_enable') || !get__post_meta('openai_ads_conversion_enable')) {
        return;
    }

    $pixel_id = get__theme_option('openai_ads_pixel_id');

    if (!$pixel_id) {
        return;
    }

    $debug = (bool) get__theme_option('openai_ads_debug');
?>
    <!-- OpenAI Ads Measurement Pixel -->
    <script>
        window.oaiq = window.oaiq || function() {
            (window.oaiq.q = window.oaiq.q || []).push(arguments);
        };
        oaiq("init", { pixelId: <?= wp_json_encode($pixel_id) ?>, debug: <?= $debug ? 'true' : 'false' ?> });
    </script>
    <script async src="https://bzrcdn.openai.com/sdk/oaiq.min.js"></script>
    <!-- End OpenAI Ads Measurement Pixel -->
<?php
}
add_action('wp_head', 'dd_inject_openai_ads_base_pixel', 10);

function dd_inject_openai_ads_cf7_listener()
{
    if (!get__theme_option('openai_ads_enable') || !get__post_meta('openai_ads_conversion_enable')) {
        return;
    }

    $form = get__post_meta('openai_ads_conversion_form');
    $form_id = isset($form[0]['id']) ? (int) $form[0]['id'] : 0;

    if (!$form_id) {
        return;
    }

    $event_name = get__post_meta('openai_ads_conversion_event') ?: 'lead_created';
    $event_shapes = array(
        'lead_created'           => 'customer_action',
        'registration_completed' => 'customer_action',
        'appointment_scheduled'  => 'customer_action',
        'custom'                 => 'custom',
    );
    $event_type = isset($event_shapes[$event_name]) ? $event_shapes[$event_name] : 'customer_action';
    $custom_event_name = get__post_meta('openai_ads_conversion_custom_event_name');
?>
    <script>
        document.addEventListener('wpcf7mailsent', function(event) {
            if (<?= $form_id ?> !== event.detail.contactFormId) {
                return;
            }

            if (typeof window.oaiq !== 'function') {
                console.error('OpenAI Ads tracking: window.oaiq is undefined. Ensure the base pixel is enabled under Theme Settings > OpenAI Ads.');
                return;
            }

            var options = { event_id: 'evt_' + Date.now() };
            <?php if ($event_name === 'custom' && $custom_event_name) : ?>
            options.custom_event_name = <?= wp_json_encode($custom_event_name) ?>;
            <?php endif; ?>

            window.oaiq("measure", <?= wp_json_encode($event_name) ?>, {
                type: <?= wp_json_encode($event_type) ?>,
            }, options);
        }, false);
    </script>
<?php
}
add_action('wp_footer', 'dd_inject_openai_ads_cf7_listener', 20);


/**
 * Add a new admin bar menu item.
 *
 * @param WP_Admin_Bar $admin_bar Admin bar reference.
 */
function action_layout_menu($admin_bar)
{
    if (!is_admin()) {
        $admin_bar->add_menu(
            array(
                'id'    => 'layouts-menu',
                'title' => 'Layouts',
                'href'  => false,
                'meta'  => array(
                    'class' => 'menupop layouts-menu',
                    'title' => 'Layouts',
                ),
            )
        );
    }
}
add_action('admin_bar_menu', 'action_layout_menu', 999999);


function action_pre_get_posts($query)
{
    if (!is_admin() && $query->is_main_query() && !is_single() && !is_page()) {
        $query->set('post_status', 'publish');
        $query->set('orderby', 'menu_order');
        $query->set('order', 'ASC');

        if (is_post_type_archive('industries') || is_post_type_archive('guides') || is_post_type_archive('casestudies')) {
            $meta_query[] = [
                'key'     => '_hide_on_list',
                'value'   => 'yes',
                'compare' => 'NOT IN',
            ];

            $query->set('meta_query', $meta_query);
        }

        if (is_post_type_archive('industries') || is_post_type_archive('capabilities')) {
            $query->set('posts_per_page', -1);
        } else if (is_post_type_archive('events') || is_tax('events_category')) {
            $meta_query[] = [
                'key'     => '_event_start_datetime',
                'value'   => date('Y-m-d'),
                'compare' => '>=',
                'type'    => 'DATETIME'
            ];
            $query->set('meta_query', $meta_query);
            $query->set('orderby', 'meta_value');
            $query->set('order', 'ASC');
        } else if (is_home() || is_category()) {
            $query->set('orderby', 'date');
            $query->set('order', 'DESC');
        }
        if (isset($_GET['posts_per_page'])) {
            $query->set('posts_per_page', $_GET['posts_per_page']);
        }
        if (isset($_GET['s'])) {
            $query->set('s', $_GET['s']);
        }
    }
    return;
}
add_action('pre_get_posts', 'action_pre_get_posts', 1);


function action_body_class($classes)
{


    $product_category_page = false;
    if (is_product_taxonomy()) {
        $product_category_page = __get_product_taxonomy_page(get_queried_object()->term_id);
        if (!$product_category_page) {
            $classes[] = 'hide-price';
            $classes[] = 'hide-stock';
            $classes[] = 'product-loop-style-1';
        }
    }
    if (is_single() || is_page()) {
        $header_background = get__post_meta('header_background');

        $hero_hidden = get__post_meta('hero_hidden');

        if ($header_background) {
            $classes[] = "hero-$header_background";
        }

        if ($hero_hidden) {
            $classes[] = 'hero-hidden';
        } else {
            $hero_background = get__post_meta('hero_background');
            $hero_background_youtube = get__post_meta('hero_background_youtube');

            $hero_form_enable = get__post_meta('hero_form_enable');
            $hero_form = get__post_meta('hero_form');

            if ($hero_form_enable && $hero_form) {
                $classes[] = 'hero-has-form';
            }
            if (!$hero_background_youtube && !$hero_background) {
                $classes[] = 'no-hero-bg';
            }
        }
    }



    return $classes;
}

add_filter('body_class', 'action_body_class');



// remove "Private: " from titles
function remove_private_prefix($title)
{
    $title = str_replace('Private: ', '', $title);
    return $title;
}
add_filter('the_title', 'remove_private_prefix');


function action_body_scripts()
{
    $body_scripts = get__theme_option('body_scripts');
    if ($body_scripts) {
        echo $body_scripts;
    }
}

add_action('wp_body_open', 'action_body_scripts');


function remove_canonical()
{
    // Disable for 'search' page
    add_filter('wpseo_canonical', '__return_false', 10, 1);
}
add_action('wp', 'remove_canonical');

/**
 * Filter the permalink for the 'event' custom post type.
 * * This ensures that whenever get_permalink() or the_permalink() is called
 * (e.g., in loop titles or buttons), it returns the external 'event_url'
 * if it exists.
 *
 * @param string  $url  The post's permalink.
 * @param WP_Post $post The post in question.
 * @return string
 */
function wpc_change_event_permalink($url, $post)
{
    // 1. Check if this is the correct post type
    if ('events' !== $post->post_type) {
        return $url;
    }

    // 2. Get the specific meta value
    $event_url = get_post_meta($post->ID, '_event_url', true);

    // 3. If the meta value is populated, use it as the permalink
    if (! empty($event_url)) {
        return esc_url($event_url);
    }

    // 4. Otherwise, return the default internal WordPress URL
    return $url;
}
add_filter('post_type_link', 'wpc_change_event_permalink', 10, 2);


/**
 * Adds submission_date and form_name fields to the data sent to Zapier by
 * the "Contact Form 7 to Zapier" plugin (cf7-to-zapier). That plugin builds
 * its payload from the form's own fields, so wpcf7_posted_data has no
 * effect on it - this filter runs on the data array right before it's
 * sent to the configured Zapier hook.
 */
add_filter('ctz_get_data_from_contact_form', 'dd_append_date_to_cf7_zapier_payload', 10, 2);

function dd_append_date_to_cf7_zapier_payload($data, $contact_form)
{
    if (is_array($data)) {
        $data['submission_date'] = wp_date('c');
        $data['form_name'] = $contact_form->title();
    }

    return $data;
}


/**
 * Registers custom shortcode attributes for Contact Form 7.
 * WordPress shortcodes only accept predefined attributes by default. This filter
 * intercepts CF7 shortcode processing and explicitly allows extra attributes to
 * be passed through to the form's rendering context (where a
 * `[hidden NAME default:shortcode_attr]` field can read them).
 *
 * - `pdf_url`: if the value is an integer it is treated as a 'documents' post ID
 *   (the '_document' attachment meta is resolved to a URL via
 *   wp_get_attachment_url()); otherwise it is passed through as a literal URL.
 * - `speak_to_an_expert_url`: always a literal custom URL, passed through as-is.
 *
 * @param array $out   The array of supported attributes and their processed values.
 * @param array $pairs The array of supported attributes and their default values.
 * @param array $atts  The array of user-defined attributes passed into the shortcode.
 * @return array The filtered array containing the authorized custom attributes.
 */
add_filter('shortcode_atts_wpcf7', 'register_cf7_pdf_url_attribute', 10, 3);

function register_cf7_pdf_url_attribute($out, $pairs, $atts)
{
    if (isset($atts['pdf_url'])) {
        if (is_numeric($atts['pdf_url'])) {
            $attachment_id = get__post_meta_by_id((int) $atts['pdf_url'], 'document');
            if ($attachment_id) {
                $out['pdf_url'] = wp_get_attachment_url($attachment_id);
            }
        } else {
            $out['pdf_url'] = $atts['pdf_url'];
        }
    }

    // Always a literal custom URL — pass through unchanged.
    if (isset($atts['speak_to_an_expert_url'])) {
        $out['speak_to_an_expert_url'] = $atts['speak_to_an_expert_url'];
    }

    return $out;
}


/**
 * Plugin/Snippet Author: Digitally Disruptive - Donald Raymundo
 *
 * Attaches the form's PDF to a Contact Form 7 email, driven by the Mail panel's
 * "File attachments" box. CF7 processes that box as mail-tags, so `[pdf_url]`
 * there resolves to the submitted PDF *URL* — which CF7 cannot attach (it only
 * attaches local file paths). This filter bridges that gap: when the active
 * mail's attachments box opts in with the `absolute_path` flag, e.g.
 *
 *     [pdf_url absolute_path="true"]
 *
 * the submitted `pdf_url` value (a literal PDF URL, or a numeric `documents`
 * post ID — same convention as register_cf7_pdf_url_attribute()) is resolved to
 * an absolute, readable file PATH and attached.
 *
 * Only files inside wp_get_upload_dir() are attached — posted data is untrusted,
 * so arbitrary server paths are rejected.
 *
 * @param array             $components    Mail components (subject, body, attachments, ...).
 * @param WPCF7_ContactForm $contact_form  The form being sent.
 * @param WPCF7_Mail        $mail          The mail template being composed (CF7 5.x+).
 * @return array The components with the resolved PDF appended to `attachments`.
 */
add_filter('wpcf7_mail_components', 'dd_attach_cf7_pdf_url_to_email', 20, 3);

function dd_attach_cf7_pdf_url_to_email($components, $contact_form = null, $mail = null)
{
    if (!$contact_form || !method_exists($contact_form, 'prop')) {
        return $components;
    }

    // Opt-in: the active mail's "File attachments" box must reference pdf_url
    // with the absolute_path flag. Fall back to scanning both mail templates on
    // older CF7 where the $mail object is not passed.
    $names = ($mail && method_exists($mail, 'name')) ? array($mail->name()) : array('mail', 'mail_2');

    $opted_in = false;
    foreach ($names as $name) {
        $props = $contact_form->prop($name);
        $box   = isset($props['attachments']) ? (string) $props['attachments'] : '';
        if (strpos($box, 'pdf_url') !== false && strpos($box, 'absolute_path') !== false) {
            $opted_in = true;
            break;
        }
    }
    if (!$opted_in) {
        return $components;
    }

    if (!class_exists('WPCF7_Submission')) {
        return $components;
    }
    $submission = WPCF7_Submission::get_instance();
    if (!$submission) {
        return $components;
    }

    $posted = $submission->get_posted_data();
    if (empty($posted['pdf_url'])) {
        return $components;
    }

    // CF7 fields can post as an array; take the first value.
    $pdf_value = is_array($posted['pdf_url']) ? reset($posted['pdf_url']) : $posted['pdf_url'];
    $pdf_value = trim((string) $pdf_value);
    if ($pdf_value === '') {
        return $components;
    }

    $path = dd_resolve_pdf_url_to_path($pdf_value);
    if ($path && is_file($path) && is_readable($path)) {
        if (empty($components['attachments'])) {
            $components['attachments'] = array();
        }
        if (!in_array($path, (array) $components['attachments'], true)) {
            $components['attachments'][] = $path;
        }
    }

    return $components;
}

/**
 * Resolves a `pdf_url` field value (numeric `documents` post ID or a URL) to a
 * local, readable file path constrained to the WordPress uploads directory.
 *
 * @param string $value Numeric `documents` post ID, or a literal URL.
 * @return string|false Absolute file path inside the uploads dir, or false.
 */
function dd_resolve_pdf_url_to_path($value)
{
    // Numeric: treat as a `documents` post ID -> its `_document` attachment.
    if (is_numeric($value)) {
        $attachment_id = get__post_meta_by_id((int) $value, 'document');
        return $attachment_id ? get_attached_file($attachment_id) : false;
    }

    // Drop any query string / fragment before mapping a URL to a path.
    $url = strtok($value, '?#');

    // Prefer resolving to a real media-library attachment.
    $attachment_id = attachment_url_to_postid($url);
    if ($attachment_id) {
        $path = get_attached_file($attachment_id);
        if ($path) {
            return $path;
        }
    }

    // Fallback: map an uploads URL straight to its path. Compare host-relative
    // and scheme-insensitively so http/https (or protocol-relative) URLs still
    // match. Reject anything outside the uploads dir; guard path traversal.
    $uploads = wp_get_upload_dir();
    if (!empty($uploads['baseurl'])) {
        $url_rel  = preg_replace('#^https?:#i', '', $url);
        $base_rel = preg_replace('#^https?:#i', '', $uploads['baseurl']);
        if ($base_rel !== '' && strpos($url_rel, $base_rel) === 0) {
            $relative = ltrim(substr($url_rel, strlen($base_rel)), '/');
            $path     = $uploads['basedir'] . '/' . $relative;
            if (strpos($relative, '..') === false && file_exists($path)) {
                return $path;
            }
        }
    }

    return false;
}


/**
 * Plugin/Snippet Author: Digitally Disruptive - Donald Raymundo
 *
 * REST endpoints backing the `dd/cf7-pdf-form` block editor dropdowns. Both are
 * gated to users who can edit posts (the block editor's apiFetch sends the
 * nonce), so the non-public CF7 and Documents post types are not exposed via
 * public core REST. Returns lightweight id/title (+ CF7 hash) lists only.
 */
function dd_register_cf7_pdf_block_rest_routes()
{
    $can_edit = function () {
        return current_user_can('edit_posts');
    };

    register_rest_route('dd/v1', '/cf7-forms', array(
        'methods'             => 'GET',
        'permission_callback' => $can_edit,
        'callback'            => 'dd_rest_list_cf7_forms',
    ));

    register_rest_route('dd/v1', '/documents', array(
        'methods'             => 'GET',
        'permission_callback' => $can_edit,
        'callback'            => 'dd_rest_list_documents',
    ));
}
add_action('rest_api_init', 'dd_register_cf7_pdf_block_rest_routes');

/**
 * Lists Contact Form 7 forms as [{ id, hash, title }]. The hash is used as the
 * shortcode `id` (matching hand-typed `[contact-form-7 id="0b54b62" …]`).
 */
function dd_rest_list_cf7_forms()
{
    if (!class_exists('WPCF7_ContactForm')) {
        return array();
    }

    $forms = WPCF7_ContactForm::find(array('posts_per_page' => -1));
    $out   = array();
    foreach ($forms as $form) {
        // Prefer the hash id (matches hand-typed `id="0b54b62"`); fall back to
        // the numeric post ID on older CF7 builds without hash() — the CF7
        // shortcode accepts either.
        $hash = method_exists($form, 'hash') ? $form->hash() : '';
        $out[] = array(
            'id'    => $form->id(),
            'hash'  => $hash ? $hash : (string) $form->id(),
            'title' => $form->title(),
        );
    }

    return $out;
}

/**
 * Resolves a `documents` post's PDF file URL from its `document` field. Reads the
 * raw Carbon meta key (`_document`) directly — reliable in any context — and only
 * falls back to the Carbon API if that is empty.
 *
 * @param int $doc_id Documents post ID.
 * @return string Attachment URL, or '' when none.
 */
function dd_document_file_url($doc_id)
{
    $attachment_id = get_post_meta($doc_id, '_document', true);
    if (empty($attachment_id)) {
        $attachment_id = get__post_meta_by_id($doc_id, 'document');
    }

    return (!empty($attachment_id) && is_numeric($attachment_id))
        ? (string) wp_get_attachment_url((int) $attachment_id)
        : '';
}

/**
 * Resolves a `documents` post's "Speak to an expert url" from the raw
 * `_speak_to_an_expert_url` meta key (Carbon's storage for that text field),
 * falling back to the Carbon API only if the raw value is empty.
 *
 * @param int $doc_id Documents post ID.
 * @return string The URL, or '' when none.
 */
function dd_document_speak_url($doc_id)
{
    $speak = get_post_meta($doc_id, '_speak_to_an_expert_url', true);
    if ($speak === '' || $speak === false || $speak === null) {
        $speak = get__post_meta_by_id($doc_id, 'speak_to_an_expert_url');
    }

    return $speak ? (string) $speak : '';
}

/**
 * Lists published `documents` posts as [{ id, title, url, speak_url }] for the
 * block's Document source dropdown, using the resolvers above so `url` (PDF) and
 * `speak_url` come straight from the CPT's meta. Avoids flipping show_in_rest on
 * the CPT.
 */
function dd_rest_list_documents()
{
    $posts = get_posts(array(
        'post_type'      => 'documents',
        'post_status'    => 'publish',
        'numberposts'    => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'suppress_filters' => false,
    ));

    $out = array();
    foreach ($posts as $post) {
        $out[] = array(
            'id'        => $post->ID,
            'title'     => get_the_title($post),
            'url'       => dd_document_file_url($post->ID),
            'speak_url' => dd_document_speak_url($post->ID),
        );
    }

    return $out;
}

