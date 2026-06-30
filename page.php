<?php get_header(); ?>

<?= do_shortcode(___hero_modules()) ?>

<?php
$container_width = get__post_meta('container_width');
?>

<section class="default-page <?= $container_width ?> md-padding-top md-padding-bottom no-overflow">
    <div class="container">
        <?php
        the_content();
        ?>
    </div>
</section>

<?php get_footer(); ?>