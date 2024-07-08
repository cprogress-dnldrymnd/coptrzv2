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


        $terms = get_terms(array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        ));

        $html = "<div class='taxonomy-terms'>";

        $html .= "<div class='row g-4 justify-content-between'>";
        if ($heading) {
            $html .= "<div class='col'>";
            $html .= "<h2>$heading</h2>";
            $html .= "</div>";
        }
        $html .= "</div>";

        $html .= "<div class='row g-3 same-image-height' style='--object-fit-contain'>";
        foreach ($terms as $term) {
            $logo = get___term_meta($term->term_id, 'image');
            $image_args['image_id'] = $logo;
            $image_args['size'] = 'medium';
            $image_args['class'] = _attribute('class', array('image-box mb-3'));

            $html .= "<div class='col-lg-3'>";
            $html .= "<div class='inner text-center h-100 border-default rounded-corner xs-padding'>";
            $html .= __image($image_args);
            $html .= __heading(array(
                'heading' => $term->name,
                'class' => _attribute('class', array('mb-0')),
                'tag' => 'h3',
            ));
            $html .= "</div>";
            $html .= "</div>";
        }
        $html .= "</div>";

        $html .= "</div>";

        return $html;
    }
}
$Shortcodes = new Shortcodes;
add_shortcode('taxonomy_terms', array($Shortcodes, 'taxonomy_terms'));
