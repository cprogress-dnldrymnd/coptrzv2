<?php get_header(); ?>

<?= do_shortcode(___hero_modules()) ?>
<section class="default-page medium-container md-padding-top md-padding-bottom">
    <div class="container">
        <?php
        the_content();
        ?>
    </div>
</section>

<?php get_footer(); ?>