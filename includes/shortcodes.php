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
        $class_val = _class($class);
        if ($heading) {
            return "<$tag $class_val>$heading</$tag>";
        }
    }
}
$Shortcodes = new Shortcodes;
add_shortcode('__heading', array($Shortcodes, '__heading'));
