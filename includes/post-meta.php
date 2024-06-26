<?php

use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Carbon_Fields\Complex_Container;
use Carbon_Fields\Field;


/*-----------------------------------------------------------------------------------*/
/* Modules
/*-----------------------------------------------------------------------------------*/

$heading_fields = array(
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
);

Container::make('post_meta', __('Modules'))
    ->where('post_template', '=', 'templates/page-modules.php')
    ->add_fields(array(
        Field::make('complex', 'module', __('Module'))
            ->setup_labels(
                array(
                    'plural_name'   => 'Modules',
                    'singular_name' => 'Module',
                )
            )
            ->set_collapsed(true)
            ->add_fields(array(
                Field::make('text', 'title', __('Title'))
                    ->set_required(true),
                Field::make('complex', 'columns', __('Columns'))
                    ->setup_labels(
                        array(
                            'plural_name'   => 'Columns',
                            'singular_name' => 'Column',
                        )
                    )
                    ->set_classes('columns')
                    ->add_fields('column', array(
                        Field::make('complex', 'items', __('Items'))
                            ->setup_labels(
                                array(
                                    'plural_name'   => 'Items',
                                    'singular_name' => 'Item',
                                )
                            )
                            ->set_collapsed(true)
                            ->add_fields('heading', $heading_fields)
                            ->add_fields(
                                'description',
                                array(
                                    Field::make('textarea', 'description', __('Description'))->set_classes('activate-tinymce'),
                                )
                            )
                            ->add_fields(
                                'embed',
                                array(
                                    Field::make('oembed', 'embed', __('Embed')),
                                )
                            )
                            ->add_fields(
                                'button',
                                array(
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

                                )
                            )
                            ->add_fields(
                                'image',
                                array(
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
                                )
                            )
                            ->add_fields(
                                'icon',
                                array(
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

                                )
                            )
                            ->add_fields(
                                'custom_html',
                                array(
                                    Field::make('textarea', 'custom_html', __('Custom HTML')),
                                )
                            )

                    ))

            ))
            ->set_header_template('<%- title %>')


    ));
