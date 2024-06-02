<?php
$terms = get_terms(array(
    'taxonomy'   => $module['taxonomy_key'],
    'hide_empty' => false,
));
?>
<section class="post-tabs <?= $classes ?>" id="<?= $module_id ?>">
    <div class="container">
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
                                    <?= do_shortcode('[post_grid class="background-primary" id="' . $post->ID . '"]') ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>

            <?php } ?>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade modal-v2 popup-form" id="modal-<?= $module_id ?>" tabindex="-1" aria-labelledby="modalSearchLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content background-white">
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-lg-6">
                        <div class="form-holder p-4 h-100 d-flex align-items-center">
                            <div class="form-inner w-100">
                                <?= do_shortcode(get_the_content(NULL, false, $id)) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 bg-image">
                        <div class="position-relative h-100">
                            <img src="<?= get_the_post_thumbnail_url($id, 'large') ?>" alt="<?= get_the_title($id) ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>