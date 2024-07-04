<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Modules 
/* Template Post Type: page, coptrztemplates
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<?php
$modules = get__post_meta('modules');
?>

<iframe src="https://www.youtube.com/embed/b74ehKejCLs?si=f55i9YxclcAiOMI7?autoplay=1&amp;controls=0&amp;showinfo=0&amp;mute=1&amp;loop=1"></iframe>
<div class="modules">
    <?php
    the_content();
    ?>
</div>

<?php get_footer(); ?>