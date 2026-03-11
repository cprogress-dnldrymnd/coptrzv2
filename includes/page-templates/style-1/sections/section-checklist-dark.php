<?php
$s     = $GLOBALS['ep_section'];
$items = pts1_repeater($s, 'items');
$tick  = '<svg xmlns="http://www.w3.org/2000/svg" width="22.5" height="22.5" viewBox="0 0 22.5 22.5"><circle cx="9" cy="9" r="9" transform="translate(2 2)" fill="#fff"/><path d="M13.25,2A11.25,11.25,0,1,0,24.5,13.25,11.269,11.269,0,0,0,13.25,2Zm5.378,8.662-6.379,6.379a.842.842,0,0,1-1.192,0L7.872,13.858a.843.843,0,1,1,1.193-1.193l2.588,2.588L17.435,9.47a.843.843,0,0,1,1.193,1.192Z" transform="translate(-2 -2)" fill="#2da1ff"/></svg>';
?>
<section class="checklist text-white bg-accent-2 py-6">
    <div class="container"><div class="container-wrapper">
        <?php if ($h = pts1_field($s,'heading')): ?><h2 class="heading-style"><?= esc_html($h) ?></h2><?php endif; ?>
        <?php if ($intro = pts1_field($s,'intro')): ?><div class="desc-box mb-5"><p><?= esc_html($intro) ?></p></div><?php endif; ?>
        <?php if ($items): ?>
            <div class="row fw-medium g-3">
                <?php foreach ($items as $item): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="inner rounded h-100 p-3 bg-accent-6 d-flex gap-3 align-items-start medium-text">
                            <?= $tick ?>
                            <span class="text"><?= esc_html($item['text']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (pts1_field($s,'outro') || pts1_field($s,'btn_text')): ?>
            <div class="row g-3 justify-content-between align-items-center mt-5">
                <?php if ($outro = pts1_field($s,'outro')): ?><div class="col-lg-7"><div class="desc-box"><p><?= esc_html($outro) ?></p></div></div><?php endif; ?>
                <?php if (($bt=pts1_field($s,'btn_text')) && ($bu=pts1_field($s,'btn_url'))): ?>
                    <div class="col-auto"><div class="button-accent col-auto button-box"><a class="rounded-10px" href="<?= esc_url($bu) ?>"><?= esc_html($bt) ?></a></div></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div></div>
</section>
