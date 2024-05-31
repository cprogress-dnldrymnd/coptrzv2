<?php
$DisplayData = new DisplayData;

$eventscategory = get_terms(
    array(
        'taxonomy'   => 'events_category',
        'hide_empty' => false,
        'meta_key'   => '_menu_order',
        'orderby'    => '_menu_order'
    )
);
if (!empty($eventscategory)) {

    foreach ($eventscategory as $key => $category) {
        $heading = get__term_meta($category->term_id, 'heading');
        $link_text = get__term_meta($category->term_id, 'link_text');
        $link_url = get__term_meta($category->term_id, 'link_url');
        $the_query = new WP_Query(
            array(
                'post_type'      => array('events'),
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'events_category',
                        'field'    => 'slug',
                        'terms'    => array($category->slug),
                        'operator' => 'IN',
                    )
                )
            )
        );
        if ($the_query->have_posts()) {
            ?>

            <section class="product-slider blog-section xl-padding-bottom archive-section">
                <div class="container mb-7">
                    <div class="row line-title line-title-v2 d-flex align-items-start fw-medium">
                        <div class="col d-flex align-items-center pt-3">
                            <span class="text text-uppercase">
                                <?= $category->name ?>
                            </span>
                            <span class="line"></span>
                        </div>
                    </div>
                </div>



                <div class="container">
                    <div class="product-slider-box">
                        <div class="mySwiper-productSwiperPost post-box-PostSlider">
                            <div class="row">
                                <?php
                                while ($the_query->have_posts()) {
                                    $the_query->the_post();
                                    $post_id = get_the_ID();
                                    $meta1 = get__post_meta_by_id($post_id, 'crb_event_start_date');
                                    $meta2 = get__post_meta_by_id($post_id, 'crb_event_start_time');
                                    ?>
                                    <div class="col-xl-3 col-lg-4 col-sm-6 post-item">
                                        <div class="swiper-slide">
                                            <div class="inner background-white d-block ">
                                                <a href="<?= get_permalink() ?>" class="box-link"></a>
                                                <?php
                                                $DisplayData->image(
                                                    array(
                                                        'image_id'    => get_post_thumbnail_id(),
                                                        'size'        => 'medium',
                                                        'placeholder' => true
                                                    ),
                                                    'position-relative image-cover-transform'
                                                );


                                                ?>

                                                <?php
                                                $DisplayData->heading(
                                                    array(
                                                        'heading' => get_the_title(),
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
                                                        <a href="<?= get_permalink() ?>" class="link-underline fw-medium">
                                                            Find out more
                                                        </a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if ($heading) { ?>
                                    <div class="col-xl-3 col-lg-4 col-sm-6 col-cta">
                                        <div class="swiper-slide">
                                            <div
                                                class="inner-cta h-100 background-accent content-margin d-flex flex-column justify-content-between">
                                                <?php if ($link_text) { ?>
                                                    <a href="<?= $link_url ?>" class="box-link"></a>
                                                <?php } ?>

                                                <div class="heading-box">
                                                    <h4>
                                                        <?= $heading ?>
                                                    </h4>
                                                </div>

                                                <?php if ($link_text) { ?>
                                                    <div class="link-box">
                                                        <a href="<?= $link_url ?>">
                                                            <?= $link_text ?>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                            </div>

                                        </div>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                </div>
            </section>

        <?php } ?>
    <?php } ?>

<?php } ?>