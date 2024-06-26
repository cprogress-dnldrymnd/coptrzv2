<?php get_header() ?>
<?php while (have_posts()) : the_post(); ?>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-<?= get_the_ID() ?>">
        Launch Popup
    </button>
    <?= do_shortcode('[popup id=' . get_the_ID() . ']') ?>
<?php endwhile; ?>
<?php get_footer() ?>