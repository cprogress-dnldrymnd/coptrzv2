<?php

class Shortcodes
{
    function __heading($atts)
    {
        extract(
            shortcode_atts(
                array(
                    'heading' => '',
                    'tag' => 'h2',
                    'class' => '',
                ),
                $atts
            )
        );
        $_attributes = _attributes(array(
            array('class', $class)
        ));
        if ($heading) {
            return "<$tag $_attributes>$heading</$tag>";
        }
    }

    function _description($atts)
    {
        extract(
            shortcode_atts(
                array(
                    'description' => '',
                    'class' => '',
                ),
                $atts
            )
        );
        $_attributes = _attributes(array(
            array('class', $class),
            array('class', 'description-box'),
        ));

        $description_val = do_shortcode(wpautop(html_entity_decode($description)));
        if ($description) {
            return "<div $_attributes>$description_val</div>";
        }
    }
}
$Shortcodes = new Shortcodes;
add_shortcode('__heading', array($Shortcodes, '__heading'));
add_shortcode('_description', array($Shortcodes, '_description'));