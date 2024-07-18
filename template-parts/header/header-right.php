<?php
$SVG = new SVG;
?>
<div class="col-auto d-flex align-items-center">
    <div class="row g-4 header-right">
        <div class="col-auto d-flex align-items-center">
            <a href="<?= get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" class="header-icon account-icon text-white d-flex align-items-center">
                <?= $SVG->user(); ?>
            </a>
        </div>
        <div class="col-auto d-flex align-items-center">
            <div class="mini-cart-wrapper">
                <a href="#" id="mini-cart-button" class="header-icon cart-icon text-white d-flex align-items-center">
                    <?= $SVG->cart(); ?>
                    <div class="cart-number">
                        <?= WC()->cart->get_cart_contents_count(); ?>
                    </div>
                </a>
                <div class="mini-cart-holder bg-white rounded-10px mt-10px">
                    <?php woocommerce_mini_cart() ?>
                </div>
            </div>
        </div>
        <div class="col-auto d-flex align-items-center d-lg-none">
            <button class="menu-burger" type="button" data-bs-toggle="offcanvas" data-bs-target="#offCanvasMenu" aria-controls="offCanvasMenu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
        <div class="col-auto button-box button-accent button-small d-none d-lg-block">
            <button class="rounded-10px" data-bs-toggle="modal" data-bs-target="#modal-268179" target="_self">Enquire Now</button>
        </div>
    </div>
</div>