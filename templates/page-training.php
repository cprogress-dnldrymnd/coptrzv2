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
                                <div class="col-auto">
                                    <select name="category" class="trigger-training-ajax-select">
                                        <option value="">Location: All</option>
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
                        <img src="https://dev.coptrz.com/wp-content/uploads/2024/07/map.jpg" alt="">
                        <span id="edinburgh"><?= $SVG->location() ?></span>
                        <span id="leeds"><?= $SVG->location() ?></span>
                        <span id="rugby"><?= $SVG->location() ?></span>
                        <span id="cardiff"><?= $SVG->location() ?></span>
                        <span id="kent"><?= $SVG->location() ?></span>
                        <span id="hampshire"><?= $SVG->location() ?></span>
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