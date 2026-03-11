<?php $s=$GLOBALS['ep_section']; ?>
<section class="cta--section cta-simple bg-accent text-white py-5">
    <div class="container"><div class="container-wrapper"><div class="inner">
        <div class="row g-3 justify-content-between">
            <div class="col-lg-7">
                <?php if($h=pts1_field($s,'heading')): ?><h2><?=esc_html($h)?></h2><?php endif; ?>
                <?php if($d=pts1_field($s,'description')): ?><div class="desc-box"><p><?=esc_html($d)?></p></div><?php endif; ?>
            </div>
            <?php if(($bt=pts1_field($s,'btn_text'))&&($bu=pts1_field($s,'btn_url'))): ?>
                <div class="col-auto"><div class="button-primary col-auto button-box mt-5"><a class="rounded-10px" href="<?=esc_url($bu)?>"><?=esc_html($bt)?></a></div></div>
            <?php endif; ?>
        </div>
    </div></div></div>
</section>
