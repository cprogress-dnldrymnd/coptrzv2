<?php
$DisplayData = new DisplayData;
$Helpers = new Helpers;
$GetData = new GetData;
$left_title = $module['left_title'];
$slider_items = $module['slider_items'];
$heading = $module['heading'];
?>

<section class="product-slider">
    <div class="container mb-7">
        <div class="row line-title line-title-v2 d-flex align-items-start fw-medium">
            <div class="col d-flex align-items-center pt-3">
                <span class="text">
                    <?= $left_title ?>
                </span>
                <span class="line"></span>
            </div>
            <div class="col text-content">
                <p>
                    <?= $heading ?>
                </p>
            </div>
        </div>
    </div>
    <div class="container extend-right">
        <div class="product-slider-box">
            <div class="swiper product-holder post-box-Slider post-box-PostSlider mySwiper-productSwiper">
                <div class="  swiper-wrapper align-items-stretch">
                    <?php foreach ($slider_items as $slider_item) { ?>
                        <div class="product-box swiper-slide">
                            <div class="inner background-white d-block ">
                                <?php
                                $DisplayData->image(
                                    array(
                                        'image_id'    => $slider_item['image'],
                                        'size'        => 'medium',
                                        'placeholder' => true
                                    ),
                                    'position-relative image-cover-transform'
                                );


                                ?>

                                <div class="bottom content-margin">
                                    <?php
                                    $DisplayData->heading(
                                        array(
                                            'heading' => $slider_item['heading'],
                                            'tag'     => 'h3'
                                        )
                                    );
                                    $DisplayData->description(
                                        array(
                                            'description' => $slider_item['description'],
                                        )
                                    );
                                    if ($slider_item['slider_button_type']) {
                                        $DisplayData->button(
                                            $slider_item['slider_button_text'],
                                            $slider_item['slider_' . $slider_item['slider_button_type']],
                                            $slider_item['slider_button_action'],
                                            $slider_item['slider_button_icon'],
                                            'button-accent text-center',
                                        );
                                    }
                                    ?>
                                </div>


                            </div>
                        </div>

                    <?php } ?>
                </div>
                <div class="swiper-nav-holder d-inline-flex">
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>
        </div>
    </div>
</section>