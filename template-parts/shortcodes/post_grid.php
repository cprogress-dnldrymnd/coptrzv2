<div class="post-grid p-4 post-<?= $id ?> <?= $class ?>">
    <?= do_shortcode('[_image id="' . get_post_thumbnail_id($id) . '"]'); ?>
    <?= do_shortcode('[_heading heading="' . get_the_title($id) . '" tag="h3"]') ?>
    <?= do_shortcode('[_description description="' . get_the_excerpt($id) . '" ]') ?>
</div>