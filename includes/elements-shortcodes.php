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

    function _elements($atts)
    {
        ob_start();
        extract(
            shortcode_atts(
                array(
                    'data' => '',
                ),
                $atts
            )
        );
        $datas = unserialize($data);
        var_dump($datas);
        foreach ($datas as $d) {
            $type = $d['_type'];
            echo $type;
            switch ($type) {
                case 'heading':
                    echo do_shortcode('[_heading heading="' . $d['heading'] . '"]');
                case 'description':
                    echo do_shortcode('[_description description="' . $d['description'] . '"]');
                    break;
            }
        }
        return ob_get_clean();
    }
}

$Elements_Shortcodes = new Elements_Shortcodes;
add_shortcode('_image', array($Elements_Shortcodes, '_image'));
add_shortcode('_heading', array($Elements_Shortcodes, '_heading'));
add_shortcode('_description', array($Elements_Shortcodes, '_description'));
add_shortcode('_elements', array($Elements_Shortcodes, '_elements'));
