<?php
/**
 * Plugin/Snippet Name: CF7 Page Report — URL Normaliser
 * Description: Pure string-transform layer that collapses a raw submitted
 *              "page_url" value (or any other page URL/path) into a canonical
 *              path suitable for grouping and lookup. Handles the specific
 *              defects present in this site's historic CF7 submission data —
 *              see current_url() in shortcodes.php:995-998, which duplicates
 *              the last path segment and appends the query string because it
 *              is built as get_permalink() . basename($_SERVER['REQUEST_URI']).
 *
 *              Scheme and host are discarded entirely so that
 *              coptrztest.local / coptrz.com / www.coptrz.com / http vs https
 *              all collapse onto the same canonical path — identity here is
 *              path-only, by design.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

namespace CoptrzTheme\CF7PageReport;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Collapses raw URLs/paths into a canonical path plus diagnostic shape info.
 *
 * Every result carries a `shape` label naming which branch produced it, so a
 * downstream report/self-test can audit exactly how a given raw value was
 * interpreted rather than trusting the output blindly.
 */
class Url_Normaliser
{
    /** Max length stored in the indexed `page_path` column. */
    const MAX_PATH_LENGTH = 191;

    /**
     * Normalise a raw URL (or bare relative path) into a canonical path.
     *
     * @param string $raw Raw value as submitted (already unslashed by the caller).
     * @return array{path:?string,query:string,shape:string}
     */
    public static function normalise($raw)
    {
        $raw = trim((string) $raw);

        if ('' === $raw) {
            return array('path' => null, 'query' => '', 'shape' => 'empty');
        }

        // Strip a fragment first; it never affects identity.
        $hash_pos = strpos($raw, '#');
        if (false !== $hash_pos) {
            $raw = substr($raw, 0, $hash_pos);
        }
        if ('' === trim($raw)) {
            return array('path' => null, 'query' => '', 'shape' => 'empty');
        }

        $has_scheme = (bool) preg_match('#^[a-z][a-z0-9+.\-]*://#i', $raw);

        if (!$has_scheme && '/' !== substr($raw, 0, 1)) {
            // Bare relative slug with no leading slash at all, e.g. "avy" —
            // observed when get_permalink() returned false at submit time
            // because the form rendered outside the main loop.
            $query_pos = strpos($raw, '?');
            $query     = '';
            if (false !== $query_pos) {
                $query = (string) substr($raw, $query_pos + 1);
                $raw   = substr($raw, 0, $query_pos);
            }

            $parts = self::split_and_clean_segments($raw);

            return self::finish($parts, $query, 'bare_slug', empty($parts));
        }

        // wp_parse_url() discards nothing we want to keep; scheme/host are
        // read here only to be thrown away below.
        $parsed = wp_parse_url($raw);
        $path   = (is_array($parsed) && isset($parsed['path'])) ? (string) $parsed['path'] : '';
        $query  = (is_array($parsed) && isset($parsed['query'])) ? (string) $parsed['query'] : '';

        $parts = self::split_and_clean_segments($path);

        if (empty($parts)) {
            return self::finish(array(), $query, 'home', true);
        }

        // Collapse the duplicated trailing segment produced by the
        // current_url() bug. Drop it once only, never twice, so a genuine
        // repeated-segment path (e.g. /events/events/) is only mangled as
        // much as the bug itself mangled it — not further.
        $shape = 'path';
        $count = count($parts);
        if ($count >= 2 && $parts[$count - 1] === $parts[$count - 2]) {
            array_pop($parts);
            $shape = 'deduplicated';
        }

        return self::finish($parts, $query, $shape, false);
    }

    /**
     * Split a path into cleaned, percent-normalised segments.
     *
     * Decoding then re-encoding each segment means %2F-style mixed-case
     * percent escapes and literal unicode both collapse to one canonical
     * representation, instead of comparing as different paths.
     *
     * @param string $path Raw path (with or without leading/trailing slashes).
     * @return string[]
     */
    private static function split_and_clean_segments($path)
    {
        $raw_parts = array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));

        return array_map(
            static function ($segment) {
                return rawurlencode(rawurldecode($segment));
            },
            $raw_parts
        );
    }

    /**
     * Canonicalise resolved path segments and package the result.
     *
     * @param string[] $parts     Cleaned path segments (empty for home).
     * @param string   $query     Raw query string (kept for display only, never identity).
     * @param string   $shape     Diagnostic label describing which branch produced $parts.
     * @param bool     $force_home Whether an empty segment list should resolve to '/'.
     * @return array{path:?string,query:string,shape:string}
     */
    private static function finish(array $parts, $query, $shape, $force_home)
    {
        if ($force_home || empty($parts)) {
            return array('path' => '/', 'query' => (string) $query, 'shape' => $shape);
        }

        $path = strtolower('/' . implode('/', $parts) . '/');

        // Strip pagination and AMP suffixes so /guides/foo/page/2/ and
        // /guides/foo/amp/ group with the base page.
        $path = (string) preg_replace('#/page/\d+/$#', '/', $path);
        $path = (string) preg_replace('#/amp/$#', '/', $path);

        // Collapse any doubled slashes left over from the transforms above.
        $path = (string) preg_replace('#/{2,}#', '/', $path);

        if ('' === $path) {
            $path = '/';
        }
        if ('/' !== substr($path, 0, 1)) {
            $path = '/' . $path;
        }
        if ('/' !== substr($path, -1)) {
            $path .= '/';
        }

        if (strlen($path) > self::MAX_PATH_LENGTH) {
            $path = substr($path, 0, self::MAX_PATH_LENGTH);
        }

        return array(
            'path'  => $path,
            'query' => (string) $query,
            'shape' => $shape,
        );
    }
}
