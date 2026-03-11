<?php
$s   = $GLOBALS['ep_section'];
$img = pts1_img($s, 'image_id');
?>
<section class="two-columns my-6">
    <div class="container">
        <?php if ($h = pts1_field($s,'heading')): ?><h2 class="heading-style text-accent-2"><?= esc_html($h) ?></h2><?php endif; ?>
        <div class="row g-3 g-lg-5">
            <?php if ($img): ?>
                <div class="col-lg-6"><div class="inner"><div class="image-box"><img src="<?= esc_url($img['url']) ?>" alt="<?= esc_attr($img['alt']) ?>"></div></div></div>
            <?php endif; ?>
            <div class="col-lg-6"><div class="inner">
                <?php if ($ch = pts1_field($s,'content_heading')): ?><h3><?= esc_html($ch) ?></h3><?php endif; ?>
                <?php if ($c = pts1_field($s,'content')): ?><div class="desc-box"><?= wp_kses_post($c) ?></div><?php endif; ?>
                <?php if (($bt = pts1_field($s,'btn_text')) && ($bu = pts1_field($s,'btn_url'))): ?>
                    <div class="button-accent col-auto button-box mt-3"><a class="rounded-10px" href="<?= esc_url($bu) ?>"><?= esc_html($bt) ?></a></div>
                <?php endif; ?>
            </div></div>
        </div>
    </div>
</section>
