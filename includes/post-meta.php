<?php

use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Carbon_Fields\Complex_Container;
use Carbon_Fields\Field;
/*-----------------------------------------------------------------------------------*/
/* Section
/*-----------------------------------------------------------------------------------*/

Container::make('post_meta', __('Sections'))
    ->where('post_template', '=', 'templates/page-modules.php')
    ->add_fields(array(
        Field::make('complex', 'sections', __(''))
            ->setup_labels(
                array(
                    'plural_name'   => 'Modules',
                    'singular_name' => 'Module',
                )
            )
            ->set_collapsed(false)
            ->add_fields(array(
                Field::make('text', 'title', __('Section Title'))->set_required(true)->set_width(33),
                Field::make('text', 'module_id', __('Section ID'))->set_width(33),
                Field::make('checkbox', 'disable_sction', __('Disable Section'))->set_width(33),
                Field::make('complex', 'section_items', __('Section Items'))
                    ->setup_labels(
                        array(
                            'plural_name'   => 'Section Items',
                            'singular_name' => 'Section Items',
                        )
                    )
                    ->add_fields('heading', array())
                    ->add_fields('columns', array(
                        Field::make('complex', 'columns', __('Columns'))
                            ->setup_labels(
                                array(
                                    'plural_name'   => 'Columns',
                                    'singular_name' => 'Column',
                                )
                            )
                            ->set_classes('columns')
                            ->add_fields(array(
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
                                        Field::make('text', 'heading', __('Heading')),
                                        Field::make('text', 'prefix', __('Prefix')),
                                        Field::make('text', 'suffix', __('Suffix')),
                                        Field::make('select', 'tag', __('Tag'))
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
                                            ->set_default_value('h2'),
                                        Field::make('select', 'size', __('Heading Size'))
                                            ->set_options(
                                                array(
                                                    '' => 'Default',
                                                    'big-heading' => 'Big Heading',
                                                    'medium-heading' => 'Medium Heading',
                                                    'small-heading' => 'Small Heading',
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
                                    ))
                                    ->add_fields('description',  array(
                                        Field::make('textarea', 'description', __('Description'))->set_classes('activate-tinymce'),
                                    ))
                                    ->add_fields('button', array(
                                        Field::make('select', 'button_type', __('Button Type'))->set_classes('trigger-selector')
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
                                        Field::make('text', 'button_text', __('Button Text')),
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
                                        Field::make('text', 'button_url_custom', __('Button URL'))
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'button_type',
                                                        'value' => 'custom',
                                                    )
                                                )
                                            ),
                                        Field::make('select', 'button_style', __('Button Style'))
                                            ->set_options(
                                                array(
                                                    'button-accent'      => 'Accent',
                                                    'button-primary'      => 'Primary',
                                                    'button-secondary' => 'Secondary',
                                                    'button-white' => 'White',
                                                    'button-bordered'    => 'Bordered',
                                                )
                                            ),

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
                                    ->add_fields('icon', array(
                                        Field::make('file', 'icon', __('Icon'))
                                            ->set_type(array('image/svg+xml')),
                                        Field::make('select', 'icon_color', 'Text Color')
                                            ->set_options(
                                                array(
                                                    'text-primary'   => 'Primary',
                                                    'text-secondary' => 'Secondary',
                                                    'text-accent'    => 'Accent',
                                                    'text-white'     => 'White',
                                                    'text-light-gray'     => 'Light Gray',
                                                    'text-body-color'     => 'Body',
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
                                        'custom_html',
                                        array(
                                            Field::make('textarea', 'custom_html', __('Custom HTML')),
                                        )
                                    )

                            )),
                    )),


                Field::make('complex', 'styles', __('Styles'))
                    ->set_duplicate_groups_allowed(false)
                    ->add_fields(
                        'background_color',
                        array(
                            Field::make('select', 'background_color', 'Background Color')
                                ->set_options(
                                    array(
                                        'background-primary'   => 'Primary',
                                        'background-secondary' => 'Secondary',
                                        'background-accent'    => 'Accent',
                                        'background-white'     => 'White',
                                        'background-light-gray'     => 'Light Gray',
                                        'background-body-color'     => 'Body',
                                        'background-custom'    => 'Custom',
                                    )
                                ),
                            Field::make('color', 'background_color_custom', __('Background Color'))
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'background_color',
                                            'value' => 'background-custom',
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
                                        'background-cover' => 'Cover',
                                        'background-contain'  => 'Contain',
                                    )
                                ),
                            Field::make('select', 'background_attachment', 'Background Attachment')
                                ->set_options(
                                    array(
                                        'background-scroll'    => 'Scroll',
                                        'background-fixed'  => 'Fixed',
                                    )
                                ),
                            Field::make('select', 'background_repeat', 'Background Repeat')
                                ->set_options(
                                    array(
                                        'background-no-repeat'    => 'No Repeat',
                                        'background-repeat'  => 'No Repeat',
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
                                        'xxs-padding-top' => 'Extra Small',
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
                                        'xxs-padding-bottom' => 'Extra Small',
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
                                        'xxs-padding-left' => 'Extra Small',
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
                                        'xxs-padding-right' => 'Extra Small',
                                    )
                                ),

                        )
                    )
                    ->add_fields(
                        'margin',
                        array(
                            Field::make('select', 'margin_top', 'margin Top')
                                ->set_options(
                                    array(
                                        ''               => 'No margin',
                                        'xl-margin-top'  => 'Extra Large',
                                        'lg-margin-top'  => 'Large',
                                        'md-margin-top'  => 'Medium',
                                        'sm-margin-top'  => 'Small',
                                        'xxs-margin-top' => 'Extra Small',
                                    )
                                ),
                            Field::make('select', 'margin_bottom', 'margin Bottom')
                                ->set_options(
                                    array(
                                        ''                  => 'No margin',
                                        'xl-margin-bottom'  => 'Extra Large',
                                        'lg-margin-bottom'  => 'Large',
                                        'md-margin-bottom'  => 'Medium',
                                        'sm-margin-bottom'  => 'Small',
                                        'xxs-margin-bottom' => 'Extra Small',
                                    )
                                ),
                            Field::make('select', 'margin_left', 'margin left')
                                ->set_options(
                                    array(
                                        ''                => 'No margin',
                                        'xl-margin-left'  => 'Extra Large',
                                        'lg-margin-left'  => 'Large',
                                        'md-margin-left'  => 'Medium',
                                        'sm-margin-left'  => 'Small',
                                        'xxs-margin-left' => 'Extra Small',
                                    )
                                ),
                            Field::make('select', 'margin_right', 'margin right')
                                ->set_options(
                                    array(
                                        ''                 => 'No margin',
                                        'xl-margin-right'  => 'Extra Large',
                                        'lg-margin-right'  => 'Large',
                                        'md-margin-right'  => 'Medium',
                                        'sm-margin-right'  => 'Small',
                                        'xxs-margin-right' => 'Extra Small',
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
                        'border_radius',
                        array(
                            Field::make('text', 'border_radius', 'Border Radius')
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
            ->set_header_template('<%- title %>')


    ));
