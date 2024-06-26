<?php

use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Carbon_Fields\Complex_Container;
use Carbon_Fields\Field;


/*-----------------------------------------------------------------------------------*/
/* Modules
/*-----------------------------------------------------------------------------------*/

Container::make('post_meta', __('Book Data'))
    ->where('post_template', '=', 'templates/page-modules.php')
    ->add_fields(array(
        Field::make('complex', 'module', __('Module'))
            ->add_fields(array(
                Field::make('text', 'title', __('Title')),
                Field::make('complex', 'row', __('Row'))
                    ->add_fields('column', array(
                        Field::make('complex', 'title', __('Title')),
                    ))
            ))
            ->set_header_template('<%- title %>')
            ->setup_labels(
                array(
                    'plural_name'   => 'Modules',
                    'singular_name' => 'Module',
                )
            )

    ));
