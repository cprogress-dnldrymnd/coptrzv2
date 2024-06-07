<?php
$source = $module['source'];
echo $source;
?>
<section class="post-grid">
    <div class="container">
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
</section>