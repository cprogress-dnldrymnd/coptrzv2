<?php
$section_1_heading_prefix = get_option('section_1_heading_prefix');
$section_1_heading = get_option('section_1_heading');
$section_1_description = get_option('section_1_description');

$section_1_col_1_image = get_option('section_1_col_1_image');
$section_1_col_1_heading = get_option('section_1_col_1_heading');
$section_1_col_1_description = get_option('section_1_col_1_description');
?>
<section class="section section-1 md-padding-top md-padding-bottom  text-center" id="section-1">
    <?php if (current_user_can('administrator')) { ?>
        <div class="section-label">
            Section 1
        </div>
    <?php } ?>
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

        <div class="row g-xs-10px g-4">
            <div class="col-lg-4 col-md-6">
                <div class="column-holder content-margin overflow-hidden position-relative h-100">

                    <?php if ($section_1_description) { ?>
                        <div class="image-box rounded-corner">
                            <?php
                            $attachment_id = attachment_url_to_postid($section_1_col_1_image);
                            echo wp_get_attachment_image($attachment_id, 'large');
                            ?>
                        </div>
                    <?php } ?>

                    <?php if ($section_1_col_1_heading) { ?>
                        <h3 class="small-heading"><?= $section_1_col_1_heading ?></h3>
                    <?php } ?>
                    <div class="description-box">
                        <p>DJI Neo gracefully takes off and lands from your palm. Simply press the mode button on Neo, select your desired shooting mode, and Neo automatically does the rest to capture impressive footage, all without a remote controller!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>