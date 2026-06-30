<?php
/**
 * Plugin/Snippet Name: Meta Shim - Writer
 * Description: Shared write engine for the meta shim. Converts a value tree into
 *              the flat Carbon Fields key=>value map (build_flat) and persists it.
 *              Used by the admin save routine (posted form data) and by the
 *              programmatic coptrz_set_* API (Carbon-format value trees, e.g. the
 *              "copy from post" utility that re-saves reader output).
 *
 *              build_flat() is the exact inverse of Reader::read_field(); a
 *              save -> load -> save cycle reproduces byte-identical storage.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

namespace CoptrzTheme\MetaShim {

if (!defined('ABSPATH')) {
    exit;
}

class Writer
{
    /**
     * Persist one root field for an object from a Carbon-format value tree
     * (the same shape Reader::read() returns / carbon_get_* returned).
     *
     * @param string $object_type post|term|option
     * @param int    $object_id
     * @param string $field_name
     * @param mixed  $value
     * @return bool True on success, false when the field is unknown.
     */
    public static function set($object_type, $object_id, $field_name, $value)
    {
        $field = Container::get_field($object_type, $field_name);
        if ($field === null) {
            // Unknown field: best-effort single-meta write so nothing silently no-ops.
            $key = Key_Formatter::KEY_PREFIX . $field_name;
            if ($object_type === 'post') {
                update_post_meta($object_id, $key, $value);
            } elseif ($object_type === 'term') {
                update_term_meta($object_id, $key, $value);
            } else {
                update_option($key, $value);
            }
            return false;
        }

        $posted = self::serialize($field, $value);
        $flat   = self::build_flat($field, array($field->name), array(), $posted);
        Key_Formatter::persist_root($object_type, $object_id, $field->name, $flat);
        return true;
    }

    /**
     * Normalise a Carbon-format value tree (reader output) into the same shape a
     * browser form would POST, so build_flat() can consume either source
     * uniformly. The key differences it reconciles:
     *   - association: array of {value,type,subtype,id} -> array of "type:subtype:id" tokens
     *   - checkbox:    bool                              -> 'yes' | ''
     *
     * @param Field $field
     * @param mixed $value
     * @return mixed
     */
    public static function serialize(Field $field, $value)
    {
        switch ($field->storage_kind()) {

            case 'none':
                return null;

            case 'scalar':
                if ($field->type === 'checkbox') {
                    return $value ? 'yes' : '';
                }
                return is_scalar($value) ? (string) $value : '';

            case 'multi':
                return array_map('strval', (array) $value);

            case 'association':
                $tokens = array();
                foreach ((array) $value as $item) {
                    if (is_array($item)) {
                        if (!empty($item['value'])) {
                            $tokens[] = $item['value'];
                        } elseif (!empty($item['id'])) {
                            $type    = isset($item['type']) ? $item['type'] : 'post';
                            $subtype = isset($item['subtype']) ? $item['subtype'] : '';
                            $tokens[] = $type . ':' . $subtype . ':' . $item['id'];
                        }
                    } elseif ($item !== '' && $item !== null) {
                        $tokens[] = (string) $item;
                    }
                }
                return $tokens;

            case 'complex':
                $rows = array();
                foreach ((array) $value as $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    $group_type  = isset($row['_type']) ? $row['_type'] : '_';
                    $posted_row  = array('_type' => $group_type);
                    foreach ($field->get_group_fields($group_type) as $sub) {
                        if (!($sub instanceof Field) || $sub->is_display_only()) {
                            continue;
                        }
                        $posted_row[$sub->name] = self::serialize(
                            $sub,
                            isset($row[$sub->name]) ? $row[$sub->name] : null
                        );
                    }
                    $rows[] = $posted_row;
                }
                return $rows;
        }

        return $value;
    }

    /**
     * Recursively convert a posted value tree into the flat CF key=>value map.
     * Inverse of Reader::read_field(); shares the same key encoding so storage is
     * byte-identical to what Carbon Fields produced.
     *
     * @param Field $field
     * @param array $hierarchy
     * @param array $ancestor_indexes
     * @param mixed $posted
     * @return array<string,string>
     */
    public static function build_flat(Field $field, $hierarchy, $ancestor_indexes, $posted)
    {
        $kind = $field->storage_kind();
        $is_simple_root = (count($hierarchy) === 1 && $kind === 'scalar');
        $flat = array();

        switch ($kind) {

            case 'none':
                return $flat;

            case 'scalar':
                $value = is_scalar($posted) ? (string) $posted : '';
                $flat[Key_Formatter::build_key($is_simple_root, $hierarchy, $ancestor_indexes, 0, 'value')] = $value;
                return $flat;

            case 'multi':
                $values = array_values(array_filter((array) $posted, static function ($v) {
                    return $v !== '' && $v !== null;
                }));
                if (empty($values)) {
                    $flat[Key_Formatter::build_key(false, $hierarchy, $ancestor_indexes, 0, Key_Formatter::KEEPALIVE_PROPERTY)] = '';
                } else {
                    foreach ($values as $i => $val) {
                        $flat[Key_Formatter::build_key(false, $hierarchy, $ancestor_indexes, $i, 'value')] = (string) $val;
                    }
                }
                return $flat;

            case 'association':
                $tokens = array_values(array_filter((array) $posted, static function ($v) {
                    return $v !== '' && $v !== null;
                }));
                if (empty($tokens)) {
                    $flat[Key_Formatter::build_key(false, $hierarchy, $ancestor_indexes, 0, Key_Formatter::KEEPALIVE_PROPERTY)] = '';
                } else {
                    foreach ($tokens as $i => $token) {
                        $parts = explode(':', $token);
                        $flat[Key_Formatter::build_key(false, $hierarchy, $ancestor_indexes, $i, 'value')]   = $token;
                        $flat[Key_Formatter::build_key(false, $hierarchy, $ancestor_indexes, $i, 'type')]    = isset($parts[0]) ? $parts[0] : '';
                        $flat[Key_Formatter::build_key(false, $hierarchy, $ancestor_indexes, $i, 'subtype')] = isset($parts[1]) ? $parts[1] : '';
                        $flat[Key_Formatter::build_key(false, $hierarchy, $ancestor_indexes, $i, 'id')]      = isset($parts[2]) ? $parts[2] : '';
                    }
                }
                return $flat;

            case 'complex':
                $rows_in = is_array($posted) ? $posted : array();
                // Drop the JS clone-template row (keyed by the placeholder index).
                // Its inputs live in a hidden div but a form still POSTs them, so
                // without this every save would append a phantom empty row. This
                // recurses, so nested repeater templates are dropped too. Harmless
                // no-op for programmatic serialize() input (never has this key).
                unset($rows_in[View::TEMPLATE_INDEX]);
                $rows = array_values($rows_in);
                $real = 0;
                foreach ($rows as $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    $group_type = isset($row['_type']) ? $row['_type'] : '_';
                    $flat[Key_Formatter::build_key(false, $hierarchy, $ancestor_indexes, $real, 'value')] = $group_type;
                    $child_indexes = array_merge($ancestor_indexes, array($real));

                    foreach ($field->get_group_fields($group_type) as $sub) {
                        if (!($sub instanceof Field) || $sub->is_display_only()) {
                            continue;
                        }
                        $sub_posted = isset($row[$sub->name]) ? $row[$sub->name] : null;
                        $flat += self::build_flat(
                            $sub,
                            array_merge($hierarchy, array($sub->name)),
                            $child_indexes,
                            $sub_posted
                        );
                    }
                    $real++;
                }
                if ($real === 0) {
                    $flat[Key_Formatter::build_key(false, $hierarchy, $ancestor_indexes, 0, Key_Formatter::KEEPALIVE_PROPERTY)] = '';
                }
                return $flat;
        }

        return $flat;
    }
}

} // end namespace CoptrzTheme\MetaShim

/* ========================================================================= */
/*  Global drop-in writers (root namespace) — carbon_set_* equivalents       */
/* ========================================================================= */

namespace {

    use CoptrzTheme\MetaShim\Writer;

    if (!function_exists('coptrz_set_post_meta')) {
        /**
         * Native replacement for carbon_set_post_meta().
         *
         * @param int    $post_id
         * @param string $field_name
         * @param mixed  $value  Carbon-format value tree (reader output shape).
         * @return void
         */
        function coptrz_set_post_meta($post_id, $field_name, $value)
        {
            Writer::set('post', $post_id, $field_name, $value);
        }
    }

    if (!function_exists('coptrz_set_term_meta')) {
        /**
         * Native replacement for carbon_set_term_meta().
         */
        function coptrz_set_term_meta($term_id, $field_name, $value)
        {
            Writer::set('term', $term_id, $field_name, $value);
        }
    }

    if (!function_exists('coptrz_set_theme_option')) {
        /**
         * Native replacement for carbon_set_theme_option().
         */
        function coptrz_set_theme_option($field_name, $value)
        {
            Writer::set('option', 0, $field_name, $value);
        }
    }
}
