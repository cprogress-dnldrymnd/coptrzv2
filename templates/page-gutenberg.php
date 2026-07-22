<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Gutenberg
/* Template Post Type: page, industries, guides, capabilities
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
if (function_exists('coptrz_sections_render_converted') && coptrz_sections_render_converted(get_the_ID())) {
  // Section-converter output: render the frozen blocks WITHOUT the_content's
  // wpautop (which mangles the frozen markup) — the same path the Modules
  // template routes through, so a converted page renders identically here.
  echo coptrz_render_converted_sections('sections', get_the_ID());
} else {
  the_content();
}
?>
</section>

<?php get_footer(); ?>