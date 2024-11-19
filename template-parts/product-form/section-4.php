<?php
$section_4_heading = get_option('section_4_heading');
$section_4_description = get_option('section_4_description');

$section_4_col_1_image = get_option('section_4_col_1_image');
$section_4_col_1_heading = get_option('section_4_col_1_heading');
$section_4_col_1_description = get_option('section_4_col_1_description');

$section_4_col_2_image = get_option('section_4_col_2_image');
$section_4_col_2_heading = get_option('section_4_col_2_heading');
$section_4_col_2_description = get_option('section_4_col_2_description');

$col_num = 4;
?>
<section class="section section-4 md-padding-top md-padding-bottom  text-center background-gradient-default" id="section-4">
    <div class="container">
        <?php if ($section_4_heading) { ?>
            <h2 class="text-center"><?= $section_4_heading ?></h2>
        <?php } ?>
        <?php if ($section_4_description) { ?>
            <div class="description-box mx-auto" style="max-width: 1000px;">
                <?= wpautop($section_4_description) ?>
            </div>
        <?php } ?>
        <div class="row g-xs-10px g-4">
            <?php for ($col_num = 1; $col_num <= 4; $col_num++) { ?>
                <?php
                $section_4_col_image = get_option('section_4_col_' . $col_num . '_image');
                $section_4_col_heading = get_option('section_4_col_' . $col_num . '_heading');
                $section_4_col_description = get_option('section_4_col_' . $col_num . '_description');
                ?>
                <div class="col-lg-3 col-md-6">
                    <div class="column-holder content-margin overflow-hidden position-relative h-100">
                        <?php if ($section_4_col_image) { ?>
                            <div class="image-box rounded-corner">
                                <?php
                                $attachment_id = attachment_url_to_postid($section_4_col_image);
                                echo wp_get_attachment_image($attachment_id, 'large');
                                ?>
                            </div>
                        <?php } ?>

                        <?php if ($section_4_col_heading) { ?>
                            <h3 class="small-heading"><?= $section_4_col_heading ?></h3>
                        <?php } ?>

                        <?php if ($section_4_col_description) { ?>
                            <div class="description-box">
                                <?= wpautop($section_4_col_description) ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>