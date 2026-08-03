<?php
/**
 * Plugin/Snippet Name: CF7 Page Report — Self Test
 * Description: Verification tool for the URL normaliser and page resolver,
 *              in the same idiom as includes/meta-shim/self-test.php: an
 *              admin_init callback, inert unless the query var is present,
 *              manage_options only, prints a plain <pre> report and exits.
 *              This theme has no PHPUnit (no tests/ dir, no require-dev in
 *              composer.json) — this is the theme's existing pattern for
 *              verification instead of inventing a test framework for one
 *              feature.
 *
 *              Two modes:
 *
 *                Fixtures — /wp-admin/?coptrz_cf7_report_selftest=fixtures
 *                  Runs Url_Normaliser over ~20 inputs taken from the real
 *                  submission data (duplicated segment, query strings, bare
 *                  slugs, percent-encoding, host/scheme variance, etc.) and
 *                  asserts the expected canonical path. Pure logic, no DB
 *                  writes, safe to run anytime.
 *
 *                Live    — /wp-admin/?coptrz_cf7_report_selftest=live
 *                  Runs the normaliser AND resolver over every distinct
 *                  `page_url` value actually stored in wp_cf7_vdata_entry,
 *                  and reports resolution coverage plus every path that
 *                  didn't resolve — this is the coverage gate described in
 *                  the plan doc, and it is read-only (Page_Resolver's cache
 *                  writes are the only DB writes, and those are harmless/
 *                  idempotent). Run this BEFORE trusting backfilled totals.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

namespace CoptrzTheme\CF7PageReport;

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_init', __NAMESPACE__ . '\\coptrz_cf7_report_self_test');

function coptrz_cf7_report_self_test()
{
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only diagnostic, gated on manage_options below.
    if (!isset($_GET['coptrz_cf7_report_selftest']) || !current_user_can('manage_options')) {
        return;
    }

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $mode = sanitize_key(wp_unslash($_GET['coptrz_cf7_report_selftest']));

    header('Content-Type: text/html; charset=utf-8');
    echo '<pre style="padding:20px;font:13px/1.5 monospace;">';
    echo "CF7 PAGE REPORT — SELF TEST ({$mode})\n";
    echo str_repeat('=', 70) . "\n\n";

    if ('live' === $mode) {
        run_live_check();
    } else {
        run_fixture_check();
    }

    echo '</pre>';
    exit;
}

/**
 * Table-driven Url_Normaliser assertions against real observed shapes.
 */
function run_fixture_check()
{
    $cases = array(
        // input => array(expected_path, expected_shape)
        'http://coptrztest.local/guides/become-a-drone-pilot/become-a-drone-pilot' => array('/guides/become-a-drone-pilot/', 'deduplicated'),
        'http://coptrztest.local/blog/top-5-drone-mapping-software/?utm_source=Email' => array('/blog/top-5-drone-mapping-software/', 'path'),
        'http://coptrztest.local/blog/foo/foo?utm_source=chatgpt.com' => array('/blog/foo/', 'deduplicated'),
        'http://coptrztest.local/' => array('/', 'home'),
        'http://coptrztest.local' => array('/', 'home'),
        'avy' => array('/avy/', 'bare_slug'),
        '' => array(null, 'empty'),
        '   ' => array(null, 'empty'),
        'HTTP://COPTRZ.COM/Guides/Foo/Foo' => array('/guides/foo/', 'deduplicated'),
        'https://www.coptrz.com/guides/foo/' => array('/guides/foo/', 'path'),
        'http://coptrz.com/guides/foo/' => array('/guides/foo/', 'path'),
        'http://coptrztest.local/guides/foo/page/2/' => array('/guides/foo/', 'path'),
        'http://coptrztest.local/guides/foo/amp/' => array('/guides/foo/', 'path'),
        // Documented accepted false positive: a genuinely repeated segment
        // collapses the same way the current_url() bug's output would.
        'http://coptrztest.local/events/events' => array('/events/', 'deduplicated'),
        'http://coptrztest.local/guides/foo%20bar/foo%20bar' => array('/guides/foo%20bar/', 'deduplicated'),
        'http://coptrztest.local/guides/FOO/FOO' => array('/guides/foo/', 'deduplicated'),
        'http://coptrztest.local/?page_id=416660?page_id=416660&preview=true' => array('/', 'home'),
        '/relative/path/path' => array('/relative/path/', 'deduplicated'),
        'https://coptrz.com/case-studies/aba-surveying-ltd/?utm_source=Email&utm_medium=WebsitePage' => array('/case-studies/aba-surveying-ltd/', 'path'),
    );

    $pass = 0;
    $fail = 0;

    foreach ($cases as $input => $expected) {
        $result = Url_Normaliser::normalise($input);
        $ok     = $result['path'] === $expected[0] && $result['shape'] === $expected[1];

        printf(
            "[%s] input=%-75s got=path:%-40s shape:%-14s expected=path:%-40s shape:%s\n",
            $ok ? 'OK  ' : 'FAIL',
            var_export($input, true),
            var_export($result['path'], true),
            $result['shape'],
            var_export($expected[0], true),
            $expected[1]
        );

        $ok ? $pass++ : $fail++;
    }

    echo "\n{$pass} passed, {$fail} failed.\n";
}

/**
 * Resolution coverage over every distinct page_url value actually stored —
 * the coverage gate described in the plan doc. Read-only aside from the
 * resolver's own cache table writes.
 */
function run_live_check()
{
    global $wpdb;

    if (!Schema::cf7_entry_table_exists()) {
        echo "Advanced CF7 DB's data table was not found — is the plugin active?\n";
        return;
    }

    $entry_table = Schema::cf7_entry_table();

    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- table name only.
    $raw_urls = $wpdb->get_col("SELECT DISTINCT value FROM {$entry_table} WHERE name = 'page_url'");

    printf("Distinct page_url values stored: %d\n\n", count($raw_urls));

    $resolved_count   = 0;
    $unresolved       = array();
    $shape_counts     = array();

    foreach ($raw_urls as $raw) {
        $normalised = Url_Normaliser::normalise((string) $raw);
        $shape_counts[$normalised['shape']] = ($shape_counts[$normalised['shape']] ?? 0) + 1;

        if (null === $normalised['path']) {
            $unresolved[] = array('raw' => $raw, 'path' => '(empty)');
            continue;
        }

        $match = Page_Resolver::resolve_path($normalised['path']);
        if ($match['page_id'] > 0) {
            $resolved_count++;
        } else {
            $unresolved[] = array('raw' => $raw, 'path' => $normalised['path']);
        }
    }

    $total = count($raw_urls);
    $pct   = $total > 0 ? round(($resolved_count / $total) * 100, 1) : 0;

    printf("Resolved: %d / %d (%s%%)\n", $resolved_count, $total, $pct);
    printf("Unresolved: %d\n\n", count($unresolved));

    echo "Shape breakdown:\n";
    foreach ($shape_counts as $shape => $count) {
        printf("  %-14s %d\n", $shape, $count);
    }
    echo "\n";

    if (!empty($unresolved)) {
        echo "Unresolved paths (raw value -> normalised path):\n";
        foreach (array_slice($unresolved, 0, 100) as $item) {
            printf("  %-80s -> %s\n", $item['raw'], $item['path']);
        }
        if (count($unresolved) > 100) {
            printf("  ... and %d more.\n", count($unresolved) - 100);
        }
        echo "\n";
    }

    echo str_repeat('-', 70) . "\n";
    echo "Overall backfill progress and coverage:\n\n";

    $progress = Repository::backfill_progress();
    printf("wp_cf7_vdata rows: %d, attribution rows written: %d\n", $progress['total'], $progress['processed']);

    $coverage = Repository::coverage(
        array(
            'start'         => '1970-01-01 00:00:00',
            'end_exclusive' => gmdate('Y-m-d H:i:s', time() + DAY_IN_SECONDS),
        )
    );
    printf(
        "Of attributed rows (all time): %d resolved to a page, %d unmatched URL, %d with no page data.\n",
        $coverage['attributed'],
        $coverage['unmatched_url'],
        $coverage['no_page_data']
    );

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only diagnostic.
    if (isset($_GET['start'], $_GET['end'])) {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $start = sanitize_text_field(wp_unslash($_GET['start'])) . ' 00:00:00';
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $end = sanitize_text_field(wp_unslash($_GET['end'])) . ' 23:59:59';

        echo "\n" . str_repeat('-', 70) . "\n";
        printf("Cross-check for %s to %s:\n\n", $start, $end);

        $independent = Repository::true_submission_count_by_submit_time($start, $end);
        printf("Independent count via submit_time (bypasses our table): %d\n", $independent);

        $ours = (int) $wpdb->get_var(
            $wpdb->prepare(
                'SELECT COUNT(*) FROM ' . Schema::submission_page_table() . ' WHERE submitted_at >= %s AND submitted_at <= %s',
                $start,
                $end
            )
        );
        printf("Our attribution table's count for the same window: %d\n", $ours);
        printf("Match: %s\n", $independent === $ours ? 'YES' : 'NO — investigate before trusting this window.');
    } else {
        echo "\nAdd &start=YYYY-MM-DD&end=YYYY-MM-DD to cross-check a specific date window against an independent query.\n";
    }
}
