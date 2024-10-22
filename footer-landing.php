<?php
global $layouts_global;
$hidden_layouts = get__post_meta('hidden_layouts');
$args = array(
    'numberposts' => -1,
    'post_type' => 'layouts',
    'fields' => 'ids',
    'exclude' => $hidden_layouts,
    'orderby' => 'menu_order',
    'order' => 'ASC',
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key' => '_display_location',
            'value' => 'before_footer',
        ),
    ),
);

$layouts = get_posts($args);
foreach ($layouts as $layout) {
    $do_not_display_on = get__post_meta_by_id($layout, 'do_not_display_on');
    if (is_404()) {
        if ($do_not_display_on != '404') {
            echo do_shortcode("[layouts id='$layout']");
            $layouts_global[] = $layout;
        }
    } else if (is_post_type_archive()) {
        $post_type = get_queried_object()->name;
        if ($do_not_display_on != $post_type) {
            echo do_shortcode("[layouts id='$layout']");
            $layouts_global[] = $layout;
        }
    } else {
        echo do_shortcode("[layouts id='$layout']");
        $layouts_global[] = $layout;
    }
}
?>
</main>
<?php wp_footer(); ?>
</body>

</html>