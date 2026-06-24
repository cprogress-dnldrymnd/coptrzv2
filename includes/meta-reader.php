<?php
/**
 * Plugin/Snippet Name: Meta Shim - Reader
 * Description: Drop-in replacements for carbon_get_post_meta / carbon_get_term_meta
 *              / carbon_get_theme_option. Reconstructs the exact nested array
 *              Carbon Fields returned by walking the registered Field tree against
 *              the flat, cache-backed meta map. Output parity (including row
 *              `_type` markers, nested complex arrays and association
 *              value/type/subtype/id sub-arrays) is what lets every existing
 *              template keep consuming the data unchanged.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

namespace CoptrzTheme\MetaShim {

if (!defined('ABSPATH')) {
    exit;
}

class Reader
{
    /**
     * Read a single root field for an object and rebuild its value.
     *
     * @param string $object_type post|term|option
     * @param int    $object_id
     * @param string $field_name  WITHOUT leading underscore.
     * @return mixed scalar for simple fields, nested arrays for complex/multi/association.
     */
    public static function read($object_type, $object_id, $field_name)
    {
        $field = Container::get_field($object_type, $field_name);

        // Unknown field -> degrade to a plain single-meta read so nothing breaks.
        if ($field === null) {
            return self::fallback($object_type, $object_id, $field_name);
        }

        $map = Key_Formatter::load_root_map($object_type, $object_id, $field->name);

        return self::read_field($map, $field, array($field->name), array(), true);
    }

    /**
     * Recursive value reconstruction for one field at a given hierarchy/indexes.
     *
     * @param array  $map               storage_key => scalar value
     * @param Field  $field
     * @param array  $hierarchy         Field-name chain incl. the root.
     * @param array  $ancestor_indexes  Chosen row index at each complex ancestor.
     * @param bool   $is_root           True only for the container's top-level field.
     * @return mixed
     */
    protected static function read_field($map, Field $field, $hierarchy, $ancestor_indexes, $is_root)
    {
        $kind = $field->storage_kind();
        // A simple root field (scalar directly on the container) collapses to `_name`.
        $is_simple_root = ($is_root && $kind === 'scalar');

        switch ($kind) {

            case 'none':
                // Display-only field (html/separator): mirror Carbon's empty string.
                return '';

            case 'scalar':
                $key = Key_Formatter::build_key($is_simple_root, $hierarchy, $ancestor_indexes, 0, 'value');
                $raw = array_key_exists($key, $map) ? $map[$key] : $field->default;
                // Carbon casts checkbox values to bool; mirror that for parity so
                // existing strict (=== true/false) comparisons keep working.
                if ($field->type === 'checkbox') {
                    return ($raw !== '' && $raw !== '0' && $raw !== false);
                }
                return $raw;

            case 'multi':
                // set / multiselect / media_gallery -> ordered array of scalars.
                $values = array();
                $i = 0;
                while (true) {
                    $key = Key_Formatter::build_key(false, $hierarchy, $ancestor_indexes, $i, 'value');
                    if (!array_key_exists($key, $map)) {
                        break;
                    }
                    $values[] = $map[$key];
                    $i++;
                }
                return $values;

            case 'association':
                // Ordered array of [value, type, subtype, id] just like Carbon.
                $items = array();
                $i = 0;
                while (true) {
                    $value_key = Key_Formatter::build_key(false, $hierarchy, $ancestor_indexes, $i, 'value');
                    if (!array_key_exists($value_key, $map)) {
                        break;
                    }
                    $items[] = array(
                        'value'   => $map[$value_key],
                        'type'    => self::cell($map, $hierarchy, $ancestor_indexes, $i, 'type'),
                        'subtype' => self::cell($map, $hierarchy, $ancestor_indexes, $i, 'subtype'),
                        'id'      => self::cell($map, $hierarchy, $ancestor_indexes, $i, 'id'),
                    );
                    $i++;
                }
                return $items;

            case 'complex':
                // Ordered array of group rows; each row tagged with its `_type`.
                $rows = array();
                $i = 0;
                while (true) {
                    $group_key = Key_Formatter::build_key(false, $hierarchy, $ancestor_indexes, $i, 'value');
                    if (!array_key_exists($group_key, $map)) {
                        break;
                    }

                    $group_type     = $map[$group_key];
                    $child_indexes  = array_merge($ancestor_indexes, array($i));
                    $row            = array('_type' => $group_type);

                    foreach ($field->get_group_fields($group_type) as $sub) {
                        if (!($sub instanceof Field)) {
                            continue;
                        }
                        $row[$sub->name] = self::read_field(
                            $map,
                            $sub,
                            array_merge($hierarchy, array($sub->name)),
                            $child_indexes,
                            false
                        );
                    }

                    $rows[] = $row;
                    $i++;
                }
                return $rows;
        }

        return $field->default;
    }

    /**
     * Fetch one association property cell with an empty-string default.
     *
     * @return string
     */
    protected static function cell($map, $hierarchy, $ancestor_indexes, $value_index, $property)
    {
        $key = Key_Formatter::build_key(false, $hierarchy, $ancestor_indexes, $value_index, $property);
        return array_key_exists($key, $map) ? $map[$key] : '';
    }

    /**
     * Best-effort read for a field that was never registered with the shim.
     *
     * @return mixed
     */
    protected static function fallback($object_type, $object_id, $field_name)
    {
        $key = Key_Formatter::KEY_PREFIX . $field_name;
        switch ($object_type) {
            case 'post':
                return get_post_meta($object_id, $key, true);
            case 'term':
                return get_term_meta($object_id, $key, true);
            case 'option':
                return get_option($key, '');
        }
        return '';
    }
}

} // end namespace CoptrzTheme\MetaShim

/* ========================================================================= */
/*  Global drop-in functions (root namespace) — direct carbon_* equivalents  */
/* ========================================================================= */

namespace {

    use CoptrzTheme\MetaShim\Reader;

    if (!function_exists('coptrz_get_post_meta')) {
        /**
         * Native replacement for carbon_get_post_meta().
         *
         * @param int    $post_id
         * @param string $field_name  WITHOUT leading underscore.
         * @return mixed
         */
        function coptrz_get_post_meta($post_id, $field_name)
        {
            return Reader::read('post', $post_id, $field_name);
        }
    }

    if (!function_exists('coptrz_get_the_post_meta')) {
        /**
         * Native replacement for carbon_get_the_post_meta() (current post in loop).
         *
         * @param string $field_name
         * @return mixed
         */
        function coptrz_get_the_post_meta($field_name)
        {
            return Reader::read('post', get_the_ID(), $field_name);
        }
    }

    if (!function_exists('coptrz_get_term_meta')) {
        /**
         * Native replacement for carbon_get_term_meta().
         *
         * @param int    $term_id
         * @param string $field_name
         * @return mixed
         */
        function coptrz_get_term_meta($term_id, $field_name)
        {
            return Reader::read('term', $term_id, $field_name);
        }
    }

    if (!function_exists('coptrz_get_theme_option')) {
        /**
         * Native replacement for carbon_get_theme_option().
         *
         * @param string $field_name
         * @return mixed
         */
        function coptrz_get_theme_option($field_name)
        {
            return Reader::read('option', 0, $field_name);
        }
    }

    if (!function_exists('coptrz_get_nav_menu_item_meta')) {
        /**
         * Native replacement for carbon_get_nav_menu_item_meta(). Nav menu item
         * meta is stored as post meta on the menu item, so this reads from the
         * 'post' object type.
         *
         * @param int    $item_id
         * @param string $field_name
         * @return mixed
         */
        function coptrz_get_nav_menu_item_meta($item_id, $field_name)
        {
            return Reader::read('post', $item_id, $field_name);
        }
    }
}
