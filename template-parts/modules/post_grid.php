<?php
$source = $module['source'];
$posts = $module[$source];
$posts_ids = array();

foreach ($posts as $post) {
    $posts_ids[] = $post['id'];
}


?>
<section class="post-grid position-relative <?= $classes ?>" id="<?= $module_id ?>" style="<?= $style_attribute . $container_width_style_attribute ?>">
    <?php
    _background_image(array(
        'baground_image' => $baground_image,
        'background_image_class' => $background_image_class,
        'background_overlay_image' => $background_overlay_image,
    ));
    ?>
    <div class="container">
        <?php
        _section_heading_description(array(
            'heading' => $module['heading'],
            'description' => $module['description'],
            'text_align' => $module['text_align'],
            'heading_prefix' => $module['heading_prefix'],
            'heading_suffix' => $module['heading_suffix'],
            'tag' => $module['tag'],
            'size' => $module['size'],
            'heading_with_line' => $module['heading_with_line'],
        ));
        ?>
        <?php
        // Build the args
        $args = array(
            'post_type' => $source,
            'post__in' => $posts_ids,
            'posts_per_page' => -1,
        );

        // Get the posts
        $posts = get_posts($args);
        ?>

        <?php if ($posts) { ?>
            <div class="row">
                <?php foreach ($posts as $post) { ?>
                    <div class="col-lg-4">
                        <?= do_shortcode('[post_grid popup_id="' . $popup_id . '" class="background-primary" id="' . $post->ID . '"]') ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</section>