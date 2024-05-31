<?php

$DisplayData = new DisplayData;

$GetData = new GetData;
$button_text = 'Read more';
if (!is_home()) {
    if (is_post_type_archive('events')) {

        $getItems = $GetData->get_posts_details('events');

        $title = 'UPCOMING THIS MONTH';

        $button_text = 'Learn more';

        $items = array();

        foreach ($getItems as $key => $item) {

            $current_date = get_the_date('m-Y');

            $date = get__post_meta_by_id($key, 'crb_event_start_date');

            $newDate = date("m-Y", strtotime($date));



            if ($newDate == $current_date) {

                $items[$key] = $item;
            }
        }
    }

?>



    <section class="product-slider md-padding-bottom">

        <div class="container mb-7">

            <div class="row line-title line-title-v2 d-flex align-items-start fw-medium">

                <div class="col d-flex align-items-center pt-3">

                    <span class="text text-uppercase">

                        <?= $title ?>

                    </span>

                    <span class="line"></span>

                </div>

            </div>

        </div>

        <div class="container extend-right">

            <div class="product-slider-box">

                <div class="swiper mySwiper-productSwiper mySwiper-productSwiper-medium">

                    <div class="swiper-wrapper product-holder post-box-PostSlider align-items-stretch">

                        <?php foreach ($items as $key => $item) { ?>

                            <?php

                            if (is_home()) {

                                $post_id = $item;

                                $meta1 = get_the_date('d/m/Y', $post_id);

                                $author_id = get_post_field('post_author', $post_id);

                                $meta2 = get_the_author_meta('display_name', $author_id);
                            } else if (is_post_type_archive('events')) {

                                $post_id = $key;

                                $meta1 = get__post_meta_by_id($post_id, 'crb_event_start_date');

                                $meta2 = get__post_meta_by_id($post_id, 'crb_event_start_time');
                            }

                            ?>

                            <div class="swiper-slide product-box">

                                <div class="inner background-white d-block ">
                                    <a href="<?= get_permalink($post_id) ?>" class="box-link"></a>

                                    <?php

                                    $DisplayData->image(

                                        array(

                                            'image_id'    => get_post_thumbnail_id($post_id),

                                            'size'        => 'medium',

                                            'placeholder' => true

                                        ),

                                        'position-relative image-cover-transform'

                                    );



                                    ?>



                                    <?php

                                    $DisplayData->heading(

                                        array(

                                            'heading' => get_the_title($post_id),

                                            'tag'     => 'h4'

                                        )

                                    );

                                    ?>

                                    <div class="bottom-box">

                                        <div class="meta-box d-flex">

                                            <span class="date">

                                                <?= $meta1 ?>

                                            </span>

                                            <div class="bull">&bull;</div>

                                            <span class="author">

                                                <?= $meta2 ?>

                                            </span>

                                        </div>



                                        <div class="link-box">

                                            <a href="<?= get_permalink($post_id) ?>" class="link-underline fw-medium">

                                                <?= $button_text ?>

                                            </a>

                                        </div>

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

<?php } ?>