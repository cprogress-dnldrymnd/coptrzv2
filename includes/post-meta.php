<?php

use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Carbon_Fields\Complex_Container;
use Carbon_Fields\Field;
/*-----------------------------------------------------------------------------------*/
/* Section
/*-----------------------------------------------------------------------------------*/

$animate_on_scroll = array("fade-up", "fade-down", "fade-right", "fade-left", "fade-up-right", "fade-up-left", "fade-down-right", "fade-down-left", "flip-left", "flip-right", "flip-up", "flip-down", "zoom-in", "zoom-in-up", "zoom-in-down", "zoom-in-left", "zoom-in-right", "zoom-out", "zoom-out-up", "zoom-out-down", "zoom-out-right", "zoom-out-left");

Container::make('post_meta', __('Hero'))
    ->add_fields(array(
        Field::make('checkbox', 'hero_hidden', __('Hide Hero'))->set_classes('inline-field'),
        Field::make('text', 'hero_heading', __('Heading'))->set_help_text('')->set_classes('inline-field')->set_attribute('placeholder', 'Defaults to page title'),
        Field::make('textarea', 'hero_description', __('Description'))->set_classes('inline-field'),
        Field::make('file', 'hero_background', __('Hero Background'))->set_classes('inline-field')->set_type(array('video', 'image'))
    ));
Container::make('post_meta', __('Sections'))
    ->where('post_template', '=', 'templates/page-modules.php')
    ->or_where('post_type', '=', 'product')
    ->add_fields(array(
        Field::make('complex', 'sections', __(''))
            ->setup_labels(
                array(
                    'plural_name'   => 'Sections',
                    'singular_name' => 'Section',
                )
            )
            ->set_collapsed(true)
            ->add_fields(array(
                Field::make('text', 'title', __('Section Title'))->set_required(true)->set_width(33),
                Field::make('text', 'section_id', __('Section ID'))->set_width(33),
                Field::make('checkbox', 'disable_section', __('Disable Section'))->set_width(33),

                Field::make('complex', 'section_items', __('Section Items'))
                    ->setup_labels(
                        array(
                            'plural_name'   => 'Section Items',
                            'singular_name' => 'Section Item',
                        )
                    )
                    ->set_collapsed(true)
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
                        Field::make('textarea', 'description', __('Description'))->set_classes('activate-tinymce'),
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
                    ->add_fields(
                        'custom_html',
                        array(
                            Field::make('textarea', 'custom_html', __('Custom HTML')),
                        )
                    )
                    ->add_fields('columns', array(
                        Field::make('html', 'html_4')->set_html('<label>Section Columns Options</label>')->set_classes('cb-label'),
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
                        Field::make('select', 'image_fit', __('Image Fit'))->set_width(25)
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
                        Field::make('text', 'image_padding', __('Image Padding'))->set_width(25)
                            ->set_help_text('Default is 30%')
                            ->set_conditional_logic(
                                array(
                                    array(
                                        'field' => 'same_image_height',
                                        'value' => true,
                                    )
                                )
                            ),
                        Field::make('html', 'html_42')->set_html('<label>Section Columns Settings</label>')->set_classes('cb-label'),
                        Field::make('complex', 'columns', __(''))
                            ->setup_labels(
                                array(
                                    'plural_name'   => 'Columns',
                                    'singular_name' => 'Column',
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
                                        Field::make('html', 'html_1')->set_html('<label>Section Heading Options</label>')->set_classes('cb-label'),
                                        Field::make('checkbox', 'has_prefix', __('Heading Has Prefix'))->set_width(33),
                                        Field::make('checkbox', 'has_suffix', __('Heading Has Suffix'))->set_width(33),
                                        Field::make('checkbox', 'has_custom_heading_settings', __('Custom Heading Settings'))->set_width(33),
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
                                        Field::make('select', 'tag', __('Tag'))->set_width(100)
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
                                        Field::make('select', 'size', __('Heading Size'))->set_width(100)
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
                                        Field::make('select', 'text_align', __('Text Align'))->set_width(100)
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
                                        Field::make('select', 'text_color', 'Text Color')->set_width(100)
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
                                        Field::make('color', 'text_color_custom', __('Text Color'))->set_width(100)
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
                                        Field::make('textarea', 'description', __('Description'))->set_classes('activate-tinymce'),
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
                                            Field::make('complex', 'buttons', __('Buttons'))
                                                ->set_classes('columns')
                                                ->setup_labels(
                                                    array(
                                                        'plural_name'   => 'Buttons',
                                                        'singular_name' => 'Button',
                                                    )
                                                )
                                                ->set_header_template('<%- button_text %>')
                                                ->add_fields(array(
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
                                                    Field::make('select', 'button_target', __('Button Target'))
                                                        ->set_options(
                                                            array(
                                                                'target="_self"'      => 'Default',
                                                                'target="_blank"'      => 'New Tab',
                                                            )
                                                        ),
                                                ))
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
                                            Field::make('select', 'margin_top', 'margin Top')
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
                                            Field::make('select', 'margin_bottom', 'margin Bottom')
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
                                            Field::make('select', 'margin_left', 'margin left')
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
                                            Field::make('select', 'margin_right', 'margin right')
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
                                    ->set_conditional_logic(
                                        array(
                                            array(
                                                'field' => 'parent.individual_column_settings',
                                                'value' => true,
                                            )
                                        )
                                    ),

                            ))
                            ->set_header_template('<%- column_title %>'),
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
                                    Field::make('select', 'margin_top', 'margin Top')
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
                                    Field::make('select', 'margin_bottom', 'margin Bottom')
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
                                    Field::make('select', 'margin_left', 'margin left')
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
                                    Field::make('select', 'margin_right', 'margin right')
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
                                                'col-lg' 	=> 'Default',
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
                                                '' 	=> 'Default',
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
                                                '' 	=> 'Default',
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
                                    Field::make('text', 'border_radius', 'Border Radius'),
                                    Field::make('select', 'border_style', 'Border Style')
                                        ->set_options(
                                            array(
                                                'border-default'   => 'Default',
                                                'border-custom'   => 'Custom',
                                            )
                                        ),

                                    Field::make('select', 'border_color', 'Border Color')
                                        ->set_options(
                                            array(
                                                'text-primary'   => 'Primary',
                                                'text-secondary' => 'Secondary',
                                                'text-accent'    => 'Accent',
                                                'text-white'     => 'White',
                                                'text-light-gray'     => 'Light Gray',
                                                'border-custom-color'    => 'Custom',
                                            )
                                        )
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field' => 'border_style',
                                                    'value' => 'border-custom',
                                                )
                                            )
                                        ),
                                    Field::make('color', 'border_color_custom', __('Border Color'))
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field' => 'border_style',
                                                    'value' => 'border-custom',
                                                )
                                            )
                                        ),
                                    Field::make('text', 'border_width', 'Border Width')
                                        ->set_conditional_logic(
                                            array(
                                                array(
                                                    'field' => 'border_style',
                                                    'value' => 'border-custom',
                                                )
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
                            Field::make('complex', 'post_box_styles', __('Post Box Styles'))
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
                                        Field::make('select', 'margin_top', 'margin Top')
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
                                        Field::make('select', 'margin_bottom', 'margin Bottom')
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
                                        Field::make('select', 'margin_left', 'margin left')
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
                                        Field::make('select', 'margin_right', 'margin right')
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
                                        Field::make('text', 'border_radius', 'Border Radius'),
                                        Field::make('select', 'border_style', 'Border Style')
                                            ->set_options(
                                                array(
                                                    'border-default'   => 'Default',
                                                    'border-custom'   => 'Custom',
                                                )
                                            ),

                                        Field::make('select', 'border_color', 'Border Color')
                                            ->set_options(
                                                array(
                                                    'text-primary'   => 'Primary',
                                                    'text-secondary' => 'Secondary',
                                                    'text-accent'    => 'Accent',
                                                    'text-white'     => 'White',
                                                    'text-light-gray'     => 'Light Gray',
                                                    'border-custom-color'    => 'Custom',
                                                )
                                            )
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'border_style',
                                                        'value' => 'border-custom',
                                                    )
                                                )
                                            ),
                                        Field::make('color', 'border_color_custom', __('Border Color'))
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'border_style',
                                                        'value' => 'border-custom',
                                                    )
                                                )
                                            ),
                                        Field::make('text', 'border_width', 'Border Width')
                                            ->set_conditional_logic(
                                                array(
                                                    array(
                                                        'field' => 'border_style',
                                                        'value' => 'border-custom',
                                                    )
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

                            Field::make('complex', 'post_elements', 'Post Elements')
                                ->set_duplicate_groups_allowed(false)
                                ->add_fields(
                                    'post_title',
                                    array(
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
                                ->add_fields(
                                    'custom_field_1',
                                    array(
                                        Field::make('text', 'custom_field_key', __('Custom Field Key')),
                                        Field::make('text', 'custom_field_type', __('Custom Field Type')),
                                        Field::make('text', 'custom_field_class', __('Wrapper Class')),
                                    )
                                )
                                ->set_header_template('Custom Field: <%- custom_field_key  %>')
                                ->add_fields(
                                    'custom_field_2',
                                    array(
                                        Field::make('text', 'custom_field_key', __('Custom Field Key')),
                                        Field::make('text', 'custom_field_type', __('Custom Field Type')),
                                        Field::make('text', 'custom_field_class', __('Wrapper Class')),
                                    )
                                )
                                ->set_header_template('Custom Field: <%- custom_field_key  %>')
                                ->add_fields(
                                    'custom_field_3',
                                    array(
                                        Field::make('text', 'custom_field_key', __('Custom Field Key')),
                                        Field::make('text', 'custom_field_type', __('Custom Field Type')),
                                        Field::make('text', 'custom_field_class', __('Wrapper Class')),
                                    )
                                )
                                ->set_header_template('Custom Field: <%- custom_field_key  %>')
                                ->add_fields(
                                    'custom_field_4',
                                    array(
                                        Field::make('text', 'custom_field_key', __('Custom Field Key')),
                                        Field::make('text', 'custom_field_type', __('Custom Field Type')),
                                        Field::make('text', 'custom_field_class', __('Wrapper Class')),
                                    )
                                )
                                ->set_header_template('Custom Field: <%- custom_field_key  %>')
                                ->add_fields(
                                    'custom_field_5',
                                    array(
                                        Field::make('text', 'custom_field_key', __('Custom Field Key')),
                                        Field::make('text', 'custom_field_type', __('Custom Field Type')),
                                        Field::make('text', 'custom_field_class', __('Wrapper Class')),
                                    )
                                )
                                ->set_header_template('Custom Field: <%- custom_field_key  %>')
                                ->set_layout('tabbed-vertical'),
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
                                        Field::make('hidden', 'taxonomy_key', '')->set_default_value('case_study_category'),
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
                                                        'taxonomy' => 'case_study_category',
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
                                ->set_layout('tabbed-vertical')

                        ),
                    )
                    ->add_fields(
                        'buttons',
                        array(
                            Field::make('complex', 'buttons', __('Buttons'))
                                ->set_classes('columns')
                                ->setup_labels(
                                    array(
                                        'plural_name'   => 'Buttons',
                                        'singular_name' => 'Button',
                                    )
                                )
                                ->set_header_template('<%- button_text %>')
                                ->add_fields(array(
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
                                    Field::make('select', 'button_target', __('Button Target'))
                                        ->set_options(
                                            array(
                                                'target="_self"'      => 'Default',
                                                'target="_blank"'      => 'New Tab',
                                            )
                                        ),
                                ))
                        )
                    ),
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
                            Field::make('select', 'margin_top', 'margin Top')
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
                            Field::make('select', 'margin_bottom', 'margin Bottom')
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
                            Field::make('select', 'margin_left', 'margin left')
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
                            Field::make('select', 'margin_right', 'margin right')
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
                            Field::make('text', 'border_radius', 'Border Radius'),
                            Field::make('select', 'border_style', 'Border Style')
                                ->set_options(
                                    array(
                                        ''   => 'None',
                                        'border-default'   => 'Default',
                                        'border-custom'   => 'Custom',
                                    )
                                ),

                            Field::make('select', 'border_color', 'Border Color')
                                ->set_options(
                                    array(
                                        ''   => 'Default',
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
                                            'value' => '',
                                            'compare' => '!='
                                        )
                                    )
                                ),
                            Field::make('color', 'border_color_custom', __('Border Color'))
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'border_color',
                                            'value' => 'border-custom-color',
                                        ),
                                    )
                                ),
                            Field::make('checkbox', 'different_border_width', 'Different Border Width')->set_width(20)
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'border_style',
                                            'value' => 'border-custom',
                                        )
                                    )
                                ),
                            Field::make('text', 'border_width', 'Border Width')
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'border_style',
                                            'value' => 'border-custom',
                                        ),
                                        array(
                                            'field' => 'different_border_width',
                                            'value' => false,
                                        )
                                    )
                                ),
                            Field::make('text', 'border_width_top', 'Top Border Width')->set_width(20)
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'border_style',
                                            'value' => 'border-custom',
                                        ),
                                        array(
                                            'field' => 'different_border_width',
                                            'value' => true,
                                        )
                                    )
                                ),
                            Field::make('text', 'border_width_right', 'Right Border Width')->set_width(20)
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'border_style',
                                            'value' => 'border-custom',
                                        ),
                                        array(
                                            'field' => 'different_border_width',
                                            'value' => true,
                                        )
                                    )
                                ),
                            Field::make('text', 'border_width_bottom', 'Bottom Border Width')->set_width(20)
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'border_style',
                                            'value' => 'border-custom',
                                        ),
                                        array(
                                            'field' => 'different_border_width',
                                            'value' => true,
                                        )
                                    )
                                ),
                            Field::make('text', 'border_width_left', 'Left Border Width')->set_width(20)
                                ->set_conditional_logic(
                                    array(
                                        array(
                                            'field' => 'border_style',
                                            'value' => 'border-custom',
                                        ),
                                        array(
                                            'field' => 'different_border_width',
                                            'value' => true,
                                        )
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
            ->set_header_template('<%- title %>')


    ));

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

/*-----------------------------------------------------------------------------------*/
/* Products
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', __('Product Settings'))
    ->where('post_type', '=', 'product')
    ->add_fields(array(
        Field::make('hidden', 'single_product_content', __(''))
    ));
