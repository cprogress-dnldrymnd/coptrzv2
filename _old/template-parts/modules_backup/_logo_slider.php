<?php
$DisplayData = new DisplayData;
$Helpers = new Helpers;
$GetData = new GetData;
$background_color = $module['background_color'];

if ($type == 'product-customer-slider') {
	$image_source = $image_source_product;
	$custom_heading = $custom_heading_product;
} else {
	$image_source = $module['image_source'];
	$custom_heading = $module['custom_heading'];
}

if ($image_source == 'select-from-gallery') {
	if ($type == 'product-customer-slider') {
		$logo_slider_id = $logo_slider_id_product;
	} else {
		$logo_slider_id = $module['gallery'];
	}


	$logo_slider = get__post_meta_by_id($logo_slider_id, 'media_gallery');
	$title = get_the_title($logo_slider_id);
} else {
	if ($type == 'product-customer-slider') {
		$logo_slider = $custom_gallery_product;
	} else {
		$logo_slider = $module['custom_gallery'];
	}
}
?>
<div class="container ">
	<div class="slider-title white-color line-title d-flex align-items-center fw-medium">
		<span class="text">
			<?= $custom_heading ? $custom_heading : $title ?>
		</span>
		<span class="line"></span>
	</div>
</div>
<div class="logo-slider-box md-padding">
	<div class="swiper mySwiper-logoSwiper">
		<div class="swiper-wrapper text-center align-items-center">
			<?php foreach ($logo_slider as $logo) { ?>
				<div class="swiper-slide">
					<?php
					$DisplayData->image(
						array(
							'image_id' => $logo,
							'size' => 'medium'
						)
					);
					?>
				</div>
			<?php } ?>
		</div>
	</div>
</div>