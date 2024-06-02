<?php
class Elements_Shortcodes
{

    function _image($atts)
    {
        ob_start();
        extract(
            shortcode_atts(
                array(
                    'id' => '',
                    'size' => '',
                    'placeholder' => '',
                    'class' => '',
                    'data_aos' => '',
                    'class' => '',
                ),
                $atts
            )
        );
        include locate_template('template-parts/components/image.php');
        return ob_get_clean();
    }
}

$Elements_Shortcodes = new Elements_Shortcodes;
add_shortcode('_image', array($Elements_Shortcodes, '_image'));
