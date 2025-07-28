<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Gutenberg
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<?php
$SVG = new SVG;
?>
<main id="main" class="page-components">
  <?php
  the_content();
  ?>
  </section>
</main>
<?php get_footer(); ?>