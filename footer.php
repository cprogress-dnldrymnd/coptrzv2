<?php
$SVG = new SVG;
$args = array(
    'numberposts' => -1,
    'post_type' => 'layouts',
    'fields' => 'ids',
    'orderby' => 'menu_order',
    'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => '_display_location',
            'value' => 'before_footer',
        ),
    ),
);
$layouts = get_posts($args);
foreach ($layouts as $layout) {
    echo do_shortcode("[layouts id='$layout']");
}


$hide_footer = get__post_meta('hide_footer');
if (!$hide_footer) {
?>

    <footer id="footer" class="bg-black text-white small-text">
        <div class="footer-top">
            <div class="container">
                <div class="inner rounded-10px">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-auto">
                            <a class="site-logo" href="#">

                            </a>
                        </div>
                        <div class="col-auto">
                            <?= do_shortcode('[socials]') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div class="footer-columns sm-padding-top sm-padding-bottom">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg col-md-12">
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
                    <div class="col-lg col-md-6">
                        <?php dynamic_sidebar('footer_column_5') ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-6">
                        <?php dynamic_sidebar('footer_bottom_left') ?>
                    </div>
                    <div class="col-lg-6 footer-right text-end">
                        <?php dynamic_sidebar('footer_bottom_right') ?>
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