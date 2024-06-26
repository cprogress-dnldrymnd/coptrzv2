<?php
$terms = get_terms(array(
    'taxonomy'   => $module['taxonomy_key'],
    'hide_empty' => false,
    'meta_query' => array(
        [
            'key' => '_menu_order',
        ]
    ),
    'meta_key' => '_menu_order',
    'orderby' => '_menu_order'
));

$popup_id = $module['popup_id'];
global $popups_id;
$popups_id[] = $popup_id;
?>
<section class="post-tabs position-relative <?= $classes ?>" id="<?= $module_id ?>" style="<?= $style_attribute  ?>">
    <?php
    _background_image(array(
        'baground_image' => $baground_image,
        'background_image_class' => $background_image_class,
        'background_overlay_image' => $background_overlay_image,
    ));
    ?>
    <div class="container position-relative <?= $container_width_class ?>  <?= $classes_text_color ?>" style="<?= $container_width_style_attribute ?>">

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

        <ul class="nav nav-tabs mb-5 justify-content-center" id="post-tab-<?= $module_id ?>" role="tablist">
            <?php foreach ($terms as $key => $term) { ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $key == 0 ? 'active' : '' ?>" id="term-<?= $term->term_id ?>-tab" data-bs-toggle="tab" data-bs-target="#term-<?= $term->term_id ?>" type="button" role="tab" aria-controls="tab-<?= $term->term_id ?>" aria-selected="<?= $key == 0 ? 'true' : 'false' ?>">
                        <?= $term->name ?>
                    </button>
                </li>
            <?php } ?>
        </ul>
        <div class="tab-content" id="myTabContent">
            <?php foreach ($terms as $key => $term) { ?>
                <div class="tab-pane fade  <?= $key == 0 ? 'show active' : '' ?>" id="term-<?= $term->term_id ?>" role="tabpanel" aria-labelledby="term-<?= $term->term_id ?>-tab">
                    <?php
                    // Build the args
                    $args = array(
                        'post_type' => $module['post_type_key'],
                        'posts_per_page' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => $module['taxonomy_key'],
                                'field' => 'id',
                                'terms' => $term->term_id,
                            )
                        )
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

            <?php } ?>
        </div>
    </div>
</section>