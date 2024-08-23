<?php get_header() ?>
<?php
echo ___hero_modules('text-start', 'small-hero');
?>
<?php
echo do_shortcode(___sections('sections', get_the_ID()));
?>

<section class="booqable-rental md-padding">
  <div class="container">
    <?= do_shortcode(get__post_meta('shortcode')); ?>
  </div>
</section>


<?= do_shortcode('[layouts id=292999]') ?>
<?php get_footer() ?>