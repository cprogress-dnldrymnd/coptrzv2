<?php
class Elements_Shortcodes
{

    function _image($atts)
    {
        ob_start();
        extract(
            shortcode_atts(
                array(
                    'id'           => '',
                    'size'         => '',
                    'link'         => '',
                    'placeholder'  => '',
                    'class'        => '',
                    'data_aos'     => '',
                    'image_height' => '',
                    'image_width'  => '',
                    'same_height'  => 'false',
                    'rounded_corners' => 'false',
                    'border_radius' => '',
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
                    'heading'       => '',
                    'heading_small' => '',
                    'tag'           => '',
                    'class'         => '',
                    'data_aos'      => '',
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
                    'class'       => '',
                    'data_aos'    => '',
                ),
                $atts
            )
        );
        include locate_template('template-parts/components/description.php');
        return ob_get_clean();
    }

    function _button($atts)
    {
        ob_start();
        extract(
            shortcode_atts(
                array(
                    'id'          => '',
                    'button_type' => '',
                    'button_text' => '',
                    'button_link' => '',
                    'button_url_custom' => '',
                    'class'       => 'button-accent',
                    'data_aos'    => '',
                ),
                $atts
            )
        );
        include locate_template('template-parts/components/button.php');
        return ob_get_clean();
    }
}

$Elements_Shortcodes = new Elements_Shortcodes;
add_shortcode('_image', array($Elements_Shortcodes, '_image'));
add_shortcode('_heading', array($Elements_Shortcodes, '_heading'));
add_shortcode('_description', array($Elements_Shortcodes, '_description'));
add_shortcode('_button', array($Elements_Shortcodes, '_button'));


function _elements($data, $module_id, $same_height_images)
{
    ob_start();
    foreach ($data as $d) {
        $type = $d['_type'];
        switch ($type) {
            case 'heading':
                echo do_shortcode('[_heading tag="' . $d['tag'] . '" heading="' . $d['heading'] . '" size="' . $d['size'] . '"]');
            case 'description':
                echo do_shortcode('[_description description="' . $d['description'] . '"]');
                break;
            case 'image':
                $same_height = $same_height_images ? 'same_height="true"' : 'same_height="false"';
                $rounded_corners = $d['rounded_corners'] ? 'true' : 'false';
                echo do_shortcode('[_image size="' . $d['size'] . '" rounded_corners="' . $rounded_corners . '" border_radius="' . $d['border_radius'] . '" ' . $same_height . '  id="' . $d['image'] . '" image_width="' . $d['image_width'] . '" image_height="' . $d['image_height'] . '"]');
                break;
            case 'button':
                echo do_shortcode('[_button id="' . $d['button_url'] . '" button_url_custom="' . $d['button_url_custom'] . '" button_type="' . $d['button_type'] . '" button_text="' . $d['button_text'] . '" ]');
                break;
            case 'accordion':
                $accordion = $d['accordion'];
                include locate_template('template-parts/components/accordion.php');
                break;
            case 'custom_html':
                echo $d['custom_html'];
                break;
        }
    }
    return ob_get_clean();
}
