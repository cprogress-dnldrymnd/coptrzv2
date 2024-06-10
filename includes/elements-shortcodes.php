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
                    'style' => '',
                    'tag'           => '',
                    'heading_prefix'     => '',
                    'heading_suffix'     => '',
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


    function _icon($atts)
    {
        ob_start();
        extract(
            shortcode_atts(
                array(
                    'id'           => '',
                    'class'  => '',
                    'icon_color_custom'  => 'false',
                    'icon_width' => 'false',
                    'icon_height' => '',
                ),
                $atts
            )
        );

        include locate_template('template-parts/components/icon.php');
        return ob_get_clean();
    }

    function _embed($atts)
    {
        ob_start();
        extract(
            shortcode_atts(
                array(
                    'src'           => '',
                ),
                $atts
            )
        );

        include locate_template('template-parts/components/embed.php');
        return ob_get_clean();
    }
}

$Elements_Shortcodes = new Elements_Shortcodes;
add_shortcode('_image', array($Elements_Shortcodes, '_image'));
add_shortcode('_icon', array($Elements_Shortcodes, '_icon'));
add_shortcode('_heading', array($Elements_Shortcodes, '_heading'));
add_shortcode('_description', array($Elements_Shortcodes, '_description'));
add_shortcode('_button', array($Elements_Shortcodes, '_button'));
add_shortcode('_embed', array($Elements_Shortcodes, '_embed'));
