<?php
/**
 * Plugin/Snippet Name: CF7 Page Report — Export
 * Description: CSV and Excel export for the page report. None of Advanced
 *              CF7 DB's own export code is reusable here — its
 *              vsz_cf7_export_to_csv() / vsz_cf7_export_to_excel() /
 *              create_export_query() (admin/class-advanced-cf7-db-admin.php)
 *              are hardcoded to a single form ID and read $_POST directly.
 *              What IS reused: the UTF-8 BOM + fputcsv() convention, the
 *              output-buffer-clearing fix for the blank-first-row bug their
 *              own leftover debug code was chasing, and — for Excel — their
 *              bundled XLSXWriter library (guarded by file_exists(), with a
 *              CSV-only fallback notice if it's missing).
 *
 *              Detail export uses a "wide table" mode not shown on-screen:
 *              CSV gets one row per submission with the canonical columns
 *              plus every distinct field name seen in the export set;
 *              Excel gets one sheet per form (that form's own natural
 *              columns via vsz_cf7_get_db_fields()) plus a Summary sheet —
 *              sidestepping the multi-form "which columns" problem entirely.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

namespace CoptrzTheme\CF7PageReport;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * CSV/XLSX writers for the page report.
 */
class Exporter
{
    /** Hard cap on exported detail rows, with a visible truncation note. */
    const EXPORT_ROW_LIMIT = 50000;

    /**
     * Stream the per-page summary (plus unmatched/no-page-data buckets and a
     * grand total row) as CSV and exit.
     *
     * @param array<string,mixed> $filters
     */
    public static function export_summary_csv(array $filters)
    {
        self::send_csv_headers('cf7-page-report-summary-' . gmdate('Y-m-d-His') . '.csv');
        $out = self::open_output();

        fputcsv(
            $out,
            array(
                __('Page', 'coptrz-theme'),
                __('Path', 'coptrz-theme'),
                __('Type', 'coptrz-theme'),
                __('Submissions', 'coptrz-theme'),
                __('Forms', 'coptrz-theme'),
                __('First submission', 'coptrz-theme'),
                __('Last submission', 'coptrz-theme'),
            )
        );

        $grand_total = 0;

        foreach (Repository::matched_summary($filters) as $row) {
            $grand_total += (int) $row['submissions'];
            fputcsv(
                $out,
                array(
                    self::page_title((int) $row['page_id']),
                    $row['page_path'],
                    $row['post_type'],
                    $row['submissions'],
                    $row['form_count'],
                    $row['first_at'],
                    $row['last_at'],
                )
            );
        }

        if (!empty($filters['include_unmatched'])) {
            foreach (Repository::unmatched_summary($filters) as $row) {
                $grand_total += (int) $row['submissions'];
                fputcsv(
                    $out,
                    array(
                        __('(unmatched URL)', 'coptrz-theme'),
                        $row['page_path'],
                        '',
                        $row['submissions'],
                        $row['form_count'],
                        $row['first_at'],
                        $row['last_at'],
                    )
                );
            }
        }

        if (!empty($filters['include_no_page_data'])) {
            $no_page_data = Repository::no_page_data_count($filters);
            if ($no_page_data > 0) {
                $grand_total += $no_page_data;
                fputcsv($out, array(__('(no page data)', 'coptrz-theme'), '', '', $no_page_data, '', '', ''));
            }
        }

        fputcsv($out, array(__('Grand total', 'coptrz-theme'), '', '', $grand_total, '', '', ''));

        fclose($out);
        exit;
    }

    /**
     * Stream the detail drill-down (wide table: canonical columns plus every
     * distinct field seen) as CSV and exit.
     *
     * @param array<string,mixed> $filters
     */
    public static function export_detail_csv(array $filters)
    {
        list($rows, $pivoted, $extra_field_names, $truncated) = self::gather_detail($filters);

        self::send_csv_headers('cf7-page-report-detail-' . gmdate('Y-m-d-His') . '.csv');
        $out = self::open_output();

        $header = array_merge(
            array(
                __('Date', 'coptrz-theme'),
                __('Page', 'coptrz-theme'),
                __('Form', 'coptrz-theme'),
                __('Name', 'coptrz-theme'),
                __('Email', 'coptrz-theme'),
                __('Phone', 'coptrz-theme'),
                __('Message', 'coptrz-theme'),
            ),
            $extra_field_names
        );
        fputcsv($out, $header);

        foreach ($rows as $row) {
            $data_id   = (int) $row['data_id'];
            $fields    = $pivoted[$data_id] ?? array();
            $canonical = Field_Map::extract($fields);

            $line = array(
                $row['submitted_at'],
                self::page_label($row),
                self::form_title((int) $row['cf7_id']),
                $canonical['name'],
                $canonical['email'],
                $canonical['phone'],
                $canonical['message'],
            );

            foreach ($extra_field_names as $name) {
                $line[] = $fields[$name] ?? '';
            }

            fputcsv($out, $line);
        }

        if ($truncated) {
            /* translators: %d: row limit */
            fputcsv($out, array(sprintf(__('Results truncated at %d rows — narrow the date range or page selection to see the rest.', 'coptrz-theme'), self::EXPORT_ROW_LIMIT)));
        }

        fclose($out);
        exit;
    }

    /**
     * Stream the detail drill-down as XLSX: a Summary sheet plus one sheet
     * per form OR one sheet per page, depending on $group_by. Exits on
     * write, or wp_die()s if the plugin's bundled XLSXWriter isn't available.
     *
     * @param array<string,mixed> $filters
     * @param string              $group_by 'form' (default) or 'page'.
     */
    public static function export_detail_xlsx(array $filters, $group_by = 'form')
    {
        $writer_path = self::locate_xlsxwriter();
        if (null === $writer_path) {
            wp_die(esc_html__('Excel export is unavailable — the Advanced CF7 DB plugin\'s bundled XLSXWriter library was not found. Use CSV export instead.', 'coptrz-theme'));
        }

        require_once $writer_path;
        if (!class_exists('\XLSXWriter')) {
            wp_die(esc_html__('Excel export is unavailable — XLSXWriter failed to load. Use CSV export instead.', 'coptrz-theme'));
        }

        list($rows, $pivoted, $extra_field_names, $truncated) = self::gather_detail($filters);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        nocache_headers();

        $writer = new \XLSXWriter();

        $writer->writeSheetRow(
            'Summary',
            array(__('Page', 'coptrz-theme'), __('Path', 'coptrz-theme'), __('Submissions', 'coptrz-theme'), __('Forms', 'coptrz-theme'))
        );
        foreach (Repository::matched_summary($filters) as $summary_row) {
            $writer->writeSheetRow(
                'Summary',
                array(
                    self::page_title((int) $summary_row['page_id']),
                    $summary_row['page_path'],
                    $summary_row['submissions'],
                    $summary_row['form_count'],
                )
            );
        }
        if ($truncated) {
            /* translators: %d: row limit */
            $writer->writeSheetRow('Summary', array(sprintf(__('Detail sheets truncated at %d rows.', 'coptrz-theme'), self::EXPORT_ROW_LIMIT)));
        }

        if ('page' === $group_by) {
            self::write_page_sheets($writer, $rows, $pivoted, $extra_field_names);
        } else {
            self::write_form_sheets($writer, $rows, $pivoted);
        }

        $suffix   = 'page' === $group_by ? '-by-page' : '-by-form';
        $filename = 'cf7-page-report' . $suffix . '-' . gmdate('Ymd-His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . sanitize_file_name($filename) . '"');
        $writer->writeToStdOut();
        exit;
    }

    /**
     * One sheet per form, each with that form's own natural columns (from
     * vsz_cf7_get_db_fields() when available).
     *
     * @param \XLSXWriter $writer
     * @param array<int,array<string,mixed>> $rows
     * @param array<int,array<string,string>> $pivoted
     */
    private static function write_form_sheets(\XLSXWriter $writer, array $rows, array $pivoted)
    {
        $by_form = array();
        foreach ($rows as $row) {
            $by_form[(int) $row['cf7_id']][] = $row;
        }

        $used_names = array();

        foreach ($by_form as $cf7_id => $form_rows) {
            $sheet_name = self::unique_sheet_name($used_names, self::form_title($cf7_id));
            $fields     = function_exists('vsz_cf7_get_db_fields')
                ? array_values(vsz_cf7_get_db_fields($cf7_id))
                : self::union_fields_for($form_rows, $pivoted);

            $writer->writeSheetRow($sheet_name, array_merge(array(__('Date', 'coptrz-theme'), __('Page', 'coptrz-theme')), $fields));

            foreach ($form_rows as $row) {
                $data_id    = (int) $row['data_id'];
                $row_fields = $pivoted[$data_id] ?? array();

                $line = array($row['submitted_at'], self::page_label($row));
                foreach ($fields as $field_name) {
                    $line[] = $row_fields[$field_name] ?? '';
                }

                $writer->writeSheetRow($sheet_name, $line);
            }
        }
    }

    /**
     * One sheet per page (plus a shared "Unmatched URLs" and "No page data"
     * sheet for the two attribution buckets). A page can have submissions
     * from several different forms, so unlike the by-form layout there is no
     * single "natural" column set — each sheet uses the same canonical
     * columns (Date, Form, Name, Email, Phone, Message) plus the union of
     * every other field seen anywhere in the export, matching the CSV
     * detail export's wide-table shape.
     *
     * @param \XLSXWriter $writer
     * @param array<int,array<string,mixed>> $rows
     * @param array<int,array<string,string>> $pivoted
     * @param string[] $extra_field_names
     */
    private static function write_page_sheets(\XLSXWriter $writer, array $rows, array $pivoted, array $extra_field_names)
    {
        $by_page = array();
        foreach ($rows as $row) {
            $key = self::page_group_key($row);
            if (!isset($by_page[$key])) {
                $by_page[$key] = array('label' => self::page_group_label($row), 'rows' => array());
            }
            $by_page[$key]['rows'][] = $row;
        }

        $header = array_merge(
            array(
                __('Date', 'coptrz-theme'),
                __('Form', 'coptrz-theme'),
                __('Name', 'coptrz-theme'),
                __('Email', 'coptrz-theme'),
                __('Phone', 'coptrz-theme'),
                __('Message', 'coptrz-theme'),
            ),
            $extra_field_names
        );

        $used_names = array();

        foreach ($by_page as $group) {
            $sheet_name = self::unique_sheet_name($used_names, $group['label']);
            $writer->writeSheetRow($sheet_name, $header);

            foreach ($group['rows'] as $row) {
                $data_id   = (int) $row['data_id'];
                $fields    = $pivoted[$data_id] ?? array();
                $canonical = Field_Map::extract($fields);

                $line = array(
                    $row['submitted_at'],
                    self::form_title((int) $row['cf7_id']),
                    $canonical['name'],
                    $canonical['email'],
                    $canonical['phone'],
                    $canonical['message'],
                );

                foreach ($extra_field_names as $name) {
                    $line[] = $fields[$name] ?? '';
                }

                $writer->writeSheetRow($sheet_name, $line);
            }
        }
    }

    /**
     * @param array<string,mixed> $row
     * @return string
     */
    private static function page_group_key(array $row)
    {
        if ((int) $row['page_id'] > 0) {
            return 'page_' . $row['page_id'];
        }

        return '' !== (string) $row['page_path'] ? 'unmatched' : 'no_data';
    }

    /**
     * @param array<string,mixed> $row
     * @return string
     */
    private static function page_group_label(array $row)
    {
        if ((int) $row['page_id'] > 0) {
            return self::page_title((int) $row['page_id']);
        }

        return '' !== (string) $row['page_path'] ? __('Unmatched URLs', 'coptrz-theme') : __('No page data', 'coptrz-theme');
    }

    /**
     * Fetch the detail row set once, plus its pivoted field values and the
     * union of field names present — shared by both the CSV and XLSX writers.
     *
     * @param array<string,mixed> $filters
     * @return array{0:array<int,array<string,mixed>>,1:array<int,array<string,string>>,2:string[],3:bool}
     */
    private static function gather_detail(array $filters)
    {
        $total = Repository::detail_count($filters);
        $limit = min(max($total, 0), self::EXPORT_ROW_LIMIT);

        $rows = $limit > 0 ? Repository::detail_rows($filters, $limit, 0) : array();

        $data_ids = array_map(
            static function ($row) {
                return (int) $row['data_id'];
            },
            $rows
        );

        $pivoted      = array();
        $extra_fields = array();

        foreach (array_chunk($data_ids, 1000) as $chunk) {
            $values      = Repository::field_values($chunk);
            $chunk_pivot = Repository::pivot_field_values($values);

            foreach ($chunk_pivot as $data_id => $fields) {
                $pivoted[$data_id] = $fields;
                foreach (array_keys($fields) as $name) {
                    $extra_fields[$name] = true;
                }
            }
        }

        $extra_field_names = array_keys($extra_fields);
        sort($extra_field_names);

        return array($rows, $pivoted, $extra_field_names, $total > self::EXPORT_ROW_LIMIT);
    }

    /**
     * @param array<int,array<string,mixed>> $form_rows
     * @param array<int,array<string,string>> $pivoted
     * @return string[]
     */
    private static function union_fields_for(array $form_rows, array $pivoted)
    {
        $fields = array();
        foreach ($form_rows as $row) {
            $data_id = (int) $row['data_id'];
            foreach (array_keys($pivoted[$data_id] ?? array()) as $name) {
                $fields[$name] = true;
            }
        }

        $names = array_keys($fields);
        sort($names);

        return $names;
    }

    /**
     * @param array<string,mixed> $row Detail row from Repository::detail_rows().
     * @return string
     */
    private static function page_label(array $row)
    {
        if ((int) $row['page_id'] > 0) {
            return self::page_title((int) $row['page_id']);
        }

        if ('' !== (string) $row['page_path']) {
            /* translators: %s: page path */
            return sprintf(__('(unmatched) %s', 'coptrz-theme'), $row['page_path']);
        }

        return __('(no page data)', 'coptrz-theme');
    }

    /**
     * @param int $page_id
     * @return string
     */
    private static function page_title($page_id)
    {
        if ($page_id <= 0) {
            return '';
        }

        $title = get_the_title($page_id);

        return '' !== $title ? $title : ('#' . $page_id);
    }

    /**
     * @param int $cf7_id
     * @return string
     */
    private static function form_title($cf7_id)
    {
        static $cache = array();

        if (isset($cache[$cf7_id])) {
            return $cache[$cf7_id];
        }

        $title = '';
        if (class_exists('\WPCF7_ContactForm')) {
            $form = \WPCF7_ContactForm::get_instance($cf7_id);
            if ($form) {
                $title = $form->title();
            }
        }
        if ('' === $title) {
            $title = '#' . $cf7_id;
        }

        $cache[$cf7_id] = $title;

        return $title;
    }

    /**
     * XLSX sheet names are capped at 31 chars and can't contain [ ] : * ? / \.
     *
     * @param string $title
     * @return string
     */
    private static function sheet_name($title)
    {
        $name = (string) preg_replace('/[\[\]:\*\?\/\\\\]/', ' ', (string) $title);
        $name = trim($name);

        if ('' === $name) {
            $name = 'Form';
        }

        return substr($name, 0, 31);
    }

    /**
     * sheet_name() truncated to 31 chars can collide (two differently-named
     * pages/forms sharing the same first 31 characters, or two pages with
     * identical titles) — real Excel rejects duplicate sheet names, so
     * dedupe with a numeric suffix before handing off to XLSXWriter.
     *
     * @param array<string,bool> $used  Sheet names already used, by reference.
     * @param string             $title
     * @return string
     */
    private static function unique_sheet_name(array &$used, $title)
    {
        $base      = self::sheet_name($title);
        $candidate = $base;
        $i         = 2;

        while (isset($used[$candidate])) {
            $suffix    = ' (' . $i . ')';
            $candidate = substr($base, 0, 31 - strlen($suffix)) . $suffix;
            $i++;
        }

        $used[$candidate] = true;

        return $candidate;
    }

    /**
     * @return string|null Path to the bundled XLSXWriter class file, or null if absent.
     */
    private static function locate_xlsxwriter()
    {
        $path = WP_PLUGIN_DIR . '/advanced-cf7-db/includes/libraries/excel/xlsx/PHP_XLSXWriter-master/xlsxwriter.class.php';

        return file_exists($path) ? $path : null;
    }

    /**
     * @param string $filename
     */
    private static function send_csv_headers($filename)
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        nocache_headers();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . sanitize_file_name($filename) . '"');
    }

    /**
     * @return resource
     */
    private static function open_output()
    {
        $handle = fopen('php://output', 'w');
        fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM, for Excel on Windows.

        return $handle;
    }
}
