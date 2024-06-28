<?php

class Shortcodes
{
    function _heading($atts)
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

        if ($heading) {
            return "<$tag class='$class'>$heading</$tag>";
        }
    }
}
$Shortcodes = new Shortcodes;
add_shortcode('_heading', array($Shortcodes, '__heading'));
