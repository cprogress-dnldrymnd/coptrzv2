<div class="accordion accordion-v2 accordion-flush" id="accordion-<?= $module_id ?>">
    <?php foreach ($accordion as $key => $accordion_item) { ?>
        <div class="accordion-item">
            <h2 class="accordion-header" id="flush-heading<?= $key ?>">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?= $key ?>" aria-expanded="<?= $key == 0 ? 'true' : 'false' ?>" aria-controls="flush-collapse<?= $key ?>">
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