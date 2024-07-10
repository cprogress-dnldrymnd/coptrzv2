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
                    'type' => 'page',
                    'id' => get_the_ID(),
                    'archive_title' => '',
                ),
                $atts
            )
        );

        $home = get_site_url();

        $html = "<div class='breadcrumbs mb-3 medium-text fw-light'>";
        $html .= "<ul class='list-inline m-0 p-0 t'>";

        $html .= "<li><a class='item text-white' href='$home'>Home</a></li>";

        if ($type == 'page') {
            $title = get_the_title($id);

            if (get_post_type($id) == 'product') {
                $product_cat = get_the_terms($id, 'product_cat');
                if ($product_cat) {
                    foreach ($product_cat as $cat) {
                        $cat_name = $cat->name;
                        $parent = $cat->parent;
                        $cat_link = get_term_link($cat->term_id);
                        $html .= "<li><a class='item text-white' href='$cat_link'>$cat_name</a></li>";
                    }
                }
            }

            $html .= "<li><span class='item text-white'  >$title</span></li>";
        } else if ($type == 'term') {
            $term = get_term($id);
            $parent = (isset($term->parent)) ? get_term_by('id', $term->parent, $term->taxonomy) : false;
            if ($parent) {
                $parent_link = get_term_link($parent->term_id);
                $parent_name = $parent->name;
                $html .= "<li><a class='item text-white' href='$parent_link'>$parent_name</a></li>";
            }
            $html .= "<li><span class='item text-white'  >$term->name</span></li>";
        } else if ($type == 'archive') {
            if ($archive_title) {
                $html .= "<li><span class='item text-white'  >$archive_title</span></li>";
            }
        }


        $html .= "</ul>";
        $html .= "</div>";

        return $html;
    }

    function product_grid_display($atts)
    {
        extract(
            shortcode_atts(
                array(
                    'id' => '',
                ),
                $atts
            )
        );

        if ($id) {
            return _product_grid_display($id);
        }
    }

    function case_study_slider_grid($atts)
    {
        extract(
            shortcode_atts(
                array(
                    'id' => '',
                ),
                $atts
            )
        );
        $post_excerpt = wpautop(get_the_excerpt($id));
        $features = get__post_meta_by_id($id, 'feature');
        $logo = get__post_meta_by_id($id, 'logo');
        $html .= __image(array(
            'image_id' => get_post_thumbnail_id($id),
            'placeholder' => true,
            'size' => 'full',
            'class' => _attribute('class', array('background-image background-overlay background-overlay-darker bg-black mx-20px rounded-10px overflow-hidden'))
        ));

        $html .= "<div class='inner md-padding-bottom lg-padding-top mx-20px  overflow-hidden position-relative'>"; //inner

        $html .= "<div class='container'>"; //container
        $html .= "<div class='row'>"; //row

        $html .= "<div class='col-lg-8'>";
        $html .= __description(array(
            'description' => $post_excerpt,
            'class' => _attribute('class', array('description-box big-text mb-5'))
        ));
        if ($logo) {
            $html .= __image(array(
                'image_id' => $logo,
                'placeholder' => true,
                'size' => 'large',
                'class' => _attribute('class', array('logo-box'))
            ));
        }
        $html .= "</div>";

        if ($features) {
            $html .= "<div class='col-lg-4'>";
            $html .= "<div class='meta-data text-end'>";
            $html .= "<ul class='list-inline p-0'>";

            foreach ($features as $feature) {
                $feature_text = $feature['feature_text'];
                $html .= "<li class='mb-3'>$feature_text</li>";
            }

            $html .= "</ul>";
            $html .= "</div>";
            $html .= "</div>";
        }

        $html .= "</div>"; //end-row
        $html .= "</div>"; //end-container
        $html .= "</div>"; //end-inner

        return $html;
    }
}
$Shortcodes = new Shortcodes;
add_shortcode('taxonomy_terms', array($Shortcodes, 'taxonomy_terms'));
add_shortcode('breadcrumbs', array($Shortcodes, 'breadcrumbs'));
add_shortcode('product_grid_display', array($Shortcodes, 'product_grid_display'));
add_shortcode('case_study_slider_grid', array($Shortcodes, 'case_study_slider_grid'));
