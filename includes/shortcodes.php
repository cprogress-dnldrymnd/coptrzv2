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
            $html .= "</div>"; //end-swiper-slide
        }



        $html .= "</div>"; //end-swiper-wrapper
        $html .= "<div class='bottom-holder'> <div class='container position-relative'> <div class='row g-4 justify-content-between align-items-end'>";
        $html .= "<div class='col-auto'> <div class='swiper-nav d-flex justify-content-start'> <div class='swiper-button-prev'></div> <div class='swiper-button-next'></div> </div> </div>";

        $html .= "<div class='col-auto'>";
        $html .= "<div class='row button-group-box d-inline-flex'>";
        $html .= __button(array(
            'button_type' => get_post_type($id),
            'button_text' => 'Read Case Study',
            'button_url' => $id,
            'button_style' => 'button-accent' . ' col-auto',
        ));

        $html .= __button(array(
            'button_type' => 'custom',
            'button_text' => 'All Case Studies',
            'button_url_custom' => get_post_type_archive_link('casestudies'),
            'button_style' => 'button-bordered' . ' col-auto',
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
        $reading_time = get__post_meta('reading_time');
?>
        <div class="blog-meta">
            <div class="row">
                <div class="col-auto">
                    <p><strong>Last updated on</strong></p>
                    <p><?= get_the_date() ?></p>
                </div>
                <?php if ($reading_time) { ?>
                    <div class="col-auto">
                        <p><strong>Read time</strong></p>
                        <p><?= $reading_time ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php
        return ob_get_clean();
    }

    function social_share()
    {
        global $post;
        $url = get_permalink($post->ID);
        $title = str_replace(' ', '%20', get_the_title($post->ID));

        $social_buttons = '';
        $social_buttons .= '<div class="social-share-buttons">';

        $social_buttons .= '<a href="#" onclick="window.open(\'https://www.facebook.com/sharer.php?u=' . $url . '&t=' . $title . '\', \'_blank\', \'width=600,height=400\'); return false;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16"> <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/> </svg></a>';

        $social_buttons .= '<a href="#" onclick="window.open(\'https://x.com/share?url=' . $url . '&text=' . $title . '\', \'_blank\', \'width=600,height=400\'); return false;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter-x" viewBox="0 0 16 16">
<path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/>
</svg></a>';
        $social_buttons .= '<a href="#" onclick="window.open(\'https://www.linkedin.com/feed/?linkOrigin=LI_BADGE&shareActive=true&shareUrl=' . $url . '\', \'_blank\', \'width=600,height=400\'); return false;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-linkedin" viewBox="0 0 16 16">
  <path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z"/>
</svg></a>';
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
            'posts_per_page' => 10,

            'post_type'      => array('post'),

            'post_status'    => 'publish',

            'category__and ' => $categories,

            'orderby' => 'rand'

        );
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            $html = "<div class='related-posts-sidebar'>";
            $html = "<div class='row g-3'>";
            while ($query->have_posts()) {
                $query->the_post();
                $data = array(
                    'id' => get_the_ID(),
                    'featured' => false,
                    'col' => true,
                    'style' => 'style-1',
                    'elements' => array('image', 'category', 'date', 'title', 'excerpt', 'button')
                );
                $html .= __post_box_blog($data);
            }
            wp_reset_postdata();
            $html .= "</div>";
            $html .= "</div>";

            return $html;
        }
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
