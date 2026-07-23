<?php
/**
 * Plugin/Snippet Name: Meta Shim - Key Formatter
 * Description: Standalone replacement for Carbon Fields' storage-key engine.
 *              Encodes/decodes the exact CF3 "Key_Toolset" pipe-delimited meta
 *              key format and provides cache-backed primitives for reading and
 *              writing the raw wp_postmeta / wp_termmeta / wp_options rows.
 *
 *              Key schema (identical to Carbon Fields 3, so existing DB rows are
 *              read and written byte-for-byte the same):
 *
 *                  _[root]|[field:names:by:colon]|[group:indexes:by:colon]|[value_index]|[property]
 *
 *              Example:
 *                  _sections|section_items:post_box_styles:column_width|1:1:0|0|value
 *                  -> field "column_width" inside complex "post_box_styles" (row 0)
 *                     inside complex "section_items" (row 1) inside root complex
 *                     "sections" (row 1).
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

namespace CoptrzTheme\MetaShim;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Pure, stateless codec + low-level data-access layer for the meta shim.
 *
 * Nothing in here knows about field *definitions*; it only knows how Carbon
 * Fields lays bytes out in the database. The tree-aware reconstruction lives in
 * meta-reader.php / Container.php which call into these primitives.
 */
class Key_Formatter
{
    /** Prefix Carbon Fields prepends to every stored key. */
    const KEY_PREFIX = '_';

    /** Property used to keep an otherwise-empty field "alive" in storage. */
    const KEEPALIVE_PROPERTY = '_empty';

    /** Glue between the 5 key segments. */
    const SEGMENT_GLUE = '|';

    /** Glue between values *inside* a segment (field names / group indexes). */
    const SEGMENT_VALUE_GLUE = ':';

    /** Default value property name. */
    const VALUE_PROPERTY = 'value';

    /** Number of segments in a fully-qualified key. */
    const TOTAL_SEGMENTS = 5;

    /* ===================================================================== */
    /*  ENCODE                                                               */
    /* ===================================================================== */

    /**
     * Trim a hierarchy-index array to the number of *ancestor* complex levels.
     *
     * Carbon keeps exactly count($full_hierarchy) - 1 indexes, because the
     * deepest field's own row index is carried in the value_index segment, not
     * in the hierarchy_index segment.
     *
     * @param array $full_hierarchy        Field-name chain incl. the root.
     * @param array $full_hierarchy_index  Chosen row index at each level.
     * @return array<int>
     */
    public static function sanitize_hierarchy_index($full_hierarchy, $full_hierarchy_index)
    {
        $full_hierarchy_index = array_slice($full_hierarchy_index, 0, count($full_hierarchy) - 1);
        if (empty($full_hierarchy_index)) {
            return array();
        }
        return array_map('intval', $full_hierarchy_index);
    }

    /**
     * Build the storage key prefix up to (and including) the trailing glue
     * before the value_index segment: `_root|a:b|0:1|`.
     *
     * @param array $full_hierarchy
     * @param array $full_hierarchy_index
     * @return string
     */
    protected static function storage_key_prefix($full_hierarchy, $full_hierarchy_index)
    {
        $full_hierarchy_index = self::sanitize_hierarchy_index($full_hierarchy, $full_hierarchy_index);
        $parents = $full_hierarchy;
        $first_parent = array_shift($parents);

        return self::KEY_PREFIX
            . $first_parent
            . self::SEGMENT_GLUE
            . implode(self::SEGMENT_VALUE_GLUE, $parents)
            . self::SEGMENT_GLUE
            . implode(self::SEGMENT_VALUE_GLUE, $full_hierarchy_index)
            . self::SEGMENT_GLUE;
    }

    /**
     * Build a complete storage key for a single value cell.
     *
     * Direct port of Carbon_Fields\Toolset\Key_Toolset::get_storage_key() so the
     * produced keys are identical to what CF3 wrote historically.
     *
     * @param bool   $is_simple_root_field  True for a top-level non-complex field.
     * @param array  $full_hierarchy        Field-name chain incl. the root.
     * @param array  $full_hierarchy_index  Ancestor row indexes.
     * @param int    $value_index           Row/value index of the field itself.
     * @param string $property              value | type | subtype | id | _empty
     * @return string
     */
    public static function build_key($is_simple_root_field, $full_hierarchy, $full_hierarchy_index, $value_index, $property = self::VALUE_PROPERTY)
    {
        if ($is_simple_root_field && $property === self::VALUE_PROPERTY) {
            // Simple root fields collapse to just `_name`.
            $hierarchy = $full_hierarchy;
            return self::KEY_PREFIX . array_shift($hierarchy);
        }

        return self::storage_key_prefix($full_hierarchy, $full_hierarchy_index)
            . intval($value_index)
            . self::SEGMENT_GLUE
            . $property;
    }

    /* ===================================================================== */
    /*  DECODE                                                               */
    /* ===================================================================== */

    /**
     * Parse a storage key back into its structured segments.
     *
     * Mirrors Key_Toolset::parse_storage_key(). Single-segment keys (simple root
     * fields such as `_hero_heading`) yield an empty hierarchy.
     *
     * @param string $storage_key
     * @return array{root:string,hierarchy:array,hierarchy_index:array,value_index:int,property:string,full_hierarchy:array}
     */
    public static function parse_key($storage_key)
    {
        $parsed = array(
            'root'            => '',
            'hierarchy'       => array(),
            'hierarchy_index' => array(),
            'value_index'     => 0,
            'property'        => self::VALUE_PROPERTY,
        );

        $key = substr($storage_key, strlen(self::KEY_PREFIX)); // drop leading "_"
        $segments = explode(self::SEGMENT_GLUE, $key);

        $parsed['root'] = $segments[0];

        if (count($segments) === self::TOTAL_SEGMENTS) {
            $parsed['hierarchy'] = array_values(array_filter(
                explode(self::SEGMENT_VALUE_GLUE, $segments[1]),
                static function ($v) {
                    return $v !== '';
                }
            ));

            if ($segments[2] !== '') {
                $parsed['hierarchy_index'] = array_map('intval', explode(self::SEGMENT_VALUE_GLUE, $segments[2]));
            }

            $parsed['value_index'] = intval($segments[3]);
            $parsed['property']    = $segments[4];
        }

        $parsed['full_hierarchy'] = array_merge(array($parsed['root']), $parsed['hierarchy']);

        return $parsed;
    }

    /* ===================================================================== */
    /*  RAW DATA ACCESS (read)                                               */
    /* ===================================================================== */

    /** @var array<string,array> Per-request cache of an object's full meta map. */
    protected static $object_cache = array();

    /**
     * Load the *entire* prefixed meta map for an object, keyed by storage key.
     *
     * Uses WordPress' own meta cache for posts/terms (a single primed query per
     * object, then served from object cache) which is precisely the lookup cost
     * we are trying to reclaim from Carbon Fields. Options are enumerated once
     * per request and memoised.
     *
     * @param string $object_type post|term|option
     * @param int    $object_id
     * @return array<string,string> storage_key => scalar value
     */
    public static function load_object_meta($object_type, $object_id)
    {
        $object_id   = (int) $object_id;
        $cache_token = $object_type . ':' . $object_id;

        if (isset(self::$object_cache[$cache_token])) {
            return self::$object_cache[$cache_token];
        }

        $map = array();

        if ($object_type === 'post' || $object_type === 'term') {
            // Returns ALL meta rows (cached by WP core) as key => array(values).
            $raw = ($object_type === 'post')
                ? get_post_meta($object_id)
                : get_term_meta($object_id);

            if (is_array($raw)) {
                foreach ($raw as $meta_key => $values) {
                    if ($meta_key === '' || $meta_key[0] !== self::KEY_PREFIX) {
                        continue; // Only shim-managed (underscore-prefixed) rows.
                    }
                    $map[$meta_key] = is_array($values) ? (string) reset($values) : (string) $values;
                }
            }
        }

        self::$object_cache[$cache_token] = $map;
        return $map;
    }

    /**
     * Load only the rows belonging to one root field for an object — the unit the
     * reader actually needs.
     *
     *  - post/term: served from WordPress' object-cached meta (one primed query).
     *  - option:    a single TARGETED query (never scans unrelated transients).
     *
     * @param string $object_type post|term|option
     * @param int    $object_id
     * @param string $root_field  Field name WITHOUT leading underscore.
     * @return array<string,string>
     */
    public static function load_root_map($object_type, $object_id, $root_field)
    {
        if ($object_type === 'option') {
            return self::load_option_root($root_field);
        }
        return self::filter_root_rows(self::load_object_meta($object_type, $object_id), $root_field);
    }

    /**
     * Targeted, memoised load of a single theme-option root field's rows.
     *
     * @param string $root_field
     * @return array<string,string>
     */
    protected static function load_option_root($root_field)
    {
        static $cache = array();
        if (isset($cache[$root_field])) {
            return $cache[$root_field];
        }

        global $wpdb;
        $exact = self::KEY_PREFIX . $root_field;
        $like  = $wpdb->esc_like($exact . self::SEGMENT_GLUE) . '%';

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name = %s OR option_name LIKE %s",
                $exact,
                $like
            ),
            ARRAY_A
        );

        $map = array();
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $map[$row['option_name']] = $row['option_value'];
            }
        }

        $cache[$root_field] = $map;
        return $map;
    }

    /**
     * Slice an already-loaded object map down to the rows belonging to one root
     * field (`_field` itself plus every `_field|...` descendant).
     *
     * @param array  $object_map  Output of load_object_meta().
     * @param string $root_field  Field name WITHOUT the leading underscore.
     * @return array<string,string>
     */
    public static function filter_root_rows($object_map, $root_field)
    {
        $exact  = self::KEY_PREFIX . $root_field;                       // _sections
        $prefix = $exact . self::SEGMENT_GLUE;                          // _sections|
        $plen   = strlen($prefix);

        $rows = array();
        foreach ($object_map as $key => $value) {
            if ($key === $exact || strncmp($key, $prefix, $plen) === 0) {
                $rows[$key] = $value;
            }
        }
        return $rows;
    }

    /**
     * True when a storage key is a keepalive sentinel (`…|_empty`) rather than
     * a real value row — written by Writer whenever a complex/multi/association
     * field is saved with zero rows, so the field stays present (rather than
     * falling back) with nothing in it. Splits on SEGMENT_GLUE and checks the
     * final segment so nested keepalives (e.g. `_sections|section_items|0|0|_empty`)
     * are caught too, not just root-level ones.
     *
     * @param string $key
     * @return bool
     */
    public static function is_keepalive_key($key)
    {
        $segments = explode(self::SEGMENT_GLUE, $key);
        return end($segments) === self::KEEPALIVE_PROPERTY;
    }

    /**
     * Flush the per-request object cache (call after a save so a subsequent read
     * in the same request reflects new values).
     *
     * @param string|null $object_type
     * @param int|null    $object_id
     * @return void
     */
    public static function flush_cache($object_type = null, $object_id = null)
    {
        if ($object_type === null) {
            self::$object_cache = array();
            return;
        }
        unset(self::$object_cache[$object_type . ':' . (int) $object_id]);
    }

    /* ===================================================================== */
    /*  RAW DATA ACCESS (write)                                              */
    /* ===================================================================== */

    /**
     * Replace every stored row for a root field with a fresh flat key=>value set.
     *
     * Carbon Fields persists a complex field by first deleting the whole subtree
     * and then inserting the new cells; we reproduce that exactly so no orphan
     * rows from a previous (longer) repeater survive.
     *
     * @param string $object_type post|term|option
     * @param int    $object_id
     * @param string $root_field   Field name WITHOUT the leading underscore.
     * @param array  $flat_rows    storage_key => scalar value (already encoded).
     * @return void
     */
    public static function persist_root($object_type, $object_id, $root_field, array $flat_rows)
    {
        self::delete_root($object_type, $object_id, $root_field);

        foreach ($flat_rows as $storage_key => $value) {
            self::write_cell($object_type, $object_id, $storage_key, $value);
        }

        self::flush_cache($object_type, $object_id);
    }

    /**
     * Delete every row (exact + descendants) for one root field.
     *
     * @param string $object_type
     * @param int    $object_id
     * @param string $root_field
     * @return void
     */
    public static function delete_root($object_type, $object_id, $root_field)
    {
        global $wpdb;

        $exact = self::KEY_PREFIX . $root_field;
        $like  = $wpdb->esc_like($exact . self::SEGMENT_GLUE) . '%';

        if ($object_type === 'post') {
            $ids = $wpdb->get_col($wpdb->prepare(
                "SELECT meta_id FROM {$wpdb->postmeta} WHERE post_id = %d AND (meta_key = %s OR meta_key LIKE %s)",
                $object_id,
                $exact,
                $like
            ));
            foreach ($ids as $mid) {
                delete_metadata_by_mid('post', $mid);
            }
        } elseif ($object_type === 'term') {
            $ids = $wpdb->get_col($wpdb->prepare(
                "SELECT meta_id FROM {$wpdb->termmeta} WHERE term_id = %d AND (meta_key = %s OR meta_key LIKE %s)",
                $object_id,
                $exact,
                $like
            ));
            foreach ($ids as $mid) {
                delete_metadata_by_mid('term', $mid);
            }
        } elseif ($object_type === 'option') {
            $names = $wpdb->get_col($wpdb->prepare(
                "SELECT option_name FROM {$wpdb->options} WHERE option_name = %s OR option_name LIKE %s",
                $exact,
                $like
            ));
            foreach ($names as $name) {
                delete_option($name);
            }
        }
    }

    /**
     * Write a single cell using core APIs (keeps object caches coherent).
     *
     * @param string $object_type
     * @param int    $object_id
     * @param string $storage_key
     * @param string $value
     * @return void
     */
    protected static function write_cell($object_type, $object_id, $storage_key, $value)
    {
        switch ($object_type) {
            case 'post':
                add_post_meta($object_id, $storage_key, wp_slash($value));
                break;
            case 'term':
                add_term_meta($object_id, $storage_key, wp_slash($value));
                break;
            case 'option':
                update_option($storage_key, $value);
                break;
        }
    }
}
