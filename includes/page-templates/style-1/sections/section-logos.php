<?php $s=$GLOBALS['ep_section']; ?>
<section class="logos my-6" aria-label="Trusted by logos">
    <div class="container">
        <div class="wrap flex-column flex-lg-row position-relative overflow-hidden">
            <div class="label"><?=esc_html(pts1_field($s,'label','Partners & Clients'))?></div>
            <?php if($sc=pts1_field($s,'shortcode')): ?><div class="logo-row" aria-hidden="true"><?=do_shortcode($sc)?></div><?php endif; ?>
        </div>
    </div>
</section>
