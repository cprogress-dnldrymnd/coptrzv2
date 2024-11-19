<?php
$section_3_heading = get_option('section_3_heading');
$section_3_description = get_option('section_3_description');

$section_3_col_1_image = get_option('section_3_col_1_image');
$section_3_col_1_heading = get_option('section_3_col_1_heading');
$section_3_col_1_description = get_option('section_3_col_1_description');

$section_3_col_2_image = get_option('section_3_col_2_image');
$section_3_col_2_heading = get_option('section_3_col_2_heading');
$section_3_col_2_description = get_option('section_3_col_2_description');

$col_num = 4;

?>

<?php if (current_user_can('administrator')) { ?>
    <div class="section-label">
        Section 3
    </div>
<?php } ?>
<section class="section section-3 md-padding-top md-padding-bottom  text-center background-gradient-default" id="section-3">
    <div class="container">
        <?php if ($section_3_heading) { ?>
            <h2 class="text-center"><?= $section_3_heading ?></h2>
        <?php } ?>
        <?php if ($section_3_description) { ?>
            <div class="description-box mx-auto" style="max-width: 1000px;">
                <?= wpautop($section_3_description) ?>
            </div>
        <?php } ?>
        <div class="row g-xs-10px gx-6 gy-6">
            <?php for ($col_num = 1; $col_num <= 3; $col_num++) { ?>
                <?php
                $section_3_col_image = get_option('section_3_col_' . $col_num . '_image');
                $section_3_col_heading = get_option('section_3_col_' . $col_num . '_heading');
                $section_3_col_description = get_option('section_3_col_' . $col_num . '_description');
                ?>
                <div class="col-lg-6 col-md-6">
                    <div class="column-holder content-margin overflow-hidden position-relative h-100">
                        <?php if ($section_3_col_image) { ?>
                            <div class="image-box rounded-corner">
                                <?php
                                $attachment_id = attachment_url_to_postid($section_3_col_image);
                                echo wp_get_attachment_image($attachment_id, 'large');
                                ?>
                            </div>
                        <?php } ?>

                        <?php if ($section_3_col_heading) { ?>
                            <h3 class="small-heading"><?= $section_3_col_heading ?></h3>
                        <?php } ?>

                        <?php if ($section_3_col_description) { ?>
                            <div class="description-box">
                                <?= wpautop($section_3_col_description) ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>