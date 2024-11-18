<?php
function customizer_section_1($wp_customize)
{
    // Add a new section to the Customizer
    $placeholder = 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Aliquid, asperiores! Et pariatur sunt animi. Hic, itaque eligendi explicabo eum aspernatur, numquam quidem facere eius debitis culpa veritatis? Odio, velit adipisci.';
    __add_section($wp_customize, 'section_1', 'Section 1');
    __add_field($wp_customize, 'section_1', 'section_1_heading_prefix', 'Heading Prefix', 'Lorem ipsum', 'text');
    __add_field($wp_customize, 'section_1', 'section_1_heading', 'Heading', 'Lorem ipsum dolor sit amet', 'text');
    __add_field($wp_customize, 'section_1', 'section_1_description', 'Description', $placeholder, 'textarea');
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

    $wp_customize->add_control($field_id, array(
        'label'    => __($field_name, 'coptrz'),
        'section'  => $section_id,
        'type'     => $field_type,
    ));
}
