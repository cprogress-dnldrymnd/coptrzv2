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

    function _button($atts)
    {
        ob_start();
        extract(
            shortcode_atts(
                array(
                    'button_text' => '',
                    'button_link' => '',
                    'class' => 'button-accent',
                    'data_aos' => '',
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


function _elements($data)
{
    ob_start();
    foreach ($data as $d) {
        $type = $d['_type'];
        switch ($type) {
            case 'heading':
                echo do_shortcode('[_heading heading="' . $d['heading'] . '"]');
            case 'description':
                echo do_shortcode('[_description description="' . $d['description'] . '"]');
                break;
            case 'image':
                echo do_shortcode('[_image id="' . $d['image'] . '"]');
                break;
            case 'button':
                if ($d['button_type'] == 'custom') {
                    $button_link = $d['button_url_custom'];
                } else {
                    $button_link = get_permalink($d['button_url']);
                }
                echo do_shortcode('[_button class="button-accent xxx" button_type="' . $d['button_type'] . '" button_text="' . $d['button_text'] . '" button_link="' . $button_link . '"]');
                break;
        }
    }
    return ob_get_clean();
}
