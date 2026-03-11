<?php
$s  = $GLOBALS['ep_section'];
$bg = pts1_img($s, 'bg_image_id');
?>
<section class="cta--section text-white my-6">
    <div class="container">
        <div class="container-wrapper bg-primary position-relative lg-padding-top lg-padding-bottom sm-padding-left sm-padding-right rounded overflow-hidden" style="--bs-border-radius:20px">
            <?php if ($bg): ?><div class="background-image no-overlay d-none d-lg-block"><img src="<?= esc_url($bg['url']) ?>" alt="<?= esc_attr($bg['alt']) ?>"></div><?php endif; ?>
            <div class="inner position-relative small-width">
                <?php if ($h=pts1_field($s,'heading')): ?><h2><?= esc_html($h) ?></h2><?php endif; ?>
                <?php if ($d=pts1_field($s,'description')): ?><div class="desc-box mb-4"><p><?= esc_html($d) ?></p></div><?php endif; ?>
                <?php if (($bt=pts1_field($s,'btn_text'))&&($bu=pts1_field($s,'btn_url'))): ?>
                    <div class="button-accent col-auto button-box mt-5"><a class="rounded-10px" href="<?= esc_url($bu) ?>"><?= esc_html($bt) ?></a></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
