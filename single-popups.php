<?php get_header() ?>
<br>
<br>
<br>
<br>
<?php while (have_posts()) : the_post(); ?>
    <!-- Button trigger modal -->
    <div class="button-box button-accent">
        <button type="button" data-bs-toggle="modal" data-bs-target="#modal-<?= get_the_ID() ?>">
            Launch Popup
        </button>
    </div>
    <?= do_shortcode('[popup id=' . get_the_ID() . ']') ?>
<?php endwhile; ?>
<?php get_footer() ?>