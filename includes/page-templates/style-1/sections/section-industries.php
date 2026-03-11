<?php
$s     = $GLOBALS['ep_section'];
$items = pts1_repeater($s, 'items');
if (!$items) return;
?>
<section class="industries bg-light-3 py-6">
    <div class="container"><div class="container-wrapper">
        <?php if ($h=pts1_field($s,'heading')): ?><h2 class="heading-style text-accent-2"><?= esc_html($h) ?></h2><?php endif; ?>
        <?php if ($d=pts1_field($s,'description')): ?><div class="desc-box mb-5"><p><?= esc_html($d) ?></p></div><?php endif; ?>
        <div class="row g-4 same-image-height justify-content-center row-global-post" style="--image-padding:35%">
            <?php foreach ($items as $item):
                $img = $item['image_id'] ? pts1_img(['image_id'=>$item['image_id']], 'image_id') : null;
            ?>
                <div class="col-lg-4 col-md-6">
                    <div class="inner image-animation-zoom content-margin">
                        <div class="image-box rounded-corner overflow-hidden">
                            <a class="text-inherit text-decoration-none" href="<?= esc_url($item['url']) ?>">
                                <?php if ($img): ?><img src="<?= esc_url($img['url']) ?>" alt="<?= esc_attr($img['alt']) ?>"><?php endif; ?>
                            </a>
                        </div>
                        <div class="content-box content-margin">
                            <a class="text-inherit text-decoration-none" href="<?= esc_url($item['url']) ?>">
                                <h4 class="fs-22 fw-semibold text-accent-2"><?= esc_html($item['title']) ?></h4>
                            </a>
                            <?php if (!empty($item['description'])): ?><div class="description-box small-text"><p><?= esc_html($item['description']) ?></p></div><?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (($bt=pts1_field($s,'btn_text'))&&($bu=pts1_field($s,'btn_url'))): ?>
            <div class="button-accent col-auto button-box text-center mt-5"><a class="rounded-10px" href="<?= esc_url($bu) ?>"><?= esc_html($bt) ?></a></div>
        <?php endif; ?>
    </div></div>
</section>
