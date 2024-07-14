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
            'name'          => 'Woocommerce Sidebar',
            'id'            => 'woocommerce_sidebar',
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

    $index = 1;
    while ($index < 5) {
        $before_title = "<h4 class='accordion-header widget-title' id='footerCol$index>";
        $before_title .= "<button class='accordion-button collapsed d-block d-lg-none' type='button' data-bs-toggle='collapse' data-bs-target='#collapse$index' aria-expanded='false' aria-controls='collapse$index'>";
        $after_title = "</button>";
        $after_title .= "</h4>";

        $before_widget = "<div id='collapse$index' class='accordion-collapse collapse' aria-labelledby='footerCol$index' data-bs-parent='#accordionFooter'>";
        $before_widget .= " <div class='accordion-body p-0'>";

        $after_widget = "</div></div>";

        register_sidebar(
            array(
                'name'          => "Footer Column $index",
                'id'            => "footer_column_$index",
                'before_widget' => $before_widget,
                'after_widget'  => $after_widget,
                'before_title'  => $before_title,
                'after_title'   => $after_title,
            )
        );
        $i++;
    }

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
}
add_action('widgets_init', 'action_widgets_init');
