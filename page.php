<?php get_header(); ?>

<?= ___hero_modules() ?>
<section class="default-page">
    <div class="container">
        <?php
        the_content();
        ?>
    </div>
</section>

<?php get_footer(); ?>