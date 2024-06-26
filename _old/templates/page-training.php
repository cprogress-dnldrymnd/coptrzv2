<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Training
/* Template Post Type: product
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<style>
    #ppcp-messages {
        display: none !important;
    }

    section.training-info .sticky-sidebar-training .simple-add-to-cart .single_add_to_cart_button {
        margin-bottom: 0 !important;
    }
</style>
<?php
global $product;
$GetData = new GetData;
$DisplayData = new DisplayData;
$terms = get_the_terms(get_the_ID(), 'product_cat');
$main_image = $product->get_image_id() ? $product->get_image_id() : get__theme_option('placeholder_image');
$images = $product->get_gallery_image_ids();
$cpd_maker = get__post_meta('cpd_maker');
$tquk_logo = get__post_meta('tquk_logo');
$product_type = $product->get_type();
$suitable_industry = get__post_meta('suitable_industry');
$faqs = carbon_get_the_post_meta('faqs');
$how_to_take = get__post_meta('how_to_take');
$what_do_i_get = get__post_meta('what_do_i_get');
$why_choose_us = carbon_get_theme_option('why_choose_us');
$self_paced = get__post_meta('self_paced');
$online_arr = array();
$classroom_arr = array();

global $hide_after_add_to_cart;
$hide_after_add_to_cart = true;

$pa_brands = $product->get_attribute('pa_brands');
$category = get_the_terms($product_id, 'product_cat');

$data['sku']      = $product->get_sku();
if ($product->get_price()) {
    $data['price']    = _price_format($product->get_price());
}
$data['name']     = $product->get_name();
$data['brand']    = $pa_brands;
$data['category'] = $category[0]->name;
?>

<style>
    #request-info-modal {
        width: 100%;
    }

    .row-archive-products-buttons .col-sm-6 {
        width: 100%;
    }
</style>
<div class="product-data d-none">
    <?= json_encode($data) ?>
</div>
<section class="hero-banner-training md-padding background-black">
    <div class="container">
        <div class="row g-3 g-lg-5 align-items-center">
            <div class="col-lg-6">
                <div class="column-holder content-margin">
                    <div class="breadcrumbs-box-training d-block d-lg-none">
                        <ul class="list-inline d-flex flex-wrap align-items-center">
                            <li>
                                <a href="<?= get_site_url() ?>">Home</a>
                            </li>
                            <li>
                                <a href="<?= get_permalink(wc_get_page_id('shop')) ?>">Shop</a>
                            </li>
                            <li>
                                <a href="<?= get_term_link($terms[0]->term_id) ?>"><?= $terms[0]->name ?></a>
                            </li>
                            <li>
                                <span><?php the_title() ?></span>
                            </li>
                        </ul>
                    </div>
                    <?php
                    $DisplayData->heading(array(
                        'heading' => get_the_title(),
                        'tag'     => 'h2'
                    ), 'd-block d-lg-none');
                    ?>
                    <?= product_gallery($main_image, $images, $product) ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="column-holder column-text content-margin">
                    <div class="breadcrumbs-box-training flex-wrap d-none d-lg-block">
                        <ul class="list-inline d-flex align-items-center">
                            <li>
                                <a href="<?= get_site_url() ?>">Home</a>
                            </li>
                            <li>
                                <a href="<?= get_permalink(wc_get_page_id('shop')) ?>">Shop</a>
                            </li>
                            <li>
                                <a href="<?= get_term_link($terms[0]->term_id) ?>"><?= $terms[0]->name ?></a>
                            </li>
                            <li>
                                <span><?php the_title() ?></span>
                            </li>
                        </ul>
                    </div>

                    <?php
                    $DisplayData->heading(array(
                        'heading' => get_the_title(),
                        'tag'     => 'h2'
                    ), 'd-none d-lg-block');
                    ?>
                    <div class="price-box">
                        <?= $GetData->product_price(get_the_ID()) ?>
                    </div>

                    <?php
                    $DisplayData->description(
                        array(
                            'description' => get_the_excerpt(),
                        ),
                    );
                    ?>
                    <div class="image-group-box d-flex align-items-center">
                        <?php if ($cpd_maker) { ?>
                            <div class="image-box cpd">
                                <img src="https://coptrz.com/wp-content/uploads/2023/08/full-logo-white-cd75d4a811.png" alt="CPD">
                            </div>
                        <?php } ?>
                        <?php if ($tquk_logo) { ?>
                            <div class="image-box">
                                <img class="tquk" src="https://coptrz.com/wp-content/uploads/2023/10/tquk.png" alt="tquk">
                            </div>
                        <?php } ?>
                    </div>
                    <div class="button-group-box">
                        <?php //do_shortcode('[add_to_cart id="' . get_the_ID() . '" show_price="FALSE"]') 
                        ?>
                        <div class="button-box button-accent">
                            <a href="#training-info">ADD TO BASKET</a>
                        </div>
                        <?= request_info_button('button-bordered') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="training-info no-overflow">
    <div class="container-fluid g-0 p-0">
        <div class="row g-0">
            <div class="col-lg-7 col-left sm-padding">
                <div class="column-holder column-boxes">
                    <div class="tabs content">
                        <div class="heading-box mb-4">
                            <h2>Training Information</h2>
                        </div>
                        <div class="info-tab content-margin">
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-Course-syllabus-tab" data-bs-toggle="tab" data-bs-target="#nav-Course-syllabus" type="button" role="tab" aria-controls="nav-Course-syllabus" aria-selected="true">
                                        Description
                                    </button>
                                    <?php if ($how_to_take) { ?>
                                        <button class="nav-link" id="nav-who-tab" data-bs-toggle="tab" data-bs-target="#nav-who" type="button" role="tab" aria-controls="nav-who" aria-selected="false">
                                            How to take this course
                                        </button>
                                    <?php } ?>
                                    <?php if ($what_do_i_get) { ?>
                                        <button class="nav-link" id="nav-what-tab" data-bs-toggle="tab" data-bs-target="#nav-what" type="button" role="tab" aria-controls="nav-what" aria-selected="false">
                                            What do I get from this course
                                        </button>
                                    <?php } ?>


                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-Course-syllabus" role="tabpanel" aria-labelledby="nav-Course-syllabus-tab">

                                    <div class="description-box content-margin">
                                        <?= the_content() ?>
                                        <?= request_info_button('button-accent') ?>
                                    </div>
                                </div>
                                <?php if ($how_to_take) { ?>
                                    <div class="tab-pane fade" id="nav-who" role="tabpanel" aria-labelledby="nav-who-tab">
                                        <?php
                                        $DisplayData->description(
                                            array(
                                                'description' => $how_to_take
                                            ),
                                        );
                                        ?>
                                    </div>
                                <?php } ?>
                                <?php if ($what_do_i_get) { ?>
                                    <div class="tab-pane fade" id="nav-what" role="tabpanel" aria-labelledby="nav-what-tab">
                                        <?php
                                        $DisplayData->description(
                                            array(
                                                'description' => $what_do_i_get
                                            ),
                                        );
                                        ?>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                    <?php if ($reviews) { ?>

                        <div class="review-box mt-5 full-width background-accent content-margin sm-padding">
                            <div class="heading-box content">
                                <h2>Reviews</h2>
                            </div>
                            <section class="customer-reviews customer-reviews-v2">
                                <?php
                                $type = 'product-reviews';
                                include(get_stylesheet_directory() . '/template-parts/modules/_customer_reviews.php');
                                ?>
                            </section>
                        </div>

                    <?php } ?>
                    <?php if ($suitable_industry[0] != '') { ?>
                        <div class="suitable-industry-box use-cases-box  d-none background-white content-margin sm-padding">
                            <div class="content content-margin">
                                <div class="heading-box">
                                    <h2>Use Cases</h2>
                                </div>
                                <div class="row g-3 row-responsive">
                                    <?php foreach ($suitable_industry as $industry) { ?>
                                        <div class="col-lg-3">
                                            <div class="column-holder h-100 content-margin background-secondary">
                                                <?php
                                                $DisplayData->image(
                                                    array(
                                                        'image_id'    => get__post_meta_by_id($industry, 'icon'),
                                                        'size'        => 'medium',
                                                        'placeholder' => true
                                                    ),
                                                );
                                                $DisplayData->heading(
                                                    array(
                                                        'heading' => get_the_title($industry),
                                                        'tag'     => 'h4'
                                                    ),
                                                );

                                                $DisplayData->description(
                                                    array(
                                                        'description' => get__post_meta_by_id($industry, 'short_descr'),
                                                    ),
                                                );
                                                ?>

                                                <div class="button-box button-accent button-small">
                                                    <a href="<?= get_permalink($industry) ?>">Read More</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                        </div>

                    <?php }  ?>

                    <?php if ($suitable_industry[0] != '') { ?>
                        <div class="suitable-industry-box background-secondary content-margin sm-padding">
                            <div class="content content-margin">
                                <div class="heading-box">
                                    <h2>Suitable Industry</h2>
                                </div>
                                <div class="row g-3 row-responsive">
                                    <?php foreach ($suitable_industry as $industry) { ?>
                                        <div class="col-lg-3">
                                            <div class="column-holder h-100 content-margin">
                                                <?php
                                                $DisplayData->image(
                                                    array(
                                                        'image_id'    => get__post_meta_by_id($industry, 'icon'),
                                                        'size'        => 'medium',
                                                        'placeholder' => true
                                                    ),
                                                );
                                                $DisplayData->heading(
                                                    array(
                                                        'heading' => get_the_title($industry),
                                                        'tag'     => 'h4'
                                                    ),
                                                );

                                                $DisplayData->description(
                                                    array(
                                                        'description' => get__post_meta_by_id($industry, 'short_descr'),
                                                    ),
                                                );
                                                ?>

                                                <div class="button-box button-accent button-small">
                                                    <a href="<?= get_permalink($industry) ?>">Read More</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($faqs) { ?>

                        <div class="faq-box content sm-padding content-margin">
                            <div class="heading-box">
                                <h2><?php the_title() ?> FAQs</h2>
                            </div>
                            <div class="accordion" id="accordionExample">
                                <?php foreach ($faqs as $key => $faq) { ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading<?= $key ?>">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $key ?>" aria-expanded="false" aria-controls="collapse<?= $key ?>">
                                                <strong><?= $faq['heading'] ?></strong>
                                            </button>
                                        </h2>
                                        <div id="collapse<?= $key ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $key ?>" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <?= wpautop($faq['description']) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="why-choose-us-box background-white sm-padding content-margin">
                        <div class="content content-margin">
                            <div class="heading-box">
                                <h2>Why choose us?</h2>
                            </div>
                            <div class="row g-3">
                                <?php foreach ($why_choose_us as $why) { ?>
                                    <div class="col-lg-4">
                                        <div class="column-holder content-margin h-100">
                                            <?php
                                            $DisplayData->image(
                                                array(
                                                    'image_id'    => $why['icon'],
                                                    'size'        => 'medium',
                                                ),
                                            );
                                            $DisplayData->heading(
                                                array(
                                                    'heading' => $why['heading'],
                                                    'tag'     => 'h4'
                                                ),
                                            );

                                            $DisplayData->description(
                                                array(
                                                    'description' => $why['description']
                                                ),
                                            );
                                            ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="customer-slider-box background-black sm-padding-top content-margin">
                        <div class="content content-margin">
                            <div class="heading-box">
                                <h2>Customer Slider</h2>
                            </div>
                            <section class="logo-slider">
                                <?php
                                $type = 'product-customer-slider';
                                $image_source_product = get__theme_option('image_source');
                                $logo_slider_id_product = get__theme_option('gallery');
                                $custom_heading_product = get__theme_option('custom_heading');
                                $custom_gallery_product = carbon_get_theme_option('custom_gallery');
                                include(get_stylesheet_directory() . '/template-parts/modules/_logo_slider.php');
                                ?>
                            </section>
                        </div>
                    </div>

                    <div class="cta-box">
                        <div class="content">
                            <section class="cta sm-padding-top has-edit" id="get-in-touch">
                                <?php
                                $post_is_training = true;
                                include(get_stylesheet_directory() . '/template-parts/modules/_cta.php');
                                ?>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 background-white p-0 col-sidebar sticky-sidebar-training">
                <div class="column-holder">
                    <div id="training-info">
                        <div class="info-tab info-tab-v2 ">
                            <div class="inner md-padding px-4 content-margin">
                                <nav>
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        <button class="nav-link active" id="nav-upcoming-classes-tab" data-bs-toggle="tab" data-bs-target="#nav-upcoming-classes" type="button" role="tab" aria-controls="nav-upcoming-classes" aria-selected="true">Upcoming classes</button>
                                        <button class="nav-link" id="nav-related-courses-tab" data-bs-toggle="tab" data-bs-target="#nav-related-courses" type="button" role="tab" aria-controls="nav-related-courses" aria-selected="false">Related Courses</button>
                                    </div>
                                </nav>

                                <?php
                                if ($product->get_type() == 'variable') {
                                    $variation = $product->get_children();
                                    foreach ($variation as $var) {
                                        $_delivery_method = get_post_meta($var, '_delivery_method', true);
                                        $_start_date = get_post_meta($var, '_start_date', true);
                                        $_end_date = get_post_meta($var, '_end_date', true);
                                        if ($_delivery_method == 'online') {
                                            $online_arr[$var] = array(
                                                '_start_date' => $_start_date,
                                                '_end_date' => $_end_date,
                                            );
                                        } else if ($_delivery_method == 'classroom') {
                                            $classroom_arr[$var] = array(
                                                '_start_date' => $_start_date,
                                                '_end_date' => $_end_date,
                                            );
                                        }
                                    }
                                }

                                ?>

                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="nav-upcoming-classes" role="tabpanel" aria-labelledby="nav-upcoming-classes-tab">
                                        <div class="delivery-methods-tab content-margin">
                                            <h4>
                                                DELIVERY METHOD
                                            </h4>
                                            <nav>
                                                <div class="nav nav-tabs" id="nav-tab-delivery" role="tablist">
                                                    <?php if (!empty($online_arr)) { ?>
                                                        <button class="nav-link active" id="nav-Online-Self-paced-tab" data-bs-toggle="tab" data-bs-target="#nav-Online-Self-paced" type="button" role="tab" aria-controls="nav-Online-Self-paced" aria-selected="true">Online Self-paced</button>
                                                    <?php } ?>

                                                    <?php if (!empty($classroom_arr)) { ?>
                                                        <button class="nav-link" id="nav-Classroom-tab" data-bs-toggle="tab" data-bs-target="#nav-Classroom" type="button" role="tab" aria-controls="nav-Classroom" aria-selected="false">Classroom</button>
                                                    <?php } ?>
                                                </div>
                                            </nav>
                                            <div class="tab-content" id="nav-tab-deliveryContent">
                                                <div class="tab-pane fade show active" id="nav-Online-Self-paced" role="tabpanel" aria-labelledby="nav-Online-Self-paced-tab">
                                                    <div class="description-box">
                                                        <?php if ($self_paced) { ?>
                                                            <?php
                                                            echo wpautop($self_paced);
                                                            ?>
                                                        <?php } ?>

                                                        <div class="select-class content-margin-small">
                                                            <?php if (!empty($online_arr)) { ?>
                                                                <?php foreach ($online_arr as $key => $online) { ?>
                                                                    <?php
                                                                    $_start_date = $online['_start_date'];
                                                                    $_end_date = $online['_end_date'];
                                                                    $variation_name = str_replace(get_the_title() . ' - ', '', get_the_title($key));
                                                                    ?>
                                                                    <div class="row button-row g-0" variation_id="<?= $key ?>" variation_name="<?= str_replace(get_the_title() . ' - ', '', get_the_title($key)) ?>" variation_price="<?= strip_tags($GetData->product_price($key)) ?>">
                                                                        <div class="d-none variation-price"></div>
                                                                        <div class="col-md-8">
                                                                            <div class="date d-flex align-items-center <?= $_start_date && $_end_date ? 'mw-100' : '' ?>">
                                                                                <?php if ($_start_date && $_end_date) { ?>
                                                                                    <span><?= date_format(date_create($_start_date), "D jS M, Y"); ?></span>
                                                                                    <span class="icon"></span>
                                                                                    <span class="text-end"><?= date_format(date_create($_end_date), "D jS M, Y"); ?></span>
                                                                                <?php } else { ?>
                                                                                    <span><?= $variation_name ?></span>
                                                                                <?php }  ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4 text-end">
                                                                            <span class="budget">
                                                                                <?= $GetData->product_price($key) ?>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </div>


                                                    </div>
                                                </div>
                                                <?php if (!empty($classroom_arr)) { ?>
                                                    <div class="tab-pane fade" id="nav-Classroom" role="tabpanel" aria-labelledby="nav-Classroom-tab">
                                                        <div class="related-products Classroom full-width content-margin ">
                                                            <div class="content">
                                                                <div class="select-class content-margin-small">
                                                                    <?php foreach ($classroom_arr as $key => $classroom) { ?>
                                                                        <?php
                                                                        $_start_date = $classroom['_start_date'];
                                                                        $_end_date = $classroom['_end_date'];
                                                                        $variation_name = str_replace(get_the_title() . ' - ', '', get_the_title($key));
                                                                        ?>
                                                                        <div class="row button-row g-0" variation_id="<?= $key ?>" variation_name="<?= $variation_name ?>" variation_price="<?= strip_tags($GetData->product_price($key)) ?>">
                                                                            <div class="col-md-8">
                                                                                <div class="date d-flex align-items-center <?= $_start_date && $_end_date ? 'mw-100' : '' ?>">
                                                                                    <?php if ($_start_date && $_end_date) { ?>
                                                                                        <span><?= date_format(date_create($_start_date), "D jS M, Y"); ?></span>
                                                                                        <span class="icon"></span>
                                                                                        <span class="text-end"><?= date_format(date_create($_end_date), "D jS M, Y"); ?></span>
                                                                                    <?php } else { ?>
                                                                                        <span><?= $variation_name ?></span>
                                                                                    <?php }  ?>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4 text-end">
                                                                                <span class="budget">
                                                                                    <?= $GetData->product_price($key) ?>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="nav-related-courses" role="tabpanel" aria-labelledby="nav-related-courses-tab">
                                        <div class="related-products related-courses full-width content-margin ">
                                            <div class="content">

                                                <section class="related-products">
                                                    <?= do_shortcode('[related_products limit="2" columns="2" orderby="popularity" class="quick-sale" ]') ?>
                                                </section>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="simple-add-to-cart select-class">
                            <div class="row justify-content-between">
                                <div class="col-auto">
                                    Course Price:
                                </div>
                                <div class="col-auto price-col">
                                    <?= $GetData->product_price(get_the_ID()) ?>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <?= request_info_button('button-bordered') ?>
                                </div>
                                <div class="col-6">
                                    <?php if ($product->get_type() == 'simple') { ?>
                                        <?= do_shortcode('[add_to_cart id="' . get_the_ID() . '" show_price="FALSE"]') ?>
                                    <?php } else { ?>
                                        <?= do_shortcode('[add_to_cart_form id="' . get_the_ID() . '" show_price="FALSE"]') ?>
                                    <?php } ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>

<script>
    var $offsetTop;
    jQuery(document).ready(function() {
        <?php if ($product->get_type() == 'variable') { ?>

            jQuery('.button-row').click(function(e) {
                $variation_id = jQuery(this).attr('variation_id');
                $variation_price = jQuery(this).attr('variation_price');
                jQuery('.add_to_cart_button').attr('data-product_id', $variation_id).removeClass('disabled');
                jQuery('.button-row').removeClass('active');
                jQuery('.price-col').text($variation_price);
                jQuery(this).addClass('active');
            });
        <?php } ?>
    });
</script>