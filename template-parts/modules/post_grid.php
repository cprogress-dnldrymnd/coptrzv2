<?php
$number_of_columns = $module['number_of_columns'];
$post_elements = $module['post_elements'];
$post_type = $module['post_type'][0]['_type'];
$source = $module['post_type'][0]['source'];
$posts = $module['post_type'][0]['post'];
$category = $module['post_type'][0]['category'];



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
        $args['post_type'] = $post_type;
        $args['posts_per_page'] = -1;


        if ($source == 'category') {
        } else if ($source == 'manually') {
            $args['post__in'] = $posts_ids;
        }
        echo '<pre>';
        var_dump($args);
        echo '</pre>';

        // Get the posts
        $posts_lists = get_posts($args);
        ?>

        <?php if ($posts_lists) { ?>
            <div class="row g-4">
                <?php foreach ($posts_lists as $post) { ?>
                    <div class="<?= $number_of_columns ? $number_of_columns : 'col-lg-4' ?>">
                        <?php
                        $id = $post->ID;
                        include locate_template('template-parts/components/post_grid.php');
                        ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</section>