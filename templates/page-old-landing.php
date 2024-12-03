<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Landing 
/* Template Post Type: page, guides
/*-----------------------------------------------------------------------------------*/
?>
<?php
get_header('clean');
?>
<div class="modules">
    <?php
    the_content();
    ?>
</div>

<?php
get_footer('clean');
?>