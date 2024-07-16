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
                    'items_per_page' => 50,
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
            $html .= "<div class='col-auto'>";
            $html .= "<h2>$heading</h2>";
            $html .= "</div>";
        }
        if ($search_filter) {
            $html .= "<div class='col-auto'>";
            $html .= "<input type='text' placeholder='Start typing to filter...' name='s'>";
            $html .= "</div>";
        }
        $html .= "</div>";

        $html .= "<div class='row g-3 same-image-height' style='--object-fit: contain; --image-padding: 20%'>";
        foreach ($terms as $term) {
            $logo = get___term_meta($term->term_id, 'image');
            $link = get_term_link($term->term_id);
            if ($logo) {
                $image_args['image_id'] = $logo;
                $image_args['size'] = 'medium';
                $image_args['class'] = _attribute('class', array('image-box mb-3'));

                $html .= "<div class='col-lg-3'>";
                $html .= "<div class='inner text-center h-100 border-default rounded-corner xs-padding'>";
                $html .= "<a href='$link' class='text-primary'>";

                $html .= __image($image_args);
                $html .= __heading(array(
                    'heading' => $term->name,
                    'class' => _attribute('class', array('mb-0')),
                    'tag' => 'h3',
                ));
                $html .= "</a>";
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
            } else {
                $post_type = get_post_type();
                $link = get_post_type_archive_link($post_type);
                $post_type_obj = get_post_type_object($post_type);
                $name = $post_type_obj->labels->name;
                $html .= "<li><a class='item text-white' href='$link'>$name</a></li>";
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

    function case_study_slider_grid()
    {

        $casestudies_featured = get__theme_option('casestudies_featured');
        $html = "<div class='case-study-slider text-white'>"; //case-study-slider
        $html .= "<div class='swiper-holder style-2'>"; //swiper-holder
        $html .= "<div class='swiper swiper-full-width'>"; //swiper

        $html .= "<div class='swiper-wrapper'>"; //swiper-wrapper

        foreach ($casestudies_featured as $casestudies) {
            $id = $casestudies['id'];
            $post_excerpt = wpautop(get_the_excerpt($id));
            $features = get__post_meta_by_id($id, 'feature');
            $logo = get__post_meta_by_id($id, 'logo');
            $html .= "<div class='swiper-slide'>"; //swiper-slide
            $html .= __image(array(
                'image_id' => get_post_thumbnail_id($id),
                'placeholder' => true,
                'size' => 'full',
                'class' => _attribute('class', array('background-image background-overlay background-overlay-darker bg-black mx-20px rounded-10px overflow-hidden'))
            ));

            $html .= "<div class='inner md-padding-bottom lg-padding-top mx-20px  overflow-hidden position-relative'>"; //inner

            $html .= "<div class='container'>"; //container
            $html .= "<div class='row g-5'>"; //row

            $html .= "<div class='col-lg-8 col-left text-center text-lg-start'>";
            $html .= __description(array(
                'description' => $post_excerpt,
                'class' => _attribute('class', array('description-box big-text mb-lg-5'))
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
                $html .= "<div class='col-lg-4 col-right'>";
                $html .= "<div class='meta-data text-center text-lg-end'>";
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
            $html .= "</div>"; //end-swiper-slide
        }



        $html .= "</div>"; //end-swiper-wrapper
        $html .= "<div class='bottom-holder'> <div class='container position-relative'> <div class='row g-4 justify-content-center justify-content-lg-between align-items-end'>";
        $html .= "<div class='col-auto'> <div class='swiper-nav d-flex justify-content-start'> <div class='swiper-button-prev'></div> <div class='swiper-button-next'></div> </div> </div>";

        $html .= "<div class='col-auto'>";
        $html .= "<div class='row g-4 text-center button-group-box justify-content-center align-items-center d-inline-flex'>";
        $html .= __button(array(
            'button_type' => get_post_type($id),
            'button_text' => 'Read Case Study',
            'button_url' => $id,
            'button_style' => 'button-accent col-12 col-sm-auto',
        ));

        $html .= __button(array(
            'button_type' => 'custom',
            'button_text' => 'All Case Studies',
            'button_url_custom' => get_post_type_archive_link('casestudies'),
            'button_style' => 'button-bordered col-12 col-sm-auto',
        ));
        $html .= "</div>";
        $html .= "</div>";



        $html .= "</div></div></div>";



        $html .= "</div>"; //end-swiper
        $html .= "</div>"; //end-swiper-holder
        $html .= "</div>"; //end case-study-slider

        return $html;
    }

    function layouts($atts)
    {
        extract(
            shortcode_atts(
                array(
                    'id' => '',
                ),
                $atts
            )
        );
        return ___sections('sections', $id);
    }
    function blog_meta()
    {
        ob_start();
?>
        <div class="blog-meta small-text">
            <div class="row">
                <div class="col-auto">
                    <p class="mb-0 fw-semibold"><strong>Last updated on</strong></p>
                    <p class="fw-light"><?= get_the_date('jS F') ?></p>
                </div>
            </div>
        </div>
    <?php
        return ob_get_clean();
    }

    function social_share()
    {
        global $post;
        $SVG = new SVG;
        $url = get_permalink($post->ID);
        $title = str_replace(' ', '%20', get_the_title($post->ID));

        $social_buttons = '';
        $social_buttons .= '<div class="social-share-buttons">';

        $social_buttons .= '<a href="#" onclick="window.open(\'https://www.facebook.com/sharer.php?u=' . $url . '&t=' . $title . '\', \'_blank\', \'width=600,height=400\'); return false;">' . $SVG->facebook() . '</a>';

        $social_buttons .= '<a href="#" onclick="window.open(\'https://www.linkedin.com/feed/?linkOrigin=LI_BADGE&shareActive=true&shareUrl=' . $url . '\', \'_blank\', \'width=600,height=400\'); return false;">' . $SVG->linkedin() . '</a>';
        $social_buttons .= '<a href="#" onclick="window.open(\'https://x.com/share?url=' . $url . '&text=' . $title . '\', \'_blank\', \'width=600,height=400\'); return false;">' . $SVG->x() . '</a>';
        $social_buttons .= '</div>';
        return $social_buttons;
    }


    function post_link()
    {
        ob_start();
    ?>
        <button onclick="copy_link()" class="post-link-copy">
            <input class="d-none" id="copy-link" value="<?= get_permalink(get_the_ID()) ?>">
            <span>Copy Link</span>
        </button>
        <script>
            function copy_link() {
                // Get the text field
                var copyText = document.getElementById("copy-link");

                // Select the text field
                copyText.select();
                copyText.setSelectionRange(0, 99999); // For mobile devices

                // Copy the text inside the text field
                navigator.clipboard.writeText(copyText.value);

                jQuery('.post-link-copy span').text('Link Copied');
            }
        </script>
<?php
        return ob_get_clean();
    }

    function related_posts()
    {
        $categories = get_the_category(get_the_ID());
        $args = array(
            'posts_per_page' => 3,

            'post_type'      => array('post'),

            'post_status'    => 'publish',

            'category__and ' => $categories,

            'orderby' => 'rand'

        );
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            $html = "<div class='related-posts-sidebar'>";
            $html .= "<h3>Related Posts</h3>";
            $html .= "<div class='row g-3 same-image-height' style='--image-padding: 35%'>";
            while ($query->have_posts()) {
                $query->the_post();
                $html .= "<div class='col-12'>";
                $data = array(
                    'id' => get_the_ID(),
                    'featured' => false,
                    'col' => false,
                    'style' => 'style-1',
                    'taxonomy' => 'category',
                    'elements' => array('image', 'category', 'date', 'title', 'excerpt', 'button')
                );
                $html .= __post_box($data);
                $html .= "</div>";
            }
            wp_reset_postdata();
            $html .= "</div>";
            $html .= "</div>";

            return $html;
        }
    }
    function latest_from_coptrz()
    {
        $latest_from_coptrz = get__theme_option('latest_from_coptrz');
        if ($latest_from_coptrz) {
            $html = "<div class='latest-from-coptrz'>";
            $html .= "<div class='row g-4 g-xs-10px'>";

            foreach ($latest_from_coptrz as $post) {
                $data = [];
                $id = $post['post'][0]['id'];
                $background = $post['background'];
                $button_text = $post['button_text'];
                $is_new = $post['is_new'];

                $data['id'] = $id;
                $data['col'] = 'col-lg-3 col-md-6';
                $data['is_new'] = $is_new;

                if ($background == 'featured-image') {
                    $data['featured'] = true;
                } else {
                    $data['featured'] = false;
                    $data['background_class'] = $background;
                }
                if (get_post_type($id) == 'post') {
                    $data['elements'] = array('category', 'image', 'date', 'title', 'button');
                    $data['taxonomy'] = 'category';
                } else {
                    $data['elements'] = array('image',  'title', 'button');
                }
                if ($button_text) {
                    $data['button_text'] = $button_text;
                }

                $html .= __post_box($data);
            }
            $html .= "</div>";
            $html .= "</div>";

            return $html;
        }
    }
    function reviews()
    {
        $SVG = new SVG;
        $reviews = get__theme_option('reviews');
        if ($reviews) {
            $html = "<div class='reviews text-white'>";
            $html .= "<div class='row g-20px'>";
            foreach ($reviews as $review) {
                $review_score = $review['review_score'];
                $review_text = $review['review_text'];
                $review_logo = $review['review_logo'];
                $html .= "<div class='col-lg-4'>";
                $html .= "<div class='column-holder bg-secondary xs-padding rounded-10px'>";

                $html .= "<div class='review-box d-flex justify-content-lg-between'>";
                $html .= "<div class='review-text'> $review_score </div>";

                $html .= "<div class='review-stars'>";
                $html .= "<div class='stars d-flex'>";
                $i = 1;
                while ($i < 6) {
                    $html .= $SVG->star();
                    $i++;
                }
                $html .= "</div>";
                $html .= "<div class='small-text'>$review_text</div>";
                $html .= "</div>";


                $html .= "</div>";
                $html .= __image(array(
                    'image_id' => $review_logo,
                    'class' => _attribute('class', array('logo-box text-center mt-4'))
                ));

                $html .= "</div>";
                $html .= "</div>";
            }

            $html .= "</div>";
            $html .= "</div>";
            return $html;
        }
    }

    function socials()
    {
        $SVG = new SVG;
        $socials = get__theme_option('socials');
        if ($socials) {
            $html = "<div class='socials'>";
            $html .= "<ul class='d-inline-flex align-items-center m-0 p-0'>";
            foreach ($socials as $social) {
                $url = $social['url'];
                $icon = $social['_type'];
                $html .= "<li>";
                $html .= "<a href='$url' target='_blank'>";
                $html .= $SVG->$icon();
                $html .= "</a>";
                $html .= "</li>";
            }

            $html .= "</ul>";
            $html .= "</div>";
            return $html;
        }
    }

    function site_logo()
    {
        $logo = get__theme_option('logo');
        $site_url = get_site_url();

        $html = "<div class='site-logo'>";
        $html .= "<a href='$site_url'>";
        $html .= __icon(array(
            'id' => $logo
        ));
        $html .= "</a>";
        $html .= "</div>";
        return $html;
    }

    function popup($atts)
    {
        extract(
            shortcode_atts(
                array(
                    'id' => '',
                ),
                $atts
            )
        );
        return __popup($id);
    }

    function event_countdown()
    {
        $crb_event_start_date = get__post_meta_by_id(get_the_ID(), 'crb_event_start_date');
        $crb_event_start_time = get__post_meta_by_id(get_the_ID(), 'crb_event_start_time');
        $date_val = $crb_event_start_date . ' ' . $crb_event_start_time;
        $date = strtotime($date_val);
        $date_format = date('M j, Y H:i:s', $date);
        $html = "<div class='event-countdown fw-medium' date='$date_format'>";
        $html .= "<div class='event-countdown-holder row g-4 align-items-center justify-content-center'>";
        $html .= "<div class='col-auto'>";
        $html .= "<div class='col-days'>";
        $html .= "<div class='countdown-box countdown-days rounded-corner'>00</div>";
        $html .= "<span>Days</span>";
        $html .= "</div>";
        $html .= "</div>";

        $html .= "<div class='col-auto'>";
        $html .= "<div class='col-hours'>";
        $html .= "<div class='countdown-box countdown-hours rounded-corner'>00</div>";
        $html .= "<span>Hours</span>";
        $html .= "</div>";
        $html .= "</div>";

        $html .= "<div class='col-auto'>";
        $html .= "<div class='col-minutes'>";
        $html .= "<div class='countdown-box countdown-minutes rounded-corner'>00</div>";
        $html .= "<span>Minutes</span>";
        $html .= "</div>";
        $html .= "</div>";

        $html .= "<div class='col-auto'>";
        $html .= "<div class='col-minutes'>";
        $html .= "<div class='countdown-box countdown-seconds rounded-corner'>00</div>";
        $html .= "<span>Seconds</span>";
        $html .= "</div>";
        $html .= "</div>";

        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    function product_compare($atts)
    {
        extract(
            shortcode_atts(
                array(
                    'id' => '',
                ),
                $atts
            )
        );
        return __product_compare($id);
    }

    function drone_servicing()
    {
        return __drone_servicing();
    }

    function brands_logo_slider()
    {

        $terms = get_terms(array(
            'taxonomy'   => 'pa_brands',
            'hide_empty' => true,
            'number' => 100
        ));
        $image_args['class'] = _attribute('class', array('image-box'));
        $image_args['size'] = 'medium';


        $html  = "<div class='gallery logo-slider'>";

        $html .= "<div id='brands-slider' class='swiper swiper-logo-slider'>";
        $html .= '<div class="swiper-wrapper align-items-center">';
        foreach ($terms as $term) {
            $logo = get___term_meta($term->term_id, 'image');
            $hide_on_slider = get___term_meta($term->term_id, 'hide_on_slider');

            $image_args['image_id'] = $logo;
            if ($logo && !$hide_on_slider) {
                $link = get_term_link($term->term_id);
                $html .= "<div class='swiper-slide'>";
                $html .= "<a href='$link'>";
                $html .= __image($image_args);
                $html .= "</a>";
                $html .= "</div>";
            }
        }

        $html  .= "</div>";
        $html  .= "</div>";
        $html  .= "<div>";
        return $html;
    }

    function testimonials()
    {
        return ____post_grid_module(array(
            'id' => 'testimonial-slider',
            'is_slider' => true,
            'number_of_slides' => 1,
            'number_of_slides_tablet' => 1,
            'number_of_slides_mobile' => 1,
            'post_grid_id' => 'testimonial-slider',
            'post_elements' => array(
                array(
                    "_type" => "icon",
                    "icon" => "271265",
                    "icon_color" => "text-accent",
                    "icon_color_custom" => "",
                    "icon_width" => "",
                    "icon_height" => ""
                ),
                array(
                    "_type" => "custom_field_1",
                    "custom_field_key" => "_testimonial_content",
                    "custom_field_type" => "p",
                    "custom_field_class" => "testimonial-content"
                ),
                array(
                    "_type" => "post_title",
                    "text_before" => "-",
                    "text_after" => "",
                    "tag" => "p",
                    "text_color" => "",
                    "text_color_custom" => ""
                )
            ),
            'post_type' => array(
                array(
                    "_type" => "testimonials",
                )
            )
        ));
    }
}
$Shortcodes = new Shortcodes;
add_shortcode('taxonomy_terms', array($Shortcodes, 'taxonomy_terms'));
add_shortcode('breadcrumbs', array($Shortcodes, 'breadcrumbs'));
add_shortcode('product_grid_display', array($Shortcodes, 'product_grid_display'));
add_shortcode('case_study_slider_grid', array($Shortcodes, 'case_study_slider_grid'));
add_shortcode('layouts', array($Shortcodes, 'layouts'));
add_shortcode('blog_meta', array($Shortcodes, 'blog_meta'));
add_shortcode('social_share', array($Shortcodes, 'social_share'));
add_shortcode('post_link', array($Shortcodes, 'post_link'));
add_shortcode('related_posts', array($Shortcodes, 'related_posts'));
add_shortcode('latest_from_coptrz', array($Shortcodes, 'latest_from_coptrz'));
add_shortcode('reviews', array($Shortcodes, 'reviews'));
add_shortcode('socials', array($Shortcodes, 'socials'));
add_shortcode('site_logo', array($Shortcodes, 'site_logo'));
add_shortcode('event_countdown', array($Shortcodes, 'event_countdown'));
add_shortcode('product_compare', array($Shortcodes, 'product_compare'));
add_shortcode('drone_servicing', array($Shortcodes, 'drone_servicing'));
add_shortcode('brands_logo_slider', array($Shortcodes, 'brands_logo_slider'));
add_shortcode('testimonials', array($Shortcodes, 'testimonials'));
