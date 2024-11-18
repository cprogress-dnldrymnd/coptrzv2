<?php
function customizer_section_1($wp_customize)
{
    // Add a new section to the Customizer

    __add_section($wp_customize, 'section_1', 'Section 1');
    __add_field($wp_customize, 'section_1', 'heading_prefix', 'Heading Prefix');
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

function __add_field($wp_customize, $section_id, $field_id, $field_name)
{
    // Add a text control to the section
    $wp_customize->add_setting($field_id, array(
        'default'           => 'Lorem ipsum',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control($field_id, array(
        'label'    => __($field_name, 'coptrz'),
        'section'  => $section_id,
        'type'     => 'text',
    ));
}
