<section class="columns <?= $classes ?>" id="<?= $module_id ?>" style="<?= $style ?>">
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
                            <div class="column-holder content-margin">
                                <?= _elements($column['items'], $module_id) ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</section>