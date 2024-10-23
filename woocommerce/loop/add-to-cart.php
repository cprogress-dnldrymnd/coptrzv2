<?php

/**
 * Loop Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     9.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

global $product;
$product_category_page = false;
$col_class = 'col-sm-12';
$class = 'mb-5';
if (is_product_taxonomy()) {
	echo __description(array(
		'description' => get_the_excerpt(),
		'class' => _attribute('class', array('product-desc px-20px mb-4')),
	));
	echo '</div>';
}
$data_encode = _single_product_data(get_the_ID());
?>
<div class="product-buttons">
	<div class="button-box button-bordered"><a class="product-btn" href="<?= get_the_permalink() ?>"> <span class="product-data d-none"><?= $data_encode ?></span> Discover</a></div>
</div>