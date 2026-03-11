<?php
$s     = $GLOBALS['ep_section'];
$items = pts1_repeater($s, 'items');
if (!$items) return;
$bc = pts1_field($s,'border_color','#FF0E0E8F');
$tc = pts1_field($s,'text_color','#FF0E0E');
?>
<section class="chip my-6" style="--border-color:<?=esc_attr($bc)?>;--text-color:<?=esc_attr($tc)?>">
    <div class="container"><div class="container-wrapper">
        <?php if($h=pts1_field($s,'heading')): ?><h2 class="fs-24 mb-4"><?=esc_html($h)?></h2><?php endif; ?>
        <div class="row g-3">
            <?php foreach($items as $item): ?>
                <div class="col-lg-6"><div class="inner medium-text text-center"><p><?=esc_html($item['text'])?></p></div></div>
            <?php endforeach; ?>
        </div>
    </div></div>
</section>
