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
                    'hide_empty' => true,
                ),
                $atts
            )
        );


        $terms = get_terms(array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => $hide_empty,
            'number' => $items_per_page
        ));

        $html = "<div class='taxonomy-terms'>";

        $html .= "<div class='row g-4 justify-content-between'>";
        if ($heading) {
            $html .= "<div class='col'>";
            $html .= "<h2>$heading</h2>";
            $html .= "</div>";
        }
        $html .= "</div>";

        $html .= "<div class='row g-3 same-image-height' style='--object-fit: contain; --image-padding: 20%'>";
        foreach ($terms as $term) {
            $logo = get___term_meta($term->term_id, 'image');
            if ($logo) {
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
        }
        $html .= "</div>";

        $html .= "</div>";

        return $html;
    }

    function breadcrumbs($atts)
    {
        extract(
            shortcode_atts(
                array(
                    'id' => get_the_ID(),
                ),
                $atts
            )
        );

        $home = get_site_url();
        $title = get_the_title($id);
        $html = "<div class='breadcrumbs mb-3 medium-text fw-light'>";
        $html .= "<ul class='list-inline p-0 t'>";

        $html .= "<li><a class='item text-white' href='$home'>Home</a></li>";
        $html .= "<li><span class='item text-white'  >$title</span></li>";


        $html .= "</ul>";
        $html .= "</div>";

        return $html;
    }
}
$Shortcodes = new Shortcodes;
add_shortcode('taxonomy_terms', array($Shortcodes, 'taxonomy_terms'));
add_shortcode('breadcrumbs', array($Shortcodes, 'breadcrumbs'));
