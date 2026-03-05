<section class="testimonial-v2">
    <div class="container">
        <div class="container-wrapper">
            <div class="row g-3 justify-content-between mb-5">
                <div class="col-lg-7">
                    <h2 class="heading-style">Trusted Where Failure Is Not An Option</h2>
                </div>
                <div class="col-auto"><div class="button-accent col-auto button-box"><a class="rounded-10px " href="#Benefits" target="_self">Speak to Our Enterprise Team</a></div></div>
            </div>
            <?php
            echo ____post_grid_module(array(
                'id'                      => 'testimonial-slider',
                'is_slider'               => true,
                'number_of_slides'        => 4,
                'number_of_slides_tablet' => 2,
                'number_of_slides_mobile' => 1,
                'post_grid_id'            => 'testimonial-slider',
                'post_elements'           => array(
                    array(
                        "_type"             => "icon",
                        "icon"              => "417802",
                        "icon_color"        => "text-accent",
                        "icon_color_custom" => "",
                        "icon_width"        => "",
                        "icon_height"       => ""
                    ),
                    array(
                        "_type"              => "custom_field_1",
                        "custom_field_key"   => "_testimonial_content",
                        "custom_field_type"  => "p",
                        "custom_field_class" => "testimonial-content"
                    ),
                    array(
                        "_type"             => "post_title",
                        "text_before"       => "-",
                        "text_after"        => "",
                        "tag"               => "p",
                        "text_color"        => "",
                        "text_color_custom" => ""
                    )
                ),
                'post_type'               => array(
                    array(
                        "_type" => "testimonials",
                    )
                )
            ));
            ?>
        </div>
    </div>
</section>