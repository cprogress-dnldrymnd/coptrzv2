<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Modules 
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<?php
$modules = get__post_meta('modules');
global $styles;
?>
<div class="modules">
    <?php
    foreach ($modules as $key => $module) {
        $type = $module['_type'];
        $module_id = 'module-' . $key;
        include locate_template('template-parts/modules/' . $type . '.php');
    }
    ?>
</div>

<?php
add_action('wp_head',)
?>
<?php get_footer(); ?>