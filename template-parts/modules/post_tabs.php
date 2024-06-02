<?php
$terms = get_terms(array(
    'taxonomy'   => $module['taxonomy_key'],
    'hide_empty' => false,
));
?>
<section class="post-tabs <?= $classes ?>" id="<?= $module_id ?>">
    <div class="container">
        <ul class="nav nav-tabs" id="post-tab-<?= $module_id ?>" role="tablist">
            <?php foreach ($terms as $key => $term) { ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $key == 0 ? 'active' : '' ?>" id="home-tab" data-bs-toggle="tab" data-bs-target="#term-<?= $term->term_id ?>" type="button" role="tab" aria-controls="tab-<?= $term->term_id ?>" aria-selected="<?= $key == 0 ? 'true' : 'false' ?>">
                        <?= $term->name ?>
                    </button>
                </li>

            <?php } ?>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">...</div>
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">...</div>
            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">...</div>
        </div>
    </div>
</section>