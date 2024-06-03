<section class="columns <?= $classes ?>" id="<?= $module_id ?>" style="<?= $style_attribute ?>">
    <div class="container">
        <?php if ($module['display_heading_description'] && ($module['heading'] || $module['description'])) {  ?>
            <div class="section-heading-description mb-5">
                <?= do_shortcode('[_heading heading="' . $module['heading'] . '" class="big-heading"]') ?>
                <?= do_shortcode("[_description description='" . $module['description'] . "']") ?>
            </div>
        <?php } ?>

        <div class="column-items">
            <?php if ($module['columns']) { ?>
                <div class="row g-4">
                    <?php foreach ($module['columns'] as $column) { ?>
                        <div class="col-sm-12 col-lg">
                            <?php
                            $styles = $column['styles'];
                            $classes = '';
                            $style_attribute = '';
                            if ($styles) {
                                foreach ($styles as $style) {
                                    $style_type = $style['_type'];
                                    switch ($style_type) {
                                        case 'background_color':
                                            if ($style['background_color'] != 'background-custom') {
                                                $classes .= ' ' . $style['background_color'];
                                            } else {
                                                $style_attribute = 'background-color: ' . $style['background_color_custom'] . ';';
                                            }
                                            break;
                                        case 'padding':
                                            $classes .= ' ' . $style['padding_top'] . ' ' . $style['padding_bottom'] . ' ' . $style['padding_left'] . ' ' . $style['padding_right'];
                                            break;
                                        case 'margin':
                                            $classes .= ' ' . $style['margin_top'] . ' ' . $style['margin_bottom'] . ' ' . $style['margin_left'] . ' ' . $style['margin_right'];
                                            break;
                                        case 'border_radius':
                                            $style_attribute .= 'border-radius: ' . $style['border_radius'] . ';';
                                            break;
                                        case 'alignment':
                                            $classes .= ' ' . $style['align_items'] . ' ' . $style['justify_content'] . ' ' . $style['text_align'];
                                            break;
                                        case 'custom_class':
                                            $classes .= ' ' .  $style['custom_class'];
                                            break;
                                    }
                                }
                            }
                            ?>
                            <div class="column-holder h-100 d-flex <?= $classes ?>" style="<?= $style_attribute ?>">
                                <div class="inner content-margin w-100">
                                    <?= _elements($column['items'], $module_id, $module['same_height_images']) ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</section>