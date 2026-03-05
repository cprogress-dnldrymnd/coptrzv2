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
