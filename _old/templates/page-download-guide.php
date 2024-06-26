<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Download Guide
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<?php while (have_posts()) {
    the_post(); ?>
    <?php the_content() ?>

    <?php
    $guides = carbon_get_the_post_meta('guides');
    $image = get__post_meta('image');
    $count = count($guides);
    list($guides_left, $guides_right) = array_chunk($guides, ceil(count($guides) / 2));
    ?>
    <section class="download-guide md-padding">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-side col-side-start">
                    <div class="column-holder guide-row-holder">
                        <?php foreach ($guides_left as $key => $gl) { ?>
                            <?php
                            $number = $key + 1;
                            ?>
                            <div class="guide-row d-flex align-items-center" target="#box-<?= $number ?>">
                                <div class="number">
                                    0<?= $number ?>
                                </div>
                                <div class="text">
                                    <?= $gl['guide_text'] ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-middle">
                    <div class="column-holder text-center">
                        <div class="image-animation-holder">
                            <div class="box-holder">
                                <?php foreach ($guides as $key => $guide) { ?>
                                    <?php
                                    $number = $key + 1;
                                    ?>
                                    <div class="box" id="box-<?= $number ?>">
                                        <div class="box-inner"></div>
                                    </div>
                                <?php } ?>
                            </div>
                            <img src="<?= wp_get_attachment_image_url($image, 'large') ?>">

                        </div>
                    </div>
                </div>
                <div class="col-side col-side-end">
                    <div class="column-holder guide-row-holder">
                        <?php foreach ($guides_right as $key => $gr) { ?>
                            <?php
                            $number = $key + 1 + count($guides_left);
                            ?>
                            <div class="guide-row guide-row-end d-flex align-items-center" target="#box-<?= $number ?>">
                                <div class="number">
                                    0<?= $number ?>
                                </div>
                                <div class="text">
                                    <?= $gr['guide_text'] ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>
<?php get_footer(); ?>