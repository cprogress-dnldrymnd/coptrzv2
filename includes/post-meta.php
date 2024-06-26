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
                Field::make('complex', 'row', __('Row'))
                    ->add_fields('movie', array(
                        
                    ))
            ))


    ));
