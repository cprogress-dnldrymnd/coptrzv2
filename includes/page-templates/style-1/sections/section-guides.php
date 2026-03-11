<?php
$s     = $GLOBALS['ep_section'];
$items = pts1_repeater($s, 'items');
if (!$items) return;
$arrow = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="11" viewBox="0 0 20 11"><g opacity="0.5"><path d="M264,6488.27l-5.657-5.27-1.414,1.22,3.243,3.01H244v1.95h16.172l-3.243,3.35,1.414,1.47Z" transform="translate(-300 -6481)" fill="currentColor" fill-rule="evenodd"/></g></svg>';
?>
<section class="guides bg-accent-3 py-6 text-white">
    <div class="container"><div class="container-wrapper">
        <?php if ($h = pts1_field($s,'heading')): ?><h2 class="heading-style"><?= esc_html($h) ?></h2><?php endif; ?>
        <?php if ($d = pts1_field($s,'description')): ?><div class="desc-box mb-5"><p><?= esc_html($d) ?></p></div><?php endif; ?>
        <div class="guides-wrapper">
            <?php if ($sl = pts1_field($s,'section_label')): ?><h3 class="text-accent mb-4"><?= esc_html($sl) ?></h3><?php endif; ?>
            <div class="guides-items">
                <?php foreach ($items as $item):
                    $img = pts1_img(['image_id' => $item['image_id'] ?? 0], 'image_id', 'thumbnail');
                    $ll  = $item['link_label'] ?: 'Access Guide';
                ?>
                    <div class="guide-item-holder">
                        <a href="<?= esc_url($item['url']) ?>" class="guide-item d-flex align-items-center gap-4">
                            <div class="guide-item-left d-flex align-items-center gap-4">
                                <?php if ($img): ?><div class="image-box"><img src="<?= esc_url($img['url']) ?>" alt="<?= esc_attr($img['alt']) ?>"></div><?php endif; ?>
                            </div>
                            <div class="guide-item-right gap-3 flex-column flex-lg-row flex-grow-1 d-flex justify-content-between align-items-start align-items-lg-center">
                                <div class="guide-name text-left"><?= esc_html($item['name']) ?></div>
                                <span class="guide-link d-inline-flex align-items-center gap-2"><span><?= esc_html($ll) ?></span><?= $arrow ?></span>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div></div>
</section>
