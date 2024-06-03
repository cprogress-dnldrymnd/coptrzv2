<?php
$GetData = new GetData;
$size = isset($size) ? $size : 'full';
$placeholder = isset($placeholder) ? true : false;
$image_url = wp_get_attachment_image_url($id, $size);
$link = isset($link) ? $link : false;

$ext = wp_check_filetype(wp_get_attachment_url($id))['ext'];
$img_class = '';
if ($image_url) {
	$image_alt = $GetData->get_image_alt($id);
} else {
	if ($placeholder) {
		$logo = get__theme_option('alt_logo');
		$image_url = wp_get_attachment_image_url($logo);
		$image_alt = $GetData->get_image_alt($logo);
		$img_class = 'placeholder-image image-contain-transform';
	}
}

if ($rounded_corners == 'true') {
	$img_class = 'rounded-corner';
}

if ($border_radius) {
	$style = 'style="--border-radius: ' . $border_radius . '"';
}

?>
<?php if ($image_url || $placeholder) { ?>

	<div class="image-box <?= $border_radius ?> <?= $same_height == 'true' ? 'image-absolute' : '' ?> <?= $class ?>" <?= $GetData->get_data_aos($data_aos) ?>>
		<?php
		if ($link) {
			echo '<a href="' . $link . '" class="d-block">';
		}
		?>
		<img <?= $style ?> <?= $image_height ? 'height="' . $image_height . '"' : '' ?> <?= $image_width ? 'width="' . $image_width . '"' : '' ?> decoding="async" class="jetpack-lazy-image <?= $img_class ?>" src="<?= $image_url ?>" alt="<?= $image_alt ?>">
	</div>
	<?php
	if ($link) {
		echo '</a>';
	}
	?>
<?php } ?>