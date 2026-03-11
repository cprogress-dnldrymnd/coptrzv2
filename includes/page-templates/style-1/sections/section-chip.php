<?php
/* section-chip.php */
$s    = $GLOBALS['ep_section'];
$items = pts1_repeater($s, 'items');
if (!$items) return;
$bc = pts1_field($s,'border_color','#FF0E0E8F');
$tc = pts1_field($s,'text_color','#FF0E0E');
?>
<section class="chip my-6" style="--border-color:<?= esc_attr($bc) ?>;--text-color:<?= esc_attr($tc) ?>">
    <div class="container"><div class="container-wrapper">
        <?php if ($h=pts1_field($s,'heading')): ?><h2 class="heading-style text-accent-2"><?= esc_html($h) ?></h2><?php endif; ?>
        <?php if ($d=pts1_field($s,'description')): ?><div class="desc-box fw-semibold mb-5"><p class="fs-24 fw-semibold"><?= esc_html($d) ?></p></div><?php endif; ?>
        <div class="row g-3">
            <?php foreach ($items as $item): ?>
                <div class="col-lg-6"><div class="inner h-100 text-center"><p><?= esc_html($item['text']) ?></p></div></div>
            <?php endforeach; ?>
        </div>
        <?php if (pts1_field($s,'outro') || pts1_field($s,'btn_text')): ?>
            <div class="row g-3 justify-content-between align-items-center mt-4">
                <?php if ($o=pts1_field($s,'outro')): ?><div class="col-lg-7"><div class="desc-box fw-semibold medium-text"><p><?= esc_html($o) ?></p></div></div><?php endif; ?>
                <?php if (($bt=pts1_field($s,'btn_text'))&&($bu=pts1_field($s,'btn_url'))): ?>
                    <div class="col-auto"><div class="button-accent col-auto button-box"><a class="rounded-10px" href="<?= esc_url($bu) ?>"><?= esc_html($bt) ?></a></div></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div></div>
</section>
