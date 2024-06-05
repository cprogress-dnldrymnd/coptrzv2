<section class="custom-html position-relative <?= $classes ?>" id="<?= $module_id ?>" style="<?= $style_attribute . $container_width_style_attribute ?>">
  <?php
  _background_image(array(
    'baground_image' => $baground_image,
    'background_image_class' => $background_image_class,
    'background_overlay_image' => $background_overlay_image,
  ));
  _section_heading_description(array(
    'heading' => $module['heading'],
    'description' => $module['description'],
    'text_align' => $module['text_align'],
    'heading_prefix' => $module['heading_prefix'],
    'heading_suffix' => $module['heading_suffix'],
    'tag' => $module['tag'],
    'size' => $module['size'],
    'heading_with_line' => $module['heading_with_line'],
  ));
  ?>
  <div class="custom-html-holder position-relative">
    <?= $module['custom_html'] ?>
  </div>
</section>