<?php
/**
 * Class newPostType
 * * Handles the registration of Custom Post Types.
 */
class newPostType
{
    public $name;
    public $singular_name;
    public $icon;
    public $supports;
    public $rewrite;
    public $show_in_rest = false;
    public $exclude_from_search = false;
    public $publicly_queryable = true;
    public $show_in_admin_bar = true;
    public $has_archive = true;
    public $hierarchical = false;
    public $text_domain = 'coptrz-theme';

    /**
     * Constructor for the Post Type Registrar.
     *
     * @param array $config Array of properties to hydrate the class with.
     */
    function __construct($config = array())
    {
        // Hydrate class properties dynamically from the passed configuration array
        if (!empty($config)) {
            foreach ($config as $property => $value) {
                if (property_exists($this, $property)) {
                    $this->$property = $value;
                }
            }
        }
        
        add_action('init', array($this, 'create_post_type'));
    }

    /**
     * Callback for the 'init' hook to register the post type.
     *
     * @return void
     */
    function create_post_type()
    {
        register_post_type(
            strtolower($this->name),
            array(
                'labels'              => array(
                    'name'               => _x($this->name, 'post type general name', $this->text_domain),
                    'singular_name'      => _x($this->singular_name, 'post type singular name', $this->text_domain),
                    'menu_name'          => _x($this->name, 'admin menu', $this->text_domain),
                    'name_admin_bar'     => _x($this->singular_name, 'add new on admin bar', $this->text_domain),
                    'add_new'            => _x('Add New', strtolower($this->name), $this->text_domain),
                    'add_new_item'       => __('Add New ' . $this->singular_name, $this->text_domain),
                    'new_item'           => __('New ' . $this->singular_name, $this->text_domain),
                    'edit_item'          => __('Edit ' . $this->singular_name, $this->text_domain),
                    'view_item'          => __('View ' . $this->singular_name, $this->text_domain),
                    'view_items'         => __('View ' . $this->name, $this->text_domain),
                    'all_items'          => __('All ' . $this->name, $this->text_domain),
                    'search_items'       => __('Search ' . $this->name, $this->text_domain),
                    'parent_item_colon'  => __('Parent :' . $this->name, $this->text_domain),
                    'not_found'          => __('No ' . strtolower($this->name) . ' found.', $this->text_domain),
                    'not_found_in_trash' => __('No ' . strtolower($this->name) . ' found in Trash.', $this->text_domain)
                ),
                'show_in_rest'        => $this->show_in_rest,
                'supports'            => $this->supports,
                'public'              => true,
                'has_archive'         => $this->has_archive,
                'hierarchical'        => $this->hierarchical,
                'rewrite'             => $this->rewrite,
                'menu_icon'           => $this->icon,
                'capability_type'     => 'page',
                'exclude_from_search' => $this->exclude_from_search,
                'publicly_queryable'  => $this->publicly_queryable,
                'show_in_admin_bar'   => $this->show_in_admin_bar,
            )
        );
    }
}

/*-----------------------------------------------------------------------------------*/
/* Taxonomy
/*-----------------------------------------------------------------------------------*/

/**
 * Class newTaxonomy
 * * Handles the registration of custom Taxonomies and applies custom admin column filters.
 */
class newTaxonomy
{
    public $taxonomy;
    public $post_type;
    public $args;

    /**
     * Constructor for the Taxonomy Registrar.
     *
     * @param string $taxonomy  The taxonomy slug.
     * @param string $post_type The post type slug to attach the taxonomy to.
     * @param array  $args      Taxonomy registration arguments.
     */
    function __construct($taxonomy = '', $post_type = '', $args = array())
    {
        if (!empty($taxonomy) && !empty($post_type)) {
            $this->taxonomy = $taxonomy;
            $this->post_type = $post_type;
            $this->args = $args;
        }

        add_action('init', array($this, 'create_taxonomy'));
        add_action('restrict_manage_posts', array($this, 'filter_by_taxonomy'), 10, 2);
        
        // Ensure properties exist before hooking into dynamic strings
        if (!empty($this->post_type)) {
            add_filter('manage_' . $this->post_type . '_posts_columns', array($this, 'change_table_column_titles'));
            add_filter('manage_' . $this->post_type . '_posts_custom_column', array($this, 'change_column_rows'), 10, 2);
            add_filter('manage_edit-' . $this->post_type . '_sortable_columns', array($this, 'change_sortable_columns'));
        }
    }

    /**
     * Callback for the 'init' hook to register the taxonomy.
     *
     * @return void
     */
    function create_taxonomy()
    {
        register_taxonomy($this->taxonomy, $this->post_type, $this->args);
    }

    /**
     * Generates standard taxonomy labels to keep configurations DRY.
     *
     * @param string $plural   Plural name of the taxonomy.
     * @param string $singular Singular name of the taxonomy.
     * @return array Array of formatted labels.
     */
    public static function generate_labels($plural, $singular)
    {
        return array(
            'name'                       => _x($plural, 'Taxonomy General Name', 'text_domain'),
            'singular_name'              => _x($singular, 'Taxonomy Singular Name', 'text_domain'),
            'menu_name'                  => __($singular, 'text_domain'),
            'all_items'                  => __('All Items', 'text_domain'),
            'parent_item'                => __('Parent Item', 'text_domain'),
            'parent_item_colon'          => __('Parent Item:', 'text_domain'),
            'new_item_name'              => __('New Item Name', 'text_domain'),
            'add_new_item'               => __('Add New Item', 'text_domain'),
            'edit_item'                  => __('Edit Item', 'text_domain'),
            'update_item'                => __('Update Item', 'text_domain'),
            'view_item'                  => __('View Item', 'text_domain'),
            'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
            'add_or_remove_items'        => __('Add or remove items', 'text_domain'),
            'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
            'popular_items'              => __('Popular Items', 'text_domain'),
            'search_items'               => __('Search Items', 'text_domain'),
            'not_found'                  => __('Not Found', 'text_domain'),
            'no_terms'                   => __('No items', 'text_domain'),
            'items_list'                 => __('Items list', 'text_domain'),
            'items_list_navigation'      => __('Items list navigation', 'text_domain'),
        );
    }

    /**
     * Outputs a select dropdown in the admin area to filter posts by this taxonomy.
     *
     * @param string $post_type The current post type being viewed.
     * @param string $which     The location of the extra table nav markup.
     * @return void
     */
    function filter_by_taxonomy($post_type, $which)
    {
        // Apply this only on a specific post type
        if ($this->post_type !== $post_type)
            return;

        // A list of taxonomy slugs to filter by
        $taxonomies = array($this->taxonomy);

        foreach ($taxonomies as $taxonomy_slug) {
            // Retrieve taxonomy data
            $taxonomy_obj = get_taxonomy($taxonomy_slug);
            $taxonomy_name = $taxonomy_obj->labels->name;

            // Retrieve taxonomy terms
            $terms = get_terms($taxonomy_slug);

            // Display filter HTML
            echo "<select name='{$taxonomy_slug}' id='{$taxonomy_slug}' class='postform'>";
            echo '<option value="">' . sprintf(esc_html__('Show All %s', 'text_domain'), $taxonomy_name) . '</option>';
            foreach ($terms as $term) {
                printf(
                    '<option value="%1$s" %2$s>%3$s (%4$s)</option>',
                    $term->slug,
                    ((isset($_GET[$taxonomy_slug]) && ($_GET[$taxonomy_slug] == $term->slug)) ? ' selected="selected"' : ''),
                    $term->name,
                    $term->count
                );
            }
            echo '</select>';
        }
    }

    /**
     * Modifies the table columns in the WordPress admin to include the taxonomy.
     *
     * @param array $columns Existing columns.
     * @return array Modified columns.
     */
    function change_table_column_titles($columns)
    {
        unset($columns['date']); // temporarily remove, to have custom column before date column
        $columns[$this->taxonomy] = $this->args['label'];
        $columns['date'] = 'Date'; // readd the date column
        return $columns;
    }

    /**
     * Populates the custom taxonomy column rows in the WordPress admin.
     *
     * @param string $column_name Name of the column being evaluated.
     * @param int    $post_id     ID of the post.
     * @return void
     */
    function change_column_rows($column_name, $post_id)
    {
        if ($column_name == $this->taxonomy) {
            echo get_the_term_list($post_id, $this->taxonomy, '', ', ', '') . PHP_EOL;
        }
    }

    /**
     * Makes the custom taxonomy column sortable.
     *
     * @param array $columns Sortable columns.
     * @return array Modified sortable columns.
     */
    function change_sortable_columns($columns)
    {
        $columns[$this->taxonomy] = $this->taxonomy;
        return $columns;
    }
}


/*===================================================================================
 * Instantiations
 *===================================================================================*/

// Testimonials
$Testimonials = new newPostType(array(
    'name'                => 'Testimonials',
    'singular_name'       => 'Testimonial',
    'icon'                => 'dashicons-testimonial',
    'supports'            => array('title', 'revisions'),
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'show_in_admin_bar'   => false,
    'has_archive'         => false,
    'show_in_rest'        => true
));

$Testimonial_Category = new newTaxonomy('testimonial_category', 'testimonials', array(
    'label'        => 'Testimonial Categories',
    'labels'       => newTaxonomy::generate_labels('Testimonial Categories', 'Testimonial Category'),
    'hierarchical' => true,
    'query_var'    => true,
    'show_in_rest' => true,
));

// FAQs
$FAQs = new newPostType(array(
    'name'                => 'FAQ',
    'singular_name'       => 'FAQ',
    'icon'                => 'dashicons-info',
    'supports'            => array('title', 'revisions', 'editor'),
    'exclude_from_search' => true,
    'publicly_queryable'  => false,
    'show_in_admin_bar'   => false,
    'has_archive'         => false
));

$FAQs_Category = new newTaxonomy('faqs_category', 'faq', array(
    'label'        => 'FAQs Categories',
    'labels'       => newTaxonomy::generate_labels('FAQs Categories', 'FAQs Category'),
    'hierarchical' => true,
    'query_var'    => true,
    'show_in_rest' => true,
));

// Team
$Team = new newPostType(array(
    'name'                => 'Team',
    'singular_name'       => 'Team',
    'icon'                => 'dashicons-groups',
    'supports'            => array('title', 'revisions', 'thumbnail'),
    'exclude_from_search' => true,
    'publicly_queryable'  => false,
    'show_in_admin_bar'   => false,
    'has_archive'         => false
));

$Team_Category = new newTaxonomy('team_category', 'team', array(
    'label'        => 'Team Categories',
    'labels'       => newTaxonomy::generate_labels('Team Categories', 'Team Category'),
    'show_in_rest' => true,
    'hierarchical' => true,
    'query_var'    => false,
    'has_archive'  => false,
));

// Case Studies
$Case_Studies = new newPostType(array(
    'name'          => 'Case Studies',
    'singular_name' => 'Case Study',
    'icon'          => 'dashicons-media-text',
    'supports'      => array('title', 'revisions', 'thumbnail', 'editor', 'excerpt'),
    'show_in_rest'  => true,
    'rewrite'       => array('with_front' => false, 'slug' => 'case-studies')
));

$Case_Study_Category = new newTaxonomy('casestudies_category', 'casestudies', array(
    'label'        => 'Case Study Categories',
    'labels'       => newTaxonomy::generate_labels('Case Study Categories', 'Case Study Category'),
    'show_in_rest' => true,
    'hierarchical' => true,
    'query_var'    => true,
    'rewrite'      => array('with_front' => false, 'slug' => 'case-study-category')
));

// Guides
$Guides = new newPostType(array(
    'name'          => 'Guides',
    'singular_name' => 'Guide',
    'icon'          => 'dashicons-index-card',
    'supports'      => array('title', 'revisions', 'editor', 'thumbnail', 'excerpt', 'author'),
    'show_in_rest'  => true,
    'rewrite'       => array('with_front' => false, 'slug' => 'guides')
));

$Guide_Category = new newTaxonomy('guides_category', 'guides', array(
    'label'        => 'Guide Categories',
    'labels'       => newTaxonomy::generate_labels('Guide Categories', 'Guide Category'),
    'show_in_rest' => true,
    'hierarchical' => true,
    'query_var'    => true,
    'rewrite'      => array('with_front' => false, 'slug' => 'guide-category')
));

// Industries
$Industry = new newPostType(array(
    'name'          => 'Industries',
    'singular_name' => 'Industry',
    'icon'          => 'dashicons-portfolio',
    'supports'      => array('title', 'revisions', 'editor', 'thumbnail', 'excerpt', 'author'),
    'show_in_rest'  => true,
    'rewrite'       => array('with_front' => false, 'slug' => 'industry-solutions')
));

// Capabilities
$Capabilities = new newPostType(array(
    'name'          => 'Capabilities',
    'singular_name' => 'Capability',
    'icon'          => 'dashicons-screenoptions',
    'supports'      => array('title', 'revisions', 'editor', 'thumbnail', 'excerpt', 'author'),
    'show_in_rest'  => true,
    'rewrite'       => array('with_front' => false, 'slug' => 'capabilities')
));

// Events
$Events = new newPostType(array(
    'name'          => 'Events',
    'singular_name' => 'Event',
    'icon'          => 'dashicons-cover-image',
    'supports'      => array('title', 'revisions', 'editor', 'thumbnail'),
    'show_in_rest'  => true,
    'rewrite'       => array('with_front' => false, 'slug' => 'events')
));

$Events_Location = new newTaxonomy('events_location', 'events', array(
    'label'        => 'Events Location',
    'labels'       => newTaxonomy::generate_labels('Events Locations', 'Events Location'),
    'rewrite'      => array('slug' => 'events-location'),
    'hierarchical' => true,
    'query_var'    => true,
    'has_archive'  => true,
    'show_in_rest' => true,
));

$Events_Category = new newTaxonomy('events_category', 'events', array(
    'label'        => 'Events Categories',
    'labels'       => newTaxonomy::generate_labels('Events Categories', 'Events Category'),
    'rewrite'      => array('slug' => 'events-category'),
    'show_in_rest' => true,
    'hierarchical' => true,
    'query_var'    => true,
    'has_archive'  => true,
));

$Events_Type = new newTaxonomy('events_type', 'events', array(
    'label'        => 'Events Type',
    'labels'       => newTaxonomy::generate_labels('Events Types', 'Events Type'),
    'rewrite'      => array('slug' => 'events-type'),
    'show_in_rest' => true,
    'hierarchical' => true,
    'query_var'    => true,
    'has_archive'  => true,
));

// Layouts
$Layouts = new newPostType(array(
    'name'                => 'Layouts',
    'singular_name'       => 'Layout',
    'icon'                => 'dashicons-layout',
    'supports'            => array('title', 'revisions', 'page-attributes', 'editor'),
    'exclude_from_search' => true,
    'publicly_queryable'  => true,
    'show_in_admin_bar'   => true,
    'has_archive'         => false,
    'show_in_rest'        => true
));

// Popups
$Popup = new newPostType(array(
    'name'                => 'Popups',
    'singular_name'       => 'Popup',
    'icon'                => 'dashicons-media-default',
    'supports'            => array('title', 'revisions', 'thumbnail', 'editor'),
    'exclude_from_search' => true,
    'publicly_queryable'  => true,
    'show_in_admin_bar'   => true,
    'has_archive'         => false,
    'show_in_rest'        => true
));

// Compare Products
$Product_compare = new newPostType(array(
    'name'                => 'Compare Products',
    'singular_name'       => 'Compare Product',
    'icon'                => 'dashicons-columns',
    'supports'            => array('title', 'revisions', 'excerpt'),
    'exclude_from_search' => true,
    'publicly_queryable'  => true,
    'show_in_admin_bar'   => false,
    'has_archive'         => true,
    'show_in_rest'        => false
));

// Product Taxonomy Pages
$Product_taxonomy_page = new newPostType(array(
    'name'                => 'Product Taxonomy Pages',
    'singular_name'       => 'Product Taxonomy Pages', // Kept identical to original request
    'icon'                => 'dashicons-products',
    'supports'            => array('title', 'revisions', 'excerpt', 'editor', 'page-attributes'),
    'exclude_from_search' => true,
    'publicly_queryable'  => true,
    'show_in_admin_bar'   => false,
    'has_archive'         => true,
    'show_in_rest'        => true
));

// Global Post Boxes
$Global_Post_Boxes = new newPostType(array(
    'name'                => 'Global Post Boxes',
    'singular_name'       => 'Global Post Box',
    'icon'                => 'dashicons-admin-site-alt3',
    'show_in_admin_bar'   => false,
    'publicly_queryable'  => false,
    'has_archive'         => false,
    'supports'            => array('title', 'revisions', 'editor', 'thumbnail', 'page-attributes'),
    'show_in_rest'        => false
));

$Global_Post_Boxes_Taxonomy = new newTaxonomy('global_post_boxes_category', 'globalpostboxes', array(
    'label'        => 'Categories',
    'labels'       => newTaxonomy::generate_labels('Categories', 'Category'),
    'hierarchical' => true,
    'query_var'    => true,
    'show_in_rest' => true,
));

// Rentals
$Rentals = new newPostType(array(
    'name'          => 'Rentals',
    'singular_name' => 'Rental',
    'icon'          => 'dashicons-portfolio',
    'supports'      => array('title', 'revisions', 'editor', 'thumbnail', 'excerpt', 'author'),
    'show_in_rest'  => true,
    'rewrite'       => array('with_front' => false, 'slug' => 'rental')
));

// Landing Pages
$Landing_Page = new newPostType(array(
    'name'          => 'Landing Pages',
    'singular_name' => 'Landing Page',
    'icon'          => 'dashicons-portfolio',
    'supports'      => array('title', 'revisions', 'editor', 'thumbnail', 'excerpt', 'author'),
    'show_in_rest'  => true,
    'rewrite'       => array('with_front' => false, 'slug' => 'landing-page')
));

/*
$Quiz = new newPostType(array(
    'name'                => 'Quiz',
    'singular_name'       => 'Quiz',
    'icon'                => 'dashicons-testimonial',
    'supports'            => array('title', 'revisions', 'editor'),
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'show_in_admin_bar'   => true,
    'has_archive'         => false,
    'show_in_rest'        => true
));
*/

// Documents
$Documents = new newPostType(array(
    'name'                => 'Documents',
    'singular_name'       => 'Document',
    'icon'                => 'dashicons-portfolio',
    'supports'            => array('title', 'revisions', 'thumbnail'),
    'show_in_admin_bar'   => false,
    'publicly_queryable'  => false,
    'has_archive'         => false,
    'show_in_rest'        => false
));