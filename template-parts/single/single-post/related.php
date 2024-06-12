<?php
$suggested_articles = get__theme_option('suggested_articles');
$categories = get_the_category(get_the_ID());
?>

<section class="product-slider md-padding background-body">
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
    <div class="container">
        <div class="swiper-post-box">
            <div class="swiper mySwiper-Post">
                <div class="swiper-wrapper">

                    <?php
                    $args = array(
                        'posts_per_page' => 10,

                        'post_type'      => array('post'),

                        'post_status'    => 'publish',

                        'category__and ' => $categories,

                        'orderby' => 'rand'

                    );
                    $query = new WP_Query($args);

                    $post_elements = array(
                        array('_type' => 'featured_image'),
                        array('_type' => 'post_title', 'size' => 'small-heading'),
                        array('_type' => 'post_excerpt'),
                        array('_type' => 'permalink', 'button_style' => 'button-accent'),
                    );

                    $style_attribute_post = '';
                    $classes = 'xxs-padding-top xxs-padding-left xxs-padding-right xxs-padding-bottom';
                    ?>
                    <?php if ($query->have_posts()) { ?>

                        <?php while ($query->have_posts()) { ?>
                            <?php
                            $query->the_post();
                            $id = get_the_ID();
                            ?>
                            <div class="swiper-slide product-box">
                                    <?php
                                    include locate_template('template-parts/components/post_grid.php');
                                    ?>
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