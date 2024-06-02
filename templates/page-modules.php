<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Modules 
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<?php
$modules = get__post_meta('modules');
$styles = array();
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
function modules_styles($styles)
{
?>
    <style id="module-styles">
        <?php var_dump($styles) ?>
    </style>
<?php
}
add_action('modules_styles', 'modules_styles');
?>
<?php get_footer(); ?>