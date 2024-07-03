<?php
function action_widgets_init()
{

    register_sidebar(
        array(
            'name'          => 'Blog Sidebar',
            'id'            => 'blog_sidebar',
            'before_widget' => '<div class="widget-inner">',
            'after_widget'  => '</div>',
            'before_title'  => '<h5 class="widget-title">',
            'after_title'   => '</h5>',
        )
    );
    register_sidebar(
        array(
            'name'          => 'Blog Single Sidebar',
            'id'            => 'blog_single_sidebar',
            'before_widget' => '<div class="widget-inner">',
            'after_widget'  => '</div>',
            'before_title'  => '<h5 class="widget-title">',
            'after_title'   => '</h5>',
        )
    );
    register_sidebar(
        array(
            'name'          => 'Footer Column 1',
            'id'            => 'footer_column_1',
            'before_widget' => '<div>',
            'after_widget'  => '</div>',
            'before_title'  => '<h5 class="widget-title">',
            'after_title'   => '</h5>',
        )
    );


    register_sidebar(
        array(
            'name'          => 'Footer Column 2',
            'id'            => 'footer_column_2',
            'before_widget' => '<div>',
            'after_widget'  => '</div>',
            'before_title'  => '<h5 class="widget-title">',
            'after_title'   => '</h5>',
        )
    );

    register_sidebar(
        array(
            'name'          => 'Footer Column 3',
            'id'            => 'footer_column_3',
            'before_widget' => '<div>',
            'after_widget'  => '</div>',
            'before_title'  => '<h5 class="widget-title">',
            'after_title'   => '</h5>',
        )
    );

    register_sidebar(
        array(
            'name'          => 'Footer Column 4',
            'id'            => 'footer_column_4',
            'before_widget' => '<div>',
            'after_widget'  => '</div>',
            'before_title'  => '<h5 class="widget-title">',
            'after_title'   => '</h5>',
        )
    );


    register_sidebar(
        array(
            'name'          => 'Footer Column 5',
            'id'            => 'footer_column_5',
            'before_widget' => '<div>',
            'after_widget'  => '</div>',
            'before_title'  => '<h5 class="widget-title">',
            'after_title'   => '</h5>',
        )
    );

    register_sidebar(
        array(
            'name'          => 'Footer Bottom Left',
            'id'            => 'footer_bottom_left',
            'before_widget' => '<div>',
            'after_widget'  => '</div>',
            'before_title'  => '<h5 class="widget-title">',
            'after_title'   => '</h5>',
        )
    );

    register_sidebar(
        array(
            'name'          => 'Footer Bottom Right',
            'id'            => 'footer_bottom_right',
            'before_widget' => '<div>',
            'after_widget'  => '</div>',
            'before_title'  => '<h5 class="widget-title">',
            'after_title'   => '</h5>',
        )
    );

    register_sidebar(
        array(
            'name'          => 'Header Right',
            'id'            => 'header_right',
            'before_widget' => '<div>',
            'after_widget'  => '</div>',
            'before_title'  => '<h5 class="widget-title">',
            'after_title'   => '</h5>',
        )
    );


    register_sidebar(
        array(
            'name'          => 'Woocommerce Listing Sidebar',
            'id'            => 'woocommerce_sidebar',
            'before_widget' => '<div>',
            'after_widget'  => '</div>',
            'before_title'  => '<h5 class="widget-title">',
            'after_title'   => '</h5>',
        )
    );
}
add_action('widgets_init', 'action_widgets_init');


// Creating the widget
class featured_item extends WP_Widget
{

    function __construct()
    {

        add_action('admin_enqueue_scripts', array($this, 'scripts'));

        parent::__construct(
            'featured_item',
            __('Featured Item', 'featured_item_domain'),
            array(
                'description' => __('Featured Item', 'featured_item_domain'),
            )
        );
    }

    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', empty($instance['title']) ? __('Default title', 'text_domain') : $instance['title']);
        $image = !empty($instance['image']) ? $instance['image'] : '';
        $url = !empty($instance['url']) ? $instance['url'] : '';
        ob_start();
?>

        <?php if ($image) : ?>
            <div class="featured-menu-item">
                <?php if ($url) { ?>
                    <a href="<?= $url ?>">
                    <?php } ?>
                    <div class="image-box image-absolute">
                        <img src="<?= esc_url($image) ?>" alt="<?= $title ?>">
                    </div>
                    <div class="heading-box">
                        <h5>
                            <?= $title ?>
                        </h5>
                    </div>
                    <?php if ($url) { ?>
                    </a>
                <?php } ?>
            </div>
        <?php endif; ?>

    <?php
        echo $args['after_widget'];
        ob_end_flush();
    }
    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : __('New title', 'text_domain');
        $image = !empty($instance['image']) ? $instance['image'] : '';
        $url = !empty($instance['url']) ? $instance['url'] : '';
    ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('URL:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="text" value="<?php echo esc_attr($url); ?>">
        </p>

        <p>
            <?php if ($image) { ?>
                <img style=" max-width: 150px; display: block; margin-bottom: 10px;" src="<?= $image ?>" alt="">
            <?php } ?>
            <input style="display: none" lass="widefat" id="<?php echo $this->get_field_id('image'); ?>" name="<?php echo $this->get_field_name('image'); ?>" type="text" value="<?php echo esc_url($image); ?>" />
            <button style="width: 100%" class="upload_image_button button button-primary">Upload Image</button>
        </p>
<?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['image'] = (!empty($new_instance['image'])) ? $new_instance['image'] : '';
        $instance['url'] = (!empty($new_instance['url'])) ? $new_instance['url'] : '';

        return $instance;
    }

    public function scripts()
    {
        wp_enqueue_script('media-upload');
        wp_enqueue_media();
        wp_enqueue_script('media-upload-js', get_template_directory_uri() . '/admin/js/media-upload-js.js', array('jquery'));
    }
}

function wpb_load_widget()
{
    register_widget('featured_item');
}
add_action('widgets_init', 'wpb_load_widget');
