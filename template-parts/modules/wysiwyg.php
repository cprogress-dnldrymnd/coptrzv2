<section class="wysiwyg position-relative <?= $classes ?> <?= $classes_text_color ?>" style="<?= $style_attribute ?>" id="<?= $module_id ?>">
  <?php
  _background_image(array(
    'baground_image' => $baground_image,
    'background_image_class' => $background_image_class,
    'background_overlay_image' => $background_overlay_image,
  ));

  ?>
  <div class="container position-relative <?= $container_width_class ?> ">
    <?php
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
    <?= do_shortcode(wpautop($module['wysiwyg'])) ?>
  </div>
</section>