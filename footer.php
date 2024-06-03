><?php

    $hide_footer = get__post_meta('hide_footer');
    if (!$hide_footer) {
    ?>
<?= do_shortcode('[coptrz_review]') ?>
<footer id="footer" class="background-primary">
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
</footer>

<?= do_shortcode('[popup id=268179]') ?>
<?php } ?>
<?php wp_footer(); ?>
</body>

</html>