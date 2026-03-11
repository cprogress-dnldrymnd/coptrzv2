<?php $items = pts1_repeater($GLOBALS['ep_section'], 'items'); if (!$items) return; ?>
<section class="usp-bar bg-accent-1 text-white text-center py-20 px-3" aria-label="Company Trust Signals">
    <div class="container-fluid"><div class="row g-3 justify-content-between">
        <?php foreach ($items as $item): ?>
            <div class="col-lg-3 col-md-6">
                <?php if ($item['icon'] === 'star'): ?>
                    <span class="usp-bar__icon usp-bar__icon--star">★</span>
                <?php else: ?>
                    <span class="usp-bar__icon usp-bar__icon--dot">•&nbsp;</span>
                <?php endif; ?>
                <span><?php if ($item['bold_text']): ?><strong><?= esc_html($item['bold_text']) ?></strong><?php endif; ?> <?= esc_html($item['normal_text']) ?></span>
            </div>
        <?php endforeach; ?>
    </div></div>
</section>
