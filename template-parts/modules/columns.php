<section class="columns <?= $classes ?>" id="<?= $module_id ?>" style="<?= $style_attribute ?>">
    <div class="container <?= $container_width_class ?>">
        <?php
        _section_heading_description(array(
            'display_heading_description' => $module['display_heading_description'],
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
        <div class="column-items" style="<?= $container_width_style_attribute ?>">
            <?php if ($module['columns']) { ?>
                <div class="row g-4 <?= $classes_row ?>">
                    <?php foreach ($module['columns'] as $column) { ?>
                        <?php
                        $styles = $column['styles'];
                        $classes = '';
                        $style_attribute = '';
                        $style_attribute_inner = '';
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
                                    case 'max_width':
                                        $style_attribute_inner .= 'max-width: ' . $style['max_width'] . ';';
                                        break;
                                    case 'column_width':
                                        $column_class =  ($style['column_width'] ? $style['column_width'] : 'col-lg') . ' ' . $style['column_width_tablet'] . ' ' . ($style['column_width_mobile'] ? $style['column_width_mobile'] : 'col-sm-12');
                                        break;
                                }
                            }
                        }
                        ?>
                        <div class="<?= $column_class ? $column_class : 'col-lg' ?>">
                            <div class="column-holder h-100 d-flex overflow-hidden <?= $classes ?>" style="<?= $style_attribute ?>">
                                <div class="inner content-margin w-100" style="<?= $style_attribute_inner ?>">
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