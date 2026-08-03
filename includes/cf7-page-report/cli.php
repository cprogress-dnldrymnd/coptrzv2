<?php
/**
 * Plugin/Snippet Name: CF7 Page Report — WP-CLI command
 * Description: `wp coptrz cf7-pages backfill` / `... status`. The preferred
 *              way to run the initial ~12,000-submission historic backfill —
 *              one command instead of ~25 browser round-trips through the
 *              admin AJAX batching UI (Admin.php's "Run backfill" button,
 *              which remains available for smaller top-ups and for admins
 *              without shell access).
 *
 *              Only loaded when WP_CLI is defined and true — see the
 *              conditional require in functions.php.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

namespace CoptrzTheme\CF7PageReport;

if (!defined('ABSPATH') || !defined('WP_CLI') || !WP_CLI) {
    return;
}

/**
 * `wp coptrz cf7-pages ...`
 */
class Cli
{
    public static function register()
    {
        \WP_CLI::add_command('coptrz cf7-pages backfill', array(__CLASS__, 'backfill'));
        \WP_CLI::add_command('coptrz cf7-pages status', array(__CLASS__, 'status'));
    }

    /**
     * Attribute every CF7 submission to a page.
     *
     * ## OPTIONS
     *
     * [--batch=<number>]
     * : Submissions processed per batch.
     * ---
     * default: 500
     * ---
     *
     * ## EXAMPLES
     *
     *     wp coptrz cf7-pages backfill
     *     wp coptrz cf7-pages backfill --batch=1000
     *
     * @param array<int,string>    $args
     * @param array<string,string> $assoc_args
     */
    public static function backfill($args, $assoc_args)
    {
        Schema::maybe_install();

        $batch_size = isset($assoc_args['batch']) ? max(1, (int) $assoc_args['batch']) : Backfill::DEFAULT_BATCH_SIZE;

        global $wpdb;
        $data_table = Schema::cf7_data_table();
        $total      = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$data_table}"); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- table name only.
        $min_id     = (int) $wpdb->get_var("SELECT MIN(id) FROM {$data_table}"); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $cursor     = max(0, $min_id - 1);

        \WP_CLI::log(sprintf('Attributing up to %d submission(s) in batches of %d...', $total, $batch_size));

        $progress  = \WP_CLI\Utils\make_progress_bar('Backfilling', $total);
        $processed = 0;
        $result    = array('done' => false);

        do {
            $result     = Backfill::process_batch($cursor, $batch_size);
            $cursor     = $result['cursor'];
            $processed += $result['processed'];
            $progress->tick($result['processed']);
        } while (!$result['done']);

        $progress->finish();

        \WP_CLI::success(sprintf('Attributed %d submission(s).', $processed));
    }

    /**
     * Show attribution coverage across the whole submission history.
     *
     * ## EXAMPLES
     *
     *     wp coptrz cf7-pages status
     */
    public static function status()
    {
        $progress = Repository::backfill_progress();

        \WP_CLI::log(sprintf('Submissions: %d total, %d attributed row(s) written.', $progress['total'], $progress['processed']));

        $coverage = Repository::coverage(
            array(
                'start'         => '1970-01-01 00:00:00',
                'end_exclusive' => gmdate('Y-m-d H:i:s', time() + DAY_IN_SECONDS),
            )
        );

        \WP_CLI::log(
            sprintf(
                'Of attributed rows: %d resolved to a page, %d unmatched URL, %d with no page data.',
                $coverage['attributed'],
                $coverage['unmatched_url'],
                $coverage['no_page_data']
            )
        );
    }
}

Cli::register();
