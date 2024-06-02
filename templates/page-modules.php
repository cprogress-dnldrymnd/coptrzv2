<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Modules 
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<?php
$modules = get__post_meta('modules');
?>
<div class="modules">
    <pre>

    </pre>
    <?php
    foreach ($modules as $key => $module) {
        $type = $module['_type'];
        $module_id = 'module-' . $key;
        include locate_template('template-parts/modules/' . $type . '.php');
        $styles[$module_id] = $module['styles'];
    }
    ?>
 
</div>
<?php get_footer(); ?>