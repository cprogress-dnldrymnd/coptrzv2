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
}
else {
	if ($placeholder) {
		$logo = get__theme_option('alt_logo');
		$image_url = wp_get_attachment_image_url($logo);
		$image_alt = $GetData->get_image_alt($logo);
		$img_class = 'placeholder-image image-contain-transform';
	}
}

?>
<?php if ($image_url || $placeholder) { ?>

	<div class="image-box <?= $class ?>" <?= $GetData->get_data_aos($data_aos) ?>>
		<?php
		if ($link) {
			echo '<a href="' . $link . '" class="d-block">';
		}
		?>
		<?php if ($ext != 'svg') { ?>
			<img <?= $image_height ? 'height="' . $image_height . '"' : '' ?> 		<?= $image_width ? 'width="' . $image_width . '"' : '' ?> decoding="async" class="jetpack-lazy-image" src="<?= $image_url ?>" alt="<?= $image_alt ?>">
		<?php }
		else { ?>
			<span class="svg-image" src="<?= $image_url ?>" alt="<?= $image_alt ?>"></span>
		<?php } ?>
	</div>
	<?php
	if ($link) {
		echo '</a>';
	}
?>
<?php } ?>