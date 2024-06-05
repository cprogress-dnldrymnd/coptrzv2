<section class="wysiwyg position-relative <?= $classes ?>" style="<?= $style_attribute ?>" id="<?= $module_id ?>">
  <?php
  _background_image(array(
    'baground_image' => $baground_image,
    'background_image_class' => $background_image_class,
    'background_overlay_image' => $background_overlay_image,
  ));
  ?>
  <div class="container position-relative <?= $container_width_class ?>">
    <?= do_shortcode(wpautop($module['wysiwyg'])) ?>
  </div>
</section>