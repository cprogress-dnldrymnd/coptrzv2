<?php
/**
 * Plugin/Snippet Name: CF7 Page Report — Schema
 * Description: Creates and version-manages the two tables this feature owns
 *              (coptrz_cf7_submission_page, coptrz_cf7_page_map), and installs
 *              extra indexes on the Advanced CF7 DB plugin's own
 *              wp_cf7_vdata_entry table — that table has no index beyond its
 *              own PRIMARY KEY, so every report query and the historic
 *              backfill scan would otherwise be a full ~209k-row scan. Both
 *              run automatically from maybe_install() (no admin-facing
 *              button, by design) — install_indexes() only ADDs indexes
 *              (never drops/alters existing columns), and
 *              create_table_cf7_vdata_entry() in the plugin's own activator
 *              only runs dbDelta() when the table doesn't exist yet, so there
 *              is no conflict with the plugin's own upgrade path.
 *
 *              This theme has never created a custom DB table before (there is
 *              zero dbDelta()/get_charset_collate()/after_switch_theme anywhere
 *              else in the codebase) — the option-versioned installer pattern
 *              here (coptrz_cf7_page_report_db_version) is new ground for this
 *              theme, modelled on the plugin-ecosystem activator convention
 *              rather than any existing theme code. See CF7PageReport's plan
 *              doc for why: a theme has no activation hook to install schema
 *              from, so `admin_init` + `after_switch_theme` stand in for one.
 *
 *              We never touch wp_cf7_vdata / wp_cf7_vdata_entry beyond the
 *              index DDL below — no writes, ever — so this feature degrades
 *              gracefully if Advanced CF7 DB is updated or deactivated.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

namespace CoptrzTheme\CF7PageReport;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Table creation, versioning, and the guarded CF7 DB index installer.
 */
class Schema
{
    /** Bump to re-run dbDelta() (e.g. after adding a column). */
    const DB_VERSION = '1.0.0';

    const DB_VERSION_OPTION = 'coptrz_cf7_page_report_db_version';

    const INDEX_VERSION_OPTION = 'coptrz_cf7_page_report_index_version';

    /** Current index-set version; bump when the index list below changes. */
    const INDEX_VERSION = 1;

    /**
     * Indexes added to the CF7 DB plugin's entry table, keyed by index name.
     * Prefix lengths are deliberate: `name` is varchar(250) and `value` is
     * TEXT, so full-length composite indexes would approach the 3072-byte
     * limit on utf8mb4 — 64/32/20 chars keep each index small while still
     * covering the equality/range comparisons the report and backfill run.
     *
     * @var array<string,string>
     */
    const CF7_ENTRY_INDEXES = array(
        'coptrz_data_id'    => '(`data_id`)',
        'coptrz_name_data'  => '(`name`(64), `data_id`)',
        'coptrz_cf7_name'   => '(`cf7_id`, `name`(64))',
        // Speeds the plugin's OWN existing per-form date filter
        // (name='submit_time' AND value BETWEEN ...); 20 chars covers
        // 'YYYY-MM-DD HH:MM:SS' exactly.
        'coptrz_name_value' => '(`name`(32), `value`(20))',
    );

    /**
     * Wire the install checks. Called once from functions.php at load time.
     */
    public static function init()
    {
        add_action('admin_init', array(__CLASS__, 'maybe_install'));
        add_action('after_switch_theme', array(__CLASS__, 'maybe_install'));
    }

    /**
     * Create/upgrade our own tables if the stored version doesn't match, and
     * install the CF7 DB reporting indexes if they aren't present yet. Safe
     * to call on every admin_init — both option checks short-circuit after
     * their first successful run, so this is normally two cheap get_option()
     * calls and nothing else.
     */
    public static function maybe_install()
    {
        if (get_option(self::DB_VERSION_OPTION) !== self::DB_VERSION) {
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            dbDelta(self::submission_page_sql($charset_collate));
            dbDelta(self::page_map_sql($charset_collate));

            update_option(self::DB_VERSION_OPTION, self::DB_VERSION, false);
        }

        if (!self::indexes_installed()) {
            self::install_indexes();
        }
    }

    /**
     * @return string Table name for the per-submission page attribution table.
     */
    public static function submission_page_table()
    {
        global $wpdb;
        return $wpdb->prefix . 'coptrz_cf7_submission_page';
    }

    /**
     * @return string Table name for the path -> post ID resolution cache.
     */
    public static function page_map_table()
    {
        global $wpdb;
        return $wpdb->prefix . 'coptrz_cf7_page_map';
    }

    /**
     * Table name for the Advanced CF7 DB plugin's per-field EAV table.
     * Prefers the plugin's own constant (defined in advanced-cf7-db.php) so a
     * future rename can't silently drift us apart from it.
     *
     * @return string
     */
    public static function cf7_entry_table()
    {
        global $wpdb;
        return defined('VSZ_CF7_DATA_ENTRY_TABLE_NAME') ? VSZ_CF7_DATA_ENTRY_TABLE_NAME : $wpdb->prefix . 'cf7_vdata_entry';
    }

    /**
     * Table name for the Advanced CF7 DB plugin's one-row-per-submission table.
     *
     * @return string
     */
    public static function cf7_data_table()
    {
        global $wpdb;
        return defined('VSZ_CF7_DATA_TABLE_NAME') ? VSZ_CF7_DATA_TABLE_NAME : $wpdb->prefix . 'cf7_vdata';
    }

    /**
     * Whether the CF7 DB plugin's entry table currently exists.
     *
     * @return bool
     */
    public static function cf7_entry_table_exists()
    {
        global $wpdb;
        $table = self::cf7_entry_table();
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- LIKE pattern is prepared below.
        $found = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table));
        return $found === $table;
    }

    /**
     * @return string Storage engine of the CF7 DB entry table, or '' if unknown.
     */
    public static function cf7_entry_table_engine()
    {
        global $wpdb;
        $table = self::cf7_entry_table();
        $engine = $wpdb->get_var(
            $wpdb->prepare(
                'SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s',
                DB_NAME,
                $table
            )
        );
        return (string) $engine;
    }

    /**
     * Rough row-count estimate for the "how big is this ALTER" admin preview.
     * TABLE_ROWS is an estimate for InnoDB, not an exact count — adequate here.
     *
     * @return int
     */
    public static function cf7_entry_row_count_estimate()
    {
        global $wpdb;
        $table = self::cf7_entry_table();
        return (int) $wpdb->get_var(
            $wpdb->prepare(
                'SELECT TABLE_ROWS FROM information_schema.TABLES WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s',
                DB_NAME,
                $table
            )
        );
    }

    /**
     * @return string[] Index names currently present on the CF7 DB entry table.
     */
    public static function existing_cf7_entry_indexes()
    {
        global $wpdb;
        $table = self::cf7_entry_table();
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- table name is not user input.
        $rows = $wpdb->get_results("SHOW INDEX FROM `{$table}`", ARRAY_A);

        $names = array();
        foreach ((array) $rows as $row) {
            if (!empty($row['Key_name'])) {
                $names[$row['Key_name']] = true;
            }
        }

        return array_keys($names);
    }

    /**
     * @return bool Whether the current reporting index set has been installed.
     */
    public static function indexes_installed()
    {
        return (int) get_option(self::INDEX_VERSION_OPTION, 0) >= self::INDEX_VERSION;
    }

    /**
     * The exact ALTER TABLE statement install_indexes() will run, for display
     * on the admin confirmation screen before the admin commits to it.
     *
     * @return string
     */
    public static function preview_sql()
    {
        $table   = self::cf7_entry_table();
        $clauses = array();
        foreach (self::CF7_ENTRY_INDEXES as $name => $definition) {
            $clauses[] = "ADD INDEX `{$name}` {$definition}";
        }

        return "ALTER TABLE `{$table}` " . implode(', ', $clauses) . ', ALGORITHM=INPLACE, LOCK=NONE;';
    }

    /**
     * Documented rollback for the admin to keep, shown alongside preview_sql().
     *
     * @return string
     */
    public static function rollback_sql()
    {
        $table = self::cf7_entry_table();
        $drops = array();
        foreach (array_keys(self::CF7_ENTRY_INDEXES) as $name) {
            $drops[] = "DROP INDEX `{$name}` ON `{$table}`";
        }

        return implode(";\n", $drops) . ';';
    }

    /**
     * Add the reporting indexes to the CF7 DB plugin's entry table. Called
     * automatically from maybe_install() once indexes_installed() is false —
     * no admin-facing button, by design. This only ever ADDs indexes (never
     * drops or alters existing columns/data), and
     * `create_table_cf7_vdata_entry()` in the CF7 DB plugin's own activator
     * only runs dbDelta() when the table doesn't exist yet, so it never
     * touches an existing table — there is no conflict risk from running
     * this independently of the plugin's own upgrade path.
     *
     * preview_sql()/rollback_sql() remain available for anyone who wants to
     * inspect or reverse this by hand (e.g. via `wp eval`), even though nothing
     * in the admin UI surfaces them.
     *
     * @return array{ok:bool,message:string}
     */
    public static function install_indexes()
    {
        global $wpdb;

        if (!self::cf7_entry_table_exists()) {
            return array(
                'ok'      => false,
                'message' => __('The Advanced CF7 DB plugin\'s data table was not found — is the plugin active?', 'coptrz-theme'),
            );
        }

        $engine = self::cf7_entry_table_engine();
        if ('' !== $engine && 'InnoDB' !== $engine && 'MyISAM' !== $engine) {
            return array(
                'ok'      => false,
                /* translators: %s: database storage engine name */
                'message' => sprintf(__('Unexpected storage engine (%s) — refusing to alter the table automatically.', 'coptrz-theme'), $engine),
            );
        }

        $table    = self::cf7_entry_table();
        $existing = self::existing_cf7_entry_indexes();
        $to_add   = array();

        foreach (self::CF7_ENTRY_INDEXES as $name => $definition) {
            if (!in_array($name, $existing, true)) {
                $to_add[$name] = $definition;
            }
        }

        if (empty($to_add)) {
            update_option(self::INDEX_VERSION_OPTION, self::INDEX_VERSION, false);
            return array('ok' => true, 'message' => __('All reporting indexes are already present.', 'coptrz-theme'));
        }

        $clauses = array();
        foreach ($to_add as $name => $definition) {
            $clauses[] = "ADD INDEX `{$name}` {$definition}";
        }

        $sql = "ALTER TABLE `{$table}` " . implode(', ', $clauses) . ', ALGORITHM=INPLACE, LOCK=NONE';

        $wpdb->hide_errors();
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- DDL statement, no user input.
        $result = $wpdb->query($sql);

        if (false === $result) {
            // Older MySQL/MariaDB may reject the ALGORITHM/LOCK clauses — retry without them.
            $sql_fallback = "ALTER TABLE `{$table}` " . implode(', ', $clauses);
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            $result = $wpdb->query($sql_fallback);
        }
        $wpdb->show_errors();

        if (false === $result) {
            return array(
                'ok'      => false,
                /* translators: %s: database error message */
                'message' => sprintf(__('Index creation failed: %s', 'coptrz-theme'), $wpdb->last_error),
            );
        }

        update_option(self::INDEX_VERSION_OPTION, self::INDEX_VERSION, false);

        return array(
            'ok'      => true,
            /* translators: %d: number of indexes added */
            'message' => sprintf(__('Added %d index(es) for reporting.', 'coptrz-theme'), count($to_add)),
        );
    }

    /**
     * ON DUPLICATE KEY UPDATE clause shared by Attribution::store() and
     * Backfill's multi-row upsert, so both write paths guard the same way:
     * a forward-captured 'container_post' row (the live request's exact
     * page ID) is never downgraded by a later, less certain URL-derived
     * backfill or reconcile pass. Keep both call sites' column order (and
     * this clause) in sync if the table gains a column.
     *
     * @return string
     */
    public static function submission_page_upsert_clause()
    {
        return "ON DUPLICATE KEY UPDATE
            cf7_id = VALUES(cf7_id),
            page_id = IF(source = 'container_post', page_id, VALUES(page_id)),
            container_post_id = VALUES(container_post_id),
            post_type = IF(source = 'container_post', post_type, VALUES(post_type)),
            page_url_raw = VALUES(page_url_raw),
            page_path = IF(source = 'container_post', page_path, VALUES(page_path)),
            page_query = VALUES(page_query),
            page_title = VALUES(page_title),
            source = IF(source = 'container_post', source, VALUES(source)),
            confidence = IF(source = 'container_post', confidence, VALUES(confidence)),
            submitted_at = VALUES(submitted_at),
            submitted_at_gmt = VALUES(submitted_at_gmt),
            resolver_version = VALUES(resolver_version),
            resolved_at = VALUES(resolved_at)";
    }

    /**
     * @param string $charset_collate Result of $wpdb->get_charset_collate().
     * @return string
     */
    private static function submission_page_sql($charset_collate)
    {
        $table = self::submission_page_table();

        return "CREATE TABLE {$table} (
            data_id BIGINT(20) UNSIGNED NOT NULL,
            cf7_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            page_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            container_post_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            post_type VARCHAR(50) NOT NULL DEFAULT '',
            page_url_raw TEXT NULL,
            page_path VARCHAR(191) NOT NULL DEFAULT '',
            page_query VARCHAR(191) NOT NULL DEFAULT '',
            page_title TEXT NULL,
            source VARCHAR(24) NOT NULL DEFAULT '',
            confidence VARCHAR(10) NOT NULL DEFAULT 'none',
            submitted_at DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
            submitted_at_gmt DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
            resolver_version SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            resolved_at DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
            PRIMARY KEY  (data_id),
            KEY page_submitted (page_id, submitted_at),
            KEY submitted_at (submitted_at),
            KEY cf7_submitted (cf7_id, submitted_at),
            KEY page_path (page_path),
            KEY resolver_version (resolver_version)
        ) {$charset_collate};";
    }

    /**
     * @param string $charset_collate Result of $wpdb->get_charset_collate().
     * @return string
     */
    private static function page_map_sql($charset_collate)
    {
        $table = self::page_map_table();

        return "CREATE TABLE {$table} (
            path_hash CHAR(32) NOT NULL,
            page_path VARCHAR(191) NOT NULL DEFAULT '',
            page_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            post_type VARCHAR(50) NOT NULL DEFAULT '',
            method VARCHAR(32) NOT NULL DEFAULT '',
            resolver_version SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            resolved_at DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
            PRIMARY KEY  (path_hash),
            KEY page_id (page_id)
        ) {$charset_collate};";
    }
}

Schema::init();
