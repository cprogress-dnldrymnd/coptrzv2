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
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.3.0
 */

if (!defined('ABSPATH')) {
	exit;
}

global $product;
?>
<?php if (_is_shop_archive() || is_product()) { ?>

	<div class="row g-3 g-sm-1 align-items-center row-archive-products-buttons">
		<div class="col-12 <?= _is_shop_archive() ? 'col-sm-6' : '' ?>">
			<?php echo apply_filters(
				'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
				sprintf(
					'<a href="%s" data-quantity="%s" class="%s m-0 w-100" %s>%s</a>',
					esc_url($product->add_to_cart_url()),
					esc_attr(isset($args['quantity']) ? $args['quantity'] : 1),
					esc_attr(isset($args['class']) ? $args['class'] : 'button'),
					isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '',
					esc_html($product->add_to_cart_text())
				),
				$product,
				$args
			);
			?>
		</div>
		<div class="col-12 col-sm-6">
			<?= product_buttons() ?>
		</div>
	</div>
<?php } else { ?>
	<a href="<?= get_permalink() ?>" class="button product_type_variable add_to_cart_button m-0" rel="nofollow">
		View Product
	</a>
<?php } ?>