<?php
$s     = $GLOBALS['ep_section'];
$items = pts1_repeater($s, 'items');
?>
<section class="chip-v2 bg-accent-2 text-white py-6">
    <div class="container"><div class="container-wrapper">
        <?php if($h=pts1_field($s,'heading')): ?><h2 class="heading-style"><?=esc_html($h)?></h2><?php endif; ?>
        <?php if($sh=pts1_field($s,'subheading')): ?><h3 class="text-accent mb-4"><?=esc_html($sh)?></h3><?php endif; ?>
        <?php if($d=pts1_field($s,'description')): ?><div class="desc-box mb-5"><p><?=esc_html($d)?></p></div><?php endif; ?>
        <?php if($items): ?>
        <div class="row g-3">
            <div class="col-lg-4">
                <h3><?=esc_html(pts1_field($s,'col_label','Our Enterprise Proof Points'))?></h3>
                <?php if(($bt=pts1_field($s,'btn_text'))&&($bu=pts1_field($s,'btn_url'))): ?>
                    <div class="button-accent col-auto button-box d-none d-lg-block"><a class="rounded-10px" href="<?=esc_url($bu)?>"><?=esc_html($bt)?></a></div>
                <?php endif; ?>
            </div>
            <div class="col-lg-8">
                <div class="chip" style="--border-color:#2DA1FF;--text-color:#FFFFFF">
                    <div class="row g-3">
                        <?php foreach($items as $item): ?>
                            <div class="col-lg-6"><div class="inner h-100 text-center d-flex align-items-center justify-content-center"><p><?=wp_kses_post($item['text'])?></p></div></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if(($bt=pts1_field($s,'btn_text'))&&($bu=pts1_field($s,'btn_url'))): ?>
            <div class="button-accent col-auto button-box mt-5 d-block d-lg-none text-center"><a class="rounded-10px" href="<?=esc_url($bu)?>"><?=esc_html($bt)?></a></div>
        <?php endif; ?>
        <?php endif; ?>
    </div></div>
</section>
