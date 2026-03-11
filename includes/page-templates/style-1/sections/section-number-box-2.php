<?php
$s     = $GLOBALS['ep_section'];
$items = pts1_repeater($s, 'items');
if (!$items) return;
?>
<section class="number-box-section number-box-section-v2 my-6">
    <div class="container">
        <?php if($h=pts1_field($s,'heading')): ?><h2 class="heading-style text-accent-2"><?=esc_html($h)?></h2><?php endif; ?>
        <div class="number-box-wrapper"><div class="row gy-3 gy-lg-0 gx-3">
            <?php foreach($items as $i=>$item): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="number-box d-flex gap-3 align-items-center">
                        <span class="number"><?=$i+1?></span><span><?=esc_html($item['text'])?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div></div>
        <?php if($fn=pts1_field($s,'footer_note')): ?><div class="desc-box mt-4 fw-semibold text-center text-medium"><p><?=esc_html($fn)?></p></div><?php endif; ?>
    </div>
</section>
