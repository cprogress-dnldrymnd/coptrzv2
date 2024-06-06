<?php
$DisplayData = new DisplayData;
$Helpers = new Helpers;
$GetData = new GetData;
$SVG = new SVG;
$testimonials_arr = array();
$testimonials = $GetData->get_posts_ids('testimonials');

foreach ($testimonials as $key => $testimonial) {
    $testimonial_source = $module['testimonial_source'];
    $testimonials = $module['testimonials'];
    $testimonial_category = $module['testimonial_category'];
    $testimonials_arr[] = array(
        'author' => $testimonial,
        'description' => get__post_meta_by_id($key, 'testimonial_content')
    );
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