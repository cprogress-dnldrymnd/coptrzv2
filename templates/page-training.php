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

<section class="training-product lg-padding-top lg-padding-top border-top-default">
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
                                        <div class="col-auto"><input name="delivery_method" value="" type="radio" id="Online" checked="">
                                            <label class="rounded-corner" for="Online">Online Self-paced</label>
                                        </div>
                                        <div class="col-auto"><input name="delivery_method" value="" type="radio" id="Classroom">
                                            <label class="rounded-corner" for="Classroom">Classroom</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="row g-3 align-items-center">
                                <div class="col-auto"><select name="category">
                                        <option value="">Category: All</option>

                                    </select>
                                </div>
                                <div class="col-auto">
                                    <select name="sort">
                                        <option value="ASC">Sort By: Latest</option>
                                        <option value="DESC">Sort By: Oldest</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="training-list">
            <div class="row">
                <div class="col-lg-6">
                    <?= custom_product_variation_training() ?>
                </div>
                <div class="col-lg-6">
                    <div class="image-box">
                        <img src="https://dev.coptrz.com/wp-content/uploads/2024/07/map.jpg" alt="">
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<div class="modules">
    <?php
    echo do_shortcode(___sections('sections_after_main', get_the_ID()));

    $sections = get__post_meta('sections');
    ?>


    <?php
    $new_sections = array();
    foreach ($sections as $section) {
        $new_sections[]['_type'] = 'section';
        $new_sections[]['sec_1'] = $section['sec_1'];
        $new_sections[]['title'] = $section['title'];
        $new_sections[]['section_id'] = $section['section_id'];
        $new_sections[]['section_class'] = $section['section_class'];
        $new_sections[]['disable_section'] = $section['disable_section'];
        $new_sections[]['section_items'] = $section['section_items'];
        $new_sections[]['html_3'] = $section['html_3'];
        $new_sections[]['section_styles'] = $section['section_styles'];
    }

    carbon_set_post_meta(get_the_ID(), 'sections', $new_sections);
    ?>
    <pre>
        <?php var_dump($new_sections); ?>
    </pre>
</div>
<?php get_footer(); ?>