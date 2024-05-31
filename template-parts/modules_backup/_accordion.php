<?php
$DisplayData = new DisplayData;
$Helpers = new Helpers;
$GetData = new GetData;
$accordion_items = $module['accordion_items'];
$disable_module = $module['disable_module'];
if (!$disable_module) { ?>
    <section <?= $GetData->get_attributes('accordion md-padding', $module_id, $template_class) ?>>
        <?php if ($template_class) { ?>
            <?= $Helpers->get_edit_url('post.php?post=' . $postid . '&action=edit', 'Edit Template') ?>
        <?php } ?>
        <div class="container">
            <div class="accordion-box accordion-no-icon">
                <div class="accordion accordion-flush" id="accordionRight-<?= $section_id ?>">
                    <?php foreach ($accordion_items as $key => $accordion_item) { ?>
                        <div class="accordion-item" data-aos="flip-up" <?= $Helpers->data_aos_delay(count($accordion_items), $key, 100) ?>>
                            <h2 class="accordion-header" id="flush-heading<?= $key ?>">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?= $key ?>" aria-expanded="<?= $key == 0 ? 'true' : 'false' ?>" aria-controls="flush-collapse<?= $key ?>">
                                    <span><?= $accordion_item['heading'] ?></span>
                                    <span class="plus-minus"></span>

                                </button>
                            </h2>
                            <div id="flush-collapse<?= $key ?>" class="accordion-collapse collapse <?= $key == 0 ? 'show' : '' ?>" aria-labelledby="flush-heading<?= $key ?>" data-bs-parent="#accordionRight-<?= $section_id ?>">
                                <?php
                                $DisplayData->description(array(
                                    'description' => $accordion_item['description']
                                ), 'accordion-body light-color medium-text');
                                ?>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </div>
    </section>
<?php } ?>