<?php get_header() ?>
<?php
$data_id = get__post_meta('data_id');
echo ___hero_modules('text-start', 'small-hero');
?>

<section class="booqable-rental md-padding">
  <div class="container">
    <?= do_shortcode('[booqable_detail id="rental-dji-matrice-350-rtk-bundle"]'); ?>
  </div>
</section>

<?php
echo do_shortcode(___sections('sections', get_the_ID()));
?>

<?= do_shortcode('[layouts id=292999]') ?>
<?php get_footer() ?>