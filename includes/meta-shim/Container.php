<?php
/**
 * Plugin/Snippet Name: Meta Shim - Container
 * Description: Standalone replacement for Carbon Fields' Container. Provides the
 *              chainable Container::make()->where()->add_tab() API so the 7k-line
 *              post-meta.php registers unchanged, and maintains a global FIELD
 *              INDEX (object-type -> root-field-name -> Field descriptor) that the
 *              tree-aware reader and the admin renderer both consult.
 *
 *              This file owns *registration & indexing*. The admin UI rendering
 *              and the byte-exact save routine are attached to this same class in
 *              meta-shim/Container_Admin.php (loaded alongside) to keep concerns
 *              readable; boot() wires both together.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

namespace CoptrzTheme\MetaShim;

if (!defined('ABSPATH')) {
    exit;
}

class Container
{
    /** Map Carbon container types onto our internal object types. */
    const OBJECT_TYPE_MAP = array(
        'post_meta'     => 'post',
        'term_meta'     => 'term',
        'theme_options' => 'option',
        'nav_menu_item' => 'post',
        'user_meta'     => 'user',
        'comment_meta'  => 'comment',
    );

    /** @var Container[] All registered containers (for the admin layer to iterate). */
    public static $containers = array();

    /**
     * Global field index used by the reader & renderer.
     *
     * @var array<string,array<string,Field>> object_type => field_name => Field
     */
    protected static $field_index = array();

    /** @var string Carbon container type. */
    public $type;

    /** @var string Internal object type (post|term|option|user|comment). */
    public $object_type;

    /** @var string Container/meta-box title. */
    public $title;

    /** @var string Unique id derived from the title. */
    public $id;

    /** @var array Ordered tabs: [ ['title'=>..., 'fields'=>Field[]], ... ]. */
    public $tabs = array();

    /** @var Field[] Flat list of every root field across all tabs. */
    public $fields = array();

    /** @var array Display conditions (where/or_where) grouped into OR-sets. */
    public $condition_sets = array();

    /** @var string Meta box context (normal|advanced|side). */
    public $context = 'normal';

    /** @var string Meta box priority (default|high|low|core). */
    public $priority = 'default';

    /**
     * Factory mirroring Carbon Fields' Container::make().
     *
     * @param string $type  post_meta|term_meta|theme_options|nav_menu_item|...
     * @param string $title
     * @return self
     */
    public static function make($type, $title = '')
    {
        $container = new self();
        $container->type        = $type;
        $container->object_type = isset(self::OBJECT_TYPE_MAP[$type]) ? self::OBJECT_TYPE_MAP[$type] : 'post';
        $container->title       = $title;
        $container->id          = sanitize_title($type . '-' . $title . '-' . wp_rand(1000, 9999));

        // Seed a first OR-set so the first where() AND-chains correctly.
        $container->condition_sets[] = array();

        self::$containers[] = $container;
        return $container;
    }

    /* --------------------------------------------------------------------- */
    /*  Display conditions                                                   */
    /* --------------------------------------------------------------------- */

    /**
     * AND a condition onto the current OR-set (Carbon's where()).
     *
     * @param string $key      post_type|post_template|term_taxonomy|...
     * @param string $compare  =|!=|IN|NOT IN
     * @param mixed  $value
     * @return self
     */
    public function where($key, $compare, $value)
    {
        $set_index = count($this->condition_sets) - 1;
        $this->condition_sets[$set_index][] = array(
            'key'     => $key,
            'compare' => $compare,
            'value'   => $value,
        );
        return $this;
    }

    /**
     * Begin a new OR-set then AND this first condition onto it (Carbon's or_where()).
     *
     * @return self
     */
    public function or_where($key, $compare, $value)
    {
        $this->condition_sets[] = array();
        return $this->where($key, $compare, $value);
    }

    /* --------------------------------------------------------------------- */
    /*  Field collection                                                     */
    /* --------------------------------------------------------------------- */

    /**
     * Add a labelled tab of fields (Carbon's add_tab()).
     *
     * @param string  $title
     * @param Field[] $fields
     * @return self
     */
    public function add_tab($title, $fields)
    {
        $fields = array_values(array_filter((array) $fields));
        $this->tabs[] = array('title' => $title, 'fields' => $fields);
        $this->collect($fields);
        return $this;
    }

    /**
     * Add fields directly (no tab) — Carbon's add_fields().
     *
     * @param Field[] $fields
     * @return self
     */
    public function add_fields($fields)
    {
        $fields = array_values(array_filter((array) $fields));
        if (empty($this->tabs)) {
            // Represent the "no tabs" case as a single anonymous tab for the UI.
            $this->tabs[] = array('title' => '', 'fields' => array());
        }
        $tab_index = count($this->tabs) - 1;
        $this->tabs[$tab_index]['fields'] = array_merge($this->tabs[$tab_index]['fields'], $fields);
        $this->collect($fields);
        return $this;
    }

    public function set_context($context)
    {
        $this->context = $context;
        return $this;
    }

    public function set_priority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Graceful no-op for unmodelled chainable Container methods (e.g.
     * set_datastore, set_page_menu_title) so post-meta.php never fatals.
     *
     * @param string $method
     * @param array  $args
     * @return self
     */
    public function __call($method, $args)
    {
        return $this;
    }

    /**
     * Register the given root fields into the flat list and the global index.
     *
     * @param Field[] $fields
     * @return void
     */
    protected function collect($fields)
    {
        foreach ($fields as $field) {
            if (!($field instanceof Field)) {
                continue;
            }
            $this->fields[] = $field;
            // Last registration for a given (object_type, name) wins, which is
            // harmless because identical field names share identical trees.
            self::$field_index[$this->object_type][$field->name] = $field;
        }
    }

    /* --------------------------------------------------------------------- */
    /*  Index lookup API (consumed by meta-reader.php & the renderer)        */
    /* --------------------------------------------------------------------- */

    /**
     * Resolve a registered root Field descriptor.
     *
     * @param string $object_type post|term|option|user|comment
     * @param string $field_name  WITHOUT leading underscore.
     * @return Field|null
     */
    public static function get_field($object_type, $field_name)
    {
        return isset(self::$field_index[$object_type][$field_name])
            ? self::$field_index[$object_type][$field_name]
            : null;
    }

    /**
     * @return array<string,array<string,Field>> The full index (debug/self-test).
     */
    public static function get_index()
    {
        return self::$field_index;
    }
}
