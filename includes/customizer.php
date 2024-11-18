<?php
function customizer_section_1($wp_customize)
{
    // Add a new section to the Customizer
    $wp_customize->add_section('section_1', array(
        'title'    => __('Section 1', 'coptrz'),
        'priority' => 160, // Adjust the priority to control the section's position
        'active_callback' => function () {
            if (get_page_template_slug() == 'templates/page-product-form.php') {
                return true;
            } else {
                return false;
            }
        },
    ));

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
