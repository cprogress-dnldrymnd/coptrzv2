<?php get_header() ?>
<?php
echo ___hero_modules('text-start', 'small-hero');
?>

<section class="booqable-rental">
  <div class="container">
    <?= do_shortcode(get__post_meta('shortcode')); ?>
  </div>
</section>

<?php
echo do_shortcode(___sections('sections', get_the_ID()));
?>

<?= do_shortcode('[layouts id=292999]') ?>
<?php get_footer() ?>