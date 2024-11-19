<?php
$section_2_heading = get_option('section_2_heading');
$section_2_description = get_option('section_2_description');

$col_num = 2;

$section_2_col_1_image = get_option('section_2_col_1_image');
$section_2_col_1_heading = get_option('section_2_col_1_heading');
$section_2_col_1_description = get_option('section_2_col_1_description');

$section_2_col_2_image = get_option('section_2_col_2_image');
$section_2_col_2_heading = get_option('section_2_col_2_heading');
$section_2_col_2_description = get_option('section_2_col_2_description');
?>

<?php if (current_user_can('administrator')) { ?>
    <div class="section-label">
        Section 2
    </div>
<?php } ?>
<section class="section section-2 background-gradient-default md-padding-top md-padding-bottom  text-center" id="section-2">
    <div class="container">
        <?php if ($section_2_heading) { ?>
            <h2 class="text-center"><?= $section_2_heading ?></h2>
        <?php } ?>
        <?php if ($section_2_description) { ?>
            <div class="description-box mx-auto" style="max-width: 1000px;">
                <?= wpautop($section_2_description) ?>
            </div>
        <?php } ?>
        <div class="row align-items-center gx-6 gy-6">
            <div class="col-lg-6">
                <div class=" text-lg-start text-md-start text-start column-holder content-margin overflow-hidden position-relative h-100">
                    <div class="description-box">
                        <?php if ($section_2_col_1_heading) { ?>
                            <h3><?= $section_2_col_1_heading ?></h3>
                        <?php } ?>

                        <?php if ($section_2_col_1_description) { ?>
                            <?= wpautop($section_2_col_1_description) ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class=" text-lg-start text-md-start text-start column-holder content-margin overflow-hidden position-relative h-100">
                    <?php if ($section_2_col_1_image) { ?>
                        <div class="image-box rounded-corner">
                            <?php
                            $section_2_col_1_image_id = attachment_url_to_postid($section_2_col_1_image);
                            echo wp_get_attachment_image($section_2_col_1_image_id, 'large');
                            ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="row align-items-center gx-6 gy-6">
            <div class="col-lg-6">
                <div class=" text-lg-start text-md-start text-start column-holder content-margin overflow-hidden position-relative h-100">
                    <?php if ($section_2_col_2_image) { ?>
                        <div class="image-box rounded-corner">
                            <?php
                            $section_2_col_2_image_id = attachment_url_to_postid($section_2_col_2_image);
                            echo wp_get_attachment_image($section_2_col_2_image_id, 'large');
                            ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class=" text-lg-start text-md-start text-start column-holder content-margin overflow-hidden position-relative h-100">
                    <div class="description-box">
                        <?php if ($section_2_col_2_heading) { ?>
                            <h3><?= $section_2_col_2_heading ?></h3>
                        <?php } ?>

                        <?php if ($section_2_col_2_description) { ?>
                            <?= wpautop($section_2_col_2_description) ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>