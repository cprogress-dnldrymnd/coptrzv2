<section class="columns <?= $classes ?>" id="<?= $module_id ?>">
    <div class="container">
        <div class="section-heading-description">
            <?= do_shortcode('[_heading heading="' . $module['heading'] . '" class="big-heading"]') ?>
            <?= do_shortcode('[_description description="' . $module['description'] . '" ]') ?>
        </div>

        <div class="column-items">
            <?php if ($module['columns']) { ?>
                <div class="row">
                    <?php foreach ($module['columns'] as $column) { ?>
                        <div class="col">
                            <pre>
                            <?php var_dump($column['items']) ?>
                            </pre>
                          
                            <?= do_shortcode('[_elements data="' . $column['items'] . '"]') ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</section>