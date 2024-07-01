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

    $attributes_args = [];
    if ($class) {
        $attributes_args[] = $class;
    }

    $_attributes = _attributes($attributes_args);

    if ($description) {
        return "<div $_attributes>$description</div>";
    }
}

function _icon($data, $html = '')
{
  

    return $html;
}

function __image($data)
{
    $featured_image = isset($data['featured_image']) ? true : false;
    $image_id = isset($data['image_id']) ? $data['image_id'] : false;
    $size = isset($data['size']) ? $data['size'] : false;
    $class = isset($data['size']) ? $data['class'] : false;

    if ($featured_image) {
        $image = get_the_post_thumbnail(get_the_ID(), $size);
    } else {
        $image = wp_get_attachment_image($image_id, $size);
    }
    if ($image) {
        $attributes_args = [];
        if ($class) {
            $attributes_args[] = $class;
        }
        $_attributes = _attributes($attributes_args);

        return "<div $_attributes>$image</div>";
    }
}

function __video($data)
{
    $video_url = wp_get_attachment_url($data['video_id']);

    if ($video_url) {
        $class = isset($data['class']) ? $data['class'] : false;
        $attributes_args = [];
        if ($class) {
            $attributes_args[] = $class;
        }
        $_attributes = _attributes($attributes_args);

        return "<div $_attributes><video autoplay loop muted src='$video_url'></video></div>";
    }
}

function _bg_image($hero_background)
{
    $mime_type =  get_post_mime_type($hero_background);

    if (str_contains($mime_type, 'video')) {
        return __video(array(
            'video_id' => $hero_background,
            'class' => _attribute('class', array('background-image', 'background-overlay'))
        ));
    } else {
        return __image(array(
            'image_id' => $hero_background,
            'class' => _attribute('class', array('background-image', 'background-overlay')),
            'size' => 'full'
        ));
    }
}
