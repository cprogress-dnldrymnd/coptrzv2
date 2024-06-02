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
    <?php
    var_dump($modules);
    ?>
    </pre>
    <?php
    foreach ($modules as $module) {
        $type = $module['_type'];
        include locate_template('template-parts/modules/' . $type . '.php');

        add_filter('wp_head', 'sdsdsdsds');
    }
    ?>
</div>
<?php get_footer(); ?>