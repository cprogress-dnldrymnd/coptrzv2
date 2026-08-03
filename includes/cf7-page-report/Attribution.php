<?php
/**
 * Plugin/Snippet Name: CF7 Page Report — Forward Capture
 * Description: Records which page each NEW Contact Form 7 submission came
 *              from, the moment Advanced CF7 DB saves it. Hooks the plugin's
 *              own `vsz_cf7_after_insert_db` action (vsz-cf7-db-function.php)
 *              because it is the only hook that hands us the freshly-inserted
 *              `data_id`, and it fires while WPCF7_Submission::get_instance()
 *              is still live.
 *
 *              Never writes to wp_cf7_vdata_entry — only to our own
 *              wp_coptrz_cf7_submission_page table — so no new column ever
 *              appears in the plugin's own per-form listing or export.
 *
 *              Derivation ladder, most to least reliable:
 *                1. CF7 core's own `_wpcf7_container_post` (submission meta
 *                   `container_post_id`) — set for every form rendered inside
 *                   the main loop. Guarded against pointing at a reusable
 *                   template post type (popups/layouts/etc — see
 *                   NON_PAGE_POST_TYPES) rather than the real page.
 *                2. The submitting page's URL, from CF7's own submission meta
 *                   `url` — reliable even for AJAX/REST submissions, since
 *                   CF7's get_request_url() already falls back to HTTP_REFERER.
 *                3. The posted `page_url` hidden field, if the form has one.
 *                4. The posted `post_title` hidden field, matched against
 *                   real post titles.
 *                5. Otherwise page_id = 0, source = 'unmatched' — the row is
 *                   still written, preserving whatever path/raw value was
 *                   found, so the report can bucket it rather than lose it.
 *
 *              A bug in this file must never block a form submission or its
 *              email — every write path is wrapped so a Throwable here is
 *              swallowed (and logged when WP_DEBUG is on), never surfaced.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

namespace CoptrzTheme\CF7PageReport;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Forward capture of new CF7 submissions' page attribution.
 */
class Attribution
{
    /**
     * Post types `_wpcf7_container_post` can point at that are reusable
     * templates rather than the page the visitor actually submitted from —
     * CF7 only sets that field when `in_the_loop()`, so a form embedded via
     * a popup/layout/global post box still resolves to ITS container, not
     * the page it was rendered on. Filterable — review against how forms
     * are actually embedded on this site (post-types.php registers these).
     *
     * @var string[]
     */
    const NON_PAGE_POST_TYPES = array('popups', 'layouts', 'globalpostboxes', 'compareproducts', 'producttaxonomypages');

    public static function init()
    {
        add_action('vsz_cf7_after_insert_db', array(__CLASS__, 'capture'), 10, 3);
    }

    /**
     * @param object|\WPCF7_ContactForm $contact_form
     * @param int                       $cf7_id
     * @param int                       $data_id
     */
    public static function capture($contact_form, $cf7_id, $data_id)
    {
        try {
            self::capture_unsafe($contact_form, $cf7_id, $data_id);
        } catch (\Throwable $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- debug-only diagnostic.
                error_log('CF7 Page Report: attribution capture failed — ' . $e->getMessage());
            }
        }
    }

    /**
     * @param object|\WPCF7_ContactForm $contact_form
     * @param int                       $cf7_id
     * @param int                       $data_id
     */
    private static function capture_unsafe($contact_form, $cf7_id, $data_id)
    {
        $cf7_id  = (int) $cf7_id;
        $data_id = (int) $data_id;

        if ($cf7_id <= 0 || $data_id <= 0 || !self::table_ready()) {
            return;
        }

        if (!class_exists('\WPCF7_Submission')) {
            return;
        }

        $submission = \WPCF7_Submission::get_instance();
        if (!$submission) {
            return;
        }

        $posted = (is_object($contact_form) && isset($contact_form->posted_data) && is_array($contact_form->posted_data))
            ? $contact_form->posted_data
            : $submission->get_posted_data();
        $posted = is_array($posted) ? $posted : array();

        $page       = self::derive_page($submission, $posted);
        $submitted  = self::submitted_at($posted);

        self::store($data_id, $cf7_id, $page, $submitted);
    }

    /**
     * Run the derivation ladder described in the file header.
     *
     * @param \WPCF7_Submission     $submission
     * @param array<string,mixed>   $posted
     * @return array<string,mixed>
     */
    private static function derive_page($submission, array $posted)
    {
        $container_post_id = (int) $submission->get_meta('container_post_id');

        if ($container_post_id > 0) {
            $post_type = (string) get_post_type($container_post_id);
            $status    = (string) get_post_status($container_post_id);

            if ('' !== $post_type && 'publish' === $status && !in_array($post_type, self::non_page_post_types(), true)) {
                $permalink = (string) get_permalink($container_post_id);

                return array(
                    'page_id'           => $container_post_id,
                    'container_post_id' => $container_post_id,
                    'post_type'         => $post_type,
                    'page_url_raw'      => '',
                    'page_path'         => $permalink ? (string) wp_parse_url($permalink, PHP_URL_PATH) : '',
                    'page_query'        => '',
                    'page_title'        => (string) get_the_title($container_post_id),
                    'source'            => 'container_post',
                    'confidence'        => 'exact',
                );
            }
        }

        $best_raw   = '';
        $best_path  = '';
        $best_query = '';

        $url = (string) $submission->get_meta('url');
        $result = self::try_url($url, $container_post_id, 'referer_url', $best_raw, $best_path, $best_query);
        if (null !== $result) {
            return $result;
        }

        $page_url_field = self::first_posted_value($posted, array('page_url'));
        $result = self::try_url($page_url_field, $container_post_id, 'page_url_field', $best_raw, $best_path, $best_query);
        if (null !== $result) {
            return $result;
        }

        $post_title_field = self::first_posted_value($posted, array('post_title'));
        if ('' !== $post_title_field) {
            $post_id = Page_Resolver::resolve_by_title($post_title_field);
            if ($post_id > 0) {
                return array(
                    'page_id'           => $post_id,
                    'container_post_id' => $container_post_id,
                    'post_type'         => (string) get_post_type($post_id),
                    'page_url_raw'      => $best_raw,
                    'page_path'         => $best_path,
                    'page_query'        => $best_query,
                    'page_title'        => $post_title_field,
                    'source'            => 'post_title_field',
                    'confidence'        => 'guessed',
                );
            }
        }

        return array(
            'page_id'           => 0,
            'container_post_id' => $container_post_id,
            'post_type'         => '',
            'page_url_raw'      => $best_raw,
            'page_path'         => $best_path,
            'page_query'        => $best_query,
            'page_title'        => '',
            'source'            => 'unmatched',
            'confidence'        => 'none',
        );
    }

    /**
     * Try to resolve a raw URL value to a page. Returns null (keep trying
     * the next fallback) unless it produces either a resolved page_id.
     * $best_raw/$best_path/$best_query are updated by reference so the
     * caller retains the most specific candidate even when nothing resolves,
     * for the final "unmatched" bucket.
     *
     * @param string $raw
     * @param int    $container_post_id
     * @param string $source
     * @param string $best_raw
     * @param string $best_path
     * @param string $best_query
     * @return array<string,mixed>|null
     */
    private static function try_url($raw, $container_post_id, $source, &$best_raw, &$best_path, &$best_query)
    {
        $raw = trim((string) $raw);
        if ('' === $raw) {
            return null;
        }

        $normalised = Url_Normaliser::normalise($raw);
        if (null === $normalised['path']) {
            return null;
        }

        $best_raw   = $raw;
        $best_path  = (string) $normalised['path'];
        $best_query = (string) $normalised['query'];

        $match = Page_Resolver::resolve_path($normalised['path']);
        if ($match['page_id'] <= 0) {
            return null;
        }

        return array(
            'page_id'           => $match['page_id'],
            'container_post_id' => $container_post_id,
            'post_type'         => $match['post_type'],
            'page_url_raw'      => $best_raw,
            'page_path'         => $best_path,
            'page_query'        => $best_query,
            'page_title'        => (string) get_the_title($match['page_id']),
            'source'            => $source,
            'confidence'        => 'derived',
        );
    }

    /**
     * @param array<string,mixed> $posted
     * @param string[]            $candidates
     * @return string
     */
    private static function first_posted_value(array $posted, array $candidates)
    {
        foreach ($candidates as $candidate) {
            if (!isset($posted[$candidate])) {
                continue;
            }

            $value = $posted[$candidate];
            if (is_array($value)) {
                $value = implode(' ', $value);
            }
            $value = trim((string) $value);

            if ('' !== $value) {
                return $value;
            }
        }

        return '';
    }

    /**
     * @param array<string,mixed> $posted
     * @return string 'Y-m-d H:i:s', site-local time.
     */
    private static function submitted_at(array $posted)
    {
        if (!empty($posted['submit_time'])) {
            return (string) $posted['submit_time'];
        }

        return current_time('mysql');
    }

    /**
     * @param int                  $data_id
     * @param int                  $cf7_id
     * @param array<string,mixed>  $page
     * @param string               $submitted_at
     */
    private static function store($data_id, $cf7_id, array $page, $submitted_at)
    {
        global $wpdb;
        $table = Schema::submission_page_table();

        $sql = "INSERT INTO {$table}
                    (data_id, cf7_id, page_id, container_post_id, post_type, page_url_raw, page_path, page_query, page_title, source, confidence, submitted_at, submitted_at_gmt, resolver_version, resolved_at)
                 VALUES (%d, %d, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s)
                 " . Schema::submission_page_upsert_clause();

        $wpdb->query(
            $wpdb->prepare(
                $sql,
                $data_id,
                $cf7_id,
                (int) $page['page_id'],
                (int) $page['container_post_id'],
                (string) $page['post_type'],
                (string) $page['page_url_raw'],
                (string) $page['page_path'],
                (string) $page['page_query'],
                (string) $page['page_title'],
                (string) $page['source'],
                (string) $page['confidence'],
                (string) $submitted_at,
                current_time('mysql', true),
                Page_Resolver::RESOLVER_VERSION,
                current_time('mysql', true)
            )
        );
    }

    /**
     * @return bool Whether our table exists yet (it is created on admin_init/
     *              after_switch_theme, not on the front end — see Schema.php).
     */
    private static function table_ready()
    {
        static $ready = null;
        if (null !== $ready) {
            return $ready;
        }

        global $wpdb;
        $table = Schema::submission_page_table();
        $found = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table));
        $ready = ($found === $table);

        return $ready;
    }

    /**
     * @return string[]
     */
    private static function non_page_post_types()
    {
        return (array) apply_filters('coptrz_cf7_page_report_non_page_post_types', self::NON_PAGE_POST_TYPES);
    }
}

Attribution::init();
