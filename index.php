<?php

/**
 * The template for displaying the home/index page.
 * This template will also be called in any case where the Wordpress engine 
 * doesn't know which template to use (e.g. 404 error)
 */
get_header(); ?>


<?php
if (is_home()) {
    $key = 'post_';
    $title = 'All Posts';
    $has_featured = true;
    $archive_title = 'Blog';
    $data = array(
        'col' => true,
        'featured' => false,
        'style' => 'style-1',
        'elements' => array('image', 'category', 'date', 'title', 'excerpt', 'button')
    );
} else if (is_post_type_archive('events')) {
    $SVG = new SVG;
    $key = 'events_';
    $title = 'All Events';
    $has_featured = false;
    $archive_title = 'Events';


    $data = array(
        'col' => true,
        'featured' => false,
        'style' => 'style-2',
        'elements' => array('image', 'category', 'title', 'excerpt', 'button'),
    );
} else if (is_post_type_archive('capabilities')) {
    $key = 'capabilities_';
    $title = 'All Events';
    $has_featured = false;
    $archive_title = 'Capabilities';
    $post_style = 'style-1';
}
echo do_shortcode(___hero_archive($key, $archive_title));
if ($has_featured) {
    echo ___featured($key);
}
?>

<section class="archive-posts md-padding-top md-padding-bottom border-top-default">
    <?php
    echo ___posts_header($title, 'category');
    ?>
    <div class="post-grid-holder">
        <div class="container">
            <div class="row g-4 same-image-height">
                <?php
                while (have_posts()) {
                    the_post();
                    $data['id'] = get_the_ID();
                    if ($key == 'events') {
                        $crb_event_start_date = get__post_meta_by_id(get_the_ID(), 'crb_event_start_time');
                        $crb_event_start_time = get__post_meta_by_id(get_the_ID(), 'crb_event_start_time');
                        $additional_content = '<ul class="meta-box list-inline text-small fw-medium">';

                        if ($crb_event_start_date) {
                            $additional_content .= "<li class='d-flex align-items-center'>";
                            $additional_content .= $SVG->calendar();
                            $additional_content .= $crb_event_start_date;
                            $additional_content .= "</li>";
                        }

                        if ($crb_event_start_time) {
                            $additional_content .= "<li class='d-flex align-items-center'>";
                            $additional_content .= $SVG->clock();
                            $additional_content .= $crb_event_start_time;
                            $additional_content .= "</li>";
                        }
                        $additional_content .= '</ul>';
                        $data['additional_content'] = $additional_content;
                    }
                    echo __post_box_blog($data);
                }
                ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>