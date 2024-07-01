<?php
function __heading($data)
{
    $class = $data['tag'];
    $heading = $data['heading'];
    $tag = $data['tag'];
    $_attributes = _attributes(array(
        array('class', $class)
    ));
    if ($heading) {
        return "<$tag $_attributes>$heading</$tag>";
    }
}

function __description($atts)
{
    extract(
        shortcode_atts(
            array(
                'description' => '',
                'class' => '',
            ),
            $atts
        )
    );
    $_attributes = _attributes(array(
        array('class', $class),
        array('class', 'description-box'),
    ));

    $description_val = do_shortcode(wpautop(html_entity_decode($description)));
    if ($description) {
        return "<div $_attributes>$description_val</div>";
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
