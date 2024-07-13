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
                            <div class="socials">
                                <ul class="d-inline-flex align-items-center m-0 p-0">
                                    <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                                                <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951" />
                                            </svg></a></li>
                                    <li><a href=""></a></li>
                                    <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter-x" viewBox="0 0 16 16">
                                                <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z" />
                                            </svg></a></li>
                                    <li><a href="#"></a></li>
                                    <li><a href="#">
                                            </svg></a></li>
                                    <li><a href="#"></a></li>
                                </ul>
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