<div class="number-counters-holder">
    <div class="row g4">
        <?php foreach ($number_counters as $number_counter) { ?>
            <div class="col-auto">
                <div class="number-counter-box row g-3">
                    <?= do_shortcode('[_image class="col-auto" size="medium" id="' . $number_counter['icon'] . '"]') ?>
                    <div class="number-counter col">
                        <div class="counter">
                            <?php if ($number_counter['prefix']) { ?>
                                <span class="prefix"><?= $number_counter['prefix'] ?></span>
                            <?php } ?>

                            <span class="counter-number">
                                <?= $number_counter['number'] ?>
                            </span>
                            <?php if ($number_counter['suffix']) { ?>
                                <span class="suffix"><?= $number_counter['suffix'] ?></span>
                            <?php } ?>
                        </div>
                        <?= do_shortcode("[_description description='" . _format_text($number_counter['description']) . "']"); ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>