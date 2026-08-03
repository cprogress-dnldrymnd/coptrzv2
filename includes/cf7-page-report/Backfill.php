<?php
/**
 * Plugin/Snippet Name: CF7 Page Report — Historic Backfill
 * Description: Attributes EXISTING CF7 submissions to a page. Attribution.php
 *              only captures new submissions going forward — this is what
 *              makes the report answer questions about a date range that
 *              already happened, which is the entire reason this feature
 *              exists.
 *
 *              Historic rows have no live WPCF7_Submission, no
 *              container_post_id, and no referer URL — only whatever was
 *              posted with the form, so the ladder here is shorter than
 *              Attribution's: the `page_url` field, then the `post_title`
 *              field, then unmatched.
 *
 *              Cursor-based (a wp_cf7_vdata.id, not an offset) so batches
 *              stay O(batch) regardless of how far in the run has progressed
 *              — an offset would get slower as it grows.
 *
 *              Two entry points, deliberately no admin-screen button: the
 *              hourly `coptrz_cf7_page_reconcile` cron (self-heals on its
 *              own — in practice this has already caught up a fresh
 *              12k-submission history within one page load's worth of
 *              WP-Cron's pseudo-cron dispatch) and `wp coptrz cf7-pages
 *              backfill` (cli.php) for forcing an immediate full pass.
 *
 *              The multi-row upsert is idempotent (PRIMARY KEY is data_id)
 *              and guarded so a re-run never downgrades an exact
 *              forward-captured row — see Schema::submission_page_upsert_clause().
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

namespace CoptrzTheme\CF7PageReport;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Batched historic backfill plus an hourly reconcile pass.
 */
class Backfill
{
    const DEFAULT_BATCH_SIZE = 500;

    const RECONCILE_HOOK = 'coptrz_cf7_page_reconcile';

    public static function init()
    {
        add_action(self::RECONCILE_HOOK, array(__CLASS__, 'reconcile'));
        add_action('after_switch_theme', array(__CLASS__, 'schedule_reconcile'));
        add_action('switch_theme', array(__CLASS__, 'unschedule_reconcile'));

        if (!wp_next_scheduled(self::RECONCILE_HOOK)) {
            self::schedule_reconcile();
        }
    }

    public static function schedule_reconcile()
    {
        if (!wp_next_scheduled(self::RECONCILE_HOOK)) {
            wp_schedule_event(time(), 'hourly', self::RECONCILE_HOOK);
        }
    }

    public static function unschedule_reconcile()
    {
        $timestamp = wp_next_scheduled(self::RECONCILE_HOOK);
        if ($timestamp) {
            wp_unschedule_event($timestamp, self::RECONCILE_HOOK);
        }
    }

    /**
     * Process one batch of submissions starting after $cursor. Called by
     * reconcile() (below) and by `wp coptrz cf7-pages backfill` (cli.php).
     *
     * @param int $cursor     A wp_cf7_vdata.id; processes rows with id > this.
     * @param int $batch_size
     * @return array{cursor:int,processed:int,done:bool}
     */
    public static function process_batch($cursor, $batch_size)
    {
        global $wpdb;
        $data_table  = Schema::cf7_data_table();
        $entry_table = Schema::cf7_entry_table();

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, created FROM {$data_table} WHERE id > %d ORDER BY id ASC LIMIT %d",
                (int) $cursor,
                max(1, (int) $batch_size)
            )
        );

        if (empty($rows)) {
            return array('cursor' => (int) $cursor, 'processed' => 0, 'done' => true);
        }

        $ids    = array_map(static function ($row) {
            return (int) $row->id;
        }, $rows);
        $min_id = min($ids);
        $max_id = max($ids);

        // Requires the `coptrz_data_id` index (Schema::install_indexes()) to
        // stay a range scan rather than a full ~209k-row table scan.
        $entries = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT data_id, cf7_id, name, value FROM {$entry_table} WHERE data_id BETWEEN %d AND %d",
                $min_id,
                $max_id
            )
        );

        $by_submission = self::group_entries($entries);

        $values_sql  = array();
        $values_args = array();
        $resolved_at = current_time('mysql', true);

        foreach ($rows as $row) {
            $data_id = (int) $row->id;
            $bucket  = $by_submission[$data_id] ?? array('cf7_id' => 0, 'fields' => array());
            $cf7_id  = (int) $bucket['cf7_id'];

            if ($cf7_id <= 0) {
                // No entry rows at all for this data_id — an orphaned or
                // already-deleted submission. Nothing to attribute.
                continue;
            }

            $fields       = $bucket['fields'];
            $page_url_raw = $fields['page_url'] ?? '';
            $post_title   = $fields['post_title'] ?? '';
            $submit_time  = $fields['submit_time'] ?? '';

            $resolved = self::resolve_historic($page_url_raw, $post_title);

            $submitted_at     = '' !== $submit_time ? $submit_time : self::derive_submitted_at_from_created((string) $row->created);
            $submitted_at_gmt = (string) $row->created;
            $page_title_value = $resolved['page_id'] > 0 ? (string) get_the_title($resolved['page_id']) : '';

            $values_sql[] = '(%d, %d, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s)';
            array_push(
                $values_args,
                $data_id,
                $cf7_id,
                (int) $resolved['page_id'],
                0,
                (string) $resolved['post_type'],
                (string) $resolved['page_url_raw'],
                (string) $resolved['page_path'],
                (string) $resolved['page_query'],
                $page_title_value,
                (string) $resolved['source'],
                (string) $resolved['confidence'],
                $submitted_at,
                $submitted_at_gmt,
                Page_Resolver::RESOLVER_VERSION,
                $resolved_at
            );
        }

        if (!empty($values_sql)) {
            self::upsert_chunk($values_sql, $values_args);
        }

        return array(
            'cursor'    => $max_id,
            'processed' => count($rows),
            'done'      => false,
        );
    }

    /**
     * Attribute anything missing since the last run, then remove orphans.
     * Hourly cron. Covers: the CF7 DB plugin's own CSV import (inserts
     * directly, bypassing Attribution's hook), and any gap left by a brief
     * period where our table didn't exist yet.
     */
    public static function reconcile()
    {
        if (!Schema::cf7_entry_table_exists()) {
            return;
        }

        Schema::maybe_install();

        global $wpdb;
        $our_table  = Schema::submission_page_table();
        $data_table = Schema::cf7_data_table();

        $first_gap = $wpdb->get_var(
            "SELECT MIN(d.id) FROM {$data_table} d
             LEFT JOIN {$our_table} o ON o.data_id = d.id
             WHERE o.data_id IS NULL"
        );

        if (null !== $first_gap) {
            $cursor = ((int) $first_gap) - 1;
            do {
                $result = self::process_batch($cursor, self::DEFAULT_BATCH_SIZE);
                $cursor = $result['cursor'];
            } while (!$result['done']);
        }

        self::delete_orphans();
    }

    /**
     * @param array<int,object> $entries
     * @return array<int,array{cf7_id:int,fields:array<string,string>}>
     */
    private static function group_entries(array $entries)
    {
        $by_submission = array();

        foreach ($entries as $entry) {
            $data_id = (int) $entry->data_id;

            if (!isset($by_submission[$data_id])) {
                $by_submission[$data_id] = array('cf7_id' => 0, 'fields' => array());
            }

            if ((int) $entry->cf7_id > 0) {
                $by_submission[$data_id]['cf7_id'] = (int) $entry->cf7_id;
            }

            $by_submission[$data_id]['fields'][(string) $entry->name] = (string) $entry->value;
        }

        return $by_submission;
    }

    /**
     * Historic derivation ladder: page_url field, then post_title field,
     * then unmatched. No container_post_id or referer URL exists for
     * historic rows, unlike Attribution's live-request ladder.
     *
     * @param string $page_url_raw
     * @param string $post_title_raw
     * @return array{page_id:int,post_type:string,page_url_raw:string,page_path:string,page_query:string,source:string,confidence:string}
     */
    private static function resolve_historic($page_url_raw, $post_title_raw)
    {
        $page_url_raw = trim((string) $page_url_raw);
        $best_path    = '';
        $best_query   = '';

        if ('' !== $page_url_raw) {
            $normalised = Url_Normaliser::normalise($page_url_raw);

            if (null !== $normalised['path']) {
                $best_path  = (string) $normalised['path'];
                $best_query = (string) $normalised['query'];

                $match = Page_Resolver::resolve_path($best_path);
                if ($match['page_id'] > 0) {
                    return array(
                        'page_id'      => $match['page_id'],
                        'post_type'    => $match['post_type'],
                        'page_url_raw' => $page_url_raw,
                        'page_path'    => $best_path,
                        'page_query'   => $best_query,
                        'source'       => 'page_url_field',
                        'confidence'   => 'derived',
                    );
                }
            }
        }

        $post_title_raw = trim((string) $post_title_raw);
        if ('' !== $post_title_raw) {
            $post_id = Page_Resolver::resolve_by_title($post_title_raw);
            if ($post_id > 0) {
                return array(
                    'page_id'      => $post_id,
                    'post_type'    => (string) get_post_type($post_id),
                    'page_url_raw' => $page_url_raw,
                    'page_path'    => $best_path,
                    'page_query'   => $best_query,
                    'source'       => 'post_title_field',
                    'confidence'   => 'guessed',
                );
            }
        }

        return array(
            'page_id'      => 0,
            'post_type'    => '',
            'page_url_raw' => $page_url_raw,
            'page_path'    => $best_path,
            'page_query'   => $best_query,
            'source'       => 'unmatched',
            'confidence'   => 'none',
        );
    }

    /**
     * @param string $created_gmt 'Y-m-d H:i:s', UTC (wp_cf7_vdata.created).
     * @return string 'Y-m-d H:i:s', site-local estimate.
     */
    private static function derive_submitted_at_from_created($created_gmt)
    {
        $timestamp = strtotime($created_gmt . ' UTC');
        if (false === $timestamp) {
            return $created_gmt;
        }

        $offset_seconds = (float) get_option('gmt_offset') * HOUR_IN_SECONDS;

        return gmdate('Y-m-d H:i:s', $timestamp + (int) $offset_seconds);
    }

    /**
     * @param string[] $values_sql  '(%d, %d, ...)' tuples, one per row.
     * @param array<int,mixed> $values_args Flat arg list, 15 per tuple, same order.
     */
    private static function upsert_chunk(array $values_sql, array $values_args)
    {
        global $wpdb;
        $table = Schema::submission_page_table();

        // At most 200 tuples (15 args each) per statement, to stay well
        // under max_allowed_packet and keep each write fast.
        $sql_chunks = array_chunk($values_sql, 200);
        $arg_chunks = array_chunk($values_args, 200 * 15);

        foreach ($sql_chunks as $i => $chunk_sql) {
            $chunk_args = $arg_chunks[$i] ?? array();

            $sql = "INSERT INTO {$table}
                        (data_id, cf7_id, page_id, container_post_id, post_type, page_url_raw, page_path, page_query, page_title, source, confidence, submitted_at, submitted_at_gmt, resolver_version, resolved_at)
                     VALUES " . implode(', ', $chunk_sql) . ' ' . Schema::submission_page_upsert_clause();

            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- built via prepare() below.
            $wpdb->query($wpdb->prepare($sql, $chunk_args));
        }
    }

    /**
     * Remove attribution rows whose submission no longer exists — deleting
     * an entry via the plugin's own admin UI doesn't notify this feature.
     */
    private static function delete_orphans()
    {
        global $wpdb;
        $our_table  = Schema::submission_page_table();
        $data_table = Schema::cf7_data_table();

        $wpdb->query(
            "DELETE o FROM {$our_table} o
             LEFT JOIN {$data_table} d ON d.id = o.data_id
             WHERE d.id IS NULL
             LIMIT 2000"
        );
    }
}

Backfill::init();
