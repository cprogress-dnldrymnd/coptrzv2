<?php
/*
function action_module_content()
{
    // Check if a post was updated (add your specific conditions here)
    if (did_action('post_updated')) {
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (get_post_type() == 'productcategorypages') {
            $post_content = '<!-- wp:html -->';

            if (_is_module()) {
                $post_content .= ___hero_modules();
            }
            $post_content .= ___sections();


            $post_content .= '<!-- /wp:html -->';

            $my_post = array(
                'ID'           => get_the_ID(),
                'post_content' => $post_content,
            );

            // Update the post into the database
            wp_update_post($my_post);
        }

        if (get_post_type() == 'product') {

            $single_product_content = ___hero_modules();
            $single_product_content .= __product_specifications();
            $single_product_content .= ___sections();
            $single_product_content_after = ___sections('sections_after_main');

            update_post_meta(get_the_ID(), '_single_product_content', $single_product_content);
            update_post_meta(get_the_ID(), '_single_product_content_after', $single_product_content_after);
        }
    }
}
add_action('shutdown', 'action_module_content');
*/

function _date_format($date_input, $include_year = false)
{
    $date = strtotime($date_input);
    $day = date('j', $date);
    $sup = date('S', $date);
    $month = date('F', $date);
    $Y = date('y', $date);
    $newDate = " $day";
    $newDate .= "<sup>$sup</sup>";
    $newDate .= " $month";
    if ($include_year == true) {
        $current_year = date("Y");
        $year = date('Y', $date);
        if ($year != $current_year) {
            $newDate .= " $year";
        }
    }
    return $newDate;
}
function modify_get_pagenum_link_defaults($result, $pagenum)
{
    if (isset($_GET['url'])) {
        $url =  $_GET['url'] . 'page/' . $pagenum;
        if (isset($_GET['posts_per_page'])) {
            $posts_per_page = '&posts_per_page=' . $_GET['posts_per_page'];
        }
        if (isset($_GET['s'])) {
            $s = '&s=' . $_GET['s'];
        }

        return $url . '?' . $posts_per_page . $s;
    } else {
        return $result;
    }
}
add_filter("get_pagenum_link", "modify_get_pagenum_link_defaults", 10, 2);

function _pagination($has_pagination, $query, $data = false)
{
    if ($has_pagination) {
        ob_start();
        $SVG = new SVG;
        if ($query == false) {
            $query = $GLOBALS['wp_query'];
        }

?>
        <div class="pagination">
            <div class="container">
                <div class="inner border-top-default sm-padding-top sm-margin-top">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <nav class="navigation pagination">
                                <div class="nav-links">
                                    <?php
                                    $paginate_links = paginate_links(array(
                                        'base'         => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                                        'total'        => $query->max_num_pages,
                                        'current'      => max(1, get_query_var('paged')),
                                        'format'       => '?paged=%#%',
                                        'show_all'     => false,
                                        'type'         => 'plain',
                                        'end_size'     => 2,
                                        'mid_size'     => 1,
                                        'prev_next'    => true,
                                        'prev_text'    => $SVG->chevron_left(),
                                        'next_text'    => $SVG->chevron_right(),
                                        'add_args'     => false,
                                        'add_fragment' => '',
                                    ));


                                    echo $paginate_links;


                                    ?>
                                </div>
                            </nav>

                        </div>
                        <div class="col-lg-4 text-center text-md-end">

                            <select name="posts_per_page" id="posts_per_page" class="w-auto number-post-trigger">

                                <?php
                                $show_options = array(6, 12, 18, 24, 30);
                                foreach ($show_options as $option) {
                                    $selected = '';
                                    if (isset($_GET['posts_per_page'])) {
                                        if ($_GET['posts_per_page'] == $option) {
                                            $selected = 'selected';
                                        }
                                    } else {
                                        if (12 == $option) {
                                            $selected = 'selected';
                                        }
                                    }
                                    echo "<option $selected value='$option'>Show: $option</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
        return ob_get_clean();
    }
}
function ___hero_modules($hero_alignment_args = false, $hero_height_args = false)
{
    $id = get_the_ID();
    $hero_heading = get__post_meta('hero_heading');
    $hero_description = get__post_meta('hero_description');
    $hero_hidden = get__post_meta('hero_hidden');
    if (get_post_type() == '') {
        $hero_background = get_post_thumbnail_id();
        $hero_background_type = 'self-hosted';
    } else {
        $hero_background = get__post_meta('hero_background');
        $hero_background_youtube = get__post_meta('hero_background_youtube');
        $hero_background_type = get__post_meta('hero_background_type');
    }

    $hero_height = get__post_meta('hero_height') ? get__post_meta('hero_height') : $hero_height_args;
    $hero_alignment = get__post_meta('hero_alignment') ? get__post_meta('hero_alignment') : $hero_alignment_args;

    $breadcrumbs_hidden = get__post_meta('breadcrumbs_hidden');
    $buttons = get__post_meta('buttons');



    $hero_form_enable = get__post_meta('hero_form_enable');


    if (get_post_type() == 'guides') {
        $hero_form_image = get__post_meta('hero_form_image');
        if (!$hero_form_image) {
            $hero_form_image = get_post_thumbnail_id();
        }
    } else {
        $hero_form_image = get__post_meta('hero_form_image');
    }


    $hero_form_heading = get__post_meta('hero_form_heading');
    $hero_form_description = get__post_meta('hero_form_description');
    $hero_form_style = get__post_meta('hero_form_style');
    $hero_form = get__post_meta('hero_form');


    if (!$hero_background && !$hero_background_youtube) {
        if (!$hero_height) {
            $hero_height = 'small-hero';
        }

        if (!$hero_alignment) {
            $text_align = 'text-left';
        } else {
            $text_align = $hero_alignment ? $hero_alignment : 'text-center';
        }
    } else {
        $text_align = $hero_alignment ? $hero_alignment : 'text-center';
    }


    if (get_post_type($id) == 'events') {
        $event_start_datetime = get__post_meta_by_id($id, 'event_start_datetime');
        $event_end_datetime = get__post_meta_by_id($id, 'event_end_datetime');
        $date_time = _date_format($event_start_datetime) . ' | ' . $event_end_datetime;
        $hero_description .= $hero_description . $date_time;
    }

    $heading_class[] = 'large-heading';
    if (!$hero_description && !$buttons) {
        $heading_class[] = 'mb-0';
    } else {
        $heading_class[] = 'mb-3';
    }

    if ($hero_height) {
        $hero_class[] = $hero_height;
    }


    if ($text_align) {
        $hero_class[] = $text_align;
    }

    $col_content_class = [];
    if ($hero_form_enable) {
        $col_content_class[] = 'text-white col-lg-7';
    } else {
        $hero_class[] = 'text-white';
    }

    if ($hero_background_youtube && $hero_background_type == 'youtube') {
        $hero_class[] = 'background-youtube';
    }

    $hero_class[] = 'hero pb-50px pt-50px rounded-10px bg-primary overflow-hidden d-flex align-items-end mx-20px position-relative';

    $hero_class_attribute = _attribute('class', $hero_class);
    $col_content_class_attribute = _attribute('class', $col_content_class);

    $hero_heading_val = $hero_heading ? $hero_heading : get_the_title();
    if (!$hero_hidden) {
        $hero = "<section $hero_class_attribute id='hero'>";
        if ($hero_background_youtube && $hero_background_type == 'youtube') {
            $hero .= __background($hero_background_youtube, true);
        } else if ($hero_background) {
            $hero .= __background($hero_background);
        }
        $hero .= "<div class='container'>";

        if ($hero_form_enable) {
            $hero .= "<div class='row align-items-center'>"; //row
            $hero .= "<div $col_content_class_attribute>"; //col
        }
        $hero .= "<div class='hero-left-content position-relative overflow-hidden hero-bg-mobile'>";

        if (!$breadcrumbs_hidden) {
            $hero .= do_shortcode("[breadcrumbs id='$id']");
        }


        $hero .= __heading(array(
            'heading' => $hero_heading_val,
            'tag' => 'h1',
            'class' => _attribute('class', $heading_class),
            ''
        ));

        $hero .= __description(array(
            'description' => $hero_description,
            'class' => _attribute('class', array('description-box fw-light medium-text small-width mx-auto mb-4')),
        ));


        if ($buttons) {
            $hero .= "<div>";
            $hero .= ____button_modules($buttons);
            $hero .= "</div>";
        }
        $hero .= "</div>";

        if ($hero_form_enable) {
            $hero .= "</div>"; //end-col

            $form_args = array(
                'form' => $hero_form,
                'form_heading' => $hero_form_heading,
                'form_description' => $hero_form_description,
                'form_image' => $hero_form_image,
                'form_style' => $hero_form_style,
            );
            $hero .= "<div class='col-lg-5'>"; //col
            $hero .= __form($form_args);
            $hero .= "</div>"; //end-col

            $hero .= "</div>"; //end-row
        }



        $hero .= "</div>";
        $hero .= "</section>";
        return $hero;
    } else {
        $html = "<div class='page-title-breadcrumbs'>";
        $html .= "<div class='header-spacer'></div>";
        $html .= "<div class='container md-margin-top'>";
        $html .= do_shortcode("[breadcrumbs id='$id']");
        $html .= __heading(array(
            'heading' => get_the_title(),
            'tag' => 'h1',
            'class' => _attribute('class', $heading_class),
            ''
        ));

        $html .= "</div>";
        $html .= "</div>";
        return $html;
    }
}
function ___hero_product_taxonomy()
{

    $term = get_queried_object();
    $id = $term->term_id;
    $hero_heading = get___term_meta($id, 'hero_heading');
    $hero_description = get___term_meta($id, 'hero_description');
    $hero_hidden = get___term_meta($id, 'hero_hidden');
    $hero_background = get___term_meta($id, 'hero_background');
    $hero_background_youtube = get___term_meta($id, 'hero_background_youtube');
    $hero_background_type = get___term_meta($id, 'hero_background_type');
    $hero_alignment = get___term_meta($id, 'hero_alignment');
    $hero_height = get___term_meta($id, 'hero_height');
    $buttons = get___term_meta($id, 'buttons');
    $breadcrumbs_hidden = get___term_meta($id, 'breadcrumbs_hidden');
    $text_align = $hero_alignment ? $hero_alignment : 'text-center';
    $term_description_val = $hero_description ? $hero_description : $term->description;


    $heading_class[] = 'large-heading';
    if (!$term_description_val) {
        $heading_class[] = 'mb-0';
    } else {
        $heading_class[] = 'mb-3';
    }



    if ($buttons) {
        unset($heading_class['mb-0']);
        $heading_class[] = 'mb-5';
    }
    $hero_heading_val = $hero_heading ? $hero_heading : $term->name;

    if (!$hero_hidden) {
        $hero = "<section class='hero pb-50px rounded-10px bg-primary overflow-hidden text-white d-flex align-items-end mx-20px position-relative $hero_height $text_align'>";
        if ($hero_background_youtube && $hero_background_type == 'youtube') {
            $hero .= __background($hero_background_youtube, true);
        } else if ($hero_background) {
            $hero .= __background($hero_background);
        }
        $hero .= "<div class='container'>";

        if (!$breadcrumbs_hidden) {
            $hero .= "[breadcrumbs id='$term->term_id' type='term']";
        }


        $hero .= __heading(array(
            'heading' => $hero_heading_val,
            'tag' => 'h1',
            'class' => _attribute('class', $heading_class),
            ''
        ));

        if ($term_description_val) {
            $hero .= __description(array(
                'description' => $term_description_val,
                'class' => _attribute('class', array('description-box small-text small-width')),
            ));
        }

        if ($buttons) {
            $hero .= "<div>";
            $hero .= ____button_modules($buttons);
            $hero .= "</div>";
        }

        $hero .= "</div>";
        $hero .= "</section>";



        return $hero;
    }
}


function ___sections($id = 'sections', $post_id = '')
{
    $post_id = $post_id ? $post_id : get_the_ID();
    $sections = get__post_meta_by_id($post_id, $id);
    $html = '';
    global $layouts_global;

    foreach ($sections as $key => $section) {
        $disable_section = $section['disable_section'];
        if (!$disable_section) {
            $classes = array();
            $styles_section = array();
            $section_id = $section['section_id'];
            $section_class = $section['section_class'];
            $section_items = $section['section_items'];
            $section_styles = $section['section_styles'];
            $section_id_val  = $section_id ? $section_id : 'section-' . $key;
            $container_styles = array();
            $container_classes = array();
            $is_container_background = false;
            $background_type = false;
            $background  = false;
            $container_classes[] = 'position-relative container-inner';
            $classes[] = 'section';
            $classes[] = 'section-' . $key;
            $styles_val = '';
            $container_styles_val = '';
            $background_image_overlay_args = false;
            if ($section_class) {
                $classes[] = $section_class;
            }
            foreach ($section_styles as $section_style) {
                $type = $section_style['_type'];
                switch ($type) {
                    case 'padding':
                        if ($section_style['padding_top']) {
                            $classes[] = $section_style['padding_top'];
                        }
                        if ($section_style['padding_bottom']) {
                            $classes[] = $section_style['padding_bottom'];
                        }
                        if ($section_style['padding_left']) {
                            $classes[] = $section_style['padding_left'];
                        }
                        if ($section_style['padding_right']) {
                            $classes[] = $section_style['padding_right'];
                        }

                        if ($section_style['container_padding_top']) {
                            $container_classes[] = $section_style['container_padding_top'];
                        }
                        if ($section_style['container_padding_bottom']) {
                            $container_classes[] = $section_style['container_padding_bottom'];
                        }
                        if ($section_style['container_padding_left']) {
                            $container_classes[] = $section_style['container_padding_left'];
                        }
                        if ($section_style['container_padding_right']) {
                            $container_classes[] = $section_style['container_padding_right'];
                        }
                        break;
                    case 'margin':
                        $classes[] = $section_style['margin_top'];
                        $classes[] = $section_style['margin_bottom'];
                        $classes[] = $section_style['margin_left'];
                        $classes[] = $section_style['margin_right'];
                        break;
                    case 'custom_class':
                        $classes[] = $section_style['custom_class'];
                        break;
                    case 'alignment':
                        $classes[] = $section_style['align_items'];
                        $classes[] = $section_style['justify_content'];
                        $classes[] = $section_style['text_align'];
                        if ($section_style['align_items'] || $section_style['justify_content']) {
                            $classes[] = 'd-flex ';
                        }
                        break;
                    case 'text_color':
                        $text_color_custom = $section_style['text_color_custom'];
                        $classes[] = $section_style['text_color'];
                        if ($text_color_custom) {
                            $styles_section[] = 'color: ' . $text_color_custom;
                        }
                        break;
                    case 'background_color':
                        $background_color_custom = $section_style['background_color_custom'];
                        $background_color_container = $section_style['background_color_container'];

                        if ($background_color_container) {
                            $container_classes[] = $background_color_container;
                        }

                        $classes[] = $section_style['background_color'];
                        if ($background_color_custom) {
                            $styles_section[] = 'background-color: ' . $background_color_custom;
                        }
                        break;
                    case 'background_image':
                        $background_image = $section_style['background_image'];
                        $classes[] = $section_style['background_attachment'];
                        $classes[] = $section_style['background_size'];
                        $classes[] = $section_style['background_repeat'];
                        if ($background_image) {
                            $styles_section[] = 'background-image: url(' . wp_get_attachment_image_url($background_image, 'full') . ')';
                        }
                        break;
                    case 'background_video':
                        $background_type = $section_style['background_type'];
                        $background = $section_style['background'];
                        $background_youtube = $section_style['background_youtube'];
                        $is_container_background = $section_style['is_container_background'];
                        break;

                    case 'background_overlay':
                        $background_overlay_type = $section_style['background_overlay_type'];
                        if ($background_overlay_type == 'image') {
                            $background_image_overlay_args['class'] = _attribute('class', array('background-image background-overlay'));
                            $background_image_overlay_args['image_id'] = $section_style['background_overlay_image'];
                            $background_image_class = array();
                            if ($section_style['background_overlay_image_opacity'] || $section_style['background_overlay_image_opacity'] == 0) {
                                $styles_section[] = '--background-image-opacity: ' . $section_style['background_overlay_image_opacity'];
                            }
                            $background_image_class[] = 'no-overlay';
                        } else if ($background_overlay_type == 'custom') {
                            $styles_section[]  = '--background-overlay-custom: ' . $section_style['background_overlay_custom'];
                            $background_image_class[] = 'custom-overlay';
                        } else {
                            $classes[] = "background-overlay $background_overlay_type";
                        }

                        break;
                    case 'background_gradient':
                        $background_gradient = $section_style['background_gradient'];
                        if ($background_gradient != 'custom') {
                            $classes[] = $background_gradient;
                        } else {
                            $background_gradient_type = $section_style['background_gradient_type'];
                            $background_gradient_color_1 = $section_style['background_gradient_color_1'];
                            $background_gradient_stop_1 = $section_style['background_gradient_stop_1'];
                            $background_gradient_color_2 = $section_style['background_gradient_color_2'];
                            $background_gradient_stop_2 = $section_style['background_gradient_stop_2'];
                            if ($background_gradient_type == 'radial-gradient') {
                                $background = "radial-gradient(circle, $background_gradient_color_1 $background_gradient_stop_1, $background_gradient_color_2 $background_gradient_stop_2)";
                            } else {
                                $background_gradient_direction = $section_style['background_gradient_direction'];
                                $background = "linear-gradient($background_gradient_direction, $background_gradient_color_1 $background_gradient_stop_1, $background_gradient_color_2 $background_gradient_stop_2)";
                            }
                            $styles_section[] = 'background: ' . $background;
                        }
                        break;

                    case 'container_width':
                        $classes[] = $section_style['container_width'];
                        if ($section_style['custom_container_width']) {
                            $container_styles[] = 'max-width: ' . $section_style['custom_container_width'];
                        }
                        break;
                    case 'height':
                        $height = $section_style['height'];
                        if ($height) {
                            $styles_section[] = 'min-height: ' . $height;
                        }
                        break;
                    case 'border':
                        $border_radius = $section_style['border_radius'];
                        if ($border_radius) {
                            if ($border_radius == 'custom') {
                                $border_radius_custom = $section_style['border_radius_custom'];
                                $styles_section[] = "border-radius: $border_radius_custom";
                            } else {
                                $classes[] = $border_radius;
                            }
                        }
                        $border_style = $section_style['border_style'];
                        if ($border_style) {
                            if ($border_style == 'border-custom') {
                                $border_color = $section_style['border_color'];
                                $border_width = $section_style['border_width'];
                                if ($border_color == 'border-custom-color') {
                                    $border_color_custom = $section_style['border_color_custom'];
                                    $styles_section[] = "border-color: $border_color_custom";
                                } else {
                                    $classes[] = $border_color;
                                }

                                if ($border_width == 'custom') {
                                    $border_width_top =  $section_style['border_width_top'];
                                    $border_width_right =  $section_style['border_width_right'];
                                    $border_width_bottom =  $section_style['border_width_bottom'];
                                    $border_width_left =  $section_style['border_width_left'];
                                    $classes[] = 'border-width-custom';

                                    if ($border_width_top) {
                                        $styles_section[] = "border-top-width: $border_width_top";
                                    }
                                    if ($border_width_right) {
                                        $styles_section[] = "border-right-width: $border_width_right";
                                    }
                                    if ($border_width_bottom) {
                                        $styles_section[] = "border-bottom-width: $border_width_bottom";
                                    }
                                    if ($border_width_left) {
                                        $styles_section[] = "border-left-width: $border_width_left";
                                    }
                                } else {
                                    $classes[] = 'border-default';
                                }
                            } else {
                                $classes[] = $border_style;
                            }
                        }

                        $container_border_radius = $section_style['container_border_radius'];
                        if ($container_border_radius) {
                            if ($container_border_radius == 'custom') {
                                $container_border_radius_custom = $section_style['container_border_radius_custom'];
                                $container_styles[] = "border-radius: $container_border_radius_custom";
                            } else {
                                $container_classes[] = $container_border_radius;
                            }
                        }
                        $container_border_style = $section_style['container_border_style'];
                        if ($container_border_style) {
                            if ($container_border_style == 'border-custom') {
                                $container_border_color = $section_style['container_border_color'];
                                $container_border_width = $section_style['container_border_width'];
                                if ($container_border_color == 'border-custom-color') {
                                    $container_border_color_custom = $section_style['container_border_color_custom'];
                                    $container_styles[] = "border-color: $container_border_color_custom";
                                } else {
                                    $container_classes[] = $container_border_color;
                                }

                                if ($container_border_width == 'custom') {
                                    $container_border_width_top =  $section_style['container_border_width_top'];
                                    $container_border_width_right =  $section_style['container_border_width_right'];
                                    $container_border_width_bottom =  $section_style['container_border_width_bottom'];
                                    $container_border_width_left =  $section_style['container_border_width_left'];
                                    $container_classes[] = 'border-width-custom';

                                    if ($container_border_width_top) {
                                        $container_styles[] = "border-top-width: $container_border_width_top";
                                    }
                                    if ($container_border_width_right) {
                                        $container_styles[] = "border-right-width: $container_border_width_right";
                                    }
                                    if ($container_border_width_bottom) {
                                        $container_styles[] = "border-bottom-width: $container_border_width_bottom";
                                    }
                                    if ($container_border_width_left) {
                                        $container_styles[] = "border-left-width: $container_border_width_left";
                                    }
                                } else {
                                    $container_classes[] = 'border-default';
                                }
                            } else {
                                $container_classes[] = $container_border_style;
                            }
                        }

                        break;
                }
            }

            $id_val = _attribute('id', array($section_id_val));
            $classes_attr = _attribute('class', $classes);
            if ($container_classes) {
                $container_classes_attr = _attribute('class', $container_classes);
            }
            if ($styles_section) {
                $styles_val = _attribute('style', $styles_section, ';');
            }



            if ($container_styles) {
                $container_styles_val = _attribute('style', $container_styles, ';');
            }

            $section_attribute = _attributes(array($classes_attr, $id_val, $styles_val));
            $container_attribute = _attributes(array($container_styles_val, $container_classes_attr));


            $html .= "<section $section_attribute>";

            if ($background_image_overlay_args) {
                $html .= __image($background_image_overlay_args);
            }
            if (!$is_container_background) {
                if ($background_type && $background_type == 'youtube') {
                    $html .= __background($background_youtube, true);
                } else if ($background) {
                    $html .= __background($background);
                }
            }

            $html .= "<div class='container'>";
            if (count($container_classes) > 1) {
                $html .= "<div $container_attribute>";
            }
            if ($is_container_background) {
                if ($background_type && $background_type == 'youtube') {
                    $html .= __background($background_youtube, true);
                } else if ($background) {
                    $html .= __background($background);
                }
            }
            foreach ($section_items as $key => $items) {
                $type = $items['_type'];
                switch ($type) {
                    case 'layouts':
                        $layouts = $items['layouts'];
                        foreach ($layouts as $layout) {
                            $layout_id = $layout['id'];
                            $layouts_global[] = $layout['id'];
                            $html .= do_shortcode("[layouts id='$layout_id']");
                        }
                        break;
                    case 'heading':
                        $html .= ____heading_modules($items);
                        break;
                    case 'columns':
                        $html .= ____columns_modules($items, $section_id_val . $key);
                        break;
                    case 'description':
                        $classes = array();
                        $styles = array();

                        $description_width = $items['description_width'];
                        $description_alignment = $items['description_alignment'];
                        $description_size = $items['description_size'];

                        $classes[] = 'description-box';

                        if ($description_width) {
                            $styles[] = "max-width: $description_width;";
                        }
                        if ($description_alignment) {
                            $classes[] = "$description_alignment";
                        }
                        if ($description_size) {
                            $classes[] = "$description_size";
                        }

                        $description_args['description'] =  $items['description'];
                        $description_args['class'] =  _attribute('class', $classes);

                        if ($styles) {
                            $description_args['style'] =  _attribute('style', $styles);
                        }


                        $html .= __description($description_args);

                        break;
                    case 'image':
                        $image_styles = array();
                        $image_classes  = array();

                        $is_background_image = $items['is_background_image'];
                        $custom_size = $items['custom_size'];
                        $image_height = $items['image_height'];
                        $image_width = $items['image_width'];
                        $rounded_corners = $items['rounded_corners'];
                        $border_radius = $items['border_radius'];
                        $image_args['image_id'] = $items['image'];
                        $image_args['size'] = $items['size'];


                        $image_classes[] = 'image-box';
                        if ($is_background_image) {
                            $image_classes[] = 'background-image background-overlay';
                        }
                        if ($custom_size) {
                            if ($image_height) {
                                $image_styles[] = "--height: $image_height;";
                                $image_classes[] = 'object-fit-cover';
                            }
                            if ($image_width) {
                                $image_styles[] = "--width: $image_width;";
                            }
                        }
                        if ($rounded_corners) {
                            $image_classes[] = 'rounded-corner';
                            if ($border_radius) {
                                $image_styles[] = "--border-radius: $border_radius;";
                            }
                        }


                        $image_args['style'] = _attribute('style', $image_styles);
                        $image_args['class'] = _attribute('class', $image_classes);

                        $html .= __image($image_args);
                        break;
                    case 'video':
                        $autoplay = $items['autoplay'] ? true : false;
                        $video_type = $items['video_type'];
                        $video = $items['video'];
                        $youtube_video_id = $items['youtube_video_id'];
                        $html .= __video(array(
                            'youtube_video_id' => $youtube_video_id,
                            'autoplay' => $autoplay,
                            'video_id' => $video,
                            'video_type' => $video_type,
                            'class' => _attribute('class', array('video-box', $video_type))
                        ));
                        break;
                    case 'gallery':
                        $html .= ____gallery_modules(array(
                            'id' => $section_id_val . $key,
                            'gallery' => $items['gallery'],
                            'gallery_style' => $items['gallery_style'],
                            'number_of_slides' => $items['number_of_slides'],
                            'number_of_slides_tablet' => $items['number_of_slides_tablet'],
                            'number_of_slides_mobile' => $items['number_of_slides_mobile'],
                            'column_width' => $items['column_width'],
                            'column_width_tablet' => $items['column_width_tablet'],
                            'column_width_mobile' => $items['column_width_mobile'],
                            'vertical_spacing' => $items['vertical_spacing'],
                            'horizontal_spacing' => $items['horizontal_spacing'],
                            'same_image_height' => $items['same_image_height'],
                        ));

                        break;
                    case 'buttons':
                        $buttons_alignment = $items['buttons_alignment'];
                        $html .= ____button_modules($items['buttons'], $buttons_alignment);
                        break;
                    case 'post_grid':
                        $html .= ____post_grid_module(array(
                            'id' => $id,
                            'is_slider' => $items['is_slider'],
                            'number_of_slides' => $items['number_of_slides'],
                            'number_of_slides_tablet' => $items['number_of_slides_tablet'],
                            'number_of_slides_mobile' => $items['number_of_slides_mobile'],
                            'post_box_styles' => $items['post_box_styles'],
                            'post_elements' => $items['post_elements'],
                            'post_type' => $items['post_type'],
                        ));
                        break;
                    case 'custom_html':
                        $html .= $items['custom_html'];
                        break;
                    case 'product_compare':
                        $compareproducts = $items['compareproducts'];
                        $compare_id = $compareproducts[0]['id'];
                        $html .= "[product_compare id='$compare_id']";
                        break;
                    case 'shortcode':
                        $shortcode = $items['shortcode'];
                        $html .= $shortcode;
                        break;
                    case 'product_slider':
                        $product_slider_args = [];
                        $heading = $items['heading'];
                        $button_text = $items['button_text'];
                        $button_url = $items['button_url'];
                        $product_cat = $items['source'];
                        $source_type = $items['source_type'];
                        $products = $items['products'];
                        $numberposts = $items['numberposts'];
                        $brands = $items['brand'];

                        $product_slider_args['numberposts'] = $numberposts ? $numberposts : -1;
                        $product_slider_args['post_type'] = 'product';
                        $product_slider_args['fields'] = 'ids';
                        $product_slider_args['post_status'] = 'publish';

                        if ($source_type == 'category') {
                            $term_ids = [];
                            foreach ($product_cat as $cat) {
                                $term_ids[] = $cat['id'];
                            }
                            $product_slider_args['tax_query']['relation'] = 'AND';

                            $product_slider_args['tax_query'][] = array(
                                'taxonomy' => 'product_cat',
                                'field'    => 'term_id',
                                'terms'    => $term_ids
                            );

                            if ($brands) {
                                $brand_ids = [];
                                foreach ($brands as $brand) {
                                    $brand_ids[] = $brand['id'];
                                }
                                $product_slider_args['tax_query'][] = array(
                                    'taxonomy' => 'pa_brands',
                                    'field'    => 'term_id',
                                    'terms'    => $brand_ids
                                );
                            }
                        } else if ($source_type == 'manually') {
                            $include = [];
                            foreach ($products as $product) {
                                $include[] = $product['id'];
                            }
                            $product_slider_args['include'] = $include;
                        } else {
                            $term_id = get_queried_object()->term_id;
                            $product_slider_args['tax_query'][] = array(
                                'taxonomy' => 'product_cat',
                                'field'    => 'term_id',
                                'terms'    => $term_id
                            );
                        }
                        $products = get_posts($product_slider_args);
                        $html .= __linked_products($products, $button_text, $button_url, 'swiper-' . $section_id_val, $heading, true, false);
                        break;
                    case 'tabs':
                        $tabs = $items['tabs'];
                        $html .= ___tab_modules($tabs, $section_id_val);
                        break;
                    case 'case_study_slider':
                        $html .= do_shortcode("[case_study_slider_grid]");
                        break;
                    case 'global_widgets':
                        $global_widgets = $items['global_widgets'];
                        foreach ($global_widgets as $global_widget) {
                            $type = $global_widget['_type'];
                            switch ($type) {
                                case 'latest_from_coptrz':
                                    $html .= do_shortcode("[latest_from_coptrz]");
                                    break;
                                case 'case_study_slider':
                                    $html .= do_shortcode("[case_study_slider_grid]");
                                    break;
                                case 'reviews':
                                    $html .= do_shortcode("[reviews]");
                                    break;
                                case 'drone_servicing':
                                    $html .= do_shortcode("[drone_servicing]");
                                    break;
                                case 'three_year_servicing_plans':
                                    $html .= do_shortcode("[three_year_servicing_plans]");
                                    break;
                                case 'remote_support':
                                    $html .= do_shortcode("[remote_support]");
                                    break;
                                case 'brands_logo_slider':
                                    $html .= do_shortcode("[brands_logo_slider]");
                                    break;
                                case 'testimonials':
                                    $html .= do_shortcode("[testimonials]");
                                    break;
                            }
                        }
                        break;

                    case 'global_post_box_selection':
                        $global_col_class = [];
                        $posts = $items['post'];
                        $source = $items['source'];
                        $category = $items['category'];
                        $column_width = $items['column_width'];
                        $column_width_tablet = $items['column_width_tablet'];
                        $column_width_mobile = $items['column_width_mobile'];

                        if ($column_width) {
                            $global_col_class[] = $column_width;
                        }
                        if ($column_width_tablet) {
                            if (count($posts) == 3 && $column_width_tablet == 'col-md-6') {
                                $global_col_class[] = 'col-md-12';
                            } else {
                                $global_col_class[] = $column_width_tablet;
                            }
                        }
                        if ($column_width_mobile) {
                            $global_col_class[] = $column_width_mobile;
                        }

                        $category_arr = [];
                        $posts_list = [];
                        foreach ($category as $cat) {
                            $category_arr[] = $cat['id'];
                        }
                        if ($source == 'category') {
                            $args = array(
                                'post_type' => 'globalpostboxes',
                                'post_status' => 'publish',
                                'fields' => 'ids',
                                'exclude' => get_the_ID(),
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'global_post_boxes_category',
                                        'field'    => 'term_id',
                                        'terms'    => $category_arr
                                    )
                                )
                            );
                            $posts = get_posts($args);
                            foreach ($posts as $post) {
                                $posts_list[]['id'] = $post;
                            }
                        } else {
                            $posts_list = $posts;
                        }

                        $html .= "<div class='row g-4 justify-content-center same-image-height row-global-post' style='--image-padding: 35%'>";

                        foreach ($posts_list as $post) {
                            $data = array(
                                'id' => $post['id'],
                                'featured' => false,
                                'tag' => 'h4',
                                'description_class' => 'excerpt-no-limit mb-0__related_posts',
                                'elements' => array('image', 'title', 'content'),
                            );
                            if ($global_col_class) {
                                $data['col'] = $global_col_class;
                            } else {
                                $data['col'] = 'col-lg-4 col-md-6';
                            }

                            $html .= __post_box($data);
                        }

                        $html .= "</div>";

                        break;
                    case 'related_post':

                        $source = $items['source'];

                        if ($source == 'post_type') {
                            $posts = [];
                            $field_key = $items['related_post'][0]['field_key'];
                            $post_type = $items['related_post'][0]['post_type'];
                            $name = get_post_type_object($post_type)->labels->singular_name;

                            $posts_list = get__post_meta($field_key);
                            foreach ($posts_list as $post) {
                                $post_status = get_post_status($post['id']);
                                if ($post_status == 'publish') {
                                    $posts[] = $post['id'];
                                }
                            }
                            if ($post_type != 'industries') {
                                $taxonomy = $post_type . '_category';
                            } else {
                                $taxonomy =  false;
                            }
                        } else {
                            $args['post_type'] = get_post_type();
                            $args['exclude'] = get_the_ID();
                            $taxonomy = get_post_type() . '_category';
                            $terms = get_the_terms(get_the_ID(), $taxonomy);
                            $terms_arr = [];
                            foreach ($terms as $term) {
                                $terms_arr = $term->term_id;
                            }
                            $args['tax_query'][] =  array(
                                'taxonomy' => $taxonomy,
                                'field'    => 'term_id',
                                'terms'    => $terms_arr
                            );

                            $name = get_post_type_object(get_post_type())->labels->singular_name;
                            $args['post_status'] = 'publish';
                            $args['numberposts'] = 3;
                            $args['orderby'] = 'rand';
                            $args['fields'] = 'ids';


                            $posts = get_posts($args);
                        }

                        if ($posts) {
                            $html .= "<div class='row g-4 same-image-height row-global-post'>";
                            foreach ($posts as $post) {
                                $data = array(
                                    'id' => $post,
                                    'featured' => false,
                                    'col' => true,
                                    'taxonomy' => $taxonomy,
                                    'button_text' => 'Read ' . $name,
                                    'elements' => array('category', 'image', 'title', 'excerpt', 'button')
                                );
                                $html .= __post_box($data);
                            }

                            $html .= "</div>";
                        }

                        break;

                    case 'related_products':
                        $type = $items['category'][0]['_type'];
                        if ($type == 'related_drones') {
                            $related_products = get__post_meta('drones');
                            $related_products_heading = 'Drones';
                            $related_id = 'Related-Drones';
                            $slider_id = 'Related-Drones-Slider';
                            $button_text = 'All Drones';
                            $button_link = '/product-category/drones/';
                        } else if ($type == 'related_payloads') {
                            $related_products = get__post_meta('payloads');
                            $related_products_heading = 'Payloads';
                            $related_id = 'Related-Payloads';
                            $slider_id = 'Related-Payloads-Slider';
                            $button_text = 'All Payloads';
                            $button_link = '/product-category/payloads-and-attachments/';
                        } else if ($type == 'related_accessories') {
                            $related_products = get__post_meta('accessories');
                            $related_products_heading = 'Accessories';
                            $related_id = 'Related-Accessories';
                            $slider_id = 'Related-Accessories-Slider';
                            $button_text = 'All Accessories';
                            $button_link = '/product-category/accessories-and-parts/';
                        } else {
                        }

                        if ($related_products) {
                            $related_products_array = array();
                            foreach ($related_products as $related_product) {
                                $post_status = get_post_status($related_product['id']);
                                if ($post_status == 'publish') {
                                    $related_products_array[] = $related_product['id'];
                                }
                            }
                            $html .= __linked_products($related_products_array, $button_text, $button_link, $slider_id, $related_products_heading, false, true, true, $related_id);
                        }
                        break;
                    case 'events_widget':
                        $events_widget = $items['events_widget'];
                        foreach ($events_widget as $event_widget) {
                            $type = $event_widget['_type'];
                            switch ($type) {
                                case 'countdown':
                                    $html .= do_shortcode('[event_countdown]');
                                    break;
                            }
                        }
                        break;
                }
            }
            if (count($container_classes) > 1) {
                $html .= "</div>";
            }
            $html .= "</div>";
            $html .= "</section>";
        }
    }
    return $html;
}

function ___tab_modules($tabs, $id)
{
    if ($tabs) {
        $html = "<div class='tabs-holder'>";
        $html .= "<ul class='nav nav-tabs' id='tab-$id' role='tablist'>";
        foreach ($tabs as $key => $tab) {
            $class = $key == 0 ? 'active' : '';
            $selected = $key == 0 ? 'true' : 'false';
            $heading = $tab['heading'];
            $html .= "<li class='nav-item' role='presentation'>";
            $html .= "<button class='nav-link $class' id='tab-$key' data-bs-toggle='tab' data-bs-target='#tab-$key-content' type='button' role='tab' aria-controls='tab-$key-content' aria-selected='$selected'>$heading</button>";
            $html .= "</li>";
        }
        $html .= "</ul>";

        $html .= "<div class='tab-content' id='tab-$id-content'>";
        foreach ($tabs as $key => $tab) {
            $class = $key == 0 ? 'show active' : '';

            $description_args['description'] =  $tab['description'];
            $description_args['class'] =  _attribute('class', array('description-box'));
            $html .= "<div class='tab-pane fade $class' id='tab-$key-content' role='tabpanel' aria-labelledby='tab-$key'>";
            $html .= __description($description_args);
            $html .= "</div>";
        }
        $html .= "</div>";

        $html .= "</div>";
        return $html;
    }
}
function ____post_grid_module($data)
{
    $is_slider = $data['is_slider'];
    $number_of_slides = isset($data['number_of_slides']) ? $data['number_of_slides'] : 1;
    $number_of_slides_tablet = isset($data['number_of_slides_tablet']) ? $data['number_of_slides_tablet'] : 1;
    $number_of_slides_mobile = isset($data['number_of_slides_mobile']) ? $data['number_of_slides_mobile'] : 1;
    $id = isset($data['id']) ? $data['id'] : '';
    $post_box_styles = isset($data['post_box_styles']) ? $data['post_box_styles'] : false;
    $post_elements = isset($data['post_elements']) ? $data['post_elements'] : false;
    $post_type = isset($data['post_type'][0]['_type']) ? $data['post_type'][0]['_type'] : false;
    $source = isset($data['post_type'][0]['source']) ? $data['post_type'][0]['source'] : false;
    $styles_val = '';
    $column_classes_val  = '';
    $post_grid_id = isset($data['post_grid_id']) ? $data['post_grid_id'] : '';


    $args['post_type'] = $post_type;
    $args['posts_per_page'] = -1;

    if ($source == 'category') {
        $category_ids = array();
        $categories = $data['post_type'][0]['category'];
        $taxonomy_key = $data['post_type'][0]['taxonomy_key'];
        foreach ($categories as $category) {
            $category_ids[] = $category['id'];
        }
        $args['tax_query'] =  array(
            array(
                'taxonomy' => $taxonomy_key,
                'field' => 'id',
                'terms' => $category_ids,
            )
        );
    } else if ($source == 'manually') {
        $posts = $data['post_type'][0]['post'];
        $posts_ids = array();
        foreach ($posts as $post) {
            $posts_ids[] = $post['id'];
        }
        $args['post__in'] = $posts_ids;
        $args['orderby'] = 'post__in';
    }
    // Get the posts
    $posts_lists = get_posts($args);


    $classes[] = 'column-holder';
    $classes[] = 'position-relative';

    $styles = array();
    $classes = array();
    $column_classes = array();
    if ($post_box_styles) {
        foreach ($post_box_styles as $post_box_style) {
            $type = $post_box_style['_type'];
            switch ($type) {
                case 'padding':
                    $classes[] = $post_box_style['padding_top'];
                    $classes[] = $post_box_style['padding_bottom'];
                    $classes[] = $post_box_style['padding_left'];
                    $classes[] = $post_box_style['padding_right'];
                    break;
                case 'margin':
                    $classes[] = $post_box_style['margin_top'];
                    $classes[] = $post_box_style['margin_bottom'];
                    $classes[] = $post_box_style['margin_left'];
                    $classes[] = $post_box_style['margin_right'];
                    break;
                case 'custom_class':
                    $classes[] = $post_box_style['custom_class'];
                    break;
                case 'alignment':
                    $classes[] = $post_box_style['align_items'];
                    $classes[] = $post_box_style['justify_content'];
                    $classes[] = $post_box_style['text_align'];
                    if ($post_box_style['align_items'] || $post_box_style['justify_content']) {
                        $classes[] = 'd-flex flex-column';
                    }
                    break;
                case 'text_color':
                    $text_color_custom = $post_box_style['text_color_custom'];
                    $classes[] = $post_box_style['text_color'];
                    if ($text_color_custom) {
                        $styles[] = 'color: ' . $text_color_custom;
                    }
                    break;
                case 'background_color':
                    $background_color_custom = $post_box_style['background_color_custom'];
                    $classes[] = $post_box_style['background_color'];
                    if ($background_color_custom) {
                        $styles[] = 'background-color: ' . $background_color_custom;
                    }
                    break;
                case 'border':
                    $border_radius = $post_box_style['border_radius'];
                    if ($border_radius) {
                        if ($border_radius == 'custom') {
                            $border_radius_custom = $post_box_style['border_radius_custom'];
                            $styles_section[] = "border-radius: $border_radius_custom";
                        } else {
                            $classes[] = $border_radius;
                        }
                    }
                    $border_style = $post_box_style['border_style'];
                    if ($border_style) {
                        if ($border_style == 'border-custom') {
                            $border_color = $post_box_style['border_color'];
                            $border_width = $post_box_style['border_width'];
                            if ($border_color == 'border-custom-color') {
                                $border_color_custom = $post_box_style['border_color_custom'];
                                $styles_section[] = "border-color: $border_color_custom";
                            } else {
                                $classes[] = $border_color;
                            }

                            if ($border_width == 'custom') {
                                $border_width_top =  $post_box_style['border_width_top'];
                                $border_width_right =  $post_box_style['border_width_right'];
                                $border_width_bottom =  $post_box_style['border_width_bottom'];
                                $border_width_left =  $post_box_style['border_width_left'];
                                $classes[] = 'border-width-custom';

                                if ($border_width_top) {
                                    $styles_section[] = "border-top-width: $border_width_top";
                                }
                                if ($border_width_right) {
                                    $styles_section[] = "border-right-width: $border_width_right";
                                }
                                if ($border_width_bottom) {
                                    $styles_section[] = "border-bottom-width: $border_width_bottom";
                                }
                                if ($border_width_left) {
                                    $styles_section[] = "border-left-width: $border_width_left";
                                }
                            } else {
                                $classes[] = 'border-default';
                            }
                        } else {
                            $classes[] = $border_style;
                        }
                    }
                    break;
                case 'column_width':
                    $column_classes[] = $post_box_style['column_width'];
                    if (count($posts_lists) == 3 && $post_box_style['column_width_tablet'] == 'col-md-6') {
                        $column_classes[] = 'col-md-12';
                    } else {
                        $column_classes[] = $post_box_style['column_width_tablet'];
                    }
                    $column_classes[] = $post_box_style['column_width_mobile'];
                    $column_classes[] = count($posts_lists);
                    break;
            }
        }
    }
    $classes[] = 'column-holder position-relative overflow-hidden content-margin h-100';

    if ($styles) {
        $styles_val = _attribute('style', $styles, ';');
    }


    if ($classes) {
        $classes_val = _attribute('class', $classes, ' ');
    }


    if ($is_slider) {
        $column_classes[] = 'swiper-inner h-100';
    }
    if ($column_classes) {
        $column_classes_val = _attribute('class', $column_classes, ' ');
    }


    $post_attribute = _attributes(array($classes_val, $styles_val));
    $column_attribute = _attributes(array($column_classes_val));

    $html = '';
    $html .= "<div class='post-grid' id='$post_grid_id'>";

    if ($is_slider) {
        $swiper_id = $id . '-swiper';
        $number_of_slides_attr = _attribute('number_of_slides', array($number_of_slides));
        $number_of_slides_tablet_attr = _attribute('number_of_slides_tablet', array($number_of_slides_tablet));
        $number_of_slides_mobile_attr = _attribute('number_of_slides_mobile', array($number_of_slides_mobile));
        $slides_attr = _attributes(array($number_of_slides_attr, $number_of_slides_tablet_attr, $number_of_slides_mobile_attr));

        $html .= "<div class='swiper-holder post-grid'>"; //swiper-holder
        $html .= "<div class='swiper swiper-sliders' id='$swiper_id' $slides_attr>"; //swiper
        $html .= "<div class='swiper-wrapper'>"; //swiper-wrapper
    } else {
        $html .= "<div class='row g-4 g-xs-10px same-image-height'>"; //row
    }
    $post_title = '';
    foreach ($posts_lists as $post) {
        if ($is_slider) {
            $html .= "<div class='swiper-slide'>";
        }
        $html .= "<div $column_attribute>";
        $html .= "<div $post_attribute>";
        foreach ($post_elements as $item) {
            $type = $item['_type'];
            switch ($type) {
                case 'post_title':
                    $post_title = '';
                    $tag =  $item['tag'] ? $item['tag'] : 'h3';
                    $text_before = $item['text_before'];
                    $text_after = $item['text_after'];
                    if ($text_before) {
                        $post_title .= $text_before;
                    }
                    $post_title .= $post->post_title;
                    if ($text_after) {
                        $post_title .= $text_after;
                    }
                    $html .= __heading(array(
                        'tag' => $tag,
                        'heading' => $post_title,
                        'class' => _attribute('class', array('post-title position-relative'))
                    ));
                    break;
                case 'permalink':
                    $hide_button_on_mobile = $item['hide_button_on_mobile'];
                    $button_class = $hide_button_on_mobile ? ' d-none d-md-block' : '';
                    $html .= __button(array(
                        'button_type' => get_post_type($post->ID),
                        'button_text' => $post_title,
                        'button_url' => $post->ID,
                        'button_style' => 'position-absolute',
                    ));
                    $html .= __button(array(
                        'button_type' => get_post_type($post->ID),
                        'button_text' => $item['button_text'],
                        'button_url' => $post->ID,
                        'button_style' => $item['button_style'] .  " position-relative$button_class",
                    ));
                    break;
                case 'featured_image':
                    $is_background_image = $item['is_background_image'];
                    $image_args['featured_image'] = $post->ID;
                    $image_args['size'] = $item['size'];
                    if ($is_background_image) {
                        $image_args['class'] = _attribute('class', array('background-image', 'background-overlay'));
                    } else {
                        $image_args['class'] = _attribute('class', array('image-box'));
                    }
                    $html .= __image($image_args);

                    break;
                case 'post_excerpt':
                    $html .= __description(array(
                        'description' => get_the_excerpt($post->ID),
                        'class' => _attribute('class', array('description-box')),
                    ));
                    break;

                case 'icon':
                    $html .= _____icon_modules($item);
                    break;
                case 'custom_field_1':
                    $custom_field_key =  $item['custom_field_key'];
                    $custom_field_type =  $item['custom_field_type'];
                    $custom_field_class =  $item['custom_field_class'];
                    $html .= _custom_field(array(
                        'id' => $post->ID,
                        'custom_field_key' => $custom_field_key,
                        'custom_field_type' => $custom_field_type,
                        'custom_field_class' => $custom_field_class
                    ));
                    break;
                case 'custom_field_2':
                    $custom_field_key =  $item['custom_field_key'];
                    $custom_field_type =  $item['custom_field_type'];
                    $custom_field_class =  $item['custom_field_class'];
                    $html .= _custom_field(array(
                        'id' => $post->ID,
                        'custom_field_key' => $custom_field_key,
                        'custom_field_type' => $custom_field_type,
                        'custom_field_class' => $custom_field_class
                    ));
                    break;
                case 'custom_field_3':
                    $custom_field_key =  $item['custom_field_key'];
                    $custom_field_type =  $item['custom_field_type'];
                    $custom_field_class =  $item['custom_field_class'];
                    $html .= _custom_field(array(
                        'id' => $post->ID,
                        'custom_field_key' => $custom_field_key,
                        'custom_field_type' => $custom_field_type,
                        'custom_field_class' => $custom_field_class
                    ));
                    break;
                case 'custom_field_4':
                    $custom_field_key =  $item['custom_field_key'];
                    $custom_field_type =  $item['custom_field_type'];
                    $custom_field_class =  $item['custom_field_class'];
                    $html .= _custom_field(array(
                        'id' => $post->ID,
                        'custom_field_key' => $custom_field_key,
                        'custom_field_type' => $custom_field_type,
                        'custom_field_class' => $custom_field_class
                    ));
                    break;
                case 'custom_field_5':
                    $custom_field_key =  $item['custom_field_key'];
                    $custom_field_type =  $item['custom_field_type'];
                    $custom_field_class =  $item['custom_field_class'];
                    $html .= _custom_field(array(
                        'id' => $post->ID,
                        'custom_field_key' => $custom_field_key,
                        'custom_field_type' => $custom_field_type,
                        'custom_field_class' => $custom_field_class
                    ));
                    break;
            }
        }
        $html .= "</div>";
        $html .= "</div>";
        if ($is_slider) {
            $html .= "</div>";
        }
    }
    $html .= "</div>";
    if ($is_slider) {
        $html .= '</div>'; //end swiper-wrapper
        $html .= '</div>'; //end swiper
        $html .= '<div class="swiper-nav d-none d-md-flex justify-content-start">'; // swipernav
        $html .= '<div class="swiper-button-prev"></div>';
        $html .= '<div class="swiper-button-next"></div>';
        $html .= '</div>'; //end swipernav
        $html .= '<div class="swiper-pagination d-flex d-md-none"></div>';
        $html .= '</div>'; //end swiper-holder
    } else {
        $html .= "</div>"; //end-row
    }

    return $html;
}
function _custom_field($data, $html = '')
{

    $id =  $data['id'];
    $custom_field_key =  $data['custom_field_key'];
    $custom_field_type =  $data['custom_field_type'];
    $custom_field_class =  $data['custom_field_class'];
    $val = get_post_meta($id, $custom_field_key, true);

    if ($custom_field_type != 'img') {
        $html .= "<$custom_field_type class='$custom_field_class'>$val</$custom_field_type>";
    } else if ($custom_field_type == 'img') {
        $image_args['image_id'] = $val;
        $image_args['class'] = _attribute('class', $custom_field_class);
        $html .= __image($image_args);
    }
    return $html;
}
function ____button_modules($buttons, $buttons_alignment = '')
{
    if ($buttons) {
        $html = "<div class='button-group-box $buttons_alignment'>";
        $html .= "<div class='row g-3 justify-content-center d-inline-flex'>";
        foreach ($buttons as $button) {
            $html .= __button(array(
                'button_type' => $button['button_type'],
                'button_text' => $button['button_text'],
                'button_url' => $button['button_url'],
                'button_url_custom' => $button['button_url_custom'],
                'button_style' => $button['button_style'] . ' col-auto',
                'button_target' => $button['button_target'],
            ));
        }
        $html .= "</div>";
        $html .= "</div>";
        return $html;
    }
}
function ____gallery_modules($data)
{
    $id = $data['id'];
    $gallery = $data['gallery'];
    $gallery_style = $data['gallery_style'];

    if ($gallery) {
        $html  = "<div class='gallery $gallery_style'>";

        if ($gallery_style == 'logo-slider') {
            $image_args['class'] = _attribute('class', array('swiper-slide'));
            $image_args['size'] = 'medium';

            $html .= "<div id='$id' class='swiper swiper-logo-slider'>";
            $html .= '<div class="swiper-wrapper align-items-center">';
        } else {
            $column_width = $data['column_width'] ? $data['column_width'] : 'col-auto';
            $column_width_tablet = $data['column_width_tablet'];
            $column_width_mobile = $data['column_width_mobile'];
            $vertical_spacing = $data['vertical_spacing'];
            $horizontal_spacing = $data['horizontal_spacing'];
            $same_image_height = $data['same_image_height'];
            $row_class[] = 'row justify-content-center align-items-center';
            if ($same_image_height) {
                $row_class[] = 'same-image-height';
            }
            if ($vertical_spacing) {
                $row_class[] = $vertical_spacing;
            }
            if ($horizontal_spacing) {
                $row_class[] = $horizontal_spacing;
            }

            if ($column_width) {
                $column_class_args[] = $column_width;
            }
            if ($column_width_tablet) {
                $column_class_args[] = $column_width_tablet;
            }
            if ($column_width_mobile) {
                $column_class_args[] = $column_width_mobile;
            }
            $image_args['class'] = _attribute('class', array('image-box rounded-corner'));
            $image_args['size'] = 'large';
            $column_grid_class = _attribute('class', $column_class_args);
            $row_class_val = _attribute('class', $row_class);
            $html .= "<div $row_class_val>";
        }

        foreach ($gallery as $image) {
            $image_args['image_id'] = $image;
            if ($gallery_style == 'logo-slider') {
                $html .= __image($image_args);
            } else {
                $html  .= "<div $column_grid_class>";
                $html .= __image($image_args);
                $html  .= "</div>";
            }
        }
        if ($gallery_style == 'logo-slider') {
            foreach ($gallery as $image) {
                $image_args['image_id'] = $image;

                $html .= __image($image_args);
            }
        }
        if ($gallery_style == 'logo-slider') {
            $html  .= "</div>";
            $html  .= "</div>";
        } else {
            $html  .= "</div>";
        }
        $html  .= "<div>";
    }

    return $html;
}
function ____columns_modules($items, $id, $html = '')
{
    $columns = $items['columns'];
    $column_styles = $items['column_styles'];
    $individual_column_settings = $items['individual_column_settings'];
    $is_slider = $items['is_slider'];
    $slider_style = $items['slider_style'];
    $number_of_slides = $items['number_of_slides'];
    $number_of_slides_tablet = $items['number_of_slides_tablet'];
    $number_of_slides_mobile = $items['number_of_slides_mobile'];
    $autoplay = $items['autoplay'] ? $items['autoplay'] : false;
    $autoplay_delay = $items['autoplay_delay'];
    $same_image_height = $items['same_image_height'];
    $horizontal_spacing = $items['horizontal_spacing'];
    $vertical_spacing = $items['vertical_spacing'];
    $mobile_styling = $items['mobile_styling'];
    $image_fit = $items['image_fit'];
    $image_padding = $items['image_padding'];
    $align_items = $items['align_items'];
    $justify_content = $items['justify_content'];
    $styles_val = '';
    $row_class = array();
    $column_class = array();
    $classes = array();
    $styles = array();

    if ($mobile_styling) {
        $classes[] = $mobile_styling;
    }

    if (!$individual_column_settings) {
        foreach ($column_styles as $column_style) {
            $type = $column_style['_type'];
            switch ($type) {
                case 'padding':
                    $classes[] = $column_style['padding_top'];
                    $classes[] = $column_style['padding_bottom'];
                    $classes[] = $column_style['padding_left'];
                    $classes[] = $column_style['padding_right'];
                    break;
                case 'margin':
                    $classes[] = $column_style['margin_top'];
                    $classes[] = $column_style['margin_bottom'];
                    $classes[] = $column_style['margin_left'];
                    $classes[] = $column_style['margin_right'];
                    break;
                case 'custom_class':
                    $classes[] = $column_style['custom_class'];
                    break;
                case 'alignment':
                    $classes[] = $column_style['align_items'];
                    $classes[] = $column_style['justify_content'];
                    $classes[] = $column_style['flex_direction'];

                    $text_align = $column_style['text_align'];
                    $text_align_tablet = $column_style['text_align_tablet'];
                    $text_align_mobile = $column_style['text_align_mobile'];

                    $classes[] = $text_align;

                    if (!$text_align_tablet) {
                        if ($text_align == 'text-lg-start') {
                            $classes[] = 'text-md-start';
                        } else if ($text_align == 'text-lg-center') {
                            $classes[] = 'text-md-center';
                        } else if ($text_align == 'text-lg-end') {
                            $classes[] = 'text-md-end';
                        }
                    } else {
                        $classes[] = $text_align_tablet;
                    }


                    if (!$text_align_mobile) {
                        if ($text_align_tablet) {
                            if ($text_align_tablet == 'text-md-start') {
                                $classes[] = 'text-start';
                            } else if ($text_align_tablet == 'text-md-center') {
                                $classes[] = 'text-center';
                            } else if ($text_align_tablet == 'text-md-end') {
                                $classes[] = 'text-end';
                            }
                        } else {
                            if ($text_align == 'text-lg-start') {
                                $classes[] = 'text-start';
                            } else if ($text_align == 'text-lg-center') {
                                $classes[] = 'text-center';
                            } else if ($text_align == 'text-lg-end') {
                                $classes[] = 'text-end';
                            }
                        }
                    } else {
                        $classes[] = $text_align_mobile;
                    }

                    if ($column_style['align_items'] || $column_style['justify_content'] || $column_style['flex_direction']) {
                        $classes[] = 'd-flex flex-wrap';
                    }
                    break;
                case 'text_color':
                    $text_color_custom = $column_style['text_color_custom'];
                    $classes[] = $column_style['text_color'];
                    if ($text_color_custom) {
                        $styles[] = 'color: ' . $text_color_custom;
                    }
                    break;
                case 'background_color':
                    $background_color_custom = $column_style['background_color_custom'];
                    $classes[] = $column_style['background_color'];
                    if ($background_color_custom) {
                        $styles[] = 'background-color: ' . $background_color_custom;
                    }
                    break;
                case 'background_image':
                    $background_image = $column_style['background_image'];
                    $classes[] = $column_style['background_attachment'];
                    $classes[] = $column_style['background_size'];
                    $classes[] = $column_style['background_repeat'];
                    if ($background_image) {
                        $styles[] = 'background-image: url(' . wp_get_attachment_image_url($background_image, 'full') . ')';
                    }
                    break;

                case 'border':
                    $border_radius = $column_style['border_radius'];
                    if ($border_radius) {
                        if ($border_radius == 'custom') {
                            $border_radius_custom = $column_style['border_radius_custom'];
                            $styles[] = "border-radius: $border_radius_custom";
                        } else {
                            $classes[] = $border_radius;
                        }
                    }
                    $border_style = $column_style['border_style'];
                    if ($border_style) {
                        if ($border_style == 'border-custom') {
                            $border_color = $column_style['border_color'];
                            $border_width = $column_style['border_width'];
                            if ($border_color == 'border-custom-color') {
                                $border_color_custom = $column_style['border_color_custom'];
                                $styles[] = "border-color: $border_color_custom";
                            } else {
                                $classes[] = $border_color;
                            }

                            if ($border_width == 'custom') {
                                $border_width_top =  $column_style['border_width_top'];
                                $border_width_right =  $column_style['border_width_right'];
                                $border_width_bottom =  $column_style['border_width_bottom'];
                                $border_width_left =  $column_style['border_width_left'];
                                $classes[] = 'border-width-custom';

                                if ($border_width_top) {
                                    $styles[] = "border-top-width: $border_width_top";
                                }
                                if ($border_width_right) {
                                    $styles[] = "border-right-width: $border_width_right";
                                }
                                if ($border_width_bottom) {
                                    $styles[] = "border-bottom-width: $border_width_bottom";
                                }
                                if ($border_width_left) {
                                    $styles[] = "border-left-width: $border_width_left";
                                }
                            } else {
                                $classes[] = 'border-default';
                            }
                        } else {
                            $classes[] = $border_style;
                        }
                    }

                    break;
                case 'column_width':
                    $column_width = $column_style['column_width'];
                    $column_width_tablet = $column_style['column_width_tablet'];
                    $column_width_mobile = $column_style['column_width_mobile'];
                    if ($column_width) {
                        $column_class[] = $column_width;
                    }
                    if ($column_width_tablet) {
                        $column_class[] = $column_width_tablet;
                    }
                    if ($column_width_mobile) {
                        $column_class[] = $column_width_mobile;
                    }

                    break;
            }
        }
    }



    if ($is_slider) {
        $swiper_id = $id . '-swiper';
        $number_of_slides_attr = _attribute('number_of_slides', array($number_of_slides));
        $number_of_slides_tablet_attr = _attribute('number_of_slides_tablet', array($number_of_slides_tablet));
        $number_of_slides_mobile_attr = _attribute('number_of_slides_mobile', array($number_of_slides_mobile));
        $autoplay = _attribute('autoplay', array($autoplay));
        $slides_attr = _attributes(array($number_of_slides_attr, $number_of_slides_tablet_attr, $number_of_slides_mobile_attr, $autoplay));

        $html .= "<div class='swiper-holder $slider_style'>"; //swiper-holder
        $html .= "<div class='swiper swiper-sliders' id='$swiper_id' $slides_attr>"; //swiper
    }



    if ($is_slider) {
        $html .= "<div class='swiper-wrapper'>"; //swiper-wrapper

    } else {
        $row_class[] = 'row';

        if (count($columns) > 2) {
            $row_class[]  = 'g-xs-10px';
        }
        if ($align_items) {
            $row_class[] = $align_items;
        }
        if ($justify_content) {
            $row_class[] = $justify_content;
        }

        if ($horizontal_spacing) {
            $row_class[] = $horizontal_spacing;
        }

        if ($vertical_spacing) {
            $row_class[] = $vertical_spacing;
        }

        if (!$vertical_spacing && !$horizontal_spacing) {
            $row_class[] = 'g-4';
        }

        $row_class_val = _attribute('class', $row_class, ' ');
        $row_class_attr = _attributes(array($row_class_val));
        $html .= "<div $row_class_attr>"; //row
    }




    foreach ($columns as $key => $column) {
        $items = $column['items'];
        $column_id = $column['column_id'];
        if ($individual_column_settings) {
            $classes = array();
            $styles = array();
            $column_class = array();

            $column_styles = $column['column_styles'];
            foreach ($column_styles as $column_style) {
                $type = $column_style['_type'];
                switch ($type) {
                    case 'padding':
                        $classes[] = $column_style['padding_top'];
                        $classes[] = $column_style['padding_bottom'];
                        $classes[] = $column_style['padding_left'];
                        $classes[] = $column_style['padding_right'];
                        break;
                    case 'margin':
                        $classes[] = $column_style['margin_top'];
                        $classes[] = $column_style['margin_bottom'];
                        $classes[] = $column_style['margin_left'];
                        $classes[] = $column_style['margin_right'];
                        break;
                    case 'custom_class':
                        $classes[] = $column_style['custom_class'];
                        break;
                    case 'alignment':
                        $classes[] = $column_style['align_items'];
                        $classes[] = $column_style['justify_content'];
                        $classes[] = $column_style['flex_direction'];

                        $text_align = $column_style['text_align'];
                        $text_align_tablet = $column_style['text_align_tablet'];
                        $text_align_mobile = $column_style['text_align_mobile'];

                        $classes[] = $text_align;

                        if (!$text_align_tablet) {
                            if ($text_align == 'text-lg-start') {
                                $classes[] = 'text-md-start';
                            } else if ($text_align == 'text-lg-center') {
                                $classes[] = 'text-md-center';
                            } else if ($text_align == 'text-lg-end') {
                                $classes[] = 'text-md-end';
                            }
                        } else {
                            $classes[] = $text_align_tablet;
                        }


                        if (!$text_align_mobile) {
                            if ($text_align_tablet) {
                                if ($text_align_tablet == 'text-md-start') {
                                    $classes[] = 'text-start';
                                } else if ($text_align_tablet == 'text-md-center') {
                                    $classes[] = 'text-center';
                                } else if ($text_align_tablet == 'text-md-end') {
                                    $classes[] = 'text-end';
                                }
                            } else {
                                if ($text_align == 'text-lg-start') {
                                    $classes[] = 'text-start';
                                } else if ($text_align == 'text-lg-center') {
                                    $classes[] = 'text-center';
                                } else if ($text_align == 'text-lg-end') {
                                    $classes[] = 'text-end';
                                }
                            }
                        } else {
                            $classes[] = $text_align_mobile;
                        }

                        if ($column_style['align_items'] || $column_style['justify_content'] || $column_style['flex_direction']) {
                            $classes[] = 'd-flex flex-wrap';
                        }
                        break;
                    case 'text_color':
                        $text_color_custom = $column_style['text_color_custom'];
                        $classes[] = $column_style['text_color'];
                        if ($text_color_custom) {
                            $styles[] = 'color: ' . $text_color_custom;
                        }
                        break;
                    case 'background_color':
                        $background_color_custom = $column_style['background_color_custom'];
                        $classes[] = $column_style['background_color'];
                        if ($background_color_custom) {
                            $styles[] = 'background-color: ' . $background_color_custom;
                        }
                        break;
                    case 'background_image':
                        $background_image = $column_style['background_image'];
                        $classes[] = $column_style['background_attachment'];
                        $classes[] = $column_style['background_size'];
                        $classes[] = $column_style['background_repeat'];
                        if ($background_image) {
                            $styles[] = 'background-image: url(' . wp_get_attachment_image_url($background_image, 'full') . ')';
                        }
                        break;

                    case 'border':
                        $border_radius = $column_style['border_radius'];
                        if ($border_radius) {
                            if ($border_radius == 'custom') {
                                $border_radius_custom = $column_style['border_radius_custom'];
                                $styles[] = "border-radius: $border_radius_custom";
                            } else {
                                $classes[] = $border_radius;
                            }
                        }
                        $border_style = $column_style['border_style'];
                        if ($border_style) {
                            if ($border_style == 'border-custom') {
                                $border_color = $column_style['border_color'];
                                $border_width = $column_style['border_width'];
                                if ($border_color == 'border-custom-color') {
                                    $border_color_custom = $column_style['border_color_custom'];
                                    $styles[] = "border-color: $border_color_custom";
                                } else {
                                    $classes[] = $border_color;
                                }

                                if ($border_width == 'custom') {
                                    $border_width_top =  $column_style['border_width_top'];
                                    $border_width_right =  $column_style['border_width_right'];
                                    $border_width_bottom =  $column_style['border_width_bottom'];
                                    $border_width_left =  $column_style['border_width_left'];
                                    $classes[] = 'border-width-custom';

                                    if ($border_width_top) {
                                        $styles[] = "border-top-width: $border_width_top";
                                    }
                                    if ($border_width_right) {
                                        $styles[] = "border-right-width: $border_width_right";
                                    }
                                    if ($border_width_bottom) {
                                        $styles[] = "border-bottom-width: $border_width_bottom";
                                    }
                                    if ($border_width_left) {
                                        $styles[] = "border-left-width: $border_width_left";
                                    }
                                } else {
                                    $classes[] = 'border-default';
                                }
                            } else {
                                $classes[] = $border_style;
                            }
                        }


                        break;
                    case 'column_width':
                        $column_width = $column_style['column_width'];
                        $column_width_tablet = $column_style['column_width_tablet'];
                        $column_width_mobile = $column_style['column_width_mobile'];
                        if ($column_width) {
                            $column_class[] = $column_width;
                        }
                        if ($column_width_tablet) {
                            $column_class[] = $column_width_tablet;
                        }
                        if ($column_width_mobile) {
                            $column_class[] = $column_width_mobile;
                        }

                        break;
                }
            }
        }

        $classes[] = 'column-holder content-margin overflow-hidden position-relative h-100';

        if ($same_image_height) {
            $classes[] = 'same-image-height';
            if ($image_fit) {
                $styles[] = "--object-fit: $image_fit;";
            }
            if ($image_padding) {
                $styles[] = "--image-padding: $image_padding;";
            }
        }



        if (!$column_class) {
            $column_class[] = 'col';
        }

        $column_class_val = _attribute('class', $column_class, ' ');
        $column_class_attr = _attributes(array($column_class_val));
        if ($styles) {
            $styles_val = _attribute('style', $styles, ';');
        }

        if ($classes) {
            $classes_val = _attribute('class', $classes, ' ');
        }


        if ($column_id) {
            $column_id_val = _attribute('id', $column_id, ' ');
        }
        $column_attributes = _attributes(array($classes_val, $styles_val, $column_id_val));


        if ($is_slider) {
            $html .= '<div class="swiper-slide">'; //swiper-slide
        } else {
            $html .= "<div $column_class_attr>"; //col
        }
        $html .= "<div $column_attributes>";
        foreach ($items as $item) {
            $type = $item['_type'];
            switch ($type) {
                case 'global_widgets':
                    $global_widgets = $item['global_widgets'];
                    foreach ($global_widgets as $global_widget) {
                        $type = $global_widget['_type'];
                        switch ($type) {
                            case 'latest_from_coptrz':
                                $html .= do_shortcode("[latest_from_coptrz]");
                                break;
                            case 'case_study_slider':
                                $html .= do_shortcode("[case_study_slider_grid]");
                                break;
                            case 'reviews':
                                $html .= do_shortcode("[reviews]");
                                break;
                            case 'drone_servicing':
                                $html .= do_shortcode("[drone_servicing]");
                                break;
                            case 'brands_logo_slider':
                                $html .= do_shortcode("[brands_logo_slider]");
                                break;
                            case 'testimonials':
                                $html .= do_shortcode("[testimonials]");
                                break;
                        }
                    }
                    break;
                case 'heading':
                    $html .= ____heading_modules($item);
                    break;
                case 'icon':
                    $html .= _____icon_modules($item);
                    break;
                case 'description':
                    $desc_classes = array();
                    $desc_styles = array();

                    $description_width = $item['description_width'];
                    $description_alignment = $item['description_alignment'];
                    $description_size = $item['description_size'];

                    $desc_classes[] = 'description-box';

                    if ($description_width) {
                        $desc_styles[] = "max-width: $description_width;";
                    }
                    if ($description_alignment) {
                        $desc_classes[] = "$description_alignment";
                    }
                    if ($description_size) {
                        $desc_classes[] = "$description_size";
                    }

                    $description_args['description'] =  $item['description'];
                    $description_args['class'] =  _attribute('class', $desc_classes);

                    if ($desc_styles) {
                        $description_args['style'] =  _attribute('style', $desc_styles);
                    }


                    $html .= __description($description_args);
                    break;
                case 'image':
                    $image_styles = array();
                    $image_classes  = array();

                    $is_background_image = $item['is_background_image'];
                    $custom_size = $item['custom_size'];
                    $image_height = $item['image_height'];
                    $image_width = $item['image_width'];
                    $rounded_corners = $item['rounded_corners'];
                    $border_radius = $item['border_radius'];
                    $image_args['image_id'] = $item['image'];
                    $image_args['size'] = $item['size'];


                    $image_classes[] = 'image-box';
                    if ($is_background_image) {
                        $image_classes[] = 'background-image background-overlay';
                    }
                    if ($custom_size) {
                        if ($image_height) {
                            $image_styles[] = "--height: $image_height;";
                        }
                        if ($image_width) {
                            $image_styles[] = "--width: $image_width;";
                        }
                    }
                    if ($rounded_corners) {
                        $image_classes[] = 'rounded-corner';
                        if ($border_radius) {
                            $image_styles[] = "--border-radius: $border_radius;";
                        }
                    }


                    $image_args['style'] = _attribute('style', $image_styles);
                    $image_args['class'] = _attribute('class', $image_classes);

                    $html .= __image($image_args);
                    break;
                case 'video':
                    $autoplay = $item['autoplay'] ? true : false;
                    $video_type = $item['video_type'];
                    $video = $item['video'];
                    $youtube_video_id = $item['youtube_video_id'];
                    $html .= __video(array(
                        'youtube_video_id' => $youtube_video_id,
                        'autoplay' => $autoplay,
                        'video_id' => $video,
                        'video_type' => $video_type,
                        'class' => _attribute('class', array('video-box rounded-corner overflow-hidden', $video_type))
                    ));
                    break;
                case 'gallery':
                    $html .= ____gallery_modules(array(
                        'id' => $id,
                        'gallery' => $item['gallery'],
                        'gallery_style' => $item['gallery_style'],
                        'number_of_slides' => $item['number_of_slides'],
                        'number_of_slides_tablet' => $item['number_of_slides_tablet'],
                        'number_of_slides_mobile' => $item['number_of_slides_mobile'],
                    ));
                    break;
                case 'buttons':
                    $html .= ____button_modules($item['buttons']);
                    break;

                case 'accordion':
                    $accordion = $item['accordion'];
                    $accordion_source = $item['accordion_source'];
                    $faqs = $item['faqs'];
                    $faqs_category = $item['faqs_category'];
                    $open_first_item = $item['open_first_item'];
                    $with_border = $item['with_border'];
                    $lower_opacity = $item['lower_opacity'];
                    $html .= __accordion_module(array(
                        'accordion' => $accordion,
                        'accordion_source' => $accordion_source,
                        'faqs' => $faqs,
                        'faqs_category' => $faqs_category,
                        'open_first_item' => $open_first_item,
                        'module_id' => $id,
                        'with_border' => $with_border,
                        'lower_opacity' => $lower_opacity
                    ));
                    break;
                case 'cf7':
                    $id = $item['form'][0]['id'];
                    $style = $item['style'];
                    $html .= "<div class='form-box $style'>";
                    $html .= do_shortcode("[contact-form-7 id='$id']");
                    $html .= "</div>";
                    break;
                case 'divider':
                    $divider_classes = array();
                    $divider_styles  = array();

                    $divider_classes[] = $item['margin_top'];
                    $divider_classes[] = $item['margin_bottom'];
                    $divider_classes[] = $item['margin_left'];
                    $divider_classes[] = $item['margin_right'];

                    $divider_classes[] = $item['border_color'];


                    $classes_val = _attribute('class', $divider_classes);

                    $html .= "<hr $classes_val>";
                    break;

                case 'post_grid':
                    if ($item['post_type']) {
                        $html .= ____post_grid_module(array(
                            'id' => $id,
                            'is_slider' => $item['is_slider'],
                            'number_of_slides' => $item['number_of_slides'],
                            'number_of_slides_tablet' => $item['number_of_slides_tablet'],
                            'number_of_slides_mobile' => $item['number_of_slides_mobile'],
                            'post_box_styles' => $item['post_box_styles'],
                            'post_elements' => $item['post_elements'],
                            'post_type' => $item['post_type'],
                        ));
                    }
                    break;
                case 'spec_box':
                    $spec_box = $item['spec_box'];
                    if ($spec_box) {
                        $html .= "<div class='row g-4'>";
                        foreach ($spec_box as $spec) {
                            $spec_label = $spec['spec_label'];
                            $spec_value = $spec['spec_value'];
                            $html .= "<div class='col-auto'>";
                            $html .= "<div class='spec-label small-text fw-medium text-uppercase'>$spec_label</div>";
                            $html .= "<div class='spec-value big-text'>$spec_value</div>";
                            $html .= "</div>";
                        }
                        $html .= "</div>";
                    }
                    break;
            }
        }
        $html .= '</div>'; //end column-holder
        $html .= '</div>'; //end col //end swiper-slide
    }
    $html .= '</div>'; //end row // end-swiper-wrapper
    if ($is_slider) {
        $html .= '</div>'; //end swiper
        $html .= '<div class="swiper-nav d-flex justify-content-start">'; // swipernav
        $html .= '<div class="swiper-button-prev"></div>';
        $html .= '<div class="swiper-button-next"></div>';
        $html .= '</div>'; //end swipernav
        $html .= '</div>'; //end swiper-holder
    }
    return $html;
}

function __accordion_module($data, $class = '')
{

    $module_id = isset($data['module_id']) ? $data['module_id'] : 'accordion';
    $faqs = isset($data['faqs']) ? $data['faqs'] : false;
    $accordion_source = isset($data['accordion_source']) ? $data['accordion_source'] : false;
    $faqs_category = isset($data['faqs_category']) ? $data['faqs_category'] : false;
    $faqs = isset($data['accordion_source']) ? $data['accordion_source'] : false;
    $accordion = isset($data['accordion']) ? $data['accordion'] : false;
    $open_first_item = isset($data['open_first_item']) ? $data['open_first_item'] : false;
    $lower_opacity = isset($data['lower_opacity']) ? $data['lower_opacity'] : false;
    $with_border = isset($data['with_border']) ? $data['with_border'] : false;
    $class = $with_border ? 'with-border' : '';
    if ($lower_opacity) {
        $class .= ' lower-opacity';
    }
    if ($accordion_source == 'faqs') {
        $accordion = array();
        foreach ($faqs as $faq) {
            $accordion[$faq['id']] = array(
                'heading' => get_the_title($faq['id']),
                'description' => get_the_content(null, false, $faq['id']),
            );
        }
    } else if ($accordion_source == 'faqs_category') {
        $faqs_cat_id = array();
        foreach ($faqs_category as $faqs_cat) {
            $faqs_cat_id[] = $faqs_cat['id'];
        }
        $args = array(
            'post_type' => 'faq',
            'post_status' => 'publish',
            'numberposts' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'faqs_category',
                    'field'    => 'term_id',
                    'terms'    => $faqs_cat_id
                )
            )
        );
        $faqs_lists = get_posts($args);
        $accordion = array();
        foreach ($faqs_lists as $faq) {
            $accordion[$faq->ID] = array(
                'heading' => $faq->post_title,
                'description' => $faq->post_content
            );
        }
    } else {
        $accordion = $accordion;
    }
    $html = "<div class='accordion $class accordion-flush' id='accordion-$module_id'>"; //accordion
    $index = 0;
    foreach ($accordion as $key => $accordion_item) {
        $heading = $accordion_item['heading'];
        $description = $accordion_item['description'];
        $button_class = $index == 0 && $open_first_item ? '' : 'collapsed';
        $content_class = $index == 0 && $open_first_item ? 'show' : '';
        $aria_expanded = $index == 0 && $open_first_item ? 'true' : 'false';
        $html .= "<div class='accordion-item position-relative mb-0'>"; //accordion-item
        $html .= "<h3 class='accordion-header' id='flush-heading-$key'>";
        $html .= "<button class='accordion-button justify-content-between px-0 py-3 $button_class' type='button' data-bs-toggle='collapse' data-bs-target='#flush-collapse-$key' aria-expanded='$aria_expanded' aria-controls='flush-collapse-$key'>";
        $html .= "<span> ";
        $html .= $heading;
        $html .= "</span> ";
        $html .= "<span class='plus-minus'></span>";
        $html .= "</button>";
        $html .= "</h3>";

        $html .= "<div id='flush-collapse-$key' class='accordion-collapse collapse $content_class' aria-labelledby='flush-heading-$key' data-bs-parent='#accordion-$module_id'>";
        $html .= __description(array(
            'description' => $description,
            'class' => _attribute('class', array('description-box small-text pb-3')),
        ));
        $html .= "</div>";
        $html .= "</div>"; //end-accordion-item
        $index++;
    }
    $html .= "</div>"; //end-accordion

    return $html;
}

function _____icon_modules($items)
{
    $icon_data['id'] = $items['icon'];
    $icon_color = $items['icon_color'];
    $icon_color_custom = $items['icon_color_custom'];
    $icon_width = $items['icon_width'];
    $icon_height = $items['icon_height'];
    $classes[] = 'icon-box';
    $styles = [];
    if ($icon_color) {
        $classes[] = $icon_color;
    }

    if ($icon_color == 'text-custom') {
        $styles[] = 'color: ' . $icon_color_custom;
    } else {
        if ($icon_color) {
            $classes[] = $icon_color;
        }
    }

    if ($icon_width) {
        $styles[] = '--width: ' . $icon_width;
    }
    if ($icon_height) {
        $styles[] = '--height: ' . $icon_height;
    }

    if ($classes) {
        $icon_data['class'] = _attribute('class', $classes);
    }
    if ($styles) {
        $icon_data['styles'] = _attribute('style', $styles, ';');
    }

    return __icon($icon_data);
}
function ____heading_modules($items)
{
    $has_suffix = $items['has_suffix'];
    $has_prefix = $items['has_prefix'];
    $has_custom_heading_settings = $items['has_custom_heading_settings'];
    $heading = $items['heading'];
    $prefix = $items['prefix'];
    $suffix = $items['suffix'];
    $tag = $items['tag'];
    $size = $items['size'];
    $text_color = $items['text_color'];
    $text_align = $items['text_align'];
    $text_color_custom = $items['text_color_custom'];
    $heading_data['heading'] = $heading;

    $classes = [];
    $styles = [];
    if ($has_custom_heading_settings) {
        if ($tag) {
            $heading_data['tag'] = $tag;
        }
        if ($size) {
            $classes[] = $size;
        }
        if ($text_align) {
            $classes[] = $text_align;
        }

        if ($text_color == 'text-custom') {
            $styles[] = 'color: ' . $text_color_custom;
        } else {
            if ($text_color) {
                $classes[] = $text_color;
            }
        }
    }


    if ($has_suffix && $suffix) {
        $heading_data['suffix'] = $suffix;
        $classes[] = 'heading-box';
    }
    if ($has_prefix && $prefix) {
        $heading_data['prefix'] = $prefix;
        $classes[] = 'heading-box';
    }
    if ($classes) {
        $heading_data['class'] = _attribute('class', $classes);
    }
    if ($styles) {
        $heading_data['styles'] = _attribute('style', $styles, ';');
    }
    return __heading($heading_data);
}

function _styles()
{
}
function _attribute($name, $attributes, $separator = ' ')
{
    $html = "$name='";
    if (is_array($attributes)) {
        $html .= implode($separator, array_unique($attributes));
    } else {
        $html .= $attributes;
    }
    $html .= "'";
    return $html;
}

function _attributes($attributes)
{
    $html = '';
    foreach ($attributes as $attribute) {
        $html .= $attribute;
    }

    return $html;
}



function _is_module($post_id = false)
{
    $id = $post_id ? $post_id : get_the_ID();
    if (get_page_template_slug($id) == 'templates/page-modules.php') {
        return true;
    } else {
        return false;
    }
}


function _output_svg_from_url($url)
{
    $content = file_get_contents($url);

    // Output the sanitized SVG
    return $content;
}

function __product_specifications($for_product_summary = false)
{
    $pa_specifications = get_the_terms(get_the_ID(), 'pa_specifications');
    if ($pa_specifications) {
        if ($for_product_summary == false) {
            $class = 'col-auto';
            $row_class = 'g-5';
            $html = '<section class="products-specifications mt-20px bg-light rounded-corner py-3 mx-20px">';
            $html .= '<div class="container-fluid">';
        } else {
            $class = 'col-6';
            $row_class = 'g-10px';
            $html = '<div class="products-specifications products-specifications-v2">';
        }
        $html .= "<div class='row $row_class justify-content-center'>";
        foreach ($pa_specifications as $specification) {
            $icon = get__term_meta($specification->term_id, 'icon');
            $mime_type =  get_post_mime_type($icon);

            $html .= "<div class='$class'>";
            $html .= '<div class="inner h-100 d-flex align-items-center">';
            if (str_contains($mime_type, 'svg')) {
                $html .= __icon(array(
                    'id' => $icon,
                    'class' => _attribute('class', array('me-3 text-accent'))
                ));
            } else {
                $html .= __image(array(
                    'image_id' => $icon,
                    'class' => _attribute('class', array('me-3 text-accent'))
                ));
            }
            $html .= __heading(array(
                'heading' => $specification->name,
                'class' => _attribute('class', array('mb-0')),
                'tag' => 'h5',
            ));
            $html .= '</div>';
            $html .= '</div>';
        }
        $html .= '</div>';
        if ($for_product_summary == false) {
            $html .= '</div>';
            $html .= '</section>';
        } else {
            $html .= '</div>';
        }
        return $html;
    }
}



function __post_box($data, $class = [], $content_box_class = [])
{
    $col = isset($data['col']) ? $data['col'] : 'false';
    $featured = isset($data['featured']) ? $data['featured'] : false;
    $style = isset($data['style']) ? $data['style'] : 'style-1';
    $taxonomy = isset($data['taxonomy']) ? $data['taxonomy'] : false;
    $button_text = isset($data['button_text']) ? $data['button_text'] : false;
    $id = isset($data['id']) ? $data['id'] : false;
    $elements = isset($data['elements']) ? $data['elements'] : array();
    $bg_image = isset($data['bg_image']) ? $data['bg_image'] : false;
    $is_new = isset($data['is_new']) ? $data['is_new'] : false;
    $background_class = isset($data['background_class']) ? $data['background_class'] : false;
    $tag = isset($data['tag']) ? $data['tag'] : 'h3';
    $description_class = isset($data['description_class']) ? $data['description_class'] : 'mb-4';
    $description_class_args[] = 'description-box small-text';
    $description_class_args[] = $description_class;
    $additional_content = isset($data['additional_content']) ? $data['additional_content'] : false;

    if ($background_class) {
        $class[] = $background_class;
        $class[] = 'rounded-corner';
        $class[] = 'p-20px d-flex flex-column justify-content-between';
    }
    $image = get_post_thumbnail_id($id);
    $date = _date_format(get_the_date('jS F Y', $id), true);

    $class[] = 'post-box column-holder position-relative overflow-hidden h-100';
    $content_box_class[] = 'content-box content-margin ';
    if ($featured) {
        $class[] = 'featured-box text-white d-flex flex-column justify-content-between p-20px rounded-10px';
    } else {
        $class[] = 'content-margin';
    }

    if ($style) {
        $class[] = $style;
    }

    if ($style == 'style-1' && $background_class == false) {
        $content_box_class[] = 'px-20px pb-20px';
    }
    $link = false;

    if (in_array('button', $elements)) {
        $link = $id;
    }

    if ($bg_image) {
        $class[] = 'xs-padding text-white text-center rounded-corner overflow-hidden h1-100 bg-black d-flex align-items-end justify-content-center';
    }
    $html = '';
    if ($col == true && $col != false && is_bool($col)) {
        $html = "<div class='col-lg-4 col-sm-6'>"; //col
    } else {
        $col_class = _attribute('class', $col);
        $html = "<div $col_class>"; //col
    }
    $class_attribute = _attribute('class', $class);
    $content_box_class_attribute = _attribute('class', $content_box_class);
    $html .= "<div $class_attribute>"; //inner

    if ($featured) {
        $html .= __background($image);
        if($taxonomy != false) {
            $html .= __post_category($id, $taxonomy, 'text-white');
        }
    } else {
        if ($bg_image) {
            $html .= __background($image);
            $content_box_class[] = "text-center xs-padding text-white";
        } else {
            $html .= __image(array(
                'image_id' => $image,
                'placeholder' => true,
                'size' => 'large',
                'class' => _attribute('class', array('image-box rounded-corner overflow-hidden')),
                'link' => $link
            ));
        }

        $html .= "<div $content_box_class_attribute>";
        if (in_array('category', $elements) && $taxonomy && $taxonomy != false) {
            $html .= __post_category($id, $taxonomy, 'text-black');
        }
    }

    if ($featured) {
        $html .= "<div class='content-box content-margin'>";
    }

    if ($is_new) {
        $html .= "<div class='bubble'>NEW</div>";
    }

    if (in_array('date', $elements)) {
        $html .= "<div class='date-box small-text mb-2'>$date</div>";
    }
    if (in_array('title', $elements)) {
        $html .= __heading(array(
            'heading' => get_the_title($id),
            'tag' => $tag,
            'link' => $link
        ));
    }
    /*
    if (get_post_type($id) == 'product') {
        $product = wc_get_product($id);
        $html .= "<div class='price-box'>";
        $html .= $product->get_price_html();
        $html .= '</div>';
    }*/

    if (in_array('excerpt', $elements)) {
        $html .= __description(array(
            'description' => wpautop(get_the_excerpt($id)),
            'class' => _attribute('class', $description_class_args),
        ));
    }
    if (in_array('content', $elements)) {
        $html .= __description(array(
            'description' => wpautop(get_the_content(NULL, false, $id)),
            'class' => _attribute('class', $description_class_args),
            'autop' => false
        ));
    }

    if ($additional_content) {
        $html .= $additional_content;
    }
    if (in_array('button', $elements)) {
        $html .= __button(array(
            'button_type' => get_post_type($id),
            'button_text' => $button_text ? $button_text : 'Read More',
            'button_url' => $id,
            'button_style' =>  'button-bordered position-relative',
        ));
    }
    $html .= "</div>";
    $html .= "</div>"; //inner
    $html .= "</div>"; //col
    return $html;
}
function ___hero_archive($key, $title, $taxonomy = false, $black_header = false)
{


    if (is_tax($taxonomy) || is_category()) {
        $term = get_queried_object();
        $hero_heading = $term->name;
        $hero_description = $term->description;
    } else {
        $hero_description = get__theme_option($key . 'archive_description');
        $hero_heading = get__theme_option($key . 'archive_title');
    }

    if ($black_header == false) {
        $hero_background = get__theme_option($key . 'archive_hero_background');
        $hero_background_youtube = get__theme_option($key . 'archive_background_youtube');
        $hero_background_type = get__theme_option($key . 'archive_hero_background_type');
    }

    $hero_alignment = get__theme_option($key . 'archive_hero_alignment');
    $hero_height = get__theme_option($key . 'archive_hero_height');
    $buttons = get__theme_option($key . 'archive_hero_buttons');
    $text_align = $hero_alignment ? $hero_alignment : 'text-center';


    $heading_class[] = 'large-heading';

    $hero = "<section class='hero pb-50px rounded-10px bg-primary overflow-hidden text-white d-flex align-items-end mx-20px position-relative $hero_height $text_align'>";
    if ($black_header == false) {
        if ($hero_background_youtube && $hero_background_type == 'youtube') {
            $hero .= __background($hero_background_youtube, true);
        } else if ($hero_background) {
            $hero .= __background($hero_background);
        }
    }
    $hero .= "<div class='container'>";


    $hero .= "[breadcrumbs type='archive' archive_title='$title']";

    $hero .= __heading(array(
        'heading' => $hero_heading,
        'tag' => 'h1',
        'class' => _attribute('class', $heading_class),
        ''
    ));

    $hero .= __description(array(
        'description' => $hero_description,
        'class' => _attribute('class', array('description-box small-text')),
    ));

    if ($buttons) {
        $hero .= "<div>";
        $hero .= ____button_modules($buttons);
        $hero .= "</div>";
    }

    $hero .= "</div>";
    $hero .= "</section>";


    return $hero;
}


function ___featured($key)
{
    $featured = get__theme_option($key . 'featured');
    if ($featured) {
        $html = "<section class='featured-posts md-padding-top md-padding-bottom'>";
        $html .= "<div class='container'>";
        $html .= __heading(array(
            'heading' => 'Featured Articles',
            'class' => _attribute('class', array('text-center'))
        ));
        $html .= "<div class='row g-4'>";

        foreach ($featured as $post) {
            $data = array(
                'id' => $post['id'],
                'featured' => true,
                'col' => true,
                'elements' => array('image', 'category', 'date', 'title', 'excerpt', 'button')
            );
            $html .= __post_box($data);
        }

        $html .= "</div>";

        $html .= "</div>";
        $html .= "</section>";

        return $html;
    }
}


function ___posts_header($key, $title, $taxonomy, $class = '')
{
    $terms = get_terms(array(
        'taxonomy'   => $taxonomy,
        'hide_empty' => true,
    ));

    $html = "<div class='post-archive-header'>";
    $html .= "<div class='container'>";
    $html .= "<div class='inner $class'>";
    $html .= "<div class='row g-3 justify-content-between align-items-end'>";

    if ((is_tax($taxonomy) || is_category()) && $key != 'events_') {
        $main_term_id = get_queried_object();
        $html .= "<div class='col-auto'>";
        $html .= __heading(array(
            'heading' => $main_term_id->name,
            'class' => _attribute('class', array('mb-0')),
        ));
        $html .= "</div>";
    } else {
        if ($title) {
            $html .= "<div class='col-auto'>";
            $html .= __heading(array(
                'heading' => $title,
                'class' => _attribute('class', array('mb-0')),
            ));
            $html .= "</div>";
        }
    }





    if ($key == 'events_') {
        $events_category = get_terms(array(
            'taxonomy'   => 'events_type',
            'hide_empty' => true,
        ));

        $html .= "<div class='col-auto'>";

        $html .= "<div class='filter-style-1'>";
        $html .= "<p class='fw-medium medium-text'>Select event type:</p>";
        $html .= "<div class='filter-box bg-light rounded-corner'>";

        $html .= "<div class='row'>";
        $html .= "<div class='col-auto'>";
        $html .= "<input name='events_type' value='' type='radio' id='term-all' checked>";
        $html .= "<label class='rounded-corner' for='term-all'>All</label>";
        $html .= "</div>";

        foreach ($events_category as $event_category) {
            $term_name = $event_category->name;
            $term_id = $event_category->term_id;
            $html .= "<div class='col-auto'>";
            $html .= "<input name='events_type' value='$term_id' type='radio' id='term-$term_id'>";
            $html .= "<label class='rounded-corner' for='term-$term_id'>$term_name</label>";
            $html .= "</div>";
        }
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
    }

    $all_url = get_post_type_archive_link(get_post_type());


    $html .= "<div class='col-auto'>";

    $html .= "<div class='row g-3 align-items-center'>";
    $html .= "<div class='col-auto'>";
    $html .= "<select name='category' class='trigger-change-link'>";
    $html .= "<option term_link='$all_url' value=''>Category: All</option>";
    foreach ($terms as $term) {
        $term_name = $term->name;
        $term_id = $term->term_id;
        $term_link = get_term_link($term_id);
        $selected = '';
        if (is_tax($taxonomy) || is_category()) {
            $main_term_id = get_queried_object()->term_id;
            if ($main_term_id == $term_id) {
                $selected = 'selected';
            }
        }

        $html .= "<option $selected term_link='$term_link' value='$term_id'>Category: $term_name</option>";
    }
    $html .= "</select>";
    $html .= "</div>";
    $s = $_GET['s'];
    if ($key == 'post_' || $key == 'casestudies_' || $key == 'guides_') {
        $html .= "<div class='col-auto'>";
        $html .= "<input id='search' type='text' value='$s' placeholder='Start typing to filter...' name='s'>";
        $html .= "</div>";
    }




    $html .= "</div>";
    $html .= "</div>";

    $html .= "</div>";
    $html .= "</div>";
    $html .= "</div>";
    $html .= "</div>";

    return $html;
}


function _events_additional_content($id)
{
    $SVG = new SVG;
    $event_start_datetime = get__post_meta_by_id($id, 'event_start_datetime');
    $event_end_datetime = get__post_meta_by_id($id, 'event_end_datetime');
    $location = get_the_terms($id, 'events_location');

    $additional_content = '<ul class="meta-box list-inline text-small fw-medium">';
    if ($event_start_datetime) {
        $additional_content .= "<li class='d-flex align-items-center'>";
        $additional_content .= $SVG->calendar();
        $additional_content .= _date_format($event_start_datetime);
        $additional_content .= "</li>";
    }

    if ($event_end_datetime) {
        $additional_content .= "<li class='d-flex align-items-center'>";
        $additional_content .= $SVG->clock();
        $additional_content .= $event_end_datetime;
        $additional_content .= "</li>";
    }

    if ($location) {
        $additional_content .= "<li class='d-flex align-items-center'>";
        $additional_content .= $SVG->location();
        $additional_content .= $location[0]->name;
        $additional_content .= "</li>";
    }
    $additional_content .= '</ul>';

    return $additional_content;
}


function __layouts($args, $return = '')
{
    $args['fields'] = 'ids';
    $args['post_type'] = 'layouts';
    $layouts = get_posts($args);
    global $layouts_global;
    if ($layouts) {
        foreach ($layouts as $layout) {
            $layouts_global[] = $layout;
            $return .=  '[layouts id=' . $layout . ']';
        }
        return $return;
    }
}


function __related_posts($posts, $data, $heading = 'Related Guides', $section_id = 'Related-Post')
{
    $html = "<section class='related-guides border-top-default md-padding-top md-padding-bottom' id='$section_id'>";
    $html .= "<div class='container'>";
    $html .= "<h2 class='text-center px-20px'>$heading</h2>";

    $html .= "<div class='row g-4 same-image-height' style='--image-padding: 37%;'>";
    foreach ($posts as $post) {
        $post_status = get_post_status($post['id']);
        if ($post_status == 'publish') {
            $html .= "<div class='col-md-4 col-sm-12'>";
            $data['id'] = $post['id'];
            $html .= __post_box($data);
            $html .= '</div>';
        }
    }
    $html .= '</div>';

    $html .= '</div>';
    $html .= '</section>';

    return $html;
}


function __form($args)
{
    $form = isset($args['form']) ? $args['form'] : false;
    $form_description = isset($args['form_description']) ? $args['form_description'] : false;
    $form_image = isset($args['form_image']) ? $args['form_image'] : false;
    $form_style = isset($args['form_style']) ? $args['form_style'] : false;
    $form_heading = isset($args['form_heading']) ? $args['form_heading'] : false;

    $form_id = $form[0]['id'];
    $image_args['image_id'] = $form_image;
    $image_args['size'] = 'medium';
    $image_args['class'] = _attribute('class', array('image-box'));

    $description_args['description'] =  $form_description;
    $description_args['class'] =  _attribute('class', array('description-box'));

    $html = "<div class='form-holder bg-white rounded-corner'>"; //form-holder
    $html .= " <div class='form-header bg-accent text-white'>"; //form-header
    $html .= "<div class='row g-0 align-items-center'>"; //row

    $class = 'col-lg-9';
    $heading_class = '';
    if ($form_image) {
        $html .= "<div class='col-lg-3'>";
        $html .= __image($image_args);
        $html .= "</div>";
    } else {
        $class = 'col-12 text-center';
        $heading_class = 'mb-1';
    }

    $html .= "<div class='$class'>";
    $html .= "<div class='column-holder p-20px'>";
    $html .= __heading(array(
        'tag' => 'h3',
        'heading' => $form_heading,
        'class' => _attribute('class', array($heading_class))
    ));
    $html .= __description($description_args);
    $html .= "</div>";
    $html .= "</div>";

    $html .= "</div>"; //end-row
    $html .= "</div>"; //end-form-header

    $html .= "<div class='form-box p-20px small-text fw-light $form_style'>";
    $html .= "<div class='inner mt-20px'>";
    $html .= do_shortcode("[contact-form-7 id='$form_id']");
    $html .= "</div>";
    $html .= "</div>";

    $html .= "</div>"; //end-form-holder

    return $html;
}


function __popup($id)
{
    $id = $id;
    $popup_layout = get__post_meta_by_id($id, 'popup_layout');
    $popup_max_width = get__post_meta_by_id($id, 'popup_max_width');
    $background_color = get__post_meta_by_id($id, 'background_color');
    $background_color_val =  $background_color ? $background_color : 'background-white';
    $col_class = get_the_post_thumbnail_url($id) ? 'col-lg-6 ' : 'col-12';
    $image_class = get_the_post_thumbnail_url($id) ? 'col-lg-6 ' : 'col-12';
    $SVG = new SVG;

    $html = "<div class='modal fade modal-v2 popup-form $popup_max_width' id='modal-$id' tabindex='-1' aria-labelledby='modalSearchLabel' aria-hidden='true'>"; //modal
    $html .= "<div class='modal-dialog modal-dialog-centered'>"; //modal-dialog
    $html .= "<div class='modal-content rounded-corner overflow-hidden  $background_color_val'>";
    $html .= "<div class='modal-body p-0 '>"; //modal-body
    $html .= "<button type='button' class='btn-popup-close bg-accent text-white' data-bs-dismiss='modal'>" . $SVG->close() . "</button>";
    if ($popup_layout == 'contact_form') {
        $html .= "<div class='row g-0'>"; //row
        $html .= "<div class='$col_class'>"; //col
        $html .= "<div class='form-box p-4  h-100 d-flex align-items-center'>"; //form-holder
        $html .= "<div class='form-inner w-100'>"; //form-inner 
        $html .= do_shortcode(get_the_content(NULL, false, $id));
        $html .= "</div>"; //form-inner
        $html .= "</div>"; //form-holder
        $html .= "</div>"; //col
        if (get_the_post_thumbnail_url($id)) {
            $html .= " <div class='$image_class bg-image d-none d-lg-block'>";
            $html .= __image(array(
                'image_id' => get_post_thumbnail_id($id),
                'size' => 'large',
                'class' => _attribute('class', array('position-relative h-100'))
            ));
            $html .= "</div>";
        }

        $html .= "</div>"; //row

    } else {
        $html .= "<div class='popup-content-default p-5'>"; //popup-content-default
        $html .= do_shortcode(get_the_content(NULL, false, $id));
        $html .= "</div>"; //popup-content-default
    }
    $html .= "</div>"; //modal-body
    $html .= "</div>"; //modal-content
    $html .= "</div>"; //modal-dialog
    $html .= "</div>"; //modal

    return $html;
}
