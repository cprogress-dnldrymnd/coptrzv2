<?php
/**
 * Plugin/Snippet Name: Meta Shim - View
 * Description: Server-side HTML renderer for the meta shim admin UI. Emits a flat,
 *              framework-free markup contract that assets/js/admin-meta-boxes.js
 *              enhances (tabs, repeaters, media, association search, conditional
 *              logic). Every control's name follows the single convention defined
 *              in Container_Admin so that what is rendered round-trips exactly
 *              through save().
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

namespace CoptrzTheme\MetaShim;

if (!defined('ABSPATH')) {
    exit;
}

class View
{
    /** Placeholder index used inside JS clone templates. */
    const TEMPLATE_INDEX = '__CMSIDX__';

    /**
     * Render a container: tab navigation + one panel per tab.
     *
     * @param Container $container
     * @param string    $object_type
     * @param int       $object_id
     * @return void
     */
    public static function container($container, $object_type, $object_id)
    {
        $tabs    = $container->tabs;
        $multi   = count($tabs) > 1;
        $root_ns = Container_Admin::INPUT_ROOT;

        echo '<div class="cms-container" data-cms-container>';

        if ($multi) {
            echo '<ul class="cms-tabs" role="tablist">';
            foreach ($tabs as $i => $tab) {
                printf(
                    '<li class="cms-tab%s" data-cms-tab="%d">%s</li>',
                    $i === 0 ? ' is-active' : '',
                    $i,
                    esc_html($tab['title'])
                );
            }
            echo '</ul>';
        }

        foreach ($tabs as $i => $tab) {
            printf(
                '<div class="cms-panel%s" data-cms-panel="%d">',
                ($multi && $i !== 0) ? ' is-hidden' : '',
                $i
            );
            foreach ($tab['fields'] as $field) {
                if (!($field instanceof Field)) {
                    continue;
                }
                $value = Reader::read($object_type, $object_id, $field->name);
                self::field($field, $value, $root_ns . '[' . $field->name . ']');
            }
            echo '</div>';
        }

        echo '</div>';
    }

    /* ===================================================================== */
    /*  Field dispatch                                                       */
    /* ===================================================================== */

    /**
     * Render a single field (wrapper + type-specific control).
     *
     * @param Field  $field
     * @param mixed  $value
     * @param string $name   Fully-qualified input name.
     * @return void
     */
    public static function field($field, $value, $name)
    {
        // html fields render their raw markup without a labelled wrapper.
        if ($field->type === 'html') {
            printf(
                '<div class="cms-field cms-field--html %s"%s>%s</div>',
                esc_attr($field->classes),
                self::conditional_attr($field),
                $field->html
            );
            return;
        }

        $style = $field->width ? ' style="width:' . (int) $field->width . '%"' : '';

        printf(
            '<div class="cms-field cms-field--%s %s" data-cms-name="%s"%s%s>',
            esc_attr($field->type),
            esc_attr($field->classes),
            esc_attr($field->name),
            self::conditional_attr($field),
            $style
        );

        if ($field->label !== '' && !in_array($field->type, array('checkbox'), true)) {
            printf('<label class="cms-label">%s</label>', esc_html($field->label));
        }

        switch ($field->storage_kind()) {
            case 'complex':
                self::complex($field, is_array($value) ? $value : array(), $name);
                break;
            case 'association':
                self::association($field, is_array($value) ? $value : array(), $name);
                break;
            case 'multi':
                self::multi($field, (array) $value, $name);
                break;
            default:
                self::scalar($field, $value, $name);
                break;
        }

        if ($field->help_text !== '') {
            printf('<p class="cms-help">%s</p>', wp_kses_post($field->help_text));
        }

        echo '</div>';
    }

    /* ===================================================================== */
    /*  Scalar controls                                                      */
    /* ===================================================================== */

    /**
     * Render scalar field types (text, textarea, select, checkbox, color,
     * image, file, hidden, date_time, header_scripts, footer_scripts).
     *
     * @param Field  $field
     * @param mixed  $value
     * @param string $name
     * @return void
     */
    protected static function scalar($field, $value, $name)
    {
        $attrs = self::attributes($field);

        switch ($field->type) {

            case 'textarea':
            case 'header_scripts':
            case 'footer_scripts':
                printf(
                    '<textarea class="cms-input" name="%s" rows="5"%s>%s</textarea>',
                    esc_attr($name),
                    $attrs,
                    esc_textarea((string) $value)
                );
                break;

            case 'select':
                echo '<select class="cms-input" name="' . esc_attr($name) . '"' . $attrs . '>';
                foreach ($field->options as $opt_val => $opt_label) {
                    printf(
                        '<option value="%s"%s>%s</option>',
                        esc_attr($opt_val),
                        selected((string) $value, (string) $opt_val, false),
                        esc_html($opt_label)
                    );
                }
                echo '</select>';
                break;

            case 'checkbox':
                // Hidden ensures an unchecked box still posts an (empty) value so
                // save() writes the same empty cell Carbon Fields did.
                printf('<input type="hidden" name="%s" value="" />', esc_attr($name));
                printf(
                    '<label class="cms-check"><input type="checkbox" name="%s" value="yes"%s /> %s</label>',
                    esc_attr($name),
                    checked((bool) $value, true, false),
                    esc_html($field->label)
                );
                break;

            case 'color':
                printf(
                    '<input type="text" class="cms-input cms-color" name="%s" value="%s" data-alpha="%s"%s />',
                    esc_attr($name),
                    esc_attr((string) $value),
                    $field->alpha_enabled ? 'true' : 'false',
                    $attrs
                );
                break;

            case 'image':
            case 'file':
                $is_image = ($field->type === 'image');
                $preview  = '';
                if ($value) {
                    $preview = $is_image
                        ? wp_get_attachment_image((int) $value, 'thumbnail')
                        : esc_html(basename((string) get_attached_file((int) $value)));
                }
                echo '<div class="cms-media" data-cms-media="' . ($is_image ? 'image' : 'file') . '">';
                printf('<input type="hidden" class="cms-media__id" name="%s" value="%s" />', esc_attr($name), esc_attr((string) $value));
                echo '<div class="cms-media__preview">' . $preview . '</div>';
                echo '<button type="button" class="button cms-media__select">' . esc_html__('Select', 'coptrz-theme') . '</button> ';
                echo '<button type="button" class="button cms-media__remove">' . esc_html__('Remove', 'coptrz-theme') . '</button>';
                echo '</div>';
                break;

            case 'date_time':
                printf(
                    '<input type="text" class="cms-input cms-datetime" name="%s" value="%s" autocomplete="off"%s />',
                    esc_attr($name),
                    esc_attr((string) $value),
                    $attrs
                );
                break;

            case 'hidden':
                printf('<input type="hidden" name="%s" value="%s" />', esc_attr($name), esc_attr((string) $value));
                break;

            default: // text + any unmodelled scalar
                printf(
                    '<input type="text" class="cms-input" name="%s" value="%s"%s />',
                    esc_attr($name),
                    esc_attr((string) $value),
                    $attrs
                );
                break;
        }
    }

    /* ===================================================================== */
    /*  Multi-value controls (set / multiselect / media_gallery)            */
    /* ===================================================================== */

    /**
     * @param Field    $field
     * @param string[] $values
     * @param string   $name
     * @return void
     */
    protected static function multi($field, $values, $name)
    {
        if ($field->type === 'media_gallery') {
            echo '<div class="cms-gallery" data-cms-gallery data-cms-name="' . esc_attr($name) . '">';
            echo '<div class="cms-gallery__items">';
            foreach ($values as $id) {
                self::gallery_item($name, $id);
            }
            echo '</div>';
            echo '<button type="button" class="button cms-gallery__add">' . esc_html__('Add images', 'coptrz-theme') . '</button>';
            // Clone template for JS.
            echo '<script type="text/html" class="cms-gallery__tpl">';
            self::gallery_item($name, self::TEMPLATE_INDEX);
            echo '</script>';
            echo '</div>';
            return;
        }

        if ($field->type === 'multiselect') {
            echo '<select class="cms-input" name="' . esc_attr($name) . '[]" multiple size="6">';
            foreach ($field->options as $opt_val => $opt_label) {
                printf(
                    '<option value="%s"%s>%s</option>',
                    esc_attr($opt_val),
                    in_array((string) $opt_val, array_map('strval', $values), true) ? ' selected' : '',
                    esc_html($opt_label)
                );
            }
            echo '</select>';
            return;
        }

        // set -> checkbox list.
        echo '<div class="cms-set">';
        foreach ($field->options as $opt_val => $opt_label) {
            printf(
                '<label class="cms-check"><input type="checkbox" name="%s[]" value="%s"%s /> %s</label>',
                esc_attr($name),
                esc_attr($opt_val),
                in_array((string) $opt_val, array_map('strval', $values), true) ? ' checked' : '',
                esc_html($opt_label)
            );
        }
        echo '</div>';
    }

    /**
     * One media-gallery thumbnail + hidden id input.
     *
     * @param string     $name
     * @param int|string $id
     * @return void
     */
    protected static function gallery_item($name, $id)
    {
        $img = is_numeric($id) ? wp_get_attachment_image((int) $id, 'thumbnail') : '';
        echo '<span class="cms-gallery__item">';
        echo $img;
        printf('<input type="hidden" name="%s[]" value="%s" />', esc_attr($name), esc_attr((string) $id));
        echo '<button type="button" class="cms-gallery__remove">&times;</button>';
        echo '</span>';
    }

    /* ===================================================================== */
    /*  Association control                                                  */
    /* ===================================================================== */

    /**
     * @param Field $field
     * @param array $items  Array of ['value','type','subtype','id'].
     * @param string $name
     * @return void
     */
    protected static function association($field, $items, $name)
    {
        $max = $field->max ? (int) $field->max : 0;

        printf(
            '<div class="cms-assoc" data-cms-assoc data-cms-types="%s" data-cms-max="%d" data-cms-name="%s" data-cms-field="%s">',
            esc_attr(wp_json_encode($field->association_types)),
            $max,
            esc_attr($name),
            esc_attr($field->name)
        );

        echo '<ul class="cms-assoc__selected">';
        foreach ($items as $item) {
            self::association_item($name, $item['value'], self::association_label($item));
        }
        echo '</ul>';

        echo '<div class="cms-assoc__search">';
        echo '<input type="text" class="cms-assoc__input" placeholder="' . esc_attr__('Search…', 'coptrz-theme') . '" autocomplete="off" />';
        echo '<ul class="cms-assoc__results"></ul>';
        echo '</div>';

        // JS clone template for a freshly-picked item.
        echo '<script type="text/html" class="cms-assoc__tpl">';
        self::association_item($name, '{{value}}', '{{label}}');
        echo '</script>';

        echo '</div>';
    }

    /**
     * One selected association row (hidden token + label + remove).
     *
     * @param string $name
     * @param string $token  "post:subtype:id"
     * @param string $label
     * @return void
     */
    protected static function association_item($name, $token, $label)
    {
        echo '<li class="cms-assoc__item">';
        printf('<input type="hidden" name="%s[]" value="%s" />', esc_attr($name), esc_attr($token));
        echo '<span class="cms-assoc__label">' . esc_html($label) . '</span>';
        echo '<button type="button" class="cms-assoc__remove" aria-label="Remove">&times;</button>';
        echo '</li>';
    }

    /**
     * Resolve a human label for a stored association item.
     *
     * @param array $item
     * @return string
     */
    protected static function association_label($item)
    {
        if (empty($item['id'])) {
            return $item['value'];
        }
        if ($item['type'] === 'term') {
            $term = get_term((int) $item['id']);
            return ($term && !is_wp_error($term)) ? $term->name : $item['value'];
        }
        $title = get_the_title((int) $item['id']);
        return $title !== '' ? $title . ' (' . $item['subtype'] . ')' : $item['value'];
    }

    /* ===================================================================== */
    /*  Complex (repeater) control                                          */
    /* ===================================================================== */

    /**
     * @param Field $field
     * @param array $rows  Array of group rows (each with '_type' + sub-values).
     * @param string $name
     * @return void
     */
    protected static function complex($field, $rows, $name)
    {
        $group_names = array_keys($field->groups);
        $multi_group = $field->has_named_groups();

        printf(
            '<div class="cms-complex" data-cms-complex data-cms-collapsed="%s" data-cms-max="%d" data-cms-duplicate="%s" data-cms-header="%s">',
            $field->collapsed ? '1' : '0',
            $field->max ? (int) $field->max : 0,
            $field->duplicate_groups_allowed ? '1' : '0',
            esc_attr($field->header_template)
        );

        echo '<div class="cms-complex__rows">';
        foreach ($rows as $i => $row) {
            $group_type = isset($row['_type']) ? $row['_type'] : (isset($group_names[0]) ? $group_names[0] : '_');
            self::complex_row($field, $group_type, $row, $name . '[' . $i . ']', false);
        }
        echo '</div>';

        // One hidden clone template per defined group.
        echo '<div class="cms-complex__templates" hidden>';
        foreach ($group_names as $group_type) {
            self::complex_row($field, $group_type, array(), $name . '[' . self::TEMPLATE_INDEX . ']', true);
        }
        echo '</div>';

        // Add controls.
        echo '<div class="cms-complex__add">';
        if ($multi_group) {
            foreach ($group_names as $group_type) {
                printf(
                    '<button type="button" class="button cms-complex__add-btn" data-cms-group="%s">%s</button> ',
                    esc_attr($group_type),
                    esc_html(self::group_label($field, $group_type, 'add'))
                );
            }
        } else {
            printf(
                '<button type="button" class="button button-primary cms-complex__add-btn" data-cms-group="%s">%s</button>',
                esc_attr(isset($group_names[0]) ? $group_names[0] : '_'),
                esc_html(self::group_label($field, '_', 'add'))
            );
        }
        echo '</div>';

        echo '</div>';
    }

    /**
     * Render one complex row (real or template).
     *
     * @param Field  $field
     * @param string $group_type
     * @param array  $row
     * @param string $name
     * @param bool   $is_template
     * @return void
     */
    protected static function complex_row($field, $group_type, $row, $name, $is_template)
    {
        $collapsed = $field->collapsed && !$is_template ? ' is-collapsed' : '';

        printf(
            '<div class="cms-row%s" data-cms-row data-cms-group="%s">',
            $collapsed,
            esc_attr($group_type)
        );

        // Row header: drag handle, live title, collapse/duplicate/delete.
        echo '<div class="cms-row__head">';
        echo '<span class="cms-row__handle" title="Drag to reorder">⋮⋮</span>';
        echo '<span class="cms-row__title">' . esc_html(self::row_header($field, $group_type, $row)) . '</span>';
        echo '<span class="cms-row__actions">';
        if ($field->duplicate_groups_allowed) {
            echo '<button type="button" class="cms-row__dup" title="Duplicate">⧉</button>';
        }
        echo '<button type="button" class="cms-row__toggle" title="Collapse">▾</button>';
        echo '<button type="button" class="cms-row__del" title="Delete">&times;</button>';
        echo '</span>';
        echo '</div>';

        // Stored group type.
        printf('<input type="hidden" class="cms-row__type" name="%s[_type]" value="%s" />', esc_attr($name), esc_attr($group_type));

        echo '<div class="cms-row__body">';
        foreach ($field->get_group_fields($group_type) as $sub) {
            if (!($sub instanceof Field)) {
                continue;
            }
            $sub_value = (!$is_template && isset($row[$sub->name])) ? $row[$sub->name] : $sub->default;
            self::field($sub, $sub_value, $name . '[' . $sub->name . ']');
        }
        echo '</div>';

        echo '</div>';
    }

    /* ===================================================================== */
    /*  Small helpers                                                        */
    /* ===================================================================== */

    /**
     * Evaluate a complex header template (`<%- field %>`) against a row for the
     * initial server paint; the JS keeps it live thereafter.
     *
     * @param Field  $field
     * @param string $group_type
     * @param array  $row
     * @return string
     */
    protected static function row_header($field, $group_type, $row)
    {
        $label = self::group_label($field, $group_type, 'singular');
        if ($field->header_template === '' || empty($row)) {
            return $label;
        }
        $rendered = preg_replace_callback('/<%[-=]\s*(\w+)\s*%>/', function ($m) use ($row) {
            return isset($row[$m[1]]) && is_scalar($row[$m[1]]) ? (string) $row[$m[1]] : '';
        }, $field->header_template);
        $rendered = trim(wp_strip_all_tags($rendered));
        return $rendered !== '' ? $rendered : $label;
    }

    /**
     * Derive a label for a group (from setup_labels(), the group name, or type).
     *
     * @param Field  $field
     * @param string $group_type
     * @param string $context add|singular
     * @return string
     */
    protected static function group_label($field, $group_type, $context)
    {
        if ($group_type !== '_' && $field->has_named_groups()) {
            $name = ucwords(str_replace(array('_', '-'), ' ', $group_type));
        } elseif (!empty($field->labels['singular_name'])) {
            $name = $field->labels['singular_name'];
        } elseif (!empty($field->labels['plural_name'])) {
            $name = $field->labels['plural_name'];
        } else {
            $name = $field->label ?: 'Row';
        }
        return $context === 'add' ? ('Add ' . $name) : $name;
    }

    /**
     * Build escaped HTML attributes from a field's attributes/required flags.
     *
     * @param Field $field
     * @return string Leading-space-prefixed attribute string.
     */
    protected static function attributes($field)
    {
        $out = '';
        foreach ($field->attributes as $k => $v) {
            $out .= ' ' . esc_attr($k) . '="' . esc_attr($v) . '"';
        }
        if ($field->required) {
            $out .= ' required';
        }
        return $out;
    }

    /**
     * Emit the conditional-logic data attribute consumed by the JS visibility
     * engine.
     *
     * @param Field $field
     * @return string
     */
    protected static function conditional_attr($field)
    {
        if (empty($field->conditional_logic)) {
            return '';
        }
        return ' data-cms-conditional="' . esc_attr(wp_json_encode($field->conditional_logic)) . '"';
    }
}
