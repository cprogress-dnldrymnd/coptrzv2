<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Training 
/* Template Post Type: product
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<div class="modules">
    <?php
    echo ___hero_modules();
    echo do_shortcode(___sections('sections', get_the_ID()));
    ?>
</div>

<section class="training-product lg-padding-top lg-padding-bottom border-top-default" id="Book-Course">
    <input type="hidden" name="product_id" value="<?= get_the_ID() ?>">
    <div class="container">
        <h2 class="text-center">Book a GVC <br> Training Course</h2>
        <div class="post-archive-header">
            <div class="container">
                <div class="inner border-bottom-default sm-padding-bottom sm-margin-bottom">
                    <div class="row g-3 justify-content-between align-items-end">
                        <div class="col-auto">
                            <div class="event-filter">
                                <p class="fw-medium medium-text">Select delivery method:</p>
                                <div class="filter-box bg-light rounded-corner">
                                    <div class="row">
                                        <div class="col-auto">
                                            <input name="delivery_method" value="online-self-paced" type="radio" id="online" checked>
                                            <label class="rounded-corner trigger-training-ajax" for="online">Online Self-paced</label>
                                        </div>
                                        <div class="col-auto">
                                            <input name="delivery_method" value="classroom" type="radio" id="classroom">
                                            <label class="rounded-corner trigger-training-ajax" for="classroom">Classroom</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="row g-3 align-items-center">
                                <div class="col-auto col-location d-none">

                                    <?php
                                    global $product;
                                    $children = $product->get_children();
                                    $locations = [];
                                    foreach ($children as $child) {
                                        $variation = wc_get_product($child);
                                        $product_attribute = $variation->get_attributes();
                                        foreach ($product_attribute as $key => $attr) {
                                            if ($key == 'pa_location') {
                                                $locations[] = $attr;
                                            }
                                        }
                                    }
                                    $locations = array_unique($locations);
                                    ?>

                                    <select name="location" class="trigger-training-ajax-select">
                                        <option value="">Location: All</option>
                                        <?php foreach ($locations as $location) { ?>
                                            <?php if ($location != 'online') { ?>
                                                <option value="<?= $location ?>" class="text-capitalize"><?= $location ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <select name="sort" class="trigger-training-ajax-select">
                                        <option value="latest">Sort By: Latest</option>
                                        <option value="oldest">Sort By: Oldest</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="training-list ajax-loading">
            <div class="row">
                <div class="col-lg-6">
                    <div class="loading-results p-5 text-center"> <svg class="spin" xmlns="http://www.w3.org/2000/svg" id="Group_27" data-name="Group 27" width="123" height="123" viewBox="0 0 123 123">
                            <g id="Ellipse_2" data-name="Ellipse 2" fill="none" stroke="#2DA1FF" stroke-width="2">
                                <circle cx="61.5" cy="61.5" r="61.5" stroke="none"></circle>
                                <circle cx="61.5" cy="61.5" r="60.5" fill="none"></circle>
                            </g>
                            <circle id="Ellipse_8" data-name="Ellipse 8" cx="6.5" cy="6.5" r="6.5" transform="translate(30 55)" fill="none" stroke="#2DA1FF" stroke-width="3"></circle>
                            <circle id="Ellipse_9" data-name="Ellipse 9" cx="6.5" cy="6.5" r="6.5" transform="translate(55 55)" fill="none" stroke="#2DA1FF" stroke-width="3"></circle>
                            <circle id="Ellipse_10" data-name="Ellipse 10" cx="6.5" cy="6.5" r="6.5" transform="translate(80 55)" fill="none" stroke="#2DA1FF" stroke-width="3"></circle>
                        </svg></div>
                    <div id="results">
                        <?= custom_product_variation_training(get_the_ID()) ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <?php $SVG = new SVG; ?>
                    <div class="image-box training-map-holder position-relative">
                        <?= $SVG->uk() ?>
                        <span title="Edinburgh" class="trigger-location-change" id="edinburgh" value="edinburgh"><?= $SVG->location() ?><div class='pulse'></div></span>
                        <span title="Leeds" class="trigger-location-change" id="leeds" value="leeds"><?= $SVG->location() ?><div class='pulse'></div></span>
                        <span title="Rugby" class="trigger-location-change" id="rugby" value="rugby"><?= $SVG->location() ?><div class='pulse'></div></span>
                        <span title="Cardiff" class="trigger-location-change" id="cardiff" value="cardiff"><?= $SVG->location() ?><div class='pulse'></div></span>
                        <span title="Kent" class="trigger-location-change" id="kent" value="kent"><?= $SVG->location() ?><div class='pulse'></div></span>
                        <span title="Hampshire" class="trigger-location-change" id="hampshire" value="hampshire"><?= $SVG->location() ?><div class='pulse'></div></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<div class="modules">
    <?php
    echo do_shortcode(___sections('sections_after_main', get_the_ID()));
    ?>
</div>
<?php get_footer(); ?>