<?php
$SVG = new SVG;
?>
<div class="cart-dropdown me-4">
    <a class="cart-icon" href="<?php echo wc_get_cart_url(); ?>">
        <div class="d-inline-flex align-items-center">
            <span class="cart-subtotal me-4"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
            <span class="cart-icon">
                <?= $SVG->cart() ?>
                <span class="cart-contents-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
            </span>
        </div>
    </a>
    <div class="cart-dropdown-content">
        <?php woocommerce_mini_cart(); ?>
    </div>
</div>