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
                    'link' => '',
                    'placeholder' => '',
                    'class' => '',
                    'data_aos' => '',
                ),
                $atts
            )
        );
        include locate_template('template-parts/components/image.php');
        return ob_get_clean();
    }

    function _heading($atts)
    {
        ob_start();
        extract(
            shortcode_atts(
                array(
                    'heading' => '',
                    'heading_small' => '',
                    'tag' => '',
                    'class' => '',
                    'data_aos' => '',
                ),
                $atts
            )
        );
        include locate_template('template-parts/components/heading.php');
        return ob_get_clean();
    }

    function _description($atts)
    {
        ob_start();
        extract(
            shortcode_atts(
                array(
                    'description' => '',
                    'class' => '',
                    'data_aos' => '',
                ),
                $atts
            )
        );
        include locate_template('template-parts/components/description.php');
        return ob_get_clean();
    }
}

$Elements_Shortcodes = new Elements_Shortcodes;
add_shortcode('_image', array($Elements_Shortcodes, '_image'));
add_shortcode('_heading', array($Elements_Shortcodes, '_heading'));
add_shortcode('_description', array($Elements_Shortcodes, '_description'));


function _elements($data)
{
    ob_start();
    foreach ($data as $d) {
        $type = $d['_type'];
        switch ($type) {
            case 'heading':
                echo do_shortcode('[_heading heading="' . $d['heading'] . ']');
            case 'description':
                echo do_shortcode('[_description description="' . $d['description'] . '"]');
                break;
            case 'image':
                echo do_shortcode('[_image id="' . $d['image'] . '"]');
                break;
        }
    }
    return ob_get_clean();
}
