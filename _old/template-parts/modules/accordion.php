<section class="accordion-section position-relative <?= $classes ?>" id="<?= $module_id ?>" style="<?= $style_attribute  ?>">
  <?php
  _background_image(array(
    'baground_image' => $baground_image,
    'background_image_class' => $background_image_class,
    'background_overlay_image' => $background_overlay_image,
  ));
  ?>
  <div class="container position-relative <?= $container_width_class ?> <?= $classes_text_color ?>" style="<?= $container_width_style_attribute ?>">
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

    $accordion = $module['accordion'];
    $accordion_source = $module['accordion_source'];
    $faqs = $module['faqs'];
    $open_first_item = $module['open_first_item'];
    $faqs_category = $module['faqs_category'];
    if ($accordion || $faqs || $faqs_category) {
      include locate_template('template-parts/components/accordion.php');
    }
    ?>
  </div>
</section>