<?php
$section_video_url = get_option('section_video_url');
$section_video_heading = get_option('section_video_heading');
$section_video_description = get_option('section_video_description');

?>
<?php if (current_user_can('administrator') && isset($_GET['customize_changeset_uuid'])) { ?>
    <div class="section-label">
        Section Video
    </div>
<?php } ?>
<section class="section section-0 normal-margins xl-padding-top background-overlay default text-white sm-margin-top  ms-20px me-20px rounded-corner" id="section-0">
    <div class="background-image background-overlay"><video autoplay loop muted src="<?= $section_video_url ?>"></video></div>
    <div class="container">
        <div class="position-relative container-inner xs-padding-top xs-padding-bottom xs-padding-left xs-padding-right">
            <?php if ($section_video_heading) { ?>
                <h3><?= $section_video_heading ?></h3>
            <?php } ?>
            <?php if ($section_video_description) { ?>
                <div class="description-box mx-auto">
                    <?= wpautop($section_video_description) ?>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
