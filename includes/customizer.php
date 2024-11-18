<?php
function my_theme_customize_register($wp_customize)
{
    // Add a new section to the Customizer
    $wp_customize->add_section('my_page_section', array(
        'title'    => __('My Page Settings', 'my-theme'),
        'priority' => 160, // Adjust the priority to control the section's position
        'active_callback' => function () {
            return get_page_template_slug() == 'templates/page-product-form.php';
        },
    ));

    // Add a text control to the section
    $wp_customize->add_setting('my_page_text', array(
        'default'           => 'Default Text',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('my_page_text', array(
        'label'    => __('Page Text', 'my-theme'),
        'section'  => 'my_page_section',
        'type'     => 'text',
    ));
}
add_action('customize_register', 'my_theme_customize_register');
