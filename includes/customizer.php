<?php
function customizer_section_1($wp_customize)
{
    // Add a new section to the Customizer
    $placeholder = 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Aliquid, asperiores! Et pariatur sunt animi. Hic, itaque eligendi explicabo eum aspernatur, numquam quidem facere eius debitis culpa veritatis? Odio, velit adipisci.';
    $image_placeholder = 'https://coptrz.com/wp-content/uploads/2023/05/placeholder-image.webp';

    /*Section 1*/
    __add_section($wp_customize, 'section_1', '--Section 1');
    __add_field($wp_customize, 'section_1', 'section_1_heading_prefix', 'Heading Prefix', 'Lorem ipsum', 'text');
    __add_field($wp_customize, 'section_1', 'section_1_heading', 'Heading', 'Lorem ipsum dolor sit amet', 'text');
    __add_field($wp_customize, 'section_1', 'section_1_description', 'Description', $placeholder, 'textarea');

    for ($section_1_col_num = 1; $section_1_col_num <= 3; $section_1_col_num++) {
        __add_field($wp_customize, 'section_1', 'section_1_col_' . $section_1_col_num . '_image', 'Image[Column ' . $section_1_col_num . ']', $image_placeholder, 'image');
        __add_field($wp_customize, 'section_1', 'section_1_col_' . $section_1_col_num . '_heading', 'Heading[Column ' . $section_1_col_num . ']', 'Lorem ipsum dolor ', 'text');
        __add_field($wp_customize, 'section_1', 'section_1_col_' . $section_1_col_num . '_description', 'Description[Column ' . $section_1_col_num . ']', $placeholder, 'textarea');
    }

    /*Section 2*/
    __add_section($wp_customize, 'section_2', '--Section 2');
    __add_field($wp_customize, 'section_2', 'section_2_heading', 'Heading', 'Lorem ipsum dolor sit amet', 'text');
    __add_field($wp_customize, 'section_2', 'section_2_description', 'Description', $placeholder, 'textarea');


    for ($section_2_col_num = 1; $section_2_col_num <= 2; $section_2_col_num++) {
        __add_field($wp_customize, 'section_2', 'section_2_col_' . $section_2_col_num . '_image', 'Image[Column ' . $section_2_col_num . ']', $image_placeholder, 'image');
        __add_field($wp_customize, 'section_2', 'section_2_col_' . $section_2_col_num . '_heading', 'Heading[Column ' . $section_2_col_num . ']', 'Lorem ipsum dolor ', 'text');
        __add_field($wp_customize, 'section_2', 'section_2_col_' . $section_2_col_num . '_description', 'Description[Column ' . $section_2_col_num . ']', $placeholder, 'textarea');
    }

    /*Section 3*/
    __add_section($wp_customize, 'section_3', '--Section 3');
    __add_field($wp_customize, 'section_3', 'section_3_heading', 'Heading', 'Lorem ipsum dolor sit amet', 'text');
    __add_field($wp_customize, 'section_3', 'section_3_description', 'Description', $placeholder, 'textarea');


    for ($section_3_col_num = 1; $section_3_col_num <= 4; $section_3_col_num++) {
        __add_field($wp_customize, 'section_3', 'section_3_col_' . $section_3_col_num . '_image', 'Image[Column ' . $section_3_col_num . ']', $image_placeholder, 'image');
        __add_field($wp_customize, 'section_3', 'section_3_col_' . $section_3_col_num . '_heading', 'Heading[Column ' . $section_3_col_num . ']', 'Lorem ipsum dolor ', 'text');
        __add_field($wp_customize, 'section_3', 'section_3_col_' . $section_3_col_num . '_description', 'Description[Column ' . $section_3_col_num . ']', $placeholder, 'textarea');
    }
}
add_action('customize_register', 'customizer_section_1');


function __add_section($wp_customize, $section_id, $section_name)
{
    $wp_customize->add_section($section_id, array(
        'title'    => __($section_name, 'coptrz'),
        'priority' => 160, // Adjust the priority to control the section's position
        'active_callback' => function () {
            if (get_page_template_slug() == 'templates/page-product-form.php') {
                return true;
            } else {
                return false;
            }
        },
    ));
}

function __add_field($wp_customize, $section_id, $field_id, $field_name, $field_placeholder, $field_type)
{
    // Add a text control to the section
    $wp_customize->add_setting($field_id, array(
        'default'           => $field_placeholder,
        'sanitize_callback' => 'sanitize_text_field',
        'type' => 'option'
    ));


    if ($field_type == 'image') {
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, $field_id, array(
            'label' => __($field_name, 'coptrz'),
            'section' => $section_id,
        )));
    } else {
        $wp_customize->add_control($field_id, array(
            'label'    => __($field_name, 'coptrz'),
            'section'  => $section_id,
            'type'     => $field_type,
        ));
    }
}
