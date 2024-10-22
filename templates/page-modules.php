<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Modules 
/* Template Post Type: page, guides
/*-----------------------------------------------------------------------------------*/
?>
<?php
$hide_on_list = get__post_meta('hide_on_list');
if ($hide_on_list) {
    get_header('landing-v2');
} else {
    get_header();
}
?>
<div class="modules">
    <?php
    echo ___hero_modules();
    echo do_shortcode(get_post_meta(get_the_ID(), '_sections_html', true));
    ?>
</div>

<?php
if ($hide_on_list) {
    get_footer('landing');
} else {
    get_footer();
}
?>