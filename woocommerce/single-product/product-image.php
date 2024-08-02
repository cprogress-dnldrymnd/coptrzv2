<?php

/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined('ABSPATH') || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if (!function_exists('wc_get_gallery_image_html')) {
	return;
}

global $product;

$columns           = apply_filters('woocommerce_product_thumbnails_columns', 4);
$post_thumbnail_id = $product->get_image_id();
$attachment_ids = $product->get_gallery_image_ids();
$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ($post_thumbnail_id ? 'with-images' : 'without-images'),
		'woocommerce-product-gallery--columns-' . absint($columns),
		'images',
	)
);
?>
<div class="images">
	<div class="woocommerce-product-gallery__wrapper">
		<div class="row">
			<div class="col-lg-7">
				<div class="product-main-image-holder">
					<div class="swiper product-main-image border-default rounded-corner overflow-hidden">
						<div class="swiper-wrapper">
							<div class="swiper-slide">
								<?php

								if ($post_thumbnail_id) {
									echo __image(array(
										'image_id' => $post_thumbnail_id,
										'class' => _attribute('class', array('product-image')),
										'size' => 'large'
									));
								} else {
									$html  = '<div class="product-image woocommerce-product-gallery__image--placeholder">';
									$html .= sprintf('<img src="%s" alt="%s" class="wp-post-image" />', esc_url(wc_placeholder_img_src('woocommerce_single')), esc_html__('Awaiting product image', 'woocommerce'));
									$html .= '</div>';
									echo $html;
								}

								?>
							</div>
							<?php
							if ($attachment_ids && $product->get_image_id()) {
								foreach ($attachment_ids as $attachment_id) {
							?>
									<div class="swiper-slide">
										<?php
										echo __image(array(
											'image_id' => $attachment_id,
											'class' => _attribute('class', array('product-image')),
											'size' => 'large'
										));
										?>
									</div>
							<?php
								}
							}
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-5">
				<?php
				do_action('woocommerce_product_thumbnails');
				?>
			</div>
		</div>
	</div>
</div>