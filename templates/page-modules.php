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
</div>
<?php get_footer(); ?>