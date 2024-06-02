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
function modules_styles() 
add_action('modules_styles',)
?>
<?php get_footer(); ?>