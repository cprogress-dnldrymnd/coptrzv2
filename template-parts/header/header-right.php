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

        <?php

        $button_type = get__theme_option('header_button_type');
        $button_text = get__theme_option('header_button_text');
        $button_url = get__theme_option('header_button_url');
        $button_url_custom = get__theme_option('header_button_url_custom');
        $button_type = get__theme_option('header_button_style');
        $button_target = get__theme_option('header_button_target');
        echo __button(array(
            'button_type' => $button_type,
            'button_text' => $button_text,
            'button_url' => $button_url,
            'button_url_custom' => $button_url_custom,
            'button_style' => $button_style . ' button-accent button-small d-none d-lg-block',
            'button_target' => $button_target,
        ));
        ?>

      
    </div>
</div>