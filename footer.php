<?php

$hide_footer = get__post_meta('hide_footer');
if (!$hide_footer) {
?>
    <footer id="footer" class="bg-black text-white">
        <div class="footer-columns md-padding-top xs-padding-bottom">
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
                <div class="row g-4">
                    <div class="col-lg-6">
                        <?php dynamic_sidebar('footer_bottom_left') ?>
                    </div>
                    <div class="col-lg-6 footer-right">
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