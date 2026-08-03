<?php
/**
 * Plugin/Snippet Name: CF7 Page Report — Repository
 * Description: Every SQL query the report screen and export run, in one
 *              place. All filtering happens against our own indexed
 *              wp_coptrz_cf7_submission_page table (Schema::submission_page_table());
 *              the Advanced CF7 DB plugin's wp_cf7_vdata_entry table is only
 *              ever touched afterwards, for a small, already-known set of
 *              data_ids (the current page of 50), which is what
 *              Schema::CF7_ENTRY_INDEXES' `coptrz_data_id` index exists for.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

namespace CoptrzTheme\CF7PageReport;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * SQL layer for the page report.
 *
 * Filters array shape used throughout:
 *   start                 string   Inclusive lower bound, 'Y-m-d H:i:s'.
 *   end_exclusive         string   Exclusive upper bound, 'Y-m-d H:i:s'.
 *   page_ids              int[]    Optional explicit page ID restriction.
 *   cf7_ids               int[]    Optional explicit form ID restriction.
 *   include_unmatched     bool     Include the page_id=0 "(unmatched URL)" bucket in detail rows.
 *   include_no_page_data  bool     Include the page_id=0,page_path='' "(no page data)" bucket in detail rows.
 */
class Repository
{
    /**
     * Grouped submission counts per attributed page.
     *
     * @param array<string,mixed> $filters
     * @return array<int,array<string,mixed>>
     */
    public static function matched_summary(array $filters)
    {
        global $wpdb;
        $table = Schema::submission_page_table();

        list($where, $params) = self::base_where($filters);
        $clauses = array($where, 'page_id > 0');

        if (!empty($filters['page_ids'])) {
            $clauses[] = self::in_clause('page_id', $filters['page_ids'], $params);
        }

        $where_sql = implode(' AND ', $clauses);

        $sql = "SELECT page_id, page_path, post_type, COUNT(*) AS submissions,
                       COUNT(DISTINCT cf7_id) AS form_count,
                       MIN(submitted_at) AS first_at, MAX(submitted_at) AS last_at
                FROM {$table}
                WHERE {$where_sql}
                GROUP BY page_id, page_path, post_type
                ORDER BY submissions DESC";

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- built from prepare() below.
        return (array) $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A);
    }

    /**
     * Grouped counts for URLs that had no resolvable page, bucketed by path
     * so the size of the gap is visible rather than silently dropped.
     *
     * @param array<string,mixed> $filters
     * @return array<int,array<string,mixed>>
     */
    public static function unmatched_summary(array $filters)
    {
        global $wpdb;
        $table = Schema::submission_page_table();

        list($where, $params) = self::base_where($filters);
        $where_sql = $where . " AND page_id = 0 AND page_path != ''";

        $sql = "SELECT page_path, COUNT(*) AS submissions,
                       COUNT(DISTINCT cf7_id) AS form_count,
                       MIN(submitted_at) AS first_at, MAX(submitted_at) AS last_at
                FROM {$table}
                WHERE {$where_sql}
                GROUP BY page_path
                ORDER BY submissions DESC";

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        return (array) $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A);
    }

    /**
     * Count of submissions with no page data at all (no page_url field, no
     * container post, no referer — forms lacking any of the hidden fields).
     *
     * @param array<string,mixed> $filters
     * @return int
     */
    public static function no_page_data_count(array $filters)
    {
        global $wpdb;
        $table = Schema::submission_page_table();

        list($where, $params) = self::base_where($filters);
        $where_sql = $where . " AND page_id = 0 AND page_path = ''";

        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$where_sql}";

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        return (int) $wpdb->get_var($wpdb->prepare($sql, $params));
    }

    /**
     * Attribution coverage for the current filter set — the honesty check
     * that stops the report being silently wrong about how much of the
     * period it can actually account for.
     *
     * @param array<string,mixed> $filters
     * @return array{total:int,attributed:int,unmatched_url:int,no_page_data:int}
     */
    public static function coverage(array $filters)
    {
        global $wpdb;
        $table = Schema::submission_page_table();

        list($where, $params) = self::base_where($filters);

        $sql = "SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN page_id > 0 THEN 1 ELSE 0 END) AS attributed,
                    SUM(CASE WHEN page_id = 0 AND page_path != '' THEN 1 ELSE 0 END) AS unmatched_url,
                    SUM(CASE WHEN page_id = 0 AND page_path = '' THEN 1 ELSE 0 END) AS no_page_data
                FROM {$table}
                WHERE {$where}";

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $row = $wpdb->get_row($wpdb->prepare($sql, $params), ARRAY_A);

        return array(
            'total'         => (int) ($row['total'] ?? 0),
            'attributed'    => (int) ($row['attributed'] ?? 0),
            'unmatched_url' => (int) ($row['unmatched_url'] ?? 0),
            'no_page_data'  => (int) ($row['no_page_data'] ?? 0),
        );
    }

    /**
     * Page x form submission counts, for the pivot table shown when the
     * selection spans a small number of forms.
     *
     * @param array<string,mixed> $filters
     * @return array<int,array<string,mixed>>
     */
    public static function page_form_pivot(array $filters)
    {
        global $wpdb;
        $table = Schema::submission_page_table();

        list($where, $params) = self::base_where($filters);
        $clauses = array($where, 'page_id > 0');

        if (!empty($filters['page_ids'])) {
            $clauses[] = self::in_clause('page_id', $filters['page_ids'], $params);
        }

        $where_sql = implode(' AND ', $clauses);

        $sql = "SELECT page_id, cf7_id, COUNT(*) AS submissions
                FROM {$table}
                WHERE {$where_sql}
                GROUP BY page_id, cf7_id
                ORDER BY page_id ASC, submissions DESC";

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        return (array) $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A);
    }

    /**
     * @param array<string,mixed> $filters
     * @return int Distinct forms represented in the current filter set.
     */
    public static function distinct_form_count(array $filters)
    {
        global $wpdb;
        $table = Schema::submission_page_table();

        list($where, $params) = self::base_where($filters);

        $sql = "SELECT COUNT(DISTINCT cf7_id) FROM {$table} WHERE {$where}";

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        return (int) $wpdb->get_var($wpdb->prepare($sql, $params));
    }

    /**
     * @param array<string,mixed> $filters
     * @return int Total detail rows matching the current filters (for pagination).
     */
    public static function detail_count(array $filters)
    {
        global $wpdb;
        $table = Schema::submission_page_table();

        list($where, $params) = self::detail_where($filters);

        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$where}";

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        return (int) $wpdb->get_var($wpdb->prepare($sql, $params));
    }

    /**
     * One page of drill-down rows, newest first.
     *
     * @param array<string,mixed> $filters
     * @param int                 $limit
     * @param int                 $offset
     * @return array<int,array<string,mixed>>
     */
    public static function detail_rows(array $filters, $limit, $offset)
    {
        global $wpdb;
        $table = Schema::submission_page_table();

        list($where, $params) = self::detail_where($filters);
        $params[] = max(1, (int) $limit);
        $params[] = max(0, (int) $offset);

        $sql = "SELECT data_id, cf7_id, page_id, page_path, page_title, submitted_at
                FROM {$table}
                WHERE {$where}
                ORDER BY submitted_at DESC
                LIMIT %d OFFSET %d";

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        return (array) $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A);
    }

    /**
     * Raw EAV rows for a known set of data_ids — the only query in this class
     * that touches the CF7 DB plugin's own table, and only for a small,
     * already-paginated set of IDs.
     *
     * @param int[] $data_ids
     * @return array<int,object>
     */
    public static function field_values(array $data_ids)
    {
        if (empty($data_ids)) {
            return array();
        }

        global $wpdb;
        $table  = Schema::cf7_entry_table();
        $params = array();
        $clause = self::in_clause('data_id', $data_ids, $params);

        $sql = "SELECT data_id, name, value FROM {$table} WHERE {$clause}";

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        return (array) $wpdb->get_results($wpdb->prepare($sql, $params));
    }

    /**
     * Pivot EAV rows into [data_id => [name => value]]. Reuses the CF7 DB
     * plugin's own vsz_cf7_sortdata() when available, so display formatting
     * (its `cf7d_entry_value` filter) stays consistent with its native
     * screens; falls back to an equivalent when the plugin is unavailable.
     *
     * @param array<int,object> $rows
     * @return array<int,array<string,string>>
     */
    public static function pivot_field_values(array $rows)
    {
        if (function_exists('vsz_cf7_sortdata')) {
            return vsz_cf7_sortdata($rows);
        }

        $pivoted = array();
        foreach ($rows as $row) {
            $data_id = (int) $row->data_id;
            if (!isset($pivoted[$data_id])) {
                $pivoted[$data_id] = array();
            }
            $pivoted[$data_id][(string) $row->name] = (string) $row->value;
        }

        return $pivoted;
    }

    /**
     * Pages actually present in the attribution table, for the report's page
     * multi-select — a short, meaningful list rather than every post on the site.
     *
     * @param int $limit
     * @return array<int,array<string,mixed>>
     */
    public static function known_pages($limit = 500)
    {
        global $wpdb;
        $table = Schema::submission_page_table();

        $sql = "SELECT page_id, page_path, post_type, COUNT(*) AS submissions
                FROM {$table}
                WHERE page_id > 0
                GROUP BY page_id, page_path, post_type
                ORDER BY submissions DESC
                LIMIT %d";

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        return (array) $wpdb->get_results($wpdb->prepare($sql, max(1, (int) $limit)), ARRAY_A);
    }

    /**
     * Distinct CF7 form IDs present in the attribution table.
     *
     * @return int[]
     */
    public static function known_forms()
    {
        global $wpdb;
        $table = Schema::submission_page_table();

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- table name only.
        $ids = $wpdb->get_col("SELECT DISTINCT cf7_id FROM {$table} WHERE cf7_id > 0 ORDER BY cf7_id ASC");

        return array_map('intval', (array) $ids);
    }

    /**
     * Cheap overall backfill progress (row counts only, no joins) — safe to
     * run on every load of the report screen since wp_cf7_vdata is small
     * (~12k rows) regardless of the missing indexes on its sibling entry table.
     *
     * @return array{total:int,processed:int}
     */
    public static function backfill_progress()
    {
        global $wpdb;
        $data_table = Schema::cf7_data_table();
        $our_table  = Schema::submission_page_table();

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- table names only.
        $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$data_table}");
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $processed = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$our_table}");

        return array('total' => $total, 'processed' => $processed);
    }

    /**
     * Independent cross-check query used only by the self-test's `=live`
     * mode and manual verification — bypasses our attribution table
     * entirely, querying the CF7 DB plugin's own `submit_time` pseudo-field
     * the way its native listing screen does. Requires the
     * `coptrz_name_value` index to be fast; deliberately not called from the
     * main report render.
     *
     * @param string $start_inclusive 'Y-m-d H:i:s'
     * @param string $end_inclusive   'Y-m-d H:i:s'
     * @return int
     */
    public static function true_submission_count_by_submit_time($start_inclusive, $end_inclusive)
    {
        global $wpdb;
        $table = Schema::cf7_entry_table();

        $sql = "SELECT COUNT(DISTINCT data_id) FROM {$table}
                WHERE name = 'submit_time' AND value >= %s AND value <= %s";

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        return (int) $wpdb->get_var($wpdb->prepare($sql, $start_inclusive, $end_inclusive));
    }

    /**
     * @param array<string,mixed> $filters
     * @return array{0:string,1:array<int,mixed>}
     */
    private static function base_where(array $filters)
    {
        $clauses = array('submitted_at >= %s', 'submitted_at < %s');
        $params  = array((string) $filters['start'], (string) $filters['end_exclusive']);

        if (!empty($filters['cf7_ids'])) {
            $clauses[] = self::in_clause('cf7_id', $filters['cf7_ids'], $params);
        }

        return array(implode(' AND ', $clauses), $params);
    }

    /**
     * Build the detail-view WHERE clause: date/form filters plus whichever
     * page buckets the caller opted into.
     *
     * Matched pages are ALWAYS included — restricted to the selected pages
     * when the page multi-select has a selection, otherwise every attributed
     * page — the same default the Summary view uses. The unmatched-URL and
     * no-page-data buckets are additive on top of that, never a replacement
     * for it: since both checkboxes default to checked on first load (no
     * page selected yet), treating the buckets as the *only* clause here
     * used to silently exclude every real, matched-page submission from the
     * Detail view and its export whenever no page was explicitly picked.
     *
     * @param array<string,mixed> $filters
     * @return array{0:string,1:array<int,mixed>}
     */
    private static function detail_where(array $filters)
    {
        list($where, $params) = self::base_where($filters);
        $clauses = array($where);

        $bucket_clauses = array();

        if (!empty($filters['page_ids'])) {
            $bucket_clauses[] = self::in_clause('page_id', $filters['page_ids'], $params);
        } else {
            $bucket_clauses[] = 'page_id > 0';
        }

        if (!empty($filters['include_unmatched'])) {
            $bucket_clauses[] = "(page_id = 0 AND page_path != '')";
        }
        if (!empty($filters['include_no_page_data'])) {
            $bucket_clauses[] = "(page_id = 0 AND page_path = '')";
        }

        $clauses[] = '(' . implode(' OR ', $bucket_clauses) . ')';

        return array(implode(' AND ', $clauses), $params);
    }

    /**
     * Build a `column IN (%d,%d,...)` clause and append its values to $params.
     *
     * @param string      $column
     * @param int[]       $values
     * @param array<int,mixed> $params Passed by reference; placeholders' values are appended.
     * @return string
     */
    private static function in_clause($column, array $values, array &$params)
    {
        $ints = array_map('intval', $values);
        $placeholders = implode(',', array_fill(0, count($ints), '%d'));

        foreach ($ints as $value) {
            $params[] = $value;
        }

        return "{$column} IN ({$placeholders})";
    }
}
