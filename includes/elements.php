<?php
function __heading($data, $html = '')
{
    $heading = isset($data['heading']) ? $data['heading'] : false;
    $class = isset($data['class']) ? $data['class'] : false;
    $tag = isset($data['tag']) ? $data['tag'] : 'h2';
    $prefix = isset($data['prefix']) ? $data['prefix'] : false;
    $suffix = isset($data['prefix']) ? $data['suffix'] : false;
    $styles = isset($data['styles']) ? $data['styles'] : false;

    $attributes_args = [];
    if ($class) {
        $attributes_args[] = $class;
    }
    if ($styles) {
        $attributes_args[] = $styles;
    }
    $_attributes = _attributes($attributes_args);

    if ($heading) {
        if ($prefix || $suffix) {
            $html .= "<div $_attributes>";
            if ($prefix) {
                $html .= "<span>$prefix</span>";
            }
            $html .= "<$tag>$heading</$tag>";
            if ($suffix) {
                $html .= "<span>$suffix</span>";
            }

            $html .= "</div>";
        } else {
            $html .= "<$tag $_attributes>$heading</$tag>";
        }
    }
    return $html;
}

function __description($data)
{
    $description = isset($data['description']) ? $data['description'] : false;
    $class = isset($data['class']) ? $data['class'] : false;
    $style = isset($data['style']) ? $data['style'] : false;

    $attributes_args = [];
    if ($class) {
        $attributes_args[] = $class;
    }


    if ($description) {
        $description_val = wpautop($description);
        $attributes_args = [];
        if ($class) {
            $attributes_args[] = $class;
        }
        if ($style) {
            $attributes_args[] = $style;
        }
        $_attributes = _attributes($attributes_args);

        return "<div $_attributes>$description_val</div>";
    }
}

function __icon($data, $html = '')
{
    $id = isset($data['id']) ? $data['id'] : false;
    if ($id) {
        $class = isset($data['class']) ? $data['class'] : false;
        $styles = isset($data['styles']) ? $data['styles'] : false;


        $attributes_args = [];
        if ($class) {
            $attributes_args[] = $class;
        }
        if ($styles) {
            $attributes_args[] = $styles;
        }
        $_attributes = _attributes($attributes_args);

        $url = wp_get_original_image_path($id);
        $html .= "<div $_attributes>";
        $html .= _output_svg_from_url($url);
        $html .= '</div>';
    }

    return $html;
}

function __image($data)
{
    $featured_image = isset($data['featured_image']) ? $data['featured_image'] : false;
    $image_id = isset($data['image_id']) ? $data['image_id'] : false;
    $size = isset($data['size']) ? $data['size'] : false;
    $class = isset($data['class']) ? $data['class'] : false;
    $style = isset($data['style']) ? $data['style'] : false;

    if ($featured_image) {
        $image = get_the_post_thumbnail($featured_image, $size);
    } else {
        $image = wp_get_attachment_image($image_id, $size);
    }
    if ($image) {
        $attributes_args = [];
        if ($class) {
            $attributes_args[] = $class;
        }
        if ($style) {
            $attributes_args[] = $style;
        }
        $_attributes = _attributes($attributes_args);

        return "<div $_attributes>$image</div>";
    }
}

function __video($data)
{
    $video_url = wp_get_attachment_url($data['video_id']);
    $video_type = $data['video_type'];
    $autoplay = $data['autoplay'] ? $data['autoplay'] : false;
    $class = isset($data['class']) ? $data['class'] : false;
    $attributes_args = [];
    if ($class) {
        $attributes_args[] = $class;
    }
    $_attributes = _attributes($attributes_args);

    if ($video_type == 'youtube') {
        $parameters = '';
        $youtube_video_id = $data['youtube_video_id'];
        if ($autoplay) {
            $parameters = "?loop=1&controls=0&rel=0&playsinline=1&autoplay=1&mute=1&controls=0&playlist=$youtube_video_id";
        }
        $source = "https://www.youtube.com/embed/$youtube_video_id$parameters";
        return "<div $_attributes><iframe src='$source'></iframe></div>";
    } else {
        if ($video_url) {

            $parameters = '';
            if ($autoplay) {
                $parameters = 'autoplay loop muted';
            } else {
                $parameters = 'controls';
            }


            return "<div $_attributes><video  $parameters src='$video_url'></video></div>";
        }
    }
}

function __background($background, $is_youtube = false, $autoplay = true)
{
    if ($is_youtube == false) {
        $mime_type =  get_post_mime_type($background);
        if (str_contains($mime_type, 'video')) {
            return __video(array(
                'video_id' => $background,
                'class' => _attribute('class', array('background-image', 'background-overlay'))
            ));
        } else {
            return __image(array(
                'image_id' => $background,
                'class' => _attribute('class', array('background-image', 'background-overlay')),
                'size' => 'full'
            ));
        }
    } else {
        $source = "https://www.youtube.com/embed/$background?loop=1&controls=0&rel=0&playsinline=1&autoplay=1&mute=1&controls=0&playlist=$background";
        return "<div class='background-image background-overlay'><iframe src='$source'></iframe></div>";
    }
}


function __button($data)
{
    $button_type        = isset($data['button_type']) ? $data['button_type'] : false;
    $button_text        = isset($data['button_text']) ? $data['button_text'] : false;
    $button_url         = isset($data['button_url']) ? $data['button_url'] : false;
    $button_url_custom  = isset($data['button_url_custom']) ? $data['button_url_custom'] : false;
    $button_style       = isset($data['button_style']) ? $data['button_style'] : false;
    $button_text        = isset($data['button_text']) ? $data['button_text'] : false;
    $button_target      = isset($data['button_target']) ? $data['button_target'] : false;

    if ($button_type != 'custom') {
        $button_url = get_permalink($button_url);
    } else {
        $button_url = $button_url_custom;
    }
    if ($button_text && $button_url) {
        $attributes_args = [];
        $attributes_args[] = _attribute('class', array($button_style, 'button-box'));

        $_attributes = _attributes($attributes_args);
        return "<div $_attributes><a class='rounded-10px' $button_target href='$button_url'>$button_text</a></div>";
    }
}

