<?php
/**
 * Plugin/Snippet Name: CF7 Page Report — Admin Screen
 * Description: The "Report by Page" admin screen: select one or more pages
 *              and a date range, see submission counts across whatever forms
 *              live on those pages, drill into individual submissions, and
 *              export. Registered as a submenu of Advanced CF7 DB's own
 *              "Advanced CF7 DB" menu (contact-form-listing) so it sits next
 *              to the plugin's existing per-form reports, falling back to a
 *              Tools page if that parent menu isn't present (e.g. the plugin
 *              deactivated).
 *
 *              Markup follows this theme's existing admin-screen convention
 *              (coptrz_render_openai_ads_conversions_page(), hooks.php) —
 *              core `wrap`/`widefat striped`/`notice` classes with inline
 *              styles, no new CSS file; capability re-checked in the render
 *              callback even though the menu registration already gates it.
 *
 *              Deliberately NOT on this screen: a "paste titles/URLs" box, a
 *              manual "run backfill" button, and a manual "optimise database"
 *              button. Historic attribution and the CF7 DB entry-table
 *              indexes are both applied automatically (Schema::maybe_install(),
 *              Backfill::reconcile() on its hourly cron) — see Schema.php and
 *              Backfill.php. `wp coptrz cf7-pages backfill` remains the way
 *              to force an immediate full pass (cli.php).
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

namespace CoptrzTheme\CF7PageReport;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin screen: menu, filters, render, export routing.
 */
class Admin
{
    const PAGE_SLUG = 'coptrz-cf7-page-report';

    const RESULTS_PER_PAGE = 50;

    const EXPORT_NONCE = 'coptrz_cf7_page_report_export';

    public static function init()
    {
        add_action('admin_menu', array(__CLASS__, 'register_menu'), 20);
        add_action('admin_init', array(__CLASS__, 'handle_export'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));
    }

    /**
     * Screen-gated assets — only ever loaded on this report's own page,
     * regardless of which parent menu it ended up under (see register_menu()).
     *
     * The Pages/Forms multi-selects are enhanced with WooCommerce's own
     * bundled selectWoo (its Select2 fork, handle `wc-enhanced-select`) —
     * WooCommerce is already active on this site, so this reuses existing
     * infrastructure instead of vendoring a new JS library. Degrades to a
     * plain multi-select if WooCommerce is ever deactivated (the handles
     * simply won't be registered, so the enqueue is a harmless no-op).
     */
    public static function enqueue_assets()
    {
        if (!self::is_report_page()) {
            return;
        }

        $path = get_template_directory() . '/assets/css/cf7-page-report.css';
        $ver  = file_exists($path) ? (string) filemtime($path) : (defined('coptz_version') ? (string) coptz_version : '1.0');

        wp_enqueue_style(
            'coptrz-cf7-page-report',
            get_template_directory_uri() . '/assets/css/cf7-page-report.css',
            array(),
            $ver
        );

        wp_enqueue_style('woocommerce_admin_styles');
        wp_enqueue_script('wc-enhanced-select');
    }

    /**
     * @return string
     */
    public static function capability()
    {
        return (string) apply_filters('coptrz_cf7_page_report_capability', 'manage_options');
    }

    public static function register_menu()
    {
        global $submenu;

        if (isset($submenu['contact-form-listing'])) {
            add_submenu_page(
                'contact-form-listing',
                __('Report by Page', 'coptrz-theme'),
                __('Report by Page', 'coptrz-theme'),
                self::capability(),
                self::PAGE_SLUG,
                array(__CLASS__, 'render_page')
            );

            return;
        }

        // Advanced CF7 DB isn't active/registered — fall back to Tools so the
        // screen is never orphaned, matching where this theme's other
        // self-contained admin screens already live (Convert to Blocks,
        // OpenAI Ads Conversions, WPML Eraser).
        add_management_page(
            __('CF7 Report by Page', 'coptrz-theme'),
            __('CF7 Report by Page', 'coptrz-theme'),
            self::capability(),
            self::PAGE_SLUG,
            array(__CLASS__, 'render_page')
        );
    }

    /**
     * Route CSV/XLSX export requests.
     */
    public static function handle_export()
    {
        if (!self::is_report_page()) {
            return;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- nonce verified below before any output.
        $export = isset($_GET['coptrz_cf7_page_export']) ? sanitize_key(wp_unslash($_GET['coptrz_cf7_page_export'])) : '';
        if ('' === $export) {
            return;
        }

        if (!current_user_can(self::capability())) {
            wp_die(esc_html__('You do not have permission to export this data.', 'coptrz-theme'));
        }

        $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
        if (!wp_verify_nonce($nonce, self::EXPORT_NONCE)) {
            wp_die(esc_html__('Invalid export request — please reload the report and try again.', 'coptrz-theme'));
        }

        $filters = self::get_filters();
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only, nonce already verified above.
        $scope = (isset($_GET['scope']) && 'detail' === $_GET['scope']) ? 'detail' : 'summary';

        if ('detail' === $scope && 'xlsx' === $export) {
            Exporter::export_detail_xlsx($filters);
            return;
        }

        if ('detail' === $scope) {
            Exporter::export_detail_csv($filters);
            return;
        }

        Exporter::export_summary_csv($filters);
    }

    /**
     * Render the report screen.
     */
    public static function render_page()
    {
        if (!current_user_can(self::capability())) {
            wp_die(esc_html__('You do not have permission to access this page.', 'coptrz-theme'));
        }

        Schema::maybe_install();

        $filters = self::get_filters();
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only view state.
        $view = (isset($_GET['view']) && 'detail' === $_GET['view']) ? 'detail' : 'summary';

        $coverage = Repository::coverage($filters);
        $matched           = Repository::matched_summary($filters);
        $unmatched         = !empty($filters['include_unmatched']) ? Repository::unmatched_summary($filters) : array();
        $no_page_data      = !empty($filters['include_no_page_data']) ? Repository::no_page_data_count($filters) : 0;

        $distinct_forms = Repository::distinct_form_count($filters);
        $show_pivot     = ($distinct_forms > 0 && $distinct_forms <= 15);
        $pivot          = $show_pivot ? Repository::page_form_pivot($filters) : array();
        $pivot_forms    = array();
        if ($show_pivot) {
            foreach ($pivot as $pivot_row) {
                $pivot_forms[(int) $pivot_row['cf7_id']] = true;
            }
        }

        $per_page = self::RESULTS_PER_PAGE;
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $paged = max(1, isset($_GET['paged']) ? (int) $_GET['paged'] : 1);

        $detail_total   = 0;
        $detail_rows    = array();
        $detail_pivoted = array();

        if ('detail' === $view) {
            $detail_total = Repository::detail_count($filters);
            $detail_rows  = Repository::detail_rows($filters, $per_page, ($paged - 1) * $per_page);

            $data_ids = array_map(
                static function ($row) {
                    return (int) $row['data_id'];
                },
                $detail_rows
            );
            $detail_pivoted = Repository::pivot_field_values(Repository::field_values($data_ids));
        }

        $known_pages = Repository::known_pages();
        $known_forms = self::known_forms_with_titles();

        self::render_html(
            $filters,
            $view,
            $coverage,
            $matched,
            $unmatched,
            $no_page_data,
            $show_pivot,
            $pivot,
            $pivot_forms,
            $known_forms,
            $known_pages,
            $per_page,
            $paged,
            $detail_total,
            $detail_rows,
            $detail_pivoted
        );
    }

    /**
     * @param array<string,mixed>            $filters
     * @param string                         $view
     * @param array{total:int,attributed:int,unmatched_url:int,no_page_data:int} $coverage
     * @param array<int,array<string,mixed>> $matched
     * @param array<int,array<string,mixed>> $unmatched
     * @param int                             $no_page_data
     * @param bool                            $show_pivot
     * @param array<int,array<string,mixed>> $pivot
     * @param array<int,bool>                $pivot_forms
     * @param array<int,string>               $known_forms
     * @param array<int,array<string,mixed>> $known_pages
     * @param int                             $per_page
     * @param int                             $paged
     * @param int                             $detail_total
     * @param array<int,array<string,mixed>> $detail_rows
     * @param array<int,array<string,string>> $detail_pivoted
     */
    private static function render_html(
        array $filters,
        $view,
        array $coverage,
        array $matched,
        array $unmatched,
        $no_page_data,
        $show_pivot,
        array $pivot,
        array $pivot_forms,
        array $known_forms,
        array $known_pages,
        $per_page,
        $paged,
        $detail_total,
        array $detail_rows,
        array $detail_pivoted
    ) {
        $summary_url = add_query_arg(array_merge(self::current_view_args(), array('view' => 'summary')), menu_page_url(self::PAGE_SLUG, false));
        $detail_url  = add_query_arg(array_merge(self::current_view_args(), array('view' => 'detail', 'paged' => 1)), menu_page_url(self::PAGE_SLUG, false));

        $grand_total  = $coverage['total'];
        $coverage_pct = $grand_total > 0 ? round(($coverage['attributed'] / $grand_total) * 100, 1) : 0;
        ?>
        <div class="wrap coptrz-cf7-report">
            <h1><?php esc_html_e('Report by Page', 'coptrz-theme'); ?></h1>
            <p class="description">
                <?php esc_html_e('Select one or more pages and a date range to see Contact Form 7 submissions across every form on those pages, with CSV/Excel export.', 'coptrz-theme'); ?>
            </p>

            <div class="coptrz-cf7-card">
                <h2><?php esc_html_e('Filters', 'coptrz-theme'); ?></h2>
                <form method="get">
                    <input type="hidden" name="page" value="<?php echo esc_attr(self::current_page_slug()); ?>">
                    <input type="hidden" name="view" value="<?php echo esc_attr($view); ?>">
                    <input type="hidden" name="filtered" value="1">
                    <div class="coptrz-cf7-filters-grid">
                        <div class="coptrz-cf7-field">
                            <label for="coptrz-cf7-start"><?php esc_html_e('From', 'coptrz-theme'); ?></label>
                            <input type="date" id="coptrz-cf7-start" name="start" value="<?php echo esc_attr($filters['start_display']); ?>">
                        </div>
                        <div class="coptrz-cf7-field">
                            <label for="coptrz-cf7-end"><?php esc_html_e('To', 'coptrz-theme'); ?></label>
                            <input type="date" id="coptrz-cf7-end" name="end" value="<?php echo esc_attr($filters['end_display']); ?>">
                        </div>

                        <div class="coptrz-cf7-field coptrz-cf7-field--wide">
                            <label for="coptrz-cf7-pages"><?php esc_html_e('Pages', 'coptrz-theme'); ?></label>
                            <select id="coptrz-cf7-pages" name="page_ids[]" multiple class="wc-enhanced-select" style="width:100%;" data-placeholder="<?php esc_attr_e('Search pages…', 'coptrz-theme'); ?>">
                                <?php foreach ($known_pages as $page_row) : ?>
                                    <?php
                                    $page_id  = (int) $page_row['page_id'];
                                    $selected = in_array($page_id, $filters['page_ids'], true);
                                    $title    = get_the_title($page_id);
                                    $title    = '' !== $title ? $title : ('#' . $page_id);
                                    ?>
                                    <option value="<?php echo esc_attr($page_id); ?>" <?php selected($selected); ?>>
                                        <?php echo esc_html($title . ' — ' . $page_row['page_path'] . ' (' . $page_row['submissions'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if (!empty($known_forms)) : ?>
                        <div class="coptrz-cf7-field coptrz-cf7-field--wide">
                            <label for="coptrz-cf7-forms"><?php esc_html_e('Forms (optional)', 'coptrz-theme'); ?></label>
                            <select id="coptrz-cf7-forms" name="cf7_ids[]" multiple class="wc-enhanced-select" style="width:100%;" data-placeholder="<?php esc_attr_e('All forms', 'coptrz-theme'); ?>">
                                <?php foreach ($known_forms as $form_id => $form_title) : ?>
                                    <option value="<?php echo esc_attr($form_id); ?>" <?php selected(in_array((int) $form_id, $filters['cf7_ids'], true)); ?>>
                                        <?php echo esc_html($form_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="coptrz-cf7-field">
                            <span class="coptrz-cf7-field-hint" style="font-weight:600;color:#1d2327;"><?php esc_html_e('Include', 'coptrz-theme'); ?></span>
                            <div class="coptrz-cf7-checkbox-list">
                                <label><input type="checkbox" name="include_unmatched" value="1" <?php checked(!empty($filters['include_unmatched'])); ?>> <?php esc_html_e('Unmatched URLs bucket', 'coptrz-theme'); ?></label>
                                <label><input type="checkbox" name="include_no_page_data" value="1" <?php checked(!empty($filters['include_no_page_data'])); ?>> <?php esc_html_e('No-page-data bucket', 'coptrz-theme'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="coptrz-cf7-filters-actions">
                        <a class="button" href="<?php echo esc_url(menu_page_url(self::PAGE_SLUG, false)); ?>"><?php esc_html_e('Reset', 'coptrz-theme'); ?></a>
                        <button type="submit" class="button button-primary"><?php esc_html_e('Apply filters', 'coptrz-theme'); ?></button>
                    </div>
                </form>
            </div>

            <div class="coptrz-cf7-stats">
                <div class="coptrz-cf7-stat coptrz-cf7-stat--good">
                    <span class="coptrz-cf7-stat-num"><?php echo esc_html(number_format_i18n($coverage['attributed'])); ?></span>
                    <span class="coptrz-cf7-stat-label"><?php esc_html_e('Attributed to a page', 'coptrz-theme'); ?></span>
                </div>
                <div class="coptrz-cf7-stat coptrz-cf7-stat--warn">
                    <span class="coptrz-cf7-stat-num"><?php echo esc_html(number_format_i18n($coverage['unmatched_url'])); ?></span>
                    <span class="coptrz-cf7-stat-label"><?php esc_html_e('Unmatched URL', 'coptrz-theme'); ?></span>
                </div>
                <div class="coptrz-cf7-stat">
                    <span class="coptrz-cf7-stat-num"><?php echo esc_html(number_format_i18n($coverage['no_page_data'])); ?></span>
                    <span class="coptrz-cf7-stat-label"><?php esc_html_e('No page data', 'coptrz-theme'); ?></span>
                </div>
                <div class="coptrz-cf7-stat coptrz-cf7-stat--total">
                    <span class="coptrz-cf7-stat-num"><?php echo esc_html(number_format_i18n($grand_total)); ?> <small>(<?php echo esc_html((string) $coverage_pct); ?>%)</small></span>
                    <span class="coptrz-cf7-stat-label"><?php esc_html_e('Total in range', 'coptrz-theme'); ?></span>
                </div>
            </div>

            <h2 class="nav-tab-wrapper coptrz-cf7-tabs">
                <a href="<?php echo esc_url($summary_url); ?>" class="nav-tab <?php echo 'summary' === $view ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Summary', 'coptrz-theme'); ?></a>
                <a href="<?php echo esc_url($detail_url); ?>" class="nav-tab <?php echo 'detail' === $view ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Detail', 'coptrz-theme'); ?></a>
            </h2>

            <div class="coptrz-cf7-panel">
                <?php if ('summary' === $view) : ?>
                    <?php self::render_summary($filters, $matched, $unmatched, $no_page_data, $grand_total, $show_pivot, $pivot, $pivot_forms, $known_forms); ?>
                <?php else : ?>
                    <?php self::render_detail($filters, $detail_rows, $detail_pivoted, $detail_total, $per_page, $paged, $known_forms); ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * @param array<string,mixed>            $filters
     * @param array<int,array<string,mixed>> $matched
     * @param array<int,array<string,mixed>> $unmatched
     * @param int                             $no_page_data
     * @param int                             $grand_total
     * @param bool                            $show_pivot
     * @param array<int,array<string,mixed>> $pivot
     * @param array<int,bool>                 $pivot_forms
     * @param array<int,string>               $known_forms
     */
    private static function render_summary(array $filters, array $matched, array $unmatched, $no_page_data, $grand_total, $show_pivot, array $pivot, array $pivot_forms, array $known_forms)
    {
        $export_summary_url = wp_nonce_url(
            add_query_arg(array_merge(self::current_view_args(), array('coptrz_cf7_page_export' => 'csv', 'scope' => 'summary')), menu_page_url(self::PAGE_SLUG, false)),
            self::EXPORT_NONCE
        );
        ?>
        <div class="coptrz-cf7-panel-toolbar">
            <a class="button" href="<?php echo esc_url($export_summary_url); ?>">
                <span class="dashicons dashicons-media-spreadsheet"></span><?php esc_html_e('Export CSV', 'coptrz-theme'); ?>
            </a>
        </div>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Page', 'coptrz-theme'); ?></th>
                    <th><?php esc_html_e('Path', 'coptrz-theme'); ?></th>
                    <th><?php esc_html_e('Type', 'coptrz-theme'); ?></th>
                    <th><?php esc_html_e('Submissions', 'coptrz-theme'); ?></th>
                    <th><?php esc_html_e('Forms', 'coptrz-theme'); ?></th>
                    <th><?php esc_html_e('First', 'coptrz-theme'); ?></th>
                    <th><?php esc_html_e('Last', 'coptrz-theme'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($matched) && empty($unmatched) && $no_page_data <= 0) : ?>
                    <tr><td colspan="7"><?php esc_html_e('No submissions in range.', 'coptrz-theme'); ?></td></tr>
                <?php endif; ?>
                <?php foreach ($matched as $row) : ?>
                    <?php $title = get_the_title((int) $row['page_id']); ?>
                    <tr>
                        <td><a href="<?php echo esc_url((string) get_edit_post_link((int) $row['page_id'])); ?>"><?php echo esc_html('' !== $title ? $title : ('#' . $row['page_id'])); ?></a></td>
                        <td><?php echo esc_html($row['page_path']); ?></td>
                        <td><?php echo esc_html($row['post_type']); ?></td>
                        <td><?php echo (int) $row['submissions']; ?></td>
                        <td><?php echo (int) $row['form_count']; ?></td>
                        <td><?php echo esc_html($row['first_at']); ?></td>
                        <td><?php echo esc_html($row['last_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php foreach ($unmatched as $row) : ?>
                    <tr>
                        <td><em><?php esc_html_e('(unmatched URL)', 'coptrz-theme'); ?></em></td>
                        <td><?php echo esc_html($row['page_path']); ?></td>
                        <td>—</td>
                        <td><?php echo (int) $row['submissions']; ?></td>
                        <td><?php echo (int) $row['form_count']; ?></td>
                        <td><?php echo esc_html($row['first_at']); ?></td>
                        <td><?php echo esc_html($row['last_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($no_page_data > 0) : ?>
                    <tr>
                        <td><em><?php esc_html_e('(no page data)', 'coptrz-theme'); ?></em></td>
                        <td>—</td>
                        <td>—</td>
                        <td><?php echo (int) $no_page_data; ?></td>
                        <td>—</td>
                        <td>—</td>
                        <td>—</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3"><?php esc_html_e('Grand total', 'coptrz-theme'); ?></th>
                    <th><?php echo (int) $grand_total; ?></th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>
        </table>

        <?php if ($show_pivot) : ?>
            <h2><?php esc_html_e('Page x Form', 'coptrz-theme'); ?></h2>
            <?php
            $by_page = array();
            foreach ($pivot as $row) {
                $by_page[(int) $row['page_id']][(int) $row['cf7_id']] = (int) $row['submissions'];
            }
            $form_ids = array_keys($pivot_forms);
            ?>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Page', 'coptrz-theme'); ?></th>
                        <?php foreach ($form_ids as $form_id) : ?>
                            <th><?php echo esc_html($known_forms[$form_id] ?? ('#' . $form_id)); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($by_page as $page_id => $counts) : ?>
                        <?php $title = get_the_title($page_id); ?>
                        <tr>
                            <td><?php echo esc_html('' !== $title ? $title : ('#' . $page_id)); ?></td>
                            <?php foreach ($form_ids as $form_id) : ?>
                                <td><?php echo isset($counts[$form_id]) ? (int) $counts[$form_id] : ''; ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <?php
    }

    /**
     * @param array<string,mixed>             $filters
     * @param array<int,array<string,mixed>> $detail_rows
     * @param array<int,array<string,string>> $detail_pivoted
     * @param int                              $detail_total
     * @param int                              $per_page
     * @param int                              $paged
     * @param array<int,string>                $known_forms
     */
    private static function render_detail(array $filters, array $detail_rows, array $detail_pivoted, $detail_total, $per_page, $paged, array $known_forms)
    {
        $export_csv_url = wp_nonce_url(
            add_query_arg(array_merge(self::current_view_args(), array('coptrz_cf7_page_export' => 'csv', 'scope' => 'detail')), menu_page_url(self::PAGE_SLUG, false)),
            self::EXPORT_NONCE
        );
        $export_xlsx_url = wp_nonce_url(
            add_query_arg(array_merge(self::current_view_args(), array('coptrz_cf7_page_export' => 'xlsx', 'scope' => 'detail')), menu_page_url(self::PAGE_SLUG, false)),
            self::EXPORT_NONCE
        );
        $total_pages = max(1, (int) ceil($detail_total / $per_page));
        ?>
        <div class="coptrz-cf7-panel-toolbar">
            <a class="button" href="<?php echo esc_url($export_csv_url); ?>">
                <span class="dashicons dashicons-media-text"></span><?php esc_html_e('Export CSV', 'coptrz-theme'); ?>
            </a>
            <a class="button" href="<?php echo esc_url($export_xlsx_url); ?>">
                <span class="dashicons dashicons-media-spreadsheet"></span><?php esc_html_e('Export Excel', 'coptrz-theme'); ?>
            </a>
        </div>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Date', 'coptrz-theme'); ?></th>
                    <th><?php esc_html_e('Page', 'coptrz-theme'); ?></th>
                    <th><?php esc_html_e('Form', 'coptrz-theme'); ?></th>
                    <th><?php esc_html_e('Name', 'coptrz-theme'); ?></th>
                    <th><?php esc_html_e('Email', 'coptrz-theme'); ?></th>
                    <th><?php esc_html_e('Phone', 'coptrz-theme'); ?></th>
                    <th><?php esc_html_e('Message', 'coptrz-theme'); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($detail_rows)) : ?>
                    <tr><td colspan="8"><?php esc_html_e('No submissions match the current filters.', 'coptrz-theme'); ?></td></tr>
                <?php endif; ?>
                <?php foreach ($detail_rows as $row) : ?>
                    <?php
                    $data_id   = (int) $row['data_id'];
                    $fields    = $detail_pivoted[$data_id] ?? array();
                    $canonical = Field_Map::extract($fields);
                    $page_id   = (int) $row['page_id'];
                    $page_label = $page_id > 0
                        ? (('' !== get_the_title($page_id)) ? get_the_title($page_id) : ('#' . $page_id))
                        : ('' !== (string) $row['page_path'] ? sprintf(__('(unmatched) %s', 'coptrz-theme'), $row['page_path']) : __('(no page data)', 'coptrz-theme'));
                    $form_title = $known_forms[(int) $row['cf7_id']] ?? ('#' . $row['cf7_id']);
                    ?>
                    <tr>
                        <td><?php echo esc_html($row['submitted_at']); ?></td>
                        <td><?php echo esc_html($page_label); ?></td>
                        <td><?php echo esc_html($form_title); ?></td>
                        <td><?php echo esc_html($canonical['name']); ?></td>
                        <td><?php echo esc_html($canonical['email']); ?></td>
                        <td><?php echo esc_html($canonical['phone']); ?></td>
                        <td><?php echo esc_html(mb_strimwidth($canonical['message'], 0, 60, '…')); ?></td>
                        <td>
                            <?php if (!empty($fields)) : ?>
                                <details>
                                    <summary><?php esc_html_e('All fields', 'coptrz-theme'); ?></summary>
                                    <ul style="margin:0;">
                                        <?php foreach ($fields as $name => $value) : ?>
                                            <li><strong><?php echo esc_html($name); ?>:</strong> <?php echo esc_html((string) $value); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </details>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1) : ?>
            <p>
                <?php
                echo wp_kses_post(
                    paginate_links(
                        array(
                            'base'    => add_query_arg(array_merge(self::current_view_args(), array('paged' => '%#%')), menu_page_url(self::PAGE_SLUG, false)),
                            'format'  => '',
                            'current' => $paged,
                            'total'   => $total_pages,
                        )
                    )
                );
                ?>
            </p>
        <?php endif; ?>
        <?php
    }

    /**
     * @return array<string,mixed>
     */
    private static function get_filters()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only filter state, applied via GET.
        $has_submitted = isset($_GET['filtered']);
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $start_raw = isset($_GET['start']) ? sanitize_text_field(wp_unslash($_GET['start'])) : '';
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $end_raw = isset($_GET['end']) ? sanitize_text_field(wp_unslash($_GET['end'])) : '';

        $start = self::parse_date($start_raw, '-7 days');
        $end   = self::parse_date($end_raw, 'today');

        if ($end < $start) {
            $swap  = $start;
            $start = $end;
            $end   = $swap;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $page_ids = isset($_GET['page_ids']) ? array_map('intval', (array) wp_unslash($_GET['page_ids'])) : array();
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $cf7_ids = isset($_GET['cf7_ids']) ? array_map('intval', (array) wp_unslash($_GET['cf7_ids'])) : array();

        return array(
            'start'                => $start->format('Y-m-d 00:00:00'),
            'end_exclusive'        => $end->modify('+1 day')->format('Y-m-d 00:00:00'),
            'start_display'        => $start->format('Y-m-d'),
            'end_display'          => $end->format('Y-m-d'),
            'page_ids'             => array_values(array_filter($page_ids)),
            'cf7_ids'              => array_values(array_filter($cf7_ids)),
            'include_unmatched'    => $has_submitted ? !empty($_GET['include_unmatched']) : true, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            'include_no_page_data' => $has_submitted ? !empty($_GET['include_no_page_data']) : true, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        );
    }

    /**
     * @param string $value
     * @param string $fallback_relative
     * @return \DateTimeImmutable
     */
    private static function parse_date($value, $fallback_relative)
    {
        if ('' !== $value) {
            $dt = \DateTimeImmutable::createFromFormat('!Y-m-d', $value, wp_timezone());
            if ($dt instanceof \DateTimeImmutable) {
                return $dt;
            }
        }

        return new \DateTimeImmutable($fallback_relative, wp_timezone());
    }

    /**
     * @return array<int,string> CF7 form ID => title.
     */
    private static function known_forms_with_titles()
    {
        $ids = Repository::known_forms();
        $out = array();

        foreach ($ids as $id) {
            $title = '#' . $id;
            if (class_exists('\WPCF7_ContactForm')) {
                $form = \WPCF7_ContactForm::get_instance($id);
                if ($form) {
                    $title = $form->title();
                }
            }
            $out[$id] = $title;
        }

        return $out;
    }

    /**
     * @return bool
     */
    private static function is_report_page()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only routing check.
        $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        return self::PAGE_SLUG === $page;
    }

    /**
     * @return string
     */
    private static function current_page_slug()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : self::PAGE_SLUG;
        return '' !== $page ? $page : self::PAGE_SLUG;
    }

    /**
     * Whitelisted current-request query args, used to preserve filter state
     * across export links, pagination, and post-redirect returns.
     *
     * @return array<string,mixed>
     */
    private static function current_view_args()
    {
        $args = array('page' => self::current_page_slug());

        $keys = array('start', 'end', 'view', 'filtered', 'include_unmatched', 'include_no_page_data', 'paged');
        foreach ($keys as $key) {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only, values re-escaped on output.
            if (isset($_GET[$key]) && '' !== $_GET[$key]) {
                $args[$key] = sanitize_text_field(wp_unslash($_GET[$key]));
            }
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if (!empty($_GET['page_ids'])) {
            $args['page_ids'] = array_map('intval', (array) wp_unslash($_GET['page_ids']));
        }
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if (!empty($_GET['cf7_ids'])) {
            $args['cf7_ids'] = array_map('intval', (array) wp_unslash($_GET['cf7_ids']));
        }

        return $args;
    }
}

Admin::init();
