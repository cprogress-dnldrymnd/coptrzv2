<?php
$s    = $GLOBALS['ep_section'];
$bg   = pts1_img($s, 'bg_image_id', 'full');
$btns = pts1_repeater($s, 'buttons');
?>
<section class="text-white hero pb-50px pt-50px m-0 bg-primary overflow-hidden d-flex align-items-center position-relative" id="hero">
    <?php if ($bg): ?>
        <div class="background-image background-overlay">
            <img width="<?= esc_attr($bg['width']) ?>" height="<?= esc_attr($bg['height']) ?>" src="<?= esc_url($bg['url']) ?>" alt="<?= esc_attr($bg['alt']) ?>">
        </div>
    <?php endif; ?>
    <div class="container">
        <div class="hero-left-content position-relative overflow-hidden hero-bg-mobile small-width">
            <?php if ($h = pts1_field($s, 'heading')): ?><h1 class="large-heading mb-3"><?= esc_html($h) ?></h1><?php endif; ?>
            <?php if ($d = pts1_field($s, 'description')): ?><div class="description-box fw-light mx-auto mb-4"><p><?= esc_html($d) ?></p></div><?php endif; ?>
            <?php if ($btns): ?>
                <div><div class="button-group-box"><div class="row g-3 justify-content-center d-inline-flex">
                    <?php foreach ($btns as $btn): ?>
                        <div class="<?= esc_attr($btn['style'] ?: 'button-accent') ?> col-12 col-sm-auto button-box">
                            <a class="rounded-10px w-100" href="<?= esc_url($btn['url']) ?>" target="_self"><?= esc_html($btn['text']) ?></a>
                        </div>
                    <?php endforeach; ?>
                </div></div></div>
            <?php endif; ?>
        </div>
    </div>
</section>
