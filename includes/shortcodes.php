<?php

class Shortcodes
{
    function taxonomy_terms($atts)
    {
        extract(
            shortcode_atts(
                array(
                    'heading' => '',
                    'taxonomy' => '',
                    'search_filter' => false,
                    'items_per_page' => 16,
                ),
                $atts
            )
        );

        $html = "<div class='taxonomy-terms'>";
        $html .= "<div class='row g-4 justify-content-between'>";

        if ($heading) {
            $html .= "<div class='col'>";
            $html .= "<h2>$heading</h2>";
            $html .= "</div>";
        }

        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }
}
$Shortcodes = new Shortcodes;
add_shortcode('taxonomy_terms', array($Shortcodes, 'taxonomy_terms'));
