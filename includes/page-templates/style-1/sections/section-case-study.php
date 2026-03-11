<?php
$s        = $GLOBALS['ep_section'];
$post_ids = pts1_post_ids($s, 'post_ids');
if (!$post_ids) return;
static $ep_cs_count = 0; $ep_cs_count++;
$sid = 'ep-cs-swiper-' . $ep_cs_count;
$arrow = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="11" viewBox="0 0 20 11"><g opacity="0.5"><path d="M264,6488.27l-5.657-5.27-1.414,1.22,3.243,3.01H244v1.95h16.172l-3.243,3.35,1.414,1.47Z" transform="translate(-300 -6481)" fill="currentColor" fill-rule="evenodd"/></g></svg>';
?>
<section class="case-studies bg-accent-3 py-6 text-white">
    <div class="container"><div class="container-wrapper">
        <?php if ($h=pts1_field($s,'heading')): ?><h2 class="heading-style mb-5"><?= esc_html($h) ?></h2><?php endif; ?>
        <div class="case-study-wrapper same-image-height">
            <div class="swiper swiper--style-v2 <?= esc_attr($sid) ?>">
                <div class="swiper-wrapper">
                    <?php foreach ($post_ids as $pid): ?>
                        <div class="swiper-slide">
                            <div class="swiper-slide-inner bg-accent-7 h-100">
                                <div class="image-box"><?= get_the_post_thumbnail($pid, 'large') ?></div>
                                <div class="content-box text-center p-4">
                                    <h4 class="medium-text fw-medium mb-0"><?= get_the_title($pid) ?></h4>
                                    <a class="fw-medium" href="<?= get_the_permalink($pid) ?>"><span>View Case Study</span><?= $arrow ?></a>
                                </div>
                            </div>
                        </div>
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
(function(){ new Swiper('.<?= esc_js($sid) ?>', {loop:true,autoplay:false,spaceBetween:25,breakpoints:{0:{slidesPerView:1},768:{slidesPerView:2},992:{slidesPerView:3}},pagination:{el:'.swiper-pagination',clickable:true}}); })();
</script>
