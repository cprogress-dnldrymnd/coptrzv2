<?php
$SVG = new SVG;
global $layouts_global;
$hidden_layouts = get__post_meta('hidden_layouts');
$args = array(
    'numberposts' => -1,
    'post_type' => 'layouts',
    'fields' => 'ids',
    'exclude' => $hidden_layouts,
    'orderby' => 'menu_order',
    'order' => 'ASC',
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key' => '_display_location',
            'value' => 'before_footer',
        ),
    ),
);


$layouts = get_posts($args);
foreach ($layouts as $layout) {
    $do_not_display_on = get__post_meta_by_id($layout, 'do_not_display_on');
    if (is_404()) {
        if ($do_not_display_on != '404') {
            echo do_shortcode("[layouts id='$layout']");
            $layouts_global[] = $layout;
        }
    } else {
        echo do_shortcode("[layouts id='$layout']");
        $layouts_global[] = $layout;
    }
}
$hide_footer = get__post_meta('hide_footer');
if (!$hide_footer) {
?>

    <footer id="footer" class="bg-black text-white small-text">
        <div class="footer-top">
            <div class="container">
                <div class="inner rounded-10px">
                    <div class="row g-3 justify-content-between align-items-center">
                        <div class="col-auto">
                            <?= do_shortcode('[site_logo]') ?>
                        </div>
                        <div class="col-auto">
                            <div class="socials">
                                <?= do_shortcode('[socials]') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-columns sm-padding-top sm-padding-bottom">
            <div class="container">
                <div class="accordion accordionFooter" id="accordionFooter">
                    <div class="row g-4">
                        <div class="col-lg col-md-6">
                            <?php dynamic_sidebar('footer_column_1') ?>
                        </div>
                        <div class="col-lg col-md-6">
                            <?php dynamic_sidebar('footer_column_2') ?>
                        </div>
                        <div class="col-lg col-md-6">
                            <?php dynamic_sidebar('footer_column_3') ?>
                        </div>
                        <div class="col-lg col-md-6">
                            <?php dynamic_sidebar('footer_column_4') ?>
                        </div>
                        <div class="col-lg col-md-12 text-center col-footer-5">
                            <?php dynamic_sidebar('footer_column_5') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="inner">
                    <div class="row g-4 align-items-center justify-content-center justify-content-lg-between">
                        <div class="col-12 col-sm-auto text-center text-lg-start footer-left">
                            <?php dynamic_sidebar('footer_bottom_left') ?>
                        </div>
                        <div class="col-12 col-sm-auto footer-right">
                            <?php dynamic_sidebar('footer_bottom_right') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

<?php } ?>
</main>


<?php wp_footer(); ?>
</body>

</html>