<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Gutenberg
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<?php
$SVG = new SVG;
?>
<div class="modules">
  <?php
  echo ___hero_modules();
  ?>
</div>
<?php
the_content();
?>
</section>

<?php get_footer(); ?>