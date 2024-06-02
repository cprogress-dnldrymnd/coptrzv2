<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Modules 
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<?php
$modules = get__post_meta('modules');
?>
<pre>
    <?php var_dump($modules) ?>
</pre>
<div class="modules">
    <?php
    foreach ($modules as $key => $module) {
        $type = $module['_type'];
        $module_id = 'module-' . get_the_ID() . '-' . $key;
        include locate_template('template-parts/modules/' . $type . '.php');
    }
    ?>
</div>

<?php get_footer(); ?>