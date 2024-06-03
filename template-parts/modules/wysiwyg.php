<section class="wysiwyg position-relative <?= $classes ?>" <?= $style_attribute ?> id="<?= $module_id ?>">
  <?php if ($baground_image) { ?>
    <?= do_shortcode('[_image class="background-image" id="' . $baground_image . '"]'); ?>
  <?php } ?>
  <div class="container position-relative">
    <?= do_shortcode(wpautop($module['wysiwyg'])) ?>
  </div>
</section>