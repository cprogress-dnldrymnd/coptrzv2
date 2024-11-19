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


<section class="section section-3 md-padding-top md-padding-bottom  text-center background-gradient-default" id="section-3">
    <div class="container">
        <?php if ($section_2_heading) { ?>
            <h2 class="text-center"><?= $section_2_heading ?></h2>
        <?php } ?>
        <?php if ($section_2_description) { ?>
            <div class="description-box mx-auto" style="max-width: 1000px;">
                <?= wpautop($section_2_description) ?>
            </div>
        <?php } ?>
        <div class="row g-xs-10px gx-6 gy-6">
            <?php for ($col_num = 1; $col_num <= 3; $col_num++) { ?>
                <?php
                $section_1_col_image = get_option('section_1_col_' . $col_num . '_image');
                $section_1_col_heading = get_option('section_1_col_' . $col_num . '_heading');
                $section_1_col_description = get_option('section_1_col_' . $col_num . '_description');
                ?>
                <div class="col-lg-6 col-md-6">
                    <div class="column-holder content-margin overflow-hidden position-relative h-100">
                        <div class="image-box rounded-corner" style=""><img width="1024" height="645" src="https://coptrz.com/wp-content/uploads/2024/10/DJI-Neo-Voice-Control-1024x645-1.jpg" class="attachment- size- lazyautosizes ls-is-cached lazyloaded" alt="" decoding="async" loading="lazy" data-src="https://coptrz.com/wp-content/uploads/2024/10/DJI-Neo-Voice-Control-1024x645-1.jpg" data-srcset="https://coptrz.com/wp-content/uploads/2024/10/DJI-Neo-Voice-Control-1024x645-1.jpg 1024w, https://coptrz.com/wp-content/uploads/2024/10/DJI-Neo-Voice-Control-1024x645-1-300x189.jpg 300w, https://coptrz.com/wp-content/uploads/2024/10/DJI-Neo-Voice-Control-1024x645-1-768x484.jpg 768w, https://coptrz.com/wp-content/uploads/2024/10/DJI-Neo-Voice-Control-1024x645-1-500x315.jpg 500w, https://coptrz.com/wp-content/uploads/2024/10/DJI-Neo-Voice-Control-1024x645-1-600x378.jpg 600w" data-sizes="auto" data-eio-rwidth="1024" data-eio-rheight="645" sizes="716px" srcset="https://coptrz.com/wp-content/uploads/2024/10/DJI-Neo-Voice-Control-1024x645-1.jpg 1024w, https://coptrz.com/wp-content/uploads/2024/10/DJI-Neo-Voice-Control-1024x645-1-300x189.jpg 300w, https://coptrz.com/wp-content/uploads/2024/10/DJI-Neo-Voice-Control-1024x645-1-768x484.jpg 768w, https://coptrz.com/wp-content/uploads/2024/10/DJI-Neo-Voice-Control-1024x645-1-500x315.jpg 500w, https://coptrz.com/wp-content/uploads/2024/10/DJI-Neo-Voice-Control-1024x645-1-600x378.jpg 600w"><noscript><img width="1024" height="645" src="https://coptrz.com/wp-content/uploads/2024/10/DJI-Neo-Voice-Control-1024x645-1.jpg" class="attachment- size-" alt="" decoding="async" loading="lazy" srcset="https://coptrz.com/wp-content/uploads/2024/10/DJI-Neo-Voice-Control-1024x645-1.jpg 1024w, https://coptrz.com/wp-content/uploads/2024/10/DJI-Neo-Voice-Control-1024x645-1-300x189.jpg 300w, https://coptrz.com/wp-content/uploads/2024/10/DJI-Neo-Voice-Control-1024x645-1-768x484.jpg 768w, https://coptrz.com/wp-content/uploads/2024/10/DJI-Neo-Voice-Control-1024x645-1-500x315.jpg 500w, https://coptrz.com/wp-content/uploads/2024/10/DJI-Neo-Voice-Control-1024x645-1-600x378.jpg 600w" sizes="(max-width: 1024px) 100vw, 1024px" data-eio="l" /></noscript></div>
                        <h3 class="small-heading">Voice Control</h3>
                        <div class="description-box">
                            <p>"Hey Fly"- Wake the DJI Fly App with these words to enable voice control and pilot DJI Neo with spoken flight directives.</p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>