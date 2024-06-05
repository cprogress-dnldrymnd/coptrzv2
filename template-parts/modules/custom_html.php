<section class="custom-html position-relative <?= $classes ?>" id="<?= $module_id ?>">
  <?php
  _background_image(array(
    'baground_image' => $baground_image,
    'background_image_class' => $background_image_class,
    'background_overlay_image' => $background_overlay_image,
  ));
  ?>
  <div class="custom-html-holder position-relative">
    <?= $module['custom_html'] ?>
  </div>
</section>