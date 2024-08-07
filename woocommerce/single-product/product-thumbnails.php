<?php

/**
 * Single Product Thumbnails
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-thumbnails.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.5.1
 */

defined('ABSPATH') || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if (!function_exists('wc_get_gallery_image_html')) {
	return;
}

global $product;
$post_thumbnail_id = $product->get_image_id();
$attachment_ids = $product->get_gallery_image_ids();

$image_ids[] = array(
	'key' => 0,
	'id' => $post_thumbnail_id
);
$key = 1;
foreach ($attachment_ids as $attachment_id) {
	$image_ids[] = array(
		'key' => $key,
		'id' => $attachment_id
	);
	$key++;
}
var_dump($image_ids);
$images_ids_per_slides = array_chunk($image_ids, 6);
?>
<div class="product-thumb-holder">
	<div class="swiper product-thumb">
		<div class="swiper-wrapper">
			<?php
			if ($image_ids) {
				$key = 1;

				foreach ($images_ids_per_slides as  $images_ids_per_slide) {
					if (count($image_ids) > 6) {
						echo '<div class="swiper-slide">';
					}
					echo '<div class="row g-4 w-100">';

					foreach ($images_ids_per_slide as $image) {
						$key = $image['key'];
						echo "<div class='col-6' target='$key'>";
						echo apply_filters('woocommerce_single_product_image_thumbnail_html', wc_get_gallery_image_html($image['id']), $post_thumbnail_id);
						echo '</div>';
					}


					echo '</div>';
					if (count($image_ids) > 6) {
						echo '</div>';
					}
				}
			}
			?>

		</div>
		<?php if (count($image_ids) > 6) { ?>
			<div class="swiper-nav d-inline-flex">
				<div class="swiper-button-prev swiper-button-prev-thumb"></div>
				<div class="swiper-button-next swiper-button-next-thumb"></div>
			</div>
		<?php } ?>
	</div>
</div>