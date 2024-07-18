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
	$product_category_page = __get_product_taxonomy_page(get_queried_object()->term_id);
	if ($product_category_page) {
		$col_class = 'col-sm-6';
		$class = '';
	} else {
		echo __description(array(
			'description' => get_the_excerpt(),
			'class' => _attribute('class', array('product-desc px-20px')),
		));
	}
}
?>
<div class="product-buttons <?= $class ?>">
	<div class="row g-10px">
		<?php if ($product_category_page) { ?>
			<div class="<?= $col_class ?>">
				<?php
				echo apply_filters(
					'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
					sprintf(
						'<a href="%s" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_%s" data-quantity="%s" class="%s" %s>%s</a>',
						esc_url($product->add_to_cart_url()),
						esc_attr($product->get_id()),
						esc_attr(isset($args['quantity']) ? $args['quantity'] : 1),
						esc_attr(isset($args['class']) ? $args['class'] : 'button'),
						isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '',
						esc_html($product->add_to_cart_text())
					),
					$product,
					$args
				);
				?>
				<span id="woocommerce_loop_add_to_cart_link_describedby_<?php echo esc_attr($product->get_id()); ?>" class="screen-reader-text">
					<?php echo esc_html($args['aria-describedby_text']); ?>
				</span>
			</div>
		<?php } ?>
		<div class="<?= $col_class ?>">
			<div class="button-box button-bordered"><a href="<?= get_the_permalink() ?>">Discover</a></div>
		</div>
	</div>
</div>