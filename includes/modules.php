<?php
function action_module_content()
{
    // Check if a post was updated (add your specific conditions here)
    if (did_action('post_updated')) {
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (_is_module()) {
            $post_content = '<!-- wp:html -->';

            $post_content .= ___hero();
            $post_content .= ___sections();


            $post_content .= '<!-- /wp:html -->';

            $my_post = array(
                'ID'           => get_the_ID(),
                'post_content' => $post_content,
            );

            // Update the post into the database
            wp_update_post($my_post);
        }
    }
}
add_action('shutdown', 'action_module_content');

function ___hero()
{
    $hero_heading = get__post_meta('hero_heading');
    $hero_heading = get__post_meta('hero_heading');
    $hero_description = _format_text(get__post_meta('hero_description'));
    $hero_hidden = get__post_meta('hero_hidden');
    $hero_background = get__post_meta('hero_background');
    $hero_heading_val = $hero_heading ? $hero_heading : get_the_title();
    if (!$hero_hidden) {
        $hero = "<section class='hero pb-50px text-center rounded-10px bg-primary overflow-hidden text-white d-flex align-items-end mx-20px position-relative'>";
        $hero .= _bg_image($hero_background);
        $hero .= "<div class='container'>";
        $hero .= __heading(array(
            'heading' => $hero_heading_val,
            'tag' => 'h1',
            'class' => _attribute('class', array('large-heading')),
            ''
        ));
        $hero .= __description(array(
            'description' => $hero_description,
            'class' => _attribute('class', array('description-box', 'medium-text')),
        ));
        $hero .= "</div>";
        $hero .= "</section>";
        return $hero;
    }
}

function ___sections()
{
    $sections = get__post_meta('sections');
    $html = '';
    foreach ($sections as $key => $section) {
        $disable_section = $section['disable_section'];
        if (!$disable_section) {
            $section_id = $section['section_id'];
            $section_items = $section['section_items'];
            $section_styles = $section['section_styles'];
            $section_id_val  = $section_id ? $section_id : 'section-' . $key;
            $classes = array();
            $styles = array();
            $container_styles = array();
            $classes[] = 'section';
            $classes[] = 'section-' . $key;

            foreach ($section_styles as $section_style) {
                $type = $section_style['_type'];
                switch ($type) {
                    case 'padding':
                        $classes[] = $section_style['padding_top'];
                        $classes[] = $section_style['padding_bottom'];
                        $classes[] = $section_style['padding_left'];
                        $classes[] = $section_style['padding_right'];
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
                            $classes[] = 'd-flex flex-column';
                        }
                        break;
                    case 'text_color':
                        $text_color_custom = $section_style['text_color_custom'];
                        $classes[] = $section_style['text_color'];
                        if ($text_color_custom) {
                            $styles[] = 'color: ' . $text_color_custom;
                        }
                        break;
                    case 'background_color':
                        $background_color_custom = $section_style['background_color_custom'];
                        $classes[] = $section_style['background_color'];
                        if ($background_color_custom) {
                            $styles[] = 'background-color: ' . $background_color_custom;
                        }
                        break;
                    case 'background_image':
                        $background_image = $section_style['background_image'];
                        $classes[] = $section_style['background_attachment'];
                        $classes[] = $section_style['background_size'];
                        $classes[] = $section_style['background_repeat'];
                        if ($background_image) {
                            $styles[] = 'background-image: url(' . wp_get_attachment_image_url($background_image, 'full') . ')';
                        }
                        break;
                    case 'container_width':
                        $classes[] = $section_style['container_width'];
                        if ($section_style['custom_container_width']) {
                            $container_styles[] = 'max-width: ' . $section_style['custom_container_width'];
                        }
                        break;
                    case 'border':
                        if ($section_style['border_radius']) {
                            $styles[] = '--border-radius: ' . $section_style['border_radius'];
                            $classes[] = 'rounded-corner';
                        }
                        break;
                }
            }


            $id_val = _attribute('id', array($section_id_val));
            $classes_attr = _attribute('class', $classes);
            if ($styles) {
                $styles_val = _attribute('style', $styles, ';');
            }

            if ($container_styles) {
                $container_styles_val = _attribute('style', $container_styles, ';');
            }


            $section_attribute = _attributes(array($classes_attr, $id_val, $styles_val));

            $html .= "<section $section_attribute>";
            $html .= "<div class='container' $container_styles_val>";

            foreach ($section_items as $key => $items) {
                $type = $items['_type'];
                switch ($type) {
                    case 'heading':
                        $html .= ____heading_modules($items);
                        break;
                    case 'columns':
                        $html .= ____columns_modules($items, $section_id_val . $key);
                        break;
                    case 'description':
                        $html .= __description(array(
                            'description' => $items['description'],
                            'class' => _attribute('class', array('description-box'))
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
                        ));
                        break;
                    case 'buttons':
                        $html .= ____button_modules($items['buttons']);
                        break;
                    case 'post_grid':
                        $html .= ____post_grid(array(
                            'post_box_styles' => $items['post_box_styles'],
                            'post_elements' => $items['post_elements'],
                            'post_type' => $items['post_type'],
                        ));
                        break;
                }
            }

            $html .= "</div>";
            $html .= "</section>";
        }
    }
    return $html;
}
function ____post_grid($data)
{
    $post_box_styles = $data['post_box_styles'];
    $post_elements = $data['post_elements'];

    $post_type = $data['post_type'][0]['_type'];
    $source = $data['post_type'][0]['source'];


    // Build the args
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
    }
    // Get the posts
    $posts_lists = get_posts($args);

    $classes[] = 'column-holder';
    $classes[] = 'position-relative';

    $styles = array();
    $classes = array();
    $column_classes = array();
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
                    $classes[] = 'd-flex';
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
                if ($post_box_style['border_radius']) {
                    $styles[] = '--border-radius: ' . $post_box_style['border_radius'];
                    $classes[] = 'rounded-corner';
                }
                break;
            case 'column_width':
                $column_classes[] = $post_box_style['column_width'];
                $column_classes[] = $post_box_style['column_width_tablet'];
                $column_classes[] = $post_box_style['column_width_mobile'];
                break;
        }
    }
    $classes[] = 'column-holder position-relative overflow-hidden h-100';

    if ($styles) {
        $styles_val = _attribute('style', $styles, ';');
    }


    if ($classes) {
        $classes_val = _attribute('class', $classes, ' ');
    }


    if ($column_classes) {
        $column_classes_val = _attribute('class', $column_classes, ' ');
    }


    $post_attribute = _attributes(array($classes_val, $styles_val));
    $column_attribute = _attributes(array($column_classes_val));

    $html = '';
    $html .= "<div class='post-grid'>";
    $html .= "<div class='row g-4'>";
    foreach ($posts_lists as $post) {
        $html .= "<div $column_attribute>";
        $html .= "<div $post_attribute>";
        foreach ($post_elements as $item) {
            $type = $item['_type'];
            switch ($type) {
                case 'post_title':
                    $html .= __heading(array(
                        'tag' => 'h3',
                        'heading' => $post->post_title,
                        'class' => _attribute('class', array('post-title position-relative'))
                    ));
                    break;
                case 'permalink':
                    $html .= __button(array(
                        'button_type' => get_post_type(),
                        'button_text' => $item['button_text'],
                        'button_url' => $post->ID,
                        'button_url_custom' => $item['button_url_custom'],
                        'button_style' => $item['button_style'] . ' position-relative',
                        'button_target' => $item['button_target'],
                    ));
                    break;
                case 'featured_image':
                    $is_background_image = $item['is_background_image'];
                    $image_args['featured_image'] = $post->ID;
                    $image_args['size'] = $item['size'];
                    if ($is_background_image) {
                        $image_args['class'] = _attribute('class', array('background-image', 'background-overlay'));
                    }
                    $html .= __image($image_args);

                    break;
            }
        }
        $html .= "</div>";
        $html .= "</div>";
    }
    $html .= "</div>";
    $html .= "</div>";

    return $html;
}
function ____button_modules($buttons)
{
    if ($buttons) {
        $html = "<div class='button-group-box d-inline-flex flex-wrap'>";
        foreach ($buttons as $button) {
            $html .= __button(array(
                'button_type' => $button['button_type'],
                'button_text' => $button['button_text'],
                'button_url' => $button['button_url'],
                'button_url_custom' => $button['button_url_custom'],
                'button_style' => $button['button_style'],
                'button_target' => $button['button_target'],
            ));
        }
        $html .= "</div>";
        return $html;
    }
}
function ____gallery_modules($data)
{
    $id = $data['id'];
    $gallery = $data['gallery'];
    $gallery_style = $data['gallery_style'];
    $number_of_slides = $data['number_of_slides'];
    $number_of_slides_tablet = $data['number_of_slides_tablet'];
    $number_of_slides_mobile = $data['number_of_slides_mobile'];
    $image_args = [];
    if ($gallery) {
        $html  = "<div class='gallery $gallery_style'>";

        if ($gallery_style == 'logo-slider') {
            $image_args['class'] = _attribute('class', array('swiper-slide'));
            $number_of_slides_attr = _attribute('number_of_slides', array($number_of_slides));
            $number_of_slides_tablet_attr = _attribute('number_of_slides_tablet', array($number_of_slides_tablet));
            $number_of_slides_mobile_attr = _attribute('number_of_slides_mobile', array($number_of_slides_mobile));
            $gallery_attr = _attributes(array($number_of_slides_attr, $number_of_slides_tablet_attr, $number_of_slides_mobile_attr));
            $image_args['size'] = 'medium';

            $html .= "<div id='$id' class='swiper swiper-logo-slider' $gallery_attr>";
            $html .= '<div class="swiper-wrapper align-items-center">';
        } else {
            $image_args['size'] = 'large';

            $html .= '<div class="row g-5">';
            $image_args['class'] = _attribute('class', array('col-lg-4'));
        }

        foreach ($gallery as $image) {
            $image_args['image_id'] = $image;

            $html .= __image($image_args);
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
    $classes = array();
    $styles = array();
    $classes[] = 'column-holder content-margin overflow-hidden position-relative h1-100';
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
                    $classes[] = $column_style['text_align'];
                    if ($column_style['align_items'] || $column_style['justify_content']) {
                        $classes[] = 'd-flex flex-column';
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
                    $border_style = $column_style['border_style'];

                    if ($column_style['border_radius']) {
                        $styles[] = '--border-radius: ' . $column_style['border_radius'];
                        $classes[] = 'rounded-corner';
                    }

                    if ($border_style == 'border-custom') {
                        $border_color = $column_style['border_color'];
                        $border_color_custom = $column_style['border_color_custom'];
                        $border_width = $column_style['border_width'];
                        if ($border_color == 'border-custom-color') {
                            $classes[] = $column_style['border_color'];
                        } else {
                            $styles[] = 'border-color: ' . $border_color_custom;
                        }
                        if ($border_width) {
                            $styles[] = 'border-width: ' . $border_width;
                        }
                    } else {
                        $classes[] = 'border-default';
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
        $slides_attr = _attributes(array($number_of_slides_attr, $number_of_slides_tablet_attr, $number_of_slides_mobile_attr));

        $html .= "<div class='swiper-holder $slider_style'>"; //swiper-holder
        $html .= "<div class='swiper swiper-sliders' id='$swiper_id' $slides_attr>"; //swiper
    }

    if ($styles) {
        $styles_val = _attribute('style', $styles, ';');
    }

    if ($classes) {
        $classes_val = _attribute('class', $classes, ' ');
    }
    $column_attributes = _attributes(array($classes_val, $styles_val));

    if ($is_slider) {
        $html .= "<div class='swiper-wrapper'>"; //swiper-wrapper

    } else {
        $html .= "<div class='row g-4'>"; //row
    }
    foreach ($columns as $key => $column) {
        $items = $column['items'];
        if ($is_slider) {
            $html .= '<div class="swiper-slide">'; //swiper-slide
        } else {
            $html .= '<div class="col">'; //col
        }
        $html .= "<div $column_attributes>";
        foreach ($items as $item) {
            $type = $item['_type'];
            switch ($type) {
                case 'heading':
                    $html .= ____heading_modules($item);
                    break;
                case 'icon':
                    $html .= ____icon_modules($item);
                    break;
                case 'description':
                    $html .= __description(array(
                        'description' => $item['description'],
                        'class' => _attribute('class', array('description-box'))
                    ));
                    break;
                case 'image':
                    $is_background_image = $item['is_background_image'];
                    $image_args['image_id'] = $item['image'];
                    $image_args['size'] = $item['size'];
                    if ($is_background_image) {
                        $image_args['class'] = _attribute('class', array('background-image', 'background-overlay'));
                    }
                    $html .= __image($image_args);
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
            }
        }
        $html .= '</div>'; //end column-holder
        $html .= '</div>'; //end col //end swiper-slide
    }
    $html .= '</div>'; //end row // end-swiper-wrapper
    if ($is_slider) {
        $html .= '</div>'; //end swiper
        $html .= '<div class="swiper-nav d-flex justify-content-start">'; //end swiper
        $html .= '<div class="swiper-button-prev"></div>';
        $html .= '<div class="swiper-button-next"></div>';
        $html .= '</div>'; //end swipernav
        $html .= '</div>'; //end swiper-holder
    }
    return $html;
}

function ____icon_modules($items)
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

    return _icon($icon_data);
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
    $html .= implode($separator, array_unique($attributes));
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


function _format_text($text)
{
    return htmlentities($text);
}

function _is_module()
{
    if (get_page_template_slug() == 'templates/page-modules.php') {
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
