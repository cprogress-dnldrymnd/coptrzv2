<?php get_header(); ?>

<?php
$args = array(
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key'   => '_display_location',
            'value' => '404',
        ),
    )
);
echo do_shortcode(__layouts($args));
?>

<?php get_footer(); ?>