<?php
$SVG = new SVG;
$DisplayData = new DisplayData;
$module['heading'];
$heading = $module['heading'];
$heading_prefix = $module['heading_prefix'];
$items = $module['items'];

?>

<div class="container text-center">
    <?php
    $DisplayData->heading(
        array(
            'heading'       => $heading,
            'heading_small' => $heading_prefix
        ),
        'small-width mb-6 section-heading ms-auto me-auto'
    );
    ?>

    <div class="accordion-box mt-5 d-block d-lg-none">
        <div class="accordion accordion-flush" id="accordionImageCenter">
            <?php foreach ($items as $key => $item) { ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-<?= $key ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#flush-collapse<?= $key ?>" aria-expanded="false" aria-controls="flush-collapse<?= $key ?>">
                            <div class="icon-heading-holder col d-flex align-items-center d-lg-block">
                                <?php
                                $DisplayData->image(
                                    array(
                                        'image_id' => $item['icon']
                                    ),
                                    'icon-box me-3'
                                );
                                $DisplayData->heading(
                                    array(
                                        'heading' => $item['heading'],
                                        'tag'     => 'h4'
                                    ),
                                );
                                ?>
                            </div>
                        </button>
                    </h2>
                    <div id="flush-collapse<?= $key ?>" class="accordion-collapse collapse" aria-labelledby="flush-<?= $key ?>"
                        data-bs-parent="#accordionImageCenter">
                        <div class="accordion-body content-margin text-start">
                            <?php
                            $DisplayData->description(
                                array(
                                    'description' => $item['description'],
                                ),
                            );
                            ?>
                            <div class="image-box image-center">
                                <img decoding="async" class="jetpack-lazy-image" src="<?= wp_get_attachment_image_url($item['image'], 'large') ?>" alt="">
                                <img decoding="async" class="jetpack-lazy-image drone-image" src="<?= wp_get_attachment_image_url($item['image_top_right'], 'large') ?>" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="tab-box d-none d-lg-block">
        <div class="row gy-3 av-tabs align-items-center" id="tab-center">
            <div class="col-12 col-lg col-left">
                <div class="column-holder py-5">
                    <div class="nav nav-tabs" role="tablist">
                        <button class="nav-link text-start active" id="nav-0-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-0" type="button" role="tab" aria-controls="nav-0" aria-selected="true">
                            <div class="icon-heading-holder d-flex align-items-center d-lg-block">
                                <?php
                                $DisplayData->image(
                                    array(
                                        'image_id' => $items[0]['icon']
                                    ),
                                    'icon-box mb-4 me-3'
                                );
                                $DisplayData->heading(
                                    array(
                                        'heading' => $items[0]['heading'],
                                        'tag'     => 'h4'
                                    ),
                                    'mb-2'
                                );

                                ?>
                            </div>
                            <?php

                            $DisplayData->description(
                                array(
                                    'description' => $items[0]['description'],
                                ),
                            );
                            ?>
                        </button>
                        <button class="nav-link text-start" id="nav-1-tab" data-bs-toggle="tab" data-bs-target="#nav-1"
                            type="button" role="tab" aria-controls="nav-1" aria-selected="false">
                            <div class="icon-heading-holder d-flex align-items-center d-lg-block">
                                <?php
                                $DisplayData->image(
                                    array(
                                        'image_id' => $items[1]['icon']
                                    ),
                                    'icon-box mb-4 me-3'
                                );
                                $DisplayData->heading(
                                    array(
                                        'heading' => $items[1]['heading'],
                                        'tag'     => 'h4'
                                    ),
                                    'mb-2'
                                );

                                ?>
                            </div>

                            <?php

                            $DisplayData->description(
                                array(
                                    'description' => $items[1]['description'],
                                ),
                            );
                            ?>

                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-center">
                <div class="column-holder">
                    <div class="tab-content" id="tab-center-tabContent">
                        <?php foreach ($items as $key => $item) { ?>
                            <div class="tab-pane fade <?= $key == 0 ? 'show active' : '' ?>" id="nav-<?= $key ?>"
                                role="tabpanel" aria-labelledby="nav-<?= $key ?>-tab">
                                <div class="image-box image-center">
                                    <img decoding="async" class="jetpack-lazy-image" src="<?= wp_get_attachment_image_url($item['image'], 'large') ?>" alt="">
                                    <img decoding="async" class="jetpack-lazy-image drone-image" src="<?= wp_get_attachment_image_url($item['image_top_right'], 'large') ?>" alt="">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg col-right">
                <div class="column-holder py-5">
                    <div class="nav nav-tabs" role="tablist">
                        <button class="nav-link text-start" id="nav-2-tab" data-bs-toggle="tab" data-bs-target="#nav-2"
                            type="button" role="tab" aria-controls="nav-2" aria-selected="false">
                            <div class="icon-heading-holder d-flex align-items-center d-lg-block">
                                <?php
                                $DisplayData->image(
                                    array(
                                        'image_id' => $items[2]['icon']
                                    ),
                                    'icon-box mb-4 me-3'
                                );
                                $DisplayData->heading(
                                    array(
                                        'heading' => $items[2]['heading'],
                                        'tag'     => 'h4'
                                    ),
                                    'mb-2'
                                );
                                ?>
                            </div>
                            <?php
                            $DisplayData->description(
                                array(
                                    'description' => $items[2]['description'],
                                ),
                            );
                            ?>
                        </button>
                        <button class="nav-link text-start" id="nav-3-tab" data-bs-toggle="tab" data-bs-target="#nav-3"
                            type="button" role="tab" aria-controls="nav-3" aria-selected="false">
                            <div class="icon-heading-holder d-flex align-items-center d-lg-block">
                                <?php
                                $DisplayData->image(
                                    array(
                                        'image_id' => $items[3]['icon']
                                    ),
                                    'icon-box mb-4 me-3'
                                );
                                $DisplayData->heading(
                                    array(
                                        'heading' => $items[3]['heading'],
                                        'tag'     => 'h4'
                                    ),
                                    'mb-2'
                                );
                                ?>
                            </div>

                            <?php
                            $DisplayData->description(
                                array(
                                    'description' => $items[3]['description'],
                                ),
                            );
                            ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>