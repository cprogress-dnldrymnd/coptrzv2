<?php get_header(); ?>

<?php
$args = array(
    'meta_query' => array(
        array(
            'key'   => '_display_location',
            'value' => '404',
        ),
        array(
            'key'   => '_do_not_display_on',
            'value' => '404',
            'compare' => '!='
        ),
    )
);
echo do_shortcode(__layouts($args));
?>

<?php get_footer(); ?>