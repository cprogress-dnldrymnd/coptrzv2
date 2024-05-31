<?php
$term = get_queried_object();
$featured_boxes = carbon_get_term_meta($term->term_id, 'featured_boxes');
?>
<?php if ($featured_boxes) { ?>
    <section class="featured-boxes overflow-visible">
        <div class="container">
            <div class="featured-boxes-holder">
                <div class="row">
                    <?php foreach ($featured_boxes as $featured_box) { ?>
                        <div class="col-lg-6">
                            <?php
                            if ($featured_box['link']) {
                                echo '<a href="' . $featured_box['link'] . '">';
                            }
                            ?>
                            <div class="column-holder text-center bg-image"
                                style="background-image: url(<?= wp_get_attachment_image_url($featured_box['image'], 'large') ?>)">

                                <div class="heading-box">
                                    <span class="prefix"><?= $featured_box['prefix'] ?></span>
                                    <h4><?= $featured_box['heading'] ?></h4>
                                </div>

                                <div class="description-box">
                                    <p>
                                        <?= $featured_box['description'] ?>
                                    </p>
                                </div>

                            </div>
                            <?php
                            if ($featured_box['link']) {
                                echo '</a>';
                            }
                            ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
<?php } ?>