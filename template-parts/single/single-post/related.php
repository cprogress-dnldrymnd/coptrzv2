<?php
$DisplayData = new DisplayData;
$suggested_articles = get__theme_option('suggested_articles');
$categories = get_the_category(get_the_ID());
?>

<section class="product-slider md-padding">
    <div class="container mb-7">
        <div class="row line-title line-title-v2 d-flex align-items-start fw-medium">
            <div class="col d-flex align-items-center pt-3">
                <span class="text text-uppercase">
                    RELATED POSTS
                </span>

                <span class="line"></span>
            </div>
        </div>
    </div>
    <div class="container extend-right">

        <div class="product-slider-box">
            <div class="swiper mySwiper-productSwiper mySwiper-productSwiper-medium ">
                <div class="swiper-wrapper product-holder post-box-PostSlider align-items-stretch">

                    <?php
                    $args = array(
                        'posts_per_page' => 10,

                        'post_type'      => array('post'),

                        'post_status'    => 'publish',

                        'category__and ' => $categories,

                        'orderby' => 'rand'

                    );
                    $query = new WP_Query($args);
                    ?>
                    <?php if ($query->have_posts()) { ?>

                        <?php while ($query->have_posts()) { ?>
                            <?php
                            $query->the_post();
                            $post_id = get_the_ID();
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
                                        'position-relative image-cover-transform image-post'
                                    );
                                    ?>
                                    <?php $categories = get_the_terms(get_the_ID(), 'category') ?>
                                    <div class="top-box">
                                        <div class="meta-box d-flex flex-wrap">
                                            <span class="date">
                                                <?php
                                                foreach ($categories as $cat) {
                                                ?>
                                                    <a href="<?= get_term_link($cat->term_id, 'category') ?>"><?= $cat->name ?></a>
                                                <?php
                                                }
                                                ?>
                                            </span>
                                            <div class="bull">&bull;</div>
                                            <span class="author">
                                                <?php
                                                $author_id = get_post_field('post_author', get_the_ID());
                                                $author_name = get_the_author_meta('display_name', $author_id);
                                                ?>
                                                <?= $author_name ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php
                                    $DisplayData->heading(
                                        array(
                                            'heading' => get_the_title($post_id),
                                            'tag'     => 'h4'
                                        )
                                    );
                                    ?>
                                    <div class="bottom-box">
                                        <div class="link-box">
                                            <a href="<?= get_permalink($post_id) ?>" class="link-underline fw-medium">
                                                Read more
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-short" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="swiper-nav-holder d-none d-sm-inline-flex">

                    <div class="swiper-button-prev"></div>

                    <div class="swiper-button-next"></div>

                </div>
                <div class="swiper-pagination-holder d-flex d-sm-none">
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </div>
</section>