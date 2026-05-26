<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Simple Header Footer 
/* Template Post Type: page, guides
/*-----------------------------------------------------------------------------------*/
?>
<?php
get_header('simple');

?>
<div class="modules">
    <?php
    echo do_shortcode(___hero_modules());
    echo do_shortcode(___sections());
    ?>
</div>

<?php
get_footer('simple');
?>