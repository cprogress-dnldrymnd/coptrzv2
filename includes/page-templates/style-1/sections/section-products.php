<?php
$s    = $GLOBALS['ep_section'];
$pids = pts1_post_ids($s, 'product_ids');
if (!$pids) return;
static $ep_p_count = 0; $ep_p_count++;
$sid = 'ep-prod-swiper-' . $ep_p_count;
?>
<section class="products-section my-6" id="Related-Products">
    <div class="container"><div class="container-wrapper">
        <?php if ($h=pts1_field($s,'heading')): ?><h2 class="heading-style text-accent-2"><?= esc_html($h) ?></h2><?php endif; ?>
        <?php if ($d=pts1_field($s,'description')): ?><div class="desc-box mb-5"><p><?= esc_html($d) ?></p></div><?php endif; ?>
        <?php if ($sl=pts1_field($s,'sub_label')): ?><h3 class="text-accent mb-4"><?= esc_html($sl) ?></h3><?php endif; ?>
        <div class="swiper-holder">
            <div class="swiper <?= esc_attr($sid) ?> swiper--style-v2 swiper--style-v2-dark">
                <div class="swiper-wrapper">
                    <?php foreach ($pids as $pid): ?>
                        <div class="swiper-slide"><?= _product_grid_display($pid) ?></div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <?php if (($bt=pts1_field($s,'btn_text'))&&($bu=pts1_field($s,'btn_url'))): ?>
            <div class="button-accent col-auto button-box text-center mt-5"><a class="rounded-10px" href="<?= esc_url($bu) ?>"><?= esc_html($bt) ?></a></div>
        <?php endif; ?>
    </div></div>
</section>
<script>
(function(){ new Swiper('.<?= esc_js($sid) ?>', {loop:true,spaceBetween:20,autoplay:false,breakpoints:{0:{slidesPerView:1},576:{slidesPerView:2},768:{slidesPerView:3},992:{slidesPerView:4}},pagination:{el:'.swiper-pagination',clickable:true}}); })();
</script>
