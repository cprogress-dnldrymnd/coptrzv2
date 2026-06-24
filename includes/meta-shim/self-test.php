<?php
/**
 * Plugin/Snippet Name: Meta Shim - Self Test
 * Description: Transitional verification tool. While Carbon Fields is STILL
 *              active, it compares carbon_get_post_meta() against the native
 *              coptrz_get_post_meta() for every registered post field of a given
 *              post and reports any mismatch. Use it to confirm zero data loss
 *              before deactivating Carbon Fields.
 *
 *              Run as an administrator:
 *                  /wp-admin/?coptrz_meta_selftest=<POST_ID>
 *
 *              Inert unless that query var is present. Remove this file (and its
 *              require in functions.php) once the migration is signed off.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Order-insensitive, loosely-typed deep comparison so that benign differences
 * (associative key order, "1" vs 1) do not register as data loss while genuine
 * structural/value divergence does.
 *
 * @param mixed $a
 * @param mixed $b
 * @return bool
 */
function coptrz_meta_shim_deep_equal($a, $b)
{
    if (is_array($a) && is_array($b)) {
        if (count($a) !== count($b)) {
            return false;
        }
        // Normalise associative arrays by key; keep list order for repeaters.
        $a_is_list = array_keys($a) === range(0, count($a) - 1);
        $b_is_list = array_keys($b) === range(0, count($b) - 1);
        if ($a_is_list !== $b_is_list) {
            return false;
        }
        if (!$a_is_list) {
            ksort($a);
            ksort($b);
        }
        $a_keys = array_keys($a);
        $b_keys = array_keys($b);
        if ($a_keys !== $b_keys) {
            return false;
        }
        foreach ($a_keys as $k) {
            if (!coptrz_meta_shim_deep_equal($a[$k], $b[$k])) {
                return false;
            }
        }
        return true;
    }

    if (is_bool($a) || is_bool($b)) {
        return (bool) $a === (bool) $b;
    }

    // Loose scalar equality (Carbon stores everything as strings).
    return (string) $a === (string) $b;
}

/**
 * Compare every registered post field for one post.
 *
 * @param int $post_id
 * @return array{summary:string,rows:array}
 */
function coptrz_meta_shim_self_test_post($post_id)
{
    if (!function_exists('carbon_get_post_meta')) {
        return array(
            'summary' => 'Carbon Fields is not active — run this test BEFORE deactivating it.',
            'rows'    => array(),
        );
    }

    $index  = \CoptrzTheme\MetaShim\Container::get_index();
    $fields = isset($index['post']) ? array_keys($index['post']) : array();
    sort($fields);

    $rows = array();
    $pass = 0;
    foreach ($fields as $field) {
        $carbon = carbon_get_post_meta($post_id, $field);
        $shim   = coptrz_get_post_meta($post_id, $field);
        $match  = coptrz_meta_shim_deep_equal($carbon, $shim);
        if ($match) {
            $pass++;
        }
        $rows[] = array(
            'field'  => $field,
            'match'  => $match,
            'carbon' => $carbon,
            'shim'   => $shim,
        );
    }

    return array(
        'summary' => sprintf('%d / %d post fields match for post #%d.', $pass, count($fields), $post_id),
        'rows'    => $rows,
    );
}

/**
 * Admin-only trigger. Renders a plain comparison report then stops.
 */
add_action('admin_init', function () {
    if (!isset($_GET['coptrz_meta_selftest']) || !current_user_can('manage_options')) {
        return;
    }

    $post_id = (int) $_GET['coptrz_meta_selftest'];
    $result  = coptrz_meta_shim_self_test_post($post_id);

    header('Content-Type: text/html; charset=utf-8');
    echo '<pre style="padding:20px;font:13px/1.5 monospace;">';
    echo "COPTRZ META SHIM — SELF TEST\n";
    echo str_repeat('=', 60) . "\n";
    echo esc_html($result['summary']) . "\n\n";

    foreach ($result['rows'] as $row) {
        $flag = $row['match'] ? 'OK  ' : 'DIFF';
        echo sprintf("[%s] %s\n", $flag, esc_html($row['field']));
        if (!$row['match']) {
            echo "      carbon: " . esc_html(wp_json_encode($row['carbon'])) . "\n";
            echo "      shim  : " . esc_html(wp_json_encode($row['shim'])) . "\n";
        }
    }
    echo '</pre>';
    exit;
});
