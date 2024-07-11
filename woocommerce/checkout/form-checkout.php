<?php

/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if (!defined('ABSPATH')) {
    exit;
}



// If checkout registration is disabled and not logged in, the user cannot checkout.
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}

?>

<div class="checkout-section">
    <div class="blockUI blockOverlay custom-ui steps-ui" style="z-index: 1000; border: none; margin: 0px; padding: 0px; width: 100%; height: 100%; top: 0px; left: 0px; background: rgb(255, 255, 255); opacity: 0.6; cursor: default; position: absolute;"></div>
    <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-7">
                    <div class="column-holder py-5">
                        <div class="logo-box">
                            <a href="<?= get_site_url() ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="256" height="26.026" viewBox="0 0 256 26.026">
                                    <g id="Group_170" data-name="Group 170" transform="translate(-99.825 -87.108)">
                                        <g id="Group_172" data-name="Group 172" transform="translate(99.825 87.108)">
                                            <path id="Path_97" data-name="Path 97" d="M137.86,105.141q0,.52-.037,1.3a8.62,8.62,0,0,1-.26,1.673,7.665,7.665,0,0,1-.706,1.784,5.4,5.4,0,0,1-1.376,1.617,7,7,0,0,1-2.25,1.171,10.9,10.9,0,0,1-3.365.446H107.819a10.9,10.9,0,0,1-3.365-.446,6.989,6.989,0,0,1-2.249-1.171,5.391,5.391,0,0,1-1.376-1.617,7.64,7.64,0,0,1-.706-1.784,8.577,8.577,0,0,1-.261-1.673q-.037-.78-.037-1.3V95.1q0-.484.037-1.265a8.577,8.577,0,0,1,.261-1.673,8,8,0,0,1,.706-1.8,5.334,5.334,0,0,1,1.376-1.635,6.989,6.989,0,0,1,2.249-1.171,10.9,10.9,0,0,1,3.365-.446h22.048a10.9,10.9,0,0,1,3.365.446,7,7,0,0,1,2.25,1.171,5.346,5.346,0,0,1,1.376,1.635,8.027,8.027,0,0,1,.706,1.8,8.621,8.621,0,0,1,.26,1.673q.037.78.037,1.265h-7.994a1.768,1.768,0,0,0-.316-1.134,2.109,2.109,0,0,0-.687-.577,2.606,2.606,0,0,0-1-.26H109.826a2.393,2.393,0,0,0-1,.259,1.677,1.677,0,0,0-1,1.705v10.012a1.923,1.923,0,0,0,.3,1.15,1.785,1.785,0,0,0,.707.593,2.38,2.38,0,0,0,1,.26h18.033a2.641,2.641,0,0,0,1-.256,2.016,2.016,0,0,0,.687-.584,1.789,1.789,0,0,0,.316-1.131Z" transform="translate(-99.825 -87.108)" />
                                            <path id="Path_98" data-name="Path 98" d="M273.549,87.108a10.9,10.9,0,0,1,3.365.446,7,7,0,0,1,2.25,1.171,5.35,5.35,0,0,1,1.376,1.635,8.013,8.013,0,0,1,.706,1.8,8.6,8.6,0,0,1,.261,1.673q.036.78.037,1.265v10.039q0,.52-.037,1.3a8.6,8.6,0,0,1-.261,1.673,7.651,7.651,0,0,1-.706,1.784,5.407,5.407,0,0,1-1.376,1.617,7,7,0,0,1-2.25,1.171,10.9,10.9,0,0,1-3.365.446H251.5a10.9,10.9,0,0,1-3.365-.446,6.989,6.989,0,0,1-2.249-1.171,5.4,5.4,0,0,1-1.376-1.617,7.639,7.639,0,0,1-.706-1.784,8.577,8.577,0,0,1-.261-1.673q-.037-.78-.037-1.3V95.1q0-.484.037-1.265a8.578,8.578,0,0,1,.261-1.673,8,8,0,0,1,.706-1.8,5.339,5.339,0,0,1,1.376-1.635,6.989,6.989,0,0,1,2.249-1.171,10.9,10.9,0,0,1,3.365-.446Zm-2.007,20a2.6,2.6,0,0,0,1-.26,2.038,2.038,0,0,0,.688-.593,1.843,1.843,0,0,0,.316-1.15V95.1a1.762,1.762,0,0,0-.316-1.131,2.125,2.125,0,0,0-.688-.575,2.618,2.618,0,0,0-1-.259H253.508a2.4,2.4,0,0,0-1,.259,1.677,1.677,0,0,0-1,1.705v10.012a1.923,1.923,0,0,0,.3,1.15,1.786,1.786,0,0,0,.706.593,2.383,2.383,0,0,0,1,.26Z" transform="translate(-201.754 -87.108)" />
                                            <path id="Path_99" data-name="Path 99" d="M417.23,87.108a10.9,10.9,0,0,1,3.365.446,7,7,0,0,1,2.25,1.171,5.35,5.35,0,0,1,1.376,1.635,8.011,8.011,0,0,1,.706,1.8,8.621,8.621,0,0,1,.261,1.673q.036.78.037,1.265t-.037,1.282a8.653,8.653,0,0,1-.261,1.692,7.993,7.993,0,0,1-.706,1.8,5.148,5.148,0,0,1-1.376,1.617,7.533,7.533,0,0,1-2.25,1.171,10.478,10.478,0,0,1-3.365.465H395.183v10h-7.994V87.108Zm-2.007,10a2.6,2.6,0,0,0,1-.26,2.022,2.022,0,0,0,.688-.595,1.847,1.847,0,0,0,.316-1.152,1.768,1.768,0,0,0-.316-1.134,2.11,2.11,0,0,0-.688-.577,2.6,2.6,0,0,0-1-.26H395.183v3.977Z" transform="translate(-303.683 -87.108)" />
                                            <path id="Path_100" data-name="Path 100" d="M568.906,87.108v6.024H553.886v20h-7.994v-20H530.871V87.108Z" transform="translate(-405.611 -87.108)" />
                                            <path id="Path_101" data-name="Path 101" d="M702.587,103.133l10,10h-10l-10-10H682.547v10h-7.994V87.108h30.041a10.9,10.9,0,0,1,3.365.446,7,7,0,0,1,2.25,1.171,5.342,5.342,0,0,1,1.376,1.635,8.024,8.024,0,0,1,.706,1.8,8.594,8.594,0,0,1,.26,1.673q.037.78.037,1.265t-.037,1.282a8.627,8.627,0,0,1-.26,1.692,8.006,8.006,0,0,1-.706,1.8,5.141,5.141,0,0,1-1.376,1.617,7.532,7.532,0,0,1-2.25,1.171,10.479,10.479,0,0,1-3.365.465Zm0-6.024a2.6,2.6,0,0,0,1-.26,2.021,2.021,0,0,0,.687-.595,1.847,1.847,0,0,0,.316-1.152,1.768,1.768,0,0,0-.316-1.134,2.108,2.108,0,0,0-.687-.577,2.6,2.6,0,0,0-1-.26h-20.04v3.977Z" transform="translate(-507.54 -87.108)" />
                                            <path id="Path_102" data-name="Path 102" d="M856.27,93.12l-28.034,13.991H856.27v6.023H818.235v-7.993l26.026-12.009H818.235V87.108H856.27Z" transform="translate(-609.469 -87.108)" />
                                        </g>
                                        <path id="Path_103" data-name="Path 103" d="M964.486,87.884l-.041-.007-1.13,2.823h-.34l-1.15-2.916-.041.007V90.7h-.367V87.108h.5l1.209,3.023h.041l1.223-3.023h.461V90.7h-.367Zm-3.589-.395h-1.056V90.7h-.374v-3.21H958.33v-.381H960.9Z" transform="translate(-609.028)" />
                                    </g>
                                </svg>
                            </a>
                        </div>
                        <div class="nav-box my-4">
                            <ul class="d-flex list-inline">
                                <li class="active">
                                    DETAILS
                                </li>
                                <li>
                                    ADDRESS
                                </li>
                                <li>
                                    PAYMENT
                                </li>
                            </ul>
                        </div>
                        <style>
                            .custom-login a {
                                margin-left: 2rem;
                                font-weight: bold;
                                text-transform: uppercase;
                                color: var(--accent-color);
                                font-size: 22px;
                                cursor: pointer;
                            }
                        </style>
                        <?php if (!is_user_logged_in()) { ?>
                            <div class="custom-login">
                                Already have an account? <a href="#customer-login">Login</a>
                            </div>
                        <?php } ?>

                        <div class="previous-steps">
                            <div class="previous-step d-none contact">
                                <div class="row">
                                    <div class="col-2">
                                        <label for="">Contact</label>
                                    </div>
                                    <div class="col-8">
                                        <span class="email"></span>
                                    </div>
                                    <div class="col-2 text-end">
                                        <a class="back-to">Edit</a>
                                    </div>
                                </div>
                            </div>
                            <div class="previous-step d-none address">
                                <div class="row">
                                    <div class="col-2">
                                        <label for="">Address</label>
                                    </div>
                                    <div class="col-8">
                                        <div class="address"></div>
                                    </div>
                                    <div class="col-2 text-end">
                                        <a class="back-to">Edit</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="step-box customer-details active" step="1">
                            <div class="heading-box">
                                <h2>Customer Details</h2>
                            </div>
                            <div id="customer_details">
                                <?php
                                do_action('woocommerce_checkout_billing');
                                ?>
                            </div>
                            <div class="step-buttons mt-5 d-flex justify-content-between">
                                <a class="continue button">CONTINUE TO ADDRESS</a>
                                <a class="d-flex align-items-center" href="/shop">
                                    <svg class="me-1" xmlns="http://www.w3.org/2000/svg" width="17" height="12" fill="none">
                                        <path d="M16 6H1M6 1L1 6l5 5" stroke="#16110E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <strong>Back to Shop</strong>
                                </a>
                            </div>
                        </div>

                        <div class="step-box address d-none" step="2">
                            <?php do_action('woocommerce_checkout_shipping'); ?>
                            <div class="step-buttons mt-5 d-flex justify-content-between">
                                <a class="continue button">CONTINUE TO PAYMENT</a>
                                <a class="back-to d-flex align-items-center">
                                    <svg class="me-1" xmlns="http://www.w3.org/2000/svg" width="17" height="12" fill="none">
                                        <path d="M16 6H1M6 1L1 6l5 5" stroke="#16110E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <strong>Back</strong>
                                </a>
                            </div>
                        </div>
                        <div class="step-box payment d-none" step="3">
                            <div class="heading-box">
                                <h2>Payment</h2>
                            </div>
                            <?php do_action('custom_woocommerce_checkout_shipping'); ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 column-order">
                    <div class="column-holder py-5">
                        <div class="heading-box basket-heading">
                            <h2>
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="25" fill="none">
                                    <path d="M7 10.5v-5a4.013 4.013 0 014-4 4.013 4.013 0 014 4v5m3.734 13H3.266a2 2 0 01-1.985-2.248L3 7.5h16l1.719 13.752a2 2 0 01-1.985 2.248z" stroke="#000" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Basket
                                <div class="count">
                                    <span class="counter" id="cart-count">
                                        <?php
                                        $cart_count = WC()->cart->get_cart_contents_count();
                                        echo sprintf(_n('%d', '%d', $cart_count), $cart_count);
                                        ?>
                                    </span>
                                </div>
                            </h2>
                        </div>
                        <div class="shipping-methods d-flex align-items-center justify-content-between">
                            <div class="shipping-method-title d-flex align-items-center">
                                <svg class="me-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
                                    <path d="M6.5 3.25l12 6M23 7l-10 5m10-5v10l-10 6M23 7L11 1 1 6m12 6L1 6m12 6v11M1 6v11l12 6" stroke="#16110E" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <strong>Shipping</strong>
                            </div>
                            <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>

                                <?php do_action('woocommerce_review_order_before_shipping'); ?>

                                <?php wc_cart_totals_shipping_html(); ?>

                                <?php do_action('woocommerce_review_order_after_shipping'); ?>

                            <?php endif; ?>
                        </div>
                        <?php do_action('woocommerce_checkout_before_order_review'); ?>
                        <div id="order_review" class="woocommerce-checkout-review-order">
                            <div class="blockUI blockOverlay custom-ui coupon-ui" style="z-index: 1000; border: none; margin: 0px; padding: 0px; width: 100%; height: 100%; top: 0px; left: 0px; background: rgb(255, 255, 255); opacity: 0.6; cursor: default; position: absolute;"></div>
                            <?php do_action('woocommerce_checkout_order_review'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>
<?php do_action('woocommerce_after_checkout_form', $checkout); ?>