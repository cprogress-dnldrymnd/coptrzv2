<?php
function __heading($data, $html = '')
{
    $heading = isset($data['heading']) ? $data['heading'] : false;
    $class = isset($data['class']) ? $data['class'] : '';
    $tag = isset($data['tag']) ? $data['tag'] : 'h2';
    $prefix = isset($data['prefix']) ? $data['prefix'] : false;
    $suffix = isset($data['prefix']) ? $data['suffix'] : false;
    $attributes_args = [];
    if ($class) {
        $attributes_args[] = $class;
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

    $attributes_args = array(
        array('class', 'description-box'),
    );

    if ($class) {
        $attributes_args[] = array(
            array('class', $class),
        );
    }

    $_attributes = _attributes($attributes_args);

    if ($description) {
        return "<div $_attributes>$description</div>";
    }
}

function __image($atts)
{
    extract(
        shortcode_atts(
            array(
                'featured_image' => false,
                'image_id' => '',
                'size' => '',
                'class' => '',
            ),
            $atts
        )
    );

    if ($featured_image) {
        $image = get_the_post_thumbnail(get_the_ID(), $size);
    } else {
        $image = wp_get_attachment_image($image_id, $size);
    }
    if ($image) {
        $_attributes = _attributes(array(
            array('class', $class),
            array('class', 'image-box'),
        ));

        return "<div $_attributes>$image</div>";
    }
}

function __video($atts)
{
    extract(
        shortcode_atts(
            array(
                'video_id' => '',
                'class' => '',
            ),
            $atts
        )
    );

    $video_url = wp_get_attachment_url($video_id);

    if ($video_url) {
        $_attributes = _attributes(array(
            array('class', $class),
            array('class', 'video-box'),
        ));

        return "<div $_attributes><video autoplay loop muted src='$video_url'></video></div>";
    }
}
