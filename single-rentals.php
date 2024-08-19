<?php get_header() ?>
<?php
echo ___hero_modules('text-start', 'small-hero');
?>

<section class="booqable-rental md-padding">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6">
        <?= do_shortcode(get__post_meta('shortcode')); ?>
      </div>
      <div class="col-lg-6">
        <h3>Included in the package</h3>
        <?= get__post_meta('included_in_package') ?>
      </div>
    </div>
  </div>
</section>

<?php
echo do_shortcode(___sections('sections', get_the_ID()));
?>

<?= do_shortcode('[layouts id=292999]') ?>
<?php get_footer() ?>