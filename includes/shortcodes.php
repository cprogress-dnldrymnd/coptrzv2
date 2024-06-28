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
            $format = '<%d>%s</%d>';
            return sprintf($format, $tag, $heading);
        }
    }
}
$Shortcodes = new Shortcodes;
add_shortcode('__heading', array($Shortcodes, '__heading'));
