<?php
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


    function __construct()
    {

        add_action('init', array($this, 'create_post_type'));
    }


    function create_post_type()
    {
        register_post_type(
            strtolower($this->name),
            array(
                'labels'              => array(
                    'name'               => _x($this->name, 'post type general name', $this->text_domain),
                    'singular_name'      => _x($this->singular_name, 'post type singular name', $this->text_domain),
                    'menu_name'          => _x($this->name, 'admin menu'),
                    $this->text_domain,
                    'name_admin_bar'     => _x($this->singular_name, 'add new on admin bar', $this->text_domain),
                    'add_new'            => _x('Add New', strtolower($this->name), $this->text_domain),
                    'add_new_item'       => __('Add New ' . $this->singular_name, $this->text_domain),
                    'new_item'           => __('New ' . $this->singular_name, $this->text_domain),
                    'edit_item'          => __('Edit ' . $this->singular_name, $this->text_domain),
                    'view_item'          => __('View ' . $this->singular_name, $this->text_domain),
                    'view_items'          => __('View ' . $this->name, $this->text_domain),
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
class newTaxonomy
{
    public $taxonomy;
    public $post_type;
    public $args;

    function __construct()
    {
        add_action('init', array($this, 'create_taxonomy'));
        add_action('restrict_manage_posts', array($this, 'filter_by_taxonomy'), 10, 2);
        add_filter('manage_' . $this->post_type . '_posts_columns', array($this, 'change_table_column_titles'));
        add_filter('manage_' . $this->post_type . '_posts_custom_column', array($this, 'change_column_rows'), 10, 2);
        add_filter('manage_edit-' . $this->post_type . '_sortable_columns', array($this, 'change_sortable_columns'));
    }

    function create_taxonomy()
    {
        register_taxonomy($this->taxonomy, $this->post_type, $this->args);
    }

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
    function change_table_column_titles($columns)
    {
        unset($columns['date']); // temporarily remove, to have custom column before date column
        $columns[$this->taxonomy] = $this->args['label'];
        $columns['date'] = 'Date'; // readd the date column
        return $columns;
    }

    function change_column_rows($column_name, $post_id)
    {
        if ($column_name == $this->taxonomy) {
            echo get_the_term_list($post_id, $this->taxonomy, '', ', ', '') . PHP_EOL;
        }
    }

    function change_sortable_columns($columns)
    {
        $columns[$this->taxonomy] = $this->taxonomy;
        return $columns;
    }
}




$Testimonials = new newPostType();
$Testimonials->name = 'Testimonials';
$Testimonials->singular_name = 'Testimonial';
$Testimonials->icon = 'dashicons-testimonial';
$Testimonials->supports = array('title', 'revisions');
$Testimonials->exclude_from_search = true;
$Testimonials->publicly_queryable = false;
$Testimonials->show_in_admin_bar = false;
$Testimonials->has_archive = false;


$Testimonial_Category = new newTaxonomy();
$Testimonial_Category->taxonomy = 'testimonial_category';
$Testimonial_Category->post_type = 'testimonials';
$Testimonial_Category->args = array(
    'label'        => 'Testimonial Categories',
    'labels' => array(
        'name'                       => _x('Testimonial Categories', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Testimonial Category', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Testimonial Category', 'text_domain'),
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
    ),
    'hierarchical' => true,
    'query_var'    => true,
    'show_in_rest' => true,

);


$FAQs = new newPostType();
$FAQs->name = 'FAQ';
$FAQs->singular_name = 'FAQ';
$FAQs->icon = 'dashicons-info';
$FAQs->supports = array('title', 'revisions', 'editor');
$FAQs->exclude_from_search = true;
$FAQs->publicly_queryable = false;
$FAQs->show_in_admin_bar = false;
$FAQs->has_archive = false;


$FAQs_Category = new newTaxonomy();
$FAQs_Category->taxonomy = 'faqs_category';
$FAQs_Category->post_type = 'faq';
$FAQs_Category->args = array(
    'label'        => 'FAQs Categories',
    'labels' => array(
        'name'                       => _x('FAQs Categories', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('FAQs Category', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('FAQs Category', 'text_domain'),
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
    ),
    'hierarchical' => true,
    'query_var'    => true,
    'show_in_rest' => true,

);


$Team = new newPostType();
$Team->name = 'Team';
$Team->singular_name = 'Team';
$Team->icon = 'dashicons-groups';
$Team->supports = array('title', 'revisions', 'thumbnail');
$Team->exclude_from_search = true;
$Team->publicly_queryable = false;
$Team->show_in_admin_bar = false;
$Team->has_archive = false;


$Case_Studies = new newPostType();
$Case_Studies->name = 'Case Studies';
$Case_Studies->singular_name = 'Case Study';
$Case_Studies->icon = 'dashicons-media-text';
$Case_Studies->supports = array('title', 'revisions', 'thumbnail', 'editor', 'excerpt');
$Case_Studies->show_in_rest = true;
$Case_Studies->rewrite = array(
    'with_front' => false,
    'slug' => 'case-studies'
);


$Guides = new newPostType();
$Guides->name = 'Guides';
$Guides->singular_name = 'Guide';
$Guides->icon = 'dashicons-index-card';
$Guides->supports = array('title', 'revisions', 'editor', 'thumbnail', 'excerpt', 'author');
$Guides->show_in_rest = true;
$Guides->rewrite = array(
    'with_front' => false,
    'slug' => 'guides'
);



$Industry = new newPostType();
$Industry->name = 'Industries';
$Industry->singular_name = 'Industry';
$Industry->icon = 'dashicons-portfolio';
$Industry->supports = array('title', 'revisions', 'editor', 'thumbnail', 'excerpt', 'author');
$Industry->show_in_rest = true;
$Industry->rewrite = array(
    'with_front' => false,
    'slug' => 'industry-solutions'
);


$Capabilities = new newPostType();
$Capabilities->name = 'Capabilities';
$Capabilities->singular_name = 'Capability';
$Capabilities->icon = 'dashicons-screenoptions';
$Capabilities->supports = array('title', 'revisions', 'editor', 'thumbnail', 'excerpt', 'author');
$Capabilities->show_in_rest = true;
$Capabilities->rewrite = array(
    'with_front' => false,
    'slug' => 'capabilities'
);


$Events = new newPostType();
$Events->name = 'Events';
$Events->singular_name = 'Event';
$Events->icon = 'dashicons-cover-image';
$Events->supports = array('title', 'revisions', 'editor', 'thumbnail');
$Events->show_in_rest = true;
$Events->rewrite = array(
    'with_front' => false,
    'slug' => 'events'
);

$Events_Location = new newTaxonomy();
$Events_Location->taxonomy = 'events_location';
$Events_Location->post_type = 'events';
$Events_Location->args = array(
    'label'        => 'Events Location',
    'labels' => array(
        'name'                       => _x('Events Locations', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Events Location', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Events Location', 'text_domain'),
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
    ),
    'rewrite'      => array('slug' => 'events-location'),
    'hierarchical' => true,
    'query_var'    => true,
    'has_archive'  => true,
    'show_in_rest' => true,
);

$Events_Category = new newTaxonomy();
$Events_Category->taxonomy = 'events_category';
$Events_Category->post_type = 'events';
$Events_Category->args = array(
    'label'        => 'Events Categories',
    'labels' => array(
        'name'                       => _x('Events Categories', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Events Category', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Events Category', 'text_domain'),
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
    ),
    'rewrite'      => array('slug' => 'events-category'),
    'show_in_rest' => true,
    'hierarchical' => true,
    'query_var'    => true,
    'has_archive'  => true,
    'show_in_rest' => true,
);

$Events_Type = new newTaxonomy();
$Events_Type->taxonomy = 'events_type';
$Events_Type->post_type = 'events';
$Events_Type->args = array(
    'label'        => 'Events Type',
    'labels' => array(
        'name'                       => _x('Events Types', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Events Type', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Events Type', 'text_domain'),
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
    ),
    'rewrite'      => array('slug' => 'events-type'),
    'show_in_rest' => true,
    'hierarchical' => true,
    'query_var'    => true,
    'has_archive'  => true,
    'show_in_rest' => true,
);


$Team_Category = new newTaxonomy();
$Team_Category->taxonomy = 'team_category';
$Team_Category->post_type = 'team';
$Team_Category->args = array(
    'label'        => 'Team Categories',
    'labels' => array(
        'name'                       => _x('Team Categories', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Team Category', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Team Category', 'text_domain'),
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
    ),
    'show_in_rest' => true,
    'hierarchical' => true,
    'query_var'    => false,
    'has_archive'  => false,
);



$Case_Study_Category = new newTaxonomy();
$Case_Study_Category->taxonomy = 'casestudies_category';
$Case_Study_Category->post_type = 'casestudies';
$Case_Study_Category->args = array(
    'label'        => 'Case Study Categories',
    'labels' => array(
        'name'                       => _x('Case Study Categories', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Case Study Category', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Case Study Category', 'text_domain'),
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
    ),
    'show_in_rest' => true,
    'hierarchical' => true,
    'query_var'    => true,
    'rewrite'      => array(
        'with_front' => false,
        'slug'         => 'case-study-category',
    )
);


$Guide_Category = new newTaxonomy();
$Guide_Category->taxonomy = 'guides_category';
$Guide_Category->post_type = 'guides';
$Guide_Category->args = array(
    'label'        => 'Guide Categories',
    'labels' => array(
        'name'                       => _x('Guide Categories', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Guide Category', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Guide Category', 'text_domain'),
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
    ),
    'show_in_rest' => true,
    'hierarchical' => true,
    'query_var'    => true,
    'rewrite'      => array(
        'with_front' => false,
        'slug'         => 'guide-category',
    )
);



$Layouts = new newPostType();
$Layouts->name = 'Layouts';
$Layouts->singular_name = 'Layout';
$Layouts->icon = 'dashicons-layout';
$Layouts->supports = array('title', 'revisions', 'page-attributes', 'editor');
$Layouts->exclude_from_search = true;
$Layouts->publicly_queryable = true;
$Layouts->show_in_admin_bar = true;
$Layouts->has_archive = false;
$Layouts->show_in_rest = true;




$Popup = new newPostType();
$Popup->name = 'Popups';
$Popup->singular_name = 'Popup';
$Popup->icon = 'dashicons-media-default';
$Popup->supports = array('title', 'revisions', 'thumbnail', 'editor');
$Popup->exclude_from_search = true;
$Popup->publicly_queryable = true;
$Popup->show_in_admin_bar = true;
$Popup->has_archive = false;
$Popup->show_in_rest = true;


$Product_compare = new newPostType();
$Product_compare->name = 'Compare Products';
$Product_compare->singular_name = 'Compare Product';
$Product_compare->icon = 'dashicons-columns';
$Product_compare->supports = array('title', 'revisions', 'excerpt');
$Product_compare->exclude_from_search = true;
$Product_compare->publicly_queryable = true;
$Product_compare->show_in_admin_bar = false;
$Product_compare->has_archive = true;
$Product_compare->show_in_rest = false;


$Product_taxonomy_page = new newPostType();
$Product_taxonomy_page->name = 'Product Taxonomy Pages';
$Product_taxonomy_page->singular_name = 'Product Taxonomy Pages';
$Product_taxonomy_page->icon = 'dashicons-products';
$Product_taxonomy_page->supports = array('title', 'revisions', 'excerpt', 'editor');
$Product_taxonomy_page->exclude_from_search = true;
$Product_taxonomy_page->publicly_queryable = true;
$Product_taxonomy_page->show_in_admin_bar = false;
$Product_taxonomy_page->has_archive = true;
$Product_taxonomy_page->show_in_rest = true;

$Global_Post_Boxes = new newPostType();
$Global_Post_Boxes->name = 'Global Post Boxes';
$Global_Post_Boxes->singular_name = 'Global Post Box';
$Global_Post_Boxes->icon = 'dashicons-admin-site-alt3';
$Global_Post_Boxes->show_in_admin_bar = false;
$Global_Post_Boxes->publicly_queryable = false;
$Global_Post_Boxes->has_archive = false;
$Global_Post_Boxes->supports = array('title', 'revisions', 'editor', 'thumbnail', 'page-attributes');
$Global_Post_Boxes->show_in_rest = false;


$Global_Post_Boxes = new newTaxonomy();
$Global_Post_Boxes->taxonomy = 'global_post_boxes_category';
$Global_Post_Boxes->post_type = 'globalpostboxes';
$Global_Post_Boxes->args = array(
    'label'        => 'Categories',
    'labels' => array(
        'name'                       => _x('Categories', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Category', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Category', 'text_domain'),
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
    ),
    'hierarchical' => true,
    'query_var'    => true,
    'show_in_rest' => true,
);

$Rentals = new newPostType();
$Rentals->name = 'Rentals';
$Rentals->singular_name = 'Rental';
$Rentals->icon = 'dashicons-portfolio';
$Rentals->supports = array('title', 'revisions', 'editor', 'thumbnail', 'excerpt', 'author');
$Rentals->show_in_rest = true;
$Rentals->rewrite = array(
    'with_front' => false,
    'slug' => 'rental'
);


$Landing_Page = new newPostType();
$Landing_Page->name = 'Landing Pages';
$Landing_Page->singular_name = 'Landing Page';
$Landing_Page->icon = 'dashicons-portfolio';
$Landing_Page->supports = array('title', 'revisions', 'editor', 'thumbnail', 'excerpt', 'author');
$Landing_Page->show_in_rest = true;
$Landing_Page->rewrite = array('slug' => false);

function na_remove_slug($post_link, $post, $leavename)
{

	if ('landingpages' != $post->post_type || 'publish' != $post->post_status) {
		return $post_link;
	}

	$post_link = str_replace('/' . $post->post_type . '/', '/', $post_link);

	return $post_link;
}
add_filter('post_type_link', 'na_remove_slug', 10, 3);

function na_parse_request($query)
{

	if (!$query->is_main_query() || 2 != count($query->query) || !isset($query->query['page'])) {
		return;
	}

	if (!empty($query->query['name'])) {
		$query->set('post_type', array('post', 'landingpages', 'page'));
	}
}
add_action('pre_get_posts', 'na_parse_request');