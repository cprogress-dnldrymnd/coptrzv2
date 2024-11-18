<?php
function customizer_section_1($wp_customize)
{
    // Add a new section to the Customizer

    __add_section($wp_customize, 'section_1', 'Section 1');

    // Add a text control to the section
    $wp_customize->add_setting('my_page_text', array(
        'default'           => 'Default Text',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('my_page_text', array(
        'label'    => __('Page Text', 'coptrz'),
        'section'  => 'section_1',
        'type'     => 'text',
    ));
}
add_action('customize_register', 'customizer_section_1');


function __add_section($wp_customize, $section_id, $section_name)
{
    return $wp_customize->add_section($section_id, array(
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
