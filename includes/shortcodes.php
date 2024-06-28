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
}
$Shortcodes = new Shortcodes;
add_shortcode('__heading', array($Shortcodes, '__heading'));
