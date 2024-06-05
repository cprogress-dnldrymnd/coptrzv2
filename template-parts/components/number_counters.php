<div class="number-counters-holder">
    <div class="row g4">
        <?php foreach ($number_counters as $number_counter) { ?>
            <div class="col-auto">
                <div class="number-counter-box row g-3">
                    <?= do_shortcode('[_image class="col-auto" size="medium" id="' . $number_counter['icon'] . '"]') ?>
                    <div class="number-counter col">
                        <div class="counter">
                            <?= $number_counter['number'] ?>
                        </div>
                        <?= do_shortcode("[_description description='" . _format_text($number_counter['description']) . "']"); ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>