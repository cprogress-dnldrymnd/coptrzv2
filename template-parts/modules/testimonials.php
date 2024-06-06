<?php
$SVG = new SVG;
$testimonials_arr = array();
$testimonial_source = $module['testimonial_source'];
$testimonials = $module['testimonials'];
$testimonial_category = $module['testimonial_category'];
if ($testimonial_source == 'testimonial') {
    $testimonials_arr = array();
    foreach ($testimonials as $testimonial) {
        $testimonials_arr[$testimonial['id']] = array(
            'author' => get_the_title($testimonial['id']),
            'description' => get__post_meta_by_id($testimonial['id'], 'testimonial_content'),
            'position' => get__post_meta_by_id($testimonial['id'], 'testimonial_title'),
        );
    }
} else if ($testimonial_source == 'testimonial_category') {
    $testimonials_cat_id = array();
    foreach ($testimonial_category as $testimonials_cat) {
        $testimonials_cat_id[] = $testimonials_cat['id'];
    }
    $args = array(
        'post_type' => 'testimonials',
        'post_status' => 'publish',
        'numberposts' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'testimonial_category',
                'field'    => 'term_id',
                'terms'    => $testimonials_cat_id
            )
        )
    );
    $testimonials_lists = get_posts($args);
    $testimonials_arr = array();
    foreach ($testimonials_lists as $testimonial) {
        $testimonials_arr[$testimonial->ID] = array(
            'author' => $testimonial->post_title,
            'description' => get__post_meta_by_id($testimonial->ID, 'testimonial_content'),
            'position' => get__post_meta_by_id($testimonial->ID, 'testimonial_title'),

        );
    }
} else {
    $testimonial = $testimonial;
}

?>
<section class="customer-reviews position-relative">
    <?php
    _background_image(array(
        'baground_image' => $baground_image,
        'background_image_class' => $background_image_class,
        'background_overlay_image' => $background_overlay_image,
    ));
    ?>
    <?php if ($display_testimonial_default_text) { ?>
        <div class="container">
            <div class="row g-4 white-color mb-7 justify-content-between">
                <div class="col-auto ">
                    <span class="text-style-1  left fw-medium">
                        COPTRZ CUSTOMER REVIEWS
                    </span>
                </div>
                <div class="col-auto">
                    <div class="heading-box mb-6">
                        <h2>
                            <?= $testimonial_heading ?>
                        </h2>

                    </div>
                    <div class="review-score d-flex align-items-center">
                        <div class="score">
                            <?= $testimonial_rating ?>
                        </div>
                        <div class="stars-holder">
                            <div class="stars d-flex">
                                <?php SVG::star() ?>
                                <?php SVG::star() ?>
                                <?php SVG::star() ?>
                                <?php SVG::star() ?>
                                <?php SVG::star_half() ?>
                            </div>
                            <div class="star-text">
                                <span>Score on <a targe="_blank" style="color: #fff" href="https://www.google.com/search?q=coptrz+google+review&rlz=1C1VDKB_enPH1019PH1019&oq=coptrz+google+review&aqs=chrome..69i57j69i64l2.3639j0j7&sourceid=chrome&ie=UTF-8">Google</a></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php  } ?>
    <div class="container">

        <?php
        _section_heading_description(array(
            'heading' => $module['heading'],
            'description' => $module['description'],
            'text_align' => $module['text_align'],
            'heading_prefix' => $module['heading_prefix'],
            'heading_suffix' => $module['heading_suffix'],
            'tag' => $module['tag'],
            'size' => $module['size'],
            'heading_with_line' => $module['heading_with_line'],
        ));
        ?>

        <div class="review-holder review-holder-case-study">
            <div class="swiper  mySwiper-Reviews mySwiper-ReviewsCaseStudy">
                <div class="swiper-wrapper ">
                    <?php foreach ($testimonials_arr as $testimonial) { ?>
                        <div class="swiper-slide">
                            <div class="review-box case-study background-primary d-flex">
                                <div class="quote">
                                    <?php SVG::quote() ?>
                                </div>
                                <div class="review-content">
                                    <?= do_shortcode('[_description class="review-text" description="' . _format_text($testimonial['description']) . '" ]') ?>

                                    <div class="author d-flex align-items-center">
                                        <span>
                                            <?= $testimonial['author'] ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php } ?>
                </div>
                <?php if ($type == 'case-study') { ?>
                    <div class="swiper-pagination d-flex justify-content-center align-items-center"></div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>