<section class="accordion-section position-relative <?= $classes ?>" id="<?= $module_id ?>" style="<?= $style_attribute . $container_width_style_attribute ?>">
  <?php
  _background_image(array(
    'baground_image' => $baground_image,
    'background_image_class' => $background_image_class,
    'background_overlay_image' => $background_overlay_image,
  ));
  ?>
  <div class="container position-relative <?= $container_width_class ?> <?= $classes_text_color ?>">
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
    <div class="accordion accordion-v2 accordion-flush" id="accordion-<?= $module_id ?>">
      <?php foreach ($module['accordion'] as $key => $accordion_item) { ?>
        <div class="accordion-item">
          <h2 class="accordion-header" id="flush-heading<?= $key ?>">
            <button class="accordion-button justify-content-between p-0 <?= $key == 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?= $key ?>" aria-expanded="<?= $key == 0 ? 'true' : 'false' ?>" aria-controls="flush-collapse<?= $key ?>">
              <span>
                <?= do_shortcode('[_heading heading="' . $accordion_item['heading'] . '" tag="h4"]') ?>
              </span>
              <span class="plus-minus"></span>
            </button>
          </h2>
          <div id="flush-collapse<?= $key ?>" class="accordion-collapse collapse <?= $key == 0 ? 'show' : '' ?>" aria-labelledby="flush-heading<?= $key ?>" data-bs-parent="#accordion-<?= $module_id ?>">
            <?= do_shortcode('[_description description="' . $accordion_item['description'] . '" ]') ?>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</section>