<!DOCTYPE html>
<html <?php language_attributes(); ?> class="html">

<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="author" content="">
    <meta name="format-detection" content="telephone=no">
    <title>
        <?php bloginfo('name'); // show the blog name, from settings 
        ?> |
        <?php is_front_page() ? bloginfo('description') : wp_title(''); // if we're on the home page, show the description, from the site's settings - otherwise, show the title of the post or page 
        ?>
    </title>
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

    <?php wp_head(); ?>
</head>
<?php
$copy_from = $_GET['copy_from'];
$copy_after = $_GET['copy_after'];
$training = $_GET['training'];
$software = $_GET['software'];
$accessories = $_GET['accessories'];
$drones = $_GET['drones'];
if ($copy_from) {
    $sections = get__post_meta_by_id($copy_from, 'sections');
    carbon_set_post_meta(get_the_ID(), 'sections', $sections);

    if ($copy_after == 'true') {
        $sections_after_main = get__post_meta_by_id($copy_from, 'sections_after_main');
        carbon_set_post_meta(get_the_ID(), 'sections_after_main', $sections_after_main);
    }


    if ($training == 'true') {
        $related_training = get__post_meta_by_id($copy_from, 'related_training');
        carbon_set_post_meta(get_the_ID(), 'related_training', $related_training);
    }

    if ($software == 'true') {
        $related_software = get__post_meta_by_id($copy_from, 'softwares');
        carbon_set_post_meta(get_the_ID(), 'softwares', $related_software);
    }

    if ($drones == 'true') {
        $drones = get__post_meta_by_id($copy_from, 'drones');
        carbon_set_post_meta(get_the_ID(), 'drones', $drones);
    }

    if ($accessories == 'true') {
        $accessories = get__post_meta_by_id($copy_from, 'accessories');
        carbon_set_post_meta(get_the_ID(), 'accessories', $accessories);
    }
}

?>
<body <?php body_class(); ?>>
    <?php
    echo do_shortcode(get_post_meta(get_the_ID(), '_sections_html', true));

    ?>
    <?php wp_footer(); ?>
</body>