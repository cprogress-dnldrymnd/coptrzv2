<?php
$hide_on_list = get__post_meta('hide_on_list');
if ($hide_on_list) {
    get_header('landing-v2');
} else {
    get_header();
}
?>
<?php
if (false === get_template_part('template-parts/single/single', get_post_type())) {
    echo ___hero_modules('text-start', 'small-hero');
    echo do_shortcode(get_post_meta(get_the_ID(), '_sections_html', true));
}
?>

<?php
global $popups_id;
$popups = array_unique($popups_id);
var_dump($popups);
foreach ($popups as $popup) {
    echo __popup($popup);
}

if ($hide_on_list) {
    get_footer('landing');
} else {
    get_footer();
}
?>