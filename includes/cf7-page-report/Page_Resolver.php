<?php
/**
 * Plugin/Snippet Name: CF7 Page Report — Page Resolver
 * Description: Resolves a canonical path (from Url_Normaliser) to a WordPress
 *              post ID, with a fallback ladder covering pages, posts, and the
 *              custom post types this theme registers (guides, casestudies,
 *              industries, capabilities, events, rentals, landingpages,
 *              documents) plus WooCommerce products — all of which register
 *              their own rewrite rules, which is why url_to_postid() is tried
 *              first rather than pattern-matching rewrite bases by hand.
 *
 *              Results are cached in wp_coptrz_cf7_page_map (Schema::page_map_table())
 *              keyed by an md5 of the path, because this site's historic data
 *              has ~610 distinct paths across ~12,000 submissions — resolving
 *              once and caching turns a would-be 12,000-call cost into ~610.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

namespace CoptrzTheme\CF7PageReport;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Canonical path -> post ID resolution, with an on-disk resolution cache.
 */
class Page_Resolver
{
    /**
     * Bump when the fallback ladder below changes meaningfully, so cached
     * misses/guesses are re-attempted rather than trusted forever. Backfill
     * and Attribution both persist this value alongside their own rows.
     */
    const RESOLVER_VERSION = 1;

    /** @var array<string,array{page_id:int,post_type:string,method:string}> */
    private static $memo = array();

    /**
     * Resolve a canonical path to a post ID.
     *
     * Never throws and never returns null — an unresolved path comes back as
     * page_id 0 with method 'unmatched' so the caller can still record and
     * bucket it, rather than silently dropping the submission.
     *
     * @param string|null $path Canonical path from Url_Normaliser::normalise().
     * @return array{page_id:int,post_type:string,method:string}
     */
    public static function resolve_path($path)
    {
        if (null === $path || '' === $path) {
            return array('page_id' => 0, 'post_type' => '', 'method' => 'empty');
        }

        if (isset(self::$memo[$path])) {
            return self::$memo[$path];
        }

        $cached = self::get_cached($path);
        if (null !== $cached) {
            self::$memo[$path] = $cached;
            return $cached;
        }

        $result = self::resolve_uncached($path);
        self::$memo[$path] = $result;
        self::put_cached($path, $result);

        return $result;
    }

    /**
     * Last-resort match against post titles, used when a submission has a
     * `post_title` field but no usable URL. Present on ~9,573 of the site's
     * historic submissions.
     *
     * Uses WP_Query's exact-match `title` param rather than the deprecated
     * get_page_by_title().
     *
     * @param string $title
     * @return int Post ID, or 0 if no exact title match was found.
     */
    public static function resolve_by_title($title)
    {
        $title = trim((string) $title);
        if ('' === $title) {
            return 0;
        }

        $query = new \WP_Query(
            array(
                'post_type'      => self::public_post_types(),
                'post_status'    => array('publish', 'private'),
                'title'          => $title,
                'posts_per_page' => 1,
                'no_found_rows'  => true,
                'fields'         => 'ids',
            )
        );

        if (!empty($query->posts)) {
            return (int) $query->posts[0];
        }

        return 0;
    }

    /**
     * Clear the in-request memo cache. Used by the self-test so repeated
     * fixture runs don't mask a resolver bug behind a stale memo entry.
     */
    public static function reset_memo()
    {
        self::$memo = array();
    }

    /**
     * @param string $path
     * @return array{page_id:int,post_type:string,method:string}
     */
    private static function resolve_uncached($path)
    {
        if ('/' === $path) {
            return self::resolve_front_page();
        }

        $post_id = (int) url_to_postid(home_url($path));
        if ($post_id > 0) {
            return array('page_id' => $post_id, 'post_type' => (string) get_post_type($post_id), 'method' => 'url_to_postid');
        }

        $public_types = self::public_post_types();

        $post = get_page_by_path(trim($path, '/'), OBJECT, $public_types);
        if ($post instanceof \WP_Post) {
            return array('page_id' => (int) $post->ID, 'post_type' => $post->post_type, 'method' => 'page_by_path');
        }

        $slug_result = self::resolve_by_last_segment($path, $public_types);
        if (null !== $slug_result) {
            return $slug_result;
        }

        $redirect_target = self::resolve_via_redirection($path);
        if ($redirect_target > 0) {
            return array('page_id' => $redirect_target, 'post_type' => (string) get_post_type($redirect_target), 'method' => 'redirection');
        }

        return array('page_id' => 0, 'post_type' => '', 'method' => 'unmatched');
    }

    /**
     * @return array{page_id:int,post_type:string,method:string}
     */
    private static function resolve_front_page()
    {
        $front = (int) get_option('page_on_front');
        if ($front > 0 && get_post($front)) {
            return array('page_id' => $front, 'post_type' => (string) get_post_type($front), 'method' => 'front_page');
        }

        $post_id = (int) url_to_postid(home_url('/'));
        if ($post_id > 0) {
            return array('page_id' => $post_id, 'post_type' => (string) get_post_type($post_id), 'method' => 'front_page');
        }

        return array('page_id' => 0, 'post_type' => '', 'method' => 'unmatched');
    }

    /**
     * Fallback B: try the final path segment as a post slug. Catches renamed
     * rewrite bases and the "bare_slug" shapes normalise() produces when
     * get_permalink() failed at submit time.
     *
     * @param string   $path
     * @param string[] $public_types
     * @return array{page_id:int,post_type:string,method:string}|null
     */
    private static function resolve_by_last_segment($path, array $public_types)
    {
        $segments = array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));
        $slug     = !empty($segments) ? (string) end($segments) : '';

        if ('' === $slug) {
            return null;
        }

        $candidates = get_posts(
            array(
                'name'           => $slug,
                'post_type'      => $public_types,
                'post_status'    => array('publish', 'private'),
                'numberposts'    => 2,
                'fields'         => 'ids',
                'no_found_rows'  => true,
            )
        );

        if (empty($candidates)) {
            return null;
        }

        $method = count($candidates) > 1 ? 'slug_guess_ambiguous' : 'slug_guess';

        return array(
            'page_id'   => (int) $candidates[0],
            'post_type' => (string) get_post_type($candidates[0]),
            'method'    => $method,
        );
    }

    /**
     * Fallback C, off by default: match the Redirection plugin's own table
     * for a renamed/redirected page and resolve the redirect target instead.
     * Gated on an option because this is a heavier query and a coarser
     * match than the others — enable only after measuring coverage without it.
     *
     * @param string $path
     * @return int Post ID of the redirect target, or 0.
     */
    private static function resolve_via_redirection($path)
    {
        if (!get_option('coptrz_cf7_page_report_use_redirection', false)) {
            return 0;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'redirection_items';

        $exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table));
        if ($exists !== $table) {
            return 0;
        }

        $lookup = trim($path, '/');

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- table name only, values are prepared.
        $target = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT action_data FROM {$table} WHERE match_url = %s AND regex = 0 AND status = 'enabled' ORDER BY id ASC LIMIT 1",
                $lookup
            )
        );

        if (empty($target)) {
            return 0;
        }

        $normalised = Url_Normaliser::normalise((string) $target);
        if (empty($normalised['path'])) {
            return 0;
        }

        // Resolve the redirect target directly (not via resolve_path()) so a
        // misconfigured redirect chain can never recurse back into this method.
        return (int) url_to_postid(home_url($normalised['path']));
    }

    /**
     * @param string $path
     * @return array{page_id:int,post_type:string,method:string}|null
     */
    private static function get_cached($path)
    {
        global $wpdb;
        $table = Schema::page_map_table();
        $hash  = md5($path);

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT page_id, post_type, method, resolver_version FROM {$table} WHERE path_hash = %s",
                $hash
            ),
            ARRAY_A
        );

        if (!$row) {
            return null;
        }

        if ((int) $row['resolver_version'] < self::RESOLVER_VERSION) {
            return null;
        }

        return array(
            'page_id'   => (int) $row['page_id'],
            'post_type' => (string) $row['post_type'],
            'method'    => (string) $row['method'],
        );
    }

    /**
     * @param string $path
     * @param array{page_id:int,post_type:string,method:string} $result
     */
    private static function put_cached($path, array $result)
    {
        global $wpdb;
        $table = Schema::page_map_table();
        $hash  = md5($path);

        $wpdb->query(
            $wpdb->prepare(
                "INSERT INTO {$table} (path_hash, page_path, page_id, post_type, method, resolver_version, resolved_at)
                 VALUES (%s, %s, %d, %s, %s, %d, %s)
                 ON DUPLICATE KEY UPDATE
                    page_path = VALUES(page_path),
                    page_id = VALUES(page_id),
                    post_type = VALUES(post_type),
                    method = VALUES(method),
                    resolver_version = VALUES(resolver_version),
                    resolved_at = VALUES(resolved_at)",
                $hash,
                $path,
                $result['page_id'],
                $result['post_type'],
                $result['method'],
                self::RESOLVER_VERSION,
                current_time('mysql')
            )
        );
    }

    /**
     * Public post types eligible for resolution, attachments excluded.
     *
     * @return string[]
     */
    private static function public_post_types()
    {
        $types = get_post_types(array('public' => true), 'names');
        unset($types['attachment']);

        return array_values($types);
    }
}
