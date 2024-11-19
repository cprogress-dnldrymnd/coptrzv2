<?php
$section_1_heading_prefix = get_option('section_1_heading_prefix');
$section_1_heading = get_option('section_1_heading');
$section_1_description = get_option('section_1_description');

$col_num = 3;
?>
<?php if (current_user_can('administrator')) { ?>
    <div class="section-label">
        Section 1
    </div>
<?php } ?>
<section class="section section-1 md-padding-top md-padding-bottom  text-center" id="section-1">
    <div class="container">
        <div class="text-center heading-box">
            <?php if ($section_1_heading_prefix) { ?>
                <span><?= get_option('section_1_heading_prefix'); ?></span>
            <?php } ?>
            <?php if ($section_1_heading) { ?>
                <h2><?= $section_1_heading ?></h2>
            <?php } ?>
        </div>

        <?php if ($section_1_description) { ?>
            <div class="description-box mx-auto" style="max-width: 1000px;">
                <?= wpautop($section_1_description) ?>
            </div>
        <?php } ?>

        <div class="row g-xs-10px g-4 same-image-height">
            <?php for ($col_num = 1; $col_num <= 3; $col_num++) { ?>
                <?php
                $section_1_col_image = get_option('section_1_col_' . $col_num . '_image');
                $section_1_col_heading = get_option('section_1_col_' . $col_num . '_heading');
                $section_1_col_description = get_option('section_1_col_' . $col_num . '_description');
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="column-holder content-margin overflow-hidden position-relative h-100">

                        <?php if ($section_1_col_image) { ?>
                            <div class="image-box rounded-corner">
                                <?php
                                $attachment_id = attachment_url_to_postid($section_1_col_image);
                                echo wp_get_attachment_image($attachment_id, 'large');
                                ?>
                            </div>
                        <?php } ?>

                        <?php if ($section_1_col_heading) { ?>
                            <h3 class="small-heading"><?= $section_1_col_heading ?></h3>
                        <?php } ?>

                        <?php if ($section_1_col_description) { ?>
                            <div class="description-box">
                                <?= wpautop($section_1_col_description) ?>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>