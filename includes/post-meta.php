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
        Field::make('complex', 'module')
            ->add_fields('section', array(
                Field::make('image', 'image'),
                Field::make('text', 'caption'),
            ))
            ->add_fields('movie', array(
                Field::make('file', 'video'),
                Field::make('text', 'title'),
                Field::make('text', 'length'),
            ))

    ));
