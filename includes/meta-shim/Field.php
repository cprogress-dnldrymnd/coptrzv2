<?php
/**
 * Plugin/Snippet Name: Meta Shim - Field
 * Description: Pure field *descriptor* with a chainable API matching Carbon
 *              Fields 3 (Field::make(...)->set_*(...)). It stores no data and
 *              renders nothing by itself; it is the single source of truth about
 *              a field's type, storage behaviour and (for complex fields) the
 *              ordered tree of sub-groups. Both the reader and the admin UI walk
 *              these descriptors.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

namespace CoptrzTheme\MetaShim;

if (!defined('ABSPATH')) {
    exit;
}

class Field
{
    /** Field types that persist MULTIPLE scalar value-rows (array of strings). */
    const MULTI_TYPES = array('set', 'multiselect', 'media_gallery');

    /** Field types that render UI but never persist a value. */
    const DISPLAY_TYPES = array('html', 'separator');

    /** @var string Carbon field type (text, select, complex, association, ...). */
    public $type;

    /** @var string Field name WITHOUT the leading underscore. */
    public $name;

    /** @var string Human label shown in the editor. */
    public $label;

    /** @var string CSS classes applied to the field wrapper. */
    public $classes = '';

    /** @var string Bootstrap-ish width hint (0-100); 0 = auto/full. */
    public $width = 0;

    /** @var string Raw HTML for `html` fields. */
    public $html = '';

    /** @var mixed Default value used when nothing is stored. */
    public $default = '';

    /** @var array Option map (value => label) for choice fields. */
    public $options = array();

    /** @var array Arbitrary input attributes. */
    public $attributes = array();

    /** @var string Inline help text. */
    public $help_text = '';

    /** @var bool Required flag. */
    public $required = false;

    /** @var array Carbon-style conditional logic ruleset. */
    public $conditional_logic = array();

    /** @var bool Enable alpha channel on color fields. */
    public $alpha_enabled = false;

    /** @var int|null Max selectable/associable items (association/complex). */
    public $max = null;

    /** @var int|null Min rows (complex). */
    public $min = null;

    /** @var array Association source definition (post/term types). */
    public $association_types = array();

    /** @var array Extra WP_Query/get_terms args merged into this association's options query. */
    public $options_query = array();

    /** @var string Underscore.js header template for complex rows. */
    public $header_template = '';

    /** @var bool Whether complex rows start collapsed. */
    public $collapsed = false;

    /** @var bool Whether duplicating multi-group rows is permitted. */
    public $duplicate_groups_allowed = true;

    /** @var string Complex layout (grid|tabbed-horizontal|tabbed-vertical). */
    public $layout = 'grid';

    /** @var array Labels from setup_labels(). */
    public $labels = array();

    /**
     * @var array<string,Field[]> Ordered map of group-name => sub-fields.
     *           Default (single) group is keyed '_' exactly like Carbon Fields.
     */
    public $groups = array();

    /**
     * Factory mirroring Carbon Fields' Field::make().
     *
     * @param string      $type
     * @param string      $name
     * @param string|null $label
     * @return self
     */
    public static function make($type, $name, $label = null)
    {
        $field = new self();
        $field->type  = $type;
        $field->name  = $name;
        $field->label = ($label === null) ? self::humanize($name) : $label;
        return $field;
    }

    /* --------------------------------------------------------------------- */
    /*  Chainable setters (CF3 parity)                                       */
    /* --------------------------------------------------------------------- */

    public function set_classes($classes)
    {
        $this->classes = trim($this->classes . ' ' . $classes);
        return $this;
    }

    public function set_width($width)
    {
        $this->width = (int) $width;
        return $this;
    }

    public function set_html($html)
    {
        $this->html = $html;
        return $this;
    }

    public function set_default_value($value)
    {
        $this->default = $value;
        return $this;
    }

    public function set_options($options)
    {
        // Accept either an array or a callable returning an array (CF parity).
        $this->options = is_callable($options) ? call_user_func($options) : (array) $options;
        return $this;
    }

    public function add_options($options)
    {
        $this->options = $this->options + (array) $options;
        return $this;
    }

    public function set_attribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    public function set_help_text($text)
    {
        $this->help_text = $text;
        return $this;
    }

    public function set_required($required = true)
    {
        $this->required = (bool) $required;
        return $this;
    }

    public function set_conditional_logic($rules)
    {
        $this->conditional_logic = (array) $rules;
        return $this;
    }

    public function set_alpha_enabled($enabled = true)
    {
        $this->alpha_enabled = (bool) $enabled;
        return $this;
    }

    public function set_max($max)
    {
        $this->max = (int) $max;
        return $this;
    }

    public function set_min($min)
    {
        $this->min = (int) $min;
        return $this;
    }

    public function set_types($types)
    {
        $this->association_types = (array) $types;
        return $this;
    }

    /**
     * Restrict an association field's picker options with extra query args
     * (e.g. tax_query / post_status), the native replacement for Carbon Fields'
     * carbon_fields_association_field_options_* filters. The args are merged into
     * the options WP_Query (posts) / get_terms (terms) by Container_Admin::ajax_search().
     *
     * NOTE: resolved server-side via the root field index, so this applies to ROOT
     * association fields only (the index does not hold fields nested in a complex).
     *
     * @param array $args WP_Query / get_terms argument overrides.
     * @return self
     */
    public function set_options_query(array $args)
    {
        $this->options_query = $args;
        return $this;
    }

    public function set_header_template($template)
    {
        $this->header_template = $template;
        return $this;
    }

    public function set_collapsed($collapsed = true)
    {
        $this->collapsed = (bool) $collapsed;
        return $this;
    }

    public function set_duplicate_groups_allowed($allowed)
    {
        $this->duplicate_groups_allowed = (bool) $allowed;
        return $this;
    }

    public function set_layout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    public function setup_labels($labels)
    {
        $this->labels = (array) $labels;
        return $this;
    }

    /**
     * Define complex sub-fields.
     *
     *   add_fields(array $fields)              -> default group "_"
     *   add_fields(string $group, array $fields) -> named group
     *
     * Named groups encode the row `_type` exactly as Carbon Fields does, which
     * is how section_items distinguishes heading / post_grid / buttons rows.
     *
     * @return self
     */
    public function add_fields($group_or_fields, $fields = null)
    {
        if (is_string($group_or_fields)) {
            $group_name = $group_or_fields;
            $group_fields = (array) $fields;
        } else {
            $group_name = '_';
            $group_fields = (array) $group_or_fields;
        }

        if (isset($this->groups[$group_name])) {
            $this->groups[$group_name] = array_merge($this->groups[$group_name], $group_fields);
        } else {
            $this->groups[$group_name] = $group_fields;
        }

        return $this;
    }

    /**
     * Graceful fallback for any chainable CF setter not explicitly modelled
     * (e.g. set_value_type, set_visible_in_rest_api). Keeps post-meta.php from
     * fatally erroring while remaining a no-op that preserves chaining.
     *
     * @param string $method
     * @param array  $args
     * @return self
     */
    public function __call($method, $args)
    {
        if (strpos($method, 'set_') === 0 || strpos($method, 'add_') === 0) {
            return $this;
        }
        return $this;
    }

    /* --------------------------------------------------------------------- */
    /*  Introspection helpers used by the reader & UI                        */
    /* --------------------------------------------------------------------- */

    /** @return bool */
    public function is_complex()
    {
        return $this->type === 'complex';
    }

    /** @return bool */
    public function is_association()
    {
        return $this->type === 'association';
    }

    /** @return bool Stores an ordered array of scalar value-rows. */
    public function is_multivalue()
    {
        return in_array($this->type, self::MULTI_TYPES, true);
    }

    /** @return bool Renders but stores nothing. */
    public function is_display_only()
    {
        return in_array($this->type, self::DISPLAY_TYPES, true);
    }

    /**
     * Classify how this field persists, so the reader/writer can branch once.
     *
     * @return string complex|association|multi|none|scalar
     */
    public function storage_kind()
    {
        if ($this->is_complex())     return 'complex';
        if ($this->is_association()) return 'association';
        if ($this->is_multivalue())  return 'multi';
        if ($this->is_display_only()) return 'none';
        return 'scalar';
    }

    /**
     * Resolve the field list for a given stored group `_type`. Falls back to the
     * default group, then to the first defined group, so unknown/legacy group
     * names still read something sensible.
     *
     * @param string $group_type
     * @return Field[]
     */
    public function get_group_fields($group_type)
    {
        if (isset($this->groups[$group_type])) {
            return $this->groups[$group_type];
        }
        if (isset($this->groups['_'])) {
            return $this->groups['_'];
        }
        return empty($this->groups) ? array() : reset($this->groups);
    }

    /** @return bool True when this complex defines more than the default group. */
    public function has_named_groups()
    {
        return !isset($this->groups['_']) && count($this->groups) > 0
            ? true
            : (count($this->groups) > 1);
    }

    /**
     * Convert a snake_case / kebab name into a Title Case label.
     *
     * @param string $name
     * @return string
     */
    protected static function humanize($name)
    {
        return ucwords(str_replace(array('_', '-'), ' ', $name));
    }
}
