<?php

use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Carbon_Fields\Complex_Container;
use Carbon_Fields\Field;



/*-----------------------------------------------------------------------------------*/
/* Theme Settings
/*-----------------------------------------------------------------------------------*/

function __latest_from_coptrz_fields()
{
    return  array(
        Field::make('complex', 'latest_from_coptrz')
            ->add_fields(array(
                Field::make('text', 'label', __('Label'))->set_classes('inline-field'),
                Field::make('text', 'button_text', __('Button Text'))->set_classes('inline-field'),
                Field::make('select', 'background', __('Background'))->set_classes('inline-field')
                    ->set_options(
                        array(
                            'featured-image' => 'Featured Image',
                            'bg-primary'      => 'Background Primary',
                            'bg-secondary'      => 'Background Secondary',
                            'bg-accent'      => 'Background Accent',
                            'bg-white' => 'Background White',
                        )
                    ),
                Field::make('checkbox', 'is_new', __('Is New'))->set_classes('inline-field'),
                Field::make('association', 'post', 'Select Post')->set_classes('inline-field')
                    ->set_types(
                        array(
                            array(
                                'type'      => 'post',
                                'post_type' => 'post',
                            ),
                            array(
                                'type'      => 'post',
                                'post_type' => 'product',
                            ),
                            array(
                                'type'      => 'post',
                                'post_type' => 'guides',
                            )
                        )
                    )
                    ->set_max(1)
            ))
            ->set_header_template('<%- label %>')
            ->set_collapsed(true)

    );
}

function __reviews_field()
{
    return array(
        Field::make('complex', 'reviews')
            ->add_fields(array(
                Field::make('text', 'review_label', __('Review Label'))->set_classes('inline-field'),
                Field::make('text', 'review_score', __('Review Score'))->set_classes('inline-field'),
                Field::make('text', 'review_text', __('Review Text'))->set_classes('inline-field'),
                Field::make('image', 'review_logo', __('Review Logo'))->set_classes('inline-field'),

            ))
            ->set_header_template('<%- review_label %>')
            ->set_collapsed(true)

    );
}

function __featured_case_studies()
{
    return array(
        Field::make('association', 'casestudies_featured', '')
            ->set_types(
                array(
                    array(
                        'type'      => 'post',
                        'post_type' => 'casestudies',
                    )
                )
            )
    );
}
function __general_settings_fields()
{
    return array(
        Field::make('image', 'logo', 'Logo')->set_classes('inline-field inline-field-wide-label'),
    );
}
function __social_fields()
{
    return array(
        Field::make('complex', 'socials')
            ->add_fields('facebook', array(
                Field::make('text', 'url', __('Facebook URL'))->set_classes('inline-field'),
            ))
            ->add_fields('instagram', array(
                Field::make('text', 'url', __('Instagram URL'))->set_classes('inline-field'),
            ))
            ->add_fields('x', array(
                Field::make('text', 'url', __('X URL'))->set_classes('inline-field'),
            ))
            ->add_fields('linkedin', array(
                Field::make('text', 'url', __('Linkedin URL'))->set_classes('inline-field'),
            ))
            ->add_fields('youtube', array(
                Field::make('text', 'url', __('Youtube URL'))->set_classes('inline-field'),
            ))
            ->set_duplicate_groups_allowed(false)
            ->set_collapsed(true)

    );
}
Container::make('theme_options', __('Theme Settings'))
    ->add_tab('General Settings', __general_settings_fields())
    ->add_tab('Socials', __social_fields());

Container::make('theme_options', __('Global Widgets'))
    ->add_tab('Latest From Coptrz', __latest_from_coptrz_fields())
    ->add_tab('Reviews', __reviews_field())
    ->add_tab('Featured Case Studies', __featured_case_studies());



/*-----------------------------------------------------------------------------------*/
/* Archives Settings
/*-----------------------------------------------------------------------------------*/

Container::make('theme_options', __('Archives Settings'))
    ->add_tab(
        'Posts',
        array(
            Field::make('html', 'post_hero')->set_html('<label>Hero Settings</label>')->set_classes('cb-label'),
            Field::make('text', 'post_archive_title', __('Archvie Hero Title'))->set_classes('inline-field inline-field-wide-label'),
            Field::make('textarea', 'post_archive_description', __('Archvie Hero Description'))->set_classes('inline-field inline-field-wide-label'),
            Field::make('select', 'post_archive_hero_background_type', __('Background Type'))->set_classes('inline-field inline-field-wide-label')
                ->set_options(
                    array(
                        'self-hosted' => 'Self Hosted',
                        'youtube' => 'Youtube',
                    )
                ),
            Field::make('select', 'post_archive_hero_height', __('Height'))->set_classes('inline-field inline-field-wide-label')
                ->set_options(
                    array(
                        '' => 'Default',
                        'medium-hero' => 'Medium',
                        'small-hero' => 'Small',
                    )
                ),
            Field::make('select', 'post_archive_hero_alignment', __('Aligment'))->set_classes('inline-field inline-field-wide-label')
                ->set_options(
                    array(
                        'text-center' => 'Default/Center',
                        'text-start' => 'Left',
                        'text-end' => 'Right',
                    )
                ),
            Field::make('file', 'post_archive_hero_background', __('Background'))->set_classes('inline-field inline-field-wide-label')->set_type(array('video', 'image'))
                ->set_conditional_logic(
                    array(
                        array(
                            'field' => 'post_archive_hero_background_type',
                            'value' => 'self-hosted',
                        )
                    )
                ),
            Field::make('text', 'post_archive_background_youtube', __('Background Youtube ID'))->set_classes('inline-field inline-field-wide-label')
                ->set_conditional_logic(
                    array(
                        array(
                            'field' => 'post_archive_hero_background_type',
                            'value' => 'youtube',
                        )
                    )
                ),
            Field::make('complex', 'post_archive_hero_buttons', __('Buttons'))->set_classes('inline-field inline-field-wide-label')
                ->setup_labels(
                    array(
                        'plural_name'   => 'Buttons',
                        'singular_name' => 'Button',
                    )
                )
                ->add_fields(array(
                    Field::make('select', 'button_type', __('Button Type'))->set_classes('trigger-selector inline-field inline-field-wide-label')
                        ->set_options(
                            array(
                                ''          => 'Select Button Type',
                                'page'      => 'Page',
                                'product'      => 'Product',
                                'guides'      => 'Guides',
                                'casestudies'      => 'Case Studies',
                                'post'      => 'Post',
                                'solutions' => 'Solution',
                                'popups'    => 'Popup',
                                'custom'     => 'Custom',
                            )
                        ),
                    Field::make('text', 'button_text', __('Button Text'))->set_classes('inline-field inline-field-wide-label'),
                    Field::make('text', 'button_url', __('Button URL'))->set_classes('field-url inline-field inline-field-wide-label')
                        ->set_conditional_logic(
                            array(
                                array(
                                    'field'   => 'button_type',
                                    'value'   => 'custom',
                                    'compare' => '!='
                                )
                            )
                        ),
                    Field::make('html', 'html')
                        ->set_html('<div class="page-selector">  </div>'),
                    Field::make('text', 'button_url_custom', __('Button URL'))->set_classes('inline-field inline-field-wide-label')
                        ->set_conditional_logic(
                            array(
                                array(
                                    'field' => 'button_type',
                                    'value' => 'custom',
                                )
                            )
                        ),
                    Field::make('select', 'button_style', __('Button Style'))->set_classes('inline-field inline-field-wide-label')
                        ->set_options(
                            array(
                                'button-accent'      => 'Accent',
                                'button-primary'      => 'Primary',
                                'button-secondary' => 'Secondary',
                                'button-white' => 'White',
                                'button-bordered'    => 'Bordered',
                            )
                        ),
                    Field::make('select', 'button_target', __('Button Target'))->set_classes('inline-field inline-field-wide-label')
                        ->set_options(
                            array(
                                'target="_self"'      => 'Default',
                                'target="_blank"'      => 'New Tab',
                            )
                        ),
                ))
                ->set_header_template('Button: <%- button_text %>'),
            Field::make('html', 'post_featured_html')->set_html('<label>Featured Posts</label>')->set_classes('cb-label'),
            Field::make('association', 'post_featured', '')
                ->set_types(
                    array(
                        array(
                            'type'      => 'post',
                            'post_type' => 'post',
                        )
                    )
                )
        ),

    )
    ->add_tab(
        'Events',
        array(
            Field::make('html', 'events_hero')->set_html('<label>Hero Settings</label>')->set_classes('cb-label'),
            Field::make('text', 'events_archive_title', __('Archvie Hero Title'))->set_classes('inline-field inline-field-wide-label'),
            Field::make('textarea', 'events_archive_description', __('Archvie Hero Description'))->set_classes('inline-field inline-field-wide-label'),
            Field::make('select', 'events_archive_hero_background_type', __('Background Type'))->set_classes('inline-field inline-field-wide-label')
                ->set_options(
                    array(
                        'self-hosted' => 'Self Hosted',
                        'youtube' => 'Youtube',
                    )
                ),
            Field::make('select', 'events_archive_hero_height', __('Height'))->set_classes('inline-field inline-field-wide-label')
                ->set_options(
                    array(
                        '' => 'Default',
                        'medium-hero' => 'Medium',
                        'small-hero' => 'Small',
                    )
                ),
            Field::make('select', 'events_archive_hero_alignment', __('Aligment'))->set_classes('inline-field inline-field-wide-label')
                ->set_options(
                    array(
                        'text-center' => 'Default/Center',
                        'text-start' => 'Left',
                        'text-end' => 'Right',
                    )
                ),
            Field::make('file', 'events_archive_hero_background', __('Background'))->set_classes('inline-field inline-field-wide-label')->set_type(array('video', 'image'))
                ->set_conditional_logic(
                    array(
                        array(
                            'field' => 'events_archive_hero_background_type',
                            'value' => 'self-hosted',
                        )
                    )
                ),
            Field::make('text', 'events_archive_background_youtube', __('Background Youtube ID'))->set_classes('inline-field inline-field-wide-label')
                ->set_conditional_logic(
                    array(
                        array(
                            'field' => 'events_archive_hero_background_type',
                            'value' => 'youtube',
                        )
                    )
                ),
            Field::make('complex', 'events_archive_hero_buttons', __('Buttons'))->set_classes('inline-field inline-field-wide-label')
                ->setup_labels(
                    array(
                        'plural_name'   => 'Buttons',
                        'singular_name' => 'Button',
                    )
                )
                ->add_fields(array(
                    Field::make('select', 'button_type', __('Button Type'))->set_classes('trigger-selector inline-field inline-field-wide-label')
                        ->set_options(
                            array(
                                ''          => 'Select Button Type',
                                'page'      => 'Page',
                                'product'      => 'Product',
                                'guides'      => 'Guides',
                                'casestudies'      => 'Case Studies',
                                'post'      => 'Post',
                                'solutions' => 'Solution',
                                'popups'    => 'Popup',
                                'custom'     => 'Custom',
                            )
                        ),
                    Field::make('text', 'button_text', __('Button Text'))->set_classes('inline-field inline-field-wide-label'),
                    Field::make('text', 'button_url', __('Button URL'))->set_classes('field-url inline-field inline-field-wide-label')
                        ->set_conditional_logic(
                            array(
                                array(
                                    'field'   => 'button_type',
                                    'value'   => 'custom',
                                    'compare' => '!='
                                )
                            )
                        ),
                    Field::make('html', 'html')
                        ->set_html('<div class="page-selector">  </div>'),
                    Field::make('text', 'button_url_custom', __('Button URL'))->set_classes('inline-field inline-field-wide-label')
                        ->set_conditional_logic(
                            array(
                                array(
                                    'field' => 'button_type',
                                    'value' => 'custom',
                                )
                            )
                        ),
                    Field::make('select', 'button_style', __('Button Style'))->set_classes('inline-field inline-field-wide-label')
                        ->set_options(
                            array(
                                'button-accent'      => 'Accent',
                                'button-primary'      => 'Primary',
                                'button-secondary' => 'Secondary',
                                'button-white' => 'White',
                                'button-bordered'    => 'Bordered',
                            )
                        ),
                    Field::make('select', 'button_target', __('Button Target'))->set_classes('inline-field inline-field-wide-label')
                        ->set_options(
                            array(
                                'target="_self"'      => 'Default',
                                'target="_blank"'      => 'New Tab',
                            )
                        ),
                ))
                ->set_header_template('Button: <%- button_text %>'),
        )
    )
    ->add_tab(
        'Capabilities',
        array(
            Field::make('html', 'capabilities_hero')->set_html('<label>Hero Settings</label>')->set_classes('cb-label'),
            Field::make('text', 'capabilities_archive_title', __('Archvie Hero Title'))->set_classes('inline-field inline-field-wide-label'),
            Field::make('textarea', 'capabilities_archive_description', __('Archvie Hero Description'))->set_classes('inline-field inline-field-wide-label'),
            Field::make('select', 'capabilities_archive_hero_background_type', __('Background Type'))->set_classes('inline-field inline-field-wide-label')
                ->set_options(
                    array(
                        'self-hosted' => 'Self Hosted',
                        'youtube' => 'Youtube',
                    )
                ),
            Field::make('select', 'capabilities_archive_hero_height', __('Height'))->set_classes('inline-field inline-field-wide-label')
                ->set_options(
                    array(
                        '' => 'Default',
                        'medium-hero' => 'Medium',
                        'small-hero' => 'Small',
                    )
                ),
            Field::make('select', 'capabilities_archive_hero_alignment', __('Aligment'))->set_classes('inline-field inline-field-wide-label')
                ->set_options(
                    array(
                        'text-center' => 'Default/Center',
                        'text-start' => 'Left',
                        'text-end' => 'Right',
                    )
                ),
            Field::make('file', 'capabilities_archive_hero_background', __('Background'))->set_classes('inline-field inline-field-wide-label')->set_type(array('video', 'image'))
                ->set_conditional_logic(
                    array(
                        array(
                            'field' => 'capabilities_archive_hero_background_type',
                            'value' => 'self-hosted',
                        )
                    )
                ),
            Field::make('text', 'capabilities_archive_background_youtube', __('Background Youtube ID'))->set_classes('inline-field inline-field-wide-label')
                ->set_conditional_logic(
                    array(
                        array(
                            'field' => 'capabilities_archive_hero_background_type',
                            'value' => 'youtube',
                        )
                    )
                ),
            Field::make('complex', 'capabilities_archive_hero_buttons', __('Buttons'))->set_classes('inline-field inline-field-wide-label')
                ->setup_labels(
                    array(
                        'plural_name'   => 'Buttons',
                        'singular_name' => 'Button',
                    )
                )
                ->add_fields(array(
                    Field::make('select', 'button_type', __('Button Type'))->set_classes('trigger-selector inline-field inline-field-wide-label')
                        ->set_options(
                            array(
                                ''          => 'Select Button Type',
                                'page'      => 'Page',
                                'product'      => 'Product',
                                'guides'      => 'Guides',
                                'casestudies'      => 'Case Studies',
                                'post'      => 'Post',
                                'solutions' => 'Solution',
                                'popups'    => 'Popup',
                                'custom'     => 'Custom',
                            )
                        ),
                    Field::make('text', 'button_text', __('Button Text'))->set_classes('inline-field inline-field-wide-label'),
                    Field::make('text', 'button_url', __('Button URL'))->set_classes('field-url inline-field inline-field-wide-label')
                        ->set_conditional_logic(
                            array(
                                array(
                                    'field'   => 'button_type',
                                    'value'   => 'custom',
                                    'compare' => '!='
                                )
                            )
                        ),
                    Field::make('html', 'html')
                        ->set_html('<div class="page-selector">  </div>'),
                    Field::make('text', 'button_url_custom', __('Button URL'))->set_classes('inline-field inline-field-wide-label')
                        ->set_conditional_logic(
                            array(
                                array(
                                    'field' => 'button_type',
                                    'value' => 'custom',
                                )
                            )
                        ),
                    Field::make('select', 'button_style', __('Button Style'))->set_classes('inline-field inline-field-wide-label')
                        ->set_options(
                            array(
                                'button-accent'      => 'Accent',
                                'button-primary'      => 'Primary',
                                'button-secondary' => 'Secondary',
                                'button-white' => 'White',
                                'button-bordered'    => 'Bordered',
                            )
                        ),
                    Field::make('select', 'button_target', __('Button Target'))->set_classes('inline-field inline-field-wide-label')
                        ->set_options(
                            array(
                                'target="_self"'      => 'Default',
                                'target="_blank"'      => 'New Tab',
                            )
                        ),
                ))
                ->set_header_template('Button: <%- button_text %>'),

        )
    )
    ->add_tab(
        'Industries',
        array(
            Field::make('html', 'solutions_hero')->set_html('<label>Hero Settings</label>')->set_classes('cb-label'),
            Field::make('text', 'solutions_archive_title', __('Archvie Hero Title'))->set_classes('inline-field inline-field-wide-label'),
            Field::make('textarea', 'solutions_archive_description', __('Archvie Hero Description'))->set_classes('inline-field inline-field-wide-label'),
            Field::make('select', 'solutions_archive_hero_background_type', __('Background Type'))->set_classes('inline-field inline-field-wide-label')
                ->set_options(
                    array(
                        'self-hosted' => 'Self Hosted',
                        'youtube' => 'Youtube',
                    )
                ),
            Field::make('select', 'solutions_archive_hero_height', __('Height'))->set_classes('inline-field inline-field-wide-label')
                ->set_options(
                    array(
                        '' => 'Default',
                        'medium-hero' => 'Medium',
                        'small-hero' => 'Small',
                    )
                ),
            Field::make('select', 'solutions_archive_hero_alignment', __('Aligment'))->set_classes('inline-field inline-field-wide-label')
                ->set_options(
                    array(
                        'text-center' => 'Default/Center',
                        'text-start' => 'Left',
                        'text-end' => 'Right',
                    )
                ),
            Field::make('file', 'solutions_archive_hero_background', __('Background'))->set_classes('inline-field inline-field-wide-label')->set_type(array('video', 'image'))
                ->set_conditional_logic(
                    array(
                        array(
                            'field' => 'solutions_archive_hero_background_type',
                            'value' => 'self-hosted',
                        )
                    )
                ),
            Field::make('text', 'solutions_archive_background_youtube', __('Background Youtube ID'))->set_classes('inline-field inline-field-wide-label')
                ->set_conditional_logic(
                    array(
                        array(
                            'field' => 'solutions_archive_hero_background_type',
                            'value' => 'youtube',
                        )
                    )
                ),
            Field::make('complex', 'solutions_archive_hero_buttons', __('Buttons'))->set_classes('inline-field inline-field-wide-label')
                ->setup_labels(
                    array(
                        'plural_name'   => 'Buttons',
                        'singular_name' => 'Button',
                    )
                )
                ->add_fields(array(
                    Field::make('select', 'button_type', __('Button Type'))->set_classes('trigger-selector inline-field inline-field-wide-label')
                        ->set_options(
                            array(
                                ''          => 'Select Button Type',
                                'page'      => 'Page',
                                'product'      => 'Product',
                                'guides'      => 'Guides',
                                'casestudies'      => 'Case Studies',
                                'post'      => 'Post',
                                'solutions' => 'Solution',
                                'popups'    => 'Popup',
                                'custom'     => 'Custom',
                            )
                        ),
                    Field::make('text', 'button_text', __('Button Text'))->set_classes('inline-field inline-field-wide-label'),
                    Field::make('text', 'button_url', __('Button URL'))->set_classes('field-url inline-field inline-field-wide-label')
                        ->set_conditional_logic(
                            array(
                                array(
                                    'field'   => 'button_type',
                                    'value'   => 'custom',
                                    'compare' => '!='
                                )
                            )
                        ),
                    Field::make('html', 'html')
                        ->set_html('<div class="page-selector">  </div>'),
                    Field::make('text', 'button_url_custom', __('Button URL'))->set_classes('inline-field inline-field-wide-label')
                        ->set_conditional_logic(
                            array(
                                array(
                                    'field' => 'button_type',
                                    'value' => 'custom',
                                )
                            )
                        ),
                    Field::make('select', 'button_style', __('Button Style'))->set_classes('inline-field inline-field-wide-label')
                        ->set_options(
                            array(
                                'button-accent'      => 'Accent',
                                'button-primary'      => 'Primary',
                                'button-secondary' => 'Secondary',
                                'button-white' => 'White',
                                'button-bordered'    => 'Bordered',
                            )
                        ),
                    Field::make('select', 'button_target', __('Button Target'))->set_classes('inline-field inline-field-wide-label')
                        ->set_options(
                            array(
                                'target="_self"'      => 'Default',
                                'target="_blank"'      => 'New Tab',
                            )
                        ),
                ))
                ->set_header_template('Button: <%- button_text %>'),

        )
    )
    ->add_tab(
        'Case Studies',
        array(
            Field::make('html', 'casestudies_hero')->set_html('<label>Hero Settings</label>')->set_classes('cb-label'),
            Field::make('text', 'casestudies_archive_title', __('Archvie Hero Title'))->set_classes('inline-field inline-field-wide-label'),
            Field::make('textarea', 'casestudies_archive_description', __('Archvie Hero Description'))->set_classes('inline-field inline-field-wide-label'),
            Field::make('select', 'casestudies_archive_hero_background_type', __('Background Type'))->set_classes('inline-field inline-field-wide-label')
                ->set_options(
                    array(
                        'self-hosted' => 'Self Hosted',
                        'youtube' => 'Youtube',
                    )
                ),
            Field::make('select', 'casestudies_archive_hero_height', __('Height'))->set_classes('inline-field inline-field-wide-label')
                ->set_options(
                    array(
                        '' => 'Default',
                        'medium-hero' => 'Medium',
                        'small-hero' => 'Small',
                    )
                ),
            Field::make('select', 'casestudies_archive_hero_alignment', __('Aligment'))->set_classes('inline-field inline-field-wide-label')
                ->set_options(
                    array(
                        'text-center' => 'Default/Center',
                        'text-start' => 'Left',
                        'text-end' => 'Right',
                    )
                ),
            Field::make('file', 'casestudies_archive_hero_background', __('Background'))->set_classes('inline-field inline-field-wide-label')->set_type(array('video', 'image'))
                ->set_conditional_logic(
                    array(
                        array(
                            'field' => 'casestudies_archive_hero_background_type',
                            'value' => 'self-hosted',
                        )
                    )
                ),
            Field::make('text', 'casestudies_archive_background_youtube', __('Background Youtube ID'))->set_classes('inline-field inline-field-wide-label')
                ->set_conditional_logic(
                    array(
                        array(
                            'field' => 'casestudies_archive_hero_background_type',
                            'value' => 'youtube',
                        )
                    )
                ),
            Field::make('complex', 'casestudies_archive_hero_buttons', __('Buttons'))->set_classes('inline-field inline-field-wide-label')
                ->setup_labels(
                    array(
                        'plural_name'   => 'Buttons',
                        'singular_name' => 'Button',
                    )
                )
                ->add_fields(array(
                    Field::make('select', 'button_type', __('Button Type'))->set_classes('trigger-selector inline-field inline-field-wide-label')
                        ->set_options(
                            array(
                                ''          => 'Select Button Type',
                                'page'      => 'Page',
                                'product'      => 'Product',
                                'guides'      => 'Guides',
                                'casestudies'      => 'Case Studies',
                                'post'      => 'Post',
                                'solutions' => 'Solution',
                                'popups'    => 'Popup',
                                'custom'     => 'Custom',
                            )
                        ),
                    Field::make('text', 'button_text', __('Button Text'))->set_classes('inline-field inline-field-wide-label'),
                    Field::make('text', 'button_url', __('Button URL'))->set_classes('field-url inline-field inline-field-wide-label')
                        ->set_conditional_logic(
                            array(
                                array(
                                    'field'   => 'button_type',
                                    'value'   => 'custom',
                                    'compare' => '!='
                                )
                            )
                        ),
                    Field::make('html', 'html')
                        ->set_html('<div class="page-selector">  </div>'),
                    Field::make('text', 'button_url_custom', __('Button URL'))->set_classes('inline-field inline-field-wide-label')
                        ->set_conditional_logic(
                            array(
                                array(
                                    'field' => 'button_type',
                                    'value' => 'custom',
                                )
                            )
                        ),
                    Field::make('select', 'button_style', __('Button Style'))->set_classes('inline-field inline-field-wide-label')
                        ->set_options(
                            array(
                                'button-accent'      => 'Accent',
                                'button-primary'      => 'Primary',
                                'button-secondary' => 'Secondary',
                                'button-white' => 'White',
                                'button-bordered'    => 'Bordered',
                            )
                        ),
                    Field::make('select', 'button_target', __('Button Target'))->set_classes('inline-field inline-field-wide-label')
                        ->set_options(
                            array(
                                'target="_self"'      => 'Default',
                                'target="_blank"'      => 'New Tab',
                            )
                        ),
                ))
                ->set_header_template('Button: <%- button_text %>'),
            Field::make('html', 'casestudies_featured_html')->set_html('<label>Featured Posts</label>')->set_classes('cb-label'),
            Field::make('association', 'casestudies_featured', '')
                ->set_types(
                    array(
                        array(
                            'type'      => 'post',
                            'post_type' => 'casestudies',
                        )
                    )
                )

        )
    )
    ->add_tab(
        'Guides',
        array(
            Field::make('html', 'guides_hero')->set_html('<label>Hero Settings</label>')->set_classes('cb-label'),
            Field::make('text', 'guides_archive_title', __('Archvie Hero Title'))->set_classes('inline-field inline-field-wide-label'),
            Field::make('textarea', 'guides_archive_description', __('Archvie Hero Description'))->set_classes('inline-field inline-field-wide-label'),
            Field::make('select', 'guides_archive_hero_background_type', __('Background Type'))->set_classes('inline-field inline-field-wide-label')
                ->set_options(
                    array(
                        'self-hosted' => 'Self Hosted',
                        'youtube' => 'Youtube',
                    )
                ),
            Field::make('select', 'guides_archive_hero_height', __('Height'))->set_classes('inline-field inline-field-wide-label')
                ->set_options(
                    array(
                        '' => 'Default',
                        'medium-hero' => 'Medium',
                        'small-hero' => 'Small',
                    )
                ),
            Field::make('select', 'guides_archive_hero_alignment', __('Aligment'))->set_classes('inline-field inline-field-wide-label')
                ->set_options(
                    array(
                        'text-center' => 'Default/Center',
                        'text-start' => 'Left',
                        'text-end' => 'Right',
                    )
                ),
            Field::make('file', 'guides_archive_hero_background', __('Background'))->set_classes('inline-field inline-field-wide-label')->set_type(array('video', 'image'))
                ->set_conditional_logic(
                    array(
                        array(
                            'field' => 'guides_archive_hero_background_type',
                            'value' => 'self-hosted',
                        )
                    )
                ),
            Field::make('text', 'guides_archive_background_youtube', __('Background Youtube ID'))->set_classes('inline-field inline-field-wide-label')
                ->set_conditional_logic(
                    array(
                        array(
                            'field' => 'guides_archive_hero_background_type',
                            'value' => 'youtube',
                        )
                    )
                ),
            Field::make('complex', 'guides_archive_hero_buttons', __('Buttons'))->set_classes('inline-field inline-field-wide-label')
                ->setup_labels(
                    array(
                        'plural_name'   => 'Buttons',
                        'singular_name' => 'Button',
                    )
                )
                ->add_fields(array(
                    Field::make('select', 'button_type', __('Button Type'))->set_classes('trigger-selector inline-field inline-field-wide-label')
                        ->set_options(
                            array(
                                ''          => 'Select Button Type',
                                'page'      => 'Page',
                                'product'      => 'Product',
                                'guides'      => 'Guides',
                                'guides'      => 'Case Studies',
                                'post'      => 'Post',
                                'solutions' => 'Solution',
                                'popups'    => 'Popup',
                                'custom'     => 'Custom',
                            )
                        ),
                    Field::make('text', 'button_text', __('Button Text'))->set_classes('inline-field inline-field-wide-label'),
                    Field::make('text', 'button_url', __('Button URL'))->set_classes('field-url inline-field inline-field-wide-label')
                        ->set_conditional_logic(
                            array(
                                array(
                                    'field'   => 'button_type',
                                    'value'   => 'custom',
                                    'compare' => '!='
                                )
                            )
                        ),
                    Field::make('html', 'html')
                        ->set_html('<div class="page-selector">  </div>'),
                    Field::make('text', 'button_url_custom', __('Button URL'))->set_classes('inline-field inline-field-wide-label')
                        ->set_conditional_logic(
                            array(
                                array(
                                    'field' => 'button_type',
                                    'value' => 'custom',
                                )
                            )
                        ),
                    Field::make('select', 'button_style', __('Button Style'))->set_classes('inline-field inline-field-wide-label')
                        ->set_options(
                            array(
                                'button-accent'      => 'Accent',
                                'button-primary'      => 'Primary',
                                'button-secondary' => 'Secondary',
                                'button-white' => 'White',
                                'button-bordered'    => 'Bordered',
                            )
                        ),
                    Field::make('select', 'button_target', __('Button Target'))->set_classes('inline-field inline-field-wide-label')
                        ->set_options(
                            array(
                                'target="_self"'      => 'Default',
                                'target="_blank"'      => 'New Tab',
                            )
                        ),
                ))
                ->set_header_template('Button: <%- button_text %>'),
            Field::make('html', 'guides_featured_html')->set_html('<label>Featured Posts</label>')->set_classes('cb-label'),
            Field::make('association', 'guides_featured', '')
                ->set_types(
                    array(
                        array(
                            'type'      => 'post',
                            'post_type' => 'guides',
                        )
                    )
                )

        )
    );



/*-----------------------------------------------------------------------------------*/
/* Hero
/*-----------------------------------------------------------------------------------*/

function __hero_fields()
{
    return array(
        Field::make('checkbox', 'hero_hidden', __('Hide Hero'))->set_width(20),
        Field::make('checkbox', 'breadcrumbs_hidden', __('Hide Breadcrumbs'))->set_width(80),
        Field::make('text', 'hero_heading', __('Heading'))->set_help_text('')->set_classes('inline-field')->set_attribute('placeholder', 'Defaults to page title'),
        Field::make('textarea', 'hero_description', __('Description'))->set_width(80)->set_classes('editor-field inline-field'),
        Field::make('html', 'activate_wysiwyg')->set_width(20)
            ->set_html('<a class="button button-primary button-large wysiwyg-editor-trigger" >Wysiwyg Editor</a>'),
        Field::make('select', 'hero_background_type', __('Background Type'))->set_classes('inline-field')
            ->set_options(
                array(
                    'self-hosted' => 'Self Hosted',
                    'youtube' => 'Youtube',
                )
            ),
        Field::make('select', 'hero_height', __('Height'))->set_classes('inline-field')
            ->set_options(
                array(
                    '' => 'Default',
                    'medium-hero' => 'Medium',
                    'small-hero' => 'Small',
                )
            ),
        Field::make('select', 'hero_alignment', __('Aligment'))->set_classes('inline-field')
            ->set_options(
                array(
                    '' => 'Default',
                    'text-center' => 'Center',
                    'text-start' => 'Left',
                    'text-end' => 'Right',
                )
            ),
        Field::make('file', 'hero_background', __('Background'))->set_classes('inline-field')->set_type(array('video', 'image'))
            ->set_conditional_logic(
                array(
                    array(
                        'field' => 'hero_background_type',
                        'value' => 'self-hosted',
                    )
                )
            ),
        Field::make('text', 'hero_background_youtube', __('Background Youtube ID'))->set_classes('inline-field')
            ->set_conditional_logic(
                array(
                    array(
                        'field' => 'hero_background_type',
                        'value' => 'youtube',
                    )
                )
            ),

    );
}

function __hero_button_fields()
{
    return array(
        Field::make('complex', 'buttons', __('Buttons'))->set_classes('inline-field')
            ->setup_labels(
                array(
                    'plural_name'   => 'Buttons',
                    'singular_name' => 'Button',
                )
            )
            ->add_fields(array(
                Field::make('select', 'button_type', __('Button Type'))->set_classes('trigger-selector inline-field')
                    ->set_options(
                        array(
                            ''          => 'Select Button Type',
                            'page'      => 'Page',
                            'product'      => 'Product',
                            'guides'      => 'Guides',
                            'casestudies'      => 'Case Studies',
                            'post'      => 'Post',
                            'solutions' => 'Solution',
                            'popups'    => 'Popup',
                            'custom'     => 'Custom',
                        )
                    ),
                Field::make('text', 'button_text', __('Button Text'))->set_classes('inline-field'),
                Field::make('text', 'button_url', __('Button URL'))->set_classes('field-url')
                    ->set_conditional_logic(
                        array(
                            array(
                                'field'   => 'button_type',
                                'value'   => 'custom',
                                'compare' => '!='
                            )
                        )
                    ),
                Field::make('html', 'html')
                    ->set_html('<div class="page-selector">  </div>'),
                Field::make('text', 'button_url_custom', __('Button URL'))->set_classes('inline-field')
                    ->set_conditional_logic(
                        array(
                            array(
                                'field' => 'button_type',
                                'value' => 'custom',
                            )
                        )
                    ),
                Field::make('select', 'button_style', __('Button Style'))->set_classes('inline-field')
                    ->set_options(
                        array(
                            'button-accent'      => 'Accent',
                            'button-primary'      => 'Primary',
                            'button-secondary' => 'Secondary',
                            'button-white' => 'White',
                            'button-bordered'    => 'Bordered',
                        )
                    ),
                Field::make('select', 'button_target', __('Button Target'))->set_classes('inline-field')
                    ->set_options(
                        array(
                            'target="_self"'      => 'Default',
                            'target="_blank"'      => 'New Tab',
                        )
                    ),
            ))
            ->set_header_template('Button: <%- button_text %>'),
    );
}
function __hero_form_fields()
{
    return array(
        Field::make('checkbox', 'hero_form_enable', 'Enable Form Hero')->set_classes('inline-field'),
        Field::make('image', 'hero_form_image', 'Image')->set_classes('inline-field'),
        Field::make('text', 'hero_form_heading', 'Form Heading')->set_classes('inline-field'),
        Field::make('text', 'hero_form_description', 'Form Description')->set_classes('inline-field'),
        Field::make('select', 'hero_form_style', 'Style')->set_classes('inline-field')
            ->set_options(
                array(
                    ''   => 'Default',
                    'style-2' => 'Style 2',
                )
            ),
        Field::make('association', 'hero_form', 'Select Form')->set_classes('inline-field')
            ->set_types(
                array(
                    array(
                        'type'      => 'post',
                        'post_type' => 'wpforms',
                    )
                )
            )
            ->set_max(1)
    );
}
Container::make('post_meta', __('Hero'))
    ->where('post_type', '=', 'page')
    ->or_where('post_type', '=', 'product')
    ->or_where('post_type', '=', 'post')
    ->or_where('post_type', '=', 'capabilities')
    ->or_where('post_type', '=', 'casestudies')
    ->or_where('post_type', '=', 'solutions')
    ->or_where('post_type', '=', 'events')
    ->or_where('post_type', '=', 'guides')
    ->add_tab('Hero Settings', __hero_fields())
    ->add_tab('Hero Buttons', __hero_button_fields())
    ->add_tab('Hero Form', __hero_form_fields());

Container::make('term_meta', __('Hero'))
    ->where('term_taxonomy', '=', 'product_cat')
    ->or_where('term_taxonomy', '=', 'pa_brands')
    ->add_tab('Hero Settings', __hero_fields())
    ->add_tab('Hero Buttons', __hero_button_fields())
    ->add_tab('Hero Form', __hero_form_fields());


function __section_fields($name = 'sections')
{
    return array(
        Field::make('complex', $name, __(''))
            ->setup_labels(
                array(
                    'plural_name'   => 'Sections',
                    'singular_name' => 'Section',
                )
            )
            ->set_collapsed(true)

            ->add_fields(array(
                Field::make('html', 'sec_1')->set_html('<label>Section Settings</label>')->set_classes('cb-label'),
                Field::make('text', 'title', __('Section Title'))->set_required(true)->set_width(25),
                Field::make('text', 'section_id', __('Section ID'))->set_width(25),
                Field::make('text', 'section_class', __('Section Class'))->set_width(25),
                Field::make('checkbox', 'disable_section', __('Disable Section'))->set_width(25),
                Field::make('html', 'sec_2')->set_html('<label>Section Items</label>')->set_classes('cb-label'),
                Field::make('complex', 'section_items', __('Section Items'))
                    ->setup_labels(
                        array(
                            'plural_name'   => 'Section Items',
                            'singular_name' => 'Section Item',
                        )
                    )
                    ->set_collapsed(true)
                    ->add_fields('layouts', array(
                        Field::make('association', 'layouts', 'Select Layouts')
                            ->set_types(
                                array(
                                    array(
                                        'type'      => 'post',
                                        'post_type' => 'layouts',
                                    )
                                )
                            )
                    ))
                    ->add_fields('global_widgets',  array(
                        Field::make('complex', 'global_widgets')
                            ->add_fields('case_study_slider', array(
                                Field::make('html', 'html')->set_html('<h3>This will display featured case study slider section </h3>'),
                            ))
                            ->add_fields('latest_from_coptrz', array(
                                Field::make('html', 'html')->set_html('<h3>This will display latest from coptrz section </h3>'),
                            ))
                            ->add_fields('reviews', array(
                                Field::make('html', 'html')->set_html('<h3>This will display reviews from different platform </h3>'),
                            ))
                    ))
                    ->add_fields('global_post_box_selection',  array(
                        Field::make('select', 'source', __('Source'))
                            ->set_options(
                                array(
                                    'manually'      => 'Select Manually',
                                    'category'      => 'Select by Category',
                                )
                            ),
                        Field::make('association', 'post', 'Select Items')
                            ->set_types(
                                array(
                                    array(
                                        'type'      => 'post',
                                        'post_type' => 'globalpostboxes',
                                    )
                                )
                            )
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'source',
                                        'value' => 'manually',
                                    )
                                )
                            ),
                        Field::make('association', 'category', 'Select Categories')
                            ->set_types(
                                array(
                                    array(
                                        'type'      => 'term',
                                        'taxonomy' => 'global_post_boxes_category',
                                    )
                                )
                            )
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'source',
                                        'value' => 'category',
                                        'comapre' => '='
                                    )
                                )
                            ),


                    ))
                    ->add_fields('related_post',  array(
                        Field::make('select', 'source', __('Source'))
                            ->set_options(
                                array(
                                    ''      => 'Default Query',
                                    'post_type'      => 'Post Type',
                                )
                            ),
                        Field::make('complex', 'related_post', 'Related Posts')
                            ->add_fields('related_guides',  array(
                                Field::make('hidden', 'post_type')->set_default_value('guides')->set_classes('d-none'),
                                Field::make('hidden', 'field_key')->set_default_value('related_guides')->set_classes('d-none'),
                                Field::make('html', 'html')->set_html('<h3>This will display related guides. </h3>'),
                            ))
                            ->add_fields('related_casestudies',  array(
                                Field::make('hidden', 'post_type')->set_default_value('casestudies')->set_classes('d-none'),
                                Field::make('hidden', 'field_key')->set_default_value('related_casestudies')->set_classes('d-none'),
                                Field::make('html', 'html')->set_html('<h3>This will display related case studies </h3>'),
                            ))
                            ->add_fields('related_post',  array(
                                Field::make('hidden', 'post_type')->set_default_value('post')->set_classes('d-none'),
                                Field::make('hidden', 'field_key')->set_default_value('related_post')->set_classes('d-none'),
                                Field::make('html', 'html')->set_html('<h3>This will display related post </h3>'),
                            ))
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'source',
                                        'value' => 'post_type',
                                    )
                                )
                            )->set_max(1)
                    ))
                    ->add_fields('heading', array(
                        Field::make('html', 'html_1')->set_html('<label>Section Heading Options</label>')->set_classes('cb-label'),
                        Field::make('checkbox', 'has_prefix', __('Heading Has Prefix'))->set_width(20),
                        Field::make('checkbox', 'has_suffix', __('Heading Has Suffix'))->set_width(20),
                        Field::make('checkbox', 'has_custom_heading_settings', __('Custom Heading Settings'))->set_width(50),
                        Field::make('html', 'html_2')->set_html('<label>Section Heading Settings</label>')->set_classes('cb-label'),
                        Field::make('text', 'heading', __('Heading'))->set_classes('inline-field'),
                        Field::make('text', 'prefix', __('Prefix'))->set_classes('inline-field')
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'has_prefix',
                                        'value' => true,
                                    )
                                )
                            ),
                        Field::make('text', 'suffix', __('Suffix'))->set_classes('inline-field')
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'has_suffix',
                                        'value' => true,
                                    )
                                )
                            ),
                        Field::make('select', 'tag', __('Tag'))->set_width(20)
                            ->set_options(
                                array(
                                    'h1' => 'h1',
                                    'h2' => 'h2',
                                    'h3' => 'h3',
                                    'h4' => 'h4',
                                    'h5' => 'h5',
                                    'h6' => 'h6',
                                )
                            )
                            ->set_default_value('h2')
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'has_custom_heading_settings',
                                        'value' => true,
                                    )
                                )
                            ),
                        Field::make('select', 'size', __('Heading Size'))->set_width(20)
                            ->set_options(
                                array(
                                    '' => 'Default',
                                    'large-heading' => 'Large Heading',
                                    'medium-heading' => 'Medium Heading',
                                    'small-heading' => 'Small Heading',
                                )
                            )
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'has_custom_heading_settings',
                                        'value' => true,
                                    )
                                )
                            ),
                        Field::make('select', 'text_align', __('Text Align'))->set_width(20)
                            ->set_options(
                                array(
                                    '' => 'Default',
                                    'text-start' => 'Text Left',
                                    'text-center' => 'Text Center',
                                    'text-end' => 'Text Right',
                                    'text-justify' => 'Text Justify',
                                )
                            )
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'has_custom_heading_settings',
                                        'value' => true,
                                    )
                                )
                            ),
                        Field::make('select', 'text_color', 'Text Color')->set_width(20)
                            ->set_options(
                                array(
                                    ''   => 'Default',
                                    'text-primary'   => 'Primary',
                                    'text-secondary' => 'Secondary',
                                    'text-accent'    => 'Accent',
                                    'text-white'     => 'White',
                                    'text-light-gray'     => 'Light Gray',
                                    'text-custom'    => 'Custom',
                                )
                            )
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'has_custom_heading_settings',
                                        'value' => true,
                                    )
                                )
                            ),
                        Field::make('color', 'text_color_custom', __('Text Color'))->set_width(20)
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'text_color',
                                        'value' => 'text-custom',
                                    ),
                                )
                            ),
                    ))

                    ->add_fields('description',  array(
                        Field::make('html', 'html_4')->set_html('<label>Section Description Settings</label>')->set_classes('cb-label'),
                        Field::make('textarea', 'description', __('Description'))->set_width(80)->set_classes('editor-field'),
                        Field::make('html', 'activate_wysiwyg')->set_width(20)
                            ->set_html('<a class="button button-primary button-large wysiwyg-editor-trigger" >Wysiwyg Editor</a>'),
                        Field::make('text', 'description_width', __('Description Custom Width')),
                        Field::make('select', 'description_alignment', __('Description Alignment'))
                            ->set_options(
                                array(
                                    '' => 'Default/Left',
                                    'ms-auto' => 'Right',
                                    'mx-auto' => 'Center',
                                )
                            ),
                        Field::make('select', 'description_size', __('Description Size'))->set_width(20)
                            ->set_options(
                                array(
                                    '' => 'Default',
                                    'small-text' => 'Small Text',
                                    'medium-text' => 'Medium Text',
                                    'large-text' => 'Large Text',
                                )
                            )
                    ))
                    ->add_fields('image', array(
                        Field::make('image', 'image', __('Image')),
                        Field::make('select', 'size', __('Size'))
                            ->set_options(
                                array(
                                    ''          => 'Default',
                                    'full'      => 'Full',
                                    'large'      => 'Large',
                                    'medium' => 'Medium',
                                    'thumbnail'    => 'Thumbnail',
                                )
                            ),
                        Field::make('checkbox', 'is_background_image', __('Is background image')),
                        Field::make('checkbox', 'custom_size', __('Custom Size')),
                        Field::make('text', 'image_width', __('Custom Image Width'))
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'custom_size',
                                        'value' => true,
                                    )
                                )
                            ),
                        Field::make('text', 'image_height', __('Custom Image Height'))
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'custom_size',
                                        'value' => true,
                                    )
                                )
                            ),
                        Field::make('checkbox', 'rounded_corners', __('Rounder Corners')),
                        Field::make('text', 'border_radius', __('Border Radius'))->set_help_text('Custom border radius')
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'rounded_corners',
                                        'value' => true,
                                    )
                                )
                            ),
                    ))
                    ->add_fields(
                        'video',
                        array(
                            Field::make('checkbox', 'autoplay', __('Is Video Autoplay'))->set_width(100),
                            Field::make('select', 'video_type', __('Background Type'))->set_classes('inline-field')
                                ->set_options(
                                    array(
                                        'youtube' => 'Youtube',
                                        'self-hosted' => 'Self Hosted',
                                    )
                                ),
                            Field::make('file', 'video', __('Video'))->set_classes('inline-field')->set_type(array('video'))
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'video_type',
                                            'value' => 'self-hosted',
                                        )
                                    )
                                ),
                            Field::make('text', 'youtube_video_id', __('Youtube ID'))->set_classes('inline-field')
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'video_type',
                                            'value' => 'youtube',
                                        )
                                    )
                                ),
                        )
                    )
                    ->add_fields(
                        'custom_html',
                        array(
                            Field::make('textarea', 'custom_html', __('Custom HTML')),
                        )
                    )
                    ->add_fields('columns', array(
                        Field::make('html', 'html_422')->set_html('<label>Section Row Settings</label>')->set_classes('cb-label')
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'is_slider',
                                        'value' => false,
                                    )
                                )
                            ),
                        Field::make('checkbox', 'individual_column_settings', __('Individual Column Settings'))->set_width(20),
                        Field::make('checkbox', 'is_slider', __('Is Slider'))->set_width(20),
                        Field::make('checkbox', 'same_image_height', __('Same Image Height'))->set_width(60),
                        Field::make('select', 'slider_style', __('Slider Style'))->set_width(25)
                            ->set_options(
                                array(
                                    'style-1' => 'Style 1',
                                )
                            )
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'is_slider',
                                        'value' => true,
                                    )
                                )
                            ),
                        Field::make('text', 'number_of_slides', __('Number of Slides Desktop'))->set_default_value(6)->set_required(true)->set_attribute('type', 'number')->set_width(25)
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'is_slider',
                                        'value' => true,
                                    )
                                )
                            ),
                        Field::make('text', 'number_of_slides_tablet', __('Number of Slides Tablet'))->set_attribute('type', 'number')->set_width(25)
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'is_slider',
                                        'value' => true,
                                    )
                                )
                            ),
                        Field::make('text', 'number_of_slides_mobile', __('Number of Slides Mobile'))->set_attribute('type', 'number')->set_width(25)

                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'is_slider',
                                        'value' => true,
                                    )
                                )
                            ),
                        Field::make('select', 'image_fit', __('Image Fit'))->set_width(50)
                            ->set_options(
                                array(
                                    '' => 'Cover',
                                    'contain' => 'Contain',
                                )
                            )
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'same_image_height',
                                        'value' => true,
                                    )
                                )
                            ),
                        Field::make('text', 'image_padding', __('Image Padding'))->set_width(50)
                            ->set_help_text('Default is 30%')
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'same_image_height',
                                        'value' => true,
                                    )
                                )
                            ),
                        Field::make('select', 'horizontal_spacing', 'Horizontal Spacing')->set_width(20)
                            ->set_options(
                                array(
                                    ''     => 'Default',
                                    'gx-6'  => 'Huge',
                                    'gx-5'  => 'Extra Large',
                                    'gx-4'  => 'Large',
                                    'gx-3'  => 'Medium',
                                    'gx-2'  => 'Small',
                                    'gx-1'  => 'Extra Small',
                                    'gx-20px'  => '20px',
                                    'gx-0'  => 'None',
                                )
                            ),
                        Field::make('select', 'vertical_spacing', 'Vertical Spacing')->set_width(20)
                            ->set_options(
                                array(
                                    ''     => 'Default',
                                    'gy-6'  => 'Huge',
                                    'gy-5'  => 'Extra Large',
                                    'gy-4'  => 'Large',
                                    'gy-3'  => 'Medium',
                                    'gy-2'  => 'Small',
                                    'gy-1'  => 'Extra Small',
                                    'gy-20px'  => '20px',
                                    'gy-0'  => 'None',
                                )
                            ),
                        Field::make('select', 'align_items', 'Align Items')->set_width(20)
                            ->set_options(
                                array(
                                    ''               => 'Default',
                                    'align-items-start'  => 'Start',
                                    'align-items-center'  => 'Center',
                                    'align-items-end'  => 'End',
                                )
                            )
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'is_slider',
                                        'value' => false,
                                    )
                                )
                            ),
                        Field::make('select', 'justify_content', 'Justify Content')->set_width(20)
                            ->set_options(
                                array(
                                    ''                  => 'Default',
                                    'justify-content-start'  => 'Start',
                                    'justify-content-center'  => 'Center',
                                    'justify-content-end'  => 'End',
                                    'justify-content-between'  => 'Between',
                                )
                            )
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'is_slider',
                                        'value' => false,
                                    )
                                )
                            ),
                        Field::make('select', 'mobile_styling', 'Mobile Styling')->set_width(20)
                            ->set_options(
                                array(
                                    ''                  => 'Default',
                                    'image_left'  => 'Image/Icon on Left',
                                    'image_right'  => 'Image/Icon on Right',
                                )
                            ),



                        Field::make('html', 'html_42')->set_html('<label>Section Columns Settings</label>')->set_classes('cb-label'),

                        Field::make('complex', 'columns', __(''))
                            ->setup_labels(
                                array(
                                    'plural_name'   => 'Columns',
                                    'singular_name' => 'Columnn',
                                )
                            )
                            ->set_classes('columns')
                            ->add_fields(array(
                                Field::make('text', 'column_title', __('Column Title')),
                                Field::make('complex', 'items', __(''))
                                    ->set_classes('items')
                                    ->setup_labels(
                                        array(
                                            'plural_name'   => 'Items',
                                            'singular_name' => 'Item',
                                        )
                                    )
                                    ->set_collapsed(true)
                                    ->add_fields('heading', array(
                                        Field::make('html', 'html_1')->set_html('<label>Heading Options</label>')->set_classes('cb-label'),
                                        Field::make('checkbox', 'has_prefix', __('Heading Has Prefix'))->set_width(33),
                                        Field::make('checkbox', 'has_suffix', __('Heading Has Suffix'))->set_width(33),
                                        Field::make('checkbox', 'has_custom_heading_settings', __('Custom Heading Settings'))->set_width(33),
                                        Field::make('html', 'html_2')->set_html('<label>Heading Settings</label>')->set_classes('cb-label'),
                                        Field::make('text', 'heading', __('Heading'))->set_classes('inline-field'),
                                        Field::make('text', 'prefix', __('Prefix'))->set_classes('inline-field')
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'has_prefix',
                                                        'value' => true,
                                                    )
                                                )
                                            ),
                                        Field::make('text', 'suffix', __('Suffix'))->set_classes('inline-field')
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'has_suffix',
                                                        'value' => true,
                                                    )
                                                )
                                            ),
                                        Field::make('select', 'tag', __('Tag'))->set_width(20)
                                            ->set_options(
                                                array(
                                                    'h1' => 'h1',
                                                    'h2' => 'h2',
                                                    'h3' => 'h3',
                                                    'h4' => 'h4',
                                                    'h5' => 'h5',
                                                    'h6' => 'h6',
                                                )
                                            )
                                            ->set_default_value('h2')
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'has_custom_heading_settings',
                                                        'value' => true,
                                                    )
                                                )
                                            ),
                                        Field::make('select', 'size', __('Heading Size'))->set_width(20)
                                            ->set_options(
                                                array(
                                                    '' => 'Default',
                                                    'large-heading' => 'Large Heading',
                                                    'medium-heading' => 'Medium Heading',
                                                    'small-heading' => 'Small Heading',
                                                )
                                            )
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'has_custom_heading_settings',
                                                        'value' => true,
                                                    )
                                                )
                                            ),
                                        Field::make('select', 'text_align', __('Text Align'))->set_width(20)
                                            ->set_options(
                                                array(
                                                    '' => 'Default',
                                                    'text-start' => 'Text Left',
                                                    'text-center' => 'Text Center',
                                                    'text-end' => 'Text Right',
                                                    'text-justify' => 'Text Justify',
                                                )
                                            )
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'has_custom_heading_settings',
                                                        'value' => true,
                                                    )
                                                )
                                            ),
                                        Field::make('select', 'text_color', 'Text Color')->set_width(20)
                                            ->set_options(
                                                array(
                                                    ''   => 'Default',
                                                    'text-primary'   => 'Primary',
                                                    'text-secondary' => 'Secondary',
                                                    'text-accent'    => 'Accent',
                                                    'text-white'     => 'White',
                                                    'text-light-gray'     => 'Light Gray',
                                                    'text-custom'    => 'Custom',
                                                )
                                            )
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'has_custom_heading_settings',
                                                        'value' => true,
                                                    )
                                                )
                                            ),
                                        Field::make('color', 'text_color_custom', __('Text Color'))->set_width(20)
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'text_color',
                                                        'value' => 'text-custom',
                                                    ),
                                                )
                                            ),
                                    ))
                                    ->set_header_template('<%- heading %>')
                                    ->add_fields('description',  array(
                                        Field::make('textarea', 'description', __('Description'))->set_width(80)->set_classes('editor-field'),
                                        Field::make('html', 'activate_wysiwyg')->set_width(20)
                                            ->set_html('<button class="button button-primary button-large wysiwyg-editor-trigger">Wysiwyg Editor</button>'),
                                        Field::make('text', 'description_width', __('Description Custom Width')),
                                        Field::make('select', 'description_alignment', __('Description Alignment'))
                                            ->set_options(
                                                array(
                                                    '' => 'Default/Left',
                                                    'ms-auto' => 'Right',
                                                    'mx-auto' => 'Center',
                                                )
                                            ),
                                        Field::make('select', 'description_size', __('Description Size'))->set_width(20)
                                            ->set_options(
                                                array(
                                                    '' => 'Default',
                                                    'small-text' => 'Small Text',
                                                    'medium-text' => 'Medium Text',
                                                    'large-text' => 'Large Text',
                                                )
                                            )

                                    ))

                                    ->add_fields('image', array(
                                        Field::make('image', 'image', __('Image')),
                                        Field::make('select', 'size', __('Size'))
                                            ->set_options(
                                                array(
                                                    ''          => 'Default',
                                                    'full'      => 'Full',
                                                    'large'      => 'Large',
                                                    'medium' => 'Medium',
                                                    'thumbnail'    => 'Thumbnail',
                                                )
                                            ),
                                        Field::make('checkbox', 'is_background_image', __('Is background image')),
                                        Field::make('checkbox', 'custom_size', __('Custom Size')),
                                        Field::make('text', 'image_width', __('Custom Image Width'))
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'custom_size',
                                                        'value' => true,
                                                    )
                                                )
                                            ),
                                        Field::make('text', 'image_height', __('Custom Image Height'))
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'custom_size',
                                                        'value' => true,
                                                    )
                                                )
                                            ),
                                        Field::make('checkbox', 'rounded_corners', __('Rounder Corners')),
                                        Field::make('text', 'border_radius', __('Border Radius'))->set_help_text('Custom border radius')
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'rounded_corners',
                                                        'value' => true,
                                                    )
                                                )
                                            ),
                                    ))
                                    ->add_fields(
                                        'gallery',
                                        array(
                                            Field::make('select', 'gallery_style', 'Gallery Style')
                                                ->set_options(
                                                    array(
                                                        'logo-slider'   => 'Logo Slider',
                                                        'grid' => 'Grid',
                                                    )
                                                ),
                                            Field::make('media_gallery', 'gallery', __('Gallery')),
                                            Field::make('text', 'number_of_slides', __('Number of Slides Desktop'))->set_default_value(6)->set_required(true)->set_attribute('type', 'number')
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'gallery_style',
                                                            'value' => 'logo-slider',
                                                        )
                                                    )
                                                ),
                                            Field::make('text', 'number_of_slides_tablet', __('Number of Slides Tablet'))
                                                ->set_attribute('type', 'number')
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'gallery_style',
                                                            'value' => 'logo-slider',
                                                        )
                                                    )
                                                ),
                                            Field::make('text', 'number_of_slides_mobile', __('Number of Slides Mobile'))
                                                ->set_attribute('type', 'number')
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'gallery_style',
                                                            'value' => 'logo-slider',
                                                        )
                                                    )
                                                ),
                                        )
                                    )
                                    ->add_fields(
                                        'buttons',
                                        array(
                                            Field::make('select', 'buttons_alignment', 'Buttons Alignment')
                                                ->set_options(
                                                    array(
                                                        ''                => 'Default',
                                                        'text-start'                => 'Left',
                                                        'text-center'                => 'Center',
                                                        'text-end'                => 'Right',
                                                    )
                                                ),
                                            Field::make('complex', 'buttons', __('Buttons'))
                                                ->set_classes('columns')
                                                ->setup_labels(
                                                    array(
                                                        'plural_name'   => 'Buttons',
                                                        'singular_name' => 'Button',
                                                    )
                                                )

                                                ->add_fields(array(
                                                    Field::make('select', 'button_type', __('Button Type'))->set_classes('trigger-selector inline-field')
                                                        ->set_options(
                                                            array(
                                                                ''          => 'Select Button Type',
                                                                'page'      => 'Page',
                                                                'product'      => 'Product',
                                                                'guides'      => 'Guides',
                                                                'casestudies'      => 'Case Studies',
                                                                'post'      => 'Post',
                                                                'solutions' => 'Solution',
                                                                'popups'    => 'Popup',
                                                                'custom'     => 'Custom',
                                                            )
                                                        ),
                                                    Field::make('text', 'button_text', __('Button Text'))->set_classes('inline-field'),
                                                    Field::make('text', 'button_url', __('Button URL'))->set_classes('field-url')
                                                        ->set_conditional_logic(
                                                            array(
                                                                array(
                                                                    'field'   => 'button_type',
                                                                    'value'   => 'custom',
                                                                    'compare' => '!='
                                                                )
                                                            )
                                                        ),
                                                    Field::make('html', 'html')
                                                        ->set_html('<div class="page-selector">  </div>'),
                                                    Field::make('text', 'button_url_custom', __('Button URL'))->set_classes('inline-field')
                                                        ->set_conditional_logic(
                                                            array(
                                                                array(
                                                                    'field' => 'button_type',
                                                                    'value' => 'custom',
                                                                )
                                                            )
                                                        ),
                                                    Field::make('select', 'button_style', __('Button Style'))->set_classes('inline-field')
                                                        ->set_options(
                                                            array(
                                                                'button-accent'      => 'Accent',
                                                                'button-primary'      => 'Primary',
                                                                'button-secondary' => 'Secondary',
                                                                'button-white' => 'White',
                                                                'button-bordered'    => 'Bordered',
                                                            )
                                                        ),
                                                    Field::make('select', 'button_target', __('Button Target'))->set_classes('inline-field')
                                                        ->set_options(
                                                            array(
                                                                'target="_self"'      => 'Default',
                                                                'target="_blank"'      => 'New Tab',
                                                            )
                                                        ),
                                                ))
                                                ->set_header_template('Button: <%- button_text %>'),
                                        )
                                    )
                                    ->add_fields('icon', array(
                                        Field::make('file', 'icon', __('Icon'))
                                            ->set_type(array('image/svg+xml')),
                                        Field::make('select', 'icon_color', 'Text Color')
                                            ->set_options(
                                                array(
                                                    ''   => 'Default',
                                                    'text-primary'   => 'Primary',
                                                    'text-secondary' => 'Secondary',
                                                    'text-accent'    => 'Accent',
                                                    'text-white'     => 'White',
                                                    'text-light-gray'     => 'Light Gray',
                                                    'text-custom'    => 'Custom',
                                                )
                                            ),
                                        Field::make('color', 'icon_color_custom', __('Text Color'))
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'icon_color',
                                                        'value' => 'text-custom',
                                                    )
                                                )
                                            ),
                                        Field::make('text', 'icon_width', __('Custom Icon Width')),
                                        Field::make('text', 'icon_height', __('Custom Icon Height'))

                                    ))
                                    ->add_fields(
                                        'accordion',
                                        array(
                                            Field::make('checkbox', 'open_first_item', __('Open First Item')),
                                            Field::make('checkbox', 'with_border', __('With Border')),
                                            Field::make('checkbox', 'lower_opacity', __('Lower opacity for not active')),
                                            Field::make('select', 'accordion_source', __('Accordion Source'))
                                                ->set_options(
                                                    array(
                                                        ''      => 'Custom',
                                                        'faqs'      => 'FAQs Select Manually',
                                                        'faqs_category'      => 'FAQs by Category',
                                                    )
                                                ),
                                            Field::make('complex', 'accordion', __('Accordion'))
                                                ->set_layout('tabbed-vertical')
                                                ->add_fields(
                                                    array(
                                                        Field::make('text', 'heading', __('Heading')),
                                                        Field::make('textarea', 'description', __('Description'))->set_width(80),
                                                        Field::make('html', 'activate_wysiwyg')->set_width(20)
                                                            ->set_html('<a class="button button-primary button-large wysiwyg-editor-trigger" >Wysiwyg Editor</a>'),
                                                    )
                                                )
                                                ->set_header_template('<%- heading  %>')
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'accordion_source',
                                                            'value' => '',
                                                            'comapre' => '='
                                                        )
                                                    )
                                                ),
                                            Field::make('association', 'faqs', 'Select FAQs')
                                                ->set_types(
                                                    array(
                                                        array(
                                                            'type'      => 'post',
                                                            'post_type' => 'faq',
                                                        )
                                                    )
                                                )
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'accordion_source',
                                                            'value' => 'faqs',
                                                            'comapre' => '='
                                                        )
                                                    )
                                                ),
                                            Field::make('association', 'faqs_category', 'Select FAQs Category')
                                                ->set_types(
                                                    array(
                                                        array(
                                                            'type'      => 'term',
                                                            'taxonomy' => 'faqs_category',
                                                        )
                                                    )
                                                )
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'accordion_source',
                                                            'value' => 'faqs_category',
                                                            'comapre' => '='
                                                        )
                                                    )
                                                ),
                                        )
                                    )
                                    ->add_fields(
                                        'wp_form',
                                        array(
                                            Field::make('select', 'style', 'Style')
                                                ->set_options(
                                                    array(
                                                        ''   => 'Default',
                                                        'style-2' => 'Style 2',
                                                    )
                                                ),
                                            Field::make('association', 'form', 'Select Form')
                                                ->set_types(
                                                    array(
                                                        array(
                                                            'type'      => 'post',
                                                            'post_type' => 'wpforms',
                                                        )
                                                    )
                                                )
                                                ->set_max(1)
                                        )
                                    )
                                    ->add_fields(
                                        'post_grid',
                                        array(
                                            Field::make('html', 'post_box_settings_html')->set_html('<label>Post Box Settings</label>')->set_classes('cb-label'),
                                            Field::make('checkbox', 'is_slider', __('Is SLider')),
                                            Field::make('text', 'number_of_slides', __('Number of Slides Desktop'))->set_default_value(6)->set_required(true)->set_attribute('type', 'number')->set_width(25)
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'is_slider',
                                                            'value' => true,
                                                        )
                                                    )
                                                ),
                                            Field::make('text', 'number_of_slides_tablet', __('Number of Slides Tablet'))->set_attribute('type', 'number')->set_width(25)
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'is_slider',
                                                            'value' => true,
                                                        )
                                                    )
                                                ),
                                            Field::make('text', 'number_of_slides_mobile', __('Number of Slides Mobile'))->set_attribute('type', 'number')->set_width(25)

                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'is_slider',
                                                            'value' => true,
                                                        )
                                                    )
                                                ),
                                            Field::make('html', 'post_box_styles_html')->set_html('<label>Post Box Styles</label>')->set_classes('cb-label'),
                                            Field::make('complex', 'post_box_styles', __(''))
                                                ->setup_labels(
                                                    array(
                                                        'plural_name'   => 'Styles',
                                                        'singular_name' => 'Style',
                                                    )
                                                )
                                                ->set_duplicate_groups_allowed(false)
                                                ->add_fields(
                                                    'background_color',
                                                    array(
                                                        Field::make('select', 'background_color', 'Background Color')
                                                            ->set_options(
                                                                array(
                                                                    'bg-primary'   => 'Primary',
                                                                    'bg-secondary' => 'Secondary',
                                                                    'bg-accent'    => 'Accent',
                                                                    'bg-white'     => 'White',
                                                                    'bg-light-gray'     => 'Light Gray',
                                                                    'bg-custom'    => 'Custom',
                                                                )
                                                            ),
                                                        Field::make('color', 'background_color_custom', __('Background Color'))
                                                            ->set_conditional_logic(
                                                                array(
                                                                    array(
                                                                        'field' => 'background_color',
                                                                        'value' => 'bg-custom',
                                                                    )
                                                                )
                                                            ),
                                                    )
                                                )
                                                ->add_fields(
                                                    'text_color',
                                                    array(
                                                        Field::make('select', 'text_color', 'Text Color')
                                                            ->set_options(
                                                                array(
                                                                    'text-primary'   => 'Primary',
                                                                    'text-secondary' => 'Secondary',
                                                                    'text-accent'    => 'Accent',
                                                                    'text-white'     => 'White',
                                                                    'text-light-gray'     => 'Light Gray',
                                                                    'text-custom'    => 'Custom',
                                                                )
                                                            ),
                                                        Field::make('color', 'text_color_custom', __('Text Color'))
                                                            ->set_conditional_logic(
                                                                array(
                                                                    array(
                                                                        'field' => 'text_color',
                                                                        'value' => 'text-custom',
                                                                    )
                                                                )
                                                            ),
                                                    )
                                                )
                                                ->add_fields(
                                                    'padding',
                                                    array(
                                                        Field::make('select', 'padding_top', 'Padding Top')
                                                            ->set_options(
                                                                array(
                                                                    ''                => 'No Padding',
                                                                    'xl-padding-top'  => 'Extra Large',
                                                                    'lg-padding-top'  => 'Large',
                                                                    'md-padding-top'  => 'Medium',
                                                                    'sm-padding-top'  => 'Small',
                                                                    'xs-padding-top' => 'Extra Small',
                                                                )
                                                            ),
                                                        Field::make('select', 'padding_bottom', 'Padding Bottom')
                                                            ->set_options(
                                                                array(
                                                                    ''                   => 'No Padding',
                                                                    'xl-padding-bottom'  => 'Extra Large',
                                                                    'lg-padding-bottom'  => 'Large',
                                                                    'md-padding-bottom'  => 'Medium',
                                                                    'sm-padding-bottom'  => 'Small',
                                                                    'xs-padding-bottom' => 'Extra Small',
                                                                )
                                                            ),
                                                        Field::make('select', 'padding_left', 'Padding left')
                                                            ->set_options(
                                                                array(
                                                                    ''                 => 'No Padding',
                                                                    'xl-padding-left'  => 'Extra Large',
                                                                    'lg-padding-left'  => 'Large',
                                                                    'md-padding-left'  => 'Medium',
                                                                    'sm-padding-left'  => 'Small',
                                                                    'xs-padding-left' => 'Extra Small',
                                                                )
                                                            ),
                                                        Field::make('select', 'padding_right', 'Padding right')
                                                            ->set_options(
                                                                array(
                                                                    ''                  => 'No Padding',
                                                                    'xl-padding-right'  => 'Extra Large',
                                                                    'lg-padding-right'  => 'Large',
                                                                    'md-padding-right'  => 'Medium',
                                                                    'sm-padding-right'  => 'Small',
                                                                    'xs-padding-right' => 'Extra Small',
                                                                )
                                                            ),

                                                    )
                                                )
                                                ->add_fields(
                                                    'margin',
                                                    array(
                                                        Field::make('select', 'margin_top', 'Margin Top')
                                                            ->set_options(
                                                                array(
                                                                    ''               => 'No margin',
                                                                    'xl-margin-top'  => 'Extra Large',
                                                                    'lg-margin-top'  => 'Large',
                                                                    'md-margin-top'  => 'Medium',
                                                                    'sm-margin-top'  => 'Small',
                                                                    'xs-margin-top' => 'Extra Small',
                                                                )
                                                            ),
                                                        Field::make('select', 'margin_bottom', 'Margin Bottom')
                                                            ->set_options(
                                                                array(
                                                                    ''                  => 'No margin',
                                                                    'xl-margin-bottom'  => 'Extra Large',
                                                                    'lg-margin-bottom'  => 'Large',
                                                                    'md-margin-bottom'  => 'Medium',
                                                                    'sm-margin-bottom'  => 'Small',
                                                                    'xs-margin-bottom' => 'Extra Small',
                                                                )
                                                            ),
                                                        Field::make('select', 'margin_left', 'Margin Left')
                                                            ->set_options(
                                                                array(
                                                                    ''                => 'No margin',
                                                                    'xl-margin-left'  => 'Extra Large',
                                                                    'lg-margin-left'  => 'Large',
                                                                    'md-margin-left'  => 'Medium',
                                                                    'sm-margin-left'  => 'Small',
                                                                    'xs-margin-left' => 'Extra Small',
                                                                )
                                                            ),
                                                        Field::make('select', 'margin_right', 'Margin Right')
                                                            ->set_options(
                                                                array(
                                                                    ''                 => 'No margin',
                                                                    'xl-margin-right'  => 'Extra Large',
                                                                    'lg-margin-right'  => 'Large',
                                                                    'md-margin-right'  => 'Medium',
                                                                    'sm-margin-right'  => 'Small',
                                                                    'xs-margin-right' => 'Extra Small',
                                                                )
                                                            ),

                                                    )
                                                )
                                                ->add_fields(
                                                    'alignment',
                                                    array(
                                                        Field::make('select', 'align_items', 'Align Items')
                                                            ->set_options(
                                                                array(
                                                                    ''               => 'Default',
                                                                    'align-items-start'  => 'Start',
                                                                    'align-items-center'  => 'Center',
                                                                    'align-items-end'  => 'End',
                                                                )
                                                            ),
                                                        Field::make('select', 'justify_content', 'Justify Content')
                                                            ->set_options(
                                                                array(
                                                                    ''                  => 'Default',
                                                                    'justify-content-start'  => 'Start',
                                                                    'justify-content-center'  => 'Center',
                                                                    'justify-content-end'  => 'End',
                                                                    'justify-content-between'  => 'Between',
                                                                )
                                                            ),
                                                        Field::make('select', 'text_align', 'Text Align')
                                                            ->set_options(
                                                                array(
                                                                    ''                => 'Default',
                                                                    'text-start'                => 'Left',
                                                                    'text-center'                => 'Center',
                                                                    'text-end'                => 'Right',
                                                                )
                                                            ),
                                                    )
                                                )
                                                ->add_fields(
                                                    'column_width',
                                                    array(
                                                        Field::make('select', 'column_width', __('Column Width Desktop'))
                                                            ->set_options(
                                                                array(
                                                                    'col-lg'     => 'Default',
                                                                    'col-12'    => '100.00%',
                                                                    'col-lg-11' => '91.67%',
                                                                    'col-lg-10' => '83.33%',
                                                                    'col-lg-9'  => '75.00%',
                                                                    'col-lg-8'  => '67.00%',
                                                                    'col-lg-7'  => '58.33%',
                                                                    'col-lg-6'  => '50.00%',
                                                                    'col-lg-5'  => '41.67%',
                                                                    'col-lg-4'  => '33.33%',
                                                                    'col-lg-3'  => '25.00%',
                                                                    'col-lg-2'  => '16.67%',
                                                                    'col-lg-1'  => '08.33%',
                                                                )
                                                            ),
                                                        Field::make('select', 'column_width_tablet', __('Column Width Tablet'))
                                                            ->set_options(
                                                                array(
                                                                    ''     => 'Default',
                                                                    'col-md-12'    => '100.00%',
                                                                    'col-md-11' => '91.67%',
                                                                    'col-md-10' => '83.33%',
                                                                    'col-md-9'  => '75.00%',
                                                                    'col-md-8'  => '67.00%',
                                                                    'col-md-7'  => '58.33%',
                                                                    'col-md-6'  => '50.00%',
                                                                    'col-md-5'  => '41.67%',
                                                                    'col-md-4'  => '33.33%',
                                                                    'col-md-3'  => '25.00%',
                                                                    'col-md-2'  => '16.67%',
                                                                    'col-md-1'  => '08.33%',
                                                                )
                                                            ),
                                                        Field::make('select', 'column_width_mobile', __('Column Width Mobile'))
                                                            ->set_options(
                                                                array(
                                                                    ''     => 'Default',
                                                                    'col-12' => '100%',
                                                                    'col-11' => '91.67%',
                                                                    'col-10' => '83.33%',
                                                                    'col-9'  => '75.00%',
                                                                    'col-8'  => '67.00%',
                                                                    'col-7'  => '58.33%',
                                                                    'col-6'  => '50.00%',
                                                                    'col-5'  => '41.67%',
                                                                    'col-4'  => '33.33%',
                                                                    'col-3'  => '25.00%',
                                                                    'col-2'  => '16.67%',
                                                                    'col-1'  => '08.33%',
                                                                )
                                                            ),
                                                    )
                                                )
                                                ->add_fields(
                                                    'border',
                                                    array(
                                                        Field::make('select', 'border_radius', 'Border Radius')->set_classes('inline-field-wide-label')
                                                            ->set_options(
                                                                array(
                                                                    ''   => 'None',
                                                                    'rounded-corner'   => 'Default[10px]',
                                                                    'custom'   => 'Custom',
                                                                )
                                                            ),
                                                        Field::make('text', 'border_radius_custom', 'Custom Border Radius')->set_classes('inline-field-wide-label')
                                                            ->set_conditional_logic(
                                                                array(
                                                                    array(
                                                                        'field' => 'border_radius',
                                                                        'value' => 'custom',
                                                                        'compare' => '='
                                                                    )
                                                                )
                                                            ),
                                                        Field::make('select', 'border_style', 'Border Style')->set_classes('inline-field-wide-label')
                                                            ->set_options(
                                                                array(
                                                                    ''   => 'None',
                                                                    'border-default'   => 'Default',
                                                                    'border-custom'   => 'Custom',
                                                                )
                                                            ),

                                                        Field::make('select', 'border_color', 'Border Color')->set_classes('inline-field-wide-label')
                                                            ->set_options(
                                                                array(
                                                                    'border-default'   => 'Default',
                                                                    'border-primary'   => 'Primary',
                                                                    'border-secondary' => 'Secondary',
                                                                    'border-accent'    => 'Accent',
                                                                    'border-white'     => 'White',
                                                                    'border-light-gray'     => 'Light Gray',
                                                                    'border-custom-color'    => 'Custom',
                                                                )
                                                            )
                                                            ->set_conditional_logic(
                                                                array(
                                                                    array(
                                                                        'field' => 'border_style',
                                                                        'value' => 'border-custom',
                                                                    ),
                                                                )
                                                            ),
                                                        Field::make('color', 'border_color_custom', __('Border Color'))->set_classes('inline-field-wide-label')
                                                            ->set_conditional_logic(
                                                                array(
                                                                    array(
                                                                        'field' => 'border_color',
                                                                        'value' => 'border-custom-color',
                                                                    ),
                                                                )
                                                            ),

                                                        Field::make('select', 'border_width', 'Border Width')->set_classes('inline-field-wide-label')
                                                            ->set_options(array(
                                                                'default' => 'Default[1px]',
                                                                'custom' => 'Custom'
                                                            ))
                                                            ->set_conditional_logic(
                                                                array(
                                                                    array(
                                                                        'field' => 'border_style',
                                                                        'value' => 'border-custom',
                                                                    ),
                                                                )
                                                            ),
                                                        Field::make('text', 'border_width_top', 'Top Border Width')->set_classes('inline-field-wide-label')->set_default_value(0)
                                                            ->set_conditional_logic(
                                                                array(
                                                                    array(
                                                                        'field' => 'border_width',
                                                                        'value' => 'custom',
                                                                    ),
                                                                )
                                                            ),
                                                        Field::make('text', 'border_width_right', 'Right Border Width')->set_classes('inline-field-wide-label')->set_default_value(0)
                                                            ->set_conditional_logic(
                                                                array(
                                                                    array(
                                                                        'field' => 'border_width',
                                                                        'value' => 'custom',
                                                                    ),
                                                                )
                                                            ),
                                                        Field::make('text', 'border_width_bottom', 'Bottom Border Width')->set_classes('inline-field-wide-label')->set_default_value(0)
                                                            ->set_conditional_logic(
                                                                array(
                                                                    array(
                                                                        'field' => 'border_width',
                                                                        'value' => 'custom',
                                                                    ),
                                                                )
                                                            ),
                                                        Field::make('text', 'border_width_left', 'Left Border Width')->set_classes('inline-field-wide-label')->set_default_value(0)
                                                            ->set_conditional_logic(
                                                                array(
                                                                    array(
                                                                        'field' => 'border_width',
                                                                        'value' => 'custom',
                                                                    ),
                                                                )
                                                            ),

                                                    )
                                                )
                                                ->add_fields(
                                                    'custom_class',
                                                    array(
                                                        Field::make('text', 'custom_class', 'Custom Class')
                                                    )
                                                )
                                                ->set_layout('tabbed-vertical'),
                                            Field::make('html', 'post_elements_html')->set_html('<label>Post Elements</label>')->set_classes('cb-label'),
                                            Field::make('complex', 'post_elements', 'Post Elements')
                                                ->set_duplicate_groups_allowed(false)
                                                ->add_fields(
                                                    'post_title',
                                                    array(
                                                        Field::make('text', 'text_before', __('Text Before')),
                                                        Field::make('text', 'text_after', __('Text After')),
                                                        Field::make('select', 'tag', __('Post Title Tag'))
                                                            ->set_options(
                                                                array(
                                                                    '' => 'Default',
                                                                    'h2' => 'h2',
                                                                    'h3' => 'h3',
                                                                    'h4' => 'h4',
                                                                    'h5' => 'h5',
                                                                    'h6' => 'h6',
                                                                    'p' => 'p',
                                                                )
                                                            ),
                                                        Field::make('select', 'text_color', 'Text Color')
                                                            ->set_options(
                                                                array(
                                                                    ''   => 'Default',
                                                                    'text-primary'   => 'Primary',
                                                                    'text-secondary' => 'Secondary',
                                                                    'text-accent'    => 'Accent',
                                                                    'text-white'     => 'White',
                                                                    'text-light-gray'     => 'Light Gray',
                                                                    'text-body-color'     => 'Body',
                                                                    'text-custom'    => 'Custom',
                                                                )
                                                            ),
                                                        Field::make('color', 'text_color_custom', __('Text Color'))
                                                            ->set_conditional_logic(
                                                                array(
                                                                    array(
                                                                        'field' => 'text_color',
                                                                        'value' => 'text-custom',
                                                                    )
                                                                )
                                                            ),
                                                    )
                                                )
                                                ->add_fields(
                                                    'featured_image',
                                                    array(
                                                        Field::make('select', 'size', __('Size'))
                                                            ->set_options(
                                                                array(
                                                                    ''          => 'Default',
                                                                    'full'      => 'Full',
                                                                    'large'      => 'Large',
                                                                    'medium' => 'Medium',
                                                                    'thumbnail'    => 'Thumbnail',
                                                                )
                                                            ),
                                                        Field::make('checkbox', 'is_background_image', __('Is Background Image')),
                                                        Field::make('checkbox', 'rounded_corners', __('Rounder Corners')),
                                                        Field::make('text', 'border_radius', __('Border Radius'))->set_help_text('Custom border radius')
                                                            ->set_conditional_logic(
                                                                array(
                                                                    array(
                                                                        'field' => 'rounded_corners',
                                                                        'value' => true,
                                                                    )
                                                                )
                                                            ),
                                                    )
                                                )
                                                ->add_fields(
                                                    'post_excerpt',
                                                    array(
                                                        Field::make('text', 'excerpt_length', 'Custom Excerpt Length')
                                                    )
                                                )
                                                ->add_fields(
                                                    'permalink',
                                                    array(
                                                        Field::make('checkbox', 'hide_button_on_mobile', 'Hide Button on Mobile'),
                                                        Field::make('text', 'button_text', 'Button Text'),
                                                        Field::make('select', 'button_style', __('Button Style'))->set_width(20)
                                                            ->set_options(
                                                                array(
                                                                    'button-accent'      => 'Accent',
                                                                    'button-primary'      => 'Primary',
                                                                    'button-secondary' => 'Secondary',
                                                                    'button-white' => 'White',
                                                                    'button-bordered'    => 'Bordered',
                                                                )
                                                            ),
                                                    )
                                                )
                                                ->add_fields('icon', array(
                                                    Field::make('file', 'icon', __('Icon'))
                                                        ->set_type(array('image/svg+xml')),
                                                    Field::make('select', 'icon_color', 'Text Color')
                                                        ->set_options(
                                                            array(
                                                                ''   => 'Default',
                                                                'text-primary'   => 'Primary',
                                                                'text-secondary' => 'Secondary',
                                                                'text-accent'    => 'Accent',
                                                                'text-white'     => 'White',
                                                                'text-light-gray'     => 'Light Gray',
                                                                'text-custom'    => 'Custom',
                                                            )
                                                        ),
                                                    Field::make('color', 'icon_color_custom', __('Text Color'))
                                                        ->set_conditional_logic(
                                                            array(
                                                                array(
                                                                    'field' => 'icon_color',
                                                                    'value' => 'text-custom',
                                                                )
                                                            )
                                                        ),
                                                    Field::make('text', 'icon_width', __('Custom Icon Width')),
                                                    Field::make('text', 'icon_height', __('Custom Icon Height'))

                                                ))
                                                ->add_fields(
                                                    'custom_field_1',
                                                    array(
                                                        Field::make('text', 'custom_field_key', __('Custom Field Key')),
                                                        Field::make('select', 'custom_field_type', __('Custom Field Type'))
                                                            ->set_options(
                                                                array(
                                                                    'p'      => 'p',
                                                                    'h2'      => 'h2',
                                                                    'h3'      => 'h3',
                                                                    'h4'      => 'h4',
                                                                    'h5'      => 'h5',
                                                                    'h6'      => 'h6',
                                                                    'img'      => 'img',
                                                                )
                                                            ),
                                                        Field::make('text', 'custom_field_class', __('Wrapper Class')),
                                                    )
                                                )
                                                ->set_header_template('Custom Field: <%- custom_field_key  %>')
                                                ->add_fields(
                                                    'custom_field_2',
                                                    array(
                                                        Field::make('text', 'custom_field_key', __('Custom Field Key')),
                                                        Field::make('select', 'custom_field_type', __('Custom Field Type'))
                                                            ->set_options(
                                                                array(
                                                                    'p'      => 'p',
                                                                    'h2'      => 'h2',
                                                                    'h3'      => 'h3',
                                                                    'h4'      => 'h4',
                                                                    'h5'      => 'h5',
                                                                    'h6'      => 'h6',
                                                                    'img'      => 'img',
                                                                )
                                                            ),
                                                        Field::make('text', 'custom_field_class', __('Wrapper Class')),
                                                    )
                                                )
                                                ->set_header_template('Custom Field: <%- custom_field_key  %>')
                                                ->add_fields(
                                                    'custom_field_3',
                                                    array(
                                                        Field::make('text', 'custom_field_key', __('Custom Field Key')),
                                                        Field::make('select', 'custom_field_type', __('Custom Field Type'))
                                                            ->set_options(
                                                                array(
                                                                    'p'      => 'p',
                                                                    'h2'      => 'h2',
                                                                    'h3'      => 'h3',
                                                                    'h4'      => 'h4',
                                                                    'h5'      => 'h5',
                                                                    'h6'      => 'h6',
                                                                    'img'      => 'img',
                                                                )
                                                            ),
                                                        Field::make('text', 'custom_field_class', __('Wrapper Class')),
                                                    )
                                                )
                                                ->set_header_template('Custom Field: <%- custom_field_key  %>')
                                                ->add_fields(
                                                    'custom_field_4',
                                                    array(
                                                        Field::make('text', 'custom_field_key', __('Custom Field Key')),
                                                        Field::make('select', 'custom_field_type', __('Custom Field Type'))
                                                            ->set_options(
                                                                array(
                                                                    'p'      => 'p',
                                                                    'h2'      => 'h2',
                                                                    'h3'      => 'h3',
                                                                    'h4'      => 'h4',
                                                                    'h5'      => 'h5',
                                                                    'h6'      => 'h6',
                                                                    'img'      => 'img',
                                                                )
                                                            ),
                                                        Field::make('text', 'custom_field_class', __('Wrapper Class')),
                                                    )
                                                )
                                                ->set_header_template('Custom Field: <%- custom_field_key  %>')
                                                ->add_fields(
                                                    'custom_field_5',
                                                    array(
                                                        Field::make('text', 'custom_field_key', __('Custom Field Key')),
                                                        Field::make('select', 'custom_field_type', __('Custom Field Type'))
                                                            ->set_options(
                                                                array(
                                                                    'p'      => 'p',
                                                                    'h2'      => 'h2',
                                                                    'h3'      => 'h3',
                                                                    'h4'      => 'h4',
                                                                    'h5'      => 'h5',
                                                                    'h6'      => 'h6',
                                                                    'img'      => 'img',
                                                                )
                                                            ),
                                                        Field::make('text', 'custom_field_class', __('Wrapper Class')),
                                                    )
                                                )
                                                ->set_header_template('Custom Field: <%- custom_field_key  %>')
                                                ->set_layout('tabbed-vertical'),
                                            Field::make('html', 'post_type_html')->set_html('<label>Post Type</label>')->set_classes('cb-label'),
                                            Field::make('complex', 'post_type', 'Post Type')
                                                ->set_duplicate_groups_allowed(false)
                                                ->set_max(1)
                                                ->add_fields(
                                                    'solutions',
                                                    array(
                                                        Field::make('select', 'source', __('Source'))
                                                            ->set_options(
                                                                array(
                                                                    'all'      => 'Select All',
                                                                    'manually'      => 'Select Manually',
                                                                )
                                                            ),

                                                        Field::make('association', 'post', 'Select Solutions')
                                                            ->set_types(
                                                                array(
                                                                    array(
                                                                        'type'      => 'post',
                                                                        'post_type' => 'solutions',
                                                                    )
                                                                )
                                                            )
                                                            ->set_conditional_logic(
                                                                array(
                                                                    array(
                                                                        'field' => 'source',
                                                                        'value' => 'manually',
                                                                    )
                                                                )
                                                            ),

                                                    )
                                                )
                                                ->add_fields(
                                                    'casestudies',
                                                    array(
                                                        Field::make('hidden', 'taxonomy_key', '')->set_default_value('casestudies_category'),
                                                        Field::make('select', 'source', __('Source'))
                                                            ->set_options(
                                                                array(
                                                                    'all'      => 'Select All',
                                                                    'manually'      => 'Select Manually',
                                                                    'category'      => 'Select by Category',
                                                                )
                                                            ),

                                                        Field::make('association', 'post', 'Select Solutions')
                                                            ->set_types(
                                                                array(
                                                                    array(
                                                                        'type'      => 'post',
                                                                        'post_type' => 'casestudies',
                                                                    )
                                                                )
                                                            )
                                                            ->set_conditional_logic(
                                                                array(
                                                                    array(
                                                                        'field' => 'source',
                                                                        'value' => 'manually',
                                                                    )
                                                                )
                                                            ),
                                                        Field::make('association', 'category', 'Select Product Categories')
                                                            ->set_types(
                                                                array(
                                                                    array(
                                                                        'type'      => 'term',
                                                                        'taxonomy' => 'casestudies_category',
                                                                    )
                                                                )
                                                            )
                                                            ->set_conditional_logic(
                                                                array(
                                                                    array(
                                                                        'field' => 'source',
                                                                        'value' => 'category',
                                                                        'comapre' => '='
                                                                    )
                                                                )
                                                            ),

                                                    )
                                                )
                                                ->add_fields(
                                                    'testimonials',
                                                    array(
                                                        Field::make('hidden', 'taxonomy_key', '')->set_default_value('testimonial_category'),
                                                        Field::make('select', 'source', __('Source'))
                                                            ->set_options(
                                                                array(
                                                                    'all'      => 'Select All',
                                                                    'manually'      => 'Select Manually',
                                                                    'category'      => 'Select by Category',
                                                                )
                                                            ),

                                                        Field::make('association', 'post', 'Select Testimonials')
                                                            ->set_types(
                                                                array(
                                                                    array(
                                                                        'type'      => 'post',
                                                                        'post_type' => 'testimonials',
                                                                    )
                                                                )
                                                            )
                                                            ->set_conditional_logic(
                                                                array(
                                                                    array(
                                                                        'field' => 'source',
                                                                        'value' => 'manually',
                                                                    )
                                                                )
                                                            ),
                                                        Field::make('association', 'category', 'Select Categories')
                                                            ->set_types(
                                                                array(
                                                                    array(
                                                                        'type'      => 'term',
                                                                        'taxonomy' => 'testimonial_category',
                                                                    )
                                                                )
                                                            )
                                                            ->set_conditional_logic(
                                                                array(
                                                                    array(
                                                                        'field' => 'source',
                                                                        'value' => 'category',
                                                                        'comapre' => '='
                                                                    )
                                                                )
                                                            ),

                                                    )
                                                )
                                                ->add_fields(
                                                    'capabilities',
                                                    array(
                                                        Field::make('select', 'source', __('Source'))
                                                            ->set_options(
                                                                array(
                                                                    'all'      => 'Select All',
                                                                    'manually'      => 'Select Manually',
                                                                )
                                                            ),

                                                        Field::make('association', 'post', 'Select capabilities')
                                                            ->set_types(
                                                                array(
                                                                    array(
                                                                        'type'      => 'post',
                                                                        'post_type' => 'capabilities',
                                                                    )
                                                                )
                                                            )
                                                            ->set_conditional_logic(
                                                                array(
                                                                    array(
                                                                        'field' => 'source',
                                                                        'value' => 'manually',
                                                                    )
                                                                )
                                                            ),


                                                    )
                                                )
                                                ->set_layout('tabbed-vertical')

                                        ),
                                    )
                                    ->add_fields(
                                        'divider',
                                        array(
                                            Field::make('select', 'margin_top', 'Margin Top')
                                                ->set_options(
                                                    array(
                                                        ''               => 'No margin',
                                                        'xl-margin-top'  => 'Extra Large',
                                                        'lg-margin-top'  => 'Large',
                                                        'md-margin-top'  => 'Medium',
                                                        'sm-margin-top'  => 'Small',
                                                        'xs-margin-top' => 'Extra Small',
                                                    )
                                                ),
                                            Field::make('select', 'margin_bottom', 'Margin Bottom')
                                                ->set_options(
                                                    array(
                                                        ''                  => 'No margin',
                                                        'xl-margin-bottom'  => 'Extra Large',
                                                        'lg-margin-bottom'  => 'Large',
                                                        'md-margin-bottom'  => 'Medium',
                                                        'sm-margin-bottom'  => 'Small',
                                                        'xs-margin-bottom' => 'Extra Small',
                                                    )
                                                ),
                                            Field::make('select', 'margin_left', 'Margin Left')
                                                ->set_options(
                                                    array(
                                                        ''                => 'No margin',
                                                        'xl-margin-left'  => 'Extra Large',
                                                        'lg-margin-left'  => 'Large',
                                                        'md-margin-left'  => 'Medium',
                                                        'sm-margin-left'  => 'Small',
                                                        'xs-margin-left' => 'Extra Small',
                                                    )
                                                ),
                                            Field::make('select', 'margin_right', 'Margin Right')
                                                ->set_options(
                                                    array(
                                                        ''                 => 'No margin',
                                                        'xl-margin-right'  => 'Extra Large',
                                                        'lg-margin-right'  => 'Large',
                                                        'md-margin-right'  => 'Medium',
                                                        'sm-margin-right'  => 'Small',
                                                        'xs-margin-right' => 'Extra Small',
                                                    )
                                                ),
                                            Field::make('select', 'border_color', 'Border Color')
                                                ->set_options(
                                                    array(
                                                        ''   => 'Default',
                                                        'text-primary'   => 'Primary',
                                                        'text-secondary' => 'Secondary',
                                                        'text-accent'    => 'Accent',
                                                        'text-white'     => 'White',
                                                        'text-light-gray'     => 'Light Gray',
                                                        'border-custom-color'    => 'Custom',
                                                    )
                                                ),
                                            Field::make('color', 'border_color_custom', __('Border Color'))
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'border_color',
                                                            'value' => 'border-custom-color',
                                                        )
                                                    )
                                                ),
                                            Field::make('text', 'border_width', 'Border Width'),
                                        )
                                    )
                                    ->add_fields(
                                        'custom_html',
                                        array(
                                            Field::make('textarea', 'custom_html', __('Custom HTML')),
                                        )
                                    ),

                                //Individual Columns Styles
                                Field::make('html', 'html_6')->set_html('<label>Column Styles</label>')->set_classes('cb-label')
                                    ->set_conditional_logic(
                                        array(
                                            array(
                                                'field' => 'parent.individual_column_settings',
                                                'value' => true,
                                            )
                                        )
                                    ),
                                Field::make('complex', 'column_styles', __(''))
                                    ->set_conditional_logic(
                                        array(
                                            array(
                                                'field' => 'parent.individual_column_settings',
                                                'value' => true,
                                            )
                                        )
                                    )
                                    ->setup_labels(
                                        array(
                                            'plural_name'   => 'Styles',
                                            'singular_name' => 'Style',
                                        )
                                    )
                                    ->set_duplicate_groups_allowed(false)
                                    ->add_fields(
                                        'background_color',
                                        array(
                                            Field::make('select', 'background_color', 'Background Color')
                                                ->set_options(
                                                    array(
                                                        'bg-primary'   => 'Primary',
                                                        'bg-secondary' => 'Secondary',
                                                        'bg-accent'    => 'Accent',
                                                        'bg-white'     => 'White',
                                                        'bg-light-gray'     => 'Light Gray',
                                                        'bg-custom'    => 'Custom',
                                                    )
                                                ),
                                            Field::make('color', 'background_color_custom', __('Background Color'))
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'background_color',
                                                            'value' => 'bg-custom',
                                                        )
                                                    )
                                                ),
                                        )
                                    )
                                    ->add_fields(
                                        'background_image',
                                        array(
                                            Field::make('image', 'background_image', 'Background Image'),
                                            Field::make('select', 'background_size', 'Background Size')
                                                ->set_options(
                                                    array(
                                                        'bg-cover' => 'Cover',
                                                        'bg-contain'  => 'Contain',
                                                    )
                                                ),
                                            Field::make('select', 'background_attachment', 'Background Attachment')
                                                ->set_options(
                                                    array(
                                                        'bg-scroll'    => 'Scroll',
                                                        'bg-fixed'  => 'Fixed',
                                                    )
                                                ),
                                            Field::make('select', 'background_repeat', 'Background Repeat')
                                                ->set_options(
                                                    array(
                                                        'bg-no-repeat'    => 'No Repeat',
                                                        'bg-repeat'  => 'No Repeat',
                                                    )
                                                ),
                                        )
                                    )
                                    ->add_fields(
                                        'background_overlay',
                                        array(
                                            Field::make('select', 'background_overlay_type', 'Background Overlay Type')
                                                ->set_options(
                                                    array(
                                                        'default'    => 'Default',
                                                        'image'  => 'Image',
                                                        'custom'  => 'Custom',
                                                    )
                                                ),
                                            Field::make('image', 'background_overlay_image', 'Image Background Overlay')
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'background_overlay_type',
                                                            'value' => 'image',
                                                        )
                                                    )
                                                ),
                                            Field::make('text', 'background_overlay_image_opacity', 'Image Background Overlay Opacity')
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'background_overlay_type',
                                                            'value' => 'image',
                                                        )
                                                    )
                                                ),
                                            Field::make('color', 'background_overlay_custom', 'Custom Background Overlay')
                                                ->set_alpha_enabled(true)
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'background_overlay_type',
                                                            'value' => 'custom',
                                                        )
                                                    )
                                                ),
                                        )
                                    )
                                    ->add_fields(
                                        'text_color',
                                        array(
                                            Field::make('select', 'text_color', 'Text Color')
                                                ->set_options(
                                                    array(
                                                        'text-primary'   => 'Primary',
                                                        'text-secondary' => 'Secondary',
                                                        'text-accent'    => 'Accent',
                                                        'text-white'     => 'White',
                                                        'text-light-gray'     => 'Light Gray',
                                                        'text-custom'    => 'Custom',
                                                    )
                                                ),
                                            Field::make('color', 'text_color_custom', __('Text Color'))
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'text_color',
                                                            'value' => 'text-custom',
                                                        )
                                                    )
                                                ),
                                        )
                                    )
                                    ->add_fields(
                                        'padding',
                                        array(
                                            Field::make('select', 'padding_top', 'Padding Top')
                                                ->set_options(
                                                    array(
                                                        ''                => 'No Padding',
                                                        'xl-padding-top'  => 'Extra Large',
                                                        'lg-padding-top'  => 'Large',
                                                        'md-padding-top'  => 'Medium',
                                                        'sm-padding-top'  => 'Small',
                                                        'xs-padding-top' => 'Extra Small',
                                                    )
                                                ),
                                            Field::make('select', 'padding_bottom', 'Padding Bottom')
                                                ->set_options(
                                                    array(
                                                        ''                   => 'No Padding',
                                                        'xl-padding-bottom'  => 'Extra Large',
                                                        'lg-padding-bottom'  => 'Large',
                                                        'md-padding-bottom'  => 'Medium',
                                                        'sm-padding-bottom'  => 'Small',
                                                        'xs-padding-bottom' => 'Extra Small',
                                                    )
                                                ),
                                            Field::make('select', 'padding_left', 'Padding left')
                                                ->set_options(
                                                    array(
                                                        ''                 => 'No Padding',
                                                        'xl-padding-left'  => 'Extra Large',
                                                        'lg-padding-left'  => 'Large',
                                                        'md-padding-left'  => 'Medium',
                                                        'sm-padding-left'  => 'Small',
                                                        'xs-padding-left' => 'Extra Small',
                                                    )
                                                ),
                                            Field::make('select', 'padding_right', 'Padding right')
                                                ->set_options(
                                                    array(
                                                        ''                  => 'No Padding',
                                                        'xl-padding-right'  => 'Extra Large',
                                                        'lg-padding-right'  => 'Large',
                                                        'md-padding-right'  => 'Medium',
                                                        'sm-padding-right'  => 'Small',
                                                        'xs-padding-right' => 'Extra Small',
                                                    )
                                                ),

                                        )
                                    )
                                    ->add_fields(
                                        'margin',
                                        array(
                                            Field::make('select', 'margin_top', 'Margin Top')
                                                ->set_options(
                                                    array(
                                                        ''               => 'No margin',
                                                        'xl-margin-top'  => 'Extra Large',
                                                        'lg-margin-top'  => 'Large',
                                                        'md-margin-top'  => 'Medium',
                                                        'sm-margin-top'  => 'Small',
                                                        'xs-margin-top' => 'Extra Small',
                                                    )
                                                ),
                                            Field::make('select', 'margin_bottom', 'Margin Bottom')
                                                ->set_options(
                                                    array(
                                                        ''                  => 'No margin',
                                                        'xl-margin-bottom'  => 'Extra Large',
                                                        'lg-margin-bottom'  => 'Large',
                                                        'md-margin-bottom'  => 'Medium',
                                                        'sm-margin-bottom'  => 'Small',
                                                        'xs-margin-bottom' => 'Extra Small',
                                                    )
                                                ),
                                            Field::make('select', 'margin_left', 'Margin Left')
                                                ->set_options(
                                                    array(
                                                        ''                => 'No margin',
                                                        'xl-margin-left'  => 'Extra Large',
                                                        'lg-margin-left'  => 'Large',
                                                        'md-margin-left'  => 'Medium',
                                                        'sm-margin-left'  => 'Small',
                                                        'xs-margin-left' => 'Extra Small',
                                                    )
                                                ),
                                            Field::make('select', 'margin_right', 'Margin Right')
                                                ->set_options(
                                                    array(
                                                        ''                 => 'No margin',
                                                        'xl-margin-right'  => 'Extra Large',
                                                        'lg-margin-right'  => 'Large',
                                                        'md-margin-right'  => 'Medium',
                                                        'sm-margin-right'  => 'Small',
                                                        'xs-margin-right' => 'Extra Small',
                                                    )
                                                ),

                                        )
                                    )
                                    ->add_fields(
                                        'alignment',
                                        array(
                                            Field::make('select', 'align_items', 'Align Items')
                                                ->set_options(
                                                    array(
                                                        ''               => 'Default',
                                                        'align-items-start'  => 'Start',
                                                        'align-items-center'  => 'Center',
                                                        'align-items-end'  => 'End',
                                                    )
                                                ),
                                            Field::make('select', 'justify_content', 'Justify Content')
                                                ->set_options(
                                                    array(
                                                        ''                  => 'Default',
                                                        'justify-content-start'  => 'Start',
                                                        'justify-content-center'  => 'Center',
                                                        'justify-content-end'  => 'End',
                                                        'justify-content-between'  => 'Between',
                                                    )
                                                ),
                                            Field::make('select', 'flex_direction', 'Flex Direction')
                                                ->set_options(
                                                    array(
                                                        ''                  => 'Default',
                                                        'flex-column'  => 'Column',
                                                        'flex-column-reverse'  => 'Column Reverse',
                                                        'flex-row'  => 'Row',
                                                        'flex-row-reverse'  => 'Row Reverse',
                                                    )
                                                ),
                                            Field::make('select', 'text_align', 'Text Align[Desktop]')
                                                ->set_options(
                                                    array(
                                                        ''                => 'Default',
                                                        'text-lg-start'                => 'Left',
                                                        'text-lg-center'                => 'Center',
                                                        'text-lg-end'                => 'Right',
                                                    )
                                                ),
                                            Field::make('select', 'text_align_tablet', 'Text Align[Tablet]')
                                                ->set_options(
                                                    array(
                                                        ''                => 'Inherit',
                                                        'text-md-start'                => 'Left',
                                                        'text-md-center'                => 'Center',
                                                        'text-lg-end'                => 'Right',
                                                    )
                                                ),
                                            Field::make('select', 'text_align_mobile', 'Text Align[Mobile]')
                                                ->set_options(
                                                    array(
                                                        ''                => 'Inherit',
                                                        'text-start'                => 'Left',
                                                        'text-center'                => 'Center',
                                                        'text-end'                => 'Right',
                                                    )
                                                ),
                                        )
                                    )
                                    ->add_fields(
                                        'column_width',
                                        array(
                                            Field::make('select', 'column_width', __('Column Width Desktop'))
                                                ->set_options(
                                                    array(
                                                        'col-lg'     => 'Default',
                                                        'col-12'    => '100.00%',
                                                        'col-lg-11' => '91.67%',
                                                        'col-lg-10' => '83.33%',
                                                        'col-lg-9'  => '75.00%',
                                                        'col-lg-8'  => '67.00%',
                                                        'col-lg-7'  => '58.33%',
                                                        'col-lg-6'  => '50.00%',
                                                        'col-lg-5'  => '41.67%',
                                                        'col-lg-4'  => '33.33%',
                                                        'col-lg-3'  => '25.00%',
                                                        'col-lg-2'  => '16.67%',
                                                        'col-lg-1'  => '08.33%',
                                                    )
                                                ),
                                            Field::make('select', 'column_width_tablet', __('Column Width Tablet'))
                                                ->set_options(
                                                    array(
                                                        ''     => 'Default',
                                                        'col-md-12'    => '100.00%',
                                                        'col-md-11' => '91.67%',
                                                        'col-md-10' => '83.33%',
                                                        'col-md-9'  => '75.00%',
                                                        'col-md-8'  => '67.00%',
                                                        'col-md-7'  => '58.33%',
                                                        'col-md-6'  => '50.00%',
                                                        'col-md-5'  => '41.67%',
                                                        'col-md-4'  => '33.33%',
                                                        'col-md-3'  => '25.00%',
                                                        'col-md-2'  => '16.67%',
                                                        'col-md-1'  => '08.33%',
                                                    )
                                                ),
                                            Field::make('select', 'column_width_mobile', __('Column Width Mobile'))
                                                ->set_options(
                                                    array(
                                                        ''     => 'Default',
                                                        'col-12' => '100%',
                                                        'col-11' => '91.67%',
                                                        'col-10' => '83.33%',
                                                        'col-9'  => '75.00%',
                                                        'col-8'  => '67.00%',
                                                        'col-7'  => '58.33%',
                                                        'col-6'  => '50.00%',
                                                        'col-5'  => '41.67%',
                                                        'col-4'  => '33.33%',
                                                        'col-3'  => '25.00%',
                                                        'col-2'  => '16.67%',
                                                        'col-1'  => '08.33%',
                                                    )
                                                ),
                                        )
                                    )
                                    ->add_fields(
                                        'border',
                                        array(
                                            Field::make('select', 'border_radius', 'Border Radius')->set_classes('inline-field-wide-label')
                                                ->set_options(
                                                    array(
                                                        ''   => 'None',
                                                        'rounded-corner'   => 'Default[10px]',
                                                        'custom'   => 'Custom',
                                                    )
                                                ),
                                            Field::make('text', 'border_radius_custom', 'Custom Border Radius')->set_classes('inline-field-wide-label')
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'border_radius',
                                                            'value' => 'custom',
                                                            'compare' => '='
                                                        )
                                                    )
                                                ),
                                            Field::make('select', 'border_style', 'Border Style')->set_classes('inline-field-wide-label')
                                                ->set_options(
                                                    array(
                                                        ''   => 'None',
                                                        'border-default'   => 'Default',
                                                        'border-custom'   => 'Custom',
                                                    )
                                                ),

                                            Field::make('select', 'border_color', 'Border Color')->set_classes('inline-field-wide-label')
                                                ->set_options(
                                                    array(
                                                        'border-default'   => 'Default',
                                                        'border-primary'   => 'Primary',
                                                        'border-secondary' => 'Secondary',
                                                        'border-accent'    => 'Accent',
                                                        'border-white'     => 'White',
                                                        'border-light-gray'     => 'Light Gray',
                                                        'border-custom-color'    => 'Custom',
                                                    )
                                                )
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'border_style',
                                                            'value' => 'border-custom',
                                                        ),
                                                    )
                                                ),
                                            Field::make('color', 'border_color_custom', __('Border Color'))->set_classes('inline-field-wide-label')
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'border_color',
                                                            'value' => 'border-custom-color',
                                                        ),
                                                    )
                                                ),

                                            Field::make('select', 'border_width', 'Border Width')->set_classes('inline-field-wide-label')
                                                ->set_options(array(
                                                    'default' => 'Default[1px]',
                                                    'custom' => 'Custom'
                                                ))
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'border_style',
                                                            'value' => 'border-custom',
                                                        ),
                                                    )
                                                ),
                                            Field::make('text', 'border_width_top', 'Top Border Width')->set_classes('inline-field-wide-label')->set_default_value(0)
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'border_width',
                                                            'value' => 'custom',
                                                        ),
                                                    )
                                                ),
                                            Field::make('text', 'border_width_right', 'Right Border Width')->set_classes('inline-field-wide-label')->set_default_value(0)
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'border_width',
                                                            'value' => 'custom',
                                                        ),
                                                    )
                                                ),
                                            Field::make('text', 'border_width_bottom', 'Bottom Border Width')->set_classes('inline-field-wide-label')->set_default_value(0)
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'border_width',
                                                            'value' => 'custom',
                                                        ),
                                                    )
                                                ),
                                            Field::make('text', 'border_width_left', 'Left Border Width')->set_classes('inline-field-wide-label')->set_default_value(0)
                                                ->set_conditional_logic(
                                                    array(
                                                        array(
                                                            'field' => 'border_width',
                                                            'value' => 'custom',
                                                        ),
                                                    )
                                                ),
                                        )
                                    )
                                    ->add_fields(
                                        'custom_class',
                                        array(
                                            Field::make('text', 'custom_class', 'Custom Class')
                                        )
                                    )
                                    ->set_layout('tabbed-vertical')

                            ))
                            ->set_header_template('Column: <%- column_title %>'),
                        Field::make('html', 'html_6')->set_html('<label>Column Styles</label>')->set_classes('cb-label')
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'individual_column_settings',
                                        'value' => false,
                                    )
                                )
                            ),
                        //All columns styles
                        Field::make('complex', 'column_styles', __(''))
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'individual_column_settings',
                                        'value' => false,
                                    )
                                )
                            )
                            ->setup_labels(
                                array(
                                    'plural_name'   => 'Styles',
                                    'singular_name' => 'Style',
                                )
                            )
                            ->set_duplicate_groups_allowed(false)
                            ->add_fields(
                                'background_color',
                                array(
                                    Field::make('select', 'background_color', 'Background Color')
                                        ->set_options(
                                            array(
                                                'bg-primary'   => 'Primary',
                                                'bg-secondary' => 'Secondary',
                                                'bg-accent'    => 'Accent',
                                                'bg-white'     => 'White',
                                                'bg-light-gray'     => 'Light Gray',
                                                'bg-custom'    => 'Custom',
                                            )
                                        ),
                                    Field::make('color', 'background_color_custom', __('Background Color'))
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field' => 'background_color',
                                                    'value' => 'bg-custom',
                                                )
                                            )
                                        ),
                                )
                            )
                            ->add_fields(
                                'background_image',
                                array(
                                    Field::make('image', 'background_image', 'Background Image'),
                                    Field::make('select', 'background_size', 'Background Size')
                                        ->set_options(
                                            array(
                                                'bg-cover' => 'Cover',
                                                'bg-contain'  => 'Contain',
                                            )
                                        ),
                                    Field::make('select', 'background_attachment', 'Background Attachment')
                                        ->set_options(
                                            array(
                                                'bg-scroll'    => 'Scroll',
                                                'bg-fixed'  => 'Fixed',
                                            )
                                        ),
                                    Field::make('select', 'background_repeat', 'Background Repeat')
                                        ->set_options(
                                            array(
                                                'bg-no-repeat'    => 'No Repeat',
                                                'bg-repeat'  => 'No Repeat',
                                            )
                                        ),
                                )
                            )
                            ->add_fields(
                                'background_overlay',
                                array(
                                    Field::make('select', 'background_overlay_type', 'Background Overlay Type')
                                        ->set_options(
                                            array(
                                                'default'    => 'Default',
                                                'image'  => 'Image',
                                                'custom'  => 'Custom',
                                            )
                                        ),
                                    Field::make('image', 'background_overlay_image', 'Image Background Overlay')
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field' => 'background_overlay_type',
                                                    'value' => 'image',
                                                )
                                            )
                                        ),
                                    Field::make('text', 'background_overlay_image_opacity', 'Image Background Overlay Opacity')
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field' => 'background_overlay_type',
                                                    'value' => 'image',
                                                )
                                            )
                                        ),
                                    Field::make('color', 'background_overlay_custom', 'Custom Background Overlay')
                                        ->set_alpha_enabled(true)
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field' => 'background_overlay_type',
                                                    'value' => 'custom',
                                                )
                                            )
                                        ),
                                )
                            )
                            ->add_fields(
                                'text_color',
                                array(
                                    Field::make('select', 'text_color', 'Text Color')
                                        ->set_options(
                                            array(
                                                'text-primary'   => 'Primary',
                                                'text-secondary' => 'Secondary',
                                                'text-accent'    => 'Accent',
                                                'text-white'     => 'White',
                                                'text-light-gray'     => 'Light Gray',
                                                'text-custom'    => 'Custom',
                                            )
                                        ),
                                    Field::make('color', 'text_color_custom', __('Text Color'))
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field' => 'text_color',
                                                    'value' => 'text-custom',
                                                )
                                            )
                                        ),
                                )
                            )
                            ->add_fields(
                                'padding',
                                array(
                                    Field::make('select', 'padding_top', 'Padding Top')
                                        ->set_options(
                                            array(
                                                ''                => 'No Padding',
                                                'xl-padding-top'  => 'Extra Large',
                                                'lg-padding-top'  => 'Large',
                                                'md-padding-top'  => 'Medium',
                                                'sm-padding-top'  => 'Small',
                                                'xs-padding-top' => 'Extra Small',
                                            )
                                        ),
                                    Field::make('select', 'padding_bottom', 'Padding Bottom')
                                        ->set_options(
                                            array(
                                                ''                   => 'No Padding',
                                                'xl-padding-bottom'  => 'Extra Large',
                                                'lg-padding-bottom'  => 'Large',
                                                'md-padding-bottom'  => 'Medium',
                                                'sm-padding-bottom'  => 'Small',
                                                'xs-padding-bottom' => 'Extra Small',
                                            )
                                        ),
                                    Field::make('select', 'padding_left', 'Padding left')
                                        ->set_options(
                                            array(
                                                ''                 => 'No Padding',
                                                'xl-padding-left'  => 'Extra Large',
                                                'lg-padding-left'  => 'Large',
                                                'md-padding-left'  => 'Medium',
                                                'sm-padding-left'  => 'Small',
                                                'xs-padding-left' => 'Extra Small',
                                            )
                                        ),
                                    Field::make('select', 'padding_right', 'Padding right')
                                        ->set_options(
                                            array(
                                                ''                  => 'No Padding',
                                                'xl-padding-right'  => 'Extra Large',
                                                'lg-padding-right'  => 'Large',
                                                'md-padding-right'  => 'Medium',
                                                'sm-padding-right'  => 'Small',
                                                'xs-padding-right' => 'Extra Small',
                                            )
                                        ),

                                )
                            )
                            ->add_fields(
                                'margin',
                                array(
                                    Field::make('select', 'margin_top', 'Margin Top')
                                        ->set_options(
                                            array(
                                                ''               => 'No margin',
                                                'xl-margin-top'  => 'Extra Large',
                                                'lg-margin-top'  => 'Large',
                                                'md-margin-top'  => 'Medium',
                                                'sm-margin-top'  => 'Small',
                                                'xs-margin-top' => 'Extra Small',
                                            )
                                        ),
                                    Field::make('select', 'margin_bottom', 'Margin Bottom')
                                        ->set_options(
                                            array(
                                                ''                  => 'No margin',
                                                'xl-margin-bottom'  => 'Extra Large',
                                                'lg-margin-bottom'  => 'Large',
                                                'md-margin-bottom'  => 'Medium',
                                                'sm-margin-bottom'  => 'Small',
                                                'xs-margin-bottom' => 'Extra Small',
                                            )
                                        ),
                                    Field::make('select', 'margin_left', 'Margin Left')
                                        ->set_options(
                                            array(
                                                ''                => 'No margin',
                                                'xl-margin-left'  => 'Extra Large',
                                                'lg-margin-left'  => 'Large',
                                                'md-margin-left'  => 'Medium',
                                                'sm-margin-left'  => 'Small',
                                                'xs-margin-left' => 'Extra Small',
                                            )
                                        ),
                                    Field::make('select', 'margin_right', 'Margin Right')
                                        ->set_options(
                                            array(
                                                ''                 => 'No margin',
                                                'xl-margin-right'  => 'Extra Large',
                                                'lg-margin-right'  => 'Large',
                                                'md-margin-right'  => 'Medium',
                                                'sm-margin-right'  => 'Small',
                                                'xs-margin-right' => 'Extra Small',
                                            )
                                        ),

                                )
                            )
                            ->add_fields(
                                'alignment',
                                array(
                                    Field::make('select', 'align_items', 'Align Items')
                                        ->set_options(
                                            array(
                                                ''               => 'Default',
                                                'align-items-start'  => 'Start',
                                                'align-items-center'  => 'Center',
                                                'align-items-end'  => 'End',
                                            )
                                        ),
                                    Field::make('select', 'justify_content', 'Justify Content')
                                        ->set_options(
                                            array(
                                                ''                  => 'Default',
                                                'justify-content-start'  => 'Start',
                                                'justify-content-center'  => 'Center',
                                                'justify-content-end'  => 'End',
                                                'justify-content-between'  => 'Between',
                                            )
                                        ),
                                    Field::make('select', 'flex_direction', 'Flex Direction')
                                        ->set_options(
                                            array(
                                                ''                  => 'Default',
                                                'flex-column'  => 'Column',
                                                'flex-column-reverse'  => 'Column Reverse',
                                                'flex-row'  => 'Row',
                                                'flex-row-reverse'  => 'Row Reverse',
                                            )
                                        ),
                                    Field::make('select', 'text_align', 'Text Align[Desktop]')
                                        ->set_options(
                                            array(
                                                ''                => 'Default',
                                                'text-lg-start'                => 'Left',
                                                'text-lg-center'                => 'Center',
                                                'text-lg-end'                => 'Right',
                                            )
                                        ),
                                    Field::make('select', 'text_align_tablet', 'Text Align[Tablet]')
                                        ->set_options(
                                            array(
                                                ''                => 'Inherit',
                                                'text-md-start'                => 'Left',
                                                'text-md-center'                => 'Center',
                                                'text-lg-end'                => 'Right',
                                            )
                                        ),
                                    Field::make('select', 'text_align_mobile', 'Text Align[Mobile]')
                                        ->set_options(
                                            array(
                                                ''                => 'Inherit',
                                                'text-start'                => 'Left',
                                                'text-center'                => 'Center',
                                                'text-end'                => 'Right',
                                            )
                                        ),
                                )
                            )
                            ->add_fields(
                                'column_width',
                                array(
                                    Field::make('select', 'column_width', __('Column Width Desktop'))
                                        ->set_options(
                                            array(
                                                'col-lg'     => 'Default',
                                                'col-12'    => '100.00%',
                                                'col-lg-11' => '91.67%',
                                                'col-lg-10' => '83.33%',
                                                'col-lg-9'  => '75.00%',
                                                'col-lg-8'  => '67.00%',
                                                'col-lg-7'  => '58.33%',
                                                'col-lg-6'  => '50.00%',
                                                'col-lg-5'  => '41.67%',
                                                'col-lg-4'  => '33.33%',
                                                'col-lg-3'  => '25.00%',
                                                'col-lg-2'  => '16.67%',
                                                'col-lg-1'  => '08.33%',
                                                'col-lg-auto'  => 'auto',
                                            )
                                        ),
                                    Field::make('select', 'column_width_tablet', __('Column Width Tablet'))
                                        ->set_options(
                                            array(
                                                ''     => 'Default',
                                                'col-md-12'    => '100.00%',
                                                'col-md-11' => '91.67%',
                                                'col-md-10' => '83.33%',
                                                'col-md-9'  => '75.00%',
                                                'col-md-8'  => '67.00%',
                                                'col-md-7'  => '58.33%',
                                                'col-md-6'  => '50.00%',
                                                'col-md-5'  => '41.67%',
                                                'col-md-4'  => '33.33%',
                                                'col-md-3'  => '25.00%',
                                                'col-md-2'  => '16.67%',
                                                'col-md-1'  => '08.33%',
                                                'col-md-auto'  => 'auto',
                                            )
                                        ),
                                    Field::make('select', 'column_width_mobile', __('Column Width Mobile'))
                                        ->set_options(
                                            array(
                                                ''     => 'Default',
                                                'col-12' => '100%',
                                                'col-11' => '91.67%',
                                                'col-10' => '83.33%',
                                                'col-9'  => '75.00%',
                                                'col-8'  => '67.00%',
                                                'col-7'  => '58.33%',
                                                'col-6'  => '50.00%',
                                                'col-5'  => '41.67%',
                                                'col-4'  => '33.33%',
                                                'col-3'  => '25.00%',
                                                'col-2'  => '16.67%',
                                                'col-1'  => '08.33%',
                                                'col-auto'  => 'auto',
                                            )
                                        ),
                                )
                            )
                            ->add_fields(
                                'border',
                                array(
                                    Field::make('select', 'border_radius', 'Border Radius')->set_classes('inline-field-wide-label')
                                        ->set_options(
                                            array(
                                                ''   => 'None',
                                                'rounded-corner'   => 'Default[10px]',
                                                'custom'   => 'Custom',
                                            )
                                        ),
                                    Field::make('text', 'border_radius_custom', 'Custom Border Radius')->set_classes('inline-field-wide-label')
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field' => 'border_radius',
                                                    'value' => 'custom',
                                                    'compare' => '='
                                                )
                                            )
                                        ),
                                    Field::make('select', 'border_style', 'Border Style')->set_classes('inline-field-wide-label')
                                        ->set_options(
                                            array(
                                                ''   => 'None',
                                                'border-default'   => 'Default',
                                                'border-custom'   => 'Custom',
                                            )
                                        ),

                                    Field::make('select', 'border_color', 'Border Color')->set_classes('inline-field-wide-label')
                                        ->set_options(
                                            array(
                                                'border-default'   => 'Default',
                                                'border-primary'   => 'Primary',
                                                'border-secondary' => 'Secondary',
                                                'border-accent'    => 'Accent',
                                                'border-white'     => 'White',
                                                'border-light-gray'     => 'Light Gray',
                                                'border-custom-color'    => 'Custom',
                                            )
                                        )
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field' => 'border_style',
                                                    'value' => 'border-custom',
                                                ),
                                            )
                                        ),
                                    Field::make('color', 'border_color_custom', __('Border Color'))->set_classes('inline-field-wide-label')
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field' => 'border_color',
                                                    'value' => 'border-custom-color',
                                                ),
                                            )
                                        ),

                                    Field::make('select', 'border_width', 'Border Width')->set_classes('inline-field-wide-label')
                                        ->set_options(array(
                                            'default' => 'Default[1px]',
                                            'custom' => 'Custom'
                                        ))
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field' => 'border_style',
                                                    'value' => 'border-custom',
                                                ),
                                            )
                                        ),
                                    Field::make('text', 'border_width_top', 'Top Border Width')->set_classes('inline-field-wide-label')->set_default_value(0)
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field' => 'border_width',
                                                    'value' => 'custom',
                                                ),
                                            )
                                        ),
                                    Field::make('text', 'border_width_right', 'Right Border Width')->set_classes('inline-field-wide-label')->set_default_value(0)
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field' => 'border_width',
                                                    'value' => 'custom',
                                                ),
                                            )
                                        ),
                                    Field::make('text', 'border_width_bottom', 'Bottom Border Width')->set_classes('inline-field-wide-label')->set_default_value(0)
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field' => 'border_width',
                                                    'value' => 'custom',
                                                ),
                                            )
                                        ),
                                    Field::make('text', 'border_width_left', 'Left Border Width')->set_classes('inline-field-wide-label')->set_default_value(0)
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field' => 'border_width',
                                                    'value' => 'custom',
                                                ),
                                            )
                                        ),

                                )
                            )
                            ->add_fields(
                                'custom_class',
                                array(
                                    Field::make('text', 'custom_class', 'Custom Class')
                                )
                            )
                            ->set_layout('tabbed-vertical')

                    ))
                    ->set_header_template('Row <%-  %>')
                    ->add_fields(
                        'gallery',
                        array(
                            Field::make('select', 'gallery_style', 'Gallery Style')
                                ->set_options(
                                    array(
                                        'logo-slider'   => 'Logo Slider',
                                        'grid' => 'Grid',
                                    )
                                ),
                            Field::make('media_gallery', 'gallery', __('Gallery')),
                            Field::make('text', 'number_of_slides', __('Number of Slides Desktop'))->set_default_value(6)->set_required(true)->set_attribute('type', 'number')
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'gallery_style',
                                            'value' => 'logo-slider',
                                        )
                                    )
                                ),
                            Field::make('text', 'number_of_slides_tablet', __('Number of Slides Tablet'))
                                ->set_attribute('type', 'number')
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'gallery_style',
                                            'value' => 'logo-slider',
                                        )
                                    )
                                ),
                            Field::make('text', 'number_of_slides_mobile', __('Number of Slides Mobile'))
                                ->set_attribute('type', 'number')
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'gallery_style',
                                            'value' => 'logo-slider',
                                        )
                                    )
                                ),
                        )
                    )
                    ->add_fields(
                        'post_grid',
                        array(
                            Field::make('html', 'post_box_settings_html')->set_html('<label>Post Box Settings</label>')->set_classes('cb-label'),
                            Field::make('checkbox', 'is_slider', __('Is SLider')),
                            Field::make('text', 'number_of_slides', __('Number of Slides Desktop'))->set_default_value(6)->set_required(true)->set_attribute('type', 'number')->set_width(25)
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'is_slider',
                                            'value' => true,
                                        )
                                    )
                                ),
                            Field::make('text', 'number_of_slides_tablet', __('Number of Slides Tablet'))->set_attribute('type', 'number')->set_width(25)
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'is_slider',
                                            'value' => true,
                                        )
                                    )
                                ),
                            Field::make('text', 'number_of_slides_mobile', __('Number of Slides Mobile'))->set_attribute('type', 'number')->set_width(25)

                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'is_slider',
                                            'value' => true,
                                        )
                                    )
                                ),
                            Field::make('html', 'post_box_styles_html')->set_html('<label>Post Box Styles</label>')->set_classes('cb-label'),
                            Field::make('complex', 'post_box_styles', __(''))
                                ->setup_labels(
                                    array(
                                        'plural_name'   => 'Styles',
                                        'singular_name' => 'Style',
                                    )
                                )
                                ->set_duplicate_groups_allowed(false)
                                ->add_fields(
                                    'background_color',
                                    array(
                                        Field::make('select', 'background_color', 'Background Color')
                                            ->set_options(
                                                array(
                                                    'bg-primary'   => 'Primary',
                                                    'bg-secondary' => 'Secondary',
                                                    'bg-accent'    => 'Accent',
                                                    'bg-white'     => 'White',
                                                    'bg-light-gray'     => 'Light Gray',
                                                    'bg-custom'    => 'Custom',
                                                )
                                            ),
                                        Field::make('color', 'background_color_custom', __('Background Color'))
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'background_color',
                                                        'value' => 'bg-custom',
                                                    )
                                                )
                                            ),
                                    )
                                )
                                ->add_fields(
                                    'text_color',
                                    array(
                                        Field::make('select', 'text_color', 'Text Color')
                                            ->set_options(
                                                array(
                                                    'text-primary'   => 'Primary',
                                                    'text-secondary' => 'Secondary',
                                                    'text-accent'    => 'Accent',
                                                    'text-white'     => 'White',
                                                    'text-light-gray'     => 'Light Gray',
                                                    'text-custom'    => 'Custom',
                                                )
                                            ),
                                        Field::make('color', 'text_color_custom', __('Text Color'))
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'text_color',
                                                        'value' => 'text-custom',
                                                    )
                                                )
                                            ),
                                    )
                                )
                                ->add_fields(
                                    'padding',
                                    array(
                                        Field::make('select', 'padding_top', 'Padding Top')
                                            ->set_options(
                                                array(
                                                    ''                => 'No Padding',
                                                    'xl-padding-top'  => 'Extra Large',
                                                    'lg-padding-top'  => 'Large',
                                                    'md-padding-top'  => 'Medium',
                                                    'sm-padding-top'  => 'Small',
                                                    'xs-padding-top' => 'Extra Small',
                                                )
                                            ),
                                        Field::make('select', 'padding_bottom', 'Padding Bottom')
                                            ->set_options(
                                                array(
                                                    ''                   => 'No Padding',
                                                    'xl-padding-bottom'  => 'Extra Large',
                                                    'lg-padding-bottom'  => 'Large',
                                                    'md-padding-bottom'  => 'Medium',
                                                    'sm-padding-bottom'  => 'Small',
                                                    'xs-padding-bottom' => 'Extra Small',
                                                )
                                            ),
                                        Field::make('select', 'padding_left', 'Padding left')
                                            ->set_options(
                                                array(
                                                    ''                 => 'No Padding',
                                                    'xl-padding-left'  => 'Extra Large',
                                                    'lg-padding-left'  => 'Large',
                                                    'md-padding-left'  => 'Medium',
                                                    'sm-padding-left'  => 'Small',
                                                    'xs-padding-left' => 'Extra Small',
                                                )
                                            ),
                                        Field::make('select', 'padding_right', 'Padding right')
                                            ->set_options(
                                                array(
                                                    ''                  => 'No Padding',
                                                    'xl-padding-right'  => 'Extra Large',
                                                    'lg-padding-right'  => 'Large',
                                                    'md-padding-right'  => 'Medium',
                                                    'sm-padding-right'  => 'Small',
                                                    'xs-padding-right' => 'Extra Small',
                                                )
                                            ),

                                    )
                                )
                                ->add_fields(
                                    'margin',
                                    array(
                                        Field::make('select', 'margin_top', 'Margin Top')
                                            ->set_options(
                                                array(
                                                    ''               => 'No margin',
                                                    'xl-margin-top'  => 'Extra Large',
                                                    'lg-margin-top'  => 'Large',
                                                    'md-margin-top'  => 'Medium',
                                                    'sm-margin-top'  => 'Small',
                                                    'xs-margin-top' => 'Extra Small',
                                                )
                                            ),
                                        Field::make('select', 'margin_bottom', 'Margin Bottom')
                                            ->set_options(
                                                array(
                                                    ''                  => 'No margin',
                                                    'xl-margin-bottom'  => 'Extra Large',
                                                    'lg-margin-bottom'  => 'Large',
                                                    'md-margin-bottom'  => 'Medium',
                                                    'sm-margin-bottom'  => 'Small',
                                                    'xs-margin-bottom' => 'Extra Small',
                                                )
                                            ),
                                        Field::make('select', 'margin_left', 'Margin Left')
                                            ->set_options(
                                                array(
                                                    ''                => 'No margin',
                                                    'xl-margin-left'  => 'Extra Large',
                                                    'lg-margin-left'  => 'Large',
                                                    'md-margin-left'  => 'Medium',
                                                    'sm-margin-left'  => 'Small',
                                                    'xs-margin-left' => 'Extra Small',
                                                )
                                            ),
                                        Field::make('select', 'margin_right', 'Margin Right')
                                            ->set_options(
                                                array(
                                                    ''                 => 'No margin',
                                                    'xl-margin-right'  => 'Extra Large',
                                                    'lg-margin-right'  => 'Large',
                                                    'md-margin-right'  => 'Medium',
                                                    'sm-margin-right'  => 'Small',
                                                    'xs-margin-right' => 'Extra Small',
                                                )
                                            ),

                                    )
                                )
                                ->add_fields(
                                    'alignment',
                                    array(
                                        Field::make('select', 'align_items', 'Align Items')
                                            ->set_options(
                                                array(
                                                    ''               => 'Default',
                                                    'align-items-start'  => 'Start',
                                                    'align-items-center'  => 'Center',
                                                    'align-items-end'  => 'End',
                                                )
                                            ),
                                        Field::make('select', 'justify_content', 'Justify Content')
                                            ->set_options(
                                                array(
                                                    ''                  => 'Default',
                                                    'justify-content-start'  => 'Start',
                                                    'justify-content-center'  => 'Center',
                                                    'justify-content-end'  => 'End',
                                                    'justify-content-between'  => 'Between',
                                                )
                                            ),
                                        Field::make('select', 'text_align', 'Text Align')
                                            ->set_options(
                                                array(
                                                    ''                => 'Default',
                                                    'text-start'                => 'Left',
                                                    'text-center'                => 'Center',
                                                    'text-end'                => 'Right',
                                                )
                                            ),
                                    )
                                )
                                ->add_fields(
                                    'column_width',
                                    array(
                                        Field::make('select', 'column_width', __('Column Width Desktop'))
                                            ->set_options(
                                                array(
                                                    'col-lg'     => 'Default',
                                                    'col-12'    => '100.00%',
                                                    'col-lg-11' => '91.67%',
                                                    'col-lg-10' => '83.33%',
                                                    'col-lg-9'  => '75.00%',
                                                    'col-lg-8'  => '67.00%',
                                                    'col-lg-7'  => '58.33%',
                                                    'col-lg-6'  => '50.00%',
                                                    'col-lg-5'  => '41.67%',
                                                    'col-lg-4'  => '33.33%',
                                                    'col-lg-3'  => '25.00%',
                                                    'col-lg-2'  => '16.67%',
                                                    'col-lg-1'  => '08.33%',
                                                )
                                            ),
                                        Field::make('select', 'column_width_tablet', __('Column Width Tablet'))
                                            ->set_options(
                                                array(
                                                    ''     => 'Default',
                                                    'col-md-12'    => '100.00%',
                                                    'col-md-11' => '91.67%',
                                                    'col-md-10' => '83.33%',
                                                    'col-md-9'  => '75.00%',
                                                    'col-md-8'  => '67.00%',
                                                    'col-md-7'  => '58.33%',
                                                    'col-md-6'  => '50.00%',
                                                    'col-md-5'  => '41.67%',
                                                    'col-md-4'  => '33.33%',
                                                    'col-md-3'  => '25.00%',
                                                    'col-md-2'  => '16.67%',
                                                    'col-md-1'  => '08.33%',
                                                )
                                            ),
                                        Field::make('select', 'column_width_mobile', __('Column Width Mobile'))
                                            ->set_options(
                                                array(
                                                    ''     => 'Default',
                                                    'col-12' => '100%',
                                                    'col-11' => '91.67%',
                                                    'col-10' => '83.33%',
                                                    'col-9'  => '75.00%',
                                                    'col-8'  => '67.00%',
                                                    'col-7'  => '58.33%',
                                                    'col-6'  => '50.00%',
                                                    'col-5'  => '41.67%',
                                                    'col-4'  => '33.33%',
                                                    'col-3'  => '25.00%',
                                                    'col-2'  => '16.67%',
                                                    'col-1'  => '08.33%',
                                                )
                                            ),
                                    )
                                )
                                ->add_fields(
                                    'border',
                                    array(
                                        Field::make('select', 'border_radius', 'Border Radius')->set_classes('inline-field-wide-label')
                                            ->set_options(
                                                array(
                                                    ''   => 'None',
                                                    'rounded-corner'   => 'Default[10px]',
                                                    'custom'   => 'Custom',
                                                )
                                            ),
                                        Field::make('text', 'border_radius_custom', 'Custom Border Radius')->set_classes('inline-field-wide-label')
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'border_radius',
                                                        'value' => 'custom',
                                                        'compare' => '='
                                                    )
                                                )
                                            ),
                                        Field::make('select', 'border_style', 'Border Style')->set_classes('inline-field-wide-label')
                                            ->set_options(
                                                array(
                                                    ''   => 'None',
                                                    'border-default'   => 'Default',
                                                    'border-custom'   => 'Custom',
                                                )
                                            ),

                                        Field::make('select', 'border_color', 'Border Color')->set_classes('inline-field-wide-label')
                                            ->set_options(
                                                array(
                                                    'border-default'   => 'Default',
                                                    'border-primary'   => 'Primary',
                                                    'border-secondary' => 'Secondary',
                                                    'border-accent'    => 'Accent',
                                                    'border-white'     => 'White',
                                                    'border-light-gray'     => 'Light Gray',
                                                    'border-custom-color'    => 'Custom',
                                                )
                                            )
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'border_style',
                                                        'value' => 'border-custom',
                                                    ),
                                                )
                                            ),
                                        Field::make('color', 'border_color_custom', __('Border Color'))->set_classes('inline-field-wide-label')
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'border_color',
                                                        'value' => 'border-custom-color',
                                                    ),
                                                )
                                            ),

                                        Field::make('select', 'border_width', 'Border Width')->set_classes('inline-field-wide-label')
                                            ->set_options(array(
                                                'default' => 'Default[1px]',
                                                'custom' => 'Custom'
                                            ))
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'border_style',
                                                        'value' => 'border-custom',
                                                    ),
                                                )
                                            ),
                                        Field::make('text', 'border_width_top', 'Top Border Width')->set_classes('inline-field-wide-label')->set_default_value(0)
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'border_width',
                                                        'value' => 'custom',
                                                    ),
                                                )
                                            ),
                                        Field::make('text', 'border_width_right', 'Right Border Width')->set_classes('inline-field-wide-label')->set_default_value(0)
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'border_width',
                                                        'value' => 'custom',
                                                    ),
                                                )
                                            ),
                                        Field::make('text', 'border_width_bottom', 'Bottom Border Width')->set_classes('inline-field-wide-label')->set_default_value(0)
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'border_width',
                                                        'value' => 'custom',
                                                    ),
                                                )
                                            ),
                                        Field::make('text', 'border_width_left', 'Left Border Width')->set_classes('inline-field-wide-label')->set_default_value(0)
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'border_width',
                                                        'value' => 'custom',
                                                    ),
                                                )
                                            ),
                                    )
                                )
                                ->add_fields(
                                    'custom_class',
                                    array(
                                        Field::make('text', 'custom_class', 'Custom Class')
                                    )
                                )
                                ->set_layout('tabbed-vertical'),
                            Field::make('html', 'post_elements_html')->set_html('<label>Post Elements</label>')->set_classes('cb-label'),
                            Field::make('complex', 'post_elements', 'Post Elements')
                                ->set_duplicate_groups_allowed(false)
                                ->add_fields(
                                    'post_title',
                                    array(
                                        Field::make('text', 'text_before', __('Text Before')),
                                        Field::make('text', 'text_after', __('Text After')),
                                        Field::make('select', 'tag', __('Post Title Tag'))
                                            ->set_options(
                                                array(
                                                    '' => 'Default',
                                                    'h2' => 'h2',
                                                    'h3' => 'h3',
                                                    'h4' => 'h4',
                                                    'h5' => 'h5',
                                                    'h6' => 'h6',
                                                    'p' => 'p',
                                                )
                                            ),
                                        Field::make('select', 'text_color', 'Text Color')
                                            ->set_options(
                                                array(
                                                    ''   => 'Default',
                                                    'text-primary'   => 'Primary',
                                                    'text-secondary' => 'Secondary',
                                                    'text-accent'    => 'Accent',
                                                    'text-white'     => 'White',
                                                    'text-light-gray'     => 'Light Gray',
                                                    'text-body-color'     => 'Body',
                                                    'text-custom'    => 'Custom',
                                                )
                                            ),
                                        Field::make('color', 'text_color_custom', __('Text Color'))
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'text_color',
                                                        'value' => 'text-custom',
                                                    )
                                                )
                                            ),
                                    )
                                )
                                ->add_fields(
                                    'featured_image',
                                    array(
                                        Field::make('select', 'size', __('Size'))
                                            ->set_options(
                                                array(
                                                    ''          => 'Default',
                                                    'full'      => 'Full',
                                                    'large'      => 'Large',
                                                    'medium' => 'Medium',
                                                    'thumbnail'    => 'Thumbnail',
                                                )
                                            ),
                                        Field::make('checkbox', 'is_background_image', __('Is Background Image')),
                                        Field::make('checkbox', 'rounded_corners', __('Rounder Corners')),
                                        Field::make('text', 'border_radius', __('Border Radius'))->set_help_text('Custom border radius')
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'rounded_corners',
                                                        'value' => true,
                                                    )
                                                )
                                            ),
                                    )
                                )
                                ->add_fields(
                                    'post_excerpt',
                                    array(
                                        Field::make('text', 'excerpt_length', 'Custom Excerpt Length')
                                    )
                                )
                                ->add_fields(
                                    'permalink',
                                    array(
                                        Field::make('checkbox', 'hide_button_on_mobile', 'Hide Button on Mobile'),
                                        Field::make('text', 'button_text', 'Button Text'),
                                        Field::make('select', 'button_style', __('Button Style'))->set_width(20)
                                            ->set_options(
                                                array(
                                                    'button-accent'      => 'Accent',
                                                    'button-primary'      => 'Primary',
                                                    'button-secondary' => 'Secondary',
                                                    'button-white' => 'White',
                                                    'button-bordered'    => 'Bordered',
                                                )
                                            ),
                                    )
                                )
                                ->add_fields('icon', array(
                                    Field::make('file', 'icon', __('Icon'))
                                        ->set_type(array('image/svg+xml')),
                                    Field::make('select', 'icon_color', 'Text Color')
                                        ->set_options(
                                            array(
                                                ''   => 'Default',
                                                'text-primary'   => 'Primary',
                                                'text-secondary' => 'Secondary',
                                                'text-accent'    => 'Accent',
                                                'text-white'     => 'White',
                                                'text-light-gray'     => 'Light Gray',
                                                'text-custom'    => 'Custom',
                                            )
                                        ),
                                    Field::make('color', 'icon_color_custom', __('Text Color'))
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field' => 'icon_color',
                                                    'value' => 'text-custom',
                                                )
                                            )
                                        ),
                                    Field::make('text', 'icon_width', __('Custom Icon Width')),
                                    Field::make('text', 'icon_height', __('Custom Icon Height'))

                                ))
                                ->add_fields(
                                    'custom_field_1',
                                    array(
                                        Field::make('text', 'custom_field_key', __('Custom Field Key')),
                                        Field::make('select', 'custom_field_type', __('Custom Field Type'))
                                            ->set_options(
                                                array(
                                                    'p'      => 'p',
                                                    'h2'      => 'h2',
                                                    'h3'      => 'h3',
                                                    'h4'      => 'h4',
                                                    'h5'      => 'h5',
                                                    'h6'      => 'h6',
                                                    'img'      => 'img',
                                                )
                                            ),
                                        Field::make('text', 'custom_field_class', __('Wrapper Class')),
                                    )
                                )
                                ->set_header_template('Custom Field: <%- custom_field_key  %>')
                                ->add_fields(
                                    'custom_field_2',
                                    array(
                                        Field::make('text', 'custom_field_key', __('Custom Field Key')),
                                        Field::make('select', 'custom_field_type', __('Custom Field Type'))
                                            ->set_options(
                                                array(
                                                    'p'      => 'p',
                                                    'h2'      => 'h2',
                                                    'h3'      => 'h3',
                                                    'h4'      => 'h4',
                                                    'h5'      => 'h5',
                                                    'h6'      => 'h6',
                                                    'img'      => 'img',
                                                )
                                            ),
                                        Field::make('text', 'custom_field_class', __('Wrapper Class')),
                                    )
                                )
                                ->set_header_template('Custom Field: <%- custom_field_key  %>')
                                ->add_fields(
                                    'custom_field_3',
                                    array(
                                        Field::make('text', 'custom_field_key', __('Custom Field Key')),
                                        Field::make('select', 'custom_field_type', __('Custom Field Type'))
                                            ->set_options(
                                                array(
                                                    'p'      => 'p',
                                                    'h2'      => 'h2',
                                                    'h3'      => 'h3',
                                                    'h4'      => 'h4',
                                                    'h5'      => 'h5',
                                                    'h6'      => 'h6',
                                                    'img'      => 'img',
                                                )
                                            ),
                                        Field::make('text', 'custom_field_class', __('Wrapper Class')),
                                    )
                                )
                                ->set_header_template('Custom Field: <%- custom_field_key  %>')
                                ->add_fields(
                                    'custom_field_4',
                                    array(
                                        Field::make('text', 'custom_field_key', __('Custom Field Key')),
                                        Field::make('select', 'custom_field_type', __('Custom Field Type'))
                                            ->set_options(
                                                array(
                                                    'p'      => 'p',
                                                    'h2'      => 'h2',
                                                    'h3'      => 'h3',
                                                    'h4'      => 'h4',
                                                    'h5'      => 'h5',
                                                    'h6'      => 'h6',
                                                    'img'      => 'img',
                                                )
                                            ),
                                        Field::make('text', 'custom_field_class', __('Wrapper Class')),
                                    )
                                )
                                ->set_header_template('Custom Field: <%- custom_field_key  %>')
                                ->add_fields(
                                    'custom_field_5',
                                    array(
                                        Field::make('text', 'custom_field_key', __('Custom Field Key')),
                                        Field::make('select', 'custom_field_type', __('Custom Field Type'))
                                            ->set_options(
                                                array(
                                                    'p'      => 'p',
                                                    'h2'      => 'h2',
                                                    'h3'      => 'h3',
                                                    'h4'      => 'h4',
                                                    'h5'      => 'h5',
                                                    'h6'      => 'h6',
                                                    'img'      => 'img',
                                                )
                                            ),
                                        Field::make('text', 'custom_field_class', __('Wrapper Class')),
                                    )
                                )
                                ->set_header_template('Custom Field: <%- custom_field_key  %>')
                                ->set_layout('tabbed-vertical'),
                            Field::make('html', 'post_type_html')->set_html('<label>Post Type</label>')->set_classes('cb-label'),
                            Field::make('complex', 'post_type', 'Post Type')
                                ->set_duplicate_groups_allowed(false)
                                ->set_max(1)
                                ->add_fields(
                                    'solutions',
                                    array(
                                        Field::make('select', 'source', __('Source'))
                                            ->set_options(
                                                array(
                                                    'all'      => 'Select All',
                                                    'manually'      => 'Select Manually',
                                                )
                                            ),

                                        Field::make('association', 'post', 'Select Solutions')
                                            ->set_types(
                                                array(
                                                    array(
                                                        'type'      => 'post',
                                                        'post_type' => 'solutions',
                                                    )
                                                )
                                            )
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'source',
                                                        'value' => 'manually',
                                                    )
                                                )
                                            ),

                                    )
                                )
                                ->add_fields(
                                    'casestudies',
                                    array(
                                        Field::make('hidden', 'taxonomy_key', '')->set_default_value('casestudies_category'),
                                        Field::make('select', 'source', __('Source'))
                                            ->set_options(
                                                array(
                                                    'all'      => 'Select All',
                                                    'manually'      => 'Select Manually',
                                                    'category'      => 'Select by Category',
                                                )
                                            ),

                                        Field::make('association', 'post', 'Select Solutions')
                                            ->set_types(
                                                array(
                                                    array(
                                                        'type'      => 'post',
                                                        'post_type' => 'casestudies',
                                                    )
                                                )
                                            )
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'source',
                                                        'value' => 'manually',
                                                    )
                                                )
                                            ),
                                        Field::make('association', 'category', 'Select Product Categories')
                                            ->set_types(
                                                array(
                                                    array(
                                                        'type'      => 'term',
                                                        'taxonomy' => 'casestudies_category',
                                                    )
                                                )
                                            )
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'source',
                                                        'value' => 'category',
                                                        'comapre' => '='
                                                    )
                                                )
                                            ),

                                    )
                                )
                                ->add_fields(
                                    'testimonials',
                                    array(
                                        Field::make('hidden', 'taxonomy_key', '')->set_default_value('testimonial_category'),
                                        Field::make('select', 'source', __('Source'))
                                            ->set_options(
                                                array(
                                                    'all'      => 'Select All',
                                                    'manually'      => 'Select Manually',
                                                    'category'      => 'Select by Category',
                                                )
                                            ),

                                        Field::make('association', 'post', 'Select Testimonials')
                                            ->set_types(
                                                array(
                                                    array(
                                                        'type'      => 'post',
                                                        'post_type' => 'testimonials',
                                                    )
                                                )
                                            )
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'source',
                                                        'value' => 'manually',
                                                    )
                                                )
                                            ),
                                        Field::make('association', 'category', 'Select Categories')
                                            ->set_types(
                                                array(
                                                    array(
                                                        'type'      => 'term',
                                                        'taxonomy' => 'testimonial_category',
                                                    )
                                                )
                                            )
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'source',
                                                        'value' => 'category',
                                                        'comapre' => '='
                                                    )
                                                )
                                            ),

                                    )
                                )
                                ->add_fields(
                                    'capabilities',
                                    array(
                                        Field::make('select', 'source', __('Source'))
                                            ->set_options(
                                                array(
                                                    'all'      => 'Select All',
                                                    'manually'      => 'Select Manually',
                                                )
                                            ),

                                        Field::make('association', 'post', 'Select capabilities')
                                            ->set_types(
                                                array(
                                                    array(
                                                        'type'      => 'post',
                                                        'post_type' => 'capabilities',
                                                    )
                                                )
                                            )
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'source',
                                                        'value' => 'manually',
                                                    )
                                                )
                                            ),


                                    )
                                )
                                ->set_layout('tabbed-vertical')

                        ),
                    )
                    ->add_fields(
                        'buttons',
                        array(
                            Field::make('select', 'buttons_alignment', 'Buttons Alignment')
                                ->set_options(
                                    array(
                                        ''                => 'Default',
                                        'text-start'                => 'Left',
                                        'text-center'                => 'Center',
                                        'text-end'                => 'Right',
                                    )
                                ),
                            Field::make('complex', 'buttons', __('Buttons'))
                                ->set_classes('columns')
                                ->setup_labels(
                                    array(
                                        'plural_name'   => 'Buttons',
                                        'singular_name' => 'Button',
                                    )
                                )
                                ->add_fields(array(
                                    Field::make('select', 'button_type', __('Button Type'))->set_classes('trigger-selector inline-field')
                                        ->set_options(
                                            array(
                                                ''          => 'Select Button Type',
                                                'page'      => 'Page',
                                                'product'      => 'Product',
                                                'guides'      => 'Guides',
                                                'casestudies'      => 'Case Studies',
                                                'post'      => 'Post',
                                                'solutions' => 'Solution',
                                                'popups'    => 'Popup',
                                                'custom'     => 'Custom',
                                            )
                                        ),
                                    Field::make('text', 'button_text', __('Button Text'))->set_classes('inline-field'),
                                    Field::make('text', 'button_url', __('Button URL'))->set_classes('field-url')
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field'   => 'button_type',
                                                    'value'   => 'custom',
                                                    'compare' => '!='
                                                )
                                            )
                                        ),
                                    Field::make('html', 'html')
                                        ->set_html('<div class="page-selector">  </div>'),
                                    Field::make('text', 'button_url_custom', __('Button URL'))->set_classes('inline-field')
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field' => 'button_type',
                                                    'value' => 'custom',
                                                )
                                            )
                                        ),
                                    Field::make('select', 'button_style', __('Button Style'))->set_classes('inline-field')
                                        ->set_options(
                                            array(
                                                'button-accent'      => 'Accent',
                                                'button-primary'      => 'Primary',
                                                'button-secondary' => 'Secondary',
                                                'button-white' => 'White',
                                                'button-bordered'    => 'Bordered',
                                            )
                                        ),
                                    Field::make('select', 'button_target', __('Button Target'))->set_classes('inline-field')
                                        ->set_options(
                                            array(
                                                'target="_self"'      => 'Default',
                                                'target="_blank"'      => 'New Tab',
                                            )
                                        ),
                                ))
                                ->set_header_template('Button: <%- button_text %>'),
                        )
                    )
                    ->add_fields('product_compare',  array(
                        Field::make('association', 'compareproducts', 'Select Product Compare')
                            ->set_types(
                                array(
                                    array(
                                        'type'      => 'post',
                                        'post_type' => 'compareproducts',
                                    )
                                )
                            )
                            ->set_max(1)
                    ))
                    ->add_fields('shortcode',  array(
                        Field::make('text', 'shortcode', 'Shortcode')
                    ))
                    ->add_fields('product_slider',  array(
                        Field::make('text', 'heading', 'Heading')->set_classes('inline-field inline-field-wide-label'),
                        Field::make('text', 'button_text', 'Button Text')->set_classes('inline-field inline-field-wide-label'),
                        Field::make('text', 'button_url', 'Button URL')->set_classes('inline-field inline-field-wide-label'),
                        Field::make('text', 'numberposts', 'Number of Posts')->set_classes('inline-field inline-field-wide-label')->set_help_text('Leave empty to display all'),
                        Field::make('select', 'source_type', __('Source'))->set_classes('inline-field inline-field-wide-label')
                            ->set_options(
                                array(
                                    'category'      => 'Select by Category',
                                    'manually'      => 'Select Manually',
                                    'main_query'      => 'Main Query(works only for product taxonomy pages)',
                                )
                            ),
                        Field::make('association', 'source', __('Category'))->set_classes('inline-field inline-field-wide-label')
                            ->set_types(array(
                                array(
                                    'type'      => 'term',
                                    'taxonomy' => 'product_cat',
                                ),
                            ))
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'source_type',
                                        'value' => 'category',
                                    )
                                )
                            ),
                        Field::make('association', 'brand', __('Brands'))->set_classes('inline-field inline-field-wide-label')
                            ->set_types(array(
                                array(
                                    'type'      => 'term',
                                    'taxonomy' => 'pa_brands',
                                ),
                            ))
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'source_type',
                                        'value' => 'category',
                                    )
                                )
                            ),
                        Field::make('association', 'products', __('Select Products'))->set_classes('inline-field inline-field-wide-label')
                            ->set_types(array(
                                array(
                                    'type'      => 'post',
                                    'post_type' => 'product',
                                ),
                            ))
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'source_type',
                                        'value' => 'manually',
                                    )
                                )
                            ),
                    ))
                    ->add_fields('case_study_slider',  array(
                        Field::make('html', 'html')->set_html('<h3>This will display featured case study slider </h3>'),
                    ))

                    ->add_fields('tabs',  array(
                        Field::make('complex', 'tabs', 'Tabs')
                            ->add_fields('tabs',  array(
                                Field::make('text', 'heading', 'Heading'),
                                Field::make('textarea', 'description', 'Description')->set_width(80),
                                Field::make('html', 'activate_wysiwyg')->set_width(20)
                                    ->set_html('<a class="button button-primary button-large wysiwyg-editor-trigger" >Wysiwyg Editor</a>'),
                            ))
                            ->set_layout('tabbed-vertical')
                            ->set_header_template('Tab: <%- heading %>')

                    ))
                    ->add_fields('events_widget',  array(
                        Field::make('complex', 'events_widget', 'Events Widget')
                            ->add_fields('countdown',  array(
                                Field::make('html', 'html')->set_html('<h3>This will display events countdown timer. </h3>'),
                            ))
                            ->set_layout('tabbed-vertical')
                    )),
                Field::make('html', 'html_3')->set_html('<label>Section Styles</label>')->set_classes('cb-label'),
                Field::make('complex', 'section_styles', __(''))
                    ->setup_labels(
                        array(
                            'plural_name'   => 'Styles',
                            'singular_name' => 'Style',
                        )
                    )
                    ->set_duplicate_groups_allowed(false)
                    ->add_fields(
                        'background_color',
                        array(
                            Field::make('select', 'background_color', 'Section Background Color')
                                ->set_options(
                                    array(
                                        ''   => 'None',
                                        'bg-primary'   => 'Primary',
                                        'bg-secondary' => 'Secondary',
                                        'bg-accent'    => 'Accent',
                                        'bg-white'     => 'White',
                                        'bg-light'     => 'Light',
                                        'bg-light-gray'     => 'Light Gray',
                                        'bg-custom'    => 'Custom',
                                    )
                                ),
                            Field::make('color', 'background_color_custom', __('Section Background Color'))
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'background_color',
                                            'value' => 'bg-custom',
                                        )
                                    )
                                ),

                            Field::make('select', 'background_color_container', 'Container Background Color')
                                ->set_options(
                                    array(
                                        ''   => 'None',
                                        'bg-primary'   => 'Primary',
                                        'bg-secondary' => 'Secondary',
                                        'bg-accent'    => 'Accent',
                                        'bg-white'     => 'White',
                                        'bg-light'     => 'Light',
                                        'bg-light-gray'     => 'Light Gray',
                                    )
                                ),
                        )
                    )
                    ->add_fields(
                        'background_image',
                        array(
                            Field::make('image', 'background_image', 'Background Image'),
                            Field::make('select', 'background_size', 'Background Size')
                                ->set_options(
                                    array(
                                        'bg-cover' => 'Cover',
                                        'bg-contain'  => 'Contain',
                                    )
                                ),
                            Field::make('select', 'background_attachment', 'Background Attachment')
                                ->set_options(
                                    array(
                                        'bg-scroll'    => 'Scroll',
                                        'bg-fixed'  => 'Fixed',
                                    )
                                ),
                            Field::make('select', 'background_repeat', 'Background Repeat')
                                ->set_options(
                                    array(
                                        'bg-no-repeat'    => 'No Repeat',
                                        'bg-repeat'  => 'No Repeat',
                                    )
                                ),
                        )
                    )
                    ->add_fields(
                        'background_video',
                        array(
                            Field::make('checkbox', 'is_container_background', __('Is Container Background'))->set_classes('inline-field'),
                            Field::make('select', 'background_type', __('Background Type'))->set_classes('inline-field')
                                ->set_options(
                                    array(
                                        'self-hosted' => 'Self Hosted',
                                        'youtube' => 'Youtube',
                                    )
                                ),
                            Field::make('file', 'background', __('Background'))->set_classes('inline-field')->set_type(array('video', 'image'))
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'background_type',
                                            'value' => 'self-hosted',
                                        )
                                    )
                                ),
                            Field::make('text', 'background_youtube', __('Background Youtube ID'))->set_classes('inline-field')
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'background_type',
                                            'value' => 'youtube',
                                        )
                                    )
                                ),
                        )
                    )
                    ->add_fields(
                        'background_overlay',
                        array(
                            Field::make('select', 'background_overlay_type', 'Background Overlay Type')
                                ->set_options(
                                    array(
                                        'default'    => 'Default',
                                        'background-overlay-2'    => 'Background Overlay 2',
                                        'image'  => 'Image',
                                        'custom'  => 'Custom',
                                    )
                                ),
                            Field::make('image', 'background_overlay_image', 'Image Background Overlay')
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'background_overlay_type',
                                            'value' => 'image',
                                        )
                                    )
                                ),
                            Field::make('text', 'background_overlay_image_opacity', 'Image Background Overlay Opacity')
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'background_overlay_type',
                                            'value' => 'image',
                                        )
                                    )
                                ),
                            Field::make('color', 'background_overlay_custom', 'Custom Background Overlay')
                                ->set_alpha_enabled(true)
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'background_overlay_type',
                                            'value' => 'custom',
                                        )
                                    )
                                ),
                        )
                    )
                    ->add_fields(
                        'background_gradient',
                        array(
                            Field::make('select', 'background_gradient', 'Background Gradient')->set_classes('inline-field inline-field-wide-label')
                                ->set_options(
                                    array(
                                        'background-gradient-default'    => 'Default',
                                        'custom'  => 'Custom',
                                    )
                                ),

                            Field::make('select', 'background_gradient_type', 'Background Gradient Type')->set_classes('inline-field inline-field-wide-label')
                                ->set_options(
                                    array(
                                        'linear-gradient'    => 'Linear Gradient',
                                        'radial-gradient'  => 'Radial Gradient',
                                    )
                                )
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'background_gradient',
                                            'value' => 'custom',
                                        )
                                    )
                                ),
                            Field::make('text', 'background_gradient_direction', 'Background Gradient Direction')->set_width(100)->set_classes('inline-field inline-field-wide-label')
                                ->set_default_value('180')
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'background_gradient_type',
                                            'value' => 'linear-gradient',
                                        ),
                                        array(
                                            'field' => 'background_gradient',
                                            'value' => 'custom',
                                        )
                                    )
                                ),

                            Field::make('color', 'background_gradient_color_1', 'Background Gradient Color[1]')->set_width(50)
                                ->set_alpha_enabled(true)
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'background_gradient',
                                            'value' => 'custom',
                                        )
                                    )
                                ),
                            Field::make('text', 'background_gradient_stop_1', 'Background Gradient Stop[1]')->set_width(50)
                                ->set_default_value('0%')
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'background_gradient',
                                            'value' => 'custom',
                                        )
                                    )
                                ),
                            Field::make('color', 'background_gradient_color_2', 'Background Gradient Color[2]')->set_width(50)
                                ->set_alpha_enabled(true)
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'background_gradient',
                                            'value' => 'custom',
                                        )
                                    )
                                ),
                            Field::make('text', 'background_gradient_stop_2', 'Background Gradient Stop[2]')->set_width(50)
                                ->set_default_value('100%')
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'background_gradient',
                                            'value' => 'custom',
                                        )
                                    )
                                ),

                        )
                    )
                    ->add_fields(
                        'text_color',
                        array(
                            Field::make('select', 'text_color', 'Text Color')
                                ->set_options(
                                    array(
                                        'text-primary'   => 'Primary',
                                        'text-secondary' => 'Secondary',
                                        'text-accent'    => 'Accent',
                                        'text-white'     => 'White',
                                        'text-light-gray'     => 'Light Gray',
                                        'text-custom'    => 'Custom',
                                    )
                                ),
                            Field::make('color', 'text_color_custom', __('Text Color'))
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'text_color',
                                            'value' => 'text-custom',
                                        )
                                    )
                                ),
                        )
                    )
                    ->add_fields(
                        'padding',
                        array(
                            Field::make('html', 'html_1')->set_html('<label>Section Padding</label>')->set_classes('cb-label'),
                            Field::make('select', 'padding_top', 'Padding Top')->set_width(25)
                                ->set_options(
                                    array(
                                        ''                => 'No Padding',
                                        'xl-padding-top'  => 'Extra Large',
                                        'lg-padding-top'  => 'Large',
                                        'md-padding-top'  => 'Medium',
                                        'sm-padding-top'  => 'Small',
                                        'xs-padding-top' => 'Extra Small',
                                        'pt-20px' => '20px',
                                    )
                                ),
                            Field::make('select', 'padding_bottom', 'Padding Bottom')->set_width(25)
                                ->set_options(
                                    array(
                                        ''                   => 'No Padding',
                                        'xl-padding-bottom'  => 'Extra Large',
                                        'lg-padding-bottom'  => 'Large',
                                        'md-padding-bottom'  => 'Medium',
                                        'sm-padding-bottom'  => 'Small',
                                        'xs-padding-bottom' => 'Extra Small',
                                        'pb-20px' => '20px',
                                    )
                                ),
                            Field::make('select', 'padding_left', 'Padding left')->set_width(25)
                                ->set_options(
                                    array(
                                        ''                 => 'No Padding',
                                        'xl-padding-left'  => 'Extra Large',
                                        'lg-padding-left'  => 'Large',
                                        'md-padding-left'  => 'Medium',
                                        'sm-padding-left'  => 'Small',
                                        'xs-padding-left' => 'Extra Small',
                                    )
                                ),
                            Field::make('select', 'padding_right', 'Padding right')->set_width(25)
                                ->set_options(
                                    array(
                                        ''                  => 'No Padding',
                                        'xl-padding-right'  => 'Extra Large',
                                        'lg-padding-right'  => 'Large',
                                        'md-padding-right'  => 'Medium',
                                        'sm-padding-right'  => 'Small',
                                        'xs-padding-right' => 'Extra Small',
                                    )
                                ),
                            Field::make('html', 'html_2')->set_html('<label>Container Padding</label>')->set_classes('cb-label'),
                            Field::make('select', 'container_padding_top', 'Padding Top')->set_width(25)
                                ->set_options(
                                    array(
                                        ''                => 'Default',
                                        'xl-padding-top'  => 'Extra Large',
                                        'lg-padding-top'  => 'Large',
                                        'md-padding-top'  => 'Medium',
                                        'sm-padding-top'  => 'Small',
                                        'xs-padding-top' => 'Extra Small',
                                    )
                                ),
                            Field::make('select', 'container_padding_bottom', 'Padding Bottom')->set_width(25)
                                ->set_options(
                                    array(
                                        ''                   => 'Default',
                                        'xl-padding-bottom'  => 'Extra Large',
                                        'lg-padding-bottom'  => 'Large',
                                        'md-padding-bottom'  => 'Medium',
                                        'sm-padding-bottom'  => 'Small',
                                        'xs-padding-bottom' => 'Extra Small',
                                    )
                                ),
                            Field::make('select', 'container_padding_left', 'Padding left')->set_width(25)
                                ->set_options(
                                    array(
                                        ''                 => 'Default',
                                        'xl-padding-left'  => 'Extra Large',
                                        'lg-padding-left'  => 'Large',
                                        'md-padding-left'  => 'Medium',
                                        'sm-padding-left'  => 'Small',
                                        'xs-padding-left' => 'Extra Small',
                                        'ps-0' => 'None',
                                    )
                                ),
                            Field::make('select', 'container_padding_right', 'Padding right')->set_width(25)
                                ->set_options(
                                    array(
                                        ''                  => 'Default',
                                        'xl-padding-right'  => 'Extra Large',
                                        'lg-padding-right'  => 'Large',
                                        'md-padding-right'  => 'Medium',
                                        'sm-padding-right'  => 'Small',
                                        'xs-padding-right' => 'Extra Small',
                                        'pe-0' => 'None',
                                    )
                                ),

                        )
                    )
                    ->add_fields(
                        'margin',
                        array(
                            Field::make('select', 'margin_top', 'Margin Top')->set_width(25)
                                ->set_options(
                                    array(
                                        ''               => 'No margin',
                                        'xl-margin-top'  => 'Extra Large',
                                        'lg-margin-top'  => 'Large',
                                        'md-margin-top'  => 'Medium',
                                        'sm-margin-top'  => 'Small',
                                        'xs-margin-top' => 'Extra Small',
                                        'mt-20px' => '20px',
                                    )
                                ),
                            Field::make('select', 'margin_bottom', 'Margin Bottom')->set_width(25)
                                ->set_options(
                                    array(
                                        ''                  => 'No margin',
                                        'xl-margin-bottom'  => 'Extra Large',
                                        'lg-margin-bottom'  => 'Large',
                                        'md-margin-bottom'  => 'Medium',
                                        'sm-margin-bottom'  => 'Small',
                                        'xs-margin-bottom' => 'Extra Small',
                                        'mb-20px' => '20px',
                                    )
                                ),
                            Field::make('select', 'margin_left', 'Margin Left')->set_width(25)
                                ->set_options(
                                    array(
                                        ''                => 'No margin',
                                        'xl-margin-left'  => 'Extra Large',
                                        'lg-margin-left'  => 'Large',
                                        'md-margin-left'  => 'Medium',
                                        'sm-margin-left'  => 'Small',
                                        'xs-margin-left' => 'Extra Small',
                                        'ms-20px' => '20px',
                                    )
                                ),
                            Field::make('select', 'margin_right', 'Margin Right')->set_width(25)
                                ->set_options(
                                    array(
                                        ''                 => 'No margin',
                                        'xl-margin-right'  => 'Extra Large',
                                        'lg-margin-right'  => 'Large',
                                        'md-margin-right'  => 'Medium',
                                        'sm-margin-right'  => 'Small',
                                        'xs-margin-right' => 'Extra Small',
                                        'me-20px' => '20px',
                                    )
                                ),

                        )
                    )
                    ->add_fields(
                        'alignment',
                        array(
                            Field::make('select', 'align_items', 'Align Items')
                                ->set_options(
                                    array(
                                        ''               => 'Default',
                                        'align-items-start'  => 'Start',
                                        'align-items-center'  => 'Center',
                                        'align-items-end'  => 'End',
                                    )
                                ),
                            Field::make('select', 'justify_content', 'Justify Content')
                                ->set_options(
                                    array(
                                        ''                  => 'Default',
                                        'justify-content-start'  => 'Start',
                                        'justify-content-center'  => 'Center',
                                        'justify-content-end'  => 'End',
                                        'justify-content-between'  => 'Between',
                                    )
                                ),
                            Field::make('select', 'text_align', 'Text Align')
                                ->set_options(
                                    array(
                                        ''                => 'Default',
                                        'text-start'                => 'Left',
                                        'text-center'                => 'Center',
                                        'text-end'                => 'Right',
                                    )
                                ),
                        )
                    )
                    ->add_fields(
                        'container_width',
                        array(
                            Field::make('select', 'container_width', 'Container Width')
                                ->set_options(
                                    array(
                                        ''               => 'Default',
                                        'large-container'  => 'Large',
                                        'medium-container'  => 'Medium',
                                        'small-container'  => 'Small',
                                        'full-width'  => 'Full',
                                        'extend-right'  => 'Padding Left',
                                        'extend-left'  => 'Padding Right',
                                        'custom-container'  => 'Custom',
                                    )
                                ),
                            Field::make('text', 'custom_container_width', 'Custom Container Width')
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'container_width',
                                            'value' => 'custom-container',
                                        )
                                    )
                                ),
                        )
                    )
                    ->add_fields(
                        'height',
                        array(
                            Field::make('text', 'height', 'Height')
                        )
                    )
                    ->add_fields(
                        'border',
                        array(
                            Field::make('html', 'html_1')->set_html('<label>Section Border</label>')->set_classes('cb-label'),
                            Field::make('select', 'border_radius', 'Border Radius')->set_classes('inline-field inline-field-wide-label')
                                ->set_options(
                                    array(
                                        ''   => 'None',
                                        'rounded-corner'   => 'Default[10px]',
                                        'custom'   => 'Custom',
                                    )
                                ),
                            Field::make('text', 'border_radius_custom', 'Custom Border Radius')->set_classes('inline-field inline-field-wide-label')
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'border_radius',
                                            'value' => 'custom',
                                            'compare' => '='
                                        )
                                    )
                                ),
                            Field::make('select', 'border_style', 'Border Style')->set_classes('inline-field inline-field-wide-label')
                                ->set_options(
                                    array(
                                        ''   => 'None',
                                        'border-default'   => 'Default',
                                        'border-custom'   => 'Custom',
                                    )
                                ),

                            Field::make('select', 'border_color', 'Border Color')->set_classes('inline-field inline-field-wide-label')
                                ->set_options(
                                    array(
                                        'border-default'   => 'Default',
                                        'border-primary'   => 'Primary',
                                        'border-secondary' => 'Secondary',
                                        'border-accent'    => 'Accent',
                                        'border-white'     => 'White',
                                        'border-light-gray'     => 'Light Gray',
                                        'border-custom-color'    => 'Custom',
                                    )
                                )
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'border_style',
                                            'value' => 'border-custom',
                                        ),
                                    )
                                ),
                            Field::make('color', 'border_color_custom', __('Border Color'))->set_classes('inline-field inline-field-wide-label')
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'border_color',
                                            'value' => 'border-custom-color',
                                        ),
                                    )
                                ),

                            Field::make('select', 'border_width', 'Border Width')->set_classes('inline-field inline-field-wide-label')
                                ->set_options(array(
                                    'default' => 'Default[1px]',
                                    'custom' => 'Custom'
                                ))
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'border_style',
                                            'value' => 'border-custom',
                                        ),
                                    )
                                ),
                            Field::make('text', 'border_width_top', 'Top Border Width')->set_classes('inline-field inline-field-wide-label')->set_default_value(0)
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'border_width',
                                            'value' => 'custom',
                                        ),
                                    )
                                ),
                            Field::make('text', 'border_width_right', 'Right Border Width')->set_classes('inline-field inline-field-wide-label')->set_default_value(0)
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'border_width',
                                            'value' => 'custom',
                                        ),
                                    )
                                ),
                            Field::make('text', 'border_width_bottom', 'Bottom Border Width')->set_classes('inline-field inline-field-wide-label')->set_default_value(0)
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'border_width',
                                            'value' => 'custom',
                                        ),
                                    )
                                ),
                            Field::make('text', 'border_width_left', 'Left Border Width')->set_classes('inline-field inline-field-wide-label')->set_default_value(0)
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'border_width',
                                            'value' => 'custom',
                                        ),
                                    )
                                ),

                            Field::make('html', 'html_2')->set_html('<label>Container Border</label>')->set_classes('cb-label'),
                            Field::make('select', 'container_border_radius', 'Border Radius')->set_classes('inline-field inline-field-wide-label')
                                ->set_options(
                                    array(
                                        ''   => 'None',
                                        'rounded-corner'   => 'Default[10px]',
                                        'custom'   => 'Custom',
                                    )
                                ),
                            Field::make('text', 'container_border_radius_custom', 'Custom Border Radius')->set_classes('inline-field inline-field-wide-label')
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'container_border_radius',
                                            'value' => 'custom',
                                            'compare' => '='
                                        )
                                    )
                                ),
                            Field::make('select', 'container_border_style', 'Border Style')->set_classes('inline-field inline-field-wide-label')
                                ->set_options(
                                    array(
                                        ''   => 'None',
                                        'border-default'   => 'Default',
                                        'border-custom'   => 'Custom',
                                    )
                                ),

                            Field::make('select', 'container_border_color', 'Border Color')->set_classes('inline-field inline-field-wide-label')
                                ->set_options(
                                    array(
                                        'border-default'   => 'Default',
                                        'border-primary'   => 'Primary',
                                        'border-secondary' => 'Secondary',
                                        'border-accent'    => 'Accent',
                                        'border-white'     => 'White',
                                        'border-light-gray'     => 'Light Gray',
                                        'border-custom-color'    => 'Custom',
                                    )
                                )
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'container_border_style',
                                            'value' => 'border-custom',
                                        ),
                                    )
                                ),
                            Field::make('color', 'container_border_color_custom', __('Border Color'))->set_classes('inline-field inline-field-wide-label')
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'container_border_color',
                                            'value' => 'border-custom-color',
                                        ),
                                    )
                                ),

                            Field::make('select', 'container_border_width', 'Border Width')->set_classes('inline-field inline-field-wide-label')
                                ->set_options(array(
                                    'default' => 'Default[1px]',
                                    'custom' => 'Custom'
                                ))
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'container_border_style',
                                            'value' => 'border-custom',
                                        ),
                                    )
                                ),
                            Field::make('text', 'container_border_width_top', 'Top Border Width')->set_classes('inline-field inline-field-wide-label')->set_default_value(0)
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'container_border_width',
                                            'value' => 'custom',
                                        ),
                                    )
                                ),
                            Field::make('text', 'container_border_width_right', 'Right Border Width')->set_classes('inline-field inline-field-wide-label')->set_default_value(0)
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'container_border_width',
                                            'value' => 'custom',
                                        ),
                                    )
                                ),
                            Field::make('text', 'container_border_width_bottom', 'Bottom Border Width')->set_classes('inline-field inline-field-wide-label')->set_default_value(0)
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'container_border_width',
                                            'value' => 'custom',
                                        ),
                                    )
                                ),
                            Field::make('text', 'container_border_width_left', 'Left Border Width')->set_classes('inline-field inline-field-wide-label')->set_default_value(0)
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'container_border_width',
                                            'value' => 'custom',
                                        ),
                                    )
                                ),

                        )
                    )



                    ->set_layout('tabbed-vertical')

            ))
            ->set_header_template('Section: <%- title %>')
    );
}
Container::make('post_meta', __('Sections'))
    ->where('post_template', '=', 'templates/page-modules.php')
    ->or_where('post_type', '=', 'product')
    ->or_where('post_type', '=', 'layouts')
    ->or_where('post_type', '=', 'capabilities')
    ->or_where('post_type', '=', 'casestudies')
    ->or_where('post_type', '=', 'producttaxonomypages')
    ->or_where('post_type', '=', 'solutions')
    ->or_where('post_type', '=', 'events')
    ->add_fields(__section_fields());



Container::make('post_meta', __('Sections after main'))
    ->or_where('post_type', '=', 'product')
    ->or_where('post_type', '=', 'capabilities')
    ->add_fields(__section_fields('sections_after_main'));

if (isset($_GET['post'])) {
    if (_is_module($_GET['post'])) {
        Container::make('post_meta', __('Preview'))
            ->add_fields(array(
                Field::make('html', 'preview')->set_html('<iframe src="' . get_permalink($_GET['post']) . '?prev=true"></iframe>')->set_classes('preview')
            ));
    }
}



/*-----------------------------------------------------------------------------------*/
/* Product Attributes
/*-----------------------------------------------------------------------------------*/
Container::make('term_meta', __('Category Properties'))
    ->where('term_taxonomy', '=', 'pa_brands')
    ->add_fields(
        array(
            Field::make('image', 'image', __('Logo')),
        )
    );


Container::make('term_meta', __('Category Properties'))
    ->where('term_taxonomy', '=', 'pa_specifications')
    ->add_fields(
        array(
            Field::make('image', 'icon', __('Icon')),
        )
    );

/*-----------------------------------------------------------------------------------*/
/* Products
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', __('Product Settings'))
    ->where('post_type', '=', 'product')
    ->add_fields(array(
        Field::make('association', 'related_guides', 'Related Guides')
            ->set_types(
                array(
                    array(
                        'type'      => 'post',
                        'post_type' => 'guides',
                    )
                )
            )
    ));




/*-----------------------------------------------------------------------------------*/
/* Products
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', __('Product Settings'))
    ->where('post_type', '=', 'compareproducts')
    ->add_fields(array(
        Field::make('association', 'products', 'Select products to compare')
            ->set_types(
                array(
                    array(
                        'type'      => 'post',
                        'post_type' => 'product',
                    )
                )
            )
    ));
/*-----------------------------------------------------------------------------------*/
/* Layouts
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', __('Conditional Display'))
    ->where('post_type', '=', 'layouts')
    ->set_context('side')
    ->add_fields(array(
        Field::make('select', 'display_location', __('Display Location'))
            ->add_options(array(
                'section' => __('Section'),
                'archive' => __('Archive'),
                'product_cat' => __('Product Category'),
                'after_header' => __('After Header'),
                'before_footer' => __('Before Footer'),
                '404' => __('404'),
            )),

        Field::make('select', 'display_location_archive', __('Select Archive'))
            ->add_options(array(
                '' => __('Select Archive'),
                'post' => __('Post'),
                'events' => __('Events'),
                'capabilities' => __('Capabilities'),
                'casestudies' => __('Case Studies'),
                'guides' => __('Guides'),
            ))
            ->set_conditional_logic(
                array(
                    array(
                        'field' => 'display_location',
                        'value' => 'archive',
                    )
                )
            ),
        Field::make('select', 'display_location_archive_position', __('Position'))
            ->add_options(array(
                '' => __('Select Position'),
                'above_loop' => __('Above Loop'),
                'below_loop' => __('Below Loop'),
            ))
            ->set_conditional_logic(
                array(
                    array(
                        'field' => 'display_location',
                        'value' => 'archive',
                    )
                )
            ),
    ));

/*-----------------------------------------------------------------------------------*/
/* Product category pages
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', __('Select taxonomy term to display content'))
    ->or_where('post_type', '=', 'producttaxonomypages')
    ->add_fields(array(
        Field::make('association', 'product_tax', 'Select Category')
            ->set_types(
                array(
                    array(
                        'type'      => 'term',
                        'taxonomy' => 'product_cat',
                    ),
                    array(
                        'type'      => 'term',
                        'taxonomy' => 'pa_brands',
                    )
                )
            )->set_max(1)
    ));



/* Landing page settings
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', __('Landing Page Settings'))
    ->where('post_template', '=', 'templates/page-landing.php')
    ->add_fields(array(
        Field::make('select', 'background_type', __('Background Type'))->set_classes('inline-field')
            ->set_options(
                array(
                    'self-hosted' => 'Self Hosted',
                    'youtube' => 'Youtube',
                )
            ),
        Field::make('file', 'background', __('Background'))->set_classes('inline-field')->set_type(array('video', 'image'))
            ->set_conditional_logic(
                array(
                    array(
                        'field' => 'background_type',
                        'value' => 'self-hosted',
                    )
                )
            ),
        Field::make('text', 'background_youtube', __('Background Youtube ID'))->set_classes('inline-field')
            ->set_conditional_logic(
                array(
                    array(
                        'field' => 'background_type',
                        'value' => 'youtube',
                    )
                )
            ),
        Field::make('association', 'wp_form', 'Select Form')->set_classes('inline-field')
            ->set_types(
                array(
                    array(
                        'type'      => 'post',
                        'post_type' => 'wpforms',
                    )
                )
            )
            ->set_max(1),
        Field::make('text', 'form_heading', 'Form Heading')->set_classes('inline-field'),
        Field::make('text', 'form_description', 'Form Description')->set_classes('inline-field'),
        Field::make('image', 'form_image', 'Form Image')->set_classes('inline-field'),
    ));


/*-----------------------------------------------------------------------------------*/
/* Events
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Event Settings')
    ->set_priority('high')
    ->or_where('post_type', '=', 'events')
    ->add_fields(
        array(
            Field::make('date', 'crb_event_start_date', __('Event Start Date'))
                ->set_storage_format('jS F Y'),
            Field::make('time', 'crb_event_start_time', 'Event Start Time')
                ->set_storage_format('g:i a'),

        )
    );


/*-----------------------------------------------------------------------------------*/
/* Events
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Case Study Settings')
    ->set_priority('high')
    ->or_where('post_type', '=', 'casestudies')
    ->add_fields(
        array(
            Field::make('complex', 'feature', __('Feature'))
                ->add_fields(array(
                    Field::make('text', 'feature_text', __('Feature Text'))
                ))
                ->set_layout('tabbed-vertical')
                ->set_header_template('Feature Text: <%- feature_text %>'),
            Field::make('image', 'logo', __('Logo'))

        )
    );



/*-----------------------------------------------------------------------------------*/
/* Testimonial
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Testimonial Content')
    ->where('post_type', '=', 'testimonials')
    ->add_fields(
        array(
            Field::make('text', 'testimonial_title', 'Testimonial Title'),
            Field::make('textarea', 'testimonial_content', 'Testimonial Content'),
        )
    );


/*-----------------------------------------------------------------------------------*/
/* Capibilities
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Capabilities Settings')
    ->where('post_type', '=', 'capabilities')
    ->add_tab(
        'Related Products',
        array(
            Field::make('text', 'related_products_heading', 'Related Products Heading'),
            Field::make('association', 'related_products', 'Related Products')
                ->set_types(
                    array(
                        array(
                            'type'      => 'post',
                            'post_type' => 'product',
                        )
                    )
                )
        )
    )
    ->add_tab(
        'Related Case Studies',
        array(
            Field::make('text', 'related_casestudies_heading', 'Related Case Studies Heading'),
            Field::make('association', 'related_casestudies', 'Related Case Studies')
                ->set_types(
                    array(
                        array(
                            'type'      => 'post',
                            'post_type' => 'casestudies',
                        )
                    )
                )
        )
    );


/*-----------------------------------------------------------------------------------*/
/* Industry
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Industry Settings')
    ->where('post_type', '=', 'solutions')
    ->add_tab(
        'General Settings',
        array(
            Field::make('checkbox', 'hide_on_list', 'Hide on List'),
        )
    )
    ->add_tab(
        'Related Guides',
        array(
            Field::make('text', 'related_guides_heading', 'Related Guides Heading'),
            Field::make('association', 'related_guides', 'Related Guides')
                ->set_types(
                    array(
                        array(
                            'type'      => 'post',
                            'post_type' => 'guides',
                        )
                    )
                )
        )
    )
    ->add_tab(
        'Related Case Studies',
        array(
            Field::make('text', 'related_casestudies_heading', 'Related Case Studies Heading'),
            Field::make('association', 'related_casestudies', 'Related Case Studies')
                ->set_types(
                    array(
                        array(
                            'type'      => 'post',
                            'post_type' => 'casestudies',
                        )
                    )
                )
        )
    );



/*-----------------------------------------------------------------------------------*/
/* Gudies
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Industry Settings')
    ->where('post_type', '=', 'guides')
    ->add_tab(
        'General Settings',
        array(
            Field::make('checkbox', 'hide_on_list', 'Hide on List'),
        )
    );

/*-----------------------------------------------------------------------------------*/
/* Popups
/*-----------------------------------------------------------------------------------*/

Container::make('post_meta', 'Popup Settings')
    ->set_priority('high')
    ->set_context('side')
    ->or_where('post_type', '=', 'popups')
    ->add_fields(
        array(
            Field::make('select', 'popup_layout', 'Popup Layout')
                ->set_options(
                    array(
                        ''                => 'Default',
                        'contact_form'     => 'Contact Form',
                    )
                ),
            Field::make('select', 'popup_max_width', 'Popup Max Width')
                ->set_options(
                    array(
                        'popup-default'    => 'Default',
                        'popup-small'     => 'Small',
                        'popup-medium'     => 'Medium',
                        'popup-large'     => 'Large',
                    )
                ),
            Field::make('select', 'background_color', 'Background Color')
                ->set_options(
                    array(
                        ''   => 'None',
                        'background-primary'   => 'Primary',
                        'background-secondary' => 'Secondary',
                        'background-accent'    => 'Accent',
                        'background-white'     => 'White',
                        'background-light-gray'     => 'Light Gray',
                        'background-body-color'     => 'Body',
                    )
                ),

        )
    );



/*-----------------------------------------------------------------------------------*/
/* Default Page
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Page Settings')
    ->where('post_type', '=', 'page.php')
    ->add_fields(array(
        Field::make('select', 'container_width', 'Container Width')
            ->set_options(
                array(
                    '' => 'Default',
                    'full-width'      => 'Full Width',
                    'large-container'      => 'Large Container',
                    'medium-container'      => 'Medium Container',
                    'small-container'      => 'Small Container',
                )
            ),
    ));
