<section class="columns <?= $classes ?>" id="<?= $module_id ?>" style="<?= $style_attribute ?>">
    <div class="container">
        <div class="section-heading-description">
            <?= do_shortcode('[_heading heading="' . $module['heading'] . '" class="big-heading"]') ?>
            <?= do_shortcode('[_description description="' . $module['description'] . '" ]') ?>
        </div>

        <div class="column-items">
            <?php if ($module['columns']) { ?>
                <div class="row align-items-center">
                    <?php foreach ($module['columns'] as $column) { ?>
                        <div class="col">
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
                                            }
                                            else {
                                                $style_attribute = 'background-color: ' . $style['background_color_custom'] . ';';
                                            }
                                            break;
                                        case 'border_radius':
                                            $style_attribute .= 'border-radius: ' . $style['border_radius'] . ';';
                                            break;
                                    }
                                }
                            }
                            ?>
                            <div class="column-holder content-margin <?= $classes ?>" style="<?= $style_attribute ?>">
                                <?= _elements($column['items'], $module_id) ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</section>